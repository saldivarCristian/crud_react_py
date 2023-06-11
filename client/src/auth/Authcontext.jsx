// auth/AuthContext.js

import React, { createContext, useState } from "react";

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [userRole, setUserRole] = useState("");

  const login = () => {
    // Lógica para realizar el inicio de sesión y obtener los datos del usuario
    // Actualizar los estados de isAuthenticated y userRole según los datos del usuario
    setIsAuthenticated(true);
    setUserRole("admin");
  };

  const logout = () => {
    // Lógica para cerrar sesión
    // Actualizar los estados de isAuthenticated y userRole
    setIsAuthenticated(false);
    setUserRole("");
  };

  const checkRolePermission = (requiredRole) => {
    // Verificar si el rol del usuario tiene permiso para acceder a cierta funcionalidad
    return userRole === requiredRole;
  };

  return (
    <AuthContext.Provider
      value={{
        isAuthenticated,
        userRole,
        login,
        logout,
        checkRolePermission,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};
