import React, { createContext, useState } from "react";
import { useNavigate } from "react-router-dom";

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [isAuthenticated, setIsAuthenticated] = useState(
    localStorage.getItem("isAuthenticated") === "true"
  );
  const [userRole, setUserRole] = useState(
    localStorage.getItem("userRole") || ""
  );

  const navigate = useNavigate();

  const login = (rules = 'admin') => {
    // Lógica para realizar el inicio de sesión y obtener los datos del usuario
    // Actualizar los estados de isAuthenticated y userRole según los datos del usuario

    // Guardar los datos en el almacenamiento local
    localStorage.setItem("isAuthenticated", true);
    localStorage.setItem("userRole", rules);

    setIsAuthenticated(true);
    setUserRole(rules);

    // Obtener la función navigate del hook useNavigate
    // Redirigir al usuario a la página de administración
    navigate("/admin");
  };

  const logout = () => {
    // Lógica para cerrar sesión
    // Actualizar los estados de isAuthenticated y userRole

    // Eliminar los datos del almacenamiento local
    localStorage.removeItem("isAuthenticated");
    localStorage.removeItem("userRole");

    setIsAuthenticated(false);
    setUserRole("");
        navigate("/");

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
