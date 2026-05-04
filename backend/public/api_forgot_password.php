<?php
ob_start();
ob_clean();
error_reporting(0);
ini_set('display_errors', 0);

$allowed_origin = "http://localhost:3000"; 
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit();
}

try {
    require_once __DIR__ . '/../src/Config/Database.php';

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $email = $data['email'] ?? '';

    if (empty($email)) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Email requis']);
        exit();
    }

    $db = new \Config\Database();
    $manager = $db->getManager();

    // Search for user by email
    $query = new \MongoDB\Driver\Query(['mail' => $email]);
    $cursor = $manager->executeQuery($db->getDbName() . '.utilisateur', $query);
    $user = current($cursor->toArray());

    if ($user) {
        // Making a secure token for password reset
        $token = bin2hex(random_bytes(32));
        // Token expires in 1 hour
        $expiry = new \MongoDB\BSON\UTCDateTime((time() + 3600) * 1000); 

        // Saving the token to the database
        $bulk = new \MongoDB\Driver\BulkWrite;
        $bulk->update(
            ['_id' => $user->_id],
            ['$set' => ['reset_token' => $token, 'reset_expires' => $expiry]]
        );
        $manager->executeBulkWrite($db->getDbName() . '.utilisateur', $bulk);

        // Forming the reset link
        $resetLink = "http://localhost:3000/reset-password?token=" . $token;

        // Attempt to send the actual email (may not work on localhost)
        $subject = "Réinitialisation de votre mot de passe TNCF";
        $message = "Bonjour,\n\nPour réinitialiser votre mot de passe, veuillez cliquer sur ce lien : \n" . $resetLink . "\n\nCe lien est valide pendant 1 heure.";
        $headers = "From: noreply@tncf.fr\r\n";
        @mail($email, $subject, $message, $headers); 

        ob_clean();
        // Returning resetLink for local testing!
        echo json_encode([
            'status' => 'success', 
            'message' => 'Lien de réinitialisation généré (voir console).', 
            'dev_link' => $resetLink
        ]);
    } else {
        ob_clean();
        echo json_encode([
            'status' => 'error', 
            'message' => "Cette adresse e-mail n'existe pas."
        ]);
    }
} catch (\Throwable $e) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}