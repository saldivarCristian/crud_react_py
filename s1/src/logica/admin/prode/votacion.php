<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Prode\Votaciones;

$app->group('/admin/prode/calificaciones', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){

        try{
            $db = $this->db;
            $list = Votaciones::getListCalification($db);
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
            $listPaises = \Clases\Admin\General\Paises::listar($db);
            $listLocal = \Clases\Admin\General\Locales::listar($db);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                    "paises"=>$listPaises, "locales"=>$listLocal
                ]
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });



});


?>