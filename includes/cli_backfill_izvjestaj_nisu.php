<?php
/**
 * Jedan izvještaj: pokrene pregledizvjestajapdf.php u režimu backfilla (bez slanja PDF-a u preglednik),
 * što upisuje izvjestaji_nisu_usaglaseni iz iste logike kao PDF.
 *
 * Primjer: php includes/cli_backfill_izvjestaj_nisu.php 123
 */
$root = dirname(__DIR__);
chdir($root);

putenv('NORMA_SETUP_BACKFILL=1');
$_GET['izvjestaj'] = (int)($argv[1] ?? 0);
if ($_GET['izvjestaj'] < 1) {
    fwrite(STDERR, "Korištenje: php includes/cli_backfill_izvjestaj_nisu.php <izvjestaji_id>\n");
    exit(1);
}

require $root . '/pregledizvjestajapdf.php';
