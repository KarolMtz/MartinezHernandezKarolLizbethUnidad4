<?php
// DATOS DE TU INFINITYFREE
$db_host = 'sql112.infinityfree.com';           // Tu Host Name
$db_name = 'if0_40502487_1004cakeboutique';     // Tu DB Name  
$db_user = 'if0_40502487';                      // Tu User Name
$db_password = 'xoxoLOVE94';                    // Tu Password

$utf8 = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

try {
    $cnnPDO = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password, $utf8);
    $cnnPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>