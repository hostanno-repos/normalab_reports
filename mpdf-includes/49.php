<?php

include_once ('reports_head.php')

function calculateSampleStandardDeviation($array) {
    $count = count($array);
    if ($count <= 1) {
        return 0;
    }

    // Calculate the mean (average)
    $mean = array_sum($array) / $count;

    // Calculate the squared differences from the mean
    $squaredDifferences = array_map(function($value) use ($mean) {
        return pow($value - $mean, 2);
    }, $array);

    // Calculate the variance (average of squared differences), divide by n-1 for sample variance
    $variance = array_sum($squaredDifferences) / ($count - 1);

    // Return the square root of the variance to get the standard deviation
    return sqrt($variance);
}

?>

<div class="main-content">
    <!-- NASLOV -->
    <h3 class="main-headline">ЗАПИСНИК О ПРЕГЛЕДУ<br>МЈЕРИЛО КРВНОГ ПРИТИСКА АУТОМАТСКО</h3>

    <?php include_once("report_intro.php"); ?>

    <!-- PRAZAN RED -->
    <br>
    
    <!-- PREGLED MJERILA -->
    <p><strong>Испитивање тачности мјерила крвног притиска аутоматског:</strong></p>

    <!-- #139 Ispitivanje tačnosti i histerezisa mjerila -->
    <p style="text-align:center;">Испитиванје тачности</p>
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 139);
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="">
        <thead>
            <tr>
                <th rowspan="2" width="150">Притисак [mmHg]</th>
                <th colspan="2">1. циклус</th>
                <th colspan="2">2. циклус</th>
                <th colspan="2">Одступање</th>
            </tr>
            <tr>
                <th>растућа</th>
                <th>опадајућа</th>
                <th>растућа</th>
                <th>опадајућа</th>
                <th>растућа</th>
                <th>опадајућа</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            
            $max1 = [];
            //$max2 = [];
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

                    ?>
                    <tr>
                        <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
                        <td style="text-align:center;"><?php echo $prvomjerenje ?></td>
                        <td style="text-align:center;"><?php echo $drugomjerenje ?></td>
                        <td style="text-align:center;"><?php echo $trecemjerenje ?></td>
                        <td style="text-align:center;"><?php echo $cetvrtomjerenje ?></td>
                        <td style="text-align:center;"><?php echo number_format((float) $odsRast, 2, '.', '') ?></td>
                        <td style="text-align:center;"><?php echo number_format((float) $odsOpad, 2, '.', '') ?></td>
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
                        <td><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
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

    <!-- #140 Ispitivanje curenja zraka u pneumatskom sistemu -->
    <p style="text-align:center;">Испитивање цурења зрака у пнеуматском систему</p>
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 140);
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
                <?php } else if($prvomjerenje == "--" && $drugomjerenje == "--" && $trecemjerenje == "--" && $cetvrtomjerenje == "--") { ?>
                    <tr>
                        <td><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>НЕ</td>
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

    <!-- #141 Ponovljivost -->
    <p style="text-align:center;">Испитиванје поновљивости аутоматског мјерила</p>
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 141);
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="">
        <thead>
            <tr>
                <th>Број мјерења</th>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $rezultatimjerenja_141_520 = new allResults;
                $rezultatimjerenja_141_520 = $rezultatimjerenja_141_520->fetch_all_results($izvjestaj['izvjestaji_id'], 141, 520);
                $niz_141_520_142_522 =  array();

                //foreach($rezultatimjerenja_141_520 as $rezultat){
                //    array_push($niz_141_520_142_522, $rezultat['rezultatimjerenja_rezultatmjerenja']);
                //}

                for ($x = 0; $x <= 9; $x++) {
                    array_push($niz_141_520_142_522, $rezultatimjerenja_141_520[$x]['rezultatimjerenja_rezultatmjerenja']);
                }
                
                $rezultatimjerenja_141_521 = new allResults;
                $rezultatimjerenja_141_521 = $rezultatimjerenja_141_521->fetch_all_results($izvjestaj['izvjestaji_id'], 141, 521);
                $niz_141_521_142_523 =  array();

                //foreach($rezultatimjerenja_141_521 as $rezultat){
                //    array_push($niz_141_521_142_523, $rezultat['rezultatimjerenja_rezultatmjerenja']);
                //}

                for ($x = 0; $x <= 9; $x++) {
                    array_push($niz_141_521_142_523, $rezultatimjerenja_141_521[$x]['rezultatimjerenja_rezultatmjerenja']);
                }

                $rezultatimjerenja_142_522 = new allResults;
                $rezultatimjerenja_142_522 = $rezultatimjerenja_142_522->fetch_all_results($izvjestaj['izvjestaji_id'], 142, 522);

                //foreach($rezultatimjerenja_142_522 as $rezultat){
                //    array_push($niz_141_520_142_522, $rezultat['rezultatimjerenja_rezultatmjerenja']);
                //}

                for ($x = 0; $x <= 9; $x++) {
                    array_push($niz_141_520_142_522, $rezultatimjerenja_142_522[$x]['rezultatimjerenja_rezultatmjerenja']);
                }
                
                //$rezultatimjerenja_142_523 = new allResults;
                //$rezultatimjerenja_142_523 = $rezultatimjerenja_142_523->fetch_all_results($izvjestaj['izvjestaji_id'], 142, 523);

                $rezultatimjerenja_142_523 = new allResultsWithSort;
                $rezultatimjerenja_142_523 = $rezultatimjerenja_142_523->fetch_all_results_with_sort($izvjestaj['izvjestaji_id'], 142, 523, 'rezultatimjerenja_id', 'ASC');

                //foreach($rezultatimjerenja_142_523 as $rezultat){
                //    array_push($niz_141_521_142_523, $rezultat['rezultatimjerenja_rezultatmjerenja']);
                //}

                for ($x = 0; $x <= 9; $x++) {
                    array_push($niz_141_521_142_523, $rezultatimjerenja_142_523[$x]['rezultatimjerenja_rezultatmjerenja']);
                }
            ?>
            <tr>
                <td width="100">Систолички притисак [mmHG]</td>
                <td><?php echo $rezultatimjerenja_141_520[0]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_520[1]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_520[2]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_520[3]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_520[4]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_520[5]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_520[6]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_520[7]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_520[8]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_520[9]['rezultatimjerenja_rezultatmjerenja'] ?></td>
            </tr>
            <tr>
                <td width="100">Дистолички притисак [mmHG]</td>
                <td><?php echo $rezultatimjerenja_141_521[0]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_521[1]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_521[2]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_521[3]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_521[4]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_521[5]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_521[6]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_521[7]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_521[8]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_141_521[9]['rezultatimjerenja_rezultatmjerenja'] ?></td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th>Број мјерења</th>
                <th>11</th>
                <th>12</th>
                <th>13</th>
                <th>14</th>
                <th>15</th>
                <th>16</th>
                <th>17</th>
                <th>18</th>
                <th>19</th>
                <th>20</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="100">Систолички притисак [mmHG]</td>
                <td><?php echo $rezultatimjerenja_142_522[0]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_522[1]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_522[2]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_522[3]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_522[4]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_522[5]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_522[6]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_522[7]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_522[8]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_522[9]['rezultatimjerenja_rezultatmjerenja'] ?></td>
            </tr>
            <tr>
                <td width="100">Дистолички притисак [mmHG]</td>
                <td><?php echo $rezultatimjerenja_142_523[0]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_523[1]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_523[2]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_523[3]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_523[4]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_523[5]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_523[6]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_523[7]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_523[8]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_142_523[9]['rezultatimjerenja_rezultatmjerenja'] ?></td>
            </tr>
        </tbody>
    </table>

    <br />
    <?php $devijacijaSistolicni = round(calculateSampleStandardDeviation($niz_141_520_142_522),2); ?>
    <?php $devijacijaDistolicni = round(calculateSampleStandardDeviation($niz_141_521_142_523),2); ?>
    <p>Грешка систоличког притиска [mmHg]: <?php echo $devijacijaSistolicni ?></p>
    <p>Грешка дистоличког притиска [mmHg]: <?php echo $devijacijaDistolicni ?></p>
    <p>Постављена вриједност: <?php echo $rezultatimjerenja_142_523[10]['rezultatimjerenja_rezultatmjerenja'] ?></p>

    <br />

    <?php $finalusaglasenost = "испуњава";
                
        $rezultatmjerenja_ = new allResultsWithSort;
        $rezultatmjerenja_ = $rezultatmjerenja_->fetch_all_results_with_sort($izvjestaj['izvjestaji_id'], 0, 0, "rezultatimjerenja_brojmjerenja", "ASC");

    ?>

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
                <td style="text-align: center;">6</td>
                <td style="text-align: center;">
                    <?php
                        if ($max_max3 !== null && $max_max3 > 6 || $max_max3 == null) {
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
                <td style="text-align: center;"><?php echo $rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"] ?></td>
                <td style="text-align: center;">10</td>
                <td style="text-align: center;"><?php if($rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"] > 10){ echo "НЕ"; $finalusaglasenost = "не испуњава";}else{ echo "ДА";} ?></td>
            </tr>
            <tr>
                <td>Поновљивост</td>
                <td style="text-align: center;"><?php echo max(array($devijacijaSistolicni, $devijacijaDistolicni)); ?></td>
                <td style="text-align: center;">3</td>
                <td style="text-align: center;"><?php if(max(array($devijacijaSistolicni, $devijacijaDistolicni)) > 3){ echo "НЕ"; $finalusaglasenost = "не испуњава";}else{ echo "ДА";} ?></td>
            </tr>
        </tbody>
    </table>
    
    <h4 class="second-headline"><strong>ЗАКЉУЧАК:</strong></h4>
    <p style="text-align:justify;">Прегледом мјерила утврђено да мјерило <input type="checkbox" <?php if($finalusaglasenost == "испуњава"){ echo "checked='true'";} ?>> <strong>исуњава</strong> <input type="checkbox" <?php if($finalusaglasenost == "не испуњава"){ echo "checked='true'";}?>> <strong>не испуњава</strong> метролошке захтјеве прописане Метролошким упутством за преглед манометара за мјерење крвног притиска („Службени гласник Републике Српске“, број 9/05) и на основу члана 20. Закона о метрологији Републике Српске („Службени гласник Републике Српске“, број 132/22 и 100/25) и члана 10. Правилника о верификацији мјерила („Службени гласник Републике Српске“, број 61/14), сачињен је овај записник.</p>
    <?php include(__DIR__ . '/snippet_rjesenje_ovlascivanje.php'); ?>

    <br /><br /><br /><br /><br />

    <h4 class="second-headline">Напомена:</h4>
    <p style="text-align:justify;"><?php echo latinicaUCirilicu($izvjestaj['izvjestaji_napomena']); ?></p>

    <br /><br /><br />

    <div style="">
        <p style="text-align: center;margin: 0 10% 0 70%;">Преглед извршио</p>
        <p style="text-align: center;margin: 0 7% 0 67%;border-bottom: 1px solid #000000;"><?php echo latinicaUCirilicu($mjerenjeizvrsio['kontrolori_ime'])." ".latinicaUCirilicu($mjerenjeizvrsio['kontrolori_prezime']); ?></p>
        <p style="text-align: center;margin: 0 10% 0 70%;">(име и презиме)</p>
    </div>
</div>

<?php include_once('reports_styles3.php'); ?>