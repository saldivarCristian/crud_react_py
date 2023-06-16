<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/clubcomodin/product/st/destacados', function(Request $request, Response $response){
    try
    {
        // Get DB Object
        $db = $this->db;

        $q="
            SELECT p.nombre_producto,p.id_producto,p.precio_bronce_producto,p.precio_plata_producto,p.precio_oro_producto
            ,p.precio_platino_producto,p.codigo_producto,p.imagen_producto,p.tipo,p.bono_sorteo
            FROM ".DB_MBO.".productos p
            WHERE p.estado_producto = 1 AND p.destacado_producto = 1
        ";
        $stmt = $db->query($q);
        $productos = $stmt->fetchAll(PDO::FETCH_OBJ);
    
        return $response->withJson(
                        array(
                                'status' => 'success', 
                                'message' => '', 
                                'data'=> [
                                    'productos' => $productos
                                ]
                            )
        ); 
    } 
    catch(PDOException $e){
        throw $e;
    } 
});

$app->get('/clubcomodin/product/st/listar', function(Request $request, Response $response){
    try
    {
        // Get DB Object
        $db = $this->db;
        $settings = $this->get('settings');

        $q="
            SELECT p.nombre_producto,p.id_producto,p.precio_bronce_producto,p.precio_plata_producto,p.precio_oro_producto
            ,p.precio_platino_producto,p.codigo_producto,p.imagen_producto,p.tipo,p.id_categoria,c.nombre_categoria,p.bono_sorteo
            FROM ".DB_MBO.".productos p join categorias c on c.id_categoria = p.id_categoria
            WHERE p.estado_producto = 1 
        ";
        $stmt = $db->query($q);
        $productos = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        $q="
            SELECT *
            FROM ".DB_MBO.".categorias
        ";
        $stmt = $db->query($q);
        $categorias = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $response->withJson(
                        array(
                                'status' => 'success', 
                                'message' => '', 
                                'data'=> [
                                    'productos' => $productos,
                                    'categorias' => $categorias
                                ]
                            )
        ); 
    } 
    catch(PDOException $e){
        throw $e;
    }
});

$app->get('/clubcomodin/product/st/listarTickets', function(Request $request, Response $response){
    try
    {
        // Get DB Object
        $db = $this->db;
        $settings = $this->get('settings');
        $fecha = date("Y-m-d H:i:s");
        $q="
            SELECT p.nombre_producto,p.id_producto,p.precio_bronce_producto,p.precio_plata_producto,p.precio_oro_producto
            ,p.precio_platino_producto,p.codigo_producto,p.imagen_producto,p.tipo,p.id_categoria,c.nombre_categoria,s.fecha_hora,p.bono_sorteo
            FROM ".DB_MBO.".productos p 
            JOIN categorias c on c.id_categoria = p.id_categoria
            JOIN sorteos_comodin s ON p.bono_sorteo = s.id_sorteo AND p.tipo = 2
            WHERE p.estado_producto = 1  and p.tipo= 2 AND (s.fecha_hora >= '$fecha' OR s.fecha_hora IS NULL)
        ";
        $stmt = $db->query($q);
        $productos = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        return $response->withJson(
                        array(
                                'status' => 'success', 
                                'message' => '', 
                                'data'=> [
                                    'productos' => $productos
                                ]
                            )
        ); 
    } 
    catch(PDOException | Exception  $e){
        throw $e;
    }
});

$app->get('/clubcomodin/product/st/detalle/{id}', function(Request $request, Response $response){
    try
    {
        // Get DB Object
        $db = $this->db;
        $id = $request->getAttribute('id');
        if( $id ){
            $q="
                SELECT p.nombre_producto,p.id_producto,p.precio_bronce_producto,p.precio_plata_producto,p.precio_oro_producto
                ,p.precio_platino_producto,p.codigo_producto,p.imagen_producto,p.tipo,desc_producto,p.bono_sorteo
                FROM ".DB_MBO.".productos p
                WHERE p.id_producto = $id 
            ";
            $stmt = $db->query($q);
            $producto = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $response->withJson(
                    array(
                            'status' => 'success', 
                            'message' => '', 
                            'data'=> $producto
                        )
            ); 
        }else{
            return $response->withJson(
                array(
                        'status' => 'fail', 
                        'message' => 'No se encontro el producto seleccionado.', 
                        'data'=> []
                    )
            ); 
        }

    } 
    catch(PDOException $e){
        throw $e;
    }
});

$app->post('/clubcomodin/product/comprarProducto', function(Request $request, Response $response){
    try
    {
        // Get DB Object
        $db = $this->db;
        $settings = $this->get('settings');

        $token = $request->getAttribute("token");
        if (@$token['aud'] === Aud()) {
            //Datos de la transaccion
            $idCliente = $token['id_cliente'];
            $sucursal = $token['sucursal'];
            //Datos personales del cliente y monto a pagar
            $monto_compra =  $request->getParam("monto_compra");
            $direccion =  $request->getParam("direccion");
            $fecha = date('Y-m-d H:i:s');
			
            //--- NIVEL
            $w= '';
            if (CONF_COMODIN['CADUCIDAD_NIVEL']!=0) {
                $fecha_actual = new DateTime(date('Y-m-d',strtotime('+0 year',time())));
                $fecha_actual = $fecha_actual->modify('last day of this month')->format('Y-m-d');
                $fecha_anterior = new DateTime( date('Y-m-d',strtotime('-'.(CONF_COMODIN['CADUCIDAD_NIVEL']).' MONTH',time())));
                $fecha_anterior = $fecha_anterior->modify('last day of this month')->format('Y-m-d');
                $w= "cierre BETWEEN '$fecha_anterior' and '$fecha_actual' AND";
            }
            $fin_t = "SELECT punto_fid,punto_usado_fid FROM ".DB_CON.".fidelidad WHERE id_cliente= ".$idCliente;
            $stmt = $db->query($fin_t);
            $datos = $stmt->fetch(PDO::FETCH_OBJ);
            $punto_fid = $datos->punto_fid;
            $punto_usado_fid = $datos->punto_usado_fid;   

            $array_f = "SELECT sum(puntos) as puntos, sum(puntos_usados) puntos_usados FROM ".DB_CON.".fidelidad_cierre WHERE $w id_cliente= ".$idCliente;
            $stmt = $db->query($array_f);
            $datos = $stmt->fetch(PDO::FETCH_OBJ);
            $det_puntos = $datos->puntos;
            $det_puntos_usados = $datos->puntos_usados;
            $puntos = $datos->puntos + $punto_fid;             
       
            $total_p_actual = ($punto_fid + $det_puntos) - ($punto_usado_fid +  $det_puntos_usados); 
            if ($total_p_actual < $monto_compra) {
                return $response->withJson(
                    array(
                        'status' => 'fail', 
                        'message' => "No posee puntos suficiente para canjear. Su punto es de $total_p_actual", 
                        'data'=> []
                    )
                ); 
            }
            // && count($request->getParam('cartItems')) > 0 
            $cartItems = $request->getParam('cartItems')?? [];
            if( isset( $cartItems ) && count($cartItems) ){

				$estado = 1;
				foreach ($cartItems as $key => $value) {
					if ($value['tipo'] == '0') {
						$estado = 0;
					}
				}

				$orden_cabecera = "
					INSERT INTO orden_cabecera (id_cliente,fecha_orden,direccion,compra,estado,origen) 
					VALUES ('$idCliente','$fecha','$direccion','$monto_compra','$estado',1)
				";
				$stmt = $db->prepare($orden_cabecera);
				$stmt->execute();
                $id_cab = $db->lastInsertId();

                foreach ($cartItems as $key => $value) {
					$id_producto=$value["id_producto"];
		            $qty=$value["count"];
		            $iva=0;
		            $precio_bronce=($value['precio_bronce_producto']);
			        $precio_plata=($value['precio_plata_producto']);
			        $precio_oro=($value['precio_oro_producto']);
			        $precio_platino=($value['precio_platino_producto']);
			        $tipo_=$value['tipo'];
			        $bono_sorteo=$value['bono_sorteo'];

                    if ($puntos >= CONF_COMODIN['FIDELIDAD']['SILVER_CREDIT'] && $puntos < CONF_COMODIN['FIDELIDAD']['GOLD_CREDIT']) {
			        	$precio = $precio_plata;
		            }elseif($puntos >= CONF_COMODIN['FIDELIDAD']['GOLD_CREDIT'] && $puntos < CONF_COMODIN['FIDELIDAD']['PLATIMUN_CREDIT']){
			        	$precio = $precio_oro;
		            }elseif($puntos >= CONF_COMODIN['FIDELIDAD']['PLATIMUN_CREDIT']){
			        	$precio = $precio_platino;
		            }else{
			        	$precio = $precio_bronce;
		            }
					
		            // codigo de insercion a la base de datos de ordenes
		            $sql = "
						INSERT INTO orders (orden_id,id_producto,producto,cantidad,precio_producto,iva) 
						VALUES ('$id_cab','$id_producto','".$value["nombre_producto"]."','$qty','$precio','$iva')";
					$stmt = $db->prepare($sql);
					$stmt->execute();
					
		            if ($tipo_ == 1) {
			        	for ($i=0; $i < $qty; $i++) {
							$sql = "SELECT * FROM ".DB_BONO.".tipos WHERE id_tipo=".$bono_sorteo;
							$stmt = $db->query($sql);
							$bono = $stmt->fetch(PDO::FETCH_ASSOC);
							$fech_actual = date('Y-m-d H:i:s');
							$vencimiento = date('Y-m-d H:i:s', strtotime($fech_actual." +".$bono['duracion']." days"));
					        $orders = "INSERT INTO ".DB_BONO.".cliente_bono
																				        	(
																								id_cliente,
																								id_tipo,
																								fecha_cliente_tipo,
																								duracion,
																								fecha_vencimiento,
																								credito,
																								id_creador,
																								nombremodi_cliente_bono,
																								fechamodi_cliente_bono,
																								sucursal
																							)
																							VALUES
																							(
																								'".$idCliente."',
																								'".$bono['id_tipo']."',
																								'$fech_actual',
																								'".$bono['duracion']."',
																								'".$vencimiento."',
																								'".$bono['valor_tipo']."',
																								'1',
																								'Web Form',
																								'$fech_actual',
																								'".$sucursal."'
																							)";
							$stmt = $db->prepare($orders);
							$stmt->execute();
			        	}
					}
					
			        if ($tipo_ == 2) {
						$fecha_ticket = date('Y:m:d H:i:s');
			        	for ($i=0; $i < $qty; $i++) {
							$sql = "SELECT * FROM sorteos_comodin WHERE id_sorteo=".$bono_sorteo;
							$stmt = $db->query($sql);
							$sorteo = $stmt->fetch(PDO::FETCH_ASSOC);
					        $orders_ = "INSERT INTO sorteos_bolillero_comodin
																		        	(
																						id_cliente,
																						id_sorteo,
																						order_id,
																						fecha_cliente_sorteo,
																						id_creador,
																						nombremodi_cliente_sorteo,
																						fechamodi_cliente_sorteo,
																						sucursal
																					)
																					VALUES
																					(
																						'".$idCliente."',
																						'".$sorteo['id_sorteo']."',
																						'".$id_cab."',
																						'$fecha_ticket',
																						'1',
																						'Web Form',
																						'$fecha_ticket',
																						'".$sucursal."'
																					)";
							$stmt = $db->prepare($orders_);
							$stmt->execute();
							$nro_boleto = $db->lastInsertId();

					        $registro_sorteo = "INSERT INTO sorteos_bolillero_registro
																		        	(
																		        		nro_boleto,
																						id_cliente,
																						id_sorteo,
																						order_id,
																						fecha_cliente_sorteo,
																						id_creador,
																						nombremodi_cliente_sorteo,
																						fechamodi_cliente_sorteo,
																						sucursal
																					)
																					VALUES
																					(
																						'".$nro_boleto."',
																						'".$idCliente."',
																						'".$sorteo['id_sorteo']."',
																						'".$id_cab."',
																						'$fecha_ticket',
																						'1',
																						'Web Form',
																						'$fecha_ticket',
																						'".$sucursal."'
																					)";
							$stmt = $db->prepare($orders_);
							$stmt->execute();
			        	}
					}
					
		        }

				$sub_total = $monto_compra;
				$sql = "SELECT * FROM ".DB_CON.".fidelidad_cierre WHERE $w id_cliente= ".$idCliente." AND puntos !=0 AND puntos != puntos_usados order by cierre asc";
				$stmt = $db->query($sql);
				$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$count = $stmt->rowCount();

				if ($count > 0) {
					foreach ($datos as $key => $value) {
						if ($sub_total > ($value['puntos'] - $value['puntos_usados']) ) {
							if ( ($value['puntos'] - $value['puntos_usados'])) {
								$descontar = $value['puntos'];
								$sub_total = $sub_total - ($value['puntos'] - $value['puntos_usados']);
							}else{
								break;
							}
						}else if($sub_total <= 0){
							break;
						}else{
							$descontar = $sub_total + $value['puntos_usados'];
							$sub_total = 0;
						}
						$id_cierre = $value['id_cierre'];
						$sql = "UPDATE ".DB_CON.".fidelidad_cierre set puntos_usados = $descontar WHERE id_cliente = '$idCliente' and id_cierre = '$id_cierre'";
						$stmt = $db->prepare($sql);
						$stmt->execute();
					}
					if ($sub_total > 0) {
						$sql = "UPDATE ".DB_CON.".fidelidad set punto_usado_fid = $sub_total WHERE id_cliente = '$idCliente'";
						$stmt = $db->prepare($sql);
						$stmt->execute();
					}
				}else{
					$sql = "SELECT * FROM ".DB_CON.".fidelidad WHERE id_cliente= ".$idCliente." AND punto_fid !=0";
					$stmt = $db->query($sql);
					$query_fide = $stmt->fetch(PDO::FETCH_ASSOC);
					$count = $stmt->rowCount();
					if ($count > 0) {
						$descontar_fide = $sub_total + $query_fide['punto_usado_fid'];
					}
					$sql = "UPDATE ".DB_CON.".fidelidad set punto_usado_fid = $descontar_fide WHERE id_cliente = '$idCliente'";
					$stmt = $db->prepare($sql);
					$stmt->execute();
				}
				// $_SESSION["total"] = $_SESSION["total"] - $monto_compra;
				$puntoActual = $total_p_actual - $monto_compra;
				return $response->withJson(
					array(
						'status' => 'success', 
						'message' => "Operación exitosa.", 
						'data'=> [
							'puntos'=> $puntoActual
						]
					)
				); 

            }else{
                return $response->withJson(
                    array(
                        'status' => 'fail', 
                        'message' => "Debe elegir un producto.", 
                        'data'=> []
                    )
                ); 
            }

        }else{
            return $response->withJson(array("status" => "fail", "message" => "Acceso Denegado","data" => array()));
        }


	    	// $w=	'';
			// if (CONF_COMODIN['CADUCIDAD_NIVEL']!=0) {
			// 	$fecha_actual = new DateTime(date('Y-m-d',strtotime('+0 year',time())));
			// 	$fecha_actual = $fecha_actual->modify('last day of this month')->format('Y-m-d');
			// 	$fecha_anterior = new DateTime( date('Y-m-d',strtotime('-'.(CONF_COMODIN['CADUCIDAD_NIVEL']).' MONTH',time())));
			// 	$fecha_anterior = $fecha_anterior->modify('last day of this month')->format('Y-m-d');
			// 	$w= "cierre BETWEEN '$fecha_anterior' and '$fecha_actual' AND";
			// }
			// $fin_t = "SELECT punto_fid,punto_usado_fid FROM ".DB_CON.".fidelidad WHERE id_cliente= ".$_SESSION["uid"];
			// $query2 = mysqli_query($db,$fin_t);
			// $fin_t1 = 0;
			// while ($row2=mysqli_fetch_array($query2)) {
			// 	$fin_t = (float) $row2['punto_fid'];
			// 	$fin_t1 = (float) $row2['punto_usado_fid'];
			// }

			// $array_f = "SELECT sum(puntos) as puntos FROM ".DB_CON.".fidelidad_cierre WHERE $w id_cliente= ".$_SESSION["uid"];
			// $query2 = mysqli_query($db,$array_f);
			// while ($row2 = mysqli_fetch_array($query2)) {
			// 	$puntos =(float) $row2['puntos']+$fin_t;
			// }

			// //--- puntos
			// $w=	'';
			// if (CONF_COMODIN['VIGENCIA_PUNTOS']!=0) {
			// 	$fecha_actual = new DateTime(date('Y-m-d',strtotime('+0 year',time())));
			// 	$fecha_actual = $fecha_actual->modify('last day of this month')->format('Y-m-d');
			// 	$fecha_anterior = new DateTime( date('Y-m-d',strtotime('-'.(CONF_COMODIN['VIGENCIA_PUNTOS']).' MONTH',time())));
			// 	$fecha_anterior = $fecha_anterior->modify('last day of this month')->format('Y-m-d');
			// 	$w= "cierre BETWEEN '$fecha_anterior' and '$fecha_actual' AND";
			// }

			// $fin_t = "SELECT punto_fid,punto_usado_fid FROM ".DB_CON.".fidelidad WHERE id_cliente= ".$_SESSION["uid"];
			// $query2 = mysqli_query($db,$fin_t);
			// while ($row2=mysqli_fetch_array($query2)) {
			// 	$fin_t = (float) ($row2['punto_fid']-$row2['punto_usado_fid']);
			// }

			// $array_f = "SELECT sum(puntos) as puntos,sum(puntos_usados) as puntos_usados FROM ".DB_CON.".fidelidad_cierre WHERE $w id_cliente= ".$_SESSION["uid"];
			// $query2 = mysqli_query($db,$array_f);
			// while ($row2=mysqli_fetch_array($query2)) {
			// 	$vigencia_puntos = (float) ($row2['puntos']-$row2['puntos_usados'])+$fin_t;
			// }

			// if ($vigencia_puntos < $monto_compra) {
			// 	echo "
			// 			<div class='alert alert-danger'>
			// 				<b>No posee puntos suficiente para canjear. Su punto es de $monto_compra</b>
			// 			</div>
			// 		 ";
			// 	exit();
			// }

			// $sql = "SELECT * FROM cart c, productos p WHERE c.user_id = '$id_usuario' AND c.p_id = p.id_producto";
	    	// $query = mysqli_query($db7,$sql);

	    	// if (mysqli_num_rows($query) > 0) {
		    //     $orden_cabecera = mysqli_query($db,"INSERT INTO orden_cabecera (id_cliente,fecha_orden,direccion,compra,estado,origen) VALUES ('$id_usuario','$fecha','$direccion','$monto_compra','$estado',1)");
		    //     $id_cab = $db->getLastID();
		    //     while ($e=mysqli_fetch_array($query)) {
		    //         $id_producto=$e["p_id"];
		    //         $qty=$e["qty"];
		    //         $iva=0;
		    //         $precio_bronce=($e['precio_bronce_producto']);
			//         $precio_plata=($e['precio_plata_producto']);
			//         $precio_oro=($e['precio_oro_producto']);
			//         $precio_platino=($e['precio_platino_producto']);
			//         $tipo_=$e['tipo'];
			//         $bono_sorteo=$e['bono_sorteo'];
			// 		if ($puntos >= CONF_COMODIN['FIDELIDAD']['SILVER_CREDIT'] && $puntos < CONF_COMODIN['FIDELIDAD']['GOLD_CREDIT']) {
			//         	$precio = $precio_plata;
		    //         }elseif($puntos >= CONF_COMODIN['FIDELIDAD']['GOLD_CREDIT'] && $puntos < CONF_COMODIN['FIDELIDAD']['PLATIMUN_CREDIT']){
			//         	$precio = $precio_oro;
		    //         }elseif($puntos >= CONF_COMODIN['FIDELIDAD']['PLATIMUN_CREDIT']){
			//         	$precio = $precio_platino;
		    //         }else{
			//         	$precio = $precio_bronce;
		    //         }
		    //         // codigo de insercion a la base de datos de ordenes
		    //         $sql = "INSERT INTO orders (orden_id,id_producto,producto,cantidad,precio_producto,iva) VALUES ('$id_cab','$id_producto','".$e["nombre_producto"]."','$qty','$precio','$iva')";
		    //         mysqli_query($db,$sql);
		    //         if ($tipo_ == 1) {
			//         	for ($i=0; $i < $qty; $i++) {
			// 	        	$sql2 = mysqli_query($db,"SELECT * FROM ".DB_BONO.".tipos WHERE id_tipo=".$bono_sorteo);
			// 				$bono = mysqli_fetch_array($sql2);
			// 				$fech_actual = date('Y-m-d H:i:s');
			// 				$vencimiento = date('Y-m-d H:i:s', strtotime($fech_actual." +".$bono['duracion']." days"));
			// 		        $orders = mysqli_query($db,"INSERT INTO ".DB_BONO.".cliente_bono
			// 																	        	(
			// 																					id_cliente,
			// 																					id_tipo,
			// 																					fecha_cliente_tipo,
			// 																					duracion,
			// 																					fecha_vencimiento,
			// 																					credito,
			// 																					id_creador,
			// 																					nombremodi_cliente_bono,
			// 																					fechamodi_cliente_bono,
			// 																					sucursal
			// 																				)
			// 																				VALUES
			// 																				(
			// 																					'".$id_usuario."',
			// 																					'".$bono['id_tipo']."',
			// 																					'$fech_actual',
			// 																					'".$bono['duracion']."',
			// 																					'".$vencimiento."',
			// 																					'".$bono['valor_tipo']."',
			// 																					'1',
			// 																					'Web Form',
			// 																					'$fech_actual',
			// 																					'".$sucursal."'
			// 																				)");
			//         	}
			//         }
			//         if ($tipo_ == 2) {
			// 					$fecha_ticket = date('Y:m:d H:i:s');
			//         	for ($i=0; $i < $qty; $i++) {
			// 	        	$sql3 = mysqli_query($db,"SELECT * FROM sorteos_comodin WHERE id_sorteo=".$bono_sorteo);
			// 						$sorteo = mysqli_fetch_array($sql3);
			// 		        $orders_ = mysqli_query($db,"INSERT INTO sorteos_bolillero_comodin
			// 															        	(
			// 																			id_cliente,
			// 																			id_sorteo,
			// 																			order_id,
			// 																			fecha_cliente_sorteo,
			// 																			id_creador,
			// 																			nombremodi_cliente_sorteo,
			// 																			fechamodi_cliente_sorteo,
			// 																			sucursal
			// 																		)
			// 																		VALUES
			// 																		(
			// 																			'".$id_usuario."',
			// 																			'".$sorteo['id_sorteo']."',
			// 																			'".$id_cab."',
			// 																			'$fecha_ticket',
			// 																			'1',
			// 																			'Web Form',
			// 																			'$fecha_ticket',
			// 																			'".$sucursal."'
			// 																		)");
			// 		        $nro_boleto = $db->getLastID();
			// 		        $registro_sorteo = mysqli_query($db,"INSERT INTO sorteos_bolillero_registro
			// 															        	(
			// 															        		nro_boleto,
			// 																			id_cliente,
			// 																			id_sorteo,
			// 																			order_id,
			// 																			fecha_cliente_sorteo,
			// 																			id_creador,
			// 																			nombremodi_cliente_sorteo,
			// 																			fechamodi_cliente_sorteo,
			// 																			sucursal
			// 																		)
			// 																		VALUES
			// 																		(
			// 																			'".$nro_boleto."',
			// 																			'".$id_usuario."',
			// 																			'".$sorteo['id_sorteo']."',
			// 																			'".$id_cab."',
			// 																			'$fecha_ticket',
			// 																			'1',
			// 																			'Web Form',
			// 																			'$fecha_ticket',
			// 																			'".$sucursal."'
			// 																		)");
			//         	}
			//         }
		    //     }


			// 	$sub_total = $monto_compra;
		    // $array2 = "SELECT * FROM ".DB_CON.".fidelidad_cierre WHERE $w id_cliente= ".$_SESSION["uid"]." AND puntos !=0 AND puntos != puntos_usados order by cierre asc";
			// 	$query3 = mysqli_query($db,$array2);
			// 	if (mysqli_num_rows($query3) > 0) {
			// 		while ($row3=mysqli_fetch_array($query3)) {
			// 			if ($sub_total > ($row3['puntos'] - $row3['puntos_usados']) ) {
			// 				if ( ($row3['puntos'] - $row3['puntos_usados'])) {
			// 					$descontar = $row3['puntos'];
			// 					$sub_total = $sub_total - ($row3['puntos'] - $row3['puntos_usados']);
			// 				}else{
			// 					break;
			// 				}
			// 			}else if($sub_total <= 0){
			// 				break;
			// 			}else{
			// 				$descontar = $sub_total + $row3['puntos_usados'];
			// 				$sub_total = 0;
			// 			}
			// 			$id_cierre = $row3['id_cierre'];
			// 			$sql = "UPDATE ".DB_CON.".fidelidad_cierre set puntos_usados = $descontar WHERE id_cliente = '$id_usuario' and id_cierre = '$id_cierre'";
			//      		mysqli_query($db7,$sql);
			// 		}
			// 		if ($sub_total > 0) {
			// 			$sql = "UPDATE ".DB_CON.".fidelidad set punto_usado_fid = $sub_total WHERE id_cliente = '$id_usuario'";
			//      		mysqli_query($db7,$sql);
			// 		}
			// 	}else{
			// 		$array_fide = "SELECT * FROM ".DB_CON.".fidelidad WHERE id_cliente= ".$_SESSION["uid"]." AND punto_fid !=0";
			// 		$query_fide = mysqli_query($db,$array_fide);
			// 		if (mysqli_num_rows($query_fide) > 0) {
			// 			$row_fide=mysqli_fetch_array($query_fide);
			// 			$descontar_fide = $sub_total + $row_fide['punto_usado_fid'];
			// 		}
			// 		$sql_fide = "UPDATE ".DB_CON.".fidelidad set punto_usado_fid = $descontar_fide WHERE id_cliente = '$id_usuario'";
			//      	mysqli_query($db7,$sql_fide);
			// 	}
			// 	$_SESSION["total"] = $_SESSION["total"] - $monto_compra;
		    //  	$sql = "DELETE FROM cart WHERE user_id = '$id_usuario'";
		    //  	mysqli_query($db7,$sql);
		    //  	echo "	<div class='alert alert-success'>
			// 				<b>Operacion exitosa!</b>
			// 			</div>";
	    	// }else{
	    	// 	echo "	<div class='alert alert-danger'>
			// 				<b>Debe elegir un producto.</b>
			// 			</div>";
	    	// }
        
    } 
    catch(PDOException | Exception  $e){
        throw $e;
    }
});

$app->get('/clubcomodin/product/misPedidos/{id_cliente}', function(Request $request, Response $response){
	$idCliente = $request->getAttribute('id_cliente');    
	//TOKEN
	$token = $request->getAttribute("token");
	if ($token['aud'] === Aud()) {
		try
		{
			// Get DB Object
			$db = $this->db;
			$q="
					SELECT o.fecha_orden,o.orden_id,r.producto,r.precio_producto,pr.imagen_producto ,r.cantidad,o.estado
					FROM ".DB_MBO.".orden_cabecera o 
					join ".DB_MBO.".orders r on o.orden_id = r.orden_id
					join ".DB_MBO.".productos pr on r.id_producto = pr.id_producto
					WHERE o.id_cliente='$idCliente' AND pr.tipo != 2
					ORDER BY o.fecha_orden desc
			";
			$stmt = $db->query($q);
			$productos = $stmt->fetchAll(PDO::FETCH_OBJ);
			
			return $response->withJson(
							array(
									'status' => 'success', 
									'message' => '', 
									'data'=> [
										'pedidos' => $productos,
									]
								)
			); 
		} 
		catch(PDOException $e){
			throw $e;
		}
	}
	else{
		return $response->withJson(array("status" => "fail", "message" => "Acceso Denegado","data" => array()));
	}  
});

$app->get('/clubcomodin/product/misTickets/{id_cliente}', function(Request $request, Response $response){
	$idCliente = $request->getAttribute('id_cliente');    
	//TOKEN
	$token = $request->getAttribute("token");
	if ($token['aud'] === Aud()) {
		try
		{
			// Get DB Object
			$db = $this->db;

			$q="
					SELECT o.fecha_orden,o.orden_id,r.producto,r.precio_producto,pr.imagen_producto  ,r.cantidad,o.estado
					FROM ".DB_MBO.".orden_cabecera o 
					join ".DB_MBO.".orders r on o.orden_id = r.orden_id
					join ".DB_MBO.".productos pr on r.id_producto = pr.id_producto
					WHERE o.id_cliente='$idCliente'  AND pr.tipo = 2
					ORDER BY o.fecha_orden desc
			";
			$stmt = $db->query($q);
			$productos = $stmt->fetchAll(PDO::FETCH_OBJ);
		
			return $response->withJson(
							array(
									'status' => 'success', 
									'message' => '', 
									'data'=> [
										'tickets' => $productos,
									]
								)
			); 
		} 
		catch(PDOException $e){
			throw $e;
		}
	}
	else{
		return $response->withJson(array("status" => "fail", "message" => "Acceso Denegado","data" => array()));
	} 
});