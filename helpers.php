<?php
// helpers.php - utility functions used across pages


// CSRF helpers
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_field() {
    $t = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($t) . '">';
}
function check_csrf() {
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
}

// Escape output to prevent XSS
function e($s) {
    return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Simple auth helpers
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}
function current_user() {
    return $_SESSION['user'] ?? null;
}
function require_login() {
    if (!is_logged_in()) {
        header('Location: /club_voting/login.php');
        exit;
    }
}
function require_role($role) {
    require_login();
    $user = current_user();
    if (!$user || $user['role'] !== $role) {
        http_response_code(403);
        echo "Access denied.";
        exit;
    }
}

// Simple flash messages
function flash_set($k, $v) {
    $_SESSION['flash'][$k] = $v;
}
function flash_get($k) {
    $v = $_SESSION['flash'][$k] ?? null;
    if (isset($_SESSION['flash'][$k])) unset($_SESSION['flash'][$k]);
    return $v;
}

// time helper - returns current server datetime
function now_sql() {
    return date('Y-m-d H:i:s');
}


