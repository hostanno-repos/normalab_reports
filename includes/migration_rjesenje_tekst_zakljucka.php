<?php
/**
 * Backfill tekst_zakljucka i tekst_zakljucka_vage za postojeća rješenja.
 *
 * @return array{ok:bool,message:string}
 */
if (!isset($pdo) || !($pdo instanceof PDO)) {
    return array('ok' => false, 'message' => 'Nema valjane PDO konekcije.');
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'rjesenje_zakljucak_helper.php';

$chk = $pdo->query("SHOW COLUMNS FROM `rjesenjazaovlascivanje` LIKE 'rjesenjazaovlascivanje_tekst_zakljucka'")->fetch(PDO::FETCH_ASSOC);
if (!$chk) {
    return array('ok' => false, 'message' => 'Kolona tekst_zakljucka ne postoji.');
}

$rows = $pdo->query('SELECT * FROM `rjesenjazaovlascivanje`')->fetchAll(PDO::FETCH_ASSOC);
$st = $pdo->prepare(
    'UPDATE `rjesenjazaovlascivanje`
     SET `rjesenjazaovlascivanje_tekst_zakljucka` = ?,
         `rjesenjazaovlascivanje_tekst_zakljucka_vage` = ?
     WHERE `rjesenjazaovlascivanje_id` = ?
     LIMIT 1'
);

$n = 0;
foreach ($rows as $row) {
    $tekst = norma_rjesenje_default_tekst_zakljucka('{{BROJRJESENJA}}', '{{DATUMRJESENJA}}');
    $st->execute(array($tekst, $tekst, (int) $row['rjesenjazaovlascivanje_id']));
    $n++;
}

return array(
    'ok'      => true,
    'message' => 'Backfill teksta zaključka: ažurirano ' . $n . ' rješenja.',
);
