<?php

include_once ('../../connection.php');
include_once ('../../class/zahtjevi.php');

if (isset($_GET['id'])) {
    $zahtjev = new zahtjevSingle;
    $zahtjev = $zahtjev->fetch_zahtjev_single($_GET["id"]);

    $zahtjevi_imeiprezime = $zahtjev['zahtjevi_imeiprezime'];
    $zahtjevi_email = $zahtjev['zahtjevi_email'];
    $zahtjevi_telefon = $zahtjev['zahtjevi_telefon'];
    $zahtjevi_predmet = $zahtjev['zahtjevi_predmet'];
    $zahtjevi_poruka = $zahtjev['zahtjevi_poruka'];
    $zahtjevi_uslugatip = $zahtjev['zahtjevi_uslugatip'];
    $zahtjevi_uslugaselect = $zahtjev['zahtjevi_uslugaselect'];
    $zahtjevi_uslugaselect = explode(", ", $zahtjevi_uslugaselect);
    $zahtjevi_timestamp = $zahtjev['zahtjevi_timestamp'];
    $naziv = $zahtjev['zahtjevi_timestamp'];
    $naziv = str_replace(' ', "-", $naziv);
    $naziv = str_replace(':', "-", $naziv);
    $naziv = str_replace('-', "", $naziv);
    $naziv = substr($naziv, 6, 2) . "-" . substr($naziv, 4, 2) . "-" . substr($naziv, 0, 4) . "-" . substr($naziv, 8, 2) . "-" . substr($naziv, 10, 2) . "-" . substr($naziv, 12, 2);
}

require ('tfpdf.php');

$pdf = new tFPDF();


class PDF extends tFPDF
{
    // Page header
    function Header()
    {
        // Logo
        $this->Image('../img/logoBez.png', 20, 10, 50);
        // Font
        $this->AddFont('DejaVu', '', 'BookAntiquaItalicc.ttf', true);
        $this->SetFont('DejaVu', '', 10);
        // Indent
        $this->Cell(35);
        // Data
        $this->Cell(155, 0, 'Norma Lab d.o.o., Banja Luka', 0, 0, 'R');
        $this->Ln(0);
        $this->Cell(35);
        $this->Cell(155, 10, 'Srpska 99, II sprat, 8b', 0, 0, 'R');
        $this->Ln(0);
        $this->Cell(35);
        $this->Cell(155, 20, 'office@normalab.ba', 0, 0, 'R');
        $this->Ln(0);
        $this->Cell(35);
        $this->Cell(155, 30, '+387 65 06 95 46', 0, 0, 'R');
        $this->Ln(30);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-20);
        // Arial italic 8
        //$this->AddFont('DejaVu','','BookAntiquaItalicc.ttf',true);
        $this->SetFont('DejaVu', '', 10);
        // Page number
        $this->Cell(0, 10, 'ŽR: 5551000057550456 Nova banka a.d. Banja Luka', 0, 0, 'R');
    }
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('DejaVu', '', 10);
$pdf->Cell(10);
$pdf->Cell(40, 10, 'Broj: _____________________');
$pdf->Ln(7);
$pdf->Cell(10);
$pdf->Cell(40, 10, 'Datum: ' . date('d.m.Y.', strtotime($zahtjevi_timestamp)));
$pdf->Ln(7);
$pdf->Cell(10);
$pdf->Cell(40, 10, 'Vrijeme: ' . explode(" ", $zahtjevi_timestamp)[1]);
$pdf->Ln(20);
$pdf->Cell(10);
$pdf->Cell(50, 10, 'Klijent: ');
$pdf->Cell(40, 10, $zahtjevi_imeiprezime);
$pdf->Ln(20);
$pdf->Cell(10);
$pdf->Cell(50, 10, 'Predmet zahtjeva: ');
$pdf->Cell(40, 10, $zahtjevi_uslugatip);

foreach ($zahtjevi_uslugaselect as $zahtjevi_uslugaselect) {
    $pdf->Ln(7);
    $pdf->Cell(10);
    $pdf->Cell(50, 10, '');
    $pdf->Cell(40, 10, $zahtjevi_uslugaselect);
}

$pdf->Ln(20);
$pdf->Cell(10);
$pdf->Cell(50, 10, 'Preispitivanje se vrši putem: ');
$pdf->Cell(40, 10, $zahtjevi_email);
$pdf->Ln(20);
$pdf->Cell(10);
$pdf->Cell(50, 10, 'Zahtjev primljen: ');
$pdf->Cell(40, 10, 'Putem e-maila');
$pdf->Output('D', $naziv . ".pdf");

?>