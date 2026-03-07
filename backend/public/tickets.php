<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TNCF — Résultats</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/ticket.css">
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <a href="home.html" class="brand">
    <div class="brand-logo">
      <img src="img/logo.svg" alt="">
    </div>
    <div class="brand-name">TNCF</div>
  </a>

  <div class="topbar-actions">
    <div class="session-timer">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <span style="font-size:0.75rem;color:rgba(255,255,255,0.5)">Session expire dans</span>
      <span class="timer-count" id="topTimer" style="color:var(--gold-light)">03:00</span>
    </div>
    <a href="#" class="cart-btn" id="cartBtn">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      Panier
      <span class="cart-count" id="cartCount">0</span>
    </a>
    <a href="login.html" class="cart-btn">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Connexion
    </a>
  </div>
</div>

<!-- SEARCH STRIP -->
<div class="search-strip">
  <div class="strip-route">
    <em>Paris</em>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" opacity="0.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    <em>Lyon</em>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" opacity="0.5" style="margin:0 2px"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    <em>Paris</em>
  </div>
  <div class="strip-meta">
    <span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Mer 15 mars
    </span>
    <span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Sam 18 mars
    </span>
    <span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      1 voyageur
    </span>
  </div>
  <button class="strip-edit" onclick="window.location.href='home.html'">✏ Modifier</button>
</div>

<!-- PAGE BODY -->
<div class="page-body">

  <!-- FILTERS -->
  <aside class="filters">
    <div class="filter-header">
      <span class="filter-title">Filtres</span>
      <button class="filter-reset">Réinitialiser</button>
    </div>

    <div class="filter-group">
      <div class="filter-group-title">Prix (€)</div>
      <div class="price-range">
        <input class="price-input" type="number" value="0" min="0" max="300" style="width:70px">
        <span style="color:var(--gray)">—</span>
        <input class="price-input" type="number" value="150" min="0" max="300" style="width:70px">
      </div>
      <input type="range" min="0" max="300" value="150">
    </div>

    <div class="filter-group">
      <div class="filter-group-title">Heure de départ</div>
      <div class="time-buttons">
        <div class="time-btn">🌅 Matin<br><small>05h–12h</small></div>
        <div class="time-btn active">☀ Après-midi<br><small>12h–18h</small></div>
        <div class="time-btn">🌆 Soir<br><small>18h–22h</small></div>
        <div class="time-btn">🌙 Nuit<br><small>22h–05h</small></div>
      </div>
    </div>

    <div class="filter-group">
      <div class="filter-group-title">Durée</div>
      <input type="range" min="60" max="360" value="240" style="margin-bottom:8px">
      <div style="font-size:0.8rem;color:var(--gray)">Jusqu'à 4h00</div>
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

  <!-- RESULTS -->
  <main class="results">
    <div class="results-header">
      <div class="results-count"><strong>12 trains</strong> disponibles · Aller: Mer 15 mars</div>
      <div class="sort-wrap">
        Trier par
        <select class="sort-select">
          <option>Prix croissant</option>
          <option>Heure de départ</option>
          <option>Durée</option>
        </select>
      </div>
    </div>

    <!-- Price calendar -->
    <div class="cal-strip" id="calStrip"></div>

    <!-- Train cards -->
    <div id="trainList"></div>
  </main>

  <!-- CART PANEL -->
  <aside class="cart-panel">
    <div class="cart-title">
      Mon panier
      <span id="cartBadge" style="font-size:0.75rem;color:var(--gray);font-weight:400">0 billet</span>
    </div>

    <div class="session-timer">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <div>
        <div style="font-size:0.7rem;color:var(--gray)">Votre session expire dans</div>
        <span class="timer-count" id="sideTimer">03:00</span>
      </div>
    </div>

    <div id="cartContent">
      <div class="cart-empty">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        <p>Sélectionnez un train et une classe pour commencer votre réservation</p>
      </div>
    </div>
  </aside>
</div>

<!-- Session expired modal -->
<div id="sessionModal" style="display:none;position:fixed;inset:0;background:rgba(10,22,40,0.85);z-index:9999;display:none;align-items:center;justify-content:center">
  <div style="background:white;border-radius:20px;padding:48px;text-align:center;max-width:400px">
    <div style="width:60px;height:60px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#e05252" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <h3 style="font-family:'Playfair Display',serif;font-size:1.4rem;margin-bottom:12px;color:var(--navy)">Session expirée</h3>
    <p style="color:var(--gray);font-size:0.9rem;margin-bottom:28px;line-height:1.6">Votre session a expiré pour des raisons de sécurité. Vos sélections ont été conservées.</p>
    <button onclick="resetTimer()" style="padding:12px 32px;background:var(--navy);color:white;border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:0.9rem;font-weight:600;cursor:pointer">Prolonger la session</button>
  </div>
</div>

<script>
// ── DATA ──
const trains = [
  { id:1, num:'TGV 6601', dep:'06:47', arr:'08:42', dur:'1h 55', direct:true, p2:29, p1:89, seats2:42, seats1:8 },
  { id:2, num:'TGV 6603', dep:'08:01', arr:'09:59', dur:'1h 58', direct:true, p2:35, p1:99, seats2:24, seats1:12 },
  { id:3, num:'TGV 6607', dep:'10:15', arr:'12:10', dur:'1h 55', direct:true, p2:42, p1:109, seats2:38, seats1:16 },
  { id:4, num:'TGV 6611', dep:'12:30', arr:'14:25', dur:'1h 55', direct:true, p2:29, p1:89, seats2:56, seats1:20 },
  { id:5, num:'TGV 6615', dep:'14:47', arr:'16:50', dur:'2h 03', direct:true, p2:55, p1:139, seats2:12, seats1:4 },
  { id:6, num:'TGV 6619', dep:'17:01', arr:'19:10', dur:'2h 09', direct:false, p2:39, p1:99, seats2:30, seats1:10 },
  { id:7, num:'TGV 6623', dep:'19:30', arr:'21:25', dur:'1h 55', direct:true, p2:45, p1:119, seats2:44, seats1:18 },
];

const calDays = [
  {dow:'Lun', date:'11 mars', price:49, cheap:false},
  {dow:'Mar', date:'12 mars', price:39, cheap:true},
  {dow:'Mer', date:'13 mars', price:29, cheap:true},
  {dow:'Jeu', date:'14 mars', price:55, cheap:false},
  {dow:'Ven', date:'15 mars', price:29, cheap:true, active:true},
  {dow:'Sam', date:'16 mars', price:72, cheap:false},
  {dow:'Dim', date:'17 mars', price:65, cheap:false},
  {dow:'Lun', date:'18 mars', price:35, cheap:true},
  {dow:'Mar', date:'19 mars', price:42, cheap:false},
];

const options = [
  { id:'silence', name:'Zone silencieuse', desc:'Wagon calme garanti', price:5, icon:'🔇' },
  { id:'socket',  name:'Prise électrique', desc:'220V à votre siège', price:3, icon:'🔌' },
  { id:'luggage', name:'Bagage supplémentaire', desc:'+1 valise en soute', price:12, icon:'🧳' },
  { id:'sms',     name:'Alertes SMS', desc:'Notifications temps réel', price:2, icon:'📱' },
  { id:'cancel',  name:'Garantie annulation', desc:'Remboursement intégral', price:8, icon:'🛡️' },
];

let selectedTrains = {}; // { aller: {train, class}, retour: {train, class} }
let selectedOptions = new Set();
let timerSeconds = 180;
let timerInterval;

// ── CALENDAR ──
const calStrip = document.getElementById('calStrip');
calDays.forEach(d => {
  const el = document.createElement('div');
  el.className = `cal-day ${d.cheap ? 'cheap' : ''} ${d.active ? 'active' : ''}`;
  el.innerHTML = `
    <div class="cal-dow">${d.dow}</div>
    <div class="cal-date">${d.date}</div>
    <div class="cal-price ${d.cheap ? '' : 'cal-price-normal'}">${d.price}€</div>
  `;
  el.addEventListener('click', () => {
    document.querySelectorAll('.cal-day').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
  });
  calStrip.appendChild(el);
});

// ── TRAINS ──
const trainList = document.getElementById('trainList');
trains.forEach(t => {
  const card = document.createElement('div');
  card.className = 'train-card';
  card.id = `train-${t.id}`;

  const urgentSeats2 = t.seats2 < 20;
  const urgentSeats1 = t.seats1 < 10;

  card.innerHTML = `
    <div class="train-card-main">
      <div class="train-number">
        <div class="train-label">Train</div>
        <div class="train-num-badge">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 20h24M4 14h20l-3-8H7L4 14z"/></svg>
          ${t.num}
        </div>
      </div>

      <div class="train-timeline">
        <div class="train-time">
          <div class="train-hour">${t.dep}</div>
          <div class="train-station">Paris (Gare de Lyon)</div>
        </div>
        <div class="train-line">
          <div class="train-duration">${t.dur}</div>
          <div class="train-track">
            <span class="train-track-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </span>
          </div>
          <div class="train-direct" style="color:${t.direct ? '#2d9e6b' : '#f0a500'}">
            ${t.direct ? '✓ Direct' : '⚡ 1 correspondance'}
          </div>
        </div>
        <div class="train-time">
          <div class="train-hour">${t.arr}</div>
          <div class="train-station">Lyon Part-Dieu</div>
        </div>
      </div>

      <div class="train-classes">
        <div class="class-btn" onclick="selectClass(${t.id}, '2', ${t.p2})">
          <div class="class-label">Classe</div>
          <div class="class-name">2ème</div>
          <div class="class-price">${t.p2}€</div>
          <div class="class-seats" style="color:${urgentSeats2 ? '#e05252' : '#2d9e6b'}">${urgentSeats2 ? '⚠ '+t.seats2+' restantes' : t.seats2+' places'}</div>
        </div>
        <div class="class-btn" onclick="selectClass(${t.id}, '1', ${t.p1})">
          <div class="class-label">Classe</div>
          <div class="class-name">1ère</div>
          <div class="class-price">${t.p1}€</div>
          <div class="class-seats" style="color:${urgentSeats1 ? '#e05252' : '#2d9e6b'}">${urgentSeats1 ? '⚠ '+t.seats1+' restantes' : t.seats1+' places'}</div>
        </div>
      </div>

      <button class="train-add-btn" id="addBtn-${t.id}" onclick="addToCart(${t.id})" disabled style="opacity:0.4;cursor:not-allowed">
        Ajouter →
      </button>
    </div>

    <button class="expand-toggle" onclick="toggleExpand(${t.id}, this)">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
      Voir les services inclus
    </button>

    <div class="train-card-expand" id="expand-${t.id}">
      <div class="options-tags">
        <span class="option-tag">🍽️ Restauration à bord</span>
        <span class="option-tag">📶 WiFi gratuit</span>
        <span class="option-tag">🚿 WC disponibles</span>
        <span class="option-tag">♿ Accessibilité PMR</span>
        ${t.direct ? '<span class="option-tag" style="color:#2d9e6b">✓ Train direct</span>' : '<span class="option-tag" style="color:#f0a500">⚡ Correspondance Lyon</span>'}
      </div>
    </div>
  `;
  trainList.appendChild(card);
});

function selectClass(trainId, cls, price) {
  // Update visual
  const card = document.getElementById(`train-${trainId}`);
  card.querySelectorAll('.class-btn').forEach((btn, i) => {
    btn.classList.toggle('selected', (i === 0 && cls === '2') || (i === 1 && cls === '1'));
  });

  // Store selection (for "aller" for now)
  selectedTrains.aller = { trainId, cls, price };

  // Enable add button
  const addBtn = document.getElementById(`addBtn-${trainId}`);
  addBtn.disabled = false;
  addBtn.style.opacity = '1';
  addBtn.style.cursor = 'pointer';
}

function addToCart(trainId) {
  if (!selectedTrains.aller || selectedTrains.aller.trainId !== trainId) return;
  const train = trains.find(t => t.id === trainId);
  const sel = selectedTrains.aller;

  // Mark card
  document.querySelectorAll('.train-card').forEach(c => c.classList.remove('selected'));
  document.getElementById(`train-${trainId}`).classList.add('selected');
  document.getElementById(`addBtn-${trainId}`).textContent = '✓ Ajouté';

  updateCart(train, sel);
  updateCount();
}

function updateCart(train, sel) {
  const content = document.getElementById('cartContent');
  content.innerHTML = `
    <div class="cart-items">
      <div class="cart-item">
        <div class="cart-item-header">
          <div class="cart-item-route">
            Paris → Lyon
            <span style="font-size:0.7rem;color:var(--gray);font-weight:400">(Aller)</span>
          </div>
          <button class="cart-item-remove" onclick="removeCart()">✕</button>
        </div>
        <div class="cart-item-details">
          <div>${train.num} · ${train.dep} → ${train.arr}</div>
          <div>${sel.cls === '1' ? '1ère' : '2ème'} classe · 1 voyageur</div>
          <div style="color:var(--navy);font-weight:600;margin-top:4px">${sel.price}€</div>
        </div>
      </div>

      <div class="options-section">
        <div class="options-title">Options à ajouter</div>
        ${options.map(o => `
          <div class="option-item ${selectedOptions.has(o.id) ? 'selected' : ''}" onclick="toggleOption('${o.id}', ${o.price})" id="opt-${o.id}">
            <div class="option-info">
              <div class="option-icon">${o.icon}</div>
              <div>
                <div class="option-name">${o.name}</div>
                <div class="option-desc">${o.desc}</div>
              </div>
            </div>
            <div class="option-right">
              <div class="option-price">+${o.price}€</div>
              <div class="option-check" id="check-${o.id}">
                ${selectedOptions.has(o.id) ? '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>' : ''}
              </div>
            </div>
          </div>
        `).join('')}
      </div>
    </div>

    <div class="promo-input-wrap">
      <input class="promo-input" type="text" placeholder="CODE PROMO" id="promoInput">
      <button class="btn-promo" onclick="applyPromo()">Appliquer</button>
    </div>

    <div class="cart-totals" id="cartTotals">
    </div>

    <button class="btn-checkout">
      Procéder au paiement
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </button>
  `;
  computeTotal(sel.price);
}

function toggleOption(id, price) {
  if (selectedOptions.has(id)) {
    selectedOptions.delete(id);
  } else {
    selectedOptions.add(id);
  }

  const el = document.getElementById(`opt-${id}`);
  el.classList.toggle('selected');
  const check = document.getElementById(`check-${id}`);
  if (selectedOptions.has(id)) {
    el.querySelector('.option-check').innerHTML = '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';
    el.querySelector('.option-check').style.background = 'var(--navy)';
    el.querySelector('.option-check').style.borderColor = 'var(--navy)';
  } else {
    el.querySelector('.option-check').innerHTML = '';
    el.querySelector('.option-check').style.background = '';
    el.querySelector('.option-check').style.borderColor = '';
  }

  if (selectedTrains.aller) computeTotal(selectedTrains.aller.price);
}

function computeTotal(basePrice) {
  let optTotal = 0;
  options.forEach(o => { if (selectedOptions.has(o.id)) optTotal += o.price; });
  const total = basePrice + optTotal;

  document.getElementById('cartTotals').innerHTML = `
    <div class="total-row"><span>Billet</span><span>${basePrice}€</span></div>
    ${optTotal > 0 ? `<div class="total-row"><span>Options</span><span>+${optTotal}€</span></div>` : ''}
    <div class="total-row main"><span>Total</span><span>${total}€</span></div>
  `;
}

function removeCart() {
  selectedTrains = {};
  selectedOptions.clear();
  document.querySelectorAll('.train-card').forEach(c => c.classList.remove('selected'));
  document.querySelectorAll('.train-add-btn').forEach(b => {
    b.textContent = 'Ajouter →'; b.disabled = true; b.style.opacity = '0.4';
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

function applyPromo() {
  const code = document.getElementById('promoInput').value.toUpperCase();
  if (code === 'TNCF20') {
    alert('✅ Code TNCF20 appliqué ! -20% sur votre commande.');
  } else {
    alert('❌ Code invalide. Réessayez.');
  }
}

function updateCount() {
  const n = Object.keys(selectedTrains).length;
  document.getElementById('cartCount').textContent = n;
  document.getElementById('cartBadge').textContent = n + ' billet' + (n > 1 ? 's' : '');
}

function toggleExpand(id, btn) {
  const panel = document.getElementById(`expand-${id}`);
  panel.classList.toggle('open');
  btn.querySelector('svg').style.transform = panel.classList.contains('open') ? 'rotate(180deg)' : '';
}

// ── TIMER ──
function formatTime(s) {
  const m = Math.floor(s/60).toString().padStart(2,'0');
  const sec = (s%60).toString().padStart(2,'0');
  return `${m}:${sec}`;
}

function startTimer() {
  timerSeconds = 180;
  clearInterval(timerInterval);
  timerInterval = setInterval(() => {
    timerSeconds--;
    const str = formatTime(timerSeconds);
    document.getElementById('topTimer').textContent = str;
    document.getElementById('sideTimer').textContent = str;

    const isUrgent = timerSeconds <= 30;
    document.getElementById('sideTimer').className = 'timer-count' + (isUrgent ? ' urgent' : '');

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

// Reset timer on activity
['click','keydown','mousemove'].forEach(e => {
  document.addEventListener(e, () => {
    if (timerSeconds > 0) { timerSeconds = 180; }
  });
});

startTimer();
</script>
</body>
</html>
