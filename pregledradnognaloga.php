<?php
//HEAD
include_once ('connection.php');
include_once ('class/getObject.php');
require ('fpdf/tfpdf.php');


//kupimo radni nalog
$radninalog = new singleObject;
$radninalog = $radninalog->fetch_single_object("radninalozi", "radninalozi_id", $_GET['radninalog']);
//generišemo sve varijable iz radnog naloga
foreach ($radninalog as $key => $value) {
    if (gettype($key) != "integer") {
        $$key = $value;
        //var_dump($key . "=" . $$key);
    }
}
//generišemo naziv fajla
$naziv = $radninalog['radninalozi_timestamp'];
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
        //kupimo radni nalog
        $radninalog = new singleObject;
        $radninalog = $radninalog->fetch_single_object("radninalozi", "radninalozi_id", $_GET['radninalog']);
        //kupimo brojač
        $brojac = new singleObject;
        $brojac = $brojac->fetch_single_object("brojacirn", "brojacirn_id", $radninalog['radninalozi_brojacrnid']);
        //generišemo varijable iz brojača
        foreach ($brojac as $key => $value) {
            if (gettype($key) != "integer") {
                $$key = $value;
                //var_dump($key . "=" . $$key);
            }
        }

        // Logo
        $this->Image('images/logoBez.png', 20, 10, 50);
        // Font
        $this->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', true);
        $this->AddFont('Calibri-Bold', '', 'calibri-bold.ttf', true);
        $this->SetFont('Calibri-Bold', '', 12);
        // Indent
        $this->Cell(65);
        // Data
        $this->Cell(120, 5, 'PR-11 PROCEDURA ZA INSPEKCIJU MJERILA U ZDRAVSTVU', 0, 0, 'C');
        $this->Ln(0);
        $this->Cell(65);
        $this->SetFont('Calibri-Regular', '', 12);
        $this->Cell(120, 20, $brojacirn_prefiks . 'RADNI NALOG ZA INSPEKCIJU MJERILA', 0, 0, 'C');
        $this->Line(20, 30, 210 - 20, 30);
        $this->Line(20, 30.3, 210 - 20, 30.3);
        $this->Line(20, 30.6, 210 - 20, 30.6);
        $this->Line(20, 30.9, 210 - 20, 30.9);
        $this->Line(20, 32, 210 - 20, 32);
        $this->Ln(0);
        //$this->Ln(0);
        //$this->Cell(35);
        //$this->Cell(155, 20, 'office@normalab.ba', 0, 0, 'R');
        //$this->Ln(0);
        //$this->Cell(35);
        //$this->Cell(150, 30, '+387 66 76 67 81', 0, 0, 'R');
        //$this->Ln(30);
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
        //$this->Cell(0, 10, 'ŽR: 5551000057550456 Nova banka a.d. Banja Luka ČĆ', 0, 0, 'R');
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
            $this->Rect($x, $y, $w, $h);
            // Print the text
            $this->MultiCell($w, 7, $data[$i], 0, $a);
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

}

$pdf = new PDF();
$pdf->AddPage();
$pdf->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', false);
$pdf->SetFont('Calibri-Regular', '', 12);
//$pdf->SetFont('Arial', '', 12);
// Table with 20 rows and 4 columns
$pdf->SetWidths(array(50, 120));
$pdf->SetAligns(array("C", "L"));
$pdf->SetLeftMargin(20);
$pdf->Ln(30);

//1. red - broj radnog naloga
$pdf->Row(array("Broj radnog naloga za inspekciju:", $radninalozi_broj));

//2.red podnosilac zahtjeva
//get klijent
$klijent = new singleObject;
$klijent = $klijent->fetch_single_object("klijenti", "klijenti_id", $radninalozi_klijentid);

$pdf->Row(array("Podnosilac zahtjeva:", $klijent['klijenti_naziv']));

//3. red adresa podnosioca zahtjeva
$pdf->Row(array("Adresa:", $klijent['klijenti_adresa']));

//4. red broj zahtjeva za inspekciju
$pdf->Row(array("Broj zahtjeva za inspekciju:", $radninalozi_brojzahtjeva));

//5. red predmet inspekcije
$vrstauredjaja = new singleObject;
$vrstauredjaja = $vrstauredjaja->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $radninalozi_vrstauredjajaid);
$pdf->Row(array("Predmet inspekcije:", $vrstauredjaja['vrsteuredjaja_naziv']));

//6.red vrsta inspekcije
//get vrsta inspekcije
$metodainspekcije = new singleObject;
$metodainspekcije = $metodainspekcije->fetch_single_object("metodeinspekcije", "metodeinspekcije_id", $radninalozi_metodainspekcijeid);
$pdf->Row(array("Vrsta inspekcije:", $metodainspekcije['metodeinspekcije_naziv']));

//7.red vrsta inspekcije
//get mjerilo
$mjerilo = new singleObject;
$mjerilo = $mjerilo->fetch_single_object("mjerila", "mjerila_id", $radninalozi_mjeriloid);
$pdf->Row(array("Broj mjerila za inspekciju:", $mjerilo['mjerila_serijskibroj']));

//8.red kontrolor
//get kontrolor
$kontrolor = new singleObject;
$kontrolor = $kontrolor->fetch_single_object("kontrolori", "kontrolori_id", $radninalozi_kontrolorid);
$pdf->Row(array("Kontrolor:", $kontrolor['kontrolori_ime'] . " " . $kontrolor['kontrolori_prezime']));

//9. red datum završetka
$radninalozi_datumzavrsetka = date('d.m.Y.', strtotime($radninalozi_datumzavrsetka));
$pdf->Row(array("Očekivani završetak inspekcije:", $radninalozi_datumzavrsetka));

//10. red posebni zahtjevi
$pdf->SetAligns(array("L", "L"));
$pdf->SetWidths(array(170, 0));
$pdf->Row2(array("Posebni zahtjevi:" . $radninalozi_posebnizahtjevi));

//BREAK
$pdf->Ln(15);

//11. red potpisi
$pdf->SetWidths(array(50, 40, 40, 40));
$pdf->SetAligns(array("L", "C", "C", "C"));
$pdf->Row1(array("", "Radni nalog otvorio:", "Radni nalog primio:", "Radni nalog zatvorio:"));
//get otvorio
$otvorio = new singleObject;
$otvorio = $otvorio->fetch_single_object("kontrolori", "kontrolori_id", $radninalozi_otvorioid);
//get primio
$primio = new singleObject;
$primio = $primio->fetch_single_object("kontrolori", "kontrolori_id", $radninalozi_primioid);
//get zatvorio
$zatvorio = new singleObject;
$zatvorio = $zatvorio->fetch_single_object("kontrolori", "kontrolori_id", $radninalozi_zatvorioid);
$pdf->SetAligns(array("L", "C", "C", "C"));
$pdf->Row1(array("Ime i prezime:", $otvorio['kontrolori_ime'] . " " . $otvorio['kontrolori_prezime'], $primio['kontrolori_ime'] . " " . $primio['kontrolori_prezime'], $zatvorio['kontrolori_ime'] . " " . $zatvorio['kontrolori_prezime']));
$pdf->SetAligns(array("L", "L", "L", "L"));
$pdf->Ln(5);
$pdf->Row1(array("Potpis:", "_________________", "_________________", "_________________"));
$pdf->Ln(5);
$pdf->Row1(array("Datum:", "_________________", "_________________", "_________________"));

$pdf->Output('D', $naziv . ".pdf");

//FOOTER
include_once ('includes/footer.php');

?>