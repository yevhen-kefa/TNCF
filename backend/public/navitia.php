<?php
/**
 * navitia.php — Mock local de l'API Navitia (OpenData SNCF).
 *
 * Remplace temporairement le proxy externe (yevhensrv.alwaysdata.net/navitia.php)
 * le temps d'obtenir une vraie clé API SNCF/Navitia.
 *
 * Renvoie des trajets au FORMAT EXACT de Navitia, donc le code de parsing du
 * frontend (Tickets.tsx) fonctionne sans aucune modification :
 *   {
 *     "journeys": [
 *       {
 *         "departure_date_time": "YYYYMMDDTHHmmss",
 *         "duration": <secondes>,
 *         "sections": [
 *           { "type": "public_transport",
 *             "display_informations": { "commercial_mode": "TGV", "headsign": "INOUI" } }
 *         ]
 *       }, ...
 *     ]
 *   }
 *
 * Paramètres acceptés (identiques à l'appel frontend) :
 *   endpoint, from, to, datetime (YYYYMMDDTHHmmss), min_nb_journeys
 *
 * Pour repasser sur la vraie API plus tard : voir la fonction navitia_real()
 * en bas de fichier (désactivée) et remettre l'URL du proxy dans Tickets.tsx.
 */

header("Access-Control-Allow-Origin: *");           // pas de cookies sur cet appel -> '*' OK
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

// ── Lecture des paramètres ──────────────────────────────────────────────
$from        = $_GET['from']            ?? 'admin:fr:75056';
$to          = $_GET['to']              ?? 'admin:fr:69123';
$datetime    = $_GET['datetime']        ?? '';
$minJourneys = isset($_GET['min_nb_journeys']) ? max(1, (int)$_GET['min_nb_journeys']) : 20;

// Date demandée = 8 premiers caractères "YYYYMMDD" ; sinon aujourd'hui.
$date = substr($datetime, 0, 8);
if (!preg_match('/^\d{8}$/', $date)) {
    $date = date('Ymd');
}

// Heure mini demandée (utile quand la recherche est "aujourd'hui") en minutes.
$startMinute = 0;
$tPos = strpos($datetime, 'T');
if ($tPos !== false && preg_match('/^(\d{2})(\d{2})/', substr($datetime, $tPos + 1), $m)) {
    $startMinute = ((int)$m[1]) * 60 + (int)$m[2];
}

// ── Durée de base déterministe selon l'itinéraire ───────────────────────
// crc32 -> même couple (from,to) = même durée de référence, rejouable.
$routeHash    = crc32($from . '>' . $to);
$baseDuration = 90 + ($routeHash % 230);   // 1h30 à ~5h20

// ── Génération des trajets sur la journée ───────────────────────────────
$firstDep = 6 * 60;          // 06h00
$lastDep  = 22 * 60 + 30;    // 22h30
$target   = max($minJourneys, 16);
$step     = max(18, intdiv($lastDep - $firstDep, $target));

$journeys = [];
$idx = 0;

for ($t = $firstDep; $t <= $lastDep && count($journeys) < $target; $t += $step) {
    $depMin = $t + (($routeHash >> ($idx % 6)) % 11);   // petit décalage "humain"

    // Pour une recherche "aujourd'hui", on n'affiche pas les départs passés.
    if ($depMin < $startMinute) { $idx++; continue; }

    $hh = intdiv($depMin, 60);
    $mm = $depMin % 60;
    if ($hh > 23) break;

    // Durée = base +/- ~12 min de variance, plancher 45 min.
    $durMin = $baseDuration + (($idx * 7) % 25) - 12;
    if ($durMin < 45) { $durMin = 45; }

    // Marque commerciale : majorité INOUI, un OUIGO sur quatre.
    $brand = ($idx % 4 === 0)
        ? ['commercial_mode' => 'TGV', 'headsign' => 'OUIGO']
        : ['commercial_mode' => 'TGV', 'headsign' => 'INOUI'];

    $journeys[] = [
        'departure_date_time' => sprintf('%sT%02d%02d00', $date, $hh, $mm),
        'duration'            => $durMin * 60,
        'sections'            => [
            [
                'type'                 => 'public_transport',
                'display_informations' => $brand,
            ],
        ],
    ];
    $idx++;
}

echo json_encode(['journeys' => $journeys], JSON_UNESCAPED_UNICODE);

/*
 * ─────────────────────────────────────────────────────────────────────────
 *  PASSAGE À LA VRAIE API NAVITIA (à activer une fois la clé obtenue)
 * ─────────────────────────────────────────────────────────────────────────
 *  1. Récupérez une clé gratuite sur https://navitia.io/ (ou le portail
 *     numerique.sncf.com). La clé sert de "username" en HTTP Basic, sans mot
 *     de passe.
 *  2. Mettez la clé dans une variable d'environnement NAVITIA_TOKEN (ne pas
 *     la committer en clair).
 *  3. Remplacez le bloc de génération ci-dessus par un appel proxifié :
 *
 *  function navitia_real(string $endpoint, array $params): array {
 *      $token = getenv('NAVITIA_TOKEN') ?: '';
 *      $url = 'https://api.navitia.io/v1/' . ltrim($endpoint, '/')
 *           . '?' . http_build_query($params);
 *      $ch = curl_init($url);
 *      curl_setopt_array($ch, [
 *          CURLOPT_RETURNTRANSFER => true,
 *          CURLOPT_USERPWD        => $token . ':',   // Basic auth, clé = user
 *          CURLOPT_TIMEOUT        => 10,
 *      ]);
 *      $body = curl_exec($ch);
 *      curl_close($ch);
 *      return json_decode($body, true) ?: ['journeys' => []];
 *  }
 */
