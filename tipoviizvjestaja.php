<?php


//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('tipoviizvjestaja', 'pregled')) {
        header('Location: index.php');
        exit;
    }

    //VARIABLES
    $itemToSelect = "tip izvještaja";
    $itemToEdit = "tipizvjestaja";

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    //GET ITEMS
    //$tipoviizvjestaja = new allObjectsWithPagination;
    //$tipoviizvjestaja = $tipoviizvjestaja->fetch_all_objects_with_pagination("tipoviizvjestaja", "tipoviizvjestaja_id", "ASC", 10);
    //$total_pages = $tipoviizvjestaja[1];
    //$tipoviizvjestaja = $tipoviizvjestaja[0];

    /* NEW CODE */
    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

    $columns = '
        tipoviizvjestaja.*,
        vrsteuredjaja.*
    ';

    $joins = [
        ['type' => 'LEFT',  'table' => 'vrsteuredjaja', 'on' => 'tipoviizvjestaja.tipoviizvjestaja_vrstauredjajaid = vrsteuredjaja.vrsteuredjaja_id']
    ];

    $objects = new allObjectsWithPagination;
    $objects = $objects->fetch_all_objects_with_pagination(
        'tipoviizvjestaja',
        'tipoviizvjestaja_id',
        'DESC',
        $perPage,
        $joins,
        NULL,
        [],
        $columns
    );

    $tipoviizvjestaja = $objects[0];
    $total_pages = (int) $objects[1];
    $total_results = isset($objects[2]) ? (int) $objects[2] : 0;
    /* NEW CODE */

    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <div class="d-flex justify-content-between mb-2">
                <h1>Tipovi izvještaja</h1>
                <div>
                    <?php if (ima_permisiju('tipoviizvjestaja', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi tip izvještaja"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi tip izvještaja</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('tipoviizvjestaja', 'dodavanje')) { ?>
                    <a href="dodajtipizvjestaja.php" id="addItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Dodaj tip izvještaja"><i class="bi-plus-square"
                                style="font-size:18px"></i> Dodaj tip izvještaja</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('tipoviizvjestaja', 'brisanje')) { ?>
                    <a onclick="deleteItem()" itemToDelete="" id="deleteItem"><button class="btn btn-dark"
                            data-placement="bottom" title="Ukloni tip izvještaja" data-toggle="modal" data-target=""><i
                                class="bi bi-trash3" style="font-size:18px"></i> Ukloni tip izvještaja</button></a>
                    <?php } ?>
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Tipovi izvještaja</li>
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
                    <?php if ($total_results > 0) { ?><small class="text-muted">Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> tipova izvještaja.</small><?php } else { ?><small class="text-muted">Trenutno nema tipova izvještaja.</small><?php } ?>
                    <div class="d-flex align-items-center">
                        <label for="per_page_select" class="mr-2 mb-0"><small>Prikaži po stranici:</small></label>
                        <select id="per_page_select" class="form-control form-control-sm" style="width:auto;" onchange="window.location.href='tipoviizvjestaja.php?page=1&per_page='+this.value;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <?php include('includes/pagination.php'); ?>
                </div>
                <?php $showOznaciTipovi = ima_permisiju('tipoviizvjestaja', 'uredivanje') || ima_permisiju('tipoviizvjestaja', 'brisanje'); ?>
                <div class="col-lg-12">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th scope="col" style="width:100px;">#</th>
                                <?php if ($showOznaciTipovi) { ?><th scope="col" class="text-center" style="width:150px;">Označi</th><?php } ?>
                                <th scope="col" class="text-center">Naziv</th>
                                <th scope="col" class="text-center">Vrsta uređaja</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tipoviizvjestaja as $tipizvjestaja) { ?>
                                <tr>
                                    <td scope="row"><?php echo $tipizvjestaja->tipoviizvjestaja_id ?></td>
                                    <?php if ($showOznaciTipovi) { ?>
                                    <td scope="row" class="text-center"><input type="checkbox" class="selectItemButton"
                                            h="tipizvjestaja" t="tipoviizvjestaja"
                                            o="<?php echo $tipizvjestaja->tipoviizvjestaja_id ?>">
                                    </td>
                                    <?php } ?>
                                    <td scope="row" class="text-center">
                                        <?php echo $tipizvjestaja->tipoviizvjestaja_naziv ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo $tipizvjestaja->vrsteuredjaja_naziv ?>
                                    </td>
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