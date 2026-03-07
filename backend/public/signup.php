<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TNCF — Créer un compte</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/singup.css">
</head>
<body>

<nav class="topbar">
  <a href="index.php" class="brand">
    <div class="brand-logo">
      <img src="img/logo.svg" alt="">
    </div>
  </a>
  <a href="login.html" class="topbar-link">Déjà un compte ? <span>Se connecter →</span></a>
</nav>

<div class="signup-wrapper">
  <div class="signup-header">
    <div class="eyebrow">Inscription gratuite</div>
    <h1>Créer mon compte</h1>
    <p>Rejoignez des millions de voyageurs et profitez des meilleurs tarifs</p>
  </div>

  <div class="progress">
    <div class="step active">
      <div class="step-circle">1</div>
      <span>Informations</span>
    </div>
    <div class="step-line"></div>
    <div class="step">
      <div class="step-circle">2</div>
      <span>Préférences</span>
    </div>
    <div class="step-line"></div>
    <div class="step">
      <div class="step-circle">3</div>
      <span>Confirmation</span>
    </div>
  </div>

  <div class="card">

    <!-- Benefits -->
    <div class="benefits">
      
      <div class="benefit-item">
        <div class="benefit-icon">
          <img src="img/layer.svg" alt="">
        </div>
        Historique des trajets
      </div>
      <div class="benefit-item">
        <div class="benefit-icon">
          <img src="img/cart.svg" alt="">
        </div>
        Paiement mémorisé
      </div>
      <div class="benefit-item">
        <div class="benefit-icon">
          <img src="img/mail_gold.svg" alt="">
        </div>
        Alertes SMS
      </div>
    </div>

    <!-- Personal info -->
    <div class="section-label">Informations personnelles</div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Prénom</label>
        <div class="input-wrap">
          <span class="input-icon">
            <img src="img/person.svg" alt="">
          </span>
          <input class="form-input" type="text" placeholder="Jean">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Nom de famille</label>
        <div class="input-wrap">
          <span class="input-icon">
            <img src="img/person.svg" alt="">
          </span>
          <input class="form-input" type="text" placeholder="Dupont">
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Date de naissance</label>
        <div class="input-wrap">
          <input class="form-input" type="date" style="padding-left:42px">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Téléphone</label>
        <div class="input-wrap">
          <input class="form-input" type="tel" placeholder="+33 6 00 00 00 00">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Adresse e-mail</label>
      <div class="input-wrap">
        <span class="input-icon">
          <img src="img/mail.svg" alt="">
        </span>
        <input class="form-input" type="email" placeholder="votre@email.com">
      </div>
    </div>

    <!-- Password section -->
    <div class="section-label" style="margin-top:16px">Sécurité</div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Mot de passe</label>
        <div class="input-wrap">
          <span class="input-icon">
            <img src="img/pass.svg" alt="">
          </span>
          <input class="form-input" type="password" id="pwd" placeholder="Min. 8 caractères" oninput="checkStrength(this.value)">
        </div>
        <div class="password-strength">
          <div class="strength-bar" id="s1"></div>
          <div class="strength-bar" id="s2"></div>
          <div class="strength-bar" id="s3"></div>
          <div class="strength-bar" id="s4"></div>
        </div>
        <div class="strength-text" id="strength-text">Entrez un mot de passe</div>
      </div>
      <div class="form-group">
        <label class="form-label">Confirmer</label>
        <div class="input-wrap">
          <span class="input-icon">
            <img src="img/pass.svg" alt="">
          </span>
          <input class="form-input" type="password" placeholder="Répétez le mot de passe">
        </div>
      </div>
    </div>


    <button class="btn-submit" onclick="window.location.href='home.html'">
      CRÉER MON COMPTE
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </button>

    <p class="login-link">Déjà inscrit ? <a href="login.php">Se connecter</a></p>
  </div>
</div>

<script>
function checkStrength(val) {
  const bars = [s1,s2,s3,s4];
  bars.forEach(b => { b.className = 'strength-bar'; });
  const txt = document.getElementById('strength-text');
  if (!val) { txt.textContent = 'Entrez un mot de passe'; return; }
  let score = 0;
  if (val.length >= 8) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const labels = ['', 'Faible', 'Moyen', 'Fort', 'Très fort'];
  const cls = ['', 'weak', 'medium', 'strong', 'strong'];
  for (let i = 0; i < score; i++) bars[i].classList.add(cls[score]);
  txt.textContent = labels[score] || 'Trop court';
}
</script>
</body>
</html>
