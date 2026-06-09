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
    echo json_encode(['status' => 'error', 'message' => 'Dompdf manquant.']);
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

// Extract fields
$trainNum     = htmlspecialchars($data['train']['num']          ?? 'TGV INOUI');
$from         = htmlspecialchars($data['train']['from']         ?? 'Paris');
$to           = htmlspecialchars($data['train']['to']           ?? 'Lyon');
$dep          = htmlspecialchars($data['train']['dep']          ?? '--:--');
$cls          = ($data['train']['cls'] ?? '2') === '1' ? '1ère classe' : '2ème classe';
$total        = number_format((float)($data['total']            ?? 0), 2, '.', '');
$prenom       = htmlspecialchars($data['passenger']['prenom']   ?? '');
$nom          = htmlspecialchars($data['passenger']['nom']      ?? '');
$civilite     = htmlspecialchars($data['passenger']['civilite'] ?? 'M');
$email        = htmlspecialchars($data['contact']['email']      ?? '');
$orderNumber  = htmlspecialchars($data['orderNumber']           ?? 'TNCF-DEMO');
$arrivalTime  = htmlspecialchars($data['arrivalTime']           ?? '--:--');
$emissionDate = date('d/m/Y à H:i');

// Travel date — priority: train.date > travelDate param > today
$travelDateRaw = $data['train']['date'] ?? $data['travelDate'] ?? null;
if ($travelDateRaw) {
    $travelDate = date('d/m/Y', strtotime($travelDateRaw));
    $ts = strtotime($travelDateRaw);
} else {
    $travelDate = '—';
    $ts = false;
}

// French day label
$days   = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
$months = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
$dayLabel = $ts ? $days[date('w',$ts)] . ' ' . date('j',$ts) . ' ' . $months[date('n',$ts)-1] . ' ' . date('Y',$ts) : $travelDate;

// Seat
$assignedSeat = $data['assignedSeat'] ?? ['wagon' => 2, 'number' => '12A'];
$wagonNum     = htmlspecialchars((string)($assignedSeat['wagon']  ?? '2'));
$seatNumber   = htmlspecialchars((string)($assignedSeat['number'] ?? 'N/A'));

$html = '<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; background:#fff; color:#0a1628; font-size:12px; }

  /* HEADER */
  .header { background:#0a1628; padding:16px 32px; }
  .header table { width:100%; border:none; border-collapse:collapse; }
  .brand { font-size:22px; font-weight:bold; letter-spacing:5px; color:#c9a84c; }
  .brand-sub { font-size:8px; color:rgba(255,255,255,0.4); letter-spacing:2px; text-transform:uppercase; margin-top:2px; }
  .order-lbl { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:rgba(255,255,255,0.45); }
  .order-num { font-size:17px; font-weight:bold; color:#c9a84c; margin-top:2px; }
  .badge-confirmed { background:rgba(45,158,107,0.2); border:1px solid #2d9e6b; color:#2d9e6b; font-size:9px; font-weight:bold; padding:3px 10px; border-radius:20px; display:inline-block; margin-top:5px; }

  /* ROUTE BAND */
  .route-band { background:#f5f0e8; padding:14px 32px; border-bottom:2px solid #e0d9cc; }
  .route-title { font-size:19px; font-weight:bold; color:#0a1628; }
  .route-arrow { color:#c9a84c; margin:0 8px; }
  .route-date { font-size:11px; color:#5a6070; margin-top:3px; }

  /* BODY TABLE */
  .body-wrap { width:100%; border-collapse:collapse; }
  .col-l { width:50%; padding:22px 32px; border-right:1px dashed #ccc; vertical-align:top; }
  .col-r { width:50%; padding:22px 32px; vertical-align:top; }
  .section-lbl { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:#8a8f9e; margin-bottom:16px; }

  /* TIMELINE — fixed with table layout, no absolute positioning */
  .tl-table { width:100%; border-collapse:collapse; }
  .tl-dot-cell { width:18px; vertical-align:top; padding-top:3px; }
  .tl-dot { width:12px; height:12px; border-radius:50%; border:2.5px solid #c9a84c; background:#fff; }
  .tl-content-cell { padding-left:10px; padding-bottom:0; vertical-align:top; }
  .tl-time { font-size:26px; font-weight:bold; color:#0a1628; line-height:1; }
  .tl-city { font-size:13px; font-weight:bold; color:#0a1628; margin-top:3px; }
  .tl-sub  { font-size:10px; color:#8a8f9e; margin-top:2px; }

  /* Line connector between stops */
  .tl-line-cell { width:18px; text-align:center; padding:0; }
  .tl-line-inner { width:2px; height:30px; background:#c9a84c; margin:0 auto; }

  /* Train segment row */
  .tl-segment-cell { padding:0 0 0 28px; }
  .tl-segment-box { background:#f5f0e8; border:1px solid #e0d9cc; border-radius:6px; padding:8px 14px; }
  .seg-badge { background:#0a1628; color:#c9a84c; font-size:8px; font-weight:bold; padding:2px 7px; border-radius:3px; letter-spacing:1px; }
  .seg-name  { font-size:11px; font-weight:bold; color:#0a1628; margin-left:6px; }
  .seg-direct { font-size:9px; color:#2d9e6b; font-weight:bold; margin-top:4px; }

  /* RIGHT COLUMN */
  .drow { margin-bottom:14px; }
  .drow table { width:100%; border-collapse:collapse; }
  .dlbl { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:#8a8f9e; margin-bottom:4px; }
  .dval { font-size:13px; font-weight:bold; color:#0a1628; }
  .dval-lg { font-size:22px; font-weight:bold; color:#c9a84c; }
  .seat-badge { background:#0a1628; color:#c9a84c; font-size:12px; font-weight:bold; padding:5px 14px; border-radius:6px; display:inline-block; }
  .cls-badge  { background:#f5f0e8; border:1px solid #c9a84c; color:#0a1628; font-size:10px; font-weight:bold; padding:4px 10px; border-radius:4px; display:inline-block; }
  .divider { border:none; border-top:1px solid #e8e2d8; margin:12px 0; }

  /* FOOTER */
  .footer { background:#f5f0e8; border-top:2px solid #e0d9cc; padding:10px 32px; }
  .footer table { width:100%; border-collapse:collapse; }
  .footer-txt { font-size:10px; color:#5a6070; }
  .footer-strong { font-weight:bold; color:#0a1628; }
  .footer-em { font-size:10px; color:#8a8f9e; text-align:right; }
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
  <table>
    <tr>
      <td style="padding:0;">
        <div class="brand">TNCF</div>
        <div class="brand-sub">Le réseau grande vitesse</div>
      </td>
      <td style="padding:0;text-align:right;">
        <div class="order-lbl">N° de réservation</div>
        <div class="order-num">' . $orderNumber . '</div>
        <span class="badge-confirmed">Confirmé</span>
      </td>
    </tr>
  </table>
</div>

<!-- ROUTE BAND -->
<div class="route-band">
  <div class="route-title">' . $from . ' <span class="route-arrow">&#8594;</span> ' . $to . '</div>
  <div class="route-date">' . $dayLabel . '</div>
</div>

<!-- BODY -->
<table class="body-wrap">
<tr>

  <!-- LEFT: Timeline using table rows (no absolute positioning) -->
  <td class="col-l">
    <div class="section-lbl">Détail du trajet</div>
    <table class="tl-table">

      <!-- DEPARTURE -->
      <tr>
        <td class="tl-dot-cell"><div class="tl-dot"></div></td>
        <td class="tl-content-cell">
          <div class="tl-time">' . $dep . '</div>
          <div class="tl-city">' . $from . '</div>
          <div class="tl-sub">Gare de départ</div>
        </td>
      </tr>

      <!-- CONNECTOR LINE -->
      <tr>
        <td class="tl-line-cell"><div class="tl-line-inner"></div></td>
        <td></td>
      </tr>

      <!-- TRAIN SEGMENT -->
      <tr>
        <td class="tl-line-cell"><div class="tl-line-inner"></div></td>
        <td class="tl-segment-cell">
          <div class="tl-segment-box">
            <span class="seg-badge">TNCF</span><span class="seg-name">' . $trainNum . '</span>
            <div class="seg-direct">Direct · Sans correspondance</div>
          </div>
        </td>
      </tr>

      <!-- CONNECTOR LINE -->
      <tr>
        <td class="tl-line-cell"><div class="tl-line-inner"></div></td>
        <td></td>
      </tr>

      <!-- ARRIVAL -->
      <tr>
        <td class="tl-dot-cell" style="padding-top:3px;"><div class="tl-dot"></div></td>
        <td class="tl-content-cell">
          <div class="tl-time">' . $arrivalTime . '</div>
          <div class="tl-city">' . $to . '</div>
          <div class="tl-sub">Gare d\'arrivée</div>
        </td>
      </tr>

    </table>
  </td>

  <!-- RIGHT: Details -->
  <td class="col-r">
    <div class="section-lbl">Passager &amp; Billet</div>

    <!-- Passenger + Class -->
    <div class="drow">
      <table>
        <tr>
          <td style="padding:0;width:55%;">
            <div class="dlbl">Passager</div>
            <div class="dval">' . $civilite . '. ' . $prenom . ' ' . $nom . '</div>
          </td>
          <td style="padding:0;width:45%;">
            <div class="dlbl">Classe</div>
            <span class="cls-badge">' . $cls . '</span>
          </td>
        </tr>
      </table>
    </div>
    <hr class="divider">

    <!-- Seat + Date -->
    <div class="drow">
      <table>
        <tr>
          <td style="padding:0;width:55%;">
            <div class="dlbl">Siège attribué</div>
            <span class="seat-badge">Voit. ' . $wagonNum . ' &middot; ' . $seatNumber . '</span>
          </td>
          <td style="padding:0;width:45%;">
            <div class="dlbl">Date du voyage</div>
            <div class="dval">' . $travelDate . '</div>
          </td>
        </tr>
      </table>
    </div>
    <hr class="divider">

    <!-- Price + Email -->
    <div class="drow">
      <table>
        <tr>
          <td style="padding:0;width:55%;">
            <div class="dlbl">Total payé</div>
            <div class="dval-lg">' . $total . '&#8364;</div>
          </td>
          <td style="padding:0;width:45%;vertical-align:top;">
            <div class="dlbl">E-mail</div>
            <div class="dval" style="font-size:10px;word-break:break-all;">' . $email . '</div>
          </td>
        </tr>
      </table>
    </div>

  </td>
</tr>
</table>

<!-- FOOTER -->
<div class="footer">
  <table>
    <tr>
      <td style="padding:0;" class="footer-txt">
        <span class="footer-strong">Billet électronique TNCF</span>
        &middot; Valable uniquement pour le trajet indiqué &middot; Présentez ce billet au contrôleur
      </td>
      <td style="padding:0;" class="footer-em">Émis le ' . $emissionDate . '</td>
    </tr>
  </table>
</div>

</body>
</html>';

try {
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('isRemoteEnabled', false);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    ob_clean();
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="ticket-' . $orderNumber . '.pdf"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    echo $dompdf->output();
    ob_end_flush();
    exit();
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'PDF Error: ' . $e->getMessage()]);
    exit();
}