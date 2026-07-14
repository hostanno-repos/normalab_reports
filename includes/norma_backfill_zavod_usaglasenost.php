<?php
/**
 * Backfill kolone izvjestaji_nisu_usaglaseni — ista logika kao Zavod PDF (mpdf-includes).
 * Ne mijenja mpdf fajlove; radi ih headless uz zaštitu od include_once / redeclare fatals.
 */

if (!function_exists('norma_backfill_latinica_u_cirilicu')) {
    function norma_backfill_latinica_u_cirilicu($tekst)
    {
        $tekst = $tekst ?? '';
        if ($tekst === '') {
            return '';
        }
        $mapa = [
            'A' => 'А', 'B' => 'Б', 'V' => 'В', 'G' => 'Г', 'D' => 'Д', 'Đ' => 'Ђ', 'E' => 'Е', 'Ž' => 'Ж',
            'Z' => 'З', 'I' => 'И', 'J' => 'Ј', 'K' => 'К', 'L' => 'Л', 'Lj' => 'Љ', 'M' => 'М', 'N' => 'Н',
            'Nj' => 'Њ', 'O' => 'О', 'P' => 'П', 'R' => 'Р', 'S' => 'С', 'T' => 'Т', 'Ć' => 'Ћ', 'U' => 'У',
            'F' => 'Ф', 'H' => 'Х', 'C' => 'Ц', 'Č' => 'Ч', 'Dž' => 'Џ', 'Š' => 'Ш',
            'a' => 'а', 'b' => 'б', 'v' => 'в', 'g' => 'г', 'd' => 'д', 'đ' => 'ђ', 'e' => 'е', 'ž' => 'ж',
            'z' => 'з', 'i' => 'и', 'j' => 'ј', 'k' => 'к', 'l' => 'л', 'lj' => 'љ', 'm' => 'м', 'n' => 'н',
            'nj' => 'њ', 'o' => 'о', 'p' => 'п', 'r' => 'р', 's' => 'с', 't' => 'т', 'ć' => 'ћ', 'u' => 'у',
            'f' => 'ф', 'h' => 'х', 'c' => 'ц', 'č' => 'ч', 'dž' => 'џ', 'š' => 'ш',
        ];
        $tekst = str_replace(
            ['Dž', 'dž', 'Lj', 'lj', 'Nj', 'nj'],
            ['Ǆ', 'ǆ', 'Ǉ', 'ǉ', 'Ǌ', 'ǌ'],
            $tekst
        );
        $tekst = strtr($tekst, $mapa);

        return str_replace(
            ['Ǆ', 'ǆ', 'Ǉ', 'ǉ', 'Ǌ', 'ǌ'],
            ['Џ', 'џ', 'Љ', 'љ', 'Њ', 'њ'],
            $tekst
        );
    }
}

if (!function_exists('calculateSampleStandardDeviation')) {
    /**
     * Ista implementacija kao u mpdf-includes/49.php — definisana jednom da second include ne fatala.
     */
    function calculateSampleStandardDeviation($array)
    {
        $count = count($array);
        if ($count <= 1) {
            return 0;
        }
        $mean = array_sum($array) / $count;
        $squaredDifferences = array_map(function ($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $array);
        $variance = array_sum($squaredDifferences) / ($count - 1);

        return sqrt($variance);
    }
}

if (!function_exists('norma_backfill_zavod_template_path')) {
    /**
     * @return array{ok:bool, vrsta_id?:int, template?:string, message?:string}
     */
    function norma_backfill_zavod_template_path(PDO $pdo, int $reportId, string $projectRoot): array
    {
        $st = $pdo->prepare(
            'SELECT i.`izvjestaji_id`, rn.`radninalozi_vrstauredjajaid` AS vrsta_id
             FROM `izvjestaji` i
             INNER JOIN `radninalozi` rn ON rn.`radninalozi_id` = i.`izvjestaji_radninalogid`
             WHERE i.`izvjestaji_id` = ?
             LIMIT 1'
        );
        $st->execute(array($reportId));
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return array('ok' => false, 'message' => 'Izvještaj #' . $reportId . ' ne postoji.');
        }

        $vrstaId = (int) ($row['vrsta_id'] ?? 0);
        if ($vrstaId <= 0) {
            return array('ok' => false, 'message' => 'Izvještaj #' . $reportId . ' nema vrstu uređaja na radnom nalogu.');
        }

        $template = $projectRoot . DIRECTORY_SEPARATOR . 'mpdf-includes' . DIRECTORY_SEPARATOR . $vrstaId . '.php';
        if (!is_readable($template)) {
            return array(
                'ok'      => false,
                'message' => 'Nedostaje Zavod template mpdf-includes/' . $vrstaId . '.php za izvještaj #' . $reportId . '.',
            );
        }

        return array(
            'ok'       => true,
            'vrsta_id' => $vrstaId,
            'template' => $template,
        );
    }
}

if (!function_exists('norma_backfill_finalusaglasenost_to_flag')) {
    function norma_backfill_finalusaglasenost_to_flag($finalusaglasenost): int
    {
        $s = (string) ($finalusaglasenost ?? '');
        if ($s === '') {
            return 0;
        }
        if (strpos($s, 'не испуњава') !== false || strpos($s, 'NISU') !== false) {
            return 1;
        }

        return 0;
    }
}

if (!function_exists('norma_backfill_bootstrap_report_vars')) {
    /**
     * Učitava iste varijable kao mpdf-includes/reports_head.php (svaki put iznova).
     *
     * @return array<string,mixed>
     */
    function norma_backfill_bootstrap_report_vars(PDO $pdo, int $reportId): array
    {
        if (!class_exists('singleObject', false)) {
            require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'getObject.php';
        }

        $izvjestaj = (new singleObject())->fetch_single_object('izvjestaji', 'izvjestaji_id', $reportId);
        if (!$izvjestaj) {
            throw new RuntimeException('Izvještaj #' . $reportId . ' nije pronađen u bootstrapu.');
        }

        $mjerenjeizvrsio = (new singleObject())->fetch_single_object('kontrolori', 'kontrolori_id', $izvjestaj['izvjestaji_izvrsioid']);
        $radninalog = (new singleObject())->fetch_single_object('radninalozi', 'radninalozi_id', $izvjestaj['izvjestaji_radninalogid']);
        $klijent = (new singleObject())->fetch_single_object('klijenti', 'klijenti_id', $radninalog['radninalozi_klijentid']);
        $mjerilo = (new singleObject())->fetch_single_object('mjerila', 'mjerila_id', $radninalog['radninalozi_mjeriloid']);
        $vrstauredjaja = (new singleObject())->fetch_single_object('vrsteuredjaja', 'vrsteuredjaja_id', $mjerilo['mjerila_vrstauredjajaid']);
        $vrsteinspekcije = (new allObjects())->fetch_all_objects('vrsteinspekcije', 'vrsteinspekcije_id', 'ASC');

        $datumInspekcije = !empty($izvjestaj['izvjestaji_datuminspekcije']) ? $izvjestaj['izvjestaji_datuminspekcije'] : '9999-12-31';
        $stmtRjesenje = $pdo->prepare(
            'SELECT * FROM rjesenjazaovlascivanje
             WHERE rjesenjazaovlascivanje_datum_izdavanja <= ?
             ORDER BY rjesenjazaovlascivanje_datum_izdavanja DESC LIMIT 1'
        );
        $stmtRjesenje->execute(array($datumInspekcije));
        $rjesenje_za_ovlascivanje = $stmtRjesenje->fetch(PDO::FETCH_ASSOC);

        return array(
            'izvjestaj'                => $izvjestaj,
            'mjerenjeizvrsio'           => $mjerenjeizvrsio,
            'radninalog'               => $radninalog,
            'klijent'                  => $klijent,
            'mjerilo'                  => $mjerilo,
            'vrstauredjaja'            => $vrstauredjaja,
            'vrsteinspekcije'          => $vrsteinspekcije,
            'rjesenje_za_ovlascivanje' => $rjesenje_za_ovlascivanje,
            'pdo'                      => $pdo,
        );
    }
}

if (!function_exists('norma_backfill_prepare_zavod_runtime_code')) {
    /**
     * Priprema template za više uzastopnih include-a u istom procesu.
     */
    function norma_backfill_prepare_zavod_runtime_code(string $templatePath, int $reportId): string
    {
        $code = file_get_contents($templatePath);
        if ($code === false) {
            throw new RuntimeException('Ne mogu pročitati template: ' . $templatePath);
        }

        // reports_head je include_once — mora se zamijeniti svježim bootstrapom za svaki izvještaj
        $code = preg_replace(
            '/include_once\s*\(\s*[\'"]reports_head\.php[\'"]\s*\)\s*;?/',
            'extract(norma_backfill_bootstrap_report_vars($pdo, ' . (int) $reportId . '), EXTR_OVERWRITE);',
            $code,
            1,
            $countHead
        );
        if ((int) $countHead === 0) {
            throw new RuntimeException('Template nema include reports_head.php: ' . basename($templatePath));
        }

        // Ne dozvoli redeclare funkcije iz 49.php / 50.php (uncatchable fatal)
        $code = preg_replace(
            '/function\s+calculateSampleStandardDeviation\s*\(\s*\$array\s*\)\s*\{(?:[^{}]++|(?R))*\}/',
            '/* calculateSampleStandardDeviation: već definisana u backfill bootstrapu */',
            $code,
            1
        );

        return $code;
    }
}

if (!function_exists('norma_backfill_compute_zavod_nisu_usaglaseni')) {
    /**
     * @return array{
     *   ok:bool,
     *   nisu_usaglaseni?:int,
     *   finalusaglasenost?:string,
     *   vrsta_id?:int,
     *   message?:string
     * }
     */
    function norma_backfill_compute_zavod_nisu_usaglaseni(PDO $pdo, int $reportId, ?string $projectRoot = null): array
    {
        if (!($pdo instanceof PDO)) {
            return array('ok' => false, 'message' => 'Nema valjane PDO konekcije.');
        }

        $projectRoot = $projectRoot ?? dirname(__DIR__);
        $resolved = norma_backfill_zavod_template_path($pdo, $reportId, $projectRoot);
        if (empty($resolved['ok'])) {
            return array('ok' => false, 'message' => (string) ($resolved['message'] ?? 'Nepoznata greška.'));
        }

        $vrstaId = (int) $resolved['vrsta_id'];
        $template = (string) $resolved['template'];
        $mpdfDir = $projectRoot . DIRECTORY_SEPARATOR . 'mpdf-includes';
        $runtimePath = $mpdfDir . DIRECTORY_SEPARATOR . '_norma_backfill_runtime.php';

        $savedGet = $_GET;
        $savedCwd = getcwd();

        try {
            if (!@chdir($projectRoot)) {
                return array('ok' => false, 'message' => 'Ne mogu chdir na korijen projekta.');
            }

            if (!class_exists('singleObject', false)) {
                require_once $projectRoot . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'getObject.php';
            }

            if (!function_exists('latinicaUCirilicu')) {
                function latinicaUCirilicu($tekst)
                {
                    return norma_backfill_latinica_u_cirilicu($tekst);
                }
            }

            $_GET['izvjestaj'] = $reportId;

            $runtimeCode = norma_backfill_prepare_zavod_runtime_code($template, $reportId);

            // Očisti state koji može ostati između izvještaja.
            unset(
                $finalusaglasenost,
                $GLOBALS['finalusaglasenost'],
                $mjernaVelicinaID,
                $incubatorForceTacnost,
                $pismo,
                $usaglasenost,
                $prvomjerenje,
                $drugomjerenje,
                $trecemjerenje
            );

            ob_start();
            try {
                if (@file_put_contents($runtimePath, $runtimeCode) !== false) {
                    include $runtimePath;
                } else {
                    // Fallback ako mpdf-includes nije writable: cwd = mpdf-includes da include('script…') radi.
                    if (!@chdir($mpdfDir)) {
                        throw new RuntimeException('Ne mogu chdir na mpdf-includes ni pisati runtime fajl.');
                    }
                    eval('?>' . $runtimeCode);
                }
            } finally {
                ob_end_clean();
            }

            $final = isset($finalusaglasenost) ? (string) $finalusaglasenost : '';
            $nisu = norma_backfill_finalusaglasenost_to_flag($final);

            return array(
                'ok'                => true,
                'nisu_usaglaseni'   => $nisu,
                'finalusaglasenost' => $final !== '' ? $final : '(nije postavljeno — tretirano kao usaglašeno)',
                'vrsta_id'          => $vrstaId,
                'message'           => 'Zavod zaključak: ' . ($final !== '' ? $final : 'испуњава (default)'),
            );
        } catch (Throwable $e) {
            return array('ok' => false, 'message' => 'Greška pri Zavod template-u: ' . $e->getMessage());
        } finally {
            $_GET = $savedGet;
            if ($savedCwd !== false) {
                @chdir($savedCwd);
            }
            if (isset($runtimePath) && is_file($runtimePath)) {
                @unlink($runtimePath);
            }
        }
    }
}

if (!function_exists('norma_backfill_compute_zavod_nisu_usaglaseni_isolated')) {
    /**
     * In-process (sigurno) — CLI više nije potreban.
     */
    function norma_backfill_compute_zavod_nisu_usaglaseni_isolated(
        PDO $pdo,
        int $reportId,
        ?string $projectRoot = null
    ): array {
        return norma_backfill_compute_zavod_nisu_usaglaseni($pdo, $reportId, $projectRoot);
    }
}

if (!function_exists('norma_backfill_apply_one_report')) {
    /**
     * @return array{ok:bool, report_id:int, old_value?:int, new_value?:int, finalusaglasenost?:string, vrsta_id?:int, message:string}
     */
    function norma_backfill_apply_one_report(PDO $pdo, int $reportId, bool $updateDb = true): array
    {
        $reportId = (int) $reportId;
        if ($reportId <= 0) {
            return array('ok' => false, 'report_id' => $reportId, 'message' => 'Nevaljan ID izvještaja.');
        }

        $stOld = $pdo->prepare('SELECT `izvjestaji_nisu_usaglaseni` FROM `izvjestaji` WHERE `izvjestaji_id` = ? LIMIT 1');
        $stOld->execute(array($reportId));
        $oldRow = $stOld->fetch(PDO::FETCH_ASSOC);
        if (!$oldRow) {
            return array('ok' => false, 'report_id' => $reportId, 'message' => 'Izvještaj #' . $reportId . ' ne postoji.');
        }

        $computed = norma_backfill_compute_zavod_nisu_usaglaseni($pdo, $reportId);
        if (empty($computed['ok'])) {
            return array(
                'ok'        => false,
                'report_id' => $reportId,
                'message'   => (string) ($computed['message'] ?? 'Rekalkulacija nije uspjela.'),
            );
        }

        $newVal = (int) ($computed['nisu_usaglaseni'] ?? 0);
        if ($updateDb) {
            $stUpd = $pdo->prepare('UPDATE `izvjestaji` SET `izvjestaji_nisu_usaglaseni` = ? WHERE `izvjestaji_id` = ? LIMIT 1');
            $stUpd->execute(array($newVal, $reportId));
        }

        return array(
            'ok'                => true,
            'report_id'         => $reportId,
            'old_value'         => (int) ($oldRow['izvjestaji_nisu_usaglaseni'] ?? 0),
            'new_value'         => $newVal,
            'finalusaglasenost' => (string) ($computed['finalusaglasenost'] ?? ''),
            'vrsta_id'          => (int) ($computed['vrsta_id'] ?? 0),
            'message'           => 'Staro=' . (int) ($oldRow['izvjestaji_nisu_usaglaseni'] ?? 0)
                . ', novo=' . $newVal
                . '. ' . (string) ($computed['message'] ?? ''),
        );
    }
}
