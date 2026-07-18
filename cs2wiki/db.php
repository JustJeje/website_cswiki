<?php
// ==========================================
// db.php — Database Connection
// CS2 Knife Wiki
// ==========================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ganti sesuai hosting
define('DB_PASS', '');           // ganti sesuai hosting
define('DB_NAME', 'cs2wiki');

function getDB(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("<div style='background:#1a0000;color:#ff4444;padding:20px;font-family:monospace;'>
            ❌ Database connection failed: " . $conn->connect_error . "
            <br><small>Check DB_HOST, DB_USER, DB_PASS, DB_NAME in db.php</small>
        </div>");
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
