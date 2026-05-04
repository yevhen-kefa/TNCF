import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useCart } from '../context/CartContext';

import '../assets/style/booking.css';
import '../assets/style/count.css';
import { logoSvg, mailSvg, persoWhiteSvg, shielSvg, boxSvg } from '../assets/img/images';

export default function EditProfile() {
  const navigate = useNavigate();
  const { cartItems } = useCart();

  const [formData, setFormData] = useState({
    prenom: '',
    nom: '',
    email: '',
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
  });

  const [message, setMessage] = useState({ text: '', type: '' });
  const [isLoading, setIsLoading] = useState(false);
  const [isPageLoading, setIsPageLoading] = useState(true);

  useEffect(() => {
    const fetchUserData = async () => {
      try {
        const response = await fetch('http://localhost:8000/api_user.php', {
          credentials: 'include'
        });
        const data = await response.json();

        if (data.status === 'success') {
          setFormData(prev => ({
            ...prev,
            prenom: data.user.prenom || '',
            nom: data.user.nom || '',
            email: data.user.mail || ''
          }));
          setIsPageLoading(false);
        } else {
          navigate('/login');
        }
      } catch (error) {
        console.error("Erreur de chargement", error);
        navigate('/login');
      }
    };
    fetchUserData();
  }, [navigate]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setMessage({ text: '', type: '' });

    if (formData.newPassword) {
      if (!formData.currentPassword) {
        setMessage({ text: 'Veuillez entrer votre mot de passe actuel pour le modifier.', type: 'error' });
        return;
      }
      if (formData.newPassword !== formData.confirmPassword) {
        setMessage({ text: 'Les nouveaux mots de passe ne correspondent pas.', type: 'error' });
        return;
      }
      if (formData.newPassword.length < 6) {
        setMessage({ text: 'Le nouveau mot de passe doit contenir au moins 6 caractères.', type: 'error' });
        return;
      }
    }

    setIsLoading(true);

    try {
      const response = await fetch('http://localhost:8000/api_update_profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(formData),
      });

      const data = await response.json();

      if (data.status === 'success') {
        setMessage({ text: '✓ Profil mis à jour avec succès !', type: 'success' });
        setFormData(prev => ({ ...prev, currentPassword: '', newPassword: '', confirmPassword: '' }));
      } else {
        setMessage({ text: data.message || 'Une erreur est survenue.', type: 'error' });
      }
    } catch (error) {
      setMessage({ text: 'Erreur réseau.', type: 'error' });
    } finally {
      setIsLoading(false);
    }
  };

  if (isPageLoading) {
    return (
      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', minHeight: '100vh', backgroundColor: '#f9f8f6' }}>
        <div style={{ textAlign: 'center', color: 'var(--gray)' }}>
          <div style={{ fontSize: '2rem', marginBottom: '12px' }}>⏳</div>
          <p>Vérification de la session...</p>
        </div>
      </div>
    );
  }

  return (
    <div style={{ backgroundColor: '#f9f8f6', minHeight: '100vh', paddingBottom: '60px' }}>

      {/* ── TOPBAR ── */}
      <nav className="topbar">
        <a href="/home" className="brand">
          <div className="brand-logo">
            <img src={logoSvg} alt="Logo TNCF" />
          </div>
        </a>
        <ul className="nav-links">
          <li><a href="/home">Voyager</a></li>
          <li><a href="/tickets">Billets</a></li>
          <li><a href="/account" className="active">Compte</a></li>
        </ul>
        <div className="nav-actions" style={{ display: 'flex', gap: '15px', alignItems: 'center' }}>
          <a href="/cart" className="cart-btn" style={{ background: 'none', border: 'none', display: 'flex', alignItems: 'center', gap: '8px', color: 'var(--navy)', textDecoration: 'none', fontWeight: 600 }}>
            <img src={boxSvg} alt="" style={{ width: '20px' }} />
            Panier
            <span className="cart-count" style={{ background: 'var(--gold)', padding: '2px 6px', borderRadius: '12px', fontSize: '0.75rem', color: 'var(--navy)' }}>
              {cartItems.length}
            </span>
          </a>
        </div>
      </nav>

      {/* ── BAND ── */}
      <div className="page-band">
        <div className="page-band-inner">
          <div className="page-eyebrow">Paramètres</div>
          <h1 className="page-title">Modifier le profil</h1>
        </div>
      </div>

      {/* ── FORM ── */}
      <div style={{ maxWidth: '800px', margin: '40px auto', padding: '0 20px' }}>

        <button
          onClick={() => navigate('/account')}
          style={{ background: 'none', border: 'none', color: 'var(--navy)', cursor: 'pointer', marginBottom: '24px', fontSize: '1rem', fontWeight: 600, display: 'flex', alignItems: 'center', gap: '6px' }}
        >
          ← Retour à mon compte
        </button>

        <div style={{ background: 'white', padding: '40px', borderRadius: '16px', border: '1px solid #ede8df', boxShadow: '0 4px 12px rgba(0,0,0,0.04)' }}>
          <form onSubmit={handleSubmit}>

            {/* ── Section 1: Infos perso ── */}
            <h3 style={{ color: 'var(--navy)', marginBottom: '20px', borderBottom: '2px solid #ede8df', paddingBottom: '12px', display: 'flex', alignItems: 'center', gap: '10px' }}>
              <img src={persoWhiteSvg} alt="" style={{ width: '20px', filter: 'invert(1) sepia(1) saturate(0)' }} />
              Informations personnelles
            </h3>

            <div style={{ display: 'flex', gap: '20px', flexWrap: 'wrap', marginBottom: '20px' }}>
              <div style={{ flex: '1 1 200px' }}>
                <label className="booking-label">Prénom *</label>
                <input
                  className="booking-input"
                  type="text"
                  name="prenom"
                  value={formData.prenom}
                  onChange={handleChange}
                  required
                  placeholder="Jean"
                />
              </div>
              <div style={{ flex: '1 1 200px' }}>
                <label className="booking-label">Nom *</label>
                <input
                  className="booking-input"
                  type="text"
                  name="nom"
                  value={formData.nom}
                  onChange={handleChange}
                  required
                  placeholder="Dupont"
                />
              </div>
            </div>

            <div style={{ marginBottom: '36px' }}>
              <label className="booking-label">Adresse e-mail *</label>
              <div style={{ position: 'relative' }}>
                <span style={{ position: 'absolute', left: '14px', top: '50%', transform: 'translateY(-50%)', display: 'flex', alignItems: 'center' }}>
                  <img src={mailSvg} alt="" style={{ width: '18px', opacity: 0.5 }} />
                </span>
                <input
                  className="booking-input"
                  type="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  required
                  placeholder="votre@email.com"
                  style={{ paddingLeft: '44px' }}
                />
              </div>
            </div>

            {/* ── Section 2: Sécurité ── */}
            <h3 style={{ color: 'var(--navy)', marginBottom: '8px', borderBottom: '2px solid #ede8df', paddingBottom: '12px', display: 'flex', alignItems: 'center', gap: '10px' }}>
              <img src={shielSvg} alt="" style={{ width: '20px' }} />
              Sécurité
              <span style={{ fontSize: '0.8rem', fontWeight: 400, color: 'var(--gray)' }}>(optionnel)</span>
            </h3>
            <p style={{ color: 'var(--gray)', fontSize: '0.9rem', marginBottom: '20px' }}>
              Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe.
            </p>

            <div style={{ marginBottom: '20px' }}>
              <label className="booking-label">Mot de passe actuel</label>
              <input
                className="booking-input"
                type="password"
                name="currentPassword"
                placeholder="••••••••"
                value={formData.currentPassword}
                onChange={handleChange}
              />
            </div>

            <div style={{ display: 'flex', gap: '20px', flexWrap: 'wrap', marginBottom: '32px' }}>
              <div style={{ flex: '1 1 200px' }}>
                <label className="booking-label">Nouveau mot de passe</label>
                <input
                  className="booking-input"
                  type="password"
                  name="newPassword"
                  placeholder="Minimum 6 caractères"
                  value={formData.newPassword}
                  onChange={handleChange}
                />
              </div>
              <div style={{ flex: '1 1 200px' }}>
                <label className="booking-label">Confirmer le mot de passe</label>
                <input
                  className="booking-input"
                  type="password"
                  name="confirmPassword"
                  placeholder="Répéter le mot de passe"
                  value={formData.confirmPassword}
                  onChange={handleChange}
                />
              </div>
            </div>

            {/* ── Message ── */}
            {message.text && (
              <div style={{
                padding: '14px 20px',
                marginBottom: '20px',
                borderRadius: '10px',
                fontWeight: 500,
                textAlign: 'center',
                backgroundColor: message.type === 'error' ? '#ffebee' : '#eef8f1',
                color: message.type === 'error' ? '#d32f2f' : '#2e7d32',
                border: `1px solid ${message.type === 'error' ? '#ffcdd2' : '#c8e6c9'}`
              }}>
                {message.text}
              </div>
            )}

            {/* ── Submit ── */}
            <button
              type="submit"
              className="booking-btn-pay"
              style={{ width: '100%', padding: '16px', fontSize: '1rem' }}
              disabled={isLoading}
            >
              {isLoading ? 'ENREGISTREMENT...' : 'ENREGISTRER LES MODIFICATIONS'}
            </button>

          </form>
        </div>
      </div>
    </div>
  );
}