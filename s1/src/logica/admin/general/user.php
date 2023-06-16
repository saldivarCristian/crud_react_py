<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\General\Usuarios;
use \Clases\Admin\Restricciones;

$app->group('/admin/user', function(\Slim\App $app) {
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

            $idRol = $request->getHeaderLine('rol');
            // Restricciones::verificarAccesoAlModulo($db,$idRol,2);

            $list = Usuarios::listar($db);
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
            $listEstados = [
                ["id_estado" =>0, "nombre_estado"=> "activo"],
                ["id_estado" =>1, "nombre_estado"=> "inactivo"]
            ];
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                    "roles"=>$listRoles,"ciudades"=>$listCiudades,"locales"=>$listLocal,"estados"=>$listEstados
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
            $claseUsuario = new Usuarios;
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
                "nombre_usuario" => $parametros['nombre_usuario'],
                "apellido_usuario" => $parametros['apellido_usuario'],
                "ci_usuario" => $parametros['ci_usuario'],
                "email_usuario" => $parametros['email_usuario'],
                "telefono_usuario" => $parametros['telefono_usuario'],
                "celular_usuario" => $parametros['celular_usuario'],
                "direccion_usuario" => $parametros['direccion_usuario'],
                "id_ciudad" => $parametros['ciudad'],
                "password_usuario" =>  $passCliente,
                "id_local" => $parametros['id_local'],
                //"estado_usuario" => $parametros['estado_usuario'],
                "id_rol" => $parametros['id_rol'],
                "sexo_usuario" => $parametros['sexo_usuario'],
                "id_creador" => $parametros['id_creador'],
                "fecha_creador" => date("Y-m-d H:y:s"),
                // "id_modificador" => $parametros['id_modificador'],
                // "fecha_modificador" => $parametros['fecha_modificador'],
                // "insert_local" => $parametros['insert_local'],
                "cuil_usuarios" => $parametros['cuil_usuario']

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
            $datos = Usuarios::getDataById($db ,$id);
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
            $claseUsuario = new Usuarios( $this->logger );
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
                "nombre_usuario" => $parametros['nombre_usuario'],
                "apellido_usuario" => $parametros['apellido_usuario'],
                "ci_usuario" => $parametros['ci_usuario'],
                "email_usuario" => $parametros['email_usuario'],
                "telefono_usuario" => $parametros['telefono_usuario'],
                "celular_usuario" => $parametros['celular_usuario'],
                "direccion_usuario" => $parametros['direccion_usuario'],
                "id_ciudad" => $parametros['id_ciudad'],
                "id_local" => $parametros['id_local'],
                "estado_usuario" => $parametros['estado_usuario'],
                "id_rol" => $parametros['id_rol'],
                "sexo_usuario" => $parametros['sexo_usuario'],
                "id_creador" => $parametros['id_creador'],
                // "fecha_creador" => $parametros['fecha_creador'],
                "id_modificador" => $parametros['id_modificador'],
                "fecha_modificador" => date("Y-m-d H:y:s"),
                // "insert_local" => $parametros['insert_local'],
                "cuil_usuarios" => $parametros['cuil_usuario']

            ];
            if($parametros['password_usuario'] != ""){
                $newObj['password_usuario'] = $passCliente;
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
            $claseUsuario = new Usuarios;
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
