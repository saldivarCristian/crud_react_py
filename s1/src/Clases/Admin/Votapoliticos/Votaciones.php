<?php
namespace Clases\Admin\Votapoliticos;
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
        $sql = "SELECT $columna FROM ".DB_BASE.".votapoliticos_votaciones";
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
                FROM ".DB_BASE.".votapoliticos_votaciones WHERE id_votacion = '$id'";
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
                FROM ".DB_BASE.".votapoliticos_votaciones WHERE id_votacion = '$nombre_club'";
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
            INSERT INTO ".DB_BASE.".votapoliticos_votaciones (
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
            update ".DB_BASE.".votapoliticos_votaciones set
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
            DELETE FROM ".DB_BASE.".votapoliticos_votaciones where id_votacion = $id_update
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
        $sql = "SELECT $columna FROM ".DB_BASE.".votapoliticos_votaciones WHERE id_cliente = $id_cliente";
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
                FROM ".DB_BASE.".votapoliticos_votaciones v
                JOIN ".DB_BASE.".votapoliticos_politicos f ON f.id_politico= v.id_politico 
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
            f.id_politico,
            f.imagen_politico,
            f.nombre_politico,
            COALESCE( SUM( v.calificacion_votacion ),0) votos ,
            COALESCE( COUNT( v.id_politico ),0 ) cantidad ,
            COALESCE( ( SUM(v.calificacion_votacion) /  COUNT(v.id_politico)),0 )  promedio,
            COALESCE( ( 100 * ( SUM(v.calificacion_votacion)) / COUNT(v.id_politico) ) / 10 ,0 )  porcentaje
                FROM ".DB_BASE.".votapoliticos_votaciones v 
                right JOIN ".DB_BASE.".votapoliticos_politicos f ON f.id_politico= v.id_politico  $where
            WHERE f.estado_auto = 0 
            GROUP BY f.id_politico 
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
            FROM ".DB_BASE.".votapoliticos_calificador e 
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
                f.id_politico,
                f.imagen_politico,
                f.nombre_politico,
                f.apellido_auto,
                COALESCE( SUM( v.calificacion_votacion ),0) votos ,
                COALESCE( SUM( v.id_politico ),0 ) cantidad ,
                COALESCE( ( SUM(v.calificacion_votacion) /  COUNT(v.id_politico)),0 )  promedio,
                COALESCE( ( 100 * ( SUM(v.calificacion_votacion)) / COUNT(v.id_politico) ) / 10 ,0 )  porcentaje
                    FROM ".DB_BASE.".votapoliticos_votaciones v 
                    right JOIN ".DB_BASE.".votapoliticos_politicos f ON f.id_politico= v.id_politico
                WHERE f.estado_auto = 0 and v.id_calificador = $id
                GROUP BY f.id_politico
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

    public static function getReultados($db, $id)
    {   
        $votoBlanco=0;
        $votoNulos=0;
        $sql = "
            SELECT 
                COUNT(v.id_politico) cantidad,s.nombre_politico,s.apellido_politico,s.imagen_politico ,s.posicion_politico
            FROM `votapoliticos_seleccion_politicos` s 
                LEFT JOIN `votapoliticos_votaciones` v ON v.id_politico = s.id_politico
            WHERE s.id_elec_cargo = $id  GROUP BY s.id_politico  ORDER BY cantidad DESC
        ";
        $stmt = $db->query($sql); 
        $detalle = $stmt->fetchAll(PDO::FETCH_OBJ);
        $sql = "
            SELECT 
            nombre_cargo,COUNT(distinct v.id_cliente) total_votantes,COUNT(id_politico) total_votos,color_elec_cargo,votos_nulos_elec_cargo,votos_blancos_elec_cargo,nombre_eleccion
            FROM  `votapoliticos_elecciones_cargos` ca 
            JOIN  `votapoliticos_votaciones` v  ON ca.id_elec_cargo=v.`id_elec_cargo`
            JOIN `votapoliticos_cargos` c ON c.id_cargo = ca.id_cargo
            JOIN `votapoliticos_elecciones` e ON e.id_eleccion = ca.id_eleccion
            WHERE v.id_elec_cargo = $id
        ";
        $stmt = $db->query($sql);
        $cabecera = $stmt->fetch(PDO::FETCH_OBJ);

        if($cabecera->votos_blancos_elec_cargo == 1){
            $sql = "
                SELECT 
                COUNT(id_politico) total_votos
                FROM  `votapoliticos_votaciones`
                WHERE id_elec_cargo = $id and id_politico = 0
            ";
            $stmt = $db->query($sql);
            $votoBlanco = $stmt->fetch(PDO::FETCH_OBJ);
            $votoBlanco = $votoBlanco->total_votos ?? 0;
        }

        if($cabecera->votos_nulos_elec_cargo == 1){
            $sql = "
                SELECT 
                COUNT(id_politico) total_votos
                FROM  `votapoliticos_votaciones`
                WHERE id_elec_cargo = $id and id_politico = -1
            ";
            $stmt = $db->query($sql);
            $votoNulos = $stmt->fetch(PDO::FETCH_OBJ);
            $votoNulos = $votoNulos->total_votos ?? 0;

        }

        return [ "cabecera" => $cabecera, "detalle" => $detalle, "extra"=>["blanco" => $votoBlanco, "nulo"=>$votoNulos] ];
        
    }
    // <img src="'.HOST.'/img/votapoliticos/logo-menu.png" alt="Emprendimientos" class="logo">
    public static function getFormatoPdf($db, $row)
    {
         // <img src="'.HOST.'/img/votapoliticos/imgpol.jpg" alt="logo" style="width: 5px;" />
        //  <img src="'.HOST.'/img/votapoliticos/logo-menu.png" alt="Emprendimientos" style="    width: 150px;margin: 10px;">

    //     <div  style="background: linear-gradient(90deg, #0f1041 0%, #15164e 50%, #557ed4 100%)">
    //     <img src="https://www.adslzone.net/app/uploads-adslzone.net/2019/04/borrar-fondo-imagen.jpg" alt="Emprendimientos" style="width: 150px;margin: 10px;">
    // </div>
    // <img src="https://www.adslzone.net/app/uploads-adslzone.net/2019/04/borrar-fondo-imagen.jpg" alt="logo" style="width: 150px;margin: 10px;" />

    // <img src="'.HOST.'/img/votapoliticos/sm/politicos/'.$imagen.'" alt="logo" style="width: 5px;" />
        $html = "";
        if($row){
            $detalle="";
            $cargo = $row['cabecera']->nombre_cargo;
            $eleccion = $row['cabecera']->nombre_eleccion;
            $total = $row['cabecera']->total_votos;
            $totalVotante = $row['cabecera']->total_votantes;
            $nulo = $row['extra']->nulo ?? 0;
            $blanco = $row['extra']->blanco ?? 0 ;
            foreach ($row['detalle'] as $key => $value) {
                $nombre = $value->nombre_politico.' '.$value->apellido_politico;
                $votos = $value->cantidad;
                $imagen = $value->imagen_politico;
                // if(){

                // }
                $porcentaje = number_format( $total > 0 ? (($votos/$total)*100 ) : 0 , 2, ',', '.');
                
                $detalle .='
                    <tr style="border-bottom: 10px solid;">
                        <td style="width: 20%;border-bottom: 1px solid black;margin:0px;padding:0px;">
                            <img src="'.$_SERVER['DOCUMENT_ROOT'].'/emprendimiento/s1/public/img/votapoliticos/sm/politicos/'.$imagen.'" alt="logo" style="width: 100px;" />
                        </td>
                        <td style="width: 80%;border-bottom: 1px solid black;margin:0px;padding:0px;"> 
                            <table style="width: 70%;">
                                <tr tyle="">
                                    <td style="font-size: 25px;margin-bottom:20px;width: 250px;"><br/>'.$nombre.'<br/> &nbsp;</td>
                                    <td></td>
                                </tr>
                                <tr  style="">
                                    <td style="width: 200px;font-size: 18px;">Votos Optenidos</td>
                                    <td style="width: 200px;font-size: 18px;">Porcentaje</td>
                                </tr>
                                <tr>
                                    <td style="width: 200px;font-size: 18px;">'.$votos.' <br>&nbsp;</td>
                                    <td style="width: 200px;font-size: 18px;">'.$porcentaje.'% <br>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>   
                '; 
            }

            $html = '
                <page backtop="10mm" backbottom="10mm" backleft="5mm" backright="5mm"> 
                    <page_header> 
                        <table cellspacing="0" class="header1" >  <!-- total width: 605 -->
                            <tr>
                                <td width="486">
                                    <b>Resultados de la elección \''.$eleccion.'\'</b><br>
                                </td>
                                <td width="150" align="right">
                                    
                                </td>
                            </tr>
                        </table>
                    </page_header> 
                    <page_footer> 
                        <table cellspacing="0" class="header2">
                            <tr>
                                <td width="640" align="center" style="font-size: 11px">
                                DOCUMENTO GENERADO DESDE LA BASE DE DATOS '.strtoupper(COMPANY).'
                                    
                                    <span style="color:#777; font-size: 11pt">[[page_cu]] / [[page_nb]]</span>
                                </td>
                            </tr>
                            <tr>
                                <td height="10">
                                    &nbsp;
                                </td>
                            </tr>
                        </table>
                    </page_footer> 
    
                        <h1 style="text-align: center;margin-top: 0px;">
                            '.$cargo.'
                        </h1>

                        <table style="text-align:center;width:100%;margin-top:10px;">
                            <tr style="text-align:center;">
                                <td style="width: 25%;font-size: 18px;">Total Votas: '.$total.'</td>
                                <td style="width: 25%;font-size: 18px;">Total Votantes: '.$totalVotante.'</td>
                                <td style="width: 25%;font-size: 18px;">Votos blancos: '.$blanco.'</td>
                                <td style="width: 25%;font-size: 18px;">Votos nulos: '.$nulo.'</td>
                            </tr>
                        </table>

                        <table style="width:100%;margin-top:10px;">
                        '.$detalle.'
                        </table>

                        <table style="width:100%;margin-top:40px;text-align;center;">
                            <tr style="text-align:center">
                                <td  style="width: 33%;"></td>
                                <td  style="width: 33%;"></td>
                                <td  style="width: 33%;padding-botton:20px">'.date('d/m/Y H:i:s').'</td>
                            </tr>
                            <tr style="text-align:center">
                                <td  style="width: 33%;">Firma</td>
                                <td  style="width: 33%;">Aclaración</td>
                                <td  style="width: 33%;">Fecha</td>
                            </tr>
                        </table>

                     
                </page> 
            ';
        }

        return $html;
    }
    
}
