<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Rifas\Sorteos;

$app->group('/admin/sorteo', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = Sorteos::listar($db);
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
            $listCiudades = \Clases\Admin\General\Ciudades::listar($db);
            $listFormula = \Clases\Admin\Rifas\Formulas::listar($db);
            $listPremio = \Clases\Admin\Rifas\Premios::listar($db);                
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                    "ciudades"=>$listCiudades, "formula"=>$listFormula,"premio"=>$listPremio
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
            $claseSorteo = new Sorteos;
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
                "nombre_sorteo" => $parametros['nombre_sorteo'],
                "descripcion_sorteo" => $parametros['descripcion_sorteo'],
                "id_ciudad" => $parametros['id_ciudad'],
                "id_formula" => $parametros['id_formula'],
                "cant_ticket_sorteo" => $parametros['cant_ticket_sorteo'],
                "id_premio" => $parametros['id_premio'],
                "tipo_sorteo" => $parametros['tipo_sorteo'],
                "fecha_sorteo" => $parametros['fecha_sorteo'],
                "fecha_creador" => date("Y-m-d H:y:s"),

            ];
            $idSorteo = $claseSorteo->insertar($db,$newObj);


        
            if($idSorteo == 0){
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
            $datos = Sorteos::getDataById($db ,$id);
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
            $claseSorteo = new Sorteos( $this->logger );
            $parametros = $request->getParsedBody();
            $parametros = $parametros['data'];
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            
            $newObj = [    
                "nombre_sorteo" => $parametros['nombre_sorteo'],
                "descripcion_sorteo" => $parametros['descripcion_sorteo'],
                "id_ciudad" => $parametros['id_ciudad'],
                "id_formula" => $parametros['id_formula'],
                "cant_ticket_sorteo" => $parametros['cant_ticket_sorteo'],
                "id_premio" => $parametros['id_premio'],
                "tipo_sorteo" => $parametros['tipo_sorteo'],
                "fecha_sorteo" => $parametros['fecha_sorteo'],
                "fecha_modificador" => date("Y-m-d H:y:s"),

            ];
            $id = $args['id'];
            $idSorteo = $claseSorteo->actualizar($db,$newObj,$id);
            if (!empty($_FILES['data'])) {
                $target = 'rifas';
                $responseImage = uploadFile('data', $target, 'sorteo');
    
                if( isset( $responseImage['error'] ) ){
                    throw new Exception( $responseImage['error'] );
                }else{
                    $image = $responseImage['name'][0];
                    $newObj = [    
                        "imagen_sorteo" => $image,
                    ];
                
                    $claseSorteo->actualizar($db,$newObj,$id);
                    unlink(__DIR__."/../../../../public/img/rifas/lg/sorteo/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/rifas/sm/sorteo/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/rifas/md/sorteo/".$parametros['file_hidden']);
                    
                }
            }
            if($idSorteo == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => [$idSorteo]
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
            $claseUsuario = new Sorteos;
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
