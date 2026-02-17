<?php
//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('mjernevelicine', 'dodavanje')) {
        header('Location: index.php');
        exit;
    }

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    $vrsteuredjaja = new allObjects;
    $vrsteuredjaja = $vrsteuredjaja->fetch_all_objects("vrsteuredjaja", "vrsteuredjaja_naziv", "ASC");
    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1 class="mb-3">Dodaj mjernu veličinu</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item"><a href="mjernevelicine.php">Mjerne veličine</a></li>
                    <li class="breadcrumb-item active">Dodaj mjernu veličinu</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_) ?>" method="post">
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="mjernevelicine_vrstauredjajaid">Vrsta mjerila:</label>
                        <input type="number" name="mjernevelicine_vrstauredjajaid" value="" hidden>
                        <select name="" id="" class="selectElement_" required>
                            <option value=""></option>
                            <?php foreach ($vrsteuredjaja as $vrstauredjaja) { ?>
                                <option value="<?php echo $vrstauredjaja['vrsteuredjaja_id'] ?>">
                                    <?php if (isset($vrstauredjaja['vrsteuredjaja_opis']) && $vrstauredjaja['vrsteuredjaja_opis'] != "") {
                                        echo $vrstauredjaja['vrsteuredjaja_naziv'] . " (" . $vrstauredjaja['vrsteuredjaja_opis'] . ") ";
                                    } else {
                                        echo $vrstauredjaja['vrsteuredjaja_naziv'];
                                    } ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="mjernevelicine_naziv">Naziv:</label>
                        <input type="text" name="mjernevelicine_naziv" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="mjernevelicine_jedinica">Jedinica:</label>
                        <input type="text" name="mjernevelicine_jedinica" required>
                    </div>
                    <div class="col-lg-12 d-flex flex-column mt-3">
                        <button name="submit_mjernevelicine" class="btn btn-primary" type="submit"
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