<?php
/**
 * Podešavanja aplikacije – vidljivo samo Administrator i Super administrator.
 * Permisije: određuju se za sve vrste korisnika osim Administratora i Super administratora (njima je uvijek sve dozvoljeno).
 */

include_once 'includes/head.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == '') {
    header('Location: index.php');
    exit;
}

if (!in_array((int)$_SESSION['user-type'], [1, 7], true)) {
    header('Location: index.php');
    exit;
}

include_once 'includes/permisije_config.php';
include_once 'includes/header.php';
include_once 'includes/sidebar.php';

// Uloge kojima se dodjeljuju permisije (sve osim Administrator=1 i Super administrator=7)
$stmtUloge = $pdo->query("SELECT `korisnickeuloge_id`, `korisnickeuloge_naziv` FROM `korisnickeuloge` ORDER BY `korisnickeuloge_id` ASC");
$sveUloge = $stmtUloge->fetchAll(PDO::FETCH_ASSOC);
$PERMISIJE_ULOGE = array();
foreach ($sveUloge as $u) {
    $id = (int) $u['korisnickeuloge_id'];
    if (!in_array($id, $PERMISIJE_ULOGE_ISKLJUCENE, true)) {
        $PERMISIJE_ULOGE[$id] = $u['korisnickeuloge_naziv'];
    }
}
$ulogaIds = array_keys($PERMISIJE_ULOGE);

// Učitaj spremljene permisije za sve uloge kojima se dodjeljuju
$spremljene = array();
if (!empty($ulogaIds)) {
    try {
        $placeholders = implode(',', array_fill(0, count($ulogaIds), '?'));
        $stmt = $pdo->prepare("SELECT `permisije_uloga_id`, `permisije_sekcija`, `permisije_akcija` FROM `permisije` WHERE `permisije_uloga_id` IN ($placeholders)");
        $stmt->execute(array_values($ulogaIds));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $spremljene[$row['permisije_uloga_id']][$row['permisije_sekcija']][$row['permisije_akcija']] = true;
        }
    } catch (PDOException $e) {
        // Tablica možda još ne postoji (prije setup.php)
    }
}

// Spremanje permisija (POST)
$permisije_poruka = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_permisije'])) {
    try {
        $pdo->beginTransaction();
        if (!empty($ulogaIds)) {
            $placeholders = implode(',', array_fill(0, count($ulogaIds), '?'));
            $del = $pdo->prepare("DELETE FROM `permisije` WHERE `permisije_uloga_id` IN ($placeholders)");
            $del->execute(array_values($ulogaIds));
        }
        $ins = $pdo->prepare("INSERT INTO `permisije` (`permisije_uloga_id`, `permisije_sekcija`, `permisije_akcija`) VALUES (?, ?, ?)");
        $postPermisije = isset($_POST['permisije']) && is_array($_POST['permisije']) ? $_POST['permisije'] : array();
        foreach ($postPermisije as $ulogaId => $sekcije) {
            $ulogaId = (int) $ulogaId;
            if (!is_array($sekcije) || !isset($PERMISIJE_ULOGE[$ulogaId])) {
                continue;
            }
            foreach ($sekcije as $sekcija => $akcije) {
                if (!is_array($akcije)) {
                    continue;
                }
                $sekcija = preg_replace('/[^a-z0-9_]/', '', $sekcija);
                foreach ($akcije as $akcija => $v) {
                    if ($v === '' && $v !== '0') {
                        continue;
                    }
                    $akcija = preg_replace('/[^a-z0-9_]/', '', $akcija);
                    $ins->execute(array($ulogaId, $sekcija, $akcija));
                }
            }
        }
        $pdo->commit();
        $permisije_poruka = 'Permisije su sačuvane.';
        // Ponovo učitaj spremljene
        $spremljene = array();
        if (!empty($ulogaIds)) {
            $placeholders = implode(',', array_fill(0, count($ulogaIds), '?'));
            $stmt = $pdo->prepare("SELECT `permisije_uloga_id`, `permisije_sekcija`, `permisije_akcija` FROM `permisije` WHERE `permisije_uloga_id` IN ($placeholders)");
            $stmt->execute(array_values($ulogaIds));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $spremljene[$row['permisije_uloga_id']][$row['permisije_sekcija']][$row['permisije_akcija']] = true;
            }
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $permisije_poruka = 'Greška pri spremanju: ' . htmlspecialchars($e->getMessage());
    }
}
?>

<main id="main" class="main">

    <div class="pagetitle">
        <div class="d-flex justify-content-between mb-2">
            <h1>Podešavanja</h1>
        </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                <li class="breadcrumb-item active">Podešavanja</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Permisije</h5>
                        <p class="text-muted small">Za svaku vrstu korisnika (osim Administratora i Super administratora, kojima je uvijek sve dozvoljeno) odredite koje sekcije i akcije mogu koristiti. Za sada se ništa ne skriva u aplikaciji – samo se čuvaju postavke.</p>

                        <?php if ($permisije_poruka !== '') { ?>
                            <div class="alert <?php echo strpos($permisije_poruka, 'Greška') !== false ? 'alert-danger' : 'alert-success'; ?>"><?php echo htmlspecialchars($permisije_poruka); ?></div>
                        <?php } ?>

                        <form method="post" action="podesavanja.php">
                            <input type="hidden" name="save_permisije" value="1">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sekcija</th>
                                            <th>Akcija</th>
                                            <?php foreach ($PERMISIJE_ULOGE as $ulogaId => $ulogaNaziv) { ?>
                                                <th class="text-center"><?php echo htmlspecialchars($ulogaNaziv); ?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (empty($PERMISIJE_ULOGE)) {
                                            echo '<tr><td colspan="' . (count($PERMISIJE_ULOGE) + 2) . '" class="text-muted">Nema vrsta korisnika kojima se dodjeljuju permisije (osim Administratora i Super administratora).</td></tr>';
                                        } else {
                                            foreach ($PERMISIJE_SEKCIJE as $sekcija) {
                                                $sk = $sekcija['kljuc'];
                                                $first = true;
                                                foreach ($PERMISIJE_AKCIJE as $akcija) {
                                                    $ak = $akcija['kljuc'];
                                                    if ($ak === 'brisanje' && !empty($PERMISIJE_SEKCIJE_BEZ_BRISANJA) && in_array($sk, $PERMISIJE_SEKCIJE_BEZ_BRISANJA, true)) {
                                                        continue;
                                                    }
                                        ?>
                                        <tr>
                                            <td><?php echo $first ? htmlspecialchars($sekcija['naziv']) : ''; ?></td>
                                            <td><?php echo htmlspecialchars($akcija['naziv']); ?></td>
                                            <?php foreach ($PERMISIJE_ULOGE as $ulogaId => $ulogaNaziv) {
                                                $c = !empty($spremljene[$ulogaId][$sk][$ak]);
                                            ?>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permisije[<?php echo (int)$ulogaId; ?>][<?php echo htmlspecialchars($sk); ?>][<?php echo htmlspecialchars($ak); ?>]" value="1" <?php echo $c ? ' checked' : ''; ?> style="width:18px;height:18px;">
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                                    $first = false;
                                                }
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-dark" style="background-color:#00335e; border-color:#00335e; color:#fff;">Sačuvaj</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

<?php
include_once 'includes/footer.php';
