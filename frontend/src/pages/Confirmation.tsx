import { useLocation, useNavigate, Link } from 'react-router-dom';
import { useState, useEffect, useRef } from 'react';

import type { SelectedTrain } from '../SelectedTrain';
import type { PassengerForm } from '../PassengerForm';
import type { ContactForm } from '../ContactForm';

import '../assets/style/confirmation.css';
import { logoSvg, boxSvg, persoWhiteSvg } from '../assets/img/images';

// ── Types ──────────────────────────────────────────────
interface BookingState {
  train: SelectedTrain;
  passenger: PassengerForm;
  contact: ContactForm;
  orderNumber: string;
  total: number;
  assignedSeat: { wagon: number; number: string; type: string };
  arrivalTime: string;
}

export default function Confirmation() {
  const location = useLocation();
  const navigate = useNavigate();

  // Retrieve data from Booking.tsx via navigate state
  const booking = location.state?.booking as BookingState | undefined;

  const [isDownloading, setIsDownloading] = useState(false);
  const [downloadError, setDownloadError] = useState('');

  // ── User state ──
  const [user, setUser] = useState<{ prenom: string, nom: string } | null>(null);
  const [showUserMenu, setShowUserMenu] = useState(false);
  const menuRef = useRef<HTMLDivElement>(null);

  // Fallback if no data (direct access to the page)
  const orderNumber = booking?.orderNumber ?? 'TNCF-DEMO01';
  const train       = booking?.train;
  const passenger   = booking?.passenger;
  const total       = booking?.total ?? 0;
  const assignedSeat = booking?.assignedSeat ?? { wagon: 2, number: '12A', type: 'standard' };
  const arrivalTime  = booking?.arrivalTime ?? '10:00';

  // ── Check session on load ──
  useEffect(() => {
    const checkSession = async () => {
      try {
        const response = await fetch('http://localhost:8000/api_user.php', { credentials: 'include' });
        const data = await response.json();
        if (data.status === 'success') {
          setUser(data.user);
        }
      } catch (error) {
        console.log("User is not authenticated");
      }
    };
    checkSession();
  }, []);

  // ── Click outside listener for user menu ──
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      const target = event.target as Node;
      if (menuRef.current && !menuRef.current.contains(target)) {
        setShowUserMenu(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  // ── Logout function ──
  const handleLogout = async () => {
    try {
      await fetch('http://localhost:8000/api_logout.php', {
        method: 'POST',
        credentials: 'include'
      });
      setUser(null);
      setShowUserMenu(false);
      navigate('/login');
    } catch (error) {
      console.error('Logout error: ', error);
    }
  };

  // ── PDF Download ──────────────────────────────
  const handleDownloadPdf = async () => {
    setIsDownloading(true);
    setDownloadError('');

    try {
      const response = await fetch('http://localhost:8000/api_export_pdf.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          orderNumber,
          passenger: booking?.passenger,
          contact:   booking?.contact,
          train:     booking?.train,
          total:     booking?.total,
          assignedSeat, 
          arrivalTime
        }),
      });

      // Check if response is OK
      if (!response.ok) {
        throw new Error(`Server error : ${response.status} ${response.statusText}`);
      }

      // Check if Content-Type is a valid PDF
      const contentType = response.headers.get('Content-Type') ?? '';
      if (!contentType.includes('application/pdf')) {
        throw new Error("The server response is not a valid PDF.");
      }

      // Convert to blob and trigger download
      const blob = await response.blob();
      const url  = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href     = url;
      link.download = `ticket-${orderNumber}.pdf`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);

    } catch (error) {
      setDownloadError(error instanceof Error ? error.message : 'Unexpected error.');
    } finally {
      setIsDownloading(false);
    }
  };

  return (
    <div className="confirmation-page">
      {/* ── TOPBAR ── */}
      <div className="topbar">
        <Link to="/" className="brand">
          <div className="brand-logo"><img src={logoSvg} alt="TNCF" /></div>
        </Link>
        <ul className="nav-links">
            <li><Link to="/">Voyager</Link></li>
            <li><Link to="/tickets">Billets</Link></li>
            <li><Link to="/account">Compte</Link></li>
        </ul>
        <div className="topbar-actions">
          <Link to="/cart" className="cart-btn"><img src={boxSvg} alt="" />Panier<span className="cart-count">0</span></Link>
          
          {/* ── Dynamic Authentication Block ── */}
          {user ? (
            <div className="user-menu-container" ref={menuRef}>
                <button 
                    className="cart-btn" 
                    onClick={() => setShowUserMenu(!showUserMenu)}
                    style={{ background: 'none', border: 'none', cursor: 'pointer', fontFamily: "'DM Sans', sans-serif" }}
                >
                    <img src={persoWhiteSvg} alt="" />
                    {user.prenom} {user.nom}
                </button>

                {showUserMenu && (
                    <ul className="user-dropdown-menu" style={{ top: '100%', right: '0', marginTop: '15px' }}>
                        <li>
                            <Link to="/account" onClick={() => setShowUserMenu(false)}>Mon profil</Link>
                        </li>
                        <li>
                            <Link to="/edit-profile" onClick={() => setShowUserMenu(false)}>Paramètres</Link>
                        </li>
                        <hr />
                        <li>
                            <button onClick={handleLogout} style={{ color: '#e05252' }}>Déconnexion</button>
                        </li>
                    </ul>
                )}
            </div>
          ) : (
            <Link to="/login" className="cart-btn"><img src={persoWhiteSvg} alt="" />Connexion</Link>
          )}
        </div>
      </div>

      <div className="confirmation-body">
        <div className="confirmation-success-icon">✓</div>
        <div className="confirmation-eyebrow">Réservation confirmée</div>
        <h1 className="confirmation-title">Merci pour votre achat !</h1>
        <p className="confirmation-subtitle">
          Votre billet a été enregistré avec succès.
          {booking?.contact.email && (
            <> Un e-mail de confirmation vous a été envoyé à <strong>{booking.contact.email}</strong>.</>
          )}
        </p>

        <div className="confirmation-card">
          <div className="confirmation-card-header">
            <div>
              <div className="confirmation-order-label">Numéro de commande</div>
              <div className="confirmation-order-num">{orderNumber}</div>
            </div>
            <div className="confirmation-badge">Confirmé</div>
          </div>

          <div className="confirmation-ticket">
            <div className="confirmation-station">
              <div className="confirmation-time">{train?.dep ?? '08:01'}</div>
              <div className="confirmation-city">{train?.from ?? 'Paris'}</div>
              <div className="confirmation-station-label">Gare de départ</div>
            </div>

            <div className="confirmation-line">
              <div className="confirmation-train-num">{train?.num ?? 'TGV INOUI 6603'}</div>
              <div className="confirmation-track">
                <div className="confirmation-dot"></div>
                <div className="confirmation-rail"></div>
                <div className="confirmation-dot"></div>
              </div>
              <div className="confirmation-direct">Direct</div>
            </div>

            <div className="confirmation-station confirmation-station-right">
              <div className="confirmation-time">{arrivalTime}</div>
              <div className="confirmation-city">{train?.to ?? 'Lyon'}</div>
              <div className="confirmation-station-label">Gare d'arrivée</div>
            </div>
          </div>

          <div className="confirmation-details">
            <div className="confirmation-detail-item">
              <div className="confirmation-detail-label">Passager</div>
              <div className="confirmation-detail-value">
                {passenger ? `${passenger.civilite}. ${passenger.prenom} ${passenger.nom}` : '—'}
              </div>
            </div>
            <div className="confirmation-detail-item">
              <div className="confirmation-detail-label">Classe & Place</div>
              <div className="confirmation-detail-value">
                {train?.cls === '1' ? '1ère' : '2ème'} cl. · <span style={{color:'var(--gold)'}}>Voiture {assignedSeat.wagon}, Place {assignedSeat.number}</span>
              </div>
            </div>
            <div className="confirmation-detail-item">
              <div className="confirmation-detail-label">Total payé</div>
              <div className="confirmation-detail-value confirmation-price">
                {total > 0 ? `${total.toFixed(2)}€` : '—'}
              </div>
            </div>
          </div>
        </div>

        <div className="confirmation-actions">
          <button className="confirmation-btn-pdf" onClick={handleDownloadPdf} disabled={isDownloading}>
            {isDownloading ? '⏳ Génération en cours...' : <><span className="confirmation-btn-icon">⬇</span>Télécharger le billet (PDF)</>}
          </button>
          <button className="confirmation-btn-secondary" onClick={() => navigate('/tickets')}>Réserver un autre billet</button>
        </div>

        {downloadError && <div className="confirmation-error">⚠ {downloadError}</div>}
      </div>
    </div>
  );
}