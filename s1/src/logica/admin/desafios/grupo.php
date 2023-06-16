<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Desafios\Grupos;

$app->group('/admin/desafios/grupos', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            $list = Grupos::listar($db);
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
            $grupos = \Clases\Admin\Desafios\Grupos::getDataById($db,$args['id']);
            $listPoliticos = \Clases\Admin\Desafios\Politicos::listarPorCargo($db,$args['id']);
            $seleccionParticipantes = \Clases\Admin\Desafios\Grupos::listFormacionById($db,$args['id']);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                     "Grupos"=>$grupos,
                     "seleccionParticipantes"=>$seleccionParticipantes,
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
            $claseUsuario = new Grupos;
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
                $claseGrupos = new Grupos;
                $parametros = $request->getParsedBody();
                $parametros = $parametros['data'];
                if(!count($parametros)){
                    return $this->response->withJson([
                        'code' => 100,
                        'status' => 'error', 
                        'message' => 'Parametros incompletos!',
                        'data' => []
                    ]);
                }
    
                
                $newObj = [    
                    "nombre_grupo" => $parametros['nombre_grupo'],
                    "color_grupo" => $parametros['color_grupo'],
                    "descripcion_grupo"=> $parametros['descripcion_grupo'],
                    "estado_grupo"=> $parametros['estado_grupo'],
                    "fecha_creador" => date("Y-m-d H:y:s")
                ];
                $idGrupos = $claseGrupos->insertar($db,$newObj);
                $target = 'desafios';
                $responseImage = uploadFile('data', $target, 'grupos');
    
                if( isset( $responseImage['error'] ) ){
                    throw new Exception( $responseImage['error'] );
                }else{
                    $image = $responseImage['name'][0];
                    $newObj = [    
                        "imagen_grupo" => $image,
                    ];
                
                    $claseGrupos->actualizar($db,$newObj,$idGrupos);
                }
    
            
                if($idGrupos == 0){
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
            $datos = Grupos::getDataById($db ,$id);
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

    $app->post('/update/{id}', function($request, $response, $args){
        try{
            $db = $this->db;
            $claseGrupos = new Grupos( $this->logger );
            $parametros = $request->getParsedBody();
            $parametros = $parametros['data'];
            if(!count($parametros)){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'Parametros incompletos!',
                    'data' => []
                ]);
            }

            
            $newObj = [    
                "nombre_grupo" => $parametros['nombre_grupo'],
                "color_grupo" => $parametros['color_grupo'],
                "descripcion_grupo"=> $parametros['descripcion_grupo'],
                "estado_grupo"=> $parametros['estado_grupo'],
                "fecha_creador" => date("Y-m-d H:y:s"),

            ];
            $id = $args['id'];
            $idGrupos = $claseGrupos->actualizar($db,$newObj,$id);
            if (!empty($_FILES['data'])) {
                $target = 'desafios';
                $responseImage = uploadFile('data', $target, 'grupos');
    
                if( isset( $responseImage['error'] ) ){
                    throw new Exception( $responseImage['error'] );
                }else{
                    $image = $responseImage['name'][0];
                    $newObj = [    
                        "imagen_grupo" => $image,
                    ];
                
                    $claseGrupos->actualizar($db,$newObj,$id);
                    unlink(__DIR__."/../../../../public/img/desafios/lg/grupos/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/desafios/sm/grupos/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/desafios/md/grupos/".$parametros['file_hidden']);
                    
                }
            }
            if($idGrupos == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => [$idGrupos]
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
        $app->post('/eleccionParticipantes/updateEstado/{id}', function($request, $response, $args){
            try{
                $db = $this->db;
                $claseUsuario = new Grupos( $this->logger );
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
                    "estado_grupo_cliente" => ($parametros['estado'] ? 0 : 1),
    
    
                ];
                $id = $args['id'];
                $idUsuario = $claseUsuario->actualizarEstado($db,$newObj,$id);
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
            $claseUsuario = new Grupos;
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
                $claseUsuario = new Grupos;
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
                $list = Grupos::listarCargos($db,$id);
                $eleccion = Grupos::getDataById($db,$id);
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
        $app->post('/eleccionParticipantes/add', function(Request $request, Response $response, array $args){
            try{
                $db = $this->db;
                $claseUsuario = new Grupos;
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
                    "id_cliente"=> $parametros['id_cliente'],
                    "nombre_con_cliente"=> $parametros['nombre_con_cliente'],
                    "posicion_cliente"=> $parametros['posicion_cliente'],
                    "ruc_cliente"=> $parametros['ruc_cliente'],
                    "apellido_con_cliente"=> $parametros['apellido_con_cliente'],
                    "fecha_creador" => date("Y-m-d H:y:s")

    
    
                ];
                $idUsuario = $claseUsuario->insertarGrupos($db,$newObj);
            
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
                $datos = Grupos::getDataById($db ,$id);
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
                $claseUsuario = new Grupos( $this->logger );
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
                $claseUsuario = new Grupos;
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
    $app->get('/eleccionParticipantes/listSelectById/{id}', function (Request $request, Response $response, array $args) {
        try {
            $db = $this->db;
            $seleccionParticipantes = \Clases\Admin\Desafios\Grupos::listarParticipantes($db, $args['id']);
            $listParticipantes = \Clases\Admin\Desafios\Clientes::listarActivos($db);
            // $seleccionPoliticos = \Clases\Admin\Desafios\Grupos::listFormacionById($db,$args['id']);
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success',
                'message' => '',
                'data' => [
                    "grupos" => $seleccionParticipantes,
                    //"seleccionPoliticos"=>$seleccionPoliticos,
                    "participantes" => $listParticipantes,

                ]
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    });

    //Agregar seleccion politicos
        $app->post('/eleccionParticipantes/addSeleccion', function(Request $request, Response $response, array $args){
            try{
                $db = $this->db;
                $claseUsuario = new Grupos;
                $parametros = $request->getParsedBody();
                if(!count($parametros)){
                    return $this->response->withJson([
                        'code' => 100,
                        'status' => 'error', 
                        'message' => 'Parametros incompletos!',
                        'data' => []
                    ]);
                }
    
                $id = $parametros['id_grupo'];
                $rowFormacion = $parametros['dataParticipantes'];
    
                $claseUsuario->eliminarSeleccionParticipantes($db,$id);
                
                foreach ($rowFormacion as $key => $value) {                  
                    $newObj = [ 
                        "id_grupo"=> $id,
                        "id_cliente"=> $value['id_cliente'],
                        "posicion_cliente"=> $value['posicion_cliente']
                    ];
                    $idUsuario = $claseUsuario->insertarSeleccionParticipantes($db,$newObj);
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
