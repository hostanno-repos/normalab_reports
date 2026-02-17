<?php
//INCLUDES
include_once ('includes/head.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] != '') {
    //INCLUDES
    include_once ('admin.php');
} else {
    //INCLUDES
    include_once ('login.php');
}

//INCLUDES
include_once ('includes/footer.php');

?>