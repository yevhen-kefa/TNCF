<?php
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
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
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


$html = <<<HTML



<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: DejaVu Sans, sans-serif;
      background: #f5f0e8;
      color: #0a1628;
      padding: 32px;
      font-size: 13px;
    }

    /* ── En-tête ── */
    .header {
      background: #0a1628;
      border-radius: 12px 12px 0 0;
      padding: 24px 32px;
    }

    .header-top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 0;
    }

    .brand {
      font-size: 22px;
      font-weight: bold;
      letter-spacing: 4px;
      color: #c9a84c;
      font-family: serif;
    }

    .brand-sub {
      font-size: 9px;
      color: rgba(255,255,255,0.4);
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-top: 3px;
    }

    .order-block {
      text-align: right;
    }

    .order-label {
      font-size: 9px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: rgba(255,255,255,0.45);
      margin-bottom: 4px;
    }

    .order-num {
      font-family: serif;
      font-size: 18px;
      font-weight: bold;
      color: #c9a84c;
      letter-spacing: 1px;
    }

    /* ── Corps blanc ── */
    .body {
      background: #ffffff;
      border-radius: 0 0 12px 12px;
      padding: 28px 32px;
      margin-bottom: 20px;
    }

    /* ── Trajet ── */
    .trajet-table {
      width: 100%;
      margin-bottom: 24px;
      padding-bottom: 20px;
      border-bottom: 2px dashed #e8e2d8;
    }

    .trajet-table td {
      vertical-align: middle;
      padding: 0;
    }

    .td-station { width: 30%; }
    .td-line    { width: 40%; text-align: center; }
    .td-arr     { width: 30%; text-align: right; }

    .station-time {
      font-family: serif;
      font-size: 32px;
      font-weight: bold;
      color: #0a1628;
      line-height: 1;
    }

    .station-city {
      font-size: 14px;
      font-weight: bold;
      color: #0a1628;
      margin-top: 4px;
    }

    .station-sub {
      font-size: 11px;
      color: #8a8f9e;
      margin-top: 2px;
    }

    .train-badge {
      display: inline-block;
      background: #0a1628;
      color: #c9a84c;
      font-size: 9px;
      font-weight: bold;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: 4px 10px;
      border-radius: 4px;
      margin-bottom: 10px;
    }

    .rail {
      width: 100%;
      height: 2px;
      background: #c9a84c;
      margin: 4px 0;
    }

    .direct {
      font-size: 10px;
      color: #2d9e6b;
      font-weight: bold;
      margin-top: 4px;
    }

    /* ── Grille de détails ── */
    .details-table {
      width: 100%;
      border-collapse: collapse;
    }

    .details-table td {
      padding: 14px 20px;
      border-right: 1px solid #f0ece4;
      vertical-align: top;
    }

    .details-table td:last-child {
      border-right: none;
    }

    .detail-label {
      font-size: 9px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: #8a8f9e;
      margin-bottom: 5px;
    }

    .detail-value {
      font-size: 13px;
      font-weight: bold;
      color: #0a1628;
    }

    .detail-price {
      font-family: serif;
      font-size: 16px;
      color: #c9a84c;
    }

    /* ── Badge confirmé ── */
    .confirmed-badge {
      display: inline-block;
      background: rgba(45, 158, 107, 0.15);
      border: 1px solid rgba(45, 158, 107, 0.4);
      color: #2d9e6b;
      font-size: 10px;
      font-weight: bold;
      padding: 5px 12px;
      border-radius: 20px;
      letter-spacing: 0.5px;
    }

    /* ── Footer ── */
    .footer {
      text-align: center;
      font-size: 10px;
      color: #8a8f9e;
      line-height: 1.6;
      margin-top: 8px;
    }

    .footer-strong {
      color: #0a1628;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <!-- EN-TÊTE -->
  <div class="header">
    <table class="trajet-table" style="width:100%;border:none;">
      <tr>
        <td style="padding:0;">
          <div class="brand">TNCF</div>
          <div class="brand-sub">Le réseau grande vitesse</div>
        </td>
        <td style="padding:0;text-align:right;">
          <div class="order-label">Numéro de commande</div>
          <div class="order-num">{$orderNumber}</div>
          <div style="margin-top:8px">
            <span class="confirmed-badge">✓ Confirmé</span>
          </div>
        </td>
      </tr>
    </table>
  </div>

  <!-- CORPS -->
  <div class="body">

    <!-- Trajet -->
    <table class="trajet-table" style="width:100%;">
      <tr>
        <td class="td-station">
          <div class="station-time">{$dep}</div>
          <div class="station-city">{$from}</div>
          <div class="station-sub">Gare de Lyon</div>
        </td>
        <td class="td-line">
          <div class="train-badge">{$trainNum}</div>
          <div class="rail"></div>
          <div class="direct">✓ Direct</div>
        </td>
        <td class="td-arr">
          <div class="station-time">Arrivée</div>
          <div class="station-city">{$to}</div>
          <div class="station-sub">Part-Dieu</div>
        </td>
      </tr>
    </table>

    <!-- Détails -->
    <table class="details-table">
      <tr>
        <td>
          <div class="detail-label">Passager</div>
          <div class="detail-value">{$civilite}. {$prenom} {$nom}</div>
        </td>
        <td>
          <div class="detail-label">Classe</div>
          <div class="detail-value">{$cls}</div>
        </td>
        <td>
          <div class="detail-label">E-mail</div>
          <div class="detail-value" style="font-size:11px">{$email}</div>
        </td>
        <td>
          <div class="detail-label">Total payé</div>
          <div class="detail-value detail-price">{$total}€</div>
        </td>
      </tr>
    </table>

  </div>

  <!-- FOOTER -->
  <div class="footer">
    <span class="footer-strong">Billet électronique TNCF</span> · Valable uniquement pour le trajet indiqué<br>
    Émis le {$emissionDate} · Présentez ce billet au contrôleur lors de votre voyage<br>
    En cas de problème, contactez le service client TNCF
  </div>

</body>
</html>
HTML;

// ── Génération avec Dompdf ────────────────────────

$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', false); 

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="ticket-' . $orderNumber . '.pdf"');
echo $dompdf->output();
exit();