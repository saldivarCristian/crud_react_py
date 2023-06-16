<?php
date_default_timezone_set(TIMEZONE);

use Clases\Admin\Desafios\Clubes;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Desafios\Encuentros;

$app->group('/clientes/desafios/encuentros', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $columnas = [
                "id_encuentro",
                "dia_encuentro",
                "hora_encuentro",
                "goles_locales_encuentro",
                "goles_visitantes_encuentro",
                "estado_actual_encuentro",
                "c.nombre_club local",
                "a.nombre_club visitante"
                
                
            ];
            $list = Encuentros::listar($db,$columnas);
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


?>
