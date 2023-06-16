<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Desafios\Desafios;
use \Clases\Admin\Desafios\Grupos;

$app->group('/clientes/desafios', function(\Slim\App $app) {
    //Listar
    $app->get('/list/{idGrupo}', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $id_grupo = $args['idGrupo'];
            $list = Desafios::listar($db);
            $getGrupo = Grupos::getDataById($db,$id_grupo);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => ['encuentros'=>$list,'grupo'=>$getGrupo]
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });
});


?>
