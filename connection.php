<?php

//AVOID DIRECT ACCESS TO FILE
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    die(header('location: 404.php'));
}

$database = "normalab_reports";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=' . $database,
        $username,
        $password,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
    );
} catch (PDOException $e) {
    exit('Database error');
}


?>