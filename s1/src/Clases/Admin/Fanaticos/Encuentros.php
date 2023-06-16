<?php
namespace Clases\Admin\Fanaticos;
use \PDO;
// print_r($settings);
class Encuentros {
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
        $sql = "SELECT $columna FROM ".DB_CON.".fanatico_encuentros";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_encuentro filtar encuentros por id_encuentro
     * @return void
     */
    public static function getDataByNombre($db, $id)
    {
        $sqlCliente = "SELECT
                   *
                FROM ".DB_CON.".fanatico_encuentros WHERE id_encuentro = '$id'";
        $stmt = $db->query($sqlCliente);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_encuentro filtar encuentros por id_encuentro
     * @return void
     */
    public static function getDataByCuil($db, $id_encuentro)
    {
        $sql = "SELECT
                   *
                FROM ".DB_CON.".fanatico_encuentros WHERE id_encuentro = '$id_encuentro'";
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
            INSERT INTO ".DB_CON.".fanatico_encuentros (
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
            update ".DB_CON.".fanatico_encuentros set
                $columnaActualizar
           where id_encuentro = $id_update
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
            DELETE FROM ".DB_CON.".fanatico_encuentros where id_encuentro = $id_update
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
     * @param [type] $id_encuentro
     * @param array $verComlumnas
     * @return array
     */
    public static function getDataById(pdo $db, $id_encuentro, $verComlumnas=[]): object
    {
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".fanatico_encuentros WHERE id_encuentro = $id_encuentro";
        $stmt = $db->query($sql);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param pdo $db
     * @param [type] $id_encuentro
     * @param array $verComlumnas
     * @return array
     */
    public static function listFormacionById(pdo $db, $id_encuentro)
    {
        $sql = "SELECT clase id, orden_seleccion_futbolista orden, nombre_futbolista nombre, id_futbolista,imagen_futbolista FROM ".DB_CON.". fanatico_seleccion_futbolista  WHERE id_encuentro = $id_encuentro";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function insertarSeleccionFutbolista($db,$obj = [])
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
            INSERT INTO ".DB_CON.".fanatico_seleccion_futbolista (
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
            DELETE FROM ".DB_CON.".fanatico_seleccion_futbolista where id_encuentro = $id_update
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $id = $db->lastInsertId();
        return $id;
    }

    public static function validarEstadoEncuentro($db,$id_update)
    {     
        $sql = "
            SELECT * FROM ".DB_CON.".fanatico_encuentros where id_encuentro = '$id_update'
        ";
        $stmt = $db->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $status = false;
        if(  $data['estado_actual_encuentro'] == 1 ){
            $fechaActual = date('Y-m-d H:i:s');
            $tiempo = $data['tiempo_cal_encuentro'];
            $inicio = $data['inicio_cal_encuentro'];
    
            $tiempoCulminacion = date("Y-m-d H:i:s",strtotime($inicio."+ $tiempo hours"));
            $firstDate  = new \DateTime($fechaActual);
            $secondDate = new \DateTime($tiempoCulminacion);
            $intvl = $secondDate->diff($firstDate);
            $hora = $intvl->h;
            $minuto = $intvl->i;
            $segunto = $intvl->s;
            if($intvl->invert == 0){
                $status = true;
            }
        }
        return $status;
    }

    public static function cambiarEstado($db,$id_update)
    {     
        $update = "UPDATE ".DB_BASE.".fanatico_encuentros SET estado_encuentro = 4,estado_actual_encuentro=3 WHERE id_encuentro = $id_update;";
        $stmt = $db->query($update);
    }

    
    /**
     * listarProximoEncuentro
     * Se va a listar el encuentro proximo sigun fecha de servidor
     * @param  mixed $db
     * @return void
     */
    public static function listarProximoEncuentroFormacion($db)
    {
        $sqlCliente = "
            SELECT    
               id_encuentro,res_favor_encuentro,res_contra_encuentro,estado_actual_encuentro,nombre_torneo,fecha_encuentro,dia_encuentro
               ,hora_encuentro,tipo_formacion,clase_formacion,nombre_club,logo_club, cancha_encuentro,nombre_estadio,nombre_arbitro,f.insert_local,tiempo_cal_encuentro,inicio_cal_encuentro
            FROM ".DB_BASE.".fanatico_encuentros f 
            join ".DB_BASE.".fanatico_clubes c on c.id_club = f.id_club
            join ".DB_BASE.".fanatico_arbitros a on a.id_arbitro = f.id_arbitro
            join ".DB_BASE.".fanatico_torneos t on t.id_torneo = f.id_torneo
            join ".DB_BASE.".fanatico_estadios e on e.id_estadio = f.id_estadio
            left join ".DB_BASE.".fanatico_formaciones fo on fo.id_formacion = f.id_formacion
            WHERE f.estado_encuentro = 0 and f.estado_actual_encuentro !=2 
            ORDER BY f.dia_encuentro asc, f.hora_encuentro asc 
        ";

        // and f.dia_encuentro < '$fecha'
        
        $stmt = $db->query($sqlCliente);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $proximoEncuentro = [];
        $rowFormacion = [];
        $update = "";
        // echo "hola";
        foreach ($data as $key => $value) {
            $id_encuentro = $value['id_encuentro'];
            $estadoEncuentro = self::validarEstadoEncuentro($db,$id_encuentro);

            if( $estadoEncuentro ){
              $update .= "UPDATE ".DB_BASE.".fanatico_encuentros SET estado_encuentro = 4,estado_actual_encuentro=3 WHERE id_encuentro = ".$value['id_encuentro'].";";
            }else{
                $proximoEncuentro = $value;
                $rowFormacion = self::listFormacionById($db, $value['id_encuentro']);
                break;
            }
        }

        if( $update != ""){
            $stmt = $db->query($update);
        }

        return ['encuentro' => $proximoEncuentro, 'formacion' =>$rowFormacion,"fechaActual" => date("Y-m-d H:i:s") ];
    }
   
}