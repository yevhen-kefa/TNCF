import type { Voyage } from "./Voyage";

export interface TrainCardProps {
  voyage: Voyage;
  isSelected: boolean;
  selectedClass?: '1' | '2';
  isAdded: boolean;
  isExpanded: boolean;
  onSelectClass: (v: Voyage, cls: '1' | '2') => void;
  onAddToCart: (trainId: string) => void;
  onToggleExpand: () => void;
  calcArrival: (dep: string, duration: string) => string;
}