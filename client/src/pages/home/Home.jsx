import React from "react";
import { Typography } from "@mui/material";

function Home() {
  return (
    <div>
      <Typography variant="h4" component="h1" align="center" gutterBottom>
        Bienvenido a la página de inicio
      </Typography>
      <Typography variant="body1" align="center">
        Aquí puedes agregar contenido relevante para tu página de inicio.
      </Typography>
    </div>
  );
}

export default Home;
