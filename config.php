<?php
// C:\xampp\htdocs\scisolve\config.php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'scisolve';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP ডিফল্টে ফাঁকা থাকে

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_error) {
    http_response_code(500);
    die('DB connect error: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
