<h4 class="second-headline">Napomena:</h4>
<p class="second-headline" style="padding: 10px; border:1px solid #000;"><?php echo $izvjestaj['izvjestaji_napomena'] ?></p>

<br />
    
<h5 class="main-headline">5. Izjava o usaglašenosti</h5>
<p class="second-headline" style="text-align:justify;">Rezultati inspekcije mjerila <?php if($finalusaglasenost == "испуњава"){ echo "su <strong>USAGLAŠENI</strong>";} ?><?php if($finalusaglasenost == "не испуњава"){ echo "nisu <strong>USAGLAŠENI</strong>";} ?> sa propisanim opsegom dozvoljenih odstupanja u skladu sa gore navedenim Pravilnikom. Na osnovu rezultata inspekcije mjerilo je označeno inspekcijskom oznakom - markicom. Rezultati inspekcije se odnose isključivo na dati predmet u trenutku inspekcije. Izvještaj o inspekciji ne smije se reprodukovati osim u cjelini.</p>

<br /><br /><br />

<table style="width: 100%; border-collapse: collapse;border: none;">
    <tr>
        <td style="width:40%; vertical-align: top;border: none;padding-left: 10px;">
            <p>Inspekciju izvršio i izvještaj izradio:</p>
            <p>Ime i prezime: <?php echo $inspekcijuizvrsio['kontrolori_ime'] . " " . $inspekcijuizvrsio['kontrolori_prezime'] ?></p>
            <p>Datum: <?php echo date('d.m.Y.', strtotime($izvjestaj['izvjestaji_izvrsiodadatum'])) ?></p>
            <br>
            <p>Potpis: _____________________</p>
        </td>
        <td style="width:20%;border: none;"></td>
        <td style="width: 40%; vertical-align: top;border: none;padding-left: 10px;">
            <p>Izvještaj ovjerio:</p>
            <p>Ime i prezime: <?php echo $inspekcijuovjerio['kontrolori_ime'] . " " . $inspekcijuovjerio['kontrolori_prezime'] ?></p>
            <p>Datum: <?php echo date('d.m.Y.', strtotime($izvjestaj['izvjestaji_ovjeriodatum'])) ?></p>
            <br>
            <p>Potpis: _____________________</p>
        </td>
    </tr>
</table>