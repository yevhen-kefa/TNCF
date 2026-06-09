import React from 'react';
import { departSvg } from '../assets/img/images';
import '../assets/style/ticketCard.css';

export interface TicketData {
  id: string;
  depart: string;
  arriver: string;
  date_depart: string;
  heure_depart: string;
  temps_arriver: string;
  prix: number;
  wagon: string;
  place: string;
  train_num: string;
  orderNumber: string;
  status: 'upcoming' | 'used' | 'cancelled';
}

interface TicketCardProps {
  ticket: TicketData;
  passengerName: string;
  onClick?: () => void; // Added optional onClick event handler
}

export default function TicketCard({ ticket, passengerName, onClick }: TicketCardProps) {
  // Format date from YYYY-MM-DD to DD/MM/YYYY if needed
  const formatDate = (raw: string) => {
    if (!raw || raw === '—') return '—';
    if (raw.includes('/')) return raw; // already DD/MM/YYYY
    const [y, m, d] = raw.split('-');
    return d && m && y ? `${d}/${m}/${y}` : raw;
  };

  const statusLabel = {
    upcoming:  'À venir',
    used:      'Utilisé',
    cancelled: 'Annulé',
  }[ticket.status];

  return (
    // Added onClick triggering and hover cursor style pointers
    <div className="ticket-card" onClick={onClick} style={{ cursor: onClick ? 'pointer' : 'default' }}>
      <div className="ticket-header">
        <div className="ticket-class-tag">2ème</div>
        <div className="ticket-train-num">
          <img src={departSvg} alt="" />
          {ticket.train_num}
        </div>
        <div className="ticket-route">
          <div className="ticket-city" style={{ textTransform: 'capitalize' }}>{ticket.depart}</div>
          <div className="ticket-arrow-wrap">
            <div className="ticket-arrow-line"></div>
            <div className="ticket-dur">{ticket.temps_arriver}</div>
          </div>
          <div className="ticket-city" style={{ textTransform: 'capitalize' }}>{ticket.arriver}</div>
        </div>
      </div>

      <div className="ticket-body">
        <div className="ticket-punch">
          <div className="punch-hole"></div>
          <div className="punch-line"></div>
          <div className="punch-hole"></div>
        </div>

        <div className="ticket-details">
          <div>
            <div className="detail-label">Date</div>
            <div className="detail-value">{formatDate(ticket.date_depart)}</div>
          </div>
          <div>
            <div className="detail-label">Départ</div>
            <div className="detail-value">{ticket.heure_depart || '--:--'}</div>
          </div>
          <div>
            <div className="detail-label">Voyageur</div>
            <div className="detail-value">{passengerName}</div>
          </div>
          <div>
            <div className="detail-label">Siège</div>
            <div className="detail-value">Voiture {ticket.wagon} · {ticket.place}</div>
          </div>
          {ticket.orderNumber && ticket.orderNumber !== '—' && (
            <div style={{ gridColumn: '1 / -1' }}>
              <div className="detail-label">N° commande</div>
              <div className="detail-value" style={{ fontSize: '0.75rem', letterSpacing: '0.05em' }}>
                {ticket.orderNumber}
              </div>
            </div>
          )}
        </div>

        <div className="ticket-footer">
          <span className={`ticket-status status-${ticket.status}`}>
            ● {statusLabel}
          </span>
          <span className="ticket-price">{ticket.prix}€</span>
        </div>
      </div>
    </div>
  );
}