import React from "react";
import { Grid, Typography, Paper } from "@mui/material";
import ServerSideTable from "../../components/ServerSideTable/ServerSideTable";

function Dashboard() {
  return (
    <>
      <Grid container spacing={5}>
        <Grid item xs={12}>
          <ServerSideTable apiURL="http://api.localhost/" />
        </Grid>
        <Grid item xs={12}>
          <Typography variant="h4" component="h1" align="center" gutterBottom>
            Dashboard
          </Typography>
        </Grid>
        <Grid item xs={12} sm={6} md={4}>
          <Paper elevation={3} sx={{ p: 2 }}>
            <Typography variant="h6" component="h2" gutterBottom>
              Estadísticas
            </Typography>
            <Typography variant="body1">
              Aquí puedes mostrar tus estadísticas y datos importantes.
            </Typography>
          </Paper>
        </Grid>
        <Grid item xs={12} sm={6} md={4}>
          <Paper elevation={3} sx={{ p: 2 }}>
            <Typography variant="h6" component="h2" gutterBottom>
              Gráficos
            </Typography>
            <Typography variant="body1">
              Aquí puedes mostrar tus gráficos y visualizaciones de datos.
            </Typography>
          </Paper>
        </Grid>
        <Grid item xs={12} md={4}>
          <Paper elevation={3} sx={{ p: 2 }}>
            <Typography variant="h6" component="h2" gutterBottom>
              Tareas
            </Typography>
            <Typography variant="body1">
              Aquí puedes mostrar una lista de tus tareas pendientes.
            </Typography>
          </Paper>
        </Grid>
      </Grid>
    </>
  );
}

export default Dashboard;
