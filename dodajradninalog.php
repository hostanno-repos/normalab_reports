<?php
//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

include_once ('includes/header.php');
include_once ('includes/sidebar.php');
//GET ITEMS
$brojaci = new allObjects;
$brojaci = $brojaci->fetch_all_objects("brojacirn", "brojacirn_id", "ASC");
$klijenti = new allObjects;
$klijenti = $klijenti->fetch_all_objects("klijenti", "klijenti_id", "ASC");
$kontrolori = new allObjects;
$kontrolori = $kontrolori->fetch_all_objects("kontrolori", "kontrolori_prezime", "ASC");
$mjerila = new allObjects;
$mjerila = $mjerila->fetch_all_objects("mjerila", "mjerila_id", "DESC");
$mjerilo = new singleObject;
$mjerilo = $mjerilo->fetch_single_object("mjerila", "mjerila_id", $_GET['mjerilo']);
// Jedan izvor istine: mapa vrsta uređaja → metode inspekcije (RU-01 … RU-12)
$vrstaUredjajaId = (int) ($mjerilo['mjerila_vrstauredjajaid'] ?? 0);
$metodaInspekcijeZaVrstu = null;
switch ($vrstaUredjajaId) {
    case 1: $metodaInspekcijeZaVrstu = 1; break;   // EKG → RU-01
    case 2: $metodaInspekcijeZaVrstu = 2; break;   // Pacijent monitor → RU-02
    case 3: $metodaInspekcijeZaVrstu = 3; break;   // Defibrilator → RU-03
    case 4: $metodaInspekcijeZaVrstu = 4; break;   // Terapeutski ultrazvuk → RU-04
    case 5: $metodaInspekcijeZaVrstu = 5; break;   // Anesteziološka mašina → RU-06
    case 6: $metodaInspekcijeZaVrstu = 6; break;   // Infuzomat → RU-05
    case 7: $metodaInspekcijeZaVrstu = 7; break;   // Inkubator → RU-07
    case 8: $metodaInspekcijeZaVrstu = 6; break;   // Perfuzor → RU-05
    case 9: $metodaInspekcijeZaVrstu = 5; break;   // Respirator → RU-06
    case 10: $metodaInspekcijeZaVrstu = 8; break;  // Dijalizni uređaj → RU-08
    case 11: case 12: case 13: case 14: case 49: case 50: $metodaInspekcijeZaVrstu = 9; break; // Krvni pritisak → RU-09
    case 52: $metodaInspekcijeZaVrstu = 10; break; // Neautomatska vaga → RU-12
}
$metodeinspekcije = new allObjects;
$metodeinspekcije = $metodeinspekcije->fetch_all_objects("metodeinspekcije", "metodeinspekcije_id", "ASC");
$vrsteuredjaja = new allObjects;
$vrsteuredjaja = $vrsteuredjaja->fetch_all_objects("vrsteuredjaja", "vrsteuredjaja_id", "ASC");
//GET THIS YEAR BROJAČ
$ovagodinaBrojac = new singleObject;
$ovagodinaBrojac = $ovagodinaBrojac->fetch_single_object("brojacirn", "brojacirn_godina", date("Y"));
$ovagodinaBrojac['brojacirn_godina'] = substr( $ovagodinaBrojac['brojacirn_godina'], -2)



?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1 class="mb-3">Dodaj radni nalog</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                <li class="breadcrumb-item"><a href="pregledradnihnaloga.php">Pregled radnih naloga</a></li>
                <li class="breadcrumb-item active">Dodaj radni nalog</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12 d-flex flex-column mb-2">
                <p class="pl-3" id="brojRadnogNaloga"><b>Broj radnog naloga:
                        <span><?php echo $ovagodinaBrojac['brojacirn_prefiks'] ."-". ($ovagodinaBrojac['brojacirn_brojac'] + 1) . "/" . $ovagodinaBrojac['brojacirn_godina'] ?></b></span>
                </p>
            </div>
            <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_) ?>" method="post">
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <!-- BROJAČ -->
                    <label for="radninalozi_brojacrnid">Brojač:</label>
                    <?php foreach ($brojaci as $brojac) {
                        if (date("Y") == $brojac['brojacirn_godina']) { ?>
                            <input type="number" name="radninalozi_brojacrnid" value="<?php echo $brojac['brojacirn_id'] ?>" hidden>
                        <?php }
                    } ?>
                    <select name="" id="" class="selectElement" required>
                        <option value=""></option>
                        <?php foreach ($brojaci as $brojac) { ?>
                            <option prefix="<?php echo $brojac['brojacirn_prefiks'] ?>"
                                brojac="<?php echo $brojac['brojacirn_brojac'] ?>"
                                godina="<?php echo $brojac['brojacirn_godina'] ?>" value="<?php echo $brojac['brojacirn_id'] ?>"
                                <?php if (date("Y") == $brojac['brojacirn_godina']) {
                                    echo "selected";
                                } ?>>
                                <?php echo $brojac['brojacirn_godina'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- BROJ RADNOG NALOGA -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_broj">Broj radnog naloga:</label>
                    <input type="text" name="radninalozi_broj"
                        value="<?php echo $ovagodinaBrojac['brojacirn_prefiks'] ."-". ($ovagodinaBrojac['brojacirn_brojac'] + 1) . "/" . $ovagodinaBrojac['brojacirn_godina'] ?>"
                        hidden>
                    <input type="text" name="radninalozi_broj_disabled"
                        value="<?php echo $ovagodinaBrojac['brojacirn_prefiks'] ."-". ($ovagodinaBrojac['brojacirn_brojac'] + 1) . "/" . $ovagodinaBrojac['brojacirn_godina'] ?>"
                        disabled>
                </div>
                <!-- KLIJENT -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_klijentid">Podnosilac zahtjeva:</label>
                    <input type="number" name="radninalozi_klijentid" value="<?php echo $mjerilo['mjerila_klijentid'] ?>" hidden>
                    <select name="" id="" class="selectElement_" required disabled>
                        <option value=""></option>
                        <?php foreach ($klijenti as $klijent) { ?>
                            <option value="<?php echo $klijent['klijenti_id'] ?>" <?php if($klijent['klijenti_id'] == $mjerilo['mjerila_klijentid']){echo "selected";} ?>>
                                <?php echo $klijent['klijenti_naziv'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- BROJ ZAHTJEVA ZA INSPEKCIJU -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_brojzahtjeva">Broj zahtjeva za inspekciju:</label>
                    <input type="text" name="radninalozi_brojzahtjeva" required>
                </div>
                <!-- PREDMET INSPEKCIJE -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_vrstauredjajaid">Predmet inspekcije:</label>
                    <input type="number" name="radninalozi_vrstauredjajaid" value="<?php echo $mjerilo['mjerila_vrstauredjajaid'] ?>" hidden>
                    <select name="" id="predmet_inspekcije_select" onchange="filterDevices()" class="selectElement_" required disabled>
                        <option value=""></option>
                        <?php foreach ($vrsteuredjaja as $vrstauredjaja) { ?>
                            <option value="<?php echo $vrstauredjaja['vrsteuredjaja_id'] ?>" <?php if($vrstauredjaja['vrsteuredjaja_id'] == $mjerilo['mjerila_vrstauredjajaid']){echo "selected";} ?>>
                                <?php echo $vrstauredjaja['vrsteuredjaja_naziv'] ?>
                                <?php 
                                if(isset($vrstauredjaja['vrsteuredjaja_opis'])){
                                    echo " - ".$vrstauredjaja['vrsteuredjaja_opis'];
                                }
                                ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- VRSTA INSPEKCIJE -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_metodainspekcijeid">Metoda inspekcije:</label>
                    <input type="number" name="radninalozi_metodainspekcijeid" value="<?php echo $metodaInspekcijeZaVrstu !== null ? (int) $metodaInspekcijeZaVrstu : ''; ?>" hidden>
                    <select name="" id="" class="selectElement_" disabled>
                        <option value=""></option>
                        <?php foreach ($metodeinspekcije as $metodainspekcije) { ?>
                            <option value="<?php echo $metodainspekcije['metodeinspekcije_id'] ?>" <?php
                                if ($metodaInspekcijeZaVrstu !== null && (int) $metodainspekcije['metodeinspekcije_id'] === $metodaInspekcijeZaVrstu) {
                                    echo 'selected';
                                }
                            ?>>
                                <?php echo $metodainspekcije['metodeinspekcije_naziv'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- BROJ MJERILA ZA INSPEKCIJU -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_mjeriloid">Mjerilo za inspekciju:</label>
                    <input type="number" name="radninalozi_mjeriloid" value="<?php echo $mjerilo['mjerila_id'] ?>" hidden>
                    <select name="" id="broj_mjerila_select" class="selectElement_" required disabled>
                        <option value=""></option>
                        <?php foreach ($mjerila as $mjerilo_) { ?>
                            <?php
                            $uredjaj = new singleObject;
                            $uredjaj = $uredjaj->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $mjerilo_['mjerila_vrstauredjajaid']); 
                            ?>
                            <option data-type="<?php echo $uredjaj['vrsteuredjaja_id'] ?>" value="<?php echo $mjerilo_['mjerila_id'] ?>" <?php if($mjerilo['mjerila_id'] == $mjerilo_['mjerila_id']){ echo "selected";} ?>>
                                <?php echo $mjerilo_['mjerila_id'].". " ?>
                                <?php echo $uredjaj['vrsteuredjaja_naziv']; if(isset($uredjaj['vrsteuredjaja_opis'])){ echo " - ".$uredjaj['vrsteuredjaja_opis'];} ?>
                                <?php if(isset($mjerilo_['mjerila_serijskibroj']) && $mjerilo_['mjerila_serijskibroj'] != ""){
                                    echo "(".$mjerilo_['mjerila_serijskibroj'].")";
                                } ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- KONTROLOR -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_kontrolorid">Kontrolor:</label>
                    <input type="number" name="radninalozi_kontrolorid" value="" hidden>
                    <select name="" id="" class="selectElement_" required>
                        <option value=""></option>
                        <?php foreach ($kontrolori as $kontrolor) { ?>
                            <option value="<?php echo $kontrolor['kontrolori_id'] ?>">
                                <?php echo $kontrolor['kontrolori_prezime']." ".$kontrolor['kontrolori_ime'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- OČEKIVANI ZAVRŠETAK INSPEKCIJE -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_datumzavrsetka">Očekivani završetak inspekcije:</label>
                    <input type="date" name="radninalozi_datumzavrsetka" required>
                </div>
                <!-- POSEBNI ZAHTJEVI -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_posebnizahtjevi">Posebni zahtjevi:</label>
                    <textarea type="text" name="radninalozi_posebnizahtjevi" rows="1"></textarea>
                </div>
                <!-- RADNI NALOG OTVORIO -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_otvorioid">Radni nalog otvorio:</label>
                    <input type="number" name="radninalozi_otvorioid" value="" hidden>
                    <select name="" id="" class="selectElement_" required>
                        <option value=""></option>
                        <?php foreach ($kontrolori as $kontrolor) { ?>
                            <option value="<?php echo $kontrolor['kontrolori_id'] ?>">
                                <?php echo $kontrolor['kontrolori_prezime']." ".$kontrolor['kontrolori_ime'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- RADNI NALOG PRIMIO -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_primioid">Radni nalog primio:</label>
                    <input type="number" name="radninalozi_primioid" value="" hidden>
                    <select name="" id="" class="selectElement_" required>
                        <option value=""></option>
                        <?php foreach ($kontrolori as $kontrolor) { ?>
                            <option value="<?php echo $kontrolor['kontrolori_id'] ?>">
                                <?php echo $kontrolor['kontrolori_prezime']." ".$kontrolor['kontrolori_ime'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- RADNI NALOG ZATVORIO -->
                <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_zatvorioid">Radni nalog zatvorio:</label>
                    <input type="number" name="radninalozi_zatvorioid" value="" hidden>
                    <select name="" id="" class="selectElement_" required>
                        <option value=""></option>
                        <?php foreach ($kontrolori as $kontrolor) { ?>
                            <option value="<?php echo $kontrolor['kontrolori_id'] ?>">
                                <?php echo $kontrolor['kontrolori_prezime']." ".$kontrolor['kontrolori_ime'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- OČEKIVANI ZAVRŠETAK INSPEKCIJE -->
                <!-- <div class="col-lg-3 d-flex flex-column mb-2">
                    <label for="radninalozi_datum">Datum:</label>
                    <input type="date" name="radninalozi_datum" required>
                </div> -->
                <!-- SUBMIT -->
                <div class="col-lg-12 d-flex flex-column mt-3">
                    <button name="submit_radninalozi" class="btn btn-primary" type="submit"
                        style="width:150px">Sačuvaj</button>
                </div>
            </form>
        </div>
    </section>

</main>

<style>
    p {
        color: #000;
    }
</style>

<script>

    $(document).ready(function () {
        var prefixBrojaca = "<?php echo $ovagodinaBrojac['brojacirn_prefiks'] ?>";
        var brojacBrojaca = "<?php echo $ovagodinaBrojac['brojacirn_brojac'] ?>";
        var godinaBrojaca = "<?php echo $ovagodinaBrojac['brojacirn_godina'] ?>";
        $(".selectElement").change(function () {
            var selectValue = $(this).val();
            $(this).prev().val(selectValue);
            prefixBrojaca = $('option:selected', this).attr('prefix');
            brojacBrojaca = $('option:selected', this).attr('brojac');
            brojacBrojaca++;
            godinaBrojaca = $('option:selected', this).attr('godina');
            var brojRadnogNaloga = prefixBrojaca+"-"+brojacBrojaca+"/"+godinaBrojaca;
            $('input[name="radninalozi_broj"]').val(brojRadnogNaloga);
            $('input[name="radninalozi_broj_disabled"]').val(brojRadnogNaloga);
            $("#brojRadnogNaloga span").text(brojRadnogNaloga);
        });
        $(".selectElement_").change(function () {
            var selectValue = $(this).val();
            $(this).prev().val(selectValue);
        });
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

    //filterDevices();

</script>

<?php

} else {
    header('Location: index.php');
}

//INCLUDES
include_once ('includes/footer.php');

?>