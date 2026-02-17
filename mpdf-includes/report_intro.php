    <!-- BROJ IZVJEŠTAJA -->
    <p>Број: <?php echo $izvjestaj['izvjestaji_broj'] ?></p>

    <!-- DATUM IZDAVANJA -->
    <p>Датум: <?php echo ($izvjestaj['izvjestaji_datumizdavanja'] !== null && $izvjestaj['izvjestaji_datumizdavanja'] !== '') ? date('d.m.Y.', strtotime($izvjestaj['izvjestaji_datumizdavanja'])) : '-'; ?></p>

    <!-- PRAZAN RED -->
    <br>

    <!-- PODNOSILAC ZAHTJEVA -->
    <p><strong>Подносилац захтјева:</strong> <?php echo $klijent['klijenti_naziv']; ?></p>
    <p>Број: <?php echo latinicaUCirilicu($radninalog['radninalozi_brojzahtjeva']); ?></p>
    <p>Датум: <?php echo ($izvjestaj['izvjestaji_datumzahtjeva'] !== null && $izvjestaj['izvjestaji_datumzahtjeva'] !== '') ? date('d.m.Y.', strtotime($izvjestaj['izvjestaji_datumzahtjeva'])) : '-'; ?></p>

    <!-- PRAZAN RED -->
    <br>

    <!-- MJERILO -->
    <p><strong>Мјерило:</strong></p>
    <table cellpadding="5" cellspacing="0" width="100%">
        <tbody>
            <tr>
                <td colspan="2">Назив мјерила:</td>
                <td colspan="4"><?php echo latinicaUCirilicu($vrstauredjaja['vrsteuredjaja_naziv']); ?></td>
            </tr>
            <tr>
                <td colspan="2">Произвођач мјерила:</td>
                <td colspan="4"><?php echo $mjerilo['mjerila_proizvodjac']; ?></td>
            </tr>
            <tr>
                <td>Тип:</td>
                <td><?php echo $mjerilo['mjerila_tip']; ?></td>
                <td>Серијски број:</td>
                <td><?php echo $mjerilo['mjerila_serijskibroj']; ?></td>
                <td>Службена ознака:</td>
                <td width="150"><?php echo $mjerilo['mjerila_sluzbenaoznaka']; ?></td>
            </tr>
            <tr>
                <td colspan="2">Година производње:</td>
                <td colspan="4"><?php echo $mjerilo['mjerila_godinaproizvodnje']; ?></td>
            </tr>
            <tr>
                <td colspan="2">Власник мјерила:</td>
                <!--<td colspan="4"><?php //echo latinicaUCirilicu($klijent['klijenti_naziv']); ?></td>-->
                <td colspan="4"><?php echo $klijent['klijenti_naziv']; ?></td>
            </tr>
            <tr>
                <td colspan="2">Локација мјерила:</td>
                <td colspan="4"><?php echo !empty($izvjestaj['izvjestaji_lokacijamjerila']) ? $izvjestaj['izvjestaji_lokacijamjerila'] : $izvjestaj['izvjestaji_mjestoinspekcije']; ?></td>
            </tr>
            <tr>
                <td colspan="2">Мјесто прегледа:</td>
                <td colspan="4"><?php echo $izvjestaj['izvjestaji_mjestoinspekcije']; ?></td>
            </tr>
        </tbody>
    </table>

    <!-- PRAZAN RED -->
    <br>

    <!-- VERIFIKACIJA -->
    <p><strong>Верификација:</strong></p>
    <div class="verifikacija-vrsta-verifikacije">
        <p>Врста верификације:</p>
        <?php foreach($vrsteinspekcije as $vrstainspekcije){ ?>
        <p>
            <input type="checkbox" <?php if($vrstainspekcije['vrsteinspekcije_id'] == $izvjestaj['izvjestaji_vrstainspekcijeid']){ echo "checked='checked'"; } ?>>
            <?php echo latinicaUCirilicu($vrstainspekcije['vrsteinspekcije_naziv']); ?>
        </p>
        <?php } ?>
    </div>
    <div class="verifikacija-radni-uslovi">
        <p>Радни услови:</p>
        <p>Температура: <?php echo $izvjestaj['izvjestaji_temperatura']; ?>°C</p>
        <p>Влажност: <?php echo $izvjestaj['izvjestaji_vlaznost']; ?>%</p>
    </div>
    <div class="verifikacija-republicki-zig">
        <p>Ознака и серијски број скинутог републичког жига: <?php echo $izvjestaj["izvjestaji_skinutizig"]; ?></p>
    </div>

    <!-- PRAZAN RED -->
    <br>

    <!-- ETALON -->
    <p><strong>Подаци о еталону:</strong></p>
    <table cellpadding="5" cellspacing="0" width="100%">
        <tbody>
            <?php 
            $izvjestaj['izvjestaji_opremazainspekciju'] = explode(',', $izvjestaj['izvjestaji_opremazainspekciju']);
            $etalon = '';
            foreach($izvjestaj['izvjestaji_opremazainspekciju'] as $oprema){
                $opremazainspekciju = new singleObject;
                $opremazainspekciju = $opremazainspekciju->fetch_single_object('opremazainspekciju', 'opremazainspekciju_id', $oprema);

                    if($opremazainspekciju['opremazainspekciju_proizvodjac'] == ""){
                        $proizvodjac = "-";
                    }else{
                        $proizvodjac = $opremazainspekciju['opremazainspekciju_proizvodjac'];
                    }

                    if($opremazainspekciju['opremazainspekciju_tip'] == ""){
                        $tip = "-";
                    }else{
                        $tip = $opremazainspekciju['opremazainspekciju_tip'];
                    }

                    if($opremazainspekciju['opremazainspekciju_serijskibroj'] == ""){
                        $serijskibroj = "-";
                    }else{
                        $serijskibroj = $opremazainspekciju['opremazainspekciju_serijskibroj'];
                    }
                    echo
                    "<tr>
                        <td>".latinicaUCirilicu($opremazainspekciju['opremazainspekciju_naziv'])."</td>
                        <td>Произвођач: ".$proizvodjac."</td>
                        <td>Тип: ".$tip."</td>
                        <td>Серијски број: ".$serijskibroj."</td>
                    </tr>";
            }
            ?> 
        </tbody>
    </table>

    <!-- PRAZAN RED -->
    <br>
    
    <!-- PREGLED MJERILA -->
    <p><strong>Преглед мјерила:</strong></p>
    <div class="pregled-mjerila">
        <p>Визуелни преглед: <?php if($izvjestaj['izvjestaji_mjerilocitljivo'] == 1){ echo "ДА";}else{echo "НЕ";} ?></p>
        <p>Провјера запрљаности: <?php if($izvjestaj['izvjestaji_mjerilocisto'] == 1){ echo "ДА";}else{echo "НЕ";} ?></p>
        <p>Провјера цјеловитости: <?php if($izvjestaj['izvjestaji_mjerilocjelovito'] == 1){ echo "ДА";}else{echo "НЕ";} ?></p>
        <p>Провјера функционалности: <?php if($izvjestaj['izvjestaji_mjerilokablovi'] == 1){ echo "ДА";}else{echo "НЕ";} ?></p>
    </div>