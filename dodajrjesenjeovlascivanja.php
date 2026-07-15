<?php
//INCLUDES
include_once ('includes/head.php');
require_once __DIR__ . '/includes/rjesenje_zakljucak_helper.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('rjesenjazaovlascivanje', 'dodavanje')) {
        header('Location: index.php');
        exit;
    }

    $defaultTekst = norma_rjesenje_default_tekst_zakljucka(
        '{{BROJRJESENJA}}',
        '{{DATUMRJESENJA}}'
    );

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
                <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_); ?>" method="post" id="form-rjesenje-ovlascivanje">
                    <div class="col-lg-4 d-flex flex-column mb-2">
                        <label for="rjesenjazaovlascivanje_broj_rjesenja">Broj rješenja:</label>
                        <input type="text" id="rjesenjazaovlascivanje_broj_rjesenja" name="rjesenjazaovlascivanje_broj_rjesenja" required placeholder="npr. 18/1.10/393.10-03-09-25/25" value="">
                    </div>
                    <div class="col-lg-4 d-flex flex-column mb-2">
                        <label for="rjesenjazaovlascivanje_datum_izdavanja">Datum izdavanja:</label>
                        <input type="date" id="rjesenjazaovlascivanje_datum_izdavanja" name="rjesenjazaovlascivanje_datum_izdavanja" required value="">
                    </div>
                    <div class="col-lg-12 d-flex flex-column mb-2">
                        <label for="rjesenjazaovlascivanje_tekst_zakljucka">Tekst zaključka:</label>
                        <textarea id="rjesenjazaovlascivanje_tekst_zakljucka" name="rjesenjazaovlascivanje_tekst_zakljucka" rows="6"><?php echo htmlspecialchars($defaultTekst, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <small class="text-muted">Placeholders: <code>{{BROJRJESENJA}}</code>, <code>{{DATUMRJESENJA}}</code> i <code>{{NOVIZIG}}</code> — zamjenjuju se na PDF-u.</small>
                    </div>
                    <div class="col-lg-12 d-flex flex-column mb-2">
                        <label for="rjesenjazaovlascivanje_tekst_zakljucka_vage">Tekst zaključka - vage:</label>
                        <textarea id="rjesenjazaovlascivanje_tekst_zakljucka_vage" name="rjesenjazaovlascivanje_tekst_zakljucka_vage" rows="6"><?php echo htmlspecialchars($defaultTekst, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <small class="text-muted">Za vrstu uređaja vaga (npr. neautomatska vaga, ID 52). Ako ostane prazno pri snimanju, kopira se iz „Tekst zaključka”.</small>
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
