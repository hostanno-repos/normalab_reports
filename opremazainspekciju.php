<?php


//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('opremazainspekciju', 'pregled')) {
        header('Location: index.php');
        exit;
    }

    //VARIABLES
    $itemToSelect = "opremu za inspekciju";
    $itemToEdit = "opremazainspekciju";

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    //GET ITEMS
    //$opremezainspekciju = new allObjectsWithPagination;
    //$opremezainspekciju = $opremezainspekciju->fetch_all_objects_with_pagination("opremazainspekciju", "opremazainspekciju_id", "ASC", 10);
    //$total_pages = $opremezainspekciju[1];
    //$opremezainspekciju = $opremezainspekciju[0];

    /* NEW CODE */
    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

    $columns = '
        opremazainspekciju.*
    ';

    $joins = [];

    $objects = new allObjectsWithPagination;
    $objects = $objects->fetch_all_objects_with_pagination(
        'opremazainspekciju',
        'opremazainspekciju_id',
        'DESC',
        $perPage,
        $joins,
        NULL,
        [],
        $columns
    );

    $opremezainspekciju = $objects[0];
    $total_pages = (int) $objects[1];
    $total_results = isset($objects[2]) ? (int) $objects[2] : 0;
    /* NEW CODE */

    if (isset($_GET['opremazainspekciju']) && isset($_GET["set"]) && ima_permisiju('opremazainspekciju', 'uredivanje')) {
        $query = $pdo->prepare('UPDATE opremazainspekciju SET opremazainspekciju_opremauupotrebi = ' . (int)$_GET["set"] . ' WHERE opremazainspekciju_id = ' . (int)$_GET['opremazainspekciju']);
        $query->execute();
        header('Location: opremazainspekciju.php');
        exit;
    }

    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <div class="d-flex justify-content-between mb-2">
                <h1>Oprema za inspekciju</h1>
                <div>
                    <?php if (ima_permisiju('opremazainspekciju', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi opremu za inspekciju"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi opremu</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('opremazainspekciju', 'dodavanje')) { ?>
                    <a href="dodajopremuzainspekciju.php" id="addItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Dodaj opremu za inspekciju"><i class="bi-plus-square"
                                style="font-size:18px"></i> Dodaj opremu</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('opremazainspekciju', 'brisanje')) { ?>
                    <a onclick="deleteItem()" itemToDelete="" id="deleteItem"><button class="btn btn-dark"
                            data-placement="bottom" title="Ukloni opremu za inspekciju" data-toggle="modal"
                            data-target=""><i class="bi bi-trash3" style="font-size:18px"></i> Ukloni opremu</button></a>
                    <?php } ?>
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Oprema za inspekciju</li>
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
                    <?php if ($total_results > 0) { ?><small class="text-muted">Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> stavki.</small><?php } else { ?><small class="text-muted">Trenutno nema opreme.</small><?php } ?>
                    <div class="d-flex align-items-center">
                        <label for="per_page_select" class="mr-2 mb-0"><small>Prikaži po stranici:</small></label>
                        <select id="per_page_select" class="form-control form-control-sm" style="width:auto;" onchange="window.location.href='opremazainspekciju.php?page=1&per_page='+this.value;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <?php include('includes/pagination.php'); ?>
                </div>
                <?php $showOznaciOprema = ima_permisiju('opremazainspekciju', 'uredivanje') || ima_permisiju('opremazainspekciju', 'brisanje'); ?>
                <div class="col-lg-12">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th scope="col" style="width:100px;">#</th>
                                <?php if ($showOznaciOprema) { ?><th scope="col" class="text-center" style="width:150px;">Označi</th><?php } ?>
                                <th scope="col" class="text-center">Naziv opreme</th>
                                <th scope="col" class="text-center">Proizvođač</th>
                                <th scope="col" class="text-center">Tip</th>
                                <th scope="col" class="text-center">Serijski broj</th>
                                <th scope="col" class="text-center">Trenutno u upotrebi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($opremezainspekciju as $opremazainspekciju) { ?>
                                <tr>
                                    <td scope="row"><?php echo $opremazainspekciju->opremazainspekciju_id ?></td>
                                    <?php if ($showOznaciOprema) { ?>
                                    <td scope="row" class="text-center"><input type="checkbox" class="selectItemButton"
                                            h="opremuzainspekciju" t="opremazainspekciju"
                                            o="<?php echo $opremazainspekciju->opremazainspekciju_id ?>">
                                    </td>
                                    <?php } ?>
                                    <td scope="row" class="text-center">
                                        <?php echo htmlspecialchars($opremazainspekciju->opremazainspekciju_naziv ?? ''); ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo $opremazainspekciju->opremazainspekciju_proizvodjac ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo $opremazainspekciju->opremazainspekciju_tip ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo $opremazainspekciju->opremazainspekciju_serijskibroj ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php if (ima_permisiju('opremazainspekciju', 'uredivanje')) { ?>
                                            <?php if ($opremazainspekciju->opremazainspekciju_opremauupotrebi == 1) { ?>
                                                <a href="opremazainspekciju.php?page=<?php echo $currentPage; ?>&opremazainspekciju=<?php echo $opremazainspekciju->opremazainspekciju_id ?>&set=0">DA</a>
                                            <?php } else { ?>
                                                <a href="opremazainspekciju.php?page=<?php echo $currentPage; ?>&opremazainspekciju=<?php echo $opremazainspekciju->opremazainspekciju_id ?>&set=1">NE</a>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php echo $opremazainspekciju->opremazainspekciju_opremauupotrebi == 1 ? 'DA' : 'NE'; ?>
                                        <?php } ?>
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