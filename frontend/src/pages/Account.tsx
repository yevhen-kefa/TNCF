import React, { useState, useRef, useEffect } from 'react';
import '../assets/style/count.css';
import '../assets/img/images';
import TicketCard from '../components/TicketCard';
import type { TicketData } from '../components/TicketCard';

import { alertSvg, arriveSvg, arrowLeftSvg, arrowRightSvg, boxGoldSvg, cartSvg, departSvg, layerSvg, logoSvg, mailSvg, passSvg, persoWhiteSvg, shielSvg, tickeSvg, boxSvg } from '../assets/img/images';
import { useCart } from '../context/CartContext';


export default function Account() {
  const [userFullName, setUserFullName] = useState('Chargement... ');
  const [userEmail, setUserEmail] = useState('Chargement... ');
  const [tickets, setTickets] = useState<TicketData[]>([]);

  //for checking session
  const [isLoading, setIsLoading] = useState(true);

  //function for checking session
useEffect(() => {
  const fetchData = async () => {
    try {
      // 1. Fetch User Data
      const userRes = await fetch('http://localhost:8000/api_user.php', { credentials: 'include' });
      const userData = await userRes.json();

      if (userData.status === 'success') {
        setUserFullName(`${userData.user.prenom} ${userData.user.nom}`);
        setUserEmail(userData.user.mail);

        // 2. Fetch Tickets
        const ticketsRes = await fetch('http://localhost:8000/api_tickets.php', { credentials: 'include' });
        const ticketsData = await ticketsRes.json();

        if (ticketsData.status === 'success') {
          setTickets(ticketsData.tickets);
        }

        setIsLoading(false);
      } else {
        window.location.href = "/login";
      }
    } catch(error) {
      console.error("Error fetching data: ", error);
      window.location.href = '/login';
    }
  };
  fetchData();
}, []);

  const scrollRef = useRef<HTMLDivElement>(null);
  const [atStart, setAtStart] = useState(true);
  const { cartItems } = useCart();
  const [atEnd, setAtEnd] = useState(false);

  const [isDragging, setIsDragging] = useState(false);
  const [startX, setStartX] = useState(0);
  const [startScrollLeft, setStartScrollLeft] = useState(0);

  const handleScroll = () => {
    if (scrollRef.current) {
      const { scrollLeft, scrollWidth, clientWidth } = scrollRef.current;
      setAtStart(scrollLeft <= 10);
      setAtEnd(scrollLeft + clientWidth >= scrollWidth - 10);
    }
  };

  useEffect(() => {
    handleScroll();
  }, []);

  const scrollByAmount = (amount: number) => {
    if (scrollRef.current) {
      scrollRef.current.scrollBy({ left: amount, behavior: 'smooth' });
    }
  };

  const handleMouseDown = (e: React.MouseEvent) => {
    setIsDragging(true);
    if (scrollRef.current) {
      setStartX(e.pageX - scrollRef.current.offsetLeft);
      setStartScrollLeft(scrollRef.current.scrollLeft);
    }
  };

  const handleMouseLeave = () => setIsDragging(false);
  const handleMouseUp = () => setIsDragging(false);

  const handleMouseMove = (e: React.MouseEvent) => {
    if (!isDragging || !scrollRef.current) return;
    e.preventDefault();
    const x = e.pageX - scrollRef.current.offsetLeft;
    const walk = x - startX;
    scrollRef.current.scrollLeft = startScrollLeft - walk;
  };

  const handleLogout = async () => {
    // Implement logout logic if backend handles it
    try{
      const response = await fetch('http://localhost:8000/api_logout.php', {
        method: 'POST',
        credentials: 'include'
      });

      const data = await response.json();

      if (data.status === 'success'){
        window.location.href = '/login';
      }else{
        window.location.href = '/login';
      }
    } catch(error){
      console.error('Error deconaction: ', error);
      window.location.href = '/login';
    }
  };

  return (
    <>
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
            Panier <span className="cart-count" style={{ background: 'var(--gold)', padding: '2px 6px', borderRadius: '12px', fontSize: '0.75rem', color: 'var(--navy)' }}>{cartItems.length}</span>
          </a>
          <a href="/tickets" className="btn-nav-outline">+ Réserver</a>
        </div>
      </nav>

      <div className="page-band">
        <div className="page-band-inner">
          <div className="page-eyebrow">Espace personnel</div>
          <h1 className="page-title">Mon Compte</h1>
        </div>
      </div>

      <div className="page-content">
        <div className="profile-card">
          <div className="avatar-wrap">
            <div className="avatar">JD</div>
            <div className="avatar-status"></div>
          </div>

          <div className="profile-info">
            <div className="profile-name">{userFullName}</div>
            <div className="profile-email">
              <img src={mailSvg} alt="" />
              {userEmail}
            </div>
            <div className="profile-badges">
              <span className="profile-badge badge-member">
                <img src={passSvg} alt="" />
                Membre Gold
              </span>
              <span className="profile-badge badge-verified">
                <img src={shielSvg} alt="" />
                Compte vérifié
              </span>
            </div>
          </div>

          {/* Statistiques */}
          <div className="profile-stats">
            <div className="stat-item">
              <div className="stat-num">12</div>
              <div className="stat-label">Trajets</div>
            </div>
            <div className="stat-item">
              <div className="stat-num">4</div>
              <div className="stat-label">À venir</div>
            </div>
            <div className="stat-item">
              <div className="stat-num">2 340</div>
              <div className="stat-label">km parcourus</div>
            </div>
          </div>

          {/* Bouton modifier */}
          <a href="#" className="btn-edit">
            <img src={persoWhiteSvg} alt="" />
            Modifier le profil
          </a>
        </div>

        {/* GRILLE DU MENU COMPTE */}
        <div className="account-grid">
          <a href="#" className="account-menu-card">
            <div className="menu-card-icon">
              <img src={tickeSvg} alt="" />
            </div>
            <div className="menu-card-text">
              <h4>Mes Réservations</h4>
              <p>Consulter et gérer vos billets</p>
            </div>
            <span className="menu-card-arrow">
              <img src={arriveSvg} alt="→" />
            </span>
          </a>

          <a href="#" className="account-menu-card">
            <div className="menu-card-icon">
              <img src={cartSvg} alt="" />
            </div>
            <div className="menu-card-text">
              <h4>Moyens de paiement</h4>
              <p>Cartes bancaires enregistrées</p>
            </div>
            <span className="menu-card-arrow">
              <img src={arriveSvg} alt="→" />
            </span>
          </a>

          <a href="#" className="account-menu-card">
            <div className="menu-card-icon">
              <img src={boxGoldSvg} alt="" />
            </div>
            <div className="menu-card-text">
              <h4>Mes avantages</h4>
              <p>Codes promo et réductions</p>
            </div>
            <span className="menu-card-arrow">
              <img src={arriveSvg} alt="→" />
            </span>
          </a>

          <a href="#" className="account-menu-card">
            <div className="menu-card-icon">
              <img src={alertSvg} alt="" />
            </div>
            <div className="menu-card-text">
              <h4>Alertes &amp; Notifications</h4>
              <p>SMS et e-mails de suivi</p>
            </div>
            <span className="menu-card-arrow">
              <img src={arriveSvg} alt="→" />
            </span>
          </a>

          <a href="#" className="account-menu-card">
            <div className="menu-card-icon">
              <img src={shielSvg} alt="" />
            </div>
            <div className="menu-card-text">
              <h4>Sécurité</h4>
              <p>Mot de passe et authentification</p>
            </div>
            <span className="menu-card-arrow">
              <img src={arriveSvg} alt="→" />
            </span>
          </a>

          <a href="#" className="account-menu-card">
            <div className="menu-card-icon">
              <img src={layerSvg} alt="" />
            </div>
            <div className="menu-card-text">
              <h4>Paramètres</h4>
              <p>Langue, accessibilité, données</p>
            </div>
            <span className="menu-card-arrow">
              <img src={arriveSvg} alt="→" />
            </span>
          </a>
        </div>

        {/* MES BILLETS */}
        <div className="section-header">
          <div>
            <div className="section-eyebrow">Historique &amp; À venir</div>
            <h2 className="section-title">Mes Billets</h2>
          </div>
          <span className="section-count">12 billets</span>
        </div>

        <div className="tickets-outer">
          <button
            className="scroll-arrow left"
            aria-label="Défiler à gauche"
            onClick={() => scrollByAmount(-310)}
            style={{ opacity: atStart ? 0.35 : 1, pointerEvents: atStart ? 'none' : 'auto' }}
          >
            <img src={arrowLeftSvg} alt="←" />
          </button>

          <div
            className="tickets-scroll"
            ref={scrollRef}
            onScroll={handleScroll}
            onMouseDown={handleMouseDown}
            onMouseLeave={handleMouseLeave}
            onMouseUp={handleMouseUp}
            onMouseMove={handleMouseMove}
            style={{ userSelect: isDragging ? 'none' : 'auto' }}
          >
            {tickets.length === 0 && !isLoading ? (
              <div style={{ padding: '40px', color: '#8a8f9e', textAlign: 'center', width: '100%', fontSize: '1.1rem' }}>
                Vous n'avez pas encore de billets.
              </div>
            ) : (
              tickets.map((ticket) => (
                <TicketCard 
                  key={ticket.id} 
                  ticket={ticket} 
                  passengerName={userFullName} 
                />
              ))
            )}
          </div>

          <button
            className="scroll-arrow right"
            aria-label="Défiler à droite"
            onClick={() => scrollByAmount(310)}
            style={{ opacity: atEnd ? 0.35 : 1, pointerEvents: atEnd ? 'none' : 'auto' }}
          >
            <img src={arrowRightSvg} alt="→" />
          </button>
        </div>

        <div className="logout-section">
          <p className="logout-note">Connecté en tant que {userEmail}</p>
          <button className="btn-logout" onClick={handleLogout}>
            Déconnexion
          </button>
        </div>
      </div>
    </>
  );
}
