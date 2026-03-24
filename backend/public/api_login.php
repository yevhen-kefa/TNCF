<?php
// Блокуємо вивід HTML помилок, щоб не ламати JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

// Setting CORS headers
$allowed_origin = "http://localhost:3000";
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit();
}

try {
    session_start();

    // Перевіряємо, чи існує файл Database.php
    $db_path = __DIR__ . '/../src/Config/Database.php';
    if (!file_exists($db_path)) {
        throw new \Exception("Le fichier Database.php est introuvable.");
    }
    require_once $db_path;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['mail']) && !empty($data['pass'])) {
        
        $db = new \Config\Database();
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

                ob_clean();
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Login successful."
                ]);
                exit();
            } else {
                ob_clean();
                http_response_code(401);
                echo json_encode(["status" => "error", "message" => "Mot de passe incorrect."]);
                exit();
            }
        } else {
            ob_clean();
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Utilisateur non trouvé."]);
            exit();
        }
    } else {
        ob_clean();
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Tous les champs doivent être remplis."]);
        exit();
    }
    
} catch (\Throwable $e) {
    // ВАЖЛИВО: Ловимо АБСОЛЮТНО всі фатальні помилки PHP
    ob_clean();
    http_response_code(200); // Ставимо 200, щоб браузер не блокував CORS
    echo json_encode([
        "status" => "error", 
        "message" => "FATAL ERROR: " . $e->getMessage()
    ]);
    exit();
}