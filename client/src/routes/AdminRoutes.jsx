import React, { useContext } from "react";
import { Route, Routes, Navigate } from "react-router-dom";
import AdminDashboard from "../pages/dashboard/Dashboard";
import AdminUser from "../pages/adminUser/AdminUser";
import NotFound from "../pages/notFound/NotFound";
import { AuthContext } from "../auth/Authcontext";
import UnauthorizedPage from "../pages/unauthorized/Unauthorized";

const ProtectedRoute = ({ path, element: Component, requiredRole }) => {
  const { isAuthenticated, userRole } = useContext(AuthContext);

  if (!isAuthenticated) {
    return <Navigate to="/login" />;
  }

  if (userRole !== requiredRole) {
    return <Navigate to="/admin/unauthorized" />;
  }

  return (
    <Routes>
      <Route path={path} element={<Component />} />
    </Routes>
  );
};

const AdminRoutes = () => {
  return (
    <Routes>
      <Route path="/unauthorized" element={<UnauthorizedPage/>} />
      <Route path="/*" element={<ProtectedRouteWrapper />} />
    </Routes>
  );
};

const ProtectedRouteWrapper = () => {
  return (
    <>
      <ProtectedRoute path="/" element={AdminDashboard} requiredRole="admin" />
      <ProtectedRoute
        path="/user/:id"
        element={AdminUser}
        requiredRole="admin"
      />
    </>
  );
};


export default AdminRoutes;
