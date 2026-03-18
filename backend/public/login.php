<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TNCF — Connexion</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style//login.css">
</head>
<body>

<div class="left-panel">
  <div class="left-content">
    <div class="brand">
      <div class="brand-logo">
        <img src="img/logo.svg" alt="" >
      </div>
    </div>

    <div class="hero-text">
      <h1>Voyagez<br>à grande <em>vitesse</em></h1>
      <p>Réservez vos billets TGV en quelques clics. Confort, rapidité et sérénité pour tous vos déplacements.</p>
      <div class="stats">
        <div class="stat-item">
          <div class="stat-num">320</div>
          <div class="stat-label">km/h max</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">200+</div>
          <div class="stat-label">destinations</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">99%</div>
          <div class="stat-label">ponctualité</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Animated tracks -->
  <div class="tracks">
    <div class="track-line"></div>
    <div class="track-line"></div>
    <div class="sleepers">
      <!-- generate many sleepers via JS below -->
    </div>
    <div class="train-silhouette">
      <div class="hero-train">
        <img src="./img/train/train.svg" alt="TGV Train" style="height: 250px; width: auto;">
      </div>
    </div>
  </div>
</div>

<div class="right-panel">
  <div class="login-card">
    <h2 class="login-title">Bon retour</h2>
    <p class="login-subtitle">Pas encore de compte ? <a href="signup.html">S'inscrire</a></p>

    <div class="form-group">
      <label class="form-label">Adresse e-mail</label>
      <div class="input-wrap">
        <span class="input-icon">
          <img src="img/mail.svg" alt="">
        </span>
        <input class="form-input" type="email" id="email" placeholder="votre@email.com">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Mot de passe</label>
      <div class="input-wrap">
        <span class="input-icon">
          <img src="img/pass.svg" alt="">
        </span>
        <input class="form-input" type="password" id="password" placeholder="••••••••">
      </div>
      <a href="#" class="forgot-link">Mot de passe oublié ?</a>
    </div>

    <div id="login-message" style="margin-bottom: 15px; font-size: 14px; display: none;"></div>

    <button class="btn-login" id="btn-login" onclick="handleLogin(event)">SE CONNECTER</button>

    <div class="divider">
      <div class="divider-line"></div>
      <span>ou</span>
      <div class="divider-line"></div>
    </div>

    <p class="signup-link">Vous n'avez pas de compte ? <a href="signup.php">Créer un compte</a></p>
  </div>
</div>

<script>
  // Generate sleepers
  const sleepersEl = document.querySelector('.sleepers');
  for(let i = 0; i < 40; i++) {
    const s = document.createElement('div');
    s.className = 'sleeper';
    sleepersEl.appendChild(s);
  }
  /**
   * Main login handler
   * @param {Event} e 
   */
  async function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const msgBox = document.getElementById('login-message');
    const btn = document.getElementById('btn-login');

    // Basic UI reset
    msgBox.style.display = 'none';
    
    if (!email || !password) {
      msgBox.textContent = "Veuillez remplir tous les champs.";
      msgBox.style.color = "#e05252";
      msgBox.style.display = 'block';
      return;
    }

    btn.disabled = true;
    btn.textContent = "CHARGEMENT...";

    try {
      // Sending request to our new API
      const response = await fetch('api_login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ mail: email, pass: password })
      });

      const data = await response.json();

      if (data.status === 'success') {
        msgBox.textContent = "Connexion réussie ! Redirection...";
        msgBox.style.color = "#2d9e6b";
        msgBox.style.display = 'block';
        
        // Redirect to index.php after success
        setTimeout(() => {
          window.location.href = 'index.php';
        }, 1500);
      } else {
        msgBox.textContent = data.message;
        msgBox.style.color = "#e05252";
        msgBox.style.display = 'block';
        btn.disabled = false;
        btn.textContent = "SE CONNECTER";
      }
    } catch (error) {
      console.error("Login error:", error);
      msgBox.textContent = "Erreur de connexion au serveur.";
      msgBox.style.color = "#e05252";
      msgBox.style.display = 'block';
      btn.disabled = false;
      btn.textContent = "SE CONNECTER";
    }
  }
</script>
</body>
</html>
