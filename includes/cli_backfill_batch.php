<?php
/**
 * CLI: backfill kolone izvjestaji_nisu_usaglaseni — svi izvještaji u jednom pokretanju.
 *
 * Ručno: php includes/cli_backfill_batch.php
 */
$root = dirname(__DIR__);
chdir($root);

$_SERVER['REQUEST_METHOD'] = 'CLI';
require $root . '/connection.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    fwrite(STDERR, "Nema PDO konekcije.\n");
    exit(1);
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'run_backfill_nisu_usaglaseni.php';

$chunkSize = 100;
$offset = 0;
$total = (int) $pdo->query('SELECT COUNT(*) FROM `izvjestaji`')->fetchColumn();

fwrite(STDERR, "Backfill izvjestaji_nisu_usaglaseni (ukupno {$total} izvještaja, chunk {$chunkSize})…\n");

do {
    $r = norma_run_backfill_nisu_usaglaseni($pdo, function ($current, $totalReports, $reportId) {
        fwrite(STDERR, '  ID ' . (int) $reportId . ' (' . (int) $current . '/' . (int) $totalReports . ")\n");
    }, $chunkSize, $offset);

    if (!$r['ok']) {
        fwrite(STDERR, "\nGREŠKA: " . $r['message'] . "\n");
        exit(1);
    }

    fwrite(STDERR, $r['message'] . "\n");
    $offset = (int) ($r['next_offset'] ?? $offset);
    $done = !empty($r['done']);
} while (!$done);

fwrite(STDERR, "\nBackfill završen.\n");
exit(0);
