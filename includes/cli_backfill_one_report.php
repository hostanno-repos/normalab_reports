<?php
/**
 * CLI worker: jedan izvještaj → JSON sa Zavod usaglašenošću.
 * Pokretanje: php includes/cli_backfill_one_report.php <izvjestaji_id>
 *
 * Odvojen proces sprječava fatal (Cannot redeclare function u mpdf-includes/49.php itd.)
 * da sruši cijeli backfill chunk.
 */
$root = dirname(__DIR__);
@chdir($root);

$_SERVER['REQUEST_METHOD'] = 'CLI';

require $root . DIRECTORY_SEPARATOR . 'connection.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'norma_backfill_zavod_usaglasenost.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    echo json_encode(array('ok' => false, 'message' => 'Nema PDO konekcije.'), JSON_UNESCAPED_UNICODE);
    exit(1);
}

$reportId = isset($argv[1]) ? (int) $argv[1] : 0;
if ($reportId <= 0) {
    echo json_encode(array('ok' => false, 'message' => 'Nedostaje izvjestaji_id.'), JSON_UNESCAPED_UNICODE);
    exit(1);
}

$result = norma_backfill_compute_zavod_nisu_usaglaseni($pdo, $reportId, $root);
echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit(!empty($result['ok']) ? 0 : 1);
