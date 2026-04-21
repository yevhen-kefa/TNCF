<?php
// Clear output buffer to prevent "headers already sent" errors
ob_start();
ob_clean();
error_reporting(0);
ini_set('display_errors', 0);

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
    ob_end_flush();
    exit();
}

try {
    // Start session to get user ID if logged in
    session_start();

    // Include Database class
    require_once __DIR__ . '/../src/Config/Database.php';

    // Read data from REACT
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!$data) {
        ob_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit();
    }

    // Getting seat 
    $cls = $data['train']['cls'] ?? '2';
    $seatMode = $data['seatMode'] ?? 'random';
    $specificSeats = $data['specificSeats'] ?? [];

    if ($seatMode === 'specific' && !empty($specificSeats)) {
        // If user chose, we use it
        $assignedSeat = $specificSeats[0];
    } else {
        // If not, we search in random
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

    // Connect to Database
    $db = new \Config\Database();
    $manager = $db->getManager();

    // Prepare data for DB
    $id_uti = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $id_voyage = $data['train']['_id'] ?? null;

    // Convert string IDs to MongoDB ObjectIds if they are valid 24-char strings
    $user_id_obj = null;
    if ($id_uti && strlen($id_uti) === 24) {
        $user_id_obj = new \MongoDB\BSON\ObjectId($id_uti);
    }

    $voyage_id_obj = null;
    if ($id_voyage && strlen($id_voyage) === 24) {
        $voyage_id_obj = new \MongoDB\BSON\ObjectId($id_voyage);
    }

    $newTicket = [
        'id_voyage' => $voyage_id_obj ?? $id_voyage,
        'id_uti' => $user_id_obj ?? $id_uti, 
        'option' => $seatMode === 'specific' ? 'choisie' : 'aleatoire',
        'wagon' => (string) $assigned_seat['wagon'],
        'place' => (string) $assigned_seat['number'],
        'orderNumber' => $orderNumber, 
        'prix_paye' => $data['total'] ?? 0,
        'date_achat' => new \MongoDB\BSON\UTCDateTime()
    ];
    // Insert into DB using BulkWrite
    $bulk = new \MongoDB\Driver\BulkWrite;
    $bulk->insert($newTicket);
    
    $manager->executeBulkWrite($db->getDbName() . '.billet', $bulk);

    // Sent good request
    ob_clean();
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Booking successful',
        'orderNumber' => $orderNumber,
        'assignedSeat' => $assignedSeat 
    ]);
    exit();

} catch (\Throwable $e) {
    ob_clean();
    // Return 200 to prevent CORS issues on fatal errors, but send error status in JSON
    http_response_code(200);
    echo json_encode([
        'status' => 'error',
        'message' => 'Fatal Error: ' . $e->getMessage()
    ]);
    exit();
}