import { useState, useEffect, useRef } from "react";
import { useNavigate, useLocation, Link } from "react-router-dom";
import type { Voyage } from "../Voyage";
import type { SelectedTrain } from "../SelectedTrain";
import TrainCard from "../components/TrainCard";
import DateStrip, { getMinPriceForDate } from "../components/DateStrip";
import { useCart } from '../context/CartContext';
import '../assets/style/ticket.css';

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

const timeToMinutes = (t: string): number => {
    const [h, m] = t.split(':').map(Number);
    return h * 60 + m;
};

// ── Dictionary of cities for Navitia API ──
const NAVITIA_CITIES: Record<string, string> = {
    'paris':      'admin:fr:75056',
    'lyon':       'admin:fr:69123',
    'marseille':  'admin:fr:13055',
    'bordeaux':   'admin:fr:33063',
    'nice':       'admin:fr:06088',
    'lille':      'admin:fr:59350',
    'rennes':     'admin:fr:35238',
    'caen':       'admin:fr:14118',
    'strasbourg': 'admin:fr:67482'
};

// ── Array of cities for Autocomplete ──
const AVAILABLE_CITIES = [
  'Paris', 'Lyon', 'Marseille', 'Bordeaux', 'Nice', 
  'Lille', 'Rennes', 'Caen', 'Strasbourg', 'Toulouse', 
  'Nantes', 'Montpellier'
];

const getCityId = (cityName: string, fallback: string) => {
    const name = cityName.toLowerCase();
    for (const [key, id] of Object.entries(NAVITIA_CITIES)) {
        if (name.includes(key)) return id;
    }
    return fallback;
};

const TIME_SLOTS = [
    { label: 'Nuit',       sublabel: '00h–06h', from: 0,    to: 360  },
    { label: 'Matin',      sublabel: '06h–12h', from: 360,  to: 720  },
    { label: 'Après-midi', sublabel: '12h–18h', from: 720,  to: 1080 },
    { label: 'Soir',       sublabel: '18h–24h', from: 1080, to: 1440 },
];

type SortKey = 'price_asc' | 'price_desc' | 'time_asc' | 'time_desc';

export default function Tickets() {
    const location = useLocation();
    const navigate = useNavigate();
    const searchParams = location.state as any;

    const [departureCity, setDepartureCity] = useState(searchParams?.departure || 'Paris');
    const [arrivalCity,   setArrivalCity]   = useState(searchParams?.arrival   || 'Lyon');
    const [searchDate,    setSearchDate]    = useState<string>(searchParams?.dateDepart || today);

    // ── Dropdown states and refs ──
    const [showDepartDropdown, setShowDepartDropdown] = useState(false);
    const [showArrivalDropdown, setShowArrivalDropdown] = useState(false);
    
    // Refs to detect clicks outside the dropdowns
    const departRef = useRef<HTMLDivElement>(null);
    const arrivalRef = useRef<HTMLDivElement>(null);
    const menuRef = useRef<HTMLDivElement>(null);

    // ── User state ──
    const [user, setUser] = useState<{ prenom: string, nom: string } | null>(null);
    const [showUserMenu, setShowUserMenu] = useState(false);

    const [voyages,    setVoyages]    = useState<Voyage[]>([]);
    const [selected,   setSelected]   = useState<SelectedTrain | null>(null);
    const { cartItems }               = useCart();
    const [expandedId, setExpandedId] = useState<string | null>(null);
    const [timerSec,   setTimerSec]   = useState(900);
    const [sessionExp, setSessionExp] = useState(false);
    const [isLoading,  setIsLoading]  = useState<boolean>(true);

    const [priceMin,      setPriceMin]      = useState<number>(0);
    const [priceMax,      setPriceMax]      = useState<number>(300);
    const [priceRangeMax, setPriceRangeMax] = useState<number>(300);
    const [activeSlots,   setActiveSlots]   = useState<number[]>([]);
    const [sortKey,       setSortKey]       = useState<SortKey>('time_asc');

    const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);

    // Filter cities based on input
    const filteredDepartCities = AVAILABLE_CITIES.filter(city => 
        city.toLowerCase().includes(departureCity.toLowerCase())
    );
    const filteredArrivalCities = AVAILABLE_CITIES.filter(city => 
        city.toLowerCase().includes(arrivalCity.toLowerCase())
    );

    // ── Check session on load ──
    useEffect(() => {
        const checkSession = async () => {
            try {
                const response = await fetch('http://localhost:8000/api_user.php', { credentials: 'include' });
                const data = await response.json();
                if (data.status === 'success') {
                    setUser(data.user);
                }
            } catch (error) {
                console.log("User is not authenticated");
            }
        };
        checkSession();
    }, []);

    // ── Click outside listener for all dropdowns ──
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            const target = event.target as Node;
            if (menuRef.current && !menuRef.current.contains(target)) setShowUserMenu(false);
            if (departRef.current && !departRef.current.contains(target)) setShowDepartDropdown(false);
            if (arrivalRef.current && !arrivalRef.current.contains(target)) setShowArrivalDropdown(false);
        };
        document.addEventListener("mousedown", handleClickOutside);
        return () => document.removeEventListener("mousedown", handleClickOutside);
    }, []);

    // ── Logout function ──
    const handleLogout = async () => {
        try {
            await fetch('http://localhost:8000/api_logout.php', {
                method: 'POST',
                credentials: 'include'
            });
            setUser(null);
            setShowUserMenu(false);
            navigate('/login');
        } catch (error) {
            console.error('Logout error: ', error);
        }
    };

    // ── Download data from Navitia ──
    useEffect(() => {
        const fetchJourneys = async () => {
            setIsLoading(true);
            setVoyages([]);

            const proxyUrl = "http://yevhensrv.alwaysdata.net/navitia.php";
            const fromId   = getCityId(departureCity, "admin:fr:75056");
            const toId     = getCityId(arrivalCity,   "admin:fr:69123");

            const isToday = searchDate === today;
            let timeStr   = "000000";
            if (isToday) {
                const now = new Date();
                timeStr = `${now.getHours().toString().padStart(2, '0')}${now.getMinutes().toString().padStart(2, '0')}00`;
            }

            const dateParam = searchDate.replace(/-/g, '');
            const datetime  = `${dateParam}T${timeStr}`;

            try {
                const response = await fetch(
                    `${proxyUrl}?endpoint=coverage/sncf/journeys&from=${fromId}&to=${toId}&datetime=${datetime}&min_nb_journeys=50`
                );
                const data = await response.json();

                if (data.journeys) {
                    const filtered = data.journeys.filter((j: any) =>
                        j.departure_date_time.startsWith(dateParam)
                    );

                    const navitiaVoyages: Voyage[] = filtered.map((j: any, index: number) => {
                        const ptSection = j.sections.find((s: any) => s.type === "public_transport");
                        const trainNum  = ptSection
                            ? `${ptSection.display_informations.commercial_mode} ${ptSection.display_informations.headsign}`
                            : "TGV INOUI";

                        const depTime = `${j.departure_date_time.substring(9, 11)}:${j.departure_date_time.substring(11, 13)}`;
                        const hours   = Math.floor(j.duration / 3600);
                        const minutes = Math.floor((j.duration % 3600) / 60);
                        const finalPrice = getMinPriceForDate(searchDate) + (index % 5) * 4;

                        return {
                            _id:           `nav_${index}_${j.departure_date_time}`,
                            depart:        departureCity,
                            arriver:       arrivalCity,
                            date_depart:   depTime,
                            temps_arriver: `${hours}h ${minutes.toString().padStart(2, '0')}`,
                            prix:          finalPrice,
                            num:           trainNum,
                        };
                    });

                    setVoyages(navitiaVoyages);

                    if (navitiaVoyages.length > 0) {
                        const prices = navitiaVoyages.map(v => v.prix);
                        const minP   = Math.floor(Math.min(...prices));
                        const maxP   = Math.ceil(Math.max(...prices) * 2.2);
                        setPriceMin(minP);
                        setPriceMax(maxP);
                        setPriceRangeMax(maxP);
                    }
                }
            } catch (error) {
                console.error("Error downloading journeys from Navitia:", error);
            } finally {
                setIsLoading(false);
            }
        };

        fetchJourneys();
    }, [searchDate, departureCity, arrivalCity]);

    // ── Inactivity Timer ──
    useEffect(() => {
        const resetTimer = () => setTimerSec(900);
        ['click', 'keydown', 'mousemove'].forEach(e => window.addEventListener(e, resetTimer));
        timerRef.current = setInterval(() => {
            setTimerSec(prev => {
                if (prev <= 1) { clearInterval(timerRef.current!); setSessionExp(true); return 0; }
                return prev - 1;
            });
        }, 1000);
        return () => {
            clearInterval(timerRef.current!);
            ['click', 'keydown', 'mousemove'].forEach(e => window.removeEventListener(e, resetTimer));
        };
    }, []);

    // ── Apply filters and sorting ──
    const displayedVoyages = voyages
        .filter(v => v.prix >= priceMin && v.prix <= priceMax)
        .filter(v => {
            if (activeSlots.length === 0) return true;
            const mins = timeToMinutes(v.date_depart);
            return activeSlots.some(idx => mins >= TIME_SLOTS[idx].from && mins < TIME_SLOTS[idx].to);
        })
        .sort((a, b) => {
            switch (sortKey) {
                case 'price_asc':  return a.prix - b.prix;
                case 'price_desc': return b.prix - a.prix;
                case 'time_asc':   return timeToMinutes(a.date_depart) - timeToMinutes(b.date_depart);
                case 'time_desc':  return timeToMinutes(b.date_depart) - timeToMinutes(a.date_depart);
                default: return 0;
            }
        });

    const handleResetFilters = () => {
        const prices = voyages.map(v => v.prix);
        const minP   = prices.length ? Math.floor(Math.min(...prices)) : 0;
        const maxP   = prices.length ? Math.ceil(Math.max(...prices) * 2.2) : 300;
        setPriceMin(minP);
        setPriceMax(maxP);
        setPriceRangeMax(maxP);
        setActiveSlots([]);
        setSortKey('time_asc');
    };

    const toggleSlot = (idx: number) =>
        setActiveSlots(prev => prev.includes(idx) ? prev.filter(i => i !== idx) : [...prev, idx]);

    const prolongSession = () => { setSessionExp(false); setTimerSec(900); };

    const selectClass = (v: Voyage, cls: '1' | '2') => {
        const price = cls === '1' ? Math.round(v.prix * 2.2) : v.prix;
        setSelected({ trainId: v._id, cls, price, num: v.num ?? `TGV INOUI`, dep: v.date_depart, from: v.depart, to: v.arriver });
    };

    const addToCart  = (trainId: string) => {
        if (!selected || selected.trainId !== trainId) return;
        navigate('/booking', { state: { train: selected } });
    };
    const removeCart = () => setSelected(null);

    const calcArrival = (dep: string, duration: string): string => {
        const [depH, depM] = dep.split(':').map(Number);
        const match = duration.match(/(\d+)h\s*(\d+)/);
        if (!match) return dep;
        const totalMin = depH * 60 + depM + parseInt(match[1]) * 60 + parseInt(match[2]);
        return `${Math.floor(totalMin / 60) % 24}`.padStart(2, '0') + ':' + `${totalMin % 60}`.padStart(2, '0');
    };

    const timerStr  = formatTime(timerSec);
    const isUrgent  = timerSec <= 30;
    const cartCount = cartItems.length;

    return (
        <div className="tickets-page">
            <div className="topbar">
                <Link to="/" className="brand"><div className="brand-logo"><img src={logoSvg} alt="TNCF" /></div></Link>
                <ul className="nav-links">
                    <li><Link to="/">Voyager</Link></li>
                    <li><Link to="/tickets" className="active">Billets</Link></li>
                    <li><Link to="/account">Compte</Link></li>
                </ul>
                <div className="topbar-actions">
                    <div className="session-timer-top">
                        <img src={clockSvg} alt="" />
                        Session expire dans
                        <span className={`timer-count${isUrgent ? ' urgent' : ''}`}>{timerStr}</span>
                    </div>
                    <Link to="/cart" className="cart-btn"><img src={boxSvg} alt="" />Panier<span className="cart-count">{cartCount}</span></Link>
                    
                    {/* ── Dynamic Authentication Block ── */}
                    {user ? (
                        <div className="user-menu-container" ref={menuRef}>
                            <button 
                                className="cart-btn" 
                                onClick={() => setShowUserMenu(!showUserMenu)}
                                style={{ background: 'none', border: 'none', cursor: 'pointer', fontFamily: "'DM Sans', sans-serif" }}
                            >
                                <img src={personWhite} alt="" />
                                {user.prenom} {user.nom}
                            </button>

                            {showUserMenu && (
                                <ul className="user-dropdown-menu" style={{ top: '100%', right: '0', marginTop: '15px' }}>
                                    <li>
                                        <Link to="/account" onClick={() => setShowUserMenu(false)}>Mon profil</Link>
                                    </li>
                                    <li>
                                        <Link to="/edit-profile" onClick={() => setShowUserMenu(false)}>Paramètres</Link>
                                    </li>
                                    <hr />
                                    <li>
                                        <button onClick={handleLogout} style={{ color: '#e05252' }}>Déconnexion</button>
                                    </li>
                                </ul>
                            )}
                        </div>
                    ) : (
                        <Link to="/login" className="cart-btn"><img src={personWhite} alt="" />Connexion</Link>
                    )}
                </div>
            </div>

            <div className="search-panel">
                <div className="sp-fields">
                    
                    {/* ── Departure Field with Autocomplete ── */}
                    <div className="spf" ref={departRef} style={{ position: 'relative' }}>
                        <span className="spf-icon"><img src={departSvg} alt="" /></span>
                        <div className="spf-inner">
                            <div className="spf-lbl">Départ</div>
                            <div className="spf-val">
                                <input 
                                    type="text" 
                                    value={departureCity} 
                                    onChange={e => setDepartureCity(e.target.value)} 
                                    onFocus={() => setShowDepartDropdown(true)}
                                />
                            </div>
                        </div>
                        {showDepartDropdown && filteredDepartCities.length > 0 && (
                            <ul className="city-dropdown" style={{ top: '100%', left: '0', right: '0' }}>
                                {filteredDepartCities.map((city, idx) => (
                                    <li key={idx} onClick={() => { setDepartureCity(city); setShowDepartDropdown(false); }}>
                                        {city}
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                    
                    {/* ── Arrival Field with Autocomplete ── */}
                    <div className="spf" ref={arrivalRef} style={{ position: 'relative' }}>
                        <span className="spf-icon"><img src={arriveSvg} alt="" /></span>
                        <div className="spf-inner">
                            <div className="spf-lbl">Arrivée</div>
                            <div className="spf-val">
                                <input 
                                    type="text" 
                                    value={arrivalCity} 
                                    onChange={e => setArrivalCity(e.target.value)} 
                                    onFocus={() => setShowArrivalDropdown(true)}
                                />
                            </div>
                        </div>
                        {showArrivalDropdown && filteredArrivalCities.length > 0 && (
                            <ul className="city-dropdown" style={{ top: '100%', left: '0', right: '0' }}>
                                {filteredArrivalCities.map((city, idx) => (
                                    <li key={idx} onClick={() => { setArrivalCity(city); setShowArrivalDropdown(false); }}>
                                        {city}
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                    
                    <div className="spf">
                        <div className="spf-inner">
                            <div className="spf-lbl">Aller</div>
                            <div className="spf-val"><input type="date" value={searchDate} min={today} onChange={e => setSearchDate(e.target.value)} /></div>
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
                    <button className="sp-search-btn"><span className="spf-icon"><img src={searchSvg} alt="" /></span></button>
                </div>
            </div>

            <div className="page-body">

                {/* ── FILTERS ── */}
                <aside className="filters">
                    <div className="filter-header">
                        <span className="filter-title">Filtres</span>
                        <button className="filter-reset" onClick={handleResetFilters}>Réinitialiser</button>
                    </div>

                    {/* Price */}
                    <div className="filter-group">
                        <div className="filter-group-title">Prix (€)</div>
                        <div className="price-range">
                            <input
                                className="price-input"
                                type="number"
                                value={priceMin}
                                min={0}
                                max={priceMax - 1}
                                onChange={e => setPriceMin(Math.min(Number(e.target.value), priceMax - 1))}
                            />
                            <span style={{ color: 'var(--gray)' }}>—</span>
                            <input
                                className="price-input"
                                type="number"
                                value={priceMax}
                                min={priceMin + 1}
                                max={priceRangeMax}
                                onChange={e => setPriceMax(Math.max(Number(e.target.value), priceMin + 1))}
                            />
                        </div>
                        <input
                            type="range"
                            min={0}
                            max={priceRangeMax}
                            value={priceMax}
                            onChange={e => setPriceMax(Math.max(Number(e.target.value), priceMin + 1))}
                        />
                        <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '0.72rem', color: 'var(--gray)', marginTop: '4px' }}>
                            <span>0€</span><span>{priceRangeMax}€</span>
                        </div>
                    </div>

                    {/* Departure Time */}
                    <div className="filter-group">
                        <div className="filter-group-title">Heure de départ</div>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '8px' }}>
                            {TIME_SLOTS.map((slot, idx) => {
                                const isActive = activeSlots.includes(idx);
                                return (
                                    <button
                                        key={idx}
                                        onClick={() => toggleSlot(idx)}
                                        style={{
                                            padding: '10px 6px',
                                            borderRadius: '10px',
                                            border: `1.5px solid ${isActive ? 'var(--navy)' : '#e8e2d8'}`,
                                            background: isActive ? 'var(--navy)' : 'var(--light)',
                                            color: isActive ? 'white' : 'var(--navy)',
                                            cursor: 'pointer',
                                            textAlign: 'center',
                                            transition: 'all .15s',
                                            fontFamily: "'DM Sans', sans-serif",
                                        }}
                                    >
                                        <div style={{ fontWeight: 600, fontSize: '0.8rem' }}>{slot.label}</div>
                                        <div style={{ fontSize: '0.7rem', opacity: 0.75, marginTop: '2px' }}>{slot.sublabel}</div>
                                    </button>
                                );
                            })}
                        </div>
                        {activeSlots.length > 0 && (
                            <button
                                onClick={() => setActiveSlots([])}
                                style={{ marginTop: '8px', fontSize: '0.75rem', color: 'var(--gold)', background: 'none', border: 'none', cursor: 'pointer', padding: 0 }}
                            >
                                Effacer la sélection
                            </button>
                        )}
                    </div>

                    {/* Class */}
                    <div className="filter-group">
                        <div className="filter-group-title">Classe</div>
                        <div className="filter-option">
                            <label className="filter-option-left">
                                <input type="checkbox" defaultChecked />
                                <span className="filter-option-label">1ère classe</span>
                            </label>
                        </div>
                        <div className="filter-option">
                            <label className="filter-option-left">
                                <input type="checkbox" defaultChecked />
                                <span className="filter-option-label">2ème classe</span>
                            </label>
                        </div>
                    </div>
                </aside>

                {/* ── RESULTS ── */}
                <div className="results">
                    <div className="results-header">
                        <div className="results-count">
                            <strong>{displayedVoyages.length}</strong> trajet{displayedVoyages.length !== 1 ? 's' : ''} trouvé{displayedVoyages.length !== 1 ? 's' : ''} pour{' '}
                            <strong>{departureCity} → {arrivalCity}</strong>
                        </div>
                        <div className="sort-wrap">
                            Trier par :
                            <select className="sort-select" value={sortKey} onChange={e => setSortKey(e.target.value as SortKey)}>
                                <option value="time_asc">Départ le plus tôt</option>
                                <option value="time_desc">Départ le plus tard</option>
                                <option value="price_asc">Prix croissant</option>
                                <option value="price_desc">Prix décroissant</option>
                            </select>
                        </div>
                    </div>

                    <DateStrip selectedDate={searchDate} onSelectDate={newDate => setSearchDate(newDate)} />

                    {isLoading ? (
                        <div style={{ padding: '60px 20px', textAlign: 'center', color: 'var(--gray)' }}>
                            <div style={{ fontSize: '2rem', marginBottom: '10px' }}>🚆</div>
                            <p>Recherche des meilleurs trajets en cours...</p>
                        </div>
                    ) : displayedVoyages.length === 0 ? (
                        <div style={{ padding: '60px 20px', textAlign: 'center', background: 'var(--white)', borderRadius: '14px', border: '1.5px solid #ede8df', marginTop: '20px' }}>
                            <h3 style={{ color: 'var(--navy)', marginBottom: '10px', fontSize: '1.2rem' }}>Aucun trajet correspondant</h3>
                            <p style={{ color: 'var(--gray)', fontSize: '0.9rem', lineHeight: '1.6' }}>
                                Aucun train ne correspond à vos filtres.<br />
                                <button onClick={handleResetFilters} style={{ marginTop: '10px', color: 'var(--gold)', background: 'none', border: 'none', cursor: 'pointer', fontWeight: 600, fontSize: '0.9rem' }}>
                                    Réinitialiser les filtres
                                </button>
                            </p>
                        </div>
                    ) : (
                        displayedVoyages.map(v => (
                            <TrainCard
                                key={v._id}
                                voyage={v}
                                isSelected={selected?.trainId === v._id}
                                selectedClass={selected?.trainId === v._id ? selected.cls : undefined}
                                isAdded={cartItems.some(item => item.train.trainId === v._id)}
                                isExpanded={expandedId === v._id}
                                onSelectClass={selectClass}
                                onAddToCart={addToCart}
                                onToggleExpand={() => setExpandedId(expandedId === v._id ? null : v._id)}
                                calcArrival={calcArrival}
                            />
                        ))
                    )}
                </div>

                {/* ── CART PANEL ── */}
                <aside className="cart-panel">
                    <div className="cart-title">
                        Mon panier
                        <span style={{ fontSize: '0.75rem', color: 'var(--gray)', fontWeight: 400 }}>{cartCount} billet{cartCount > 1 ? 's' : ''}</span>
                    </div>
                    <div className="session-timer">
                        <div>
                            <div style={{ fontSize: '0.7rem', color: 'var(--gray)' }}>Votre session expire dans</div>
                            <span className={`timer-count${isUrgent ? ' urgent' : ''}`}>{timerStr}</span>
                        </div>
                    </div>
                    {!selected ? (
                        <div className="cart-empty">
                            <img src={boxGSvg} alt="" />
                            <p>Sélectionnez un train et une classe pour configurer votre réservation</p>
                            {cartCount > 0 && (
                                <button className="btn-checkout" onClick={() => navigate('/cart')} style={{ marginTop: '15px' }}>
                                    Voir mon panier ({cartCount})
                                </button>
                            )}
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
                            </div>
                            <button className="btn-checkout" onClick={() => navigate('/booking', { state: { train: selected } })}>
                                Configurer les passagers
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    )}
                </aside>
            </div>

            {/* ── SESSION MODAL ── */}
            {sessionExp && (
                <div id="sessionModal" style={{ display: 'flex' }}>
                    <div style={{ background: 'white', borderRadius: 20, padding: 48, textAlign: 'center', maxWidth: 400 }}>
                        <h3 style={{ fontFamily: "'Playfair Display', serif", fontSize: '1.4rem', marginBottom: 12, color: 'var(--navy)' }}>Session expirée</h3>
                        <p style={{ color: 'var(--gray)', fontSize: '0.9rem', marginBottom: 28, lineHeight: 1.6 }}>Votre session a expiré pour des raisons de sécurité.</p>
                        <button onClick={prolongSession} style={{ padding: '12px 32px', background: 'var(--navy)', color: 'white', border: 'none', borderRadius: 10, fontFamily: "'DM Sans', sans-serif", fontSize: '0.9rem', fontWeight: 600, cursor: 'pointer' }}>
                            Prolonger la session
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}