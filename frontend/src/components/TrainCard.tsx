import React from "react";
import type { Voyage } from "../Voyage";
import type { TrainCardProps } from "../TrainCardProps";


export default function TrainCard({
    voyage,
    isSelected,
    selectedClass,
    isAdded,
    isExpanded,
    onSelectClass,
    onAddToCart,
    onToggleExpand,
    calcArrival
}: TrainCardProps) {
    const trainNum = voyage.num ?? `TGV INOUI № ${voyage._id.slice(-4)}`;

    return(
        <div className={`train-card${isAdded ? ' selected' : ''}`}>
      <div className="train-card-main">

        <div className="train-number">
          <div className="train-label">Train</div>
          <div className="train-num-badge">{trainNum}</div>
        </div>

        <div className="train-timeline">
          <div className="train-time">
            <div className="train-hour">{voyage.date_depart}</div>
            <div className="train-station">{voyage.depart}</div>
          </div>
          <div className="train-line">
            <div className="train-duration">{voyage.temps_arriver}</div>
            <div className="train-track"></div>
            <div className="train-direct" style={{ color: '#2d9e6b' }}>✓ Direct</div>
          </div>
          <div className="train-time">
            <div className="train-hour">{calcArrival(voyage.date_depart, voyage.temps_arriver)}</div>
            <div className="train-station">{voyage.arriver}</div>
          </div>
        </div>

        <div className="train-classes">
          <div
            className={`class-btn${isSelected && selectedClass === '2' ? ' selected' : ''}`}
            onClick={() => onSelectClass(voyage, '2')}
          >
            <div className="class-label">Classe</div>
            <div className="class-name">2ème</div>
            <div className="class-price">{voyage.prix}€</div>
            <div className="class-seats" style={{ color: '#2d9e6b' }}>Places dispo.</div>
          </div>
          <div
            className={`class-btn${isSelected && selectedClass === '1' ? ' selected' : ''}`}
            onClick={() => onSelectClass(voyage, '1')}
          >
            <div className="class-label">Classe</div>
            <div className="class-name">1ère</div>
            <div className="class-price">{Math.round(voyage.prix * 2.2)}€</div>
            <div className="class-seats" style={{ color: '#2d9e6b' }}>Places dispo.</div>
          </div>
        </div>

        <button
          className="train-add-btn"
          onClick={() => onAddToCart(voyage._id)}
          disabled={!isSelected || isAdded}
          style={{ opacity: isSelected ? 1 : 0.4, cursor: isSelected ? 'pointer' : 'not-allowed' }}
        >
          {isAdded ? '✓ Ajouté' : 'Ajouter →'}
        </button>
      </div>

      <button
        className="expand-toggle"
        onClick={onToggleExpand}
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
    )

}