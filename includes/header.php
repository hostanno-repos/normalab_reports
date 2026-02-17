<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center">
            <img src="./images/main-logo.svg" alt="" width="250">
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

            <li class="nav-item dropdown pe-3"><?php echo $_SESSION['user'] ?></li>

            <li class="nav-item dropdown pe-3">

                <a class="dropdown-item d-flex align-items-center" href="logout.php">
                    <span>Odjava </span><i class="fas fa-sign-out ml-2"></i>
                </a>
            </li>
        </ul>
    </nav>

</header>