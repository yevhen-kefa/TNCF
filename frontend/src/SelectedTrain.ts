export interface SelectedTrain {
  trainId: string;
  cls: '1' | '2';
  price: number;
  num: string;
  dep: string;
  from: string;
  to: string;
  date: string; // added — format YYYY-MM-DD
}