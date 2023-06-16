<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Fanaticos\Encuentros;
use \Clases\Admin\Fanaticos\Votaciones;
$app->group('/fanatico', function(\Slim\App $app) {
    //Listar
    $app->get('/verEncuentro/{id}', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $listarProximoEncuentroFormacion = Encuentros::listarProximoEncuentroFormacion($db);
            $id = $args['id'];
            $calificados = [];
            if(isset($args['id']) && $id != ""){
                $idEncuentro= $listarProximoEncuentroFormacion['encuentro']->id_encuentro ?? 0;
                $calificados = Votaciones::getVotosById($db ,$id,$idEncuentro,['f.id_futbolista']);
                $listarProximoEncuentroFormacion['calificados'] = $calificados ;
            }
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => $listarProximoEncuentroFormacion
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });
});