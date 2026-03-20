<?php
// clear output buffer to prevent "headers already sent" errors
ob_start();
ob_clean();

//Setting CORS headers
$allowed_origin = "http://localhost:3000";
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true"); // Allow cookies to be sent
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit();
}

// Start session to manage user login state olny after handling CORS and preflight to avoid "headers already sent" issues
session_start();

require_once __DIR__ . '/../src/Config/Database.php';
use Config\Database;

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['mail']) && !empty($data['pass'])) {
    try {
        $db = new Database();
        $manager = $db->getManager();

        $filter = ['mail' => $data['mail']];
        $query = new \MongoDB\Driver\Query($filter);
        $cursor = $manager->executeQuery($db->getDbName() . '.utilisateurs', $query);
        $user = current($cursor->toArray());

        if ($user) {
            if (password_verify($data['pass'], $user->pass)) {
                
                // --- SAVE TO SESSION ---
                $_SESSION['user_id'] = (string)$user->_id;
                $_SESSION['nom'] = $user->nom;
                $_SESSION['prenom'] = $user->prenom;
                $_SESSION['mail'] = $user->mail;
                // -----------------------

                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Login successful."
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["status" => "error", "message" => "Mot de passe incorrect."]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Utilisateur non trouvé."]);
        }
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Tous les champs doivent être remplis."]);
}