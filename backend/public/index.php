<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TNCF — Accueil</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/index.css">
</head>
<body>

<!-- NAV -->
<nav id="navbar">
  <a href="index.php" class="brand">
    <div class="brand-logo">
      <img src="img/logo.svg" alt="" >
    </div>
  </a>

  <ul class="nav-links">
    <li><a href="index.php" class="nav-link active">Voyager</a></li>
    <li><a href="#">Billets</a></li>
    <li><a href="#">Offres</a></li>
    <li><a href="#">Compte</a></li>
  </ul>

  <div class="nav-actions">
    <a href="login.php" class="btn-nav-outline">Se connecter</a>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg-grid"></div>
  <div class="hero-glow"></div>
  <div class="hero-rail"></div>

  <!-- animated mini-train -->
  <div class="hero-train">
    <img src="./img/train/train.svg" alt="TGV Train" style="height: 150px; width: auto;">
  </div>

  <div class="hero-content">
    <div class="hero-eyebrow">Voyages Grande Vitesse</div>
    <h1>La France à<br>grande <em>vitesse</em></h1>
    <p>Trouvez et réservez vos billets TGV au meilleur prix. Plus de 200 destinations, des départs chaque heure.</p>
  </div>

  <!-- SEARCH BOX -->
  <div class="search-box">
    <div class="search-tabs">
      <button class="search-tab active">Aller simple</button>
      <button class="search-tab">Aller-retour</button>
    </div>

    <div class="search-fields">
      <div class="search-field" style="position:relative">
        <label>Départ</label>
        <div class="search-field-inner">
          <span class="search-icon">
            <img src="./img/depart.svg" alt="depart image">
          </span>
          <input type="text" placeholder="Ville ou gare" value="Paris (Gare de Lyon)">
        </div>
        <button class="swap-btn" onclick="swapCities()">
          <img src="img/switch.svg" alt="">
        </button>
      </div>

      <div class="search-field">
        <label>Arrivée</label>
        <div class="search-field-inner">
          <span class="search-icon">
            <img src="img/arrive.svg" alt="arrive image" style="height: 17px;">
          </span>
          <input type="text" placeholder="Ville ou gare" value="Lyon Part-Dieu">
        </div>
      </div>

      <div class="search-field">
        <label>Départ</label>
        <div class="search-field-inner">
          <input type="date" value="<?= date('Y-m-d') ?>">
        </div>
      </div>

      <div class="search-field" id="retour-field">
        <label>Retour</label>
        <div class="search-field-inner">
          <input type="date" value="<?= date('Y-m-d', strtotime('+2 day')) ?>">
        </div>
      </div>

      <div>
        <button class="btn-search" onclick="window.location.href='tickets.php'">
          <img src="img/search.svg" alt="">
          Rechercher
        </button>
      </div>
    </div>
  </div>
</section>

<!-- POPULAR ROUTES -->
<section class="section">
  <div class="section-header">
    <div class="section-eyebrow">Destinations populaires</div>
    <h2 class="section-title">Les trajets les plus<br>empruntés</h2>
    <p class="section-sub">Découvrez nos meilleures liaisons TGV à prix réduits</p>
  </div>

  <div class="routes-grid">
    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-paris"></div>
        <div class="route-img-overlay"></div>   
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Paris</span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-marseille"></div>
        <div class="route-img-overlay"></div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Marseille</span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-bordeaux"></div>
        <div class="route-img-overlay"></div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Bordeaux</span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-nice"></div>
        <div class="route-img-overlay"></div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Nice</span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-lille"></div>
        <div class="route-img-overlay"></div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Lille</span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-lyon"></div>
        <div class="route-img-overlay"></div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Lyon</span>
        </div>
      </div>
    </a>
    
    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-lyon"></div>
        <div class="route-img-overlay"></div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Rennes</span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-lyon"></div>
        <div class="route-img-overlay"></div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Caen</span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-lyon"></div>
        <div class="route-img-overlay"></div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Strasbourg</span>
        </div>
      </div>
    </a>


  </div>
</section>

<!-- FEATURES -->
<section class="section section-alt">
  <div class="section-header">
    <div class="section-eyebrow">Pourquoi TNCF</div>
    <h2 class="section-title">Le confort du voyage<br>à chaque étape</h2>
  </div>

  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon">
        <img src="img/shield.svg" alt="" style="height: 30px;">
      </div>
      <div class="feature-title">Garantie Annulation</div>
      <p class="feature-text">Annulez jusqu'à 24 heure avant le départ et soyez remboursé intégralement.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">
          <img src="img/cart.svg" alt="">
      </div>
      <div class="feature-title">Paiement Sécurisé</div>
      <p class="feature-text">Toutes vos transactions sont protégées.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">
        <img src="img/alert.svg" alt="">
      </div>
      <div class="feature-title">Alertes Email</div>
      <p class="feature-text">Restez informé de l'état de votre train en temps réel par Email.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">
        <img src="img/ticket.svg" alt="" style="height: 20px;">
      </div>
      <div class="feature-title">Billet Mobile</div>
      <p class="feature-text">Votre billet directement sur votre smartphone — sans impression requise.</p>
    </div>
  </div>
</section>

<!-- PROMO BANNER -->
<section class="section">
  <div class="promo-banner">
    <div class="promo-text">
      <div class="promo-eyebrow">Offre limitée — Semaine du voyageur</div>
      <div class="promo-title">Jusqu'à -40% sur<br>les billets Weekend</div>
      <p class="promo-sub">Valable pour les voyages du vendredi au dimanche. Offre valable jusqu'au 31 mars 2026.</p>
    </div>
    <div class="promo-action">
      <a href="tickets.html" class="btn-promo">Profiter de l'offre →</a>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-top">
    <div class="footer-brand">
      <span class="brand-name" style="font-family:'Playfair Display',serif;color:white;letter-spacing:4px">TNCF</span>
      <p>Le réseau ferroviaire grande vitesse français. Voyagez vite, voyagez bien, voyagez TNCF.</p>
    </div>
    <div class="footer-col">
      <h4>Voyager</h4>
      <ul>
        <li><a href="#">Horaires & Prix</a></li>
        <li><a href="#">Abonnements</a></li>
        <li><a href="#">Cartes de réduction</a></li>
        <li><a href="#">TGV Inoui</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Services</h4>
      <ul>
        <li><a href="#">Bagages</a></li>
        <li><a href="#">Espace silencieux</a></li>
        <li><a href="#">WiFi à bord</a></li>
        <li><a href="#">Restauration</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>TNCF</h4>
      <ul>
        <li><a href="#">À propos</a></li>
        <li><a href="#">Presse</a></li>
        <li><a href="#">Recrutement</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© 2026 TNCF — Tous droits réservés</span>
    <span>Mentions légales · CGU · Confidentialité</span>
  </div>
</footer>

<script>
  // Navbar scroll effect
  window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 60);
  });
  document.getElementById('navbar').classList.add('scrolled'); // always dark on homepage



  // Search tabs
  document.querySelectorAll('.search-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.search-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
    });
  });

  // Swap cities
  function swapCities() {
    const inputs = document.querySelectorAll('.search-field-inner input[type="text"]');
    const temp = inputs[0].value;
    inputs[0].value = inputs[1].value;
    inputs[1].value = temp;
  }


  // Search tabs
  const tabs = document.querySelectorAll('.search-tab');
  const retourField = document.getElementById('retour-field');
  const retourInput = retourField.querySelector('input');

  retourField.style.opacity = '0.4';
  retourField.style.pointerEvents = 'none';
  retourInput.disabled = true;

  tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.search-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      if (index === 0) {
        retourField.style.opacity = '0.4';
        retourField.style.pointerEvents = 'none';
        retourInput.disabled = true;
      } else {
        retourField.style.opacity = '1';
        retourField.style.pointerEvents = 'auto';
        retourInput.disabled = false;
      }
    });
  });
</script>
</body>
</html>
