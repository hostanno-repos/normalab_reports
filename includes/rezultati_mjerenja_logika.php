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
    // Kao u mpdf-includes skriptama: apsolutna greška se računa iz zaokružene srednje vrijednosti.
    $apsolutnagreska = abs($srednjavrijednost - $refXs);
    if ($refXs == 0.0) {
        $relativnagreska = abs(round(($apsolutnagreska / 1) * 100, 2));
    } else {
        $relativnagreska = abs(round(($apsolutnagreska / $refXs) * 100, 2));
    }
} else {
    $srednjavrijednost = '-';
    $apsolutnagreska = '-';
    $relativnagreska = '-';
}

// Uvijek računamo tačnost po trenutnoj mjernoj veličini (ne smije se naslijediti iz prethodnog reda).
$rezultati_mjerenja_odstupanje_decimals = norma_mjerna_odstupanje_decimals($mjvId);
$odstupanje_decimals = (int) $rezultati_mjerenja_odstupanje_decimals;
$odstupanje_decimals = in_array($odstupanje_decimals, [0, 1, 3], true) ? $odstupanje_decimals : 0;
$dozvZaUsporedbu = round($dozvOdstupanje, $odstupanje_decimals);

$usporediApsolutno = norma_usaglasenost_usporedi_apsolutno($mjvId, $refXs, $refId);
$isShownRelative = in_array($mjvId, norma_mjerna_shown_relative_ids(), true);
$isShownAbsolute = in_array($mjvId, norma_mjerna_shown_absolute_ids(), true);
$debugReason = '';

// --- Usaglašenost (ista logika kao u mpdf skriptama) ---
if (norma_usaglasenost_mjerenje_nije_izvrseno($prvomjerenje, $drugomjerenje, $trecemjerenje)) {
    $usaglasenost = 'NE';
    $finalusaglasenost = 'NISU USAGLAŠENI';
    $debugReason = 'Sva tri mjerenja su "--" (nije mjerljivo) -> NE';
} elseif (($mjvId === 19 || $mjvId === 155) && norma_usaglasenost_sva_tri_crtica($prvomjerenje, $drugomjerenje, $trecemjerenje)) {
    // Kiseonik u inkubatoru: sva tri "-" => DA (absolute skripta)
    $usaglasenost = 'DA';
    $debugReason = 'Specijalno pravilo O2 (19/155): sva tri "-" -> DA';
} elseif (($mjvId === 20 || $mjvId === 156) && norma_usaglasenost_sva_tri_crtica($prvomjerenje, $drugomjerenje, $trecemjerenje)) {
    // Relativna vlažnost: sva tri "-" => DA (relative skripta)
    $usaglasenost = 'DA';
    $debugReason = 'Specijalno pravilo vlaznost (20/156): sva tri "-" -> DA';
} elseif (!$sveBrojcano) {
    if ($isShownRelative || $isShownAbsolute) {
        // script[one-shown-two-not-measurable-relative]/absolute: "-" (nije mjereno) => DA; samo "---" => NE (gore)
        $usaglasenost = 'DA';
        $debugReason = 'Shown varijanta i nebrojcani unos ("-"/mjesovito) -> DA';
    } else {
        // hidden-relative / absolute: mješovito "-" i brojevi — red se ne ispisuje, ne ruši zaključak
        $usaglasenost = '-';
        $debugReason = 'Hidden varijanta i nebrojcani unos -> "-" (ne obara final)';
        if (!isset($finalusaglasenost)) {
            $finalusaglasenost = 'su USAGLAŠENI';
        }
    }
} else {
    if ($usporediApsolutno) {
        if ($apsolutnagreska > $dozvZaUsporedbu) {
            $usaglasenost = 'NE';
            $finalusaglasenost = 'NISU USAGLAŠENI';
            $debugReason = 'Apsolutno pravilo: |dX| > dozvoljeno -> NE';
        } else {
            $usaglasenost = 'DA';
            $debugReason = 'Apsolutno pravilo: |dX| <= dozvoljeno -> DA';
        }
    } else {
        if ($relativnagreska > $dozvZaUsporedbu) {
            $usaglasenost = 'NE';
            $finalusaglasenost = 'NISU USAGLAŠENI';
            $debugReason = 'Relativno pravilo: rel. greska > dozvoljeno -> NE';
        } else {
            $usaglasenost = 'DA';
            $debugReason = 'Relativno pravilo: rel. greska <= dozvoljeno -> DA';
        }
    }
    if (!isset($finalusaglasenost)) {
        $finalusaglasenost = 'su USAGLAŠENI';
    }
}

if (!isset($finalusaglasenost)) {
    $finalusaglasenost = 'su USAGLAŠENI';
}

if (!empty($GLOBALS['norma_debug_usaglasenost_collect'])) {
    if (!isset($GLOBALS['norma_debug_usaglasenost_rows']) || !is_array($GLOBALS['norma_debug_usaglasenost_rows'])) {
        $GLOBALS['norma_debug_usaglasenost_rows'] = array();
    }
    $GLOBALS['norma_debug_usaglasenost_rows'][] = array(
        'mjernavelicina_id' => $mjvId,
        'referentna_id' => $refId,
        'xs' => $refXs,
        'm1' => $prvomjerenje,
        'm2' => $drugomjerenje,
        'm3' => $trecemjerenje,
        'srednja' => $srednjavrijednost,
        'aps' => $apsolutnagreska,
        'rel' => $relativnagreska,
        'dozv_raw' => $dozvOdstupanje,
        'dozv_cmp' => $dozvZaUsporedbu,
        'decimals' => $odstupanje_decimals,
        'aps_rule' => $usporediApsolutno ? 1 : 0,
        'usaglasenost' => $usaglasenost,
        'reason' => $debugReason
    );
}
