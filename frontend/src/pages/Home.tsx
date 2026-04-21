import React, { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';

import {
  logoSvg,
  departSvg,
  arriveSvg,
  switcSvg,
  searcSvg,
  shielSvg,
  cartSvg,
  alertSvg,
  tickeSvg
} from '../assets/img/images';

import trainSvg from '../assets/img/train/train.svg';
import parisImg from '../assets/img/cities/paris.jpg';
import marseilleImg from '../assets/img/cities/marseille.jpg';
import bordeauxImg from '../assets/img/cities/bordeaux.jpg';
import niceImg from '../assets/img/cities/nice.jpg';
import lilleImg from '../assets/img/cities/lille.jpg';
import lyonImg from '../assets/img/cities/lyon.jpg';
import rennesImg from '../assets/img/cities/rennes.jpg';
import caenImg from '../assets/img/cities/caen.jpg';
import strasbourgImg from '../assets/img/cities/strasbourg.jpg';

import '../assets/style/index.css';

export default function Home() {
  const navigate = useNavigate();

  const [isScrolled, setIsScrolled] = useState(true); 

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 60);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const getTodayDate = () => new Date().toISOString().split('T')[0];
  const getReturnDate = () => {
    const d = new Date();
    d.setDate(d.getDate() + 2);
    return d.toISOString().split('T')[0];
  };

  const [tripType, setTripType] = useState<'aller-simple' | 'aller-retour'>('aller-simple');
  const [departure, setDeparture] = useState('Paris (Gare de Lyon)');
  const [arrival, setArrival] = useState('Lyon Part-Dieu');
  const [dateDepart, setDateDepart] = useState(getTodayDate());
  const [dateRetour, setDateRetour] = useState(getReturnDate());

  const handleSwapCities = () => {
    const temp = departure;
    setDeparture(arrival);
    setArrival(temp);
  };

  const handleSearch = () => {
    navigate('/tickets', { state: { departure, arrival, dateDepart, tripType } });
  };

  return (
    <>
      {/* ── NAV ── */}
      <nav id="navbar" className={isScrolled ? 'scrolled' : ''}>
        <Link to="/home" className="brand">
          <div className="brand-logo">
            <img src={logoSvg} alt="TNCF" />
          </div>
        </Link>

        <ul className="nav-links">
          <li><Link to="/home" className="nav-link active">Voyager</Link></li>
          <li><Link to="/tickets">Billets</Link></li>
          <li><Link to="/account">Compte</Link></li>
        </ul>

        <div className="nav-actions">
          <Link to="/login" className="btn-nav-outline">Se connecter</Link>
        </div>
      </nav>

      {/* ── HERO ── */}
      <section className="hero">
        <div className="hero-bg-grid"></div>
        <div className="hero-glow"></div>
        <div className="hero-rail"></div>

        {/* animated mini-train */}
        <div className="hero-train">
          <img src={trainSvg} alt="TGV Train" style={{ height: '150px', width: 'auto' }} />
        </div>

        <div className="hero-content">
          <div className="hero-eyebrow">Voyages Grande Vitesse</div>
          <h1>La France à<br />grande <em>vitesse</em></h1>
          <p>Trouvez et réservez vos billets TGV au meilleur prix. Plus de 200 destinations, des départs chaque heure.</p>
        </div>

        {/* ── SEARCH BOX ── */}
        <div className="search-box">
          <div className="search-tabs">
            <button 
              className={`search-tab ${tripType === 'aller-simple' ? 'active' : ''}`}
              onClick={() => setTripType('aller-simple')}
            >
              Aller simple
            </button>
            <button 
              className={`search-tab ${tripType === 'aller-retour' ? 'active' : ''}`}
              onClick={() => setTripType('aller-retour')}
            >
              Aller-retour
            </button>
          </div>

          <div className="search-fields">
            {/* Départ */}
            <div className="search-field" style={{ position: 'relative' }}>
              <label>Départ</label>
              <div className="search-field-inner">
                <span className="search-icon">
                  <img src={departSvg} alt="depart" />
                </span>
                <input 
                  type="text" 
                  placeholder="Ville ou gare" 
                  value={departure}
                  onChange={(e) => setDeparture(e.target.value)}
                />
              </div>
              <button className="swap-btn" onClick={handleSwapCities}>
                <img src={switcSvg} alt="swap" />
              </button>
            </div>

            {/* Arrivée */}
            <div className="search-field">
              <label>Arrivée</label>
              <div className="search-field-inner">
                <span className="search-icon">
                  <img src={arriveSvg} alt="arrive" style={{ height: '17px' }} />
                </span>
                <input 
                  type="text" 
                  placeholder="Ville ou gare" 
                  value={arrival}
                  onChange={(e) => setArrival(e.target.value)}
                />
              </div>
            </div>

            {/* Date Départ */}
            <div className="search-field">
              <label>Départ</label>
              <div className="search-field-inner">
                <input 
                  type="date" 
                  value={dateDepart}
                  onChange={(e) => setDateDepart(e.target.value)}
                />
              </div>
            </div>

            {/* Date Retour */}
            <div 
              className="search-field" 
              id="retour-field"
              style={{
                opacity: tripType === 'aller-simple' ? 0.4 : 1,
                pointerEvents: tripType === 'aller-simple' ? 'none' : 'auto'
              }}
            >
              <label>Retour</label>
              <div className="search-field-inner">
                <input 
                  type="date" 
                  value={dateRetour}
                  onChange={(e) => setDateRetour(e.target.value)}
                  disabled={tripType === 'aller-simple'}
                />
              </div>
            </div>

            {/* Submit Button */}
            <div>
              <button className="btn-search" onClick={handleSearch}>
                <img src={searcSvg} alt="search" />
                Rechercher
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* ── POPULAR ROUTES ── */}
      <section className="section">
        <div className="section-header">
          <div className="section-eyebrow">Destinations populaires</div>
          <h2 className="section-title">Les trajets les plus<br />empruntés</h2>
          <p className="section-sub">Découvrez nos meilleures liaisons TGV à prix réduits</p>
        </div>

        <div className="routes-grid">
          {[
            { city: 'Paris', img: parisImg, bgClass: 'bg-paris' },
            { city: 'Marseille', img: marseilleImg, bgClass: 'bg-marseille' },
            { city: 'Bordeaux', img: bordeauxImg, bgClass: 'bg-bordeaux' },
            { city: 'Nice', img: niceImg, bgClass: 'bg-nice' },
            { city: 'Lille', img: lilleImg, bgClass: 'bg-lille' },
            { city: 'Lyon', img: lyonImg, bgClass: 'bg-lyon' },
            { city: 'Rennes', img: rennesImg, bgClass: 'bg-lyon' },
            { city: 'Caen', img: caenImg, bgClass: 'bg-lyon' },
            { city: 'Strasbourg', img: strasbourgImg, bgClass: 'bg-lyon' }
          ].map((route, idx) => (
            <Link to="/tickets" className="route-card" key={idx}>
              <div className="route-img">
                <div className={`route-img-bg ${route.bgClass}`}>
                  <img src={route.img} alt={route.city} />
                </div>
                <div className="route-img-overlay"></div>   
              </div>
              <div className="route-body">
                <div className="route-cities">
                  <span className="route-city">{route.city}</span>
                </div>
              </div>
            </Link>
          ))}
        </div>
      </section>

      {/* ── FEATURES ── */}
      <section className="section section-alt">
        <div className="section-header">
          <div className="section-eyebrow">Pourquoi TNCF</div>
          <h2 className="section-title">Le confort du voyage<br />à chaque étape</h2>
        </div>

        <div className="features-grid">
          <div className="feature-card">
            <div className="feature-icon">
              <img src={shielSvg} alt="Garantie" style={{ height: '30px' }} />
            </div>
            <div className="feature-title">Garantie Annulation</div>
            <p className="feature-text">Annulez jusqu'à 24 heure avant le départ et soyez remboursé intégralement.</p>
          </div>
          <div className="feature-card">
            <div className="feature-icon">
                <img src={cartSvg} alt="Paiement" />
            </div>
            <div className="feature-title">Paiement Sécurisé</div>
            <p className="feature-text">Toutes vos transactions sont protégées.</p>
          </div>
          <div className="feature-card">
            <div className="feature-icon">
              <img src={alertSvg} alt="Alertes" />
            </div>
            <div className="feature-title">Alertes Email</div>
            <p className="feature-text">Restez informé de l'état de votre train en temps réel par Email.</p>
          </div>
          <div className="feature-card">
            <div className="feature-icon">
              <img src={tickeSvg} alt="Billet" style={{ height: '20px' }} />
            </div>
            <div className="feature-title">Billet Mobile</div>
            <p className="feature-text">Votre billet directement sur votre smartphone — sans impression requise.</p>
          </div>
        </div>
      </section>

      {/* ── FOOTER ── */}
      <footer>
        <div className="footer-top">
          <div className="footer-brand">
            <span className="brand-name" style={{ fontFamily: "'Playfair Display', serif", color: 'white', letterSpacing: '4px' }}>TNCF</span>
            <p>Le réseau ferroviaire grande vitesse français. Voyagez vite, voyagez bien, voyagez TNCF.</p>
          </div>
          <div className="footer-col">
            <h4>Voyager</h4>
            <ul>
              <li><a href="#">Horaires & Prix</a></li>
              <li><a href="#">Abonnements</a></li>
              <li><a href="#">Cartes de réduction</a></li>
              <li><a href="#">TGV Inoui</a></li>
            </ul>
          </div>
          <div className="footer-col">
            <h4>Services</h4>
            <ul>
              <li><a href="#">Bagages</a></li>
              <li><a href="#">Espace silencieux</a></li>
              <li><a href="#">WiFi à bord</a></li>
              <li><a href="#">Restauration</a></li>
            </ul>
          </div>
          <div className="footer-col">
            <h4>TNCF</h4>
            <ul>
              <li><a href="#">À propos</a></li>
              <li><a href="#">Presse</a></li>
              <li><a href="#">Recrutement</a></li>
              <li><a href="#">Contact</a></li>
            </ul>
          </div>
        </div>
        <div className="footer-bottom">
          <span>© 2026 TNCF — Tous droits réservés</span>
          <span>Mentions légales · CGU · Confidentialité</span>
        </div>
      </footer>
    </>
  );
}