<?php
// Zaokruživanje odstupanja pri usporedbi (0, 1 ili 3 decimale) – ovisno o mjernoj veličini
if (isset($mjernaVelicinaID)) {
    if ($mjernaVelicinaID == 11) {
        $rezultati_mjerenja_odstupanje_decimals = 3;
    } elseif ($mjernaVelicinaID == 8 || $mjernaVelicinaID == 18 || $mjernaVelicinaID == 27 || $mjernaVelicinaID == 28) {
        $rezultati_mjerenja_odstupanje_decimals = 1;
    } elseif ($mjernaVelicinaID == 15) {
        $rezultati_mjerenja_odstupanje_decimals = 2;
    } else {
        $rezultati_mjerenja_odstupanje_decimals = 0;
    }
} else {
    $rezultati_mjerenja_odstupanje_decimals = 0;
}
$tacnost = $rezultati_mjerenja_odstupanje_decimals;
if (!isset($finalusaglasenost)) {
    $finalusaglasenost = 'испуњава';
}

// Relativna vlažnost u inkubatoru (mj. veličina 20):
// ako su sva tri mjerenja "-" želimo red sa "-" i usaglašenost DA,
// ali samo za tu mjernu veličinu
$isRelHum20 = isset($mjernavelicina) && (int)$mjernavelicina['mjernevelicine_id'] === 20;

foreach ($referentnevrijednosti as $referentnavrijednost) {
    include __DIR__ . '/../includes/rezultati_mjerenja_logika.php';

    if ($usaglasenost === 'NE') {
        $finalusaglasenost = 'не испуњава';
    }
    $usaglasenostCyr = ($usaglasenost === 'DA') ? 'ДА' : (($usaglasenost === 'NE') ? 'НЕ' : $usaglasenost);

    // Posebna obrada: relativna vlažnost (20) i sva tri mjerenja "-"
    if ($isRelHum20 && $prvomjerenje === '-' && $drugomjerenje === '-' && $trecemjerenje === '-') {
        $usaglasenost = 'DA';
        $usaglasenostCyr = 'ДА';
    }

    if ($prvomjerenje !== '-' && $prvomjerenje !== '--' && $drugomjerenje !== '-' && $drugomjerenje !== '--' && $trecemjerenje !== '-' && $trecemjerenje !== '--') { ?>
        <tr>
            <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
            <td style="text-align:center;"><?php echo $prvomjerenje ?></td>
            <td style="text-align:center;"><?php echo $drugomjerenje ?></td>
            <td style="text-align:center;"><?php echo $trecemjerenje ?></td>
            <td style="text-align:center;"><?php echo number_format((float) $srednjavrijednost, 2, '.', '') ?></td>
            <td style="text-align:center;"><?php echo number_format((float) $apsolutnagreska, 2, '.', '') ?></td>
            <td style="text-align:center;"><?php echo number_format((float) $relativnagreska, 2, '.', '') ?></td>
            <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_odstupanje'], $tacnost) ?></td>
            <td style="text-align:center;"><?php if(isset($pismo) && $pismo == "LAT"){echo $usaglasenost;}else{echo $usaglasenostCyr; } ?></td>
        </tr>
    <?php } elseif ($prvomjerenje === '--' && $drugomjerenje === '--' && $trecemjerenje === '--') { ?>
        <tr>
            <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;"><?php if(isset($pismo) && $pismo == "LAT"){echo "NE";}else{echo "НЕ"; } ?></td>
        </tr>
    <?php } elseif ($isRelHum20 && $prvomjerenje === '-' && $drugomjerenje === '-' && $trecemjerenje === '-') { ?>
        <tr>
            <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_odstupanje'], $tacnost) ?></td>
            <td style="text-align:center;"><?php if(isset($pismo) && $pismo == "LAT"){echo "DA";}else{echo "ДА"; } ?></td>
        </tr>
    <?php } else { ?>
        <!--<tr>
            <td style="text-align:center;"><?php //echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
            <td style="text-align:center;"><?php //echo $prvomjerenje ?></td>
            <td style="text-align:center;"><?php //echo $drugomjerenje ?></td>
            <td style="text-align:center;"><?php //echo $trecemjerenje ?></td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;"><?php //echo $usaglasenostCyr ?></td>
        </tr>-->
    <?php }
}
?>
