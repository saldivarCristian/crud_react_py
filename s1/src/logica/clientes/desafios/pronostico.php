<?php
date_default_timezone_set(TIMEZONE);

use Clases\Admin\Desafios\Encuentros;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Desafios\Pronosticos;
use \Clases\Admin\Desafios\Grupos;
use \Clases\Admin\Desafios\Desafios;

$app->group('/desafios/pronostico', function(\Slim\App $app) {

    //Agregar
    $app->post('/add', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $clasePronostico = new Pronosticos;
            $claseDesafio = new Desafios;

            $parametros = $request->getParsedBody();
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }
            $token = $request->getAttribute("token");
            $id_cliente = $token['id_cliente'];
            $idPronostico = 0;
            $ban = 0;
            foreach ($parametros['pronostico'] as $key => $value) {
                if($ban == 0){
                    $ban++;
                    $getDefafio = $claseDesafio->getDataById($db,$value['id_desafio'],['estado_desafio']);
                    if($getDefafio){
                        if($getDefafio->estado_desafio != '1'){
                            return $this->response->withJson([
                                'code' => 100,
                                'status' => 'error', 
                                'message' => 'El desafio ya ha culminado!.',
                                'data' => []
                            ]);
                        }
                    }else{
                        return $this->response->withJson([
                            'code' => 100,
                            'status' => 'error', 
                            'message' => 'El desafio no existe!.',
                            'data' => []
                        ]);
                    }
                }
                $newObj = [    
                    "id_encuentro" => $value['id_encuentro'],
                    "id_desafio" => $value['id_desafio'],
                    "id_cliente" => $id_cliente,
                    "ganador_pronostico" => $value['pronostico'],
                    "id_grupo" => $value['id_grupo'],
                    "id_club" => $value['id_club'],
                    "fecha_pronotico" => date("Y-m-d H:y:s"),
                ];
                $idPronostico = $clasePronostico->insertar($db,$newObj);
            }
        
            if($idPronostico == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => $parametros
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

    //Agregar
    $app->post('/verResultados', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $clasePronostico = new Pronosticos;
            $parametros = $request->getParsedBody();
           
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            $token = $request->getAttribute("token");
            // $id_cliente = $token['id_cliente'];
            $idDesafio =  $parametros['id_desafio'];
            $idGrupo =  $parametros['id_grupo'];
            $resultado = $clasePronostico->verRestultado($db,$idGrupo,$idDesafio);
            $getGrupo = Grupos::getDataById($db,$idGrupo);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => 'Operación exitosa!.',
                'data' =>['resultado' => $resultado,'grupo'=> $getGrupo]
            ]);
            
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //Agregar
    $app->post('/verMiResultado', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $clasePronostico = new Pronosticos;
            $parametros = $request->getParsedBody();
           
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            $token = $request->getAttribute("token");
            // $id_cliente = $token['id_cliente'];
            $idDesafio =  $parametros['id_desafio'];
            $idGrupo =  $parametros['id_grupo'];
            $idCliente =  $parametros['id_cliente'];
            $resultado = $clasePronostico->verMiResultado($db,$idCliente,$idGrupo,$idDesafio);
            $getGrupo = Grupos::getDataById($db,$idGrupo);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => 'Operación exitosa!.',
                'data' =>['resultado' => $resultado,'grupo'=> $getGrupo]
            ]);
            
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //Agregar
    $app->post('/verResultadoFinal', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $clasePronostico = new Pronosticos;
            $parametros = $request->getParsedBody();
           
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            $token = $request->getAttribute("token");
            // $id_cliente = $token['id_cliente'];
            $idDesafio =  $parametros['id_desafio'];
            $idGrupo =  $parametros['id_grupo'];
            $resultado = $clasePronostico->verRestultadoFinal($db,$idDesafio);
            // $getGrupo = Grupos::getDataById($db,$idGrupo);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => 'Operación exitosa!.',
                'data' =>['resultado' => $resultado]
            ]);
            
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    $app->get('/listResultadosEncuentros/{id}', function(Request $request, Response $response, array $args){
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
            $columnas = [
                "id_encuentro",
                "dia_encuentro",
                "hora_encuentro",
                "goles_locales_encuentro",
                "goles_visitantes_encuentro",
                "ganador_encuentro",
                "estado_actual_encuentro",
                "c.nombre_club local",
                "a.nombre_club visitante"
                
                
            ];
            $list = Encuentros::listar($db,$id,$columnas);
            $getDesafio = Desafios::getDataById($db,$id);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => ['encuentro'=> $list,'desafio'=> $getDesafio]
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });
    
    $app->post('/listMiPronostico', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $parametros = $request->getParsedBody();
           
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }
            $idGrupo = $parametros['id_grupo'];
            $token = $request->getAttribute("token");
            $idCliente = $token['id_cliente'];
            $idDesafio =  $parametros['id_desafio'];
            $list = Pronosticos::verMiPronostico($db,$idGrupo,$idDesafio,$idCliente);
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
