<?php
// api/challenge_join.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); exit; }

$challenge_id = intval($_POST['challenge_id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? 1;

if ($challenge_id <= 0) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'challenge_id required']); exit; }

// increment participants_count
$stmt = $mysqli->prepare("UPDATE challenges SET participants_count = participants_count + 1 WHERE challenge_id = ?");
$stmt->bind_param("i", $challenge_id);
if ($stmt->execute()) {
    // return new count
    $res = $mysqli->query("SELECT participants_count FROM challenges WHERE challenge_id = {$challenge_id}");
    $row = $res->fetch_assoc();
    echo json_encode(['success'=>true,'participants_count'=> (int)$row['participants_count']]);
} else {
    http_response_code(500); echo json_encode(['success'=>false,'error'=>$stmt->error]);
}
