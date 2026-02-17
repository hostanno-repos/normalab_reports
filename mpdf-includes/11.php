<?php

include_once ('reports_head.php')

?>

<div class="main-content">
    <!-- NASLOV -->
    <h3 class="main-headline">ЗАПИСНИК О ПРЕГЛЕДУ<br>МЈЕРИЛО КРВНОГ ПРИТИСКА АНЕРОИДНО</h3>

    <?php include_once("report_intro.php"); ?>

    <!-- PRAZAN RED -->
    <br>
    
    <!-- PREGLED MJERILA -->
    <p><strong>Испитивање тачности мјерила крвног притиска анероидног:</strong></p>

    <!-- #30 Ispitivanje tačnosti i histerezisa mjerila -->
    <p style="text-align:center;">Испитиванје тачности и хистерезиса мјерила</p>
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 30);
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Притисак [mmHg]</th>
                <th colspan="2">1. циклус</th>
                <th colspan="2">2. циклус</th>
                <th colspan="2">Одступање</th>
                <th colspan="2">Хистерезис</th>
            </tr>
            <tr>
                <th>растућа</th>
                <th>опадајућа</th>
                <th>растућа</th>
                <th>опадајућа</th>
                <th>растућа</th>
                <th>опадајућа</th>
                <th>1. циклус</th>
                <th>2. циклус</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            
            $max1 = [];
            $max2 = [];
            foreach ($referentnevrijednosti as $referentnavrijednost) {

                //kupimo rezultate mjerenja za ovu referentnu vrijednost
                $rezultatimjerenja = new allResults;
                $rezultatimjerenja = $rezultatimjerenja->fetch_all_results($izvjestaj['izvjestaji_id'], $mjernavelicina['mjernevelicine_id'], $referentnavrijednost['referentnevrijednosti_id']);

                $prvomjerenje = "-";
                $drugomjerenje = "-";
                $trecemjerenje = "-";
                $cetvrtomjerenje = "-";

                //vrtimo rezultate mjerenja
                foreach ($rezultatimjerenja as $rezultatmjerenja) {
                    switch ($rezultatmjerenja) {
                        case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 1:
                            $prvomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                            break;
                        case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 2:
                            $drugomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                            break;
                        case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 3:
                            $trecemjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                            break;
                        case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 4:
                            $cetvrtomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                            break;
                    }
                }

                //PRORAČUN
                //ako je vršeno mjerenje odnosno ako nije uneseno "-" kao rezultat mjerenja
                if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-" && $cetvrtomjerenje != "-" && $prvomjerenje != "--" && $drugomjerenje != "--" && $trecemjerenje != "--" && $cetvrtomjerenje != "--") {

                    $odsRast = abs(((abs($prvomjerenje) + abs($trecemjerenje)) / 2) - $referentnavrijednost['referentnevrijednosti_referentnavrijednost']);
                    array_push($max1, $odsRast);
                    $odsOpad = abs(((abs($drugomjerenje) + abs($cetvrtomjerenje)) / 2) - $referentnavrijednost['referentnevrijednosti_referentnavrijednost']);
                    array_push($max1, $odsOpad);
                    $hist1 = abs((abs($prvomjerenje) - abs($drugomjerenje)));
                    array_push($max2, $hist1);
                    $hist2 = abs((abs($trecemjerenje) - abs($cetvrtomjerenje)));
                    array_push($max2, $hist2);

                    /*$pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, $trecemjerenje, $cetvrtomjerenje, number_format((float) $odsRast, 2, '.', ''), number_format((float) $odsOpad, 2, '.', ''), number_format((float) $hist1, 2, '.', ''), round($hist2, 0)));*/

                    ?>
                    <tr>
                        <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
                        <td style="text-align:center;"><?php echo $prvomjerenje ?></td>
                        <td style="text-align:center;"><?php echo $drugomjerenje ?></td>
                        <td style="text-align:center;"><?php echo $trecemjerenje ?></td>
                        <td style="text-align:center;"><?php echo $cetvrtomjerenje ?></td>
                        <td style="text-align:center;"><?php echo number_format((float) $odsRast, 2, '.', '') ?></td>
                        <td style="text-align:center;"><?php echo number_format((float) $odsOpad, 2, '.', '') ?></td>
                        <td style="text-align:center;"><?php echo number_format((float) $hist1, 2, '.', '') ?></td>
                        <td style="text-align:center;"><?php echo number_format((float) $hist2, 2, '.', '') ?></td>
                    </tr>
                    <?php

                //ako nije vršeno mjerenje odnosno ako je uneseno "-" kao rezultat mjerenja
                } else {

                    $odsRast = "-";
                    $odsOpad = "-";
                    $hist1 = "-";
                    $hist2 = "-";

                }                

                //USAGLAŠENOST
                /*if ($relativnagreska > round($referentnavrijednost['referentnevrijednosti_odstupanje'], 0)) {
                    $usaglasenost = "НЕ";
                    $finalusaglasenost = "не испуњава";
                } else if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-") {
                    $usaglasenost = "ДА";
                } else {
                    $usaglasenost = "-";
                }
                
                if (!isset($finalusaglasenost)) {
                    $finalusaglasenost = "испуњава";
                }*/

                if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-" && $cetvrtomjerenje != "-" && $prvomjerenje != "--" && $drugomjerenje != "--" && $trecemjerenje != "--" && $cetvrtomjerenje != "--") { ?>
                
                    <!-- <tr>
                        <td style="text-align:center;"><?php //echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
                        <td style="text-align:center;"><?php //echo $prvomjerenje ?></td>
                        <td style="text-align:center;"><?php //echo $drugomjerenje ?></td>
                        <td style="text-align:center;"><?php //echo $trecemjerenje ?></td>
                        <td style="text-align:center;"><?php //echo number_format((float) $srednjavrijednost, 2, '.', '') ?></td>
                        <td style="text-align:center;"><?php //echo number_format((float) $apsolutnagreska, 2, '.', '') ?></td>
                        <td style="text-align:center;"><?php //echo number_format((float) $relativnagreska, 2, '.', '') ?></td>
                        <td style="text-align:center;"><?php //echo round($referentnavrijednost['referentnevrijednosti_odstupanje'], 3) ?></td>
                        <td style="text-align:center;"><?php //echo $usaglasenost ?></td>
                    </tr> -->
                <?php } else if($prvomjerenje == "--" && $drugomjerenje == "--" && $trecemjerenje == "--" && $cetvrtomjerenje == "--") { ?>
                    <tr>
                        <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
                        <td style="text-align:center;">-</td>
                        <td style="text-align:center;">-</td>
                        <td style="text-align:center;">-</td>
                        <td style="text-align:center;">-</td>
                        <td style="text-align:center;">-</td>
                        <td style="text-align:center;">-</td>
                        <td style="text-align:center;">-</td>
                        <td style="text-align:center;">-</td>
                    </tr>
                <?php } else { ?>
                    <!--<tr class="red-bez-mjerenja">
                        <td><?php //echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>ДА</td>
                    </tr>-->
                <?php }
                $prvomjerenje = "-";
                $drugomjerenje = "-";
                $trecemjerenje = "-";
                $cetvrtomjerenje = "-";
            }   ?>
        </tbody>
    </table>

    <br /><br /><br />

    <!-- #31 Ispitivanje curenja zraka u pneumatskom sistemu -->
    <p style="text-align:center;">Испитивање цурења зрака у пнеуматском систему</p>
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 31);
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="">
        <thead>
            <tr>
                <th rowspan="2">Притисак [mmHg]</th>
                <th colspan="1">p1 [mmHg]</th>
                <th colspan="1">p2 [mmHg]</th>
                <th rowspan="2">Разлика p1-p2 [mmHg]</th>
                <th rowspan="2">Стопа испуштања притиска [mmHg/min]</th>
            </tr>
            <tr>
                <th>1. очитавање</th>
                <th>Очитавање након 5 минута</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            
            $max3 = [];
            foreach ($referentnevrijednosti as $referentnavrijednost) {

                //kupimo rezultate mjerenja za ovu referentnu vrijednost
                $rezultatimjerenja = new allResults;
                $rezultatimjerenja = $rezultatimjerenja->fetch_all_results($izvjestaj['izvjestaji_id'], $mjernavelicina['mjernevelicine_id'], $referentnavrijednost['referentnevrijednosti_id']);

                $prvomjerenje = "-";
                $drugomjerenje = "-";

                //vrtimo rezultate mjerenja
                foreach ($rezultatimjerenja as $rezultatmjerenja) {
                    switch ($rezultatmjerenja) {
                        case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 1:
                            $prvomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                            break;
                        case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 2:
                            $drugomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                            break;
                    }
                }

                //PRORAČUN
                //ako je vršeno mjerenje odnosno ako nije uneseno "-" kao rezultat mjerenja
                if ($prvomjerenje != "-" && $drugomjerenje != "-" && $prvomjerenje != "--" && $drugomjerenje != "--") {

                    $razp1p2 = abs(abs($drugomjerenje) - abs($prvomjerenje));
                    $stoIsp = $razp1p2 / 5;
                    array_push($max3, $stoIsp);
                    /*$pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, number_format((float) $razp1p2, 2, '.', ''), number_format((float) $stoIsp, 2, '.', '')));*/

                    /*$pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, $trecemjerenje, $cetvrtomjerenje, number_format((float) $odsRast, 2, '.', ''), number_format((float) $odsOpad, 2, '.', ''), number_format((float) $hist1, 2, '.', ''), round($hist2, 0)));*/

                    ?>
                    <tr>
                        <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
                        <td style="text-align:center;"><?php echo $prvomjerenje ?></td>
                        <td style="text-align:center;"><?php echo $drugomjerenje ?></td>
                        <td style="text-align:center;"><?php echo number_format((float) $razp1p2, 2, '.', '') ?></td>
                        <td style="text-align:center;"><?php echo number_format((float) $stoIsp, 2, '.', '') ?></td>
                    </tr>
                    <?php

                //ako nije vršeno mjerenje odnosno ako je uneseno "-" kao rezultat mjerenja
                } else {
                    $odsRast = "-";
                    $odsOpad = "-";
                    $hist1 = "-";
                    $hist2 = "-";
                    $razp1p2 = "-";
                    $stoIsp = "-";
                }                

                //USAGLAŠENOST
                /*if ($relativnagreska > round($referentnavrijednost['referentnevrijednosti_odstupanje'], 0)) {
                    $usaglasenost = "НЕ";
                    $finalusaglasenost = "не испуњава";
                } else if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-") {
                    $usaglasenost = "ДА";
                } else {
                    $usaglasenost = "-";
                }
                
                if (!isset($finalusaglasenost)) {
                    $finalusaglasenost = "испуњава";
                }*/

                if ($prvomjerenje != "-" && $drugomjerenje != "-" && 
                    $prvomjerenje != "--" && $drugomjerenje != "--") { ?>
                
                    <!-- <tr>
                        <td style="text-align:center;"><?php //echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
                        <td style="text-align:center;"><?php //echo $prvomjerenje ?></td>
                        <td style="text-align:center;"><?php //echo $drugomjerenje ?></td>
                        <td style="text-align:center;"><?php //echo $trecemjerenje ?></td>
                        <td style="text-align:center;"><?php //echo number_format((float) $srednjavrijednost, 2, '.', '') ?></td>
                        <td style="text-align:center;"><?php //echo number_format((float) $apsolutnagreska, 2, '.', '') ?></td>
                        <td style="text-align:center;"><?php //echo number_format((float) $relativnagreska, 2, '.', '') ?></td>
                        <td style="text-align:center;"><?php //echo round($referentnavrijednost['referentnevrijednosti_odstupanje'], 3) ?></td>
                        <td style="text-align:center;"><?php //echo $usaglasenost ?></td>
                    </tr> -->
                <?php } else if($prvomjerenje == "--" && $drugomjerenje == "--") { ?>
                    <tr>
                        <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
                        <td style="text-align:center;">-</td>
                        <td style="text-align:center;">-</td>
                        <td style="text-align:center;">-</td>
                        <td style="text-align:center;">-</td>
                        <!--<td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>НЕ</td>-->
                    </tr>
                <?php } else { ?>
                    <!--<tr class="red-bez-mjerenja">
                        <td><?php //echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>ДА</td>
                    </tr>-->
                <?php }
                $prvomjerenje = "-";
                $drugomjerenje = "-";
                $trecemjerenje = "-";
                $cetvrtomjerenje = "-";
            }   ?>
        </tbody>
    </table>

    <br />

    <?php $finalusaglasenost = "испуњава"; ?>

    <table cellpadding="5" cellspacing="0" width="100%" class="">
        <thead>
            <tr>
                <th></th>
                <th>Максимално одступање</th>
                <th>Максимално дозвољено одступање</th>
                <th>Задовољава</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Тачност показивања</td>
                <td style="text-align: center;">
                    <?php
                        if (!empty($max1)) {
                            $max_max1 = max($max1);
                            echo number_format((float)$max_max1, 2, '.', '');
                        } else {
                            echo "-";
                            $max_max1 = null;
                        }
                    ?>
                </td>
                <td style="text-align: center;">5</td>
                <td style="text-align: center;">
                    <?php
                        if ($max_max1 !== null && $max_max1 > 5 || $max_max1 == null) {
                            echo "НЕ";
                            $finalusaglasenost = "не испуњава";
                        } else {
                            echo "ДА";
                        }
                    ?>
                </td>
            </tr>
                
            <tr>
                <td>Хистерезис</td>
                <td style="text-align: center;">
                    <?php
                        if (!empty($max2)) {
                            $max_max2 = max($max2);
                            echo number_format((float) $max_max2, 2, '.', '');
                        } else {
                            echo "-";
                            $max_max2 = null;
                        }
                    ?>
                </td>
                <td style="text-align: center;">4</td>
                <td style="text-align: center;">
                    <?php
                        if ($max_max2 !== null && $max_max2 > 4 || $max_max2 == null) {
                            echo "НЕ";
                            $finalusaglasenost = "не испуњава";
                        } else {
                            echo "ДА";
                        }
                    ?>
                </td>
            </tr>

            <tr>
                <td>Испитивање цурења зрака</td>
                <td style="text-align: center;">
                    <?php
                        if (!empty($max3)) {
                            $max_max3 = max($max3);
                            echo $max_max3;
                        } else {
                            echo "-";
                            $max_max3 = null;
                        }
                    ?>
                </td>
                <td style="text-align: center;">4</td>
                <td style="text-align: center;">
                    <?php
                        if ($max_max3 !== null && $max_max3 > 4 || $max_max3 == null) {
                            echo "НЕ";
                            $finalusaglasenost = "не испуњава";
                        } else {
                            echo "ДА";
                        }
                    ?>
                </td>
                </tr>
            <tr>
                <td>Испитивање вентила брзог испуста</td>
                <?php
                
                $rezultatmjerenja_ = new allResultsWithSort;
                $rezultatmjerenja_ = $rezultatmjerenja_->fetch_all_results_with_sort($izvjestaj['izvjestaji_id'], 0, 0, "rezultatimjerenja_brojmjerenja", "ASC");
                print_r($rezultatmjerenja_);
                ?>
                <td style="text-align: center;"><?php if($rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"] == null){ echo "-";}else{echo $rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"];} ?></td>
                <td style="text-align: center;">10</td>
                <td style="text-align: center;"><?php if($rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"] > 10 || $rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"] == null){ echo "НЕ"; $finalusaglasenost = "не испуњава";}else{ echo "ДА";} ?></td>
            </tr>
        </tbody>
    </table>
    
    <h4 class="second-headline"><strong>ЗАКЉУЧАК:</strong></h4>
    <p style="text-align:justify;">Прегледом мјерила утврђено да мјерило <input type="checkbox" <?php if($finalusaglasenost == "испуњава"){ echo "checked='true'";} ?>> <strong>исуњава</strong> <input type="checkbox" <?php if($finalusaglasenost == "не испуњава"){ echo "checked='true'";}?>> <strong>не испуњава</strong> метролошке захтјеве прописане Метролошким упутством за преглед манометара за мјерење крвног притиска („Службени гласник Републике Српске“, број 9/05) и на основу члана 20. Закона о метрологији Републике Српске („Службени гласник Републике Српске“, број 132/22 и 100/25) и члана 10. Правилника о верификацији мјерила („Службени гласник Републике Српске“, број 61/14), сачињен је овај записник.</p>
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