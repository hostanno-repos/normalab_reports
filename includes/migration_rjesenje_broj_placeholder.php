<?php
/**
 * Zamjenjuje konkretan broj rješenja placeholderom u već sačuvanim tekstovima.
 *
 * @return array{ok:bool,message:string}
 */
if (!isset($pdo) || !($pdo instanceof PDO)) {
    return array('ok' => false, 'message' => 'Nema valjane PDO konekcije.');
}

$chk = $pdo->query(
    "SHOW COLUMNS FROM `rjesenjazaovlascivanje` LIKE 'rjesenjazaovlascivanje_tekst_zakljucka'"
)->fetch(PDO::FETCH_ASSOC);
if (!$chk) {
    return array('ok' => false, 'message' => 'Kolona tekst_zakljucka ne postoji.');
}

$rows = $pdo->query(
    'SELECT `rjesenjazaovlascivanje_id`,
            `rjesenjazaovlascivanje_broj_rjesenja`,
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
    $broj = trim((string) ($row['rjesenjazaovlascivanje_broj_rjesenja'] ?? ''));
    if ($broj === '') {
        continue;
    }

    $tekst = (string) ($row['rjesenjazaovlascivanje_tekst_zakljucka'] ?? '');
    $tekstVage = (string) ($row['rjesenjazaovlascivanje_tekst_zakljucka_vage'] ?? '');
    $noviTekst = str_replace($broj, '{{BROJRJESENJA}}', $tekst);
    $noviTekstVage = str_replace($broj, '{{BROJRJESENJA}}', $tekstVage);

    if ($noviTekst === $tekst && $noviTekstVage === $tekstVage) {
        continue;
    }

    $update->execute(array($noviTekst, $noviTekstVage, (int) $row['rjesenjazaovlascivanje_id']));
    $n++;
}

return array(
    'ok' => true,
    'message' => 'Placeholder broja rješenja: ažurirano ' . $n . ' rješenja.',
);
