import { useState } from 'react'
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';

// Import your components
import Login from './pages/Login';
import Signup from './pages/Signup';
import Tickets from './pages/Tickets';

export default function App() {
  return (
    <Router>
      <Routes>
        {/* Temporary Home route for testing purposes */}
        <Route 
          path="/" 
          element={
            <div style={{ padding: '50px', textAlign: 'center', fontFamily: 'sans-serif' }}>
              <h1>Home Page</h1>
              <p style={{ color: '#666' }}>En cours de développement par une autre équipe...</p>
              <div style={{ marginTop: '20px', display: 'flex', gap: '15px', justifyContent: 'center' }}>
                <Link to="/login" style={{ padding: '10px 20px', background: '#0055A4', color: 'white', textDecoration: 'none', borderRadius: '5px' }}>
                  Tester Login
                </Link>
                <Link to="/signup" style={{ padding: '10px 20px', background: '#e05252', color: 'white', textDecoration: 'none', borderRadius: '5px' }}>
                  Teste Signup
                </Link>
                <Link to="/tickets" style={{ padding: '10px 20px', background: '#d452e0', color: 'white', textDecoration: 'none', borderRadius: '5px' }}>
                  Teste Tickets
                </Link>
              </div>
            </div>
          } 
        />

        {/* Your actual routes */}
        <Route path="/login" element={<Login />} />
        <Route path="/signup" element={<Signup />} />
        <Route path='/tickets' element={<Tickets/>}/>
      </Routes>
    </Router>
  );
}
 
