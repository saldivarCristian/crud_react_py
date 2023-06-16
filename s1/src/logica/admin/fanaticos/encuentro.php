<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Fanaticos\Encuentros;

$app->group('/admin/encounters', function(\Slim\App $app) {
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
            $list = Encuentros::listar($db);
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
            $listTorneos = \Clases\Admin\Fanaticos\Torneos::listar($db);
            $listEstadios = \Clases\Admin\Fanaticos\Estadios::listar($db);
            $listLocal =[];
            $listArbitro = \Clases\Admin\Fanaticos\Arbitros::listar($db);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                    "torneos"=>$listTorneos,
                     "locales"=>$listLocal, 
                     "estadios"=>$listEstadios, 
                     "clubes"=>$listClubes, 
                     "arbitros"=>$listArbitro
                ]
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //Listar select
    $app->get('/listSelectById/{id}', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $encuentro = \Clases\Admin\Fanaticos\Encuentros::listar($db);
            $listFormaciones = \Clases\Admin\Fanaticos\Formaciones::listar($db);
            $listfutbolistas = \Clases\Admin\Fanaticos\Futbolistas::listar($db);
            $seleccionFutbolistas = \Clases\Admin\Fanaticos\Encuentros::listFormacionById($db,$args['id']);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                     "encuentro"=>$encuentro[0],
                     "seleccionFutbolistas"=>$seleccionFutbolistas,
                     "formaciones"=>$listFormaciones,
                     "futbolistas"=>$listfutbolistas
                ]
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //Agregar formacion
    $app->post('/addFormacion', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $claseUsuario = new Encuentros;
            $parametros = $request->getParsedBody();
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            $id = $parametros['id_encuentro'];
            $id_formacion = $parametros['id_formacion'];
            $rowFormacion = $parametros['dataFutbolista'];

            $claseUsuario->eliminarSeleccionFormacion($db,$id);
            
            foreach ($rowFormacion as $key => $value) {                  
                $newObj = [    
                    "id_encuentro" => $id,
                    "orden_seleccion_futbolista" => $value['orden'],
                    "id_futbolista" => $value['id_futbolista'],
                    "nombre_futbolista" => $value['nombre'],
                    "imagen_futbolista" => $value['imagen_futbolista'],
                    "clase" => $value['id']
                ];
                $idUsuario = $claseUsuario->insertarSeleccionFutbolista($db,$newObj);
            }

            $newObj = [    
                "id_formacion" => $id_formacion
            ];
            $idUsuario = $claseUsuario->actualizar($db,$newObj,$id);
        
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
    //Agregar
    $app->post('/add', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $claseUsuario = new Encuentros;
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
                "id_torneo" => $parametros['id_torneo'],
                "fecha_encuentro" => $parametros['fecha_encuentro'],
                "dia_encuentro" => $parametros['dia_encuentro'],
                "hora_encuentro" => $parametros['hora_encuentro'],
                "id_club" => $parametros['id_club'],
                "cancha_encuentro" => $parametros['cancha_encuentro'],
                "id_estadio" => $parametros['id_estadio'],
                "estado_encuentro"=> $parametros['estado_encuentro'],
                "id_arbitro" => $parametros['id_arbitro'],
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
            $datos = Encuentros::getDataById($db ,$id);
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
            $claseUsuario = new Encuentros( $this->logger );
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
                "id_torneo" => $parametros['id_torneo'],
                "fecha_encuentro" => $parametros['fecha_encuentro'],
                "dia_encuentro" => $parametros['dia_encuentro'],
                "hora_encuentro" => $parametros['hora_encuentro'],
                "id_club" => $parametros['id_club'],
                "cancha_encuentro" => $parametros['cancha_encuentro'],
                "id_estadio" => $parametros['id_estadio'],
                "estado_encuentro"=> $parametros['estado_encuentro'],
                "id_arbitro" => $parametros['id_arbitro'],
                "inicio_cal_encuentro" => date("Y-m-d H:y:s"),
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

        //Estado del encuentro
        $app->post('/updateEstado/{id}', function($request, $response, $args){
            try{
                $db = $this->db;
                $claseUsuario = new Encuentros( $this->logger );
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
                    "estado_actual_encuentro" => $parametros['estado_actual_encuentro'],
                    "res_contra_encuentro" => $parametros['res_contra_encuentro'],
                    "res_favor_encuentro" => $parametros['res_favor_encuentro'],
                    "id_creador" => $parametros['id_creador'],
                    "id_modificador" => $parametros['id_modificador'],
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
            $claseUsuario = new Encuentros;
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
