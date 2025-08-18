<?php
// leaderboard_top.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$sql = "SELECT user_id, name, score
        FROM users
        ORDER BY score DESC
        LIMIT 3";
$res = $mysqli->query($sql);

$list = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $list[] = $row;
    }
    $res->free();
}

echo json_encode($list);
