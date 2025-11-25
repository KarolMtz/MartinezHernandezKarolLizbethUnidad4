<?php
session_start();
require_once('db_conexion.php');

// Define role constants
define('ROLE_ADMIN', 'administrador');
define('ROLE_EDITOR', 'editor');
define('ROLE_USER', 'usuario');

/**
 * Check if user is logged in and session token is valid
 * @return bool
 */
function isLoggedIn() {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        return false;
    }
    if (!isset($_SESSION['user_id'], $_SESSION['session_token'])) {
        return false;
    }

    global $cnnPDO;
    $query = $cnnPDO->prepare('SELECT session_token FROM register WHERE id = :userId');
    $query->bindParam(':userId', $_SESSION['user_id']);
    $query->execute();
    $result = $query->fetch();

    if (!$result) {
        return false;
    }

    return hash_equals($result['session_token'], $_SESSION['session_token']);
}

/**
 * Check if user has one of the required roles
 * @param array|string $roles
 * @return bool
 */
function hasRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }
    $userRole = $_SESSION['user_role'] ?? 'usuario';
    if (is_array($roles)) {
        return in_array($userRole, $roles);
    }
    return $userRole === $roles;
}

/**
 * Require login and role(s) to access page
 * Redirects to login.php if not logged in
 * Shows 403 error if role not authorized
 * @param array|string $roles
 */
function requireAuth($roles = []) {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
    if (!empty($roles) && !hasRole($roles)) {
        http_response_code(403);
        echo "<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>";
        exit();
    }
}
?>
