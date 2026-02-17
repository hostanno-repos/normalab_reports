<?php 
    include_once('./connection.php');
    include_once('./class/getObject.php');

    $izvjestajbroj = $izvjestaj['izvjestaji_broj'];
    $izvjestajbroj = explode("/", $izvjestajbroj);
    $izvjestajbroj_ = substr($izvjestajbroj[1], -2);
    $izvjestajbroj = $izvjestajbroj[0] . "/" . $izvjestajbroj_;

    //FETCH RADNI NALOG
    $radninalog = new singleObject;
    $radninalog = $radninalog->fetch_single_object("radninalozi", "radninalozi_id", $izvjestaj['izvjestaji_radninalogid']);

    $radninalogbroj = $radninalog['radninalozi_broj'];
    $radninalogbroj = explode("-", $radninalogbroj);
    $radninalogbroj = $radninalogbroj[count($radninalogbroj) - 1];
    $radninalogbroj = explode("/", $radninalogbroj);
    $radninalogbroj_ = substr($radninalogbroj[1], -2);
    $radninalogbroj = $radninalogbroj[0] . "/" . $radninalogbroj_;

    //FETCH VRSTA UREĐAJA
    $vrstauredjaja = new singleObject;
    $vrstauredjaja = $vrstauredjaja->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $radninalog['radninalozi_vrstauredjajaid']);

    //FETCH PODNOSILAC ZAHTJEVA
    $podnosilaczahtjeva = new singleObject;
    $podnosilaczahtjeva = $podnosilaczahtjeva->fetch_single_object("klijenti", "klijenti_id", $radninalog['radninalozi_klijentid']);

    //FETCH MJERILO
    $mjerilo = new singleObject;
    $mjerilo = $mjerilo->fetch_single_object("mjerila", "mjerila_id", $izvjestaj['izvjestaji_mjeriloid']);

    //FETCH METODA INSPEKCIJE
    $metodainspekcije = new singleObject;
    $metodainspekcije = $metodainspekcije->fetch_single_object("metodeinspekcije", "metodeinspekcije_id", $radninalog['radninalozi_metodainspekcijeid']);
    
    //FETCH VRSTA INSPEKCIJE
    $vrstainspekcije = new singleObject;
    $vrstainspekcije = $vrstainspekcije->fetch_single_object("vrsteinspekcije", "vrsteinspekcije_id", $izvjestaj['izvjestaji_vrstainspekcijeid']);

    $opremaniz = explode(",", $izvjestaj['izvjestaji_opremazainspekciju']);
    $opremafinal = "";
    $proizvodjacfinal = "";
    $serijskifinal = "";
    foreach ($opremaniz as $singleoprema) {
        $opremazainspekciju = new singleObject;
        $opremazainspekciju = $opremazainspekciju->fetch_single_object("opremazainspekciju", "opremazainspekciju_id", $singleoprema);
        $opremafinal = $opremafinal . $opremazainspekciju['opremazainspekciju_naziv'] . "; ";
        $proizvodjacfinal = $proizvodjacfinal . $opremazainspekciju['opremazainspekciju_proizvodjac'] . "; ";
        $serijskifinal = $serijskifinal . $opremazainspekciju['opremazainspekciju_serijskibroj'] . "; ";
    }

    if ($izvjestaj['izvjestaji_mjerilocisto'] == 1) { $izvjestaji_mjerilocisto = "DA"; } else { $izvjestaji_mjerilocisto = "NE"; }
    if ($izvjestaj['izvjestaji_mjerilocjelovito'] == 1) { $izvjestaji_mjerilocjelovito = "DA"; } else { $izvjestaji_mjerilocjelovito = "NE"; }
    if ($izvjestaj['izvjestaji_mjerilocitljivo'] == 1) { $izvjestaji_mjerilocitljivo = "DA"; } else { $izvjestaji_mjerilocitljivo = "NE"; }
    if ($izvjestaj['izvjestaji_mjerilokablovi'] == 1) { $izvjestaji_mjerilokablovi = "DA"; } else { $izvjestaji_mjerilokablovi = "NE"; }
?>

<div class="intro-bata">

    <h5 class="main-headline">
        Izvještaj o ispitivanju mjerila
        <br>
        <?php echo $vrstauredjaja['vrsteuredjaja_naziv'] ?>
    </h5>
    <p>Broj radnog naloga: <?php echo $radninalogbroj ?></p>
    <p>Broj izvještaja: <?php echo $izvjestajbroj ?></p>
    <p>Datum izdavanja: <?php echo date('d.m.Y.', strtotime($izvjestaj['izvjestaji_datumizdavanja'])) ?></p>
    <p>Datum inspekcije: <?php echo date('d.m.Y.', strtotime($izvjestaj['izvjestaji_datuminspekcije'])) ?></p>

    <h5 class="main-headline">1. Podnosilac zahtjeva</h5>
    <p>Naziv ustanove: <?php echo $podnosilaczahtjeva['klijenti_naziv'] ?></p>
    <p>Adresa ustanove: <?php echo $podnosilaczahtjeva['klijenti_adresa'] ?></p>
    <p>Zahtjev za ispitivanje mjerila: <?php echo $izvjestaj['izvjestaji_zahtjevzaispitivanje'] ?></p>

    <h5 class="main-headline">2. Identifikacija mjerila</h5>
    <p>Zadovoljava: <?php echo $mjerilo['mjerila_zadovoljava'] ?></p>
    <p>Proizvođač: <?php echo $mjerilo['mjerila_proizvodjac'] ?></p>
    <p>Tip mjerila: <?php echo $mjerilo['mjerila_tip'] ?></p>
    <p>Serijski broj mjerila: <?php echo $mjerilo['mjerila_serijskibroj'] ?></p>
    <p>Godina proizvodnje: <?php echo $mjerilo['mjerila_godinaproizvodnje'] ?></p>
    <p>Službena oznaka: <?php echo $mjerilo['mjerila_sluzbenaoznaka'] ?></p>
    <?php if ($vrstauredjaja['vrsteuredjaja_id'] == 13 || $vrstauredjaja['vrsteuredjaja_id'] == 14 || $vrstauredjaja['vrsteuredjaja_id'] == 50) { ?>
    <p>Mjerilo krvnog pritiska za djecu i novorođenčad: DA</p>
    <?php } else if ($vrstauredjaja['vrsteuredjaja_id'] == 11 || $vrstauredjaja['vrsteuredjaja_id'] == 12 || $vrstauredjaja['vrsteuredjaja_id'] == 49) { ?>
    <p>Mjerilo krvnog pritiska za djecu i novorođenčad: NE</p>
    <?php } ?>

    <h5 class="main-headline">3. Verifikacija mjerila</h5>
    <p>Mjesto inspekcije: <?php echo $izvjestaj['izvjestaji_mjestoinspekcije'] ?></p>
    <p>Metoda inspekcije: <?php echo $metodainspekcije['metodeinspekcije_naziv'] ?></p>
    <p>Vrsta inspekcije: <?php echo $vrstainspekcije['vrsteinspekcije_naziv'] ?></p>
    <p>Oprema za inspekciju: <?php echo $opremafinal ?></p>
    <p>Proizvođač: <?php $proizvodjacfinal ?></p>
    <p>S/N: <?php echo $serijskifinal ?> </p>

    <br>
    <p style="text-align: justify;"><?php echo $izvjestaj['izvjestaji_opisprocedure'] ?></p>

    <h5 class="main-headline">3.1. Identifikacija ambijentalnih uslova</h5>

    <p>Temperatura [°C]: <?php echo $izvjestaj['izvjestaji_temperatura'] ?></p>
    <p>Vlažnost [%]: <?php echo $izvjestaj['izvjestaji_vlaznost'] ?></p>

    <h5 class="main-headline">3.2. Vizuelni pregled mjerila</h5>

    <div style="width: 80%; margin-left: auto; margin-right: auto; font-size:12px;font-weight: normal;">
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
            <tbody>
                <tr>
                    <th style="border: 1px solid #000; text-align: left;">1. Mjerilo je čisto i uredno:</th>
                    <th style="border: 1px solid #000;width:50px;"><?php echo $izvjestaji_mjerilocisto; ?></th>
                </tr>
                <tr>
                    <th style="border: 1px solid #000; text-align: left;">2. Mjerilo je cjelovito i propisane konstrukcije:</th>
                    <th style="border: 1px solid #000;width:50px;"><?php echo $izvjestaji_mjerilocjelovito; ?></th>
                </tr>
                <tr>
                    <th style="border: 1px solid #000; text-align: left;">3. Mjerilo ima čitljive natpise i oznake:</th>
                    <th style="border: 1px solid #000;width:50px;"><?php echo $izvjestaji_mjerilocitljivo; ?></th>
                </tr>
                <tr>
                    <th style="border: 1px solid #000; text-align: left;">4. Mjerilo jposjeduje napojne kablove i ostale dodatke neophodne za rad:</th>
                    <th style="border: 1px solid #000;width:50px;"><?php echo $izvjestaji_mjerilokablovi; ?></th>
                </tr>
            </tbody>
        </table>
    </div>

    <h5 class="main-headline" style="margin-bottom:0;">4. Ispitivanje greške mjerila</h5>

    <?php
        if ($vrstauredjaja['vrsteuredjaja_id'] != 11 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 12 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 13 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 14 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 49 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 50) {
    ?>
    <p style="text-align: center;">Skraćenice korištene u ispitivanju greške mjerenja:</p>

    <p style="width:50%; float:left; text-align:center;">Xs - Zadana vrijednost mjerne veličine</p>
    <p style="width:50%; float:left; text-align:center;">ΔX - Apsolutna greška mjerenja ΔX = |<Xm>-Xs|</p>
    <p style="width:50%; float:left; text-align:center;">Xm - Izmjerena vrijednost mjerne veličine</p>
    <p style="width:50%; float:left; text-align:center;">δ - Relativna greška mjerenja δ=ΔX/Xs*100%</p>
    <p style="width:50%; float:left; text-align:center;">&lt;Xm&gt; - Srednja vrijednost mjerne veličine</p>
    <?php } ?>

</div>

<style>
    .intro-bata * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    .intro-bata .main-headline{
        text-align: center;
        font-size: 11px;
    }
    .intro-bata .second-headline{
        font-size: 11px;
    }
    .intro-bata p{
        margin:2px 0;
        font-size: 11px;
    }
    table, tr, td, th{
        font-size: 11px;
        font-weight: 500;
    }
</style>