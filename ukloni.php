<?php
//INCLUDES
include_once ('includes/head.php');

if (isset($_GET['t']) && isset($_GET['o'])) {

    if ($_GET['t'] == 'klijenti' && !ima_permisiju('pregledklijenata', 'brisanje')) {
        header('Location: pregledklijenata.php');
        exit;
    }
    if ($_GET['t'] == 'mjerila' && !ima_permisiju('pregledmjerila', 'brisanje')) {
        header('Location: pregledmjerila.php');
        exit;
    }
    if ($_GET['t'] == 'opremazainspekciju' && !ima_permisiju('opremazainspekciju', 'brisanje')) {
        header('Location: opremazainspekciju.php');
        exit;
    }
    if ($_GET['t'] == 'brojacirn' && !ima_permisiju('brojacirn', 'brisanje')) {
        header('Location: brojacirn.php');
        exit;
    }
    if ($_GET['t'] == 'radninalozi' && !ima_permisiju('pregledradnihnaloga', 'brisanje')) {
        header('Location: pregledradnihnaloga.php');
        exit;
    }
    if ($_GET['t'] == 'tipoviizvjestaja' && !ima_permisiju('tipoviizvjestaja', 'brisanje')) {
        header('Location: tipoviizvjestaja.php');
        exit;
    }
    if ($_GET['t'] == 'izvjestaji' && !ima_permisiju('pregledizvjestaja', 'brisanje')) {
        header('Location: pregledizvjestaja.php');
        exit;
    }
    if ($_GET['t'] == 'kontrolori' && !ima_permisiju('kontrolori', 'brisanje')) {
        header('Location: kontrolori.php');
        exit;
    }
    if ($_GET['t'] == 'metodeinspekcije' && !ima_permisiju('metodeinspekcije', 'brisanje')) {
        header('Location: metodeinspekcije.php');
        exit;
    }
    if ($_GET['t'] == 'vrsteinspekcije' && !ima_permisiju('vrsteinspekcije', 'brisanje')) {
        header('Location: vrsteinspekcije.php');
        exit;
    }
    if ($_GET['t'] == 'vrsteuredjaja' && !ima_permisiju('vrsteuredjaja', 'brisanje')) {
        header('Location: vrsteuredjaja.php');
        exit;
    }
    if ($_GET['t'] == 'mjernevelicine') {
        header('Location: mjernevelicine.php');
        exit;
    }
    if ($_GET['t'] == 'referentnevrijednosti' && !ima_permisiju('referentnevrijednosti', 'brisanje')) {
        header('Location: referentnevrijednosti.php');
        exit;
    }
    if ($_GET['t'] == 'nivoihijerarhije' && !ima_permisiju('nivoihijerarhije', 'brisanje')) {
        header('Location: nivoihijerarhije.php');
        exit;
    }
    if ($_GET['t'] == 'korisnickeuloge' && !ima_permisiju('korisnickeuloge', 'brisanje')) {
        header('Location: korisnickeuloge.php');
        exit;
    }
    if ($_GET['t'] == 'korisnici' && !ima_permisiju('korisnici', 'brisanje')) {
        header('Location: korisnici.php');
        exit;
    }
    if ($_GET['t'] == 'rjesenjazaovlascivanje' && !ima_permisiju('rjesenjazaovlascivanje', 'brisanje')) {
        header('Location: rjesenjaovlascivanja.php');
        exit;
    }
    if ($_GET['t'] == 'korisnici' && (int)$_SESSION['user-type'] === 1) {
        $stmtKu = $pdo->prepare("SELECT korisnici_korisnickaulogaid FROM korisnici WHERE korisnici_id = ?");
        $stmtKu->execute([(int)$_GET['o']]);
        $rowKu = $stmtKu->fetch(PDO::FETCH_ASSOC);
        if ($rowKu && in_array((int)$rowKu['korisnici_korisnickaulogaid'], [1, 7], true)) {
            header('Location: korisnici.php');
            exit;
        }
    }
    if ($_GET['t'] == 'izvjestaji' && (int)$_SESSION['user-type'] === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mKlijent)) {
        $stmtIj = $pdo->prepare("SELECT mjerila_klijentid FROM mjerila m INNER JOIN izvjestaji i ON i.izvjestaji_mjeriloid = m.mjerila_id WHERE i.izvjestaji_id = ?");
        $stmtIj->execute([(int)$_GET['o']]);
        $rowIj = $stmtIj->fetch(PDO::FETCH_ASSOC);
        if (!$rowIj || (int)$rowIj['mjerila_klijentid'] !== (int)$mKlijent[1]) {
            header('Location: pregledizvjestaja.php');
            exit;
        }
    }
    if ($_GET['t'] == 'radninalozi' && (int)$_SESSION['user-type'] === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mKlijent)) {
        $stmtRn = $pdo->prepare("SELECT mjerila_klijentid FROM mjerila m INNER JOIN radninalozi r ON r.radninalozi_mjeriloid = m.mjerila_id WHERE r.radninalozi_id = ?");
        $stmtRn->execute([(int)$_GET['o']]);
        $rowRn = $stmtRn->fetch(PDO::FETCH_ASSOC);
        if (!$rowRn || (int)$rowRn['mjerila_klijentid'] !== (int)$mKlijent[1]) {
            header('Location: pregledradnihnaloga.php');
            exit;
        }
    }
    if ($_GET['t'] == 'mjerila' && (int)$_SESSION['user-type'] === 5 && !empty($_SESSION['user']) && preg_match('/^klijent_(\d+)$/', $_SESSION['user'], $mKlijent)) {
        $stmtM = $pdo->prepare("SELECT mjerila_klijentid FROM mjerila WHERE mjerila_id = ?");
        $stmtM->execute(array($_GET['o']));
        $rowM = $stmtM->fetch(PDO::FETCH_ASSOC);
        if (!$rowM || (int)$rowM['mjerila_klijentid'] !== (int)$mKlijent[1]) {
            header('Location: pregledmjerila.php');
            exit;
        }
    }

    $query = $pdo->prepare('DELETE FROM ' . $_GET['t'] . ' WHERE ' . $_GET['t'] . '_id = ' . $_GET['o']);

    switch ($_GET['t']) {
        case "klijenti":
            $finalHeader = 'pregledklijenata.php';
            $tabeleToCheck = array('radninalozi');
            $idToCheck = array('klijentid');
            break;
        case "mjerila":
            $finalHeader = 'pregledmjerila.php';
            $tabeleToCheck = array('radninalozi', 'izvjestaji');
            $idToCheck = array('mjeriloid');
            break;
        case "opremazainspekciju":
            $finalHeader = 'opremazainspekciju.php';
            $tabeleToCheck = array('izvjestaji');
            $idToCheck = array('opremazainspekciju');
            break;
        case "brojacirn":
            $finalHeader = 'brojacirn.php';
            $tabeleToCheck = array('radninalozi');
            $idToCheck = array('brojacrnid');
            break;
        case "radninalozi":
            $finalHeader = 'pregledradnihnaloga.php';
            $tabeleToCheck = array('izvjestaji');
            $idToCheck = array('radninalogid');
            break;
        case "tipoviizvjestaja":
            $finalHeader = 'tipoviizvjestaja.php';
            $tabeleToCheck = array('izvjestaji');
            $idToCheck = array('tipizvjestajaid');
            break;
        case "izvjestaji":
            $finalHeader = 'pregledizvjestaja.php';
            break;
        case "kontrolori":
            $finalHeader = 'kontrolori.php';
            $tabeleToCheck = array('radninalozi', 'izvjestaji');
            $idToCheck = array('kontrolorid', 'otvorioid', 'primioid', 'zatvorioid', 'izvrsioid', 'ovjerioid');
            break;
        case "metodeinspekcije":
            $finalHeader = 'metodeinspekcije.php';
            $tabeleToCheck = array('radninalozi');
            $idToCheck = array('metodainspekcijeid');
            break;
        case "vrsteinspekcije":
            $finalHeader = 'vrsteinspekcije.php';
            $tabeleToCheck = array('izvjestaji');
            $idToCheck = array('vrstainspekcijeid');
            break;
        case "vrsteuredjaja":
            $finalHeader = 'vrsteuredjaja.php';
            $tabeleToCheck = array('radninalozi', 'mjernevelicine');
            $idToCheck = array('vrstauredjajaid');
            break;
        case "mjernevelicine":
            $finalHeader = 'mjernevelicine.php';
            $tabeleToCheck = array('referentnevrijednosti', 'rezultatimjerenja');
            $idToCheck = array('mjernavelicinaid');
            break;
        case "referentnevrijednosti":
            $finalHeader = 'referentnevrijednosti.php';
            $tabeleToCheck = array('rezultatimjerenja');
            $idToCheck = array('referentnavrijednostid');
            break;
        case "nivoihijerarhije":
            $finalHeader = 'nivoihijerarhije.php';
            $tabeleToCheck = array('korisnickeuloge');
            $idToCheck = array('nivohijerarhijeid');
            break;
        case "korisnickeuloge":
            $finalHeader = 'korisnickeuloge.php';
            $tabeleToCheck = array('korisnici');
            $idToCheck = array('korisnickaulogaid');
            break;
        case "korisnici":
            $finalHeader = 'korisnici.php';
            break;
        case "rjesenjazaovlascivanje":
            $finalHeader = 'rjesenjaovlascivanja.php';
            $tabeleToCheck = array();
            $idToCheck = array();
            break;
    }

    $message = false;

    if ($_GET['t'] != "opremazainspekciju" && $_GET['t'] != "izvjestaji") {
        foreach ($tabeleToCheck as $tabelaToCheck) {
            foreach ($idToCheck as $idToCheck_) {
                $provjeraZavisnosti = new allObjectsBy;
                $provjeraZavisnosti = $provjeraZavisnosti->fetch_all_objects_by($tabelaToCheck, $tabelaToCheck . "_" . $idToCheck_, $_GET['o'], $tabelaToCheck . "_id", "ASC");
                if (count($provjeraZavisnosti) > 0) {
                    $message = true;
                }
            }
        }
    } else if ($_GET['t'] == "izvjestaji") {
        $query_ = $pdo->prepare('DELETE FROM rezultatimjerenja WHERE rezultatimjerenja_izvjestajid = ' . $_GET['o']);
        $query_->execute();
    } else {
        foreach ($tabeleToCheck as $tabelaToCheck) {
            foreach ($idToCheck as $idToCheck_) {
                $provjeraZavisnosti = new allObjectsByLike;
                $provjeraZavisnosti = $provjeraZavisnosti->fetch_all_objects_by($tabelaToCheck, $tabelaToCheck . "_" . $idToCheck_, $_GET['o'], $tabelaToCheck . "_id", "ASC");
                if (count($provjeraZavisnosti) > 0) {
                    $message = true;
                }
            }
        }
    }

    //var_dump($page_);

    if ($message == false) {
        $query->execute();
        if ($_GET['t'] == 'klijenti') {
            $delKorisnik = $pdo->prepare("DELETE FROM korisnici WHERE korisnici_username = ?");
            $delKorisnik->execute(array('klijent_' . $_GET['o']));
            $cacheFile = __DIR__ . '/cache/klijenti_count.json';
            if (is_file($cacheFile)) {
                @unlink($cacheFile);
            }
        }
        header("Location: " . $finalHeader);
    } else {
        header("Location: " . $finalHeader . "?message=true");
    }

}

//INCLUDES
include_once ('includes/footer.php');

?>