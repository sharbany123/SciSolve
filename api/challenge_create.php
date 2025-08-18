<?php
// api/challenge_create.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';
session_start();

// check method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false,'error'=>'Method not allowed']);
    exit;
}

// simple auth: use session user_id if exists, otherwise fall back to 1 (change later)
$created_by = $_SESSION['user_id'] ?? 1;

// sanitize inputs
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$difficulty = trim($_POST['difficulty'] ?? 'Easy');
$deadline = trim($_POST['deadline'] ?? null);
$prize = trim($_POST['prize'] ?? '');

if (!$title) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Title required']);
    exit;
}

// handle file uploads (optional)
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
$resources = [];

if (!empty($_FILES) && isset($_FILES['files'])) {
    $files = $_FILES['files'];
    for ($i=0; $i<count($files['name']); $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        $name = time() . '-' . preg_replace('/[^a-zA-Z0-9\-_\.]/','_', $files['name'][$i]);
        $target = $uploadDir . $name;
        if (move_uploaded_file($files['tmp_name'][$i], $target)) {
            $resources[] = 'uploads/' . $name;
        }
    }
}

// insert into DB (prepared)
$stmt = $mysqli->prepare("INSERT INTO challenges (title, description, category, difficulty, deadline, prize, created_by, created_at) VALUES (?,?,?,?,?,?,?,NOW())");
$stmt->bind_param("ssssssi", $title, $description, $category, $difficulty, $deadline, $prize, $created_by);
$res = $stmt->execute();
if ($res) {
    $newId = $stmt->insert_id;
    // optionally save first resource as example (or create separate resources table)
    if (!empty($resources)) {
      // store first file path into image_url (if you use it)
      $img = $mysqli->real_escape_string($resources[0]);
      $mysqli->query("UPDATE challenges SET image_url='{$img}' WHERE challenge_id={$newId}");
    }
    echo json_encode(['success'=>true,'id'=>$newId]);
} else {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$stmt->error]);
}
