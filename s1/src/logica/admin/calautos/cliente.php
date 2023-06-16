<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Calautos\Clientes;

$app->group('/admin/calautos/clientes', function(\Slim\App $app) {
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

            $passCliente = password_hash($parametros['password_usuario'], PASSWORD_BCRYPT);
            $newObj = [    
                "nombre_con_cliente" => $parametros['nombre_usuario'],
                "apellido_con_cliente" => $parametros['apellido_usuario'],
                "ruc_cliente" => $parametros['ci_usuario'],
                "email_con_cliente" => $parametros['email_usuario'],
                "tel_con_cliente" => $parametros['telefono_usuario'],
                "direccion_cliente" => $parametros['direccion_usuario'],
                "id_ciudad" => $parametros['ciudad'],
                "pass_cliente" => $passCliente,
                //"id_local" => $parametros['id_local'],
                // "estado_usuario" => $parametros['estado_usuario'],
                //"id_rol" => $parametros['id_rol'],
                "sexo_cliente" => $parametros['sexo_usuario'],
                //"id_creador" => $parametros['id_creador'],
                "fecha_creador" => date("Y-m-d H:y:s"),
                // "id_modificador" => $parametros['id_modificador'],
                // "fecha_modificador" => $parametros['fecha_modificador'],
                // "insert_local" => $parametros['insert_local'],
                //"cuil_Clientes" => $parametros['cuil_usuario']

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

            $passCliente = password_hash($parametros['password_usuario'], PASSWORD_BCRYPT);
            $newObj = [    
                "nombre_con_cliente" => $parametros['nombre_usuario'],
                "apellido_con_cliente" => $parametros['apellido_usuario'],
                "ruc_cliente" => $parametros['ci_usuario'],
                "email_con_cliente" => $parametros['email_usuario'],
                "tel_con_cliente" => $parametros['telefono_usuario'],
                //"direccion_cliente" => $parametros['direccion_usuario'],
                "id_ciudad" => $parametros['id_ciudad'],
                //"id_local" => $parametros['id_local'],
                "estado_cliente" => $parametros['estado_cliente'],
                //"id_rol" => $parametros['id_rol'],
                "sexo_cliente" => $parametros['sexo_usuario'],
                //"id_creador" => $parametros['id_creador'],
                "fecha_creador" => date("Y-m-d H:y:s"),
                // "id_modificador" => $parametros['id_modificador'],
                // "fecha_modificador" => $parametros['fecha_modificador'],
                // "insert_local" => $parametros['insert_local'],
                //"cuil_Clientes" => $parametros['cuil_usuario']

            ];
            if($parametros['password_usuario'] != ""){
                $newObj['pass_cliente'] = $passCliente;
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
