<?php

//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');

    //Kupimo radni nalog
    $radninalog = new singleObject;
    $radninalog = $radninalog->fetch_single_object("radninalozi", "radninalozi_id", $_GET['radninalog']);

    //Kupimo mjerilo
    $mjerilo = new singleObject;
    $mjerilo = $mjerilo->fetch_single_object("mjerila", "mjerila_id", $radninalog['radninalozi_mjeriloid']);

    //Kupimo vrstu uređaja
    $vrstauredjaja_ = new singleObject;
    $vrstauredjaja_ = $vrstauredjaja_->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $radninalog['radninalozi_vrstauredjajaid']);

    //Kupimo klijenta
    $klijent = new singleObject;
    $klijent = $klijent->fetch_single_object("klijenti", "klijenti_id", $radninalog['radninalozi_klijentid']);

    //Kupimo sva mjerila
    $mjerila = new allObjectsBy;
    $mjerila = $mjerila->fetch_all_objects_by("mjerila", "mjerila_vrstauredjajaid", $radninalog['radninalozi_vrstauredjajaid'], "mjerila_serijskibroj", "ASC");

    //Kupimo metodu inspekcije
    $metodainspekcije = new singleObject;
    $metodainspekcije = $metodainspekcije->fetch_single_object("metodeinspekcije", "metodeinspekcije_id", $radninalog['radninalozi_metodainspekcijeid']);

    //Kupimo aktivnu opremu za mjerenje
    $aktivneopreme = new allObjectsBy;
    $aktivneopreme = $aktivneopreme->fetch_all_objects_by("opremazainspekciju", "opremazainspekciju_opremauupotrebi", 1, "opremazainspekciju_id", "ASC");

    //Kupimo mjerne veličine
    $mjernevelicine = new allObjectsBy;
    $mjernevelicine = $mjernevelicine->fetch_all_objects_by("mjernevelicine", "mjernevelicine_vrstauredjajaid", $radninalog['radninalozi_vrstauredjajaid'], "mjernevelicine_id", "ASC");

    //Kupimo kontrolore
    $kontrolori = new allObjects;
    $kontrolori = $kontrolori->fetch_all_objects("kontrolori", "kontrolori_id", "ASC");

    //Kupimo vrste inspekcije
    $vrsteinspekcije = new allObjects;
    $vrsteinspekcije = $vrsteinspekcije->fetch_all_objects("vrsteinspekcije", "vrsteinspekcije_id", "ASC");

    //Kupimo tipove izvještaja
    $tipoviizvjestaja = new allObjects;
    $tipoviizvjestaja = $tipoviizvjestaja->fetch_all_objects("tipoviizvjestaja", "tipoviizvjestaja_id", "ASC");
    ?>

    <main id="main" class="main">

        <!-- NASLOV I BREADCRUMB -->
        <div class="pagetitle">
            <!-- NASLOV -->
            <h1 class="mb-3">Dodaj izvještaj</h1>
            <nav>
                <!-- BREADCRUMB -->
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item"><a href="pregledizvjestaja.php">Pregled izvještaja</a></li>
                    <li class="breadcrumb-item active">Dodaj izvještaj</li>
                </ol>
            </nav>
        </div>

        <!-- IZVJEŠTAJ -->
        <section class="section dashboard">
            <div class="row">
                <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_) ?>" method="post">

                    <!-- NASLOV GLAVNI -->
                    <h6 class="w-100 mb-4 font-weight-bold">Izvještaj o ispitivanju mjerila -
                        <?php echo $vrstauredjaja_['vrsteuredjaja_naziv'] ?>
                    </h6>

                    <!-- BROJ RADNOG NALOGA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_radninalogid">Broj radnog naloga:</label>
                        <input type="number" name="izvjestaji_radninalogid"
                            value="<?php echo $radninalog['radninalozi_id'] ?>" hidden>
                        <p><?php echo $radninalog['radninalozi_broj'] ?></p>
                    </div>

                    <!-- BROJ IZVJEŠTAJA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_broj">Broj izvještaja:</label>
                        <?php
                        $brojNaloga = $radninalog['radninalozi_broj'];
                        $brojNaloga = explode("-", $brojNaloga);
                        $brojNaloga = $brojNaloga[count($brojNaloga) - 1];
                        ?>
                        <input type="text" name="izvjestaji_broj" value="<?php echo $brojNaloga ?>" hidden>
                        <p><?php echo $brojNaloga ?></p>
                    </div>

                    <!-- TIP IZVJEŠTAJA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_tipizvjestajaid">Tip izvještaja:</label>
                        <input 
                            type="number" 
                            name="izvjestaji_tipizvjestajaid" 
                            value="<?php foreach ($tipoviizvjestaja as $tipizvjestaja) {
                                    if($radninalog['radninalozi_vrstauredjajaid'] == $tipizvjestaja['tipoviizvjestaja_vrstauredjajaid']){ echo $tipizvjestaja['tipoviizvjestaja_id'];}
                                } ?>" 
                            hidden>
                        <select name="" id="" class="selectElement_" disabled>
                            <option value="" vrstauredjaja=""></option>
                            <?php foreach ($tipoviizvjestaja as $tipizvjestaja) { ?>
                                <option value="<?php echo $tipizvjestaja['tipoviizvjestaja_id'] ?>" <?php if($radninalog['radninalozi_vrstauredjajaid'] == $tipizvjestaja['tipoviizvjestaja_vrstauredjajaid']){ echo "selected";} ?>>
                                    <?php echo $tipizvjestaja['tipoviizvjestaja_naziv'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- VRSTA UREĐAJA -->
                    <div class=" col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_vrstauredjaja">Vrsta uređaja:</label>
                        <input type="text" name="izvjestaji_vrstauredjaja" id="" value="<?php echo $vrstauredjaja_['vrsteuredjaja_naziv'] ?>" disabled>
                    </div>

                    <!-- DATUM IZDAVANJA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_datumizdavanja">Datum izdavanja:</label>
                        <input type="date" name="izvjestaji_datumizdavanja" value="">
                    </div>

                    <!-- DATUM INSPEKCIJE -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_datuminspekcije">Datum inspekcije:</label>
                        <input type="date" name="izvjestaji_datuminspekcije" value="">
                    </div>

                    <!-- DATUM ZAHTJEVA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_datumzahtjeva">Datum zahtjeva:</label>
                        <input type="date" name="izvjestaji_datumzahtjeva" value="">
                    </div>

                    <!-- DIVIDER -->
                    <hr class="w-100">

                    <!-- NASLOV - PODNOSILAC ZAHTJEVA -->
                    <h6 class="w-100 mb-4 font-weight-bold">1. Podnosilac zahtjeva:</h6>

                    <!-- NAZIV USTANOVE -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="">Naziv ustanove:</label>
                        <p><?php echo $klijent['klijenti_naziv'] ?></p>
                    </div>

                    <!-- ADRESA USTANOVE -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="">Adresa ustanove:</label>
                        <p><?php echo $klijent['klijenti_adresa'] ?></p>
                    </div>

                    <!-- ZAHTJEV ZA ISPITIVANJE MJERILA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_zahtjevzaispitivanje">Zahtjev za ispitivanje mjerila:</label>
                        <input type="text" name="izvjestaji_zahtjevzaispitivanje" value="">
                    </div>

                    <!-- DIVIDER -->
                    <hr class="w-100">

                    <!-- NASLOV - IDENTIFIKACIJA MJERILA -->
                    <h6 class="w-100 mb-4 font-weight-bold">2. Identifikacija mjerila:</h6>

                    <!-- MJERILO -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_mjeriloid">Mjerilo:</label>
                        <input type="text" name="izvjestaji_mjeriloid" value="<?php echo $radninalog['radninalozi_mjeriloid'] ?>" hidden>
                        <input type="text" name="" value="<?php echo $vrstauredjaja_['vrsteuredjaja_naziv']; if(isset($vrstauredjaja_['vrsteuredjaja_opis']) && $vrstauredjaja_['vrsteuredjaja_opis'] != ""){ echo " - ".$vrstauredjaja_['vrsteuredjaja_opis'];} ?>" disabled>
                        <!-- <select name="" id="" class="selectElement_">
                            <option value="" zadovoljava="" proizvodjac="" tip="" serijskibroj="" godinaproizvodnje=""
                                sluzbenaoznaka=""></option>
                            <?php //foreach ($mjerila as $mjerilo) { 
                                //$uredjaj = new singleObject;
                                //$uredjaj = $uredjaj->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $mjerilo['mjerila_vrstauredjajaid']);    
                            ?>
                                <option value="<?php //echo $mjerilo['mjerila_id'] ?>"
                                    zadovoljava="<?php //echo $mjerilo['mjerila_zadovoljava'] ?>"
                                    proizvodjac="<?php //echo $mjerilo['mjerila_proizvodjac'] ?>"
                                    tip="<?php //echo $mjerilo['mjerila_tip'] ?>"
                                    serijskibroj="<?php //echo $mjerilo['mjerila_serijskibroj'] ?>"
                                    godinaproizvodnje="<?php //echo $mjerilo['mjerila_godinaproizvodnje'] ?>"
                                    sluzbenaoznaka="<?php //echo $mjerilo['mjerila_sluzbenaoznaka'] ?>">
                                    <?php //echo $mjerilo['mjerila_serijskibroj'] ?>
                                </option>
                            <?php //} ?>
                        </select> -->
                    </div>

                    <!-- ZADOVOLJAVA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_">Zadovoljava:</label>
                        <input type="text" name="izvjestaji_mjerilozadovoljava" value="<?php echo $mjerilo['mjerila_zadovoljava'] ?>" disabled>
                    </div>

                    <!-- PROIZVOĐAČ -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_">Proizvođač:</label>
                        <input type="text" name="izvjestaji_mjeriloproizvodjac" value="<?php echo $mjerilo['mjerila_proizvodjac'] ?>" disabled>
                    </div>

                    <!-- TIP MJERILA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_">Tip mjerila:</label>
                        <input type="text" name="izvjestaji_mjerilotip" value="<?php echo $mjerilo['mjerila_tip'] ?>" disabled>
                    </div>

                    <!-- SERIJSKI BROJ MJERILA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_">Serijski broj mjerila:</label>
                        <input type="text" name="izvjestaji_mjeriloserijskibroj" value="<?php echo $mjerilo['mjerila_serijskibroj'] ?>" disabled>
                    </div>

                    <!-- GODINA PROIZVODNJE -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_">Godina proizvodnje:</label>
                        <input type="text" name="izvjestaji_mjerilogodinaproizvodnje" value="<?php echo $mjerilo['mjerila_godinaproizvodnje'] ?>" disabled>
                    </div>

                    <!-- SLUŽBENA OZNAKA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_">Službena oznaka:</label>
                        <input type="text" name="izvjestaji_mjerilosluzbenaoznaka" value="<?php echo $mjerilo['mjerila_sluzbenaoznaka'] ?>" disabled>
                    </div>

                    <!-- DIVIDER -->
                    <hr class="w-100">

                    <!-- NASLOV - VERIFIKACIJA MJERILA -->
                    <h6 class="w-100 mb-4 font-weight-bold">3. Verifikacija mjerila:</h6>

                    <!-- MJESTO INSPEKCIJE -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_mjestoinspekcije">Mjesto inspekcije:</label>
                        <input type="text" name="izvjestaji_mjestoinspekcije" value="">
                    </div>

                    <!-- LOKACIJA MJERILA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_lokacijamjerila">Lokacija mjerila:</label>
                        <input type="text" name="izvjestaji_lokacijamjerila" value="">
                    </div>

                    <!-- METODA INSPEKCIJE -->
                    <?php if ($metodainspekcije != false) { ?>
                        <div class="col-lg-3 d-flex flex-column mb-2">
                            <label for="izvjestaji_">Metoda inspekcije:</label>
                            <p><?php echo $metodainspekcije['metodeinspekcije_naziv'] ?></p>
                        </div>
                    <?php } ?>

                    <!-- VRSTA INSPEKCIJE -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_vrstainspekcijeid">Vrsta inspekcije:</label>
                        <select name="izvjestaji_vrstainspekcijeid" id="" class="">
                            <option value=""></option>
                            <?php foreach ($vrsteinspekcije as $vrstainspekcije) { ?>
                                <option value="<?php echo $vrstainspekcije['vrsteinspekcije_id'] ?>">
                                    <?php echo $vrstainspekcije['vrsteinspekcije_naziv'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- NASLOV - OPREMA ZA INSPEKCIJU -->
                    <h6 class="w-100 mb-4 font-weight-bold">Oprema za inspekciju:</h6>
                    <input type="text" name="izvjestaji_opremazainspekciju" value="" hidden>

                    <!-- OPREMA SELECTS -->
                    <?php foreach ($aktivneopreme as $aktivnaoprema) { ?>
                        <div class="col-lg-3 d-flex flex-row mb-2 align-items-start">
                            <input type="checkbox" class="checkOprema m-1 mr-2"
                                idOpreme="<?php echo $aktivnaoprema['opremazainspekciju_id'] ?>">
                            <label for="izvjestaji_"><?php echo $aktivnaoprema['opremazainspekciju_naziv'] ?></label>
                        </div>
                    <?php } ?>

                    <!-- OPIS PROCEDURE -->
                    <div class="col-lg-12 d-flex flex-column mb-2">
                        <label for="izvjestaji_">Opis procedure:</label>
                        <?php
                        $skracenice = "Skraćenice korištene u ispitivanju greške mjerenja:";
                        $legendaArray = ["Xs - Zadana vrijednost mjerene veličine", "ΔX - Apsolutna greška mjerenja ΔX = |&lt;Xm&gt;-Xs|", "Xm - Izmjerena vrijednost mjerene veličine", "δ - Relativna greška mjerenja δ=ΔX/Xs*100%", "&lt;Xm&gt; - Srednja vrijednost mjerene veličine"];
                        ?>
                        <textarea name="izvjestaji_opisprocedure"
                            hidden><?php echo $vrstauredjaja_['vrsteuredjaja_opisprocedure'] ?></textarea>
                        <p><?php echo $vrstauredjaja_['vrsteuredjaja_opisprocedure'] ?></p>
                    </div>

                    <!-- DIVIDER -->
                    <hr class="w-100">

                    <!-- NASLOV - IDENTIFIKACIJA AMBIJENTALNIH USLOVA -->
                    <h6 class="w-100 mb-4 font-weight-bold">3.1. Identifikacija ambijentalnih uslova:</h6>

                    <!-- TEMPERATURA -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_temperatura">Temperatura[°C]: +/-1°C</label>
                        <input type="text" name="izvjestaji_temperatura" value="1">
                    </div>

                    <!-- RELATIVNA VLAŽNOST -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_vlaznost">Relativna vlažnost[%]: +/-1%</label>
                        <input type="text" name="izvjestaji_vlaznost" value="1">
                    </div>

                    <!-- SKINUTI ŽIG -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_skinutizig">Oznaka i serijski broj skinutog republičkog žiga:</label>
                        <input type="text" name="izvjestaji_skinutizig" value="">
                    </div>

                    <!-- DIVIDER -->
                    <hr class="w-100">

                    <!-- NASLOV - VIZUELNI PREGLED MJERILA -->
                    <h6 class="w-100 mb-4 font-weight-bold">3.2. Vizuelni pregled mjerila:</h6>

                    <!-- MJERILO ČISTO I UREDNO -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_mjerilocisto">1. Mjerilo čisto i uredno:</label>
                        <select name="izvjestaji_mjerilocisto" id="">
                            <option value=""></option>
                            <option value="1">DA</option>
                            <option value="0">NE</option>
                        </select>
                    </div>

                    <!-- MJERILO CJELOVITO -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_mjerilocjelovito">2. Mjerilo je cjelovito i propisane konstrukcije:</label>
                        <select name="izvjestaji_mjerilocjelovito" id="">
                            <option value=""></option>
                            <option value="1">DA</option>
                            <option value="0">NE</option>
                        </select>
                    </div>

                    <!-- MJERIVO ČITLJIVO -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_mjerilocitljivo">3. Mjerilo ima čitljive natpise i oznake:</label>
                        <select name="izvjestaji_mjerilocitljivo" id="">
                            <option value=""></option>
                            <option value="1">DA</option>
                            <option value="0">NE</option>
                        </select>
                    </div>

                    <!-- MJERILO POSJEDUJE SVE -->
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="izvjestaji_mjerilokablovi">4. Mjerilo posjeduje napojne kablove i ostale dodatke
                            neophodne za
                            rad:</label>
                        <select name="izvjestaji_mjerilokablovi" id="">
                            <option value=""></option>
                            <option value="1">DA</option>
                            <option value="0">NE</option>
                        </select>
                    </div>

                    <!-- DIVIDER -->
                    <hr class="w-100">

                    <!-- NASLOV - ISPITIVANJE GREŠKE MJERILA -->
                    <h6 class="w-100 mb-4 font-weight-bold">4. Ispitivanje greške mjerila</h6>

                    <?php 
                    //AKO NIJE MJERILO KRVNOG PRITISKA - TREBA NAM LEGENDA
                    if ($vrstauredjaja_['vrsteuredjaja_id'] != 11 && 
                        $vrstauredjaja_['vrsteuredjaja_id'] != 12 && 
                        $vrstauredjaja_['vrsteuredjaja_id'] != 13 && 
                        $vrstauredjaja_['vrsteuredjaja_id'] != 14 && 
                        $vrstauredjaja_['vrsteuredjaja_id'] != 49 && 
                        $vrstauredjaja_['vrsteuredjaja_id'] != 50) { ?>

                        <!-- ISPISUJEMO LEGENDU -->
                        <div class="col-lg-12 d-flex flex-column mb-2">
                            <label for="izvjestaji_"><?php echo $skracenice ?></label>
                        </div>
                        <?php
                        foreach ($legendaArray as $legendaItem) { ?>
                            <div class="col-lg-6 d-flex flex-column mb-2">
                                <label for="izvjestaji_"><?php echo $legendaItem ?></label>
                            </div>
                        <?php }
                    }

                    //FOR PETLJA ZA MJERNE VELIČINE
                    for ($i = 1; $i <= count($mjernevelicine); $i++) {

                        //AKO NIJE MV 142
                        if ($mjernevelicine[$i - 1]['mjernevelicine_id'] != 142) { ?>

                        <!-- GENERIČEMO NASLOV -->
                        <h6 class="w-100 mb-4 font-weight-bold">4.<?php echo $i ?>.
                            <?php echo $mjernevelicine[$i - 1]['mjernevelicine_naziv'] ?>
                        </h6>

                        <?php } ?>

                        <!-- ISPISUJEMO TABELU -->
                        <table class="table table-bordered">

                            <?php 

                            //AKO SU DEFIBRILATORI
                            if ($vrstauredjaja_['vrsteuredjaja_id'] == 3 || 
                                $vrstauredjaja_['vrsteuredjaja_id'] == 18) {
                            ?>
                                <!-- NEMAMO KLASIČNO ZAGLAVLJE -->

                            <!-- AKO NIJE MJERILO KRVNOG PRITISKA -->
                            <?php } else if ($vrstauredjaja_['vrsteuredjaja_id'] != 11 && 
                                $vrstauredjaja_['vrsteuredjaja_id'] != 12 && 
                                $vrstauredjaja_['vrsteuredjaja_id'] != 13 && 
                                $vrstauredjaja_['vrsteuredjaja_id'] != 14 && 
                                $vrstauredjaja_['vrsteuredjaja_id'] != 49 && 
                                $vrstauredjaja_['vrsteuredjaja_id'] != 50) {
                            ?>
                                <!-- ISPISUJEMO HEAD TABELE -->
                                <thead>

                                    <!-- PRVI RED HEADA -->
                                    <tr>
                                        <th class="text-center" scope="col" rowspan="2">Xs
                                            [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                        <th class="text-center" scope="col" colspan="3">Xm
                                            [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                        <th class="text-center" scope="col" rowspan="2">&lt;Xm&gt
                                            [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                        <th class="text-center" scope="col" rowspan="2">ΔX
                                            [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                        <th class="text-center" scope="col" rowspan="2">δ [%]</th>
                                        <th class="text-center" scope="col" rowspan="2">Dozvoljeno odstupanje</th>
                                        <th class="text-center" scope="col" rowspan="2">Usaglašenost</th>
                                    </tr>

                                    <!-- DRUGI RED HEADA -->
                                    <tr>
                                        <th class="text-center" scope="col">1</th>
                                        <th class="text-center" scope="col">2</th>
                                        <th class="text-center" scope="col">3</th>
                                    </tr>
                                </thead>
                            
                            <!-- AKO SU MJERNE VELIČINE ZA TAČNOST I HISTEREZIS - 30,32, 34, 36, 139 -->
                            <?php } else if ($mjernevelicine[$i - 1]['mjernevelicine_id'] == 30 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 32 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 34 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 36 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 139 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 143) { ?>
                            
                                <!-- HEAD ZA TAČNOST I HISTEREZIS -->
                                <thead>

                                    <!-- PRVI RED HEADA -->
                                    <tr>
                                        <th class="text-center align-middle" scope="col" rowspan="2">Pritisak [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                        <th class="text-center align-middle" scope="col" colspan="2">1. ciklus</th>
                                        <th class="text-center align-middle" scope="col" colspan="2">2. ciklus</th>
                                        <th class="text-center align-middle" scope="col" colspan="2">Odstupanje</th>

                                        <!-- AKO NIJE 34, 36 i 139 -->
                                        <?php 
                                        if ($mjernevelicine[$i - 1]['mjernevelicine_id'] != 34 && 
                                            $mjernevelicine[$i - 1]['mjernevelicine_id'] != 36 && 
                                            $mjernevelicine[$i - 1]['mjernevelicine_id'] != 139 && 
                                            $mjernevelicine[$i - 1]['mjernevelicine_id'] != 143) {
                                        ?>
                                                <th class="text-center align-middle" scope="col" colspan="2">Histerezis</th>
                                        <?php } ?>
                                    
                                    </tr>
                                    
                                    <!-- DRUGI RED HEADA -->
                                    <tr>
                                        <th class="text-center align-middle" scope="col">Rastuća</th>
                                        <th class="text-center align-middle" scope="col">Opadajuća</th>
                                        <th class="text-center align-middle" scope="col">Rastuća</th>
                                        <th class="text-center align-middle" scope="col">Opadajuća</th>
                                        <th class="text-center align-middle" scope="col">Rastuća</th>
                                        <th class="text-center align-middle" scope="col">Opadajuća</th>

                                        <!-- AKO NIJE 34, 36 i 139 -->
                                        <?php 
                                        if ($mjernevelicine[$i - 1]['mjernevelicine_id'] != 34 && 
                                            $mjernevelicine[$i - 1]['mjernevelicine_id'] != 36 && 
                                            $mjernevelicine[$i - 1]['mjernevelicine_id'] != 139 && 
                                            $mjernevelicine[$i - 1]['mjernevelicine_id'] != 143) {
                                        ?>
                                                <th class="text-center align-middle" scope="col">1. ciklus</th>
                                                <th class="text-center align-middle" scope="col">2. ciklus</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                            
                            <!-- AKO JE MJERNA VELIČINA 141 -->
                            <?php } else if ($mjernevelicine[$i - 1]['mjernevelicine_id'] == 141 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 145) { ?>
                                <thead>
                                    <tr>
                                        <th class="percent10">Broj mjerenja</th>
                                        <th class="percent9">1</th>
                                        <th class="percent9">2</th>
                                        <th class="percent9">3</th>
                                        <th class="percent9">4</th>
                                        <th class="percent9">5</th>
                                        <th class="percent9">6</th>
                                        <th class="percent9">7</th>
                                        <th class="percent9">8</th>
                                        <th class="percent9">9</th>
                                        <th class="percent9">10</th>
                                    </tr>
                                </thead>

                            <!-- AKO JE MJERNA VELIČNA 142 -->
                            <?php } else if ($mjernevelicine[$i - 1]['mjernevelicine_id'] == 142 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 146) { ?>
                                <thead>
                                    <tr>
                                        <th class="percent10">Broj mjerenja</th>
                                        <th class="percent9">11</th>
                                        <th class="percent9">12</th>
                                        <th class="percent9">13</th>
                                        <th class="percent9">14</th>
                                        <th class="percent9">15</th>
                                        <th class="percent9">16</th>
                                        <th class="percent9">17</th>
                                        <th class="percent9">18</th>
                                        <th class="percent9">19</th>
                                        <th class="percent9">20</th>
                                    </tr>
                                </thead>

                            <!-- SVE OSTALE MJERNE VELIČINE MJERILA KRVNOG PRITISKA -->
                            <?php } else { ?>
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" scope="col" rowspan="2">Pritisak [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                        <th class="text-center align-middle" scope="col" colspan="1">p1 [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                        <th class="text-center align-middle" scope="col" colspan="1">p2 [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                        <th class="text-center align-middle" scope="col" rowspan="2">Razlika p1 - p2 [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                        <th class="text-center align-middle" scope="col" rowspan="2">Stopa ispuštanja pritiska [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>/min]</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" scope="col">1. očitavanje</th>
                                        <th class="text-center" scope="col">Očitavanje nakon 5 minuta</th>
                                    </tr>
                                </thead>
                            <?php } ?>

                            <!-- BODY TABELE -->
                            <tbody>
                                <?php

                                //KUPIMO REFERENTNE VRIJEDNOSTI ZA MJERNU VELIČINU
                                $referentnevrijednosti = new allObjectsBy;
                                $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by("referentnevrijednosti", "referentnevrijednosti_mjernavelicinaid", $mjernevelicine[$i - 1]['mjernevelicine_id'], "referentnevrijednosti_referentnavrijednost", "ASC");

                                //LOOP KROZ SVE REFERENTNE VRIJEDNOSTI
                                foreach ($referentnevrijednosti as $referentnavrijednost) { ?>

                                    <?php
                                    
                                    if ($vrstauredjaja_['vrsteuredjaja_id'] == 3 ||  $vrstauredjaja_['vrsteuredjaja_id'] == 18) {
                                        
                                    if($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 2 || $referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 100){ ?>

                                    <!-- PRVO ZAGLAVLJE ZA PRVA 4 REDA -->
                                    <thead>

                                        <!-- PRVI RED HEADA -->
                                        <tr>
                                            <th class="text-center" scope="col" rowspan="2">Xs
                                                [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                            <th class="text-center" scope="col" colspan="3">Xm
                                                [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                            <th class="text-center" scope="col" rowspan="2">&lt;Xm&gt
                                                [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                            <th class="text-center" scope="col" rowspan="2">ΔX
                                                [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                            <th class="text-center" scope="col" rowspan="2">δ [%]</th>
                                            <th class="text-center" scope="col" rowspan="2">Dozvoljeno odstupanje <?php if($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 2){ echo "[J]";}else{ echo "[%]";} ?></th>
                                            <th class="text-center" scope="col" rowspan="2">Usaglašenost</th>
                                        </tr>

                                        <!-- DRUGI RED HEADA -->
                                        <tr>
                                            <th class="text-center" scope="col">1</th>
                                            <th class="text-center" scope="col">2</th>
                                            <th class="text-center" scope="col">3</th>
                                        </tr>
                                        </thead>
                                    <?php } ?>

                                    <!-- REDOVI ZA UNOS -->
                                    <tr class="singleRed">

                                        <!-- ISPISUJEMO REFERENTNU VRIJEDNOST -->
                                        <td class="text-center refVr">
                                            <?php echo ($referentnavrijednost['referentnevrijednosti_referentnavrijednost']); ?>
                                        </td>

                                        <!-- GENERŠEMO POLJA ZA 3 MJERENJA -->
                                        <?php for ($c = 1; $c <= 3; $c++) { ?>
                                            <td><input type="text"
                                                    name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>"
                                                    value="" class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" step=".01">
                                            </td>
                                        <?php } ?>

                                        <!-- SREDNJA VRIJEDNOST -->
                                        <td class="text-center sredVr"></td>

                                        <!-- APSOLUTNA GREŠKA -->
                                        <td class="text-center apsGr"></td>

                                        <!-- RELATIVNA GREŠKA -->
                                        <td class="text-center relGr"></td>

                                        <!-- DOZVOLJENO ODSTUPANJE -->
                                        <td class="text-center dozvOds">
                                            <?php echo (number_format($referentnavrijednost['referentnevrijednosti_odstupanje'], 2)) ?>
                                        </td>

                                        <!-- USAGLAŠENOST -->
                                        <td class="text-center usaGl"></td>

                                        </tr>

                                    <!-- AKO NISU U PITANJU MJERILA KRVNOG PRITISKA -->
                                    <?php } else if ($vrstauredjaja_['vrsteuredjaja_id'] != 11 && 
                                        $vrstauredjaja_['vrsteuredjaja_id'] != 12 && 
                                        $vrstauredjaja_['vrsteuredjaja_id'] != 13 && 
                                        $vrstauredjaja_['vrsteuredjaja_id'] != 14 && 
                                        $vrstauredjaja_['vrsteuredjaja_id'] != 49 && 
                                        $vrstauredjaja_['vrsteuredjaja_id'] != 50) {
                                    ?>

                                        <!-- GENERIŠEMO REDU TABELI ZA SVAKU REF. VRIJEDNOST -->
                                        <tr class="singleRed">

                                            <!-- ISPISUJEMO REFERENTNU VRIJEDNOST -->
                                            <td class="text-center refVr">
                                                <?php echo ($referentnavrijednost['referentnevrijednosti_referentnavrijednost']); ?>
                                            </td>

                                            <!-- GENERŠEMO POLJA ZA 3 MJERENJA -->
                                            <?php for ($c = 1; $c <= 3; $c++) { ?>
                                                <td><input type="text"
                                                        name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>"
                                                        value="-" class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" step=".01">
                                                </td>
                                            <?php } ?>

                                            <!-- SREDNJA VRIJEDNOST -->
                                            <td class="text-center sredVr"></td>

                                            <!-- APSOLUTNA GREŠKA -->
                                            <td class="text-center apsGr"></td>

                                            <!-- RELATIVNA GREŠKA -->
                                            <td class="text-center relGr"></td>
                                            <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 4){ ?>
                                            <!-- DOZVOLJENO ODSTUPANJE -->
                                            <td class="text-center dozvOds">
                                                <?php echo (number_format($referentnavrijednost['referentnevrijednosti_odstupanje'], 3)) . " W" ?>
                                            </td>
                                            <?php } else { ?>
                                                <!-- DOZVOLJENO ODSTUPANJE -->
                                                <td class="text-center dozvOds">
                                                <?php echo (number_format($referentnavrijednost['referentnevrijednosti_odstupanje'], 2)) . "%" ?>
                                            </td>
                                            <?php } ?>

                                            <!-- USAGLAŠENOST -->
                                            <td class="text-center usaGl"></td>

                                        </tr>
                                    
                                    <!-- AKO SU MJERNE VELIČINE ZA TAČNOST I HISTEREZIS - 30,32, 34, 36, 139 -->
                                    <?php 
                                    } else if ($mjernevelicine[$i - 1]['mjernevelicine_id'] == 30 || 
                                               $mjernevelicine[$i - 1]['mjernevelicine_id'] == 32 || 
                                               $mjernevelicine[$i - 1]['mjernevelicine_id'] == 34 || 
                                               $mjernevelicine[$i - 1]['mjernevelicine_id'] == 36 || 
                                               $mjernevelicine[$i - 1]['mjernevelicine_id'] == 139 || 
                                               $mjernevelicine[$i - 1]['mjernevelicine_id'] == 143) {
                                    ?>
                                        <!-- GENERIŠEMO REDU TABELI ZA SVAKU REF. VRIJEDNOST -->
                                        <tr class="singleRed">

                                            <!-- ISPISUJEMO REFERENTNU VRIJEDNOST -->
                                            <td class="text-center refVr">
                                                <?php echo ($referentnavrijednost ['referentnevrijednosti_referentnavrijednost']); ?>
                                            </td>
                                            
                                            <!-- GENERŠEMO POLJA ZA 4 MJERENJA -->
                                            <?php for ($c = 1; $c <= 4; $c++) { ?>
                                            <td><input 
                                                    type="text" 
                                                    name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>" 
                                                    value="" 
                                                    class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" 
                                                    step=".01">
                                            </td>
                                            <?php } ?>
                                            
                                            <!-- ODSTUPANJE RASTUĆA -->
                                            <td class="text-center odsRast"></td>

                                            <!-- ODSTUPANJE OPADAJUĆA -->
                                            <td class="text-center odsOpad"></td>


                                            <?php 
                                            //AKO SU MJERNE VELIČINE 30, 32
                                            if ($mjernevelicine[$i - 1]['mjernevelicine_id'] == 30 || 
                                                $mjernevelicine[$i - 1]['mjernevelicine_id'] == 32) {
                                            ?>

                                            <!-- HISTEREZEIS 1 -->
                                            <td class="text-center hist1"></td>

                                            <!-- HISTEREZEIS 2 -->
                                            <td class="text-center hist2"></td>

                                            <?php } ?>
                                        </tr>

                                    <!-- AKO JE MJERNA VELIČINA 141 -->
                                    <?php } else if ($mjernevelicine[$i - 1]['mjernevelicine_id'] == 141 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 145) { ?>

                                        <tr class="singleRed mjernavelicina_<?php echo $mjernevelicine[$i - 1]['mjernevelicine_id'] ?>">

                                            <!-- NEMAMO REF. VR. PA ISPISUJEMO SISTOLIČKI ILI DISTOLIČKI -->
                                            <td class="text-center refVr">
                                                <?php if($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 0){ echo "Sistolički pritisak [mmHG]";}else{ echo "Distolički pritisak [mmHG]"; } ?>
                                            </td>

                                            <!-- GENERIŠEMO POLJA ZA PRVIH 10 MJERENJA -->
                                            <?php for ($c = 1; $c <= 10; $c++) { ?>
                                            <td><input type="text"
                                                       name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>" 
                                                       value="1" 
                                                       class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" 
                                                       step=".01">
                                                    </td>
                                            <?php } ?>

                                        </tr>
                                    
                                    <!-- AKO JE MJERNA VELIČINA 142 -->
                                    <?php } else if ($mjernevelicine[$i - 1]['mjernevelicine_id'] == 142 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 146) { ?>

                                        <tr class="singleRed mjernavelicina_<?php echo $mjernevelicine[$i - 1]['mjernevelicine_id'] ?>">

                                            <!-- NEMAMO REF. VR. PA ISPISUJEMO SISTOLIČKI ILI DISTOLIČKI -->
                                            <td class="text-center refVr">
                                                <?php if($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 0){ echo "Sistolički pritisak [mmHG]";}else{ echo "Distolički pritisak [mmHG]"; } ?>
                                            </td>
                                            
                                            <!-- GENERIŠEMO POLJA ZA DRUGIH 10 MJERENJA -->
                                            <?php for ($c = 11; $c <= 20; $c++) { ?>
                                            <td><input type="text"
                                                       name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>"   
                                                       value="1" 
                                                       class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" 
                                                       step=".01">
                                                    </td>
                                            <?php } ?>

                                        </tr>
                                    
                                    <!-- AKO SU SVE OSTALE REF. VR. ZA MJERILA KRVNOG PRITISKA -->
                                    <?php } else { ?>
                                        
                                        <tr class="singleRed mjernavelicina_<?php echo $mjernevelicine[$i - 1]['mjernevelicine_id'] ?>">

                                            <!-- REFERENTNA VRIJEDNOST -->
                                            <td class="text-center refVr">
                                                <?php echo ($referentnavrijednost['referentnevrijednosti_referentnavrijednost']); ?>
                                            </td>

                                            <!-- GENERŠEMO POLJA ZA 2 MJERENJA -->
                                            <?php for ($c = 1; $c <= 2; $c++) { ?>
                                            <td><input type="text"
                                                       name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>"
                                                        value="" 
                                                        class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" 
                                                        step=".01">
                                                    </td>
                                            <?php } ?>
                                            
                                            <!-- RAZLIKA P1-P2 -->
                                            <td class="text-center razp1p2"></td>

                                            <!-- STOPA ISPUŠTANJA -->
                                            <td class="text-center stoIsp"></td>

                                        </tr>
                                    <?php }
                                } ?>
                            </tbody>
                        </table>

                        <!-- AKO JE MJERNA VELIČINA 142  -->
                        <?php if($mjernevelicine[$i - 1]['mjernevelicine_id'] == 142 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 146) { ?>
                        
                        <!-- GREŠKA SISTOLIČKI -->
                        <h6 class="w-100">Greška sistoličkog pritiska [mmHg]: <span id="greskaSistolicni">0,00</span></h6>

                        <!-- NOVI RED -->
                        <br>

                        <!-- GREŠKA DISTOLIČKI -->
                        <h6 class="w-100">Greška distoličkog pritiska [mmHg]: <span id="greskaDistolicni">0,00</span></h6>

                        <!-- POSTAVLJENAVRIJEDNOST -->
                        <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 49){ ?>
                            <div class="w-100 d-flex align-items-center"><h6 class="mr-1 mb-0">Postavljena vrijednost: </h6><input type="text" name="rezultat_142_523_21" id="" class="" value="120/80"></div>
                        <?php } else{ ?>
                            <div class="w-100 d-flex align-items-center"><h6 class="mr-1 mb-0">Postavljena vrijednost: </h6><input type="text" name="rezultat_146_536_21" id="" class="" value="120/80"></div>
                        <?php } ?>
                        

                        <?php } 
                    } ?>

                    <!-- AKO NIJE UREĐAJ 49 -->
                    <?php if($vrstauredjaja_['vrsteuredjaja_id'] != 49 && $vrstauredjaja_['vrsteuredjaja_id'] != 50) { ?>

                        <!-- NOVI ŽIG -->
                        <div class="col-lg-3 d-flex flex-column mb-2 p-0">
                            <label for="izvjestaji_novizig">Novi republički žig:</label>
                            <input type="text" name="izvjestaji_novizig" value="">
                        </div>

                        <!-- NAPOMENA -->
                        <div class="col-lg-12 d-flex flex-column mb-2 p-0">
                            <label for="izvjestaji_napomena">Napomena</label>
                            <textarea name="izvjestaji_napomena" id=""></textarea>
                        </div>

                    <?php } ?>
                    
                    <!-- DIVIDER -->
                    <hr class="w-100">

                    <!-- AKO NISU MJERILA KRVNOG PRITISKA -->
                    <?php 
                    if ($vrstauredjaja_['vrsteuredjaja_id'] != 11 && 
                        $vrstauredjaja_['vrsteuredjaja_id'] != 12 &&
                        $vrstauredjaja_['vrsteuredjaja_id'] != 13 && 
                        $vrstauredjaja_['vrsteuredjaja_id'] != 14 && 
                        $vrstauredjaja_['vrsteuredjaja_id'] != 49 && 
                        $vrstauredjaja_['vrsteuredjaja_id'] != 50) { 
                    ?>
                    
                    <!-- IZJAVA OUSAGLAČENOSTI -->
                    <h6 class="w-100 mb-4 font-weight-bold">5. Izjava o usaglašenosti</h6>

                    <!-- AKO JESU MJERILA KRVNOG PRITISKA -->
                    <?php 
                    } else if ($vrstauredjaja_['vrsteuredjaja_id'] == 11 || 
                               $vrstauredjaja_['vrsteuredjaja_id'] == 13 || 
                               $vrstauredjaja_['vrsteuredjaja_id'] == 12 || 
                               $vrstauredjaja_['vrsteuredjaja_id'] == 14 || 
                               $vrstauredjaja_['vrsteuredjaja_id'] == 49 || 
                               $vrstauredjaja_['vrsteuredjaja_id'] == 50) {
                    ?>

                    <!-- REZULTATI ISPITIVANJA  MJERILA -->
                    <h6 class="w-100 mb-4 font-weight-bold">5. Rezultati ispitivanja mjerila</h6>
                    
                    <!-- TABELA -->
                    <table class="table table-bordered">

                        <thead>

                            <!-- HEAD - JER JE SVA TABELA USTVARI <thead> -->
                            <tr>

                                <!-- NEVIDLJIVA ĆELIJA -->
                                <th class="text-center" scope="col" style="border-top:1px solid transparent;border-left:1px solid transparent;"></th>

                                <!-- MAKSIMALNO ODSTUPANJE -->
                                <th class="text-center" scope="col">Maksimalno odstupanje</th>

                                <!-- MAKSIMALNO DOZVOLJENO ODSTUPANJE -->
                                <th class="text-center" scope="col">Maksimalno dozvoljeno odstupanje</th>

                                <!-- ZADOVOLJAVA -->
                                <th class="text-center" scope="col">Zadovoljava</th>

                            </tr>

                            <!-- PRVI RED -->
                            <tr>

                                <!-- TAČNOST POKAZIVANJA -->
                                <th class="text-center" scope="col">Tačnost pokazivanja</th>

                                <!-- MAX1 -->
                                <th class="text-center max1" scope="col"></th>

                                <!-- AKO JE VRSTA UREĐAJA 49 -->
                                <?php if ($vrstauredjaja_['vrsteuredjaja_id'] == 49 || $vrstauredjaja_['vrsteuredjaja_id'] == 50 || $vrstauredjaja_['vrsteuredjaja_id'] == 11 || $vrstauredjaja_['vrsteuredjaja_id'] == 12 || $vrstauredjaja_['vrsteuredjaja_id'] == 13 || $vrstauredjaja_['vrsteuredjaja_id'] == 14) { ?>

                                <!-- MAKS. DOZVOLJENO ODSTUPANJE JE 3 -->
                                <th class="text-center" scope="col">3</th>

                                <!-- ZA OSTALE -->
                                <?php } else { ?>
                                   
                                <!-- MAKS. DOZVOLJENO ODSTUPANJE JE 4 -->
                                <th class="text-center" scope="col">4</th>

                                <?php } ?>

                                <!-- USAGLAŠENOST 1 -->
                                <th class="text-center usaGl1" scope="col"></th>
                            </tr>
                            
                            <!-- AKO NIJE MJERILO 49 -->
                            <?php if ($vrstauredjaja_['vrsteuredjaja_id'] != 49 && $vrstauredjaja_['vrsteuredjaja_id'] != 50) {  
                            ?>
                            
                            <!-- DRUGI RED -->
                                <tr>
                                    
                                    <?php 
                                    //AKO SU UREĐAJI 11, 13
                                    if ($vrstauredjaja_['vrsteuredjaja_id'] == 11 || 
                                        $vrstauredjaja_['vrsteuredjaja_id'] == 13) { 
                                    ?>
                                    
                                    <!-- HISTEREZIS -->
                                    <th class="text-center" scope="col">Histerezis</th>
                                    
                                    <!-- AKO NISU 11, 13 -->
                                    <?php } else { ?>
                                    
                                    <!-- UTICAJ ŽIVE -->
                                    <th class="text-center" scope="col">Uticaj žive na rad mjerila</th>
                                        
                                    <?php }
                                    //AKO SU UREĐAJI 11, 13
                                    if ($vrstauredjaja_['vrsteuredjaja_id'] == 11 || 
                                        $vrstauredjaja_['vrsteuredjaja_id'] == 13) { 
                                    ?>
                                    
                                    <!-- MAX2 -->
                                    <th class="text-center max2" scope="col"></th>
                                    
                                    <!-- AKO NISU 11, 13 -->
                                    <?php } else { ?>
                                    
                                    <th class="text-center" scope="col">
                                        <input class="text-right rezultat_0_0_1 mjerenje" 
                                            type="number" 
                                            name="rezultat_0_0_1" 
                                            value="0.00" 
                                            step=".01">
                                    </th>
                                    
                                    <?php }
                                    //AKO SU UREĐAJI 11, 13
                                    if ($vrstauredjaja_['vrsteuredjaja_id'] == 11 || 
                                        $vrstauredjaja_['vrsteuredjaja_id'] == 13) {
                                    ?>
                                    
                                    <!-- MAKS. DOZV. ODSTUPANJE je 4 -->
                                    <th class="text-center" scope="col">4</th>
                                    
                                    <!-- AKO NISU UREĐAJI 11, 13 -->
                                    <?php } else { ?>
                                    
                                    <!-- MAKS. DOZV. ODSTUPANJE je 1.5 -->
                                    <th class="text-center" scope="col">1.5</th>
                                    
                                    <?php } ?>
                                    
                                    <!-- USAGLAŠENOST 2 -->
                                    <th class="text-center usaGl2" scope="col"></th>

                                </tr>
                            <?php } ?>
                            
                            <!-- TREĆI RED -->
                            <tr>

                                <!-- ISPITIVANJE CURENJA ZRAKA -->
                                <th class="text-center" scope="col">Ispitivanje curenja zraka</th>

                                <!-- MAX3 -->
                                <th class="text-center max3" scope="col"></th>

                                <!-- AKO JE UREĐAJ 49 -->
                                <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 49 || $vrstauredjaja_['vrsteuredjaja_id'] == 50){ ?>

                                <!-- MAKS. DOZV. ODSTUPANJE JE 6 -->
                                <th class="text-center" scope="col">6</th>

                                <!-- AKO NIJE UREĐAJ 49 -->
                                <?php } else { ?>
                                
                                <!-- MAKS. DOZV. ODSTUPANJE JE 4 -->
                                <th class="text-center" scope="col">4</th>

                                <?php } ?>

                                <!-- USAGLAŠENOST 3 -->
                                <th class="text-center usaGl3" scope="col"></th>
                            </tr>

                            <!-- ČETVRTI RED -->
                            <tr>

                                <!-- ISPITIVANJE VENTILA BRZOG ISPUSTA -->
                                <th class="text-center" scope="col">Ispitivanja ventila brzog ispusta</th>
                                        
                                <!-- MAX4 -->
                                <th class="text-center max4" scope="col">     
                                    <input class="text-right rezultat_0_0_0 mjerenje" 
                                           type="number" 
                                           name="rezultat_0_0_0"
                                           value="0.00" 
                                           step=".01">
                                </th>
                                
                                <th class="text-center" scope="col">
                                <!-- AKO JE UREĐAJ 11, 12, 49 -> MAKS. DOZV. ODS. JE 10 -->
                                <?php if ($vrstauredjaja_['vrsteuredjaja_id'] == 11 || 
                                          $vrstauredjaja_['vrsteuredjaja_id'] == 12 || 
                                          $vrstauredjaja_['vrsteuredjaja_id'] == 49) {
                                    echo "10";
                                //AKO JE UREĐAJ 11, 12, 49 -> MAKS. DOZV. ODS. JE 5
                                } else {
                                    echo "5";
                                } ?>
                                </th>

                                <!-- USAGLAŠENOST 4 -->
                                <th class="text-center usaGl4" scope="col"></th>
                            </tr>

                            <!-- AKO JE UREĐAJ 49 -->
                            <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 49 || $vrstauredjaja_['vrsteuredjaja_id'] == 50){ ?>

                            <!-- PETI RED -->
                            <tr>

                                <!-- PONOVLJIVOST -->
                                <th class="text-center" scope="col">Ponovljivost</th>

                                <!-- MAX5 -->
                                <th class="text-center max5" scope="col"></th>

                                <!-- MAKS. DOZV. ODSTUPANJE -->
                                <th class="text-center" scope="col">3</th>

                                <!-- USAGLAŠENOST 5 -->
                                <th class="text-center usaGl5" scope="col"></th>

                            </tr>
                            <?php } ?>
                        </thead>
                    </table>

                    <?php 
                    //AKO JE UREĐAJ 12, 14
                    if ($vrstauredjaja_['vrsteuredjaja_id'] == 12 || 
                        $vrstauredjaja_['vrsteuredjaja_id'] == 14) {
                    ?>
                    <!-- TABELA -->
                    <table class="table table-bordered">

                        <!-- HEAD -->
                        <thead>
                            <tr>
                                <th>Pregled</th>
                                <th>Zadovoljava</th>
                            </tr>
                        </thead>

                        <!-- BODY -->
                        <tbody>
                            <tr>
                                <!-- ISPITIVANJE CURENJA ŽIVE -->
                                <td>Ispitivanje curenja žive</td>

                                <!-- SELECT -->
                                <td>
                                    <select name="rezultat_0_0_2" id="" class="w-100">
                                        <option value=""></option>
                                        <option value="1">DA</option>
                                        <option value="0">NE</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <!-- ISPITIVANJE MEHANIZMA -->
                                <td>Ispitivanje mehanizma za zaključavanje žive</td>

                                <!-- SELECT -->
                                <td>
                                    <select name="rezultat_0_0_3" id="" class="w-100">
                                        <option value=""></option>
                                        <option value="1">DA</option>
                                        <option value="0">NE</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <!-- ISPITIVANJE KVALITETA -->
                                <td>Ispitivanje kvaliteta žive</td>

                                <!-- SELECT -->
                                <td>
                                    <select name="rezultat_0_0_4" id="" class="w-100">
                                        <option value=""></option>
                                        <option value="1">DA</option>
                                        <option value="0">NE</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <?php } 
                    
                    //AKO JE UREĐAJ 49 -->
                    if($vrstauredjaja_['vrsteuredjaja_id'] == 49 || $vrstauredjaja_['vrsteuredjaja_id'] == 50) { ?>

                    <!-- NOVI ŽIG -->
                    <div class="col-lg-3 d-flex flex-column mb-2 p-0">
                        <label for="izvjestaji_novizig">Novi republički žig:</label>
                        <input type="text" name="izvjestaji_novizig" value="">
                    </div>

                    <!-- NAPOMENA -->
                    <div class="col-lg-12 d-flex flex-column mb-2 p-0">
                        <label for="izvjestaji_napomena">Napomena</label>
                        <textarea name="izvjestaji_napomena" id=""></textarea>
                    </div>

                    <?php } ?>
                    
                    <!-- NASLOV - IZJAVA O USAGLAŠENOSTI -->
                    <h6 class="w-100 mb-4 font-weight-bold">6. Izjava o usaglašenosti</h6>

                    <!-- AKO NISU MJERILA KRVNOG PRITISKA -->
                    <?php } else { ?>

                    <!-- NASLOV - IZJAVA O USAGLAŠENOSTI -->
                    <h6 class="w-100 mb-4 font-weight-bold">6. Izjava o usaglašenosti</h6>

                    <?php } ?>

                    <!-- IZJAVA O USAGLAŠENOSTI -->
                    <div class="col-lg-12 d-flex flex-column mb-2 p-0">
                        <p class="">Rezultati inspekcije mjerila <span class="usaGlFinal"></span> sa propisanim opsegom dozvoljenih odstupanja u skladu sa gore navedenim Pravilnikom.<br>Na osnovu rezultata inspekcije mjerilo je označeno inspekcijskom oznakom - markicom.<br>Rezultati inspekcije se odnose isključivo na dati predmet u trenutku inspekcije.<br>Izvještaj o inspekciji ne smije se reprodukovati osim u cjelini.</p>
                    </div>

                    <!-- INSPEKCIJU IZVRŠIO -->
                    <div class="col-lg-3 d-flex flex-column mb-2 p-0">
                        <label for="izvjestaji_izvrsioid">Inspekciju izvršio i izvještaj izradio:</label>
                        <input type="text" name="izvjestaji_izvrsioid" value="" hidden>
                        <select name="" id="" class="selectElement_">
                            <option value=""></option>
                            <?php foreach ($kontrolori as $kontrolor) { ?>
                                <option value="<?php echo $kontrolor['kontrolori_id'] ?>">
                                    <?php echo $kontrolor['kontrolori_ime'] . " " . $kontrolor['kontrolori_prezime'] ?>
                                </option>
                            <?php } ?>
                        </select>

                        <!-- DATUM -->
                        <label for="izvjestaji_izvrsiodadatum">Datum:</label>
                        <input type="date" name="izvjestaji_izvrsiodadatum">
                    </div>

                    <!-- PRAZNE KOLONE -->
                    <div class="col-lg-3 d-flex flex-column mb-2 p-0"></div>
                    <div class="col-lg-3 d-flex flex-column mb-2 p-0"></div>

                    <!-- IZVJEŠTAJ OVJERIO -->
                    <div class="col-lg-3 d-flex flex-column mb-2 p-0">
                        <label for="izvjestaji_ovjerioid">Izvještaj ovjerio:</label>
                        <input type="text" name="izvjestaji_ovjerioid" value="" hidden>
                        <select name="" id="" class="selectElement_">
                            <option value=""></option>
                            <?php foreach ($kontrolori as $kontrolor) { ?>
                                <option value="<?php echo $kontrolor['kontrolori_id'] ?>">
                                    <?php echo $kontrolor['kontrolori_ime'] . " " . $kontrolor['kontrolori_prezime'] ?>
                                </option>
                            <?php } ?>
                        </select>

                        <!-- DATUM -->
                        <label for="izvjestaji_ovjeriodatum">Datum:</label>
                        <input type="date" name="izvjestaji_ovjeriodatum">
                    </div>

                    <!-- SAČUVAJ IZVJEŠTAJ -->
                    <div class="col-lg-12 d-flex flex-column mt-3">
                        <button name="sacuvaj_izvjestaji" class="btn btn-primary" type="submit"
                            style="width:150px">Sačuvaj</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <style>
        p,
        h6 {
            color: #000;
        }
        .percent10{
            width:10%;
        }
        .percent9{
            width:9%;
        }
        .percent9 input{
            width:100%;
        }
    </style>

    <script>

        $(document).ready(function () {

            //STANDARDNA DEVIJACIJA
            function stdev(arr) {
                if (arr.length < 2) return 0;
                var mean = arr.reduce(function(sum, value) {
                    return sum + value;
                }, 0) / arr.length;
                var sumOfSquares = arr.reduce(function(sum, value) {
                    return sum + Math.pow(value - mean, 2);
                }, 0);
                var variance = sumOfSquares / (arr.length - 1);
                return Math.sqrt(variance);
            }

            //SELECT BOX PROMJENE
            $(".selectElement_").change(function () {
                //selected item id
                var selectValue = $(this).val();
                $(this).prev().val(selectValue);
                //zadovoljava
                var zadovoljava = $('option:selected', this).attr('zadovoljava');
                //console.log(zadovoljava);
                if (zadovoljava != undefined) {
                    $('input[name="izvjestaji_mjerilozadovoljava"]').val(zadovoljava);
                }
                //proizvodjac
                var proizvodjac = $('option:selected', this).attr('proizvodjac');
                if (proizvodjac != undefined) {
                    $('input[name="izvjestaji_mjeriloproizvodjac"]').val(proizvodjac);
                }
                //tip
                var tip = $('option:selected', this).attr('tip');
                if (tip != undefined) {
                    $('input[name="izvjestaji_mjerilotip"]').val(tip);
                }
                //serijski broj
                var serijskibroj = $('option:selected', this).attr('serijskibroj');
                if (serijskibroj != undefined) {
                    $('input[name="izvjestaji_mjeriloserijskibroj"]').val(serijskibroj);
                }
                //godina proizvodnje
                var godinaproizvodnje = $('option:selected', this).attr('godinaproizvodnje');
                if (godinaproizvodnje != undefined) {
                    $('input[name="izvjestaji_mjerilogodinaproizvodnje"]').val(godinaproizvodnje);
                }
                //sluzbena oznaka
                var sluzbenaoznaka = $('option:selected', this).attr('sluzbenaoznaka');
                if (sluzbenaoznaka != undefined) {
                    $('input[name="izvjestaji_mjerilosluzbenaoznaka"]').val(sluzbenaoznaka);
                }
                //vrsta uredjaja
                var vrstauredjaja = $('option:selected', this).attr('vrstauredjaja');
                if (vrstauredjaja != undefined) {
                    $('input[name="izvjestaji_vrstauredjaja"]').val(vrstauredjaja);
                }
            });

            //CHEKIRANJE OPREME ZA INSPEKCIJU
            var opremaZaInspekcijuArray = [];
            $(".checkOprema").click(function () {
                var idOpreme = $(this).attr("idOpreme");
                if (jQuery.inArray(idOpreme, opremaZaInspekcijuArray) !== -1) {
                    opremaZaInspekcijuArray = jQuery.grep(opremaZaInspekcijuArray, function (value) {
                        return value != idOpreme;
                    });
                    $('input[name="izvjestaji_opremazainspekciju"]').val(opremaZaInspekcijuArray);
                } else {
                    opremaZaInspekcijuArray.push($(this).attr("idOpreme"));
                    $('input[name="izvjestaji_opremazainspekciju"]').val(opremaZaInspekcijuArray);
                };
            });

            //PRORAČUN PRILIKOM UNOSA MJERENJA
            <?php
            //AKO NISU MJERILA KRVNOG PRITISKA
            if ($vrstauredjaja_['vrsteuredjaja_id'] != 11 && 
                $vrstauredjaja_['vrsteuredjaja_id'] != 12 && 
                $vrstauredjaja_['vrsteuredjaja_id'] != 13 && 
                $vrstauredjaja_['vrsteuredjaja_id'] != 14 && 
                $vrstauredjaja_['vrsteuredjaja_id'] != 49 && 
                $vrstauredjaja_['vrsteuredjaja_id'] != 50) {
            ?>

            //FUNKCIJA ZA PRORAČUN
            function proracunTabela() {

                //Setujemo brojač na 0
                var $i = 0;
                var usaGlFinal = "USAGLAŠENI su";
                //ZA SVAKI RED U TABELI
                $(".singleRed").each(function () {

                    //KUPIMO REFERENTNU VRIJEDNOST
                    var refVr = parseFloat($(this).find(".refVr").html()).toFixed(2);
                    
                    //SETUJEMO SREDNJU VRUIJEDNOST NA 0
                    var sredVr = 0;
                    
                    //SETUJEMO APSOLUTNA GREŠKA = REFERENTNA VRIJEDNOST
                    var apsGr = refVr;

                    // SETUJEMO RELATIVNU GREĐKU NA 100
                    var relGr = 100;

                    //SETUJEMO DOZVOLJENO ODSTUPANJE NA 0
                    var dozvOds = 0;

                    //SETUJEMO USAGLAŠENOST NA NE
                    var usaGl = "NE";
                        
                    //SETUJEMO MAIN USAGLAŠENOST
                    //var usaGlFinal = "USAGLAŠENI su";

                    //KUPIMO PRVO MJERENJE AKO POSTOJI
                    if ($(this).find(".mjerenje1").val().length === 0) {
                        var mjerenje1 = 0;
                    } else {
                        var mjerenje1 = $(this).find(".mjerenje1").val();
                    }

                    //KUPIMO DRUGO MJERENJE AKO POSTOJI
                    if ($(this).find(".mjerenje2").val().length === 0) {
                        var mjerenje2 = 0;
                    } else {
                        var mjerenje2 = $(this).find(".mjerenje2").val();
                    }

                    //KUPIMO TREĆE MJERENJE AKO POSTOJI
                    if ($(this).find(".mjerenje3").val().length === 0) {
                        var mjerenje3 = 0;
                    } else {
                        var mjerenje3 = $(this).find(".mjerenje3").val();
                    }

                    //UPISUJEMO PRVO MJERENJE
                    $(this).find(".mjerenje1").val(mjerenje1);

                    //UPISUJEMO DRUGO MJERENJE
                    $(this).find(".mjerenje2").val(mjerenje2);

                    //UPISUJEMO TREĆE MJERENJE
                    $(this).find(".mjerenje3").val(mjerenje3);

                    //UKLANJAMO "%" IZ DOZVOLJENOG ODSTUPANJA
                    dozvOds = $(this).find(".dozvOds").html();
                    <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 4){ ?>
                    dozvOds = dozvOds.replace('%', '');
                    dozvOds = (parseFloat(dozvOds)).toFixed(3);
                    <?php } else { ?>
                    dozvOds = dozvOds.replace('%', '');
                    dozvOds = (parseFloat(dozvOds)).toFixed(2);
                    <?php } ?>

                    //AKO SU NAM SVA TRI MJERENJA "-"
                    if (mjerenje1 == "-" && mjerenje2 == "-" && mjerenje3 == "-") {
                        sredVr = "-";
                        $(this).find(".sredVr").html(sredVr);
                        apsGr = "-";
                        $(this).find(".apsGr").html(apsGr);
                        relGr = "-";
                        $(this).find(".relGr").html(relGr);
                        dozvOds = "-";
                        $(this).find(".dozvOds").html(dozvOds);
                        usaGl = "-";
                        $(this).find(".usaGl").html(usaGl);
                    
                    } else if (mjerenje1 == "--" || mjerenje2 == "--" || mjerenje3 == "--") {
                        sredVr = "-";
                        $(this).find(".sredVr").html(sredVr);
                        apsGr = "-";
                        $(this).find(".apsGr").html(apsGr);
                        relGr = "-";
                        $(this).find(".relGr").html(relGr);
                        dozvOds = "-";
                        $(this).find(".dozvOds").html(dozvOds);
                        usaGl = "NE";
                        $(this).find(".usaGl").html(usaGl);
                    //AKO NISU SVA TRI MJERENJA "-"
                    } else {

                        //RAČUNAMO SREDNJU VRIJEDNOST MJERENJA
                        sredVr = (parseFloat(mjerenje1, 10) + parseFloat(mjerenje2, 10) + parseFloat(mjerenje3, 10)) / 3;
                        
                        //UPISUJEMO SREDNJU VRIJEDNOST U TABELU
                        <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 4){ ?>
                            $(this).find(".sredVr").html((sredVr).toFixed(3));
                        <?php } else { ?>
                            $(this).find(".sredVr").html((sredVr).toFixed(2));
                        <?php } ?>

                        //RAČUNAMO APSOLUTNU GREŠKU
                        apsGr = Math.abs(sredVr - refVr);
                            
                        //UPISUJEMO APSOLUTNU GREŠKU
                        <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 4){ ?>
                            $(this).find(".apsGr").html((apsGr).toFixed(3));
                        <?php } else { ?>
                            $(this).find(".apsGr").html((apsGr).toFixed(2));
                        <?php } ?>
                        
                        //ODUSTAO ???
                        if (refVr == 0) {
                            //refVr = 1;
                        }
                        
                        //RAČUNAMO RELATIVNU GREŠKU
                        relGr = Math.abs(apsGr / refVr * 100);

                        //UPISUJEMO RELATIVNU GREŠKU
                        $(this).find(".relGr").html((relGr).toFixed(2));
                    }
                    <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 3 || $vrstauredjaja_['vrsteuredjaja_id'] == 18){ ?>

                        if(refVr == 2.00 || refVr == 10.00 || refVr == 30.00 || refVr == 70.00){
                            //AKO JE RELATIVNA GREŠKA VEĆA OD DOZVOLJENOG ODSTUPANJA
                            if (apsGr > dozvOds) {
                                usaGl = "NE";
                                usaGlFinal = "NISU USAGLAŠENI";
                            
                            //AKO SU SVA MJERENJA "-"
                            } else if (mjerenje1 != "-" && mjerenje2 != "-" && mjerenje3 != "-" && mjerenje1 != "--" && mjerenje2 != "--" && mjerenje3 != "--") {
                                usaGl = "DA";
                            }
                        }else{
                            //AKO JE RELATIVNA GREŠKA VEĆA OD DOZVOLJENOG ODSTUPANJA
                            if (relGr > dozvOds) {
                                usaGl = "NE";
                                usaGlFinal = "NISU USAGLAŠENI";
                            
                            //AKO SU SVA MJERENJA "-"
                            } else if (mjerenje1 != "-" && mjerenje2 != "-" && mjerenje3 != "-" && mjerenje1 != "--" && mjerenje2 != "--" && mjerenje3 != "--") {
                                usaGl = "DA";
                            }
                        }

                    <?php }else{ ?>
                        //AKO JE RELATIVNA GREŠKA VEĆA OD DOZVOLJENOG ODSTUPANJA
                        <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 4){ ?>
                            if (apsGr > dozvOds) {
                            usaGl = "NE";
                            usaGlFinal = "NISU USAGLAŠENI";
                            
                            //AKO SU SVA MJERENJA "-"
                            } else if (mjerenje1 != "-" && mjerenje2 != "-" && mjerenje3 != "-" && mjerenje1 != "--" && mjerenje2 != "--" && mjerenje3 != "--") {
                                usaGl = "DA";
                            }
                        <?php } else { ?>
                            if (relGr > dozvOds) {
                            usaGl = "NE";
                            usaGlFinal = "NISU USAGLAŠENI";
                            
                            //AKO SU SVA MJERENJA "-"
                            } else if (mjerenje1 != "-" && mjerenje2 != "-" && mjerenje3 != "-" && mjerenje1 != "--" && mjerenje2 != "--" && mjerenje3 != "--") {
                                usaGl = "DA";
                            }
                        <?php } ?>
                    <?php } ?>

                    //UPISUJEMO USAGLAŠENOST REDA
                    $(this).find(".usaGl").html(usaGl);

                    //UPISUJEMO GLAVNU USAGLAŠENOST
                    $("span.usaGlFinal").html(usaGlFinal);

                    console.log(usaGlFinal);
                });
            }

            //AKO JESU MJERILA KRVNOG PRITISKA
            <?php } else { ?>

            //FUNKCIJA ZA PRORAČUN
            function proracunTabela() {

                //INICIJALIZACIJA ZA MAKSIMUME
                var max1 = Array();
                var max2 = Array();
                var max3 = Array();
                var max5 = Array();

                //SETUJEMO BROJAČ NA 0
                var $i = 0;

                //ZA SVAKI RED U TABELI
                $(".singleRed").each(function () {
                    
                    //AKO JE MJERNA VELIČINA 31, 33, 35, 37 ili 140
                    if ($(this).hasClass("mjernavelicina_31") || $(this).hasClass("mjernavelicina_33") || $(this).hasClass("mjernavelicina_35") || $(this).hasClass("mjernavelicina_37") || $(this).hasClass("mjernavelicina_140") || $(this).hasClass("mjernavelicina_144")) {
                        
                        //KUPIMO REFERENTNU VRIJEDNOST
                        var refVr = parseFloat($(this).find(".refVr").html()).toFixed(2);

                        //SETUJEMO RAZLIKU P1 i P2 na 0
                        var razp1p2 = 0;

                        //SETUJEMO STOPU ISPUŠTANJA NA 0
                        var stoIsp = 0;

                        //SETUJEMO GLAVNU USAGLAŠENOST
                        //var usaGlFinal = "USAGLAŠENI su";


                        //KUPIMO PRVO MJERENJE AKO POSTOJI
                        if ($(this).find(".mjerenje1").val().length === 0) {
                            var mjerenje1 = 0;
                        } else {
                            var mjerenje1 = $(this).find(".mjerenje1").val();
                        }

                        //UPISUJEMO PRVO MJERENJE
                        $(this).find(".mjerenje1").val(mjerenje1);

                        //KUPIMO DRUGO MJERENJE AKO POSTOJI
                        if ($(this).find(".mjerenje2").val().length === 0) {
                            var mjerenje2 = 0;
                        } else {
                            var mjerenje2 = $(this).find(".mjerenje2").val();
                        }

                        //UPISUJEMO DRUGO MJERENJE
                        $(this).find(".mjerenje2").val(mjerenje2);

                        //AKO SU OBA MJERENJA "-"
                        if (mjerenje1 == "-" && mjerenje2 == "-") {
                            //SETUJEMO I UPISUJEMO SVE KAO "-"
                            razp1p2 = "-";
                            $(this).find(".odsRast").html(razp1p2);
                            stoIsp = "-";
                            $(this).find(".odsOpad").html(stoIsp);
                        
                        //AKO MJERENJA NISU "-"
                        } else {

                            //RAČUNAMO RAZLIKU
                            razp1p2 = Math.abs(parseFloat(mjerenje2) - parseFloat(mjerenje1));

                            //UPISUJEMO RAZLIKU
                            $(this).find(".razp1p2").html(razp1p2);

                            //RAČUNAMO STOPU ISPUŠTANJA
                            stoIsp = (razp1p2 / 5).toFixed(2);

                            //DODAJEMO STOPU ISPUŠTANJA U NIZ
                            max3.push(stoIsp);

                            //UPISUJEMO STOPU ISPUŠTANJA
                            $(this).find(".stoIsp").html(stoIsp);

                        }

                    //AKO JE MJERNA VELIČINA 141, 142
                    } else if ($(this).hasClass("mjernavelicina_141") || $(this).hasClass("mjernavelicina_142")) {
                        
                        //SETUJEMO USAGLAŠENOST
                        var usaGlFinal = "USAGLAŠENI su";

                        //INICIRAMO NIZOVE ZA 20 MJERENJA
                        var sistolickiNiz = [];
                        var distolickiNiz = [];

                        //KUPIMO PRVIH 10 MJERENJA u NIZOVE
                        for(var i = 1; i <= 10; i++){
                            if($("input[name='rezultat_141_520_"+i+"']").val() != ""){
                                sistolickiNiz.push(parseInt($("input[name='rezultat_141_520_"+i+"']").val(),10));
                            }
                            if($("input[name='rezultat_141_521_"+i+"']").val() != ""){
                                distolickiNiz.push(parseInt($("input[name='rezultat_141_521_"+i+"']").val(),10));
                            }
                        }

                        //KUPIMO DRUGIH 10 MJERENJA u NIZOVE
                        for(var i = 11; i <= 20; i++){
                            if($("input[name='rezultat_142_522_"+i+"']").val() != ""){
                                sistolickiNiz.push(parseInt($("input[name='rezultat_142_522_"+i+"']").val(),10));
                            }
                            if($("input[name='rezultat_142_523_"+i+"']").val() != ""){
                                distolickiNiz.push(parseInt($("input[name='rezultat_142_523_"+i+"']").val(),10));
                            }
                        }

                        //DEVIJACIJA PRVOG NIZA
                        var stdevSist = stdev(sistolickiNiz).toFixed(2);

                        //DEVIJACIJA DRUGOG NIZA
                        var stdevDist = stdev(distolickiNiz).toFixed(2); 

                        //UPISUJEMO REZULTATE DEVIJACIJA KAO GREŠKE
                        $("#greskaSistolicni").html(stdevSist);
                        $("#greskaDistolicni").html(stdevDist);

                        //UPISUJEMO GREŠKE U NIZZA MAKSIMALNU GREŠKU
                        max5.push(stdevSist);
                        max5.push(stdevDist);
                    
                    } else if ($(this).hasClass("mjernavelicina_145") || $(this).hasClass("mjernavelicina_146")) {

                        //SETUJEMO USAGLAŠENOST
                        var usaGlFinal = "USAGLAŠENI su";

                        //INICIRAMO NIZOVE ZA 20 MJERENJA
                        var sistolickiNiz = [];
                        var distolickiNiz = [];

                        //KUPIMO PRVIH 10 MJERENJA u NIZOVE
                        for(var i = 1; i <= 10; i++){
                            if($("input[name='rezultat_145_533_"+i+"']").val() != ""){
                                sistolickiNiz.push(parseInt($("input[name='rezultat_145_533_"+i+"']").val(),10));
                            }
                            if($("input[name='rezultat_145_534_"+i+"']").val() != ""){
                                distolickiNiz.push(parseInt($("input[name='rezultat_145_534_"+i+"']").val(),10));
                            }
                        }

                        //KUPIMO DRUGIH 10 MJERENJA u NIZOVE
                        for(var i = 11; i <= 20; i++){
                            if($("input[name='rezultat_146_535_"+i+"']").val() != ""){
                                sistolickiNiz.push(parseInt($("input[name='rezultat_146_535_"+i+"']").val(),10));
                            }
                            if($("input[name='rezultat_146_536_"+i+"']").val() != ""){
                                distolickiNiz.push(parseInt($("input[name='rezultat_146_536_"+i+"']").val(),10));
                            }
                        }

                        //DEVIJACIJA PRVOG NIZA
                        var stdevSist = stdev(sistolickiNiz).toFixed(2);

                        //DEVIJACIJA DRUGOG NIZA
                        var stdevDist = stdev(distolickiNiz).toFixed(2); 

                        //UPISUJEMO REZULTATE DEVIJACIJA KAO GREŠKE
                        $("#greskaSistolicni").html(stdevSist);
                        $("#greskaDistolicni").html(stdevDist);

                        //UPISUJEMO GREŠKE U NIZZA MAKSIMALNU GREŠKU
                        max5.push(stdevSist);
                        max5.push(stdevDist);


                    //ZA SVE OSTALE MJERNE VELIČINE MJERILA KRVNOG PRITISKA
                    } else {
                        
                        //KUPIMO REFERENTNU VRIJEDNOST
                        var refVr = parseFloat($(this).find(".refVr").html()).toFixed(2);
                        
                        //SETUJEMO ODSTUPANJE
                        var odsRast = refVr;
                        
                        //SETUJEMO ODSTUPANJE
                        var odsOpad = refVr;
                        
                        //SETUJEMO HISTEREZIS 1
                        var hist1 = 0;
                        
                        //SETUJEMO HISTEREZIS 2
                        var hist2 = 0;
                        
                        //SETUJEMO USAGLAŠENOST
                        var usaGlFinal = "USAGLAŠENI su";

                        //KUPIMO PRVO MJERENJE AKO POSTOJI
                        if ($(this).find(".mjerenje1").val().length === 0) {
                            var mjerenje1 = 0;
                        } else {
                            var mjerenje1 = $(this).find(".mjerenje1").val();
                        }

                        //KUPIMO DRUGO MJERENJE AKO POSTOJI
                        if ($(this).find(".mjerenje2").val().length === 0) {
                            var mjerenje2 = 0;
                        } else {
                            var mjerenje2 = $(this).find(".mjerenje2").val();
                        }

                        //KUPIMO TREĆE MJERENJE AKO POSTOJI
                        if ($(this).find(".mjerenje3").val().length === 0) {
                            var mjerenje3 = 0;
                        } else {
                            var mjerenje3 = $(this).find(".mjerenje3").val();
                        }

                        //KUPIMO ČETVRTO MJERENJE AKO POSTOJI
                        if ($(this).find(".mjerenje4").val().length === 0) {
                            var mjerenje4 = 0;
                        } else {
                            var mjerenje4 = $(this).find(".mjerenje4").val();
                        }

                        //UPISUJEMO SVA ČETIRI MJERENJA
                        $(this).find(".mjerenje1").val(mjerenje1);
                        $(this).find(".mjerenje2").val(mjerenje2);
                        $(this).find(".mjerenje3").val(mjerenje3);
                        $(this).find(".mjerenje4").val(mjerenje4);

                        //AKO SU SVA MJERENJA "-"
                        if ((mjerenje1 == "-" && mjerenje2 == "-" && mjerenje3 == "-" && mjerenje4 == "-") || (mjerenje1 == "--" && mjerenje2 == "--" && mjerenje3 == "--" && mjerenje4 == "--")) {

                            //SETUJEMO I UPISUJEMO SVE KAO "-"
                            odsRast = "-";
                            $(this).find(".odsRast").html(odsRast);
                            odsOpad = "-";
                            $(this).find(".odsOpad").html(odsOpad);
                            hist1 = "-";
                            $(this).find(".hist1").html(hist1);
                            hist2 = "-";
                            $(this).find(".hist2").html(hist2);
                        
                        //AKO MJERENJA NISU "-"    
                        } else {
                            
                            //RAČUNAMO ODSTUPANJE RASTUĆE
                            odsRast = Math.abs((parseFloat(mjerenje1) + parseFloat(mjerenje3)) / 2 - parseFloat(refVr)).toFixed(2);

                            //UPISUJEMO ODSTUPANJE U NIZ ZA MAKSIMUM
                            max1.push(odsRast);
                                
                            //UPISUJEMO ODSTUPANJE U TABELU
                            $(this).find(".odsRast").html(odsRast);
                            
                            //RAČUNAMO ODSTUPANJE OPADAJUĆE
                            odsOpad = Math.abs((parseFloat(mjerenje2) + parseFloat(mjerenje4)) / 2 - parseFloat(refVr)).toFixed(2);
                                
                            //UPISUJEMO ODSTUPANJE U NIZ ZA MAKSIMUM
                            max1.push(odsOpad);
                                
                            //UPISUJEMO ODSTUPANJE U TABELU
                            $(this).find(".odsOpad").html(odsOpad);

                            //RAČUNAMO HISTEREZIS 1
                            hist1 = Math.abs(parseFloat(mjerenje1) - parseFloat(mjerenje2)).toFixed(2);
                                
                            //UPISUJEMO HISTEREZIS U NIZ ZA MAKSIMUM
                            max2.push(hist1);
                                
                            //UPISUJEMO HISTEREZIS U TABELU
                            $(this).find(".hist1").html(hist1);
                            
                            //RAČUNAMO HISTEREZIS 2
                            hist2 = Math.abs(parseFloat(mjerenje3) - parseFloat(mjerenje4)).toFixed(2);
                                
                            //UPISUJEMO HISTEREZIS U NIZ ZA MAKSIMUM
                            max2.push(hist2);
                                
                            //UPISUJEMO HISTEREZIS U TABELU
                            $(this).find(".hist2").html(hist2);

                        }
                    }

                    console.log(usaGlFinal);

                });

                //FINALNI PRORAČUN

                //SETUJEMO USAGLAŠENOST
                usaGlFinal = "USAGLAŠENI su";

                //RAČUNAMO MAKSIMUM 1
                var maksimum1 = (Math.max.apply(Math, max1)).toFixed(2);
                
                <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 11 || $vrstauredjaja_['vrsteuredjaja_id'] == 12 || $vrstauredjaja_['vrsteuredjaja_id'] == 13 || $vrstauredjaja_['vrsteuredjaja_id'] == 14){ ?>
                    //MAKSIMUM 1 NE SMIJE BITI VEĆI OD 4
                    if (maksimum1 > 3) {
                        $(".usaGl1").html("NE");
                        usaGlFinal = "NISU USAGLAŠENI";
                    } else {
                        $(".usaGl1").html("DA");
                    }
                <?php }else{ ?>
                    //MAKSIMUM 1 NE SMIJE BITI VEĆI OD 4
                    if (maksimum1 > 4) {
                        $(".usaGl1").html("NE");
                        usaGlFinal = "NISU USAGLAŠENI";
                    } else {
                        $(".usaGl1").html("DA");
                    }
                <?php } ?>

                
                
                //RAČUNAMO MAKSIMUM 2
                var maksimum2 = (Math.max.apply(Math, max2)).toFixed(2);

                //NE SMIJE BITI VEĆI OD 4
                if (maksimum2 > 4) {
                    $(".usaGl2").html("NE");
                    usaGlFinal = "NISU USAGLAŠENI";
                } else {
                    $(".usaGl2").html("DA");
                }

                //RAČUNAMO MAKSIMUM 3
                var maksimum3 = (Math.max.apply(Math, max3)).toFixed(2);

                <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 49 || $vrstauredjaja_['vrsteuredjaja_id'] == 50) { ?>
                //NE SMIJE BITI VEĆI OD 6
                if (maksimum3 > 6) {
                    $(".usaGl3").html("NE");
                    usaGlFinal = "NISU USAGLAŠENI";
                } else {
                    $(".usaGl3").html("DA");
                }
                <?php } else { ?>
                //NE SMIJE BITI VEĆI OD 4
                if (maksimum3 > 4) {
                    $(".usaGl3").html("NE");
                    usaGlFinal = "NISU USAGLAŠENI";
                } else {
                    $(".usaGl3").html("DA");
                }
                <?php } ?>

                //UPISUJEMO MAKSIMUME U TABELU
                $(".max1").html(maksimum1);
                $(".max2").html(maksimum2);
                $(".max3").html(maksimum3);

                //DODATNI INPUT
                var zadnjiInput = $(".rezultat_0_0_0").val();
                $(".rezultat_0_0_0").val((parseFloat(zadnjiInput)).toFixed(2));

                //AKO JE MJERILO KRVNOG PRITISKA ZA DJECU I NOVOROĐENČAD
                <?php if ($vrstauredjaja_['vrsteuredjaja_id'] == 13 || $vrstauredjaja_['vrsteuredjaja_id'] == 14 || $vrstauredjaja_['vrsteuredjaja_id'] == 50) { ?>
                
                //DODATNI INPUT NE SMIJE BITI VEĆI OD 5
                if (parseFloat(zadnjiInput) > 5) {
                    $(".usaGl4").html("NE");
                    usaGlFinal = "NISU USAGLAŠENI";
                } else {
                    $(".usaGl4").html("DA");
                }

                //AKO NIJE MJERILO KRVNOG PRITISKA ZA DJECU I NOVOROĐENČAD
                <?php } else { ?>

                //DODATNI INPUT NE SMIJE BITI VEĆI OD 10
                if (parseFloat(zadnjiInput) > 10) {
                    $(".usaGl4").html("NE");
                    usaGlFinal = "NISU USAGLAŠENI";
                } else {
                    $(".usaGl4").html("DA");
                }

                <?php } ?>

                //DRUGI DODATNI INPUT    
                var zadnjiInput2 = $(".rezultat_0_0_1").val();
                $(".rezultat_0_0_1").val((parseFloat(zadnjiInput2)).toFixed(2));

                //DRUGI DODATNI NE SMIJE BITI VEĆI OD 1.5
                if (parseFloat(zadnjiInput2) > 1.5) {
                    $(".usaGl2").html("NE");
                    usaGlFinal = "NISU USAGLAŠENI";
                } else {
                    $(".usaGl2").html("DA");
                }

                //RAČUNAMO MAKSIMUM 5
                var maksimum5 = (Math.max.apply(Math, max5)).toFixed(2);

                //UPISUJEMO MAKSIUMUM 5
                $(".max5").html(maksimum5);

                //NE SMIJE BITI VEći od 3
                if(maksimum5 <= 3){
                    $(".usaGl5").html("DA");
                }else{
                    $(".usaGl5").html("NE");
                    usaGlFinal = "NISU USAGLAŠENI";
                }

                //UPISUJEMO FINALNU USAGLAŠENOST
                $(".usaGlFinal").html(usaGlFinal);

            }
        <?php } ?>

        //U STARTU POPUNIMO TABELE
        proracunTabela();
            
        //NASVAKU IZMJENU OPALIMO FUNKCIJU OPET
        $(".mjerenje").change(function () {
            proracunTabela();
        })

        //NA ENTER NEĆEMO SUBMIT
        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });

    </script>

    <?php

} else {
    header('Location: index.php');
}

//INCLUDES
include_once ('includes/footer.php');

?>