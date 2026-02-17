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

//FETCH INSPEKCIJU IZVRŠIO
$inspekcijuizvrsio = new singleObject;
$inspekcijuizvrsio = $inspekcijuizvrsio->fetch_single_object("kontrolori", "kontrolori_id", $izvjestaj['izvjestaji_izvrsioid']);

//FETCH INSPEKCIJU OVJERIO
$inspekcijuovjerio = new singleObject;
$inspekcijuovjerio = $inspekcijuovjerio->fetch_single_object("kontrolori", "kontrolori_id", $izvjestaj['izvjestaji_ovjerioid']);

?>

<div class="main-content">
    
    <!-- PREGLED MJERILA -->
    <!--<p><strong>Испитивање тачности инфузомата:</strong></p>-->

    <!-- #16 Protok -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 16);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 16,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Protok</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs [ml/h]</th>
                <th colspan="3">Xm [ml/h]</th>
                <th rowspan="2">&lt;Xm&gt; [ml/h]</th>
                <th rowspan="2">ΔX [ml/h]</th>
                <th rowspan="2">δ [%]</th>
                <th rowspan="2">Dozvoljeno odstupanje</th>
                <th rowspan="2">Usaglašenost</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
            </tr>
        </thead>
        <tbody>
            <?php $pismo = "LAT"; include('script[one-hidden-two-not-measurable-relative].php'); ?>
        </tbody>
    </table>

    <br />

    <?php } ?>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    .main-headline{
        text-align: center;
        font-size: 12px;
    }
    .second-headline{
        font-size: 12px;
    }
    .main-content p{
        margin:2px 0;
        font-size: 12px;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }

    table, th, td {
        border: 1px solid #000;
        font-size: 12px;
    }
    table.rezultati-otkucaji{
        table-layout: fixed;
    }
    table.rezultati-otkucaji, table.rezultati-otkucaji th, table.rezultati-otkucaji td{
        width: 35%;
        word-wrap: break-word;
    }
    .verifikacija-vrsta-verifikacije,
    .verifikacija-radni-uslovi,
    .podaci-etalon,
    .pregled-mjerila{
       margin:2px 0;
       padding: 0;
       line-height: 12px;
    }
    .verifikacija-republicki-zig{
       margin:2px 0;
       padding: 0;
       line-height: 12px;
    }
    .verifikacija-vrsta-verifikacije p,
    .verifikacija-radni-uslovi p{
        float: left;
        width:25%;
        margin:2px 0;
    }
    .verifikacija-republicki-zig p{
        float: left;
        margin:2px 0;
    }
    .podaci-etalon p{
        float: left;
        width:33%;
        margin:2px 0;
    }
    .pregled-mjerila p{
        float: left;
        width:50%;
        margin:2px 0;
    }
</style>