<?php
date_default_timezone_set(TIMEZONE);

use Clases\Admin\Desafios\Clubes;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Desafios\Encuentros;

$app->group('/admin/desafios/encuentros', function(\Slim\App $app) {
    //Listar
    $app->get('/list/{id}', function(Request $request, Response $response, array $args){
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
                "estado_actual_encuentro",
                "c.nombre_club local",
                "a.nombre_club visitante"
                
                
            ];
            $list = Encuentros::listar($db,$id,$columnas);
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
            $listClubes = \Clases\Admin\Desafios\Clubes::listar($db);
            $listTorneos = \Clases\Admin\Desafios\Torneos::listar($db);
            $listEstadios = \Clases\Admin\Desafios\Estadios::listar($db);
            $listLocal =[];
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                    "torneos"=>$listTorneos,
                     "locales"=>$listLocal, 
                     "estadios"=>$listEstadios, 
                     "clubes"=>$listClubes
                ]
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //Listar select
    $app->get('/listSelectById/{id}', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $eleccionCargo = \Clases\Admin\Desafios\Encuentros::getDataById($db,$args['id']);
            $listParticipantes = \Clases\Admin\Desafios\Clientes::listarPorCargo($db,$args['id']);
            $seleccionParticipantes = \Clases\Admin\Desafios\Encuentros::listFormacionById($db,$args['id']);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                     "Encuentros"=>$eleccionCargo,
                     "seleccionParticipantes"=>$seleccionParticipantes,
                     "politicos"=>$listParticipantes,
                     
                ]
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
            $claseUsuario = new Encuentros;
            $parametros = $request->getParsedBody();
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            $clubes = new Clubes();
            $nombreLocal = $clubes->getDataById($db,$parametros['id_club_local'],["nombre_club"]);
            $nombreVisitante = $clubes->getDataById($db,$parametros['id_club_visitante'],["nombre_club"]);
            $newObj = [    
                "cancha_encuentro"=> $parametros['cancha_encuentro'],
                "dia_encuentro"=> $parametros['dia_encuentro'],
                "estado_encuentro"=> $parametros['estado_encuentro'],
                "fecha_encuentro"=> $parametros['fecha_encuentro'],
                "hora_encuentro"=> $parametros['hora_encuentro'],
                "id_club_local" => $parametros['id_club_local'],
                "id_estadio" => $parametros['id_estadio'],
                "id_torneo" => $parametros['id_torneo'],
                "id_desafio" => $parametros['id_desafio'],
                "id_club_visitante" => $parametros['id_club_visitante'],
                "nombre_club_visitante" => $nombreVisitante->nombre_club,
                "nombre_club_local" => $nombreLocal->nombre_club,
                "fecha_creador" => date("Y-m-d H:y:s"),
            ];
            $idUsuario = $claseUsuario->insertar($db,$newObj);
        
            if($idUsuario == 0){
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

    //listar para editar
    $app->get('/edit/{id}', function($request, $response, $args){
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
            $datos = Encuentros::getDataById($db ,$id);
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

    //Editar
    $app->post('/update/{id}', function($request, $response, $args){
        try{
            $db = $this->db;
            $claseUsuario = new Encuentros( $this->logger );
            $parametros = $request->getParsedBody();
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }
            $clubes = new Clubes();
            $nombreLocal = $clubes->getDataById($db,$parametros['id_club_local'],["nombre_club"]);
            $nombreVisitante = $clubes->getDataById($db,$parametros['id_club_visitante'],["nombre_club"]);
            
            $newObj = [    
                "cancha_encuentro"=> $parametros['cancha_encuentro'],
                "dia_encuentro"=> $parametros['dia_encuentro'],
                "estado_encuentro"=> $parametros['estado_encuentro'],
                "fecha_encuentro"=> $parametros['fecha_encuentro'],
                "hora_encuentro"=> $parametros['hora_encuentro'],
                "id_club_local" => $parametros['id_club_local'],
                "id_estadio" => $parametros['id_estadio'],
                "id_torneo" => $parametros['id_torneo'],
                "id_desafio" => $parametros['id_desafio'],
                "id_club_visitante" => $parametros['id_club_visitante'],
                "fecha_modificador" => date("Y-m-d H:y:s"),
                "nombre_club_visitante" => $nombreVisitante->nombre_club,
                "nombre_club_local" => $nombreLocal->nombre_club


            ];
            
            $id = $args['id'];
            $idUsuario = $claseUsuario->actualizar($db,$newObj,$id);
            if($idUsuario == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => [$idUsuario]
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

    //Eliminar
    $app->delete('/delete/{id}', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            if (!isset($args['id']) ) {
                return json_encode( array( 
                    'code' => 404, 
                    'status' => 'fail', 
                    'message' => 'Parametros incompletos',
                    'data' =>  $request
                ) );
            }

            $id = $args['id'];
            $claseUsuario = new Encuentros;
            $claseUsuario->eliminar($db,$id);
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

            //Estado del encuentro
            $app->post('/updateEstado/{id}', function($request, $response, $args){
                try{
                    $db = $this->db;
                    $claseUsuario = new Encuentros( $this->logger );
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
                        "goles_locales_encuentro" => $parametros['goles_locales_encuentro'],
                        "goles_visitantes_encuentro" => $parametros['goles_visitantes_encuentro'],
                        "ganador_encuentro" => $parametros['ganador_encuentro'],
                        "id_creador" => $parametros['id_creador'],
                        "id_modificador" => $parametros['id_modificador'],
                        "fecha_modificador" => date("Y-m-d H:y:s"),
        
        
                    ];
                    $id = $args['id'];
                    $idUsuario = $claseUsuario->actualizar($db,$newObj,$id);
                    if($idUsuario == 0){
                        return $this->response->withJson([
                            'code' => 100,
                            'status' => 'error', 
                            'message' => 'No se pudo completar la operación !.',
                            'data' => [$idUsuario]
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

});


?>
