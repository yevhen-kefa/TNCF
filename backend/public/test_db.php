<?php
// Дозволяємо вивід помилок на екран для тестування
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../src/Config/Database.php';

try {
    // Пробуємо підключитися
    $db = new \Config\Database();
    $manager = $db->getManager();

    // Відправляємо тестову команду "ping" до бази
    $command = new \MongoDB\Driver\Command(['ping' => 1]);
    $cursor = $manager->executeCommand('admin', $command);
    $response = $cursor->toArray()[0];

    echo json_encode([
        "status" => "success",
        "message" => "Connexion à MongoDB réussie ! 🎉",
        "details" => $response
    ]);

} catch (\Exception $e) {
    // Якщо підключення не вдалося
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Erreur de connexion à MongoDB ❌",
        "erreur_detail" => $e->getMessage()
    ]);
}