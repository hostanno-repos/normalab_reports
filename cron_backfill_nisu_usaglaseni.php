<?php
/**
 * Backfill kolone izvjestaji_nisu_usaglaseni — samo CLI (cron), bez Apache timeouta.
 *
 * Produkcija (Linux, primjer crontaba — prilagodi putanje i PHP):
 *   0 3 * * 0 cd /var/www/normalab_reports && /usr/bin/php cron_backfill_nisu_usaglaseni.php >> tmp/cron_backfill_nisu.log 2>&1
 *
 * - Idempotentno: možeš ponavljati, prepisuje iste vrijednosti.
 * - Ne dira setup_migrations: ovo je operativni posao, ne migracija sheme.
 * - Ako već radi drugi proces, izlaz 2 (flock).
 * - Izlaz 0 = OK, 1 = greška (poruka u logu i STDERR).
 *
 * MySQL procedura za ovaj zaključak nije praktična: ista logika kao u pregledizvjestajapdf.php (grane po vrsti uređaja).
 */
if (PHP_SAPI !== 'cli') {
    header('HTTP/1.0 403 Forbidden');
    exit('CLI only.');
}

$root = __DIR__;
chdir($root);

require $root . '/connection.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    fwrite(STDERR, "Nema PDO konekcije (provjeri cred.php).\n");
    exit(1);
}

$tmpDir = $root . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmpDir)) {
    @mkdir($tmpDir, 0775, true);
}

$lockPath = $tmpDir . DIRECTORY_SEPARATOR . 'cron_backfill_nisu.lock';
$logPath = $tmpDir . DIRECTORY_SEPARATOR . 'cron_backfill_nisu.log';

$log = function ($msg) use ($logPath) {
    $line = date('c') . ' ' . $msg . "\n";
    fwrite(STDERR, $line);
    @file_put_contents($logPath, $line, FILE_APPEND | LOCK_EX);
};

$lockFp = @fopen($lockPath, 'c');
if ($lockFp === false) {
    $log('Ne mogu otvoriti lock datoteku: ' . $lockPath);
    exit(1);
}
if (!flock($lockFp, LOCK_EX | LOCK_NB)) {
    fwrite(STDERR, date('c') . " Cron backfill već radi (lock). Izlaz 2.\n");
    fclose($lockFp);
    exit(2);
}

require_once $root . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'run_backfill_nisu_usaglaseni.php';

$log('Pokretanje backfill-a izvjestaji_nisu_usaglaseni…');

try {
    $r = norma_run_backfill_nisu_usaglaseni($pdo, function ($current, $total, $reportId) use ($log) {
        if ($current === 1 || $current === $total || $current % 50 === 0) {
            $log("Napredak: {$current}/{$total} (zadnji ID {$reportId})");
        }
    });

    $log($r['ok'] ? ('Završeno: ' . $r['message']) : ('GREŠKA: ' . $r['message']));

    exit($r['ok'] ? 0 : 1);
} finally {
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
}
