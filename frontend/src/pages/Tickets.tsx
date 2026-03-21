import { useState, useEffect, useRef } from "react";
import type { Voyage } from "../Voyage";
import type { SelectedTrain } from "../SelectedTrain";
import '../assets/style/ticket.css'

import logoSvg       from '../assets/img/logo.svg';
import clockSvg      from '../assets/img/clock.svg';
import boxSvg        from '../assets/img/box.svg';
import boxGSvg       from '../assets/img/box_g.svg';
import personWhite   from '../assets/img/person_white.svg';
import departSvg     from '../assets/img/depart.svg';
import arriveSvg     from '../assets/img/arrive.svg';
import searchSvg     from '../assets/img/search.svg';


// ── Mock data (même que PHP) ───────────────────────────
const mockVoyages: Voyage[] = [
  { _id: 'm1', depart: 'Paris', arriver: 'Lyon', date_depart: '06:47', temps_arriver: '1h 55', prix: 29, num: 'TGV 6601' },
  { _id: 'm2', depart: 'Paris', arriver: 'Lyon', date_depart: '08:01', temps_arriver: '1h 58', prix: 35, num: 'TGV 6603' },
  { _id: 'm3', depart: 'Paris', arriver: 'Lyon', date_depart: '10:15', temps_arriver: '1h 55', prix: 42, num: 'TGV 6607' },
  { _id: 'm4', depart: 'Paris', arriver: 'Lyon', date_depart: '12:30', temps_arriver: '1h 55', prix: 29, num: 'TGV 6611' },
  { _id: 'm5', depart: 'Paris', arriver: 'Lyon', date_depart: '14:47', temps_arriver: '2h 03', prix: 55, num: 'TGV 6615' },
];


// ── Helpers ────────────────────────────────────────────
const today = new Date().toISOString().split('T')[0];
const dayAfter = new Date(Date.now() + 2 * 86400000).toISOString().split('T')[0];
const formatTime = (s: number) =>
  `${Math.floor(s / 60).toString().padStart(2, '0')}:${(s % 60).toString().padStart(2, '0')}`;


export default function Tickets(){
    const [voyages, setVoyages]         = useState<Voyage[]>(mockVoyages);
    const [selected, setSelected]       = useState<SelectedTrain | null>(null);
    const [cartAdded, setCartAdded]     = useState<string | null>(null);
    const [expandedId, setExpandedId]   = useState<string | null>(null);
    const [timerSec, setTimerSec]       = useState(900);
    const [sessionExp, setSessionExp]   = useState(false);
    const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);

    useEffect(() => { 
        fetch('http://localhost:8000/api_voyages.php')
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success'){
                    setVoyages([...data.data, ...mockVoyages]);
                }
            })
        .catch(() => {})
    }, []);

    useEffect(()=> {
        const reseTimer = () => setTimerSec(900);
        ['click', 'keydown', 'mousemove'].forEach(e => window.addEventListener(e, reseTimer));

        timerRef.current = setInterval(() => {
            setTimerSec(prev => {
                if(prev <= 1) {
                    clearInterval(timerRef.current!);
                    setSessionExp(true);
                    return 0;
                }
                return prev -1;
            });
        }, 1000);

        return () => {
            clearInterval(timerRef.current!);
            ['click', 'keydown', 'mousemove'].forEach(e => window.removeEventListener(e, reseTimer));
            
        }
    }, []);

    const prolongSession = () => {
        setSessionExp(false);
        setTimerSec(900);
    };

    const selectClass = (v: Voyage, cls: '1' | '2') => {
        const price = cls === '1' ? Math.round(v.prix * 2.2) : v.prix;
        setSelected({
            trainId: v._id,
            cls,
            price, 
            num: v.num ?? `TGV INOUI № ${v._id.slice(-4)}`,
            dep: v.date_depart,
            from: v.depart,
            to: v.arriver,
        });
    };



    const addToCart = (trainId: string) => {
        if (!selected || selected.trainId !== trainId) return;
        setCartAdded(trainId);
    };  
    
    
    const removeCart = () => {
        setSelected(null);
        setCartAdded(null);
    }

    const timerStr  = formatTime(timerSec);
    const isUrgent  = timerSec <= 30;
    const cartCount = cartAdded ? 1 : 0;


    return (
        <div className="tickets-page">

            {/* ── TOPBAR ── */}
            <div className="topbar">
                <a href="/" className="brand">
                <div className="brand-logo">
                    <img src={logoSvg} alt="TNCF" />
                </div>
                </a>
                <div className="topbar-actions">
                <div className="session-timer-top">
                    <img src={clockSvg} alt="" />
                    Session expire dans
                    <span className={`timer-count${isUrgent ? ' urgent' : ''}`}>{timerStr}</span>
                </div>
                <a href="#" className="cart-btn">
                    <img src={boxSvg} alt="" />
                    Panier
                    <span className="cart-count">{cartCount}</span>
                </a>
                <a href="/login" className="cart-btn">
                    <img src={personWhite} alt="" />
                    Connexion
                </a>
                </div>
            </div>

            {/* ── SEARCH PANEL ── */}
            <div className="search-panel">
                <div className="sp-fields">
                <div className="spf">
                    <span className="spf-icon"><img src={departSvg} alt="" /></span>
                    <div className="spf-inner">
                    <div className="spf-lbl">Départ</div>
                    <div className="spf-val"><input type="text" defaultValue="Paris (toutes gares intramuros)" /></div>
                    </div>
                </div>
                <div className="spf">
                    <span className="spf-icon"><img src={arriveSvg} alt="" /></span>
                    <div className="spf-inner">
                    <div className="spf-lbl">Arrivée</div>
                    <div className="spf-val"><input type="text" defaultValue="Bruxelles" /></div>
                    </div>
                </div>
                <div className="spf">
                    <div className="spf-inner">
                    <div className="spf-lbl">Aller</div>
                    <div className="spf-val"><input type="date" defaultValue={today} /></div>
                    </div>
                </div>
                <div className="sp-divider"></div>
                <div className="spf">
                    <div className="spf-inner">
                    <div className="spf-lbl">Retour</div>
                    <div className="spf-val"><input type="date" defaultValue={dayAfter} /></div>
                    </div>
                </div>
                <div className="spf narrow">
                    <span className="spf-icon"><img src={personWhite} alt="" /></span>
                    <div className="spf-inner">
                    <div className="spf-lbl">Passagers</div>
                    <div className="spf-val">× 1</div>
                    </div>
                </div>
                <button className="sp-search-btn">
                    <span className="spf-icon"><img src={searchSvg} alt="" /></span>
                </button>
                </div>
            </div>

            {/* ── PAGE BODY ── */}
            <div className="page-body">

                {/* FILTRES */}
                <aside className="filters">
                <div className="filter-header">
                    <span className="filter-title">Filtres</span>
                    <button className="filter-reset">Réinitialiser</button>
                </div>
                <div className="filter-group">
                    <div className="filter-group-title">Prix (€)</div>
                    <div className="price-range">
                    <input className="price-input" type="number" defaultValue={0}   min={0} max={300} style={{ width: 70 }} />
                    <span style={{ color: 'var(--gray)' }}>—</span>
                    <input className="price-input" type="number" defaultValue={150} min={0} max={300} style={{ width: 70 }} />
                    </div>
                    <input type="range" min={0} max={300} defaultValue={150} />
                </div>
                <div className="filter-group">
                    <div className="filter-group-title">Classe</div>
                    <div className="filter-option">
                    <label className="filter-option-left">
                        <input type="checkbox" defaultChecked />
                        <span className="filter-option-label">1ère classe</span>
                    </label>
                    <span className="filter-option-count">8</span>
                    </div>
                    <div className="filter-option">
                    <label className="filter-option-left">
                        <input type="checkbox" defaultChecked />
                        <span className="filter-option-label">2ème classe</span>
                    </label>
                    <span className="filter-option-count">12</span>
                    </div>
                </div>
                </aside>

                {/* RÉSULTATS */}
                <div className="results">
                <div className="results-header">
                    <div className="results-count">
                    <strong>{voyages.length}</strong> trajets trouvés
                    </div>
                    <div className="sort-wrap">
                    Trier par :
                    <select className="sort-select">
                        <option>Prix croissant</option>
                        <option>Départ le plus tôt</option>
                    </select>
                    </div>
                </div>

                {voyages.map(v => {
                    const trainNum   = v.num ?? `TGV INOUI № ${v._id.slice(-4)}`;
                    const isSelected = selected?.trainId === v._id;
                    const isAdded    = cartAdded === v._id;
                    const isExpanded = expandedId === v._id;

                    return (
                    <div key={v._id} className={`train-card${isAdded ? ' selected' : ''}`}>
                        <div className="train-card-main">

                        <div className="train-number">
                            <div className="train-label">Train</div>
                            <div className="train-num-badge">{trainNum}</div>
                        </div>

                        <div className="train-timeline">
                            <div className="train-time">
                            <div className="train-hour">{v.date_depart}</div>
                            <div className="train-station">{v.depart}</div>
                            </div>
                            <div className="train-line">
                            <div className="train-duration">{v.temps_arriver}</div>
                            <div className="train-track"></div>
                            <div className="train-direct" style={{ color: '#2d9e6b' }}>✓ Direct</div>
                            </div>
                            <div className="train-time">
                            <div className="train-hour">Arrivée</div>
                            <div className="train-station">{v.arriver}</div>
                            </div>
                        </div>

                        <div className="train-classes">
                            <div
                            className={`class-btn${isSelected && selected?.cls === '2' ? ' selected' : ''}`}
                            onClick={() => selectClass(v, '2')}
                            >
                            <div className="class-label">Classe</div>
                            <div className="class-name">2ème</div>
                            <div className="class-price">{v.prix}€</div>
                            <div className="class-seats" style={{ color: '#2d9e6b' }}>Places dispo.</div>
                            </div>
                            <div
                            className={`class-btn${isSelected && selected?.cls === '1' ? ' selected' : ''}`}
                            onClick={() => selectClass(v, '1')}
                            >
                            <div className="class-label">Classe</div>
                            <div className="class-name">1ère</div>
                            <div className="class-price">{Math.round(v.prix * 2.2)}€</div>
                            <div className="class-seats" style={{ color: '#2d9e6b' }}>Places dispo.</div>
                            </div>
                        </div>

                        <button
                            className="train-add-btn"
                            onClick={() => addToCart(v._id)}
                            disabled={!isSelected || isAdded}
                            style={{ opacity: isSelected ? 1 : 0.4, cursor: isSelected ? 'pointer' : 'not-allowed' }}
                        >
                            {isAdded ? '✓ Ajouté' : 'Ajouter →'}
                        </button>
                        </div>

                        <button
                        className="expand-toggle"
                        onClick={() => setExpandedId(isExpanded ? null : v._id)}
                        >
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
                            style={{ transform: isExpanded ? 'rotate(180deg)' : '' }}>
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                        Voir les services inclus
                        </button>

                        {isExpanded && (
                        <div className="train-card-expand open">
                            <div className="options-tags">
                            <span className="option-tag">🍽️ Restauration à bord</span>
                            <span className="option-tag">📶 WiFi gratuit</span>
                            <span className="option-tag">🚿 WC disponibles</span>
                            </div>
                        </div>
                        )}
                    </div>
                    );
                })}
                </div>

                {/* PANIER */}
                <aside className="cart-panel">
                <div className="cart-title">
                    Mon panier
                    <span style={{ fontSize: '0.75rem', color: 'var(--gray)', fontWeight: 400 }}>
                    {cartCount} billet{cartCount > 1 ? 's' : ''}
                    </span>
                </div>

                <div className="session-timer">
                    <div>
                    <div style={{ fontSize: '0.7rem', color: 'var(--gray)' }}>Votre session expire dans</div>
                    <span className={`timer-count${isUrgent ? ' urgent' : ''}`}>{timerStr}</span>
                    </div>
                </div>

                {!cartAdded || !selected ? (
                    <div className="cart-empty">
                    <img src={boxGSvg} alt="" />
                    <p>Sélectionnez un train et une classe pour commencer votre réservation</p>
                    </div>
                ) : (
                    <div className="cart-items">
                    <div className="cart-item">
                        <div className="cart-item-header">
                        <div className="cart-item-route">
                            {selected.from} → {selected.to}
                            <span style={{ fontSize: '0.7rem', color: 'var(--gray)', fontWeight: 400 }}> (Aller)</span>
                        </div>
                        <button className="cart-item-remove" onClick={removeCart}>✕</button>
                        </div>
                        <div className="cart-item-details">
                        <div>{selected.num} · {selected.dep}</div>
                        <div>{selected.cls === '1' ? '1ère' : '2ème'} classe · 1 voyageur</div>
                        <div style={{ color: 'var(--navy)', fontWeight: 600, marginTop: 4 }}>{selected.price}€</div>
                        </div>
                    </div>

                    <div className="cart-totals">
                        <div className="total-row"><span>Billet</span><span>{selected.price}€</span></div>
                        <div className="total-row main"><span>Total</span><span>{selected.price}€</span></div>
                    </div>

                    <button className="btn-checkout">
                        Procéder au paiement
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </button>
                    </div>
                )}
                </aside>
            </div>

            {/* SESSION MODAL */}
            {sessionExp && (
                <div id="sessionModal" style={{ display: 'flex' }}>
                <div style={{ background: 'white', borderRadius: 20, padding: 48, textAlign: 'center', maxWidth: 400 }}>
                    <h3 style={{ fontFamily: "'Playfair Display', serif", fontSize: '1.4rem', marginBottom: 12, color: 'var(--navy)' }}>
                    Session expirée
                    </h3>
                    <p style={{ color: 'var(--gray)', fontSize: '0.9rem', marginBottom: 28, lineHeight: 1.6 }}>
                    Votre session a expiré pour des raisons de sécurité.
                    </p>
                    <button onClick={prolongSession} style={{ padding: '12px 32px', background: 'var(--navy)', color: 'white', border: 'none', borderRadius: 10, fontFamily: "'DM Sans', sans-serif", fontSize: '0.9rem', fontWeight: 600, cursor: 'pointer' }}>
                    Prolonger la session
                    </button>
                </div>
                </div>
            )}
        </div>
    );
}