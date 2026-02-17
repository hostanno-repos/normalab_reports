<?php

//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('mjernevelicine', 'pregled')) {
        header('Location: index.php');
        exit;
    }

    //VARIABLES
    $itemToSelect = "mjernu veličinu";
    $itemToEdit = "mjernavelicina";

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    //GET ITEMS
    //$mjernevelicine = new allObjectsWithPagination;
    //$mjernevelicine = $mjernevelicine->fetch_all_objects_with_pagination("mjernevelicine", "mjernevelicine_id", "DESC", 10);
    //$total_pages = $mjernevelicine[1];
    //$mjernevelicine = $mjernevelicine[0];

    /* NEW CODE */
    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

    $columns = '
        mjernevelicine.*,
        vrsteuredjaja.*
    ';

    $joins = [
        ['type' => 'LEFT',  'table' => 'vrsteuredjaja', 'on' => 'mjernevelicine.mjernevelicine_vrstauredjajaid = vrsteuredjaja.vrsteuredjaja_id']
    ];

    $objects = new allObjectsWithPagination;
    $objects = $objects->fetch_all_objects_with_pagination(
        'mjernevelicine',
        'mjernevelicine_id',
        'DESC',
        $perPage,
        $joins,
        NULL,
        [],
        $columns
    );

    $mjernevelicine = $objects[0];
    $total_pages = (int) $objects[1];
    $total_results = isset($objects[2]) ? (int) $objects[2] : 0;
    /* NEW CODE */

    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <div class="d-flex justify-content-between mb-2">
                <h1>Mjerne veličine</h1>
                <div>
                    <?php if (ima_permisiju('mjernevelicine', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi mjernu veličinu"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi mjernu veličinu</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('mjernevelicine', 'dodavanje')) { ?>
                    <a href="dodajmjernuvelicinu.php" id="addItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Dodaj mjernu veličinu"><i class="bi-plus-square"
                                style="font-size:18px"></i> Dodaj mjernu veličinu</button></a>
                    <?php } ?>
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Mjerne veličine</li>
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
                    <?php if ($total_results > 0) { ?><small class="text-muted">Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> mjernih veličina.</small><?php } else { ?><small class="text-muted">Trenutno nema mjernih veličina.</small><?php } ?>
                    <div class="d-flex align-items-center">
                        <label for="per_page_select" class="mr-2 mb-0"><small>Prikaži po stranici:</small></label>
                        <select id="per_page_select" class="form-control form-control-sm" style="width:auto;" onchange="window.location.href='mjernevelicine.php?page=1&per_page='+this.value;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <?php include('includes/pagination.php'); ?>
                </div>
                <?php $showOznaciMjerne = ima_permisiju('mjernevelicine', 'uredivanje'); ?>
                <div class="col-lg-12">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th scope="col" style="width:100px;">#</th>
                                <?php if ($showOznaciMjerne) { ?><th scope="col" class="text-center" style="width:150px;">Označi</th><?php } ?>
                                <th scope="col" class="text-center" style="">Vrsta uređaja</th>
                                <th scope="col" class="text-center">Naziv mjerne veličine</th>
                                <th scope="col" class="text-center">Jedinica</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mjernevelicine as $mjernavelicina) { ?>
                                <tr>
                                    <td scope="row"><?php echo $mjernavelicina->mjernevelicine_id ?></td>
                                    <?php if ($showOznaciMjerne) { ?>
                                    <td scope="row" class="text-center"><input type="checkbox" class="selectItemButton" h="mjernuvelicinu" t="mjernevelicine" o="<?php echo $mjernavelicina->mjernevelicine_id ?>">
                                    </td>
                                    <?php } ?>
                                    <td scope="row" class="text-center">
                                        <?php if (isset($mjernavelicina->vrsteuredjaja_opis) && $mjernavelicina->vrsteuredjaja_opis != "") {
                                        echo $mjernavelicina->vrsteuredjaja_naziv . " (" . $mjernavelicina->vrsteuredjaja_opis . ") ";
                                    } else {
                                        echo $mjernavelicina->vrsteuredjaja_naziv;
                                    } ?></td>
                                    <td scope="row" class="text-center"><?php echo $mjernavelicina->mjernevelicine_naziv ?>
                                    </td>
                                    <td scope="row" class="text-center"><?php echo $mjernavelicina->mjernevelicine_jedinica ?>
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