<?php
ob_start();

$allowed_origin = "http://localhost:3000"; 
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type"); 

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit();
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Dompdf manquant. Veuillez installer avec composer.']);
    ob_end_flush();
    exit();
}
require_once $autoloadPath;

use Dompdf\Dompdf;
use Dompdf\Options;

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    ob_end_flush();
    exit();
}

$trainNum    = htmlspecialchars($data['train']['num']      ?? 'TGV INOUI');
$from        = htmlspecialchars($data['train']['from']     ?? 'Paris');
$to          = htmlspecialchars($data['train']['to']       ?? 'Lyon');
$dep         = htmlspecialchars($data['train']['dep']      ?? '--:--');
$cls         = ($data['train']['cls'] ?? '2') === '1' ? '1ère classe' : '2ème classe';
$total       = number_format((float)($data['total']        ?? 0), 2, '.', '');
$prenom      = htmlspecialchars($data['passenger']['prenom']    ?? '');
$nom         = htmlspecialchars($data['passenger']['nom']       ?? '');
$civilite    = htmlspecialchars($data['passenger']['civilite']  ?? 'M');
$email       = htmlspecialchars($data['contact']['email']       ?? '');
$emissionDate = date('d/m/Y à H:i');
$orderNumber = htmlspecialchars($data['orderNumber']      ?? 'TNCF-DEMO');

$travelDateRaw = $data['travelDate'] ?? date('Y-m-d');
$travelDate    = date('d/m/Y', strtotime($travelDateRaw));
$arrivalTime   = htmlspecialchars($data['arrivalTime'] ?? '--:--');

$assignedSeat = $data['assignedSeat'] ?? ['wagon' => 2, 'number' => '12A'];
$wagonNum   = htmlspecialchars((string)$assignedSeat['wagon']);
$seatNumber = htmlspecialchars((string)$assignedSeat['number']);
$seatDisplay = "Voiture $wagonNum · Place $seatNumber";

// 4. HTML ШАБЛОН (безпечний варіант)
$html = '
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; background: #f5f0e8; color: #0a1628; padding: 32px; font-size: 13px; }
    .header { background: #0a1628; border-radius: 12px 12px 0 0; padding: 24px 32px; }
    .brand { font-size: 22px; font-weight: bold; letter-spacing: 4px; color: #c9a84c; font-family: serif; }
    .brand-sub { font-size: 9px; color: rgba(255,255,255,0.4); letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }
    .order-label { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.45); margin-bottom: 4px; }
    .order-num { font-size: 18px; font-weight: bold; color: #c9a84c; letter-spacing: 1px; }
    .confirmed-badge { display: inline-block; background: rgba(45, 158, 107, 0.15); border: 1px solid rgba(45, 158, 107, 0.4); color: #2d9e6b; font-size: 10px; font-weight: bold; padding: 5px 12px; border-radius: 20px; letter-spacing: 0.5px; }
    .body { background: #ffffff; border-radius: 0 0 12px 12px; padding: 28px 32px; margin-bottom: 20px; }
    .trajet-table { width: 100%; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px dashed #e8e2d8; }
    .trajet-table td { vertical-align: middle; padding: 0; }
    .td-station { width: 30%; }
    .td-line { width: 40%; text-align: center; }
    .td-arr { width: 30%; text-align: right; }
    .station-time { font-size: 32px; font-weight: bold; color: #0a1628; line-height: 1; }
    .station-city { font-size: 14px; font-weight: bold; color: #0a1628; margin-top: 4px; }
    .train-badge { display: inline-block; background: #0a1628; color: #c9a84c; font-size: 9px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; padding: 4px 10px; border-radius: 4px; margin-bottom: 10px; }
    .rail { width: 100%; height: 2px; background: #c9a84c; margin: 4px 0; }
    .direct { font-size: 10px; color: #2d9e6b; font-weight: bold; margin-top: 4px; }
    .details-table { width: 100%; border-collapse: collapse; }
    .details-table td { padding: 14px 20px; border-right: 1px solid #f0ece4; vertical-align: top; width: 25%; }
    .details-table td:last-child { border-right: none; }
    .details-table tr:first-child td { border-bottom: 1px solid #f0ece4; }
    .detail-label { font-size: 9px; letter-spacing: 1.5px; text-transform: uppercase; color: #8a8f9e; margin-bottom: 5px; }
    .detail-value { font-size: 13px; font-weight: bold; color: #0a1628; }
    .detail-price { font-size: 16px; color: #c9a84c; }
    .seat-badge { display: inline-block; background: #0a1628; color: #c9a84c; font-size: 11px; font-weight: bold; padding: 4px 10px; border-radius: 6px; letter-spacing: 1px; }
    .footer { text-align: center; font-size: 10px; color: #8a8f9e; line-height: 1.6; margin-top: 8px; }
    .footer-strong { color: #0a1628; font-weight: bold; }
  </style>
</head>
<body>
  <div class="header">
    <table style="width:100%;border:none;">
      <tr>
        <td style="padding:0;">
          <div class="brand">TNCF</div>
          <div class="brand-sub">Le réseau grande vitesse</div>
        </td>
        <td style="padding:0;text-align:right;">
          <div class="order-label">Numéro de commande</div>
          <div class="order-num">' . $orderNumber . '</div>
          <div style="margin-top:8px;">
            <span class="confirmed-badge">Confirmé</span>
          </div>
        </td>
      </tr>
    </table>
  </div>
  <div class="body">
    <table class="trajet-table" style="width:100%;">
      <tr>
        <td class="td-station">
          <div class="station-time">' . $dep . '</div>
          <div class="station-city">' . $from . '</div>
        </td>
        <td class="td-line">
          <div class="train-badge">' . $trainNum . '</div>
          <div class="rail"></div>
          <div class="direct">Direct</div>
        </td>
        <td class="td-arr">
          <div class="station-time">' . $arrivalTime . '</div>
          <div class="station-city">' . $to . '</div>
        </td>
      </tr>
    </table>
    <table class="details-table">
      <tr>
        <td>
          <div class="detail-label">Passager</div>
          <div class="detail-value">' . $civilite . '. ' . $prenom . ' ' . $nom . '</div>
        </td>
        <td>
          <div class="detail-label">Classe</div>
          <div class="detail-value">' . $cls . '</div>
        </td>
        <td>
          <div class="detail-label">Date du voyage</div>
          <div class="detail-value">' . $travelDate . '</div>
        </td>
        <td>
          <div class="detail-label">Place attribuée</div>
          <div class="detail-value">
            <span class="seat-badge">' . $seatDisplay . '</span>
          </div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="detail-label">E-mail</div>
          <div class="detail-value" style="font-size:11px;">' . $email . '</div>
        </td>
        <td>
          <div class="detail-label">Total payé</div>
          <div class="detail-value detail-price">' . $total . '€</div>
        </td>
        <td>
          <div class="detail-label">Émis le</div>
          <div class="detail-value" style="font-size:11px;">' . $emissionDate . '</div>
        </td>
        <td></td>
      </tr>
    </table>
  </div>
  <div class="footer">
    <span class="footer-strong">Billet électronique TNCF</span> · Valable uniquement pour le trajet indiqué<br>
    Présentez ce billet au contrôleur lors de votre voyage
  </div>
</body>
</html>
';

// 5. ГЕНЕРАЦІЯ ТА ВИВІД PDF (з відловом помилок)
try {
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('isRemoteEnabled', false);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    ob_clean(); 
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="ticket-' . $orderNumber . '.pdf"');
    header('Cache-Control: no-cache, no-store, must-revalidate');

    echo $dompdf->output();
    ob_end_flush();
    exit();
    
} catch (Exception $e) {
    ob_clean(); // Очищаємо сміття
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'PDF Error: ' . $e->getMessage()]);
    exit();
}