<?php
// Start the session to store user data
session_start();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

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
        echo json_encode(["status" => "error", "message" => "Server error"]);
    }
}