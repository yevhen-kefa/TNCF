import React, { useState } from 'react';
 import '../assets/style/singup.css'; 
import type { FormData } from '../FormData';


import logoSvg from "../assets/img/logo.svg";
import layerSvg from "../assets/img/layer.svg";
import cartSvg from "../assets/img/cart.svg";
import mailGoldSvg from "../assets/img/mail_gold.svg";
import personSvg from "../assets/img/person.svg";
import mailSvg from "../assets/img/mail.svg";
import passSvg from "../assets/img/pass.svg";


interface FormMessage {
  text: string;
  type: 'error' | 'success' | '';
}

interface PwdStrength {
  score: number;
  label: string;
}

export default function Signup() {
  const [formData, setFormData] = useState<FormData>({
    civilite: 'M',
    prenom: '',
    nom: '',
    dob: '',
    telephone: '',
    email: '',
    pwd: '',
    pwdConfirm: ''
  });

  const [message, setMessage] = useState<FormMessage>({ text: '', type: '' });
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [pwdStrength, setPwdStrength] = useState<PwdStrength>({ score: 0, label: 'Entrez un mot de passe' });

  // TYPE THE EVENT OBJECT (React.ChangeEvent<HTMLInputElement>)
  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { id, name, value, type } = e.target;
    const fieldName = type === 'radio' ? name : id;
    
    setFormData((prev) => ({
      ...prev,
      [fieldName as keyof FormData]: value // Tell TS that fieldName is a valid key of FormData
    }));



    // If password field changes, check strength
    if (id === 'pwd') {
      checkStrength(value);
    }
  };



  // Visual password strength indicator logic
  const checkStrength = (val: string) => {
    if (!val) {
      setPwdStrength({ score: 0, label: 'Entrez un mot de passe' });
      return;
    }
    
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    
    const labels = ['', 'Faible', 'Moyen', 'Fort', 'Très fort'];
    setPwdStrength({ score, label: labels[score] || 'Trop court' });
  };




  //TYPE THE MOUSE EVENT FOR THE BUTTON
  const handleSubmit = async (e: React.MouseEvent<HTMLButtonElement>) => {
    e.preventDefault();

    // Basic validation
    if (!formData.prenom || !formData.nom || !formData.telephone || !formData.email || !formData.pwd) {
      setMessage({ text: 'Veuillez remplir tous les champs obligatoires.', type: 'error' });
      return;
    }

    if (formData.pwd !== formData.pwdConfirm) {
      setMessage({ text: 'Les mots de passe ne correspondent pas.', type: 'error' });
      return;
    }

    if (formData.pwd.length < 8) {
      setMessage({ text: 'Le mot de passe doit contenir au moins 8 caractères.', type: 'error' });
      return;
    }

    setIsLoading(true);

    // Prepare payload
    const payload = {
      civilite: formData.civilite,
      prenom: formData.prenom,
      nom: formData.nom,
      telephone: formData.telephone,
      mail: formData.email,
      pass: formData.pwd
    };

    try {
      // Connect to PHP Backend running on port 8000
      const response = await fetch('http://localhost:8000/api_register.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      const data = await response.json();

      if (data.status === 'success') {
        setMessage({ text: 'Compte créé avec succès ! Redirection...', type: 'success' });
        
        // Redirect to login after 2 seconds
        setTimeout(() => {
          window.location.href = '/login'; 
        }, 2000);
      } else {
        setMessage({ text: data.message || 'Une erreur est survenue.', type: 'error' });
        setIsLoading(false);
      }
    } catch (error) {
      console.error('API Error:', error);
      setMessage({ text: 'Erreur de connexion au serveur.', type: 'error' });
      setIsLoading(false);
    }
  };

  // Helper arrays for password strength UI

  const strengthClasses = ['', 'weak', 'medium', 'strong', 'strong'];
  const activeClass = strengthClasses[pwdStrength.score] || '';

  return (
    <>
    <div className="signup-page">
      <nav className="topbar-signup">
        <a href="/" className="brand">
          <div className="brand-logo">
            <img src={logoSvg} alt="Logo" />
          </div>
        </a>
        <a href="/login" className="topbar-link">Déjà un compte ? <span>Se connecter →</span></a>
      </nav>

      <div className="signup-wrapper">
        <div className="signup-header">
          <div className="eyebrow">Inscription gratuite</div>
          <h1>Créer mon compte</h1>
          <p>Rejoignez des millions de voyageurs et profitez des meilleurs tarifs</p>
        </div>

        <div className="progress">
          <div className="step active">
            <div className="step-circle">1</div>
            <span>Informations</span>
          </div>
          <div className="step-line"></div>
          <div className="step">
            <div className="step-circle">2</div>
            <span>Préférences</span>
          </div>
          <div className="step-line"></div>
          <div className="step">
            <div className="step-circle">3</div>
            <span>Confirmation</span>
          </div>
        </div>

        <div className="card">
          <div className="benefits">
            <div className="benefit-item">
              <div className="benefit-icon"><img src={layerSvg} alt="" /></div>
              Historique des trajets
            </div>
            <div className="benefit-item">
              <div className="benefit-icon"><img src={cartSvg} alt="" /></div>
              Paiement mémorisé
            </div>
            <div className="benefit-item">
              <div className="benefit-icon"><img src={mailGoldSvg} alt="" /></div>
              Alertes SMS
            </div>
          </div>

          <div className="section-label">Informations personnelles</div>
          
          <div className="form-group" style={{ marginBottom: '16px' }}>
            <label className="form-label">Civilité</label>
            <div className="input-wrap" style={{ display: 'flex', gap: '16px' }}>
              <label>
                <input type="radio" name="civilite" value="M" checked={formData.civilite === 'M'} onChange={handleChange} /> M.
              </label>
              <label>
                <input type="radio" name="civilite" value="Mme" checked={formData.civilite === 'Mme'} onChange={handleChange} /> Mme.
              </label>
            </div>
          </div>

          <div className="form-row">
            <div className="form-group">
              <label className="form-label">Prénom</label>
              <div className="input-wrap">
                <span className="input-icon"><img src={personSvg} alt="" /></span>
                <input className="form-input" type="text" id="prenom" placeholder="Jean" value={formData.prenom} onChange={handleChange} />
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Nom de famille</label>
              <div className="input-wrap">
                <span className="input-icon"><img src={personSvg} alt="" /></span>
                <input className="form-input" type="text" id="nom" placeholder="Dupont" value={formData.nom} onChange={handleChange} />
              </div>
            </div>
          </div>

          <div className="form-row">
            <div className="form-group">
              <label className="form-label">Date de naissance</label>
              <div className="input-wrap">
                <input className="form-input" type="date" id="dob" style={{ paddingLeft: '42px' }} value={formData.dob} onChange={handleChange} />
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Téléphone</label>
              <div className="input-wrap">
                <input className="form-input" type="tel" id="telephone" placeholder="+33 6 00 00 00 00" value={formData.telephone} onChange={handleChange} />
              </div>
            </div>
          </div>

          <div className="form-group">
            <label className="form-label">Adresse e-mail</label>
            <div className="input-wrap">
              <span className="input-icon"><img src={mailSvg} alt="" /></span>
              <input className="form-input" type="email" id="email" placeholder="votre@email.com" value={formData.email} onChange={handleChange} />
            </div>
          </div>

          <div className="section-label" style={{ marginTop: '16px' }}>Sécurité</div>

          <div className="form-row">
            <div className="form-group">
              <label className="form-label">Mot de passe</label>
              <div className="input-wrap">
                <span className="input-icon"><img src={passSvg} alt="" /></span>
                <input className="form-input" type="password" id="pwd" placeholder="Min. 8 caractères" value={formData.pwd} onChange={handleChange} />
              </div>
              <div className="password-strength">
                {[1, 2, 3, 4].map((level) => (
                  <div key={level} className={`strength-bar ${pwdStrength.score >= level ? activeClass : ''}`}></div>
                ))}
              </div>
              <div className="strength-text">{pwdStrength.label}</div>
            </div>
            <div className="form-group">
              <label className="form-label">Confirmer</label>
              <div className="input-wrap">
                <span className="input-icon"><img src={passSvg} alt="" /></span>
                <input className="form-input" type="password" id="pwdConfirm" placeholder="Répétez le mot de passe" value={formData.pwdConfirm} onChange={handleChange} />
              </div>
            </div>
          </div>

          {message.text && (
            <div className={`alert-message ${message.type === 'error' ? 'alert-error' : 'alert-success'}`} style={{ display: 'block' }}>
              {message.text}
            </div>
          )}

          <button className="btn-submit" onClick={handleSubmit} disabled={isLoading}>
            {isLoading ? 'CHARGEMENT...' : (
              <>
                CRÉER MON COMPTE
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              </>
            )}
          </button>

          <p className="login-link">Déjà inscrit ? <a href="/login">Se connecter</a></p>
        </div>
      </div>
    </div>
    </>
  );
}