import React, { useState, useEffect } from "react";
import { DataGrid, GridToolbar } from "@mui/x-data-grid";
import { createTheme, ThemeProvider } from "@mui/material/styles";
import { Button, Box, Typography } from "@mui/material";
import { saveAs } from "file-saver";
import html2canvas from "html2canvas";
import { jsPDF } from "jspdf";

const theme = createTheme({
  components: {
    MuiDataGrid: {
      styleOverrides: {
        root: {
          backgroundColor: "#f5f5f5",
        },
      },
    },
  },
});

const ServerSideTable = ({ apiURL }) => {
  const [data, setData] = useState([]);
  const [columns, setColumns] = useState([]);
  const [totalRows, setTotalRows] = useState(0);
  const [pageSize, setPageSize] = useState(10);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");

  useEffect(() => {
    fetchData();
  }, []);

  const handleSearch = () => {
  // Obtener los filtros de cada columna
  const columnFilters = columns.map((column) => ({
    field: column.field,
    value: column.filterValue || '',
  }));

  // Actualizar la URL de la solicitud con los filtros
  fetchData(1, columnFilters); // Llamar a fetchData con el número de página y los filtros
};

const fetchData = async (page = 1, filters = []) => {
  try {
    setLoading(true);

    // Construir la URL de la solicitud con los filtros
    const filterParams = filters
      .map((filter) => `filters[]=${encodeURIComponent(JSON.stringify(filter))}`)
      .join('&');
    const response = await fetch(
      `${apiURL}?page=${page}&pageSize=${pageSize}&${filterParams}`
    );
    const jsonData = await response.json();
    setData(jsonData.data);
    setTotalRows(jsonData.totalRows);
    setColumns(jsonData.columns);
    setLoading(false);
  } catch (error) {
    console.error("Error al obtener los datos del servidor:", error);
    setLoading(false);
  }
};

  const handleExportExcel = () => {
    const exportData = data.map((item) => {
      const formattedItem = {};
      for (let key in item) {
        formattedItem[
          columns.find((column) => column.field === key)?.headerName || key
        ] = item[key];
      }
      return formattedItem;
    });

    import("xlsx").then((xlsx) => {
      const worksheet = xlsx.utils.json_to_sheet(exportData);
      const workbook = xlsx.utils.book_new();
      xlsx.utils.book_append_sheet(workbook, worksheet, "Sheet 1");
      const excelBuffer = xlsx.write(workbook, {
        bookType: "xlsx",
        type: "array",
      });
      const dataBlob = new Blob([excelBuffer], {
        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      });
      saveAs(dataBlob, "table.xlsx");
    });
  };

  const handleExportPDF = () => {
    html2canvas(document.getElementById("table-container")).then((canvas) => {
      const imgData = canvas.toDataURL("image/png");
      const pdf = new jsPDF("p", "pt", "a4");
      const width = pdf.internal.pageSize.getWidth();
      const height = pdf.internal.pageSize.getHeight();
      pdf.addImage(imgData, "PNG", 0, 0, width, height);
      pdf.save("table.pdf");
    });
  };

  const handlePrint = () => {
    window.print();
  };

  const handleShareWhatsApp = () => {
    const tableData = data.map((item) => {
      const rowData = [];
      for (let key in item) {
        rowData.push(
          `${
            columns.find((column) => column.field === key)?.headerName || key
          }: ${item[key]}`
        );
      }
      return rowData.join("\n");
    });
    const text = encodeURIComponent(tableData.join("\n\n"));
    window.open(`https://wa.me/?text=${text}`);
  };

  const handlePageChange = (page) => {
    fetchData(page);
  };

  const filterModel = {
   items: [
    {
      columnField: "name", // Campo de columna para realizar la búsqueda (puede ser "id", "name" o "email")
      operatorValue: searchTerm,
      operator: "contains", // Utilizando el operador "contains" para la búsqueda
      value: searchTerm,
      field: "name", // Campo de datos en el servidor para realizar la búsqueda
    },
  ],
        // items: [
        //     { id: 1, field: 'id', operator: '>', value: '4' },
        //     { id: 2, field: 'name', operator: 'is', value: 'true' },
        // ],
        // logicOperator: GridLogicOperator.Or,


  };

  return (
    <ThemeProvider theme={theme}>
      <Box p={2}>
        <Typography variant="h5" component="div" gutterBottom>
          Tabla de datos
        </Typography>
        <Box display="flex" flexDirection="column">
          <Box mb={1}>
            <Button variant="contained" onClick={handleExportExcel}>
              Exportar a Excel
            </Button>
          </Box>
          <Box mb={1}>
            <Button variant="contained" onClick={handleExportPDF}>
              Exportar a PDF
            </Button>
          </Box>
          <Box mb={1}>
            <Button variant="contained" onClick={handlePrint}>
              Imprimir
            </Button>
          </Box>
          <Box mb={1}>
            <Button variant="contained" onClick={handleShareWhatsApp}>
              Compartir por WhatsApp
            </Button>
          </Box>
        </Box>
        <div id="table-container" style={{ height: 400, width: "100%" }}>
          <DataGrid
            rows={data}
            columns={columns}
            pagination
            pageSize={pageSize}
            rowCount={totalRows}
            columnBuffer={columns.length}
            onPageChange={(params) => handlePageChange(params.page)}
            components={{
              Toolbar: GridToolbar,
            }}
            filterModel={filterModel}
            checkboxSelection
            disableSelectionOnClick
            loading={loading}
            autoHeight
          />
        </div>
      </Box>
    </ThemeProvider>
  );
};

export default ServerSideTable;
