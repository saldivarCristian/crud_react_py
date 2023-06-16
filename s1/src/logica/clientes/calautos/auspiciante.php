<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Calautos\Auspiciantes1;
use \Clases\Admin\Calautos\Auspiciantes2;
use \Clases\Admin\Calautos\Auspiciantes3;
use \Clases\Admin\Calautos\AuspiciantesPrem;
$app->group('/calautos', function(\Slim\App $app) {
    //Listar
    $app->get('/verAuspiciantes1', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = Auspiciantes1::listarPorEstado($db);
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
});

$app->group('/calautos', function(\Slim\App $app) {
    //Listar
    $app->get('/verAuspiciantes2', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = Auspiciantes2::listarPorEstado($db);
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
});
$app->group('/calautos', function(\Slim\App $app) {
    //Listar
    $app->get('/verAuspiciantes3', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = Auspiciantes3::listarPorEstado($db);
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
});
$app->group('/calautos', function(\Slim\App $app) {
    //Listar
    $app->get('/verAuspiciantesPrem', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = AuspiciantesPrem::listarPorEstado($db);
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
});