<?php

include_once ('reports_head.php')

?>

<div class="main-content">
    <!-- NASLOV -->
    <h3 class="main-headline">ЗАПИСНИК О ПРЕГЛЕДУ<br>ПАЦИЈЕНТ МОНИТОРА</h3>

    <?php include_once("report_intro.php"); ?>

    <!-- PRAZAN RED -->
    <br>
    
    <!-- PREGLED MJERILA -->
    <p><strong>Испитивање тачности пацијент монитора:</strong></p>

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

    <p style="text-align:center;">Број откуцаја срца у временском интервалу од 1 минут</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (BPM)</th>
                <th colspan="3">Xm (BPM)</th>
                <th rowspan="2">&lt;Xm&gt; (BPM)</th>
                <th rowspan="2">ΔX (BPM)</th>
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

    <!-- #4 Amplituda naponskog signala -->
    <p style="text-align:center;">Aмплитудa напонског сигнала</p>
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
                <th rowspan="2">Xs (mV)</th>
                <th colspan="3">Xm (mV)</th>
                <th rowspan="2">&lt;Xm&gt; (mV)</th>
                <th rowspan="2">ΔX (mV)</th>
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

    <p style="text-align:center;">Респирација</p>

    <?php
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (BRPM)</th>
                <th colspan="3">Xm (BRPM)</th>
                <th rowspan="2">&lt;Xm&gt; (BRPM)</th>
                <th rowspan="2">ΔX (BRPM)</th>
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

    <p style="text-align:center;">Неинвазивни крвни притисак - систолни</p>

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
                <th rowspan="2">Г (%)</th>
                <th rowspan="2">НДГ (mmHG)</th>
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

    <p style="text-align:center;">Неинвазивни крвни притисак - дијастолни</p>

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
                <th rowspan="2">Г (%)</th>
                <th rowspan="2">НДГ (mmHG)</th>
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

    <!-- #157 Invazivni krvni pritisak -->
    <p style="text-align:center;">Инвазивни крвни притисак</p>
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
                <th rowspan="2">Г (%)</th>
                <th rowspan="2">НДГ (mmHG)</th>
                <th rowspan="2">Усаглашеност</th>
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

    <p style="text-align:center;">Температура</p>

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
                <th rowspan="2">Г (%)</th>
                <th rowspan="2">НДГ (°C)</th>
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

        <p style="text-align:center;">Сатурација SpO2</p>

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
                <?php include('script[one-hidden-two-not-measurable-absolute].php'); ?>
            </tbody>
        </table>
        
        <br />

    <?php } ?>
    
    <h4 class="second-headline"><strong>ЗАКЉУЧАК:</strong></h4>
    <p style="text-align:justify;">Прегледом мјерила утврђено да мјерило <input type="checkbox" <?php if($finalusaglasenost == "испуњава"){ echo "checked='true'";} ?>> <strong>исуњава</strong> <input type="checkbox" <?php if($finalusaglasenost == "не испуњава"){ echo "checked='true'";}?>> <strong>не испуњава</strong> метролошке захтјеве прописане Правилником о верификацији пацијент-монитора („Службени гласник Републике Српске“, број 98/23) и на основу члана 20. Закона о метрологији Републике Српске („Службени гласник Републике Српске“, број 132/22 и 100/25) и члана 10. Правилника о верификацији мјерила („Службени гласник Републике Српске“, број 61/14), сачињен је овај записник.</p>
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