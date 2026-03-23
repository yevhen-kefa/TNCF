import { useState, useEffect, useRef } from "react";
import type { Voyage } from "../Voyage";
import type { SelectedTrain } from "../SelectedTrain";
import TrainCard from "../components/TrainCard";
import DateStrip, { getMinPriceForDate } from "../components/DateStrip";
import '../assets/style/ticket.css'

import logoSvg       from '../assets/img/logo.svg';
import clockSvg      from '../assets/img/clock.svg';
import boxSvg        from '../assets/img/box.svg';
import boxGSvg       from '../assets/img/box_g.svg';
import personWhite   from '../assets/img/person_white.svg';
import departSvg     from '../assets/img/depart.svg';
import arriveSvg     from '../assets/img/arrive.svg';
import searchSvg     from '../assets/img/search.svg';


// ── Helpers ────────────────────────────────────────────
const getLocalYMD = (date: Date) => {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
};

const todayDate = new Date();
const today = getLocalYMD(todayDate);

const dayAfterDate = new Date();
dayAfterDate.setDate(todayDate.getDate() + 2);
const dayAfter = getLocalYMD(dayAfterDate);

const formatTime = (s: number) =>
  `${Math.floor(s / 60).toString().padStart(2, '0')}:${(s % 60).toString().padStart(2, '0')}`;


export default function Tickets(){
    const [voyages, setVoyages]         = useState<Voyage[]>([]);
    const [selected, setSelected]       = useState<SelectedTrain | null>(null);
    const [cartAdded, setCartAdded]     = useState<string | null>(null);
    const [expandedId, setExpandedId]   = useState<string | null>(null);
    const [timerSec, setTimerSec]       = useState(900);
    const [sessionExp, setSessionExp]   = useState(false);

    const [searchDate, setSearchDate]   = useState<string>(today);
    const [isLoading, setIsLoading]     = useState<boolean>(true);

    const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);

    //Dowload data from Navitia
    useEffect(() => {
        const fetchJourneys = async () => {
            setIsLoading(true); // On loader
            setVoyages([]); // Clear old result

            const proxyUrl = "http://yevhensrv.alwaysdata.net/navitia.php";
            
            const fromId = "admin:fr:75056"; // Paris
            const toId = "admin:fr:69123";   // Lyon
            
            //Format date for Navitia
            const isToday = searchDate === today;
            let timeStr = "000000";

            //if we seaching for today, take curent time
            if (isToday){
                const now = new Date();
                timeStr = `${now.getHours().toString().padStart(2, '0')}${now.getMinutes().toString().padStart(2, '0')}00`
            }

            const dateParam = searchDate.replace(/-/g, '');
            const datetime = `${dateParam}T${timeStr}`; 

            try {
                // Do ask for journeys, max 50 for taking all day
                const response = await fetch(`${proxyUrl}?endpoint=coverage/sncf/journeys&from=${fromId}&to=${toId}&datetime=${datetime}&min_nb_journeys=50`);
                const data = await response.json();

                if (data.journeys) {
                    // Show only the trains for day chosed
                    const filterJourneys = data.journeys.filter((j: any) => j.departure_date_time.startsWith(dateParam));


                    const navitiaVoyages: Voyage[] = filterJourneys.map((j: any, index: number) => {
                        
                        // Search for the public_transport section to get train info
                        const ptSection = j.sections.find((s: any) => s.type === "public_transport");
                        const trainNum = ptSection 
                            ? `${ptSection.display_informations.commercial_mode} ${ptSection.display_informations.headsign}` 
                            : "TGV INOUI";

                        const depTime = `${j.departure_date_time.substring(9, 11)}:${j.departure_date_time.substring(11, 13)}`;
                        const arrTime = `${j.arrival_date_time.substring(9, 11)}:${j.arrival_date_time.substring(11, 13)}`;
                        
                        // Calculate duration (from seconds to hours and minutes)
                        const hours = Math.floor(j.duration / 3600);
                        const minutes = Math.floor((j.duration % 3600) / 60);

                        const basePriceForDay = getMinPriceForDate(searchDate);
                        const timeVariation = (index % 5) * 4; // Adds 0€, 4€, 8€, 12€, or 16€
                        
                        const finalPrice = basePriceForDay + timeVariation;

                        return {
                            _id: `nav_${index}_${j.departure_date_time}`,
                            depart: "Paris",
                            arriver: "Lyon",
                            date_depart: depTime,
                            temps_arriver: `${hours}h ${minutes.toString().padStart(2, '0')}`,
                            prix: finalPrice, 
                            num: trainNum
                        };
                    });

                    setVoyages(navitiaVoyages);
                }
            } catch (error) {
                console.error("Error downloading journeys from Navitia:", error);
            } finally {
                setIsLoading(false); //off
            }
        };

        fetchJourneys();
    }, [searchDate]);
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

    const calcArrival = (dep: string, duration: string): string => {
        const [depH, depM] = dep.split(':').map(Number);
        const match = duration.match(/(\d+)h\s*(\d+)/);
        if (!match) return dep;

        const durH = parseInt(match[1]);
        const durM = parseInt(match[2]);

        const totalMin = depH * 60 + depM + durH * 60 + durM;
        
        return `${Math.floor(totalMin / 60) % 24}`.padStart(2, '0') + ':' + `${totalMin % 60}`.padStart(2, '0');
    };

    return (
        <div className="tickets-page">

            {/* ── TOPBAR ── */}
            <div className="topbar">
                <a href="/" className="brand">
                <div className="brand-logo">
                    <img src={logoSvg} alt="TNCF" />
                </div>
                </a>
                <ul className="nav-links">
                    <li><a href="/">Voyager</a></li>
                    <li><a href="/tickets" className="active">Billets</a></li>
                    <li><a href="/account">Compte</a></li>
                </ul>
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
                    <div className="spf-val"><input type="text" defaultValue="Lyon" /></div>
                    </div>
                </div>
                <div className="spf">
                    <div className="spf-inner">
                    <div className="spf-lbl">Aller</div>
                    <div className="spf-val"><input type="date" value={searchDate} min={today} onChange={(e) => setSearchDate(e.target.value)}/></div>
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
                        <strong>{voyages?.length || 0}</strong> trajets trouvés
                        </div>
                        <div className="sort-wrap">
                        Trier par :
                        <select className="sort-select">
                            <option>Prix croissant</option>
                            <option>Départ le plus tôt</option>
                        </select>
                        </div>
                    </div>

                
                    <DateStrip 
                        selectedDate={searchDate} 
                        onSelectDate={(newDate) => setSearchDate(newDate)} 
                    />

                    {/* Showing */}
                    {isLoading ? (
                        <div style={{ padding: '60px 20px', textAlign: 'center', color: 'var(--gray)' }}>
                            <div style={{ fontSize: '2rem', marginBottom: '10px' }}>🚆</div>
                            <p>Recherche des meilleurs trajets en cours...</p>
                        </div>
                    ) : voyages.length === 0 ? (
                        <div style={{ 
                            padding: '60px 20px', 
                            textAlign: 'center', 
                            background: 'var(--white)', 
                            borderRadius: '14px', 
                            border: '1.5px solid #ede8df',
                            marginTop: '20px'
                        }}>
                            <h3 style={{ color: 'var(--navy)', marginBottom: '10px', fontSize: '1.2rem' }}>
                                Aucun train disponible
                            </h3>
                            <p style={{ color: 'var(--gray)', fontSize: '0.9rem', lineHeight: '1.6' }}>
                                Il n'y a plus de trains disponibles pour cette date ou l'heure est déjà passée. <br/>
                                Veuillez sélectionner un autre jour dans le calendrier.
                            </p>
                        </div>
                    ) : (
                        voyages.map(v => (
                            <TrainCard
                                key={v._id}
                                voyage={v}
                                isSelected={selected?.trainId === v._id}
                                selectedClass={selected?.trainId === v._id ? selected.cls : undefined}
                                isAdded={cartAdded === v._id}
                                isExpanded={expandedId === v._id}
                                onSelectClass={selectClass}
                                onAddToCart={addToCart}
                                onToggleExpand={() => setExpandedId(expandedId === v._id ? null : v._id)}
                                calcArrival={calcArrival}
                            />
                        ))
                    )}
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