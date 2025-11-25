<?php
require_once('db_conexion.php');

$users = [
    [
        'name' => 'Admin User',
        'secondname' => 'Admin',
        'email' => 'admin@example.com',
        'pssword' => password_hash('Admin@123', PASSWORD_DEFAULT),
        'role' => 'administrador'
    ],
    [
        'name' => 'Editor User',
        'secondname' => 'Editor',
        'email' => 'editor@example.com',
        'pssword' => password_hash('Editor@123', PASSWORD_DEFAULT),
        'role' => 'editor'
    ],
    [
        'name' => 'Regular User',
        'secondname' => 'User',
        'email' => 'user@example.com',
        'pssword' => password_hash('User@123', PASSWORD_DEFAULT),
        'role' => 'usuario'
    ]
];

foreach ($users as $user) {
    $stmt = $cnnPDO->prepare("INSERT INTO register (name, secondname, email, pssword, role) VALUES (:name, :secondname, :email, :pssword, :role)");
    $stmt->bindParam(':name', $user['name']);
    $stmt->bindParam(':secondname', $user['secondname']);
    $stmt->bindParam(':email', $user['email']);
    $stmt->bindParam(':pssword', $user['pssword']);
    $stmt->bindParam(':role', $user['role']);
    $stmt->execute();
}

echo "Sample users created successfully.";
?>
