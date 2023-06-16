<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Calautos\Votaciones;

$app->group('/calautos/votaciones', function(\Slim\App $app) {
    //Listar calificaciones generales
    $app->get('/list/{id}', function(Request $request, Response $response, array $args){

        try{
            $db = $this->db;
            $id = $args['id'];
            $list = Votaciones::getListCalification($db,$id);
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
    
    //Listar calificaciones por encuentro
    $app->get('/listEncuentro', function(Request $request, Response $response, array $args){

        try{
            $db = $this->db;
            $list = Votaciones::getListCalificationEncuetro($db);
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
    
    

    //Agregar
    $app->post('/add', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $claseVotacion = new Votaciones;
            $parametros = $request->getParsedBody();
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            
            $newObj = [    
                "id_calificador" => $parametros['id_calificador'],
                // "id_formacion" => $parametros['id_formacion'],
                "id_cliente" => $parametros['id_cliente'],
                // "tipo_cliente" => $parametros['tipo_cliente'],
                // "insert_local" => $parametros['insert_local'],
                // "fecha_creador" => date("Y-m-d H:y:s"),
                "calificacion_votacion" => $parametros['calificacion_votacion'],
                "id_auto" => $parametros['id_auto'],
                "fecha_votacion" => date("Y-m-d H:y:s"),

            ];
            $idVotacion = $claseVotacion->insertar($db,$newObj);
        
            if($idVotacion == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => []
                ]);
            }      

            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => 'Operación exitosa!.',
                'data' => []
            ]);
            
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //lista calificaciones generales por promedio
    $app->get('/getCalificacionCliente/{id}', function($request, $response, $args){
        try{
            $db = $this->db;
            if (!isset($args['id']) ) {
                return json_encode( array( 
                    'code' => 404, 
                    'status' => 'fail', 
                    'message' => 'Parametros incompletos',
                    'data' =>  []
                ) );
            }
            $id = $args['id'];
            $datos = Votaciones::getListCalification($db ,"",$id);
            if($datos){
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'success', 
                    'message' => '',
                    'data' => $datos
                ]);
            }else{
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'fail', 
                    'message' => 'Dato no encontrado.',
                    'data' => $datos
                ]);
            }
            
        } catch(PDOException $e)
        {
            throw $e;
        }
    });
        //Listar INFORMES DE CALIFICACION
        $app->get('/getInformes/{id}', function ($request, $response, $args) {
            try {
                $db = $this->db;
                if (!isset($args['id'])) {
                    return json_encode(array(
                        'code' => 404,
                        'status' => 'fail',
                        'message' => 'Parametros incompletos',
                        'data' =>  []
                    ));
                }
                $id = $args['id'];
                $datos = Votaciones::getInformesConFiltro($db, $id);
                if ($datos) {
                    return $this->response->withJson([
                        'code' => 200,
                        'status' => 'success',
                        'message' => '',
                        'data' => $datos
                    ]);
                } else {
                    return $this->response->withJson([
                        'code' => 200,
                        'status' => 'fail',
                        'message' => 'Dato no encontrado.',
                        'data' => $datos
                    ]);
                }
            } catch (PDOException $e) {
                throw $e;
            }
        });   



    //Listar calificacion por cliente
    $app->get('/getCalificacion/{id}', function($request, $response, $args){
        try{
            $db = $this->db;
            if (!isset($args['id']) ) {
                return json_encode( array( 
                    'code' => 404, 
                    'status' => 'fail', 
                    'message' => 'Parametros incompletos',
                    'data' =>  []
                ) );
            }
            $id = $args['id'];
            $datos = Votaciones::getAllDataById($db ,$id);
            if($datos){
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'success', 
                    'message' => '',
                    'data' => $datos
                ]);
            }else{
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'fail', 
                    'message' => 'Dato no encontrado.',
                    'data' => $datos
                ]);
            }
            
        } catch(PDOException $e)
        {
            throw $e;
        }
    });   
 

});



?>
