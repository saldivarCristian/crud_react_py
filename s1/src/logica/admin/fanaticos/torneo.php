<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Fanaticos\Torneos;

$app->group('/admin/tournaments', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){

        // $roles = $conexion->consulta("SELECT * FROM roles r JOIN permisos p on p.id_rol=r.id_rol WHERE r.id_rol=".$_SESSION['id_rol']." AND p.cod_mod='item_".$_SESSION['item_menu']."'","");
        // $modulos = $conexion->consulta("SELECT * FROM modulos WHERE  nivel_mod='".$_SESSION['item_menu']."'","");
        // $alias='u';
        // $validar='true';
        // $ocultar_creador='true';
        // if ($_SESSION['id_rol']!=1) {
        //     require_once $repeat."common/model/restricciones.php";
        // }


        try{
            $db = $this->db;
            $list = Torneos::listar($db);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => $list
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //Listar select
    $app->get('/listSelect', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $listClubes = \Clases\Admin\Fanaticos\Clubes::listar($db);
            $listPaises = \Clases\Admin\General\Paises::listar($db);
            $listLocal = [];
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                    "paises"=>$listPaises, "locales"=>$listLocal, "clubes"=>$listClubes
                ]
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //Agregar
    $app->post('/add', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $claseUsuario = new Torneos;
            $parametros = $request->getParsedBody();
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            
            $newObj = [    
                "nombre_torneo" => $parametros['nombre_torneo'],
                "tipo_torneo" => $parametros['tipo_torneo'],
                "fecha_ini_torneo" => $parametros['fecha_ini_torneo'],
                "fecha_creador" => date("Y-m-d H:y:s"),

            ];
            $idUsuario = $claseUsuario->insertar($db,$newObj);
        
            if($idUsuario == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => []
                ]);
            }      

            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => 'Operación exitosa!.',
                'data' => []
            ]);
            
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //listar para editar
    $app->get('/edit/{id}', function($request, $response, $args){
        try{
            $db = $this->db;
            if (!isset($args['id']) ) {
                return json_encode( array( 
                    'code' => 404, 
                    'status' => 'fail', 
                    'message' => 'Parametros incompletos',
                    'data' =>  []
                ) );
            }
            $id = $args['id'];
            $datos = Torneos::getDataById($db ,$id);
            if($datos){
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'success', 
                    'message' => '',
                    'data' => $datos
                ]);
            }else{
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'fail', 
                    'message' => 'Dato no encontrado.',
                    'data' => $datos
                ]);
            }
            
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //Editar
    $app->post('/update/{id}', function($request, $response, $args){
        try{
            $db = $this->db;
            $claseUsuario = new Torneos( $this->logger );
            $parametros = $request->getParsedBody();
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            
            $newObj = [    
                "nombre_torneo" => $parametros['nombre_torneo'],
                "tipo_torneo" => $parametros['tipo_torneo'],
                "fecha_ini_torneo" => $parametros['fecha_ini_torneo'],
                "fecha_modificador" => date("Y-m-d H:y:s"),

            ];
            $id = $args['id'];
            $idUsuario = $claseUsuario->actualizar($db,$newObj,$id);
            if($idUsuario == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => [$idUsuario]
                ]);
            }      

            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => 'Operación exitosa!.',
                'data' => []
            ]);
            
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //Eliminar
    $app->delete('/delete/{id}', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            if (!isset($args['id']) ) {
                return json_encode( array( 
                    'code' => 404, 
                    'status' => 'fail', 
                    'message' => 'Parametros incompletos',
                    'data' =>  $request
                ) );
            }

            $id = $args['id'];
            $claseUsuario = new Torneos;
            $claseUsuario->eliminar($db,$id);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => 'Operación exitosa!.',
                'data' => []
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

});


?>
