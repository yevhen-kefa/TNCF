import React from 'react';
import { departSvg } from '../assets/img/images';

export interface TicketData {
  id: string;
  depart: string;
  arriver: string;
  date_depart: string;
  temps_arriver: string;
  prix: number;
  wagon: string;
  place: string;
  train_num: string;
  status: 'upcoming' | 'used' | 'cancelled';
}

interface TicketCardProps {
  ticket: TicketData;
  passengerName: string;
}

export default function TicketCard({ ticket, passengerName }: TicketCardProps) {
  return (
    <div className="ticket-card">
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
          <div><div className="detail-label">Date</div><div className="detail-value">{ticket.date_depart}</div></div>
          <div><div className="detail-label">Départ</div><div className="detail-value">--:--</div></div>
          <div><div className="detail-label">Voyageur</div><div className="detail-value">{passengerName}</div></div>
          <div><div className="detail-label">Siège</div><div className="detail-value">Voiture {ticket.wagon} · {ticket.place}</div></div>
        </div>
        <div className="ticket-footer">
          <span className={`ticket-status status-${ticket.status}`}>
            ● {ticket.status === 'upcoming' ? 'À venir' : ticket.status === 'used' ? 'Utilisé' : 'Annulé'}
          </span>
          <span className="ticket-price">{ticket.prix}€</span>
        </div>
      </div>
    </div>
  );
}