// App.js

import React from 'react';
import { BrowserRouter as Router } from 'react-router-dom';
import { AuthProvider } from './auth/Authcontext';
import AppRoutes from "./routes";
import "./App.css"
function App() {
  return (
     <Router>
        <AuthProvider>
          {/* <Router> */}
            <div className="App">
              {/* Otros componentes o elementos */}
              {/* <header>...</header> */}
              <main>
                {/* Agregar las rutas */}
                <AppRoutes />
              </main>
              {/* <footer>...</footer> */}
            </div>
          {/* </Router> */}
        </AuthProvider>
     </Router>
  );
}

export default App;