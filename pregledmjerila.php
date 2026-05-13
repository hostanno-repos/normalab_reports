<?php


//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('pregledmjerila', 'pregled')) {
        header('Location: index.php');
        exit;
    }

    //VARIABLES
    $itemToSelect = "mjerilo";
    $itemToEdit = "mjerilo";

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');

    /* Klijent (uloga 5) vidi samo mjerila koja su njegova (vlasnik = taj klijent) */
    $whereMjerila = null;
    $paramsMjerila = [];
    if ((int)$_SESSION['user-type'] === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $m)) {
        $whereMjerila = 'mjerila.mjerila_klijentid = ?';
        $paramsMjerila = [(int)$m[1]];
    }

    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

    $columns = '
        mjerila.*,
        vrsteuredjaja.*,
        klijenti.*
    ';

    $joins = [
        ['type' => 'LEFT',  'table' => 'vrsteuredjaja', 'on' => 'mjerila.mjerila_vrstauredjajaid = vrsteuredjaja.vrsteuredjaja_id'],
        ['type' => 'LEFT',  'table' => 'klijenti', 'on' => 'mjerila.mjerila_klijentid = klijenti.klijenti_id'],
    ];

    $objects = new allObjectsWithPagination;
    $objects = $objects->fetch_all_objects_with_pagination(
        'mjerila',
        'mjerila_id',
        'DESC',
        $perPage,
        $joins,
        $whereMjerila,
        $paramsMjerila,
        $columns
    );

    $mjerila = $objects[0];
    $total_pages = (int) $objects[1];
    $total_results = isset($objects[2]) ? (int) $objects[2] : 0;

    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <div class="d-flex justify-content-between mb-2">
                <h1>Pregled mjerila</h1>
                <div>
                    <?php if (ima_permisiju('pregledmjerila', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi mjerilo"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi mjerilo</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledmjerila', 'dodavanje')) { ?>
                    <a href="dodajmjerilo.php" id="addItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Dodaj mjerilo"><i class="bi-plus-square"
                                style="font-size:18px"></i> Dodaj mjerilo</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledradnihnaloga', 'dodavanje')) { ?>
                    <a onclick="addRadniNalog()" mjeriloToProvide="" id="addRadniNalog"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Dodaj radni nalog"><i class="bi bi-clipboard-plus"
                                style="font-size:18px"></i> Dodaj radni nalog</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledmjerila', 'brisanje')) { ?>
                    <a onclick="deleteItem()" itemToDelete="" id="deleteItem"><button class="btn btn-dark"
                            data-placement="bottom" title="Ukloni mjerilo" data-toggle="modal" data-target=""><i
                                class="bi bi-trash3" style="font-size:18px"></i> Ukloni mjerilo</button></a>
                    <?php } ?>
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Pregled mjerila</li>
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
                    <?php if ($total_results > 0) { ?><small class="text-muted">Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> mjerila.</small><?php } else { ?><small class="text-muted">Trenutno nema mjerila.</small><?php } ?>
                    <div class="d-flex align-items-center">
                        <label for="per_page_select" class="mr-2 mb-0"><small>Prikaži po stranici:</small></label>
                        <select id="per_page_select" class="form-control form-control-sm" style="width:auto;" onchange="window.location.href='pregledmjerila.php?page=1&per_page='+this.value;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <?php include('includes/pagination.php'); ?>
                </div>
                <?php $showOznaciMjerila = ima_permisiju('pregledmjerila', 'uredivanje') || ima_permisiju('pregledmjerila', 'brisanje') || ima_permisiju('pregledradnihnaloga', 'dodavanje'); ?>
                <div class="col-lg-12">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th scope="col" style="width:100px;">#</th>
                                <?php if ($showOznaciMjerila) { ?><th scope="col" class="text-center" style="width:150px;">Označi</th><?php } ?>
                                <th scope="col" class="text-center">Vrsta mjerila</th>
                                <th scope="col" class="text-center">Vlasnik mjerila</th>
                                <th scope="col" class="text-center">Zadovoljava</th>
                                <th scope="col" class="text-center">Proizvođač</th>
                                <th scope="col" class="text-center">Tip</th>
                                <th scope="col" class="text-center">Serijski broj mjerila</th>
                                <th scope="col" class="text-center">Godina proizvodnje</th>
                                <th scope="col" class="text-center">Službena oznaka</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mjerila as $mjerilo) { ?>
                                <tr>
                                    <td scope="row"><?php echo $mjerilo->mjerila_id ?></td>
                                    <?php if ($showOznaciMjerila) { ?>
                                    <td scope="row" class="text-center"><input type="checkbox" class="selectItemButton"
                                            h="mjerilo" t="mjerila" o="<?php echo $mjerilo->mjerila_id ?>">
                                    </td>
                                    <?php } ?>
                                    <td scope="row" class="text-center"><?php
                                    echo $mjerilo->vrsteuredjaja_naziv; if(isset($mjerilo->vrsteuredjaja_opis) && $mjerilo->vrsteuredjaja_opis != ""){ echo " - ".$mjerilo->vrsteuredjaja_opis;} ?></td>
                                    <td scope="row" class="text-center"><?php
                                    echo $mjerilo->klijenti_naziv; ?></td>
                                    <td scope="row" class="text-center"><?php echo $mjerilo->mjerila_zadovoljava ?></td>
                                    <td scope="row" class="text-center"><?php echo $mjerilo->mjerila_proizvodjac ?></td>
                                    <td scope="row" class="text-center"><?php echo $mjerilo->mjerila_tip ?></td>
                                    <td scope="row" class="text-center"><?php echo $mjerilo->mjerila_serijskibroj ?></td>
                                    <td scope="row" class="text-center"><?php echo $mjerilo->mjerila_godinaproizvodnje ?></td>
                                    <td scope="row" class="text-center"><?php echo $mjerilo->mjerila_sluzbenaoznaka ?></td>
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