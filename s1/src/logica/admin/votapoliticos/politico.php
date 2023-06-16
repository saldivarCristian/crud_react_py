<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Votapoliticos\Politicos;

$app->group('/admin/votapoliticos/politico', function(\Slim\App $app) {
    //Listar
    $app->get('/list', function(Request $request, Response $response, array $args){

        // $roles = $conexion->consulta("SELECT * FROM roles r JOIN permisos p on p.id_rol=r.id_rol WHERE r.id_rol=".$_SESSION['id_rol']." AND p.cod_mod='item_".$_SESSION['item_menu']."'","");
        // $modulos = $conexion->consulta("SELECT * FROM modulos WHERE  nivel_mod='".$_SESSION['item_menu']."'","");
        // $alias='u';
        // $validar='true';
        // $ocultar_creador='true';
        // if ($_SESSION['id_rol']!=1) {
        //     require_once $repeat."common/model/restricciones.php";
        // }


        try{
            $db = $this->db;
            $list = Politicos::listar($db);
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
            $listPartido = \Clases\Admin\Votapoliticos\Partidos::listar($db);
            $listMovimiento = \Clases\Admin\Votapoliticos\Movimientos::listar($db);
            $listCargo = \Clases\Admin\Votapoliticos\Cargos::listar($db);
            $listLocal = [];
            $listEstados = [
                ["id_estado" =>0, "nombre_estado"=> "activo"],
                ["id_estado" =>1, "nombre_estado"=> "inactivo"]
            ];
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                    "partido"=>$listPartido, "locales"=>$listLocal, "movimientos"=>$listMovimiento, "estados"=>$listEstados, "cargos"=>$listCargo,
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
            $clasePoliticos = new Politicos;
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
                "nombre_politico" => $parametros['nombre_politico'],
                "apellido_politico" => $parametros['apellido_politico'],
                "ci_politico" => $parametros['ci_politico'],
                "descripcion_politico" => $parametros['descripcion_politico'],
                "id_movimiento" => $parametros['id_movimiento'],
                "id_cargo" => $parametros['id_cargo'],
                "estado_politico" => $parametros['estado_politico'],
                "fecha_creador" => date("Y-m-d H:y:s"),

            ];
            $idPoliticos = $clasePoliticos->insertar($db,$newObj);
            $target = 'votapoliticos';
            $responseImage = uploadFile('data', $target, 'politicos');

            if( isset( $responseImage['error'] ) ){
                throw new Exception( $responseImage['error'] );
            }else{
                $image = $responseImage['name'][0];
                $newObj = [    
                    "imagen_politico" => $image,
                ];
            
                $clasePoliticos->actualizar($db,$newObj,$idPoliticos);
            }

        
            if($idPoliticos == 0){
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
            $datos = Politicos::getDataById($db ,$id);
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
            $clasePoliticos = new Politicos( $this->logger );
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
                "nombre_politico" => $parametros['nombre_politico'],
                "apellido_politico" => $parametros['apellido_politico'],
                "ci_politico" => $parametros['ci_politico'],
                "descripcion_politico" => $parametros['descripcion_politico'],
                "id_movimiento" => $parametros['id_movimiento'],
                "id_cargo" => $parametros['id_cargo'],
                "estado_politico" => $parametros['estado_politico'],
                "fecha_modificador" => date("Y-m-d H:y:s"),

            ];
            $id = $args['id'];
            $idPoliticos = $clasePoliticos->actualizar($db,$newObj,$id);
            if (!empty($_FILES['data'])) {
                $target = 'votapoliticos';
                $responseImage = uploadFile('data', $target, 'politicos');
    
                if( isset( $responseImage['error'] ) ){
                    throw new Exception( $responseImage['error'] );
                }else{
                    $image = $responseImage['name'][0];
                    $newObj = [    
                        "imagen_politico" => $image,
                    ];
                
                    $clasePoliticos->actualizar($db,$newObj,$id);
                    unlink(__DIR__."/../../../../public/img/votapoliticos/lg/politicos/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/votapoliticos/sm/politicos/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/votapoliticos/md/politicos/".$parametros['file_hidden']);
                    
                }
            }
            if($idPoliticos == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => [$idPoliticos]
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
            $claseUsuario = new Politicos;
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
