<?php
/**
 * Authentication Helper Functions.
 */
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getRole() {
    return $_SESSION['user_role'] ?? null;
}

function isAgency() {
    return getRole() === 'agency';
}

function isCustomer() {
    return getRole() === 'customer';
}

function loginUser($user_id, $role, $name) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_role'] = $role;
    $_SESSION['user_name'] = $name;
}

function logoutUser() {
    session_destroy();
    header("Location: login.php");
    exit();
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        if (isAgency()) header("Location: agency/dashboard.php");
        else header("Location: index.php");
        exit();
    }
}
?>
