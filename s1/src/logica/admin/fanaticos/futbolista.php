<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Fanaticos\Futbolistas;

$app->group('/admin/futbolistas', function(\Slim\App $app) {
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
            $list = Futbolistas::listar($db);
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
            $listClubes = \Clases\Admin\Fanaticos\Clubes::listar($db);
            $listTorneos = \Clases\Admin\Fanaticos\Torneos::listar($db);
            $listEstadios = \Clases\Admin\Fanaticos\Estadios::listar($db);
            $listLocal = [];
            $listArbitro = \Clases\Admin\Fanaticos\Arbitros::listar($db);
            $listEstados = [
                ["id_estado" =>0, "nombre_estado"=> "activo"],
                ["id_estado" =>1, "nombre_estado"=> "inactivo"]
            ];
            return $this->response->withJson([
                'code' => 200,
                'status' => 'success', 
                'message' => '',
                'data' => [
                    "torneos"=>$listTorneos, "locales"=>$listLocal, "estadios"=>$listEstadios, "clubes"=>$listClubes, "arbitros"=>$listArbitro, "estados"=>$listEstados
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
            $claseFutbolista = new Futbolistas;
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
                "nombre_futbolista" => $parametros['nombre_futbolista'],
                "apellido_futbolista" => $parametros['apellido_futbolista'],
                "ci_futbolista" => $parametros['ci_futbolista'],
                "telefono_futbolista" => $parametros['telefono_futbolista'],
                "estado_futbolista" => $parametros['estado_futbolista'],
                "fecha_creador" => date("Y-m-d H:y:s"),

            ];
            $idFutbolista = $claseFutbolista->insertar($db,$newObj);
            $target = 'fanaticos';
            $responseImage = uploadFile('data', $target, 'futbolistas');

            if( isset( $responseImage['error'] ) ){
                throw new Exception( $responseImage['error'] );
            }else{
                $image = $responseImage['name'][0];
                $newObj = [    
                    "imagen_futbolista" => $image,
                ];
            
                $claseFutbolista->actualizar($db,$newObj,$idFutbolista);
            }

        
            if($idFutbolista == 0){
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
            $datos = Futbolistas::getDataById($db ,$id);
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
            $claseFutbolista = new Futbolistas( $this->logger );
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
                "nombre_futbolista" => $parametros['nombre_futbolista'],
                "apellido_futbolista" => $parametros['apellido_futbolista'],
                "ci_futbolista" => $parametros['ci_futbolista'],
                "telefono_futbolista" => $parametros['telefono_futbolista'],
                "estado_futbolista" => $parametros['estado_futbolista'],
                "fecha_modificador" => date("Y-m-d H:y:s"),

            ];
            $id = $args['id'];
            $idFutbolista = $claseFutbolista->actualizar($db,$newObj,$id);
            if (!empty($_FILES['data'])) {
                $target = 'fanaticos';
                $responseImage = uploadFile('data', $target, 'futbolistas');
    
                if( isset( $responseImage['error'] ) ){
                    throw new Exception( $responseImage['error'] );
                }else{
                    $image = $responseImage['name'][0];
                    $newObj = [    
                        "imagen_futbolista" => $image,
                    ];
                
                    $claseFutbolista->actualizar($db,$newObj,$id);
                    unlink(__DIR__."/../../../../public/img/fanaticos/lg/futbolistas/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/fanaticos/sm/futbolistas/".$parametros['file_hidden']);
                    unlink(__DIR__."/../../../../public/img/fanaticos/md/futbolistas/".$parametros['file_hidden']);
                    
                }
            }
            if($idFutbolista == 0){
                return $this->response->withJson([
                    'code' => 100,
                    'status' => 'error', 
                    'message' => 'No se pudo completar la operación !.',
                    'data' => [$idFutbolista]
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
            $claseUsuario = new Futbolistas;
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
