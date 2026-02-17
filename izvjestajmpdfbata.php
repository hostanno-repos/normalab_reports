<?php

require_once __DIR__ . '/vendor/autoload.php';

function latinicaUCirilicu($tekst) {
    $tekst = $tekst ?? '';
    if ($tekst === '') {
        return '';
    }
    $mapa = [
        'A'=>'А', 'B'=>'Б', 'V'=>'В', 'G'=>'Г', 'D'=>'Д', 'Đ'=>'Ђ', 'E'=>'Е', 'Ž'=>'Ж',
        'Z'=>'З', 'I'=>'И', 'J'=>'Ј', 'K'=>'К', 'L'=>'Л', 'Lj'=>'Љ', 'M'=>'М', 'N'=>'Н',
        'Nj'=>'Њ', 'O'=>'О', 'P'=>'П', 'R'=>'Р', 'S'=>'С', 'T'=>'Т', 'Ć'=>'Ћ', 'U'=>'У',
        'F'=>'Ф', 'H'=>'Х', 'C'=>'Ц', 'Č'=>'Ч', 'Dž'=>'Џ', 'Š'=>'Ш',

        'a'=>'а', 'b'=>'б', 'v'=>'в', 'g'=>'г', 'd'=>'д', 'đ'=>'ђ', 'e'=>'е', 'ž'=>'ж',
        'z'=>'з', 'i'=>'и', 'j'=>'ј', 'k'=>'к', 'l'=>'л', 'lj'=>'љ', 'm'=>'м', 'n'=>'н',
        'nj'=>'њ', 'o'=>'о', 'p'=>'п', 'r'=>'р', 's'=>'с', 't'=>'т', 'ć'=>'ћ', 'u'=>'у',
        'f'=>'ф', 'h'=>'х', 'c'=>'ц', 'č'=>'ч', 'dž'=>'џ', 'š'=>'ш'
    ];

    // Prvo zamijeni digrafe sa većim brojem slova
    $tekst = str_replace(
        ['Dž', 'dž', 'Lj', 'lj', 'Nj', 'nj'],
        ['Ǆ', 'ǆ', 'Ǉ', 'ǉ', 'Ǌ', 'ǌ'], // privremeno zamjeni
        $tekst
    );

    // Sad radi transliteraciju jedno po jedno
    $tekst = strtr($tekst, $mapa);

    // Vrati stvarne ćirilične znakove
    $tekst = str_replace(
        ['Ǆ', 'ǆ', 'Ǉ', 'ǉ', 'Ǌ', 'ǌ'],
        ['Џ', 'џ', 'Љ', 'љ', 'Њ', 'њ'],
        $tekst
    );

    return $tekst;
}

include_once __DIR__ . '/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/includes/permisije_check.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == '' || !ima_permisiju('pregledizvjestaja', 'pregled')) {
    header('Location: index.php');
    exit;
}

$uredjaj = $_GET['uredjaj'] ?? '';
$izvjestaj_id = (int)($_GET['izvjestaj'] ?? 0);

if ((int)($_SESSION['user-type'] ?? 0) === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mKlijent)) {
    $stmtK = $pdo->prepare("SELECT mjerila_klijentid FROM mjerila m INNER JOIN izvjestaji i ON i.izvjestaji_mjeriloid = m.mjerila_id WHERE i.izvjestaji_id = ?");
    $stmtK->execute([$izvjestaj_id]);
    $rowK = $stmtK->fetch(PDO::FETCH_ASSOC);
    if (!$rowK || (int)$rowK['mjerila_klijentid'] !== (int)$mKlijent[1]) {
        header('Location: pregledizvjestaja.php');
        exit;
    }
}

$mpdf = new \Mpdf\Mpdf([
    'margin_left'   => 10,
    'margin_right'  => 10,
    'margin_top'    => 10,
    'margin_bottom' => 10,
    'margin_header' => 10,
    'margin_footer' => 10
]);

// Uključi header
ob_start();
include 'mpdf-includes/headerbata.php';
$headerHtml = ob_get_clean();

// Uključi footer
ob_start();
include 'mpdf-includes/footerbata.php';
$footerHtml = ob_get_clean();

// Uključi intro
ob_start();
include 'mpdf-includes/introbata.php';
$introHtml = ob_get_clean();

// Uključi glavni sadržaj
ob_start();
include 'mpdf-includes/'.$uredjaj.'_bata.php';
$body = ob_get_clean();

// Uključi end
ob_start();
include 'mpdf-includes/endbata.php';
$endHtml = ob_get_clean();

$mpdf = new \Mpdf\Mpdf([
    'margin_top' => 30,
    'margin_bottom' => 30,
]);

$mpdf->WriteHTML($headerHtml);
$mpdf->WriteHTML($footerHtml);
$mpdf->WriteHTML($introHtml);
$mpdf->WriteHTML($body);
$mpdf->WriteHTML($endHtml);

$mpdf->Output('dokument.pdf', \Mpdf\Output\Destination::INLINE);

?>