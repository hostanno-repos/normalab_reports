<?php

//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('korisnici', 'pregled')) {
        header('Location: index.php');
        exit;
    }

    //VARIABLES
    $itemToSelect = "korisnika";
    $itemToEdit = "korisnik";

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    //GET ITEMS
    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

    if (ima_permisiju('korisnici', 'pregled')) {
        $columns = 'korisnici.*, korisnickeuloge.korisnickeuloge_naziv';
        $joins = [
            ['type' => 'LEFT', 'table' => 'korisnickeuloge', 'on' => 'korisnici.korisnici_korisnickaulogaid = korisnickeuloge.korisnickeuloge_id']
        ];
        $objects = new allObjectsWithPagination;
        $objects = $objects->fetch_all_objects_with_pagination(
            'korisnici',
            'korisnici_id',
            'ASC',
            $perPage,
            $joins,
            NULL,
            [],
            $columns
        );
        $korisnici = $objects[0];
        $total_pages = (int) $objects[1];
        $total_results = isset($objects[2]) ? (int) $objects[2] : 0;
    } else {
        $korisniciRaw = new allObjectsBy;
        $korisniciRaw = $korisniciRaw->fetch_all_objects_by("korisnici", "korisnici_username", $_SESSION['user'], "korisnici_id", "ASC");
        $korisnici = array_map(function ($r) { return (object) $r; }, $korisniciRaw);
        foreach ($korisnici as $k) {
            $uloga = (new singleObject)->fetch_single_object("korisnickeuloge", "korisnickeuloge_id", $k->korisnici_korisnickaulogaid);
            $k->korisnickeuloge_naziv = $uloga['korisnickeuloge_naziv'] ?? '';
        }
        $total_results = count($korisnici);
        $total_pages = $total_results > 0 ? 1 : 0;
    }
    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <div class="d-flex justify-content-between mb-2">
                <h1>Korisnici</h1>
                <div>
                    <?php if (ima_permisiju('korisnici', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi korisnika"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi korisnika</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('korisnici', 'dodavanje')) { ?>
                    <a href="dodajkorisnika.php" id="addItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Dodaj korisnika"><i class="bi-plus-square"
                                style="font-size:18px"></i> Dodaj korisnika</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('korisnici', 'brisanje')) { ?>
                    <a onclick="deleteItem()" itemToDelete="" id="deleteItem"><button class="btn btn-dark"
                            data-placement="bottom" title="Ukloni korisnika" data-toggle="modal" data-target=""><i
                                class="bi bi-trash3" style="font-size:18px"></i> Ukloni korisnika</button></a>
                    <?php } ?>
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Korisnici</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12 mb-3 d-flex justify-content-between align-items-center">
                    <?php if ($total_results > 0) {
                        $from = ($currentPage - 1) * $perPage + 1;
                        $to = in_array($_SESSION['user-type'], [1, 7]) ? min($currentPage * $perPage, $total_results) : $total_results;
                    } ?>
                    <?php if ($total_results > 0) { ?><small class="text-muted">Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> korisnika.</small><?php } else { ?><small class="text-muted">Trenutno nema korisnika.</small><?php } ?>
                    <div class="d-flex align-items-center">
                        <label for="per_page_select" class="mr-2 mb-0"><small>Prikaži po stranici:</small></label>
                        <select id="per_page_select" class="form-control form-control-sm" style="width:auto;" onchange="window.location.href='korisnici.php?page=1&per_page='+this.value;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <?php include('includes/pagination.php'); ?>
                </div>
                <?php $showOznaciKorisnici = ima_permisiju('korisnici', 'uredivanje') || ima_permisiju('korisnici', 'brisanje'); ?>
                <div class="col-lg-12">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th scope="col" style="width:100px;">#</th>
                                <?php if ($showOznaciKorisnici) { ?><th scope="col" class="text-center" style="width:150px;">Označi</th><?php } ?>
                                <th scope="col" class="text-center">Ime</th>
                                <th scope="col" class="text-center">Prezime</th>
                                <th scope="col" class="text-center">Telefon</th>
                                <th scope="col" class="text-center">E-mail</th>
                                <th scope="col" class="text-center">Korisničko ime</th>
                                <th scope="col" class="text-center">Korisnička uloga</th>
                                <?php if (ima_permisiju('korisnici', 'uredivanje') || (int)$_SESSION['user-type'] === 7) { ?>
                                <th scope="col" class="text-center">Lozinka (za klijenta)</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($korisnici as $korisnik) { ?>
                                <tr>
                                    <td scope="row"><?php echo $korisnik->korisnici_id ?></td>
                                    <?php if ($showOznaciKorisnici) { ?>
                                    <td scope="row" class="text-center"><input type="checkbox" class="selectItemButton"
                                            h="korisnika" t="korisnici" o="<?php echo $korisnik->korisnici_id ?>">
                                    </td>
                                    <?php } ?>
                                    <td scope="row" class="text-center">
                                        <?php echo htmlspecialchars($korisnik->korisnici_ime ?? ''); ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo htmlspecialchars($korisnik->korisnici_prezime ?? ''); ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo htmlspecialchars($korisnik->korisnici_telefon ?? ''); ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo htmlspecialchars($korisnik->korisnici_email ?? ''); ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo htmlspecialchars($korisnik->korisnici_username ?? ''); ?>
                                    </td>
                                    <td scope="row" class="text-center">
                                        <?php echo htmlspecialchars($korisnik->korisnickeuloge_naziv ?? ''); ?>
                                    </td>
                                    <?php if (ima_permisiju('korisnici', 'uredivanje') || (int)$_SESSION['user-type'] === 7) { ?>
                                    <td scope="row" class="text-center">
                                        <?php echo htmlspecialchars($korisnik->korisnici_lozinka_prikaz ?? ''); ?>
                                    </td>
                                    <?php } ?>
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