<?php

//AVOID DIRECT ACCESS TO FILE
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    die(header('location: 404.php'));
}


//INCLUDES
include_once ('includes/head.php');
include_once ('includes/header.php');
include_once ('includes/sidebar.php');

?>



<?php
//INCLUDES
include_once ('includes/footer.php');

?>