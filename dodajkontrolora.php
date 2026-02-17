<?php
//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('kontrolori', 'dodavanje')) {
        header('Location: index.php');
        exit;
    }

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1 class="mb-3">Dodaj kontrolora</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item"><a href="kontrolori.php">Pregled kontrolora</a></li>
                    <li class="breadcrumb-item active">Dodaj kontrolora</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_) ?>" method="post">
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="kontrolori_ime">Ime:</label>
                        <input type="text" name="kontrolori_ime" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="kontrolori_prezime">Prezime:</label>
                        <input type="text" name="kontrolori_prezime" required>
                    </div>
                    <div class="col-lg-12 d-flex flex-column mt-3">
                        <button name="submit_kontrolori" class="btn btn-primary" type="submit"
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