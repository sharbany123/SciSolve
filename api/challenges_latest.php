<?php
// challenges_latest.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$sql = "SELECT challenge_id, title, category, difficulty, description, image_url
        FROM challenges
        ORDER BY created_at DESC
        LIMIT 6";

$res = $mysqli->query($sql);

$list = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $list[] = $row;
    }
    $res->free();
}

echo json_encode($list);

