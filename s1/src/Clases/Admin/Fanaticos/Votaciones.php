<?php
namespace Clases\Admin\Fanaticos;
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
        $sql = "SELECT $columna FROM ".DB_BASE.".fanatico_votaciones";
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
                FROM ".DB_BASE.".fanatico_votaciones WHERE id_votacion = '$id'";
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
                FROM ".DB_BASE.".fanatico_votaciones WHERE id_votacion = '$nombre_club'";
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
            INSERT INTO ".DB_BASE.".fanatico_votaciones (
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
            update ".DB_BASE.".fanatico_votaciones set
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
            DELETE FROM ".DB_BASE.".fanatico_votaciones where id_votacion = $id_update
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $id = $db->lastInsertId();
        return $id;
    }
    public function eliminarTodo($db)
    {        
        $sql = "
            DELETE FROM ".DB_CON.".fanatico_votaciones
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return [];
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
        $sql = "SELECT $columna FROM ".DB_BASE.".fanatico_votaciones WHERE id_cliente = $id_cliente";
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
    public static function validateDataById(pdo $db, $id_cliente,$id_encuento,$id_futbolista): array
    {
        $columna = "*";
        $sql = "SELECT $columna FROM " . DB_BASE . ".fanatico_votaciones WHERE id_cliente = $id_cliente and id_encuentro=$id_encuento and id_futbolista= $id_futbolista";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
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
                FROM ".DB_BASE.".fanatico_votaciones v
                JOIN ".DB_BASE.".fanatico_futbolistas f ON f.id_futbolista= v.id_futbolista 
                WHERE v.id_cliente = $id_cliente";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
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
    public static function getVotosById(pdo $db, $id_cliente, $idEncuentro, $verComlumnas=[])
    {
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "
               SELECT $columna
                FROM ".DB_BASE.".fanatico_votaciones v
                JOIN ".DB_BASE.".fanatico_futbolistas f ON f.id_futbolista= v.id_futbolista 
                WHERE v.id_cliente = $id_cliente and v.id_encuentro = $idEncuentro ";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public static function getListCalification(pdo $db, $idEncuentro="", $idCliente = "" )
    {
        $where = "";
        if ($idEncuentro !="" ) {
            $where .= " and v.id_encuentro = '$idEncuentro' ";
        }

        if ($idCliente !="" ) {
            $where .= " and id_cliente = '$idCliente' ";
        }

        $sql = "
            SELECT 
            f.id_futbolista,
            f.imagen_futbolista,
            f.nombre_futbolista,
            f.apellido_futbolista,
            COALESCE( SUM( v.calificacion_votacion ),0) votos ,
            COALESCE( COUNT( v.id_futbolista ),0 ) cantidad ,
            COALESCE( ( SUM(v.calificacion_votacion) /  COUNT(v.id_futbolista)),0 )  promedio,
            COALESCE( ( 100 * ( SUM(v.calificacion_votacion)) / COUNT(v.id_futbolista) ) / 10 ,0 )  porcentaje
                FROM ".DB_BASE.".fanatico_votaciones v 
                right JOIN ".DB_BASE.".fanatico_futbolistas f ON f.id_futbolista= v.id_futbolista  $where
            WHERE f.estado_futbolista = 0 
            GROUP BY f.id_futbolista 
            ORDER BY promedio DESC
           
        ";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }
    public static function getInformesConFiltro(pdo $db, $id = "",$torneo="",$encuentro="" )
    {
        $where = "";
        $where2 = "";
        if ($id =="mujeres" ) {
            $where2 .= " and c.sexo_cliente ='f' ";
        }
        if ($id =="hombres" ) {
            $where2 .= " and c.sexo_cliente ='m' ";
        }
        if ($id =="socios" ) {
            $where2 .= " and c.socio_cliente ='1' ";
        }
        if ($id =="nosocios" ) {
            $where2 .= " and c.socio_cliente ='2' ";
        }

        if($torneo != ""){
            $where .= " and e.id_torneo ='$torneo' ";
            $where2 .= " and e.id_torneo ='$torneo' ";
        }

        if($encuentro != ""){
            $where .= " and e.id_encuentro ='$encuentro' ";
            $where2 .= " and e.id_encuentro ='$encuentro' ";
        }

        $sql = "
            SELECT 
            f.id_futbolista,
            f.imagen_futbolista,
            f.nombre_futbolista,
            f.apellido_futbolista,
            COALESCE( SUM( v.calificacion_votacion ),0) votos ,
            COALESCE( COUNT( v.id_futbolista ),0 ) cantidad ,
            COALESCE( ( SUM(v.calificacion_votacion) /  COUNT(v.id_futbolista)),0 )  promedio,
            COALESCE( ( 100 * ( SUM(v.calificacion_votacion)) / COUNT(v.id_futbolista) ) / 10 ,0 )  porcentaje
                FROM ".DB_BASE.".fanatico_votaciones v
                JOIN ".DB_BASE.".fanatico_encuentros e on e.id_encuentro = v.id_encuentro
                JOIN ".DB_BASE.".fanatico_clientes c ON c.id_cliente = v.id_cliente $where2
                right JOIN ".DB_BASE.".fanatico_futbolistas f ON f.id_futbolista= v.id_futbolista
            WHERE 1 = 1
            GROUP BY f.id_futbolista 
            ORDER BY promedio DESC
        
        ";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        $sql ="
            SELECT 
                COUNT(DISTINCT v.id_cliente ) total_votos,
                COUNT(DISTINCT IF( c.socio_cliente='1' ,v.id_cliente ,NULL) ) socio,
                COUNT(DISTINCT IF( c.socio_cliente='2' ,v.id_cliente ,NULL) ) nosocio,
                COUNT(DISTINCT IF( c.sexo_cliente='f' ,v.id_cliente ,NULL) ) femenino,
                COUNT(DISTINCT IF( c.sexo_cliente='m' ,v.id_cliente ,NULL) ) masculino
            FROM ".DB_BASE.".fanatico_votaciones v
            JOIN ".DB_BASE.".fanatico_encuentros e on e.id_encuentro = v.id_encuentro
            JOIN ".DB_BASE.".fanatico_clientes c ON c.id_cliente = v.id_cliente $where
            WHERE 1 = 1
        ";
        $stmt = $db->query($sql);
        $data2 = $stmt->fetchAll(PDO::FETCH_OBJ);
        return ["detalle1"=>$data,"detalle2"=>$data2,"detalle3" => ""];
    }

    public static function getListCalificationEncuetro(pdo $db, $idEncuentro="", $idCliente = "" )
    {
        $fecha = date('Y-m-d');

        $sql = "
            SELECT e.id_encuentro,e.fecha_encuentro,t.nombre_torneo ,e.dia_encuentro,e.hora_encuentro
            FROM ".DB_BASE.".fanatico_encuentros e 
                join fanatico_torneos t on t.id_torneo = e.id_torneo  
            
            order by e.dia_encuentro desc limit 6
        ";

        // where e.dia_encuentro < '$fecha'
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        $arrayEncuentros = [];
        foreach ($data as $key => $value) {
            $id = $value->id_encuentro;
            $sql = "
                SELECT 
                f.id_futbolista,
                f.imagen_futbolista,
                f.nombre_futbolista,
                f.apellido_futbolista,
                COALESCE( SUM( v.calificacion_votacion ),0) votos ,
                COALESCE( SUM( v.id_futbolista ),0 ) cantidad ,
                COALESCE( ( SUM(v.calificacion_votacion) /  COUNT(v.id_futbolista)),0 )  promedio,
                COALESCE( ( 100 * ( SUM(v.calificacion_votacion)) / COUNT(v.id_futbolista) ) / 10 ,0 )  porcentaje
                    FROM ".DB_BASE.".fanatico_votaciones v 
                    right JOIN ".DB_BASE.".fanatico_futbolistas f ON f.id_futbolista= v.id_futbolista
                WHERE f.estado_futbolista = 0 and v.id_encuentro = $id
                GROUP BY f.id_futbolista
                ORDER BY promedio DESC
            
            ";
            $stmt2 = $db->query($sql);
            $arrayCalificacion = $stmt2->fetchAll(PDO::FETCH_OBJ);
            $arrayEncuentros[] = [
                'id_encuentro' => $value->id_encuentro,
                'dia_encuentro' => date("d/m/Y",strtotime($value->dia_encuentro)),
                'hora_encuentro' => $value->hora_encuentro,
                'fecha_encuentro' => $value->fecha_encuentro,
                'nombre_torneo' => $value->nombre_torneo,
                'calificacion' => $arrayCalificacion,
            ];
        }
        return $arrayEncuentros;
    }

    public static function getListEncuentroGroup(pdo $db, $idEncuentro="", $idCliente = "" )
    {
        $fecha = date('Y-m-d');
        $sql = "
            SELECT *
            FROM ".DB_BASE.".fanatico_torneos
            order by id_torneo
        ";

        // where e.dia_encuentro < '$fecha'
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        $arrayTorneos = [];
        foreach ($data as $key => $value) {
            $id = $value->id_torneo;
            $sql = "
                SELECT * FROM ".DB_BASE.".fanatico_encuentros   where id_torneo = $id           
            ";
            // $sql = "
            //     SELECT * FROM ".DB_BASE.".fanatico_encuentros   where (estado_actual_encuentro =1 or estado_actual_encuentro =2) and  id_torneo = $id           
            // ";

            $stmt2 = $db->query($sql);
            $arrayEncuentros = $stmt2->fetchAll(PDO::FETCH_OBJ);
            $arrayTorneos[] = [
                'id_torneo' => $value->id_torneo,
                'nombre_torneo' => $value->nombre_torneo,
                'encuentros' => $arrayEncuentros,
            ];
        }
        return $arrayTorneos;
    }
    
}
