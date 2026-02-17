<?php
//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {

    if (!ima_permisiju('opremazainspekciju', 'dodavanje')) {
        header('Location: index.php');
        exit;
    }

    include_once ('includes/header.php');
    include_once ('includes/sidebar.php');

    ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1 class="mb-3">Dodaj opremu za inspekciju</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Početna</a></li>
                    <li class="breadcrumb-item"><a href="opremazainspekciju.php">Oprema za inspekciju</a></li>
                    <li class="breadcrumb-item active">Dodaj opremu za inspekciju</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <form class="col-lg-12 d-flex flex-wrap" action="<?php echo end($page_) ?>" method="post">
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="opremazainspekciju_naziv">Naziv:</label>
                        <input type="text" name="opremazainspekciju_naziv" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="opremazainspekciju_proizvodjac">Proizvođač:</label>
                        <input type="text" name="opremazainspekciju_proizvodjac" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="opremazainspekciju_tip">Tip:</label>
                        <input type="text" name="opremazainspekciju_tip" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <label for="opremazainspekciju_serijskibroj">Serijski broj:</label>
                        <input type="text" name="opremazainspekciju_serijskibroj" required>
                    </div>
                    <div class="col-lg-3 d-flex flex-column mb-2">
                        <!-- <label for="opremazainspekciju_opremauupotrebi">Oprema u upotrebi:</label> -->
                        <input type="text" name="opremazainspekciju_opremauupotrebi" value="1" hidden>
                    </div>
                    <div class="col-lg-12 d-flex flex-column mt-3">
                        <button name="submit_opremazainspekciju" class="btn btn-primary" type="submit"
                            style="width:150px">Sačuvaj</button>
                    </div>
                </form>
            </div>
        </section>

    </main>

    <style>
        .btn.btn-primary {
            background-color: #00335e;
        }
    </style>

    <!-- <script>

    $(document).ready(function () {
        $(".selectElement_").change(function () {
            var selectValue = $(this).val();
            $(this).prev().val(selectValue);
        });
    });

</script> -->

    <?php

} else {
    header('Location: index.php');
}

//INCLUDES
include_once ('includes/footer.php');

?>