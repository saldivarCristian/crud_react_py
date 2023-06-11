import React, { useContext } from "react";
import { Route, Navigate, Routes } from "react-router-dom";
import AdminDashboard from "../pages/dashboard/Dashboard";
import AdminUser from "../pages/adminUser/AdminUser";
import NotFound from "../pages/notFound/NotFound";
import { AuthContext } from "../auth/Authcontext";

const ProtectedRoute = ({ path, element: Component, requiredRole }) => {
  const { isAuthenticated, userRole } = useContext(AuthContext);

  if (!isAuthenticated) {
    return <Navigate to="/login" />;
  }

  if (userRole !== requiredRole) {
    return <Navigate to="/admin" />;
  }

  return <Route path={path} element={<Component />} />;
};

const AdminRoutes = () => {
  return (
    <Routes>

      <Route
        path="/"
        element={
          <ProtectedRoute
            path="/admin"
            element={AdminDashboard}
            requiredRole="admin"
          />
        }
      />
      <Route
        path="/user/:id"
        element={
          <ProtectedRoute
            path="/admin/user/:id"
            element={AdminUser}
            requiredRole="admin"
          />
        }
      />
      <Route path="*" element={<NotFound />} />
    </Routes>
  );
};

export default AdminRoutes;
