<?php
require_once('db_conexion.php');

try {
    $sql = "ALTER TABLE register ADD COLUMN role VARCHAR(50) NOT NULL DEFAULT 'usuario'";
    $cnnPDO->exec($sql);
    echo "Column 'role' added successfully.";
} catch (PDOException $e) {
    echo "Error adding column 'role': " . $e->getMessage();
}
?>
