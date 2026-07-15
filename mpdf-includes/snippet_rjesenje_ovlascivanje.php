<?php
// Ako nije već učitano (npr. u _bata fajlovima), učitaj rješenje po datumu inspekcije
require_once __DIR__ . '/../includes/rjesenje_zakljucak_helper.php';

if (!isset($rjesenje_za_ovlascivanje) && isset($izvjestaj) && !empty($izvjestaj['izvjestaji_datuminspekcije'])) {
    global $pdo;
    if (isset($pdo) && $pdo instanceof PDO) {
        $rjesenje_za_ovlascivanje = norma_rjesenje_fetch_for_datum($pdo, $izvjestaj['izvjestaji_datuminspekcije']);
    }
}

$vrstaId = isset($vrstauredjaja['vrsteuredjaja_id']) ? (int) $vrstauredjaja['vrsteuredjaja_id'] : 0;
$noviZig = isset($izvjestaj['izvjestaji_novizig']) ? (string) $izvjestaj['izvjestaji_novizig'] : '';
$tekstZakljucka = norma_rjesenje_ispis_zakljucka(
    isset($rjesenje_za_ovlascivanje) && is_array($rjesenje_za_ovlascivanje) ? $rjesenje_za_ovlascivanje : null,
    $noviZig,
    $vrstaId
);
?>
<p style="text-align:justify;"><?php echo $tekstZakljucka; ?></p>
