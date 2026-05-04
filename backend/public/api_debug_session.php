<?php
ob_start(); ob_clean();
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

session_start();
require_once __DIR__ . '/../src/Config/Database.php';

$debug = [];

// 1. Що є в сесії
$debug['session_user_id']  = $_SESSION['user_id']  ?? 'MISSING';
$debug['session_prenom']   = $_SESSION['prenom']   ?? 'MISSING';
$debug['session_nom']      = $_SESSION['nom']      ?? 'MISSING';
$debug['session_mail']     = $_SESSION['mail']     ?? 'MISSING';

try {
    $db      = new \Config\Database();
    $manager = $db->getManager();
    $id_str  = $_SESSION['user_id'] ?? null;

    // 2. Чи валідний ObjectId
    $debug['id_length'] = strlen($id_str ?? '');
    $debug['id_value']  = $id_str;

    if ($id_str && strlen($id_str) === 24) {
        $userId = new \MongoDB\BSON\ObjectId($id_str);
        $debug['objectid_created'] = (string)$userId;

        // 3. Шукаємо у колекції utilisateurs
        $query  = new \MongoDB\Driver\Query(['_id' => $userId]);
        $cursor = $manager->executeQuery('tncf.utilisateurs', $query);
        $arr    = $cursor->toArray();
        $debug['found_count'] = count($arr);

        if (count($arr) > 0) {
            $u = $arr[0];
            $debug['user_prenom'] = $u->prenom ?? 'NO FIELD';
            $debug['user_nom']    = $u->nom    ?? 'NO FIELD';
            $debug['user_mail']   = $u->mail   ?? 'NO FIELD';
            $debug['has_pass']    = isset($u->pass) ? 'YES' : 'NO';
        } else {
            // 4. Скидаємо всі документи з колекції (перші 3)
            $allQuery  = new \MongoDB\Driver\Query([], ['limit' => 3]);
            $allCursor = $manager->executeQuery('tncf.utilisateurs', $allQuery);
            $allDocs   = $allCursor->toArray();
            $debug['total_docs_sample'] = count($allDocs);
            foreach ($allDocs as $i => $doc) {
                $debug["doc_{$i}_id"]     = (string)$doc->_id;
                $debug["doc_{$i}_prenom"] = $doc->prenom ?? '?';
                $debug["doc_{$i}_mail"]   = $doc->mail   ?? '?';
            }
        }
    } else {
        $debug['error'] = 'user_id length is not 24 or missing';
    }
} catch (\Throwable $e) {
    $debug['exception'] = $e->getMessage();
}

echo json_encode($debug, JSON_PRETTY_PRINT);
