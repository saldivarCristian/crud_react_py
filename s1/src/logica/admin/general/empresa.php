<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\General\Empresas;

$app->group('/admin/business', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = Empresas::listar($db);
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
            $listRoles = \Clases\Admin\General\Roles::listar($db);
            $listCiudades = \Clases\Admin\General\Ciudades::listar($db);
            $listLocal = \Clases\Admin\General\Locales::listar($db);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                    "roles"=>$listRoles,"ciudades"=>$listCiudades,"locales"=>$listLocal
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
            $claseEmpresa = new Empresas;
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
                "empresa_marca" => $parametros['empresa_marca'],
                "empresa_razon_social" => $parametros['empresa_razon_social'],
                "empresa_ruc" => $parametros['empresa_ruc'],
                "empresa_actividades" => $parametros['empresa_actividades'],
                "empresa_direccion" => $parametros['empresa_direccion'],
                "empresa_telefono" => $parametros['empresa_telefono'],
                "empresa_email" => $parametros['empresa_email'],
                "fecha_creador" => date("Y-m-d H:y:s"),

            ];
            $idEmpresa = $claseEmpresa->insertar($db,$newObj);
            $target = 'fanaticos';
            $responseImage = uploadFile('data', $target, 'empresas');

            if( isset( $responseImage['error'] ) ){
                throw new Exception( $responseImage['error'] );
            }else{
                $image = $responseImage['name'][0];
                $newObj = [    
                    "empresa_logo" => $image,
                ];
            
                $claseEmpresa->actualizar($db,$newObj,$idEmpresa);
            }
        
            if($idEmpresa == 0){
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
            $datos = Empresas::getDataById($db ,$id);
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
            $claseEmpresa = new Empresas( $this->logger );
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
                "empresa_marca" => $parametros['empresa_marca'],
                "empresa_razon_social" => $parametros['empresa_razon_social'],
                "empresa_ruc" => $parametros['empresa_ruc'],
                "empresa_actividades" => $parametros['empresa_actividades'],
                "empresa_direccion" => $parametros['empresa_direccion'],
                "empresa_telefono" => $parametros['empresa_telefono'],
                "empresa_email" => $parametros['empresa_email'],
                "fecha_modificador" => date("Y-m-d H:y:s"),

            ];
            $id = $args['id'];
            $idEmpresa = $claseEmpresa->actualizar($db,$newObj,$id);
            if (!empty($_FILES['data'])) {
                $target = 'fanaticos';
                $responseImage = uploadFile('data', $target, 'empresas');
    
                if( isset( $responseImage['error'] ) ){
                    throw new Exception( $responseImage['error'] );
                }else{
                    $image = $responseImage['name'][0];
                    $newObj = [    
                        "empresa_logo" => $image,
                    ];
                
                    $claseEmpresa->actualizar($db,$newObj,$id);
                    unlink(__DIR__."/../../../../public/img/fanaticos/lg/empresas/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/fanaticos/sm/empresas/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/fanaticos/md/empresas/".$parametros['file_hidden']);
                    
                }
            }
            if($idEmpresa == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => [$idEmpresa]
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
            $claseUsuario = new Empresas;
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
