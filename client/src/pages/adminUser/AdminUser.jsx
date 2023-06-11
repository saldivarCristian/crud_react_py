import React from "react";
import {
  Typography,
  Paper,
  List,
  ListItem,
  ListItemText,
  ListItemIcon,
} from "@mui/material";
import { AccountCircle, Email, Phone } from "@mui/icons-material";

function AdminUser() {
  return (
    <Paper elevation={3} sx={{ p: 2 }}>
      <Typography variant="h6" component="h2" gutterBottom>
        Información del Usuario
      </Typography>
      <List>
        <ListItem>
          <ListItemIcon>
            <AccountCircle />
          </ListItemIcon>
          <ListItemText primary="Nombre" secondary="John Doe" />
        </ListItem>
        <ListItem>
          <ListItemIcon>
            <Email />
          </ListItemIcon>
          <ListItemText
            primary="Correo Electrónico"
            secondary="johndoe@example.com"
          />
        </ListItem>
        <ListItem>
          <ListItemIcon>
            <Phone />
          </ListItemIcon>
          <ListItemText primary="Teléfono" secondary="123-456-7890" />
        </ListItem>
      </List>
    </Paper>
  );
}

export default AdminUser;
