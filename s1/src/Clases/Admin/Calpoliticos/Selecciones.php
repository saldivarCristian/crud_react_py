<?php
namespace Clases\Admin\Calpoliticos;
use \PDO;
// print_r($settings);
class Selecciones {
    private $log;
    function __construct($log =null) 
    {
        $this->log = $log;
    }

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
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".calautos_seleccion_autos";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $ci filtar seleccion por ci
     * @return void
     */
    public static function getDataByCI($db, $ci)
    {
        $sqlCliente = "SELECT
                   *
                FROM ".DB_CON.".calautos_seleccion_autos WHERE id_seleccion_futbolista = '$ci'";
        $stmt = $db->query($sqlCliente);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_seleccion_futbolista filtar seleccion por id_seleccion_futbolista
     * @return void
     */
    public static function getDataByCuil($db, $id_seleccion_futbolista)
    {
        $sql = "SELECT
                   *
                FROM ".DB_CON.".calautos_seleccion_autos WHERE id_seleccion_futbolista = '$id_seleccion_futbolista'";
        $stmt = $db->query($sql);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    public function insertar($db,$obj = [])
    {
        if(!count($obj)){
            return 0;
        }
        $columnaAInsertar = "";
        $variableAInsertar = "";
        $bandera = 0;
        foreach ($obj as $key => $value) {
            if($bandera == 0){
                $bandera++;
                $columnaAInsertar .= $key;
                $variableAInsertar .= ":".$key;
            }else{
                $columnaAInsertar .= ",".$key;
                $variableAInsertar .= ",:".$key;  
            }
        }
        
        $sql = "
            INSERT INTO ".DB_CON.".calautos_seleccion_autos (
                $columnaAInsertar
            )
            VALUES (
                $variableAInsertar   
            )
        ";
        $stmt = $db->prepare($sql);

        foreach ($obj as $key => $value) {
            $stmt->bindValue(':'.$key, $value);
        }
        try {
            $stmt->execute();
            $id = $db->lastInsertId();
            return $id;
            //code...
        } catch (\Throwable $th) {
            if ($this->log) {
                ob_start();
                $stmt->debugDumpParams();
                $r = ob_get_contents();
                $this->log->error( $r );
                ob_end_clean();
            }
            throw $th;
        }
    }

    public function actualizar($db,$obj = [],$id_update)
    {
        if(!count($obj)){
            return 0;
        }
        $columnaActualizar = "";
        $bandera = 0;
        $update = "";
        foreach ($obj as $key => $value) {
            if($bandera == 0){
                $bandera++;
                $columnaActualizar .= $key." = :".$key;
                $update .= "$key='$value'";
            }else{
                $columnaActualizar .= ",".$key." = :".$key;
                $update .= ",$key='$value'";
            }
        }
        
        $sql = "
            update ".DB_CON.".calautos_seleccion_autos set
                $columnaActualizar
           where id_seleccion_futbolista = $id_update
        ";
   
        $stmt = $db->prepare($sql);
        foreach ($obj as $k => $v) {
            $stmt->bindValue(':'.$k, $v );
        }

        try {
            $stmt->execute();
            $id = $stmt->rowCount();
            return  1;
        } catch (\PDOException $th) {
            if ($this->log) {
                ob_start();
                $stmt->debugDumpParams();
                $r = ob_get_contents();
                $this->log->error( $r );
                ob_end_clean();
            }
            throw $th;
        }
       
    }

    public function eliminar($db,$id_update)
    {        
        $sql = "
            DELETE FROM ".DB_CON.".calautos_seleccion_autos where id_seleccion_futbolista = $id_update
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $id = $db->lastInsertId();
        return $id;
    }

    /**
     * Undocumented function
     *
     * @param pdo $db
     * @param [type] $id_seleccion_futbolista
     * @param array $verComlumnas
     * @return array
     */
    public static function getDataById(pdo $db, $id_seleccion_futbolista, $verComlumnas=[]): object
    {
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".calautos_seleccion_autos WHERE id_seleccion_futbolista = $id_seleccion_futbolista";
        $stmt = $db->query($sql);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }
   
}