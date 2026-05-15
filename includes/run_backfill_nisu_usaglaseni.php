<?php
/**
 * Backfill kolone izvjestaji_nisu_usaglaseni (ista logika kao pregledizvjestajapdf.php do zaključka).
 *
 * Faza 1: za svaki ID pokrene PDF logiku s odgođenim UPDATE-om — rezultati u $GLOBALS pa u JSON.
 * Faza 2: jedna transakcija, batch UPDATE (brže od pojedinačnog commit-a po redu).
 *
 * @param PDO $pdo
 * @param callable|null $onProgress function(int $current, int $total, int $reportId): void
 * @return array{ok:bool,message:string}
 */
if (!function_exists('norma_run_backfill_nisu_usaglaseni')) {
    function norma_run_backfill_nisu_usaglaseni(PDO $pdo, $onProgress = null)
    {
        if (!($pdo instanceof PDO)) {
            return array('ok' => false, 'message' => 'Nema valjane PDO konekcije.');
        }

        $chk = $pdo->query("SHOW COLUMNS FROM `izvjestaji` LIKE 'izvjestaji_nisu_usaglaseni'")->fetch(PDO::FETCH_ASSOC);
        if (!$chk) {
            return array(
                'ok'      => false,
                'message' => 'Kolona izvjestaji_nisu_usaglaseni ne postoji. Prvo odradi SQL migracije.',
            );
        }

        $ids = $pdo->query('SELECT `izvjestaji_id` FROM `izvjestaji` ORDER BY `izvjestaji_id` ASC')->fetchAll(PDO::FETCH_COLUMN, 0);
        $total = count($ids);
        if ($total === 0) {
            return array('ok' => true, 'message' => 'Nema izvještaja u bazi.');
        }

        $root = dirname(__DIR__);
        $savedCwd = getcwd();
        if (!@chdir($root)) {
            return array('ok' => false, 'message' => 'Ne mogu chdir na korijen projekta: ' . $root);
        }

        $pdfPath = $root . DIRECTORY_SEPARATOR . 'pregledizvjestajapdf.php';
        if (!is_readable($pdfPath)) {
            @chdir($savedCwd);

            return array('ok' => false, 'message' => 'Nedostaje pregledizvjestajapdf.php');
        }

        $jsonDir = $root . DIRECTORY_SEPARATOR . 'tmp';
        $jsonPath = $jsonDir . DIRECTORY_SEPARATOR . 'norma_backfill_nisu_usaglaseni.json';
        @mkdir($jsonDir, 0775, true);

        putenv('NORMA_SETUP_BACKFILL=1');
        putenv('NORMA_SETUP_BACKFILL_LOOP=1');
        $GLOBALS['norma_backfill_loop_active'] = true;

        $GLOBALS['norma_backfill_defer_db'] = true;
        $GLOBALS['norma_backfill_rows'] = array();

        $fail = 0;
        $firstErr = '';
        $n = 0;

        try {
            foreach ($ids as $id) {
                $id = (int) $id;
                $n++;
                try {
                    (function () use ($pdfPath, $id) {
                        $_GET['izvjestaj'] = $id;
                        require $pdfPath;
                    })();
                } catch (Throwable $e) {
                    $fail++;
                    if ($firstErr === '') {
                        $firstErr = $e->getMessage();
                    }
                }

                if (is_callable($onProgress)) {
                    $onProgress($n, $total, $id);
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
                'total_izvjestaja_u_bazi' => $total,
                'prikupljeno_redova'      => count($rows),
                'greske_pri_racunanju'    => $fail,
                'rows'                    => $rows,
            );
            @file_put_contents($jsonPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            if ($fail > 0) {
                return array(
                    'ok'      => false,
                    'message' => "Backfill: greške na {$fail} od {$total} izvještaja. Prva: {$firstErr}. JSON (djelomično): {$jsonPath}",
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

            return array(
                'ok'      => true,
                'message' => 'Backfill izvjestaji_nisu_usaglaseni: ažurirano ' . count($rows) . ' redova u jednoj transakciji. JSON: ' . $jsonPath . ' (možeš obrisati nakon provjere). Filter na pregledu sada može ispravno raditi.',
            );
        } finally {
            putenv('NORMA_SETUP_BACKFILL_LOOP');
            putenv('NORMA_SETUP_BACKFILL');
            unset(
                $GLOBALS['norma_backfill_loop_active'],
                $GLOBALS['norma_backfill_defer_db'],
                $GLOBALS['norma_backfill_rows']
            );
            @chdir($savedCwd);
        }
    }
}
