<?php foreach ($referentnevrijednosti as $referentnavrijednost) {

   //kupimo rezultate mjerenja za ovu referentnu vrijednost
   $rezultatimjerenja = new allResults;
   $rezultatimjerenja = $rezultatimjerenja->fetch_all_results($izvjestaj['izvjestaji_id'], $mjernavelicina['mjernevelicine_id'], $referentnavrijednost['referentnevrijednosti_id']);

    if(count($rezultatimjerenja) == 0){
        $prvomjerenje = "-";
        $drugomjerenje = "-";
        $trecemjerenje = "-";
    }else{
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
    }

                

    //PRORAČUN
    //ako je vršeno mjerenje odnosno ako nije uneseno "-" kao rezultat mjerenja
    if (isset($prvomjerenje) && isset($drugomjerenje) && isset($trecemjerenje) &&
        $prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-" &&
        $prvomjerenje != "--" && $drugomjerenje != "--" && $trecemjerenje != "--") {

        //računamo srednju vrijednost iz naša tri mjerenja
        $srednjavrijednost = round(($prvomjerenje + $drugomjerenje + $trecemjerenje) / 3, 2);
        //računamo apsolutnu grešku mjerenja
        $apsolutnagreska = abs($srednjavrijednost - $referentnavrijednost['referentnevrijednosti_referentnavrijednost']);

        //ako nam je referentna vrijednost 0 onda moramo paziti pri množenju pa taj slučaj izdvajamo
        if ($referentnavrijednost['referentnevrijednosti_referentnavrijednost'] == 0) {  
            //računamo relativnu grešku u postotcimagrešku
            $relativnagreska = abs(round(($apsolutnagreska / 1) * 100, 2));
        } else {
            //računamo relativnu grešku u postotcimagrešku
            $relativnagreska = abs(round(($apsolutnagreska / $referentnavrijednost['referentnevrijednosti_referentnavrijednost']) * 100, 2));
        }

    //ako nije vršeno mjerenje odnosno ako je uneseno "-" kao rezultat mjerenja
    } else {

        if(!isset($prvomjerenje) && !isset($drugomjerenje) && !isset($trecemjerenje)){
            $prvomjerenje = "-";
            $drugomjerenje = "-";
            $trecemjerenje = "-";
        }

        //srednja vrijednost je takođe "-"
        $srednjavrijednost = "--";
        //apsolutna greška je takođe "-"
        $apsolutnagreska = "--";
        //onda je i relativna greska "-"
        $relativnagreska = "--";

    }                

    //USAGLAŠENOST
    if ($relativnagreska > round($referentnavrijednost['referentnevrijednosti_odstupanje'], 0)) {
        if(isset($pismo) && $pismo == "LAT"){$usaglasenost = "NE";}else{$usaglasenost = "НЕ"; };
        //$usaglasenost = "НЕ";
        $finalusaglasenost = "не испуњава";
    } else if ($prvomjerenje != "--" && $drugomjerenje != "--" && $trecemjerenje != "--") {
        if(isset($pismo) && $pismo == "LAT"){$usaglasenost = "DA";}else{$usaglasenost = "ДА"; };
        //$usaglasenost = "ДА";
    } else {
        if(isset($pismo) && $pismo == "LAT"){$usaglasenost = "NE";}else{$usaglasenost = "НЕ"; };
        //$usaglasenost = "НЕ";
        $finalusaglasenost = "не испуњава";
    }
    
    if (!isset($finalusaglasenost)) {
        $finalusaglasenost = "испуњава";
    }

    if ($prvomjerenje != "--" && $drugomjerenje != "--" && $trecemjerenje != "--" &&
        $prvomjerenje != "-" && $drugomjerenje != "-" && $trecemjerenje != "-") { ?>
    
        <tr>
            <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
            <td style="text-align:center;"><?php echo $prvomjerenje ?></td>
            <td style="text-align:center;"><?php echo $drugomjerenje ?></td>
            <td style="text-align:center;"><?php echo $trecemjerenje ?></td>
            <td style="text-align:center;"><?php echo number_format((float) $srednjavrijednost, 2, '.', '') ?></td>
            <td style="text-align:center;"><?php echo number_format((float) $apsolutnagreska, 2, '.', '') ?></td>
            <td style="text-align:center;"><?php echo number_format((float) $relativnagreska, 2, '.', '') ?></td>
            <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_odstupanje'], 0) ?></td>
            <td style="text-align:center;"><?php echo $usaglasenost ?></td>
        </tr>
    <?php } else if($prvomjerenje == "--" && $drugomjerenje == "--" && $trecemjerenje == "--") { ?>
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
    <?php } else { ?>
        <tr class="red-bez-mjerenja">
            <td style="text-align:center;"><?php echo round($referentnavrijednost['referentnevrijednosti_referentnavrijednost'], 1) ?></td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;">-</td>
            <td style="text-align:center;"><?php if(isset($pismo) && $pismo == "LAT"){echo "DA";}else{echo "ДА"; } ?></td>
        </tr>
    <?php }
    $prvomjerenje = "-";
    $drugomjerenje = "-";
    $trecemjerenje = "-";
}   ?>