<?php
// Minimal bootstrap za AJAX zahtjeve - bez header() i bez HTML ispisa
session_start();
include_once __DIR__ . '/../connection.php';
include_once __DIR__ . '/permisije_check.php';
include_once __DIR__ . '/../class/getObject.php';
include_once __DIR__ . '/../class/getUniversal.php';
