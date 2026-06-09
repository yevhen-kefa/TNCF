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
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit();
}

session_start();
if (!isset($_SESSION['user_id'])) {
    ob_clean();
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

try {
    require_once __DIR__ . '/../src/Config/Database.php';

    $db      = new \Config\Database();
    $manager = $db->getManager();
    $dbName  = $db->getDbName();
    $user_id = $_SESSION['user_id'];

    // 1. Шукаємо квитки поточного юзера
    $userIdObj = (strlen($user_id) === 24) ? new \MongoDB\BSON\ObjectId($user_id) : $user_id;

    $queryBillet  = new \MongoDB\Driver\Query(['id_uti' => $userIdObj]);
    $cursorBillet = $manager->executeQuery($dbName . '.billet', $queryBillet);
    $billets      = $cursorBillet->toArray();

    if (empty($billets)) {
        ob_clean();
        http_response_code(200);
        echo json_encode(['status' => 'success', 'tickets' => []]);
        exit();
    }

    // 2. Збираємо унікальні ID рейсів (id_voyage)
    $voyageIds = [];
    foreach ($billets as $b) {
        $vid = isset($b->id_voyage) ? (string)$b->id_voyage : null;
        if ($vid && !in_array($vid, $voyageIds)) {
            $voyageIds[] = $vid;
        }
    }

    // 3. Робимо запит до voyage (якщо вони там є)
    $voyageMap = [];
    if (!empty($voyageIds)) {
        $voyageObjs = [];
        foreach ($voyageIds as $id) {
            $voyageObjs[] = (strlen($id) === 24) ? new \MongoDB\BSON\ObjectId($id) : $id;
        }
        $queryVoyage  = new \MongoDB\Driver\Query(['_id' => ['$in' => $voyageObjs]]);
        $cursorVoyage = $manager->executeQuery($dbName . '.voyage', $queryVoyage);
        
        foreach ($cursorVoyage as $v) {
            $voyageMap[(string)$v->_id] = $v;
        }
    }

    // 4. Формуємо фінальний масив квитків (Розумний мапінг)
    $tickets = [];
    $now     = new DateTime();

    foreach ($billets as $b) {
        $vid    = isset($b->id_voyage) ? (string)$b->id_voyage : null;
        $voyage = $voyageMap[$vid] ?? null;

        // Статус
        $rawStatus = $b->statut ?? $b->status ?? 'active';
        $status = 'upcoming';

        if (str_starts_with(strtolower($rawStatus), 'annul')) {
            $status = 'cancelled';
        } elseif (str_starts_with(strtolower($rawStatus), 'utilis')) {
            $status = 'used';
        } else {
            $testDate = $voyage->date_depart ?? $b->date_depart ?? null;
            if ($testDate) {
                try {
                    $departDate = new DateTime($testDate);
                    if ($departDate < $now) {
                        $status = 'used';
                    }
                } catch (Exception $e) {}
            }
        }

        // Use voyage data if found, otherwise fall back to train_snapshot saved at booking time
        $snapshot = $b->train_snapshot ?? null;

        $tickets[] = [
            'id'            => (string)$b->_id,
            'depart'        => $voyage->depart        ?? $snapshot->depart       ?? 'N/A',
            'arriver'       => $voyage->arriver       ?? $snapshot->arriver      ?? 'N/A',
            'date_depart'   => $voyage->date_depart   ?? $snapshot->date_depart ?? $b->date_depart ?? 'N/A',
            'heure_depart'  => $voyage->heure_depart  ?? $snapshot->heure_depart ?? '--:--',
            'temps_arriver' => $voyage->duree         ?? $snapshot->duree        ?? 'N/A',
            'prix'          => (float)($b->prix_paye  ?? 0),
            'wagon'         => (string)($b->wagon     ?? '2'),
            'place'         => (string)($b->place     ?? 'N/A'),
            'train_num'     => $voyage->numero_train  ?? $snapshot->numero_train ?? 'N/A',
            'orderNumber'   => $b->orderNumber        ?? 'N/A',
            'status'        => $status,
        ];
    }

    // Сортуємо: спочатку 'upcoming', потім 'used', потім 'cancelled'
    usort($tickets, function($a, $b) {
        $order = ['upcoming' => 0, 'used' => 1, 'cancelled' => 2];
        $oa = $order[$a['status']] ?? 1;
        $ob = $order[$b['status']] ?? 1;
        if ($oa !== $ob) return $oa - $ob;
        return strcmp($b['date_depart'], $a['date_depart']);
    });

    ob_clean();
    http_response_code(200);
    echo json_encode(['status' => 'success', 'tickets' => $tickets]);

} catch (\Throwable $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}