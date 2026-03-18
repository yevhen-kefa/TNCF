<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../src/Config/Database.php';
use Config\Database;

$data = json_decode(file_get_contents("php://input"), true);

if (
    !empty($data['nom']) &&
    !empty($data['prenom']) &&
    !empty($data['mail']) &&
    !empty($data['telephone']) &&
    !empty($data['pass'])
) {
    try {
        $db = new Database();
        $manager = $db->getManager();

        $hashed_password = password_hash($data['pass'], PASSWORD_BCRYPT);

        $newUser = [
            'civilite' => $data['civilite'] ?? 'M',
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'mail' => $data['mail'],
            'telephone' => $data['telephone'],
            'pass' => $hashed_password,
            
            'abonnement' => [
                'is_actif' => false,
                'date_expiration' => null,
                'num_carte' => null,
                'code_promotion' => null
            ],
            
            'security' => [
                'role' => 'user',
                'verified' => false,
                'totp_enabled' => false,
                'totp_secret' => null,
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
                'reset_token' => null,
                'reset_expires_at' => null
            ],
            
            'created_at' => new \MongoDB\BSON\UTCDateTime()
        ];

        $bulk = new \MongoDB\Driver\BulkWrite;
        $bulk->insert($newUser);
        
        $result = $manager->executeBulkWrite($db->getDbName() . '.utilisateurs', $bulk);

        http_response_code(201); // 201 Created
        echo json_encode([
            "status" => "success",
            "message" => "L'utilisateur a été enregistré avec succès."
        ]);
        
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error server: " . $e->getMessage()
        ]);
    }
} else {
    http_response_code(400); // 400 Bad Request
    echo json_encode([
        "status" => "error",
        "message" => "Tous les champs doivent être remplis."
    ]);
}