<?php
namespace Clases\Admin\Votapoliticos;
use \PDO;
// print_r($settings);
class Elecciones {
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
        $sql = "SELECT $columna FROM ".DB_CON.".votapoliticos_elecciones";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_eleccion filtar encuentros por id_eleccion
     * @return void
     */
    public static function getDataByNombre($db, $id)
    {
        $sqlCliente = "SELECT
                   *
                FROM ".DB_CON.".votapoliticos_elecciones WHERE id_eleccion = '$id'";
        $stmt = $db->query($sqlCliente);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param [type] $id_eleccion filtar encuentros por id_eleccion
     * @return void
     */
    public static function getDataByCuil($db, $id_eleccion)
    {
        $sql = "SELECT
                   *
                FROM ".DB_CON.".votapoliticos_elecciones WHERE id_eleccion = '$id_eleccion'";
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
            INSERT INTO ".DB_CON.".votapoliticos_elecciones (
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
            update ".DB_CON.".votapoliticos_elecciones set
                $columnaActualizar
           where id_eleccion = $id_update
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
            DELETE FROM ".DB_CON.".votapoliticos_elecciones where id_eleccion = $id_update
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
     * @param [type] $id_eleccion
     * @param array $verComlumnas
     * @return array
     */
    public static function getDataById(pdo $db, $id_eleccion, $verComlumnas=[]): object
    {
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".votapoliticos_elecciones WHERE id_eleccion = $id_eleccion";
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
                e.`id_eleccion`,
                e.nombre_eleccion,
                e.`descripcion_eleccion`
                
            FROM
                votapoliticos_elecciones e
                WHERE e.inicio_eleccion <= '$fecha' 
                AND e.fin_eleccion >= '$fecha' 
                AND e.estado_eleccion = 0 limit 1
        ";        
        $stmt = $db->query($sql);
        $proximaEleccion = $stmt->fetch(PDO::FETCH_ASSOC);
        $cargos = [];
        $postulantes = [];
        if(!$proximaEleccion){
            $proximaEleccion = [];
        }else{
            $idEleccion = $proximaEleccion['id_eleccion'];
            $sql = "
            SELECT
            c.id_elec_cargo,
            c.cant_votos_elec_cargo,
            c.votos_blancos_elec_cargo,
            c.votos_nulos_elec_cargo,
            car.`id_cargo`,
            car.`nombre_cargo`,
            cant_votos_elec_cargo,
            color_elec_cargo,
            id_cliente
            FROM  `votapoliticos_elecciones_cargos` c 
            JOIN `votapoliticos_cargos` car ON car.`id_cargo` = c.`id_cargo`
            LEFT JOIN `votapoliticos_votaciones` v ON v.id_elec_cargo = c.`id_elec_cargo` AND id_cliente = $idCliente
            WHERE c.id_eleccion = $idEleccion AND id_cliente IS NULL order by c.orden_eleccion asc
            ";
            $stmt = $db->query($sql);
            $cargos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if( $cargos ){
                foreach ($cargos as $key => $value) {
                    $arrayPostulante = [];
                    $idElecCargo = $value['id_elec_cargo'];
                    $sql = "
                        SELECT
                        s.id_elec_cargo,
                        s.id_politico,
                        s.`nombre_politico`,
                        s.`apellido_politico`,
                        s.`imagen_politico`,
                        posicion_politico
                        FROM `votapoliticos_seleccion_politicos` s 
                        WHERE  s.id_elec_cargo = $idElecCargo order by s.orden_seleccion_politico asc
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

    //elecciones cargo

    public static function listarCargos(pdo $db, $id, $verComlumnas=[])
    {
        
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".votapoliticos_elecciones_cargos WHERE id_eleccion = $id ORDER BY orden_eleccion ASC";
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
                e.`id_eleccion`,
                e.nombre_eleccion,
                e.inicio_eleccion,
                e.`descripcion_eleccion`
            FROM
                votapoliticos_elecciones e
        ";        
        $stmt = $db->query($sql);
        $elecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $listElecciones = [];
        if(!$elecciones){
            $elecciones = [];
        }else{
            foreach ($elecciones as $key => $value) {
                $cargos = [];
                $idEleccion = $value['id_eleccion'];
                $fecha = $value['inicio_eleccion'];
                $nombre = $value['nombre_eleccion'];
                $sql = "
                    SELECT
                    c.id_elec_cargo,
                    car.`id_cargo`,
                    car.`nombre_cargo`,
                    cant_votos_elec_cargo,
                    color_elec_cargo
                    FROM  `votapoliticos_elecciones_cargos` c 
                    JOIN `votapoliticos_cargos` car ON car.`id_cargo` = c.`id_cargo`
                    WHERE c.id_eleccion = $idEleccion order by c.orden_eleccion asc
                ";
                $stmt = $db->query($sql);
                $cargos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if($cargos){
                    $listElecciones[$key]['nombre_eleccion'] = $nombre;
                    $listElecciones[$key]['fecha'] = $fecha;
                    $listElecciones[$key]['cargos'] = $cargos;
                }else{
                    $listElecciones[$key]['nombre_eleccion'] = $nombre;
                    $listElecciones[$key]['fecha'] = $fecha;
                    $listElecciones[$key]['cargos'] = [];
                }
            }
        }
     

        return  $listElecciones;
    }

    public function insertarCargos($db,$obj = [])
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
                    /**
     * Undocumented function
     *
     * @param [type] $db conexion de base de datos
     * @param array $verComlumnas si se quiere obtener una columna
     * @return void
     */
    public static function listarPoliticosCargo(pdo $db,$id_elec_cargo, $verComlumnas=[]): array
    {
        
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".votapoliticos_politicos  WHERE estado_politico = 0 ";
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
    public static function listarPostulantesCargo(pdo $db,$id_elec_cargo, $verComlumnas=[]): array
    {
        
        $columna = "*";
        if (count($verComlumnas)) {
            $columna = implode(",", $verComlumnas);
        }
        $sql = "SELECT $columna FROM ".DB_CON.".votapoliticos_seleccion_politicos p join ".DB_CON.".votapoliticos_elecciones_cargos c on c.id_elec_cargo = p.id_elec_cargo WHERE c.id_elec_cargo = $id_elec_cargo";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }


    //ELECCIONES POSTULANTES

    public function eliminarSeleccionPostulantes($db,$id_update)
    {        
        $sql = "
            DELETE FROM ".DB_CON.".votapoliticos_seleccion_politicos where id_elec_cargo = $id_update
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $id = $db->lastInsertId();
        return $id;
    }
    public function insertarSeleccionPostulantes($db,$obj = [])
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

}