import React from "react";
import { Typography, Container } from "@mui/material";

function UnauthorizedPage() {
  return (
    <Container maxWidth="sm">
      <Typography variant="h4" component="h1" align="center" gutterBottom>
        403 - Acceso no autorizado
      </Typography>
      <Typography variant="body1" align="center">
        Acceso no autorizado
      </Typography>
    </Container>
  );
}

export default UnauthorizedPage;
