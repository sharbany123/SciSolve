<?php
// api/challenge_submit.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'error'=>'Method not allowed']); exit;
}

$challenge_id = intval($_POST['challenge_id'] ?? 0);
$notes = trim($_POST['notes'] ?? '');
$user_id = $_SESSION['user_id'] ?? 1; // change to session user in production

if ($challenge_id <= 0) {
    http_response_code(400); echo json_encode(['success'=>false,'error'=>'challenge_id required']); exit;
}

// handle file (single or multiple: we support single input named "file" or array "files[]")
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) @mkdir($uploadDir,0755,true);

$file_path = null;
if (!empty($_FILES['file']['name'])) {
    $fname = time().'-'.preg_replace('/[^a-zA-Z0-9\-_\.]/','_', $_FILES['file']['name']);
    $target = $uploadDir . $fname;
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        $file_path = 'uploads/' . $fname;
    }
}

// insert
$stmt = $mysqli->prepare("INSERT INTO challenge_submissions (challenge_id, user_id, file_path, notes) VALUES (?,?,?,?)");
$stmt->bind_param("iiss", $challenge_id, $user_id, $file_path, $notes);
if ($stmt->execute()) {
    echo json_encode(['success'=>true,'submission_id'=>$stmt->insert_id]);
} else {
    http_response_code(500); echo json_encode(['success'=>false,'error'=>$stmt->error]);
}

