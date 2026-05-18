<?php
/**
 * Zajednička logika za rezultate mjerenja (jedan red tabele – standardna tabela s 3 mjerenja).
 * Usklađeno s mpdf-includes skriptama (relative / absolute / shown-relative, posebni slučajevi 19, 20).
 *
 * Očekuje: $izvjestaj, $mjernavelicina, $referentnavrijednost.
 * Opcionalno: $rezultati_mjerenja_odstupanje_decimals (inače iz norma_usaglasenost_pravila.php).
 * Postavlja: $prvomjerenje, $drugomjerenje, $trecemjerenje, $srednjavrijednost, $apsolutnagreska,
 *             $relativnagreska, $usaglasenost, $finalusaglasenost.
 */

require_once __DIR__ . '/norma_usaglasenost_pravila.php';

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
    $broj = (int) ($rezultatmjerenja['rezultatimjerenja_brojmjerenja'] ?? 0);
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

$mjvId = (int) ($mjernavelicina['mjernevelicine_id'] ?? 0);
$refId = (int) ($referentnavrijednost['referentnevrijednosti_id'] ?? 0);
$refXs = (float) ($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] ?? 0);
$dozvOdstupanje = (float) ($referentnavrijednost['referentnevrijednosti_odstupanje'] ?? 0);

$sveBrojcano = norma_usaglasenost_sva_tri_brojcano($prvomjerenje, $drugomjerenje, $trecemjerenje);

if ($sveBrojcano) {
    $srednjaSirova = ((float) $prvomjerenje + (float) $drugomjerenje + (float) $trecemjerenje) / 3;
    $srednjavrijednost = round($srednjaSirova, 2);
    $apsolutnagreska = abs($srednjaSirova - $refXs);
    if ($refXs == 0.0) {
        $relativnagreska = abs(($apsolutnagreska / 1) * 100);
    } else {
        $relativnagreska = abs(($apsolutnagreska / $refXs) * 100);
    }
} else {
    $srednjavrijednost = '-';
    $apsolutnagreska = '-';
    $relativnagreska = '-';
}

if (!isset($rezultati_mjerenja_odstupanje_decimals)) {
    $rezultati_mjerenja_odstupanje_decimals = norma_mjerna_odstupanje_decimals($mjvId);
}
$odstupanje_decimals = (int) $rezultati_mjerenja_odstupanje_decimals;
$odstupanje_decimals = in_array($odstupanje_decimals, [0, 1, 3], true) ? $odstupanje_decimals : 0;
$dozvZaUsporedbu = round($dozvOdstupanje, $odstupanje_decimals);

$usporediApsolutno = norma_usaglasenost_usporedi_apsolutno($mjvId, $refXs, $refId);
$isShownRelative = in_array($mjvId, norma_mjerna_shown_relative_ids(), true);

// --- Usaglašenost (ista logika kao u mpdf skriptama) ---
if (norma_usaglasenost_mjerenje_nije_izvrseno($prvomjerenje, $drugomjerenje, $trecemjerenje)) {
    $usaglasenost = 'NE';
    $finalusaglasenost = 'NISU USAGLAŠENI';
} elseif ($mjvId === 19 && norma_usaglasenost_sva_tri_crtica($prvomjerenje, $drugomjerenje, $trecemjerenje)) {
    // Kiseonik u inkubatoru: sva tri "-" => DA (absolute skripta)
    $usaglasenost = 'DA';
} elseif ($mjvId === 20 && norma_usaglasenost_sva_tri_crtica($prvomjerenje, $drugomjerenje, $trecemjerenje)) {
    // Relativna vlažnost: sva tri "-" => DA (relative skripta)
    $usaglasenost = 'DA';
} elseif (!$sveBrojcano) {
    if ($isShownRelative) {
        // script[one-shown-two-not-measurable-relative]: "-" (nije mjereno) => DA; samo "---" => NE (gore)
        $usaglasenost = 'DA';
    } else {
        // hidden-relative / absolute: mješovito "-" i brojevi — red se ne ispisuje, ne ruši zaključak
        $usaglasenost = '-';
        if (!isset($finalusaglasenost)) {
            $finalusaglasenost = 'su USAGLAŠENI';
        }
    }
} else {
    if ($usporediApsolutno) {
        if ($apsolutnagreska > $dozvZaUsporedbu) {
            $usaglasenost = 'NE';
            $finalusaglasenost = 'NISU USAGLAŠENI';
        } else {
            $usaglasenost = 'DA';
        }
    } else {
        if ($relativnagreska > $dozvZaUsporedbu) {
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
