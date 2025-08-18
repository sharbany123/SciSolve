<?php
// api/challenge_detail.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Challenge ID required']);
    exit;
}

$challenge_id = (int) $_GET['id'];

$sql = "SELECT c.challenge_id, c.title, c.category, c.difficulty, 
               c.description, c.prize, c.deadline, c.created_at,
               u.name AS creator_name
        FROM challenges c
        LEFT JOIN users u ON c.created_by = u.user_id
        WHERE c.challenge_id = ? LIMIT 1";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $challenge_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    echo json_encode($row);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Challenge not found']);
}
