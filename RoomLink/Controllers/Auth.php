<?php
// auth.php - Authentication middleware with helper functions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../models/db.php';
require_once '../models/UserFactory.php';
require_once 'AccessControl.php';

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: signin.php");
        exit();
    }
}

function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $db = Database::getInstance()->getConnection();
    $stmt = mysqli_prepare($db, "SELECT * FROM users WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $userData = mysqli_fetch_assoc($result);
    
    if ($userData) {
        return UserFactory::createUser($userData);
    }
    return null;
}

function checkPageAccess($currentPage) {
    requireLogin();
    
    $userType = $_SESSION['user_type'];
    $strategy = AccessStrategyFactory::getStrategy($userType);
    $controller = new AccessController($strategy);
    $controller->validateAccess($currentPage);
}
?>