<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Calautos\Calificadores;

$app->group('/admin/calautos/calificador', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = Calificadores::listar($db);
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
            $listLocal =[];
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                     "locales"=>$listLocal, 

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
            $calificador = \Clases\Admin\Calautos\Calificadores::getDataById($db,$args['id']);
            $listFormaciones = \Clases\Admin\Calautos\Formaciones::listar($db);
            $listAutos = \Clases\Admin\Calautos\Autos::listar($db);
            $seleccionAutos = \Clases\Admin\Calautos\Calificadores::listFormacionById($db,$args['id']);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                     "calificador"=>$calificador,
                     "seleccionAutos"=>$seleccionAutos,
                     "formaciones"=>$listFormaciones,
                     "autos"=>$listAutos
                ]
            ]);
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    //Agregar formacion
    $app->post('/addFormacion', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $claseUsuario = new Calificadores;
            $parametros = $request->getParsedBody();
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            $id = $parametros['id_calificador'];
            $id_formacion = $parametros['id_formacion'];
            $rowFormacion = $parametros['dataAutos'];

            $claseUsuario->eliminarSeleccionFormacion($db,$id);
            
            foreach ($rowFormacion as $key => $value) {                  
                $newObj = [    
                    "id_calificador" => $id,
                    "orden_seleccion_auto" => $value['orden'],
                    "id_auto" => $value['id_auto'],
                    "nombre_auto" => $value['nombre'],
                    "imagen_auto" => $value['imagen_auto'],
                    "clase" => $value['id']
                ];
                $idUsuario = $claseUsuario->insertarSeleccionAuto($db,$newObj);
            }

            $newObj = [    
                "id_formacion" => $id_formacion
            ];
            $idUsuario = $claseUsuario->actualizar($db,$newObj,$id);
        
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
    //Agregar
    $app->post('/add', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $claseUsuario = new Calificadores;
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
                "nombre_calificador" => $parametros['nombre_calificador'],
                "dia_calificador" => $parametros['dia_calificador'],
                "hora_calificador" => $parametros['hora_calificador'],
                "estado_calificador"=> $parametros['estado_calificador'],
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
            $datos = Calificadores::getDataById($db ,$id);
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
            $claseUsuario = new Calificadores( $this->logger );
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
                "nombre_calificador" => $parametros['nombre_calificador'],
                "dia_calificador" => $parametros['dia_calificador'],
                "hora_calificador" => $parametros['hora_calificador'],
                "estado_calificador"=> $parametros['estado_calificador'],
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

        //Estado del encuentro
        $app->post('/updateEstado/{id}', function($request, $response, $args){
            try{
                $db = $this->db;
                $claseUsuario = new Calificadores( $this->logger );
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
                    "estado_actual_calificador" => $parametros['estado_actual_calificador'],
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
            $claseUsuario = new Calificadores;
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

});


?>
