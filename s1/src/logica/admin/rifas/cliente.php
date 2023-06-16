<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Rifas\Clientes;

$app->group('/admin/rifas/clientes', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = Clientes::listar($db);
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
    $app->post('/add', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $claseUsuario = new Clientes;
            $parametros = $request->getParsedBody();
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            $passCliente = password_hash($parametros['password_cliente'], PASSWORD_BCRYPT);
            $newObj = [    
                "apellido_cliente"=> $parametros['apellido_cliente'],
                "cod_pais_cliente" => $parametros['cod_pais_cliente'],
                "fecha_nacimiento_cliente" => $parametros['fecha_nacimiento_cliente'],
                "direccion_cliente" => $parametros['direccion_cliente'],
                "doc_cliente" => $parametros['doc_cliente'],
                "email_cliente" => $parametros['email_cliente'],
                "estado_cliente" => $parametros['estado_cliente'],
                "nickname_cliente" => $parametros['nickname_cliente'],
                "nombre_cliente" => $parametros['nombre_cliente'],
                "tel_cliente"=> $parametros['tel_cliente'],
                "password_cliente" => $passCliente,
                "fecha_creador" => date("Y-m-d H:y:s")
 

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
        //Listar select
        $app->get('/listSelect', function(Request $request, Response $response, array $args){
            try{
                $db = $this->db;
                $listCiudades = \Clases\Admin\General\Ciudades::listar($db);
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'success', 
                    'message' => '',
                    'data' => [
                        "ciudades"=>$listCiudades
                    ]
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
            $datos = Clientes::getDataById($db ,$id);
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
            $claseUsuario = new Clientes( $this->logger );
            $parametros = $request->getParsedBody();
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            $passCliente = password_hash($parametros['password_cliente'], PASSWORD_BCRYPT);
            $newObj = [    
                "apellido_cliente"=> $parametros['apellido_cliente'],
                "cod_pais_cliente" => $parametros['cod_pais_cliente'],
                "fecha_nacimiento_cliente" => $parametros['fecha_nacimiento_cliente'],
                "direccion_cliente" => $parametros['direccion_cliente'],
                "doc_cliente" => $parametros['doc_cliente'],
                "email_cliente" => $parametros['email_cliente'],
                "estado_cliente" => $parametros['estado_cliente'],
                "nickname_cliente" => $parametros['nickname_cliente'],
                "nombre_cliente" => $parametros['nombre_cliente'],
                "tel_cliente"=> $parametros['tel_cliente'],
                "password_cliente" => $passCliente,
                "fecha_creador" => date("Y-m-d H:y:s"),


            ];
            if($parametros['password_cliente'] != ""){
                $newObj['password_cliente'] = $passCliente;
            }
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
            $claseUsuario = new Clientes;
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
