import React, { useState, useRef, useEffect } from 'react';
import '../assets/style/count.css';

import logoSvg from '../assets/img/logo.svg';
import mailSvg from '../assets/img/mail.svg';
import passSvg from '../assets/img/pass.svg';
import shieldSvg from '../assets/img/shield.svg';
import personWhiteSvg from '../assets/img/person_white.svg';
import ticketSvg from '../assets/img/ticket.svg';
import cartSvg from '../assets/img/cart.svg';
import boxGSvg from '../assets/img/box_g.svg';
import alertSvg from '../assets/img/alert.svg';
import layerSvg from '../assets/img/layer.svg';
import arriveSvg from '../assets/img/arrive.svg';
import departSvg from '../assets/img/depart.svg';
import arrowLSvg from '../assets/img/arrow_l.svg';
import arrowRSvg from '../assets/img/arrow_r.svg';

export default function Account() {
  const [userFullName, setUserFullName] = useState('Chargement... ');
  const [userEmail, setUserEmail] = useState('Chargement... ');

  //for checking session
  const [isLoading, setIsLoading] = useState(true);

  //function for checking session
  useEffect(() => {
    const checkSession = async () => {
      try{
        const response = await fetch('http://localhost:8000/api_user.php', {
          method: 'GET',
          credentials: 'include'
        });

        const data = await response.json();

        if(data.status === 'success'){
          setUserFullName(`${data.user.prenom} ${data.user.nom}`);
          setUserEmail(data.user.mail);
          setIsLoading(false);
        }else{
          //session was closed
          window.location.href = "/login";
        }
      }catch(error){
        console.error("Error checking session: ", error);
        window.location.href = '/login';
      }
    };
    checkSession();
  }, [])


  const scrollRef = useRef<HTMLDivElement>(null);
  const [atStart, setAtStart] = useState(true);
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
        <a href="/" className="brand">
          <div className="brand-logo">
            <img src={logoSvg} alt="Logo TNCF" />
          </div>
        </a>

        <ul className="nav-links">
          <li><a href="/">Voyager</a></li>
          <li><a href="/tickets">Billets</a></li>
          <li><a href="/account" className="active">Compte</a></li>
        </ul>
        <div className="nav-actions">
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
                <img src={shieldSvg} alt="" />
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
            <img src={personWhiteSvg} alt="" />
            Modifier le profil
          </a>
        </div>

        {/* GRILLE DU MENU COMPTE */}
        <div className="account-grid">
          <a href="#" className="account-menu-card">
            <div className="menu-card-icon">
              <img src={ticketSvg} alt="" />
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
              <img src={boxGSvg} alt="" />
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
              <img src={shieldSvg} alt="" />
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
            <img src={arrowLSvg} alt="←" />
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

            {/* BILLET 1 */}
            <div className="ticket-card">
              <div className="ticket-header">
                <div className="ticket-class-tag">1ère</div>
                <div className="ticket-train-num">
                  <img src={departSvg} alt="" />
                  TGV 6601
                </div>
                <div className="ticket-route">
                  <div className="ticket-city">Paris</div>
                  <div className="ticket-arrow-wrap">
                    <div className="ticket-arrow-line"></div>
                    <div className="ticket-dur">1h 55min</div>
                  </div>
                  <div className="ticket-city">Lyon</div>
                </div>
              </div>
              <div className="ticket-body">
                <div className="ticket-punch">
                  <div className="punch-hole"></div>
                  <div className="punch-line"></div>
                  <div className="punch-hole"></div>
                </div>
                <div className="ticket-details">
                  <div><div className="detail-label">Date</div><div className="detail-value">18 mars 2026</div></div>
                  <div><div className="detail-label">Départ</div><div className="detail-value">06:47</div></div>
                  <div><div className="detail-label">Voyageur</div><div className="detail-value">J. Dupont</div></div>
                  <div><div className="detail-label">Siège</div><div className="detail-value">Voiture 4 · 12A</div></div>
                </div>
                <div className="ticket-footer">
                  <span className="ticket-status status-upcoming">● À venir</span>
                  <span className="ticket-price">89€</span>
                </div>
              </div>
            </div>

            {/* BILLET 2 */}
            <div className="ticket-card">
              <div className="ticket-header">
                <div className="ticket-class-tag">2ème</div>
                <div className="ticket-train-num">
                  <img src={departSvg} alt="" />
                  TGV 6820
                </div>
                <div className="ticket-route">
                  <div className="ticket-city">Paris</div>
                  <div className="ticket-arrow-wrap">
                    <div className="ticket-arrow-line"></div>
                    <div className="ticket-dur">3h 05min</div>
                  </div>
                  <div className="ticket-city">Marseille</div>
                </div>
              </div>
              <div className="ticket-body">
                <div className="ticket-punch">
                  <div className="punch-hole"></div>
                  <div className="punch-line"></div>
                  <div className="punch-hole"></div>
                </div>
                <div className="ticket-details">
                  <div><div className="detail-label">Date</div><div className="detail-value">22 mars 2026</div></div>
                  <div><div className="detail-label">Départ</div><div className="detail-value">10:15</div></div>
                  <div><div className="detail-label">Voyageur</div><div className="detail-value">J. Dupont</div></div>
                  <div><div className="detail-label">Siège</div><div className="detail-value">Voiture 7 · 33B</div></div>
                </div>
                <div className="ticket-footer">
                  <span className="ticket-status status-upcoming">● À venir</span>
                  <span className="ticket-price">45€</span>
                </div>
              </div>
            </div>

            {/* BILLET 3 */}
            <div className="ticket-card">
              <div className="ticket-header">
                <div className="ticket-class-tag">2ème</div>
                <div className="ticket-train-num">
                  <img src={departSvg} alt="" />
                  TGV 7214
                </div>
                <div className="ticket-route">
                  <div className="ticket-city">Bordeaux</div>
                  <div className="ticket-arrow-wrap">
                    <div className="ticket-arrow-line"></div>
                    <div className="ticket-dur">2h 04min</div>
                  </div>
                  <div className="ticket-city">Paris</div>
                </div>
              </div>
              <div className="ticket-body">
                <div className="ticket-punch">
                  <div className="punch-hole"></div>
                  <div className="punch-line"></div>
                  <div className="punch-hole"></div>
                </div>
                <div className="ticket-details">
                  <div><div className="detail-label">Date</div><div className="detail-value">28 févr. 2026</div></div>
                  <div><div className="detail-label">Départ</div><div className="detail-value">14:30</div></div>
                  <div><div className="detail-label">Voyageur</div><div className="detail-value">J. Dupont</div></div>
                  <div><div className="detail-label">Siège</div><div className="detail-value">Voiture 2 · 08C</div></div>
                </div>
                <div className="ticket-footer">
                  <span className="ticket-status status-used">● Utilisé</span>
                  <span className="ticket-price">29€</span>
                </div>
              </div>
            </div>

            {/* BILLET 4 */}
            <div className="ticket-card">
              <div className="ticket-header">
                <div className="ticket-class-tag">1ère</div>
                <div className="ticket-train-num">
                  <img src={departSvg} alt="" />
                  TGV 5503
                </div>
                <div className="ticket-route">
                  <div className="ticket-city">Paris</div>
                  <div className="ticket-arrow-wrap">
                    <div className="ticket-arrow-line"></div>
                    <div className="ticket-dur">1h 02min</div>
                  </div>
                  <div className="ticket-city">Lille</div>
                </div>
              </div>
              <div className="ticket-body">
                <div className="ticket-punch">
                  <div className="punch-hole"></div>
                  <div className="punch-line"></div>
                  <div className="punch-hole"></div>
                </div>
                <div className="ticket-details">
                  <div><div className="detail-label">Date</div><div className="detail-value">14 févr. 2026</div></div>
                  <div><div className="detail-label">Départ</div><div className="detail-value">08:00</div></div>
                  <div><div className="detail-label">Voyageur</div><div className="detail-value">J. Dupont</div></div>
                  <div><div className="detail-label">Siège</div><div className="detail-value">Voiture 1 · 02A</div></div>
                </div>
                <div className="ticket-footer">
                  <span className="ticket-status status-used">● Utilisé</span>
                  <span className="ticket-price">59€</span>
                </div>
              </div>
            </div>

            {/* BILLET 5 */}
            <div className="ticket-card">
              <div className="ticket-header">
                <div className="ticket-class-tag">2ème</div>
                <div className="ticket-train-num">
                  <img src={departSvg} alt="" />
                  TGV 6340
                </div>
                <div className="ticket-route">
                  <div className="ticket-city">Lyon</div>
                  <div className="ticket-arrow-wrap">
                    <div className="ticket-arrow-line"></div>
                    <div className="ticket-dur">1h 45min</div>
                  </div>
                  <div className="ticket-city">Nice</div>
                </div>
              </div>
              <div className="ticket-body">
                <div className="ticket-punch">
                  <div className="punch-hole"></div>
                  <div className="punch-line"></div>
                  <div className="punch-hole"></div>
                </div>
                <div className="ticket-details">
                  <div><div className="detail-label">Date</div><div className="detail-value">05 janv. 2026</div></div>
                  <div><div className="detail-label">Départ</div><div className="detail-value">11:22</div></div>
                  <div><div className="detail-label">Voyageur</div><div className="detail-value">J. Dupont</div></div>
                  <div><div className="detail-label">Siège</div><div className="detail-value">Voiture 5 · 17D</div></div>
                </div>
                <div className="ticket-footer">
                  <span className="ticket-status status-cancelled">● Annulé</span>
                  <span className="ticket-price">38€</span>
                </div>
              </div>
            </div>

            {/* BILLET 6 */}
            <div className="ticket-card">
              <div className="ticket-header">
                <div className="ticket-class-tag">1ère</div>
                <div className="ticket-train-num">
                  <img src={departSvg} alt="" />
                  TGV 9901
                </div>
                <div className="ticket-route">
                  <div className="ticket-city">Paris</div>
                  <div className="ticket-arrow-wrap">
                    <div className="ticket-arrow-line"></div>
                    <div className="ticket-dur">2h 00min</div>
                  </div>
                  <div className="ticket-city">Nantes</div>
                </div>
              </div>
              <div className="ticket-body">
                <div className="ticket-punch">
                  <div className="punch-hole"></div>
                  <div className="punch-line"></div>
                  <div className="punch-hole"></div>
                </div>
                <div className="ticket-details">
                  <div><div className="detail-label">Date</div><div className="detail-value">30 mars 2026</div></div>
                  <div><div className="detail-label">Départ</div><div className="detail-value">17:45</div></div>
                  <div><div className="detail-label">Voyageur</div><div className="detail-value">J. Dupont</div></div>
                  <div><div className="detail-label">Siège</div><div className="detail-value">Voiture 3 · 21B</div></div>
                </div>
                <div className="ticket-footer">
                  <span className="ticket-status status-upcoming">● À venir</span>
                  <span className="ticket-price">109€</span>
                </div>
              </div>
            </div>

          </div>

          <button
            className="scroll-arrow right"
            aria-label="Défiler à droite"
            onClick={() => scrollByAmount(310)}
            style={{ opacity: atEnd ? 0.35 : 1, pointerEvents: atEnd ? 'none' : 'auto' }}
          >
            <img src={arrowRSvg} alt="→" />
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
