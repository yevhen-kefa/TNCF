<?php
ob_start();
ob_clean();

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

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Non connected"]);
    ob_end_flush();
    exit();
}

$user_id = $_SESSION['user_id'];

$tickets = [
    [
        "id_billet" => "demo1",
        "depart" => "Paris",
        "arriver" => "Lyon",
        "date_depart" => "08/03/2026",
        "temps_arriver" => "5h6min",
        "prix" => 89,
        "wagon" => "4",
        "place" => "12A",
        "status" => "upcoming"
    ],
    [
        "id_billet" => "demo2",
        "depart" => "Bordeaux",
        "arriver" => "Paris",
        "date_depart" => "28/02/2026",
        "temps_arriver" => "2h04min",
        "prix" => 45,
        "wagon" => "2",
        "place" => "08C",
        "status" => "used"
    ]
];

http_response_code(200);
echo json_encode([
    "status" => "success",
    "tickets" => $tickets
]);

ob_end_flush();
exit();
?>