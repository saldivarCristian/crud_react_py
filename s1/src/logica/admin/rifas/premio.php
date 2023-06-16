<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Rifas\Premios;

$app->group('/admin/rifas/premio', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = Premios::listar($db);
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
            $listProductos = \Clases\Admin\Rifas\Productos::listar($db);            
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                    "productos"=>$listProductos
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
            $clasePremios = new Premios;
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
                "nombre_premio" => $parametros['nombre_premio'],
                "tipo_premio" => $parametros['tipo_premio'],
                "monto_premio" => $parametros['monto_premio'],
                "id_producto" => $parametros['id_producto']
                

            ];
            $idPremios = $clasePremios->insertar($db,$newObj);
            $target = 'rifas';
            $responseImage = uploadFile('data', $target, 'premio');

            if( isset( $responseImage['error'] ) ){
                throw new Exception( $responseImage['error'] );
            }else{
                $image = $responseImage['name'][0];
                $newObj = [    
                    "imagen_premio" => $image,
                ];
            
                $clasePremios->actualizar($db,$newObj,$idPremios);
            }

        
            if($idPremios == 0){
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
            $datos = Premios::getDataById($db ,$id);
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
            $clasePremios = new Premios( $this->logger );
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
                "nombre_premio" => $parametros['nombre_premio'],
                "tipo_premio" => $parametros['tipo_premio'],
                "monto_premio" => $parametros['monto_premio'],
                "id_producto" => $parametros['id_producto'],
               
            ];
            $id = $args['id'];
            $idPremios = $clasePremios->actualizar($db,$newObj,$id);
            if (!empty($_FILES['data'])) {
                $target = 'rifas';
                $responseImage = uploadFile('data', $target, 'premio');
    
                if( isset( $responseImage['error'] ) ){
                    throw new Exception( $responseImage['error'] );
                }else{
                    $image = $responseImage['name'][0];
                    $newObj = [    
                        "imagen_premio" => $image,
                    ];
                
                    $clasePremios->actualizar($db,$newObj,$id);
                    unlink(__DIR__."/../../../../public/img/rifas/lg/premio/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/rifas/sm/premio/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/rifas/md/premio/".$parametros['file_hidden']);
                    
                }
                
            }
            if($idPremios == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => [$idPremios]
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
            $clasePremio = new Premios;
            $clasePremio->eliminar($db,$id);
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
