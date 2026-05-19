<?php
/**
 * Setup / migracije baze podataka
 * Na live domenu jednom posjeti rutu /setup.php da se izvrše sve izmjene u bazi.
 * Sve buduće izmjene sheme/ podataka čuvaj u nizu $migrations ispod.
 * Stavka može imati 'sql' (jedan exec) ili 'php' (putanja do skripte koja vrati niz ['ok'=>bool,'message'=>string]).
 */

include_once __DIR__ . '/connection.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    header('Content-Type: text/html; charset=utf-8');
    die('Greška: konekcija na bazu nije dostupna.');
}
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!function_exists('norma_setup_stream_log')) {
    /**
     * Ispis jedne linije u live log (flush u preglednik).
     */
    function norma_setup_stream_log($cssClass, $message)
    {
        echo '<div class="' . htmlspecialchars((string) $cssClass, ENT_QUOTES, 'UTF-8') . '">';
        echo htmlspecialchars('[' . date('H:i:s') . '] ' . (string) $message, ENT_QUOTES, 'UTF-8');
        echo "</div>\n";
        if (ob_get_level() > 0) {
            @ob_flush();
        }
        @flush();
    }
}

@ini_set('implicit_flush', '1');
@ini_set('output_buffering', '0');
@ini_set('zlib.output_compression', '0');
while (ob_get_level() > 0) {
    @ob_end_flush();
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
        body { font-family: sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; }
        h1 { font-size: 1.25rem; }
        #setup-live-log { font-family: Consolas, "Courier New", monospace; font-size: 12px; border: 1px solid #ccc; padding: 10px; margin: 1rem 0; max-height: 380px; overflow-y: auto; background: #fafafa; line-height: 1.45; }
        #setup-live-log .log { color: #333; }
        #setup-live-log .log-ok { color: #070; }
        #setup-live-log .log-err { color: #c00; }
        #setup-live-log .log-skip { color: #666; }
        #setup-live-log .log-step { color: #06c; font-weight: 600; margin-top: 6px; }
        h2 { margin-top: 1.5rem; font-size: 1.1rem; }
        ul { list-style: none; padding: 0; }
        li { padding: 0.5rem 0; border-bottom: 1px solid #eee; }
        .ok { color: #0a0; }
        .err { color: #c00; }
    </style>
</head>
<body>
    <h1>Setup baze podataka</h1>
    <p><strong>Status u hodu</strong> — stranica se puni korak po korak; ne zatvaraj je dok se ne pojavi sažetak na dnu. Ako se dugo ništa ne pojavlja, proxy ili hosting može držati buffer (probaj direktno na serveru ili <code>php includes/cli_backfill_batch.php</code>).</p>
    <?php
    $setupBackfillStats = ['total' => 0, 'nisu_1' => 0, 'offset' => 0];
    try {
        $chkCol = $pdo->query("SHOW COLUMNS FROM `izvjestaji` LIKE 'izvjestaji_nisu_usaglaseni'")->fetch(PDO::FETCH_ASSOC);
        if ($chkCol) {
            $setupBackfillStats['total'] = (int) $pdo->query('SELECT COUNT(*) FROM `izvjestaji`')->fetchColumn();
            $setupBackfillStats['nisu_1'] = (int) $pdo->query(
                'SELECT COUNT(*) FROM `izvjestaji` WHERE `izvjestaji_nisu_usaglaseni` = 1'
            )->fetchColumn();
            $stProg = $pdo->prepare(
                'SELECT `offset` FROM `setup_backfill_progress` WHERE `migration_id` = ? LIMIT 1'
            );
            $stProg->execute(['backfill_izvjestaji_nisu_usaglaseni_v2']);
            $rp = $stProg->fetch(PDO::FETCH_ASSOC);
            if ($rp && isset($rp['offset'])) {
                $setupBackfillStats['offset'] = (int) $rp['offset'];
            }
        }
    } catch (Throwable $e) {
    }
    ?>
    <div style="margin:1rem 0;padding:1rem;border:1px solid #06c;border-radius:8px;background:#f0f7ff;">
        <h2 style="margin:0 0 0.5rem;font-size:1.05rem;">Filter „Nisu usaglašeni” — backfill svih izvještaja</h2>
        <p style="margin:0 0 0.75rem;font-size:0.92rem;">
            Za svaki izvještaj pokreće se ista logika kao pri generisanju PDF-a
            (apsolutno odstupanje u mmHg, ℃, J itd. gdje PDF tako kaže; relativno u % gdje PDF tako kaže).
            Kolona <code>izvjestaji_nisu_usaglaseni</code> se ažurira u bazi.
        </p>
        <p style="margin:0 0 0.5rem;font-size:0.9rem;">
            U bazi: <strong><?php echo (int) $setupBackfillStats['total']; ?></strong> izvještaja,
            trenutno nisu usaglašeni: <strong><?php echo (int) $setupBackfillStats['nisu_1']; ?></strong>.
            <?php if ($setupBackfillStats['offset'] > 0) { ?>
                Offset backfilla: <strong><?php echo (int) $setupBackfillStats['offset']; ?></strong>.
            <?php } ?>
        </p>
        <p style="margin:0;font-size:0.9rem;">
            <a href="setup.php?setup_mode=backfill_nisu_usaglaseni_chunk&amp;force=1" style="display:inline-block;margin-right:8px;padding:8px 12px;background:#06c;color:#fff;text-decoration:none;border-radius:6px;">Pokreni / nastavi backfill (300 po 300)</a>
            <a href="setup.php?setup_mode=backfill_nisu_usaglaseni_chunk&amp;force=1&amp;reset=1" style="display:inline-block;padding:8px 12px;background:#555;color:#fff;text-decoration:none;border-radius:6px;">Od početka (reset)</a>
        </p>
    </div>
    <div id="setup-live-log">
<?php
norma_setup_stream_log('log', 'Konekcija na bazu uspostavljena.');

$results = array();

// Chunk backfill (klikni "Nastavi" da ide 300 po 300)
$setupMode = (string)($_GET['setup_mode'] ?? '');
$onlyBackfillChunk = ($setupMode === 'backfill_nisu_usaglaseni_chunk');
$backfillForce = $onlyBackfillChunk && isset($_GET['force']) && (string) $_GET['force'] === '1';
$backfillReset = $onlyBackfillChunk && isset($_GET['reset']) && (string) $_GET['reset'] === '1';

$backfillMigrationId = 'backfill_izvjestaji_nisu_usaglaseni_v2';
$backfillMigrationIdLegacy = 'backfill_izvjestaji_nisu_usaglaseni';
$backfillChunkLimit = 300;
$stopAfterBackfillChunk = false;
$backfillContinueUrl = '';
$backfillChunkDone = true;

// Defaults za sažetak (da ne bi bilo undefined pri ranom exit-u)
$createdUsers = array();
$zavodUserCreated = false;

// Napredak backfill-a (offset) da možeš nastaviti narednim klikom
$pdo->exec("CREATE TABLE IF NOT EXISTS `setup_backfill_progress` (
    `migration_id` VARCHAR(128) NOT NULL PRIMARY KEY,
    `offset` INT UNSIGNED NOT NULL DEFAULT 0,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$stmtBackfillProgGet = $pdo->prepare("SELECT `offset` FROM `setup_backfill_progress` WHERE `migration_id` = ? LIMIT 1");
$stmtBackfillProgUpsert = $pdo->prepare("INSERT INTO `setup_backfill_progress` (`migration_id`, `offset`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `offset` = VALUES(`offset`)");
$stmtBackfillProgDelete = $pdo->prepare("DELETE FROM `setup_backfill_progress` WHERE `migration_id` = ?");

if ($backfillReset) {
    $stmtDelMig = $pdo->prepare('DELETE FROM `setup_migrations` WHERE `migration_id` = ?');
    foreach (array($backfillMigrationId, $backfillMigrationIdLegacy) as $midReset) {
        $stmtBackfillProgDelete->execute(array($midReset));
        $stmtDelMig->execute(array($midReset));
    }
}

$backfillOffset = 0;
$stmtBackfillProgGet->execute(array($backfillMigrationId));
$rowProg = $stmtBackfillProgGet->fetch(PDO::FETCH_ASSOC);
if ($rowProg && isset($rowProg['offset'])) {
    $backfillOffset = (int) $rowProg['offset'];
}
$GLOBALS['norma_setup_backfill_chunk_limit'] = $backfillChunkLimit;
$GLOBALS['norma_setup_backfill_chunk_offset'] = $backfillOffset;

// Tablica za evidenciju odrađenih migracija (svaka migracija se izvrši samo jednom)
$pdo->exec("CREATE TABLE IF NOT EXISTS `setup_migrations` (
    `migration_id` VARCHAR(128) NOT NULL PRIMARY KEY,
    `executed_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
norma_setup_stream_log('log', 'Tablica setup_migrations provjerena / kreirana.');

$stmtCheck = $pdo->prepare("SELECT 1 FROM `setup_migrations` WHERE `migration_id` = ? LIMIT 1");
$stmtInsert = $pdo->prepare("INSERT INTO `setup_migrations` (`migration_id`) VALUES (?)");

$GLOBALS['norma_setup_progress_callback'] = function ($current, $total, $reportId) {
    norma_setup_stream_log(
        'log',
        'Gotov izvještaj ID ' . (int) $reportId . ' (' . (int) $current . '/' . (int) $total . ').'
    );
};

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
        'id'   => 'izvjestaji_mjeriloneispravno',
        'name' => 'Kolona izvjestaji_mjeriloneispravno (mjerilo neispravno na izvještaju)',
        'sql'  => "ALTER TABLE `izvjestaji` ADD COLUMN `izvjestaji_mjeriloneispravno` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `izvjestaji_mjerilokablovi`"
    ),
    array(
        'id'   => 'izvjestaji_nisu_usaglaseni',
        'name' => 'Kolona izvjestaji_nisu_usaglaseni (zaključak mjerenja: nisu usaglašeni – filter/pregled)',
        'sql'  => "ALTER TABLE `izvjestaji` ADD COLUMN `izvjestaji_nisu_usaglaseni` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `izvjestaji_mjeriloneispravno`"
    ),
    array(
        'id'   => 'mjerila_neispravno',
        'name' => 'Kolona mjerila_neispravno (sinkronizacija s izvještajem)',
        'sql'  => "ALTER TABLE `mjerila` ADD COLUMN `mjerila_neispravno` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `mjerila_zadovoljava`"
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
    array(
        'id'   => 'backfill_izvjestaji_nisu_usaglaseni_v2',
        'name' => 'Backfill izvjestaji_nisu_usaglaseni v2 (apsolutno/relativno po mjernoj veličini — kao PDF)',
        'php'  => __DIR__ . '/includes/migration_backfill_izvjestaji_nisu_usaglaseni.php',
    ),
);

// U modu "klikni Nastavi" izvršavamo samo backfill migraciju.
if ($onlyBackfillChunk) {
    $migrations = array_values(array_filter($migrations, function ($m) use ($backfillMigrationId) {
        return isset($m['id']) && $m['id'] === $backfillMigrationId;
    }));
}

foreach ($migrations as $m) {
    norma_setup_stream_log('log-step', 'Migracija: ' . $m['name']);
    $migrationId = isset($m['id']) ? $m['id'] : null;
    if ($migrationId !== null) {
        $skipIfDone = !($onlyBackfillChunk && $backfillForce && $migrationId === $backfillMigrationId);
        if ($skipIfDone) {
            $stmtCheck->execute(array($migrationId));
            if ($stmtCheck->fetch()) {
                $results[] = array('name' => $m['name'], 'ok' => true, 'message' => 'preskočeno (već odrađeno)');
                norma_setup_stream_log('log-skip', 'Preskočeno (već odrađeno): ' . $m['name']);
                continue;
            }
        }
    }
    try {
        if (!empty($m['php'])) {
            $phpPath = $m['php'];
            if (!is_string($phpPath) || !is_readable($phpPath)) {
                throw new PDOException('PHP migracija: put nije čitljiv: ' . $phpPath);
            }
            @set_time_limit(0);
            @ignore_user_abort(true);
            $ret = include $phpPath;
            if (!is_array($ret) || !array_key_exists('ok', $ret)) {
                throw new PDOException('PHP migracija mora vratiti niz s ključem ok');
            }
            if ($ret['ok'] !== true) {
                $msg = isset($ret['message']) ? $ret['message'] : 'Neuspjeh';
                throw new PDOException($msg);
            }
            $okMsg = isset($ret['message']) ? $ret['message'] : 'OK';
            $done = ($ret['done'] ?? true) === true;
            $nextOffset = isset($ret['next_offset']) ? (int) $ret['next_offset'] : null;
            if ($migrationId !== null) {
                if ($migrationId === $backfillMigrationId) {
                    if ($done) {
                        $stmtBackfillProgDelete->execute(array($migrationId));
                        $stmtInsert->execute(array($migrationId));
                    } else {
                        $stmtBackfillProgUpsert->execute(array($migrationId, (int) ($nextOffset ?? 0)));
                        $stopAfterBackfillChunk = true;
                        $backfillChunkDone = false;
                        $backfillContinueUrl = 'setup.php?setup_mode=backfill_nisu_usaglaseni_chunk'
                            . ($backfillForce ? '&force=1' : '');
                        $okMsg .= ' (klikni Nastavi za sljedećih ' . (int)$backfillChunkLimit . ')';
                    }
                } else {
                    $stmtInsert->execute(array($migrationId));
                }
            }
            $results[] = array('name' => $m['name'], 'ok' => true, 'message' => $okMsg);
        } elseif (isset($m['sql'])) {
            $pdo->exec($m['sql']);
            if ($migrationId !== null) {
                $stmtInsert->execute(array($migrationId));
            }
            $results[] = array('name' => $m['name'], 'ok' => true, 'message' => 'OK');
        } else {
            throw new PDOException('Migracija mora imati ključ sql ili php');
        }
    } catch (Throwable $e) {
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
    $lastR = end($results);
    if ($lastR !== false) {
        $shortMsg = $lastR['message'];
        if (function_exists('mb_strlen') && mb_strlen($shortMsg, 'UTF-8') > 140) {
            $shortMsg = mb_substr($shortMsg, 0, 137, 'UTF-8') . '…';
        } elseif (strlen($shortMsg) > 140) {
            $shortMsg = substr($shortMsg, 0, 137) . '…';
        }
        norma_setup_stream_log($lastR['ok'] ? 'log-ok' : 'log-err', $lastR['name'] . ' — ' . $shortMsg);
    }

    if ($stopAfterBackfillChunk) {
        break;
    }
}

unset($GLOBALS['norma_setup_progress_callback']);

// Ako je backfill u "klikni Nastavi" modu, ne idemo na ostale faze.
if ($onlyBackfillChunk || $stopAfterBackfillChunk) {
    $backfillRestartUrl = 'setup.php?setup_mode=backfill_nisu_usaglaseni_chunk&force=1&reset=1';
    if ($stopAfterBackfillChunk && !$backfillChunkDone && !empty($backfillContinueUrl)) {
        echo '<p style="margin: 1rem 0; padding: 0 0.5rem;"><a href="' . htmlspecialchars($backfillContinueUrl, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;padding:10px 14px;background:#06c;color:#fff;text-decoration:none;border-radius:6px;">Nastavi backfill (sljedećih ' . (int)$backfillChunkLimit . ')</a></p>';
        echo '<p style="margin: 0.5rem 0; padding: 0 0.5rem; font-size: 0.9rem;">Filter „Nisu usaglašeni” sada prati istu logiku kao PDF (apsolutno u jedinicama mjere gdje treba, relativno u % gdje treba). Nakon deploya obavezno pokreni backfill v2.</p>';
    } else {
        echo '<p style="margin: 1rem 0; padding: 0 0.5rem;"><strong>Backfill završena.</strong></p>';
        echo '<p style="margin: 0.5rem 0; padding: 0 0.5rem;"><a href="' . htmlspecialchars($backfillRestartUrl, ENT_QUOTES, 'UTF-8') . '">Ponovi cijeli backfill od početka</a> (ako želiš ponovno prebrojati sve nakon novog deploya).</p>';
    }
    echo "</div>";
    echo "</body></html>";
    exit;
}

norma_setup_stream_log('log-step', 'Faza: permisije za uloge korisnika…');
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
    norma_setup_stream_log('log-ok', 'Permisije ažurirane.');
} catch (Throwable $e) {
    $results[] = array('name' => 'Permisije: sve vrste – sve dopušteno', 'ok' => false, 'message' => $e->getMessage());
    norma_setup_stream_log('log-err', 'Permisije: ' . $e->getMessage());
}

norma_setup_stream_log('log-step', 'Faza: korisnički nalozi (klijenti / Zavod)…');
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

norma_setup_stream_log('log', 'Korisnički nalozi provjereni / kreirani po potrebi.');
norma_setup_stream_log('log-step', 'Završeno. Sažetak ispod.');

?>
    </div>

    <h2>Rezime migracija i faza</h2>
    <p>Lista rezultata (isto što je i u hodu iznad, pregledno):</p>
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
