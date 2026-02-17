<?php

include_once ('reports_head.php')

?>

<div class="main-content">
    <!-- NASLOV -->
    <h3 class="main-headline">ЗАПИСНИК О ПРЕГЛЕДУ<br>НЕОНАТАЛНИХ И ПЕДИЈАТРИЈСКИХ ИНКУБАТОРА</h3>

    <?php include_once("report_intro.php"); ?>

    <!-- PRAZAN RED -->
    <br>
    
    <!-- PREGLED MJERILA -->
    <p><strong>Испитивање тачности неонаталних и педијатријских инкубатора:</strong></p>

    <!-- #17 Temperatura zraka -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 17);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 17,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Температура зрака</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (℃)</th>
                <th colspan="3">Xm (℃)</th>
                <th rowspan="2">&lt;Xm&gt; (℃)</th>
                <th rowspan="2">ΔX (℃)</th>
                <th rowspan="2">Г (%)</th>
                <th rowspan="2">НДГ (℃)</th>
                <th rowspan="2">Усаглашеност</th>
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

    <!-- #18 Temperatura kože -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 18);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 18,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Температура коже</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (℃)</th>
                <th colspan="3">Xm (℃)</th>
                <th rowspan="2">&lt;Xm&gt; (℃)</th>
                <th rowspan="2">ΔX (℃)</th>
                <th rowspan="2">Г (%)</th>
                <th rowspan="2">НДГ (℃)</th>
                <th rowspan="2">Усаглашеност</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $mjernaVelicinaID = 18;
            include('script[one-hidden-two-not-measurable-absolute].php'); 
            ?>
        </tbody>
    </table>

    <br />

    <?php } ?>

    <!-- #19 Kiseonik -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 19);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 19,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Кисеоник</p>

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
                <th rowspan="2">Г (%)</th>
                <th rowspan="2">НДГ (Vol.%)</th>
                <th rowspan="2">Усаглашеност</th>
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

    <!-- #20 Relativna vlažnost -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 20);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 20,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Релативна влажност</p>

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
                <th rowspan="2">Г (%)</th>
                <th rowspan="2">НДГ (%)</th>
                <th rowspan="2">Усаглашеност</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
            </tr>
        </thead>
        <tbody>
            <?php include('script[one-hidden-two-not-measurable-relative].php'); ?>
        </tbody>
    </table>

    <br />

    <?php } ?>

    <!-- #21 Masa 0-2 kg -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 21);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 21,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Маса (0-2 [kg])</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (kg)</th>
                <th colspan="3">Xm (kg)</th>
                <th rowspan="2">&lt;Xm&gt; (kg)</th>
                <th rowspan="2">ΔX (kg)</th>
                <th rowspan="2">Г (%)</th>
                <th rowspan="2">НДГ (%)</th>
                <th rowspan="2">Усаглашеност</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
            </tr>
        </thead>
        <tbody>
            <?php include('script[one-shown-two-not-measurable-relative].php'); ?>
        </tbody>
    </table>

    <br />

    <?php } ?>

    <!-- #22 Masa 0-10 kg -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 22);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 22,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Маса (0-10 [kg])</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (kg)</th>
                <th colspan="3">Xm (kg)</th>
                <th rowspan="2">&lt;Xm&gt; (kg)</th>
                <th rowspan="2">ΔX (kg)</th>
                <th rowspan="2">Г (%)</th>
                <th rowspan="2">НДГ (%)</th>
                <th rowspan="2">Усаглашеност</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
            </tr>
        </thead>
        <tbody>
            <?php include('script[one-shown-two-not-measurable-relative].php'); ?>
        </tbody>
    </table>

    <br />

    <?php } ?>
    
    <h4 class="second-headline"><strong>ЗАКЉУЧАК:</strong></h4>
    <p style="text-align:justify;">Прегледом мјерила утврђено да мјерило <input type="checkbox" <?php if($finalusaglasenost == "испуњава"){ echo "checked='true'";} ?>> <strong>исуњава</strong> <input type="checkbox" <?php if($finalusaglasenost == "не испуњава"){ echo "checked='true'";}?>> <strong>не испуњава</strong> метролошке захтјеве прописане Правилником о верификацији неонаталних и педијатријских инкубатора („Службени гласник Републике Српске“, број 98/23) и на основу члана 20. Закона о метрологији Републике Српске („Службени гласник Републике Српске“, број 132/22 и 100/25) и члана 10. Правилника о верификацији мјерила („Службени гласник Републике Српске“, број 61/14), сачињен је овај записник.</p>
    <?php include(__DIR__ . '/snippet_rjesenje_ovlascivanje.php'); ?>

    <br />

    <h4 class="second-headline">Напомена:</h4>
    <p style="text-align:justify;"><?php echo latinicaUCirilicu($izvjestaj['izvjestaji_napomena']); ?></p>

    <br /><br /><br />

    <div style="">
        <p style="text-align: center;margin: 0 10% 0 70%;">Преглед извршио</p>
        <p style="text-align: center;margin: 0 7% 0 67%;border-bottom: 1px solid #000000;"><?php echo latinicaUCirilicu($mjerenjeizvrsio['kontrolori_ime'])." ".latinicaUCirilicu($mjerenjeizvrsio['kontrolori_prezime']); ?></p>
        <p style="text-align: center;margin: 0 10% 0 70%;">(име и презиме)</p>
    </div>
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