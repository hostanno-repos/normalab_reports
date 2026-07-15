<?php
/**
 * Zamjenjuje konkretan datum rješenja placeholderom u već sačuvanim tekstovima.
 *
 * @return array{ok:bool,message:string}
 */
if (!isset($pdo) || !($pdo instanceof PDO)) {
    return array('ok' => false, 'message' => 'Nema valjane PDO konekcije.');
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'rjesenje_zakljucak_helper.php';

$chk = $pdo->query(
    "SHOW COLUMNS FROM `rjesenjazaovlascivanje` LIKE 'rjesenjazaovlascivanje_tekst_zakljucka'"
)->fetch(PDO::FETCH_ASSOC);
if (!$chk) {
    return array('ok' => false, 'message' => 'Kolona tekst_zakljucka ne postoji.');
}

$rows = $pdo->query(
    'SELECT `rjesenjazaovlascivanje_id`,
            `rjesenjazaovlascivanje_datum_izdavanja`,
            `rjesenjazaovlascivanje_tekst_zakljucka`,
            `rjesenjazaovlascivanje_tekst_zakljucka_vage`
     FROM `rjesenjazaovlascivanje`'
)->fetchAll(PDO::FETCH_ASSOC);

$update = $pdo->prepare(
    'UPDATE `rjesenjazaovlascivanje`
     SET `rjesenjazaovlascivanje_tekst_zakljucka` = ?,
         `rjesenjazaovlascivanje_tekst_zakljucka_vage` = ?
     WHERE `rjesenjazaovlascivanje_id` = ?
     LIMIT 1'
);

$n = 0;
foreach ($rows as $row) {
    $datum = norma_rjesenje_format_datum($row['rjesenjazaovlascivanje_datum_izdavanja'] ?? null);
    $tekst = (string) ($row['rjesenjazaovlascivanje_tekst_zakljucka'] ?? '');
    $tekstVage = (string) ($row['rjesenjazaovlascivanje_tekst_zakljucka_vage'] ?? '');
    $noviTekst = str_replace($datum, '{{DATUMRJESENJA}}', $tekst);
    $noviTekstVage = str_replace($datum, '{{DATUMRJESENJA}}', $tekstVage);

    if ($noviTekst === $tekst && $noviTekstVage === $tekstVage) {
        continue;
    }

    $update->execute(array($noviTekst, $noviTekstVage, (int) $row['rjesenjazaovlascivanje_id']));
    $n++;
}

return array(
    'ok' => true,
    'message' => 'Placeholder datuma rješenja: ažurirano ' . $n . ' rješenja.',
);
