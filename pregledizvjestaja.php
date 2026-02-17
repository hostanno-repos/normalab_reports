<?php
//INCLUDES – za AJAX bez HTML/header da odgovor bude čisti JSON
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    include_once __DIR__ . '/includes/ajax_init.php';
} else {
    include_once ('includes/head.php');
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('pregledizvjestaja', 'pregled')) {
        if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'forbidden', 'rows' => '', 'total_results' => 0, 'from' => 0, 'to' => 0, 'total_pages' => 0, 'current_page' => 1, 'pagination' => '']);
            exit;
        }
        header('Location: index.php');
        exit;
    }

    //GET ITEMS (bez header/sidebar dok ne znamo trebamo li JSON za AJAX)
    //$izvjestaji = new allObjectsWithPagination;
    //$izvjestaji = $izvjestaji->fetch_all_objects_with_pagination("izvjestaji", "izvjestaji_id", "DESC", 10);
    //$total_pages = $izvjestaji[1];
    //$izvjestaji = $izvjestaji[0];

    /* NEW CODE */
    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $search_rn = trim((string)($_GET['search_rn'] ?? ''));
    $search_serija = trim((string)($_GET['search_serija'] ?? ''));

    $columns = '
        izvjestaji.*,
        radninalozi.*,
        mjerila.*,
        vrsteuredjaja.*,
        izvrsio.kontrolori_id AS izvrsio_id,
        izvrsio.kontrolori_ime AS izvrsio_ime,
        izvrsio.kontrolori_prezime AS izvrsio_prezime,
        ovjerio.kontrolori_id AS ovjerio_id,
        ovjerio.kontrolori_ime AS ovjerio_ime,
        ovjerio.kontrolori_prezime AS ovjerio_prezime
    ';

    $joins = [
        ['type' => 'LEFT',  'table' => 'radninalozi', 'on' => 'izvjestaji.izvjestaji_radninalogid = radninalozi.radninalozi_id'],
        ['type' => 'LEFT',  'table' => 'mjerila', 'on' => 'izvjestaji.izvjestaji_mjeriloid = mjerila.mjerila_id'],
        ['type' => 'LEFT',  'table' => 'vrsteuredjaja', 'on' => 'mjerila.mjerila_vrstauredjajaid = vrsteuredjaja.vrsteuredjaja_id'],
        ['type' => 'LEFT',  'table' => 'kontrolori AS izvrsio', 'on' => 'izvjestaji.izvjestaji_izvrsioid = izvrsio.kontrolori_id'],
        ['type' => 'LEFT',  'table' => 'kontrolori AS ovjerio', 'on' => 'izvjestaji.izvjestaji_ovjerioid = ovjerio.kontrolori_id']
    ];

    $whereParts = [];
    $paramsIzvjestaji = [];
    if ((int)$_SESSION['user-type'] === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mKlijent)) {
        $whereParts[] = 'mjerila.mjerila_klijentid = ?';
        $paramsIzvjestaji[] = (int)$mKlijent[1];
    }
    if ($search_rn !== '') {
        $whereParts[] = 'radninalozi.radninalozi_broj LIKE ?';
        $paramsIzvjestaji[] = '%' . $search_rn . '%';
    }
    if ($search_serija !== '') {
        $whereParts[] = 'mjerila.mjerila_serijskibroj LIKE ?';
        $paramsIzvjestaji[] = '%' . $search_serija . '%';
    }
    $whereIzvjestaji = !empty($whereParts) ? implode(' AND ', $whereParts) : null;

    $objects = new allObjectsWithPagination;
    $objects = $objects->fetch_all_objects_with_pagination(
        'izvjestaji',
        'izvjestaji_id',
        'DESC',
        $perPage,
        $joins,
        $whereIzvjestaji,
        $paramsIzvjestaji,
        $columns
    );

    $izvjestaji = $objects[0];
    $total_pages = (int) $objects[1];
    $total_results = isset($objects[2]) ? (int) $objects[2] : 0;

    $from = $total_results > 0 ? ($currentPage - 1) * $perPage + 1 : 0;
    $to = $total_results > 0 ? min($currentPage * $perPage, $total_results) : 0;

    $tbody_rows_html = '';
    foreach ($izvjestaji as $izvjestaj) {
        $pdfLink = 'pregledizvjestajapdf.php?izvjestaj=' . (int)$izvjestaj->izvjestaji_id;
        $tbody_rows_html .= '<tr>';
        $tbody_rows_html .= '<td scope="row">' . (isset($izvjestaj->izvjestaji_id) ? (int)$izvjestaj->izvjestaji_id : '') . '</td>';
        $tbody_rows_html .= '<td scope="row" class="text-center"><input type="checkbox" class="selectItemButton" h="izvjestaj" t="izvjestaji" o="' . (int)$izvjestaj->izvjestaji_id . '" m="' . (int)($izvjestaj->radninalozi_vrstauredjajaid ?? 0) . '" i="' . htmlspecialchars($pdfLink) . '"></td>';
        $tbody_rows_html .= '<td scope="row" class="text-center">' . (isset($izvjestaj->radninalozi_broj) ? htmlspecialchars($izvjestaj->radninalozi_broj) : '') . '</td>';
        $tbody_rows_html .= '<td scope="row" class="text-center">' . (isset($izvjestaj->izvjestaji_broj) ? htmlspecialchars($izvjestaj->izvjestaji_broj) : '') . '</td>';
        $tbody_rows_html .= '<td scope="row" class="text-center">' . (isset($izvjestaj->izvjestaji_datumizdavanja) ? htmlspecialchars($izvjestaj->izvjestaji_datumizdavanja) : '') . '</td>';
        $tbody_rows_html .= '<td scope="row" class="text-center">' . htmlspecialchars($izvjestaj->vrsteuredjaja_naziv ?? '');
        if (isset($izvjestaj->mjerila_serijskibroj) && $izvjestaj->mjerila_serijskibroj !== '') {
            $tbody_rows_html .= ' - ' . htmlspecialchars($izvjestaj->mjerila_serijskibroj);
        }
        $tbody_rows_html .= '</td>';
        $tbody_rows_html .= '<td scope="row" class="text-center">' . (isset($izvjestaj->izvjestaji_mjestoinspekcije) ? htmlspecialchars($izvjestaj->izvjestaji_mjestoinspekcije) : '') . '</td>';
        $izvrsio = (isset($izvjestaj->izvrsio_ime) && isset($izvjestaj->izvrsio_prezime)) ? (htmlspecialchars($izvjestaj->izvrsio_ime) . ' ' . htmlspecialchars($izvjestaj->izvrsio_prezime)) : '';
        $tbody_rows_html .= '<td scope="row" class="text-center">' . $izvrsio . '</td>';
        $tbody_rows_html .= '<td scope="row" class="text-center">' . $izvrsio . '</td>';
        $tbody_rows_html .= '</tr>';
    }

    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
        $paginationQueryExtra = '';
        if ($search_rn !== '') { $paginationQueryExtra .= '&search_rn=' . rawurlencode($search_rn); }
        if ($search_serija !== '') { $paginationQueryExtra .= '&search_serija=' . rawurlencode($search_serija); }
        ob_start();
        include(__DIR__ . '/includes/pagination.php');
        $paginationHtml = ob_get_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'rows' => $tbody_rows_html,
            'total_results' => $total_results,
            'from' => $from,
            'to' => $to,
            'total_pages' => $total_pages,
            'current_page' => $currentPage,
            'pagination' => $paginationHtml
        ]);
        exit;
    }

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');
    /* NEW CODE */

    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <div class="d-flex justify-content-between mb-2">
                <h1>Pregled izvještaja</h1>
                <?php
                $userType = (int)($_SESSION['user-type'] ?? 0);
                $isZavod = ($userType === 6);
                $showPdfIzvjestaj = ima_permisiju('pregledizvjestaja', 'pregled') && !$isZavod;
                $showPdfIzvjestajZavod = ima_permisiju('pregledizvjestaja', 'pregled');
                ?>
                <div>
                    <?php if (ima_permisiju('pregledizvjestaja', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi izvještaj"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi izvještaj</button></a>
                    <?php } ?>
                    <?php if ($showPdfIzvjestaj) { ?>
                    <a onclick="kreirajOtvoriIzvjestaj()" reporttoshow="" id="openReport"><button class="btn btn-dark"
                            data-toggle="tooltip" data-placement="bottom" title="Pdf izvještaj"><i
                                class="bi bi-file-earmark-pdf" style="font-size:18px"></i> Pdf izvještaj</button></a>
                    <?php } ?>
                    <?php if ($showPdfIzvjestajZavod) { ?>
                    <a href="" id="openReportZavod"><button class="btn btn-dark"
                            data-toggle="tooltip" data-placement="bottom" title="Pdf izvještaj - Zavod"><i
                                class="bi bi-file-earmark-pdf" style="font-size:18px"></i> Pdf izvještaj - Zavod</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledizvjestaja', 'brisanje')) { ?>
                    <a onclick="deleteItem()" itemToDelete="" id="deleteItem"><button class="btn btn-dark"
                            data-placement="bottom" title="Ukloni izvještaj" data-toggle="modal" data-target=""><i
                                class="bi bi-trash3" style="font-size:18px"></i> Ukloni izvještaj</button></a>
                    <?php } ?>
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Pregled izvještaja</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <?php
            $paginationQueryExtra = '';
            if ($search_rn !== '') { $paginationQueryExtra .= '&search_rn=' . rawurlencode($search_rn); }
            if ($search_serija !== '') { $paginationQueryExtra .= '&search_serija=' . rawurlencode($search_serija); }
            ?>
            <div class="row">
                <div class="col-lg-12 mb-3 d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-4">
                            <label for="search_rn" class="form-label mb-0">Broj radnog naloga</label>
                            <input type="text" id="search_rn" class="form-control" placeholder="Pretraži..." value="<?php echo htmlspecialchars($search_rn); ?>" style="width:180px;">
                        </div>
                        <div>
                            <label for="search_serija" class="form-label mb-0">Serijski broj uređaja</label>
                            <input type="text" id="search_serija" class="form-control" placeholder="Pretraži..." value="<?php echo htmlspecialchars($search_serija); ?>" style="width:180px;">
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap">
                        <span id="izvjestaji-count" class="text-muted mr-2"><?php if ($total_results > 0) { ?>Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> izvještaja.<?php } else { ?>Trenutno nema izvještaja.<?php } ?></span>
                        <label for="per_page_select" class="mr-2 mb-0">Prikaži po stranici:</label>
                        <select id="per_page_select" class="form-control" style="width:auto;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3" id="izvjestaji-pagination-top">
                    <?php include('includes/pagination.php'); ?>
                </div>
                <div class="col-lg-12">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text-center">Označi</th>
                                <th scope="col" class="text-center">Broj radnog naloga</th>
                                <th scope="col" class="text-center">Broj izvještaja</th>
                                <th scope="col" class="text-center">Datum inspekcije</th>
                                <th scope="col" class="text-center">Mjerilo</th>
                                <th scope="col" class="text-center">Mjesto inspekcije</th>
                                <th scope="col" class="text-center">Inspekciju izvršio</th>
                                <th scope="col" class="text-center">Izvještaj ovjerio</th>
                            </tr>
                        </thead>
                        <tbody id="izvjestaji-tbody">
                            <?php echo $tbody_rows_html; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-12 mt-3" id="izvjestaji-pagination-bottom">
                    <?php include('includes/pagination.php'); ?>
                </div>
            </div>
        </section>

    </main>

    <style>
        tr:hover {
            background-color: #eee;
        }
    </style>

    <script>
    (function() {
        var searchRn = document.getElementById('search_rn');
        var searchSerija = document.getElementById('search_serija');
        var perPageSelect = document.getElementById('per_page_select');
        var tbody = document.getElementById('izvjestaji-tbody');
        var countEl = document.getElementById('izvjestaji-count');
        var paginationTop = document.getElementById('izvjestaji-pagination-top');
        var paginationBottom = document.getElementById('izvjestaji-pagination-bottom');
        var debounceTimer = null;
        var debounceMs = 400;
        var currentPage = <?php echo (int)$currentPage; ?>;

        function buildQueryString(page, perPage) {
            page = page || 1;
            perPage = perPage || (perPageSelect ? parseInt(perPageSelect.value, 10) : <?php echo (int)$perPage; ?>);
            var params = ['page=' + page, 'per_page=' + perPage, 'ajax=1'];
            if (searchRn && searchRn.value.trim()) params.push('search_rn=' + encodeURIComponent(searchRn.value.trim()));
            if (searchSerija && searchSerija.value.trim()) params.push('search_serija=' + encodeURIComponent(searchSerija.value.trim()));
            return 'pregledizvjestaja.php?' + params.join('&');
        }

        function applySearch() {
            var perPage = perPageSelect ? parseInt(perPageSelect.value, 10) : <?php echo (int)$perPage; ?>;
            var activeEl = document.activeElement;
            fetch(buildQueryString(1, perPage))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.error) { window.location.href = 'index.php'; return; }
                    if (tbody) tbody.innerHTML = data.rows || '';
                    if (countEl) {
                        if (data.total_results > 0) {
                            countEl.textContent = 'Prikaz ' + data.from + '–' + data.to + ' od ' + data.total_results + ' izvještaja.';
                        } else {
                            countEl.textContent = 'Trenutno nema izvještaja.';
                        }
                    }
                    if (paginationTop && data.pagination) paginationTop.innerHTML = data.pagination;
                    if (paginationBottom && data.pagination) paginationBottom.innerHTML = data.pagination;
                    currentPage = data.current_page || 1;
                    if (activeEl && (activeEl === searchRn || activeEl === searchSerija)) activeEl.focus();
                })
                .catch(function() {});
        }

        function onSearchInput() {
            if (debounceTimer) clearTimeout(debounceTimer);
            debounceTimer = setTimeout(applySearch, debounceMs);
        }

        if (searchRn) searchRn.addEventListener('input', onSearchInput);
        if (searchSerija) searchSerija.addEventListener('input', onSearchInput);
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                var activeEl = document.activeElement;
                fetch(buildQueryString(1, parseInt(perPageSelect.value, 10)))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.error) { window.location.href = 'index.php'; return; }
                        if (tbody) tbody.innerHTML = data.rows || '';
                        if (countEl) {
                            if (data.total_results > 0) {
                                countEl.textContent = 'Prikaz ' + data.from + '–' + data.to + ' od ' + data.total_results + ' izvještaja.';
                            } else {
                                countEl.textContent = 'Trenutno nema izvještaja.';
                            }
                        }
                        if (paginationTop && data.pagination) paginationTop.innerHTML = data.pagination;
                        if (paginationBottom && data.pagination) paginationBottom.innerHTML = data.pagination;
                        currentPage = data.current_page || 1;
                        if (activeEl) activeEl.focus();
                    })
                    .catch(function() {});
            });
        }
    })();
    </script>

    <?php

} else {
    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'unauthorized', 'rows' => '', 'total_results' => 0, 'from' => 0, 'to' => 0, 'total_pages' => 0, 'current_page' => 1, 'pagination' => '']);
        exit;
    }
    header('Location: index.php');
}

//INCLUDES
include_once ('includes/footer.php');

?>