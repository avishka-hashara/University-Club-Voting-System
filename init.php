<?php
// init.php - create PDO and common initialization
require_once __DIR__ . '/config.php';

// Set timezone (IMPORTANT: adjust if needed, matches your local time)
date_default_timezone_set('Asia/Colombo');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

require_once __DIR__ . '/helpers.php';
