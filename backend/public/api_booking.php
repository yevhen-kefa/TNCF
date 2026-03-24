<?php
// Settings for CORS
$allowed_origin = "http://localhost:3000"; 
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 

// PREFLIGHT (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Read data from REACT
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit();
}

// Getting seat 
$cls = $data['train']['cls'] ?? '2';
$seatMode = $data['seatMode'] ?? 'random';
$specificSeats = $data['specificSeats'] ?? [];

if ($seatMode === 'specific' && !empty($specificSeats)) {
    // If user chosed, we use it
    $assignedSeat = $specificSeats[0];
} else {
    // if not, we search in random
    $wagon = ($cls === '1') ? rand(1, 2) : rand(3, 7);
    $row = rand(1, 14);
    $letters = ($cls === '1') ? ['A', 'C', 'D'] : ['A', 'B', 'C', 'D'];
    $letter = $letters[array_rand($letters)];
    
    $assignedSeat = [
        'wagon' => $wagon,
        'number' => $row . $letter,
        'type' => (rand(0, 1) ? 'standard' : 'table')
    ];
}

// Generation number of order
$orderNumber = 'TNCF-' . strtoupper(substr(md5(uniqid()), 0, 6));

// Sent good request
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Réservation réussie',
    'orderNumber' => $orderNumber,
    'assignedSeat' => $assignedSeat 
]);
exit();