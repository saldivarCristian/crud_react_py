<?php

    class Event 
    {
        private static $events = [];
    
        public static function listen($name, $callback) {
            self::$events[$name][] = $callback;
        }
    
        public static function trigger($name, $argument = null) {
            foreach (self::$events[$name] as $event => $callback) {
                if($argument && is_array($argument)) {
                    call_user_func_array($callback, $argument);
                }
                elseif ($argument && !is_array($argument)) {
                    call_user_func($callback, $argument);
                }
                else {
                    call_user_func($callback);
                }
            }
        }

        public static function configDefaultEvents()
        {
            // Event::listen('login', function(){
            //     echo 'Event user login fired! <br>';
            // });

            // Event::listen('logout', function($param){
            //     echo 'Event '. $param .' logout fired! <br>';
            // });

            // Event::listen('loginUser', function(){
               
            // });


            
        }
    }

    class SendMail
    {
        public $_mailer;
        public function __construct()
        {
            $opt['ssl']['verify_peer'] = FALSE;
            $opt['ssl']['verify_peer_name'] = FALSE;
            $transport = (new Swift_SmtpTransport('smtp.gmail.com', 587,'TLS'))
	        ->setUsername('emprendimientosvirtuales365@gmail.com')
            ->setPassword('emprendimientos.2021')
            ->setStreamOptions($opt);
			;
			// Create the Mailer using your created Transport
            $this->_mailer = new Swift_Mailer($transport);
            
            // $transport = (new Swift_SmtpTransport('mail.proinso.sa.com', 465,'SSL'))
            // ->setUsername('developer@proinso.sa.com')
            // ->setPassword('Tincho2020')
            // ;
            // // Create the Mailer using your created Transport
            // $this->_mailer = new Swift_Mailer($transport);
        }

        public function Send(string $title, array $from, array $to, string $body )
        {
            $message = (new Swift_Message($title))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body, 'text/html')
            ;
            // Send the message
            return $this->_mailer->send($message);
        }
    }

    class ServidorEmail
    {
        public static function MaqBloqueada($fecha, $codMaquina, $rucCliente, $sectorError)
        {
            $cuerpo =
            '<h1 style="font-family:Arial;font-size:36px;line-height:44px;padding-top:10px;padding-bottom:10px">Informacion</h1>
            <p><b>Categoria:</b> Bloqueo de puesto por validacion de datos.</p><br>
            <p><b>Fecha de Registro:</b> '.$fecha.'.</p><br>
            <p><b>Codigo Maquina:</b> '.$codMaquina.'.</p><br>
            <p><b>Cedula Identidad Usuario:</b> '.$rucCliente.'.</p><br>
            <p><b>Modulo Error:</b> '.$sectorError.'.</p><br>
            <p><b>Internal ID Error:</b> '.$sectorError.'.</p><br>';

            $mensaje = self::FormatoMensaje($cuerpo);

            $title = '📌 Puestos Comodin';
            $from = ['developer@proinso.sa.com' => 'Sistema'];
            $to = REPORTES_CORREOS;
            $body = $mensaje;
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);
        }

        public static function UsuarioNuevo($fecha, $rucCliente)
        {
            $cuerpo =
            '<h1 style="font-family:Arial;font-size:36px;line-height:44px;padding-top:10px;padding-bottom:10px">Informacion</h1>
            <p><b>Categoria: </b>Registro Usuario.</p><br>
            <p><b>Fecha de Registro: </b>'.$fecha.'.</p><br>
            <p><b>Cedula Identidad Usuario: </b>'.$rucCliente.'.</p><br>';

            $mensaje = self::FormatoMensaje($cuerpo);
            $title = '📌 Interesados Comodin';
            $from = ['developer@proinso.sa.com' => 'Sistema'];
            $to = REPORTES_CORREOS;
            $body = $mensaje;
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);

        }

        public static function ValidacionFotoUsuario($fecha, $rucCliente)
        {
            $cuerpo =
            '<h1 style="font-family:Arial;font-size:36px;line-height:44px;padding-top:10px;padding-bottom:10px">Informacion</h1>
            <p><b>Categoria: </b> Validacion Identidad Del Usuario.</p><br>
            <p><b>Fecha de Registro: </b>'.$fecha.'.</p><br>
            <p><b>Cedula Identidad Usuario: </b>'.$rucCliente.'.</p><br>';

            $mensaje = self::FormatoMensaje($cuerpo);

            $title = '📌 Interesados Comodin';
            $from = ['developer@proinso.sa.com' => 'Sistema'];
            $to = REPORTES_CORREOS;
            $body = $mensaje;
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);

        }

        public static function AutoExclusionUsuario($fecha, $rucCliente, $fechaFinalAutoExclusion)
        {
            $cuerpo =
            '<h1 style="font-family:Arial;font-size:36px;line-height:44px;padding-top:10px;padding-bottom:10px">Informacion</h1>
            <p><b>Categoria: </b>Usuario AutoExcluido.</p><br>
            <p><b>Fecha de Registro: </b>'.$fecha.'.</p><br>
            <p><b>Cedula Identidad Usuario: </b>'.$rucCliente.'.</p><br>
            <p><b>Fecha fin AutoExclusion: </b>'.$fechaFinalAutoExclusion.'.</p><br>';

            $mensaje = self::FormatoMensaje($cuerpo);

            $title = '📌 Interesados Comodin';
            $from = ['developer@proinso.sa.com' => 'Sistema'];
            $to = REPORTES_CORREOS;
            $body = $mensaje;
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);
        }

        public static function Soporte( $fecha, $rucCliente, $tema)
        {
            $cuerpo =
            '<h1 style="font-family:Arial;font-size:36px;line-height:44px;padding-top:10px;padding-bottom:10px">Informacion</h1>
            <p><b>Categoria: </b>Soporte.</p><br>
            <p><b>Fecha de Registro: </b> '.$fecha.'.</p><br>
            <p><b>Cedula Identidad Usuario: </b> '.$rucCliente.'.</p><br>
            <p><b>Tema: </b> '.$tema.'.</p><br>';

            $mensaje = self::FormatoMensaje($cuerpo);

            $title = '📌Soporte Comodin';
            $from = ['developer@proinso.sa.com' => 'Sistema'];
            $to = REPORTES_CORREOS;
            $body = $mensaje;
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);

        }

        public static function AlertaTopeBanca($fecha,$netwin)
        {
            $cuerpo =
            '<h1 style="font-family:Arial;font-size:36px;line-height:44px;padding-top:10px;padding-bottom:10px">Informacion</h1>
            <p><b>Categoria: </b>Tope De Banca.</p><br>
            <p><b>Fecha de Registro: </b> '.$fecha.'.</p><br>
            <p><b>Netwin: </b> '.$netwin.'.</p><br>
            Ha alcanzado el limite de tope de banca<br>
            ';

            $mensaje = self::FormatoMensaje($cuerpo);

            $title = '📌Tope Banca';
            $from = ['developer@proinso.sa.com' => 'Sistema'];
            $to = REPORTES_CORREOS;
            $body = $mensaje;
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);
        }

        public static function PeticionCargaExtraBilletera($fecha, $rucCliente, $tipoTransaccion, $empresa)
        {
            $cuerpo =
            '<h1 style="font-family:Arial;font-size:36px;line-height:44px;padding-top:10px;padding-bottom:10px">Informacion</h1>
            <p><b>Categoria: </b>Transaccion Billetera.</p><br>
            <p><b>Fecha de Registro: </b> '.$fecha.'.</p><br>
            <p><b>Cedula Identidad Usuario: </b> '.$rucCliente.'.</p><br>
            <p><b>Tipo Transaccion: </b> '.$tipoTransaccion.'.</p><br>
            <p><b>Empresa: </b> '.$empresa.'.</p><br>';

            $mensaje = self::FormatoMensaje($cuerpo);

            $title = '📌Transacciones Comodin';
            $from = ['developer@proinso.sa.com' => 'Sistema'];
            $to = REPORTES_CORREOS;
            $body = $mensaje;
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);
        }

        public static function AlertaPdv($fecha, $rucCliente, $codPdv, $alerta)
        {
            $cuerpo =
            '<h1 style="font-family:Arial;font-size:36px;line-height:44px;padding-top:10px;padding-bottom:10px">Informacion</h1>
            <p><b>Categoria:</b> Bloqueo de puesto por validacion de datos.</p><br>
            <p><b>Fecha de Registro:</b> '.$fecha.'.</p><br>
            <p><b>Codigo Pdv:</b> '.$codPdv.'.</p><br>
            <p><b>Ultimo cliente en Transaccion:</b> '.$rucCliente.'.</p><br>
            <p><b>Alerta Descripcion:</b> '.$alerta.'.</p><br>';

            $mensaje = self::FormatoMensaje($cuerpo);

            $title = '📌Puestos Comodin';
            $from = ['developer@proinso.sa.com' => 'Sistema'];
            $to = REPORTES_CORREOS;
            $body = $mensaje;
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);
        }

        public static function FormatoMensaje($cuerpo)
        {
            $formato = '<html>
                <style type="text/css">
                    @media all and (max-width: 599px) {
                        .container600 {
                            width: 100%;
                        }
                    }
                </style>
                </head>
                <body style="background-color:#F4F4F4;">


                <table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
                    <tr>
                    <td width="100%" style="min-width:100%;background-color:#F4F4F4;padding:10px;">
                        <center>
                        <table class="container600" cellpadding="0" cellspacing="0" width="600" style="margin:0 auto;">
                            <tr>
                            <td width="100%" style="text-align:left;">
                                <table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
                                <tr>
                                    <td width="100%" style="min-width:100%;background-color:#000000;color:#000000;padding:30px;">
                                    <center>
                                        <img alt="" src="https://local.quattropy.com/flexible.tools2/server_webv4/public/comodin.png" width="210" style="display: block;" />
                                    </center>
                                    </td>
                                </tr>
                                </table>
                                <table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
                                <tr>
                                    <td width="100%" style="min-width:100%;background-color:#F8F7F0;color:#58585A;padding:30px;">
                                    '.$cuerpo.'
                                    </td>
                                </tr>
                                </table>
                                <table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
                                <tr>
                                    <td width="100%" style="min-width:100%;background-color:#58585A;color:#FFFFFF;padding:30px;">
                                    <p style="font-size:16px;line-height:20px;font-family:Georgia,Arial,sans-serif;text-align:center;">2019 @ COPYRIGHT - PROINSO S.A</p>
                                    </td>
                                </tr>
                                </table>
                            </td>
                            </tr>
                        </table>
                        </center>
                    </td>
                    </tr>
                </table>
                </body>';

            return $formato;
        }
    }

	class Util
    {
       /**
		* Metodo para sentencias sql
		*
		* @param PDO $db 'Coneccion de base de datos'
		* @param string $query 'sentencia sql'
		* @param array $data 'datos por arreglo de condiciones'
		* @param string $type 'traer una fila o todas fecht/fecthAll solo funciona para los select'
		* @param string $fetchStyle 'tipo FETCH_OBJ... ect. solo funciona para los select'
		* @return array 'retorna una arreglo'
		*/

		public static function executeQuery(PDO $db, string $query, array $data = [], string $type = "",  string $fetchStyle = "FETCH_OBJ") : array
        {
			$resultado['data'] = null;
			$númargs = func_num_args();
            try {
				if($númargs != 2){
					$params = array();
					preg_match_all('/:\w+/', $query, $matches);

					if(count($matches[0]) != count($data)){
						throw new PDOException('Incomplete Parameters: '.json_encode($data).' / '.json_encode($matches[0])  );
					}

					foreach($matches[0] as $param) {
						$paramName = substr($param, 1);
						if(!isset($data[$paramName])){
							throw new PDOException('Undefined index: '.json_encode($data) );
						}
						$params[$param] = $data[$paramName] ??  null;
					}
				}

				//Pepare the SQL statement
                if(($stmt =  $db->prepare($query)) !== false) {
					if($númargs != 2){
						//Bind all parameters
						foreach($params as $param => $value) {
							$stmt->bindValue($param, $value);
						}
					}
                    //Execute the statement
					$stmt->execute();
					$type = explode(" " , trim($query) );
					switch ($type[0]) {
						case "insert":
						case "INSERT":
							$resultado['data'] = $db->lastInsertId();
							break;
						case "SELECT":
						case "select":
							$result = "";
                            $fetchStyle;
							switch ($fetchStyle) {
								case 'FETCH_ASSOC':
									if($type == "ALL"){
										$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
									}else{
										$result = $stmt->fetch(PDO::FETCH_ASSOC);
									}
									break;
								case 'FETCH_OBJ':
									if($type == "ALL"){
										$result = $stmt->fetchAll(PDO::FETCH_OBJ);
									}else{
										$result = $stmt->fetch(PDO::FETCH_OBJ);
									}
									break;

								default:
									if($type == "ALL"){
										$result = $stmt->fetchAll(PDO::FETCH_OBJ);
									}else{
										$result = $stmt->fetch(PDO::FETCH_OBJ);
									}
									break;
							}
							if(!$result){
								$result = [];
							}
                            // print_r($result);
							$resultado['data'] =  $result;
							// $resultado['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
							break;
						default:
							$resultado['data'] =  $stmt->rowCount();
							break;
					}
                }
            }
            catch(PDOException $e){
                $resultado['error'] = $e->getMessage();
			}
            return $resultado;
        }
                
        /**
         * historialGanadores
         *
         * @param  mixed $db
         * @param  mixed $ciCliente
         * @param  mixed $tipo
         * @param  mixed $fechaHora
         * @param  mixed $idJuego
         * @param  mixed $codMaquina
         * @param  mixed $msj 
         * @param  mixed $lastId 
         * @return void
         */
        public static function historialGanadores( PDO $db,$id_cliente,$tipo,$fechaHora,$idJuego,$codMaquina,$msj,$lastId )
        {
            self::executeQuery( $db , "INSERT ".DB_MBO.".historial_ganadores(
                id_cliente,
                tipo_hg,
                fecha_hg,
                id_juego,
                cod_maquina,
                descripcion,
                referencia
            )
            VALUES
                (
                '$id_cliente',
                '$tipo',
                '$fechaHora',
                '$idJuego',
                '$codMaquina',
                '$msj',
                '$lastId'
                );
            " );
        }

    }

    class Client
    {
        public function AddClient($db, $rucCliente, $nombre, $apellido, $fechaNacimiento, $sexo,  $nickname, $password, $sucursal, $email, $telefono, $imagen)
        {
            $fechaActual = date('Y-m-d H:i:s');
    
            $sql = "INSERT INTO " . DB_CON . ".fanatico_clientes (
                                            razon_cliente,
                                            cliente_cliente,
                                            proveedor_cliente,
                                            ruc_cliente,
                                            nickname,
                                            nombre_con_cliente,
                                            apellido_con_cliente,
                                            sexo_cliente,
                                            tel_con_cliente,
                                            email_con_cliente,
                                            pass_cliente,
                                            fecha_nac_cliente,
                                            estado_cliente,
                                            doc_cliente,
                                            id_creador,
                                            nombremodi_cliente,
                                            fechamodi_cliente,
                                            sucursal
                                        )
                                        VALUES (
                                                :razon_cliente,
                                                1,
                                                0,
                                                :ci_usu,
                                                :nickname,
                                                :nombre_usu,
                                                :apellido_usu,
                                                :sexo_usu,
                                                :telefono_usu,
                                                :email,
                                                :pass_usu,
                                                :fec_nac_usu,
                                                :estado_usu,
                                                :doc_cliente,
                                                :id_creador,
                                                :nombremodi_usu,
                                                :fechamodi_usu,
                                                :sucursal)";
            $stmt = $db->prepare($sql);
            $razon =  $nombre . ' ' . $apellido;
            $stmt->bindParam(':razon_cliente', $razon);
            $stmt->bindParam(':nombre_usu', $nombre);
            $stmt->bindParam(':apellido_usu', $apellido);
            $stmt->bindParam(':sexo_usu', $sexo);
            $stmt->bindParam(':fec_nac_usu', $fechaNacimiento);
            $stmt->bindParam(':ci_usu', $rucCliente);
            $stmt->bindParam(':nickname', $nickname);
            $stmt->bindParam(':pass_usu', $password);
            $stmt->bindParam(':fechamodi_usu', $fechaActual);
            $stmt->bindParam(':sucursal', $sucursal);
            $stmt->bindParam(':id_creador', "0");
            $stmt->bindParam(':nombremodi_usu', "");
            $stmt->bindParam(':telefono_usu', $telefono);
            $stmt->bindParam(':doc_cliente', $imagen);
            $stmt->bindParam(':email', $email);
        }
    
        public function loadDocumentClient($imagenClave = "image_ci", $subfijo = "")
        {
            if (isset($_FILES[$imagenClave])) {
                if ((($_FILES[$imagenClave]["type"] == "image/jpeg") && ($_FILES[$imagenClave]["size"] < 20000000000))) {
                    if ($_FILES[$imagenClave]["error"] > 0) {
                        return null;
                    } else {
    
                        $ext = explode('.', basename($_FILES[$imagenClave]["name"]));
                        $extension = array_pop($ext);
                        $nombre_imagen = substr($_FILES[$imagenClave]["name"], 0, 20) . "_" . date("ymdHis") . "." . $extension;
                        $nombre_imagen = str_replace(' ', '', $nombre_imagen);
    
                        $target_lg = __DIR__ . "/../../admin/client/" . CLIENT . "/images/lg/interesados/" . $nombre_imagen . "/" . $subfijo;
                        $target_md = __DIR__ . "/../../admin/client/" . CLIENT . "/images/md/interesados/" . $nombre_imagen . "/" . $subfijo;
                        $target_sm = __DIR__ . "/../../admin/client/" . CLIENT . "/images/sm/interesados/" . $nombre_imagen . "/" . $subfijo;
                        // Inicio funcion de recorte de Martin
                        $w = 430; // ancho deseado
                        $h = 270; // alto deseado
                        if (exif_imagetype($_FILES[$imagenClave]["tmp_name"]) != IMAGETYPE_PNG) {
                            $source = @imagecreatefromjpeg($_FILES[$imagenClave]["tmp_name"]); // cambiar de donde estirar la imagen
                        } else {
                            $source = @imagecreatefrompng($_FILES[$imagenClave]["tmp_name"]); // cambiar de donde estirar la imagen
                        }
    
                        $source = imagerotate($source, 270, 0);
    
                        $width = imagesx($source);
                        $height = imagesy($source);
                        $ratio = $h / $w;
    
                        if ($height / $width >= $ratio) {
                            $width_src = $width;
                            $height_src = $width * $ratio;
                        } else {
                            $width_src = $height / $ratio;
                            $height_src = $height;
                        }
                        $x_src = intval(($width - $width_src) / 2);
                        $y_src = intval(($height - $height_src) / 2);
                        $thumb = imagecreatetruecolor($w, $h);
                        if (exif_imagetype($_FILES[$imagenClave]["tmp_name"]) != IMAGETYPE_PNG) {
                            imagefill($thumb, 0, 0, imagecolorallocate($thumb, 255, 255, 255));
                            imagecopyresampled($thumb, $source, 0, 0, $x_src, $y_src, $w, $h, $width_src, $height_src);
                            header('Content-Type: image/jpeg'); // esto tiene que tener la funcion para recortar la imagen
                            imagejpeg($thumb, $target_md, 80); // usar 100 para maxima calidad
                            // imagedestroy($thumb);
                            $paths[] = $target_lg;
                            $success = true;
                        } else {
                            imagealphablending($thumb, false);
                            imagefill($thumb, 0, 0, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
                            imagesavealpha($thumb, true);
                            imagecopyresampled($thumb, $source, 0, 0, $x_src, $y_src, $w, $h, $width_src, $height_src);
                            header('Content-Type: image/png'); // esto tiene que tener la funcion para recortar la imagen
                            imagepng($thumb, $target_md, 0); // usar 0 para maxima calidad
                            // imagedestroy($thumb);
                            $paths[] = $target_lg;
                            $success = true;
                        }
                        // Fin funcion de Recorte de Martin
    
    
                        move_uploaded_file($_FILES[$imagenClave]["tmp_name"], $target_lg);
    
                        return $nombre_imagen;
                    }
                } else {
                    return null;
                }
            } else {
                return false;
            }
        }
    
    
        public function getDataClientById($db, $idClient)
        {
            $sqlClient = "SELECT
                            ruc_cliente,
                            nombre_con_cliente,
                            apellido_con_cliente,
                            pass_cliente,
                            nickname,
                            sexo_cliente,
                            tel_con_cliente,
                            email_con_cliente,
                            direccion_cliente,
                            estado_cliente,
                            fecha_nac_cliente,
                            autoexclusion,
                            id_club_futbol
                        FROM " . DB_CON . ".fanatico_clientes WHERE id_cliente = '$idClient'";
            $stmt = $db->query($sqlClient);
            $dataClient = $stmt->fetch(PDO::FETCH_OBJ);
    
            return $dataClient;
        }
    
        public function getDataClientByEmail($db, $rucCliente)
        {
            $sqlCliente = "SELECT
                            id_cliente,
                            nombre_con_cliente,
                            apellido_con_cliente,
                            pass_cliente,
                            nickname,
                            sexo_cliente,
                            tel_con_cliente,
                            email_con_cliente,
                            direccion_cliente,
                            estado_cliente,
                            fecha_nac_cliente,
                            autoexclusion,
                            id_club_futbol,
                            id_ciudad,
                            id_pais,
                            ruc_cliente,
                            sucursal
                        FROM " . DB_CON . ".fanatico_clientes WHERE email_con_cliente = '$rucCliente'";
            $stmt = $db->query($sqlCliente);
            $dataClient = $stmt->fetch(PDO::FETCH_OBJ);
            return $dataClient;
        }
    
        public function getDataClientByCI($db, $rucCliente)
        {
            $sqlCliente = "SELECT
                            id_cliente,
                            nombre_con_cliente,
                            apellido_con_cliente,
                            pass_cliente,
                            nickname,
                            sexo_cliente,
                            tel_con_cliente,
                            email_con_cliente,
                            direccion_cliente,
                            estado_cliente,
                            fecha_nac_cliente,
                            autoexclusion,
                            id_club_futbol,
                            id_ciudad,
                            id_pais,
                            ruc_cliente
                            -- sucursal
                        FROM " . DB_CON . ".fanatico_clientes WHERE ruc_cliente = '$rucCliente'";
            $stmt = $db->query($sqlCliente);
            $dataClient = $stmt->fetch(PDO::FETCH_OBJ);
            return $dataClient;
        }
    
        public function isBlocked($db, $idCliente)
        {
            $sqlStateClient = "SELECT
                            estado_cliente
                            FROM " . DB_CON . ".fanatico_clientes WHERE id_cliente = '$idCliente'";
            $stmt = $db->query($sqlStateClient);
            $dataStateClient = $stmt->fetch(PDO::FETCH_OBJ);
    
            $state = $dataStateClient->estado_cliente == "4" ? true : false;
    
            return $state;
        }
    }
function Aud()
{
    $aud = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // $aud = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // $aud = $_SERVER['REMOTE_ADDR'];
    }
    $aud .= @$_SERVER['HTTP_USER_AGENT'];
    $aud .= gethostname();
    return sha1($aud);
}

function generateRandomString($length = 5, $tipo = 4)
{
    switch ($tipo) {
        case '4':
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
            break;

        case '2':
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;

        case '1':
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;

        case '0':
            $characters = '0123456789';
            break;
    }
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Undocumented function
 * un arcchivos
 * @param [type] $name
 * @param [type] $target
 * @param [type] $targetContent
 * @param boolean $comprime
 * @param string $nameFile
 * @return void
 */
function uploadFile($name, $target, $targetContent, $comprime = false, $nameFile = "")
{
    try {

        $respuestas = [];
        $nombre_archivo = "";
        // COMPROBACIÓN INICIAL ANTES DE CONTINUAR CON EL PROCESO DE UPLOAD
        // **********************************************************************
        // Si no se ha llegado ha definir el array global $_FILES, cancelaremos el resto del proceso
        if (empty($_FILES[$name])) {
            // Devolvemos un array asociativo con la clave error en formato JSON como respuesta
            // Cancelamos el resto del script
            return ['error' => 'No hay ficheros para realizar upload.'];
        }

        // DEFINICIÓN DE LAS VARIABLES DE TRABAJO (CONSTANTES, ARRAYS Y VARIABLES)
        // ************************************************************************

        // Definimos la constante con el directorio de destino de las descargas

        $targetLg = __DIR__ . "/../public/img/$target/lg/$targetContent/";
        $targetMd = __DIR__ . "/../public/img/$target/md/$targetContent/";
        $targetSm = __DIR__ . "/../public/img/$target/sm/$targetContent/";
        // Obtenemos el array de ficheros enviados
        $ficheros = $_FILES[$name];
        // Establecemos el indicador de proceso correcto (simplemente no indicando nada)
        $estado_proceso = NULL;
        // Paths para almacenar
        $paths = array();
        // Obtenemos los nombres de los ficheros
        $nombres_ficheros = $ficheros['name'];
        $nuevos_nombres = [];

        // LÍNEAS ENCARGADAS DE REALIZAR EL PROCESO DE UPLOAD POR CADA FICHERO RECIBIDO
        // ****************************************************************************

        // Si no existe la carpeta de destino la creamos
        if (!file_exists($targetLg)) @mkdir($targetLg);
        // Sólo en el caso de que exista esta carpeta realizaremos el proceso
        if (file_exists($targetLg)) {
            // Recorremos el array de nombres para realizar proceso de upload
            for ($i = 0; $i < count($nombres_ficheros); $i++) {
                // Extraemos el nombre y la extensión del nombre completo del fichero
                $nombre_extension = explode('.', basename($nombres_ficheros['file'][$i]));
                // Obtenemos la extensión
                $extension = array_pop($nombre_extension);
                // Obtenemos el nombre
                $nombre = str_replace(" ", "-", array_pop($nombre_extension) . "_" . time());

                if ($nameFile != "") {
                    $nombre = $nameFile;
                }
                // Creamos la ruta de destino
                $archivo_destino = $targetLg . DIRECTORY_SEPARATOR . utf8_decode($nombre) . '.' . $extension;

                $nuevos_nombres[] = $nombre . '.' . $extension;

                $targetSm = __DIR__ . "/../public/img/$target/sm/$targetContent/$nombre.$extension";
                $targetMd = __DIR__ . "/../public/img/$target/md/$targetContent/$nombre.$extension";

                // Mover el archivo de la carpeta temporal a la nueva ubicación
                if (move_uploaded_file($ficheros['tmp_name']['file'][$i], $archivo_destino)) {
                    /* read the source image */

                    $src = $archivo_destino;
                    $dest = $targetSm;
                    $desired_width = 90;
                    if ($extension == 'png') {
                        $source_image = @imagecreatefrompng($src);
                    } else {
                        $source_image = @imagecreatefromjpeg($src);
                    }
                    $width = imagesx($source_image);
                    $height = imagesy($source_image);
                    /* find the "desired height" of this thumbnail, relative to the desired width  */
                    $desired_height = floor($height * ($desired_width / $width));
                    /* create a new, "virtual" image */
                    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
                    /* preserve transparency if png */
                    if ($extension == 'png') {
                        imagealphablending($virtual_image, FALSE);
                        imagesavealpha($virtual_image, TRUE);
                        /* copy source image at a resized size */
                        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
                        /* create the physical thumbnail image to its destination */
                        imagepng($virtual_image, $dest);
                    } else {
                        /* copy source image at a resized size */
                        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
                        /* create the physical thumbnail image to its destination */
                        imagejpeg($virtual_image, $dest);
                    }

                    // $nombre_archivo = "";
                    // Activamos el indicador de proceso correcto
                    $estado_proceso = true;
                    // Almacenamos el nombre del archivo de destino
                    $paths[] = $archivo_destino;

                    $src = $archivo_destino;
                    $dest = $targetMd;
                    $desired_width = 350;
                    if ($extension == 'png') {
                        $source_image = @imagecreatefrompng($src);
                    } else {
                        $source_image = @imagecreatefromjpeg($src);
                    }
                    $width = imagesx($source_image);
                    $height = imagesy($source_image);
                    /* find the "desired height" of this thumbnail, relative to the desired width  */
                    $desired_height = floor($height * ($desired_width / $width));
                    /* create a new, "virtual" image */
                    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
                    /* preserve transparency if png */
                    if ($extension == 'png') {
                        imagealphablending($virtual_image, FALSE);
                        imagesavealpha($virtual_image, TRUE);
                        /* copy source image at a resized size */
                        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
                        /* create the physical thumbnail image to its destination */
                        imagepng($virtual_image, $dest);
                    } else {
                        /* copy source image at a resized size */
                        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
                        /* create the physical thumbnail image to its destination */
                        imagejpeg($virtual_image, $dest);
                    }

                    // $nombre_archivo = "";
                    // Activamos el indicador de proceso correcto
                    $estado_proceso = true;
                    // Almacenamos el nombre del archivo de destino
                    $paths[] = $archivo_destino;
                } else {
                    // Activamos el indicador de proceso erroneo
                    $estado_proceso = false;
                    // Rompemos el bucle para que no continue procesando ficheros
                    break;
                }
            }
        } else {

            return ['error' => 'No existe la carpeta. ' . $targetLg];
        }
        // PREPARAR LAS RESPUESTAS SOBRE EL ESTADO DEL PROCESO REALIZADO
        // **********************************************************************

        // Comprobamos si el estado del proceso a finalizado de forma correcta
        if ($estado_proceso === true) {
            /* Podríamos almacenar información adicional en una base de datos
                con el resto de los datos enviados por el método POST */

            // Como mínimo tendremos que devolver una respuesta correcta por medio de un array vacio.
            $respuestas = ['dirupload' => basename($targetLg), 'name' => $nuevos_nombres, 'total' => count($paths)];
            /* Podemos devolver cualquier otra información adicional que necesitemos por medio de un array asociativo
                Por ejemplo, prodríamos devolver la lista de ficheros subidos de esta manera:
                    $respuestas = ['ficheros' => $paths];
                Posteriormente desde el evento fileuploaded del plugin iríamos mostrando el array de ficheros utilizando la propiedad response
                del parámetro data:
                respuesta = data.response;
                respuesta.ficheros.forEach(function(nombre) {alert(nombre); });
            */
        } elseif ($estado_proceso === false) {
            $respuestas = ['error' => 'Error al subir los archivos. Póngase en contacto con el administrador del sistema'];
            // Eliminamos todos los archivos subidos
            foreach ($paths as $fichero) {
                unlink($fichero);
            }
            // Si no se han llegado a procesar ficheros $estado_proceso seguirá siendo NULL
        } else {
            $respuestas = ['error' => 'No se ha procesado ficheros.'];
        }

        // RESPUESTA DEVUELTA POR EL SCRIPT EN FORMATO JSON
        // **********************************************************************

        // Devolvemos el array asociativo en formato JSON como respuesta
        return $respuestas;
        //code...
    } catch (\Throwable $th) {
        throw $th;
    }
}
/**
 * Undocumented function
 * multimple arcchivos
 * @param [type] $name
 * @param [type] $target
 * @param [type] $targetContent
 * @param boolean $comprime
 * @param string $nameFile
 * @return void
 */
function uploadFiles($name, $target, $targetContent, $comprime = false, $nameFile = "")
{
    try {

        $respuestas = [];
        $nombre_archivo = "";
        // COMPROBACIÓN INICIAL ANTES DE CONTINUAR CON EL PROCESO DE UPLOAD
        // **********************************************************************

        // Si no se ha llegado ha definir el array global $_FILES, cancelaremos el resto del proceso
        if (empty($_FILES[$name])) {
            // Devolvemos un array asociativo con la clave error en formato JSON como respuesta
            // Cancelamos el resto del script
            return ['error' => 'No hay ficheros para realizar upload.'];
        }

        // DEFINICIÓN DE LAS VARIABLES DE TRABAJO (CONSTANTES, ARRAYS Y VARIABLES)
        // ************************************************************************

        // Definimos la constante con el directorio de destino de las descargas

        $targetLg = __DIR__ . "/../public/img/$target/lg/$targetContent/";
        $targetMd = __DIR__ . "/../public/img/$target/md/$targetContent/";
        $targetSm = __DIR__ . "/../public/img/$target/sm/$targetContent/";
        // Obtenemos el array de ficheros enviados
        $ficheros = $_FILES[$name];
        // Establecemos el indicador de proceso correcto (simplemente no indicando nada)
        $estado_proceso = NULL;
        // Paths para almacenar
        $paths = array();
        // Obtenemos los nombres de los ficheros
        $nombres_ficheros = $ficheros['name'];
        $nuevos_nombres = [];

        // LÍNEAS ENCARGADAS DE REALIZAR EL PROCESO DE UPLOAD POR CADA FICHERO RECIBIDO
        // ****************************************************************************

        // Si no existe la carpeta de destino la creamos
        if (!file_exists($targetLg)) @mkdir($targetLg);
        // Sólo en el caso de que exista esta carpeta realizaremos el proceso
        if (file_exists($targetLg)) {
            // Recorremos el array de nombres para realizar proceso de upload
            for ($i = 0; $i < count($nombres_ficheros); $i++) {
                // Extraemos el nombre y la extensión del nombre completo del fichero
                $nombre_extension = explode('.', basename($nombres_ficheros[$i]));
                // Obtenemos la extensión
                $extension = array_pop($nombre_extension);
                // Obtenemos el nombre
                $nombre = str_replace(" ", "-", array_pop($nombre_extension) . "_" . time());

                if ($nameFile != "") {
                    $nombre = $nameFile;
                }
                // Creamos la ruta de destino
                $archivo_destino = $targetLg . DIRECTORY_SEPARATOR . utf8_decode($nombre) . '.' . $extension;

                $nuevos_nombres[] = $nombre . '.' . $extension;

                $targetSm = __DIR__ . "/../../$target/sm/$targetContent/$nombre.$extension";
                $targetMd = __DIR__ . "/../../$target/md/$targetContent/$nombre.$extension";

                // Mover el archivo de la carpeta temporal a la nueva ubicación
                if (move_uploaded_file($ficheros['tmp_name'][$i], $archivo_destino)) {
                    /* read the source image */

                    $src = $archivo_destino;
                    $dest = $targetSm;
                    $desired_width = 90;
                    if ($extension == 'png') {
                        $source_image = @imagecreatefrompng($src);
                    } else {
                        $source_image = @imagecreatefromjpeg($src);
                    }
                    $width = imagesx($source_image);
                    $height = imagesy($source_image);
                    /* find the "desired height" of this thumbnail, relative to the desired width  */
                    $desired_height = floor($height * ($desired_width / $width));
                    /* create a new, "virtual" image */
                    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
                    /* preserve transparency if png */
                    if ($extension == 'png') {
                        imagealphablending($virtual_image, FALSE);
                        imagesavealpha($virtual_image, TRUE);
                        /* copy source image at a resized size */
                        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
                        /* create the physical thumbnail image to its destination */
                        imagepng($virtual_image, $dest);
                    } else {
                        /* copy source image at a resized size */
                        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
                        /* create the physical thumbnail image to its destination */
                        imagejpeg($virtual_image, $dest);
                    }

                    // $nombre_archivo = "";
                    // Activamos el indicador de proceso correcto
                    $estado_proceso = true;
                    // Almacenamos el nombre del archivo de destino
                    $paths[] = $archivo_destino;

                    $src = $archivo_destino;
                    $dest = $targetMd;
                    $desired_width = 350;
                    if ($extension == 'png') {
                        $source_image = @imagecreatefrompng($src);
                    } else {
                        $source_image = @imagecreatefromjpeg($src);
                    }
                    $width = imagesx($source_image);
                    $height = imagesy($source_image);
                    /* find the "desired height" of this thumbnail, relative to the desired width  */
                    $desired_height = floor($height * ($desired_width / $width));
                    /* create a new, "virtual" image */
                    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
                    /* preserve transparency if png */
                    if ($extension == 'png') {
                        imagealphablending($virtual_image, FALSE);
                        imagesavealpha($virtual_image, TRUE);
                        /* copy source image at a resized size */
                        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
                        /* create the physical thumbnail image to its destination */
                        imagepng($virtual_image, $dest);
                    } else {
                        /* copy source image at a resized size */
                        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
                        /* create the physical thumbnail image to its destination */
                        imagejpeg($virtual_image, $dest);
                    }

                    // $nombre_archivo = "";
                    // Activamos el indicador de proceso correcto
                    $estado_proceso = true;
                    // Almacenamos el nombre del archivo de destino
                    $paths[] = $archivo_destino;
                } else {
                    // Activamos el indicador de proceso erroneo
                    $estado_proceso = false;
                    // Rompemos el bucle para que no continue procesando ficheros
                    break;
                }
            }
        } else {

            return ['error' => 'No existe la carpeta. ' . $targetLg];
        }
        // PREPARAR LAS RESPUESTAS SOBRE EL ESTADO DEL PROCESO REALIZADO
        // **********************************************************************

        // Comprobamos si el estado del proceso a finalizado de forma correcta
        if ($estado_proceso === true) {
            /* Podríamos almacenar información adicional en una base de datos
                con el resto de los datos enviados por el método POST */

            // Como mínimo tendremos que devolver una respuesta correcta por medio de un array vacio.
            $respuestas = ['dirupload' => basename($targetLg), 'name' => $nuevos_nombres, 'total' => count($paths)];
            /* Podemos devolver cualquier otra información adicional que necesitemos por medio de un array asociativo
                Por ejemplo, prodríamos devolver la lista de ficheros subidos de esta manera:
                    $respuestas = ['ficheros' => $paths];
                Posteriormente desde el evento fileuploaded del plugin iríamos mostrando el array de ficheros utilizando la propiedad response
                del parámetro data:
                respuesta = data.response;
                respuesta.ficheros.forEach(function(nombre) {alert(nombre); });
            */
        } elseif ($estado_proceso === false) {
            $respuestas = ['error' => 'Error al subir los archivos. Póngase en contacto con el administrador del sistema'];
            // Eliminamos todos los archivos subidos
            foreach ($paths as $fichero) {
                unlink($fichero);
            }
            // Si no se han llegado a procesar ficheros $estado_proceso seguirá siendo NULL
        } else {
            $respuestas = ['error' => 'No se ha procesado ficheros.'];
        }

        // RESPUESTA DEVUELTA POR EL SCRIPT EN FORMATO JSON
        // **********************************************************************

        // Devolvemos el array asociativo en formato JSON como respuesta
        return $respuestas;
        //code...
    } catch (\Throwable $th) {
        throw $th;
    }
}

function facturaRetencion($obj)
{

    $timbrado = $obj['timbrado'];
    $codigoVerificacion = $obj['codigoVerificacion'];
    $fechaVigencia = $obj['fechaVigencia'];
    $rucEmpresa = $obj['rucEmpresa'];
    $nroFactura = $obj['nroFactura'];

    $fechaEmision = $obj['fechaEmision'];
    $ruc = $obj['ruc'];
    $razon = $obj['razon'];
    $direccion = $obj['direccion'];

    $nroComprobante = $obj['nroComprobante'];
    $fechaComprobante = $obj['fechaComprobante'];
    $moneda = $obj['moneda'];

    $valorParcial = $obj['valorParcial'];
    $totalComprobante = $obj['totalComprobante'];
    $baseRetencion = $obj['baseRetencion'];
    $factorRetencion = $obj['factorRetencion'];
    $totalRetenido = $obj['totalRetenido'];
    $totalGeneral = $obj['totalGeneral'];
    $nro_retencion = $obj['nro_retencion'];
    $factura_timbrado = $obj['factura_timbrado'];

    $descripcion = $obj['descripcion'];
    $monto = $obj['monto'];

    $detalles = '<tr>
            <td width="40">
                1
            </td>
            <td width="290">
               ' . $descripcion . ' 
            </td>
            <td width="66">
                ' . $monto . ' 
            </td>
            <td width="91">
                ' . $monto . ' 
            </td>
            <td width="91">
           0
            </td>
            <td width="91">
            0
            </td>
        </tr>';

    $html = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html lang="es">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
                    <title>Factura</title>
                </head>
                <body>
                    <div>

                        <span class="dato font3" style="left:35mm;margin-top:14mm;position: absolute;font-size: 4mm;font-weight:bold;">Proinso S.A.</span>
                        <span class="dato font2" style="left:8mm; margin-top:23mm;position: absolute;font-size: 2.8mm;"><div>OTRAS ACTIVIDADES DE DIVERSION Y ENTRETENIMIENTO N.C.P.</div></span>
                        <span class="dato font2" style="left:37mm; margin-top:26mm;position: absolute;font-size: 2.8mm;">Tel: 0981 203 902</span>
                        <span class="dato font1" style="left:20mm; margin-top:31mm;position: absolute;font-size: 3.3mm;">CALLE,ITEIPU NUMERO #7 //OFICINA</span>
                        
                        <span class="dato font1" style="left:148.8mm; margin-top:9.6mm;position: absolute;font-size: 3.3mm;">' . $timbrado . '</span>
                        <span class="dato font1" style="left:148.8mm; margin-top:14.6mm;position: absolute;font-size: 3.3mm;">' . $codigoVerificacion . '</span>
                        <span class="dato font1" style="left:152.9mm; margin-top:21.8mm;position: absolute;font-size: 3.3mm;">' . $fechaVigencia . '</span>
                        <span class="dato font1" style="left:141.8mm;margin-top:27.8mm;position: absolute;font-size: 3.3mm;"><b>' . $rucEmpresa . '</b></span>

                        <span class="dato font3" style="left:133.5mm; margin-top:40mm;position: absolute;font-size: 4mm;font-weight:bold;">' . $nro_retencion . '</span>

                        <span class="dato font1" style="left:29.5mm; margin-top:46.5mm;position: absolute;font-size: 3.3mm;">' . $fechaEmision . '</span>
                        <span class="dato font1" style="left:50mm; margin-top:53.8mm;position: absolute;font-size: 3.3mm;">' . $ruc . '</span>
                        <span class="dato font1" style="left:47mm; margin-top:60.5mm;position: absolute;font-size: 3.3mm;">' . $razon . '</span>
                        <span class="dato font1" style="left:25mm; margin-top:67.5mm;position: absolute;font-size: 3.3mm;">' . $direccion . '</span>

                        <span class="dato font1" style="left:147mm; margin-top:53.8mm;position: absolute;font-size: 3.3mm;">' . $nroComprobante . '</span>
                        <span class="dato font1" style="left:147mm; margin-top:60.5mm;position: absolute;font-size: 3.3mm;">' . $fechaComprobante . '</span>
                        <span class="dato font1" style="left:120mm; margin-top:67.5mm;position: absolute;font-size: 3.3mm;">' . $moneda . '</span>

                        <table style="left:10mm; margin-top:91mm;font-size: 2.8mm;position: absolute;">
                            ' . $detalles . '
                        </table>

                        <span style="left:130mm; margin-top:123mm;font-size: 2.8mm;position: absolute;">' . $valorParcial . '</span>
                        <span style="left:155mm; margin-top:123mm;font-size: 2.8mm;position: absolute;">0</span>
                        <span style="left:179mm; margin-top:123mm;font-size: 2.8mm;position: absolute;">0</span>
                        <span style="left:179mm; margin-top:130mm;font-size: 2.8mm;position: absolute;">' . $totalComprobante . '</span>

                        <span style="left:83mm; margin-top:159mm;font-size: 2.8mm;position: absolute;">' . $baseRetencion . '</span>
                        <span style="left:105mm; margin-top:159mm;font-size: 2.8mm;position: absolute;"> 0</span>
                        <span style="left:130mm; margin-top:159mm;font-size: 2.8mm;position: absolute;"> 0</span>
                        <span style="left:153mm; margin-top:159mm;font-size: 2.8mm;position: absolute;"> 0</span>
                        <span style="left:178mm; margin-top:159mm;font-size: 2.8mm;position: absolute;"> 0</span>

                        <span style="left:83mm; margin-top:169mm;font-size: 2.8mm;position: absolute;">' . $factorRetencion . '</span>
                        <span style="left:105mm; margin-top:169mm;font-size: 2.8mm;position: absolute;"> 0%</span>
                        <span style="left:130mm; margin-top:169mm;font-size: 2.8mm;position: absolute;"> 0%</span>
                        <span style="left:153mm; margin-top:169mm;font-size: 2.8mm;position: absolute;"> 0%</span>
                        <span style="left:178mm; margin-top:169mm;font-size: 2.8mm;position: absolute;"> 0%</span>

                        <span style="left:83mm; margin-top:179mm;font-size: 2.8mm;position: absolute;">' . $totalRetenido . '</span>
                        <span style="left:105mm; margin-top:179mm;font-size: 2.8mm;position: absolute;"> 0</span>
                        <span style="left:130mm; margin-top:179mm;font-size: 2.8mm;position: absolute;"> 0</span>
                        <span style="left:153mm; margin-top:179mm;font-size: 2.8mm;position: absolute;"> 0</span>
                        <span style="left:178mm; margin-top:179mm;font-size: 2.8mm;position: absolute;"> 0</span>

                        <span style="left:178mm; margin-top:186mm;font-size: 3.2mm;position: absolute;"><b>' . $totalGeneral . '</b></span>

                    </div>
                    <img src="https://local.quattropy.com/flexible.tools2/server_webv4/public/img/retencion.png" alt="Factura" style="position:absolute; width:200mm; height:191mm;" />
                </body>
            </html>
        ';

    return $html;
}

function validateEmail($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}

//--- MONEDA
function moneda_local($valor, $moneda = "")
{
    if ($moneda == "") {
        if ($valor > 0 || $valor < 0) {
            return number_format($valor, MONEDA[0], MONEDA[1], MONEDA[2]);
        } else {
            return 0;
        }
    } elseif ($moneda == "usd_") {
        if ($valor > 0 || $valor < 0) {
            return number_format($valor, 2, '.', ',');
        } else {
            return 0;
        }
    }
}