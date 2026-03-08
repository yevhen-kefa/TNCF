<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TNCF — Résultats</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style/ticket.css">
</head>
<body>


<!-- ════════════════════════════════════════
     TOPBAR  (logo + session + panier + connexion)
════════════════════════════════════════ -->
<div class="topbar">

  <a href="home.html" class="brand">
    <div class="brand-logo">
      <img src="img/logo.svg" alt="TNCF">
    </div>
  </a>

  <div class="topbar-actions">

    <div class="session-timer-top">
      <img src="img/clock.svg" alt="">
      Session expire dans
      <span class="timer-count" id="topTimer">15:00</span>
    </div>

    <a href="#" class="cart-btn">
      <img src="img/box.svg" alt="">
      Panier
      <span class="cart-count" id="cartCount">0</span>
    </a>

    <a href="login.html" class="cart-btn">
      <img src="img/person_white.svg" alt="">
      Connexion
    </a>

  </div>
</div>


<!-- ════════════════════════════════════════
     SEARCH PANEL
════════════════════════════════════════ -->
<div class="search-panel">


  <div class="sp-fields">

    <div class="spf" onclick="this.classList.toggle('active')">
      <span class="spf-icon">
        <img src="img/depart.svg" alt="">
      </span>
      <div class="spf-inner">
        <div class="spf-lbl">Départ</div>
        <div class="spf-val">
          <input type="text" value="Paris (toutes gares intramuros)">
        </div>
      </div>
    </div>

    <div class="spf" onclick="this.classList.toggle('active')">
      <span class="spf-icon">
        <img src="img/arrive.svg" alt="">
      </span>
      <div class="spf-inner">
        <div class="spf-lbl">Arrivée</div>
        <div class="spf-val">
          <input type="text" value="Bruxelles">
        </div>
      </div>
    </div>

    <div class="spf" onclick="this.classList.toggle('active')">

      <div class="spf-inner">
        <div class="spf-lbl">Aller</div>
        <div class="spf-val">
          <input type="date" value="<?= date('Y-m-d') ?>">
        </div>
      </div>
    </div>

    <div class="sp-divider"></div>

    <div class="spf" onclick="this.classList.toggle('active')">
      <div class="spf-inner">
        <div class="spf-lbl">Retour</div>
        <div class="spf-val">
          <input type="date" value="<?= date('Y-m-d', strtotime('+2 day')) ?>">
        </div>
      </div>
    </div>

    <div class="spf narrow" onclick="this.classList.toggle('active')">
      <span class="spf-icon">
        <img src="img/person_white.svg" alt="">
      </span>
      <div class="spf-inner">
        <div class="spf-lbl">Passagers</div>
        <div class="spf-val">× 1</div>
      </div>
    </div>

    <button class="sp-search-btn" title="Lancer la recherche">
      <span class="spf-icon">
        <img src="img/search.svg" alt="">
      </span>
    </button>

  </div><!-- fin sp-fields -->


</div><!-- fin search-panel -->


<!-- ════════════════════════════════════════
     PAGE BODY  (3 colonnes : filtres | résultats | panier)
════════════════════════════════════════ -->
<div class="page-body">


  <!-- ── COLONNE GAUCHE : FILTRES ── -->
  <aside class="filters">

    <div class="filter-header">
      <span class="filter-title">Filtres</span>
      <button class="filter-reset">Réinitialiser</button>
    </div>

    <div class="filter-group">
      <div class="filter-group-title">Prix (€)</div>
      <div class="price-range">
        <input class="price-input" type="number" value="0"   min="0" max="300" style="width: 70px">
        <span style="color: var(--gray)">—</span>
        <input class="price-input" type="number" value="150" min="0" max="300" style="width: 70px">
      </div>
      <input type="range" min="0" max="300" value="150">
    </div>

    <div class="filter-group">
      <div class="filter-group-title">Classe</div>
      <div class="filter-option">
        <label class="filter-option-left">
          <input type="checkbox" checked>
          <span class="filter-option-label">1ère classe</span>
        </label>
        <span class="filter-option-count">8</span>
      </div>
      <div class="filter-option">
        <label class="filter-option-left">
          <input type="checkbox" checked>
          <span class="filter-option-label">2ème classe</span>
        </label>
        <span class="filter-option-count">12</span>
      </div>
    </div>

    <div class="filter-group">
      <div class="filter-group-title">Options</div>
      <div class="filter-option">
        <label class="filter-option-left">
          <input type="checkbox">
          <span class="filter-option-label">Espace silencieux</span>
        </label>
      </div>
      <div class="filter-option">
        <label class="filter-option-left">
          <input type="checkbox">
          <span class="filter-option-label">Prise électrique</span>
        </label>
      </div>
      <div class="filter-option">
        <label class="filter-option-left">
          <input type="checkbox">
          <span class="filter-option-label">Place vélo</span>
        </label>
      </div>
    </div>

  </aside>


  <!-- ── COLONNE CENTRALE : RÉSULTATS ── -->
  <main class="results">

    <div class="results-header">
      <div class="results-count">
        <strong>12 trains</strong> disponibles · Aller: Mer 15 mars
      </div>
      <div class="sort-wrap">
        Trier par
        <select class="sort-select">
          <option>Prix croissant</option>
          <option>Heure de départ</option>
          <option>Durée</option>
        </select>
      </div>
    </div>

    <!-- Calendrier de prix style SNCF -->
    <div class="cal-section">
      <div class="cal-section-label">
        Train
      </div>
      <div class="cal-rule"></div>
      <div class="cal-strip" id="calStrip"></div>
    </div>

    <!-- Liste des trains (générée par JS) -->
    <div id="trainList"></div>

  </main>


  <!-- ── COLONNE DROITE : PANIER ── -->
  <aside class="cart-panel">

    <div class="cart-title">
      Mon panier
      <span id="cartBadge" style="font-size: 0.75rem; color: var(--gray); font-weight: 400">
        0 billet
      </span>
    </div>

    <!-- Timer dans le panier -->
    <div class="session-timer">
      <div>
        <div style="font-size: 0.7rem; color: var(--gray)">Votre session expire dans</div>
        <span class="timer-count" id="sideTimer">15:00</span>
      </div>
    </div>

    <!-- Contenu dynamique du panier -->
    <div id="cartContent">
      <div class="cart-empty">
        <img src="img/box_g.svg" alt="">
        <p>Sélectionnez un train et une classe pour commencer votre réservation</p>
      </div>
    </div>

  </aside>


</div><!-- fin page-body -->


<!-- ════════════════════════════════════════
     MODAL : SESSION EXPIRÉE
════════════════════════════════════════ -->
<div id="sessionModal">
  <div style="background: white; border-radius: 20px; padding: 48px; text-align: center; max-width: 400px;">

    <div style="width: 60px; height: 60px; background: #fef2f2; border-radius: 50%;
                display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
    </div>

    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; margin-bottom: 12px; color: var(--navy)">
      Session expirée
    </h3>
    <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 28px; line-height: 1.6">
      Votre session a expiré pour des raisons de sécurité. Vos sélections ont été conservées.
    </p>
    <button
      onclick="resetTimer()"
      style="padding: 12px 32px; background: var(--navy); color: white; border: none;
             border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 0.9rem;
             font-weight: 600; cursor: pointer;">
      Prolonger la session
    </button>

  </div>
</div>


<!-- ════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════ -->
<script>

/* ── DONNÉES ── */

const trains = [
  { id: 1, num: 'TGV 6601', dep: '06:47', arr: '08:42', dur: '1h 55', direct: true,  p2: 29, p1: 89,  seats2: 42, seats1: 8  },
  { id: 2, num: 'TGV 6603', dep: '08:01', arr: '09:59', dur: '1h 58', direct: true,  p2: 35, p1: 99,  seats2: 24, seats1: 12 },
  { id: 3, num: 'TGV 6607', dep: '10:15', arr: '12:10', dur: '1h 55', direct: true,  p2: 42, p1: 109, seats2: 38, seats1: 16 },
  { id: 4, num: 'TGV 6611', dep: '12:30', arr: '14:25', dur: '1h 55', direct: true,  p2: 29, p1: 89,  seats2: 56, seats1: 20 },
  { id: 5, num: 'TGV 6615', dep: '14:47', arr: '16:50', dur: '2h 03', direct: true,  p2: 55, p1: 139, seats2: 12, seats1: 4  },
  { id: 6, num: 'TGV 6619', dep: '17:01', arr: '19:10', dur: '2h 09', direct: false, p2: 39, p1: 99,  seats2: 30, seats1: 10 },
  { id: 7, num: 'TGV 6623', dep: '19:30', arr: '21:25', dur: '1h 55', direct: true,  p2: 45, p1: 119, seats2: 44, seats1: 18 },
];

const calDays = [
  { dow: 'Dim', date: '08', price: null },
  { dow: 'Lun', date: '09', price: null },
  { dow: 'Mar', date: '10', price: null },
  { dow: 'Mer', date: '11', price: null, active: true },
  { dow: 'Jeu', date: '12', price: null },
  { dow: 'Ven', date: '13', price: 29, cheap: true  },
  { dow: 'Sam', date: '14', price: 39 },
  { dow: 'Dim', date: '15', price: 29, cheap: true  },
  { dow: 'Lun', date: '16', price: 55 },
  { dow: 'Mar', date: '17', price: 42 },
  { dow: 'Mer', date: '18', price: 35, cheap: true  },
  { dow: 'Jeu', date: '19', price: 72 },
];

const options = [
  { id: 'silence', name: 'Zone silencieuse',      desc: 'Wagon calme garanti',    price: 5,  icon: '🔇' },
  { id: 'socket',  name: 'Prise électrique',       desc: '220V à votre siège',     price: 3,  icon: '🔌' },
  { id: 'luggage', name: 'Bagage supplémentaire',  desc: '+1 valise en soute',     price: 12, icon: '🧳' },
  { id: 'sms',     name: 'Alertes SMS',            desc: 'Notifications temps réel', price: 2, icon: '📱' },
  { id: 'cancel',  name: 'Garantie annulation',    desc: 'Remboursement intégral', price: 8,  icon: '🛡️' },
];

/* ── ÉTAT ── */

let selectedTrains  = {};
let selectedOptions = new Set();
let timerSeconds    = 900;
let timerInterval;


/* ════════════════════
   CALENDRIER DE PRIX
════════════════════ */

const calStrip = document.getElementById('calStrip');

calDays.forEach(function(d) {
  const el = document.createElement('div');
  el.className = 'cal-day' + (d.cheap ? ' cheap' : '') + (d.active ? ' active' : '');

  const priceHTML = d.price
    ? '<div class="cal-price' + (d.cheap ? '' : ' cal-price-dash') + '">' + d.price + '€</div>'
    : '<div class="cal-price cal-price-dash">-</div>';

  el.innerHTML = '<div class="cal-dow">' + d.dow + '</div>'
               + '<div class="cal-date">' + d.date + '</div>'
               + priceHTML;

  el.addEventListener('click', function() {
    document.querySelectorAll('.cal-day').forEach(function(c) { c.classList.remove('active'); });
    el.classList.add('active');
  });

  calStrip.appendChild(el);
});

// Centrer le jour actif au chargement
setTimeout(function() {
  const active = calStrip.querySelector('.cal-day.active');
  if (active) active.scrollIntoView({ inline: 'center', behavior: 'smooth' });
}, 20);


/* ════════════════════
   CARTES DE TRAIN
════════════════════ */

const trainList = document.getElementById('trainList');

trains.forEach(function(t) {

  const urgentSeats2 = t.seats2 < 20;
  const urgentSeats1 = t.seats1 < 10;

  const seatsColor2 = urgentSeats2 ? '#e05252' : '#2d9e6b';
  const seatsLabel2 = urgentSeats2 ? '⚠ ' + t.seats2 + ' restantes' : t.seats2 + ' places';

  const seatsColor1 = urgentSeats1 ? '#e05252' : '#2d9e6b';
  const seatsLabel1 = urgentSeats1 ? '⚠ ' + t.seats1 + ' restantes' : t.seats1 + ' places';

  const directColor = t.direct ? '#2d9e6b' : '#f0a500';
  const directLabel = t.direct ? '✓ Direct' : '⚡ 1 correspondance';
  const directTag   = t.direct
    ? '<span class="option-tag" style="color:#2d9e6b">✓ Train direct</span>'
    : '<span class="option-tag" style="color:#f0a500">⚡ Correspondance Lyon</span>';

  const card = document.createElement('div');
  card.className = 'train-card';
  card.id = 'train-' + t.id;

  card.innerHTML = `
    <div class="train-card-main">

      <!-- Badge numéro de train -->
      <div class="train-number">
        <div class="train-label">Train</div>
        <div class="train-num-badge">
          ${t.num}
        </div>
      </div>

      <!-- Timeline départ → arrivée -->
      <div class="train-timeline">
        <div class="train-time">
          <div class="train-hour">${t.dep}</div>
          <div class="train-station">Paris (Gare de Lyon)</div>
        </div>
        <div class="train-line">
          <div class="train-duration">${t.dur}</div>
          <div class="train-track">
            
          </div>
          <div class="train-direct" style="color: ${directColor}">${directLabel}</div>
        </div>
        <div class="train-time">
          <div class="train-hour">${t.arr}</div>
          <div class="train-station">Lyon Part-Dieu</div>
        </div>
      </div>

      <!-- Sélection de classe -->
      <div class="train-classes">
        <div class="class-btn" onclick="selectClass(${t.id}, '2', ${t.p2})">
          <div class="class-label">Classe</div>
          <div class="class-name">2ème</div>
          <div class="class-price">${t.p2}€</div>
          <div class="class-seats" style="color: ${seatsColor2}">${seatsLabel2}</div>
        </div>
        <div class="class-btn" onclick="selectClass(${t.id}, '1', ${t.p1})">
          <div class="class-label">Classe</div>
          <div class="class-name">1ère</div>
          <div class="class-price">${t.p1}€</div>
          <div class="class-seats" style="color: ${seatsColor1}">${seatsLabel1}</div>
        </div>
      </div>

      <!-- Bouton ajout au panier -->
      <button
        class="train-add-btn"
        id="addBtn-${t.id}"
        onclick="addToCart(${t.id})"
        disabled
        style="opacity: 0.4; cursor: not-allowed">
        Ajouter →
      </button>

    </div><!-- fin train-card-main -->

    <!-- Bouton "services inclus" -->
    <button class="expand-toggle" onclick="toggleExpand(${t.id}, this)">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
      Voir les services inclus
    </button>

    <!-- Panneau services (masqué par défaut) -->
    <div class="train-card-expand" id="expand-${t.id}">
      <div class="options-tags">
        <span class="option-tag">🍽️ Restauration à bord</span>
        <span class="option-tag">📶 WiFi gratuit</span>
        <span class="option-tag">🚿 WC disponibles</span>
        <span class="option-tag">♿ Accessibilité PMR</span>
        ${directTag}
      </div>
    </div>
  `;

  trainList.appendChild(card);
});


/* ════════════════════
   SÉLECTION DE CLASSE
════════════════════ */

function selectClass(trainId, cls, price) {

  // Mettre à jour l'apparence des boutons de classe
  const card = document.getElementById('train-' + trainId);
  card.querySelectorAll('.class-btn').forEach(function(btn, i) {
    const isSelected = (i === 0 && cls === '2') || (i === 1 && cls === '1');
    btn.classList.toggle('selected', isSelected);
  });

  // Mémoriser la sélection
  selectedTrains.aller = { trainId: trainId, cls: cls, price: price };

  // Activer le bouton "Ajouter"
  const addBtn = document.getElementById('addBtn-' + trainId);
  addBtn.disabled = false;
  addBtn.style.opacity = '1';
  addBtn.style.cursor  = 'pointer';
}


/* ════════════════════
   AJOUT AU PANIER
════════════════════ */

function addToCart(trainId) {
  if (!selectedTrains.aller || selectedTrains.aller.trainId !== trainId) return;

  const train = trains.find(function(t) { return t.id === trainId; });
  const sel   = selectedTrains.aller;

  // Marquer la carte comme sélectionnée
  document.querySelectorAll('.train-card').forEach(function(c) { c.classList.remove('selected'); });
  document.getElementById('train-' + trainId).classList.add('selected');
  document.getElementById('addBtn-' + trainId).textContent = '✓ Ajouté';

  updateCart(train, sel);
  updateCount();
}


/* ════════════════════
   MISE À JOUR DU PANIER
════════════════════ */

function updateCart(train, sel) {

  const classLabel = sel.cls === '1' ? '1ère' : '2ème';

  const optionsHTML = options.map(function(o) {
    const isSelected = selectedOptions.has(o.id);
    const checkHTML  = isSelected
      ? '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>'
      : '';
    return `
      <div class="option-item ${isSelected ? 'selected' : ''}"
           onclick="toggleOption('${o.id}', ${o.price})"
           id="opt-${o.id}">
        <div class="option-info">
          <div class="option-icon">${o.icon}</div>
          <div>
            <div class="option-name">${o.name}</div>
            <div class="option-desc">${o.desc}</div>
          </div>
        </div>
        <div class="option-right">
          <div class="option-price">+${o.price}€</div>
          <div class="option-check" id="check-${o.id}">${checkHTML}</div>
        </div>
      </div>`;
  }).join('');

  document.getElementById('cartContent').innerHTML = `
    <div class="cart-items">

      <div class="cart-item">
        <div class="cart-item-header">
          <div class="cart-item-route">
            Paris → Lyon
            <span style="font-size: 0.7rem; color: var(--gray); font-weight: 400">(Aller)</span>
          </div>
          <button class="cart-item-remove" onclick="removeCart()">✕</button>
        </div>
        <div class="cart-item-details">
          <div>${train.num} · ${train.dep} → ${train.arr}</div>
          <div>${classLabel} classe · 1 voyageur</div>
          <div style="color: var(--navy); font-weight: 600; margin-top: 4px">${sel.price}€</div>
        </div>
      </div>

      <div class="options-section">
        <div class="options-title">Options à ajouter</div>
        ${optionsHTML}
      </div>

    </div>

    <div class="promo-input-wrap">
      <input class="promo-input" type="text" placeholder="CODE PROMO" id="promoInput">
      <button class="btn-promo" onclick="applyPromo()">Appliquer</button>
    </div>

    <div class="cart-totals" id="cartTotals"></div>

    <button class="btn-checkout">
      Procéder au paiement
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M5 12h14M12 5l7 7-7 7"/>
      </svg>
    </button>
  `;

  computeTotal(sel.price);
}


/* ════════════════════
   OPTIONS
════════════════════ */

function toggleOption(id, price) {
  if (selectedOptions.has(id)) {
    selectedOptions.delete(id);
  } else {
    selectedOptions.add(id);
  }

  const el  = document.getElementById('opt-' + id);
  const chk = document.getElementById('check-' + id);

  el.classList.toggle('selected');

  if (selectedOptions.has(id)) {
    chk.innerHTML    = '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';
    chk.style.background   = 'var(--navy)';
    chk.style.borderColor  = 'var(--navy)';
  } else {
    chk.innerHTML          = '';
    chk.style.background   = '';
    chk.style.borderColor  = '';
  }

  if (selectedTrains.aller) {
    computeTotal(selectedTrains.aller.price);
  }
}


/* ════════════════════
   CALCUL DU TOTAL
════════════════════ */

function computeTotal(basePrice) {
  let optTotal = 0;
  options.forEach(function(o) {
    if (selectedOptions.has(o.id)) optTotal += o.price;
  });

  const total = basePrice + optTotal;

  document.getElementById('cartTotals').innerHTML =
      '<div class="total-row"><span>Billet</span><span>' + basePrice + '€</span></div>'
    + (optTotal > 0 ? '<div class="total-row"><span>Options</span><span>+' + optTotal + '€</span></div>' : '')
    + '<div class="total-row main"><span>Total</span><span>' + total + '€</span></div>';
}


/* ════════════════════
   SUPPRESSION DU PANIER
════════════════════ */

function removeCart() {
  selectedTrains  = {};
  selectedOptions.clear();

  document.querySelectorAll('.train-card').forEach(function(c) { c.classList.remove('selected'); });
  document.querySelectorAll('.train-add-btn').forEach(function(b) {
    b.textContent  = 'Ajouter →';
    b.disabled     = true;
    b.style.opacity = '0.4';
  });

  document.getElementById('cartContent').innerHTML = `
    <div class="cart-empty">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
        <line x1="3" y1="6" x2="21" y2="6"/>
        <path d="M16 10a4 4 0 0 1-8 0"/>
      </svg>
      <p>Sélectionnez un train et une classe pour commencer votre réservation</p>
    </div>`;

  updateCount();
}


/* ════════════════════
   CODE PROMO
════════════════════ */

function applyPromo() {
  const code = document.getElementById('promoInput').value.toUpperCase();
  if (code === 'TNCF20') {
    alert('Code TNCF20 appliqué ! -20% sur votre commande.');
  } else {
    alert('Code invalide. Réessayez.');
  }
}


/* ════════════════════
   COMPTEUR PANIER
════════════════════ */

function updateCount() {
  const n = Object.keys(selectedTrains).length;
  document.getElementById('cartCount').textContent  = n;
  document.getElementById('cartBadge').textContent  = n + ' billet' + (n > 1 ? 's' : '');
}


/* ════════════════════
   EXPAND "SERVICES INCLUS"
════════════════════ */

function toggleExpand(id, btn) {
  const panel = document.getElementById('expand-' + id);
  panel.classList.toggle('open');
  btn.querySelector('svg').style.transform = panel.classList.contains('open') ? 'rotate(180deg)' : '';
}


/* ════════════════════
   TIMER DE SESSION
════════════════════ */

function formatTime(seconds) {
  const m = Math.floor(seconds / 60).toString().padStart(2, '0');
  const s = (seconds % 60).toString().padStart(2, '0');
  return m + ':' + s;
}

function startTimer() {
  timerSeconds = 900;
  clearInterval(timerInterval);

  timerInterval = setInterval(function() {
    timerSeconds--;

    const timeStr  = formatTime(timerSeconds);
    const isUrgent = timerSeconds <= 30;
    const className = 'timer-count' + (isUrgent ? ' urgent' : '');

    document.getElementById('topTimer').textContent  = timeStr;
    document.getElementById('sideTimer').textContent = timeStr;
    document.getElementById('topTimer').className    = className;
    document.getElementById('sideTimer').className   = className;

    if (timerSeconds <= 0) {
      clearInterval(timerInterval);
      document.getElementById('sessionModal').style.display = 'flex';
    }

  }, 1000);
}

function resetTimer() {
  document.getElementById('sessionModal').style.display = 'none';
  startTimer();
}

// Réinitialiser le timer sur toute activité utilisateur
['click', 'keydown', 'mousemove'].forEach(function(event) {
  document.addEventListener(event, function() {
    if (timerSeconds > 0) timerSeconds = 900;
  });
});

startTimer();

</script>
</body>
</html>