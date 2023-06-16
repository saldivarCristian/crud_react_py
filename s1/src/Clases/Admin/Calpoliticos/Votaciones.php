<?php
namespace Clases\Admin\Calpoliticos;
use \PDO;
// print_r($settings);
class Votaciones {
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
        $sql = "SELECT $columna FROM ".DB_BASE.".calautos_votaciones";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_votacion filtar votaciones por id_votacion
     * @return void
     */
    public static function getDataByNombre($db, $id)
    {
        $sqlCliente = "SELECT
                   *
                FROM ".DB_BASE.".calautos_votaciones WHERE id_votacion = '$id'";
        $stmt = $db->query($sqlCliente);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $nombre_club filtar votaciones por nombre_club
     * @return void
     */
    public static function getDataByCuil($db, $nombre_club)
    {
        $sql = "SELECT
                   *
                FROM ".DB_BASE.".calautos_votaciones WHERE id_votacion = '$nombre_club'";
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
            INSERT INTO ".DB_BASE.".calautos_votaciones (
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
            update ".DB_BASE.".calautos_votaciones set
                $columnaActualizar
           where id_votacion = $id_update
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
            DELETE FROM ".DB_BASE.".calautos_votaciones where id_votacion = $id_update
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
     * @param [type] $id_cliente
     * @param array $verComlumnas
     * @return array
     */
    public static function getDataById(pdo $db, $id_cliente, $verComlumnas=[]): object
    {
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_BASE.".calautos_votaciones WHERE id_cliente = $id_cliente";
        $stmt = $db->query($sql);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }
   
        /**
     * Undocumented function
     *
     * @param pdo $db
     * @param [type] $id_cliente
     * @param array $verComlumnas
     * @return array
     */
    public static function getAllDataById(pdo $db, $id_cliente, $verComlumnas=[])
    {
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "
               SELECT $columna
                FROM ".DB_BASE.".calautos_votaciones v
                JOIN ".DB_BASE.".calautos_autos f ON f.id_auto= v.id_auto 
                WHERE v.id_cliente = $id_cliente";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public static function getListCalification(pdo $db, $idCalificador="", $idCliente = "" )
    {
        $where = "";
        if ($idCalificador !="" ) {
            $where .= " and v.id_calificador = $idCalificador ";
        }

        if ($idCliente !="" ) {
            $where .= " and id_cliente = $idCliente ";
        }

        $sql = "
            SELECT 
            f.id_auto,
            f.imagen_auto,
            f.nombre_auto,
            COALESCE( SUM( v.calificacion_votacion ),0) votos ,
            COALESCE( COUNT( v.id_auto ),0 ) cantidad ,
            COALESCE( ( SUM(v.calificacion_votacion) /  COUNT(v.id_auto)),0 )  promedio,
            COALESCE( ( 100 * ( SUM(v.calificacion_votacion)) / COUNT(v.id_auto) ) / 10 ,0 )  porcentaje
                FROM ".DB_BASE.".calautos_votaciones v 
                right JOIN ".DB_BASE.".calautos_autos f ON f.id_auto= v.id_auto  $where
            WHERE f.estado_auto = 0 
            GROUP BY f.id_auto 
            ORDER BY promedio DESC
           
        ";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public static function getListCalificationEncuetro(pdo $db, $idCalificador="", $idCliente = "" )
    {
        $fecha = date('Y-m-d');

        $sql = "
            SELECT e.id_calificador,e.fecha_calificador,t.nombre_torneo ,e.dia_calificador,e.hora_calificador
            FROM ".DB_BASE.".calautos_calificador e 
                join calautos_torneos t on t.id_torneo = e.id_torneo  
            
            order by e.dia_calificador desc limit 6
        ";

        // where e.dia_calificador < '$fecha'
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        $arrayEncuentros = [];
        foreach ($data as $key => $value) {
            $id = $value->id_calificador;
            $sql = "
                SELECT 
                f.id_auto,
                f.imagen_auto,
                f.nombre_auto,
                f.apellido_auto,
                COALESCE( SUM( v.calificacion_votacion ),0) votos ,
                COALESCE( SUM( v.id_auto ),0 ) cantidad ,
                COALESCE( ( SUM(v.calificacion_votacion) /  COUNT(v.id_auto)),0 )  promedio,
                COALESCE( ( 100 * ( SUM(v.calificacion_votacion)) / COUNT(v.id_auto) ) / 10 ,0 )  porcentaje
                    FROM ".DB_BASE.".calautos_votaciones v 
                    right JOIN ".DB_BASE.".calautos_autos f ON f.id_auto= v.id_auto
                WHERE f.estado_auto = 0 and v.id_calificador = $id
                GROUP BY f.id_auto
                ORDER BY promedio DESC
            
            ";
            $stmt2 = $db->query($sql);
            $arrayCalificacion = $stmt2->fetchAll(PDO::FETCH_OBJ);
            $arrayEncuentros[] = [
                'id_calificador' => $value->id_calificador,
                'dia_calificador' => date("d/m/Y",strtotime($value->dia_calificador)),
                'hora_calificador' => $value->hora_calificador,
                'fecha_calificador' => $value->fecha_calificador,
                'calificacion' => $arrayCalificacion,
            ];
        }
        return $arrayEncuentros;
    }
    
}
