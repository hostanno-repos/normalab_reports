<?php
//INCLUDES – za AJAX bez HTML/header da odgovor bude čisti JSON
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    include_once __DIR__ . '/includes/ajax_init.php';
} else {
    include_once ('includes/head.php');
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('pregledradnihnaloga', 'pregled')) {
        if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'forbidden', 'rows' => '', 'total_results' => 0, 'from' => 0, 'to' => 0, 'total_pages' => 0, 'current_page' => 1, 'pagination' => '']);
            exit;
        }
        header('Location: index.php');
        exit;
    }

    //VARIABLES
    $itemToSelect = "radni nalog";
    $itemToEdit = "radninalog";

    /* NEW CODE */
    $perPageOptions = array(10, 25, 50, 100);
    $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $search_rn = trim((string)($_GET['search_rn'] ?? ''));
    $search_serija = trim((string)($_GET['search_serija'] ?? ''));

    $columns = '
        radninalozi.*,
        izvjestaji.*,
        klijenti.*,
        vrsteuredjaja.*,
        mjerila.*
    ';

    $joins = [
        ['type' => 'LEFT',  'table' => 'izvjestaji', 'on' => 'radninalozi.radninalozi_id = izvjestaji.izvjestaji_radninalogid'],
        ['type' => 'LEFT',  'table' => 'klijenti', 'on' => 'radninalozi.radninalozi_klijentid = klijenti.klijenti_id'],
        ['type' => 'LEFT',  'table' => 'vrsteuredjaja', 'on' => 'radninalozi.radninalozi_vrstauredjajaid = vrsteuredjaja.vrsteuredjaja_id'],
        ['type' => 'LEFT',  'table' => 'mjerila', 'on' => 'radninalozi.radninalozi_mjeriloid = mjerila.mjerila_id'],
    ];

    $whereParts = [];
    $paramsRadniNalozi = [];
    if ((int)$_SESSION['user-type'] === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mKlijent)) {
        $whereParts[] = 'mjerila.mjerila_klijentid = ?';
        $paramsRadniNalozi[] = (int)$mKlijent[1];
    }
    if ($search_rn !== '') {
        $whereParts[] = 'radninalozi.radninalozi_broj LIKE ?';
        $paramsRadniNalozi[] = '%' . $search_rn . '%';
    }
    if ($search_serija !== '') {
        $whereParts[] = 'mjerila.mjerila_serijskibroj LIKE ?';
        $paramsRadniNalozi[] = '%' . $search_serija . '%';
    }
    $whereRadniNalozi = !empty($whereParts) ? implode(' AND ', $whereParts) : null;

    $objects = new allObjectsWithPagination;
    $objects = $objects->fetch_all_objects_with_pagination(
        'radninalozi',
        'radninalozi.radninalozi_id',
        'DESC',
        $perPage,
        $joins,
        $whereRadniNalozi,
        $paramsRadniNalozi,
        $columns
    );

    $radninalozi = $objects[0];
    $total_pages = (int) $objects[1];
    $total_results = isset($objects[2]) ? (int) $objects[2] : 0;

    $from = $total_results > 0 ? ($currentPage - 1) * $perPage + 1 : 0;
    $to = $total_results > 0 ? min($currentPage * $perPage, $total_results) : 0;

    $tbody_rows_html = '';
    foreach ($radninalozi as $radninalog) {
        $reportLink = ($radninalog->izvjestaji_id == false)
            ? 'dodajizvjestaj.php?radninalog=' . (int)$radninalog->radninalozi_id
            : 'izvjestajmpdf.php?uredjaj=' . (int)$radninalog->vrsteuredjaja_id . '&izvjestaj=' . (int)$radninalog->izvjestaji_id;
        $predmet = htmlspecialchars($radninalog->vrsteuredjaja_naziv ?? '');
        if (isset($radninalog->vrsteuredjaja_opis) && $radninalog->vrsteuredjaja_opis !== '') {
            $predmet .= ' - ' . htmlspecialchars($radninalog->vrsteuredjaja_opis);
        }

        $tbody_rows_html .= '<tr>';
        $tbody_rows_html .= '<td scope="row">' . (int)$radninalog->radninalozi_id . '</td>';
        $tbody_rows_html .= '<td scope="row" class="text-center"><input type="checkbox" class="selectItemButton" h="radninalog" t="radninalozi" o="' . (int)$radninalog->radninalozi_id . '" i="' . htmlspecialchars($reportLink) . '"></td>';
        $tbody_rows_html .= '<td scope="row" class="text-center">' . htmlspecialchars($radninalog->radninalozi_broj ?? '') . '</td>';
        $tbody_rows_html .= '<td scope="row" class="text-center">' . htmlspecialchars($radninalog->klijenti_naziv ?? '') . '</td>';
        $tbody_rows_html .= '<td scope="row" class="text-center">' . htmlspecialchars($radninalog->radninalozi_brojzahtjeva ?? '') . '</td>';
        $tbody_rows_html .= '<td scope="row" class="text-center">' . $predmet . '</td>';
        $tbody_rows_html .= '</tr>';
    }

    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
        $paginationQueryExtra = '';
        if ($search_rn !== '') {
            $paginationQueryExtra .= '&search_rn=' . rawurlencode($search_rn);
        }
        if ($search_serija !== '') {
            $paginationQueryExtra .= '&search_serija=' . rawurlencode($search_serija);
        }
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
            'pagination' => $paginationHtml,
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
                <h1>Pregled radnih naloga</h1>
                <div>
                    <?php if (ima_permisiju('pregledradnihnaloga', 'uredivanje')) { ?>
                    <a onclick="editItem()" itemToEdit="" id="editItem"><button class="btn btn-dark" data-toggle="tooltip"
                            data-placement="bottom" title="Uredi radni nalog"><i class="bi bi-pencil-square"
                                style="font-size:18px"></i> Uredi radni nalog</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledradnihnaloga', 'pregled')) { ?>
                    <a onclick="openPdfRadniNalog()" pdfToOpen="" id="opetPdf"><button class="btn btn-dark"
                            data-toggle="tooltip" data-placement="bottom" title="Preuzmi pdf"><i class="bi-filetype-pdf"
                                style="font-size:18px"></i> Preuzmi pdf</button></a>
                    <a onclick="openPdfRadniNalogBatch()" id="stampajPdfRadniNalozi"><button class="btn btn-dark"
                            data-toggle="tooltip" data-placement="bottom" title="Spojeni PDF za štampu"><i class="bi bi-printer"
                                style="font-size:18px"></i> Štampaj PDF</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledradnihnaloga', 'pregled') && ima_permisiju('pregledizvjestaja', 'dodavanje')) { ?>
                    <a onclick="kreirajOtvoriIzvjestaj()" reportToShow="" id="openReport"><button class="btn btn-dark"
                            data-toggle="tooltip" data-placement="bottom" title="Kreiraj/preuzmi izvještaj"><i class="bi-clipboard-data"
                                style="font-size:18px"></i> Kreiraj/preuzmi izvještaj</button></a>
                    <?php } ?>
                    <?php if (ima_permisiju('pregledradnihnaloga', 'brisanje')) { ?>
                    <a onclick="deleteItem()" itemToDelete="" id="deleteItem"><button class="btn btn-dark"
                            data-placement="bottom" title="Ukloni radni nalog" data-toggle="modal" data-target=""><i
                                class="bi bi-trash3" style="font-size:18px"></i> Ukloni radni nalog</button></a>
                    <?php } ?>
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item active">Radni nalozi</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <?php
            $paginationQueryExtra = '';
            if ($search_rn !== '') {
                $paginationQueryExtra .= '&search_rn=' . rawurlencode($search_rn);
            }
            if ($search_serija !== '') {
                $paginationQueryExtra .= '&search_serija=' . rawurlencode($search_serija);
            }
            ?>
            <div class="row">
                <div class="col-lg-12 mb-3 d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-4">
                            <label for="search_rn" class="form-label mb-0">Broj radnog naloga</label>
                            <input type="text" id="search_rn" class="form-control" placeholder="Pretraži..." value="<?php echo htmlspecialchars($search_rn); ?>" style="width:180px;">
                        </div>
                        <div class="mr-4">
                            <label for="search_serija" class="form-label mb-0">Serijski broj uređaja</label>
                            <input type="text" id="search_serija" class="form-control" placeholder="Pretraži..." value="<?php echo htmlspecialchars($search_serija); ?>" style="width:180px;">
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <label for="per_page_select" class="mr-2 mb-0"><small>Prikaži po stranici:</small></label>
                        <select id="per_page_select" class="form-control form-control-sm" style="width:auto;">
                            <?php foreach ($perPageOptions as $opt) { ?>
                                <option value="<?php echo $opt; ?>" <?php echo $perPage == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <small id="radninalozi-count" class="text-muted"><?php if ($total_results > 0) { ?>Prikaz <?php echo $from; ?>–<?php echo $to; ?> od <?php echo $total_results; ?> radnih naloga.<?php } else { ?>Trenutno nema radnih naloga.<?php } ?></small>
                </div>
                <div class="col-lg-12 mb-3" id="radninalozi-pagination-top">
                    <?php include('includes/pagination.php'); ?>
                </div>
                <div class="col-lg-12">
                    <table class="table w-100" data-multi-select="1">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text-center" style="width:150px;">Označi</th>
                                <th scope="col" class="text-center">Broj radnog naloga</th>
                                <th scope="col" class="text-center">Podnosilac zahtjeva</th>
                                <th scope="col" class="text-center">Broj zahtjeva za inspekciju</th>
                                <th scope="col" class="text-center">Predmet inspekcije</th>
                            </tr>
                        </thead>
                        <tbody id="radninalozi-tbody">
                            <?php echo $tbody_rows_html; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-12 mt-3" id="radninalozi-pagination-bottom">
                    <?php include('includes/pagination.php'); ?>
                </div>
            </div>
        </section>

    </main>

    <script>
    function openPdfRadniNalogBatch() {
        var ids = [];
        document.querySelectorAll('.selectItemButton:checked').forEach(function (el) {
            var id = parseInt(el.getAttribute('o'), 10);
            if (id > 0) {
                ids.push(id);
            }
        });
        if (ids.length === 0) {
            alert('Molimo označite barem jedan radni nalog za štampu.');
            return;
        }
        window.open('pregledradnognaloga.php?ids=' + ids.join(','), '_blank');
    }

    (function() {
        var searchRn = document.getElementById('search_rn');
        var searchSerija = document.getElementById('search_serija');
        var perPageSelect = document.getElementById('per_page_select');
        var tbody = document.getElementById('radninalozi-tbody');
        var countEl = document.getElementById('radninalozi-count');
        var paginationTop = document.getElementById('radninalozi-pagination-top');
        var paginationBottom = document.getElementById('radninalozi-pagination-bottom');
        var debounceTimer = null;
        var debounceMs = 400;

        function buildQueryString(page, perPage) {
            page = page || 1;
            perPage = perPage || (perPageSelect ? parseInt(perPageSelect.value, 10) : <?php echo (int)$perPage; ?>);
            var params = ['page=' + page, 'per_page=' + perPage, 'ajax=1'];
            if (searchRn && searchRn.value.trim()) {
                params.push('search_rn=' + encodeURIComponent(searchRn.value.trim()));
            }
            if (searchSerija && searchSerija.value.trim()) {
                params.push('search_serija=' + encodeURIComponent(searchSerija.value.trim()));
            }
            return 'pregledradnihnaloga.php?' + params.join('&');
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
                            countEl.textContent = 'Prikaz ' + data.from + '–' + data.to + ' od ' + data.total_results + ' radnih naloga.';
                        } else {
                            countEl.textContent = 'Trenutno nema radnih naloga.';
                        }
                    }
                    if (paginationTop && data.pagination) paginationTop.innerHTML = data.pagination;
                    if (paginationBottom && data.pagination) paginationBottom.innerHTML = data.pagination;
                    if (activeEl && (activeEl === searchRn || activeEl === searchSerija)) activeEl.focus();
                })
                .catch(function() {
                    var params = ['page=1', 'per_page=' + (perPageSelect ? parseInt(perPageSelect.value, 10) : <?php echo (int)$perPage; ?>)];
                    if (searchRn && searchRn.value.trim()) {
                        params.push('search_rn=' + encodeURIComponent(searchRn.value.trim()));
                    }
                    if (searchSerija && searchSerija.value.trim()) {
                        params.push('search_serija=' + encodeURIComponent(searchSerija.value.trim()));
                    }
                    window.location.href = 'pregledradnihnaloga.php?' + params.join('&');
                });
        }

        function onSearchInput() {
            if (debounceTimer) clearTimeout(debounceTimer);
            debounceTimer = setTimeout(applySearch, debounceMs);
        }

        if (searchRn) searchRn.addEventListener('input', onSearchInput);
        if (searchSerija) searchSerija.addEventListener('input', onSearchInput);
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                applySearch();
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

if (!(isset($_GET['ajax']) && $_GET['ajax'] === '1')) {
    include_once ('includes/footer.php');
}

?>
