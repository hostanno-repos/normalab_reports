<?php
include 'cred.php';
try {
    $pdo = new PDO('mysql:host=localhost;dbname=' . $database, $username, $password, [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
} catch (Throwable $e) {
    die('DB: ' . $e->getMessage());
}

$id = 2190;
$r = $pdo->query("SELECT i.*, rn.radninalozi_vrstauredjajaid, rn.radninalozi_broj, v.vrsteuredjaja_naziv
    FROM izvjestaji i
    LEFT JOIN radninalozi rn ON rn.radninalozi_id = i.izvjestaji_radninalogid
    LEFT JOIN mjerila m ON m.mjerila_id = i.izvjestaji_mjeriloid
    LEFT JOIN vrsteuredjaja v ON v.vrsteuredjaja_id = rn.radninalozi_vrstauredjajaid
    WHERE i.izvjestaji_id = $id")->fetch(PDO::FETCH_ASSOC);
echo "=== IZVJESTAJ $id ===\n";
print_r($r);

echo "\n=== REZULTATI (numeric) ===\n";
$rows = $pdo->query("SELECT rm.rezultatimjerenja_mjernavelicinaid AS mv, mv.mjernevelicine_naziv,
    rm.rezultatimjerenja_referentnavrijednostid AS ref, rv.referentnevrijednosti_referentnavrijednost AS xs,
    rv.referentnevrijednosti_odstupanje AS dozv,
    rm.rezultatimjerenja_brojmjerenja AS br, rm.rezultatimjerenja_rezultatmjerenja AS val
    FROM rezultatimjerenja rm
    LEFT JOIN mjernevelicine mv ON mv.mjernevelicine_id = rm.rezultatimjerenja_mjernavelicinaid
    LEFT JOIN referentnevrijednosti rv ON rv.referentnevrijednosti_id = rm.rezultatimjerenja_referentnavrijednostid
    WHERE rm.rezultatimjerenja_izvjestajid = $id
    ORDER BY mv, ref, br")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
