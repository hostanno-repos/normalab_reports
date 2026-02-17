<?php

//HEAD
include_once ('connection.php');
include_once ('class/getObject.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once ('includes/permisije_check.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == '' || !ima_permisiju('pregledizvjestaja', 'pregled')) {
    header('Location: index.php');
    exit;
}

require ('fpdf/tfpdf.php');

//kupimo izvjestaj
$izvjestaj = new singleObject;
$izvjestaj = $izvjestaj->fetch_single_object("izvjestaji", "izvjestaji_id", $_GET['izvjestaj'] ?? 0);

if (!$izvjestaj) {
    header('Location: pregledizvjestaja.php');
    exit;
}

if ((int)($_SESSION['user-type'] ?? 0) === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mKlijent)) {
    $stmtK = $pdo->prepare("SELECT mjerila_klijentid FROM mjerila WHERE mjerila_id = ?");
    $stmtK->execute([(int)($izvjestaj['izvjestaji_mjeriloid'] ?? 0)]);
    $rowK = $stmtK->fetch(PDO::FETCH_ASSOC);
    if (!$rowK || (int)$rowK['mjerila_klijentid'] !== (int)$mKlijent[1]) {
        header('Location: pregledizvjestaja.php');
        exit;
    }
}

//generišemo sve varijable iz izvjestaja
foreach ($izvjestaj as $key => $value) {
    if (gettype($key) != "integer") {
        $$key = $value;
        //var_dump($key . "=" . $$key);
    }
}
//generišemo naziv fajla
$naziv = $izvjestaj['izvjestaji_timestamp'];
$naziv = str_replace(' ', "-", $naziv);
$naziv = str_replace(':', "-", $naziv);
$naziv = str_replace('-', "", $naziv);
$naziv = substr($naziv, 6, 2) . "-" . substr($naziv, 4, 2) . "-" . substr($naziv, 0, 4) . "-" . substr($naziv, 8, 2) . "-" . substr($naziv, 10, 2) . "-" . substr($naziv, 12, 2);
//var_dump($naziv);

$pdf = new tFPDF();

class PDF extends tFPDF
{
    // Page header
    function Header()
    {
        //kupimo izvjestaj
        $izvjestaj = new singleObject;
        $izvjestaj = $izvjestaj->fetch_single_object("izvjestaji", "izvjestaji_id", $_GET['izvjestaj']);
        //kupimo tipizvjestaja
        $tipizvjestaja = new singleObject;
        $tipizvjestaja = $tipizvjestaja->fetch_single_object("tipoviizvjestaja", "tipoviizvjestaja_id", $izvjestaj['izvjestaji_tipizvjestajaid']);
        //kupimo vrstu uređaja sa tipa izvještaja
        $vrstauredjaja = new singleObject;
        $vrstauredjaja = $vrstauredjaja->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $tipizvjestaja['tipoviizvjestaja_vrstauredjajaid']);
        //generišemo varijable iz brojača
        foreach ($tipizvjestaja as $key => $value) {
            if (gettype($key) != "integer") {
                $$key = $value;
                //var_dump($key . "=" . $$key);
            }
        }

        // Logo
        $this->Image('images/logoBez.png', 20, 10, 40);
        // Font
        $this->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', true);
        $this->AddFont('Calibri-Bold', '', 'calibri-bold.ttf', true);
        $this->SetFont('Calibri-Bold', '', 10);
        // Indent
        $this->Cell(45);
        // Data
        $this->Cell(100, 5, $tipoviizvjestaja_naziv, 0, 0, 'C');
        $this->Ln(0);
        $this->Cell(45);
        $this->SetFont('Calibri-Regular', '', 10);
        $this->Cell(100, 15, $vrstauredjaja['vrsteuredjaja_naziv'], 0, 0, 'C');
		$this->Ln(0);
        $this->Cell(45);
        //$this->Line(15, 30, 210 - 15, 30);
        //$this->Line(15, 30.3, 210 - 15, 30.3);
        //$this->Line(15, 30.6, 210 - 15, 30.6);
        //$this->Line(15, 30.9, 210 - 15, 30.9);
        //$this->Line(15, 32, 210 - 15, 32);
        $this->Ln(0);
        $this->Ln(0);
        $this->Cell(35);
        $this->Cell(150, 0, 'Norma Lab d.o.o., Banja Luka', 0, 0, 'R');
        $this->Ln(0);
        $this->Cell(35);
        $this->Cell(150, 10, 'Srpska 99, II sprat, 8b', 0, 0, 'R');
        $this->Ln(0);
        $this->Cell(35);
        $this->Cell(150, 20, 'office@normalab.ba', 0, 0, 'R');
        $this->Ln(0);
        $this->Cell(35);
        $this->Cell(150, 30, '+387 66 76 67 81', 0, 0, 'R');
        if($this->PageNo() > 1){
            $izvjestajbroj = $izvjestaj['izvjestaji_broj'];
            $izvjestajbroj = explode("/", $izvjestajbroj);
            $izvjestajbroj_ = substr($izvjestajbroj[1], -2);
            $izvjestajbroj = $izvjestajbroj[0] . "/" . $izvjestajbroj_;
            $this->Ln(5);
            $this->Cell(100, 25, "Broj izvještaja: ".$izvjestajbroj, 0, 0, 'L'); 
        }else{
           $this->Ln(10); 
        }
        $this->Ln(10);
        // Na svim stranicama postavljamo Y ispod headera da sadržaj nikad ne prelazi preko "Broj izvještaja" ili loga (35 mm = minimalan razmak)
        $this->SetY(35);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-20);
        // Arial italic 8
        //$this->AddFont('DejaVu','','BookAntiquaItalicc.ttf',true);
        $this->SetFont('Calibri-Regular', '', 10);
        // Page number
        //$this->Cell(0, 20, 'ŽR: 5551000057550456 Nova banka a.d. Banja Luka ČĆ', 0, 0, 'C');
        $this->Cell(35);
        $this->Image('images/bata-logo.jpg', 95, 275, 20);
    }

    protected $widths;
    protected $aligns;

    function SetWidths($w)
    {
        // Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        // Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data)
    {
        // Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        // Issue a page break first if needed
        $this->CheckPageBreak($h);
        // Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            // Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            // Draw the border
            $this->Rect($x, $y, $w, $h);
            // Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            // Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        // Go to the next line
        $this->Ln($h);
    }

    function Row1($data)
    {
        // Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 7 * $nb;
        // Issue a page break first if needed
        $this->CheckPageBreak($h);
        // Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            // Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            // Draw the border
            //$this->Rect($x, $y, $w, $h);
            // Print the text
            $this->MultiCell($w, 7, $data[$i], 0, $a);
            // Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        // Go to the next line
        $this->Ln($h);
    }

    function Row2($data)
    {
        // Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        if ($nb <= 8) {
            $h = 7 * 8;
        } else {
            $h = 7 * $nb;
        }

        // Issue a page break first if needed
        $this->CheckPageBreak($h);
        // Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            // Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            // Draw the border
            $this->Rect($x, $y, $w, $h);
            // Print the text
            $this->MultiCell($w, 7, $data[$i], 0, $a);
            // Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        // Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        // If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        // Compute the number of lines a MultiCell of width w will take
        if (!isset($this->CurrentFont))
            $this->Error('No font has been set');
        $cw = $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string) $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += 400;
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function calculateSampleStandardDeviation($array) {
        $count = count($array);
        if ($count <= 1) {
            return 0;
        }
    
        // Calculate the mean (average)
        $mean = array_sum($array) / $count;
    
        // Calculate the squared differences from the mean
        $squaredDifferences = array_map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $array);
    
        // Calculate the variance (average of squared differences), divide by n-1 for sample variance
        $variance = array_sum($squaredDifferences) / ($count - 1);
    
        // Return the square root of the variance to get the standard deviation
        return sqrt($variance);
    }
}

//initialize new page
$pdf = new PDF();
// Veća donja margina (30 mm) da tabela nikad ne prelazi preko BATA loga u footeru
$pdf->SetAutoPageBreak(true, 30);
$pdf->AddPage();

//$lineNumber = 0;

//set bold font
$pdf->AddFont('Calibri-Bold', '', 'calibri-bold.ttf', false);
$pdf->SetFont('Calibri-Bold', '', 9);

//CONTENT
$pdf->SetLeftMargin(15);
$pdf->SetTopMargin(10);
$pdf->Cell(180, 0, 'Izvještaj o ispitivanju mjerila', 0, 0, 'C');
$pdf->Ln(5);

//radni nalog
$radninalog = new singleObject;
$radninalog = $radninalog->fetch_single_object("radninalozi", "radninalozi_id", $izvjestaj['izvjestaji_radninalogid']);

//vrsta uređaja
$vrstauredjaja = new singleObject;
$vrstauredjaja = $vrstauredjaja->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $radninalog['radninalozi_vrstauredjajaid']);

if ($vrstauredjaja['vrsteuredjaja_id'] == 13) {

    //ne treba dapiše da je zadjecu i novorođenčad
    $pdf->Cell(180, 0, "Mjerilo krvnog pritiska - aneroidno", 0, 0, 'C');
    $pdf->Ln(5);

} elseif ($vrstauredjaja['vrsteuredjaja_id'] == 14) {

    //ne treba dapiše da je zadjecu i novorođenčad
    $pdf->Cell(180, 0, "Mjerilo krvnog pritiska Hg", 0, 0, 'C');
    $pdf->Ln(5);

} elseif ($vrstauredjaja['vrsteuredjaja_id'] == 49 || $vrstauredjaja['vrsteuredjaja_id'] == 50) {

    //ne treba dapiše da je zadjecu i novorođenčad
    $pdf->Cell(180, 0, "Mjerilo krvnog pritiska - automatsko", 0, 0, 'C');
    $pdf->Ln(5);

} else {

    //za ostale piše tačno kako je uneseno
    $pdf->Cell(180, 0, $vrstauredjaja['vrsteuredjaja_naziv'], 0, 0, 'C');
    $pdf->Ln(5);
    
}

//set regular font
$pdf->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', false);
$pdf->SetFont('Calibri-Regular', '', 9);

//broj radnog naloga
$pdf->Cell(40, 0, "Broj radnog naloga: ", 0, 0, 'L');
$radninalogbroj = $radninalog['radninalozi_broj'];
$radninalogbroj = explode("-", $radninalogbroj);
$radninalogbroj = $radninalogbroj[count($radninalogbroj) - 1];
$radninalogbroj = explode("/", $radninalogbroj);
$radninalogbroj_ = substr($radninalogbroj[1], -2);
$radninalogbroj = $radninalogbroj[0] . "/" . $radninalogbroj_;
$pdf->Cell(140, 0, $radninalogbroj, 0, 0, 'L');
$pdf->Ln(5);

//broj izvještaja
$pdf->Cell(40, 0, "Broj izvještaja: ", 0, 0, 'L');
$izvjestajbroj = $izvjestaj['izvjestaji_broj'];
$izvjestajbroj = explode("/", $izvjestajbroj);
$izvjestajbroj_ = substr($izvjestajbroj[1], -2);
$izvjestajbroj = $izvjestajbroj[0] . "/" . $izvjestajbroj_;
$pdf->Cell(140, 0, $izvjestajbroj, 0, 0, 'L');
$pdf->Ln(5);

//Datum izdavanja
$pdf->Cell(40, 0, "Datum izdavanja: ", 0, 0, 'L');
$pdf->Cell(140, 0, date('d.m.Y.', strtotime($izvjestaj['izvjestaji_datumizdavanja'])), 0, 0, 'L');
$pdf->Ln(5);

//Datum inspekcije
$pdf->Cell(40, 0, "Datum inspekcije: ", 0, 0, 'L');
$pdf->Cell(140, 0, date('d.m.Y.', strtotime($izvjestaj['izvjestaji_datuminspekcije'])), 0, 0, 'L');
$pdf->Ln(5);

//set bold font
$pdf->AddFont('Calibri-Bold', '', 'calibri-bold.ttf', false);
$pdf->SetFont('Calibri-Bold', '', 9);

//naslov 2 - podnosilac zahtjeva
$pdf->Cell(180, 0, '1. Podnosilac zahtjeva', 0, 0, 'C');
$pdf->Ln(5);

//kupimo podnosioca zahtjeva
$podnosilaczahtjeva = new singleObject;
$podnosilaczahtjeva = $podnosilaczahtjeva->fetch_single_object("klijenti", "klijenti_id", $radninalog['radninalozi_klijentid']);

//set regular font
$pdf->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', false);
$pdf->SetFont('Calibri-Regular', '', 9);

//naziv ustanove
$pdf->Cell(40, 0, "Naziv ustanove: ", 0, 0, 'L');
$pdf->Cell(140, 0, $podnosilaczahtjeva['klijenti_naziv'], 0, 0, 'L');
$pdf->Ln(5);

//adresa ustanove
$pdf->Cell(40, 0, "Adresa ustanove: ", 0, 0, 'L');
$pdf->Cell(140, 0, $podnosilaczahtjeva['klijenti_adresa'], 0, 0, 'L');
$pdf->Ln(5);

//Zahtjev za ispitivanje mjerila
$pdf->Cell(40, 0, "Zahtjev za ispitivanje mjerila: ", 0, 0, 'L');
$pdf->Cell(140, 0, $izvjestaj['izvjestaji_zahtjevzaispitivanje'], 0, 0, 'L');
$pdf->Ln(5);

//set bold font
$pdf->AddFont('Calibri-Bold', '', 'calibri-bold.ttf', false);
$pdf->SetFont('Calibri-Bold', '', 9);

//naslov 3 - Identifikacija mjerila
$pdf->Cell(180, 0, '2. Identifikacija mjerila', 0, 0, 'C');
$pdf->Ln(5);

//set regular font
$pdf->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', false);
$pdf->SetFont('Calibri-Regular', '', 9);

//kupimo mjerilo
$mjerilo = new singleObject;
$mjerilo = $mjerilo->fetch_single_object("mjerila", "mjerila_id", $izvjestaj['izvjestaji_mjeriloid']);

//Mjerilo zadovoljava
$pdf->Cell(40, 0, "Zadovoljava: ", 0, 0, 'L');
$pdf->Cell(140, 0, $mjerilo['mjerila_zadovoljava'], 0, 0, 'L');
$pdf->Ln(5);

//Proizvođač mjerila
$pdf->Cell(40, 0, "Proizvođač: ", 0, 0, 'L');
$pdf->Cell(140, 0, $mjerilo['mjerila_proizvodjac'], 0, 0, 'L');
$pdf->Ln(5);

//Tip mjerila
$pdf->Cell(40, 0, "Tip mjerila: ", 0, 0, 'L');
$pdf->Cell(140, 0, $mjerilo['mjerila_tip'], 0, 0, 'L');
$pdf->Ln(5);

//Serijski broj mjerila
$pdf->Cell(40, 0, "Serijski broj mjerila: ", 0, 0, 'L');
$pdf->Cell(140, 0, $mjerilo['mjerila_serijskibroj'], 0, 0, 'L');
$pdf->Ln(5);

//Godina proizvodnje mjerila
$pdf->Cell(40, 0, "Godina proizvodnje: ", 0, 0, 'L');
$pdf->Cell(140, 0, $mjerilo['mjerila_godinaproizvodnje'], 0, 0, 'L');
$pdf->Ln(5);

//Službena oznaka mjerila
$pdf->Cell(40, 0, "Službena oznaka: ", 0, 0, 'L');
$pdf->Cell(140, 0, $mjerilo['mjerila_sluzbenaoznaka'], 0, 0, 'L');
$pdf->Ln(5);

//Dodatni red za mjerila krvnog pritiska i navodimo da li je za djecu i novorođenčad
if ($vrstauredjaja['vrsteuredjaja_id'] == 13 || $vrstauredjaja['vrsteuredjaja_id'] == 14 || $vrstauredjaja['vrsteuredjaja_id'] == 50) {

    //Mjerilo za djecu - DA
    $djeca = "DA";
    $pdf->Cell(65, 0, "Mjerilo krvnog pritiska za djecu i novorođenčad: ", 0, 0, 'L');
    $pdf->Cell(140, 0, $djeca, 0, 0, 'L');
    $pdf->Ln(5);

} else if ($vrstauredjaja['vrsteuredjaja_id'] == 11 || $vrstauredjaja['vrsteuredjaja_id'] == 12 || $vrstauredjaja['vrsteuredjaja_id'] == 49) {

    //Mjerilo za djecu - NE
    $djeca = "NE";
    $pdf->Cell(65, 0, "Mjerilo krvnog pritiska za djecu i novorođenčad: ", 0, 0, 'L');
    $pdf->Cell(140, 0, $djeca, 0, 0, 'L');
    $pdf->Ln(5);

}

//set bold font
$pdf->AddFont('Calibri-Bold', '', 'calibri-bold.ttf', false);
$pdf->SetFont('Calibri-Bold', '', 9);

//naslov 4 - Verifkacija mjerila 
$pdf->Cell(180, 0, '3. Verifikacija mjerila', 0, 0, 'C');
$pdf->Ln(5);

//set regular font
$pdf->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', false);
$pdf->SetFont('Calibri-Regular', '', 9);

//Mjesto inspekcije
$pdf->Cell(40, 0, "Mjesto inspekcije: ", 0, 0, 'L');
$pdf->Cell(140, 0, $izvjestaj['izvjestaji_mjestoinspekcije'], 0, 0, 'L');
$pdf->Ln(5);

//Kupimo metodu inspekcije
$metodainspekcije = new singleObject;
$metodainspekcije = $metodainspekcije->fetch_single_object("metodeinspekcije", "metodeinspekcije_id", $radninalog['radninalozi_metodainspekcijeid']);

//Ako postoji metoda inspekcije
if ($metodainspekcije != false) {
    $pdf->Cell(40, 0, "Metoda inspekcije: ", 0, 0, 'L');
    $pdf->Cell(140, 0, $metodainspekcije['metodeinspekcije_naziv'], 0, 0, 'L');
    $pdf->Ln(5);
}

//kupimo vrstu inspekcije
$vrstainspekcije = new singleObject;
$vrstainspekcije = $vrstainspekcije->fetch_single_object("vrsteinspekcije", "vrsteinspekcije_id", $izvjestaj['izvjestaji_vrstainspekcijeid']);

//Vrsta inspekcije
$pdf->Cell(40, 0, "Vrsta inspekcije: ", 0, 0, 'L');
$pdf->Cell(140, 0, $vrstainspekcije['vrsteinspekcije_naziv'], 0, 0, 'L');
$pdf->Ln(2.5);

//Oprema za inspekciju
$pdf->Cell(40, 5, "Oprema za inspekciju: ", 0, 0, 'L');

//Razbijamo niz id-jeva opreme za inspekciju
$opremaniz = explode(",", $izvjestaj['izvjestaji_opremazainspekciju']);
//formiramo nizove opreme, proizvođača opreme i serijskih brojeva opreme
$opremafinal = "";
$proizvodjacfinal = "";
$serijskifinal = "";
foreach ($opremaniz as $singleoprema) {
    $opremazainspekciju = new singleObject;
    $opremazainspekciju = $opremazainspekciju->fetch_single_object("opremazainspekciju", "opremazainspekciju_id", $singleoprema);
    $opremafinal = $opremafinal . $opremazainspekciju['opremazainspekciju_naziv'] . "; ";
    $proizvodjacfinal = $proizvodjacfinal . $opremazainspekciju['opremazainspekciju_proizvodjac'] . "; ";
    $serijskifinal = $serijskifinal . $opremazainspekciju['opremazainspekciju_serijskibroj'] . "; ";
}

//izlistavamo opremu
$pdf->MultiCell(140, 5, $opremafinal, 0, 'J');
$pdf->Ln(0);

//izlistavamo proizvođače opreme
$pdf->Cell(40, 5, "Proizvođač: ", 0, 0, 'L');
$pdf->MultiCell(140, 5, $proizvodjacfinal, 0, 'J');
$pdf->Ln(0);

//izlistavamo serijske brojeve opreme
$pdf->Cell(40, 5, "S/N: ", 0, 0, 'L');
$pdf->MultiCell(140, 5, $serijskifinal, 0, 'J');
$pdf->Ln(2.5);

//Opis procedure
$pdf->MultiCell(180, 5, $izvjestaj['izvjestaji_opisprocedure'], 0, 'J');
$pdf->Ln(5);

//set bold font
$pdf->AddFont('Calibri-Bold', '', 'calibri-bold.ttf', false);
$pdf->SetFont('Calibri-Bold', '', 9);

//naslov 5 - Identifikacija ambijentalnih uslova
$pdf->Cell(180, 0, '3.1. Identifikacija ambijentalnih uslova', 0, 0, 'C');
$pdf->Ln(5);

//set regular font
$pdf->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', false);
$pdf->SetFont('Calibri-Regular', '', 9);

//Temperatura
$pdf->Cell(40, 5, "Temperatura [°C]: ", 0, 0, 'L');
$pdf->Cell(140, 5, $izvjestaj['izvjestaji_temperatura'] . "°C ±1°C", 0, 0, 'L');
$pdf->Ln(5);

//Vlažnost
$pdf->Cell(40, 5, "Vlažnost [%]: ", 0, 0, 'L');
$pdf->Cell(140, 5, $izvjestaj['izvjestaji_vlaznost'] . "% ±1%", 0, 0, 'L');
$pdf->Ln(5);

//set bold font
$pdf->AddFont('Calibri-Bold', '', 'calibri-bold.ttf', false);
$pdf->SetFont('Calibri-Bold', '', 9);

//naslov 6 - Vizuelni pregled mjerila
$pdf->Cell(180, 0, '3.2. Vizuelni pregled mjerila', 0, 0, 'C');
$pdf->Ln(5);

//set regular font
$pdf->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', false);
$pdf->SetFont('Calibri-Regular', '', 9);

//setujemo parametre za tabelu
$pdf->SetWidths(array(120, 20));
$pdf->SetAligns(array("L", "C"));
$pdf->SetLeftMargin(35);

//konvertujemo 0 i 1 u NE i DA
if ($izvjestaj['izvjestaji_mjerilocisto'] == 1) { $izvjestaji_mjerilocisto = "DA"; } else { $izvjestaji_mjerilocisto = "NE"; }
if ($izvjestaj['izvjestaji_mjerilocjelovito'] == 1) { $izvjestaji_mjerilocjelovito = "DA"; } else { $izvjestaji_mjerilocjelovito = "NE"; }
if ($izvjestaj['izvjestaji_mjerilocitljivo'] == 1) { $izvjestaji_mjerilocitljivo = "DA"; } else { $izvjestaji_mjerilocitljivo = "NE"; }
if ($izvjestaj['izvjestaji_mjerilokablovi'] == 1) { $izvjestaji_mjerilokablovi = "DA"; } else { $izvjestaji_mjerilokablovi = "NE"; }

//ispisujemo 4 reda tabele
$pdf->Row(array("1. Mjerilo je čisto i uredno: ", $izvjestaji_mjerilocisto));
$pdf->Row(array("2. Mjerilo je cjelovito i propisane konstrukcije: ", $izvjestaji_mjerilocjelovito));
$pdf->Row(array("3. Mjerilo ima čitljive natpise i oznake: ", $izvjestaji_mjerilocitljivo));
$pdf->Row(array("4. Mjerilo jposjeduje napojne kablove i ostale dodatke neophodne za rad: ", $izvjestaji_mjerilokablovi));

//setujemo parametreza nastavak izvještaja
$pdf->SetLeftMargin(15);
$pdf->Ln(5);

//set bold font
$pdf->AddFont('Calibri-Bold', '', 'calibri-bold.ttf', false);
$pdf->SetFont('Calibri-Bold', '', 9);

// Prije naslova "4." – prelaz samo ako nema prostora za naslov (≈20 mm); legendu kasnije provjerava CheckPageBreak(35)
$pdf->CheckPageBreak(20);

//naslov 7 - Ispitivanje greške mjerila
$pdf->Cell(180, 0, '4. Ispitivanje greške mjerila', 0, 0, 'C');
$pdf->Ln(5);

//set regular font
$pdf->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', false);
$pdf->SetFont('Calibri-Regular', '', 9);

//Ako uređaji NISU MJERILA KRVNOG PRITISKA
if ($vrstauredjaja['vrsteuredjaja_id'] != 11 && 
    $vrstauredjaja['vrsteuredjaja_id'] != 12 && 
    $vrstauredjaja['vrsteuredjaja_id'] != 13 && 
    $vrstauredjaja['vrsteuredjaja_id'] != 14 && 
    $vrstauredjaja['vrsteuredjaja_id'] != 49 && 
    $vrstauredjaja['vrsteuredjaja_id'] != 50) {

    $pdf->CheckPageBreak(35);
    // Legenda (samo ako ima prostora)
    //ISPISUJEMO LEGENDU
    $pdf->Cell(180, 0, 'Skraćenice korištene u ispitivanju greške mjerenja:', 0, 0, 'C');
    $pdf->Ln(10);
    $pdf->Cell(90, 0, 'Xs - Zadana vrijednost mjerne veličine', 0, 0, 'L');
    $pdf->Cell(90, 0, 'ΔX - Apsolutna greška mjerenja ΔX = |<Xm>-Xs|', 0, 0, 'L');
    $pdf->Ln(5);
    $pdf->Cell(90, 0, 'Xm - Izmjerena vrijednost mjerne veličine', 0, 0, 'L');
    $pdf->Cell(90, 0, 'δ - Relativna greška mjerenja δ=ΔX/Xs*100%', 0, 0, 'L');
    $pdf->Ln(5);
    $pdf->Cell(90, 0, '<Xm> - Srednja vrijednost mjerne veličine', 0, 0, 'L');
    $pdf->Ln(5);
}

//set regular font
$pdf->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', false);
$pdf->SetFont('Calibri-Regular', '', 9);

//kupimo mjerne veličine za uređaj
$mjernevelicine = new allObjectsBy;
$mjernevelicine = $mjernevelicine->fetch_all_objects_by("mjernevelicine", "mjernevelicine_vrstauredjajaid", $radninalog['radninalozi_vrstauredjajaid'], "mjernevelicine_id", "ASC");

//setujemo brojač na 1 da bismo imali naslove 4.1 recimo
$i = 1;

//vrtimo svemjerne veličine
foreach ($mjernevelicine as $mjernavelicina) {

    $pdf->CheckPageBreak(40);

    //ako je u pitanju "Ispitivanje tačnosti i histerezisa mjerila" kod svih mjerila krvnog pritiska
    if ($mjernavelicina['mjernevelicine_id'] != 30 && 
        $mjernavelicina['mjernevelicine_id'] != 32 && 
        $mjernavelicina['mjernevelicine_id'] != 34 && 
        $mjernavelicina['mjernevelicine_id'] != 36 && 
        $mjernavelicina['mjernevelicine_id'] != 139 && 
        $mjernavelicina['mjernevelicine_id'] != 143) {
        $pdf->Ln(5);
    }

    if($mjernavelicina['mjernevelicine_id'] != 142 && $mjernavelicina['mjernevelicine_id'] != 146) {
        //generičemo naslov za određeno ispitivanje greške
        $pdf->Cell(180, 0, '4.' . $i . ' ' . $mjernavelicina['mjernevelicine_naziv'], 0, 0, 'C');
        //prelazimo u novi red (3 mm razmaka ispod naslova)
        $pdf->Ln(3);
    }

    //bojimo zaglavlje
    $pdf->SetFillColor(238, 238, 238);

    //Ako uređaji NISU MJERILA KRVNOG PRITISKA
    if ($vrstauredjaja['vrsteuredjaja_id'] != 11 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 12 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 13 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 14 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 49 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 50 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 3 && 
        $vrstauredjaja['vrsteuredjaja_id'] != 18) {
        
        //ispisujemo zaglavlje
        $pdf->Cell(20, 10, "Xs[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(45, 5, "Xm[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(23, 5, "<Xm>", 'L,T,R', 0, 'C', 1);
        $pdf->Cell(23, 5, "ΔX", 'L,T,R', 0, 'C', 1);
        if($vrstauredjaja['vrsteuredjaja_id'] == 3){
           $pdf->Cell(23, 10, "δ", 'L,T,R,B', 0, 'C', 1); 
        }else{
            $pdf->Cell(23, 10, "δ[%]", 'L,T,R,B', 0, 'C', 1); 
        }
        $pdf->Cell(23, 5, "Dozvoljeno", 'L,T', 0, 'C', 1);
        $pdf->Cell(23, 10, "Usaglašenost", 'L,T,R,B', 0, 'C', 1);
        $pdf->Ln(5);
        $pdf->Cell(20, 5, "", 0, 0, 'C');
        $pdf->Cell(15, 5, "1", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 5, "2", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 5, "3", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(23, 5, "[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,R,B', 0, 'C', 1);
        $pdf->Cell(23, 5, "[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,R,B', 0, 'C', 1);
        $pdf->Cell(23, 5, "", 'B', 0, 'C');
        $pdf->Cell(23, 5, "odstupanje", "L,R,B", 0, 'C', 1);
        $pdf->Ln(5);

        //kupimo sve referentne vrijednosti prema mjernoj veličini koja je na redu
        $referentnevrijednosti = new allObjectsBy;
        $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by("referentnevrijednosti", "referentnevrijednosti_mjernavelicinaid", $mjernavelicina['mjernevelicine_id'], "referentnevrijednosti_referentnavrijednost", "ASC");

        //setujemo parametre redova nastavka tabele
        $pdf->SetWidths(array(20, 15, 15, 15, 23, 23, 23, 23, 23));
        $pdf->SetAligns(array("C", "C", "C", "C", "C", "C", "C", "C", "C"));

        //vrtimo sve referentne vrijednosti
        foreach ($referentnevrijednosti as $referentnavrijednost) {
            include __DIR__ . '/includes/rezultati_mjerenja_logika.php';

            if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-" && $prvomjerenje != "--" && $drugomjerenje != "--" && $trecemjerenje != "--") {
				if($referentnavrijednost['referentnevrijednosti_id'] == 58 || $referentnavrijednost['referentnevrijednosti_id'] == 59 || $referentnavrijednost['referentnevrijednosti_id'] == 60 || $referentnavrijednost['referentnevrijednosti_id'] == 61 || $referentnavrijednost['referentnevrijednosti_id'] == 62){
					$pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, $trecemjerenje, number_format((float) $srednjavrijednost, 2, '.', ''), number_format((float) $apsolutnagreska, 2, '.', ''), number_format((float) $relativnagreska, 2, '.', '')." J", $referentnavrijednost['referentnevrijednosti_odstupanje']." J", $usaglasenost));
                }else if($referentnavrijednost['referentnevrijednosti_id'] == 63 || $referentnavrijednost['referentnevrijednosti_id'] == 64 || $referentnavrijednost['referentnevrijednosti_id'] == 65 || $referentnavrijednost['referentnevrijednosti_id'] == 66){
                    $pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, $trecemjerenje, number_format((float) $srednjavrijednost, 2, '.', ''), number_format((float) $apsolutnagreska, 2, '.', ''), number_format((float) $relativnagreska, 2, '.', '')." %", round($referentnavrijednost['referentnevrijednosti_odstupanje'], 0)." %", $usaglasenost));
				}else{
                    if($vrstauredjaja['vrsteuredjaja_id'] == 4){
                        $pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, $trecemjerenje, number_format((float) $srednjavrijednost, 2, '.', ''), number_format((float) $apsolutnagreska, 2, '.', ''), number_format((float) $relativnagreska, 2, '.', ''), round($referentnavrijednost['referentnevrijednosti_odstupanje'], 3)." W", $usaglasenost));
                    }else{
                        $pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, $trecemjerenje, number_format((float) $srednjavrijednost, 2, '.', ''), number_format((float) $apsolutnagreska, 2, '.', ''), number_format((float) $relativnagreska, 2, '.', ''), round($referentnavrijednost['referentnevrijednosti_odstupanje'], 0), $usaglasenost));
                    }
				}
            } else {
                // U mpdf-includes: script[one-hidden-two-not-measurable-absolute] i sl. NE ispisuju red u else (mješovito / sva tri "-").
                // script[one-hidden-two-not-measurable-relative] u else ISPISUJE red (ref, p1, p2, p3, "-", usaglašenost) – uključujući kad su sva tri "-".
                // Mjerne veličine 10, 11, 17, 18, 19, 20, 28, 151, 152: ne prikazujemo red u else; ostale (npr. 1 Brzina otkucaja): prikazujemo kao u mpdf-u.
                $standardnaTabelaNePrikazujeRedZaMjesovito = in_array((int)$mjernavelicina['mjernevelicine_id'], [10, 11, 17, 18, 19, 20, 28, 151, 152], true);
                if (!$standardnaTabelaNePrikazujeRedZaMjesovito) {
                    $pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, $trecemjerenje, "-", "-", "-", "-", $usaglasenost));
                }
            }
        }

    } else if ($mjernavelicina['mjernevelicine_id'] == 30 || $mjernavelicina['mjernevelicine_id'] == 32 || $mjernavelicina['mjernevelicine_id'] == 34 || $mjernavelicina['mjernevelicine_id'] == 36 || $mjernavelicina['mjernevelicine_id'] == 139 || $mjernavelicina['mjernevelicine_id'] == 143) {
        if ($mjernavelicina['mjernevelicine_id'] == 30 || $mjernavelicina['mjernevelicine_id'] == 32) {
            $sirineKolona1 = array("30", "40", "40", "35");
            $sirineKolona2 = array("30", "20", "20", "20", "20", "17.5", "17.5");
        } else {
            $sirineKolona1 = array("30", "50", "50", "50");
            $sirineKolona2 = array("30", "25", "25", "25", "25", "25", "25");
        }
        $pdf->Cell($sirineKolona1[0], 10, "Pritisak[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell($sirineKolona1[1], 5, "1. ciklus", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell($sirineKolona1[2], 5, "2. ciklus", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell($sirineKolona1[3], 5, "Odstupanje", 'L,T,R,B', 0, 'C', 1);
        if ($mjernavelicina['mjernevelicine_id'] == 30 || $mjernavelicina['mjernevelicine_id'] == 32) {
            $pdf->Cell(35, 5, "Histerezis", 'L,T,R,B', 0, 'C', 1);
        }
        $pdf->Ln(5);
        $pdf->Cell($sirineKolona2[0], 5, "", '', 0, 'C', 0);
        $pdf->Cell($sirineKolona2[1], 5, "Rastuća", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell($sirineKolona2[2], 5, "Opadajuća", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell($sirineKolona2[3], 5, "Rastuća", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell($sirineKolona2[4], 5, "Opadajuća", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell($sirineKolona2[5], 5, "Rastuća", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell($sirineKolona2[6], 5, "Opadajuća", 'L,T,R,B', 0, 'C', 1);
        if ($mjernavelicina['mjernevelicine_id'] == 30 || $mjernavelicina['mjernevelicine_id'] == 32) {
            $pdf->Cell(17.5, 5, "1. ciklus", 'L,T,R,B', 0, 'C', 1);
            $pdf->Cell(17.5, 5, "2.ciklus", 'L,T,R,B', 0, 'C', 1);
        }
        $pdf->Ln(5);
        $referentnevrijednosti = new allObjectsBy;
        $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by("referentnevrijednosti", "referentnevrijednosti_mjernavelicinaid", $mjernavelicina['mjernevelicine_id'], "referentnevrijednosti_referentnavrijednost", "ASC");
        if ($mjernavelicina['mjernevelicine_id'] == 30 || $mjernavelicina['mjernevelicine_id'] == 32) {
            $pdf->SetWidths(array(30, 20, 20, 20, 20, 17.5, 17.5, 17.5, 17.5));
            $pdf->SetAligns(array("C", "C", "C", "C", "C", "C", "C", "C", "C"));
        } else {
            $pdf->SetWidths(array(30, 25, 25, 25, 25, 25, 25));
            $pdf->SetAligns(array("C", "C", "C", "C", "C", "C", "C"));
        }
        $max1 = [];
        $max2 = [];
        foreach ($referentnevrijednosti as $referentnavrijednost) {
            $prvomjerenje = $drugomjerenje = $trecemjerenje = $cetvrtomjerenje = '-';
            $rezultatimjerenja = new allResults;
            $rezultatimjerenja = $rezultatimjerenja->fetch_all_results($izvjestaj['izvjestaji_id'], $mjernavelicina['mjernevelicine_id'], $referentnavrijednost['referentnevrijednosti_id']);
            foreach ($rezultatimjerenja as $rezultatmjerenja) {
                switch ($rezultatmjerenja) {
                    case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 1:
                        $prvomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                        break;
                    case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 2:
                        $drugomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                        break;
                    case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 3:
                        $trecemjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                        break;
                    case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 4:
                        $cetvrtomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                        break;
                }
            }
            if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-" && $cetvrtomjerenje != "-" && $prvomjerenje != "--" && $drugomjerenje != "--" && $trecemjerenje != "--" && $cetvrtomjerenje != "--") {
                // Formule usklađene s izvjestajmpdf (11.php): odstupanje i histerezis kao apsolutne vrijednosti
                $odsRast = abs(((abs($prvomjerenje) + abs($trecemjerenje)) / 2) - $referentnavrijednost['referentnevrijednosti_referentnavrijednost']);
                array_push($max1, $odsRast);
                $odsOpad = abs(((abs($drugomjerenje) + abs($cetvrtomjerenje)) / 2) - $referentnavrijednost['referentnevrijednosti_referentnavrijednost']);
                array_push($max1, $odsOpad);
                $hist1 = abs(abs($prvomjerenje) - abs($drugomjerenje));
                array_push($max2, $hist1);
                $hist2 = abs(abs($trecemjerenje) - abs($cetvrtomjerenje));
                array_push($max2, $hist2);
                $p1 = (is_numeric($prvomjerenje) ? number_format((float) $prvomjerenje, 2, '.', '') : $prvomjerenje);
                $p2 = (is_numeric($drugomjerenje) ? number_format((float) $drugomjerenje, 2, '.', '') : $drugomjerenje);
                $p3 = (is_numeric($trecemjerenje) ? number_format((float) $trecemjerenje, 2, '.', '') : $trecemjerenje);
                $p4 = (is_numeric($cetvrtomjerenje) ? number_format((float) $cetvrtomjerenje, 2, '.', '') : $cetvrtomjerenje);
                if ($mjernavelicina['mjernevelicine_id'] == 30 || $mjernavelicina['mjernevelicine_id'] == 32) {
                    $pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $p1, $p2, $p3, $p4, number_format((float) $odsRast, 2, '.', ''), number_format((float) $odsOpad, 2, '.', ''), number_format((float) $hist1, 2, '.', ''), number_format((float) $hist2, 2, '.', '')));
                } else {
                    $pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $p1, $p2, $p3, $p4, number_format((float) $odsRast, 2, '.', ''), number_format((float) $odsOpad, 2, '.', '')));
                }
            } else {
                $odsRast = "-";
                $odsOpad = "-";
                $hist1 = "-";
                $hist2 = "-";
                // Kao u mpdf-includes (11.php, 50.php): red prikazujemo samo ako su sva 4 mjerenja '--'; inače (mješovito -/--) ne prikazujemo red
                $svaCetiriNisuIzmjerena = ($prvomjerenje === '--' && $drugomjerenje === '--' && $trecemjerenje === '--' && $cetvrtomjerenje === '--');
                if ($svaCetiriNisuIzmjerena) {
                    if ($mjernavelicina['mjernevelicine_id'] == 30 || $mjernavelicina['mjernevelicine_id'] == 32) {
                        $pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), "-", "-", "-", "-", "-", "-", "-", "-"));
                    } else {
                        $pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), "-", "-", "-", "-", "-", "-"));
                    }
                }
            }
        }
    } else if($mjernavelicina['mjernevelicine_id'] == 141 || $mjernavelicina['mjernevelicine_id'] == 142) {

        $rezultatimjerenja_141_520 = new allResults;
        $rezultatimjerenja_141_520 = $rezultatimjerenja_141_520->fetch_all_results($izvjestaj['izvjestaji_id'], 141, 520);
        $niz_141_520_142_522 =  array();

        //foreach($rezultatimjerenja_141_520 as $rezultat){
        //    array_push($niz_141_520_142_522, $rezultat['rezultatimjerenja_rezultatmjerenja']);
        //}

        for ($x = 0; $x <= 9; $x++) {
            array_push($niz_141_520_142_522, $rezultatimjerenja_141_520[$x]['rezultatimjerenja_rezultatmjerenja']);
        }
        
        $rezultatimjerenja_141_521 = new allResults;
        $rezultatimjerenja_141_521 = $rezultatimjerenja_141_521->fetch_all_results($izvjestaj['izvjestaji_id'], 141, 521);
        $niz_141_521_142_523 =  array();

        //foreach($rezultatimjerenja_141_521 as $rezultat){
        //    array_push($niz_141_521_142_523, $rezultat['rezultatimjerenja_rezultatmjerenja']);
        //}

        for ($x = 0; $x <= 9; $x++) {
            array_push($niz_141_521_142_523, $rezultatimjerenja_141_521[$x]['rezultatimjerenja_rezultatmjerenja']);
        }

        $rezultatimjerenja_142_522 = new allResults;
        $rezultatimjerenja_142_522 = $rezultatimjerenja_142_522->fetch_all_results($izvjestaj['izvjestaji_id'], 142, 522);

        //foreach($rezultatimjerenja_142_522 as $rezultat){
        //    array_push($niz_141_520_142_522, $rezultat['rezultatimjerenja_rezultatmjerenja']);
        //}

        for ($x = 0; $x <= 9; $x++) {
            array_push($niz_141_520_142_522, $rezultatimjerenja_142_522[$x]['rezultatimjerenja_rezultatmjerenja']);
        }
        
        //$rezultatimjerenja_142_523 = new allResults;
        //$rezultatimjerenja_142_523 = $rezultatimjerenja_142_523->fetch_all_results($izvjestaj['izvjestaji_id'], 142, 523);

        $rezultatimjerenja_142_523 = new allResultsWithSort;
        $rezultatimjerenja_142_523 = $rezultatimjerenja_142_523->fetch_all_results_with_sort($izvjestaj['izvjestaji_id'], 142, 523, 'rezultatimjerenja_id', 'ASC');

        //foreach($rezultatimjerenja_142_523 as $rezultat){
        //    array_push($niz_141_521_142_523, $rezultat['rezultatimjerenja_rezultatmjerenja']);
        //}

        for ($x = 0; $x <= 9; $x++) {
            array_push($niz_141_521_142_523, $rezultatimjerenja_142_523[$x]['rezultatimjerenja_rezultatmjerenja']);
        }

        if($mjernavelicina['mjernevelicine_id'] == 141) {

        $pdf->Cell(30, 10, "Broj mjerenja", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "1", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "2", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "3", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "4", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "5", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "6", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "7", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "8", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "9", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "10", 'L,T,R,B', 0, 'C', 1);

        $pdf->Ln(10);

        $pdf->Cell(30, 5, "Sistolički", 'L,T,R', 0, 'C', 1);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_520[0]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_520[1]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_520[2]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_520[3]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_520[4]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_520[5]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_520[6]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_520[7]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_520[8]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_520[9]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Ln(5);
        $pdf->Cell(30, 5, "pritisak [mmHG]", 'L,R,B', 0, 'C', 1);

        $pdf->Ln(5);

        $pdf->Cell(30, 5, "Distolički", 'L,T,R', 0, 'C', 1);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_521[0]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_521[1]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_521[2]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_521[3]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_521[4]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_521[5]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_521[6]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_521[7]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_521[8]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_141_521[9]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Ln(5);
        $pdf->Cell(30, 5, "pritisak [mmHG]", 'L,R,B', 0, 'C', 1);

        } else if($mjernavelicina['mjernevelicine_id'] == 142) {

        $pdf->Cell(30, 10, "Broj mjerenja", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "11", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "12", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "13", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "14", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "15", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "16", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "17", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "18", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "19", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "20", 'L,T,R,B', 0, 'C', 1);

        $pdf->Ln(10);

        $pdf->Cell(30, 5, "Sistolički", 'L,T,R', 0, 'C', 1);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_522[0]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_522[1]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_522[2]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_522[3]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_522[4]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_522[5]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_522[6]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_522[7]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_522[8]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_522[9]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Ln(5);
        $pdf->Cell(30, 5, "pritisak [mmHG]", 'L,R,B', 0, 'C', 1);

        $pdf->Ln(5);

        $pdf->Cell(30, 5, "Distolički", 'L,T,R', 0, 'C', 1);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_523[0]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_523[1]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_523[2]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_523[3]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_523[4]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_523[5]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_523[6]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_523[7]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_523[8]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_142_523[9]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Ln(5);
        $pdf->Cell(30, 5, "pritisak [mmHG]", 'L,R,B', 0, 'C', 1);
        $pdf->Ln(5);
        $devijacijaSistolicni = round($pdf->calculateSampleStandardDeviation($niz_141_520_142_522),2);
        $pdf->Cell(180, 10, "Greška sistoličkog pritiska [mmHg]: ".$devijacijaSistolicni, 0, 0, 'L', 0);
        $pdf->Ln(5);
        $devijacijaDistolicni = round($pdf->calculateSampleStandardDeviation($niz_141_521_142_523),2);
        $pdf->Cell(180, 10, "Greška distoličkog pritiska [mmHg]: ".$devijacijaDistolicni, 0, 0, 'L', 0);
        $pdf->Ln(5);
        $pdf->Cell(180, 10, "Postavljena vrijednost: ".$rezultatimjerenja_142_523[10]['rezultatimjerenja_rezultatmjerenja'], 0, 0, 'L', 0);
        $pdf->Ln(10);

        }
    } else if($mjernavelicina['mjernevelicine_id'] == 145 || $mjernavelicina['mjernevelicine_id'] == 146) {

        $rezultatimjerenja_145_533 = new allResults;
        $rezultatimjerenja_145_533 = $rezultatimjerenja_145_533->fetch_all_results($izvjestaj['izvjestaji_id'], 145, 533);
        $niz_145_533_146_535 =  array();

        //foreach($rezultatimjerenja_141_520 as $rezultat){
        //    array_push($niz_141_520_142_522, $rezultat['rezultatimjerenja_rezultatmjerenja']);
        //}

        for ($x = 0; $x <= 9; $x++) {
            array_push($niz_145_533_146_535, $rezultatimjerenja_145_533[$x]['rezultatimjerenja_rezultatmjerenja']);
        }
        
        $rezultatimjerenja_145_534 = new allResults;
        $rezultatimjerenja_145_534 = $rezultatimjerenja_145_534->fetch_all_results($izvjestaj['izvjestaji_id'], 145, 534);
        $niz_145_534_146_536 =  array();

        //foreach($rezultatimjerenja_141_521 as $rezultat){
        //    array_push($niz_141_521_142_523, $rezultat['rezultatimjerenja_rezultatmjerenja']);
        //}

        for ($x = 0; $x <= 9; $x++) {
            array_push($niz_145_534_146_536, $rezultatimjerenja_145_534[$x]['rezultatimjerenja_rezultatmjerenja']);
        }

        $rezultatimjerenja_146_535 = new allResults;
        $rezultatimjerenja_146_535 = $rezultatimjerenja_146_535->fetch_all_results($izvjestaj['izvjestaji_id'], 146, 535);

        //foreach($rezultatimjerenja_142_522 as $rezultat){
        //    array_push($niz_141_520_142_522, $rezultat['rezultatimjerenja_rezultatmjerenja']);
        //}

        for ($x = 0; $x <= 9; $x++) {
            array_push($niz_145_533_146_535, $rezultatimjerenja_146_535[$x]['rezultatimjerenja_rezultatmjerenja']);
        }
        
        $rezultatimjerenja_146_536 = new allResultsWithSort;
        $rezultatimjerenja_146_536 = $rezultatimjerenja_146_536->fetch_all_results_with_sort($izvjestaj['izvjestaji_id'], 146, 536, 'rezultatimjerenja_id', 'ASC');

        //var_dump($rezultatimjerenja_146_536);

        //foreach($rezultatimjerenja_142_523 as $rezultat){
        //    array_push($niz_141_521_142_523, $rezultat['rezultatimjerenja_rezultatmjerenja']);
        //}

        for ($x = 0; $x <= 9; $x++) {
            array_push($niz_145_534_146_536, $rezultatimjerenja_146_536[$x]['rezultatimjerenja_rezultatmjerenja']);
        }

        if($mjernavelicina['mjernevelicine_id'] == 145) {

        $pdf->Cell(30, 10, "Broj mjerenja", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "1", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "2", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "3", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "4", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "5", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "6", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "7", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "8", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "9", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "10", 'L,T,R,B', 0, 'C', 1);

        $pdf->Ln(10);

        $pdf->Cell(30, 5, "Sistolički", 'L,T,R', 0, 'C', 1);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_533[0]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_533[1]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_533[2]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_533[3]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_533[4]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_533[5]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_533[6]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_533[7]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_533[8]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_533[9]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Ln(5);
        $pdf->Cell(30, 5, "pritisak [mmHG]", 'L,R,B', 0, 'C', 1);

        $pdf->Ln(5);

        $pdf->Cell(30, 5, "Distolički", 'L,T,R', 0, 'C', 1);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_534[0]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_534[1]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_534[2]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_534[3]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_534[4]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_534[5]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_534[6]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_534[7]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_534[8]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_145_534[9]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Ln(5);
        $pdf->Cell(30, 5, "pritisak [mmHG]", 'L,R,B', 0, 'C', 1);

        } else if($mjernavelicina['mjernevelicine_id'] == 146) {

        $pdf->Cell(30, 10, "Broj mjerenja", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "11", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "12", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "13", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "14", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "15", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "16", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "17", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "18", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "19", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(15, 10, "20", 'L,T,R,B', 0, 'C', 1);

        $pdf->Ln(10);

        $pdf->Cell(30, 5, "Sistolički", 'L,T,R', 0, 'C', 1);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_535[0]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_535[1]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_535[2]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_535[3]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_535[4]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_535[5]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_535[6]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_535[7]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_535[8]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_535[9]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Ln(5);
        $pdf->Cell(30, 5, "pritisak [mmHG]", 'L,R,B', 0, 'C', 1);

        $pdf->Ln(5);

        $pdf->Cell(30, 5, "Distolički", 'L,T,R', 0, 'C', 1);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_536[0]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_536[1]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_536[2]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_536[3]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_536[4]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_536[5]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_536[6]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_536[7]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_536[8]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(15, 10, $rezultatimjerenja_146_536[9]['rezultatimjerenja_rezultatmjerenja'], 'L,T,R,B', 0, 'C', 0);
        $pdf->Ln(5);
        $pdf->Cell(30, 5, "pritisak [mmHG]", 'L,R,B', 0, 'C', 1);
        $pdf->Ln(5);
        $devijacijaSistolicni = round($pdf->calculateSampleStandardDeviation($niz_145_533_146_535),2);
        $pdf->Cell(180, 10, "Greška sistoličkog pritiska [mmHg]: ".$devijacijaSistolicni, 0, 0, 'L', 0);
        $pdf->Ln(5);
        $devijacijaDistolicni = round($pdf->calculateSampleStandardDeviation($niz_145_534_146_536),2);
        $pdf->Cell(180, 10, "Greška distoličkog pritiska [mmHg]: ".$devijacijaDistolicni, 0, 0, 'L', 0);
        $pdf->Ln(5);
        $pdf->Cell(180, 10, "Postavljena vrijednost: ".$rezultatimjerenja_146_536[10]['rezultatimjerenja_rezultatmjerenja'], 0, 0, 'L', 0);
        $pdf->Ln(10);

        }
    } else if($vrstauredjaja['vrsteuredjaja_id'] == 3 || $vrstauredjaja['vrsteuredjaja_id'] == 18) {

        //kupimo sve referentne vrijednosti prema mjernoj veličini koja je na redu
        $referentnevrijednosti = new allObjectsBy;
        $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by("referentnevrijednosti", "referentnevrijednosti_mjernavelicinaid", $mjernavelicina['mjernevelicine_id'], "referentnevrijednosti_referentnavrijednost", "ASC");

        //setujemo parametre redova nastavka tabele
        $pdf->SetWidths(array(20, 15, 15, 15, 23, 23, 23, 23, 23));
        $pdf->SetAligns(array("C", "C", "C", "C", "C", "C", "C", "C", "C"));

        //vrtimo sve referentne vrijednosti
        foreach ($referentnevrijednosti as $referentnavrijednost) {

            if($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 2 || $referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 100){
                //ispisujemo zaglavlje
                $pdf->Cell(20, 10, "Xs[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,T,R,B', 0, 'C', 1);
                $pdf->Cell(45, 5, "Xm[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,T,R,B', 0, 'C', 1);
                $pdf->Cell(23, 5, "<Xm>", 'L,T,R', 0, 'C', 1);
                $pdf->Cell(23, 5, "ΔX", 'L,T,R', 0, 'C', 1);
                $pdf->Cell(23, 10, "δ[%]", 'L,T,R,B', 0, 'C', 1);
                $pdf->Cell(23, 5, "Dozvoljeno", 'L,T', 0, 'C', 1);
                $pdf->Cell(23, 10, "Usaglašenost", 'L,T,R,B', 0, 'C', 1);
                $pdf->Ln(5);
                $pdf->Cell(20, 5, "", 0, 0, 'C');
                $pdf->Cell(15, 5, "1", 'L,T,R,B', 0, 'C', 1);
                $pdf->Cell(15, 5, "2", 'L,T,R,B', 0, 'C', 1);
                $pdf->Cell(15, 5, "3", 'L,T,R,B', 0, 'C', 1);
                $pdf->Cell(23, 5, "[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,R,B', 0, 'C', 1);
                $pdf->Cell(23, 5, "[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,R,B', 0, 'C', 1);
                $pdf->Cell(23, 5, "", 'B', 0, 'C');
                if($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 2){
                    $pdf->Cell(23, 5, "odstupanje[J]", "L,R,B", 0, 'C', 1);
                }else{
                    $pdf->Cell(23, 5, "odstupanje[%]", "L,R,B", 0, 'C', 1);
                }
                $pdf->Ln(5);
            }

            //kupimo rezultate mjerenja za ovu referentnu vrijednost
            $rezultatimjerenja = new allResults;
            $rezultatimjerenja = $rezultatimjerenja->fetch_all_results($izvjestaj['izvjestaji_id'], $mjernavelicina['mjernevelicine_id'], $referentnavrijednost['referentnevrijednosti_id']);

            //vrtimo rezultate mjerenja
            foreach ($rezultatimjerenja as $rezultatmjerenja) {
                //razvrstavamo sve rezultate na prvo, drugo i treće mjerenje
                switch ($rezultatmjerenja) {
                    case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 1:
                        $prvomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                        break;
                    case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 2:
                        $drugomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                        break;
                    case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 3:
                        $trecemjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                        break;
                }
            }

            //SREDNJA VRIJEDNOST REZULTATA MJERENJA
            //ako je vršeno mjerenje odnosno ako nije uneseno "-" kao rezultat mjerenja
            if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-") {

                //računamo srednju vrijednost iz naša tri mjerenja
                $srednjavrijednost = round(($prvomjerenje + $drugomjerenje + $trecemjerenje) / 3, 2);

            //ako nije vršeno mjerenje odnosno ako je uneseno "-" kao rezultat mjerenja
            } else {

                //srednja vrijednost je takođe "-"
                $srednjavrijednost = "-";

            }

            //APSOLUTNA GREŠKA MJERENJA
            //ako je vršeno mjerenje odnosno ako nije uneseno "-" kao rezultat mjerenja
            if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-") {

                //računamo apsolutnu grešku mjerenja
                $apsolutnagreska = abs($srednjavrijednost - $referentnavrijednost['referentnevrijednosti_referentnavrijednost']);
            
            //ako nije vršeno mjerenje odnosno ako je uneseno "-" kao rezultat mjerenja
            } else {

                //apsolutna greška je takođe "-"
                $apsolutnagreska = "-";

            }

            //ako je vršeno mjerenje odnosno ako nije uneseno "-" kao rezultat mjerenja
            if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-") {

                //ako nam je referentna vrijednost 0 onda moramo paziti pri množenju pa taj slučaj izdvajamo
                if ($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 0) {
                    //računamo relativnu grešku u postotcimagrešku
					$relativnagreska = abs(round(($apsolutnagreska / 1) * 100, 2));
                //ako referentna vrijednost nije 0
                } else {

                    //računamo relativnu grešku u postotcimagrešku
					$relativnagreska = abs(round(($apsolutnagreska / $referentnavrijednost['referentnevrijednosti_referentnavrijednost']) * 100, 2));

				}
            
            //ako smo unosili "-" za rezultate mjerenja
            } else {

                //onda je i relativna greska "-"
                $relativnagreska = "-";

            }
            
            //ako je u pitanju prvih 5 referentnih vrijednosti za defibrilator ili sve za terapeutski ultrazvuk
			if($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 2 || $referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 10 || $referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 30 || $referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 70){

                //upoređujemo relativnu grešku direktno sa odstupanjem jer odstupanje nije u %
                //ako je greška veća od dozvoljene
				if ($apsolutnagreska > $referentnavrijednost['referentnevrijednosti_odstupanje']) {
					$usaglasenost = "NE";
					$finalusaglasenost = "NISU USAGLAŠENI";
                
                //ako je greška u granicama dozvoljenog
				} else if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-") {
					$usaglasenost = "DA";
                
                // usaglašenost mora biti "-" ako su inputi "-"
				} else {
					$usaglasenost = "-";
				}

			}else{

				if ($relativnagreska > round($referentnavrijednost['referentnevrijednosti_odstupanje'], 0)) {
					$usaglasenost = "NE";
					$finalusaglasenost = "NISU USAGLAŠENI";
				} else if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-") {
					$usaglasenost = "DA";
				} else {
					$usaglasenost = "-";
				}

			}
			
            if (!isset($finalusaglasenost)) {
                $finalusaglasenost = "su USAGLAŠENI";
            }
            if ($prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-") {
				if($referentnavrijednost['referentnevrijednosti_id'] == 58 || $referentnavrijednost['referentnevrijednosti_id'] == 59 || $referentnavrijednost['referentnevrijednosti_id'] == 60 || $referentnavrijednost['referentnevrijednosti_id'] == 61 || $referentnavrijednost['referentnevrijednosti_id'] == 62 || $referentnavrijednost['referentnevrijednosti_id'] == 67 || $referentnavrijednost['referentnevrijednosti_id'] == 68 || $referentnavrijednost['referentnevrijednosti_id'] == 69 || $referentnavrijednost['referentnevrijednosti_id'] == 70 || $referentnavrijednost['referentnevrijednosti_id'] == 71 || $referentnavrijednost['referentnevrijednosti_id'] == 72 || $referentnavrijednost['referentnevrijednosti_id'] == 73 || $referentnavrijednost['referentnevrijednosti_id'] == 74 || $referentnavrijednost['referentnevrijednosti_id'] == 75 || $referentnavrijednost['referentnevrijednosti_id'] == 76){
					$pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, $trecemjerenje, number_format((float) $srednjavrijednost, 2, '.', ''), number_format((float) $apsolutnagreska, 2, '.', ''), number_format((float) $relativnagreska, 2, '.', ''), $referentnavrijednost['referentnevrijednosti_odstupanje'], $usaglasenost));
                }else if($referentnavrijednost['referentnevrijednosti_id'] == 63 || $referentnavrijednost['referentnevrijednosti_id'] == 64 || $referentnavrijednost['referentnevrijednosti_id'] == 65 || $referentnavrijednost['referentnevrijednosti_id'] == 66){
                    $pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, $trecemjerenje, number_format((float) $srednjavrijednost, 2, '.', ''), number_format((float) $apsolutnagreska, 2, '.', ''), number_format((float) $relativnagreska, 2, '.', ''), round($referentnavrijednost['referentnevrijednosti_odstupanje'], 0), $usaglasenost));
				}else{
					$pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, $trecemjerenje, number_format((float) $srednjavrijednost, 2, '.', ''), number_format((float) $apsolutnagreska, 2, '.', ''), number_format((float) $relativnagreska, 2, '.', ''), round($referentnavrijednost['referentnevrijednosti_odstupanje'], 0), $usaglasenost));
					}
            } else {
                // Vrsta 3 (defibrilator): 3.php koristi absolute-up-to-100 i relative-over-100 – oba "NE ISPISUJEMO NIŠTA" u else; red ne prikazujemo u mješovitom slučaju
                if ($vrstauredjaja['vrsteuredjaja_id'] != 3) {
                    $pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, $trecemjerenje, "-", "-", "-", "-", $usaglasenost));
                }
            }
        }

    } else {
        $pdf->Cell(30, 10, "Pritisak[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(40, 5, "p1[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(40, 5, "p2[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(35, 5, "Razlika p1 - p2", 'L,T,R', 0, 'C', 1);
        $pdf->Cell(35, 5, "Stopa ispuštanja", 'L,T,R', 0, 'C', 1);
        $pdf->Ln(5);
        $pdf->Cell(30, 5, "", '', 0, 'C', 0);
        $pdf->Cell(40, 5, "1. očitavanje", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(40, 5, "Očitavanje nakon 5 minuta", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(35, 5, "[" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,R,B', 0, 'C', 1);
        $pdf->Cell(35, 5, "pritiska [" . $mjernavelicina['mjernevelicine_jedinica'] . "]", 'L,R,B', 0, 'C', 1);
        $pdf->Ln(5);
        $referentnevrijednosti = new allObjectsBy;
        $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by("referentnevrijednosti", "referentnevrijednosti_mjernavelicinaid", $mjernavelicina['mjernevelicine_id'], "referentnevrijednosti_referentnavrijednost", "ASC");
        $pdf->SetWidths(array(30, 40, 40, 35, 35));
        $pdf->SetAligns(array("C", "C", "C", "C", "C", "C", "C", "C", "C"));
        $max3 = [];
        foreach ($referentnevrijednosti as $referentnavrijednost) {
            $prvomjerenje = $drugomjerenje = '-';
            $rezultatimjerenja = new allResults;
            $rezultatimjerenja = $rezultatimjerenja->fetch_all_results($izvjestaj['izvjestaji_id'], $mjernavelicina['mjernevelicine_id'], $referentnavrijednost['referentnevrijednosti_id']);
            foreach ($rezultatimjerenja as $rezultatmjerenja) {
                switch ($rezultatmjerenja) {
                    case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 1:
                        $prvomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                        break;
                    case $rezultatmjerenja['rezultatimjerenja_brojmjerenja'] == 2:
                        $drugomjerenje = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'];
                        break;
                }
            }
            if ($prvomjerenje != "-" && $drugomjerenje != "-" && $prvomjerenje != "--" && $drugomjerenje != "--") {
                $razp1p2 = abs(abs($drugomjerenje) - abs($prvomjerenje));
                $stoIsp = $razp1p2 / 5;
                array_push($max3, $stoIsp);
                $pdf->Row(array(round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1), $prvomjerenje, $drugomjerenje, number_format((float) $razp1p2, 2, '.', ''), number_format((float) $stoIsp, 2, '.', '')));
            } else {
                $razp1p2 = "-";
                $stoIsp = "-";
                // Kao u mpdf-includes (11.php): red prikazujemo samo kad su oba mjerenja validna; inače red ne prikazujemo
            }
        }
    }
    $i++;
}
$pdf->Ln(5);

$pdf->CheckPageBreak(40);

//set bold font
$pdf->AddFont('Calibri-Bold', '', 'calibri-bold.ttf', false);
$pdf->SetFont('Calibri-Bold', '', 9);

if ($vrstauredjaja['vrsteuredjaja_id'] != 11 && $vrstauredjaja['vrsteuredjaja_id'] != 12 && $vrstauredjaja['vrsteuredjaja_id'] != 13 && $vrstauredjaja['vrsteuredjaja_id'] != 14 && $vrstauredjaja['vrsteuredjaja_id'] != 49 && $vrstauredjaja['vrsteuredjaja_id'] != 50) {
    //red 15
    $pdf->MultiCell(180, 5, "Napomena:", 0, 'J');
    $pdf->MultiCell(180, 5, $izvjestaj['izvjestaji_napomena'], 1, 'J');
    $pdf->Ln(5);
    //naslov 2
    $pdf->Cell(180, 0, '5. Izjava o usaglašenosti', 0, 0, 'C');
    $pdf->Ln(5);
} else if ($mjernavelicina['mjernevelicine_id'] == 31 || $mjernavelicina['mjernevelicine_id'] == 33 || $mjernavelicina['mjernevelicine_id'] == 35 || $mjernavelicina['mjernevelicine_id'] == 37 || $vrstauredjaja['vrsteuredjaja_id'] == 49 || $vrstauredjaja['vrsteuredjaja_id'] == 50) {

    $rezultatmjerenja_ = new allResultsWithSort;
    $rezultatmjerenja_ = $rezultatmjerenja_->fetch_all_results_with_sort($izvjestaj['izvjestaji_id'], 0, 0, "rezultatimjerenja_brojmjerenja", "ASC");

    $finalusaglasenost = "USAGLAŠENI su";

    $pdf->Cell(180, 0, '5. Rezultati ispitivanja mjerila', 0, 0, 'C');
    $pdf->Ln(5);

    $pdf->Cell(50, 5, "", '', 0, 'C', 0);
    $pdf->Cell(40, 5, "Maksimalno odstupanje", 'L,T,R,B', 0, 'C', 1);
    $pdf->Cell(60, 5, "Maksimalno dozvoljeno odstupanje", 'L,T,R,B', 0, 'C', 1);
    $pdf->Cell(30, 5, "Zadovoljava", 'L,T,R,B', 0, 'C', 1);
    $pdf->Ln(5);

    $pdf->Cell(50, 5, "Tačnost pokazivanja", 'L,T,R,B', 0, 'C', 1);
    $pdf->Cell(40, 5, (count($max1) > 0 ? number_format((float) max($max1), 2, '.', '') : '-'), 'L,T,R,B', 0, 'C', 0);
    if($vrstauredjaja['vrsteuredjaja_id'] == 49 || $vrstauredjaja['vrsteuredjaja_id'] == 50 || $vrstauredjaja['vrsteuredjaja_id'] == 11 || $vrstauredjaja['vrsteuredjaja_id'] == 12 || $vrstauredjaja['vrsteuredjaja_id'] == 13 || $vrstauredjaja['vrsteuredjaja_id'] == 14){
        $pdf->Cell(60, 5, "3", 'L,T,R,B', 0, 'C', 0);
        if (max($max1) > 3) {
            $zadovoljava = "NE";
            $finalusaglasenost = "NISU USAGLAŠENI";
        } else {
            $zadovoljava = "DA";
        }
    }else{
        $pdf->Cell(60, 5, "4", 'L,T,R,B', 0, 'C', 0);
        if (max($max1) > 4) {
            $zadovoljava = "NE";
            $finalusaglasenost = "NISU USAGLAŠENI";
        } else {
            $zadovoljava = "DA";
        }
    }
    $pdf->Cell(30, 5, $zadovoljava, 'L,T,R,B', 0, 'C', 0);
    $pdf->Ln(5);
    if ($mjernavelicina['mjernevelicine_id'] == 31 || $mjernavelicina['mjernevelicine_id'] == 33) {
        $pdf->Cell(50, 5, "Histerezis", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(40, 5, (count($max2) > 0 ? number_format((float) max($max2), 2, '.', '') : '-'), 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(60, 5, "4", 'L,T,R,B', 0, 'C', 0);
        if (max($max2) > 4) {
            $zadovoljava = "NE";
            $finalusaglasenost = "NISU USAGLAŠENI";
        } else {
            $zadovoljava = "DA";
        }
        $pdf->Cell(30, 5, $zadovoljava, 'L,T,R,B', 0, 'C', 0);
        $pdf->Ln(5);
    } else if($vrstauredjaja['vrsteuredjaja_id'] != 49 && $vrstauredjaja['vrsteuredjaja_id'] != 50) {
        $pdf->Cell(50, 5, "Uticaj žive na rad mjerila", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(40, 5, number_format((float) $rezultatmjerenja_[1]["rezultatimjerenja_rezultatmjerenja"], 2, '.', ''), 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(60, 5, "1.5", 'L,T,R,B', 0, 'C', 0);
        if ((float) $rezultatmjerenja_[1]["rezultatimjerenja_rezultatmjerenja"] > 1.5) {
            $zadovoljava = "NE";
            $finalusaglasenost = "NISU USAGLAŠENI";
        } else {
            $zadovoljava = "DA";
        }
        $pdf->Cell(30, 5, $zadovoljava, 'L,T,R,B', 0, 'C', 0);
        $pdf->Ln(5);
    }
    $pdf->Cell(50, 5, "Ispitivanje curenja zraka", 'L,T,R,B', 0, 'C', 1);
    $pdf->Cell(40, 5, (isset($max3) && count($max3) > 0 ? number_format((float) max($max3), 2, '.', '') : '-'), 'L,T,R,B', 0, 'C', 0);

    if($vrstauredjaja['vrsteuredjaja_id'] == 49 || $vrstauredjaja['vrsteuredjaja_id'] == 50){
        $pdf->Cell(60, 5, "6", 'L,T,R,B', 0, 'C', 0);
        if (max($max3) > 6) {
            $zadovoljava = "NE";
            $finalusaglasenost = "NISU USAGLAŠENI";
        } else {
            $zadovoljava = "DA";
        }
    }else{
        $pdf->Cell(60, 5, "4", 'L,T,R,B', 0, 'C', 0);
        if (max($max3) > 4) {
            $zadovoljava = "NE";
            $finalusaglasenost = "NISU USAGLAŠENI";
        } else {
            $zadovoljava = "DA";
        }
    }
    $pdf->Cell(30, 5, $zadovoljava, 'L,T,R,B', 0, 'C', 0);
    $pdf->Ln(5);

    //var_dump($rezultatmjerenja_);

    $pdf->Cell(50, 5, "Ispitivanja ventila brzog ispusta", 'L,T,R,B', 0, 'C', 1);
    $pdf->Cell(40, 5, number_format((float) $rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"], 2, '.', ''), 'L,T,R,B', 0, 'C', 0);
    if ($vrstauredjaja['vrsteuredjaja_id'] == 13 || $vrstauredjaja['vrsteuredjaja_id'] == 14 || $vrstauredjaja['vrsteuredjaja_id'] == 50) {
        $pdf->Cell(60, 5, "5", 'L,T,R,B', 0, 'C', 0);
    } else {
        $pdf->Cell(60, 5, "10", 'L,T,R,B', 0, 'C', 0);
    }

    if ($vrstauredjaja['vrsteuredjaja_id'] == 13 || $vrstauredjaja['vrsteuredjaja_id'] == 14 || $vrstauredjaja['vrsteuredjaja_id'] == 50) {
        if ($rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"] > 5) {
            $zadovoljava = "NE";
            $finalusaglasenost = "NISU USAGLAŠENI";
        } else {
            $zadovoljava = "DA";
        }
    } else {
        if ($rezultatmjerenja_[0]["rezultatimjerenja_rezultatmjerenja"] > 10) {
            $zadovoljava = "NE";
            $finalusaglasenost = "NISU USAGLAŠENI";
        } else {
            $zadovoljava = "DA";
        }
    }

    $pdf->Cell(30, 5, $zadovoljava, 'L,T,R,B', 0, 'C', 0);
    $pdf->Ln(5);

    if($vrstauredjaja['vrsteuredjaja_id'] == 49 || $vrstauredjaja['vrsteuredjaja_id'] == 50){
        $pdf->Cell(50, 5, "Ponovljivost", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(40, 5, number_format((float) max(array($devijacijaSistolicni, $devijacijaDistolicni)), 2, '.', ''), 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(60, 5, "3", 'L,T,R,B', 0, 'C', 0);

        if (max(array($devijacijaSistolicni, $devijacijaDistolicni)) > 3) {
            $zadovoljava = "NE";
            $finalusaglasenost = "NISU USAGLAŠENI";
        } else {
            $zadovoljava = "DA";
        }

        $pdf->Cell(30, 5, $zadovoljava, 'L,T,R,B', 0, 'C', 0);
        $pdf->Ln(10);
    }

    if ($vrstauredjaja['vrsteuredjaja_id'] == 12 || $vrstauredjaja['vrsteuredjaja_id'] == 14) {

        $pdf->Cell(35, 5, "", '', 0, 'C', 0);
        $pdf->Cell(80, 5, "Pregled", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(30, 5, "Zadovoljava", 'L,T,R,B', 0, 'C', 1);
        $pdf->Cell(35, 5, "", '', 0, 'C', 0);
        $pdf->Ln(5);

        if ($rezultatmjerenja_[2]["rezultatimjerenja_rezultatmjerenja"] == 1) {
            $rez2 = "DA";
        } else {
            $rez2 = "NE";
        }

        $pdf->Cell(35, 5, "", '', 0, 'C', 0);
        $pdf->Cell(80, 5, "Ispitivanje curenja žive", 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(30, 5, $rez2, 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(35, 5, "", '', 0, 'C', 0);
        $pdf->Ln(5);

        if ($rezultatmjerenja_[3]["rezultatimjerenja_rezultatmjerenja"] == 1) {
            $rez3 = "DA";
        } else {
            $rez3 = "NE";
        }

        $pdf->Cell(35, 5, "", '', 0, 'C', 0);
        $pdf->Cell(80, 5, "Ispitivanje mehanizma za zaključavanje žive", 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(30, 5, $rez3, 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(35, 5, "", '', 0, 'C', 0);
        $pdf->Ln(5);

        if ($rezultatmjerenja_[4]["rezultatimjerenja_rezultatmjerenja"] == 1) {
            $rez4 = "DA";
        } else {
            $rez4 = "NE";
        }

        $pdf->Cell(35, 5, "", '', 0, 'C', 0);
        $pdf->Cell(80, 5, "Ispitivanje kvaliteta žive", 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(30, 5, $rez4, 'L,T,R,B', 0, 'C', 0);
        $pdf->Cell(35, 5, "", '', 0, 'C', 0);
        $pdf->Ln(5);
        $pdf->Ln(10);
    }
    //red 15
    $pdf->MultiCell(180, 5, "Napomena:", 0, 'J');
    $pdf->MultiCell(180, 5, $izvjestaj['izvjestaji_napomena'], 1, 'J');
    $pdf->Ln(5);
    $pdf->CheckPageBreak(30);
    //naslov 2
    $pdf->Cell(180, 0, '6. Izjava o usaglašenosti', 0, 0, 'C');
    $pdf->Ln(5);
} else {
    //red 15
    $pdf->MultiCell(180, 5, "Napomena:", 0, 'J');
    $pdf->MultiCell(180, 5, $izvjestaj['izvjestaji_napomena'], 1, 'J');
    $pdf->Ln(5);
    //naslov 2
    $pdf->Cell(180, 0, '6. Izjava o usaglašenosti', 0, 0, 'C');
    $pdf->Ln(5);
}
//$finalusaglasenost = 0;
//set regular font
$pdf->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', false);
$pdf->SetFont('Calibri-Regular', '', 9);
$pdf->MultiCell(180, 5, "Rezultati inspekcije mjerila " . $finalusaglasenost . " sa propisanim opsegom dozvoljenih odstupanja u skladu sa gore navedenim Pravilnikom. Na osnovu rezultata inspekcije mjerilo je označeno inspekcijskom oznakom - markicom.", 0, 'C');
$pdf->MultiCell(180, 5, "Rezultati inspekcije se odnose isključivo na dati predmet u trenutku inspekcije.", 0, 'C');
$pdf->MultiCell(180, 5, "Izvještaj o inspekciji ne smije se reprodukovati osim u cjelini.", 0, 'C');
$pdf->Ln(5);

$pdf->Cell(60, 5, 'Inspekciju izvršio i izvještaj izradio:', 0, 0, 'L');
$pdf->Cell(60, 5, '', 0, 0, 'C');
$pdf->Cell(60, 5, 'Izvještaj ovjerio:', 0, 0, 'L');
$pdf->Ln(5);

$inspekcijuizvrsio = new singleObject;
$inspekcijuizvrsio = $inspekcijuizvrsio->fetch_single_object("kontrolori", "kontrolori_id", $izvjestaj['izvjestaji_izvrsioid']);

$inspekcijuovjerio = new singleObject;
$inspekcijuovjerio = $inspekcijuovjerio->fetch_single_object("kontrolori", "kontrolori_id", $izvjestaj['izvjestaji_ovjerioid']);

$pdf->Cell(60, 5, 'Ime i prezime: ' . $inspekcijuizvrsio['kontrolori_ime'] . " " . $inspekcijuizvrsio['kontrolori_prezime'], 0, 0, 'L');
$pdf->Cell(60, 5, '', 0, 0, 'C');
$pdf->Cell(60, 5, 'Ime i prezime: ' . $inspekcijuovjerio['kontrolori_ime'] . " " . $inspekcijuovjerio['kontrolori_prezime'], 0, 0, 'L');
$pdf->Ln(5);

$pdf->Cell(60, 5, 'Datum: ' . date('d.m.Y.', strtotime($izvjestaj['izvjestaji_izvrsiodadatum'])), 0, 0, 'L');
$pdf->Cell(60, 5, '', 0, 0, 'C');
$pdf->Cell(60, 5, 'Datum: ' . date('d.m.Y.', strtotime($izvjestaj['izvjestaji_ovjeriodatum'])), 0, 0, 'L');
$pdf->Ln(10);

$pdf->Cell(60, 5, 'Potpis: _________________', 0, 0, 'L');
$pdf->Cell(60, 5, '', 0, 0, 'C');
$pdf->Cell(60, 5, 'Potpis: _________________', 0, 0, 'L');
$pdf->Ln(5);

$pdf->Output('I', $naziv . ".pdf");

//FOOTER
include_once ('includes/footer.php');

?>