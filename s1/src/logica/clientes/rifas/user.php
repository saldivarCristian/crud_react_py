<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Clases\Admin\General\Usuarios;
use \Firebase\JWT\JWT;


$app->group('/rifas/api/app', function(\Slim\App $app) {
    //acceso al sistema por app
    $app->get('/login/{username}&{password}', function(Request $request, Response $response){
        $username = $request->getAttribute('username');
        $password = $request->getAttribute('password');
        $username = htmlentities(trim($username));
        try{
            // Get DB Object
            $db = $this->db;
            $agente = new Usuarios();
            $datos = $agente->getDataByCI($db, $username);
            $db = null;

            if( !$datos ){
                return $response->withJson(
                    array('status' => 'fail', 'message' => 'Usuario/Contraseña Incorrecta.', 'data' => array())
                );
            }else{
                if(password_verify($password, @$datos->password_usuario) )
                {  

                    $nombre =  $datos->nombre_usuario." ".$datos->apellido_usuario;
                    $email =  $datos->email_usuario;
                    $id =  $datos->id_usuario;
                    $settings = $this->get('settings'); // get settings array.
                    $option=[
                                'aud' =>  Aud(),
                                'id_usu' => $id
                            ];
                    $token = JWT::encode($option, $settings['jwt']['secret'], "HS256");
                    return $response->withJson(
                        array(
                            'status' => 'success',
                            'message' => 'Ok',
                            'data'=> array(
                                'usuario' =>[
                                    'user' => $nombre,
                                    'email' => $email,
                                    'img' => '',
                                    'rol' => '',
                                    'ciUser' => $id,
                                    'sexo' => '',
                                    'token' => $token
                                ],
                                'config' => RIFA['SOPORTE']
                            )
                            )
                    );
                }else{
                    return $response->withJson(
                        array('status' => 'fail', 'message' => 'Usuario/Contraseña Incorrecta .', 'data' => array())
                    );
                }
            }

        } catch(PDOException $e){
            throw $e;
        }
    });
});