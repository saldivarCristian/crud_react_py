<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Prode\EleccionesCargos;

$app->group('/admin/prode/eleccionCargo', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = EleccionesCargos::listar($db);
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
            $listLocal =[];
            $listCargo = \Clases\Admin\Prode\Cargos::listar($db);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                     "locales"=>$listLocal,
                     "cargos"=>$listCargo 

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
            $eleccionCargo = \Clases\Admin\Prode\EleccionesCargos::getDataById($db,$args['id']);
            $listPoliticos = \Clases\Admin\Prode\Politicos::listarPorCargo($db,$args['id']);
            $seleccionPoliticos = \Clases\Admin\Prode\EleccionesCargos::listFormacionById($db,$args['id']);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                     "eleccionesCargos"=>$eleccionCargo,
                     "seleccionPoliticos"=>$seleccionPoliticos,
                     "politicos"=>$listPoliticos,
                     
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
            $claseUsuario = new EleccionesCargos;
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
                "escano_elec_cargo"=> $parametros['escano_elec_cargo'],
                "cant_votos_elec_cargo"=> $parametros['cant_votos_elec_cargo'],
                "color_elec_cargo"=> $parametros['color_elec_cargo'],
                "id_cargo"=> $parametros['id_cargo'],
                "estado_elec_cargo"=> $parametros['estado_elec_cargo'],
                "votos_blancos_elec_cargo" => $parametros['votos_blancos_elec_cargo'],
                "votos_nulos_elec_cargo" => $parametros['votos_nulos_elec_cargo'],
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
            $datos = EleccionesCargos::getDataById($db ,$id);
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
            $claseUsuario = new EleccionesCargos( $this->logger );
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
                "escano_elec_cargo"=> $parametros['escano_elec_cargo'],
                "cant_votos_elec_cargo"=> $parametros['cant_votos_elec_cargo'],
                "color_elec_cargo"=> $parametros['color_elec_cargo'],
                "id_cargo"=> $parametros['id_cargo'],
                "estado_elec_cargo"=> $parametros['estado_elec_cargo'],
                "votos_blancos_elec_cargo" => $parametros['votos_blancos_elec_cargo'],
                "votos_nulos_elec_cargo" => $parametros['votos_nulos_elec_cargo'],
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
            $claseUsuario = new EleccionesCargos;
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
