<?php
session_start();
require_once __DIR__ . '/../src/Config/Database.php';
use Config\Database;

try {
    $db = new Database();
    $manager = $db->getManager();
    
    // 1. Get real data from MongoDB
    $query = new \MongoDB\Driver\Query([]);
    $cursor = $manager->executeQuery($db->getDbName() . '.voyages', $query);
    $realVoyages = $cursor->toArray();
    
    // 2. Mock data for "mass" effect (from your script)
    // We format it to match MongoDB object structure for the loop
    $mockVoyages = [
        (object)['_id' => 'm1', 'depart' => 'Paris', 'arriver' => 'Lyon', 'date_depart' => '06:47', 'temps_arriver' => '1h 55', 'prix' => 29, 'num' => 'TGV 6601'],
        (object)['_id' => 'm2', 'depart' => 'Paris', 'arriver' => 'Lyon', 'date_depart' => '08:01', 'temps_arriver' => '1h 58', 'prix' => 35, 'num' => 'TGV 6603'],
        (object)['_id' => 'm3', 'depart' => 'Paris', 'arriver' => 'Lyon', 'date_depart' => '10:15', 'temps_arriver' => '1h 55', 'prix' => 42, 'num' => 'TGV 6607'],
        (object)['_id' => 'm4', 'depart' => 'Paris', 'arriver' => 'Lyon', 'date_depart' => '12:30', 'temps_arriver' => '1h 55', 'prix' => 29, 'num' => 'TGV 6611'],
        (object)['_id' => 'm5', 'depart' => 'Paris', 'arriver' => 'Lyon', 'date_depart' => '14:47', 'temps_arriver' => '2h 03', 'prix' => 55, 'num' => 'TGV 6615']
    ];

    // 3. Merge both lists
    $allVoyages = array_merge($realVoyages, $mockVoyages);
    
} catch (\Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

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
  <div class="results" id="trainList">
    <div class="results-header">
      <div class="results-count">
        <strong><?= count($allVoyages) ?></strong> trajets trouvés
      </div>
      <div class="sort-wrap">
        Trier par :
        <select class="sort-select">
          <option>Prix croissant</option>
          <option>Départ le plus tôt</option>
        </select>
      </div>
    </div>

    <?php foreach ($allVoyages as $v): ?>
      <?php 
        // Helper to display train number
        $trainNum = isset($v->num) ? $v->num : "TGV INOUI № " . substr((string)$v->_id, -4);
      ?>
      <div class="train-card" id="train-<?= (string)$v->_id ?>">
        <div class="train-card-main">
          <div class="train-number">
            <div class="train-label">Train</div>
            <div class="train-num-badge"><?= $trainNum ?></div>
          </div>

          <div class="train-timeline">
            <div class="train-time">
              <div class="train-hour"><?= $v->date_depart ?></div>
              <div class="train-station"><?= ucfirst($v->depart) ?></div>
            </div>

            <div class="train-line">
              <div class="train-duration"><?= $v->temps_arriver ?></div>
              <div class="train-track"></div>
              <div class="train-direct" style="color: #2d9e6b">✓ Direct</div>
            </div>

            <div class="train-time">
              <div class="train-hour">Arrivée</div>
              <div class="train-station"><?= ucfirst($v->arriver) ?></div>
            </div>
          </div>

          <div class="train-classes">
            <div class="class-btn" onclick="selectClass('<?= (string)$v->_id ?>', '2', <?= $v->prix ?>)">
              <div class="class-label">Classe</div>
              <div class="class-name">2ème</div>
              <div class="class-price"><?= $v->prix ?>€</div>
              <div class="class-seats" style="color: #2d9e6b">Places dispo.</div>
            </div>
          </div>

          <button class="train-add-btn" id="addBtn-<?= (string)$v->_id ?>" onclick="addToCart('<?= (string)$v->_id ?>')" disabled style="opacity: 0.4; cursor: not-allowed">
            Ajouter →
          </button>
        </div>

        <button class="expand-toggle" onclick="toggleExpand('<?= (string)$v->_id ?>', this)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"/>
          </svg>
          Voir les services inclus
        </button>

        <div class="train-card-expand" id="expand-<?= (string)$v->_id ?>">
          <div class="options-tags">
            <span class="option-tag">🍽️ Restauration à bord</span>
            <span class="option-tag">📶 WiFi gratuit</span>
            <span class="option-tag">🚿 WC disponibles</span>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>


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

  /* ── СТАН ── */
  let selectedTrains  = {};
  let selectedOptions = new Set();
  let timerSeconds    = 900;
  let timerInterval;

  const options = [
    { id: 'silence', name: 'Zone silencieuse',      desc: 'Wagon calme garanti',      price: 5,  icon: '🔇' },
    { id: 'socket',  name: 'Prise électrique',       desc: '220V à votre siège',       price: 3,  icon: '🔌' },
    { id: 'luggage', name: 'Bagage supplémentaire',  desc: '+1 valise en soute',       price: 12, icon: '🧳' },
    { id: 'sms',     name: 'Alertes SMS',            desc: 'Notifications temps réel', price: 2,  icon: '📱' },
    { id: 'cancel',  name: 'Garantie annulation',    desc: 'Remboursement intégral',   price: 8,  icon: '🛡️' },
  ];


  /* ════════════════════
    SÉLECTION DE CLASSE
  ════════════════════ */

  function selectClass(trainId, cls, price) {
    const card = document.getElementById('train-' + trainId);

    card.querySelectorAll('.class-btn').forEach(function(btn) {
      btn.classList.remove('selected');
    });

    // Знаходимо натиснуту кнопку по класу
    card.querySelectorAll('.class-btn').forEach(function(btn) {
      if (btn.getAttribute('data-class') === cls) {
        btn.classList.add('selected');
      }
    });

    selectedTrains.aller = { trainId: trainId, cls: cls, price: price };

    const addBtn = document.getElementById('addBtn-' + trainId);
    if (addBtn) {
      addBtn.disabled      = false;
      addBtn.style.opacity = '1';
      addBtn.style.cursor  = 'pointer';
    }
  }


  /* ════════════════════
    AJOUT AU PANIER
  ════════════════════ */

  function addToCart(trainId) {
    if (!selectedTrains.aller || selectedTrains.aller.trainId !== trainId) return;

    const sel  = selectedTrains.aller;
    const card = document.getElementById('train-' + trainId);

    // Récupère les infos depuis le DOM (généré par PHP)
    const num     = card.querySelector('.train-num-badge')  ? card.querySelector('.train-num-badge').textContent.trim()  : '';
    const dep     = card.querySelectorAll('.train-hour')[0] ? card.querySelectorAll('.train-hour')[0].textContent.trim() : '';
    const arr     = card.querySelectorAll('.train-hour')[1] ? card.querySelectorAll('.train-hour')[1].textContent.trim() : 'Arrivée';
    const from    = card.querySelectorAll('.train-station')[0] ? card.querySelectorAll('.train-station')[0].textContent.trim() : '';
    const to      = card.querySelectorAll('.train-station')[1] ? card.querySelectorAll('.train-station')[1].textContent.trim() : '';

    document.querySelectorAll('.train-card').forEach(function(c) { c.classList.remove('selected'); });
    card.classList.add('selected');

    const addBtn = document.getElementById('addBtn-' + trainId);
    if (addBtn) addBtn.textContent = '✓ Ajouté';

    updateCart({ num, dep, arr, from, to }, sel);
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
              ${train.from} → ${train.to}
              <span style="font-size:0.7rem;color:var(--gray);font-weight:400">(Aller)</span>
            </div>
            <button class="cart-item-remove" onclick="removeCart()">✕</button>
          </div>
          <div class="cart-item-details">
            <div>${train.num} · ${train.dep} → ${train.arr}</div>
            <div>${classLabel} classe · 1 voyageur</div>
            <div style="color:var(--navy);font-weight:600;margin-top:4px">${sel.price}€</div>
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
      chk.innerHTML         = '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';
      chk.style.background  = 'var(--navy)';
      chk.style.borderColor = 'var(--navy)';
    } else {
      chk.innerHTML         = '';
      chk.style.background  = '';
      chk.style.borderColor = '';
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
      b.textContent   = 'Ajouter →';
      b.disabled      = true;
      b.style.opacity = '0.4';
    });

    document.getElementById('cartContent').innerHTML = `
      <div class="cart-empty">
        <img src="img/box_g.svg" alt="">
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
    document.getElementById('cartCount').textContent = n;
    document.getElementById('cartBadge').textContent = n + ' billet' + (n > 1 ? 's' : '');
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

      const timeStr   = formatTime(timerSeconds);
      const isUrgent  = timerSeconds <= 30;
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

  ['click', 'keydown', 'mousemove'].forEach(function(event) {
    document.addEventListener(event, function() {
      if (timerSeconds > 0) timerSeconds = 900;
    });
  });

  startTimer();

</script>
</body>
</html>