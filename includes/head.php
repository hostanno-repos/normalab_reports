<?php

ob_start();
session_start();

include_once ('./connection.php');
include_once ('./includes/permisije_check.php');

if (!isset($itemToSelect)) {
    $itemToSelect = "";
}
if (!defined('KLIJENTI_PER_PAGE')) {
    define('KLIJENTI_PER_PAGE', 10);
}

header('Content-type: text/html; charset=utf-8');

include_once ('./class/getObject.php');
include_once ('./class/getUniversal.php');

$isPodesavanjaPage = (basename($_SERVER['SCRIPT_FILENAME']) === 'podesavanja.php');
if (!empty($_POST) && !$isPodesavanjaPage) {
    include_once ('./class/insertObject.php');
    include_once ('./class/editObject.php');
}

$path = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$file = explode("?", basename($path))[0];
if (isset($_SERVER['HTTP_REFERER'])) {
    $page_ = explode("/", $_SERVER['HTTP_REFERER']);
}

if (isset($_GET['message']) && $_GET['message'] == true) { ?>
    <script>
        alert("Objekat ne može biti obrisan jer su u sistemu pronađeni povezani objekti. Molimo uklonite povezane objekte pa pokušajte ponovo.");
    </script>
<?php } ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Hostanno s.p.">
    <meta name="author" content="Ljuban Jajčanin">
    <meta name="keyword" content="Hostanno s.p.">
    <meta name="format-detection" content="telephone=no">
    <title>NormaLab d.o.o.</title>
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link href="./css/bootstrap-icons.css" rel="stylesheet">
    <!-- Template Main CSS File -->
    <link href="css/style.css?v=4" rel="stylesheet">
    <!-- COOKIES -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>
    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>