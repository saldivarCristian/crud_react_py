<?php
namespace Clases\Admin\Desafios;
use \PDO;
// print_r($settings);
class Grupos {
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
        $sql = "SELECT $columna FROM ".DB_CON.".prode_grupos";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

        /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param array $verComlumnas si se quiere obtener una columna
     * @return void
     */
    public static function listarMiGrupo(pdo $db, $token): array
    {
        
        $id_cliente = $token['id_cliente']; 
        $sql = "SELECT * FROM ".DB_CON.".prode_grupos g  JOIN ".DB_CON.".prode_grupo_clientes gc ON g.id_grupo = gc.id_grupo WHERE gc.id_cliente = $id_cliente and estado_grupo_cliente = 0";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public static function getIntegrantes(pdo $db, $id): array
    {
        
        
        $sql = "SELECT * FROM ".DB_CON.".prode_grupo_clientes g  JOIN ".DB_CON.".prode_clientes gc ON g.id_cliente = gc.id_cliente WHERE g.id_grupo = $id";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    


    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_grupo filtar encuentros por id_grupo
     * @return void
     */
    public static function getDataByNombre($db, $id)
    {
        $sqlCliente = "SELECT
                   *
                FROM ".DB_CON.".prode_grupos WHERE id_grupo = '$id'";
        $stmt = $db->query($sqlCliente);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_grupo filtar encuentros por id_grupo
     * @return void
     */
    public static function getDataByCuil($db, $id_grupo)
    {
        $sql = "SELECT
                   *
                FROM ".DB_CON.".prode_grupos WHERE id_grupo = '$id_grupo'";
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
            INSERT INTO ".DB_CON.".prode_grupos (
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
            update ".DB_CON.".prode_grupos set
                $columnaActualizar
           where id_grupo = $id_update
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

    public function actualizarEstado($db,$obj = [],$id_update)
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
            update ".DB_CON.".prode_grupo_clientes set
                $columnaActualizar
           where id_grupo_cliente = $id_update
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
            DELETE FROM ".DB_CON.".prode_grupos where id_grupo = $id_update
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $id = $db->lastInsertId();
        return $id;
    }

    public function eliminarTodo($db)
    {        
        $sql = "
            DELETE FROM ".DB_CON.".votapoliticos_votaciones
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return [];
    }

    /**
     * Undocumented function
     *
     * @param pdo $db
     * @param [type] $id_grupo
     * @param array $verComlumnas
     * @return array
     */
    public static function getDataById(pdo $db, $id_grupo, $verComlumnas=[])
    {
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".prode_grupos WHERE id_grupo = $id_grupo";
        $stmt = $db->query($sql);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }


     
    /**
     * listarProximoEncuentro
     * Se va a listar el encuentro proximo sigun fecha de servidor
     * @param  mixed $db
     * @return void
     */
    public static function listarPostulaciones($db,$idCliente)
    {
        // $fechaActual = date("d-m-Y H:i:s");
        $fecha = date("Y-m-d H:i:s");
        $sql = "
            SELECT
                e.`id_grupo`,
                e.nombre_encuentro,
                e.`descripcion_encuentro`
                
            FROM
                prode_grupos e
                WHERE e.inicio_encuentro <= '$fecha' 
                AND e.fin_encuentro >= '$fecha' 
                AND e.estado_encuentro = 0 limit 1
        ";        
        $stmt = $db->query($sql);
        $proximaEleccion = $stmt->fetch(PDO::FETCH_ASSOC);
        $cargos = [];
        $postulantes = [];
        if(!$proximaEleccion){
            $proximaEleccion = [];
        }else{
            $idEleccion = $proximaEleccion['id_grupo'];
            $sql = "
            SELECT
            c.id_cliente,
            c.cant_votos_cliente,
            c.votos_blancos_cliente,
            c.votos_nulos_cliente,
            car.`id_cargo`,
            car.`nombre_cargo`,
            cant_votos_cliente,
            color_cliente,
            id_cliente
            FROM  `prode_grupo_clientes` c 
            JOIN `votapoliticos_cargos` car ON car.`id_cargo` = c.`id_cargo`
            LEFT JOIN `votapoliticos_votaciones` v ON v.id_cliente = c.`id_cliente` AND id_cliente = $idCliente
            WHERE c.id_grupo = $idEleccion AND id_cliente IS NULL order by c.orden_encuentro asc
            ";
            $stmt = $db->query($sql);
            $cargos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if( $cargos ){
                foreach ($cargos as $key => $value) {
                    $arrayPostulante = [];
                    $idElecCargo = $value['id_cliente'];
                    $sql = "
                        SELECT
                        s.id_cliente,
                        s.id_politico,
                        s.`nombre_politico`,
                        s.`apellido_politico`,
                        s.`imagen_politico`,
                        posicion_politico
                        FROM `prode_grupo_clientes` s 
                        WHERE  s.id_cliente = $idElecCargo order by s.orden_seleccion_politico asc
                    ";
                    $stmt = $db->query($sql);
                    $arrayPostulante = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if($arrayPostulante){
                        $cargos[$key]['postulantes'] = $arrayPostulante;
                    }
                }
            }else{
                $proximaEleccion = [];
            }
        }
     

        return [
            'eleccion' => $proximaEleccion,
            'cargos' =>$cargos
        ];
    }

    //Grupos cargo

    public static function listarCargos(pdo $db, $id, $verComlumnas=[])
    {
        
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".prode_grupo_clientes WHERE id_grupo = $id ORDER BY orden_encuentro ASC";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    //elecciones cargo

    public static function listarEleccionesCargos(pdo $db)
    {
        
        // $fechaActual = date("d-m-Y H:i:s");
        $fecha = date("Y-m-d H:i:s");
        $sql = "
            SELECT
                e.`id_grupo`,
                e.nombre_encuentro,
                e.inicio_encuentro,
                e.`descripcion_encuentro`
            FROM
                prode_grupos e
        ";        
        $stmt = $db->query($sql);
        $elecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $listElecciones = [];
        if(!$elecciones){
            $elecciones = [];
        }else{
            foreach ($elecciones as $key => $value) {
                $cargos = [];
                $idEleccion = $value['id_grupo'];
                $fecha = $value['inicio_encuentro'];
                $nombre = $value['nombre_encuentro'];
                $sql = "
                    SELECT
                    c.id_cliente,
                    car.`id_cargo`,
                    car.`nombre_cargo`,
                    cant_votos_cliente,
                    color_cliente
                    FROM  `prode_grupo_clientes` c 
                    JOIN `votapoliticos_cargos` car ON car.`id_cargo` = c.`id_cargo`
                    WHERE c.id_grupo = $idEleccion order by c.orden_encuentro asc
                ";
                $stmt = $db->query($sql);
                $cargos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if($cargos){
                    $listElecciones[$key]['nombre_encuentro'] = $nombre;
                    $listElecciones[$key]['fecha'] = $fecha;
                    $listElecciones[$key]['cargos'] = $cargos;
                }else{
                    $listElecciones[$key]['nombre_encuentro'] = $nombre;
                    $listElecciones[$key]['fecha'] = $fecha;
                    $listElecciones[$key]['cargos'] = [];
                }
            }
        }
     

        return  $listElecciones;
    }

    public function insertarGrupos($db,$obj = [])
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
            INSERT INTO ".DB_CON.".prode_grupo_clientes (
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

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param array $verComlumnas si se quiere obtener una columna
     * @return void
     */
    public static function listarParticipantes(pdo $db,$id_grupo, $verComlumnas=[]): array
    {
        
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".prode_grupo_clientes p join ".DB_CON.".prode_clientes c on c.id_cliente = p.id_cliente WHERE p.id_grupo = $id_grupo";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }


    //ELECCIONES POSTULANTES

    public function eliminarSeleccionParticipantes($db,$id_update)
    {        
        $sql = "
            DELETE FROM ".DB_CON.".prode_grupo_clientes where id_grupo = $id_update
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $id = $db->lastInsertId();
        return $id;
    }
    public function insertarSeleccionParticipantes($db,$obj = [])
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
            INSERT INTO ".DB_CON.".prode_grupo_clientes (
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
    public function actualizarPostulantes($db,$obj = [],$id_update)
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
            update ".DB_CON.".prode_grupos_clientes set
                $columnaActualizar
           where id_cliente = $id_update
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

}