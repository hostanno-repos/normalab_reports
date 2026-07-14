<?php
/**
 * Backfill kolone izvjestaji_nisu_usaglaseni — ista logika kao Zavod PDF (izvjestajmpdf.php + mpdf-includes).
 * Ne mijenja mpdf fajlove; samo ih učitava u headless modu i čita $finalusaglasenost.
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
        $tekst = str_replace(
            ['Ǆ', 'ǆ', 'Ǉ', 'ǉ', 'Ǌ', 'ǌ'],
            ['Џ', 'џ', 'Љ', 'љ', 'Њ', 'њ'],
            $tekst
        );

        return $tekst;
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

if (!function_exists('norma_backfill_compute_zavod_nisu_usaglaseni')) {
    /**
     * Pokreće isti mpdf-includes/{vrsta}.php kao izvjestajmpdf.php i vraća flag za kolonu.
     *
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

        $savedGet = $_GET;
        $savedCwd = getcwd();
        $savedFinal = $GLOBALS['finalusaglasenost'] ?? null;

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

            unset($finalusaglasenost);
            $_GET['izvjestaj'] = $reportId;

            ob_start();
            try {
                include $template;
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
            if ($savedFinal === null) {
                unset($GLOBALS['finalusaglasenost']);
            } else {
                $GLOBALS['finalusaglasenost'] = $savedFinal;
            }
        }
    }
}

if (!function_exists('norma_backfill_compute_zavod_nisu_usaglaseni_isolated')) {
    /**
     * Računa usaglašenost u odvojenom PHP CLI procesu (sigurno od redeclare fatals u mpdf templateima).
     *
     * @return array{ok:bool,nisu_usaglaseni?:int,finalusaglasenost?:string,vrsta_id?:int,message?:string}
     */
    function norma_backfill_compute_zavod_nisu_usaglaseni_isolated(
        PDO $pdo,
        int $reportId,
        ?string $projectRoot = null
    ): array {
        $projectRoot = $projectRoot ?? dirname(__DIR__);
        $script = __DIR__ . DIRECTORY_SEPARATOR . 'cli_backfill_one_report.php';
        if (!is_readable($script)) {
            return array('ok' => false, 'message' => 'Nedostaje cli_backfill_one_report.php');
        }

        $phpBin = defined('PHP_BINARY') && PHP_BINARY !== '' ? PHP_BINARY : 'php';
        if (!function_exists('shell_exec') || in_array('shell_exec', array_map('trim', explode(',', (string) ini_get('disable_functions'))), true)) {
            // Bez CLI izolacije: rizik fatala na mpdf 49/50.php (Cannot redeclare).
            return norma_backfill_compute_zavod_nisu_usaglaseni($pdo, $reportId, $projectRoot);
        }

        $cmd = escapeshellarg($phpBin) . ' ' . escapeshellarg($script) . ' ' . (int) $reportId;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cmd .= ' 2>NUL';
        } else {
            $cmd .= ' 2>/dev/null';
        }

        $out = @shell_exec($cmd);
        if ($out === null || trim((string) $out) === '') {
            // Fallback: isti proces (rizik od redeclare fatals na 49/50)
            return norma_backfill_compute_zavod_nisu_usaglaseni($pdo, $reportId, $projectRoot);
        }

        $decoded = json_decode(trim((string) $out), true);
        if (!is_array($decoded) || !array_key_exists('ok', $decoded)) {
            return array(
                'ok'      => false,
                'message' => 'CLI worker nije vratio validan JSON za #' . $reportId . '. Output: ' . substr(trim((string) $out), 0, 200),
            );
        }

        return $decoded;
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

        $computed = norma_backfill_compute_zavod_nisu_usaglaseni_isolated($pdo, $reportId);
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
