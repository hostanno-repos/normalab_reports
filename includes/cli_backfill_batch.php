<?php
/**
 * CLI: backfill kolone izvjestaji_nisu_usaglaseni (jedan proces, ista logika kao PDF).
 *
 * Ručno: php includes/cli_backfill_batch.php
 */
$root = dirname(__DIR__);
chdir($root);

require $root . '/connection.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    fwrite(STDERR, "Nema PDO konekcije.\n");
    exit(1);
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'run_backfill_nisu_usaglaseni.php';

$r = norma_run_backfill_nisu_usaglaseni($pdo, function ($current, $total, $reportId) {
    fwrite(STDERR, 'Gotov izvještaj ID ' . (int) $reportId . ' (' . (int) $current . '/' . (int) $total . ").\n");
});

fwrite(STDERR, "\n");
echo $r['message'] . "\n";
exit($r['ok'] ? 0 : 1);
