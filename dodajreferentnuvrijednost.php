<?php
//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('referentnevrijednosti', 'dodavanje')) {
        header('Location: index.php');
        exit;
    }

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    $mjernevelicine = new allObjects;
    $mjernevelicine = $mjernevelicine->fetch_all_objects("mjernevelicine", "mjernevelicine_id", "ASC");
    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1 class="mb-3">Dodaj referentnu vrijednost</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item"><a href="referentnevrijednosti.php">Referentne vrijednosti</a></li>
                    <li class="breadcrumb-item active">Dodaj referentnu vrijednost</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_) ?>" method="post">
                    <div class="col-lg-4 d-flex flex-column mb-2">
                        <label for="referentnevrijednosti_mjernavelicinaid">Mjerna veličina:</label>
                        <input type="number" name="referentnevrijednosti_mjernavelicinaid" value="" hidden>
                        <select name="" id="" class="selectElement_" required>
                            <option value=""></option>
                            <?php foreach ($mjernevelicine as $mjernavelicina) {
                                $vrstauredjaja = new singleObject;
                                $vrstauredjaja = $vrstauredjaja->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $mjernavelicina['mjernevelicine_vrstauredjajaid']);
                                ?>
                                <option value="<?php echo $mjernavelicina['mjernevelicine_id'] ?>">
                                    <?php
                                    if (isset($vrstauredjaja['vrsteuredjaja_opis']) && $vrstauredjaja['vrsteuredjaja_opis'] != "") {
                                        echo $vrstauredjaja['vrsteuredjaja_naziv'] . " (" . $vrstauredjaja['vrsteuredjaja_opis'] . ") - " . $mjernavelicina['mjernevelicine_naziv'];
                                    } else {
                                        echo $vrstauredjaja['vrsteuredjaja_naziv'] . " - " . $mjernavelicina['mjernevelicine_naziv'];
                                    }
                                    ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-4 d-flex flex-column mb-2">
                        <label for="referentnevrijednosti_referentnavrijednost">Referentna vrijednost:</label>
                        <input type="number" name="referentnevrijednosti_referentnavrijednost" step="any" required>
                    </div>
                    <div class="col-lg-4 d-flex flex-column mb-2">
                        <label for="referentnevrijednosti_odstupanje">Dozvoljeno odstupanje:</label>
                        <input type="number" name="referentnevrijednosti_odstupanje" step="any" required>
                    </div>
                    <div class="col-lg-12 d-flex flex-column mt-3">
                        <button name="submit_referentnevrijednosti" class="btn btn-primary" type="submit"
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

    <!-- <script>

    $(document).ready(function () {
        $(".selectElement_").change(function () {
            var selectValue = $(this).val();
            $(this).prev().val(selectValue);
        });
    });

</script> -->

    <?php

} else {
    header('Location: index.php');
}

//INCLUDES
include_once ('includes/footer.php');

?>