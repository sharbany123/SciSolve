<?php
// C:\xampp\htdocs\scisolve\config.php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'scisolve';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP ডিফল্টে ফাঁকা থাকে

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['ok'=>false, 'error'=>'DB connect error: ' . $mysqli->connect_error]);
    exit;
}
$mysqli->set_charset('utf8mb4');

// Secure session
ini_set('session.cookie_httponly', 1);
session_start();

// Default JSON header
header('Content-Type: application/json; charset=utf-8');
