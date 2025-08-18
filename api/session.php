<?php
// session.php
header('Content-Type: application/json; charset=utf-8');
session_start();

$response = [
    'logged_in' => false,
    'user_id' => null,
    'name' => null
];

if (!empty($_SESSION['user_id'])) {
    $response['logged_in'] = true;
    $response['user_id'] = $_SESSION['user_id'];
    $response['name'] = $_SESSION['name'] ?? null;
}

echo json_encode($response);
