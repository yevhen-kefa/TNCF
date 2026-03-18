<?php
// Start session to access logged-in user data
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['mail'])) {
    header("Location: login.php");
    exit();
}

// Prepare variables for display
$userFullName = ucfirst($_SESSION['prenom']) . ' ' . strtoupper($_SESSION['nom']);
$userEmail = $_SESSION['mail'];
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TNCF — Mon Compte</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style/count.css">
</head>
<body>
<nav class="topbar">
  <a href="index.php" class="brand">
    <div class="brand-logo">
      <img src="img/logo.svg" alt="Logo TNCF">
    </div>
  </a>

  <ul class="nav-links">
    <li><a href="index.php">Voyager</a></li>
    <li><a href="tickets.php">Billets</a></li>
    <li><a href="#">Offres</a></li>
    <li><a href="count.php" class="active">Count</a></li>
  </ul>
  <div class="nav-actions">
    <a href="tickets.php" class="btn-nav-outline">+ Réserver</a>
  </div>

</nav>

<div class="page-band">
  <div class="page-band-inner">
    <div class="page-eyebrow">Espace personnel</div>
    <h1 class="page-title">Mon Compte</h1>
  </div>
</div>


<div class="page-content">
  <div class="profile-card">
    <div class="avatar-wrap">
      <div class="avatar">JD</div>
      <div class="avatar-status"></div>
    </div>

    <div class="profile-info">
      <div class="profile-name"><?= $userFullName ?></div>
      <div class="profile-email">
        <img src="img/mail.svg" alt="">
        <?= $userEmail ?>
      </div>
      <div class="profile-badges">
        <span class="profile-badge badge-member">
          <img src="img/pass.svg" alt="">
          Membre Gold
        </span>
        <span class="profile-badge badge-verified">
          <img src="img/shield.svg" alt="">
          Compte vérifié
        </span>
      </div>

    </div>

    <!-- Statistiques -->
    <div class="profile-stats">
      <div class="stat-item">
        <div class="stat-num">12</div>
        <div class="stat-label">Trajets</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">4</div>
        <div class="stat-label">À venir</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">2 340</div>
        <div class="stat-label">km parcourus</div>
      </div>
    </div>

    <!-- Bouton modifier -->
    <a href="#" class="btn-edit">
      <img src="img/person_white.svg" alt="">
      Modifier le profil
    </a>

  </div><!-- fin .profile-card -->


  <!-- ════════════════════════════════════
       GRILLE DU MENU COMPTE
       6 cartes en 3 colonnes
  ════════════════════════════════════ -->
  <div class="account-grid">

    <a href="#" class="account-menu-card">
      <div class="menu-card-icon">
        <img src="img/ticket.svg" alt="">
      </div>
      <div class="menu-card-text">
        <h4>Mes Réservations</h4>
        <p>Consulter et gérer vos billets</p>
      </div>
      <span class="menu-card-arrow">
        <img src="img/arrive.svg" alt="→">
      </span>
    </a>

    <a href="#" class="account-menu-card">
      <div class="menu-card-icon">
        <img src="img/cart.svg" alt="">
      </div>
      <div class="menu-card-text">
        <h4>Moyens de paiement</h4>
        <p>Cartes bancaires enregistrées</p>
      </div>
      <span class="menu-card-arrow">
        <img src="img/arrive.svg" alt="→">
      </span>
    </a>

    <a href="#" class="account-menu-card">
      <div class="menu-card-icon">
        <img src="img/box_g.svg" alt="">
      </div>
      <div class="menu-card-text">
        <h4>Mes avantages</h4>
        <p>Codes promo et réductions</p>
      </div>
      <span class="menu-card-arrow">
        <img src="img/arrive.svg" alt="→">
      </span>
    </a>

    <a href="#" class="account-menu-card">
      <div class="menu-card-icon">
        <img src="img/alert.svg" alt="">
      </div>
      <div class="menu-card-text">
        <h4>Alertes &amp; Notifications</h4>
        <p>SMS et e-mails de suivi</p>
      </div>
      <span class="menu-card-arrow">
        <img src="img/arrive.svg" alt="→">
      </span>
    </a>

    <a href="#" class="account-menu-card">
      <div class="menu-card-icon">
        <img src="img/shield.svg" alt="">
      </div>
      <div class="menu-card-text">
        <h4>Sécurité</h4>
        <p>Mot de passe et authentification</p>
      </div>
      <span class="menu-card-arrow">
        <img src="img/arrive.svg" alt="→">
      </span>
    </a>

    <a href="#" class="account-menu-card">
      <div class="menu-card-icon">
        <img src="img/layer.svg" alt="">
      </div>
      <div class="menu-card-text">
        <h4>Paramètres</h4>
        <p>Langue, accessibilité, données</p>
      </div>
      <span class="menu-card-arrow">
        <img src="img/arrive.svg" alt="→">
      </span>
    </a>

  </div><!-- fin .account-grid -->


  <!-- ════════════════════════════════════
       MES BILLETS  (galerie scrollable)
  ════════════════════════════════════ -->

  <div class="section-header">
    <div>
      <div class="section-eyebrow">Historique &amp; À venir</div>
      <h2 class="section-title">Mes Billets</h2>
    </div>
    <span class="section-count">12 billets</span>
  </div>

  <div class="tickets-outer">

    <!-- Flèche gauche -->
    <button class="scroll-arrow left" id="arrowLeft" aria-label="Défiler à gauche">
      <img src="img/arrow_l.svg" alt="←">
    </button>

    <!-- Galerie -->
    <div class="tickets-scroll" id="ticketsScroll">


      <!-- ── BILLET 1 ── -->
      <div class="ticket-card">
        <div class="ticket-header">
          <div class="ticket-class-tag">1ère</div>
          <div class="ticket-train-num">
            <img src="img/depart.svg" alt="">
            TGV 6601
          </div>
          <div class="ticket-route">
            <div class="ticket-city">Paris</div>
            <div class="ticket-arrow-wrap">
              <div class="ticket-arrow-line"></div>
              <div class="ticket-dur">1h 55min</div>
            </div>
            <div class="ticket-city">Lyon</div>
          </div>
        </div>
        <div class="ticket-body">
          <div class="ticket-punch">
            <div class="punch-hole"></div>
            <div class="punch-line"></div>
            <div class="punch-hole"></div>
          </div>
          <div class="ticket-details">
            <div><div class="detail-label">Date</div><div class="detail-value">18 mars 2026</div></div>
            <div><div class="detail-label">Départ</div><div class="detail-value">06:47</div></div>
            <div><div class="detail-label">Voyageur</div><div class="detail-value">J. Dupont</div></div>
            <div><div class="detail-label">Siège</div><div class="detail-value">Voiture 4 · 12A</div></div>
          </div>
          <div class="ticket-footer">
            <span class="ticket-status status-upcoming">● À venir</span>
            <span class="ticket-price">89€</span>
          </div>
        </div>
      </div>


      <!-- ── BILLET 2 ── -->
      <div class="ticket-card">
        <div class="ticket-header">
          <div class="ticket-class-tag">2ème</div>
          <div class="ticket-train-num">
            <img src="img/depart.svg" alt="">
            TGV 6820
          </div>
          <div class="ticket-route">
            <div class="ticket-city">Paris</div>
            <div class="ticket-arrow-wrap">
              <div class="ticket-arrow-line"></div>
              <div class="ticket-dur">3h 05min</div>
            </div>
            <div class="ticket-city">Marseille</div>
          </div>
        </div>
        <div class="ticket-body">
          <div class="ticket-punch">
            <div class="punch-hole"></div>
            <div class="punch-line"></div>
            <div class="punch-hole"></div>
          </div>
          <div class="ticket-details">
            <div><div class="detail-label">Date</div><div class="detail-value">22 mars 2026</div></div>
            <div><div class="detail-label">Départ</div><div class="detail-value">10:15</div></div>
            <div><div class="detail-label">Voyageur</div><div class="detail-value">J. Dupont</div></div>
            <div><div class="detail-label">Siège</div><div class="detail-value">Voiture 7 · 33B</div></div>
          </div>
          <div class="ticket-footer">
            <span class="ticket-status status-upcoming">● À venir</span>
            <span class="ticket-price">45€</span>
          </div>
        </div>
      </div>


      <!-- ── BILLET 3 ── -->
      <div class="ticket-card">
        <div class="ticket-header">
          <div class="ticket-class-tag">2ème</div>
          <div class="ticket-train-num">
            <img src="img/depart.svg" alt="">
            TGV 7214
          </div>
          <div class="ticket-route">
            <div class="ticket-city">Bordeaux</div>
            <div class="ticket-arrow-wrap">
              <div class="ticket-arrow-line"></div>
              <div class="ticket-dur">2h 04min</div>
            </div>
            <div class="ticket-city">Paris</div>
          </div>
        </div>
        <div class="ticket-body">
          <div class="ticket-punch">
            <div class="punch-hole"></div>
            <div class="punch-line"></div>
            <div class="punch-hole"></div>
          </div>
          <div class="ticket-details">
            <div><div class="detail-label">Date</div><div class="detail-value">28 févr. 2026</div></div>
            <div><div class="detail-label">Départ</div><div class="detail-value">14:30</div></div>
            <div><div class="detail-label">Voyageur</div><div class="detail-value">J. Dupont</div></div>
            <div><div class="detail-label">Siège</div><div class="detail-value">Voiture 2 · 08C</div></div>
          </div>
          <div class="ticket-footer">
            <span class="ticket-status status-used">● Utilisé</span>
            <span class="ticket-price">29€</span>
          </div>
        </div>
      </div>


      <!-- ── BILLET 4 ── -->
      <div class="ticket-card">
        <div class="ticket-header">
          <div class="ticket-class-tag">1ère</div>
          <div class="ticket-train-num">
            <img src="img/depart.svg" alt="">
            TGV 5503
          </div>
          <div class="ticket-route">
            <div class="ticket-city">Paris</div>
            <div class="ticket-arrow-wrap">
              <div class="ticket-arrow-line"></div>
              <div class="ticket-dur">1h 02min</div>
            </div>
            <div class="ticket-city">Lille</div>
          </div>
        </div>
        <div class="ticket-body">
          <div class="ticket-punch">
            <div class="punch-hole"></div>
            <div class="punch-line"></div>
            <div class="punch-hole"></div>
          </div>
          <div class="ticket-details">
            <div><div class="detail-label">Date</div><div class="detail-value">14 févr. 2026</div></div>
            <div><div class="detail-label">Départ</div><div class="detail-value">08:00</div></div>
            <div><div class="detail-label">Voyageur</div><div class="detail-value">J. Dupont</div></div>
            <div><div class="detail-label">Siège</div><div class="detail-value">Voiture 1 · 02A</div></div>
          </div>
          <div class="ticket-footer">
            <span class="ticket-status status-used">● Utilisé</span>
            <span class="ticket-price">59€</span>
          </div>
        </div>
      </div>


      <!-- ── BILLET 5 ── -->
      <div class="ticket-card">
        <div class="ticket-header">
          <div class="ticket-class-tag">2ème</div>
          <div class="ticket-train-num">
            <img src="img/depart.svg" alt="">
            TGV 6340
          </div>
          <div class="ticket-route">
            <div class="ticket-city">Lyon</div>
            <div class="ticket-arrow-wrap">
              <div class="ticket-arrow-line"></div>
              <div class="ticket-dur">1h 45min</div>
            </div>
            <div class="ticket-city">Nice</div>
          </div>
        </div>
        <div class="ticket-body">
          <div class="ticket-punch">
            <div class="punch-hole"></div>
            <div class="punch-line"></div>
            <div class="punch-hole"></div>
          </div>
          <div class="ticket-details">
            <div><div class="detail-label">Date</div><div class="detail-value">05 janv. 2026</div></div>
            <div><div class="detail-label">Départ</div><div class="detail-value">11:22</div></div>
            <div><div class="detail-label">Voyageur</div><div class="detail-value">J. Dupont</div></div>
            <div><div class="detail-label">Siège</div><div class="detail-value">Voiture 5 · 17D</div></div>
          </div>
          <div class="ticket-footer">
            <span class="ticket-status status-cancelled">● Annulé</span>
            <span class="ticket-price">38€</span>
          </div>
        </div>
      </div>


      <!-- ── BILLET 6 ── -->
      <div class="ticket-card">
        <div class="ticket-header">
          <div class="ticket-class-tag">1ère</div>
          <div class="ticket-train-num">
            <img src="img/depart.svg" alt="">
            TGV 9901
          </div>
          <div class="ticket-route">
            <div class="ticket-city">Paris</div>
            <div class="ticket-arrow-wrap">
              <div class="ticket-arrow-line"></div>
              <div class="ticket-dur">2h 00min</div>
            </div>
            <div class="ticket-city">Nantes</div>
          </div>
        </div>
        <div class="ticket-body">
          <div class="ticket-punch">
            <div class="punch-hole"></div>
            <div class="punch-line"></div>
            <div class="punch-hole"></div>
          </div>
          <div class="ticket-details">
            <div><div class="detail-label">Date</div><div class="detail-value">30 mars 2026</div></div>
            <div><div class="detail-label">Départ</div><div class="detail-value">17:45</div></div>
            <div><div class="detail-label">Voyageur</div><div class="detail-value">J. Dupont</div></div>
            <div><div class="detail-label">Siège</div><div class="detail-value">Voiture 3 · 21B</div></div>
          </div>
          <div class="ticket-footer">
            <span class="ticket-status status-upcoming">● À venir</span>
            <span class="ticket-price">109€</span>
          </div>
        </div>
      </div>


    </div><!-- fin .tickets-scroll -->

    <!-- Flèche droite -->
    <button class="scroll-arrow right" id="arrowRight" aria-label="Défiler à droite">
      <img src="img/arrow_r.svg" alt="→">
    </button>

  </div><!-- fin .tickets-outer -->


  <!-- ════════════════════════════════════
       DÉCONNEXION
  ════════════════════════════════════ -->
  <div class="logout-section">
    <p class="logout-note">Connecté en tant que jean.dupont@email.com</p>
    <button class="btn-logout">
      Déconnexion
    </button>
  </div>


</div><!-- fin .page-content -->


<!-- ════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════ -->
<script>

/* ── Flèches de navigation ── */

const scroll = document.getElementById('ticketsScroll');
const arrowL = document.getElementById('arrowLeft');
const arrowR = document.getElementById('arrowRight');
const STEP   = 310;

arrowL.addEventListener('click', function() {
  scroll.scrollBy({ left: -STEP, behavior: 'smooth' });
});

arrowR.addEventListener('click', function() {
  scroll.scrollBy({ left: STEP, behavior: 'smooth' });
});

/* ── Afficher / masquer selon la position ── */

function updateArrows() {
  const atStart = scroll.scrollLeft <= 10;
  const atEnd   = scroll.scrollLeft + scroll.clientWidth >= scroll.scrollWidth - 10;

  arrowL.style.opacity       = atStart ? '0.35' : '1';
  arrowL.style.pointerEvents = atStart ? 'none'  : 'auto';

  arrowR.style.opacity       = atEnd ? '0.35' : '1';
  arrowR.style.pointerEvents = atEnd ? 'none'  : 'auto';
}

scroll.addEventListener('scroll', updateArrows);
updateArrows();

/* ── Drag-to-scroll ── */

let isDragging     = false;
let startX         = 0;
let startScrollLeft = 0;

scroll.addEventListener('mousedown', function(e) {
  isDragging      = true;
  startX          = e.pageX - scroll.offsetLeft;
  startScrollLeft = scroll.scrollLeft;
  scroll.style.userSelect = 'none';
});

scroll.addEventListener('mouseleave', function() { isDragging = false; });
scroll.addEventListener('mouseup',    function() { isDragging = false; scroll.style.userSelect = ''; });

scroll.addEventListener('mousemove', function(e) {
  if (!isDragging) return;
  e.preventDefault();
  scroll.scrollLeft = startScrollLeft - (e.pageX - scroll.offsetLeft - startX);
});

</script>
</body>
</html>