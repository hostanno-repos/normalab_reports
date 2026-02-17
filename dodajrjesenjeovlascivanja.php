<?php
//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('rjesenjazaovlascivanje', 'dodavanje')) {
        header('Location: index.php');
        exit;
    }

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1 class="mb-3">Dodaj rješenje o ovlašćivanju</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item"><a href="rjesenjaovlascivanja.php">Rješenja o ovlašćivanju</a></li>
                    <li class="breadcrumb-item active">Dodaj rješenje</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_); ?>" method="post">
                    <div class="col-lg-4 d-flex flex-column mb-2">
                        <label for="rjesenjazaovlascivanje_broj_rjesenja">Broj rješenja:</label>
                        <input type="text" name="rjesenjazaovlascivanje_broj_rjesenja" required placeholder="npr. 18/1.10/393.10-03-09-25/25">
                    </div>
                    <div class="col-lg-4 d-flex flex-column mb-2">
                        <label for="rjesenjazaovlascivanje_datum_izdavanja">Datum izdavanja:</label>
                        <input type="date" name="rjesenjazaovlascivanje_datum_izdavanja" required>
                    </div>
                    <div class="col-lg-12 d-flex flex-column mt-3">
                        <button name="submit_rjesenjazaovlascivanje" class="btn btn-primary" type="submit" style="width:150px">Sačuvaj</button>
                    </div>
                </form>
            </div>
        </section>

    </main>

    <style>
        .btn.btn-primary { background-color: #00335e; }
    </style>

    <?php

} else {
    header('Location: index.php');
}

include_once ('includes/footer.php');

?>
