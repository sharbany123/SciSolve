<?php
// C:\xampp\htdocs\scisolve\api\home_stats.php
header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';

// Users count
$users_count = 0;
if ($res = $mysqli->query("SELECT COUNT(*) AS total FROM users")) {
    $row = $res->fetch_assoc();
    $users_count = (int)$row['total'];
    $res->free();
}

// Challenges count
$challenges_count = 0;
if ($res = $mysqli->query("SELECT COUNT(*) AS total FROM challenges")) {
    $row = $res->fetch_assoc();
    $challenges_count = (int)$row['total'];
    $res->free();
}

// Running simulations count
$running_sims = 0;
if ($res = $mysqli->query("SELECT COUNT(*) AS total FROM simulations WHERE status='running'")) {
    $row = $res->fetch_assoc();
    $running_sims = (int)$row['total'];
    $res->free();
}

echo json_encode([
    "users" => $users_count,
    "challenges" => $challenges_count,
    "running_simulations" => $running_sims
]);
