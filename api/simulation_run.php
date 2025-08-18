<?php
// api/simulation_run.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$challenge_id = intval($data['challengeId'] ?? 0);
$type = $mysqli->real_escape_string($data['type'] ?? 'projectile');
$params = json_encode($data['parameters'] ?? []);

// simple fake sim: generate a few points (you can replace with real logic)
$points = [];
for ($t=0;$t<=5;$t+=0.5) {
    $points[] = ['time'=>$t, 'x'=>round(rand(0,100)/10,2), 'y'=>round(rand(0,50)/10,2)];
}

// save run to DB (optional: create simulations table earlier; for now just return result)
$result = ['distance'=>round(rand(50,500),2), 'height'=>round(rand(5,200),2), 'time'=>round(rand(1,10),2), 'dataPoints'=>$points];

echo json_encode(['success'=>true,'results'=>$result]);
