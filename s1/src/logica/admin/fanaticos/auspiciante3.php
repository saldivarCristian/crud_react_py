<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Fanaticos\Auspiciantes3;

$app->group('/admin/auspiciantes3', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = Auspiciantes3::listar($db);
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
    $app->get('/listSelect', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $listEstados = [
                ["id_estado" =>0, "nombre_estado"=> "activo"],
                ["id_estado" =>1, "nombre_estado"=> "inactivo"]
            ];
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                   "estados"=>$listEstados
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
            $claseAuspiciante = new Auspiciantes3;
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
                "nombre_auspiciante" => $parametros['nombre_auspiciante'],
                "webpage_auspiciante" => $parametros['webpage_auspiciante'],
                "insert_local" => $parametros['insert_local'],
                "id_creador" => $parametros['id_creador'],
                "fecha_creador" => date("Y-m-d H:y:s"),

            ];
            $idAuspiciante = $claseAuspiciante->insertar($db,$newObj);

            $target = 'fanaticos';
            $responseImage = uploadFile('data', $target, 'auspiciantes');

            if( isset( $responseImage['error'] ) ){
                throw new Exception( $responseImage['error'] );
            }else{
                $image = $responseImage['name'][0];
                $newObj = [    
                    "imagen_auspiciante" => $image,
                ];
            
                $claseAuspiciante->actualizar($db,$newObj,$idAuspiciante);
            }

        
            if($idAuspiciante == 0){
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
            $datos = Auspiciantes3::getDataById($db ,$id);
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
            $claseAuspiciante = new Auspiciantes3( $this->logger );
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
                "nombre_auspiciante" => $parametros['nombre_auspiciante'],
                "webpage_auspiciante" => $parametros['webpage_auspiciante'],
                "fecha_modificador" => date("Y-m-d H:y:s"),
                "estado_auspiciante" => $parametros['estado_auspiciante'],


            ];
            $id = $args['id'];
            $idAuspiciante = $claseAuspiciante->actualizar($db,$newObj,$id);
            if (!empty($_FILES['data'])) {
                $target = 'fanaticos';
                $responseImage = uploadFile('data', $target, 'auspiciantes');
    
                if( isset( $responseImage['error'] ) ){
                    throw new Exception( $responseImage['error'] );
                }else{
                    $image = $responseImage['name'][0];
                    $newObj = [    
                        "imagen_auspiciante" => $image,
                    ];
                
                    $claseAuspiciante->actualizar($db,$newObj,$id);
                    unlink(__DIR__."/../../../../public/img/fanaticos/lg/auspiciantes/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/fanaticos/sm/auspiciantes/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/fanaticos/md/auspiciantes/".$parametros['file_hidden']);
                    
                }
            }
            

            if($idAuspiciante == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => [$idAuspiciante]
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
            $claseUsuario = new Auspiciantes3;
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
