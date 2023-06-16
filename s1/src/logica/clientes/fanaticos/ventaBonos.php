<?php
    use Slim\Http\Request;
    use Slim\Http\Response;
    use Api\Pasarela\Personal;
    use Api\Pasarela\Tigo;
    use Api\Pasarela\TigoMoney;
    use Api\Pasarela\PayPal;

    $app->get('/clubcomodin/ventaBonos/bonos', function(Request $request, Response $response){
        try
        {
            // Get DB Object
            $db = $this->db;
            $fecha = date('Y-m-d H:i:s');
            $query = "UPDATE ".DB_MBO.".venta_bonos SET estado_venta_bono = 2 WHERE estado_venta_bono != 3 AND fin_venta_bono <= '$fecha' ";
            $stmt = $db->query($query);

            $queryPromo = "
                SELECT img_venta_bono,id_venta_bono,id_bono,limite_inferior_tipo,limite_superior_tipo,simbolo_moneda FROM ".DB_MBO.".venta_bonos a  
                    join ".DB_BONO.".tipos t on t.id_tipo=a.id_bono 
                    join ".DB_MBO.".juegos j on j.id_juego= t.id_juego
                    join ".DB_CON.".monedas m on m.id_moneda= j.id_moneda
                WHERE a.estado_venta_bono = 0 AND a.inicio_venta_bono <= '$fecha' AND a.fin_venta_bono >= '$fecha'
            ";
            $stmt = $db->query($queryPromo);
            $promo = $stmt->fetchAll(PDO::FETCH_OBJ);

            $queryJuego = "SELECT j.id_juego,simbolo_moneda FROM ".DB_MBO.".juegos j join ".DB_CON.".monedas m on m.id_moneda = j.id_moneda";
            $stmt = $db->query($queryJuego);
            $juego = $stmt->fetchAll(PDO::FETCH_OBJ);

            $db = NULL;

            return $response->withJson(
                            array(
                                    'status' => 'success', 
                                    'message' => '', 
                                    'data'=> [
                                        "juegos" => $juego,
                                        "ventas" => $promo
                                    ]
                                )
            ); 
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    $app->get('/clubcomodin/ventaBonos/detalle/{id}', function(Request $request, Response $response){
        try
        {
            $id = $request->getAttribute('id');    

            // Get DB Object
            $db = $this->db;
            $fecha = date('Y-m-d H:i:s');

            $jsonBonos = [];
            $monto = 0;
            $id_juego = "";
            $id_moneda = "";
            if( isset( $id ) ){
                $sql = "
                    SELECT 
                        * 
                    FROM ".DB_MBO.".venta_bonos a 
                        join ".DB_BONO.".tipos t on t.id_tipo=a.id_bono
                        join ".DB_MBO.".juegos j on j.id_juego=a.id_juego
                        join ".DB_CON.".monedas m on m.id_moneda=j.id_moneda
                    WHERE  
                        a.estado_venta_bono = 0
                        AND a.inicio_venta_bono <= '$fecha'
                        AND a.fin_venta_bono >= '$fecha'
                        AND a.id_venta_bono = '$id'
                ";
                $stmt = $db->query($sql);
                $promo = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($promo) {
                    foreach ($promo as $key => $value) {
                        $id_juego = $value['id_juego'];
                        $id_moneda = $value['simbolo_moneda'];
                        $monto = $value['limite_inferior_tipo'];
                        $jsonBonos = [
                            "id" => $value['id_tipo'], 
                            "nombre" => $value['nombre_tipo'], 
                            "valor" => $value['valor_tipo'], 
                            "id_juego" => $value['id_juego'],
                            "tipo" => $value['tipo_valor_tipo'],
                            "limite_inferior_tipo" => $value['limite_inferior_tipo'],
                            "limite_superior_tipo" => $value['limite_superior_tipo'],
                            "valor_juego" => $value['valor_credito']
                        ];
                    }
                }
            }

            $sql = "
                SELECT * 
                FROM ".DB_MBO.".venta_bonos_operadora 
                WHERE id_venta_bono = '$id'
            ";
            $stmt = $db->query($sql);
            $operadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $arrayOperadora = [];
            foreach ($operadoras as $key => $value) {
                switch ($value['id_operadora']) {
                    case '1':
                        $arrayOperadora[] = 'Tigo';
                        break;
                    case '2':
                        $arrayOperadora[] = 'Personal';
                        break;
                    case '3':
                        $arrayOperadora[] = 'Paypal';
                        break;
                }
                
            }
            $db = NULL;

            if(count($jsonBonos)){
                $data = [
                    "jsonBono"      => $jsonBonos,
                    "moneda"        => $id_moneda,
                    "monto"         => $monto,
                    "id_venta_bono" => $id,
                    "operadoras"    => $arrayOperadora
                ];
            }else{
                 return $response->withJson(
                            array(
                                    'status' => 'fail', 
                                    'message' => 'No existe esta promo', 
                                    'data'=> [] 
                                )
                ); 

            }
        

            return $response->withJson(
                            array(
                                    'status' => 'success', 
                                    'message' => '', 
                                    'data'=>  $data 
                                )
            ); 
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    $app->post('/clubcomodin/ventaBonos/procesarCompra', function(Request $request, Response $response){
        try
        {
            // Get DB Object
            $db = $this->db;
            $fecha = date('Y-m-d H:i:s');
            $post = $request->getParams();
            $settings = $this->get('settings');

            $token = @$request->getAttribute("token");
            if (@$token['aud'] === Aud())
            {
                if( isset( $post['metodoPago'] ) && $post['metodoPago'] != "" &&
                    isset( $post['valorMonto'] ) && $post['valorMonto'] > 0 &&
                    isset( $post['idCliente'] ) && $post['idCliente']  != "" &&
                    isset( $post['idVentaBono'] ) && $post['idVentaBono'] != ""
                )
                {
                    $idVentaBono = $post['idVentaBono'];
                    $idCliente = $token['id_cliente'];
                    $sql = "
                        SELECT * FROM ".DB_CON.".clientes WHERE id_cliente = $idCliente
                    ";
                    $stmt = $db->query($sql);
                    $cliente = $stmt->fetch(PDO::FETCH_OBJ);
                    if(!$cliente){
                        return $response->withJson(
                            array("status" => "fail", "message" => "Error 1",  "data" => array() )
                        );
                    }
                    $telefono = $cliente->tel_con_cliente;
                    $monto = $post['valorMonto'];
                    $monto = $post['valorMonto'];
                    $idJuego = $post['idJuego'];
                    $empresa = strtolower($post['metodoPago']);

                    switch ($post['metodoPago']) {
                        case 'personal':
                            $ref_trans = insertTransaction($db,'Personal');
                            $fecha = date('Y-m-d H:i:s');
                            $d = [];
                            $d['id_venta_bono'] = $idVentaBono;
                            $d['id_cliente'] = $idCliente;
                            $d['tel_trans'] = $telefono;
                            $d['monto_trans'] = $monto;
                            $d['ref_trans'] = $ref_trans;
                            $d['id_juego'] = $idJuego;
                            $d['empresa_trans'] = $empresa;
                            $d['fecha_ini_trans'] = $fecha;
                            $d['tipo_trans'] = 0;
                            $d['estado_trans'] = 0;
                            $d['id_creador'] = 0;
                            $d['fecha_creador'] = $fecha;
                            $d['nombremodi_trans'] = 'Web Form';
                            $d['fechamodi_trans'] = $fecha;
                            $d['sucursal'] = CONF_COMODIN['ID_SUCURSAL_MADRE'];
                        
                            $ref = insertPayment($db,$d);
            
                            if(!$ref){
                                return $response->withJson(
                                    array("status" => "fail", "message" => "Error 2",  "data" => array() )
                                );
                                break;
                            }

                            // $api = (new Personal($settings['personal']))->templateView($ref_trans, $telefono , $monto);
                            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                            $url = $actual_link."/personal/?ref=$ref&monto=$monto&telefono=$telefono";
                            $data = [
                                'url' => $url,
                                'reference' => $ref,
                                'company' => 'personal'
                            ];
                            // print_r( $db->getToken()->getContent() );
                            // print_r($db->sendPago('1234','595981975747','100'));
            
                            // print_r( $db->status('46456465') );
                            break;
                        case 'tigo':
            
                            $ref_trans = insertTransaction($db,'Tigo');
                            $fecha = date('Y-m-d H:i:s');
                            $data['id_venta_bono'] = $idVentaBono;
                            $data['id_cliente'] = $idCliente;
                            $data['tel_trans'] = $telefono;
                            $data['monto_trans'] = $monto;
                            $data['ref_trans'] = $ref_trans;
                            $data['id_juego'] = $idJuego;
                            $data['empresa_trans'] = $empresa;
                            $data['fecha_ini_trans'] = $fecha;
                            $data['tipo_trans'] = 0;
                            $data['estado_trans'] = 0;
                            $data['id_creador'] = 0;
                            $data['fecha_creador'] = $fecha;
                            $data['nombremodi_trans'] = 'Web Form';
                            $data['fechamodi_trans'] = $fecha;
                            $data['sucursal'] = CONF_COMODIN['ID_SUCURSAL_MADRE'];
                        
                            $ref = insertPayment($db,$data);
            
                            if(!$ref){
                                return $response->withJson(
                                    array("status" => "fail", "message" => "Error",  "data" => array() )
                                );
                                break;
                            }
                            // $api = (new Tigo($setting['tigo']))->sendPayments('300', '0952110190' , '100');
                            $api = (new Tigo($settings['tigo']))->sendPayments($ref_trans, $telefono , $monto);
            
                            $data = [
                                'url' => $api,
                                'company' => 'tigo',
                                'reference' => $ref
                            ];
                            break;
                            case 'paypal':
                                // try {
                                //     $monto =  (float) str_replace(',', '.', $_POST['total_amt']);
                                //     $paypalHelper = new PayPal($settings['paypal']);
                                //     $randNo= (string)rand(10000,20000);
                                //     $orderData = '{
                                //         "intent" : "CAPTURE",
                                //         "purchase_units" : [ 
                                //             {
                                //                 "invoice_id" : "'.$randNo.'",
                                //                 "amount" : {
                                //                     "currency_code" : "USD",
                                //                     "value" : "'.$monto.'"
                                //                 }
                                            
                                //             }
                                //         ]
                                //     }';
                                    
                                //     $api = $paypalHelper->orderCreate($orderData);
                                //     $ref_trans = $api['data']['id'];
                                    
                                //     $fecha = date('Y-m-d H:i:s');
                                //     $data['id_venta_bono'] = $_POST['id_venta_bono'];
                                //     $data['id_cliente'] = $_SESSION["uid"];
                                //     $data['tel_trans'] = $_SESSION["telefono"];
                                //     $data['monto_trans'] = $monto;
                                //     $data['ref_trans'] = $ref_trans;
                                //     $data['id_juego'] = $_POST["juego"];
                                //     $data['empresa_trans'] = $_POST['empresa'];
                                //     $data['fecha_ini_trans'] = $fecha;
                                //     $data['tipo_trans'] = 0;
                                //     $data['estado_trans'] = 0;
                                
                                //     $data['id_creador'] = 0;
                                //     $data['fecha_creador'] = $fecha;
                                //     $data['nombremodi_trans'] = 'Web Form';
                                //     $data['fechamodi_trans'] = $fecha;
                                //     $data['sucursal'] = $_SESSION['CONF_COMODIN']['ID_SUCURSAL_MADRE_USD'];
                                
                                //     $ref = insertPayment($data,"usd_");
                    
                                //     header('Content-Type: application/json');
                                //     echo json_encode($api);
                                //     exit();
                                // } catch (\Throwable $th) {
                                //     echo json_encode(['error' =>  $th]);
                                //     exit();
                                // }                
                                break;            
                        default:
                            return $response->withJson(
                                array("status" => "fail", "message" => "Error de Parametro",  "data" => array() )
                            );
                            break;
                    }
                }else
                {
                    return $response->withJson(
                        array("status" => "fail", "message" => "Error de Parametros",  "data" => array() )
                    );
                }
            }else{
                return $response->withJson(
                    array("status" => "fail", "message" => "Acceso Denegado",  "data" => array() )
                );
            }

            $db = NULL;

            return $response->withJson(
                            array(
                                    'status' => 'success', 
                                    'message' => '', 
                                    'data'=>  $data 
                                )
            ); 
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    $app->get('/clubcomodin/ventaBonos/procesarCompra/personal/', function(Request $request, Response $response , array $args){
        try
        {
            // Get DB Object
            $db = $this->db;
            $fecha = date('Y-m-d H:i:s');
            $fechaValidacion = date('Y-m-d H:i:s', strtotime ( '-1 minute' , strtotime ($fecha) ) );
            $post = $request->getParams();
            $id = $post['ref'] ?? "";
            $monto = $post['monto'] ?? "";
            $telefono = $post['telefono'] ?? "";
            if( 
                $id == "" ||
                $monto == "" ||
                $telefono == "" 
            ){
                // echo "hsdofjasdkl;fjasdl;kfjasdkl;fjasdkl;fjasdl;fjasdl;fjk";
                // return "hola";
                return $this->rendererV2->render($response, 'personalError.twig', [
                    "messagge" => "Operación Cancelada!"
                ]);
            }
            $sql = "
                SELECT * FROM ".DB_MBO.".transacciones_bonos WHERE id_trans = '$id' and fecha_ini_trans > '$fechaValidacion'
            ";
            $stmt = $db->query($sql);
            $transaccion = $stmt->fetch(PDO::FETCH_OBJ);
            if( isset($_GET['cancel'])){

                // cancelar la transaccion 
                $sql = " UPDATE ".DB_MBO.".transacciones_bonos SET response='cancelado', estado_trans = 2,fecha_fin_trans='$fecha' WHERE empresa_trans='personal' and id_trans = '$id' and estado_trans = 0 ";
                $db->query($sql);
                $stmt = $db->query($sql);

                return $this->rendererV2->render($response, 'personalError.twig', [
                  "messagge" => "Operación Cancelada!"
                ]);
            }elseif(!$transaccion){

                // cancelar la transaccion 
                $sql = " UPDATE ".DB_MBO.".transacciones_bonos SET response='cancelado', estado_trans = 2,fecha_fin_trans='$fecha' WHERE empresa_trans='personal' and id_trans = '$id' and estado_trans = 0 ";
                $db->query($sql);
                $stmt = $db->query($sql);

                return $this->rendererV2->render($response, 'personalError.twig', [
                    "messagge" => "El token ha caducado."
                  ]);

            }else{
                // 
                // $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                // $url = "./pagar/";
    
                return $this->rendererV2->render($response, 'personalMain.twig', [
                    'id' => $id,
                    'monto' => $_GET['monto'],
                    'empresa' => 'Club Comodin',
                    'telefono' => $_GET['telefono'],
                    // 'url'   => $url
                ]);

            }
            $db = NULL;
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    $app->post('/clubcomodin/ventaBonos/procesarCompra/personal/pagar/', function(Request $request, Response $response){
        try
        {

            // Get DB Object
            $db = $this->db;
            $fecha = date('Y-m-d H:i:s');
            $post = $request->getParams();
            $ref = $_POST['referencia'];
            $sql = "SELECT * FROM ".DB_MBO.".transacciones_bonos WHERE estado_trans = 0 AND id_trans = '$ref' ";
            $stmt = $db->query($sql);
            $transaccion = $stmt->fetch(PDO::FETCH_OBJ);
            $settings = $this->get('settings');

            if ($transaccion) {
                $monto = $transaccion->monto_trans;
                $referencia = $transaccion->ref_trans;
                $api = (new Personal($settings['personal']))->sendPayments($referencia, $_POST["telefono"] , $monto);
                // $api = (new Personal($setting['personal']))->sendPayments($ref, $_POST["telefono"] , 100);
                $json = [
                    'status' => true,
                    'company' => 'personal',
                    'reference' => $ref ,
                    'data' => $api
                ];
            }else{
                $json = [
                    'status' => false,
                    'company' => 'personal',
                    'reference' => $ref ,
                    'data' => [
                            "codigoTransaccion" => "-101",
                            "comprobante" => "0",
                            "mensajeTransaccion" => "EL PEDIDO DE TRANSACCION YA FUE REALIZADA"
                        ]
                ];
            }

            return $response->withJson(
                $json
            ); 
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    $app->get('/clubcomodin/ventaBonos/procesarCompra/personal/status', function(Request $request, Response $response){
        try
        {
            // Get DB Object
            $db = $this->db;
            $fecha = date('Y-m-d H:i:s');
            $post = $request->getParams();

            $descripcion = $post['descripcion'];
            $id_trans = $post['referencia'];
            $empresa = 'personal';
            $sql = "SELECT * FROM ".DB_MBO.".transacciones_bonos WHERE estado_trans = 0 AND id_trans = '$id_trans' ";
            $stmt = $db->query($sql);
            $transaccion = $stmt->fetch(PDO::FETCH_OBJ);

            if ($transaccion) {
                if(  isset($post['codigoTransaccion']) && $post['codigoTransaccion'] == '0'  ){
                    
                    $sql = " UPDATE ".DB_MBO.".transacciones_bonos SET response='$descripcion', estado_trans = 1,fecha_fin_trans='$fecha' WHERE empresa_trans='$empresa' and id_trans = '$id_trans' and estado_trans = 0 ";
                    $db->query($sql);
                    $stmt = $db->query($sql);
                    if($stmt){
                        $prefijo="";
                        $sufijo="";
                        $idCreador = 0;
                        $nombreCreador = "WEB FORM";
                        $sucursal = $transaccion->sucursal;
                        $idCliente = $transaccion->id_cliente;
                        $monto = $transaccion->monto_trans;
                        $id_venta_bono = $transaccion->id_venta_bono;
                        $juego = $transaccion->id_juego;
        
                        transactionCompra($db,$id_trans,$empresa,$monto,$sucursal,$idCliente,$idCreador,$nombreCreador,$juego, $prefijo,$sufijo);
                        addBonosCompra($db,$idCliente,$monto,$juego,$id_trans,$sucursal,$id_venta_bono,$prefijo);
                    }

                    return $this->rendererV2->render($response, 'personalSuccess.twig', [
                        "messagge" => "Operación exitosa!"
                    ]);

                }else{
                    // cancelar la transaccio   
                    $sql = " UPDATE ".DB_MBO.".transacciones_bonos SET response='$descripcion', estado_trans = 2,fecha_fin_trans='$fecha' WHERE id_trans = '$id_trans' and estado_trans = 0 ";
                    $db->query($sql);

                    return $this->rendererV2->render($response, 'personalError.twig', [
                        "messagge" => $post['descripcion']
                    ]);
                }
            }else{
                return $this->rendererV2->render($response, 'personalError.twig', [
                    "messagge" => "La Operación ya fue realizada."
                ]);
            }

        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    $app->get('/clubcomodin/ventaBonos/procesarCompra/tigo/status', function(Request $request, Response $response){
        try
        {
            // Get DB Object
            $db = $this->db;
            $fecha = date('Y-m-d H:i:s');
            $post = $request->getParams();

            $descripcion = $post['descripcion'];
            $id_trans = $post['referencia'];
            $empresa = 'tigo';
            $sql = "SELECT * FROM ".DB_MBO.".transacciones_bonos WHERE estado_trans = 0 AND id_trans = '$id_trans' ";
            $stmt = $db->query($sql);
            $transaccion = $stmt->fetch(PDO::FETCH_OBJ);

            if ($transaccion) {
                if (isset($_GET['transactionStatus']) && $_GET['transactionStatus'] == 'success' ) {                    
                    $sql = " UPDATE ".DB_MBO.".transacciones_bonos SET response='$descripcion', estado_trans = 1,fecha_fin_trans='$fecha' WHERE empresa_trans='$empresa' and id_trans = '$id_trans' and estado_trans = 0 ";
                    $db->query($sql);
                    $stmt = $db->query($sql);
                    if($stmt){
                        $prefijo="";
                        $sufijo="";
                        $idCreador = 0;
                        $nombreCreador = "WEB FORM";
                        $sucursal = $transaccion->sucursal;
                        $idCliente = $transaccion->id_cliente;
                        $monto = $transaccion->monto_trans;
                        $id_venta_bono = $transaccion->id_venta_bono;
                        $juego = $transaccion->id_juego;
        
                        transactionCompra($db,$id_trans,$empresa,$monto,$sucursal,$idCliente,$idCreador,$nombreCreador,$juego, $prefijo,$sufijo);
                        addBonosCompra($db,$idCliente,$monto,$juego,$id_trans,$sucursal,$id_venta_bono,$prefijo);
                    }

                    return $this->rendererV2->render($response, 'personalSuccess.twig', [
                        "messagge" => "Operación exitosa!"
                    ]);

                }else{
                    // cancelar la transaccio   
                    $sql = " UPDATE ".DB_MBO.".transacciones_bonos SET response='$descripcion', estado_trans = 2,fecha_fin_trans='$fecha' WHERE id_trans = '$id_trans' and estado_trans = 0 ";
                    $db->query($sql);

                    return $this->rendererV2->render($response, 'personalError.twig', [
                        "messagge" => $post['descripcion']
                    ]);
                }
            }else{
                return $this->rendererV2->render($response, 'personalError.twig', [
                    "messagge" => "La Operación ya fue realizada."
                ]);
            }

        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    $app->post('/clubcomodin/ventaBonos/procesarCompra/tigo/verificarTransaccion', function(Request $request, Response $response){
        try
        {
            // Get DB Object
            $db = $this->db;
            $fecha = date('Y-m-d H:i:s');
            $post = $request->getParams();
            $settings = $this->get('settings');

            $id_trans = $post['id'];
            $empresa = 'tigo';
            $sql = "SELECT * FROM ".DB_MBO.".transacciones_bonos WHERE estado_trans = 0 AND id_trans = '$id_trans' ";
            $stmt = $db->query($sql);
            $transaccion = $stmt->fetch(PDO::FETCH_OBJ);

            if ($transaccion) {
                $ref = $transaccion->ref_trans;
                $api = (new Tigo($settings['tigo']))->status( $ref );
                $res = json_encode($api);
                if( isset( $api['Transaction']['status'] ) && $api['Transaction']['status'] == 'success' ) {                  
                    $sql = " UPDATE ".DB_MBO.".transacciones_bonos SET response='$res', estado_trans = 1,fecha_fin_trans='$fecha' WHERE empresa_trans='$empresa' and id_trans = '$id_trans' and estado_trans = 0 ";
                    $db->query($sql);
                    $stmt = $db->query($sql);
                    if($stmt){
                        $prefijo="";
                        $sufijo="";
                        $idCreador = 0;
                        $nombreCreador = "WEB FORM";
                        $sucursal = $transaccion->sucursal;
                        $idCliente = $transaccion->id_cliente;
                        $monto = $transaccion->monto_trans;
                        $id_venta_bono = $transaccion->id_venta_bono;
                        $juego = $transaccion->id_juego;
                        transactionCompra($db,$id_trans,$empresa,$monto,$sucursal,$idCliente,$idCreador,$nombreCreador,$juego, $prefijo,$sufijo);
                        addBonosCompra($db,$idCliente,$monto,$juego,$id_trans,$sucursal,$id_venta_bono,$prefijo);
                    }

                    // return $this->rendererV2->render($response, 'personalSuccess.twig', [
                    //     "messagge" => "Operación exitosa!"
                    // ]);

                }else{
                    // cancelar la transaccio   
                    $sql = " UPDATE ".DB_MBO.".transacciones_bonos SET response='$res', estado_trans = 2,fecha_fin_trans='$fecha' WHERE id_trans = '$id_trans' and estado_trans = 0 ";
                    $db->query($sql);

                    // return $this->rendererV2->render($response, 'personalError.twig', [
                    //     "messagge" => $api['Transaction']
                    // ]);
                }
            }else{
                // return $this->rendererV2->render($response, 'personalError.twig', [
                //     "messagge" => "La Operación ya fue realizada."
                // ]);
            }

            return $response->withJson(
                array('status' => 'success', 'message' => '', 'data' => array())
            );
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

?>