<?php

//HEAD
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('brojacirn', 'pregled')) {
        header('Location: index.php');
        exit;
    }

    //VARIABLES
    $itemToSelect = "brojač";
    $itemToEdit = "brojac";
    //INCLUDES
    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    //GET ITEMS
    //$brojaci = new allObjectsWithPagination;
    //$brojaci = $brojaci->fetch_all_objects_with_pagination("brojacirn", "brojacirn_id", "ASC", 10);
    //$total_pages = $brojaci[1];
    //$brojaci = $brojaci[0];

    /* NEW CODE */
    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

    $columns = '
        brojacirn.*
    ';

    $joins = [];

    $objects = new allObjectsWithPagination;
    $objects = $objects->fetch_all_objects_with_pagination(
        'brojacirn',
        'brojacirn_id',
        'DESC',
        $perPage,
        $joins,
        NULL,
        [],
        $columns
    );

    $brojaci = $objects[0];
    $total_pages = (int) $objects[1];
    $total_results = isset($objects[2]) ? (int) $objects[2] : 0;
    /* NEW CODE */

    //POVEĆAJ ILI SMANJI BROJAČ
    if (isset($_GET['setbrojac'], $_GET['brojacGodina']) && ima_permisiju('brojacirn', 'uredivanje')) {
        $newValueBrojac = (int)$_GET['setbrojac'];
        $brojacGodina = (int)$_GET['brojacGodina'];
        $sql = $pdo->prepare("UPDATE brojacirn SET brojacirn_brojac = ? WHERE brojacirn_godina = ?");
        $sql->execute([$newValueBrojac, $brojacGodina]);
        header('Location: brojacirn.php');
        exit;
    }

    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <div class="d-flex justify-content-between mb-2">
                <h1>Brojači radnih naloga</h1>
                <div>
                    <?php if (ima_permisiju('brojacirn', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi brojač"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi brojač</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('brojacirn', 'dodavanje')) { ?>
                    <a href="dodajbrojac.php" id="addItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Dodaj brojač"><i class="bi-plus-square"
                                style="font-size:18px"></i> Dodaj brojač</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('brojacirn', 'brisanje')) { ?>
                    <a onclick="deleteItem()" itemToDelete="" id="deleteItem"><button class="btn btn-dark"
                            data-placement="bottom" title="Ukloni brojač" data-toggle="modal" data-target=""><i
                                class="bi bi-trash3" style="font-size:18px"></i> Ukloni brojač</button></a>
                    <?php } ?>
                </div>
            </div>

            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Brojači radnih naloga</li>
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
                    <?php if ($total_results > 0) { ?><small class="text-muted">Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> brojača.</small><?php } else { ?><small class="text-muted">Trenutno nema brojača.</small><?php } ?>
                    <div class="d-flex align-items-center">
                        <label for="per_page_select" class="mr-2 mb-0"><small>Prikaži po stranici:</small></label>
                        <select id="per_page_select" class="form-control form-control-sm" style="width:auto;" onchange="window.location.href='brojacirn.php?page=1&per_page='+this.value;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <?php include('includes/pagination.php'); ?>
                </div>
                <?php $showOznaciBrojac = ima_permisiju('brojacirn', 'uredivanje') || ima_permisiju('brojacirn', 'brisanje'); ?>
                <div class="col-lg-12">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <?php if ($showOznaciBrojac) { ?><th scope="col" class="text-center">Označi</th><?php } ?>
                                <th scope="col" class="text-center">Prefiks brojača</th>
                                <th scope="col" class="text-center">Vrijednost brojača</th>
                                <th scope="col" class="text-center">Godina brojača</th>
                                <th scope="col" class="text-center">Umanji brojač</th>
                                <th scope="col" class="text-center">Povećaj brojač</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($brojaci as $brojac) { ?>
                                <tr>
                                    <td scope="row"><?php echo $brojac->brojacirn_id ?></td>
                                    <?php if ($showOznaciBrojac) { ?>
                                    <td scope="row" class="text-center"><input type="checkbox" class="selectItemButton"
                                            h="brojač" t="brojacirn" o="<?php echo $brojac->brojacirn_id ?>">
                                    </td>
                                    <?php } ?>
                                    <td scope="row" class="text-center"><?php echo htmlspecialchars($brojac->brojacirn_prefiks ?? ''); ?></td>
                                    <td scope="row" class="text-center"><?php echo (int)$brojac->brojacirn_brojac; ?></td>
                                    <td scope="row" class="text-center"><?php echo (int)$brojac->brojacirn_godina; ?></td>
                                    <td scope="row" class="text-center">
                                        <?php if (ima_permisiju('brojacirn', 'uredivanje')) { ?>
                                        <a href="brojacirn.php?setbrojac=<?php echo (int)($brojac->brojacirn_brojac - 1); ?>&brojacGodina=<?php echo (int)$brojac->brojacirn_godina; ?>"><i class="bi bi-dash-square-fill" style="font-size:22px"></i></a>
                                        <?php } else { ?>
                                        <i class="bi bi-dash-square" style="font-size:22px; color:#ccc;"></i>
                                        <?php } ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php if (ima_permisiju('brojacirn', 'uredivanje')) { ?>
                                        <a href="brojacirn.php?setbrojac=<?php echo (int)($brojac->brojacirn_brojac + 1); ?>&brojacGodina=<?php echo (int)$brojac->brojacirn_godina; ?>"><i class="bi bi-plus-square-fill" style="font-size:22px"></i></a>
                                        <?php } else { ?>
                                        <i class="bi bi-plus-square" style="font-size:22px; color:#ccc;"></i>
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