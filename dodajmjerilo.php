<?php
//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('pregledmjerila', 'dodavanje')) {
        header('Location: index.php');
        exit;
    }

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    //GET ITEMS
    $vrsteuredjaja = new allObjects;
    $vrsteuredjaja = $vrsteuredjaja->fetch_all_objects("vrsteuredjaja", "vrsteuredjaja_naziv", "ASC");

    $klijenti = new allObjects;
    $klijenti = $klijenti->fetch_all_objects("klijenti", "klijenti_naziv", "ASC");

    $mjerneVelicine = new allObjects;
    $mjerneVelicine = $mjerneVelicine->fetch_all_objects("mjernevelicine", "mjernevelicine_naziv", "ASC");

    $referentneVrijednosti = new allObjects;
    $referentneVrijednosti = $referentneVrijednosti->fetch_all_objects("referentnevrijednosti", "referentnevrijednosti_referentnavrijednost", "ASC");
    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1 class="mb-3">Dodaj mjerilo</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item"><a href="pregledmjerila.php">Pregled mjerila</a></li>
                    <li class="breadcrumb-item active">Dodaj mjerilo</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_) ?>" method="post">
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="mjerila_vrstauredjajaid">Vrsta mjerila:</label>
                        <input type="number" name="mjerila_vrstauredjajaid" value="" hidden>
                        <select name="" id="vrstaMjerila" class="selectElement_" required>
                            <option value=""></option>
                            <?php foreach ($vrsteuredjaja as $vrstauredjaja) { ?>
                                <option value="<?php echo $vrstauredjaja['vrsteuredjaja_id'] ?>">
                                    <?php
                                    if (isset($vrstauredjaja['vrsteuredjaja_opis'])) {
                                        echo $vrstauredjaja['vrsteuredjaja_naziv'] . " (" . $vrstauredjaja['vrsteuredjaja_opis'] . ") ";
                                    } else {
                                        echo $vrstauredjaja['vrsteuredjaja_naziv'];
                                    }
                                    ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="mjerila_klijentid">Vlasnik mjerila:</label>
                        <input type="number" name="mjerila_klijentid" value="" hidden>
                        <select name="" id="klijent" class="selectElement_" required>
                            <option value=""></option>
                            <?php foreach ($klijenti as $klijent) { ?>
                                <option value="<?php echo $klijent['klijenti_id'] ?>">
                                    <?php echo $klijent['klijenti_naziv']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="mjerila_zadovoljava">Zadovoljava:</label>
                        <select name="mjerila_zadovoljava" id="" required>
                            <option value=""></option>
                            <option value="DA">DA</option>
                            <option value="NE">NE</option>
                        </select>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="mjerila_proizvodjac">Proizvođač:</label>
                        <input type="text" name="mjerila_proizvodjac" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="mjerila_tip">Tip:</label>
                        <input type="text" name="mjerila_tip" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="mjerila_serijskibroj">Serijski broj:</label>
                        <input type="text" name="mjerila_serijskibroj">
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="mjerila_godinaproizvodnje">Godina proizvodnje:</label>
                        <input type="text" name="mjerila_godinaproizvodnje">
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="mjerila_sluzbenaoznaka">Službena oznaka:</label>
                        <input type="text" name="mjerila_sluzbenaoznaka">
                    </div>
                    <!-- <div class="col-lg-3 d-flex flex-column mb-2">
                        <label id="hiddenLabel" for="mjerila_djeca" hidden>Mjerilo krvnog pritiska za djecu i
                            novorođenčad:</label>
                        <input type="text" name="mjerila_djeca" hidden>
                        <select name="" id="hiddenSelect" class="selectElement_" hidden>
                            <option value=""></option>
                            <option value="1">DA</option>
                            <option value="0">NE</option>
                        </select>
                    </div>
                    <div class="col-lg-12 d-flex flex-column mb-2">
                        <label for="mjerila_sluzbenaoznaka">Mjerne veličine i referentne vrijednosti:</label>
                        <input type="text" name="mjerila_referentnevrijedosti" hidden_>
                        <?php 
                        
                        foreach ($mjerneVelicine as $mjernaVelicina) { ?>

                            <p class="text-dark uredjaj_all uredjaj_<?php echo $mjernaVelicina['mjernevelicine_vrstauredjajaid'] ?> d-none"><?php echo $mjernaVelicina['mjernevelicine_naziv'] ?></p>
                            <div class="flex-wrap uredjaj_all uredjaj_<?php echo $mjernaVelicina['mjernevelicine_vrstauredjajaid'] ?> d-none">
                            <?php foreach ($referentneVrijednosti as $referentnaVrijednost) { 
                                
                                if($mjernaVelicina["mjernevelicine_id"] == $referentnaVrijednost['referentnevrijednosti_mjernavelicinaid']){ 
                                ?>
                                    <div class="col-lg-1">
                                        <input class="referentna-vrijednost" value="<?php echo $referentnaVrijednost['referentnevrijednosti_id'] ?>" name="" type="checkbox">
                                        <label for=""><?php echo $referentnaVrijednost['referentnevrijednosti_referentnavrijednost'] ?></label>
                                    </div>
                            
                                <?php } 
                            } ?>
                            </div>
                        
                        <?php } ?>
                        
                        <script>
                        $(document).ready(function () {

                            //ispis svih ref. vr. i mjer. vel
                            $('#vrstaMjerila').on('change', function () {
                                var value = $(this).val();

                                // 1. Sklanjamo 'd-flex', dodajemo 'd-none' na sve .uredjaj_all
                                $('.uredjaj_all').removeClass('d-flex').addClass('d-none');

                                // 2. Pronalazimo sve .uredjaj_$value, uklanjamo 'd-none', dodajemo 'd-flex'
                                $('.uredjaj_' + value).removeClass('d-none').addClass('d-flex');
                            });

                            //selekcija mjer. vel
                            let selektovaneVrijednosti = [];

                            $(document).on('change', '.referentna-vrijednost', function () {
                                let value = $(this).val();
                                let index = selektovaneVrijednosti.indexOf(value);

                                if ($(this).is(':checked')) {
                                    if (index === -1) {
                                        selektovaneVrijednosti.push(value);
                                    }
                                } else {
                                    if (index !== -1) {
                                        selektovaneVrijednosti.splice(index, 1);
                                    }
                                }

                                // Sortiranje niza kao brojeva (ne stringova)
                                let sortirano = selektovaneVrijednosti
                                    .map(Number)
                                    .sort((a, b) => a - b)
                                    .join(',');

                                // Ažuriraj input
                                $('input[name="mjerila_referentnevrijedosti"]').val(sortirano);
                            });
                        });
                        </script>
                    </div> -->
                    <div class="col-lg-12 d-flex flex-column mt-3">
                        <button name="submit_mjerila" class="btn btn-primary" type="submit"
                            style="width:150px">Sačuvaj</button>
                    </div>
                </form>
            </div>
        </section>

    </main>

    <style>
        .btn.btn-primary {
            background-color: #00335e;
        }
    </style>

    <?php

} else {
    header('Location: index.php');
}

//INCLUDES
include_once ('includes/footer.php');

?>