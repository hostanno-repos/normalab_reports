<?php

echo '<sethtmlpageheader name="header" value="on" show-this-page="1" />';
//echo '<sethtmlpagefooter name="footer" value="on" show-this-page="1" />';

include_once('./connection.php');
include_once('./class/getObject.php');

//FETCH IZVJEŠTAJ
$izvjestaj = new singleObject;
$izvjestaj = $izvjestaj->fetch_single_object('izvjestaji', 'izvjestaji_id', $_GET['izvjestaj']);

//FETCH MJERITELJ
$mjerenjeizvrsio = new singleObject;
$mjerenjeizvrsio = $mjerenjeizvrsio->fetch_single_object('kontrolori', 'kontrolori_id', $izvjestaj['izvjestaji_izvrsioid']);

//FETCH RADNI NALOG
$radninalog = new singleObject;
$radninalog = $radninalog->fetch_single_object('radninalozi', 'radninalozi_id', $izvjestaj['izvjestaji_radninalogid']);

//FETCH KLIJENT
$klijent = new singleObject;
$klijent = $klijent->fetch_single_object('klijenti', 'klijenti_id', $radninalog['radninalozi_klijentid']);

//FETCH MJERILO
$mjerilo = new singleObject;
$mjerilo = $mjerilo->fetch_single_object('mjerila', 'mjerila_id', $radninalog['radninalozi_mjeriloid']);

//FETCH VRSTA UREĐAJA
$vrstauredjaja = new singleObject;
$vrstauredjaja = $vrstauredjaja->fetch_single_object('vrsteuredjaja', 'vrsteuredjaja_id', $mjerilo['mjerila_vrstauredjajaid']);

//FETCH VRSTE INSPEKCIJE
$vrsteinspekcije = new allObjects;
$vrsteinspekcije = $vrsteinspekcije->fetch_all_objects('vrsteinspekcije', 'vrsteinspekcije_id', 'ASC');

// Rješenje o ovlašćivanju: ono čiji je datum izdavanja <= datum inspekcije izvještaja (najnovije takvo)
$datumInspekcije = !empty($izvjestaj['izvjestaji_datuminspekcije']) ? $izvjestaj['izvjestaji_datuminspekcije'] : '9999-12-31';
$stmtRjesenje = $pdo->prepare("SELECT * FROM rjesenjazaovlascivanje WHERE rjesenjazaovlascivanje_datum_izdavanja <= ? ORDER BY rjesenjazaovlascivanje_datum_izdavanja DESC LIMIT 1");
$stmtRjesenje->execute(array($datumInspekcije));
$rjesenje_za_ovlascivanje = $stmtRjesenje->fetch(PDO::FETCH_ASSOC);

?>