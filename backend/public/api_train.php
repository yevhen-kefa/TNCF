<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../src/Config/Database.php';
use Config\Database;

try {
    $db = new Database();
    $manager = $db->getManager();
    

    $query = new \MongoDB\Driver\Query([]);
    $cursor = $manager->executeQuery($db->getDbName() . '.trains', $query);
    
    $trains = [];
    foreach ($cursor as $document) {
        $trains[] = $document;
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $trains
    ]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Помилка сервера: ' . $e->getMessage()
    ]);
}