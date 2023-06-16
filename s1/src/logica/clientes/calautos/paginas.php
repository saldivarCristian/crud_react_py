<?php

use Slim\Http\Request;
use Slim\Http\Response;
$app->group('/calautos', function(\Slim\App $app) {
    // las configuraciones que vienen del admin
    $app->get('/paginas/configuraciones', function(Request $request, Response $response){
        try
        {
            $settings = $this->get('settings');
            return $response->withJson(
                            array(
                                    'status' => 'success', 
                                    'message' => '', 
                                    'data'=> URLS
                                )
            ); 
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    // tutoriales del juego
    $app->get('/clubcomodin/paginas/tutoriales', function(Request $request, Response $response){
        try
        {
            // Get DB Object
            $db = $this->db;

            $array = "SELECT  * FROM ".DB_MBO.".videos_tutoriales where estado = 1 ORDER BY orden asc ";
            $stmt = $db->query($array);
            $dato = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = NULL;
            return $response->withJson(
                            array(
                                    'status' => 'success', 
                                    'message' => '', 
                                    'data'=> $dato 
                                )
            ); 
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    // formulario de socios para influencer
    $app->post('/clubcomodin/paginas/addInfluencer', function(Request $request, Response $response){
        try
        {
            $tipo_socio = 2;
            $nombre_socio = $request->getParam('name');
            $apellido_socio = $request->getParam('last_name');
            $identificacion_socio = $request->getParam('identification');
            $perfil_inf_socio = $request->getParam('influencer_profile');
            $telefono_socio = $request->getParam('telephone');
            $email_socio = $request->getParam('email');
            $ciudad_socio = $request->getParam('city');
            $rubro_local_socio = "";
            $nombre_local_socio = "";
            $fecha_socio = date("Y-m-d H:i:s");

            // Get DB Object
            $db = $this->db;

            $sql = "INSERT INTO ".DB_MBO.".socios 
                (
                    tipo_socio,
                    nombre_socio,
                    apellido_socio,
                    identificacion_socio,
                    telefono_socio,
                    email_socio,
                    ciudad_socio,
                    rubro_local_socio,
                    nombre_local_socio,
                    perfil_inf_socio,
                    fecha_socio
                )
                VALUES 
                (
                    :tipo_socio,
                    :nombre_socio,
                    :apellido_socio,
                    :identificacion_socio,
                    :telefono_socio,
                    :email_socio,
                    :ciudad_socio,
                    :rubro_local_socio,
                    :nombre_local_socio,
                    :perfil_inf_socio,
                    '$fecha_socio'
            )";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':tipo_socio', $tipo_socio);
            $stmt->bindParam(':nombre_socio', $nombre_socio);
            $stmt->bindParam(':apellido_socio', $apellido_socio);
            $stmt->bindParam(':identificacion_socio', $identificacion_socio);
            $stmt->bindParam(':perfil_inf_socio', $perfil_inf_socio);
            $stmt->bindParam(':telefono_socio', $telefono_socio);
            $stmt->bindParam(':email_socio', $email_socio);
            $stmt->bindParam(':ciudad_socio', $ciudad_socio);
            $stmt->bindParam(':rubro_local_socio', $rubro_local_socio);
            $stmt->bindParam(':nombre_local_socio', $nombre_local_socio);

            $stmt->execute();
            // $idCliente = $db->lastInsertId();

            $title = '📌 Nuevos Socios';
            $from = ['developer@proinso.sa.com' => 'Soporte Comodin'];
            $to = ['nynbritez@gmail.com'];
            $msj = "
                Hay una nueva solitud de socio. Verificar <a href='https://mbokaja.net'>Aqui</a>
            ";
            $body = ServidorEmail::FormatoMensaje($msj);
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);

            return $response->withJson( 
                array("status"=> "success", "message" => "Operación exitosa.",  "data" => array())
            );
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    // formulario de socios para representantes
    $app->post('/clubcomodin/paginas/addRepresentante', function(Request $request, Response $response){
        try
        {
            $tipo_socio = 3;
            $nombre_socio = $request->getParam('name');
            $apellido_socio = $request->getParam('last_name');
            $identificacion_socio = $request->getParam('identification');
            $perfil_inf_socio = "";
            $telefono_socio = $request->getParam('telephone');
            $email_socio = $request->getParam('email');
            $ciudad_socio = $request->getParam('city');
            $rubro_local_socio = "";
            $nombre_local_socio = "";
            $fecha_socio = date("Y-m-d H:i:s");


            // Get DB Object
            $db = $this->db;

            $sql = "INSERT INTO ".DB_MBO.".socios 
                (
                    tipo_socio,
                    nombre_socio,
                    apellido_socio,
                    identificacion_socio,
                    telefono_socio,
                    email_socio,
                    ciudad_socio,
                    rubro_local_socio,
                    nombre_local_socio,
                    perfil_inf_socio,
                    fecha_socio
                )
                VALUES 
                (
                    :tipo_socio,
                    :nombre_socio,
                    :apellido_socio,
                    :identificacion_socio,
                    :telefono_socio,
                    :email_socio,
                    :ciudad_socio,
                    :rubro_local_socio,
                    :nombre_local_socio,
                    :perfil_inf_socio,
                    '$fecha_socio'
            )";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':tipo_socio', $tipo_socio);
            $stmt->bindParam(':nombre_socio', $nombre_socio);
            $stmt->bindParam(':apellido_socio', $apellido_socio);
            $stmt->bindParam(':identificacion_socio', $identificacion_socio);
            $stmt->bindParam(':perfil_inf_socio', $perfil_inf_socio);
            $stmt->bindParam(':telefono_socio', $telefono_socio);
            $stmt->bindParam(':email_socio', $email_socio);
            $stmt->bindParam(':ciudad_socio', $ciudad_socio);
            $stmt->bindParam(':rubro_local_socio', $rubro_local_socio);
            $stmt->bindParam(':nombre_local_socio', $nombre_local_socio);

            $stmt->execute();
            // $idCliente = $db->lastInsertId();

            $title = '📌 Nuevos Socios';
            $from = ['developer@proinso.sa.com' => 'Soporte Comodin'];
            $to = ['nynbritez@gmail.com'];
            $msj = "
                Hay una nueva solitud de socio. Verificar <a href='https://mbokaja.net'>Aqui</a>
            ";
            $body = ServidorEmail::FormatoMensaje($msj);
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);
            return $response->withJson( 
                array("status"=> "success", "message" => "Operación exitosa.",  "data" => array())
            );
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    // formulario de socios para local
    $app->post('/clubcomodin/paginas/addLocal', function(Request $request, Response $response){
        try
        {
            $tipo_socio = 1;
            $nombre_socio = $request->getParam('name');
            $apellido_socio = $request->getParam('last_name');
            $identificacion_socio = $request->getParam('identification');
            $perfil_inf_socio = "";
            $telefono_socio = $request->getParam('telephone');
            $email_socio = $request->getParam('email');
            $ciudad_socio = $request->getParam('city');
            $rubro_local_socio = $request->getParam('heading');
            $nombre_local_socio = $request->getParam('name_local');
            $fecha_socio = date("Y-m-d H:i:s");


            // Get DB Object
            $db = $this->db;

            $sql = "INSERT INTO ".DB_MBO.".socios 
                (
                    tipo_socio,
                    nombre_socio,
                    apellido_socio,
                    identificacion_socio,
                    telefono_socio,
                    email_socio,
                    ciudad_socio,
                    rubro_local_socio,
                    nombre_local_socio,
                    perfil_inf_socio,
                    fecha_socio
                )
                VALUES 
                (
                    :tipo_socio,
                    :nombre_socio,
                    :apellido_socio,
                    :identificacion_socio,
                    :telefono_socio,
                    :email_socio,
                    :ciudad_socio,
                    :rubro_local_socio,
                    :nombre_local_socio,
                    :perfil_inf_socio,
                    '$fecha_socio'
            )";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':tipo_socio', $tipo_socio);
            $stmt->bindParam(':nombre_socio', $nombre_socio);
            $stmt->bindParam(':apellido_socio', $apellido_socio);
            $stmt->bindParam(':identificacion_socio', $identificacion_socio);
            $stmt->bindParam(':perfil_inf_socio', $perfil_inf_socio);
            $stmt->bindParam(':telefono_socio', $telefono_socio);
            $stmt->bindParam(':email_socio', $email_socio);
            $stmt->bindParam(':ciudad_socio', $ciudad_socio);
            $stmt->bindParam(':rubro_local_socio', $rubro_local_socio);
            $stmt->bindParam(':nombre_local_socio', $nombre_local_socio);

            $stmt->execute();
            // $idCliente = $db->lastInsertId();

            $title = '📌 Nuevos Socios';
            $from = ['developer@proinso.sa.com' => 'Soporte Comodin'];
            $to = ['nynbritez@gmail.com'];
            $msj = "
                Hay una nueva solitud de socio. Verificar <a href='https://mbokaja.net'>Aqui</a>
            ";
            $body = ServidorEmail::FormatoMensaje($msj);
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);
            return $response->withJson( 
                array("status"=> "success", "message" => "Operación exitosa.",  "data" => array())
            );
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    // formulario para contactar
    $app->post('/clubcomodin/paginas/addContacto', function(Request $request, Response $response){
        try
        {
            $tipo_socio = 4;
            $nombre_socio = $request->getParam('name');
            $apellido_socio = $request->getParam('last_name');
            $identificacion_socio ="";
            $perfil_inf_socio = "";
            $telefono_socio = "";
            $email_socio = $request->getParam('email');
            $mensaje_socio = $request->getParam('message');
            $ciudad_socio = "";
            $rubro_local_socio = "";
            $nombre_local_socio = "";
            $fecha_socio = date("Y-m-d H:i:s");


            // Get DB Object
            $db = $this->db;

            $sql = "INSERT INTO ".DB_MBO.".socios 
                (
                    tipo_socio,
                    nombre_socio,
                    apellido_socio,
                    identificacion_socio,
                    telefono_socio,
                    email_socio,
                    ciudad_socio,
                    rubro_local_socio,
                    nombre_local_socio,
                    perfil_inf_socio,
                    mensaje_socio,
                    fecha_socio
                )
                VALUES 
                (
                    :tipo_socio,
                    :nombre_socio,
                    :apellido_socio,
                    :identificacion_socio,
                    :telefono_socio,
                    :email_socio,
                    :ciudad_socio,
                    :rubro_local_socio,
                    :nombre_local_socio,
                    :perfil_inf_socio,
                    :mensaje_socio,
                    '$fecha_socio'
            )";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':tipo_socio', $tipo_socio);
            $stmt->bindParam(':nombre_socio', $nombre_socio);
            $stmt->bindParam(':apellido_socio', $apellido_socio);
            $stmt->bindParam(':identificacion_socio', $identificacion_socio);
            $stmt->bindParam(':perfil_inf_socio', $perfil_inf_socio);
            $stmt->bindParam(':telefono_socio', $telefono_socio);
            $stmt->bindParam(':email_socio', $email_socio);
            $stmt->bindParam(':ciudad_socio', $ciudad_socio);
            $stmt->bindParam(':rubro_local_socio', $rubro_local_socio);
            $stmt->bindParam(':mensaje_socio', $mensaje_socio);
            $stmt->bindParam(':nombre_local_socio', $nombre_local_socio);

            $stmt->execute();
            // $idCliente = $db->lastInsertId();

            $title = '📌 Nuevos Socios';
            $from = ['developer@proinso.sa.com' => 'Soporte Comodin'];
            $to = ['nynbritez@gmail.com'];
            $msj = "
                Hay una nueva solitud de socio. Verificar <a href='https://mbokaja.net'>Aqui</a>
            ";
            $body = ServidorEmail::FormatoMensaje($msj);
            $SendMail = new SendMail();
            $SendMail->Send($title,$from,$to,$body);
            return $response->withJson( 
                array("status"=> "success", "message" => "Operación exitosa.",  "data" => array())
            );
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    // ofertas para promociones
    $app->get('/clubcomodin/paginas/ofertas', function(Request $request, Response $response){
        try
        {
            // Get DB Object
            $db = $this->db;
            $fecha = date('Y-m-d H:i:s');
            $query = "UPDATE ".DB_MBO.".comodin_promociones SET estado_comodin_promo = 2 WHERE estado_comodin_promo != 3 AND fin_comodin_promo <= '$fecha' ";
            $stmt = $db->query($query);

            $sql = "SELECT img_comodin_promo,btn_comodin_promo,link_comodin_promo 
                FROM ".DB_MBO.".comodin_promociones 
                WHERE  estado_comodin_promo = 0 AND inicio_comodin_promo <= '$fecha' AND fin_comodin_promo >= '$fecha'
            ";
            $stmt = $db->query($sql);
            $dato = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = NULL;
            return $response->withJson(
                            array(
                                    'status' => 'success', 
                                    'message' => '', 
                                    'data'=> $dato 
                                )
            ); 
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    // ofertas para promociones
    $app->get('/clubcomodin/paginas/noticias', function(Request $request, Response $response){
        try
        {
            // Get DB Object
            $db = $this->db;
            $fecha = date('Y-m-d H:i:s');
            $sql = "
                SELECT c.id_noticia,c.nombre_noticia,c.desc_corta_noticia,c.desc_larga_noticia,pf2.nombre,pf2.orden
                FROM ".DB_MBO.".comodin_noticias c 
                LEFT JOIN ( 
                    SELECT pf.id_noticia, pf.nombre, pf.orden 
                    FROM ".DB_MBO.".comodin_noticias_img pf
                    WHERE pf.orden = (SELECT MIN(pf3.orden) FROM ".DB_MBO.".comodin_noticias_img pf3 WHERE pf3.id_noticia = pf.id_noticia)
                    GROUP BY pf.id_noticia ORDER BY pf.orden ASC
                ) pf2 ON pf2.id_noticia = c.id_noticia
            ";
            $stmt = $db->query($sql);
            $dato = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = NULL;
            return $response->withJson(
                            array(
                                    'status' => 'success', 
                                    'message' => '', 
                                    'data'=> $dato 
                                )
            );
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

    // ofertas para promociones
    $app->get('/clubcomodin/paginas/noticias/{id}', function(Request $request, Response $response){
        try
        {
            // Get DB Object
            $db = $this->db;
            $id = $request->getAttribute('id');
            $sql = "
                SELECT *
                FROM ".DB_MBO.".comodin_noticias  
                WHERE id_noticia = $id
            ";
            $stmt = $db->query($sql);
            $datoC = $stmt->fetchAll(PDO::FETCH_OBJ);

            $sql = "
                SELECT *
                FROM ".DB_MBO.".comodin_noticias_img 
                WHERE id_noticia = $id
            ";

            $stmt = $db->query($sql);
            $datoD = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = NULL;

            return $response->withJson(
                            array(
                                    'status' => 'success', 
                                    'message' => '', 
                                    'data'=> [
                                        'cabecera' => $datoC,
                                        'detalle' => $datoD
                                    ] 
                                )
            ); 
            
        } 
        catch(PDOException $e){
            throw $e;
        } 
    });

});