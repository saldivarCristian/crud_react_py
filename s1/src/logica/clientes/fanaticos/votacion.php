<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Fanaticos\Votaciones;

$app->group('/fanatico/votaciones', function(\Slim\App $app) {
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
    
    //Listar calificaciones por encuentro
      $app->get('/listEncuentroGroup', function(Request $request, Response $response, array $args){

        try{
            $db = $this->db;
            $list = Votaciones::getListEncuentroGroup($db);
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

            $validarAdd = $claseVotacion->validateDataById($db , $parametros['id_cliente'], $parametros['id_encuentro'], $parametros['id_futbolista'] );

            if( $validarAdd ){
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Operación exitosa!.',
                    'data' => []
                ]);
            }

            
            $newObj = [    
                "id_encuentro" => $parametros['id_encuentro'],
                // "id_formacion" => $parametros['id_formacion'],
                "id_cliente" => $parametros['id_cliente'],
                // "tipo_cliente" => $parametros['tipo_cliente'],
                // "insert_local" => $parametros['insert_local'],
                // "fecha_creador" => date("Y-m-d H:y:s"),
                "calificacion_votacion" => $parametros['calificacion_votacion'],
                "id_futbolista" => $parametros['id_futbolista'],
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
            $torneo = $_GET['torneo'] ?? "";
            $encuentro = $_GET['encuentro'] ?? "";
            $datos = Votaciones::getInformesConFiltro($db, $id,$torneo,$encuentro);
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
 

});



?>
