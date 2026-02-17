<?php
/**
 * Provjera permisija na osnovu uloge korisnika.
 * Administrator (1) i Super administrator (7) uvijek imaju sve dozvoljeno.
 * Ostale uloge provjeravamo u tabeli permisije.
 *
 * @param string $sekcija Ključ sekcije (npr. 'pregledklijenata')
 * @param string $akcija  Ključ akcije (npr. 'pregled', 'dodavanje', 'uredivanje', 'brisanje')
 * @return bool
 */
function ima_permisiju($sekcija, $akcija) {
    if (!isset($_SESSION['user-type'])) {
        return false;
    }
    $uloga = (int) $_SESSION['user-type'];
    if (in_array($uloga, array(1, 7), true)) {
        return true;
    }
    global $pdo;
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        return false;
    }
    try {
        $stmt = $pdo->prepare(
            "SELECT 1 FROM `permisije` WHERE `permisije_uloga_id` = ? AND `permisije_sekcija` = ? AND `permisije_akcija` = ? LIMIT 1"
        );
        $stmt->execute(array($uloga, $sekcija, $akcija));
        return (bool) $stmt->fetch();
    } catch (PDOException $e) {
        return false;
    }
}
