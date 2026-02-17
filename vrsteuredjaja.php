<?php


//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('vrsteuredjaja', 'pregled')) {
        header('Location: index.php');
        exit;
    }

    //VARIABLES
    $itemToSelect = "uređaj";
    $itemToEdit = "uredjaj";

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    //GET ITEMS
    //$vrsteuredjaja = new allObjectsWithPagination;
    //$vrsteuredjaja = $vrsteuredjaja->fetch_all_objects_with_pagination("vrsteuredjaja", "vrsteuredjaja_id", "ASC", 10);
    //$total_pages = $vrsteuredjaja[1];
    //$vrsteuredjaja = $vrsteuredjaja[0];

    /* NEW CODE */
    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

    $columns = '
        vrsteuredjaja.*
    ';

    $joins = [];

    $objects = new allObjectsWithPagination;
    $objects = $objects->fetch_all_objects_with_pagination(
        'vrsteuredjaja',
        'vrsteuredjaja_id',
        'DESC',
        $perPage,
        $joins,
        NULL,
        [],
        $columns
    );

    $vrsteuredjaja = $objects[0];
    $total_pages = (int) $objects[1];
    $total_results = isset($objects[2]) ? (int) $objects[2] : 0;
    /* NEW CODE */

    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <div class="d-flex justify-content-between mb-2">
                <h1>Vrste uređaja</h1>
                <div>
                    <?php if (ima_permisiju('vrsteuredjaja', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi uređaj"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi vrstu uređaja</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('vrsteuredjaja', 'dodavanje')) { ?>
                    <a href="dodajvrstuuredjaja.php" id="addItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Dodaj uređaj"><i class="bi-plus-square"
                                style="font-size:18px"></i> Dodaj vrstu uređaja</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('vrsteuredjaja', 'brisanje')) { ?>
                    <a onclick="deleteItem()" itemToDelete="" id="deleteItem"><button class="btn btn-dark"
                            data-placement="bottom" title="Ukloni vrstu uređaja" data-toggle="modal" data-target=""><i
                                class="bi bi-trash3" style="font-size:22px"></i> Ukloni vrstu uređaja</button></a>
                    <?php } ?>
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Vrste uređaja</li>
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
                    <?php if ($total_results > 0) { ?><small class="text-muted">Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> vrsta uređaja.</small><?php } else { ?><small class="text-muted">Trenutno nema vrsta uređaja.</small><?php } ?>
                    <div class="d-flex align-items-center">
                        <label for="per_page_select" class="mr-2 mb-0"><small>Prikaži po stranici:</small></label>
                        <select id="per_page_select" class="form-control form-control-sm" style="width:auto;" onchange="window.location.href='vrsteuredjaja.php?page=1&per_page='+this.value;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <?php include('includes/pagination.php'); ?>
                </div>
                <?php $showOznaciVrsteUr = ima_permisiju('vrsteuredjaja', 'uredivanje') || ima_permisiju('vrsteuredjaja', 'brisanje'); ?>
                <div class="col-lg-12">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th scope="col" style="width:100px;">#</th>
                                <?php if ($showOznaciVrsteUr) { ?><th scope="col" class="text-center" style="width:150px;">Označi</th><?php } ?>
                                <th scope="col" class="text-center">Naziv uređaja</th>
                                <th scope="col" class="text-center">Opis procedure</th>
                                <th scope="col" class="text-center">Opis uređaja</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vrsteuredjaja as $vrstauredjaja) { ?>
                                <tr>
                                    <td scope="row"><?php echo $vrstauredjaja->vrsteuredjaja_id ?></td>
                                    <?php if ($showOznaciVrsteUr) { ?>
                                    <td scope="row" class="text-center"><input type="checkbox" class="selectItemButton" h="vrstuuređaja" t="vrsteuredjaja" o="<?php echo $vrstauredjaja->vrsteuredjaja_id ?>">
                                    </td>
                                    <?php } ?>
                                    <td scope="row" class="text-center"><?php echo $vrstauredjaja->vrsteuredjaja_naziv ?></td>
                                    <td scope="row" class="truncate" data-max-length="150">
                                        <?php echo $vrstauredjaja->vrsteuredjaja_opisprocedure ?>
                                    </td>
                                    <td scope="row" class="">
                                        <?php echo $vrstauredjaja->vrsteuredjaja_opis ?>
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