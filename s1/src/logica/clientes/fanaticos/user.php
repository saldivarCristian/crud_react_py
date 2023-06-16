<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;
use Api\DataTables;

// Routes

//acceso al sistema por app
    $app->post('/cliente/user/st/login', function(Request $request, Response $response){

        $username = $request->getParam('identification');
        $password = $request->getParam('password');
        $username = htmlentities(trim($username));
        
        try{
            // Get DB Object
            $db = $this->db;

            $client = new Client();
            $datos = $client->getDataClientByCI($db, $username);

            if ( isset($datos->pass_cliente) ) 
            {
                $idCliente = $datos->id_cliente;
                if ($datos->estado_cliente == '1')
                {
                    return $response->withJson(
                        array('status' => 'fail', 'message' => 'Cuenta Bloqueada. \nComuniquese con el proveedor.', 'data' => array())
                    );
                }
                $fechaActual = date("Y-m-d H:i:s");
                $fechaActual = strtotime ( $fechaActual );
                $fechaAuto = strtotime ($datos->autoexclusion);

                $fechaAutoExlusion = '';
                if ($fechaActual < $fechaAuto) {

                    $fechaAutoExlusion = $datos->autoexclusion;
                }

                if(password_verify($password, @$datos->pass_cliente) )
                {                           
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $fecha = date('Y-m-d H:i:s');
                    // $sql = "INSERT INTO ".DB_MBO.".geolocation (id_juego, ip_geo, cliente, id_maquina, lat_geo, lon_geo, fecha_geo,acceso ) VALUES ('0','$ip','$username','0', '', '', '$fecha',1)";
                    // $stmt = $db->prepare($sql);
                    // $stmt->execute();

                    $nickname =  $datos->nickname != null ? $datos->nickname : "";
                    $id_club_futbol = -1;
                    if($datos->id_club_futbol != 0) $id_club_futbol = $datos->id_club_futbol;

                    $settings = $this->get('settings'); // get settings array.
                    $option=[
                                'aud' =>  Aud(),
                                'id_cliente' => $idCliente,
                                'nombre' => $datos->nombre_con_cliente,
                                'apellido' => $datos->apellido_con_cliente,
                                'ruc_cliente' => $datos->ruc_cliente,
                                // 'sucursal' => $datos->sucursal
                            ];
                    $token = JWT::encode($option, $settings['jwt']['secret'], "HS256");

                    return $response->withJson(
                                        array(
                                                'status' => 'success',
                                                'message' => 'Ok',
                                                'data'=> array(
                                                    'id_cliente' => $idCliente,
                                                    'nombre' => $datos->nombre_con_cliente,
                                                    'apellido' => $datos->apellido_con_cliente,
                                                    'sexo' => $datos->sexo_cliente,
                                                    'telefono' => $datos->tel_con_cliente,
                                                    'ruc_cliente' => $datos->ruc_cliente,
                                                    'email' => $datos->email_con_cliente,
                                                    'direccion' => $datos->direccion_cliente,
                                                    'fecha_nac'    => $datos->fecha_nac_cliente,
                                                    'nickname' => $nickname,
                                                    'token' => $token
                                                    )
                                                )
                                    );
                                               
                }else{
                    return $response->withJson(array(
                        "status" => "fail", "message" => "Usuario/Contraseña Incorrecta .",  "data" => array() )
                    );
                }
             }else{
                return $response->withJson(array(
                    "status" => "fail", "message" => "Usuario/Contraseña Incorrecta ..",  "data" => array() )
                );
            }
            $db = null;

        } catch(PDOException $e){
            throw $e;
        }

    });

//Add usuario
    $app->post('/cliente/user/st/addUsuario', function(Request $request, Response $response){
        // validar acutalizaciones de app
        // $input = $request->getParsedBody();

        $nombreDocumentoCliente = '';       
        $estadoCliente = 0;
        $rucCliente = $request->getParam('identification');
        // $nickname = $request->getParam('nickname');
        $nombreCliente = $request->getParam('name');
        $socioCliente = $request->getParam('socio_cliente');
        $apellidoCliente = $request->getParam('last_name');
        $password = $request->getParam('password');
        $emailCliente = $request->getParam('email');
        $fechaNacCliente = $request->getParam('year')."-".$request->getParam('month')."-".$request->getParam('day');
        $codigoPais = $request->getParam('codigo_pais');
        $telefonoCliente = $request->getParam('phone');
        $sexoCliente = $request->getParam('sexo_cliente');

        if($rucCliente == "" || $nombreCliente == "" || $password == "" || $telefonoCliente == "" ){
            return $response->withJson(
                array("status" => "fail", "message" => "Complete todos los campos",  "data" => $request->getParsedBody())
            );
        }

        // $sexoCliente = $request->getParam('sexo_usu');
        // $sexoCliente = '';
        $passCliente = password_hash($password, PASSWORD_BCRYPT);

        // $nickname = strtolower($nickname);
        // Get DB Object
        $db = $this->db;

        // $db = null;
        $insert_local = SUCURSAL_DEFECTO;

        $fecha_modificador = date('Y-m-d H:i:s');
        $nombre_modificador = "Web Form";
        $id_creador = 0;

        $sqlNumeroTelefono = "SELECT COUNT(*) AS cantidad FROM  ".DB_CON.".fanatico_clientes WHERE tel_con_cliente = '$telefonoCliente'";
        $stmt = $db->query($sqlNumeroTelefono);
        $datoTelefonoUsuario = $stmt->fetch(PDO::FETCH_OBJ);
        $cantidad_nro = $datoTelefonoUsuario->cantidad;

        if($cantidad_nro >= 1){
            return $response->withJson(
                array("status" => "fail", "message" => "El Nro de Telefono ya existe.",  "data" => array())
            );
        }
        
        $sqlrucCliente = "SELECT COUNT(*) AS cantidad FROM  ".DB_CON.".fanatico_clientes WHERE ruc_cliente = '$rucCliente'";
        $stmt = $db->query($sqlrucCliente);
        $datoRucCliente = $stmt->fetch(PDO::FETCH_OBJ);
        $cantidad_ruc = $datoRucCliente->cantidad;


        if($cantidad_ruc >= 1){
            return $response->withJson(
                array("status" => "fail", "message" => "El nro de documento ya se encuentra registrado",  "data" => array())
            );
        }

        if(strlen($telefonoCliente)  <= 9 ){
            return $response->withJson(
                array("status" => "fail", "message" => "El Nro de Telefono es invalido. \n Ejemplo: 981111111",  "data" => array())

            );
        }

        // $sqlValidacionNickname = "SELECT id_cliente FROM ".DB_CON.".fanatico_clientes WHERE nickname = '$nickname'";
        // $stmt = $db->query($sqlValidacionNickname);
        // $datosNickname = $stmt->fetch(PDO::FETCH_OBJ);
        // $count = $stmt->rowCount();
        // if($count > 0)
        // {
        //     return $response->withJson(
        //         array("status" => "fail", "message" => "Nickname existente.",  "data" => array())

        //     );
        // }

        $codigoValidacion = "";
        // if(CODIGO_VALIDACION){
        //     $codigoValidacion = generateRandomString(4,0);
        //     sendMsg("+".$telefonoCliente,$codigoValidacion,0,$ref = time());
        // }

        try{
            $sql = "INSERT INTO ".DB_CON.".fanatico_clientes 
            (
                razon_cliente,
                ruc_cliente,
                nombre_con_cliente,
                apellido_con_cliente,
                socio_cliente,
                sexo_cliente,
                tel_con_cliente,
                codigo_pais,
                email_con_cliente,
                pass_cliente,
                fecha_nac_cliente,
                estado_cliente,
                doc_cliente,
                codigo_validacion,
                id_creador,
                fecha_creador,
                nombremodi_cliente,
                fecha_modificador,
                insert_local
            )
            VALUES 
            (
                :razon_cliente,
                :ci_usu,
                :nombre_usu,
                :apellido_usu,
                :socio_cliente,
                :sexo_usu,
                :telefono_usu,
                :codigo_pais,
                :email,
                :pass_usu,
                :fec_nac_usu,
                :estado_usu,
                :doc_cliente,
                :codigo_validacion,
                :id_creador,
                :fecha_creador,
                :nombre_modificador,
                :fecha_modificador,
                :insert_local
            )"; 
            $stmt = $db->prepare($sql);
            $razon =  $nombreCliente.' '.$apellidoCliente;
            $stmt->bindParam(':razon_cliente', $razon);
            $stmt->bindParam(':nombre_usu', $nombreCliente);
            $stmt->bindParam(':apellido_usu', $apellidoCliente);
            $stmt->bindParam(':sexo_usu', $sexoCliente);
            $stmt->bindParam(':fec_nac_usu', $fechaNacCliente);
            $stmt->bindParam(':ci_usu', $rucCliente);
            $stmt->bindParam(':socio_cliente', $socioCliente);
            $stmt->bindParam(':pass_usu', $passCliente);
            $stmt->bindParam(':fecha_modificador', $fecha_modificador);
            $stmt->bindParam(':insert_local', $insert_local);
            $stmt->bindParam(':id_creador', $id_creador);
            $stmt->bindParam(':fecha_creador', $fecha_modificador);
            $stmt->bindParam(':nombre_modificador', $nombre_modificador);
            $stmt->bindParam(':telefono_usu', $telefonoCliente);
            $stmt->bindParam(':estado_usu', $estadoCliente);
            $stmt->bindParam(':doc_cliente', $nombreDocumentoCliente);
            $stmt->bindParam(':email', $emailCliente);
            $stmt->bindParam(':codigo_pais', $codigoPais);
            $stmt->bindParam(':codigo_validacion', $codigoValidacion);

            $stmt->execute();
            $idCliente = $db->lastInsertId();

            //ServidorEmail::UsuarioNuevo($fecha_modificador, $ci_usu);

            return $response->withJson( 
                array("status"=> "success", "message" => "Operacion exitosa.",  "data" => array())
            );
        }
        catch(PDOException $e)
        {
           throw $e;
        }
        
    });

//Actualizar datos personales
    $app->post('/cliente/user/editUsuario', function(Request $request, Response $response){

        $razon_cliente = $request->getParam('nombre').' '.$request->getParam('apellido');
        $nombre = $request->getParam('nombre');
        $apellido = $request->getParam('apellido');
        $telefono = $request->getParam('telefono');
        $sexo = $request->getParam('sexo');
        $email = $request->getParam('email');
        $nickname = $request->getParam('nickname');
        $fec_nac_usu = $request->getParam('fec_nac_usu');
        $ci_usu = $request->getParam('ruc_cliente');
        $direccion_cliente = $request->getParam('direccion');
        $codigoPais = $request->getParam('codigo_pais');
        // $id_club_futbol = $request->getParam('idClub');
        
        //TOKEN
        $token = @$request->getAttribute("token");
        if (@$token['aud'] === Aud())
        {
            try
            {
                $idCliente = $token['id_cliente'];
                $nickname = strtolower($nickname);
                // Get DB Object
                $db = $this->db;

                $sql_nro = "SELECT COUNT(*) AS cantidad FROM  ".DB_CON.".fanatico_clientes WHERE tel_con_cliente = '$telefono' AND ruc_cliente != '$ci_usu' ";
                $stmt = $db->query($sql_nro);
                $datoTelefonoUsuario = $stmt->fetch(PDO::FETCH_OBJ);
                $cantidad_nro = $datoTelefonoUsuario->cantidad;

                if($cantidad_nro >= 1){
                    return $response->withJson(
                        array("status" => "fail", "message" => "El Nro de Telefono ya existe.", "data"=> [])
                    );
                }

                if(strlen($telefono)  != 10 ){
                    return $response->withJson(
                        array("status" => "fail", "message" => "El Nro de Telefono es invalido. \n Ejemplo: 09XXXXXXXX.", "data"=> [])
                    );
                }

                $sqlValidacionNickname = "SELECT id_cliente FROM ".DB_CON.".fanatico_clientes WHERE nickname = '$nickname' AND id_cliente != $idCliente";
                $stmt = $db->query($sqlValidacionNickname);
                $datosNickname = $stmt->fetch(PDO::FETCH_OBJ);
                $count = $stmt->rowCount();
                if($count > 0)
                {
                    return $response->withJson(
                        array("status" => "fail", "message" => "Nickname existente", "data"=> [])
                    );
                }


                $sql = "SELECT estado_cliente FROM ".DB_CON.".fanatico_clientes WHERE id_cliente =".$idCliente;

                $stmt = $db->query($sql);
                $datos = $stmt->fetch(PDO::FETCH_OBJ);
                $estado_cliente = $datos->estado_cliente;
                if ($estado_cliente == 1 || $estado_cliente == 3 || $estado_cliente == 4)
                {
                    $sql = "UPDATE ".DB_CON.".fanatico_clientes SET
                            sexo_cliente = :sexo_cliente,
                            email_con_cliente = :email_con_cliente,
                            direccion_cliente = :direccion_cliente,
                            nickname = :nickname
                            WHERE ruc_cliente = :ci_usu ";

                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':sexo_cliente', $sexo);
                    $stmt->bindParam(':ci_usu', $ci_usu);
                }
                else
                {
                    $sql = "UPDATE ".DB_CON.".fanatico_clientes SET
                    razon_cliente = :razon_cliente,
                    nombre_con_cliente = :nombre_con_cliente,
                    apellido_con_cliente = :apellido_con_cliente,
                    sexo_cliente = :sexo_cliente,
                    fecha_nac_cliente = :fecha_nac_cliente,
                    email_con_cliente = :email_con_cliente,
                    direccion_cliente = :direccion_cliente,
                    nickname = :nickname,
                    codigo_pais = :codigo_pais
                    WHERE ruc_cliente = :ci_usu ";

                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':razon_cliente', $razon_cliente);
                    $stmt->bindParam(':codigo_pais', $codigoPais);
                    $stmt->bindParam(':nombre_con_cliente', $nombre);
                    $stmt->bindParam(':apellido_con_cliente', $apellido);
                    $stmt->bindParam(':sexo_cliente', $sexo);
                    $stmt->bindParam(':fecha_nac_cliente', $fec_nac_usu);
                    $stmt->bindParam(':direccion_cliente', $direccion_cliente);
                    $stmt->bindParam(':ci_usu', $ci_usu);
                }
                $stmt->bindParam(':nickname', $nickname);
                // $stmt->bindParam(':tel_con_cliente', $telefono);
                $stmt->bindParam(':email_con_cliente', $email);
                $stmt->execute();

                $db = null;

                $settings = $this->get('settings'); // get settings array.
                $option=[
                            'aud' =>  Aud(),
                            'id_cliente' => $idCliente,
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'ruc_cliente' => $ci_usu
                        ];
                $token = JWT::encode($option, $settings['jwt']['secret'], "HS256");

                return $response->withJson(
                    array(
                        "status" => "success", 
                        "message" => "Datos actualizados",  
                        'data'=> array(
                            'id_cliente' => $idCliente,
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'sexo' => $sexo,
                            'telefono' => $telefono,
                            'ruc_cliente' => $ci_usu,
                            'email' => $email,
                            'direccion' => $direccion_cliente,
                            'fecha_nac'    =>  $fec_nac_usu,
                            'nickname' => $nickname,
                            'token' => $token
                        )
                    )
                );

            }
            catch(PDOException $e)
            {
                throw $e;
                // return $response->withJson(array("response"=>"0", 'error' => $e,  "data" => array('msg' => 'Error Interno')));
            }
        }else{
            return $response->withJson(
                array("status" => "fail", "message" => "Acceso Denegado",  "data" => array() )
            );
        }
    });

//RECUPERAR CONTRASENA
    $app->post('/cliente/user/st/recuperar', function(Request $request, Response $response){
        try
        {
            $db = $this->db;
            $paramters = $request->getParsedBody();
            $email = $paramters['email'];
            $destinatario[] = $email;
            $sql = "SELECT *
                    FROM ".DB_CON.".fanatico_clientes
                    WHERE email_con_cliente = '$email' ";
            $stmt = $db->query($sql);
            $datos = $stmt->fetch(PDO::FETCH_OBJ);
            if (!$datos) {
                return json_encode( array( 
                                            'status' => 'fail',
                                            'message' => 'El email que ha proporcionado no existe',
                                            'data' => []
                                        ) );
            }

            $cont = rand(1000,9999);
            $pass_usu = password_hash($cont, PASSWORD_BCRYPT);

            $sql = "UPDATE ".DB_CON.".fanatico_clientes SET pass_cliente='".$pass_usu."' where email_con_cliente='".$email."'";
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $title = '📌 Recuperar contraseña';
            $from = ['emprendimientosvirtuales365@gmail.com' => 'Soporte'];
            $to = $destinatario;
            $msj = "
                Tu nueva contraseña $cont
            ";
            $body = ServidorEmail::FormatoMensaje($msj);
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);

            return json_encode( array( 
                                        'status' => 'success' , 
                                        'message' => 'operacion_exitosa',
                                        'data' => ['e' => $email]
                                    ) );
        }
        catch(PDOException $e)
        {
            return json_encode( array( 
                                        'code' => 505, 
                                        'status' => false, 
                                        'message' => '',
                                        'data' => $e 
                                    ) );
        }
    });

//Cambiar Contrasena
    $app->post('/cliente/user/cambiarContrasena', function(Request $request, Response $response){
        $password_viejo = $request->getParam('old_password');
        $password_nuevo = $request->getParam('new_password');
        $pass_usu = password_hash($password_nuevo, PASSWORD_BCRYPT);
        
        //TOKEN
        $token = @$request->getAttribute("token");
        if (@$token['aud'] === Aud())
        {
            try
            {
                $id_cliente = $token['id_cliente'];
                // Get DB Object
                $db = $this->db;
                $sql_usuario = "SELECT * FROM  ".DB_CON.".fanatico_clientes WHERE id_cliente = '$id_cliente'";
                $stmt = $db->query($sql_usuario);
                $datos = $stmt->fetch(PDO::FETCH_OBJ);

                if(password_verify($password_viejo, @$datos->pass_cliente) ){
                    $sql = "UPDATE ".DB_CON.".fanatico_clientes SET pass_cliente = '$pass_usu' WHERE id_cliente = '$id_cliente'";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    return $response->withJson(
                        array("status"=> "success", "message" => "Contraseña actualizada",  "data" => array())
                    );
                }
                else
                {
                    return $response->withJson(
                        array("status"=> "fail", "message" => "Error de contraseña",  "data" => array())
                    );
                }
                $db = null;

            }
            catch(PDOException $e)
            {
                throw $e;
                // return $response->withJson( array("response"=>"0", 'error' => $e,  "data" => array('msg' => 'Error Interno')));
            }
        }else{

            return $response->withJson(
                array("status"=> "success", "message" => "Acceso Denegado",  "data" => array())
            );
        }
    });