<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TNCF — Accueil</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --navy: #0a1628;
    --blue: #1a3a6e;
    --gold: #c9a84c;
    --gold-light: #e8c97a;
    --cream: #f5f0e8;
    --white: #ffffff;
    --gray: #8a8f9e;
    --light: #f9f7f3;
  }

  html { scroll-behavior: smooth; }
  body { font-family: 'DM Sans', sans-serif; background: var(--light); color: var(--navy); overflow-x: hidden; }

  /* ─── NAV ─── */
  nav {
    position: fixed; top: 0; left: 0; right: 0;
    padding: 0 56px;
    height: 72px;
    display: flex; align-items: center; justify-content: space-between;
    z-index: 1000;
    transition: all 0.3s;
  }
  nav.scrolled {
    background: rgba(10,22,40,0.95);
    backdrop-filter: blur(16px);
    box-shadow: 0 2px 24px rgba(0,0,0,0.3);
  }

  .brand {
    display: flex; align-items: center; gap: 12px;
    text-decoration: none;
  }
  .brand-logo {
    width: 40px; height: 40px;
    background: var(--gold);
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
  }
  .brand-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem; font-weight: 900;
    color: var(--white); letter-spacing: 4px;
  }

  .nav-links {
    display: flex; align-items: center; gap: 32px;
    list-style: none;
  }
  .nav-links a {
    font-size: 0.85rem; color: rgba(255,255,255,0.75);
    text-decoration: none; letter-spacing: 0.5px;
    transition: color 0.2s;
  }
  .nav-links a:hover { color: var(--gold); }

  .nav-actions { display: flex; align-items: center; gap: 12px; }

  .btn-nav-outline {
    padding: 8px 20px;
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 6px;
    color: rgba(255,255,255,0.85);
    font-family: 'DM Sans', sans-serif;
    font-size: 0.82rem; letter-spacing: 0.5px;
    cursor: pointer; background: transparent;
    text-decoration: none;
    transition: all 0.2s;
  }
  .btn-nav-outline:hover { border-color: var(--gold); color: var(--gold); }

  .btn-nav-fill {
    padding: 8px 22px;
    background: var(--gold);
    border: none; border-radius: 6px;
    color: var(--navy);
    font-family: 'DM Sans', sans-serif;
    font-size: 0.82rem; font-weight: 600; letter-spacing: 0.5px;
    cursor: pointer; text-decoration: none;
    transition: all 0.2s;
  }
  .btn-nav-fill:hover { background: var(--gold-light); }

  /* ─── HERO ─── */
  .hero {
    min-height: 100vh;
    background: linear-gradient(175deg, #0a1628 0%, #0f2347 40%, #1a3a6e 70%, #112040 100%);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center;
    position: relative;
    overflow: hidden;
    padding: 0 24px;
  }

  .hero-bg-grid {
    position: absolute; inset: 0;
    background-image:
      linear-gradient(rgba(201,168,76,0.04) 1px, transparent 1px),
      linear-gradient(90deg, rgba(201,168,76,0.04) 1px, transparent 1px);
    background-size: 60px 60px;
  }

  .hero-glow {
    position: absolute;
    width: 600px; height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(201,168,76,0.12) 0%, transparent 70%);
    top: 50%; left: 50%;
    transform: translate(-50%, -60%);
  }

  /* Animated train line */
  .hero-rail {
    position: absolute;
    bottom: 120px; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(201,168,76,0.3), transparent);
  }

  .hero-train {
    position: absolute;
    bottom: 104px;
    left: -300px;
    animation: trainMove 12s linear infinite;
  }

  @keyframes trainMove {
    0% { left: -300px; }
    100% { left: 110%; }
  }

  .hero-content { position: relative; z-index: 2; max-width: 760px; }

  .hero-eyebrow {
    display: inline-flex; align-items: center; gap: 10px;
    padding: 6px 16px;
    border: 1px solid rgba(201,168,76,0.3);
    border-radius: 100px;
    font-size: 0.72rem; letter-spacing: 2.5px;
    color: var(--gold); text-transform: uppercase;
    margin-bottom: 28px;
    background: rgba(201,168,76,0.06);
  }

  .hero-eyebrow::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--gold);
    animation: blink 2s ease-in-out infinite;
  }

  @keyframes blink { 0%,100%{opacity:1}50%{opacity:0.3} }

  .hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.8rem, 6vw, 5rem);
    font-weight: 700;
    color: var(--white);
    line-height: 1.05;
    margin-bottom: 24px;
    letter-spacing: -0.5px;
  }

  .hero h1 em { font-style: italic; color: var(--gold); }

  .hero p {
    font-size: 1.1rem; font-weight: 300;
    color: rgba(255,255,255,0.55);
    line-height: 1.75;
    max-width: 520px; margin: 0 auto 48px;
  }

  /* ─── SEARCH BOX ─── */
  .search-box {
    background: var(--white);
    border-radius: 20px;
    padding: 28px 32px;
    max-width: 860px;
    width: 100%;
    margin: 0 auto;
    box-shadow: 0 24px 80px rgba(0,0,0,0.35);
    position: relative; z-index: 2;
    margin-top: -20px;
  }

  .search-tabs {
    display: flex; gap: 0;
    margin-bottom: 24px;
    border-bottom: 2px solid #f0ece4;
  }

  .search-tab {
    padding: 10px 20px;
    font-size: 0.85rem; font-weight: 500;
    color: var(--gray);
    cursor: pointer;
    border: none; background: none;
    font-family: 'DM Sans', sans-serif;
    position: relative; bottom: -2px;
    transition: all 0.2s;
    letter-spacing: 0.3px;
  }

  .search-tab.active {
    color: var(--navy);
    border-bottom: 2px solid var(--navy);
  }

  .search-fields {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr auto;
    gap: 12px;
    align-items: end;
  }

  .search-field { position: relative; }

  .search-field label {
    display: block;
    font-size: 0.68rem; font-weight: 600;
    letter-spacing: 1.5px; text-transform: uppercase;
    color: var(--gray); margin-bottom: 8px;
  }

  .search-field-inner {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 14px;
    border: 1.5px solid #e8e2d8;
    border-radius: 10px;
    cursor: pointer;
    background: var(--light);
    transition: all 0.2s;
    position: relative;
  }

  .search-field-inner:hover,
  .search-field-inner:focus-within { border-color: var(--gold); background: var(--white); }

  .search-field-inner input,
  .search-field-inner select {
    border: none; outline: none; background: transparent;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.95rem; color: var(--navy);
    width: 100%; cursor: pointer;
  }

  .search-icon { color: var(--gold); flex-shrink: 0; }

  .swap-btn {
    position: absolute;
    right: -20px; top: 50%; transform: translateY(-50%);
    width: 32px; height: 32px;
    background: var(--white);
    border: 1.5px solid #e8e2d8;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; z-index: 10;
    transition: all 0.2s;
    font-size: 0; /* hide label */
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }
  .swap-btn:hover { border-color: var(--gold); background: var(--gold); }
  .swap-btn:hover svg { stroke: var(--white); }

  .btn-search {
    padding: 13px 28px;
    background: var(--navy);
    color: var(--white);
    border: none; border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem; font-weight: 600;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.25s;
    display: flex; align-items: center; gap: 8px;
    white-space: nowrap;
  }

  .btn-search:hover {
    background: var(--blue);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(10,22,40,0.25);
  }

  /* ─── POPULAR ROUTES ─── */
  .section { padding: 100px 56px; }
  .section-alt { background: var(--cream); }

  .section-header { margin-bottom: 52px; }
  .section-eyebrow {
    font-size: 0.7rem; letter-spacing: 3px;
    text-transform: uppercase; color: var(--gold);
    font-weight: 500; margin-bottom: 12px;
  }
  .section-title {
    font-family: 'Playfair Display', serif;
    font-size: 2.4rem; font-weight: 700;
    color: var(--navy); line-height: 1.2;
  }
  .section-sub {
    font-size: 0.95rem; color: var(--gray);
    font-weight: 300; margin-top: 10px;
  }

  .routes-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
  }

  .route-card {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #f0ece4;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: block;
  }

  .route-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 48px rgba(10,22,40,0.12);
    border-color: rgba(201,168,76,0.3);
  }

  .route-img {
    height: 160px;
    position: relative;
    overflow: hidden;
  }

  .route-img-bg {
    width: 100%; height: 100%;
    background-size: cover;
    background-position: center;
    transition: transform 0.4s;
  }

  .route-card:hover .route-img-bg { transform: scale(1.05); }

  .route-img-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(10,22,40,0.6), transparent);
  }

  .route-badge {
    position: absolute;
    top: 12px; right: 12px;
    padding: 4px 10px;
    background: var(--gold);
    border-radius: 100px;
    font-size: 0.7rem; font-weight: 600;
    color: var(--navy); letter-spacing: 0.5px;
  }

  .route-body { padding: 20px 22px; }

  .route-cities {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 8px;
  }

  .route-city {
    font-family: 'Playfair Display', serif;
    font-size: 1.05rem; font-weight: 600;
    color: var(--navy);
  }

  .route-arrow { color: var(--gold); }

  .route-meta {
    display: flex; justify-content: space-between;
    align-items: center;
  }

  .route-duration {
    font-size: 0.8rem; color: var(--gray);
    display: flex; align-items: center; gap: 5px;
  }

  .route-price {
    font-size: 1.1rem; font-weight: 600;
    color: var(--navy);
  }

  .route-price span { font-size: 0.75rem; color: var(--gray); font-weight: 400; }

  /* ─── FEATURES ─── */
  .features-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
  }

  .feature-card {
    text-align: center;
    padding: 36px 24px;
    border-radius: 16px;
    background: var(--white);
    border: 1px solid #f0ece4;
    transition: all 0.3s;
  }

  .feature-card:hover {
    border-color: rgba(201,168,76,0.3);
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(10,22,40,0.06);
  }

  .feature-icon {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, #0a1628, #1a3a6e);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
  }

  .feature-title {
    font-family: 'Playfair Display', serif;
    font-size: 1rem; font-weight: 600;
    color: var(--navy); margin-bottom: 8px;
  }

  .feature-text {
    font-size: 0.83rem; color: var(--gray);
    line-height: 1.65; font-weight: 300;
  }

  /* ─── PROMO BANNER ─── */
  .promo-banner {
    background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
    border-radius: 24px;
    padding: 56px 64px;
    display: flex; align-items: center; justify-content: space-between;
    overflow: hidden; position: relative;
  }

  .promo-banner::before {
    content: '';
    position: absolute;
    right: -60px; top: -60px;
    width: 280px; height: 280px;
    border-radius: 50%;
    background: rgba(201,168,76,0.08);
  }

  .promo-banner::after {
    content: '';
    position: absolute;
    right: 60px; bottom: -80px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(201,168,76,0.05);
  }

  .promo-text { position: relative; z-index: 2; }
  .promo-eyebrow {
    font-size: 0.7rem; letter-spacing: 3px;
    text-transform: uppercase; color: var(--gold);
    margin-bottom: 12px;
  }
  .promo-title {
    font-family: 'Playfair Display', serif;
    font-size: 2rem; font-weight: 700;
    color: var(--white); margin-bottom: 12px;
  }
  .promo-sub { font-size: 0.9rem; color: rgba(255,255,255,0.55); font-weight: 300; }

  .promo-action { position: relative; z-index: 2; }
  .btn-promo {
    padding: 15px 36px;
    background: var(--gold);
    color: var(--navy);
    border: none; border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.95rem; font-weight: 600;
    cursor: pointer; letter-spacing: 0.5px;
    transition: all 0.2s; text-decoration: none;
    display: inline-block;
  }
  .btn-promo:hover { background: var(--gold-light); transform: translateY(-1px); }

  /* ─── FOOTER ─── */
  footer {
    background: var(--navy);
    padding: 64px 56px 40px;
    color: rgba(255,255,255,0.5);
  }

  .footer-top {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 48px;
    margin-bottom: 48px;
  }

  .footer-brand .brand-name { font-size: 1.6rem; margin-bottom: 16px; display: block; }
  .footer-brand p { font-size: 0.85rem; line-height: 1.7; font-weight: 300; }

  .footer-col h4 {
    font-size: 0.72rem; letter-spacing: 2px;
    text-transform: uppercase; color: var(--gold);
    margin-bottom: 20px; font-weight: 500;
  }

  .footer-col ul { list-style: none; }
  .footer-col ul li { margin-bottom: 10px; }
  .footer-col ul li a {
    color: rgba(255,255,255,0.45);
    text-decoration: none; font-size: 0.85rem;
    transition: color 0.2s;
  }
  .footer-col ul li a:hover { color: var(--gold); }

  .footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.08);
    padding-top: 28px;
    display: flex; justify-content: space-between;
    font-size: 0.78rem;
  }

  /* City backgrounds (CSS gradient placeholders) */
  .bg-paris { background: linear-gradient(135deg, #1e3a5f, #2c5282); }
  .bg-lyon { background: linear-gradient(135deg, #2d3748, #4a5568); }
  .bg-marseille { background: linear-gradient(135deg, #1a4a6e, #2b6cb0); }
  .bg-bordeaux { background: linear-gradient(135deg, #3d1f1f, #6b3030); }
  .bg-nice { background: linear-gradient(135deg, #1a5276, #2980b9); }
  .bg-lille { background: linear-gradient(135deg, #1c2e4a, #2c3e6e); }

  @media (max-width: 1024px) {
    .search-fields { grid-template-columns: 1fr 1fr; }
    .btn-search { grid-column: span 2; }
    .routes-grid { grid-template-columns: 1fr 1fr; }
    .features-grid { grid-template-columns: 1fr 1fr; }
  }
</style>
</head>
<body>

<!-- NAV -->
<nav id="navbar">
  <a href="home.html" class="brand">
    <div class="brand-logo">
      <svg width="22" height="22" viewBox="0 0 28 28" fill="none">
        <path d="M2 20h24M4 14h20l-3-8H7L4 14z" stroke="#0a1628" stroke-width="2" stroke-linecap="round"/>
        <circle cx="8" cy="22" r="2" fill="#0a1628"/>
        <circle cx="20" cy="22" r="2" fill="#0a1628"/>
      </svg>
    </div>
    <div class="brand-name">TNCF</div>
  </a>

  <ul class="nav-links">
    <li><a href="#">Horaires</a></li>
    <li><a href="#">Offres</a></li>
    <li><a href="#">Services</a></li>
    <li><a href="#">Mon compte</a></li>
  </ul>

  <div class="nav-actions">
    <a href="login.html" class="btn-nav-outline">Connexion</a>
    <a href="signup.html" class="btn-nav-fill">S'inscrire</a>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg-grid"></div>
  <div class="hero-glow"></div>
  <div class="hero-rail"></div>

  <!-- animated mini-train -->
  <div class="hero-train">
    <svg width="200" height="24" viewBox="0 0 200 24" fill="none">
      <rect x="0" y="6" width="180" height="16" rx="8" fill="#c9a84c" opacity="0.6"/>
      <path d="M160 6 Q190 6 190 14 Q190 22 160 22" fill="#c9a84c" opacity="0.6"/>
      <rect x="0" y="6" width="4" height="16" rx="2" fill="#e8c97a" opacity="0.8"/>
      <rect x="24" y="10" width="12" height="8" rx="2" fill="rgba(255,255,255,0.2)"/>
      <rect x="48" y="10" width="12" height="8" rx="2" fill="rgba(255,255,255,0.2)"/>
      <rect x="72" y="10" width="12" height="8" rx="2" fill="rgba(255,255,255,0.2)"/>
      <rect x="96" y="10" width="12" height="8" rx="2" fill="rgba(255,255,255,0.2)"/>
      <rect x="120" y="10" width="12" height="8" rx="2" fill="rgba(255,255,255,0.2)"/>
    </svg>
  </div>

  <div class="hero-content">
    <div class="hero-eyebrow">Voyages Grande Vitesse</div>
    <h1>La France à<br>grande <em>vitesse</em></h1>
    <p>Trouvez et réservez vos billets TGV au meilleur prix. Plus de 200 destinations, des départs chaque heure.</p>
  </div>

  <!-- SEARCH BOX -->
  <div class="search-box">
    <div class="search-tabs">
      <button class="search-tab active">Aller-retour</button>
      <button class="search-tab">Aller simple</button>
      <button class="search-tab">Multi-étapes</button>
    </div>

    <div class="search-fields">
      <div class="search-field" style="position:relative">
        <label>Départ</label>
        <div class="search-field-inner">
          <span class="search-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M2 12h5M17 12h5M12 2v5M12 17v5"/></svg>
          </span>
          <input type="text" placeholder="Ville ou gare" value="Paris (Gare de Lyon)">
        </div>
        <button class="swap-btn" onclick="swapCities()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0a1628" stroke-width="2"><path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
        </button>
      </div>

      <div class="search-field">
        <label>Arrivée</label>
        <div class="search-field-inner">
          <span class="search-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </span>
          <input type="text" placeholder="Ville ou gare" value="Lyon Part-Dieu">
        </div>
      </div>

      <div class="search-field">
        <label>Départ</label>
        <div class="search-field-inner">
          <span class="search-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </span>
          <input type="date" value="2026-03-15">
        </div>
      </div>

      <div class="search-field">
        <label>Retour</label>
        <div class="search-field-inner">
          <span class="search-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </span>
          <input type="date" value="2026-03-18">
        </div>
      </div>

      <div>
        <button class="btn-search" onclick="window.location.href='tickets.html'">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
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
        <div class="route-badge">⚡ Le plus rapide</div>
        <!-- Eiffel tower silhouette in SVG -->
        <svg style="position:absolute;bottom:10px;left:20px;opacity:0.4" width="40" height="60" viewBox="0 0 40 60"><path d="M20 0 L14 20 L16 20 L12 40 L8 60 L32 60 L28 40 L24 20 L26 20 Z" fill="white"/><rect x="10" y="30" width="20" height="2" fill="white"/><rect x="6" y="52" width="28" height="2" fill="white"/></svg>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Paris</span>
          <span class="route-arrow">→</span>
          <span class="route-city">Lyon</span>
        </div>
        <div class="route-meta">
          <span class="route-duration">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            1h 55min
          </span>
          <span class="route-price">dès 29€ <span>/ pers.</span></span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-marseille"></div>
        <div class="route-img-overlay"></div>
        <div class="route-badge">🌊 Populaire</div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Paris</span>
          <span class="route-arrow">→</span>
          <span class="route-city">Marseille</span>
        </div>
        <div class="route-meta">
          <span class="route-duration">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            3h 05min
          </span>
          <span class="route-price">dès 39€ <span>/ pers.</span></span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-bordeaux"></div>
        <div class="route-img-overlay"></div>
        <div class="route-badge">🍷 Escapade</div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Paris</span>
          <span class="route-arrow">→</span>
          <span class="route-city">Bordeaux</span>
        </div>
        <div class="route-meta">
          <span class="route-duration">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            2h 04min
          </span>
          <span class="route-price">dès 25€ <span>/ pers.</span></span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-nice"></div>
        <div class="route-img-overlay"></div>
        <div class="route-badge">☀️ Côte d'Azur</div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Paris</span>
          <span class="route-arrow">→</span>
          <span class="route-city">Nice</span>
        </div>
        <div class="route-meta">
          <span class="route-duration">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            5h 40min
          </span>
          <span class="route-price">dès 49€ <span>/ pers.</span></span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-lille"></div>
        <div class="route-img-overlay"></div>
        <div class="route-badge">🎨 Culture</div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Paris</span>
          <span class="route-arrow">→</span>
          <span class="route-city">Lille</span>
        </div>
        <div class="route-meta">
          <span class="route-duration">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            1h 02min
          </span>
          <span class="route-price">dès 19€ <span>/ pers.</span></span>
        </div>
      </div>
    </a>

    <a href="tickets.html" class="route-card">
      <div class="route-img">
        <div class="route-img-bg bg-lyon"></div>
        <div class="route-img-overlay"></div>
        <div class="route-badge">🍴 Gastronomie</div>
      </div>
      <div class="route-body">
        <div class="route-cities">
          <span class="route-city">Lyon</span>
          <span class="route-arrow">→</span>
          <span class="route-city">Marseille</span>
        </div>
        <div class="route-meta">
          <span class="route-duration">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            1h 45min
          </span>
          <span class="route-price">dès 22€ <span>/ pers.</span></span>
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
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      </div>
      <div class="feature-title">Garantie Annulation</div>
      <p class="feature-text">Annulez jusqu'à 30 min avant le départ et soyez remboursé intégralement.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      </div>
      <div class="feature-title">Paiement Sécurisé</div>
      <p class="feature-text">Toutes vos transactions sont protégées par chiffrement SSL 256 bits.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12"/><path d="M9.09 2.91a19.79 19.79 0 0 1 12.91 12.99"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
      </div>
      <div class="feature-title">Alertes SMS</div>
      <p class="feature-text">Restez informé de l'état de votre train en temps réel par SMS.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="1.5"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><line x1="16" y1="7" x2="16" y2="3"/><line x1="8" y1="7" x2="8" y2="3"/></svg>
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
</script>
</body>
</html>
