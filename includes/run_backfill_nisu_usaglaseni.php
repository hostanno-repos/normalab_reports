<?php
/**
 * Backfill kolone izvjestaji_nisu_usaglaseni (ista logika kao Zavod PDF — mpdf-includes).
 *
 * Faza 1: za svaki ID pokrene Zavod template headless — rezultati u $GLOBALS pa u JSON.
 * Faza 2: jedna transakcija, batch UPDATE (brže od pojedinačnog commit-a po redu).
 *
 * @param PDO $pdo
 * @param callable|null $onProgress function(int $current, int $total, int $reportId): void
 * @param int|null $limit Koliko izvještaja po chunk-u (default: 300)
 * @param int|null $offset Početni offset u sortiranom skupu izvještaja (default: 0)
 * @return array{ok:bool,message:string,done?:bool,total?:int,processed?:int,next_offset?:int}
 */
if (!function_exists('norma_run_backfill_nisu_usaglaseni')) {
    function norma_run_backfill_nisu_usaglaseni(PDO $pdo, $onProgress = null, ?int $limit = null, ?int $offset = null)
    {
        if (!($pdo instanceof PDO)) {
            return array('ok' => false, 'message' => 'Nema valjane PDO konekcije.');
        }

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'norma_backfill_zavod_usaglasenost.php';

        $limit = $limit ?? (int)($GLOBALS['norma_setup_backfill_chunk_limit'] ?? 300);
        $offset = $offset ?? (int)($GLOBALS['norma_setup_backfill_chunk_offset'] ?? 0);
        $limit = $limit > 0 ? $limit : 300;
        $offset = $offset >= 0 ? $offset : 0;

        $chk = $pdo->query("SHOW COLUMNS FROM `izvjestaji` LIKE 'izvjestaji_nisu_usaglaseni'")->fetch(PDO::FETCH_ASSOC);
        if (!$chk) {
            return array(
                'ok'      => false,
                'message' => 'Kolona izvjestaji_nisu_usaglaseni ne postoji. Prvo odradi SQL migracije.',
            );
        }

        $total = (int) $pdo->query('SELECT COUNT(*) FROM `izvjestaji`')->fetchColumn();
        if ($total === 0) {
            return array('ok' => true, 'message' => 'Nema izvještaja u bazi.');
        }

        if ($offset >= $total) {
            return array(
                'ok'         => true,
                'message'    => 'Backfill: nema više posla (offset >= ukupno).',
                'done'       => true,
                'total'      => $total,
                'processed'  => 0,
                'next_offset'=> $total,
            );
        }

        // LIMIT/OFFSET moraju biti literalni int u SQL-u (PDO ih inače binda kao string → 1064 na MariaDB).
        $limit = (int) $limit;
        $offset = (int) $offset;
        $ids = $pdo->query(
            'SELECT `izvjestaji_id` FROM `izvjestaji` ORDER BY `izvjestaji_id` ASC LIMIT ' . $limit . ' OFFSET ' . $offset
        )->fetchAll(PDO::FETCH_COLUMN, 0);
        $chunkCount = is_array($ids) ? count($ids) : 0;

        if ($chunkCount === 0) {
            return array(
                'ok'          => true,
                'message'     => 'Backfill: chunk je prazan.',
                'done'        => true,
                'total'       => $total,
                'processed'   => 0,
                'next_offset' => $offset,
            );
        }

        $root = dirname(__DIR__);
        $savedCwd = getcwd();
        if (!@chdir($root)) {
            return array('ok' => false, 'message' => 'Ne mogu chdir na korijen projekta: ' . $root);
        }

        $jsonDir = $root . DIRECTORY_SEPARATOR . 'tmp';
        $jsonPath = $jsonDir . DIRECTORY_SEPARATOR . 'norma_backfill_nisu_usaglaseni.json';
        @mkdir($jsonDir, 0775, true);

        $GLOBALS['norma_backfill_rows'] = array();

        $fail = 0;
        $firstErr = '';
        $n = 0;

        try {
            foreach ($ids as $id) {
                $id = (int) $id;
                $n++;
                try {
                    $computed = norma_backfill_compute_zavod_nisu_usaglaseni($pdo, $id, $root);
                    if (empty($computed['ok'])) {
                        throw new RuntimeException((string) ($computed['message'] ?? 'Nepoznata greška.'));
                    }
                    $GLOBALS['norma_backfill_rows'][] = array(
                        'izvjestaji_id'              => $id,
                        'izvjestaji_nisu_usaglaseni' => (int) ($computed['nisu_usaglaseni'] ?? 0),
                        'finalusaglasenost'          => (string) ($computed['finalusaglasenost'] ?? ''),
                        'vrsta_id'                   => (int) ($computed['vrsta_id'] ?? 0),
                    );
                } catch (Throwable $e) {
                    $fail++;
                    if ($firstErr === '') {
                        $firstErr = 'ID ' . $id . ': ' . $e->getMessage();
                    }
                }

                if (is_callable($onProgress)) {
                    $onProgress($offset + $n, $total, $id);
                }

                if ($n % 50 === 0) {
                    gc_collect_cycles();
                }
            }

            $rows = isset($GLOBALS['norma_backfill_rows']) && is_array($GLOBALS['norma_backfill_rows'])
                ? $GLOBALS['norma_backfill_rows']
                : array();

            $payload = array(
                'generated_at' => date('c'),
                'source'       => 'zavod_mpdf_includes',
                'total_izvjestaja_u_bazi' => $total,
                'chunk_offset' => $offset,
                'chunk_limit'  => $limit,
                'chunk_processed_attempts' => $n,
                'prikupljeno_redova'      => count($rows),
                'greske_pri_racunanju'    => $fail,
                'rows'                    => $rows,
            );
            @file_put_contents($jsonPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            if ($fail > 0) {
                return array(
                    'ok'      => false,
                    'message' => "Backfill: greške na {$fail} od {$n} u chunk-u. Prva: {$firstErr}. JSON (djelomično): {$jsonPath}",
                );
            }

            if (count($rows) === 0) {
                return array('ok' => false, 'message' => 'Nema prikupljenih redova za UPDATE (neočekivano). JSON: ' . $jsonPath);
            }

            $pdo->beginTransaction();
            try {
                $st = $pdo->prepare('UPDATE `izvjestaji` SET `izvjestaji_nisu_usaglaseni` = ? WHERE `izvjestaji_id` = ? LIMIT 1');
                foreach ($rows as $r) {
                    $st->execute(array((int) $r['izvjestaji_nisu_usaglaseni'], (int) $r['izvjestaji_id']));
                }
                $pdo->commit();
            } catch (Throwable $e) {
                $pdo->rollBack();

                return array(
                    'ok'      => false,
                    'message' => 'Batch UPDATE nije uspio: ' . $e->getMessage() . ' JSON ostaje: ' . $jsonPath,
                );
            }

            $nextOffset = $offset + $n;
            $done = $nextOffset >= $total;

            return array(
                'ok'      => true,
                'message' => 'Backfill izvjestaji_nisu_usaglaseni (Zavod PDF): ažurirano ' . count($rows) . ' redova u jednoj transakciji (chunk offset ' . $offset . ', ' . $n . ' pokušaja). JSON: ' . $jsonPath . ' (možeš obrisati nakon provjere).',
                'done' => $done,
                'total' => $total,
                'processed' => $n,
                'next_offset' => $nextOffset,
            );
        } finally {
            unset($GLOBALS['norma_backfill_rows']);
            @chdir($savedCwd);
        }
    }
}
