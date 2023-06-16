<?php
namespace Clases\Admin\Votapoliticos;
use \PDO;
// print_r($settings);
class EleccionesCargos {
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
        $sql = "SELECT $columna FROM ".DB_CON.".votapoliticos_elecciones_cargos";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_elec_cargo filtar encuentros por id_elec_cargo
     * @return void
     */
    public static function getDataByNombre($db, $id)
    {
        $sqlCliente = "SELECT
                   *
                FROM ".DB_CON.".votapoliticos_elecciones_cargos WHERE id_elec_cargo = '$id'";
        $stmt = $db->query($sqlCliente);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_elec_cargo filtar encuentros por id_elec_cargo
     * @return void
     */
    public static function getDataByCuil($db, $id_elec_cargo)
    {
        $sql = "SELECT
                   *
                FROM ".DB_CON.".votapoliticos_elecciones_cargos WHERE id_elec_cargo = '$id_elec_cargo'";
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
            INSERT INTO ".DB_CON.".votapoliticos_elecciones_cargos (
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
            update ".DB_CON.".votapoliticos_elecciones_cargos set
                $columnaActualizar
           where id_elec_cargo = $id_update
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
            DELETE FROM ".DB_CON.".votapoliticos_elecciones_cargos where id_elec_cargo = $id_update
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
     * @param [type] $id_elec_cargo
     * @param array $verComlumnas
     * @return array
     */
    public static function getDataById(pdo $db, $id_elec_cargo, $verComlumnas=[]): object
    {
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".votapoliticos_elecciones_cargos WHERE id_elec_cargo = $id_elec_cargo";
        $stmt = $db->query($sql);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param pdo $db
     * @param [type] $id_elec_cargo
     * @param array $verComlumnas
     * @return array
     */
    public static function listFormacionById(pdo $db, $id_elec_cargo)
    { 
        $sql = "SELECT clase id, orden_seleccion_politico orden, nombre_politico nombre, id_politico,imagen_politico FROM ".DB_CON.". votapoliticos_seleccion_politicos  WHERE id_elec_cargo = $id_elec_cargo";
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
            INSERT INTO ".DB_CON.".votapoliticos_seleccion_politicos (
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
            DELETE FROM ".DB_CON.".votapoliticos_seleccion_politicos where id_elec_cargo = $id_update
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
               id_elec_cargo,estado_elec_cargo,fecha_elec_cargo,dia_elec_cargo
               ,hora_elec_cargo,tipo_formacion,clase_formacion,f.insert_local
            FROM ".DB_BASE.".votapoliticos_elecciones_cargos f 
            left join ".DB_BASE.".calautos_formaciones fo on fo.id_formacion = f.id_formacion
            WHERE f.estado_elec_cargo = 0 and f.estado_elec_cargo !=2 
            ORDER BY f.dia_elec_cargo asc, f.hora_calificador asc 
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
                $update .= "UPDATE ".DB_BASE.".votapoliticos_elecciones_cargos SET estado_calificador = 4 WHERE id_elec_cargo = ".$value['id_elec_cargo'].";";
            }else{
                $proximoCalificador = $value;
                $rowFormacion = self::listFormacionById($db, $value['id_elec_cargo']);
                break;
            }
        }

        if( $update != ""){
            $stmt = $db->query($update);
        }

        return ['calificador' => $proximoCalificador, 'formacion' =>$rowFormacion ];
    }
   
}