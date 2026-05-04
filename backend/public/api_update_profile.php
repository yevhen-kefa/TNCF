<?php
// CORS — самий перший рядок, нічого до цього
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_reporting(0);
ini_set('display_errors', 0);
ob_start();

try {
    session_start();
    require_once __DIR__ . '/../src/Config/Database.php';

    if (!isset($_SESSION['user_id'])) {
        ob_end_clean();
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Non autorisé. Veuillez vous connecter.']);
        exit();
    }

    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);

    $prenom          = trim($data['prenom']          ?? '');
    $nom             = trim($data['nom']             ?? '');
    $email           = trim($data['email']           ?? '');
    $currentPassword = $data['currentPassword']      ?? '';
    $newPassword     = $data['newPassword']          ?? '';

    if (empty($prenom) || empty($nom) || empty($email)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Les champs Prénom, Nom et Email sont obligatoires.']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Adresse e-mail invalide.']);
        exit();
    }

    $db         = new \Config\Database();
    $manager    = $db->getManager();
    $collection = $db->getDbName() . '.utilisateurs';
    $userId     = new \MongoDB\BSON\ObjectId($_SESSION['user_id']);

    $query  = new \MongoDB\Driver\Query(['_id' => $userId]);
    $cursor = $manager->executeQuery($collection, $query);
    $users  = $cursor->toArray();

    if (count($users) === 0) {
        ob_end_clean();
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Utilisateur introuvable.']);
        exit();
    }

    $user = $users[0];

    $updateData = [
        'prenom' => $prenom,
        'nom'    => $nom,
        'mail'   => $email,
    ];

    if (!empty($newPassword)) {
        if (empty($currentPassword)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Veuillez entrer votre mot de passe actuel.']);
            exit();
        }
        if (strlen($newPassword) < 6) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Le nouveau mot de passe doit contenir au moins 6 caractères.']);
            exit();
        }
        if (!password_verify($currentPassword, $user->pass)) {
            ob_end_clean();
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Le mot de passe actuel est incorrect.']);
            exit();
        }
        $updateData['pass'] = password_hash($newPassword, PASSWORD_BCRYPT);
    }

    $bulk = new \MongoDB\Driver\BulkWrite();
    $bulk->update(
        ['_id' => $userId],
        ['$set' => $updateData]
    );
    $result = $manager->executeBulkWrite($collection, $bulk);

    $_SESSION['prenom'] = $prenom;
    $_SESSION['nom']    = $nom;
    $_SESSION['mail']   = $email;

    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'status'         => 'success',
        'message'        => 'Profil mis à jour avec succès.',
        'modified_count' => $result->getModifiedCount(),
    ]);

} catch (\Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Erreur serveur: ' . $e->getMessage(),
    ]);
}