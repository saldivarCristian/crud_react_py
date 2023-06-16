<?php
namespace Clases\Admin\General;
use \PDO;
class Barrios {
    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param array $verComlumnas si se quiere obtener una columna
     * @return void
     */
    public static function listar(pdo $db, $verComlumnas=[]): array
    {
        
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",:", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".base_ciudades";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }
}