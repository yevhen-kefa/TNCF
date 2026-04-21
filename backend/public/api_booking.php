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
    session_start();
    require_once __DIR__ . '/../src/Config/Database.php';

    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!$data) {
        ob_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit();
    }

    $cartItems = $data['cartItems'] ?? [];
    if (empty($cartItems)) {
        ob_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
        exit();
    }

    $orderNumber = 'TNCF-' . strtoupper(substr(md5(uniqid()), 0, 6));
    
    $db = new \Config\Database();
    $manager = $db->getManager();
    $id_uti = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Convert user string ID to ObjectId
    $user_id_obj = null;
    if ($id_uti && strlen($id_uti) === 24) {
        $user_id_obj = new \MongoDB\BSON\ObjectId($id_uti);
    }

    $bulk = new \MongoDB\Driver\BulkWrite;
    $assignedSeats = [];

    // Loop through ALL items in the cart
    foreach ($cartItems as $item) {
        $train = $item['train'] ?? [];
        $cls = $train['cls'] ?? '2';
        $seatMode = $item['seatMode'] ?? 'random';
        $specificSeats = $item['specificSeats'] ?? [];

        // Seat assignment logic
        if ($seatMode === 'specific' && !empty($specificSeats)) {
            $assignedSeat = $specificSeats[0];
        } else {
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

        $assignedSeats[] = $assignedSeat;

        // Convert voyage string ID to ObjectId
        $id_voyage = $train['_id'] ?? null;
        $voyage_id_obj = null;
        if ($id_voyage && strlen($id_voyage) === 24) {
            $voyage_id_obj = new \MongoDB\BSON\ObjectId($id_voyage);
        }

        // Prepare ticket document
        $newTicket = [
            'id_voyage' => $voyage_id_obj ?? $id_voyage,
            'id_uti' => $user_id_obj ?? $id_uti,
            'option' => $seatMode === 'specific' ? 'choisie' : 'aleatoire',
            'wagon' => (string) $assignedSeat['wagon'],
            'place' => (string) $assignedSeat['number'],
            'orderNumber' => $orderNumber, 
            'prix_paye' => $item['total'] ?? 0,
            'date_achat' => new \MongoDB\BSON\UTCDateTime()
        ];

        // Add to bulk operation
        $bulk->insert($newTicket);
    }

    // Execute all inserts at once
    $manager->executeBulkWrite($db->getDbName() . '.billet', $bulk);

    ob_clean();
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Booking successful',
        'orderNumber' => $orderNumber,
        'assignedSeats' => $assignedSeats 
    ]);
    exit();

} catch (\Throwable $e) {
    ob_clean();
    http_response_code(200);
    echo json_encode([
        'status' => 'error',
        'message' => 'Fatal Error: ' . $e->getMessage()
    ]);
    exit();
}