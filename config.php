<?php
// config.php - single place for configuration (DB credentials, site constants)
session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'club_voting');
define('DB_USER', 'root');
define('DB_PASS', ''); // default XAMPP has no password; change as needed

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// University email domain enforcement (optional)
define('UNIVERSITY_EMAIL_DOMAIN', '@kdu.ac'); // set to '' to disable

// Site constants
define('SITE_NAME', 'Club Voting System');

// Ensure upload dir exists
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
