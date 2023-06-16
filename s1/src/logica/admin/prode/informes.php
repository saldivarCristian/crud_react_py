<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Clases\Admin\Prode\Votaciones;
use \Clases\Admin\Prode\Elecciones;
use Spipu\Html2Pdf\Html2Pdf;
use Dompdf\Dompdf;


$app->group('/admin/prode/informes', function(\Slim\App $app) {
    //Listar
    $app->get('/listaDeCargos', function(Request $request, Response $response, array $args){

        try{
            $db = $this->db;
            $list = Elecciones::listarEleccionesCargos($db);
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
    $app->get('/resultados/{id}', function(Request $request, Response $response, array $args){

        try{
            $db = $this->db;
            if (isset($args['id']) ) {
                $id = $args['id'];
                $list = Votaciones::getReultados($db,$id);
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'success', 
                    'message' => '',
                    'data' => $list
                ]);
            }else{
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'fail', 
                    'message' => 'Dato no encontrado.',
                    'data' => []
                ]);
            }
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

    $app->get('/resultados/pdf/{id}', function(Request $request, Response $response, array $args){
        try{
            $db = $this->db;
            if (isset($args['id']) ) {
                $db = $this->db;
                $id = $args['id'];
                $list = Votaciones::getReultados($db,$id);
                $pdf = Votaciones::getFormatoPdf($db, $list);
                // $html2pdf = new Html2Pdf();
                $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', array(15, 5, 15, 5));
                $html2pdf->pdf->SetDisplayMode('fullpage');

                // $html2pdf = new HTML2PDF('P', 'A4', 'en', 'UTF-8');
                $html2pdf->writeHTML($pdf );
                // $html2pdf->Output('FacturaRetencion', 'S');
        
                //Close and output PDF document
                $response = 
                    $this->response->withHeader( 'Content-Disposition', 'inline;filename=Resultado_'.date("d-m-Y H:i:s") )
                    ->withHeader( 'Content-type', 'application/pdf' );
        
                $response->write( $html2pdf->Output() );
                
                return $response;
            }else{
                return $this->response->withJson([
                    'code' => 200,
                    'status' => 'fail', 
                    'message' => 'Dato no encontrado.',
                    'data' => []
                ]);
            }
        } catch(PDOException $e)
        {
            throw $e;
        }
    });

});


?>
