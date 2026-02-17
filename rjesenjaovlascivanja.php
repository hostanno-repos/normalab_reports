<?php

//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('rjesenjazaovlascivanje', 'pregled')) {
        header('Location: index.php');
        exit;
    }

    $itemToSelect = "rješenje o ovlašćivanju";
    $itemToEdit = "rjesenjeovlascivanja";

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');

    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

    $columns = 'rjesenjazaovlascivanje.*';
    $joins = [];

    $objects = new allObjectsWithPagination;
    $objects = $objects->fetch_all_objects_with_pagination(
        'rjesenjazaovlascivanje',
        'rjesenjazaovlascivanje_datum_izdavanja',
        'DESC',
        $perPage,
        $joins,
        NULL,
        [],
        $columns
    );

    $rjesenja = $objects[0];
    $total_pages = (int) $objects[1];
    $total_results = isset($objects[2]) ? (int) $objects[2] : 0;

    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <div class="d-flex justify-content-between mb-2">
                <h1>Rješenja o ovlašćivanju</h1>
                <div>
                    <?php if (ima_permisiju('rjesenjazaovlascivanje', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi rješenje"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi rješenje</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('rjesenjazaovlascivanje', 'dodavanje')) { ?>
                    <a href="dodajrjesenjeovlascivanja.php" id="addItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Dodaj rješenje"><i class="bi-plus-square"
                                style="font-size:18px"></i> Dodaj rješenje</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('rjesenjazaovlascivanje', 'brisanje')) { ?>
                    <a onclick="deleteItem()" itemToDelete="" id="deleteItem"><button class="btn btn-dark"
                            data-placement="bottom" title="Ukloni rješenje" data-toggle="modal" data-target=""><i
                                class="bi bi-trash3" style="font-size:18px"></i> Ukloni rješenje</button></a>
                    <?php } ?>
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Rješenja o ovlašćivanju</li>
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
                    <?php if ($total_results > 0) { ?><small class="text-muted">Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> rješenja.</small><?php } else { ?><small class="text-muted">Trenutno nema unosa. Dodajte rješenje da bi se na izvještajima ispisivali broj i datum ovlašćenja.</small><?php } ?>
                    <div class="d-flex align-items-center">
                        <label for="per_page_select" class="mr-2 mb-0"><small>Prikaži po stranici:</small></label>
                        <select id="per_page_select" class="form-control form-control-sm" style="width:auto;" onchange="window.location.href='rjesenjaovlascivanja.php?page=1&per_page='+this.value;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <?php include('includes/pagination.php'); ?>
                </div>
                <?php $showOznaci = ima_permisiju('rjesenjazaovlascivanje', 'uredivanje') || ima_permisiju('rjesenjazaovlascivanje', 'brisanje'); ?>
                <div class="col-lg-12">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th scope="col" style="width:80px;">#</th>
                                <?php if ($showOznaci) { ?><th scope="col" class="text-center" style="width:120px;">Označi</th><?php } ?>
                                <th scope="col" class="text-center">Broj rješenja</th>
                                <th scope="col" class="text-center">Datum izdavanja</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rjesenja as $r) { ?>
                                <tr>
                                    <td scope="row"><?php echo $r->rjesenjazaovlascivanje_id ?></td>
                                    <?php if ($showOznaci) { ?>
                                    <td scope="row" class="text-center"><input type="checkbox" class="selectItemButton" h="rjesenjeovlascivanja" t="rjesenjazaovlascivanje" o="<?php echo $r->rjesenjazaovlascivanje_id ?>"></td>
                                    <?php } ?>
                                    <td scope="row" class="text-center"><?php echo htmlspecialchars($r->rjesenjazaovlascivanje_broj_rjesenja); ?></td>
                                    <td scope="row" class="text-center"><?php echo $r->rjesenjazaovlascivanje_datum_izdavanja ? date('d.m.Y.', strtotime($r->rjesenjazaovlascivanje_datum_izdavanja)) : '-'; ?></td>
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

include_once ('includes/footer.php');

?>
