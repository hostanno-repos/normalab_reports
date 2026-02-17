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

?>

<div class="main-content">
    <!-- NASLOV -->
    <h3 class="main-headline">ЗАПИСНИК О ПРЕГЛЕДУ<br>ПЕРФУЗОРА</h3>

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
    <p><strong>Испитивање тачности перфузора:</strong></p>

    <!-- #23 Protok -->
    <p style="text-align:center;">Проток</p>
    <?php
    // MJERNA VELIČINA
    $mjernavelicina = new singleObject;
    $mjernavelicina = $mjernavelicina->fetch_single_object('mjernevelicine', 'mjernevelicine_id', 23);
    //REFERENTNE VRIJEDNOSTI
    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by('referentnevrijednosti', 'referentnevrijednosti_mjernavelicinaid', $mjernavelicina['mjernevelicine_id'], 'referentnevrijednosti_referentnavrijednost', 'ASC');
    ?>
    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs (ml/h)</th>
                <th colspan="3">Xm (ml/h)</th>
                <th rowspan="2">&lt;Xm&gt; (ml/h)</th>
                <th rowspan="2">ΔX (ml/h)</th>
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
    
    <h4 class="second-headline"><strong>ЗАКЉУЧАК:</strong></h4>
    <p style="text-align:justify;">Прегледом мјерила утврђено да мјерило <input type="checkbox" <?php if($finalusaglasenost == "испуњава"){ echo "checked='true'";} ?>> <strong>исуњава</strong> <input type="checkbox" <?php if($finalusaglasenost == "не испуњава"){ echo "checked='true'";}?>> <strong>не испуњава</strong> метролошке захтјеве прописане Правилником о верификацији перфузора („Службени гласник Републике Српске“, број 98/23) и на основу члана 20. Закона о метрологији Републике Српске („Службени гласник Републике Српске“, број 132/22 и 100/25) ) и члана 10. Правилника о верификацији мјерила („Службени гласник Републике Српске“, број 61/14), сачињен је овај записник.</p>
    <p style="text-align:justify;">Резултати инспекције се односе искључиво на дати предмет у тренутку инспекције. На основу Рјешења о измјени и допуни рјешења о овлашћивању тијела за верификацију мјерила број 18/1.10/393.10-03-09-25/25 од 30.12.2025. године, на мјерило је постављен републички жиг у облику наљепнице број: <?php echo $izvjestaj["izvjestaji_novizig"];?>.</p>

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
</style>