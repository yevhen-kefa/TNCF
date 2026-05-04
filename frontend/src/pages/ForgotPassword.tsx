import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import '../assets/style/login.css';

import logoSvg from '../assets/img/logo.svg';
import mailSvg from '../assets/img/mail.svg';
import trainSvg from '../assets/img/train/train.svg';

interface FormMessage {
  text: string;
  color: string;
}

export default function ForgotPassword() {
  const [email, setEmail] = useState<string>('');
  const [message, setMessage] = useState<FormMessage>({ text: '', color: '' });
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    // Reset message
    setMessage({ text: '', color: '' });

    // Validation
    if (!email) {
      setMessage({ text: 'Veuillez entrer votre adresse e-mail.', color: '#e05252' });
      return;
    }

    setIsLoading(true);
    
    // --- ПОЧАТОК СИМУЛЯЦІЇ ---
    
    // Крок 1: Нібито перевіряємо чи є такий юзер (2 секунди)
    setMessage({ text: 'Vérification de l\'adresse e-mail en cours...', color: 'var(--navy)' });
    
    setTimeout(() => {
      // Крок 2: Нібито намагаємось підключитися до поштового сервера (ще 3 секунди)
      setMessage({ text: 'Génération du lien sécurisé...', color: 'var(--navy)' });
      
      setTimeout(() => {
        // Крок 3: Викидаємо красиву помилку сервера
        setIsLoading(false);
        setMessage({ 
          text: 'Erreur 504 : Le serveur ne répond pas. Impossible de générer le lien pour le moment.', 
          color: '#e05252' 
        });
      }, 3000);

    }, 2000);

    // --- КІНЕЦЬ СИМУЛЯЦІЇ ---


    /* --- СПРАВЖНІЙ КОД (ЗАКОМЕНТОВАНО ДЛЯ ПРЕЗЕНТАЦІЇ) ---
    try {
      const response = await fetch('http://localhost:8000/api_forgot_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email }),
      });

      const data = await response.json();

      if (data.status === 'success') {
        setMessage({ text: data.message, color: '#2d9e6b' });
        if (data.dev_link) console.log("LIEN DE RESET (Mode Dev) :", data.dev_link);
      } else {
        setMessage({ text: data.message, color: '#e05252' });
      }
    } catch (error) {
      console.error('Error:', error);
      setMessage({ text: 'Erreur réseau.', color: '#e05252' });
    } finally {
      setIsLoading(false);
    }
    -------------------------------------------------------- */
  };

  return (
    <div className="login-page">
      {/* Left Panel */}
      <div className="left-panel">
        <div className="left-content">
          <div className="brand">
            <div className="brand-logo">
              <a href="/home"><img src={logoSvg} alt="TNCF" /></a>
            </div>
          </div>

          <div className="hero-text">
            <h1>Récupération<br />de <em>compte</em></h1>
            <p>Entrez votre adresse e-mail pour recevoir un lien de réinitialisation de votre mot de passe.</p>
            <div className="stats">
              <div className="stat-item">
                <div className="stat-num-login">320</div>
                <div className="stat-label">km/h max</div>
              </div>
              <div className="stat-item">
                <div className="stat-num-login">200+</div>
                <div className="stat-label">destinations</div>
              </div>
              <div className="stat-item">
                <div className="stat-num-login">99%</div>
                <div className="stat-label">ponctualité</div>
              </div>
            </div>
          </div>
        </div>

        {/* Animated tracks */}
        <div className="tracks">
          <div className="track-line"></div>
          <div className="track-line"></div>
          <div className="sleepers">
            {Array.from({ length: 40 }).map((_, i) => (
              <div key={i} className="sleeper"></div>
            ))}
          </div>
          <div className="train-silhouette">
            <div className="hero-train">
              <img src={trainSvg} alt="TGV Train" style={{ height: "250px", width: "auto" }} />
            </div>
          </div>
        </div>
      </div>

      {/* Right Panel */}
      <div className="right-panel">
        <div className="login-card">
          <h2 className="login-title">Mot de passe oublié</h2>
          <p className="login-subtitle">
            Vous vous souvenez de votre mot de passe ?
          </p>
           <p> <a href="/login" onClick={(e) => { e.preventDefault(); navigate('/login'); }}>Se connecter</a></p>

          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label className="form-label">Adresse e-mail</label>
              <div className="input-wrap">
                <span className="input-icon">
                  <img src={mailSvg} alt="" />
                </span>
                <input 
                  className="form-input" 
                  type="email" 
                  placeholder="votre@email.com" 
                  value={email} 
                  onChange={(e) => setEmail(e.target.value)}
                  required
                  disabled={isLoading}
                />
              </div>
            </div>

            {message.text && (
              <div style={{ 
                marginBottom: '15px', 
                fontSize: '14px', 
                padding: '12px', 
                borderRadius: '8px',
                backgroundColor: message.color === '#e05252' ? '#ffebee' : '#eef8f1',
                color: message.color,
                textAlign: 'center',
                fontWeight: 500
              }}>
                {message.text}
              </div>
            )}

            <button 
              type="submit" 
              className="btn-login" 
              disabled={isLoading}
            >
              {isLoading ? 'TRAITEMENT EN COURS...' : 'ENVOYER LE LIEN'}
            </button>
          </form>

          <div className="divider">
            <div className="divider-line"></div>
            <span>ou</span>
            <div className="divider-line"></div>
          </div>

          <p className="signup-link">
            Vous n'avez pas de compte ? <a href="/signup" onClick={(e) => { e.preventDefault(); navigate('/signup'); }}>Créer un compte</a>
          </p>
        </div>
      </div>
    </div>
  );
}