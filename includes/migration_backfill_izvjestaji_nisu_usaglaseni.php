<?php
/**
 * Poziva se iz setup.php (migrations stavka s ključem "php"). Očekuje $pdo.
 * Backfill u istom PHP procesu kao setup (brzo + callback za live status u setup.php).
 *
 * @return array{ok:bool,message:string}
 */
if (!isset($pdo) || !($pdo instanceof PDO)) {
    return array('ok' => false, 'message' => 'Nema valjane PDO konekcije.');
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'run_backfill_nisu_usaglaseni.php';

$cb = null;
if (isset($GLOBALS['norma_setup_progress_callback']) && is_callable($GLOBALS['norma_setup_progress_callback'])) {
    $cb = $GLOBALS['norma_setup_progress_callback'];
}

$limit = (int)($GLOBALS['norma_setup_backfill_chunk_limit'] ?? 50);
$offset = (int)($GLOBALS['norma_setup_backfill_chunk_offset'] ?? 0);

return norma_run_backfill_nisu_usaglaseni($pdo, $cb, $limit, $offset);
