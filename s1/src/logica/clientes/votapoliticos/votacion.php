<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Votapoliticos\Calificadores;
use Clases\Admin\Votapoliticos\Elecciones;
use \Clases\Admin\Votapoliticos\Votaciones;
$app->group('/votapoliticos', function(\Slim\App $app) {
    //Listar
    $app->get('/verPostulaciones/{id}', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $id = $args['id'];
            $listarProximoCalificadorFormacion = Elecciones::listarPostulaciones($db,$id);
            // $calificados = [];
            // if(isset($args['id']) && $id != ""){
            //     $calificados = Votaciones::getAllDataById($db ,$id,['f.id_auto']);
            //     $listarProximoCalificadorFormacion['calificados'] = $calificados ;
            // }
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

    //Agregar
    $app->post('/votaciones/add', function(Request $request, Response $response, array $args){
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
           
            foreach ($parametros['votos'] as $key => $value) {
                if($value == null) continue;           
                $newObj = [    
                    "fecha_votacion" =>  date("Y-m-d H:y:s"),
                    "id_eleccion" => $value['id_eleccion'],
                    "id_cliente" => $value['id_cliente'],
                    "id_cargo" => $value['id_cargo'],
                    "id_politico" => $value['id_politico'],
                    "apellido_politico" => $value['apellido_politico'],
                    "nombre_politico" => $value['nombre_politico'],
                    "imagen_politico" => $value['imagen_politico'],
                    "insert_local" => 1,
                    "id_creador" => 0,
                    "id_modificador" => 0,
                    "fecha_creador" =>  date("Y-m-d H:y:s"),
                    "fecha_modificador" => date("Y-m-d H:y:s"),
                    "id_elec_cargo" => $value['id_elec_cargo'],    
                    "posicion_politico" => $value['posicion_politico']    
                ];
                $claseVotacion->insertar($db,$newObj);
            }
        
            // if($idVotacion == 0){
            //     return $this->response->withJson([
            //         'code' => 100,
            //         'status' => 'error', 
            //         'message' => 'No se pudo completar la operación !.',
            //         'data' => []
            //     ]);
            // }      

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