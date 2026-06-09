import React, { useState, useRef, useEffect } from 'react';
import { useNavigate } from 'react-router-dom'; // Imported useNavigate hook
import '../assets/style/count.css';
import '../assets/img/images';
import TicketCard from '../components/TicketCard';
import type { TicketData } from '../components/TicketCard';

import { alertSvg, arriveSvg, arrowLeftSvg, arrowRightSvg, boxGoldSvg, cartSvg, departSvg, layerSvg, logoSvg, mailSvg, passSvg, persoWhiteSvg, shielSvg, tickeSvg, boxSvg } from '../assets/img/images';
import { useCart } from '../context/CartContext';

export default function Account() {
  const navigate = useNavigate(); // Initialized navigate object
  
  const [userFullName, setUserFullName] = useState('Chargement...');
  const [userEmail, setUserEmail] = useState('Chargement...');
  const [tickets, setTickets] = useState<TicketData[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  
  // State to hold user raw properties needed for building the confirmation state structure
  const [rawUser, setRawUser] = useState<any>(null);

  // Calculate stats dynamically
  const totalTickets = tickets.length;
  const upcomingTickets = tickets.filter(t => t.status === 'upcoming').length;

  // Function for checking session and downloading data
  useEffect(() => {
    const fetchData = async () => {
      try {
        // 1. Fetch User Data
        const userRes = await fetch('http://localhost:8000/api_user.php', { credentials: 'include' });
        const userData = await userRes.json();

        if (userData.status === 'success') {
          setUserFullName(`${userData.user.prenom} ${userData.user.nom}`);
          setUserEmail(userData.user.mail);
          setRawUser(userData.user); // Kept reference to the full user object

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
    try {
      await fetch('http://localhost:8000/api_logout.php', { method: 'POST', credentials: 'include' });
      window.location.href = '/login';
    } catch(error){
      console.error('Error logout: ', error);
      window.location.href = '/login';
    }
  };

  // Navigates to confirmation page with perfectly structured booking data
  const handleTicketClick = (ticket: TicketData) => {
    // Reconstruct arrival time based on departure and duration
    let arrTime = '--:--';
    if (ticket.heure_depart) {
      const [h, m] = ticket.heure_depart.split(':').map(Number);
      let durH = 1, durM = 55; // Default standard fallback
      if (ticket.temps_arriver) {
        const match = ticket.temps_arriver.match(/(\d+)h\s*(\d+)/);
        if (match) {
          durH = parseInt(match[1]);
          durM = parseInt(match[2]);
        }
      }
      const totalM = h * 60 + m + durH * 60 + durM;
      arrTime = `${Math.floor(totalM / 60) % 24}`.padStart(2, '0') + ':' + `${totalM % 60}`.padStart(2, '0');
    }

    // Prepare state structure matching precisely what Confirmation.tsx handles
    const bookingState = {
      train: {
        num: ticket.train_num,
        dep: ticket.heure_depart,
        from: ticket.depart,
        to: ticket.arriver,
        cls: '2', // Fallback value matching class view
        price: ticket.prix
      },
      passenger: {
        civilite: rawUser?.civilite || 'M',
        prenom: rawUser?.prenom || '',
        nom: rawUser?.nom || '',
        dob: rawUser?.dob || ''
      },
      contact: {
        email: rawUser?.mail || userEmail,
        telephone: rawUser?.telephone || ''
      },
      orderNumber: ticket.orderNumber,
      total: ticket.prix,
      assignedSeat: {
        wagon: parseInt(ticket.wagon) || 2,
        number: ticket.place,
        type: 'standard'
      },
      arrivalTime: arrTime
    };

   navigate('/confirmation', {
    state: {
      booking: {         // this wrapper was missing!
        train: {
          num:   ticket.train_num,
          dep:   ticket.heure_depart,
          from:  ticket.depart,
          to:    ticket.arriver,
          cls:   '2',
          price: ticket.prix,
          trainId: ticket.id,
        },
        passenger: {
          civilite:  rawUser?.civilite  || 'M.',
          prenom:    rawUser?.prenom    || '',
          nom:       rawUser?.nom       || '',
          dob:       rawUser?.dob       || '',
        },
        contact: {
          email:     rawUser?.mail      || userEmail,
          telephone: rawUser?.telephone || '',
        },
        orderNumber:  ticket.orderNumber,
        total:        ticket.prix,
        assignedSeat: {
          wagon:  parseInt(ticket.wagon) || 2,
          number: ticket.place,
          type:   'standard',
        },
        arrivalTime: arrTime,
      }
    }
  });
};

  return (
    <>
      <nav className="topbar">
        <a href="/home" className="brand">
          <div className="brand-logo"><img src={logoSvg} alt="Logo TNCF" /></div>
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
              <span className="profile-badge badge-member"><img src={passSvg} alt="" /> Membre Gold</span>
              <span className="profile-badge badge-verified"><img src={shielSvg} alt="" /> Compte vérifié</span>
            </div>
          </div>

          <div className="profile-stats">
            <div className="stat-item">
              <div className="stat-num">{totalTickets}</div>
              <div className="stat-label">Trajets</div>
            </div>
            <div className="stat-item">
              <div className="stat-num">{upcomingTickets}</div>
              <div className="stat-label">À venir</div>
            </div>
          </div>

          <a href="/edit-profile" className="btn-edit">
            <img src={persoWhiteSvg} alt="" /> Modifier le profil
          </a>
        </div>

        {/* SECTION MES BILLETS */}
        <div className="section-header">
          <div>
            <div className="section-eyebrow">Historique &amp; À venir</div>
            <h2 className="section-title">Mes Billets</h2>
          </div>
          <span className="section-count">{totalTickets} billet{totalTickets !== 1 ? 's' : ''}</span>
        </div>

        <div className="tickets-outer">
          <button className="scroll-arrow left" onClick={() => scrollByAmount(-310)} style={{ opacity: atStart ? 0.35 : 1 }}>
            <img src={arrowLeftSvg} alt="←" />
          </button>

          <div className="tickets-scroll" ref={scrollRef} onScroll={handleScroll} onMouseDown={handleMouseDown} onMouseLeave={handleMouseLeave} onMouseUp={handleMouseUp} onMouseMove={handleMouseMove} style={{ userSelect: isDragging ? 'none' : 'auto' }}>
            {tickets.length === 0 && !isLoading ? (
              <div style={{ padding: '40px', color: '#8a8f9e', textAlign: 'center', width: '100%' }}>Vous n'avez pas encore de billets.</div>
            ) : (
              tickets.map((ticket) => (
                <TicketCard 
                  key={ticket.id} 
                  ticket={ticket} 
                  passengerName={userFullName} 
                  onClick={() => handleTicketClick(ticket)} // Linked the click handler event
                />
              ))
            )}
          </div>

          <button className="scroll-arrow right" onClick={() => scrollByAmount(310)} style={{ opacity: atEnd ? 0.35 : 1 }}>
            <img src={arrowRightSvg} alt="→" />
          </button>
        </div>

        <div className="logout-section">
          <p className="logout-note">Connecté en tant que {userEmail}</p>
          <button className="btn-logout" onClick={handleLogout}>Déconnexion</button>
        </div>
      </div>
    </>
  );
}