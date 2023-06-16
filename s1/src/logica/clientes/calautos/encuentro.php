<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Calautos\Calificadores;
use \Clases\Admin\Calautos\Votaciones;
$app->group('/calautos', function(\Slim\App $app) {
    //Listar
    $app->get('/verEncuentro/{id}', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $listarProximoCalificadorFormacion = Calificadores::listarProximoCalificadorFormacion($db);
            $id = $args['id'];
            $calificados = [];
            if(isset($args['id']) && $id != ""){
                $calificados = Votaciones::getAllDataById($db ,$id,['f.id_auto']);
                $listarProximoCalificadorFormacion['calificados'] = $calificados ;
            }
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => $listarProximoCalificadorFormacion
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });
});