<?php
ob_start();
ob_clean();
error_reporting(0);
ini_set('display_errors', 0);

$allowed_origin = "http://localhost:3000"; 
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
}
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type"); 

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); exit();
}

try {
    require_once __DIR__ . '/../src/Config/Database.php';

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    
    $token = $data['token'] ?? '';
    $newPassword = $data['password'] ?? '';

    if (empty($token) || empty($newPassword)) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
        exit();
    }

    $db = new \Config\Database();
    $manager = $db->getManager();

    //Search for user by token and check if it's still valid
    $now = new \MongoDB\BSON\UTCDateTime();
    $query = new \MongoDB\Driver\Query([
        'reset_token' => $token,
        'reset_expires' => ['$gt' => $now]
    ]);
    $cursor = $manager->executeQuery($db->getDbName() . '.utilisateur', $query);
    $user = current($cursor->toArray());

    if ($user) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $bulk = new \MongoDB\Driver\BulkWrite;
        // Update password and remove reset token/expiry
        $bulk->update(
            ['_id' => $user->_id],
            [
                '$set' => ['mdp' => $hashedPassword],
                '$unset' => ['reset_token' => '', 'reset_expires' => '']
            ]
        );
        $manager->executeBulkWrite($db->getDbName() . '.utilisateur', $bulk);

        ob_clean();
        echo json_encode(['status' => 'success', 'message' => 'Mot de passe mis à jour']);
    } else {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Lien invalide ou expiré']);
    }
} catch (\Throwable $e) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}