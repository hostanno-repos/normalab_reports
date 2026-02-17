<?php


//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('pregledradnihnaloga', 'pregled')) {
        header('Location: index.php');
        exit;
    }

    //VARIABLES
    $itemToSelect = "radni nalog";
    $itemToEdit = "radninalog";

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    //GET ITEMS
    //$radniNalozi = new allObjectsWithPagination;
    //$radniNalozi = $radniNalozi->fetch_all_objects_with_pagination("radninalozi", "radninalozi_id", "DESC", 10);
    //$total_pages = $radniNalozi[1];
    //$radniNalozi = $radniNalozi[0];

    /* NEW CODE */
    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

    $columns = '
        radninalozi.*,
        izvjestaji.*,
        klijenti.*,
        vrsteuredjaja.*
    ';

    $joins = [
        ['type' => 'LEFT',  'table' => 'izvjestaji', 'on' => 'radninalozi.radninalozi_id = izvjestaji.izvjestaji_radninalogid'],
        ['type' => 'LEFT',  'table' => 'klijenti', 'on' => 'radninalozi.radninalozi_klijentid = klijenti.klijenti_id'],
        ['type' => 'LEFT',  'table' => 'vrsteuredjaja', 'on' => 'radninalozi.radninalozi_vrstauredjajaid = vrsteuredjaja.vrsteuredjaja_id']
    ];

    $whereRadniNalozi = null;
    $paramsRadniNalozi = [];
    if ((int)$_SESSION['user-type'] === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mKlijent)) {
        $joins[] = ['type' => 'INNER', 'table' => 'mjerila', 'on' => 'radninalozi.radninalozi_mjeriloid = mjerila.mjerila_id'];
        $whereRadniNalozi = 'mjerila.mjerila_klijentid = ?';
        $paramsRadniNalozi = [(int)$mKlijent[1]];
    }

    $objects = new allObjectsWithPagination;
    $objects = $objects->fetch_all_objects_with_pagination(
        'radninalozi',
        'radninalozi_id',
        'DESC',
        $perPage,
        $joins,
        $whereRadniNalozi,
        $paramsRadniNalozi,
        $columns
    );

    $radninalozi = $objects[0];
    $total_pages = (int) $objects[1];
    $total_results = isset($objects[2]) ? (int) $objects[2] : 0;

    //var_dump($radninalozi);
    //die();
    /* NEW CODE */


    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <div class="d-flex justify-content-between mb-2">
                <h1>Pregled radnih naloga</h1>
                <div>
                    <?php if (ima_permisiju('pregledradnihnaloga', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi radni nalog"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi radni nalog</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledradnihnaloga', 'pregled')) { ?>
                    <a onclick="openPdfRadniNalog()" pdfToOpen="" id="opetPdf"><button class="btn btn-dark"
                            data-toggle="tooltip" data-placement="bottom" title="Preuzmi pdf"><i class="bi-filetype-pdf"
                                style="font-size:18px"></i> Preuzmi pdf</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledradnihnaloga', 'pregled') && ima_permisiju('pregledizvjestaja', 'dodavanje')) { ?>
                    <a onclick="kreirajOtvoriIzvjestaj()" reportToShow="" id="openReport"><button class="btn btn-dark"
                            data-toggle="tooltip" data-placement="bottom" title="Kreiraj/preuzmi izvještaj"><i class="bi-clipboard-data"
                                style="font-size:18px"></i> Kreiraj/preuzmi izvještaj</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledradnihnaloga', 'brisanje')) { ?>
                    <a onclick="deleteItem()" itemToDelete="" id="deleteItem"><button class="btn btn-dark"
                            data-placement="bottom" title="Ukloni radni nalog" data-toggle="modal" data-target=""><i
                                class="bi bi-trash3" style="font-size:18px"></i> Ukloni radni nalog</button></a>
                    <?php } ?>
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Radni nalozi</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12 mb-3 d-flex justify-content-between align-items-center">
                    <?php if ($total_results > 0) {
                        $from = ($currentPage - 1) * $perPage + 1;
                        $to = min($currentPage * $perPage, $total_results);
                    } ?>
                    <?php if ($total_results > 0) { ?><small class="text-muted">Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> radnih naloga.</small><?php } else { ?><small class="text-muted">Trenutno nema radnih naloga.</small><?php } ?>
                    <div class="d-flex align-items-center">
                        <label for="per_page_select" class="mr-2 mb-0"><small>Prikaži po stranici:</small></label>
                        <select id="per_page_select" class="form-control form-control-sm" style="width:auto;" onchange="window.location.href='pregledradnihnaloga.php?page=1&per_page='+this.value;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <?php include('includes/pagination.php'); ?>
                </div>
                <div class="col-lg-12">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text-center" style="width:150px;">Označi</th>
                                <th scope="col" class="text-center">Broj radnog naloga</th>
                                <th scope="col" class="text-center">Podnosilac zahtjeva</th>
                                <th scope="col" class="text-center">Broj zahtjeva za inspekciju</th>
                                <th scope="col" class="text-center">Predmet inspekcije</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($radninalozi as $radninalog) { ?>
                                <tr>
                                    <td scope="row"><?php echo $radninalog->radninalozi_id ?></td>
                                    <td scope="row" class="text-center">
                                        <input type="checkbox" class="selectItemButton" h="radninalog" t="radninalozi"
                                            o="<?php echo $radninalog->radninalozi_id ?>" i="<?php
                                               if ($radninalog->izvjestaji_id == false) {
                                                   echo "dodajizvjestaj.php?radninalog=" . $radninalog->radninalozi_id;
                                               } else {
                                                   echo "izvjestajmpdf.php?uredjaj=".$radninalog->vrsteuredjaja_id."&izvjestaj=" . $radninalog->izvjestaji_id;
                                               }
                                               ?>">
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo $radninalog->radninalozi_broj ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo $radninalog->klijenti_naziv; ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo $radninalog->radninalozi_brojzahtjeva ?>
                                    </td>
                                    <td scope="row" class="text-center"><?php echo $radninalog->vrsteuredjaja_naziv; if(isset($radninalog->vrsteuredjaja_opis) && $radninalog->vrsteuredjaja_opis != ""){ echo " - ".$radninalog->vrsteuredjaja_opis;} ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-12 mt-3">
                    <?php include('includes/pagination.php'); ?>
                </div>
            </div>
        </section>

    </main>

    <?php

} else {
    header('Location: index.php');
}

//INCLUDES
include_once ('includes/footer.php');

?>