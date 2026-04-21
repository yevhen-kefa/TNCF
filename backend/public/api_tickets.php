<?php
ob_start();
ob_clean();
error_reporting(0);
ini_set('display_errors', 0);

// Set CORS headers
$allowed_origin = "http://localhost:3000"; 
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
} else {
    header("Access-Control-Allow-Origin: *");
}
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit();
}

// check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

// ==========================================
// Fake data for testing - in a real application, you would fetch this from MongoDB
// ==========================================
$mockTickets = [
    [
        'id' => '69c28860b5c3d2c6f102d4e1',
        'depart' => 'Paris',
        'arriver' => 'Lyon',
        'date_depart' => '24/03/2026',
        'temps_arriver' => '1h 55min',
        'prix' => 40,
        'wagon' => '4',
        'place' => '12A',
        'train_num' => 'TGV 6601',
        'status' => 'upcoming' // Future ticket
    ],
    
];

// Send response in React
ob_clean();
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'tickets' => $mockTickets
]);
exit();