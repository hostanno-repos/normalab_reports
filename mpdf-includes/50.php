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
    <h3 class="main-headline">ЗАПИСНИК О ПРЕГЛЕДУ<br>МЈЕРИЛО КРВНОГ ПРИТИСКА АУТОМАТСКО<br>ЗА ДЈЕЦУ И НОВОРОЂЕНЧАД</h3>

    <!-- BROJ IZVJEŠTAJA -->
    <p>Број: <?php echo $izvjestaj['izvjestaji_broj'] ?></p>

    <!-- DATUM IZDAVANJA -->
    <p>Датум: <?php echo ($izvjestaj['izvjestaji_datumizdavanja'] !== null && $izvjestaj['izvjestaji_datumizdavanja'] !== '') ? date('d.m.Y.', strtotime($izvjestaj['izvjestaji_datumizdavanja'])) : '-'; ?></p>

    <!-- PRAZAN RED -->
    <br>

    <!-- PODNOSILAC ZAHTJEVA -->
    <p><strong>Подносилац захтјева:</strong> <?php echo latinicaUCirilicu($klijent['klijenti_naziv']); ?></p>
    <p>Број: <?php echo latinicaUCirilicu($radninalog['radninalozi_brojzahtjeva']); ?></p>
    <p>Датум: <?php echo ($izvjestaj['izvjestaji_datumzahtjeva'] !== null && $izvjestaj['izvjestaji_datumzahtjeva'] !== '') ? date('d.m.Y.', strtotime($izvjestaj['izvjestaji_datumzahtjeva'])) : '-'; ?></p>

    <!-- PRAZAN RED -->
    <br>

    <!-- MJERILO -->
    <p><strong>Мјерило:</strong></p>
    <table cellpadding="5" cellspacing="0" width="100%">
        <tbody>
            <tr>
                <td colspan="2">Назив мјерила:</td>
                <td colspan="4"><?php echo latinicaUCirilicu($vrstauredjaja['vrsteuredjaja_naziv']); ?></td>
            </tr>
            <tr>
                <td colspan="2">Произвођач мјерила:</td>
                <td colspan="4"><?php echo $mjerilo['mjerila_proizvodjac']; ?></td>
            </tr>
            <tr>
                <td>Тип:</td>
                <td><?php echo $mjerilo['mjerila_tip']; ?></td>
                <td>Серијски број:</td>
                <td><?php echo $mjerilo['mjerila_serijskibroj']; ?></td>
                <td>Службена ознака:</td>
                <td width="150"><?php echo $mjerilo['mjerila_sluzbenaoznaka']; ?></td>
            </tr>
            <tr>
                <td colspan="2">Година производње:</td>
                <td colspan="4"><?php echo $mjerilo['mjerila_godinaproizvodnje']; ?></td>
            </tr>
            <tr>
                <td colspan="2">Власник мјерила:</td>
                <td colspan="4"><?php echo latinicaUCirilicu($klijent['klijenti_naziv']); ?></td>
            </tr>
            <tr>
                <td colspan="2">Локација мјерила:</td>
                <td colspan="4"><?php echo latinicaUCirilicu($izvjestaj['izvjestaji_mjestoinspekcije']); ?></td>
            </tr>
            <tr>
                <td colspan="2">Мјесто прегледа:</td>
                <td colspan="4"><?php echo latinicaUCirilicu($izvjestaj['izvjestaji_mjestoinspekcije']); ?></td>
            </tr>
        </tbody>
    </table>

    <!-- PRAZAN RED -->
    <br>

    <!-- VERIFIKACIJA -->
    <p><strong>Верификација:</strong></p>
    <div class="verifikacija-vrsta-verifikacije">
        <p>Врста верификације:</p>
        <?php foreach($vrsteinspekcije as $vrstainspekcije){ ?>
        <p>
            <input type="checkbox" <?php if($vrstainspekcije['vrsteinspekcije_id'] == $izvjestaj['izvjestaji_vrstainspekcijeid']){ echo "checked='checked'"; } ?>>
            <?php echo latinicaUCirilicu($vrstainspekcije['vrsteinspekcije_naziv']); ?>
        </p>
        <?php } ?>
    </div>
    <div class="verifikacija-radni-uslovi">
        <p>Радни услови:</p>
        <p>Температура: <?php echo $izvjestaj['izvjestaji_temperatura']; ?>°C</p>
        <p>Влажност: <?php echo $izvjestaj['izvjestaji_vlaznost']; ?>%</p>
    </div>
    <div class="verifikacija-republicki-zig">
        <p>Ознака и серијски број скинутог републичког жига: <?php echo $izvjestaj["izvjestaji_skinutizig"]; ?></p>
    </div>

    <!-- PRAZAN RED -->
    <br>

    <!-- ETALON -->
    <p><strong>Подаци о еталону:</strong></p>
    <table cellpadding="5" cellspacing="0" width="100%">
        <tbody>
            <?php 
            $izvjestaj['izvjestaji_opremazainspekciju'] = explode(',', $izvjestaj['izvjestaji_opremazainspekciju']);
            $etalon = '';
            foreach($izvjestaj['izvjestaji_opremazainspekciju'] as $oprema){
                $opremazainspekciju = new singleObject;
                $opremazainspekciju = $opremazainspekciju->fetch_single_object('opremazainspekciju', 'opremazainspekciju_id', $oprema);

                    if($opremazainspekciju['opremazainspekciju_proizvodjac'] == ""){
                        $proizvodjac = "-";
                    }else{
                        $proizvodjac = $opremazainspekciju['opremazainspekciju_proizvodjac'];
                    }

                    if($opremazainspekciju['opremazainspekciju_tip'] == ""){
                        $tip = "-";
                    }else{
                        $tip = $opremazainspekciju['opremazainspekciju_tip'];
                    }

                    if($opremazainspekciju['opremazainspekciju_serijskibroj'] == ""){
                        $serijskibroj = "-";
                    }else{
                        $serijskibroj = $opremazainspekciju['opremazainspekciju_serijskibroj'];
                    }
                    echo
                    "<tr>
                        <td>".latinicaUCirilicu($opremazainspekciju['opremazainspekciju_naziv'])."</td>
                        <td>Произвођач: ".$proizvodjac."</td>
                        <td>Тип: ".$tip."</td>
                        <td>Серијски број: ".$serijskibroj."</td>
                    </tr>";
            }
            ?> 
        </tbody>
    </table>

    <!-- PRAZAN RED -->
    <br>
    
    <!-- PREGLED MJERILA -->
    <p><strong>Преглед мјерила:</strong></p>
    <div class="pregled-mjerila">
        <p>Визуелни преглед: <?php if($izvjestaj['izvjestaji_mjerilocitljivo'] == 1){ echo "ДА";}else{echo "НЕ";} ?></p>
        <p>Провјера запрљаности: <?php if($izvjestaj['izvjestaji_mjerilocisto'] == 1){ echo "ДА";}else{echo "НЕ";} ?></p>
        <p>Провјера цјеловитости: <?php if($izvjestaj['izvjestaji_mjerilocjelovito'] == 1){ echo "ДА";}else{echo "НЕ";} ?></p>
        <p>Провјера функционалности: <?php if($izvjestaj['izvjestaji_mjerilokablovi'] == 1){ echo "ДА";}else{echo "НЕ";} ?></p>
    </div>

    <!-- PRAZAN RED -->
    <br>
    
    <!-- PREGLED MJERILA -->
    <p><strong>Испитивање тачности мјерила крвног притиска аутоматског за дјецу и новорођенчад:</strong></p>

    <!-- #143 Ispitivanje tačnosti i histerezisa mjerila -->
    <p style="text-align:center;">Испитиванје тачности</p>
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 143);
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

    <!-- #144 Ispitivanje curenja zraka u pneumatskom sistemu -->
    <p style="text-align:center;">Испитивање цурења зрака у пнеуматском систему</p>
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 144);
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
                
                $rezultatimjerenja_145_533 = new allResults;
                $rezultatimjerenja_145_533 = $rezultatimjerenja_145_533->fetch_all_results($izvjestaj['izvjestaji_id'], 145, 533);
                $niz_145_533_146_535 =  array();

                //foreach($rezultatimjerenja_141_520 as $rezultat){
                //    array_push($niz_141_520_142_522, $rezultat['rezultatimjerenja_rezultatmjerenja']);
                //}

                for ($x = 0; $x <= 9; $x++) {
                    array_push($niz_145_533_146_535, $rezultatimjerenja_145_533[$x]['rezultatimjerenja_rezultatmjerenja']);
                }
                
                $rezultatimjerenja_145_534 = new allResults;
                $rezultatimjerenja_145_534 = $rezultatimjerenja_145_534->fetch_all_results($izvjestaj['izvjestaji_id'], 145, 534);
                $niz_145_534_146_536 =  array();

                //foreach($rezultatimjerenja_141_521 as $rezultat){
                //    array_push($niz_141_521_142_523, $rezultat['rezultatimjerenja_rezultatmjerenja']);
                //}

                for ($x = 0; $x <= 9; $x++) {
                    array_push($niz_145_534_146_536, $rezultatimjerenja_145_534[$x]['rezultatimjerenja_rezultatmjerenja']);
                }

                $rezultatimjerenja_146_535 = new allResults;
                $rezultatimjerenja_146_535 = $rezultatimjerenja_146_535->fetch_all_results($izvjestaj['izvjestaji_id'], 146, 535);

                //foreach($rezultatimjerenja_142_522 as $rezultat){
                //    array_push($niz_141_520_142_522, $rezultat['rezultatimjerenja_rezultatmjerenja']);
                //}

                for ($x = 0; $x <= 9; $x++) {
                    array_push($niz_145_533_146_535, $rezultatimjerenja_146_535[$x]['rezultatimjerenja_rezultatmjerenja']);
                }
                
                $rezultatimjerenja_146_536 = new allResultsWithSort;
                $rezultatimjerenja_146_536 = $rezultatimjerenja_146_536->fetch_all_results_with_sort($izvjestaj['izvjestaji_id'], 146, 536, 'rezultatimjerenja_id', 'ASC');

                //var_dump($rezultatimjerenja_146_536);

                //foreach($rezultatimjerenja_142_523 as $rezultat){
                //    array_push($niz_141_521_142_523, $rezultat['rezultatimjerenja_rezultatmjerenja']);
                //}

                for ($x = 0; $x <= 9; $x++) {
                    array_push($niz_145_534_146_536, $rezultatimjerenja_146_536[$x]['rezultatimjerenja_rezultatmjerenja']);
                }
            ?>
            <tr>
                <td width="100">Систолички притисак [mmHG]</td>
                <td><?php echo $rezultatimjerenja_145_533[0]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_533[1]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_533[2]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_533[3]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_533[4]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_533[5]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_533[6]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_533[7]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_533[8]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_533[9]['rezultatimjerenja_rezultatmjerenja'] ?></td>
            </tr>
            <tr>
                <td width="100">Дистолички притисак [mmHG]</td>
                <td><?php echo $rezultatimjerenja_145_534[0]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_534[1]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_534[2]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_534[3]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_534[4]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_534[5]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_534[6]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_534[7]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_534[8]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_145_534[9]['rezultatimjerenja_rezultatmjerenja'] ?></td>
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
                <td><?php echo $rezultatimjerenja_146_535[0]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_535[1]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_535[2]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_535[3]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_535[4]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_535[5]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_535[6]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_535[7]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_535[8]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_535[9]['rezultatimjerenja_rezultatmjerenja'] ?></td>
            </tr>
            <tr>
                <td width="100">Дистолички притисак [mmHG]</td>
                <td><?php echo $rezultatimjerenja_146_536[0]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_536[1]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_536[2]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_536[3]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_536[4]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_536[5]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_536[6]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_536[7]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_536[8]['rezultatimjerenja_rezultatmjerenja'] ?></td>
                <td><?php echo $rezultatimjerenja_146_536[9]['rezultatimjerenja_rezultatmjerenja'] ?></td>
            </tr>
        </tbody>
    </table>

    <br />
    <?php $devijacijaSistolicni = round(calculateSampleStandardDeviation($niz_145_533_146_535),2); ?>
    <?php $devijacijaDistolicni = round(calculateSampleStandardDeviation($niz_145_534_146_536),2); ?>
    <p>Грешка систоличког притиска [mmHg]: <?php echo $devijacijaSistolicni ?></p>
    <p>Грешка дистоличког притиска [mmHg]: <?php echo $devijacijaDistolicni ?></p>
    <p>Постављена вриједност: <?php echo $rezultatimjerenja_146_536[10]['rezultatimjerenja_rezultatmjerenja'] ?></p>

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
                <td style="text-align: center;">5</td>
                <td style="text-align: center;"><?php if($rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"] > 5){ echo "НЕ"; $finalusaglasenost = "не испуњава";}else{ echo "ДА";} ?></td>
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
    <p style="text-align:justify;">Резултати инспекције се односе искључиво на дати предмет у тренутку инспекције. На основу Рјешења о измјени и допуни рјешења о овлашћивању тијела за верификацију мјерила број 18/1.10/393.10-03-09-25/25 од 30.12.2025. године, на мјерило је постављен републички жиг у облику наљепнице број: <?php echo $izvjestaj["izvjestaji_novizig"];?>.</p>

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