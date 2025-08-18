<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$where = [];
$params = [];
$types = '';

// Filter by category
if (!empty($_GET['category']) && $_GET['category'] !== 'all') {
    $where[] = "LOWER(category) = ?";
    $params[] = strtolower($_GET['category']);
    $types .= 's';
}

// Filter by difficulty
if (!empty($_GET['difficulty']) && $_GET['difficulty'] !== 'all') {
    $where[] = "LOWER(difficulty) = ?";
    $params[] = strtolower($_GET['difficulty']);
    $types .= 's';
}

// Search term
if (!empty($_GET['search'])) {
    $where[] = "title LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
    $types .= 's';
}

// Base query
$sql = "SELECT challenge_id, title, category, difficulty, description, image_url, prize, deadline, created_at 
        FROM challenges";

// Apply filters
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// Sorting
$sort = $_GET['sort'] ?? 'newest';
switch ($sort) {
    case 'deadline':
        $sql .= " ORDER BY deadline ASC";
        break;
    case 'prize':
        $sql .= " ORDER BY prize DESC";
        break;
    case 'popular':
        // Placeholder: later join submissions count
        $sql .= " ORDER BY created_at DESC";
        break;
    default:
        $sql .= " ORDER BY created_at DESC";
        break;
}

$stmt = $mysqli->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

$list = [];
while ($row = $res->fetch_assoc()) {
    $list[] = $row;
}

echo json_encode($list);
