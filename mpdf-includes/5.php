<?php

include_once ('reports_head.php')

?>

<div class="main-content">
    <!-- NASLOV -->
    <h3 class="main-headline">ЗАПИСНИК О ПРЕГЛЕДУ<br>АНЕСТЕЗИОЛОШКЕ МАШИНЕ</h3>

    <?php include_once("report_intro.php"); ?>

    <!-- PRAZAN RED -->
    <br>
    
    <!-- PREGLED MJERILA -->
    <p><strong>Испитивање тачности анестезиолошке машине:</strong></p>

    <!-- #12 Protok -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 12);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 12,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Проток</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (l/min)</th>
                <th colspan="3">Xm (l/min)</th>
                <th rowspan="2">&lt;Xm&gt; (l/min)</th>
                <th rowspan="2">ΔX (l/min)</th>
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
    <br /><br /><br /><br /><br />
    <?php } ?>

    <!-- #13 Izlazni pritisak respiratora -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 13);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 13,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Излазни притисак респиратора</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (cmH2O)</th>
                <th colspan="3">Xm (cmH2O)</th>
                <th rowspan="2">&lt;Xm&gt; (cmH2O)</th>
                <th rowspan="2">ΔX (cmH2O)</th>
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

    <!-- #14 Volumen -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 14);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 14,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Волумен</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (ml)</th>
                <th colspan="3">Xm (ml)</th>
                <th rowspan="2">&lt;Xm&gt; (ml)</th>
                <th rowspan="2">ΔX (ml)</th>
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

    <!-- #15 Koncentracija anestezioloških gasova -->
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 15);

    //SVI REZULTATI
    $svirezultati = new allObjectsBy2;
    $svirezultati = $svirezultati->fetch_all_objects_by2('rezultatimjerenja', 'rezultatimjerenja_mjernavelicinaid', 15,'rezultatimjerenja_izvjestajid', $_GET['izvjestaj'], 'rezultatimjerenja_id', 'ASC');

    if(count($svirezultati) == 0){

    }else{ ?>

    <p style="text-align:center;">Концентртација анестезиолошких гасова</p>

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
    
    <h4 class="second-headline"><strong>ЗАКЉУЧАК:</strong></h4>
    <p style="text-align:justify;">Прегледом мјерила утврђено да мјерило <input type="checkbox" <?php if($finalusaglasenost == "испуњава"){ echo "checked='true'";} ?>> <strong>исуњава</strong> <input type="checkbox" <?php if($finalusaglasenost == "не испуњава"){ echo "checked='true'";}?>> <strong>не испуњава</strong> метролошке захтјеве прописане Правилником о верификацији анестезиолошких машина („Службени гласник Републике Српске“, број 98/23) и на основу члана 20. Закона о метрологији Републике Српске („Службени гласник Републике Српске“, број 132/22 и 100/25) и члана 10. Правилника о верификацији мјерила („Службени гласник Републике Српске“, број 61/14), сачињен је овај записник.</p>
    <?php include(__DIR__ . '/snippet_rjesenje_ovlascivanje.php'); ?>

    <br /><br /><br /><br />

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