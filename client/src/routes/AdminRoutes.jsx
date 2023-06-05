// routes/admin/AdminRoutes.js

import React, { useContext } from "react";
import { Route, Redirect } from "react-router-dom";
import AdminDashboard from "./AdminDashboard";
import AdminUser from "./AdminUser";
import NotFound from "../NotFound/NotFound";
import { AuthContext } from "../../auth/AuthContext";

const ProtectedRoute = ({ component: Component, requiredRole, ...rest }) => {
  const { isAuthenticated, userRole } = useContext(AuthContext);

  return (
    <Route
      {...rest}
      render={(props) =>
        isAuthenticated && userRole === requiredRole ? (
          <Component {...props} />
        ) : (
          <Redirect to="/login" />
        )
      }
    />
  );
};

const AdminRoutes = () => {
  return (
    <Switch>
      <ProtectedRoute
        exact
        path="/admin"
        component={AdminDashboard}
        requiredRole="admin"
      />
      <ProtectedRoute
        path="/admin/user/:id"
        component={AdminUser}
        requiredRole="admin"
      />

      <Route component={NotFound} />
    </Switch>
  );
};

export default AdminRoutes;
