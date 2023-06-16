<?php

use Slim\Http\Request;
use Slim\Http\Response;
$app->group('/desafio', function(\Slim\App $app) {
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

});