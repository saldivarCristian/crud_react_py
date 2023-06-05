// App.js

import React from 'react';
import { BrowserRouter as Router } from 'react-router-dom';
import { AuthProvider } from './auth/AuthContext';
import Routes from './routes';

function App() {
  return (
    <AuthProvider>
      <Router>
        <div className="App">
          {/* Otros componentes o elementos */}
          <header>...</header>
          <main>
            {/* Agregar las rutas */}
            <Routes />
          </main>
          <footer>...</footer>
        </div>
      </Router>
    </AuthProvider>
  );
}

export default App;