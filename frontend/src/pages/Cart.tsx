import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useCart } from '../context/CartContext';

import '../assets/style/booking.css';
import { logoSvg, clockSvg, persoWhiteSvg } from '../assets/img/images';

export default function Cart() {
  const { cartItems, removeFromCart, clearCart } = useCart();
  const navigate = useNavigate();

  const [cardDetails, setCardDetails] = useState({
    name: '',
    number: '',
    expiry: '',
    cvv: ''
  });
  const [message, setMessage] = useState({ text: '', type: '' });
  const [isLoading, setIsLoading] = useState(false);

  const cartTotal = cartItems.reduce((sum, item) => sum + item.total, 0);

  const handleCardChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setCardDetails(prev => ({ ...prev, [name]: value }));
  };

  const handleCheckout = async () => {
    if (cartItems.length === 0) return;

    if (!cardDetails.name || !cardDetails.number || !cardDetails.expiry || !cardDetails.cvv) {
      setMessage({ text: 'Veuillez remplir tous les champs de la carte bancaire.', type: 'error' });
      return;
    }

    setIsLoading(true);

    const payload = {
      cartItems,
      payment: 'card',
      cardDetails: {
          name: cardDetails.name,
          number: cardDetails.number.replace(/\s/g, ''),
          expiry: cardDetails.expiry
      },
      total: cartTotal
    };

    try {
      const response = await fetch('http://localhost:8000/api_booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include', 
        body: JSON.stringify(payload),
      });
      const data = await response.json();

      if (data.status === 'success') {
        clearCart();
        navigate('/account'); 
        return; 
      } else {
        setMessage({ text: data.message || 'Une erreur est survenue.', type: 'error' });
        setIsLoading(false);
      }
    } catch {
      setMessage({ text: 'Erreur réseau ou paiement non disponible.', type: 'error' });
      setIsLoading(false);
    }
  };

  return (
    <div className="booking-page">
      {/* ── TOPBAR ── */}
      <div className="topbar">
        <a href="/" className="brand">
          <div className="brand-logo">
            <img src={logoSvg} alt="TNCF" />
          </div>
        </a>
        <ul className="nav-links">
            <li><a href="/">Voyager</a></li>
            <li><a href="/tickets">Billets</a></li>
            <li><a href="/account">Compte</a></li>
        </ul>
        <div className="topbar-actions">
          <a href="/login" className="cart-btn">
            <img src={persoWhiteSvg} alt="" />
            Connexion
          </a>
        </div>
      </div>

      {/* ── BODY ── */}
      <div className="booking-body" style={{ maxWidth: '1000px', margin: '40px auto' }}>
        
        <div className="booking-form" style={{ width: '100%' }}>
          <button className="booking-back" onClick={() => navigate('/tickets')}>
            ← Continuer mes achats
          </button>

          <div className="booking-section-header">
            <h2>Mon Panier ({cartItems.length} réservation{cartItems.length > 1 ? 's' : ''})</h2>
          </div>

          {cartItems.length === 0 ? (
            <p style={{ padding: '20px', color: 'var(--gray)', textAlign: 'center' }}>Votre panier est complètement vide.</p>
          ) : (
            <>
              {cartItems.map((item, index) => (
                <div key={item.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '16px', marginBottom: '16px', background: 'var(--light)', border: '1px solid #ede8df', borderRadius: '12px' }}>
                  <div>
                    <div style={{ fontWeight: 'bold', color: 'var(--navy)', marginBottom: '8px' }}>
                      {item.train.from} → {item.train.to}
                    </div>
                    <div style={{ color: 'var(--gray)', fontSize: '0.9rem' }}>
                      {item.train.dep} • {item.train.num} • {item.train.cls === '1' ? '1ère' : '2ème'} classe
                    </div>
                    <div style={{ marginTop: '8px', fontSize: '0.85rem' }}>
                      Passager: {item.passenger.prenom} {item.passenger.nom} 
                    </div>
                  </div>
                  <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-end', justifyContent: 'space-between' }}>
                    <div style={{ fontWeight: 'bold', fontSize: '1.2rem', color: 'var(--navy)' }}>
                      {item.total.toFixed(2)}€
                    </div>
                    <button 
                      onClick={() => removeFromCart(item.id)}
                      style={{ background: 'none', border: 'none', color: '#e05252', cursor: 'pointer', fontSize: '0.9rem', textDecoration: 'underline' }}
                    >
                      Supprimer
                    </button>
                  </div>
                </div>
              ))}

              {/* 5. PAYMENT */}
              <div className="booking-section" style={{ marginTop: '40px' }}>
                <div className="booking-section-header">
                  <h2>Paiement des réservations (Total : {cartTotal.toFixed(2)}€)</h2>
                </div>
                <p className="booking-section-sub">Saisissez les informations de votre carte bancaire</p>

                <div className="booking-payment-options">
                    <label className="booking-payment-card selected">
                      <div className="booking-payment-radio">
                        <div className="booking-payment-dot active"></div>
                      </div>
                      <span className="booking-payment-icon">💳</span>
                      <span className="booking-payment-label">Carte bancaire</span>
                    </label>
                </div>

                <div className="booking-card-form" style={{ marginTop: '20px', padding: '24px', border: '1px solid #ede8df', borderRadius: '12px', background: 'var(--light)' }}>
                  <div className="booking-form-row">
                    <div className="booking-form-group">
                      <label className="booking-label">Titulaire de la carte *</label>
                      <input
                        className="booking-input"
                        type="text" name="name"
                        placeholder="Jean Dupont"
                        value={cardDetails.name}
                        onChange={handleCardChange}
                      />
                    </div>
                  </div>
                  
                  <div className="booking-form-row" style={{ display: 'flex', gap: '16px', flexWrap: 'wrap' }}>
                    <div className="booking-form-group" style={{ flex: '2 1 200px' }}>
                      <label className="booking-label">Numéro de carte *</label>
                      <input
                        className="booking-input"
                        type="text" name="number"
                        placeholder="0000 0000 0000 0000"
                        maxLength={19}
                        value={cardDetails.number}
                        onChange={handleCardChange}
                      />
                    </div>
                    <div className="booking-form-group" style={{ flex: '1 1 80px' }}>
                      <label className="booking-label">Expiration *</label>
                      <input
                        className="booking-input"
                        type="text" name="expiry"
                        placeholder="MM/AA"
                        maxLength={5}
                        value={cardDetails.expiry}
                        onChange={handleCardChange}
                      />
                    </div>
                    <div className="booking-form-group" style={{ flex: '1 1 80px' }}>
                      <label className="booking-label">CVC *</label>
                      <input
                        className="booking-input"
                        type="text" name="cvv"
                        placeholder="123"
                        maxLength={4}
                        value={cardDetails.cvv}
                        onChange={handleCardChange}
                      />
                    </div>
                  </div>
                </div>
              </div>

              {message.text && (
                <div className={`booking-message ${message.type === 'error' ? 'booking-message-error' : 'booking-message-success'}`}>
                  {message.text}
                </div>
              )}

              <button
                className="booking-btn-pay"
                onClick={handleCheckout}
                disabled={isLoading}
                style={{ width: '100%', marginTop: '20px', padding: '16px', fontSize: '1.1rem' }}
              >
                {isLoading ? 'TRAITEMENT EN COURS...' : `Payer ${cartTotal.toFixed(2)}€`}
              </button>
            </>
          )}

        </div>
      </div>
    </div> 
  );
}
