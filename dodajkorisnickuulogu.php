<?php
//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('korisnickeuloge', 'dodavanje')) {
        header('Location: index.php');
        exit;
    }

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');

    //GET ITEMS
    $nivoiHijerarhije = new allObjects;
    $nivoiHijerarhije = $nivoiHijerarhije->fetch_all_objects("nivoihijerarhije", "nivoihijerarhije_id", "ASC");
    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1 class="mb-3">Dodaj korisničku ulogu</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item"><a href="korisnickeuloge.php">Korisničke uloge</a></li>
                    <li class="breadcrumb-item active">Dodaj korisničku ulogu</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_) ?>" method="post">
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="korisnickeuloge_naziv">Naziv:</label>
                        <input type="text" name="korisnickeuloge_naziv" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="korisnickeuloge_nivohijerarhijeid">Nivo hijerarhije:</label>
                        <input type="text" name="korisnickeuloge_nivohijerarhijeid" hidden>
                        <select name="" id="" class="selectElement_" required>
                            <option value=""></option>
                            <?php foreach ($nivoiHijerarhije as $nivoHijerarhije) { ?>
                                <option value="<?php echo $nivoHijerarhije['nivoihijerarhije_id'] ?>">
                                    <?php echo $nivoHijerarhije['nivoihijerarhije_nivo'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-12 d-flex flex-column mt-3">
                        <button name="submit_korisnickeuloge" class="btn btn-primary" type="submit"
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