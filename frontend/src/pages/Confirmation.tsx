import { useLocation, useNavigate } from 'react-router-dom';
import { useState } from 'react';

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
}

export default function Confirmation() {
  const location = useLocation();
  const navigate = useNavigate();

  // Récupère les données depuis Booking.tsx via navigate state
  const booking = location.state?.booking as BookingState | undefined;

  const [isDownloading, setIsDownloading] = useState(false);
  const [downloadError, setDownloadError] = useState('');

  // Fallback si pas de données (accès direct à la page)
  const orderNumber = booking?.orderNumber ?? 'TNCF-DEMO01';
  const train       = booking?.train;
  const passenger   = booking?.passenger;
  const total       = booking?.total ?? 0;

  // ── Téléchargement PDF ──────────────────────────────
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
        }),
      });

      // Vérifie que la réponse est OK
      if (!response.ok) {
        throw new Error(`Erreur serveur : ${response.status} ${response.statusText}`);
      }

      // Vérifie que le Content-Type est bien PDF
      const contentType = response.headers.get('Content-Type') ?? '';
      if (!contentType.includes('application/pdf')) {
        throw new Error("La réponse du serveur n'est pas un PDF valide.");
      }

      // Convertit en blob et déclenche le téléchargement
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
      if (error instanceof TypeError) {
        // Erreur réseau (pas de connexion, CORS, etc.)
        setDownloadError('Impossible de contacter le serveur. Vérifiez votre connexion.');
      } else if (error instanceof Error) {
        setDownloadError(error.message);
      } else {
        setDownloadError('Une erreur inattendue est survenue.');
      }
    } finally {
      setIsDownloading(false);
    }
  };

  return (
    <div className="confirmation-page">

      {/* ── TOPBAR ── */}
      <div className="topbar">
        <a href="/" className="brand">
          <div className="brand-logo">
            <img src={logoSvg} alt="TNCF" />
          </div>
        </a>
        <div className="topbar-actions">
          <a href="#" className="cart-btn">
            <img src={boxSvg} alt="" />
            Panier
            <span className="cart-count">0</span>
          </a>
          <a href="/login" className="cart-btn">
            <img src={persoWhiteSvg} alt="" />
            Connexion
          </a>
        </div>
      </div>

      {/* ── CONTENU PRINCIPAL ── */}
      <div className="confirmation-body">

        {/* Icône succès */}
        <div className="confirmation-success-icon">✓</div>

        <div className="confirmation-eyebrow">Réservation confirmée</div>
        <h1 className="confirmation-title">Merci pour votre achat !</h1>
        <p className="confirmation-subtitle">
          Votre billet a été enregistré avec succès.
          {booking?.contact.email && (
            <> Un e-mail de confirmation vous a été envoyé à <strong>{booking.contact.email}</strong>.</>
          )}
        </p>

        {/* ── CARTE DU BILLET ── */}
        <div className="confirmation-card">

          {/* En-tête sombre */}
          <div className="confirmation-card-header">
            <div>
              <div className="confirmation-order-label">Numéro de commande</div>
              <div className="confirmation-order-num">{orderNumber}</div>
            </div>
            <div className="confirmation-badge">✓ Confirmé</div>
          </div>

          {/* Visuel du trajet */}
          <div className="confirmation-ticket">

            <div className="confirmation-station">
              <div className="confirmation-time">{train?.dep ?? '08:01'}</div>
              <div className="confirmation-city">{train?.from ?? 'Paris'}</div>
              <div className="confirmation-station-label">Gare de Lyon</div>
            </div>

            <div className="confirmation-line">
              <div className="confirmation-train-num">
                {train?.num ?? 'TGV INOUI 6603'}
              </div>
              <div className="confirmation-track">
                <div className="confirmation-dot"></div>
                <div className="confirmation-rail"></div>
                <div className="confirmation-dot"></div>
              </div>
              <div className="confirmation-direct">✓ Direct</div>
            </div>

            <div className="confirmation-station confirmation-station-right">
              <div className="confirmation-time">Arrivée</div>
              <div className="confirmation-city">{train?.to ?? 'Lyon'}</div>
              <div className="confirmation-station-label">Part-Dieu</div>
            </div>

          </div>

          {/* Détails passager / classe / prix */}
          <div className="confirmation-details">
            <div className="confirmation-detail-item">
              <div className="confirmation-detail-label">Passager</div>
              <div className="confirmation-detail-value">
                {passenger
                  ? `${passenger.civilite}. ${passenger.prenom} ${passenger.nom}`
                  : '—'}
              </div>
            </div>
            <div className="confirmation-detail-item">
              <div className="confirmation-detail-label">Classe</div>
              <div className="confirmation-detail-value">
                {train?.cls === '1' ? '1ère classe' : '2ème classe'}
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

        {/* ── ACTIONS ── */}
        <div className="confirmation-actions">

          <button
            className="confirmation-btn-pdf"
            onClick={handleDownloadPdf}
            disabled={isDownloading}
          >
            {isDownloading ? (
              '⏳ Génération en cours...'
            ) : (
              <>
                <span className="confirmation-btn-icon">⬇</span>
                Télécharger le billet (PDF)
              </>
            )}
          </button>

          <button
            className="confirmation-btn-secondary"
            onClick={() => navigate('/tickets')}
          >
            Réserver un autre billet
          </button>

        </div>

        {/* Erreur téléchargement */}
        {downloadError && (
          <div className="confirmation-error">
            ⚠ {downloadError}
          </div>
        )}

      </div>
    </div>
  );
}
