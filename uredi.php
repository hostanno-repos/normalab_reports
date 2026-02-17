<?php
//INCLUDES
include_once ('includes/head.php');
$page_ = explode("/", $_SERVER['HTTP_REFERER']);

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

$table = isset($_GET['t']) ? $_GET['t'] : '';
if ($table == 'klijenti' && !ima_permisiju('pregledklijenata', 'uredivanje')) {
    header('Location: pregledklijenata.php');
    exit;
}
if ($table == 'mjerila' && !ima_permisiju('pregledmjerila', 'uredivanje')) {
    header('Location: pregledmjerila.php');
    exit;
}
if ($table == 'opremazainspekciju' && !ima_permisiju('opremazainspekciju', 'uredivanje')) {
    header('Location: opremazainspekciju.php');
    exit;
}
if ($table == 'brojacirn' && !ima_permisiju('brojacirn', 'uredivanje')) {
    header('Location: brojacirn.php');
    exit;
}
if ($table == 'radninalozi' && !ima_permisiju('pregledradnihnaloga', 'uredivanje')) {
    header('Location: pregledradnihnaloga.php');
    exit;
}
if ($table == 'tipoviizvjestaja' && !ima_permisiju('tipoviizvjestaja', 'uredivanje')) {
    header('Location: tipoviizvjestaja.php');
    exit;
}
if ($table == 'izvjestaji' && !ima_permisiju('pregledizvjestaja', 'uredivanje')) {
    header('Location: pregledizvjestaja.php');
    exit;
}
if ($table == 'kontrolori' && !ima_permisiju('kontrolori', 'uredivanje')) {
    header('Location: kontrolori.php');
    exit;
}
if ($table == 'metodeinspekcije' && !ima_permisiju('metodeinspekcije', 'uredivanje')) {
    header('Location: metodeinspekcije.php');
    exit;
}
if ($table == 'vrsteinspekcije' && !ima_permisiju('vrsteinspekcije', 'uredivanje')) {
    header('Location: vrsteinspekcije.php');
    exit;
}
if ($table == 'vrsteuredjaja' && !ima_permisiju('vrsteuredjaja', 'uredivanje')) {
    header('Location: vrsteuredjaja.php');
    exit;
}
if ($table == 'mjernevelicine' && !ima_permisiju('mjernevelicine', 'uredivanje')) {
    header('Location: mjernevelicine.php');
    exit;
}
if ($table == 'referentnevrijednosti' && !ima_permisiju('referentnevrijednosti', 'uredivanje')) {
    header('Location: referentnevrijednosti.php');
    exit;
}
if ($table == 'nivoihijerarhije' && !ima_permisiju('nivoihijerarhije', 'uredivanje')) {
    header('Location: nivoihijerarhije.php');
    exit;
}
if ($table == 'korisnickeuloge' && !ima_permisiju('korisnickeuloge', 'uredivanje')) {
    header('Location: korisnickeuloge.php');
    exit;
}
if ($table == 'korisnici' && !ima_permisiju('korisnici', 'uredivanje')) {
    header('Location: korisnici.php');
    exit;
}

include_once ('includes/header.php');
include_once ('includes/sidebar.php');
//get parameters
$hadline = $_GET['h'];
//fix headline
switch ($hadline) {
    case "radninalog":
        $hadline = "radni nalog";
        break;
    case "vrstuuređaja":
        $hadline = "vrstu uređaja";
        break;
    case "metoduinspekcije":
        $hadline = "metodu inspekcije";
        break;
    case "mjernuvelicinu":
        $hadline = "mjernu veličinu";
        break;
    case "referentnuvrijednost":
        $hadline = "referentnu vrijednost";
        break;
    case "korisnickuulogu":
        $hadline = "korisničku ulogu";
        break;
    case "nivohijerarhije":
        $hadline = "nivo hijerarhije";
        break;
    case "opremuzainspekciju":
        $hadline = "opremu za inspekciju";
        break;
}
$table = isset($_GET['t']) ? $_GET['t'] : $table;
$object = $_GET['o'];
//get single object
$singleObject = new singleObject;
$singleObject = $singleObject->fetch_single_object($table, $table . "_id", $_GET['o']);
// Klijent (uloga 5) može uređivati samo mjerila koja su njegova (vlasnik)
if ($table == 'mjerila' && (int)$_SESSION['user-type'] === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mKlijent)) {
    $klijentId = (int)$mKlijent[1];
    if (empty($singleObject['mjerila_klijentid']) || (int)$singleObject['mjerila_klijentid'] !== $klijentId) {
        header('Location: pregledmjerila.php');
        exit;
    }
}
// Klijent (uloga 5) može uređivati samo radne naloge vezane za mjerila čiji je vlasnik
if ($table == 'radninalozi' && (int)$_SESSION['user-type'] === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mKlijent)) {
    $stmtRn = $pdo->prepare("SELECT mjerila_klijentid FROM mjerila WHERE mjerila_id = ?");
    $stmtRn->execute([(int)($singleObject['radninalozi_mjeriloid'] ?? 0)]);
    $rowRn = $stmtRn->fetch(PDO::FETCH_ASSOC);
    if (!$rowRn || (int)$rowRn['mjerila_klijentid'] !== (int)$mKlijent[1]) {
        header('Location: pregledradnihnaloga.php');
        exit;
    }
}
// Klijent (uloga 5) može uređivati samo izvještaje vezane za mjerila čiji je vlasnik
if ($table == 'izvjestaji' && (int)$_SESSION['user-type'] === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mKlijent)) {
    $stmtIj = $pdo->prepare("SELECT mjerila_klijentid FROM mjerila WHERE mjerila_id = ?");
    $stmtIj->execute([(int)($singleObject['izvjestaji_mjeriloid'] ?? 0)]);
    $rowIj = $stmtIj->fetch(PDO::FETCH_ASSOC);
    if (!$rowIj || (int)$rowIj['mjerila_klijentid'] !== (int)$mKlijent[1]) {
        header('Location: pregledizvjestaja.php');
        exit;
    }
}
$select = $pdo->query('SELECT * FROM ' . $table);
$total_column = $select->columnCount();

?>

<main id="main" class="main">

    <div class="pagetitle">
        <div class="d-flex justify-content-between mb-2">
            <h1>Uredi <?php echo $hadline ?></h1>
        </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                <li class="breadcrumb-item active">Uredi <?php echo $hadline ?></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_) ?>" method="post">
                <?php

                if($_GET['t'] != "izvjestaji"){

                    for ($counter = 0; $counter < $total_column; $counter++) {
                        $meta = $select->getColumnMeta($counter);
                        //var_dump($meta['name']);
                        //var_dump($meta['native_type']);
                        $labelName = ucfirst(explode("_", $meta['name'])[1]);
                        $tableName = explode("_", $meta['name'])[0];
                        $readonly = 0;
                        //var_dump($meta['native_type']);
                        switch ($meta['native_type']) {
                            case "LONG":
                                $tip = "number";
                                break;
                            case "VAR_STRING":
                                $tip = "text";
                                break;
                            case "DATE":
                                $tip = "date";
                                break;
                            default:
                                //$tip = "text";
                                break;
                        }
                        switch ($meta['name']) {
                            case "radninalozi_brojacrnid":
                                $labelName = "Brojač";
                                $input = "select";
                                $tableSelect = "brojacirn";
                                $columnToEqual = "radninalozi_brojacrnid";
                                $columnToShow = "brojacirn_godina";
                                $tip = "number";
                                $disabled = 1;
                                break;
                            case "radninalozi_broj":
                                $labelName = "Broj radnog naloga";
                                $input = "input";
                                $tip = "text";
                                $disabled = 1;
                                break;
                            case "radninalozi_klijentid":
                                $labelName = "Podnosilac zahtjeva";
                                $input = "select";
                                $tableSelect = "klijenti";
                                $columnToEqual = "radninalozi_klijentid";
                                $columnToShow = "klijenti_naziv";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "radninalozi_brojzahtjeva":
                                $labelName = "Broj zahtjeva za inspekciju";
                                $input = "input";
                                $tip = "text";
                                $disabled = 0;
                                break;
                            case "radninalozi_metodainspekcijeid":
                                $labelName = "Metoda inspekcije";
                                $input = "select";
                                $tableSelect = "metodeinspekcije";
                                $columnToEqual = "radninalozi_metodainspekcijeid";
                                $columnToShow = "metodeinspekcije_naziv";
                                $columnToShow_1 = "";
                                $columnToShow_2 = "";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "radninalozi_mjeriloid":
                                $labelName = "Mjerilo";
                                $input = "select";
                                $tableSelect = "mjerila";
                                $columnToEqual = "radninalozi_mjeriloid";
                                $columnToShow_1 = "mjerila_id";
                                $columnToShow_2 = "mjerila_serijskibroj";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "radninalozi_kontrolorid":
                                $labelName = "Kontrolor";
                                $input = "select";
                                $tableSelect = "kontrolori";
                                $columnToEqual = "radninalozi_kontrolorid";
                                $columnToShow_1 = "kontrolori_ime";
                                $columnToShow_2 = "kontrolori_prezime";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "radninalozi_datumzavrsetka":
                                $labelName = "Očekivani završetak inspekcije";
                                $input = "input";
                                $disabled = 0;
                                break;
                            case "radninalozi_posebnizahtjevi":
                                $labelName = "Posebni zahtjevi";
                                $input = "textarea";
                                $disabled = 0;
                                break;
                            case "radninalozi_vrstauredjajaid":
                                $labelName = "Predmet inspekcije";
                                $input = "select";
                                $tableSelect = "vrsteuredjaja";
                                $columnToEqual = "radninalozi_vrstauredjajaid";
                                $columnToShow_1 = "vrsteuredjaja_naziv";
                                $columnToShow_2 = "vrsteuredjaja_opis";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "radninalozi_otvorioid":
                                $labelName = "Radni nalog otvorio";
                                $input = "select";
                                $tableSelect = "kontrolori";
                                $columnToEqual = "radninalozi_otvorioid";
                                $columnToShow_1 = "kontrolori_ime";
                                $columnToShow_2 = "kontrolori_prezime";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "radninalozi_primioid":
                                $labelName = "Radni nalog primio";
                                $input = "select";
                                $tableSelect = "kontrolori";
                                $columnToEqual = "radninalozi_primioid";
                                $columnToShow_1 = "kontrolori_ime";
                                $columnToShow_2 = "kontrolori_prezime";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "radninalozi_zatvorioid":
                                $labelName = "Radni nalog zatvorio";
                                $input = "select";
                                $tableSelect = "kontrolori";
                                $columnToEqual = "radninalozi_zatvorioid";
                                $columnToShow_1 = "kontrolori_ime";
                                $columnToShow_2 = "kontrolori_prezime";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "radninalozi_datum":
                                $labelName = "Datum";
                                $input = "input";
                                $tip = "date";
                                $disabled = 0;
                                break;
                            case "mjerila_vrstauredjajaid":
                                $labelName = "Vrsta uređaja";
                                $input = "select";
                                $tableSelect = "vrsteuredjaja";
                                $columnToEqual = "mjerila_vrstauredjajaid";
                                $columnToShow_1 = "vrsteuredjaja_naziv";
                                $columnToShow_2 = "vrsteuredjaja_opis";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "mjerila_klijentid":
                                $labelName = "Vlasnik uređaja";
                                $input = "select";
                                $tableSelect = "klijenti";
                                $columnToEqual = "mjerila_klijentid";
                                $columnToShow = "klijenti_naziv";
                                $columnToShow_1 = "";
                                $columnToShow_2 = "";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "mjerila_proizvodjac":
                                $labelName = "Proizvođač";
                                break;
                            case "mjerila_serijskibroj":
                                $labelName = "Serijski broj";
                                break;
                            case "mjerila_godinaproizvodnje":
                                $labelName = "Godina proizvodnje";
                                break;
                            case "mjerila_sluzbenaoznaka":
                                $labelName = "Službena oznaka";
                                break;
                            case "mjernevelicine_vrstauredjajaid":
                                $labelName = "Vrsta uređaja";
                                $input = "select";
                                $tableSelect = "vrsteuredjaja";
                                $columnToEqual = "mjernevelicine_vrstauredjajaid";
                                $columnToShow = "vrsteuredjaja_naziv";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "referentnevrijednosti_mjernavelicinaid":
                                $labelName = "Mjerna veličina";
                                $input = "select";
                                $tableSelect = "mjernevelicine";
                                $columnToEqual = "referentnevrijednosti_mjernavelicinaid";
                                $columnToShow = "mjernevelicine_naziv";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "referentnevrijednosti_referentnavrijednost":
                                $labelName = "Referentna vrijednost";
                                $input = "input";
                                $tip = "number";
                                break;
                            case "korisnickeuloge_nivohijerarhijeid":
                                $labelName = "Nivo hijerarhije";
                                $input = "select";
                                $tableSelect = "nivoihijerarhije";
                                $columnToEqual = "korisnickeuloge_nivohijerarhijeid";
                                $columnToShow = "nivoihijerarhije_nivo";
                                $tip = "number";
                                $disabled = 0;
                                break;
                           case "korisnici_ime":
                               $labelName = "Ime";
                               $input = "input";
                               $tip = "text";
                               if(!in_array($_SESSION['user-type'], [1, 7])){
                                   $disabled = 1;
                               }else{
                                   $disabled = 0;
                               }
                               break;
                            case "korisnici_prezime":
                                $labelName = "Prezime";
                                $input = "input";
                                $tip = "text";
                                if(!in_array($_SESSION['user-type'], [1, 7])){
                                    $disabled = 1;
                                }else{
                                    $disabled = 0;
                                }
                                break;
                            case "korisnici_username":
                                $labelName = "Korisničko ime";
                                $input = "input";
                                $tip = "text";
                                if(!in_array($_SESSION['user-type'], [1, 7])){
                                    $disabled = 1;
                                }else{
                                    $disabled = 0;
                                }
                                break;
                            case "korisnici_password":
                                $labelName = "Lozinka";
                                $input = "input";
                                $tip = "password";
                                $disabled = 0;
                                break;
                            case "korisnici_korisnickaulogaid":
                                $labelName = "Korisnička uloga";
                                $input = "select";
                                $tableSelect = "korisnickeuloge";
                                $columnToEqual = "korisnici_korisnickaulogaid";
                                $columnToShow = "korisnickeuloge_naziv";
                                $tip = "number";
                                if(!in_array($_SESSION['user-type'], [1, 7])){
                                    $disabled = 1;
                                }else{
                                    $disabled = 0;
                                }
                                break;
                            case "korisnici_lozinka_prikaz":
                                $labelName = "Lozinka (za proslijediti klijentu)";
                                $input = "input";
                                $tip = "text";
                                $readonly = 1;
                                $disabled = 0;
                                break;
                            case "opremazainspekciju_opremauupotrebi":
                                $labelName = "Oprema u upotrebi (0/1)";
                                $input = "input";
                                $tip = "number";
                                break;
                            case "tipoviizvjestaja_vrstauredjajaid":
                                $labelName = "Vrsta uređaja";
                                $input = "select";
                                $tableSelect = "vrsteuredjaja";
                                $columnToEqual = "tipoviizvjestaja_vrstauredjajaid";
                                $columnToShow = "vrsteuredjaja_naziv";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            case "radninalozi_timestamp":
                                $labelName = "Datum i vrijeme kreiranja";
                                $input = "input";
                                $tip = "datetime-local";
                                $disabled = 1;
                                break;
                            case "izvjestaji_timestamp":
                                $labelName = "Datum i vrijeme kreiranja";
                                $input = "input";
                                $tip = "datetime-local";
                                $disabled = 1;
                                break;
                            case "vrsteuredjaja_opisprocedure":
                                $labelName = "Opis procedure";
                                $input = "textarea";
                                $disabled = 0;
                                break;
                            case "mjerila_djeca":
                                $labelName = "Mjerilo krvnog pritiska za djecu i novorođenčad";
                                $input = "input";
                                $tip = "number";
                                $disabled = 0;
                                break;
                            default:
                                $input = "input";
                                //$tip = "text";
                                $disabled = 0;
                                break;
                        } ?>

                        <?php if ($tableName != "radninalozi_") { ?>

                            <?php if ($labelName != "Id" && $labelName != "Timestamp" && $meta['name'] != "mjerila_djeca" && !($meta['name'] == 'korisnici_lozinka_prikaz' && !in_array($_SESSION['user-type'], [1, 7]))) { ?>
                                <div class="col-lg-3 d-flex flex-column mb-2">
                                    <label for="<?php echo $meta['name'] ?>"><?php echo $labelName ?>:</label>
                                <?php } ?>

                                <?php //if ($input == "input" && $meta['name'] == "mjerila_djeca") { ?>
                                <!-- <div class="col-lg-3 d-flex flex-column mb-2">
                                    <label id="hiddenLabel" for="<?php echo $meta['name'] ?>" hidden><?php echo $labelName ?>:</label>
                                
                                    <input id="" type="<?php //echo $tip ?>" name="<?php //echo $meta['name'] ?>"
                                        value="<?php //if($meta['name'] != "korisnici_password"){echo $singleObject[$meta['name']];} ?>" <?php //if ($labelName == "Id" || $labelName == "Timestamp") {
                                               //echo "hidden";
                                           //}else if($disabled == 1){echo "disabled";} ?> step="any" hidden>
                                        <select name="" id="hiddenSelect" class="selectElement_" hidden>
                                            <option value=""></option>
                                            <option value="1">DA</option>
                                            <option value="0">NE</option>
                                        </select> -->
                                <?php //} ?>


                                <?php if ($input == "input" && $meta['name'] != "mjerila_djeca" && !($meta['name'] == 'korisnici_lozinka_prikaz' && !in_array($_SESSION['user-type'], [1, 7]))) { ?>
                                    <input type="<?php echo $tip ?>" name="<?php echo $meta['name'] ?>"
                                        value="<?php if($meta['name'] != "korisnici_password"){echo htmlspecialchars($singleObject[$meta['name']] ?? '');} ?>" <?php if ($labelName == "Id" || $labelName == "Timestamp") {
                                               echo "hidden";
                                           }else if($disabled == 1){echo "disabled";}else if(!empty($readonly)){echo "readonly";} ?> step="any">
                                <?php } else if ($input == "textarea") { ?>
                                        <textarea name="<?php echo $meta['name'] ?>"
                                            id="" rows="1"><?php echo $singleObject[$meta['name']] ?></textarea>

                                <?php } else if ($input == "select") { ?>
                                            <input type="<?php echo $tip ?>" name="<?php echo $meta['name'] ?>"
                                                value="<?php echo $singleObject[$meta['name']] ?>" hidden>
                                            <select name="" id="<?php if($meta['name'] == "radninalozi_vrstauredjajaid"){echo "predmet_inspekcije_select";}elseif($meta['name']== "radninalozi_mjeriloid"){ echo "broj_mjerila_select";} ?>"<?php if($meta['name'] == "radninalozi_vrstauredjajaid"){echo 'onchange="filterDevices()"';} ?> class="selectElement_" <?php if($disabled == 1){echo "disabled";} ?>>
                                                <option value=""></option>
                                            <?php
                                            $selectedItems = new allObjects;
                                            $selectedItems = $selectedItems->fetch_all_objects($tableSelect, $tableSelect . "_id", "ASC");
                                            if ($meta['name'] == 'korisnici_korisnickaulogaid') {
                                                $allowedIds = array(1, 4, 5, 6, 7);
                                                $currentVal = isset($singleObject[$columnToEqual]) ? $singleObject[$columnToEqual] : null;
                                                if ($currentVal != '' && $currentVal !== null && !in_array($currentVal, $allowedIds)) {
                                                    $allowedIds[] = $currentVal;
                                                }
                                                $selectedItems = array_values(array_filter($selectedItems, function($r) use ($allowedIds) { return in_array($r['korisnickeuloge_id'], $allowedIds); }));
                                            }
                                            foreach ($selectedItems as $selectedItem) {
                                                ?>
                                                    <option value="<?php echo $selectedItem[$tableSelect . "_id"] ?>" <?php if($selectedItem[$tableSelect . "_id"] == $singleObject[$columnToEqual]){echo "selected";}?>
                                                    <?php if($meta['name']== "radninalozi_mjeriloid"){echo 'data-type="'.$selectedItem[$tableSelect.'_vrstauredjajaid'].'"';} ?>    
                                                    >
                                            <?php 
                                            if(isset($columnToShow_1) && isset($columnToShow_2) && $columnToShow_1 != "" && $columnToShow_2 != ""){
                                                if($meta['name']== "radninalozi_mjeriloid"){
                                                    echo $selectedItem[$columnToShow_1].". ".$selectedItem[$columnToShow_2]; 
                                                }else{
                                                   echo $selectedItem[$columnToShow_1]." ".$selectedItem[$columnToShow_2]; 
                                                }
                                                
                                            }else{
                                               echo $selectedItem[$columnToShow];
                                            }
                                             ?></option>
                                        <?php } ?>
                                            </select>
                                <?php } ?>

                                <?php if ($labelName != "Id" && $labelName != "Timestamp" && !($meta['name'] == 'korisnici_lozinka_prikaz' && !in_array($_SESSION['user-type'], [1, 7]))) { ?>
                                </div>
                            <?php } ?>

                        <?php } ?>

                    <?php } 
                

                $vrstauredjaja_['vrsteuredjaja_id'] = 0;


                //UREDI IZVJEŠTAJ
                }else{ 
                
                    //KUPIMO IZVJEŠTAJ
                    $izvjestaj = new singleObject;
                    $izvjestaj = $izvjestaj->fetch_single_object("izvjestaji", "izvjestaji_id", $_GET['o']);

                    //KUPIMO RADNI NALOG
                    $radninalog = new singleObject;
                    $radninalog = $radninalog->fetch_single_object("radninalozi", "radninalozi_id", $izvjestaj['izvjestaji_radninalogid']);

                    //KUPIMO KLIJENTA
                    $klijent = new singleObject;
                    $klijent = $klijent->fetch_single_object("klijenti", "klijenti_id", $radninalog['radninalozi_klijentid']);

                    //KUPIMO METODU INSPEKCIJE
                    $metodainspekcije = new singleObject;
                    $metodainspekcije = $metodainspekcije->fetch_single_object("metodeinspekcije", "metodeinspekcije_id", $radninalog['radninalozi_metodainspekcijeid']);

                    //KUPIMO VRSTU UREĐAJA
                    $vrstauredjaja_ = new singleObject;
                    $vrstauredjaja_ = $vrstauredjaja_->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $radninalog['radninalozi_vrstauredjajaid']);

                    //KUPIMO MJERNE VELIČINE
                    $mjernevelicine = new allObjectsBy;
                    $mjernevelicine = $mjernevelicine->fetch_all_objects_by("mjernevelicine", "mjernevelicine_vrstauredjajaid", $radninalog['radninalozi_vrstauredjajaid'], "mjernevelicine_id", "ASC");

                    //KUPIMO TIP IZVJEŠTAJA
                    $tipoviizvjestaja = new allObjects;
                    $tipoviizvjestaja = $tipoviizvjestaja->fetch_all_objects("tipoviizvjestaja", "tipoviizvjestaja_id", "ASC");

                    //KUPIMO MJERILA
                    $mjerila = new allObjectsBy;    
                    $mjerila = $mjerila->fetch_all_objects_by("mjerila", "mjerila_vrstauredjajaid", $radninalog['radninalozi_vrstauredjajaid'], "mjerila_serijskibroj", "ASC");

                    //KUPIMO VRSTU INSPEKCIJE
                    $vrsteinspekcije = new allObjects;
                    $vrsteinspekcije = $vrsteinspekcije->fetch_all_objects("vrsteinspekcije", "vrsteinspekcije_id", "ASC");

                    //KUPIMO AKTIVNU OPREMU
                    $aktivneopreme = new allObjectsBy;
                    $aktivneopreme = $aktivneopreme->fetch_all_objects_by("opremazainspekciju", "opremazainspekciju_opremauupotrebi", 1, "opremazainspekciju_id", "ASC");

                    //SKRAĆENICE
                    $skracenice = "Skraćenice korištene u ispitivanju greške mjerenja:";
                    
                    //LEGENDA
                    $legendaArray = ["Xs - Zadana vrijednost mjerene veličine", "ΔX - Apsolutna greška mjerenja ΔX = |&lt;Xm&gt;-Xs|", "Xm - Izmjerena vrijednost mjerene veličine", "δ - Relativna greška mjerenja δ=ΔX/Xs*100%", "&lt;Xm&gt; - Srednja vrijednost mjerene veličine"];

                    //KUPIMO KONTROLORE
                    $kontrolori = new allObjects;
                    $kontrolori = $kontrolori->fetch_all_objects("kontrolori", "kontrolori_id", "ASC");
                    
                ?>

                <!-- ID IZVJEŠTAJA -->
                <input name="izvjestaji_id" type="number" value="<?php echo $izvjestaj['izvjestaji_id'] ?>" hidden>

                <!-- ID RADNOG NALOGA -->
                <input name="izvjestaji_radninalogid" type="number" value="<?php echo $izvjestaj['izvjestaji_radninalogid'] ?>" hidden>
                
                <!-- NASLOV MAIN -->
                <h6 class="w-100 mb-4 font-weight-bold color-black">Izvještaj o ispitivanju mjerila -
                    <?php echo $vrstauredjaja_['vrsteuredjaja_naziv'] ?>
                </h6>
                
                <!-- BROJ RADNOG NALOGA -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_broj">Broj radnog naloga:</label>
                    <?php 
                        $radninalogbroj = $radninalog['radninalozi_broj']; 
                        $radninalogbroj = explode("-", $radninalogbroj); 
                        $radninalogbroj = $radninalogbroj[count($radninalogbroj)-1];
                        $radninalogbroj = explode("/", $radninalogbroj);
                        $radninalogbroj_ = substr($radninalogbroj[1], -2);
                        $radninalogbroj = $radninalogbroj[0]."/".$radninalogbroj_;
                    ?>
                    <input type="text" name="izvjestaji_broj" value="<?php echo $radninalogbroj ?>" disabled>
                </div>
                
                <!-- BROJ IZVJEŠTAJA -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_broj">Broj izvještaja:</label>
                    <?php 
                        $izvjestajbroj = $izvjestaj['izvjestaji_broj']; 
                        $izvjestajbroj = explode("/", $izvjestajbroj);
                        $izvjestajbroj_ = substr($izvjestajbroj[1], -2);
                        $izvjestajbroj = $izvjestajbroj[0]."/".$izvjestajbroj_;
                    ?>
                    <input type="text" name="izvjestaji_broj" value="<?php echo $izvjestajbroj ?>">
                </div>
                
                <!-- TIP IZVJEŠTAJA -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_tipizvjestajaid">Tip izvještaja:</label>
                    <input type="text" name="izvjestaji_tipizvjestajaid" value="<?php echo $izvjestaj['izvjestaji_tipizvjestajaid'] ?>" hidden>
                    <select class="selectElement_" id="">
                        <option value=""></option>
                        <?php foreach($tipoviizvjestaja as $tipizvjestaja) { ?>
                            <option value="<?php echo $tipizvjestaja['tipoviizvjestaja_id'] ?>" <?php if($tipizvjestaja['tipoviizvjestaja_id'] == $izvjestaj['izvjestaji_tipizvjestajaid']){echo "selected";} ?> vrstauredjaja="<?php
                               $vrstauredjaja = new singleObject;
                               $vrstauredjaja = $vrstauredjaja->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $tipizvjestaja['tipoviizvjestaja_vrstauredjajaid']);
                               echo $vrstauredjaja['vrsteuredjaja_naziv'] ?>"><?php echo $tipizvjestaja['tipoviizvjestaja_naziv'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                
                <!-- VRSTA UREĐAJA -->
                <div class=" col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_vrstauredjaja">Vrsta uređaja:</label>
                    <input type="text" name="izvjestaji_vrstauredjaja" id="" value="<?php 
                    $tipizvjestaja = new singleObject;
                    $tipizvjestaja = $tipizvjestaja->fetch_single_object("tipoviizvjestaja", "tipoviizvjestaja_id", $izvjestaj['izvjestaji_tipizvjestajaid']);
                    $vrstauredjaja_0 = new singleObject;
                    $vrstauredjaja_0 = $vrstauredjaja_0->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $tipizvjestaja['tipoviizvjestaja_vrstauredjajaid']); echo $vrstauredjaja_0['vrsteuredjaja_naziv']; ?>" disabled>
                        
                </div>
                
                <!-- DATUM IZDAVANJA -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_datumizdavanja">Datum izdavanja:</label>
                    <input type="date" name="izvjestaji_datumizdavanja" value="<?php echo $izvjestaj['izvjestaji_datumizdavanja'] ?>">
                </div>
                
                <!-- DATUM INSPEKCIJE -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_datuminspekcije">Datum inspekcije:</label>
                    <input type="date" name="izvjestaji_datuminspekcije" value="<?php echo $izvjestaj['izvjestaji_datuminspekcije'] ?>">
                </div>

                <!-- DATUM ZAHTJEVA -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_datumzahtjeva">Datum zahtjeva:</label>
                    <input type="date" name="izvjestaji_datumzahtjeva" value="<?php echo $izvjestaj['izvjestaji_datumzahtjeva'] ?>">
                </div>
                
                <!-- PRAZNA POLJA -->
                <div class="col-lg-3 d-flex flex-column mb-2"></div>
                <div class="col-lg-3 d-flex flex-column mb-2"></div>
                
                <!-- DIVIDER -->
                <hr class="w-100">

                <!-- NASLOV PODNOSILAC ZAHTJEVA -->
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
                    <input type="text" name="izvjestaji_zahtjevzaispitivanje" value="<?php echo $izvjestaj['izvjestaji_zahtjevzaispitivanje'] ?>">
                </div>
                
                <!-- PRAZNO POLJE -->
                <div class="col-lg-3 d-flex flex-column mb-2"></div>
                
                <!-- DIVIDER -->
                <hr class="w-100">

                <!-- NASLOV IDENTIFIKACIJA MJERILA -->
                <h6 class="w-100 mb-4 font-weight-bold">2. Identifikacija mjerila:</h6>

                <!-- MJERILO -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_mjeriloid">Mjerilo:</label>
                    <input type="text" name="izvjestaji_mjeriloid" value="<?php echo $izvjestaj['izvjestaji_mjeriloid'] ?>" hidden>
                    <select class="selectElement__" id="">
                        <option value=""></option>
                        <?php foreach ($mjerila as $mjerilo) { ?>
                            <option value="<?php echo $mjerilo['mjerila_id'] ?>"
                                zadovoljava="<?php echo $mjerilo['mjerila_zadovoljava'] ?>"
                                proizvodjac="<?php echo $mjerilo['mjerila_proizvodjac'] ?>"
                                tip="<?php echo $mjerilo['mjerila_tip'] ?>"
                                serijskibroj="<?php echo $mjerilo['mjerila_serijskibroj'] ?>"
                                godinaproizvodnje="<?php echo $mjerilo['mjerila_godinaproizvodnje'] ?>"
                                sluzbenaoznaka="<?php echo $mjerilo['mjerila_sluzbenaoznaka'] ?>" <?php if($mjerilo['mjerila_id'] == $izvjestaj['izvjestaji_mjeriloid']){echo "selected";} ?>>
                                <?php echo $mjerilo['mjerila_serijskibroj'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                
                <!-- MJERILO PODACI -->
                <?php 
                    $mymjerilo = new singleObject; 
                    $mymjerilo = $mymjerilo -> fetch_single_object("mjerila", "mjerila_id",$izvjestaj['izvjestaji_mjeriloid']); 
                ?>

                <!-- ZADOVOLJAVA -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_">Zadovoljava:</label>
                    <input type="text" name="izvjestaji_mjerilozadovoljava" value="<?php echo $mymjerilo['mjerila_zadovoljava'] ?>" disabled>
                </div>

                <!-- PROIZVOĐAČ -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_">Proizvođač:</label>
                    <input type="text" name="izvjestaji_mjeriloproizvodjac" value="<?php echo $mymjerilo['mjerila_proizvodjac'] ?>" disabled>
                </div>

                <!-- TIP MJERILA -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_">Tip mjerila:</label>
                    <input type="text" name="izvjestaji_mjerilotip" value="<?php echo $mymjerilo['mjerila_tip'] ?>" disabled>
                </div>

                <!-- SERIJSKI BROJ MJERILA -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_">Serijski broj mjerila:</label>
                    <input type="text" name="izvjestaji_mjeriloserijskibroj" value="<?php echo $mymjerilo['mjerila_serijskibroj'] ?>" disabled>
                </div>

                <!-- GODINA PROIZVODNJE -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_">Godina proizvodnje:</label>
                    <input type="text" name="izvjestaji_mjerilogodinaproizvodnje" value="<?php echo $mymjerilo['mjerila_godinaproizvodnje'] ?>" disabled>
                </div>

                <!-- SLUŽBENA OZNAKA -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_">Službena oznaka:</label>
                    <input type="text" name="izvjestaji_mjerilosluzbenaoznaka" value="<?php echo $mymjerilo['mjerila_sluzbenaoznaka'] ?>" disabled>
                </div>

                <!-- DIVIDER -->
                <hr class="w-100">

                <!-- NASLOV VERIFIKACIJA MJERILA -->
                <h6 class="w-100 mb-4 font-weight-bold">3. Verifikacija mjerila:</h6>


                <!-- MJESTO INSPEKCIJE -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_mjestoinspekcije">Mjesto inspekcije:</label>
                    <input type="text" name="izvjestaji_mjestoinspekcije" value="<?php echo $izvjestaj['izvjestaji_mjestoinspekcije'] ?>">
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
                    <input type="text" name="izvjestaji_vrstainspekcijeid" value="<?php echo $izvjestaj['izvjestaji_vrstainspekcijeid'] ?>" hidden>
                    <select class="selectElement_" id="">
                        <option value=""></option>
                        <?php foreach($vrsteinspekcije as $vrstainspekcije) { ?>
                            <option value="<?php echo $vrstainspekcije['vrsteinspekcije_id'] ?>" <?php if($vrstainspekcije['vrsteinspekcije_id'] == $izvjestaj['izvjestaji_vrstainspekcijeid']){echo "selected";} ?>><?php echo $vrstainspekcije['vrsteinspekcije_naziv'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                
                <!-- NASLOVOPREMA ZA INSPEKCIJU -->
                <h6 class="w-100 mb-4 font-weight-bold">Oprema za inspekciju:</h6>
                
                <!-- SKRIVENI INPUT OPREMA -->
                <div class="col-lg-12 d-flex flex-column mb-2">
                    <input type="text" name="izvjestaji_opremazainspekciju" value="<?php echo $izvjestaj['izvjestaji_opremazainspekciju'] ?>" hidden>
                </div>
                
                <!-- FOREACH OPREMA -->
                <?php foreach ($aktivneopreme as $aktivnaoprema) { ?>
                <div class="col-lg-3 d-flex flex-row mb-2 align-items-start">
                    <input type="checkbox" class="checkOprema m-1 mr-2"
                        idOpreme="<?php echo $aktivnaoprema['opremazainspekciju_id'] ?>" <?php if (in_array($aktivnaoprema['opremazainspekciju_id'], explode(",",$izvjestaj['izvjestaji_opremazainspekciju']))) { echo "checked"; } ?> >
                    <label for="izvjestaji_"><?php echo $aktivnaoprema['opremazainspekciju_naziv'] ?></label>
                </div>
                <?php } ?>

                <!-- OPIS PROCEDURE -->
                <div class="col-lg-12 d-flex flex-column mb-2">
                    <label for="izvjestaji_opisprocedure">Opis procedure:</label>
                    <textarea name="izvjestaji_opisprocedure" id="" rows="3"><?php echo $izvjestaj['izvjestaji_opisprocedure'] ?></textarea>
                </div>

                <!-- DIVIDER -->
                <hr class="w-100">

                <!-- NASLOV AMBIJENTALNI USLOVI -->
                <h6 class="w-100 mb-4 font-weight-bold">3.1. Identifikacija ambijentalnih uslova:</h6>

                <!-- TEMPERATURA -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_temperatura">Temperatura[°C]: +/-1°C</label>
                    <input type="text" name="izvjestaji_temperatura" value="<?php echo $izvjestaj['izvjestaji_temperatura'] ?>">
                </div>

                <!-- VLAŽNOST -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_vlaznost">Relativna vlažnost[%]: +/-1%</label>
                    <input type="text" name="izvjestaji_vlaznost" value="<?php echo $izvjestaj['izvjestaji_vlaznost'] ?>">
                </div>

                <!-- SKINUTI ŽIG -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_skinutizig">Oznaka i serijski broj skinutog republičkog žiga:</label>
                    <input type="text" name="izvjestaji_skinutizig" value="<?php echo $izvjestaj['izvjestaji_skinutizig'] ?>">
                </div>
                
                <!-- DIVIDER -->
                <hr class="w-100">

                <!-- NASLOV VIZUELNI PREGLED MJERILA -->
                <h6 class="w-100 mb-4 font-weight-bold">3.2. Vizuelni pregled mjerila:</h6>

                <!-- MJERILO ČISTO I UREDNO -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_mjerilocisto">1. Mjerilo čisto i uredno:</label>
                    <select name="izvjestaji_mjerilocisto" id="">
                        <option value=""></option>
                        <option value="1" <?php if($izvjestaj['izvjestaji_mjerilocisto'] == 1){ echo "selected";} ?>>DA</option>
                        <option value="0" <?php if($izvjestaj['izvjestaji_mjerilocisto'] == 0){ echo "selected";} ?>>NE</option>
                    </select>
                </div>

                <!-- MJERILO CJELOVITO -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_mjerilocjelovito">2. Mjerilo je cjelovito i propisane konstrukcije:</label>
                    <select name="izvjestaji_mjerilocjelovito" id="">
                        <option value=""></option>
                        <option value="1" <?php if($izvjestaj['izvjestaji_mjerilocjelovito'] == 1){ echo "selected";} ?>>DA</option>
                        <option value="0" <?php if($izvjestaj['izvjestaji_mjerilocjelovito'] == 0){ echo "selected";} ?>>NE</option>
                    </select>
                </div>

                <!-- MJERILO ČITLJIVO -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_mjerilocitljivo">3. Mjerilo ima čitljive natpise i oznake:</label>
                    <select name="izvjestaji_mjerilocitljivo" id="">
                        <option value=""></option>
                        <option value="1" <?php if($izvjestaj['izvjestaji_mjerilocitljivo'] == 1){ echo "selected";} ?>>DA</option>
                        <option value="0" <?php if($izvjestaj['izvjestaji_mjerilocitljivo'] == 0){ echo "selected";} ?>>NE</option>
                    </select>
                </div>

                <!-- MJERILO POSJEDUJE SVE -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="izvjestaji_mjerilokablovi">4. Mjerilo posjeduje napojne kablove i ostale dodatke
                        neophodne za
                        rad:</label>
                    <select name="izvjestaji_mjerilokablovi" id="">
                        <option value=""></option>
                        <option value="1" <?php if($izvjestaj['izvjestaji_mjerilokablovi'] == 1){ echo "selected";} ?>>DA</option>
                        <option value="0" <?php if($izvjestaj['izvjestaji_mjerilokablovi'] == 0){ echo "selected";} ?>>NE</option>
                    </select>
                </div>

                <!-- DIVIDER -->
                <hr class="w-100">

                <!-- NASLOV ISPITIVANJE GREŠKE MJERILA -->
                <h6 class="w-100 mb-4 font-weight-bold">4. Ispitivanje greške mjerila</h6>

                <!-- AKO NISU MJERILA KRVNOG PRITISKA -->
                <?php 
                if ($vrstauredjaja_['vrsteuredjaja_id'] != 11 && 
                    $vrstauredjaja_['vrsteuredjaja_id'] != 12 && 
                    $vrstauredjaja_['vrsteuredjaja_id'] != 13 && 
                    $vrstauredjaja_['vrsteuredjaja_id'] != 14 && 
                    $vrstauredjaja_['vrsteuredjaja_id'] != 49 && 
                    $vrstauredjaja_['vrsteuredjaja_id'] != 50) {
                ?>

                <!-- SKRAĆENICE NASLOV -->
                <div class="col-lg-12 d-flex flex-column mb-2">
                    <label for="izvjestaji_"><?php echo $skracenice ?></label>
                </div>

                <!-- LEGENDA -->
                <?php foreach ($legendaArray as $legendaItem) { ?>
                    <div class="col-lg-6 d-flex flex-column mb-2">
                        <label for="izvjestaji_"><?php echo $legendaItem ?></label>
                    </div>
                <?php } ?>

                <!-- DIVIDER -->
                <hr class="w-100">

                <?php } ?>

                <!-- ZA SVE MJERNE VELIČINE -->
                <?php for ($i = 1; $i <= count($mjernevelicine); $i++) { ?>

                    <!-- NASLOV -->
                    <?php if($mjernevelicine[$i - 1]['mjernevelicine_id'] != 142 && $mjernevelicine[$i - 1]['mjernevelicine_id'] != 146) { ?>
                    <h6 class="w-100 mb-4 font-weight-bold">4.<?php echo $i ?>.
                        <?php echo $mjernevelicine[$i - 1]['mjernevelicine_naziv'] ?>
                    </h6>
                    <?php } ?>

                    <!-- TABELA -->
                    <table class="table table-bordered">

                        <!-- ZAGLAVLJE -->
                        <thead>

                        <?php //AKO SU DEFIBRILATORI
                            if ($vrstauredjaja_['vrsteuredjaja_id'] == 3 || 
                                $vrstauredjaja_['vrsteuredjaja_id'] == 18) {
                            ?>

                        <!-- AKO NISU MJERILA KRVNOG PRITISKA -->
                        <?php }
                        else if ($vrstauredjaja_['vrsteuredjaja_id'] != 11 && 
                            $vrstauredjaja_['vrsteuredjaja_id'] != 12 && 
                            $vrstauredjaja_['vrsteuredjaja_id'] != 13 && 
                            $vrstauredjaja_['vrsteuredjaja_id'] != 14 && 
                            $vrstauredjaja_['vrsteuredjaja_id'] != 49 && 
                            $vrstauredjaja_['vrsteuredjaja_id'] != 50) {
                        ?>
                            <!-- PRVI RED ZAGLAVLJA -->
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
                            <!-- DRUGI RED ZAGLAVLJA -->
                            <tr>
                                <th class="text-center" scope="col">1</th>
                                <th class="text-center" scope="col">2</th>
                                <th class="text-center" scope="col">3</th>
                            </tr>

                            <!-- AKO SU MJERNE VELIČINE 30, 32, 34, 36, 139 -->
                            <?php 
                            } else if ($mjernevelicine[$i - 1]['mjernevelicine_id'] == 30 || 
                                       $mjernevelicine[$i - 1]['mjernevelicine_id'] == 32 || 
                                       $mjernevelicine[$i - 1]['mjernevelicine_id'] == 34 || 
                                       $mjernevelicine[$i - 1]['mjernevelicine_id'] == 36 || 
                                       $mjernevelicine[$i - 1]['mjernevelicine_id'] == 139 || 
                                       $mjernevelicine[$i - 1]['mjernevelicine_id'] == 143) {
                            ?>
                            <tr>
                                <th class="text-center align-middle" scope="col" rowspan="2">Pritisak [<?php echo $mjernevelicine[$i - 1]['mjernevelicine_jedinica'] ?>]</th>
                                <th class="text-center align-middle" scope="col" colspan="2">1. ciklus</th>
                                <th class="text-center align-middle" scope="col" colspan="2">2. ciklus</th>
                                <th class="text-center align-middle" scope="col" colspan="2">Odstupanje</th>
                                <?php if($mjernevelicine[$i - 1]['mjernevelicine_id'] == 30 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 32) { ?>
                                <th class="text-center align-middle" scope="col" colspan="2">Histerezis</th>
                                <?php } ?>
                                </tr>
                                <tr>
                                    <th class="text-center align-middle" scope="col">Rastuća</th>
                                    <th class="text-center align-middle" scope="col">Opadajuća</th>
                                    <th class="text-center align-middle" scope="col">Rastuća</th>
                                    <th class="text-center align-middle" scope="col">Opadajuća</th>
                                    <th class="text-center align-middle" scope="col">Rastuća</th>
                                    <th class="text-center align-middle" scope="col">Opadajuća</th>
                                    <?php if($mjernevelicine[$i - 1]['mjernevelicine_id'] == 30 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 32) { ?>
                                    <th class="text-center align-middle" scope="col">1. ciklus</th>
                                    <th class="text-center align-middle" scope="col">2. ciklus</th>
                                    <?php } ?>
                                </tr>
                            <?php } else if($mjernevelicine[$i - 1]['mjernevelicine_id'] == 141 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 145) { ?>
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
                            <?php } else if($mjernevelicine[$i - 1]['mjernevelicine_id'] == 142 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 146) { ?>
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
                            <?php } else { ?>
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
                            <?php } ?>
                            <?php

                            $referentnevrijednosti = new allObjectsBy;
                            $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by("referentnevrijednosti", "referentnevrijednosti_mjernavelicinaid", $mjernevelicine[$i - 1]['mjernevelicine_id'], "referentnevrijednosti_referentnavrijednost", "ASC");

                            $rezultatimjerenja = new allObjectsBy;
                            $rezultatimjerenja = $rezultatimjerenja->fetch_all_objects_by('rezultatimjerenja', 'rezultatimjerenja_izvjestajid',$izvjestaj['izvjestaji_id'], "rezultatimjerenja_id", "ASC");
                            
                            foreach($rezultatimjerenja as $rezultatmjerenja){
                                $prvi = $rezultatmjerenja['rezultatimjerenja_mjernavelicinaid'];
                                $drugi = $rezultatmjerenja['rezultatimjerenja_referentnavrijednostid'];
                                $treci = $rezultatmjerenja['rezultatimjerenja_brojmjerenja'];
                                $kombinacija = "rezultat_".$prvi."_".$drugi."_".$treci;
                                $$kombinacija = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                                //var_dump($kombinacija);
                            }
                            
                            
                            //var_dump($rezultatimjerenja);

                            foreach ($referentnevrijednosti as $referentnavrijednost) {

                                if ($vrstauredjaja_['vrsteuredjaja_id'] == 3 ||  $vrstauredjaja_['vrsteuredjaja_id'] == 18) {

                                    if($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 2 || $referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 100){ ?>

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
                                    <!-- DRUGI RED ZAGLAVLJA -->
                                    <tr>
                                        <th class="text-center" scope="col">1</th>
                                        <th class="text-center" scope="col">2</th>
                                        <th class="text-center" scope="col">3</th>
                                    </tr>

                                    <?php } ?>

                                    <tr class="singleRed">
                                        <td class="text-center refVr">
                                            <?php echo ($referentnavrijednost['referentnevrijednosti_referentnavrijednost']); ?>
                                        </td>
                                        <?php for ($c = 1; $c <= 3; $c++) { ?>
                                            <td><input type="text"
                                                    name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>"
                                                    value="<?php $kombinacija = "rezultat_".$mjernevelicine[$i - 1]['mjernevelicine_id']."_".$referentnavrijednost['referentnevrijednosti_id']."_".$c; echo $$kombinacija ?>" class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" step=".01">
                                            </td>
                                        <?php } ?>
                                        <td class="text-center sredVr"></td>
                                        <td class="text-center apsGr"></td>
                                        <td class="text-center relGr"></td>
                                        <td class="text-center dozvOds">
                                            <?php echo ($referentnavrijednost['referentnevrijednosti_odstupanje']) ?>
                                        </td>
                                        <td class="text-center usaGl"></td>
                                    </tr>


                                <?php } else if ($vrstauredjaja_['vrsteuredjaja_id'] != 11 && $vrstauredjaja_['vrsteuredjaja_id'] != 12 && $vrstauredjaja_['vrsteuredjaja_id'] != 13 && $vrstauredjaja_['vrsteuredjaja_id'] != 14 && $vrstauredjaja_['vrsteuredjaja_id'] != 49 && $vrstauredjaja_['vrsteuredjaja_id'] != 50) { ?>
                                <tr class="singleRed">
                                    <td class="text-center refVr">
                                        <?php echo ($referentnavrijednost['referentnevrijednosti_referentnavrijednost']); ?>
                                    </td>
                                    <?php for ($c = 1; $c <= 3; $c++) { ?>
                                        <td><input type="text"
                                                name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>"
                                                value="<?php $kombinacija = "rezultat_".$mjernevelicine[$i - 1]['mjernevelicine_id']."_".$referentnavrijednost['referentnevrijednosti_id']."_".$c; if(isset($$kombinacija)){echo $$kombinacija;}else{echo "-";}  ?>" class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" step=".01">
                                        </td>
                                    <?php } ?>
                                    <td class="text-center sredVr"></td>
                                    <td class="text-center apsGr"></td>
                                    <td class="text-center relGr"></td>
                                    <td class="text-center dozvOds">
                                        <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 4){
                                            echo ($referentnavrijednost['referentnevrijednosti_odstupanje']) . " W";
                                        }else{
                                            echo ($referentnavrijednost['referentnevrijednosti_odstupanje']) . "%";
                                        } ?>
                                    </td>
                                    <td class="text-center usaGl"></td>
                                </tr>
                                <?php } else if ($mjernevelicine[$i - 1]['mjernevelicine_id'] == 30 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 32 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 34  || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 36 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 139 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 143) { ?>
                                    <tr class="singleRed">
                                        <td class="text-center refVr">
                                            <?php echo ($referentnavrijednost['referentnevrijednosti_referentnavrijednost']); ?>
                                        </td>
                                        <?php for ($c = 1; $c <= 4; $c++) { ?>
                                            <td><input type="text" name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>" value="<?php $kombinacija = "rezultat_".$mjernevelicine[$i - 1]['mjernevelicine_id']."_".$referentnavrijednost['referentnevrijednosti_id']."_".$c; echo $$kombinacija ?>" class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" step=".01"></td>
                                         <?php } ?>
                                        <td class="text-center odsRast"></td>
                                        <td class="text-center odsOpad"></td>
                                        <?php if($mjernevelicine[$i - 1]['mjernevelicine_id'] == 30 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 32){ ?>
                                        <td class="text-center hist1"></td>
                                        <td class="text-center hist2"></td>
                                        <?php } ?>
                                    </tr>
                                <?php } else if($mjernevelicine[$i - 1]['mjernevelicine_id'] == 141 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 145) { ?>

                                    <tr class="singleRed mjernavelicina_<?php echo $mjernevelicine[$i - 1]['mjernevelicine_id'] ?>">
                                        <!-- NEMAMO REF. VR. PA ISPISUJEMO SISTOLIČKI ILI DISTOLIČKI -->
                                        <td class="text-center refVr">
                                            <?php if($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 0){ echo "Sistolički pritisak [mmHG]";}else{ echo "Distolički pritisak [mmHG]"; } ?>
                                        </td>

                                    <!-- GENERIŠEMO POLJA ZA PRVIH 10 MJERENJA -->
                                    <?php for ($c = 1; $c <= 10; $c++) { ?>
                                            <td><input type="text"
                                                       name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>" 
                                                       value="<?php $kombinacija = "rezultat_".$mjernevelicine[$i - 1]['mjernevelicine_id']."_".$referentnavrijednost['referentnevrijednosti_id']."_".$c; echo $$kombinacija ?>" 
                                                       class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" 
                                                       step=".01">
                                                    </td>
                                            <?php } ?>
                                    </tr>

                                <?php } else if($mjernevelicine[$i - 1]['mjernevelicine_id'] == 142 || $mjernevelicine[$i - 1]['mjernevelicine_id'] == 146) { ?>

                                    <tr class="singleRed mjernavelicina_<?php echo $mjernevelicine[$i - 1]['mjernevelicine_id'] ?>">
                                        <!-- NEMAMO REF. VR. PA ISPISUJEMO SISTOLIČKI ILI DISTOLIČKI -->
                                        <td class="text-center refVr">
                                            <?php if($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 0){ echo "Sistolički pritisak [mmHG]";}else{ echo "Distolički pritisak [mmHG]"; } ?>
                                        </td>
                                    <!-- GENERIŠEMO POLJA ZA DRUGIH 10 MJERENJA -->
                                    <?php for ($c = 11; $c <= 20; $c++) { ?>
                                        <td><input type="text"
                                                   name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>"   
                                                   value='<?php $kombinacija = "rezultat_".$mjernevelicine[$i - 1]['mjernevelicine_id']."_".$referentnavrijednost['referentnevrijednosti_id']."_".$c; echo $$kombinacija ?>'
                                                   class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" 
                                                   step=".01">
                                                    </td>
                                            <?php } ?>
                                    </tr>

                                <?php } else { ?>
                                    <tr class="singleRed mjernavelicina_<?php echo $mjernevelicine[$i - 1]['mjernevelicine_id'] ?>">
                                                <td class="text-center refVr">
                                                <?php echo ($referentnavrijednost['referentnevrijednosti_referentnavrijednost']); ?>
                                                </td>
                                            <?php for ($c = 1; $c <= 2; $c++) { ?>
                                                    <td><input type="text"
                                                            name="<?php echo "rezultat_" . $mjernevelicine[$i - 1]['mjernevelicine_id'] . "_" . $referentnavrijednost['referentnevrijednosti_id'] . "_" . $c ?>"
                                                            value="<?php $kombinacija = "rezultat_".$mjernevelicine[$i - 1]['mjernevelicine_id']."_".$referentnavrijednost['referentnevrijednosti_id']."_".$c; echo $$kombinacija ?>" class="w-100 mjerenje mjerenje<?php echo $c ?> text-right" step=".01">
                                                    </td>
                                            <?php } ?>
                                                <td class="text-center razp1p2"></td>
                                                <td class="text-center stoIsp"></td>
                                            </tr>
                            <?php  }} ?>
                        </thead>
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
                        <div class="w-100 d-flex align-items-center"><h6 class="mr-1 mb-0">Postavljena vrijednost: </h6><input type="text" name="rezultat_142_523_21" id="" class="" value="<?php $kombinacija = "rezultat_".$mjernevelicine[$i - 1]['mjernevelicine_id']."_".$referentnavrijednost['referentnevrijednosti_id']."_21"; echo $$kombinacija ?>"></div>

                        <!-- NOVI RED -->
                        <br><br><br>

                    <?php }
                 }
                if ($vrstauredjaja_['vrsteuredjaja_id'] != 11 && $vrstauredjaja_['vrsteuredjaja_id'] != 12 && $vrstauredjaja_['vrsteuredjaja_id'] != 13 && $vrstauredjaja_['vrsteuredjaja_id'] != 14 && $vrstauredjaja_['vrsteuredjaja_id'] != 49 && $vrstauredjaja_['vrsteuredjaja_id'] != 50) { ?>
                        <!-- NOVI ŽIG -->
                        <div class="col-lg-3 d-flex flex-column mb-2 p-0">
                            <label for="izvjestaji_novizig">Novi republički žig:</label>
                            <input type="text" name="izvjestaji_novizig" value="<?php echo $izvjestaj['izvjestaji_novizig'] ?>">
                        </div>
                        <div class="col-lg-12 d-flex flex-column mb-2 p-0">
                            <label for="izvjestaji_napomena">Napomena</label>
                            <textarea name="izvjestaji_napomena" id=""><?php echo $izvjestaj['izvjestaji_napomena'] ?></textarea>
                        </div>
                        <hr class="w-100">
                        <h6 class="w-100 mb-4 font-weight-bold">5. Izjava o usaglašenosti</h6>
                        <?php } else if ($vrstauredjaja_['vrsteuredjaja_id'] == 11 || $vrstauredjaja_['vrsteuredjaja_id'] == 13 || $vrstauredjaja_['vrsteuredjaja_id'] == 12 || $vrstauredjaja_['vrsteuredjaja_id'] == 14 || $vrstauredjaja_['vrsteuredjaja_id'] == 49 || $vrstauredjaja_['vrsteuredjaja_id'] == 50) { ?>
                            <!-- Rezultati ispitivanja mjerila -->
                            <h6 class="w-100 mb-4 font-weight-bold">5. Rezultati ispitivanja mjerila</h6>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center" scope="col"
                                            style="border-top:1px solid transparent;border-left:1px solid transparent;"></th>
                                        <th class="text-center" scope="col" style="">Maksimalno odstupanje</th>
                                        <th class="text-center" scope="col" style="">Maksimalno dozvoljeno odstupanje</th>
                                        <th class="text-center" scope="col" style="">Zadovoljava</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" scope="col">Tačnost pokazivanja</th>
                                        <th class="text-center max1" scope="col"></th>
                                        <?php if($vrstauredjaja_['vrsteuredjaja_id'] != 49 && $vrstauredjaja_['vrsteuredjaja_id'] != 50 && $vrstauredjaja_['vrsteuredjaja_id'] != 11 && $vrstauredjaja_['vrsteuredjaja_id'] != 12 && $vrstauredjaja_['vrsteuredjaja_id'] != 13 && $vrstauredjaja_['vrsteuredjaja_id'] != 14){ ?>
                                        <th class="text-center" scope="col">4</th>
                                        <?php } else{ ?>
                                        <th class="text-center" scope="col">3</th>
                                        <?php } ?>
                                        <th class="text-center usaGl1" scope="col"></th>
                                    </tr>

                                    <?php if ($vrstauredjaja_['vrsteuredjaja_id'] != 49 && $vrstauredjaja_['vrsteuredjaja_id'] != 50) { ?>
                                    <tr>

                                        <?php if ($vrstauredjaja_['vrsteuredjaja_id'] == 11 || $vrstauredjaja_['vrsteuredjaja_id'] == 13) { ?>
                                        <th class="text-center" scope="col">Histerezis</th>

                                        <?php } else { ?>        
                                            <th class="text-center" scope="col">Uticaj žive na rad mjerila</th>
                                        <?php } ?>

                                        <?php if ($vrstauredjaja_['vrsteuredjaja_id'] == 11 || $vrstauredjaja_['vrsteuredjaja_id'] == 13) { ?>
                                            <th class="text-center max2" scope="col"></th>
                                        <?php } else { ?>
                                            <th class="text-center" scope="col">
                                                <input class="text-right rezultat_0_0_1 mjerenje" type="number" name="rezultat_0_0_1"
                                                    value="<?php echo $rezultat_0_0_1 ?>" step=".01">
                                            </th>
                                    <?php } ?>

                                    <?php if ($vrstauredjaja_['vrsteuredjaja_id'] == 11 || $vrstauredjaja_['vrsteuredjaja_id'] == 13) { ?>
                                            <th class="text-center" scope="col">4</th>
                                    <?php } else { ?>
                                            <th class="text-center" scope="col">1.5</th>
                                    <?php } ?>
                                        <th class="text-center usaGl2" scope="col"></th>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <th class="text-center" scope="col">Ispitivanje curenja zraka</th>
                                        <th class="text-center max3" scope="col"></th>
                                        <?php if($vrstauredjaja_['vrsteuredjaja_id'] != 49 && $vrstauredjaja_['vrsteuredjaja_id'] != 50){ ?>
                                            <th class="text-center" scope="col">4</th>
                                        <?php } else{ ?>
                                            <th class="text-center" scope="col">6</th>
                                        <?php } ?>
                                        <th class="text-center usaGl3" scope="col"></th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" scope="col">Ispitivanja ventila brzog ispusta</th>
                                        <th class="text-center max4" scope="col">
                                            <input class="text-right rezultat_0_0_0 mjerenje" type="number" name="rezultat_0_0_0"
                                                value="<?php echo $rezultat_0_0_0 ?>" step=".01">
                                        </th>
                                        <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 13 || $vrstauredjaja_['vrsteuredjaja_id'] == 14 || $vrstauredjaja_['vrsteuredjaja_id'] == 50) { ?>
                                        <th class="text-center" scope="col">5</th>
                                        <?php }else{ ?>
                                        <th class="text-center" scope="col">10</th>
                                        <?php } ?>
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
                            <?php if($vrstauredjaja_['vrsteuredjaja_id'] == 12 || $vrstauredjaja_['vrsteuredjaja_id'] == 14){ ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Pregled</th>
                                            <th>Zadovoljava</th>
                                        </tr>
                                    </thead>
                                    <>
                                        <tr>
                                            <td>Ispitivanje curenja žive</td>
                                            <th><select name="rezultat_0_0_2" id="" class="w-100">
                                                    <option value=""></option>
                                                    <option value="1" <?php if($rezultat_0_0_2 == 1){ echo "selected";} ?>>DA</option>
                                                    <option value="0"<?php if($rezultat_0_0_2 == 0){ echo "selected";} ?>>NE</option>
                                                </select></th>
                                        </tr>
                                        <tr>
                                            <td>Ispitivanje mehanizma za zaključavanje žive</td>
                                            <th><select name="rezultat_0_0_3" id="" class="w-100">
                                                    <option value=""></option>
                                                    <option value="1" <?php if($rezultat_0_0_3 == 1){ echo "selected";} ?>>DA</option>
                                                    <option value="0"<?php if($rezultat_0_0_3 == 0){ echo "selected";} ?>>NE</option>
                                                </select></th>
                                        </tr>
                                        <tr>
                                            <td>Ispitivanje kvaliteta žive</td>
                                            <th><select name="rezultat_0_0_4" id="" class="w-100">
                                                    <option value=""></option>
                                                    <option value="1" <?php if($rezultat_0_0_4 == 1){ echo "selected";} ?>>DA</option>
                                                    <option value="0"<?php if($rezultat_0_0_4 == 0){ echo "selected";} ?>>NE</option>
                                                </select></th>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php } ?>
                            <!-- NOVI ŽIG -->
                            <div class="col-lg-3 d-flex flex-column mb-2 p-0">
                                <label for="izvjestaji_novizig">Novi republički žig:</label>
                                <input type="text" name="izvjestaji_novizig" value="<?php echo $izvjestaj['izvjestaji_novizig'] ?>">
                            </div>
                            <div class="col-lg-12 d-flex flex-column mb-2 p-0">
                                <label for="izvjestaji_napomena">Napomena</label>
                                <textarea name="izvjestaji_napomena" id=""><?php echo $izvjestaj['izvjestaji_napomena'] ?></textarea>
                            </div>
                        <hr class="w-100">
                        <?php } else { ?>
                        <!-- Rezultati ispitivanja mjerila -->
                        <h6 class="w-100 mb-4 font-weight-bold">6. Izjava o usaglašenosti</h6>
                        <?php } ?>
                        <div class="col-lg-12 d-flex flex-column mb-2 p-0">
                            <p class="">Rezultati inspekcije mjerila <span class="usaGlFinal"></span> sa propisanim opsegom
                                dozvoljenih odstupanja u skladu sa gore navedenim Pravilnikom.<br>Na osnovu rezultata inspekcije
                                mjerilo je označeno inspekcijskom oznakom - markicom.<br>Rezultati inspekcije se odnose
                                isključivo na dati predmet u trenutku inspekcije.<br>Izvještaj o inspekciji ne smije se
                                reprodukovati osim u cjelini.</p>
                        </div>
                        <div class="col-lg-3 d-flex flex-column mb-2 p-0">
                            <label for="izvjestaji_izvrsioid">Inspekciju izvršio i izvještaj izradio:</label>
                            <input type="text" name="izvjestaji_izvrsioid" value="<?php echo $izvjestaj['izvjestaji_izvrsioid'] ?>" hidden>
                            <select name="" id="" class="selectElement_">
                                <option value=""></option>
                                <?php foreach ($kontrolori as $kontrolor) { ?>
                                    <option value="<?php echo $kontrolor['kontrolori_id'] ?>" <?php if($izvjestaj['izvjestaji_izvrsioid'] == $kontrolor['kontrolori_id']){ echo "selected"; } ?>>
                                        <?php echo $kontrolor['kontrolori_ime'] . " " . $kontrolor['kontrolori_prezime'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <label for="izvjestaji_izvrsiodadatum">Datum:</label>
                            <input type="date" name="izvjestaji_izvrsiodadatum"  value="<?php echo $izvjestaj['izvjestaji_izvrsiodadatum'] ?>">
                        </div>
                        <div class="col-lg-3 d-flex flex-column mb-2 p-0"></div>
                        <div class="col-lg-3 d-flex flex-column mb-2 p-0"></div>
                        <div class="col-lg-3 d-flex flex-column mb-2 p-0">
                            <label for="izvjestaji_ovjerioid">Izvještaj ovjerio:</label>
                            <input type="text" name="izvjestaji_ovjerioid" value="<?php echo $izvjestaj['izvjestaji_ovjerioid'] ?>" hidden>
                            <select name="" id="" class="selectElement_">
                                <option value=""></option>
                                <?php foreach ($kontrolori as $kontrolor) { ?>
                                    <option value="<?php echo $kontrolor['kontrolori_id'] ?>" <?php if($izvjestaj['izvjestaji_ovjerioid'] == $kontrolor['kontrolori_id']){ echo "selected"; } ?>>
                                        <?php echo $kontrolor['kontrolori_ime'] . " " . $kontrolor['kontrolori_prezime'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <label for="izvjestaji_ovjeriodatum">Datum:</label>
                            <input type="date" name="izvjestaji_ovjeriodatum" value="<?php echo $izvjestaj['izvjestaji_ovjeriodatum'] ?>">
                        </div>
                <?php } ?>
                
                    <div class="col-lg-12 d-flex flex-column mt-3">
                        <button name="edit_<?php echo $table ?>" class="btn btn-primary" type="submit"
                            style="width:150px">Sačuvaj</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

</main>

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
                        var mjerenje1 = "-";
                    } else {
                        var mjerenje1 = $(this).find(".mjerenje1").val();
                    }

                    //KUPIMO DRUGO MJERENJE AKO POSTOJI
                    if ($(this).find(".mjerenje2").val().length === 0) {
                        var mjerenje2 = "-";
                    } else {
                        var mjerenje2 = $(this).find(".mjerenje2").val();
                    }

                    //KUPIMO TREĆE MJERENJE AKO POSTOJI
                    if ($(this).find(".mjerenje3").val().length === 0) {
                        var mjerenje3 = "-";
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
                            
                            //AKO SU SVA MJERENJA BROJEVI (nijedno "-" ni "--")
                            } else if (mjerenje1 != "-" && mjerenje2 != "-" && mjerenje3 != "-" && mjerenje1 != "--" && mjerenje2 != "--" && mjerenje3 != "--") {
                                usaGl = "DA";
                            }
                        }else{
                            //AKO JE RELATIVNA GREŠKA VEĆA OD DOZVOLJENOG ODSTUPANJA
                            if (relGr > dozvOds) {
                                usaGl = "NE";
                                usaGlFinal = "NISU USAGLAŠENI";
                            
                            //AKO SU SVA MJERENJA BROJEVI
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
                            
                            //AKO SU SVA MJERENJA BROJEVI
                            } else if (mjerenje1 != "-" && mjerenje2 != "-" && mjerenje3 != "-" && mjerenje1 != "--" && mjerenje2 != "--" && mjerenje3 != "--") {
                                usaGl = "DA";
                            }
                        <?php } else { ?>
                            if (relGr > dozvOds) {
                            usaGl = "NE";
                            usaGlFinal = "NISU USAGLAŠENI";
                            
                            //AKO SU SVA MJERENJA BROJEVI
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
                        
                        //AKO JE BILO KOJE MJERENJE "--" (nije mjerljivo)
                        } else if (mjerenje1 == "--" || mjerenje2 == "--") {
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

                        //AKO SU SVA MJERENJA "-" ili sva "--"
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

        function filterDevices() {
            console.log("1");
            const typeSelect = document.getElementById('predmet_inspekcije_select');
            const selectedType = typeSelect.value;
            const deviceOptions = document.querySelectorAll('#broj_mjerila_select option');

            deviceOptions.forEach(option => {
                if (option.dataset.type === selectedType || selectedType === "") {
                    option.style.display = "block"; // Prikaži opcije koje odgovaraju
                } else {
                    option.style.display = "none"; // Sakrij opcije koje ne odgovaraju
                }
            });
        }
    });

    </script>

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

<?php

} else {
    header('Location: index.php');
}

//INCLUDES
include_once ('includes/footer.php');

?>