import React from "react";
import { Typography, Container } from "@mui/material";

function NotFound() {
  return (
    <Container maxWidth="sm">
      <Typography variant="h4" component="h1" align="center" gutterBottom>
        404 - Página no encontrada
      </Typography>
      <Typography variant="body1" align="center">
        Lo sentimos, la página que estás buscando no existe.
      </Typography>
    </Container>
  );
}

export default NotFound;
