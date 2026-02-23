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
    <!--<p><strong>Испитивање тачности мјерила крвног притиска анероидног:</strong></p>-->

    <!-- #30 Ispitivanje tačnosti i histerezisa mjerila -->
    <p style="text-align:center;">Ispitivanje tačnosti i histereze mjerila</p>
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
                <th rowspan="2">Priitisak [mmHg]</th>
                <th colspan="2">1. ciklus</th>
                <th colspan="2">2. ciklus</th>
                <th colspan="2">Odstupanje</th>
                <th colspan="2">Histerezis</th>
            </tr>
            <tr>
                <th>rastuća</th>
                <th>opadajuća</th>
                <th>rastuća</th>
                <th>opadajuća</th>
                <th>rastuća</th>
                <th>opadajuća</th>
                <th>1. ciklus</th>
                <th>2. ciklus</th>
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
                    $usaglasenost = "NE";
                    $finalusaglasenost = "не испуњава";
                } else if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-") {
                    $usaglasenost = "DA";
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
                        <td>DA</td>
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
    <p style="text-align:center;">Ispitivanje curenja zraka u pneutmaskom sistemu</p>
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
                <th rowspan="2">Priitisak [mmHg]</th>
                <th colspan="1">p1 [mmHg]</th>
                <th colspan="1">p2 [mmHg]</th>
                <th rowspan="2">Razlika p1-p2 [mmHg]</th>
                <th rowspan="2">Stopa ispuštanja priitiska [mmHg/min]</th>
            </tr>
            <tr>
                <th>1. očitavanje</th>
                <th>Očitavanje nakon 5 minuta</th>
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
                    $usaglasenost = "NE";
                    $finalusaglasenost = "не испуњава";
                } else if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-") {
                    $usaglasenost = "DA";
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
                        <td>NE</td>-->
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
                        <td>DA</td>
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
                <th>Maksimalno odstupanje</th>
                <th>Maksimalno dozvoljeno odstupanje</th>
                <th>Zadovoljava</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tačnost pokazivanja</td>
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
                            echo "NE";
                            $finalusaglasenost = "не испуњава";
                        } else {
                            echo "DA";
                        }
                    ?>
                </td>
            </tr>
                
            <tr>
                <td>Histerezis</td>
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
                            echo "NE";
                            $finalusaglasenost = "не испуњава";
                        } else {
                            echo "DA";
                        }
                    ?>
                </td>
            </tr>

            <tr>
                <td>Ispitivanje curenja zraka</td>
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
                            echo "NE";
                            $finalusaglasenost = "не испуњава";
                        } else {
                            echo "DA";
                        }
                    ?>
                </td>
                </tr>
            <tr>
                <td>Ispitivanje ventila brzog ispusta</td>
                <?php
                
                $rezultatmjerenja_ = new allResultsWithSort;
                $rezultatmjerenja_ = $rezultatmjerenja_->fetch_all_results_with_sort($izvjestaj['izvjestaji_id'], 0, 0, "rezultatimjerenja_brojmjerenja", "ASC");
                print_r($rezultatmjerenja_);
                ?>
                <td style="text-align: center;"><?php if($rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"] == null){ echo "-";}else{echo $rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"];} ?></td>
                <td style="text-align: center;">10</td>
                <td style="text-align: center;"><?php if($rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"] > 10 || $rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"] == null){ echo "NE"; $finalusaglasenost = "не испуњава";}else{ echo "DA";} ?></td>
            </tr>
        </tbody>
    </table>
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