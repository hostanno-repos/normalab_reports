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
    <!--<p><strong>Испитивање тачности пацијент монитора:</strong></p>-->

    <!-- #3 Brzina otkucaja u vremenskom intervalu od 1 minut -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 3);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 3,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Broj otkucaja srca u vremenskom intervalu od 1 minut</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs [BPM]</th>
                <th colspan="3">Xm [BPM]</th>
                <th rowspan="2">&lt;Xm&gt; [BPM]</th>
                <th rowspan="2">ΔX [BPM]</th>
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
            <?php 
            $pismo = "LAT";
            include('script[one-hidden-two-not-measurable-relative].php'); ?>
        </tbody>
    </table>

    <br />

    <?php } ?>

    <!-- #4 Amplituda naponskog signala -->
    <p style="text-align:center;">Amplituda naponskog signala</p>
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 4);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 4,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');
    
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs [mV]</th>
                <th colspan="3">Xm [mV]</th>
                <th rowspan="2">&lt;Xm&gt; [mV]</th>
                <th rowspan="2">ΔX [mV]</th>
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
            <?php 
            $pismo = "LAT";
            include('script[one-shown-two-not-measurable-relative].php'); ?>
        </tbody>
    </table>

    <br />

    <!-- #5 Respiracija -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 5);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 5,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Respiracija</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs [BRPM]</th>
                <th colspan="3">Xm [BRPM]</th>
                <th rowspan="2">&lt;Xm&gt; [BRPM]</th>
                <th rowspan="2">ΔX [BRPM]</th>
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
            <?php 
            $pismo = "LAT";
            include('script[one-hidden-two-not-measurable-relative].php'); ?>
        </tbody>
    </table>

    <br />

    <?php } ?>

    <!-- #151 Neinvazivni krvni pritisak - Sistolni -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 151);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 151,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Neinvazivni krvni pritisak - sistolni</p>

    <?php

    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs [mmHG]</th>
                <th colspan="3">Xm [mmHG]</th>
                <th rowspan="2">&lt;Xm&gt; [mmHG]</th>
                <th rowspan="2">ΔX [mmHG]</th>
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
            <?php 
            $pismo = "LAT";
            include('script[one-hidden-two-not-measurable-absolute].php'); ?>
        </tbody>
    </table>
    
    <br />
    <?php } ?>

    <!-- #152 Neinvazivni krvni pritisak - Sistolni -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 152);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 152,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Neinvazivni krvni pritisak - dijastolni</p>

    <?php

    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (mmHG)</th>
                <th colspan="3">Xm (mmHG)</th>
                <th rowspan="2">&lt;Xm&gt; (mmHG)</th>
                <th rowspan="2">ΔX (mmHG)</th>
                <th rowspan="2">δ (%)</th>
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
            <?php include('script[one-hidden-two-not-measurable-absolute].php'); ?>
        </tbody>
    </table>
    
    <br />

    <?php } ?>

    <!-- #157 Invazivni krvni pritisak -->
    <p style="text-align:center;">Invazivni krvni pritisak</p>
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 7);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 7,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (mmHG)</th>
                <th colspan="3">Xm (mmHG)</th>
                <th rowspan="2">&lt;Xm&gt; (mmHG)</th>
                <th rowspan="2">ΔX (mmHG)</th>
                <th rowspan="2">δ (%)</th>
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
            <?php include('script[one-shown-two-not-measurable-absolute].php'); ?>
        </tbody>
    </table>
    
    <br />

    <!-- #8 Temperatura -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 8);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 8, 'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Temperatura</p>

    <?php

    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (°C)</th>
                <th colspan="3">Xm (°C)</th>
                <th rowspan="2">&lt;Xm&gt; (°C)</th>
                <th rowspan="2">ΔX (°C)</th>
                <th rowspan="2">δ (%)</th>
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
            <?php 
            $mjernaVelicinaID = 8;
            include('script[one-hidden-two-not-measurable-absolute].php'); 
            ?>
        </tbody>
    </table>
    
    <br />

    <?php } ?>

    <!-- #9 Saturacija -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 9);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 9,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

        <p style="text-align:center;">Saturacija SpO2</p>

        <?php

        //REFERENTNE VRIJEDNOSTI
        $referentnevrijednosti = new allObjectsBy;
        $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
        ?>
        <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
            <thead>
                <tr>
                    <th rowspan="2">Xs (%)</th>
                    <th colspan="3">Xm (%)</th>
                    <th rowspan="2">&lt;Xm&gt; (%)</th>
                    <th rowspan="2">ΔX (%)</th>
                    <th rowspan="2">δ (%)</th>
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
                <?php include('script[one-hidden-two-not-measurable-absolute].php'); ?>
            </tbody>
        </table>
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
    .red-bez-mjerenja td{
        text-align: center;
    }
</style>