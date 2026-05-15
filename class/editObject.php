<?php

if (!isset($_POST['username']) && !isset($_POST['password']) && !isset($_POST['edit_izvjestaji'])) {

    $nazivTabele = '';
    foreach ($_POST as $key => $_v) {
        if (strpos($key, 'edit_') === 0 && $key !== 'edit_izvjestaji') {
            $parts = explode('_', $key, 2);
            if (!empty($parts[1]) && preg_match('/^[a-zA-Z0-9_]+$/', $parts[1])) {
                $nazivTabele = $parts[1];
            }
            break;
        }
    }

    $pk = $nazivTabele !== '' ? $nazivTabele . '_id' : '';
    $objectId = ($pk !== '' && array_key_exists($pk, $_POST)) ? $_POST[$pk] : null;

    if ($nazivTabele !== '' && $objectId !== null && $objectId !== '') {
        try {
            $stmtDesc = $pdo->prepare('DESCRIBE `' . str_replace('`', '``', $nazivTabele) . '`');
            $stmtDesc->execute();
            $columnNames = $stmtDesc->fetchAll(PDO::FETCH_COLUMN, 0);
        } catch (Throwable $e) {
            $columnNames = array();
        }

        if ($columnNames !== array()) {
            $allowedCols = array_flip($columnNames);
            $setParts = array();
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'edit_') === 0) {
                    continue;
                }
                if ($key === $pk) {
                    continue;
                }
                if (!isset($allowedCols[$key])) {
                    continue;
                }
                if ($key === 'korisnici_password' && (string)$value === '') {
                    continue;
                }
                if ($key === 'korisnici_password' && (string)$value !== '') {
                    $value = md5((string)$value);
                }
                $colQ = '`' . str_replace('`', '``', $key) . '`';
                if (substr($key, -2) === 'id' && $value === '') {
                    $setParts[] = $colQ . ' = NULL';
                } else {
                    $setParts[] = $colQ . " = '" . str_replace("'", "''", (string)$value) . "'";
                }
            }

            $editString = implode(', ', $setParts);
            if ($editString !== '') {
                $oid = (int)$objectId;
                if ($oid > 0) {
                    $pkQ = '`' . str_replace('`', '``', $pk) . '`';
                    $tblQ = '`' . str_replace('`', '``', $nazivTabele) . '`';
                    $query = $pdo->prepare('UPDATE ' . $tblQ . ' SET ' . $editString . ' WHERE ' . $pkQ . ' = ' . $oid);
                    $query->execute();
                }
            }
        }
    }
}

if (isset($_POST['edit_izvjestaji'])) {

    $izvjestaji_id = (int)($_POST['izvjestaji_id'] ?? 0);

    //GET CERTAIN RESULT
    $stmt = $pdo->prepare('SELECT * FROM rezultatimjerenja 
    WHERE rezultatimjerenja_izvjestajid = :izvjestajid 
      AND rezultatimjerenja_mjernavelicinaid = :mjernavelicinaid 
      AND rezultatimjerenja_referentnavrijednostid = :referentnavrijednostid 
      AND rezultatimjerenja_brojmjerenja = :brojmjerenja');

    foreach ($_POST as $key => $value) {
        if (substr($key, 0, 8) !== "rezultat") {
            continue;
        }
        if ($value == "-") {
            continue;
        }
        $pieces = explode("_", $key);
        if (count($pieces) < 4 || $pieces[0] !== 'rezultat') {
            continue;
        }
        if (!ctype_digit((string)$pieces[1]) || !ctype_digit((string)$pieces[2]) || !ctype_digit((string)$pieces[3])) {
            continue;
        }
        $editString_ = "";
        $editString_ .= "rezultatimjerenja_rezultatmjerenja = " . "'" . str_replace("'", "''", (string)$value) . "'";
        $stmt->execute([
            ':izvjestajid' => $izvjestaji_id,
            ':mjernavelicinaid' => $pieces[1],
            ':referentnavrijednostid' => $pieces[2],
            ':brojmjerenja' => $pieces[3]
        ]);
        $selectRezultat = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($selectRezultat != false) {
            $queryRezultat = $pdo->prepare('UPDATE rezultatimjerenja SET ' . $editString_ . ' WHERE 
                rezultatimjerenja_izvjestajid = ' . $izvjestaji_id . ' 
                AND rezultatimjerenja_mjernavelicinaid = ' . $pieces[1] . ' 
                AND rezultatimjerenja_referentnavrijednostid = ' . $pieces[2] . ' 
                AND rezultatimjerenja_brojmjerenja = ' . $pieces[3]);
        } else {
            $queryRezultat = $pdo->prepare('INSERT INTO rezultatimjerenja 
                (rezultatimjerenja_izvjestajid, rezultatimjerenja_mjernavelicinaid, rezultatimjerenja_referentnavrijednostid, rezultatimjerenja_brojmjerenja, rezultatimjerenja_rezultatmjerenja)
                VALUES (' . $izvjestaji_id . ', ' . $pieces[1] . ', ' . $pieces[2] . ', ' . $pieces[3] . ", '" . str_replace("'", "''", (string)$value) . "')");
        }
        $queryRezultat->execute();
    }

    foreach ($_POST as $key => $value) {
        if ($value != "-" || substr($key, 0, 8) !== "rezultat") {
            continue;
        }
        $pieces = explode("_", $key);
        if (count($pieces) < 4 || $pieces[0] !== 'rezultat') {
            continue;
        }
        if (!ctype_digit((string)$pieces[1]) || !ctype_digit((string)$pieces[2]) || !ctype_digit((string)$pieces[3])) {
            continue;
        }
        $stmt->execute([
            ':izvjestajid' => $izvjestaji_id,
            ':mjernavelicinaid' => $pieces[1],
            ':referentnavrijednostid' => $pieces[2],
            ':brojmjerenja' => $pieces[3]
        ]);
        $selectRezultat = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($selectRezultat != false) {
            $queryRezultat = $pdo->prepare('DELETE FROM rezultatimjerenja WHERE  rezultatimjerenja_izvjestajid = ' . $izvjestaji_id . '  AND rezultatimjerenja_mjernavelicinaid = ' . $pieces[1] . '  AND rezultatimjerenja_referentnavrijednostid = ' . $pieces[2] . '  AND rezultatimjerenja_brojmjerenja = ' . $pieces[3]);
            $queryRezultat->execute();
        }
    }

    $setParts = array();
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'izvjestaji_') !== 0 || $key === 'izvjestaji_id') {
            continue;
        }
        $setParts[] = $key . "='" . str_replace("'", "''", (string)$value) . "'";
    }
    if (!empty($setParts) && $izvjestaji_id > 0) {
        $editString = implode(', ', $setParts);
        $queryIzvjestaj = $pdo->prepare('UPDATE izvjestaji SET ' . $editString . ' WHERE izvjestaji_id = ' . $izvjestaji_id);
        $queryIzvjestaj->execute();
    }

    // Djelomični POST: ako je poslan zaključak usaglašenosti, uskladi i „mjerilo neispravno” (isti flag).
    if (array_key_exists('izvjestaji_nisu_usaglaseni', $_POST) && $izvjestaji_id > 0) {
        $vSync = ((string) $_POST['izvjestaji_nisu_usaglaseni'] === '1') ? 1 : 0;
        $stSync = $pdo->prepare(
            'UPDATE izvjestaji SET izvjestaji_mjeriloneispravno = ?, izvjestaji_nisu_usaglaseni = ? WHERE izvjestaji_id = ? LIMIT 1'
        );
        $stSync->execute(array($vSync, $vSync, $izvjestaji_id));
        $_POST['izvjestaji_mjeriloneispravno'] = (string) $vSync;
    }

    if (array_key_exists('izvjestaji_mjeriloneispravno', $_POST)) {
        $mId = isset($_POST['izvjestaji_mjeriloid']) ? (int)$_POST['izvjestaji_mjeriloid'] : 0;
        if ($mId === 0 && $izvjestaji_id > 0) {
            $stM = $pdo->prepare('SELECT izvjestaji_mjeriloid FROM izvjestaji WHERE izvjestaji_id = ? LIMIT 1');
            $stM->execute(array($izvjestaji_id));
            $rM = $stM->fetch(PDO::FETCH_ASSOC);
            if ($rM) {
                $mId = (int)$rM['izvjestaji_mjeriloid'];
            }
        }
        if ($mId > 0) {
            $vNeispravno = ((string)$_POST['izvjestaji_mjeriloneispravno'] === '1') ? 1 : 0;
            $updMjerila = $pdo->prepare('UPDATE mjerila SET mjerila_neispravno = ? WHERE mjerila_id = ? LIMIT 1');
            $updMjerila->execute(array($vNeispravno, $mId));
        }
    }

    header('Location: pregledizvjestaja.php?page=1');
}


?>
