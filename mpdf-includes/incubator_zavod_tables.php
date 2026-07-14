<?php
/**
 * Dinamičke tabele mjerenja za Zavod inkubator (klasični i transportni).
 * Učitava mjerne veličine po vrsti uređaja iz radnog naloga (isto kao uredi.php).
 *
 * Očekuje: $radninalog, $izvjestaj
 * Opcionalno: $pismo ('LAT' za Bata), $incubatorZavodCyrillic (default: true osim kad je $pismo === 'LAT')
 */

if (!isset($incubatorZavodCyrillic)) {
    $incubatorZavodCyrillic = !isset($pismo) || $pismo !== 'LAT';
}

$mjernevelicineInkubator = new allObjectsBy;
$mjernevelicineInkubator = $mjernevelicineInkubator->fetch_all_objects_by(
    'mjernevelicine',
    'mjernevelicine_vrstauredjajaid',
    $radninalog['radninalozi_vrstauredjajaid'],
    'mjernevelicine_id',
    'ASC'
);

$incubatorSectionTotal = count($mjernevelicineInkubator);
$incubatorSectionIndex = 0;

foreach ($mjernevelicineInkubator as $mjernavelicina) {
    $incubatorSectionIndex++;
    $mvId = (int) $mjernavelicina['mjernevelicine_id'];
    $jedinica = $mjernavelicina['mjernevelicine_jedinica'];

    // Isti redoslijed skripti kao u 7.php: 3× apsolutna hidden, 1× relativna hidden, 2× shown relativna
    if ($incubatorSectionIndex >= $incubatorSectionTotal - 1) {
        $incubatorScript = 'script[one-shown-two-not-measurable-relative].php';
    } elseif ($incubatorSectionIndex === $incubatorSectionTotal - 2) {
        $incubatorScript = 'script[one-hidden-two-not-measurable-relative].php';
    } else {
        $incubatorScript = 'script[one-hidden-two-not-measurable-absolute].php';
    }

    $mjernaVelicinaID = $mvId;
    if ($incubatorSectionIndex <= 2) {
        $incubatorForceTacnost = 1;
    } else {
        unset($incubatorForceTacnost);
    }

    $tabJedinica = $jedinica;
    if ($incubatorZavodCyrillic && ($jedinica === '°C' || $jedinica === '℃')) {
        $tabJedinica = '℃';
    }

    if ($incubatorSectionIndex === 3) {
        $ndgLabel = $incubatorZavodCyrillic ? 'НДГ (Vol.%)' : 'Dozvoljeno odstupanje (Vol.%)';
    } elseif ($incubatorSectionIndex >= $incubatorSectionTotal - 1) {
        $ndgLabel = $incubatorZavodCyrillic ? 'НДГ (%)' : 'Dozvoljeno odstupanje';
    } else {
        $ndgLabel = $incubatorZavodCyrillic
            ? 'НДГ (' . $tabJedinica . ')'
            : 'Dozvoljeno odstupanje';
    }

    $deltaLabel = $incubatorZavodCyrillic ? 'Г (%)' : 'δ [%]';
    $usagLabel = $incubatorZavodCyrillic ? 'Усаглашеност' : 'Usaglašenost';
    $unitOpen = $incubatorZavodCyrillic ? '(' : '[';
    $unitClose = $incubatorZavodCyrillic ? ')' : ']';

    $naziv = $mjernavelicina['mjernevelicine_naziv'];
    if ($incubatorZavodCyrillic && function_exists('latinicaUCirilicu')) {
        $naziv = latinicaUCirilicu($naziv);
    }

    $referentnevrijednosti = new allObjectsBy;
    $referentnevrijednosti = $referentnevrijednosti->fetch_all_objects_by(
        'referentnevrijednosti',
        'referentnevrijednosti_mjernavelicinaid',
        $mvId,
        'referentnevrijednosti_referentnavrijednost',
        'ASC'
    );
    ?>

    <p style="text-align:center;"><?php echo $naziv; ?></p>

    <table cellpadding="5" cellspacing="0" width="100%" class="rezultati-otkucaji">
        <thead>
            <tr>
                <th rowspan="2">Xs <?php echo $unitOpen . $tabJedinica . $unitClose; ?></th>
                <th colspan="3">Xm <?php echo $unitOpen . $tabJedinica . $unitClose; ?></th>
                <th rowspan="2">&lt;Xm&gt; <?php echo $unitOpen . $tabJedinica . $unitClose; ?></th>
                <th rowspan="2">ΔX <?php echo $unitOpen . $tabJedinica . $unitClose; ?></th>
                <th rowspan="2"><?php echo $deltaLabel; ?></th>
                <th rowspan="2"><?php echo $ndgLabel; ?></th>
                <th rowspan="2"><?php echo $usagLabel; ?></th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
            </tr>
        </thead>
        <tbody>
            <?php include($incubatorScript); ?>
        </tbody>
    </table>

    <br />

    <?php
    unset($incubatorForceTacnost);
}
