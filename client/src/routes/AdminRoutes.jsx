import React, { useContext } from "react";
import { Route, Routes, Navigate } from "react-router-dom";
import AdminDashboard from "../pages/dashboard/Dashboard";
import AdminUser from "../pages/adminUser/AdminUser";
import { AuthContext } from "../auth/Authcontext";
import UnauthorizedPage from "../pages/unauthorized/Unauthorized";
import Navbar from "../layouts/Navbar";
import { Box } from "@mui/material";

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
    <>
      <Navbar />
      <Box marginTop="20px">
        <Routes>
          <Route path="/unauthorized" element={<UnauthorizedPage />} />
          <Route path="/*" element={<ProtectedRouteWrapper />} />
        </Routes>
      </Box>
    </>
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
