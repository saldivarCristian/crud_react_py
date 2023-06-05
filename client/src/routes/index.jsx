// routes/index.js

import React from "react";
import { Route, Switch } from "react-router-dom";
import Home from "../pages/Home/Home";
import NotFound from "../pages/NotFound/NotFound";

// Importar las rutas específicas de admin
import AdminRoutes from "./admin/AdminRoutes";

const Routes = () => {
  return (
    <Switch>
      <Route exact path="/" component={Home} />

      {/* Rutas para admin */}
      <Route path="/admin" component={AdminRoutes} />

      {/* Ruta para manejar 404 - Not Found */}
      <Route component={NotFound} />
    </Switch>
  );
};

export default Routes;
