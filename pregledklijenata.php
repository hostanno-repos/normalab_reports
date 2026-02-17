<?php


//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('pregledklijenata', 'pregled')) {
        header('Location: index.php');
        exit;
    }

    //VARIABLES
    $itemToSelect = "klijenta";
    $itemToEdit = "klijent";

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    //GET ITEMS
    //$klijenti = new allObjectsWithPagination;
    //$klijenti = $klijenti->fetch_all_objects_with_pagination("klijenti", "klijenti_id", "ASC", 10);
    //$total_pages = $klijenti[1];
    //$klijenti = $klijenti[0];

    /* NEW CODE */

    $columns = 'klijenti.klijenti_id, klijenti.klijenti_naziv, klijenti.klijenti_adresa';
    $joins = [];
    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : KLIJENTI_PER_PAGE;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

    $objects = new allObjectsWithPagination;
    $objects = $objects->fetch_all_objects_with_pagination(
        'klijenti',
        'klijenti_id',
        'DESC',
        $perPage,
        $joins,
        NULL,
        [],
        $columns
    );

    $klijenti = $objects[0];
    $total_pages = (int) $objects[1];
    $total_results = isset($objects[2]) ? (int) $objects[2] : 0;
    
    /* NEW CODE */

    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <div class="d-flex justify-content-between mb-2">
                <h1>Pregled klijenata</h1>
                <div>
                    <?php if (ima_permisiju('pregledklijenata', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi klijenta"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi klijenta</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledklijenata', 'dodavanje')) { ?>
                    <a href="dodajklijenta.php" id="addItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Dodaj klijenta"><i class="bi-plus-square"
                                style="font-size:18px"></i> Dodaj klijenta</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledklijenata', 'brisanje')) { ?>
                    <a onclick="deleteItem()" itemToDelete="" id="deleteItem"><button class="btn btn-dark"
                            data-placement="bottom" title="Ukloni klijenta" data-toggle="modal" data-target=""><i
                                class="bi bi-trash3" style="font-size:18px"></i> Ukloni klijenta</button></a>
                    <?php } ?>
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Pregled klijenata</li>
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
                    <?php if ($total_results > 0) { ?><small class="text-muted">Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> klijenata.</small><?php } else { ?><small class="text-muted">Trenutno nema klijenata.</small><?php } ?>
                    <div class="d-flex align-items-center">
                        <label for="per_page_select" class="mr-2 mb-0"><small>Prikaži po stranici:</small></label>
                        <select id="per_page_select" class="form-control form-control-sm" style="width:auto;" onchange="window.location.href='pregledklijenata.php?page=1&per_page='+this.value;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <?php include('includes/pagination.php') ?>
                </div>
                <div class="col-lg-12">
                    <?php $showOznaciKlijenti = ima_permisiju('pregledklijenata', 'uredivanje') || ima_permisiju('pregledklijenata', 'brisanje'); ?>
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <?php if ($showOznaciKlijenti) { ?><th scope="col" class="text-center">Označi</th><?php } ?>
                                <th scope="col" class="text-center">Naziv</th>
                                <th scope="col" class="text-center">Adresa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($klijenti)) { foreach ($klijenti as $klijent) { ?>
                                <tr>
                                    <td scope="row"><?php echo $klijent->klijenti_id ?></td>
                                    <?php if ($showOznaciKlijenti) { ?>
                                    <th scope="col" class="text-center"><input type="checkbox" class="selectItemButton"
                                            h="klijenta" t="klijenti" o="<?php echo $klijent->klijenti_id ?>">
                                    </th>
                                    <?php } ?>
                                    <td scope="row" class="text-center"><?php echo htmlspecialchars($klijent->klijenti_naziv ?? ''); ?></td>
                                    <td scope="row" class="text-center"><?php echo htmlspecialchars($klijent->klijenti_adresa ?? ''); ?></td>
                                </tr>
                            <?php } } else { ?>
                                <tr>
                                    <td colspan="<?php echo $showOznaciKlijenti ? 4 : 3; ?>" class="text-center">Trenutno nema klijenata.</td>
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