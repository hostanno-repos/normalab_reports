<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <!-- <li class="nav-item">
            <a class="nav-link <?php //if ($file != "index.php") {
            //echo "collapsed";
            //} ?>" href="index.php">
                <i class="bi bi-grid"></i>
                <span>Početna</span>
            </a>
        </li>End Dashboard Nav -->

        <?php if (ima_permisiju('pregledklijenata', 'pregled')) { ?>
        <li class="nav-heading">Klijenti</li>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "pregledklijenata.php") {
                echo "collapsed";
            } ?>" href="pregledklijenata.php?page=1">
                <i class="bi bi-people-fill"></i>
                <span>Pregled klijenata</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('pregledmjerila', 'pregled') || ima_permisiju('opremazainspekciju', 'pregled')) { ?>
        <li class="nav-heading">Mjerila</li>
        <?php } ?>
        <?php if (ima_permisiju('pregledmjerila', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "pregledmjerila.php") {
                echo "collapsed";
            } ?>" href="pregledmjerila.php?page=1">
                <i class="bi bi-speedometer"></i>
                <span>Pregled mjerila</span>
            </a>
        </li>
        <?php } ?>
        <?php if (ima_permisiju('opremazainspekciju', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "opremazainspekciju.php") {
                echo "collapsed";
            } ?>" href="opremazainspekciju.php?page=1">
                <i class="bi bi-building-gear"></i>
                <span>Oprema za inspekciju</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('brojacirn', 'pregled') || ima_permisiju('pregledradnihnaloga', 'pregled')) { ?>
        <li class="nav-heading">Radni nalozi</li>
        <?php } ?>

        <?php if (ima_permisiju('brojacirn', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "brojacirn.php") {
                echo "collapsed";
            } ?>" href="brojacirn.php?page=1">
                <i class="bi bi-list-ol"></i>
                <span>Brojači radnih naloga</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('pregledradnihnaloga', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "pregledradnihnaloga.php") {
                echo "collapsed";
            } ?>" href="pregledradnihnaloga.php?page=1">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Pregled radnih naloga</span>
            </a>
        </li>
        <?php } ?>

        <!-- <li class="nav-heading">Mjerenja</li>

        <li class="nav-item">
            <a class="nav-link <?php //if ($file != "rezultatiMjerenja.php") {
            //echo "collapsed";
            //} ?>" href="rezultatiMjerenja.php">
                <i class="bi bi-rulers"></i>
                <span>Rezultati mjerenja</span>
            </a>
        </li> -->

        <?php if (ima_permisiju('tipoviizvjestaja', 'pregled') || ima_permisiju('pregledizvjestaja', 'pregled')) { ?>
        <li class="nav-heading">Izvještaji</li>
        <?php } ?>

        <?php if (ima_permisiju('tipoviizvjestaja', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "tipoviizvjestaja.php") {
                echo "collapsed";
            } ?>" href="tipoviizvjestaja.php?page=1">
                <i class="bi bi-list-stars"></i>
                <span>Tipovi izvještaja</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('pregledizvjestaja', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "pregledizvjestaja.php") {
                echo "collapsed";
            } ?>" href="pregledizvjestaja.php?page=1">
                <i class="bi bi-table"></i>
                <span>Pregled Izvještaja</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('kontrolori', 'pregled') || ima_permisiju('metodeinspekcije', 'pregled') || ima_permisiju('vrsteinspekcije', 'pregled') || ima_permisiju('vrsteuredjaja', 'pregled') || ima_permisiju('mjernevelicine', 'pregled') || ima_permisiju('referentnevrijednosti', 'pregled') || ima_permisiju('rjesenjazaovlascivanje', 'pregled')) { ?>
        <li class="nav-heading">Šifarnici</li>
        <?php } ?>

        <?php if (ima_permisiju('kontrolori', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "kontrolori.php") {
                echo "collapsed";
            } ?>" href="kontrolori.php?page=1">
                <i class="bi bi-binoculars"></i>
                <span>Kontrolori</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('metodeinspekcije', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "metodeinspekcije.php") {
                echo "collapsed";
            } ?>" href="metodeinspekcije.php?page=1">
                <i class="bi bi-boxes"></i>
                <span>Metode inspekcije</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('vrsteinspekcije', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "vrsteinspekcije.php") {
                echo "collapsed";
            } ?>" href="vrsteinspekcije.php?page=1">
                <i class="bi bi-boxes"></i>
                <span>Vrste inspekcije</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('vrsteuredjaja', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "vrsteuredjaja.php") {
                echo "collapsed";
            } ?>" href="vrsteuredjaja.php?page=1">
                <i class="bi bi-gpu-card"></i>
                <span>Vrste uređaja</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('mjernevelicine', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "mjernevelicine.php") {
                echo "collapsed";
            } ?>" href="mjernevelicine.php?page=1">
                <i class="bi bi-arrows-expand-vertical"></i>
                <span>Mjerne veličine</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('referentnevrijednosti', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "referentnevrijednosti.php") {
                echo "collapsed";
            } ?>" href="referentnevrijednosti.php?page=1">
                <i class="bi bi-sliders2-vertical"></i>
                <span>Referentne vrijednosti</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('rjesenjazaovlascivanje', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "rjesenjaovlascivanja.php") {
                echo "collapsed";
            } ?>" href="rjesenjaovlascivanja.php?page=1">
                <i class="bi bi-file-earmark-text"></i>
                <span>Rješenja o ovlašćivanju</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('nivoihijerarhije', 'pregled') || ima_permisiju('korisnickeuloge', 'pregled') || ima_permisiju('korisnici', 'pregled')) { ?>
        <li class="nav-heading">Korisnici</li>
        <?php } ?>

        <?php if (ima_permisiju('nivoihijerarhije', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "nivoihijerarhije.php") {
                echo "collapsed";
            } ?>" href="nivoihijerarhije.php?page=1">
                <i class="bi bi-water"></i>
                <span>Nivoi hijerarhije</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('korisnickeuloge', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "korisnickeuloge.php") {
                echo "collapsed";
            } ?>" href="korisnickeuloge.php?page=1">
                <i class="bi bi-person-fill-exclamation"></i>
                <span>Korisničke uloge</span>
            </a>
        </li>
        <?php } ?>

        <?php if (ima_permisiju('korisnici', 'pregled')) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "korisnici.php") {
                echo "collapsed";
            } ?>" href="korisnici.php?page=1">
                <i class="bi bi-person-fill-gear"></i>
                <span>Korisnici</span>
            </a>
        </li>
        <?php } ?>

        <?php if (in_array((int)$_SESSION['user-type'], [1, 7])) { ?>
        <li class="nav-heading">PODEŠAVANJA</li>
        <li class="nav-item">
            <a class="nav-link <?php if ($file != "podesavanja.php") {
                echo "collapsed";
            } ?>" href="podesavanja.php">
                <i class="bi bi-gear"></i>
                <span>Podešavanja</span>
            </a>
        </li>
        <?php } ?>

    </ul>

</aside>