import { useLocation, useNavigate } from "react-router-dom";
import { useState, useEffect } from "react";

import type { PassengerForm } from "../PassengerForm";
import type { ContactForm } from "../ContactForm";
import type { SelectedTrain } from "../SelectedTrain";
import type { Seat } from '../components/SeatModal'; 
import SeatMapModal from "../components/SeatModal";
import { useCart } from '../context/CartContext';

import '../assets/style/booking.css';
import '../assets/img/images';
import { boxSvg, clockSvg, logoSvg, persoWhiteSvg } from "../assets/img/images";

type SeatMode = 'random' | 'specific'; // New type for seat selection mode

interface BaggageOptions {
  extra: number;
  special: number;
}

// ── Helpers ────────────────────────────────────────────
const formatTime = (s: number) => `${Math.floor(s / 60).toString().padStart(2, '0')}:${(s % 60).toString().padStart(2, '0')}`;

export default function Booking() {
  const location = useLocation();
  const navigate = useNavigate();

  // Retrieves train data from Tickets.tsx via navigate state
  const train = location.state?.train as SelectedTrain | undefined;

  // ── Form state ──
  const [passenger, setPassenger] = useState<PassengerForm>({
    civilite: 'M',
    prenom: '',
    nom: '',
    dob: '',
  });

  const [contact, setContact] = useState<ContactForm>({
    email: '',
    telephone: '',
  });

  const { addToCart } = useCart();

  const [seatMode, setSeatMode] = useState<SeatMode>('random');
  const [isSeatMapOpen, setIsSeatMapOpen] = useState(false);
  const [specificSeats, setSpecificSeats] = useState<Seat[]>([]);
  
  const [baggage, setBaggage] = useState<BaggageOptions>({ extra: 0, special: 0 });
  const [message, setMessage] = useState<{ text: string; type: 'error' | 'success' | '' }>({ text: '', type: '' });
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    const autofillUserData = async () => {
      try {
        const response = await fetch('http://localhost:8000/api_user.php', {
          method: 'GET',
          credentials: 'include'
        });
        const data = await response.json();

        if (data.status === 'success') {
          // repmlire user
          setPassenger(prev => ({
            ...prev,
            civilite: data.user.civilite || 'M',
            prenom: data.user.prenom || '',
            nom: data.user.nom || '',
            dob: data.user.dob || ''
          }));
          
          // remplire contacts
          setContact(prev => ({
            ...prev,
            email: data.user.mail || '',
            telephone: data.user.telephone || ''
          }));
        }
      } catch (error) {
        console.log("Utilisateur non connecté ou erreur serveur");
      }
    };

    autofillUserData();
  }, []);

  // ── Timer ──
  const [timerSec] = useState(900);
  const timerStr = formatTime(timerSec);

  // ── Price ──
  const currentSeatPrice = seatMode === 'specific' ? 5 : 0; // +5€ if they want to choose a seat
  const baggagePrice = baggage.extra * 6.49 + baggage.special * 9.99;
  const basePrice = train?.price ?? 0;
  const total = basePrice + currentSeatPrice + baggagePrice;

  // ── Handlers ──
  const handlePassengerChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setPassenger(prev => ({ ...prev, [name]: value }));
  };

  const handleContactChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setContact(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async () => {
    if (!passenger.prenom || !passenger.nom) {
      setMessage({ text: 'Veuillez remplir le prénom et le nom du passager.', type: 'error' });
      return;
    }
    if (!contact.email) {
      setMessage({ text: 'Veuillez entrer votre adresse e-mail.', type: 'error' });
      return;
    }
    if (seatMode === 'specific' && specificSeats.length === 0) {
      setMessage({ text: 'Veuillez sélectionner votre place sur le plan interactif.', type: 'error' });
      return;
    }
    if(!train) {
      setMessage({ text: 'Train est invalide.', type: 'error' });
      return;
    }

    addToCart({
      id: Date.now().toString(),
      train,
      passenger,
      contact,
      seatMode,
      specificSeats,
      baggage,
      total
    });

    navigate('/cart');
  };

  return (
    <div className="booking-page">

      {/* ── TOPBAR ── */}
      <div className="topbar">
        <a href="/home" className="brand">
          <div className="brand-logo">
            <img src={logoSvg} alt="TNCF" />
          </div>
        </a>
        <div className="topbar-actions">
          <div className="session-timer-top">
            <img src={clockSvg} alt="" />
            Session expire dans
            <span className="timer-count">{timerStr}</span>
          </div>
          <a href="/login" className="cart-btn">
            <img src={persoWhiteSvg} alt="" />
            Connexion
          </a>
        </div>
      </div>

      {/* ── BODY ── */}
      <div className="booking-body">

        {/* ── LEFT COLUMN: FORM ── */}
        <div className="booking-form">

          <button className="booking-back" onClick={() => navigate('/tickets')}>
            ← Retour
          </button>

          {/* 1. PASSENGERS */}
          <div className="booking-section">
            <div className="booking-section-header">
              <div className="booking-step-num">1</div>
              <h2>Passagers</h2>
            </div>
            <p className="booking-section-sub">* Information obligatoire</p>

            <div className="booking-form-row">
              <div className="booking-form-group">
                <label className="booking-label">Civilité</label>
                <div className="booking-radio-group">
                  <label className={`booking-radio${passenger.civilite === 'M' ? ' active' : ''}`}>
                    <input
                      type="radio" name="civilite" value="M"
                      checked={passenger.civilite === 'M'}
                      onChange={handlePassengerChange}
                    /> M.
                  </label>
                  <label className={`booking-radio${passenger.civilite === 'Mme' ? ' active' : ''}`}>
                    <input
                      type="radio" name="civilite" value="Mme"
                      checked={passenger.civilite === 'Mme'}
                      onChange={handlePassengerChange}
                    /> Mme.
                  </label>
                </div>
              </div>
            </div>

            <div className="booking-form-row">
              <div className="booking-form-group">
                <label className="booking-label">Prénom *</label>
                <input
                  className="booking-input"
                  type="text" name="prenom"
                  placeholder="Jean"
                  value={passenger.prenom}
                  onChange={handlePassengerChange}
                />
              </div>
              <div className="booking-form-group">
                <label className="booking-label">Nom *</label>
                <input
                  className="booking-input"
                  type="text" name="nom"
                  placeholder="Dupont"
                  value={passenger.nom}
                  onChange={handlePassengerChange}
                />
              </div>
            </div>

            <div className="booking-form-row">
              <div className="booking-form-group">
                <label className="booking-label">Date de naissance</label>
                <input
                  className="booking-input"
                  type="date" name="dob"
                  value={passenger.dob}
                  onChange={handlePassengerChange}
                />
              </div>
            </div>
          </div>

          {/* 2. SEAT RESERVATION */}
          <div className="booking-section">
            <div className="booking-section-header">
              <div className="booking-step-num">2</div>
              <h2>Réservation de siège</h2>
            </div>

            <div className="booking-seat-mode-container" style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px', marginBottom: '20px' }}>
              {/* Random Seat Option */}
              <div
                className={`booking-seat-card ${seatMode === 'random' ? 'selected' : ''}`}
                onClick={() => { 
                  setSeatMode('random'); 
                  setSpecificSeats([]); 
                }}
                style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', padding: '24px', textAlign: 'center', height: '100%', cursor: 'pointer' }}
              >
                <div className="booking-seat-label">Place aléatoire</div>
                <div className="booking-seat-desc" style={{ marginBottom: '12px' }}>Attribution automatique</div>
                <div className="booking-seat-price">Inclus</div>
              </div>

              {/* Specific Seat Option */}
              <div
                className={`booking-seat-card ${seatMode === 'specific' ? 'selected' : ''}`}
                onClick={() => { 
                  setSeatMode('specific'); 
                  setIsSeatMapOpen(true); 
                }}
                style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', padding: '24px', textAlign: 'center', height: '100%', cursor: 'pointer' }}
              >
                <div className="booking-seat-label">Choisir sa place</div>
                <div className="booking-seat-desc" style={{ marginBottom: '12px' }}>Sur le plan interactif</div>
                <div className="booking-seat-price">+ 5€</div>
              </div>
            </div>

            {/* If the user has selected specific seats on the map */}
            {seatMode === 'specific' && specificSeats.length > 0 && (
              <div style={{ padding: '16px', border: '2px solid var(--navy)', backgroundColor: 'var(--light)', borderRadius: '12px', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <div>
                  <div style={{ fontWeight: 'bold', color: 'var(--navy)', marginBottom: '4px' }}>Siège(s) sélectionné(s) sur le plan :</div>
                  <div style={{ fontSize: '0.9rem', color: 'var(--gray)' }}>
                    {specificSeats.map(s => `Voiture ${s.wagon}, Place ${s.number}`).join(' | ')}
                  </div>
                </div>
                <button
                  onClick={() => setIsSeatMapOpen(true)}
                  style={{ background: 'var(--gold)', color: 'var(--navy)', padding: '8px 16px', borderRadius: '8px', border: 'none', fontWeight: 'bold', cursor: 'pointer' }}
                >
                  Modifier
                </button>
              </div>
            )}
          </div>

          {/* 3. OPTIONS */}
          <div className="booking-section">
            <div className="booking-section-header">
              <div className="booking-step-num">3</div>
              <h2>Options</h2>
            </div>

            <div className="booking-option-included">
              <div className="booking-option-icon">🧳</div>
              <div>
                <div className="booking-option-name">Inclus par personne</div>
                <div className="booking-option-desc">1 sac à main · 1 Bagage en soute 20 kg</div>
              </div>
            </div>

            {[
              { key: 'extra',   label: 'Bagage supplémentaire', desc: '20 kg · 80×50×30 cm', price: 6.49 },
              { key: 'special', label: 'Bagage spécial',        desc: '30 kg · 240 cm',      price: 9.99 },
            ].map(opt => (
              <div key={opt.key} className="booking-option-row">
                <div className="booking-option-info">
                  <div className="booking-option-name">{opt.label}</div>
                  <div className="booking-option-desc">{opt.desc}</div>
                </div>
                <div className="booking-option-right">
                  <div className="booking-option-price">+ {opt.price}€</div>
                  <div className="booking-counter">
                    <button
                      className="booking-counter-btn"
                      onClick={() => setBaggage(prev => ({
                        ...prev,
                        [opt.key]: Math.max(0, prev[opt.key as keyof BaggageOptions] - 1)
                      }))}
                    >−</button>
                    <span className="booking-counter-val">
                      {baggage[opt.key as keyof BaggageOptions]}
                    </span>
                    <button
                      className="booking-counter-btn"
                      onClick={() => setBaggage(prev => ({
                        ...prev,
                        [opt.key]: prev[opt.key as keyof BaggageOptions] + 1
                      }))}
                    >+</button>
                  </div>
                </div>
              </div>
            ))}
          </div>

          {/* 4. CONTACT */}
          <div className="booking-section">
            <div className="booking-section-header">
              <div className="booking-step-num">4</div>
              <h2>Contact</h2>
            </div>

            <div className="booking-form-row">
              <div className="booking-form-group">
                <label className="booking-label">E-mail *</label>
                <input
                  className="booking-input"
                  type="email" name="email"
                  placeholder="votre@email.com"
                  value={contact.email}
                  onChange={handleContactChange}
                />
              </div>
              <div className="booking-form-group">
                <label className="booking-label">Téléphone</label>
                <input
                  className="booking-input"
                  type="tel" name="telephone"
                  placeholder="+33 6 00 00 00 00"
                  value={contact.telephone}
                  onChange={handleContactChange}
                />
              </div>
            </div>
          </div>

  

          {/* MESSAGE */}
          {message.text && (
            <div className={`booking-message ${message.type === 'error' ? 'booking-message-error' : 'booking-message-success'}`}>
              {message.text}
            </div>
          )}

        </div>

        {/* ── RIGHT COLUMN: SUMMARY ── */}
        <aside className="booking-summary">
          <div className="booking-summary-title">Votre commande</div>

          <div className="booking-summary-timer">
            <img src={clockSvg} alt="" />
            <span className="timer-count">{timerStr}</span>
          </div>

          {train && (
            <div className="booking-summary-train">
              <div className="booking-summary-route">
                {train.from} → {train.to}
              </div>
              <div className="booking-summary-num">{train.num}</div>
              <div className="booking-summary-time">{train.dep}</div>
              <div className="booking-summary-class">
                {train.cls === '1' ? '1ère' : '2ème'} classe · 1 voyageur
              </div>
            </div>
          )}

          <div className="booking-summary-totals">
            <div className="booking-total-row">
              <span>Billet</span>
              <span>{basePrice}€</span>
            </div>
            {currentSeatPrice > 0 && (
              <div className="booking-total-row">
                <span>Choix de la place</span>
                <span>+{currentSeatPrice}€</span>
              </div>
            )}
            {baggagePrice > 0 && (
              <div className="booking-total-row">
                <span>Bagages</span>
                <span>+{baggagePrice.toFixed(2)}€</span>
              </div>
            )}
            <div className="booking-total-row booking-total-main">
              <span>Total (TTC)</span>
              <span>{total.toFixed(2)}€</span>
            </div>
          </div>

          <button
            className="booking-btn-pay"
            onClick={handleSubmit}
            disabled={isLoading}
          >
            {isLoading ? 'CHARGEMENT...' : 'Ajouter au panier'}
          </button>
        </aside>

      </div>
      {/* MODAL AT THE END */}
      <SeatMapModal 
        isOpen={isSeatMapOpen} 
        maxSeats={1}
        currentSelection={specificSeats}
        onClose={() => setIsSeatMapOpen(false)} 
        onConfirm={(seats) => {
          setSpecificSeats(seats);
          setIsSeatMapOpen(false);
          if (seats.length === 0) setSeatMode('random'); 
        }}
      />
    </div> 
  );
}