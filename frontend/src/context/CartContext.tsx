import React, { createContext, useContext, useState, useEffect } from 'react';
import type { SelectedTrain } from '../SelectedTrain';
import type { PassengerForm } from '../PassengerForm';
import type { ContactForm } from '../ContactForm';
import type { Seat } from '../components/SeatModal';

export interface BaggageOptions {
  extra: number;
  special: number;
}

export interface CartItem {
  id: string; // Unique ID for the cart item
  train: SelectedTrain;
  passenger: PassengerForm;
  contact: ContactForm;
  seatMode: 'random' | 'specific';
  specificSeats: Seat[];
  baggage: BaggageOptions;
  total: number;
}

interface CartContextType {
  cartItems: CartItem[];
  addToCart: (item: CartItem) => void;
  removeFromCart: (itemId: string) => void;
  clearCart: () => void;
}

const CartContext = createContext<CartContextType | undefined>(undefined);

export const CartProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [cartItems, setCartItems] = useState<CartItem[]>(() => {
    const saved = localStorage.getItem('tncf_cart');
    if (saved) {
      try {
        return JSON.parse(saved);
      } catch (e) {
        return [];
      }
    }
    return [];
  });

  useEffect(() => {
    localStorage.setItem('tncf_cart', JSON.stringify(cartItems));
  }, [cartItems]);

  const addToCart = (item: CartItem) => {
    setCartItems(prev => [...prev, item]);
  };

  const removeFromCart = (itemId: string) => {
    setCartItems(prev => prev.filter(item => item.id !== itemId));
  };

  const clearCart = () => {
    setCartItems([]);
  };

  return (
    <CartContext.Provider value={{ cartItems, addToCart, removeFromCart, clearCart }}>
      {children}
    </CartContext.Provider>
  );
};

export const useCart = () => {
  const context = useContext(CartContext);
  if (context === undefined) {
    throw new Error('useCart must be used within a CartProvider');
  }
  return context;
};
