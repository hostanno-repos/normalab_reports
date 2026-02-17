<?php
/**
 * Setup / migracije baze podataka
 * Na live domenu jednom posjeti rutu /setup.php da se izvrše sve izmjene u bazi.
 * Sve buduće izmjene sheme/ podataka čuvaj u nizu $migrations ispod.
 */

include_once __DIR__ . '/connection.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    header('Content-Type: text/html; charset=utf-8');
    die('Greška: konekcija na bazu nije dostupna.');
}
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$results = array();

// Tablica za evidenciju odrađenih migracija (svaka migracija se izvrši samo jednom)
$pdo->exec("CREATE TABLE IF NOT EXISTS `setup_migrations` (
    `migration_id` VARCHAR(128) NOT NULL PRIMARY KEY,
    `executed_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$stmtCheck = $pdo->prepare("SELECT 1 FROM `setup_migrations` WHERE `migration_id` = ? LIMIT 1");
$stmtInsert = $pdo->prepare("INSERT INTO `setup_migrations` (`migration_id`) VALUES (?)");

$migrations = array(
    array(
        'id'   => 'korisnickeuloge_klijent',
        'name' => 'Dodavanje vrste korisnika Klijent (korisnickeuloge)',
        'sql'  => "INSERT IGNORE INTO `korisnickeuloge` (`korisnickeuloge_id`, `korisnickeuloge_naziv`, `korisnickeuloge_nivohijerarhijeid`) VALUES (5, 'Klijent', 5)"
    ),
    array(
        'id'   => 'korisnici_lozinka_prikaz',
        'name' => 'Kolona korisnici_lozinka_prikaz (prikaz lozinke za admina)',
        'sql'  => "ALTER TABLE `korisnici` ADD COLUMN `korisnici_lozinka_prikaz` VARCHAR(255) DEFAULT NULL"
    ),
    array(
        'id'   => 'klijenti_naziv_index',
        'name' => 'Indeks na klijenti.klijenti_naziv (pretraga/sortiranje)',
        'sql'  => "ALTER TABLE `klijenti` ADD INDEX `idx_klijenti_naziv` (`klijenti_naziv`(100))"
    ),
    array(
        'id'   => 'korisnickeuloge_zavod',
        'name' => 'Dodavanje vrste korisnika Zavod (korisnickeuloge)',
        'sql'  => "INSERT IGNORE INTO `korisnickeuloge` (`korisnickeuloge_id`, `korisnickeuloge_naziv`, `korisnickeuloge_nivohijerarhijeid`) VALUES (6, 'Zavod', 5)"
    ),
    array(
        'id'   => 'korisnickeuloge_superadmin',
        'name' => 'Dodavanje vrste korisnika Super administrator (korisnickeuloge)',
        'sql'  => "INSERT IGNORE INTO `korisnickeuloge` (`korisnickeuloge_id`, `korisnickeuloge_naziv`, `korisnickeuloge_nivohijerarhijeid`) VALUES (7, 'Super administrator', 1)"
    ),
    array(
        'id'   => 'korisnici_superadmin_ljuban',
        'name' => 'Dodjela uloge Super administrator korisniku ljuban.jajcanin',
        'sql'  => "UPDATE `korisnici` SET `korisnici_korisnickaulogaid` = 7 WHERE `korisnici_username` = 'ljuban.jajcanin'"
    ),
    array(
        'id'   => 'permisije_tablica',
        'name' => 'Tablica permisije (dodjele za Klijent i Zavod)',
        'sql'  => "CREATE TABLE IF NOT EXISTS `permisije` (
            `permisije_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `permisije_uloga_id` INT UNSIGNED NOT NULL,
            `permisije_sekcija` VARCHAR(100) NOT NULL,
            `permisije_akcija` VARCHAR(50) NOT NULL,
            PRIMARY KEY (`permisije_id`),
            UNIQUE KEY `permisije_uloga_sekcija_akcija` (`permisije_uloga_id`, `permisije_sekcija`, `permisije_akcija`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ),
    array(
        'id'   => 'izvjestaji_lokacijamjerila',
        'name' => 'Kolona izvjestaji_lokacijamjerila (lokacija mjerila na izvještaju)',
        'sql'  => "ALTER TABLE `izvjestaji` ADD COLUMN `izvjestaji_lokacijamjerila` VARCHAR(255) NULL DEFAULT NULL AFTER `izvjestaji_mjestoinspekcije`"
    ),
    array(
        'id'   => 'rjesenjazaovlascivanje_tablica',
        'name' => 'Tablica rjesenjazaovlascivanje (rješenja o ovlašćivanju – broj i datum za izvještaje)',
        'sql'  => "CREATE TABLE IF NOT EXISTS `rjesenjazaovlascivanje` (
            `rjesenjazaovlascivanje_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `rjesenjazaovlascivanje_broj_rjesenja` VARCHAR(128) NOT NULL,
            `rjesenjazaovlascivanje_datum_izdavanja` DATE NOT NULL,
            PRIMARY KEY (`rjesenjazaovlascivanje_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ),
);

foreach ($migrations as $m) {
    $migrationId = isset($m['id']) ? $m['id'] : null;
    if ($migrationId !== null) {
        $stmtCheck->execute(array($migrationId));
        if ($stmtCheck->fetch()) {
            $results[] = array('name' => $m['name'], 'ok' => true, 'message' => 'preskočeno (već odrađeno)');
            continue;
        }
    }
    try {
        $pdo->exec($m['sql']);
        if ($migrationId !== null) {
            $stmtInsert->execute(array($migrationId));
        }
        $results[] = array('name' => $m['name'], 'ok' => true, 'message' => 'OK');
    } catch (PDOException $e) {
        $isDuplicate = ($e->getCode() == '42S21' || strpos($e->getMessage(), 'Duplicate column') !== false || strpos($e->getMessage(), 'Duplicate key') !== false);
        if ($isDuplicate && $migrationId !== null) {
            $stmtInsert->execute(array($migrationId));
            $results[] = array('name' => $m['name'], 'ok' => true, 'message' => 'OK (već postojalo u bazi, zabilježeno)');
        } else if ($isDuplicate) {
            $results[] = array('name' => $m['name'], 'ok' => true, 'message' => 'OK (već postoji)');
        } else {
            $results[] = array('name' => $m['name'], 'ok' => false, 'message' => $e->getMessage());
        }
    }
}

// Permisije: svima (osim Administrator i Super administrator) uključi sve sekcije i akcije
try {
    include_once __DIR__ . '/includes/permisije_config.php';
    $stmtUloge = $pdo->query("SELECT `korisnickeuloge_id` FROM `korisnickeuloge` WHERE `korisnickeuloge_id` NOT IN (1, 7)");
    $uloge = $stmtUloge->fetchAll(PDO::FETCH_COLUMN);
    $insPerm = $pdo->prepare("INSERT IGNORE INTO `permisije` (`permisije_uloga_id`, `permisije_sekcija`, `permisije_akcija`) VALUES (?, ?, ?)");
    foreach ($uloge as $ulogaId) {
        foreach ($PERMISIJE_SEKCIJE as $s) {
            foreach ($PERMISIJE_AKCIJE as $a) {
                $insPerm->execute(array($ulogaId, $s['kljuc'], $a['kljuc']));
            }
        }
    }
    $results[] = array('name' => 'Permisije: sve vrste korisnika – sve dopušteno (sačuvano)', 'ok' => true, 'message' => 'OK');
} catch (Throwable $e) {
    $results[] = array('name' => 'Permisije: sve vrste – sve dopušteno', 'ok' => false, 'message' => $e->getMessage());
}

$createdUsers = array();
$stmtKlijenti = $pdo->query("SELECT `klijenti_id`, `klijenti_naziv` FROM `klijenti` ORDER BY `klijenti_id` ASC");
$klijenti = $stmtKlijenti->fetchAll(PDO::FETCH_ASSOC);
foreach ($klijenti as $k) {
    $username = 'klijent_' . $k['klijenti_id'];
    $check = $pdo->prepare("SELECT 1 FROM `korisnici` WHERE `korisnici_username` = ? LIMIT 1");
    $check->execute(array($username));
    if ($check->fetch()) {
        continue;
    }
    $plainPassword = substr(bin2hex(random_bytes(8)), 0, 12);
    $hashPassword = md5($plainPassword);
    $naziv = $k['klijenti_naziv'];
    if (mb_strlen($naziv) > 255) {
        $naziv = mb_substr($naziv, 0, 252) . '...';
    }
    $ins = $pdo->prepare("INSERT INTO `korisnici` (`korisnici_ime`, `korisnici_prezime`, `korisnici_telefon`, `korisnici_email`, `korisnici_username`, `korisnici_password`, `korisnici_korisnickaulogaid`, `korisnici_lozinka_prikaz`) VALUES (?, ?, ?, ?, ?, ?, 5, ?)");
    $ins->execute(array('Klijent', $naziv, '', '', $username, $hashPassword, $plainPassword));
    $createdUsers[] = array('username' => $username, 'naziv' => $k['klijenti_naziv']);
}

$zavodUserCreated = false;
$checkZavod = $pdo->prepare("SELECT 1 FROM `korisnici` WHERE `korisnici_username` = 'zavod' LIMIT 1");
$checkZavod->execute();
if (!$checkZavod->fetch()) {
    $plainZavod = substr(bin2hex(random_bytes(8)), 0, 12);
    $hashZavod = md5($plainZavod);
    $insZavod = $pdo->prepare("INSERT INTO `korisnici` (`korisnici_ime`, `korisnici_prezime`, `korisnici_telefon`, `korisnici_email`, `korisnici_username`, `korisnici_password`, `korisnici_korisnickaulogaid`, `korisnici_lozinka_prikaz`) VALUES (?, ?, ?, ?, 'zavod', ?, 6, ?)");
    $insZavod->execute(array('Zavod', 'Zavod', '', '', $hashZavod, $plainZavod));
    $zavodUserCreated = array('username' => 'zavod', 'password' => $plainZavod);
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup baze – NormaLab</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; }
        h1 { font-size: 1.25rem; }
        ul { list-style: none; padding: 0; }
        li { padding: 0.5rem 0; border-bottom: 1px solid #eee; }
        .ok { color: #0a0; }
        .err { color: #c00; }
    </style>
</head>
<body>
    <h1>Setup baze podataka</h1>
    <p>Izmjene u bazi:</p>
    <ul>
        <?php foreach ($results as $r) { ?>
        <li class="<?php echo $r['ok'] ? 'ok' : 'err'; ?>">
            <?php echo htmlspecialchars($r['name']); ?> — <?php echo $r['ok'] ? htmlspecialchars($r['message']) : htmlspecialchars($r['message']); ?>
        </li>
        <?php } ?>
    </ul>
    <?php if (count(array_filter($results, function($x) { return !$x['ok']; })) === 0) { ?>
    <p><strong>Sve izmjene su uspješno primijenjene.</strong></p>
    <?php } ?>
    <?php if (!empty($createdUsers)) { ?>
    <h2 style="margin-top:1.5rem;">Kreirani korisnički nalozi za klijente</h2>
    <p>Ukupno kreirano: <?php echo count($createdUsers); ?> naloga (korisničko ime: klijent_ID). Lozinke vidi u admin panelu → Korisnici.</p>
    <ul>
        <?php foreach ($createdUsers as $u) { ?>
        <li><strong><?php echo htmlspecialchars($u['username']); ?></strong> — <?php echo htmlspecialchars($u['naziv']); ?></li>
        <?php } ?>
    </ul>
    <?php } ?>
    <?php if ($zavodUserCreated) { ?>
    <h2 style="margin-top:1.5rem;">Korisnik Zavod</h2>
    <p>Kreiran korisnik <strong>zavod</strong>. Lozinka: <strong><?php echo htmlspecialchars($zavodUserCreated['password']); ?></strong> (vidljiva i u admin panelu → Korisnici).</p>
    <?php } ?>
</body>
</html>
