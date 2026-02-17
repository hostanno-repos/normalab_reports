<?php

$insertObject = 0;

foreach ($_POST as $key => $value) {
    if (strpos($key, 'submit') !== false) {
        $insertObject = 1;
    }
}

if ($insertObject == 1) {

    global $target_file;

    //GET UTM PARAMETERS
    $getCount = 0;
    $getString = "?";
    foreach ($_GET as $key => $value) {
        if ($getCount == 0) {
            $getString = $getString . $key . "=" . $value;
            $getCount++;
        } else {
            $getString = $getString . "&" . $key . "=" . $value;
            $getCount++;
        }
    }

    //GET NUMBER OF FILES
    $countfiles = count($_FILES);
    for ($i = 0; $i < $countfiles; $i++) {
        $helpVar = $_FILES['files']['name'];
        $helpVar = substr($helpVar[0], -4);
        $date = date('d-m-y');
        $time = date('h:i:s');
        $date = trim($date, " ");
        $time = str_replace(":", "", $time);
        $filename = $date . "-" . $time . $i . $helpVar;
        $target_file = '../uploads/' . $filename;
        $target_file_ = './uploads/' . $filename;
        $file_extension = pathinfo($target_file, PATHINFO_EXTENSION);
        $file_extension = strtolower($file_extension);
        $valid_extension = array("png", "jpeg", "jpg");
        if (in_array($file_extension, $valid_extension)) {
            if (move_uploaded_file($_FILES['files']['tmp_name'][0], $target_file)) {
                //do nothing
                var_dump("DONE");
            }
        }
    }

    // INSERT INTO TABLE START
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'submit') !== false) {
            $nazivTabele = explode("_", $key)[1];
        } else {
            if (strpos($key, 'password') !== false) {
                $$key = md5($value);
            } else {
                if (substr($key, -2) == "id" && $value == "") {
                    $$key = (int) NULL;
                } else {
                    $$key = $value;
                }
            }
        }
    }

    if ($nazivTabele == 'klijenti' && !ima_permisiju('pregledklijenata', 'dodavanje')) {
        header('Location: pregledklijenata.php');
        exit;
    }
    if ($nazivTabele == 'mjerila' && !ima_permisiju('pregledmjerila', 'dodavanje')) {
        header('Location: pregledmjerila.php');
        exit;
    }
    if ($nazivTabele == 'mjerila' && isset($_SESSION['user-type'], $_SESSION['user']) && (int)$_SESSION['user-type'] === 5 && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mK)) {
        $mjerila_klijentid = (int)$mK[1];
    }
    if ($nazivTabele == 'opremazainspekciju' && !ima_permisiju('opremazainspekciju', 'dodavanje')) {
        header('Location: opremazainspekciju.php');
        exit;
    }
    if ($nazivTabele == 'brojacirn' && !ima_permisiju('brojacirn', 'dodavanje')) {
        header('Location: brojacirn.php');
        exit;
    }
    if ($nazivTabele == 'tipoviizvjestaja' && !ima_permisiju('tipoviizvjestaja', 'dodavanje')) {
        header('Location: tipoviizvjestaja.php');
        exit;
    }
    if ($nazivTabele == 'kontrolori' && !ima_permisiju('kontrolori', 'dodavanje')) {
        header('Location: kontrolori.php');
        exit;
    }
    if ($nazivTabele == 'metodeinspekcije' && !ima_permisiju('metodeinspekcije', 'dodavanje')) {
        header('Location: metodeinspekcije.php');
        exit;
    }
    if ($nazivTabele == 'vrsteinspekcije' && !ima_permisiju('vrsteinspekcije', 'dodavanje')) {
        header('Location: vrsteinspekcije.php');
        exit;
    }
    if ($nazivTabele == 'vrsteuredjaja' && !ima_permisiju('vrsteuredjaja', 'dodavanje')) {
        header('Location: vrsteuredjaja.php');
        exit;
    }
    if ($nazivTabele == 'mjernevelicine' && !ima_permisiju('mjernevelicine', 'dodavanje')) {
        header('Location: mjernevelicine.php');
        exit;
    }
    if ($nazivTabele == 'referentnevrijednosti' && !ima_permisiju('referentnevrijednosti', 'dodavanje')) {
        header('Location: referentnevrijednosti.php');
        exit;
    }
    if ($nazivTabele == 'nivoihijerarhije' && !ima_permisiju('nivoihijerarhije', 'dodavanje')) {
        header('Location: nivoihijerarhije.php');
        exit;
    }
    if ($nazivTabele == 'korisnickeuloge' && !ima_permisiju('korisnickeuloge', 'dodavanje')) {
        header('Location: korisnickeuloge.php');
        exit;
    }
    if ($nazivTabele == 'korisnici' && !ima_permisiju('korisnici', 'dodavanje')) {
        header('Location: korisnici.php');
        exit;
    }
    if ($nazivTabele == 'rjesenjazaovlascivanje' && !ima_permisiju('rjesenjazaovlascivanje', 'dodavanje')) {
        header('Location: rjesenjaovlascivanja.php');
        exit;
    }

    if (isset($fakture_brojac)) {
        $fakture_brojac = $fakture_brojac + 1;
    }


    if ($countfiles > 0) {
        $nazivTabele_2 = $nazivTabele . "_slika";
        $$nazivTabele_2 = $target_file;
    }

    $queryColumns = $pdo->prepare("DESCRIBE " . $nazivTabele);
    $queryColumns->execute();
    $columnNames = $queryColumns->fetchAll(PDO::FETCH_COLUMN);
    if (($key = array_search($nazivTabele . "_id", $columnNames)) !== false) {
        unset($columnNames[$key]);
    }
    if (($key = array_search($nazivTabele . "_timestamp", $columnNames)) !== false) {
        unset($columnNames[$key]);
    }
    $columnNames = array_values($columnNames);
    $countColums = count($columnNames);

    global $columnsArrayString;
    global $querstionMarks;

    foreach ($columnNames as $key => $value) {
        if ($columnsArrayString == "") {
            $columnsArrayString = $value;
            $querstionMarks = "?";
        } else {
            $columnsArrayString = $columnsArrayString . "," . $value;
            $querstionMarks = $querstionMarks . ",?";
        }
    }

    $query = $pdo->prepare('INSERT INTO ' . $nazivTabele . '(' . $columnsArrayString . ')VALUES(' . $querstionMarks . ')');

    $counterKeys = 0;
    $counterBinds = 1;

    while ($counterKeys < $countColums) {
        $$counterBinds = $columnNames[$counterKeys];
        $query->bindValue($counterBinds, $$$counterBinds);
        $counterKeys++;
        $counterBinds++;
    }
    $query->execute();

    if ($nazivTabele == 'klijenti') {
        $cacheFile = dirname(__DIR__) . '/cache/klijenti_count.json';
        if (is_file($cacheFile)) {
            @unlink($cacheFile);
        }
        $noviKlijentId = $pdo->lastInsertId();
        if ($noviKlijentId) {
            $username = 'klijent_' . $noviKlijentId;
            $check = $pdo->prepare("SELECT 1 FROM korisnici WHERE korisnici_username = ? LIMIT 1");
            $check->execute(array($username));
            if (!$check->fetch()) {
                $plainPassword = substr(bin2hex(random_bytes(8)), 0, 12);
                $hashPassword = md5($plainPassword);
                $nazivPrezime = isset($klijenti_naziv) ? $klijenti_naziv : '';
                if (mb_strlen($nazivPrezime) > 255) {
                    $nazivPrezime = mb_substr($nazivPrezime, 0, 252) . '...';
                }
                $ins = $pdo->prepare("INSERT INTO korisnici (korisnici_ime, korisnici_prezime, korisnici_telefon, korisnici_email, korisnici_username, korisnici_password, korisnici_korisnickaulogaid, korisnici_lozinka_prikaz) VALUES (?, ?, ?, ?, ?, ?, 5, ?)");
                $ins->execute(array('Klijent', $nazivPrezime, '', '', $username, $hashPassword, $plainPassword));
            }
        }
    }

    if (isset($_POST['radninalozi_brojacrnid'])) {
        $brojac = new singleObject;
        $brojac = $brojac->fetch_single_object("brojacirn", "brojacirn_id", $_POST['radninalozi_brojacrnid']);
        $noviBrojac = $brojac['brojacirn_brojac'] + 1;
        $updateBrojac = $pdo->prepare('UPDATE brojacirn SET brojacirn_brojac = ' . $noviBrojac . ' WHERE brojacirn_id = ' . $_POST['radninalozi_brojacrnid']);
        $updateBrojac->execute();
    }


    $thisFile = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);

    switch ($thisFile) {
        case "dodajklijenta.php":
            header('Location: pregledklijenata.php');
            break;
        case "dodajkontrolora.php":
            header('Location: kontrolori.php');
            break;
        case "dodajmjerilo.php":
            header('Location: pregledmjerila.php');
            break;
        case "dodajmetoduinspekcije.php":
            header('Location: metodeinspekcije.php');
            break;
        case "dodajbrojac.php":
            header('Location: brojacirn.php');
            break;
        case "dodajradninalog.php":
            header('Location: pregledradnihnaloga.php');
            break;
        case "dodajvrstuuredjaja.php":
            header('Location: vrsteuredjaja.php');
            break;
        case "dodajmjernuvelicinu.php":
            header('Location: mjernevelicine.php');
            break;
        case "dodajreferentnuvrijednost.php":
            header('Location: referentnevrijednosti.php');
            break;
        case "dodajopremuzainspekciju.php":
            header('Location: opremazainspekciju.php');
            break;
        case "dodajtipizvjestaja.php":
            header('Location: tipoviizvjestaja.php');
            break;
        case "dodajnivohijerarhije.php":
            header('Location: nivoihijerarhije.php');
            break;
        case "dodajkorisnickuulogu.php":
            header('Location: korisnickeuloge.php');
            break;
        case "dodajkorisnika.php":
            header('Location: korisnici.php');
            break;
        case "dodajrjesenjeovlascivanja.php":
            header('Location: rjesenjaovlascivanja.php');
            break;
        default:
            header('Location: ' . basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']) . $getString);
            break;
    }
    //INSERT INTO TABLE END
}

$insertIzvjestaj = 0;

foreach ($_POST as $key => $value) {
    if (strpos($key, 'sacuvaj') !== false) {
        $insertIzvjestaj = 1;
    }
}

if ($insertIzvjestaj == 1) {
    //var_dump($_POST);
    //GET VALUES FROM POST

    foreach ($_POST as $key => $value) {
        if ($value != '') {
            $$key = $value;
        } else {
            $$key = NULL;
        }
    }

    //$izvjestaji_radninalogid = $_POST['izvjestaji_radninalogid'];
    //$izvjestaji_broj = $_POST['izvjestaji_broj'];
    //$izvjestaji_datumizdavanja = $_POST['izvjestaji_datumizdavanja'];
    //$izvjestaji_datuminspekcije = $_POST['izvjestaji_datuminspekcije'];
    //$izvjestaji_zahtjevzaispitivanje = $_POST['izvjestaji_zahtjevzaispitivanje'];
    //$izvjestaji_mjeriloid = $_POST['izvjestaji_mjeriloid'];
    //$izvjestaji_mjestoinspekcije = $_POST['izvjestaji_mjestoinspekcije'];
    //$izvjestaji_vrstainspekcijeid = $_POST['izvjestaji_vrstainspekcijeid'];
    //$izvjestaji_opremazainspekciju = $_POST['izvjestaji_opremazainspekciju'];
    //$izvjestaji_opisprocedure = $_POST['izvjestaji_opisprocedure'];
    //$izvjestaji_temperatura = $_POST['izvjestaji_temperatura'];
    //$izvjestaji_vlaznost = $_POST['izvjestaji_vlaznost'];
    //$izvjestaji_mjerilocisto = $_POST['izvjestaji_mjerilocisto'];
    //$izvjestaji_mjerilocjelovito = $_POST['izvjestaji_mjerilocjelovito'];
    //$izvjestaji_mjerilocitljivo = $_POST['izvjestaji_mjerilocitljivo'];
    //$izvjestaji_mjerilokablovi = $_POST['izvjestaji_mjerilokablovi'];
    //$izvjestaji_napomena = $_POST['izvjestaji_napomena'];
    //$izvjestaji_izvrsioid = $_POST['izvjestaji_izvrsioid'];
    //$izvjestaji_izvrsiodadatum = $_POST['izvjestaji_izvrsiodadatum'];
    //$izvjestaji_ovjerioid = $_POST['izvjestaji_ovjerioid'];
    //$izvjestaji_ovjeriodatum = $_POST['izvjestaji_ovjeriodatum'];

    //WRITE QUERY
    $query = $pdo->prepare('INSERT INTO izvjestaji (izvjestaji_radninalogid,izvjestaji_broj,izvjestaji_tipizvjestajaid,izvjestaji_datumizdavanja,izvjestaji_datuminspekcije,izvjestaji_datumzahtjeva,izvjestaji_zahtjevzaispitivanje,izvjestaji_mjeriloid,izvjestaji_mjestoinspekcije,izvjestaji_lokacijamjerila,izvjestaji_vrstainspekcijeid,izvjestaji_opremazainspekciju,izvjestaji_opisprocedure,izvjestaji_temperatura,izvjestaji_vlaznost,izvjestaji_skinutizig,izvjestaji_mjerilocisto,izvjestaji_mjerilocjelovito,izvjestaji_mjerilocitljivo,izvjestaji_mjerilokablovi,izvjestaji_novizig,izvjestaji_napomena,izvjestaji_izvrsioid,izvjestaji_izvrsiodadatum,izvjestaji_ovjerioid,izvjestaji_ovjeriodatum)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');

    //BIND VALUES
    $query->bindValue(1, $izvjestaji_radninalogid);
    $query->bindValue(2, $izvjestaji_broj);
    $query->bindValue(3, $izvjestaji_tipizvjestajaid);
    $query->bindValue(4, $izvjestaji_datumizdavanja);
    $query->bindValue(5, $izvjestaji_datuminspekcije);
    $query->bindValue(6, $izvjestaji_datumzahtjeva);
    $query->bindValue(7, $izvjestaji_zahtjevzaispitivanje);
    $query->bindValue(8, $izvjestaji_mjeriloid);
    $query->bindValue(9, $izvjestaji_mjestoinspekcije);
    $query->bindValue(10, isset($izvjestaji_lokacijamjerila) ? $izvjestaji_lokacijamjerila : null);
    $query->bindValue(11, $izvjestaji_vrstainspekcijeid);
    $query->bindValue(12, $izvjestaji_opremazainspekciju);
    $query->bindValue(13, $izvjestaji_opisprocedure);
    $query->bindValue(14, $izvjestaji_temperatura);
    $query->bindValue(15, $izvjestaji_vlaznost);
    $query->bindValue(16, $izvjestaji_skinutizig);
    $query->bindValue(17, $izvjestaji_mjerilocisto);
    $query->bindValue(18, $izvjestaji_mjerilocjelovito);
    $query->bindValue(19, $izvjestaji_mjerilocitljivo);
    $query->bindValue(20, $izvjestaji_mjerilokablovi);
    $query->bindValue(21, $izvjestaji_novizig);
    $query->bindValue(22, $izvjestaji_napomena);
    $query->bindValue(23, $izvjestaji_izvrsioid);
    $query->bindValue(24, $izvjestaji_izvrsiodadatum);
    $query->bindValue(25, $izvjestaji_ovjerioid);
    $query->bindValue(26, $izvjestaji_ovjeriodatum);

    //print_r($query);

    //EXECUTE QUERY
    $query->execute();

    //GET LAST ID FROM IZVJEŠTAJI
    $idizvjestaja = $pdo->lastInsertId();
    //var_dump($idizvjestaja);
    //die();

    if ($idizvjestaja != 0) {
        //INSERT RESULTS
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'rezultat') !== false) {

                if($value != "-"){

                    //var_dump($key);

                    //BREAK NAME INTO PIECES
                    $keypieces = explode("_", $key);

                    //GET VARIABLES
                    //var_dump($idizvjestaja);
                    $mjernavelicina = $keypieces[1];
                    //var_dump($mjernavelicina);
                    $referentnavrijednost = $keypieces[2];
                    //var_dump($referentnavrijednost);
                    $brojmjerenja = $keypieces[3];
                    //var_dump($brojmjerenja);
                    $rezultatmjerenja = $value;
                    //var_dump($rezultatmjerenja);

                    //WRITE QUERY
                    $query = $pdo->prepare('INSERT INTO rezultatimjerenja (rezultatimjerenja_izvjestajid, rezultatimjerenja_mjernavelicinaid, rezultatimjerenja_referentnavrijednostid,rezultatimjerenja_brojmjerenja, rezultatimjerenja_rezultatmjerenja)VALUES(?,?,?,?,?)');
                    $query->bindValue(1, $idizvjestaja);
                    $query->bindValue(2, $mjernavelicina);
                    $query->bindValue(3, $referentnavrijednost);
                    $query->bindValue(4, $brojmjerenja);
                    $query->bindValue(5, $rezultatmjerenja);

                    //EXECUTE QUERY
                    $query->execute();

                    //REDIRECT TO IZVJEŠTAJI
                    header('Location: pregledizvjestaja.php?page=1');

                }
            }
        }
    }


}

?>