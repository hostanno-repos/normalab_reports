<?php

if (!isset($_POST['username']) && !isset($_POST['password']) && !isset($_POST['edit_izvjestaji'])) {

    global $editString;
    $editString = "";
    $nazivTabele = "";
    $objectId = null;

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

    // INSERT INTO TABLE START
    $i = 1;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'edit') !== false) {
            $parts = explode("_", $key, 2);
            $nazivTabele = isset($parts[1]) ? trim($parts[1]) : "";
            $i++;
        } else {
            if (explode("_", $key)[1] == "id") {
                $objectId = $value;
            }
            if ($key == "korisnici_password") {
                $$key = md5($value);
            } else {
                if (substr($key, -2) == "id" && $value == "") {
                    $$key = (int) NULL;
                } else {
                    $$key = $value;
                }

            }
            if ($i < count($_POST) - 1) {
                $editString = $editString . " " . $key . " = '" . $$key . "', ";
                $i++;
            } else {
                $editString = $editString . " " . $key . " = '" . $$key . "'";
                $i++;
            }
        }
    }

    // Izvršavaj UPDATE samo ako imamo valjani naziv tabele i SET dio (izbjegava SQL grešku "near ''")
    if ($nazivTabele !== "" && $editString !== "" && $objectId !== null) {
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

    $query = $pdo->prepare('UPDATE ' . $nazivTabele . ' SET ' . $editString . ' WHERE ' . $nazivTabele . '_id = ' . $objectId);
    //print_r($query);
    //die();
    $query->execute();
    }

}

if (isset($_POST['edit_izvjestaji'])) {

    //GET CERTAIN RESULT
    $stmt = $pdo->prepare('SELECT * FROM rezultatimjerenja 
    WHERE rezultatimjerenja_izvjestajid = :izvjestajid 
      AND rezultatimjerenja_mjernavelicinaid = :mjernavelicinaid 
      AND rezultatimjerenja_referentnavrijednostid = :referentnavrijednostid 
      AND rezultatimjerenja_brojmjerenja = :brojmjerenja');

    $broj = 0;

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'izvjestaji_') === 0) {
            //print_r($key."=".$value . "</br>");
            $broj++;
        }
    }
    //var_dump($broj);
    $editString = "";
    $i = 1;
    foreach ($_POST as $key => $value) {
        if($value != "-" || strpos($key, 'izvjestaji_') === 0){
            if (substr($key, 0, 10) === "izvjestaji") {
                //var_dump($key);
                $$key = $value;
                if ($i < ($broj)) {
                    $editString .= $key . "='" . $$key . "', ";
                    $i++;
                } else {
                    $editString .= $key . "='" . $$key . "'";
                    $i++;
                }

            } else if (substr($key, 0, 8) === "rezultat") {
                $editString_ = "";
                $pieces = explode("_", $key);
                //var_dump($pieces[1]);
                //var_dump($pieces[2]);
                //var_dump($pieces[3]);
                $editString_ .= "rezultatimjerenja_rezultatmjerenja = " . "'" . $value . "'";
                $stmt->execute([
                    ':izvjestajid' => $_POST['izvjestaji_id'],
                    ':mjernavelicinaid' => $pieces[1],
                    ':referentnavrijednostid' => $pieces[2],
                    ':brojmjerenja' => $pieces[3]
                ]);
                $selectRezultat = $stmt->fetch(PDO::FETCH_ASSOC);
                //var_dump($selectRezultat);
                if ($selectRezultat != false) {
                    $queryRezultat = $pdo->prepare('UPDATE rezultatimjerenja SET ' . $editString_ . ' WHERE 
                        rezultatimjerenja_izvjestajid = ' . $_POST['izvjestaji_id'] . ' 
                        AND rezultatimjerenja_mjernavelicinaid = ' . $pieces[1] . ' 
                        AND rezultatimjerenja_referentnavrijednostid = ' . $pieces[2] . ' 
                        AND rezultatimjerenja_brojmjerenja = ' . $pieces[3]);
                } else {
                    $queryRezultat = $pdo->prepare('INSERT INTO rezultatimjerenja 
                        (rezultatimjerenja_izvjestajid, rezultatimjerenja_mjernavelicinaid, rezultatimjerenja_referentnavrijednostid, rezultatimjerenja_brojmjerenja, rezultatimjerenja_rezultatmjerenja)
                        VALUES (' . $_POST['izvjestaji_id'] . ', ' . $pieces[1] . ', ' . $pieces[2] . ', ' . $pieces[3] . ", '" . $value . "')");
                }

                //print_r($queryRezultat);
                //die();
                $queryRezultat->execute();
            }
        }else if($value == "-" && substr($key, 0, 8) === "rezultat"){
            $pieces = explode("_", $key);
            $stmt->execute([
                ':izvjestajid' => $_POST['izvjestaji_id'],
                ':mjernavelicinaid' => $pieces[1],
                ':referentnavrijednostid' => $pieces[2],
                ':brojmjerenja' => $pieces[3]
            ]);
            if ($selectRezultat != false) {
                $queryRezultat = $pdo->prepare('DELETE FROM rezultatimjerenja WHERE  rezultatimjerenja_izvjestajid = ' . $_POST['izvjestaji_id'] . '  AND rezultatimjerenja_mjernavelicinaid = ' . $pieces[1] . '  AND rezultatimjerenja_referentnavrijednostid = ' . $pieces[2] . '  AND rezultatimjerenja_brojmjerenja = ' . $pieces[3]);
                $queryRezultat->execute();
            }
        }

    }
    $queryIzvjestaj = $pdo->prepare('UPDATE izvjestaji SET ' . $editString . ' WHERE izvjestaji_id = ' . $izvjestaji_id);
    //print_r($queryIzvjestaj);
    //die();
    $queryIzvjestaj->execute();
    header('Location: pregledizvjestaja.php?page=1');
}


?>