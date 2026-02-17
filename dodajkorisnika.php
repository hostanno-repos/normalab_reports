<?php
//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('korisnici', 'dodavanje')) {
        header('Location: index.php');
        exit;
    }

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');

    //GET ITEMS
    $korisnickeUloge = new allObjects;
    $korisnickeUloge = $korisnickeUloge->fetch_all_objects("korisnickeuloge", "korisnickeuloge_id", "ASC");
    $vrsteKorisnikaZaMeni = array();
    foreach ($korisnickeUloge as $u) {
        if (in_array($u['korisnickeuloge_id'], array(1, 4, 5, 6, 7))) {
            $vrsteKorisnikaZaMeni[] = $u;
        }
    }

    //var_dump($korisnickeUloge);

    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1 class="mb-3">Dodaj korisnika</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item"><a href="korisnici.php">Korisnici</a></li>
                    <li class="breadcrumb-item active">Dodaj korisnika</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_) ?>" method="post">
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="korisnici_ime">Ime:</label>
                        <input type="text" name="korisnici_ime" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="korisnici_prezime">Prezime:</label>
                        <input type="text" name="korisnici_prezime" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="korisnici_telefon">Telefon:</label>
                        <input type="text" name="korisnici_telefon" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="korisnici_email">E-mail:</label>
                        <input type="text" name="korisnici_email" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="korisnici_username">Korisničko ime:</label>
                        <input type="text" name="korisnici_username" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="korisnici_password">Lozinka:</label>
                        <input type="password" name="korisnici_password" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="korisnici_korisnickaulogaid">Korisnička uloga:</label>
                        <input type="text" name="korisnici_korisnickaulogaid" hidden>
                        <select name="" id="" class="selectElement_" required>
                            <option value=""></option>
                            <?php foreach ($vrsteKorisnikaZaMeni as $korisnickaUloga) { ?>
                                <option value="<?php echo $korisnickaUloga['korisnickeuloge_id'] ?>">
                                    <?php echo $korisnickaUloga['korisnickeuloge_naziv'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-12 d-flex flex-column mt-3">
                        <button name="submit_korisnici" class="btn btn-primary" type="submit"
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