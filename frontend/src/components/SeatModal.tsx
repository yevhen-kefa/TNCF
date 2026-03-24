import React, { useState, useEffect } from 'react';

type SeatStatus = 'available' | 'occupied';
type SeatType = 'standard' | 'table';

export interface Seat {
  id: string;
  wagon: number;
  number: string;
  status: SeatStatus;
  type: SeatType;
}

interface Row {
  id: string;
  seats: Seat[];
}

interface RowGroup {
  id: string;
  type: SeatType;
  rows: Row[];
}

interface Wagon {
  id: number;
  groups: RowGroup[];
  isFirstClass?: boolean;
}


//Logic Generate trains
const generateTrain = (): Wagon[] => {
  const numWagons = Math.floor(Math.random() * 3) + 5;
  const wagons: Wagon[] = [];

  for (let w = 1; w <= numWagons; w++) {
    const groups: RowGroup[] = [];
    let rowNum = 1;
    
    const isFirstClass = w <= 2; 

    while (rowNum <= 14) {
      const isTable = rowNum < 14 && Math.random() > 0.65;

      const generateRow = (rNum: number, sType: SeatType): Row => {
        const letters = isFirstClass ? ['A', 'C', 'D'] : ['A', 'B', 'C', 'D'];
        
        return {
          id: `W${w}-R${rNum}`,
          seats: letters.map(letter => ({
            id: `W${w}-${rNum}${letter}`,
            wagon: w,
            number: `${rNum}${letter}`,
            type: sType,
            status: Math.random() > 0.65 ? 'occupied' : 'available',
          }))
        };
      };

      if (isTable) {
        groups.push({
          id: `W${w}-G${rowNum}-table`,
          type: 'table',
          rows: [generateRow(rowNum, 'table'), generateRow(rowNum + 1, 'table')]
        });
        rowNum += 2;
      } else {
        groups.push({
          id: `W${w}-G${rowNum}-std`,
          type: 'standard',
          rows: [generateRow(rowNum, 'standard')]
        });
        rowNum++;
      }
    }
    
    wagons.push({ id: w, groups, isFirstClass }); 
  }
  return wagons;
};



export default function SeatMapModal({ 
  isOpen, 
  onClose, 
  onConfirm, 
  maxSeats = 1,
  currentSelection = []
}: { 
  isOpen: boolean; 
  onClose: () => void; 
  onConfirm: (seats: Seat[]) => void;
  maxSeats?: number;
  currentSelection?: Seat[];
}) {
  const [wagons, setWagons] = useState<Wagon[]>([]);
  const [activeWagonIdx, setActiveWagonIdx] = useState(0);
  const [selectedSeats, setSelectedSeats] = useState<Seat[]>([]);

  useEffect(() => {
    if (isOpen) {
        if(wagons.length === 0){
            setWagons(generateTrain());
            setActiveWagonIdx(0);
        }
      setSelectedSeats(currentSelection);
    }
  }, [isOpen]);

  if (!isOpen || wagons.length === 0) return null;

  const activeWagon = wagons[activeWagonIdx];

  const toggleSeat = (seat: Seat) => {
      if (seat.status === 'occupied') return;
      
      setSelectedSeats(prev => {
        const isSelected = prev.some(s => s.id === seat.id);
        if (isSelected) {
            return prev.filter(s => s.id !== seat.id);
        } else {
            if (prev.length >= maxSeats) {
              return [seat]; 
            }
            return [...prev, seat];
        }
      });
  };

  const isSeatSelected = (seatId: string) => selectedSeats.some(s => s.id === seatId);

  const renderSeatRow = (row: Row) => {
    const isFirstClass = row.seats.length === 3;
    
    const leftSeats = isFirstClass ? [row.seats[0]] : [row.seats[0], row.seats[1]];
    const rightSeats = isFirstClass ? [row.seats[1], row.seats[2]] : [row.seats[2], row.seats[3]];

    return (
      <div className="seat-row" key={row.id}>
        <div className="seat-pair">
          {leftSeats.map(seat => (
            <SeatButton key={seat.id} seat={seat} isSelected={isSeatSelected(seat.id)} onClick={() => toggleSeat(seat)} />
          ))}
        </div>
        <div className="seat-aisle">Couloir</div>
        <div className="seat-pair">
          {rightSeats.map(seat => (
            <SeatButton key={seat.id} seat={seat} isSelected={isSeatSelected(seat.id)} onClick={() => toggleSeat(seat)} />
          ))}
        </div>
      </div>
    );
  };

  return (
    <div className="seat-modal-overlay" onClick={onClose}>
      <div className="seat-modal-container" onClick={(e) => e.stopPropagation()}>
        
        {/* HEADER */}
        <div className="seat-modal-header">
          <h2 className="seat-modal-title">Choix des places</h2>
          <button className="seat-modal-close" onClick={onClose}>✕</button>
        </div>

        <div className="seat-modal-body">
          
          {/* LEFT: TRAIN MAP */}
          <div className="seat-map-section">
            
            {/* Wagon Navigation */}
            <div className="seat-map-nav">
              <button 
                disabled={activeWagonIdx === 0}
                onClick={() => setActiveWagonIdx(prev => prev - 1)}
                className="btn-wagon-nav"
              >
                ← Précédent
              </button>
              <div className="wagon-title">
                Voiture {activeWagon.id} <span style={{fontSize: '0.8rem', color: 'var(--gray)', fontWeight: 'normal'}}>({activeWagon.isFirstClass ? '1ère classe' : '2ème classe'})</span>
              </div>
              <button 
                disabled={activeWagonIdx === wagons.length - 1}
                onClick={() => setActiveWagonIdx(prev => prev + 1)}
                className="btn-wagon-nav"
              >
                Suivant →
              </button>
            </div>

            {/* Legend */}
            <div className="seat-map-legend">
              <div className="legend-item"><div className="legend-box available"></div>Libre</div>
              <div className="legend-item"><div className="legend-box selected"></div>Sélectionnée</div>
              <div className="legend-item"><div className="legend-box occupied"></div>Occupée</div>
            </div>

            {/* Scrollable Interior */}
            <div className="seat-map-scroll">
              <div className="train-car-container">
                <div className="train-direction">Sens de la marche 🡑</div>

                <div className="train-groups">
                  {activeWagon.groups.map(group => (
                    <div key={group.id} className="train-group">
                      
                      {group.type === 'table' ? (
                        // TABLE LAYOUT
                        <div className="table-group">
                          {renderSeatRow(group.rows[0])}
                          
                          <div className="table-graphic">
                            <div className="table-surface" style={{ width: activeWagon.isFirstClass ? '44px' : '90px' }}></div>
                            <div className="table-surface"></div>
                          </div>

                          {renderSeatRow(group.rows[1])}
                        </div>
                      ) : (
                        // STANDARD LAYOUT
                        renderSeatRow(group.rows[0])
                      )}
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>

          {/* RIGHT: SUMMARY SIDEBAR */}
          <div className="seat-summary-section">
            <h3 className="summary-title">Votre sélection</h3>
            
            <div className="summary-list">
              {selectedSeats.length === 0 ? (
                <div className="summary-empty">
                  Sélectionnez vos places sur le plan à gauche
                </div>
              ) : (
                selectedSeats.map(seat => (
                  <div key={seat.id} className="selected-seat-item">
                    <div>
                      <div className="selected-seat-num">Place {seat.number}</div>
                      <div className="selected-seat-desc">Voiture {seat.wagon} • {seat.type === 'table' ? 'Carré (Table)' : 'Standard'}</div>
                    </div>
                    <button className="remove-seat-btn" onClick={() => toggleSeat(seat)}>✕</button>
                  </div>
                ))
              )}
            </div>

            <div className="summary-footer">
              <div className="summary-total-row">
                <span>Total places :</span>
                <span className="summary-total-num">{selectedSeats.length} / {maxSeats}</span>
              </div>
              <button 
                disabled={selectedSeats.length === 0}
                onClick={() => {
                  onConfirm(selectedSeats);
                  onClose();
                }}
                className="btn-confirm-seats"
              >
                Valider
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>
  );
}

function SeatButton({ seat, isSelected, onClick }: { seat: Seat; isSelected: boolean; onClick: () => void }) {
  let statusClass = "available";
  if (seat.status === 'occupied') statusClass = "occupied";
  else if (isSelected) statusClass = "selected";

  return (
    <button 
      onClick={onClick}
      disabled={seat.status === 'occupied'}
      className={`seat-btn ${statusClass}`}
      title={seat.status === 'occupied' ? 'Place occupée' : `Voiture ${seat.wagon}, Place ${seat.number}`}
    >
      <div className="seat-headrest"></div>
      {seat.number}
    </button>
  );
}