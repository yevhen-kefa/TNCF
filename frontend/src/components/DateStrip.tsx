import React, { useMemo } from "react";

interface DateStringProps {
    selectedDate: string;
    onSelectDate: (date: string) => void;
}

export const getMinPriceForDate = (dateStr: string) => {
  let hash = 0;
  for (let i = 0; i < dateStr.length; i++) {
    hash = dateStr.charCodeAt(i) + ((hash << 5) - hash);
  }
  // Generates a base price between 29€ and 68€ based on the date string
  return Math.abs(hash) % 20 + 19; 
};

// Helper to format date safely
const getLocalYMD = (date: Date) => {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
};

export default function DateStrip({selectedDate, onSelectDate}: DateStringProps) {

    const days = useMemo(() => {
        const res = [];
        const today = new Date();
        today.setHours(12, 0, 0, 0);

        const [y, m, d] = selectedDate.split('-').map(Number);
        const current = new Date(y, m - 1, d, 12, 0, 0);

        let start = new Date(current);
        start.setDate(start.getDate() - 2);
        
        const startDayOnly = new Date(start.getFullYear(), start.getMonth(), start.getDate());
        const todayDayOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        
        if (startDayOnly < todayDayOnly) {
        start = new Date(today);
        }

        for (let i = 0; i < 7; i++) {
            const currentDateObj = new Date(start);
            currentDateObj.setDate(start.getDate() + i);
        
            const dateStr = getLocalYMD(currentDateObj);
      
            const formatter = new Intl.DateTimeFormat('fr-FR', { weekday: 'short', day: 'numeric' });
            let formattedDate = formatter.format(currentDateObj);
            formattedDate = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1).replace('.', '');

            res.push({
                dateStr,
                label: formattedDate,
                price: getMinPriceForDate(dateStr)
            });
                }
        return res;
    }, [selectedDate]);

    return(
        <div className="cal-section">
            <div className="cal-strip">
                {days.map((day) => {
                const isActive = day.dateStr === selectedDate;
                return (
                    <div 
                    key={day.dateStr}
                    className={`cal-day ${isActive ? 'active' : ''}`}
                    onClick={() => onSelectDate(day.dateStr)}
                    >
                    <div className="cal-dow">{day.label}</div>
                    <div className="cal-price">{day.price} €</div>
                    </div>
                );
                })}
            </div>
        </div>
    );
}