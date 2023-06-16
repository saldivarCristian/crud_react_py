<?php
namespace Clases\Admin\Calpoliticos;
use \PDO;
// print_r($settings);
class Calificadores {
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
        $sql = "SELECT $columna FROM ".DB_CON.".calautos_calificador";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_calificador filtar encuentros por id_calificador
     * @return void
     */
    public static function getDataByNombre($db, $id)
    {
        $sqlCliente = "SELECT
                   *
                FROM ".DB_CON.".calautos_calificador WHERE id_calificador = '$id'";
        $stmt = $db->query($sqlCliente);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_calificador filtar encuentros por id_calificador
     * @return void
     */
    public static function getDataByCuil($db, $id_calificador)
    {
        $sql = "SELECT
                   *
                FROM ".DB_CON.".calautos_calificador WHERE id_calificador = '$id_calificador'";
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
            INSERT INTO ".DB_CON.".calautos_calificador (
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
            update ".DB_CON.".calautos_calificador set
                $columnaActualizar
           where id_calificador = $id_update
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
            DELETE FROM ".DB_CON.".calautos_calificador where id_calificador = $id_update
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
     * @param [type] $id_calificador
     * @param array $verComlumnas
     * @return array
     */
    public static function getDataById(pdo $db, $id_calificador, $verComlumnas=[]): object
    {
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".calautos_calificador WHERE id_calificador = $id_calificador";
        $stmt = $db->query($sql);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param pdo $db
     * @param [type] $id_calificador
     * @param array $verComlumnas
     * @return array
     */
    public static function listFormacionById(pdo $db, $id_calificador)
    { 
        $sql = "SELECT clase id, orden_seleccion_auto orden, nombre_auto nombre, id_auto,imagen_auto FROM ".DB_CON.". calautos_seleccion_autos  WHERE id_calificador = $id_calificador";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function insertarSeleccionAuto($db,$obj = [])
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

    public function eliminarSeleccionFormacion($db,$id_update)
    {        
        $sql = "
            DELETE FROM ".DB_CON.".calautos_seleccion_autos where id_calificador = $id_update
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $id = $db->lastInsertId();
        return $id;
    }

    
    /**
     * listarProximoEncuentro
     * Se va a listar el encuentro proximo sigun fecha de servidor
     * @param  mixed $db
     * @return void
     */
    public static function listarProximoCalificadorFormacion($db)
    {
        $fechaActual = date("d-m-Y H:i:00");
        $fecha = date("Y-m-d");
        $sqlCliente = "
            SELECT    
               id_calificador,estado_actual_calificador,fecha_calificador,dia_calificador
               ,hora_calificador,tipo_formacion,clase_formacion,f.insert_local
            FROM ".DB_BASE.".calautos_calificador f 
            left join ".DB_BASE.".calautos_formaciones fo on fo.id_formacion = f.id_formacion
            WHERE f.estado_calificador = 0 and f.estado_actual_calificador !=2 
            ORDER BY f.dia_calificador asc, f.hora_calificador asc 
        ";

        // and f.dia_encuentro < '$fecha'
        
        $stmt = $db->query($sqlCliente);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $proximoCalificador = [];
        $rowFormacion = [];
        $update = "";
        foreach ($data as $key => $value) {
            $dia_partido_pasado = date("d-m-Y H:i", strtotime($value['dia_calificador']." ".$value['hora_calificador']));
            if( strtotime($fechaActual) > strtotime("+100 minutes", strtotime($dia_partido_pasado)) ){
                $update .= "UPDATE ".DB_BASE.".calautos_calificador SET estado_calificador = 4 WHERE id_calificador = ".$value['id_calificador'].";";
            }else{
                $proximoCalificador = $value;
                $rowFormacion = self::listFormacionById($db, $value['id_calificador']);
                break;
            }
        }

        if( $update != ""){
            $stmt = $db->query($update);
        }

        return ['calificador' => $proximoCalificador, 'formacion' =>$rowFormacion ];
    }
   
}