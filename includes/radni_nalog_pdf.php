<?php

require_once __DIR__ . '/../fpdf/tfpdf.php';

if (!class_exists('NormaRadniNalogPdf', false)) {
    class NormaRadniNalogPdf extends tFPDF
    {
        /** @var int */
        public $radninalogId = 0;

        public function setRadniNalogId(int $id): void
        {
            $this->radninalogId = $id;
        }

        function Header()
        {
            $radninalog = new singleObject;
            $radninalog = $radninalog->fetch_single_object('radninalozi', 'radninalozi_id', $this->radninalogId);
            $brojac = new singleObject;
            $brojac = $brojac->fetch_single_object('brojacirn', 'brojacirn_id', $radninalog['radninalozi_brojacrnid']);
            foreach ($brojac as $key => $value) {
                if (gettype($key) != 'integer') {
                    $$key = $value;
                }
            }

            $this->Image(__DIR__ . '/../images/logoBez.png', 20, 10, 50);
            $this->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', true);
            $this->AddFont('Calibri-Bold', '', 'calibri-bold.ttf', true);
            $this->SetFont('Calibri-Bold', '', 12);
            $this->Cell(65);
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
        }

        function Footer()
        {
            $this->SetY(-20);
            $this->SetFont('Calibri-Regular', '', 10);
        }

        protected $widths;
        protected $aligns;

        function SetWidths($w)
        {
            $this->widths = $w;
        }

        function SetAligns($a)
        {
            $this->aligns = $a;
        }

        function Row($data)
        {
            $nb = 0;
            for ($i = 0; $i < count($data); $i++) {
                $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
            }
            $h = 7 * $nb;
            $this->CheckPageBreak($h);
            for ($i = 0; $i < count($data); $i++) {
                $w = $this->widths[$i];
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                $x = $this->GetX();
                $y = $this->GetY();
                $this->Rect($x, $y, $w, $h);
                $this->MultiCell($w, 7, $data[$i], 0, $a);
                $this->SetXY($x + $w, $y);
            }
            $this->Ln($h);
        }

        function Row1($data)
        {
            $nb = 0;
            for ($i = 0; $i < count($data); $i++) {
                $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
            }
            $h = 7 * $nb;
            $this->CheckPageBreak($h);
            for ($i = 0; $i < count($data); $i++) {
                $w = $this->widths[$i];
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                $x = $this->GetX();
                $y = $this->GetY();
                $this->MultiCell($w, 7, $data[$i], 0, $a);
                $this->SetXY($x + $w, $y);
            }
            $this->Ln($h);
        }

        function Row2($data)
        {
            $nb = 0;
            for ($i = 0; $i < count($data); $i++) {
                $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
            }
            if ($nb <= 8) {
                $h = 7 * 8;
            } else {
                $h = 7 * $nb;
            }

            $this->CheckPageBreak($h);
            for ($i = 0; $i < count($data); $i++) {
                $w = $this->widths[$i];
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                $x = $this->GetX();
                $y = $this->GetY();
                $this->Rect($x, $y, $w, $h);
                $this->MultiCell($w, 7, $data[$i], 0, $a);
                $this->SetXY($x + $w, $y);
            }
            $this->Ln($h);
        }

        function CheckPageBreak($h)
        {
            if ($this->GetY() + $h > $this->PageBreakTrigger) {
                $this->AddPage($this->CurOrientation);
            }
        }

        function NbLines($w, $txt)
        {
            if (!isset($this->CurrentFont)) {
                $this->Error('No font has been set');
            }
            $cw = $this->CurrentFont['cw'];
            if ($w == 0) {
                $w = $this->w - $this->rMargin - $this->x;
            }
            $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
            $s = str_replace("\r", '', (string) $txt);
            $nb = strlen($s);
            if ($nb > 0 && $s[$nb - 1] == "\n") {
                $nb--;
            }
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
                if ($c == ' ') {
                    $sep = $i;
                }
                $l += 400;
                if ($l > $wmax) {
                    if ($sep == -1) {
                        if ($i == $j) {
                            $i++;
                        }
                    } else {
                        $i = $sep + 1;
                    }
                    $sep = -1;
                    $j = $i;
                    $l = 0;
                    $nl++;
                } else {
                    $i++;
                }
            }
            return $nl;
        }
    }
}

if (!function_exists('norma_radni_nalog_pdf_naziv')) {
    function norma_radni_nalog_pdf_naziv(array $radninalog): string
    {
        $naziv = $radninalog['radninalozi_timestamp'];
        $naziv = str_replace(' ', '-', $naziv);
        $naziv = str_replace(':', '-', $naziv);
        $naziv = str_replace('-', '', $naziv);

        return substr($naziv, 6, 2) . '-' . substr($naziv, 4, 2) . '-' . substr($naziv, 0, 4) . '-'
            . substr($naziv, 8, 2) . '-' . substr($naziv, 10, 2) . '-' . substr($naziv, 12, 2);
    }
}

if (!function_exists('norma_radni_nalog_pdf_render')) {
    function norma_radni_nalog_pdf_render(NormaRadniNalogPdf $pdf, int $radninalogId): void
    {
        $radninalog = new singleObject;
        $radninalog = $radninalog->fetch_single_object('radninalozi', 'radninalozi_id', $radninalogId);
        if (!$radninalog) {
            return;
        }

        foreach ($radninalog as $key => $value) {
            if (gettype($key) != 'integer') {
                $$key = $value;
            }
        }

        $pdf->SetLeftMargin(20);
        $pdf->Ln(30);
        $pdf->SetWidths(array(50, 120));
        $pdf->SetAligns(array('C', 'L'));

        $pdf->Row(array('Broj radnog naloga za inspekciju:', $radninalozi_broj));

        $klijent = new singleObject;
        $klijent = $klijent->fetch_single_object('klijenti', 'klijenti_id', $radninalozi_klijentid);
        $pdf->Row(array('Podnosilac zahtjeva:', $klijent['klijenti_naziv']));
        $pdf->Row(array('Adresa:', $klijent['klijenti_adresa']));
        $pdf->Row(array('Broj zahtjeva za inspekciju:', $radninalozi_brojzahtjeva));

        $vrstauredjaja = new singleObject;
        $vrstauredjaja = $vrstauredjaja->fetch_single_object('vrsteuredjaja', 'vrsteuredjaja_id', $radninalozi_vrstauredjajaid);
        $pdf->Row(array('Predmet inspekcije:', $vrstauredjaja['vrsteuredjaja_naziv']));

        $metodainspekcije = new singleObject;
        $metodainspekcije = $metodainspekcije->fetch_single_object('metodeinspekcije', 'metodeinspekcije_id', $radninalozi_metodainspekcijeid);
        $pdf->Row(array('Vrsta inspekcije:', $metodainspekcije['metodeinspekcije_naziv']));

        $mjerilo = new singleObject;
        $mjerilo = $mjerilo->fetch_single_object('mjerila', 'mjerila_id', $radninalozi_mjeriloid);
        $pdf->Row(array('Broj mjerila za inspekciju:', $mjerilo['mjerila_serijskibroj']));

        $kontrolor = new singleObject;
        $kontrolor = $kontrolor->fetch_single_object('kontrolori', 'kontrolori_id', $radninalozi_kontrolorid);
        $pdf->Row(array('Kontrolor:', $kontrolor['kontrolori_ime'] . ' ' . $kontrolor['kontrolori_prezime']));

        $radninalozi_datumzavrsetka = date('d.m.Y.', strtotime($radninalozi_datumzavrsetka));
        $pdf->Row(array('Očekivani završetak inspekcije:', $radninalozi_datumzavrsetka));

        $pdf->SetAligns(array('L', 'L'));
        $pdf->SetWidths(array(170, 0));
        $pdf->Row2(array('Posebni zahtjevi:' . $radninalozi_posebnizahtjevi));

        $pdf->Ln(15);
        $pdf->SetWidths(array(50, 40, 40, 40));
        $pdf->SetAligns(array('L', 'C', 'C', 'C'));
        $pdf->Row1(array('', 'Radni nalog otvorio:', 'Radni nalog primio:', 'Radni nalog zatvorio:'));

        $otvorio = new singleObject;
        $otvorio = $otvorio->fetch_single_object('kontrolori', 'kontrolori_id', $radninalozi_otvorioid);
        $primio = new singleObject;
        $primio = $primio->fetch_single_object('kontrolori', 'kontrolori_id', $radninalozi_primioid);
        $zatvorio = new singleObject;
        $zatvorio = $zatvorio->fetch_single_object('kontrolori', 'kontrolori_id', $radninalozi_zatvorioid);

        $pdf->SetAligns(array('L', 'C', 'C', 'C'));
        $pdf->Row1(array(
            'Ime i prezime:',
            $otvorio['kontrolori_ime'] . ' ' . $otvorio['kontrolori_prezime'],
            $primio['kontrolori_ime'] . ' ' . $primio['kontrolori_prezime'],
            $zatvorio['kontrolori_ime'] . ' ' . $zatvorio['kontrolori_prezime'],
        ));
        $pdf->SetAligns(array('L', 'L', 'L', 'L'));
        $pdf->Ln(5);
        $pdf->Row1(array('Potpis:', '_________________', '_________________', '_________________'));
        $pdf->Ln(5);
        $pdf->Row1(array('Datum:', '_________________', '_________________', '_________________'));
    }
}
