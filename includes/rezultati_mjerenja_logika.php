<?php
/**
 * Zajednička logika za rezultate mjerenja (jedan red tabele – standardna tabela s 3 mjerenja).
 * Očekuje: $izvjestaj, $mjernavelicina, $referentnavrijednost (niz iz baze).
 * Opcionalno: $vrstauredjaja (za posebne formatiranje), $rezultati_mjerenja_odstupanje_decimals (0|1|3 za zaokruživanje odstupanja pri usporedbi, npr. za mpdf scriptove).
 * Postavlja: $prvomjerenje, $drugomjerenje, $trecemjerenje, $srednjavrijednost, $apsolutnagreska, $relativnagreska, $usaglasenost, $finalusaglasenost.
 */

$prvomjerenje = null;
$drugomjerenje = null;
$trecemjerenje = null;

$rezultatimjerenja = new allResults;
$rezultatimjerenja = $rezultatimjerenja->fetch_all_results(
    $izvjestaj['izvjestaji_id'],
    $mjernavelicina['mjernevelicine_id'],
    $referentnavrijednost['referentnevrijednosti_id']
);

foreach ($rezultatimjerenja as $rezultatmjerenja) {
    $broj = (int)($rezultatmjerenja['rezultatimjerenja_brojmjerenja'] ?? 0);
    $vrijednost = $rezultatmjerenja['rezultatimjerenja_rezultatmjerenja'] ?? null;
    if ($broj === 1) {
        $prvomjerenje = $vrijednost;
    } elseif ($broj === 2) {
        $drugomjerenje = $vrijednost;
    } elseif ($broj === 3) {
        $trecemjerenje = $vrijednost;
    }
}

if (!isset($prvomjerenje)) {
    $prvomjerenje = '-';
}
if (!isset($drugomjerenje)) {
    $drugomjerenje = '-';
}
if (!isset($trecemjerenje)) {
    $trecemjerenje = '-';
}

$sveBrojcano = (
    $prvomjerenje !== '-' && $prvomjerenje !== '--' &&
    $drugomjerenje !== '-' && $drugomjerenje !== '--' &&
    $trecemjerenje !== '-' && $trecemjerenje !== '--'
);

if ($sveBrojcano) {
    $srednjavrijednost = round(($prvomjerenje + $drugomjerenje + $trecemjerenje) / 3, 2);
    $apsolutnagreska = abs($srednjavrijednost - (float)$referentnavrijednost['referentnevrijednosti_referentnavrijednost']);
    $refVr = (float)$referentnavrijednost['referentnevrijednosti_referentnavrijednost'];
    if ($refVr == 0) {
        $relativnagreska = abs(round(($apsolutnagreska / 1) * 100, 2));
    } else {
        $relativnagreska = abs(round(($apsolutnagreska / $refVr) * 100, 2));
    }
} else {
    $srednjavrijednost = '-';
    $apsolutnagreska = '-';
    $relativnagreska = '-';
}

$odstupanje_decimals = isset($rezultati_mjerenja_odstupanje_decimals) ? (int)$rezultati_mjerenja_odstupanje_decimals : 0;
$odstupanje_decimals = in_array($odstupanje_decimals, [0, 1, 3], true) ? $odstupanje_decimals : 0;

$refId = (int)($referentnavrijednost['referentnevrijednosti_id'] ?? 0);
$specRefIds = [58, 59, 60, 61, 62, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76];
$isSpecRef = in_array($refId, $specRefIds, true);
$dozvOdstupanje = $referentnavrijednost['referentnevrijednosti_odstupanje'] ?? 0;

if ($prvomjerenje === '--' || $drugomjerenje === '--' || $trecemjerenje === '--') {
    $usaglasenost = 'NE';
    $finalusaglasenost = 'NISU USAGLAŠENI';
} elseif (!$sveBrojcano) {
    $usaglasenost = '-';
    if (!isset($finalusaglasenost)) {
        $finalusaglasenost = 'su USAGLAŠENI';
    }
} else {
    if ($isSpecRef) {
        if ($apsolutnagreska > $dozvOdstupanje) {
            $usaglasenost = 'NE';
            $finalusaglasenost = 'NISU USAGLAŠENI';
        } else {
            $usaglasenost = 'DA';
        }
    } else {
        if ($relativnagreska > round($dozvOdstupanje, $odstupanje_decimals)) {
            $usaglasenost = 'NE';
            $finalusaglasenost = 'NISU USAGLAŠENI';
        } else {
            $usaglasenost = 'DA';
        }
    }
    if (!isset($finalusaglasenost)) {
        $finalusaglasenost = 'su USAGLAŠENI';
    }
}

if (!isset($finalusaglasenost)) {
    $finalusaglasenost = 'su USAGLAŠENI';
}
