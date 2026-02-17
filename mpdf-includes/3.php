<?php

include_once ('reports_head.php')

?>

<div class="main-content">
    <!-- NASLOV -->
    <h3 class="main-headline">ЗАПИСНИК О ПРЕГЛЕДУ<br>ДЕФИБРИЛАТОРА</h3>

    <?php include_once("report_intro.php"); ?>

    <!-- PRAZAN RED -->
    <br>
    
    <!-- PREGLED MJERILA -->
    <p><strong>Испитивање тачности дефибрилатора:</strong></p>

    <!-- #10 Izlazna energija -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 10);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 10,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Излазна енергија</p>

    <?php

    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (J)</th>
                <th colspan="3">Xm (J)</th>
                <th rowspan="2">&lt;Xm&gt; (J)</th>
                <th rowspan="2">ΔX (J)</th>
                <th rowspan="2">Г (%)</th>
                <th rowspan="2">НДГ (J)</th>
                <th rowspan="2">Усаглашеност</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
            </tr>
        </thead>
        <tbody>
            <?php include('script[one-hidden-two-not-measurable-absolute-up-to-100].php'); ?>
        </tbody>
    </table>

    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (J)</th>
                <th colspan="3">Xm (J)</th>
                <th rowspan="2">&lt;Xm&gt; (J)</th>
                <th rowspan="2">ΔX (J)</th>
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
            <?php include('script[one-hidden-two-not-measurable-relative-over-100].php'); ?>
        </tbody>
    </table>
    
    <br />

    <?php } ?>
    
    <h4 class="second-headline"><strong>ЗАКЉУЧАК:</strong></h4>
    <p style="text-align:justify;">Прегледом мјерила утврђено да мјерило <input type="checkbox" <?php if($finalusaglasenost == "испуњава"){ echo "checked='true'";} ?>> <strong>исуњава</strong> <input type="checkbox" <?php if($finalusaglasenost == "не испуњава"){ echo "checked='true'";}?>> <strong>не испуњава</strong> метролошке захтјеве прописане Правилником о верификацији дефибрилатора („Службени гласник Републике Српске“, број 98/23) и на основу члана 20. Закона о метрологији Републике Српске („Службени гласник Републике Српске“, број 132/22 и 100/25) и члана 10. Правилника о верификацији мјерила („Службени гласник Републике Српске“, број 61/14), сачињен је овај записник.</p>
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

<?php include_once('reports_styles.php'); ?>