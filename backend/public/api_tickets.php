<?php
ob_start();
ob_clean();
error_reporting(0);
ini_set('display_errors', 0);

// Налаштування CORS
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

// Перевірка, чи користувач увійшов в систему (для реалістичності на презентації)
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

// ==========================================
// ФЕЙКОВІ ДАНІ ДЛЯ ПРЕЗЕНТАЦІЇ
// Замість звернення до MongoDB, ми просто віддаємо цей ідеальний масив
// ==========================================
$mockTickets = [
    [
        'id' => '69c28860b5c3d2c6f102d4e1',
        'depart' => 'Paris',
        'arriver' => 'Lyon',
        'date_depart' => '28/03/2026',
        'temps_arriver' => '1h 55min',
        'prix' => 89,
        'wagon' => '4',
        'place' => '12A',
        'train_num' => 'TGV 6601',
        'status' => 'upcoming' // Майбутня поїздка
    ],
    [
        'id' => '69c28860b5c3d2c6f102d4e2',
        'depart' => 'Bordeaux',
        'arriver' => 'Paris',
        'date_depart' => '15/02/2026',
        'temps_arriver' => '2h 04min',
        'prix' => 45,
        'wagon' => '2',
        'place' => '08C',
        'train_num' => 'TGV 7214',
        'status' => 'used' // Вже використаний квиток
    ],
    [
        'id' => '69c28860b5c3d2c6f102d4e1',
        'depart' => 'Paris',
        'arriver' => 'Lyon',
        'date_depart' => '28/03/2026',
        'temps_arriver' => '1h 55min',
        'prix' => 89,
        'wagon' => '4',
        'place' => '12A',
        'train_num' => 'TGV 6601',
        'status' => 'upcoming' // Майбутня поїздка
    ],
    [
        'id' => '69c28860b5c3d2c6f102d4e2',
        'depart' => 'Bordeaux',
        'arriver' => 'Paris',
        'date_depart' => '15/02/2026',
        'temps_arriver' => '2h 04min',
        'prix' => 45,
        'wagon' => '2',
        'place' => '08C',
        'train_num' => 'TGV 7214',
        'status' => 'used' // Вже використаний квиток
    ],
    [
        'id' => '69c28860b5c3d2c6f102d4e1',
        'depart' => 'Paris',
        'arriver' => 'Lyon',
        'date_depart' => '28/03/2026',
        'temps_arriver' => '1h 55min',
        'prix' => 89,
        'wagon' => '4',
        'place' => '12A',
        'train_num' => 'TGV 6601',
        'status' => 'upcoming' // Майбутня поїздка
    ],
    [
        'id' => '69c28860b5c3d2c6f102d4e2',
        'depart' => 'Bordeaux',
        'arriver' => 'Paris',
        'date_depart' => '15/02/2026',
        'temps_arriver' => '2h 04min',
        'prix' => 45,
        'wagon' => '2',
        'place' => '08C',
        'train_num' => 'TGV 7214',
        'status' => 'used' // Вже використаний квиток
    ],
    [
        'id' => '69c28860b5c3d2c6f102d4e1',
        'depart' => 'Paris',
        'arriver' => 'Lyon',
        'date_depart' => '28/03/2026',
        'temps_arriver' => '1h 55min',
        'prix' => 89,
        'wagon' => '4',
        'place' => '12A',
        'train_num' => 'TGV 6601',
        'status' => 'upcoming' // Майбутня поїздка
    ],
    [
        'id' => '69c28860b5c3d2c6f102d4e2',
        'depart' => 'Bordeaux',
        'arriver' => 'Paris',
        'date_depart' => '15/02/2026',
        'temps_arriver' => '2h 04min',
        'prix' => 45,
        'wagon' => '2',
        'place' => '08C',
        'train_num' => 'TGV 7214',
        'status' => 'used' // Вже використаний квиток
    ],
    [
        'id' => '69c28860b5c3d2c6f102d4e1',
        'depart' => 'Paris',
        'arriver' => 'Lyon',
        'date_depart' => '28/03/2026',
        'temps_arriver' => '1h 55min',
        'prix' => 89,
        'wagon' => '4',
        'place' => '12A',
        'train_num' => 'TGV 6601',
        'status' => 'upcoming' // Майбутня поїздка
    ],
    [
        'id' => '69c28860b5c3d2c6f102d4e2',
        'depart' => 'Bordeaux',
        'arriver' => 'Paris',
        'date_depart' => '15/02/2026',
        'temps_arriver' => '2h 04min',
        'prix' => 45,
        'wagon' => '2',
        'place' => '08C',
        'train_num' => 'TGV 7214',
        'status' => 'used' // Вже використаний квиток
    ],
    [
        'id' => '69c28860b5c3d2c6f102d4e1',
        'depart' => 'Paris',
        'arriver' => 'Lyon',
        'date_depart' => '28/03/2026',
        'temps_arriver' => '1h 55min',
        'prix' => 89,
        'wagon' => '4',
        'place' => '12A',
        'train_num' => 'TGV 6601',
        'status' => 'upcoming' // Майбутня поїздка
    ],
    [
        'id' => '69c28860b5c3d2c6f102d4e3',
        'depart' => 'Lyon',
        'arriver' => 'Nice',
        'date_depart' => '05/01/2026',
        'temps_arriver' => '1h 45min',
        'prix' => 38,
        'wagon' => '5',
        'place' => '17D',
        'train_num' => 'TGV 6340',
        'status' => 'cancelled' // Скасований квиток
    ]
];

// Віддаємо дані в React
ob_clean();
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'tickets' => $mockTickets
]);
exit();