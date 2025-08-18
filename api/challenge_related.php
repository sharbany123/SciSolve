<?php
// api/challenge_related.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$cat = $mysqli->real_escape_string($_GET['category'] ?? '');
$id = intval($_GET['exclude'] ?? 0);

$sql = "SELECT challenge_id, title, prize FROM challenges WHERE category = '$cat' AND challenge_id != $id ORDER BY created_at DESC LIMIT 5";
$res = $mysqli->query($sql);
$out = [];
while ($r = $res->fetch_assoc()) $out[] = $r;
echo json_encode($out);
