<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\General\Usuarios;
use \Firebase\JWT\JWT;

$app->group('/admin/login', function(\Slim\App $app) {
    //acceso al sistema por app
    $app->post('/singing', function(Request $request, Response $response){
        $username = $request->getParam('identification');
        $password = $request->getParam('password');
        $username = htmlentities(trim($username));
        try{
            // Get DB Object
            $db = $this->db;
            $datos = Usuarios::getDataByCI($db,$username);
            if ( isset($datos->password_usuario) ) 
            {
                $idUsuario = $datos->id_usuario;
                if ($datos->estado_usuario == '1')
                {
                    return $response->withJson(
                        array('status' => 'fail', 'message' => 'Cuenta Bloqueada. \nComuniquese con el proveedor.', 'data' => array())
                    );
                }
                $fechaActual = date("Y-m-d H:i:s");
        
                if(password_verify($password, @$datos->password_usuario) )
                {                           
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $fecha = date('Y-m-d H:i:s');
            
                    $settings = $this->get('settings'); // get settings array.
                    $option=[
                                'aud' =>  Aud(),
                                'id_usuario' => $idUsuario,
                                'nombre' => $datos->nombre_usuario,
                                'apellido' => $datos->apellido_usuario,
                                'ci' => $datos->ci_usuario,
                                'rol' => $datos->id_rol,
                                'local' => $datos->id_local
                            ];
                    $token = JWT::encode($option, $settings['jwt']['secret'], "HS256");
                    $id_rol= $datos->id_rol;
                    $menu = @Util::executeQuery($db,"SELECT * FROM ".DB_BASE.".base_roles WHERE id_rol = $id_rol",[],"")['data'];
                    $menu = @Util::executeQuery($db,"SELECT * FROM ".DB_BASE.".base_roles WHERE id_rol = $id_rol",[],"")['data'];
                    return $response->withJson(
                                        array(
                                                'status' => 'success',
                                                'message' => 'Ok',
                                                'data'=> array(
                                                    'id_usuario' => $idUsuario,
                                                    'nombre' => $datos->nombre_usuario,
                                                    'apellido' => $datos->apellido_usuario,
                                                    'ci' => $datos->ci_usuario,
                                                    'local' => $datos->id_local,
                                                    'rol' => $datos->id_rol,
                                                    'menu' => json_decode($menu->rol_menu),
                                                    'menu_rol' => $_SERVER,
                                                    'token' => $token
                                                )
                                            )
                                    );
                                            
                }else{
                    return $response->withJson(array(
                        "status" => "fail", "message" => "Usuario/Contrase単a Incorrecta .",  "data" => array() )
                    );
                }
            }else{
                return $response->withJson(array(
                    "status" => "fail", "message" => "Usuario/Contrase単a Incorrecta ..",  "data" => array() )
                );
            }
            $db = null;

        } catch(PDOException $e){
            throw $e;
        }

    });
});