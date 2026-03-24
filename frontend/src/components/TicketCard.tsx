import React from 'react';
import '../assets/style/ticketCard.css';
import departSvg from '../assets/img/depart.svg';

export interface TicketProps {
  travelClass: string;
  trainNum: string;
  departureCity: string;
  duration: string;
  arrivalCity: string;
  date: string;
  departureTime: string;
  travelerName: string;
  seat: string;
  status: 'upcoming' | 'used' | 'cancelled';
  price: string;
}

export default function TicketCard(props: TicketProps) {
  const getStatusLabel = () => {
    switch(props.status) {
      case 'upcoming': return '● À venir';
      case 'used': return '● Utilisé';
      case 'cancelled': return '● Annulé';
      default: return '';
    }
  };

  const getStatusClass = () => {
    switch(props.status) {
      case 'upcoming': return 'status-upcoming';
      case 'used': return 'status-used';
      case 'cancelled': return 'status-cancelled';
      default: return '';
    }
  };

  return (
    <div className="ticket-card">
      <div className="ticket-header">
        <div className="ticket-class-tag">{props.travelClass}</div>
        <div className="ticket-train-num">
          <img src={departSvg} alt="" />
          {props.trainNum}
        </div>
        <div className="ticket-route">
          <div className="ticket-city">{props.departureCity}</div>
          <div className="ticket-arrow-wrap">
            <div className="ticket-arrow-line"></div>
            <div className="ticket-dur">{props.duration}</div>
          </div>
          <div className="ticket-city">{props.arrivalCity}</div>
        </div>
      </div>
      <div className="ticket-body">
        <div className="ticket-punch">
          <div className="punch-hole"></div>
          <div className="punch-line"></div>
          <div className="punch-hole"></div>
        </div>
        <div className="ticket-details">
          <div><div className="detail-label">Date</div><div className="detail-value">{props.date}</div></div>
          <div><div className="detail-label">Départ</div><div className="detail-value">{props.departureTime}</div></div>
          <div><div className="detail-label">Voyageur</div><div className="detail-value">{props.travelerName}</div></div>
          <div><div className="detail-label">Siège</div><div className="detail-value">{props.seat}</div></div>
        </div>
        <div className="ticket-footer">
          <span className={`ticket-status ${getStatusClass()}`}>{getStatusLabel()}</span>
          <span className="ticket-price">{props.price}</span>
        </div>
      </div>
    </div>
  );
}
