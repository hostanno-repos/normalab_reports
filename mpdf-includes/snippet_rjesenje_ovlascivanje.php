<?php
// Ako nije već učitano (npr. u _bata fajlovima), učitaj rješenje po datumu inspekcije
if (!isset($rjesenje_za_ovlascivanje) && isset($izvjestaj) && !empty($izvjestaj['izvjestaji_datuminspekcije'])) {
    global $pdo;
    $stmtRj = $pdo->prepare("SELECT * FROM rjesenjazaovlascivanje WHERE rjesenjazaovlascivanje_datum_izdavanja <= ? ORDER BY rjesenjazaovlascivanje_datum_izdavanja DESC LIMIT 1");
    $stmtRj->execute(array($izvjestaj['izvjestaji_datuminspekcije']));
    $rjesenje_za_ovlascivanje = $stmtRj->fetch(PDO::FETCH_ASSOC);
}
// Ispis broja i datuma rješenja o ovlašćivanju (dinamički ili fallback na staru vrijednost)
$rjesenje_broj = ($rjesenje_za_ovlascivanje && !empty($rjesenje_za_ovlascivanje['rjesenjazaovlascivanje_broj_rjesenja'])) ? $rjesenje_za_ovlascivanje['rjesenjazaovlascivanje_broj_rjesenja'] : '18/1.10/393.10-03-09-25/25';
$rjesenje_datum = ($rjesenje_za_ovlascivanje && !empty($rjesenje_za_ovlascivanje['rjesenjazaovlascivanje_datum_izdavanja'])) ? date('d.m.Y.', strtotime($rjesenje_za_ovlascivanje['rjesenjazaovlascivanje_datum_izdavanja'])) : '30.12.2025.';
?>
<p style="text-align:justify;">Резултати инспекције се односе искључиво на дати предмет у тренутку инспекције. На основу Рјешења о измјени и допуни рјешења о овлашћивању тијела за верификацију мјерила број <?php echo $rjesenje_broj; ?> од <?php echo $rjesenje_datum; ?> године, на мјерило је постављен републички жиг у облику наљепнице број: <?php echo $izvjestaj["izvjestaji_novizig"];?>.</p>
