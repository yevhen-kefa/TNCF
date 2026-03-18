<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TNCF — Créer un compte</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/singup.css">
<style>
  /* Styles for the alert message */
  .alert-message {
    padding: 12px;
    border-radius: 8px;
    margin-top: 16px;
    font-size: 0.85rem;
    display: none;
    text-align: center;
  }
  .alert-error {
    background-color: #fde8e8;
    color: #c53030;
    border: 1px solid #feb2b2;
  }
  .alert-success {
    background-color: #e6fffa;
    color: #276749;
    border: 1px solid #b2f5ea;
  }
</style>
</head>
<body>

<nav class="topbar">
  <a href="index.php" class="brand">
    <div class="brand-logo">
      <img src="img/logo.svg" alt="">
    </div>
  </a>
  <a href="login.php" class="topbar-link">Déjà un compte ? <span>Se connecter →</span></a>
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

    <div class="section-label">Informations personnelles</div>
    
    <div class="form-group" style="margin-bottom: 16px;">
      <label class="form-label">Civilité</label>
      <div class="input-wrap" style="display: flex; gap: 16px;">
        <label><input type="radio" name="civilite" value="M" checked> M.</label>
        <label><input type="radio" name="civilite" value="Mme"> Mme.</label>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Prénom</label>
        <div class="input-wrap">
          <span class="input-icon">
            <img src="img/person.svg" alt="">
          </span>
          <input class="form-input" type="text" id="prenom" placeholder="Jean">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Nom de famille</label>
        <div class="input-wrap">
          <span class="input-icon">
            <img src="img/person.svg" alt="">
          </span>
          <input class="form-input" type="text" id="nom" placeholder="Dupont">
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Date de naissance</label>
        <div class="input-wrap">
          <input class="form-input" type="date" id="dob" style="padding-left:42px">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Téléphone</label>
        <div class="input-wrap">
          <input class="form-input" type="tel" id="telephone" placeholder="+33 6 00 00 00 00">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Adresse e-mail</label>
      <div class="input-wrap">
        <span class="input-icon">
          <img src="img/mail.svg" alt="">
        </span>
        <input class="form-input" type="email" id="email" placeholder="votre@email.com">
      </div>
    </div>

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
          <input class="form-input" type="password" id="pwd_confirm" placeholder="Répétez le mot de passe">
        </div>
      </div>
    </div>

    <div id="form-message" class="alert-message"></div>

    <button class="btn-submit" id="btn-register" onclick="registerUser(event)">
      CRÉER MON COMPTE
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </button>

    <p class="login-link">Déjà inscrit ? <a href="login.php">Se connecter</a></p>
  </div>
</div>

<script>
// Visual password strength indicator
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

// Display message helper
function showMessage(msg, type) {
  const msgBox = document.getElementById('form-message');
  msgBox.textContent = msg;
  msgBox.className = 'alert-message ' + (type === 'error' ? 'alert-error' : 'alert-success');
  msgBox.style.display = 'block';
}

// Handle registration request
function registerUser(e) {
  e.preventDefault(); // Prevent default button behavior
  
  // 1. Gather input values
  const prenom = document.getElementById('prenom').value.trim();
  const nom = document.getElementById('nom').value.trim();
  const telephone = document.getElementById('telephone').value.trim();
  const email = document.getElementById('email').value.trim();
  const pass = document.getElementById('pwd').value;
  const passConfirm = document.getElementById('pwd_confirm').value;
  
  // Get checked radio button for civilite
  const civilite = document.querySelector('input[name="civilite"]:checked').value;

  // 2. Basic front-end validation
  if (!prenom || !nom || !telephone || !email || !pass) {
    showMessage('Veuillez remplir tous les champs obligatoires.', 'error');
    return;
  }

  if (pass !== passConfirm) {
    showMessage('Les mots de passe ne correspondent pas.', 'error');
    return;
  }

  if (pass.length < 8) {
    showMessage('Le mot de passe doit contenir au moins 8 caractères.', 'error');
    return;
  }

  // Disable button to prevent double submission
  const btn = document.getElementById('btn-register');
  btn.disabled = true;
  btn.innerHTML = 'CHARGEMENT...';

  // 3. Prepare data payload for the API
  const payload = {
    civilite: civilite,
    prenom: prenom,
    nom: nom,
    telephone: telephone,
    mail: email,
    pass: pass
  };

  // 4. Send request to the PHP API
  fetch('api_register.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      showMessage('Compte créé avec succès ! Redirection...', 'success');
      
      // Redirect to login page after 2 seconds
      setTimeout(() => {
        window.location.href = 'login.php';
      }, 2000);
    } else {
      showMessage(data.message || 'Une erreur est survenue.', 'error');
      btn.disabled = false;
      btn.innerHTML = 'CRÉER MON COMPTE <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
    }
  })
  .catch(error => {
    console.error('API Error:', error);
    showMessage('Erreur de connexion au serveur.', 'error');
    btn.disabled = false;
    btn.innerHTML = 'CRÉER MON COMPTE <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
  });
}
</script>
</body>
</html>