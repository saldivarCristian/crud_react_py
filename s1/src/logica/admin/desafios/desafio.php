<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Desafios\Desafios;

$app->group('/admin/desafios/desafios', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = Desafios::listar($db);
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
            $listCargo = \Clases\Admin\Desafios\Cargos::listar($db);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                     "locales"=>$listLocal,
                     "cargos"=>$listCargo 

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
            $eleccion = \Clases\Admin\Desafios\Desafios::getDataById($db,$args['id']);
            $listPoliticos = \Clases\Admin\Desafios\Politicos::listarPorCargo($db,$args['id']);
            $seleccionPoliticos = \Clases\Admin\Desafios\Desafios::listFormacionById($db,$args['id']);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                     "elecciones"=>$eleccion,
                     "seleccionPoliticos"=>$seleccionPoliticos,
                     "politicos"=>$listPoliticos,
                     
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
            $claseUsuario = new Desafios;
            $parametros = $request->getParsedBody();
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            $id = $parametros['id_eleccion'];
            $id_formacion = $parametros['id_formacion'];
            $rowFormacion = $parametros['dataPoliticos'];

            $claseUsuario->eliminarSeleccionFormacion($db,$id);
            
            foreach ($rowFormacion as $key => $value) {                  
                $newObj = [    
                    "id_eleccion" => $id,
                    "id_politico" => $value['id_politico'],
                    "nombre_politico" => $value['nombre'],
                    "imagen_politico" => $value['imagen_politico'],
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
            $claseUsuario = new Desafios;
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
                "nombre_desafio" => $parametros['nombre_desafio'],
                "monto_desafio" => $parametros['monto_desafio'],
                "estado_desafio"=> $parametros['estado_desafio'],
                "fecha_inicio"=> $parametros['fecha_inicio'],
                "fecha_fin"=> $parametros['fecha_fin']

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
            $datos = Desafios::getDataById($db ,$id);
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
            $claseUsuario = new Desafios( $this->logger );
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
                "nombre_desafio" => $parametros['nombre_desafio'],
                "monto_desafio" => $parametros['monto_desafio'],
                "estado_desafio"=> $parametros['estado_desafio'],
                "fecha_inicio"=> $parametros['fecha_inicio'],
                "fecha_fin"=> $parametros['fecha_fin'],
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
                $claseUsuario = new Desafios( $this->logger );
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
                    "estado_actual_eleccion" => $parametros['estado_actual_eleccion'],
                    "votos_blanco_eleccion" => $parametros['votos_blanco_eleccion'],
                    "votos_nulo_eleccion" => $parametros['votos_nulo_eleccion'],
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
            $claseUsuario = new Desafios;
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
        //Eliminar
        $app->delete('/delete', function(Request $request, Response $response, array $args){
            try{
                $db = $this->db;
                $claseUsuario = new Desafios;
                $claseUsuario->eliminarTodo($db);
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


//RUTAS DE CARGOS

        //Listar
        $app->get('/eleccionCargo/{id}/list', function(Request $request, Response $response, array $args){
            try{
                $db = $this->db;
                $id = $args['id'];
                $list = Desafios::listarCargos($db,$id);
                $eleccion = Desafios::getDataById($db,$id);
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'success', 
                    'message' => '',
                    'data' => [
                        'eleccion'=> $eleccion, 
                        'cargos'=>$list
                    ]
                ]);
            } catch(PDOException $e)
            {
                throw $e;
            }
        });
    
        //Listar select
        $app->get('/eleccionCargo/listSelect', function(Request $request, Response $response, array $args){
            try{
                $db = $this->db;
                $listLocal =[];
                $listCargo = \Clases\Admin\Desafios\Cargos::listar($db);
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'success', 
                    'message' => '',
                    'data' => [
                         "locales"=>$listLocal,
                         "cargos"=>$listCargo 
    
                    ]
                ]);
            } catch(PDOException $e)
            {
                throw $e;
            }
        });
    
        //Agregar
        $app->post('/eleccionCargo/add', function(Request $request, Response $response, array $args){
            try{
                $db = $this->db;
                $claseUsuario = new Desafios;
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
                    "escano_elec_cargo"=> $parametros['escano_elec_cargo'],
                    "cant_votos_elec_cargo"=> $parametros['cant_votos_elec_cargo'],
                    "color_elec_cargo"=> $parametros['color_elec_cargo'],
                    "id_cargo"=> $parametros['id_cargo'],
                    "id_eleccion"=> $parametros['id_eleccion'],
                    "estado_elec_cargo"=> $parametros['estado_elec_cargo'],
                    "votos_blancos_elec_cargo" => $parametros['votos_blancos_elec_cargo'],
                    "votos_nulos_elec_cargo" => $parametros['votos_nulos_elec_cargo'],
                    "fecha_creador" => date("Y-m-d H:y:s"),
    
    
                ];
                $idUsuario = $claseUsuario->insertarCargos($db,$newObj);
            
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
        $app->get('/eleccionCargo/edit/{id}', function($request, $response, $args){
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
                $datos = Desafios::getDataById($db ,$id);
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
        $app->post('/eleccionCargo/update/{id}', function($request, $response, $args){
            try{
                $db = $this->db;
                $claseUsuario = new Desafios( $this->logger );
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
                    "escano_elec_cargo"=> $parametros['escano_elec_cargo'],
                    "cant_votos_elec_cargo"=> $parametros['cant_votos_elec_cargo'],
                    "color_elec_cargo"=> $parametros['color_elec_cargo'],
                    "id_cargo"=> $parametros['id_cargo'],
                    "estado_elec_cargo"=> $parametros['estado_elec_cargo'],
                    "votos_blancos_elec_cargo" => $parametros['votos_blancos_elec_cargo'],
                    "votos_nulos_elec_cargo" => $parametros['votos_nulos_elec_cargo'],
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
        $app->delete('/eleccionCargo/delete/{id}', function(Request $request, Response $response, array $args){
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
                $claseUsuario = new Desafios;
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
    //rutas postulantes

    //Listar select
    $app->get('/eleccionPostulantes/listSelectById/{id}', function (Request $request, Response $response, array $args) {
        try {
            $db = $this->db;
            $seleccionCargo = \Clases\Admin\Desafios\Desafios::listarPostulantesCargo($db, $args['id']);
            $listPoliticos = \Clases\Admin\Desafios\Desafios::listarPoliticosCargo($db, $args['id']);
            // $seleccionPoliticos = \Clases\Admin\Desafios\Desafios::listFormacionById($db,$args['id']);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success',
                'message' => '',
                'data' => [
                    "selecciones" => $seleccionCargo,
                    //"seleccionPoliticos"=>$seleccionPoliticos,
                    "politicos" => $listPoliticos,

                ]
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    });

    //Agregar seleccion politicos
        $app->post('/eleccionPostulantes/addSeleccion', function(Request $request, Response $response, array $args){
            try{
                $db = $this->db;
                $claseUsuario = new Desafios;
                $parametros = $request->getParsedBody();
                if(!count($parametros)){
                    return $this->response->withJson([
                        'code' => 100,
                        'status' => 'error', 
                        'message' => 'Parametros incompletos!',
                        'data' => []
                    ]);
                }
    
                $id = $parametros['id_elec_cargo'];
                $rowFormacion = $parametros['dataPostulantes'];
    
                $claseUsuario->eliminarSeleccionPostulantes($db,$id);
                
                foreach ($rowFormacion as $key => $value) {                  
                    $newObj = [    
                        "id_elec_cargo" => $id,
                        "id_politico" => $value['id_politico'],
                        "nombre_politico" => $value['nombre_politico'],
                        "apellido_politico" => $value['apellido_politico'],
                        "ci_politico" => $value['ci_politico'],
                        "posicion_politico" => $value['posicion_politico'],
                        "imagen_politico" => $value['imagen_politico'],
                        "clase" => $value['id_politico']
                    ];
                    $idUsuario = $claseUsuario->insertarSeleccionPostulantes($db,$newObj);
                }
            
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
});



?>
