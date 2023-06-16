<?php
date_default_timezone_set(TIMEZONE);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// use Endroid\QrCode\Color\Color;
// use Endroid\QrCode\Encoding\Encoding;
// use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
// use Endroid\QrCode\QrCode;
// use Endroid\QrCode\Label\Label;
// use Endroid\QrCode\Logo\Logo;
// use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
// use Endroid\QrCode\Writer\PngWriter;
use Clases\GenerarQr;
use Spipu\Html2Pdf\Html2Pdf;

$app->group('/rifas/api/app/ventas', function(\Slim\App $app) {

    //acceso al sistema por app
    $app->get('/qr', function(Request $request, Response $response){
        try{
            set_time_limit(0);
            $start_time = microtime(true);


            $classGenerarQr = New GenerarQr();
            $uriTarget = 'https://local.quattropy.com/emprendimiento/s1/public/rifas/api/app/ventas/escanear/';
            $logo = __DIR__.'/../../../../public/comodin.png';
            $label = "";
            $nro = "";
            $monto = "";
            $ciudad = "";
            $fecha = date("d/m/Y");
            $imagenFondo = __DIR__.'/../../../../public/tickets2.png';
            $font = __DIR__.'/../../../../public/arial.ttf';
            $tipo = "url";
            $img = "";

            $arrayNumeros = $classGenerarQr->generarNumerosAleatorios(1,9999,20);
            for ($i=0; $i < 10 ; $i++) { 
                $nro1 = array_shift($arrayNumeros);
                $nro2 = array_shift($arrayNumeros);
                $id = '100'.$i;
                $imageQr = $classGenerarQr->generarTicketV1($tipo,$uriTarget.$id,$logo,$label,$imagenFondo,$font,$nro.$id,$monto,$ciudad,$fecha,$nro1,$nro2);
                $img .= "<img style='width:45mm;height:91mm;' src='$imageQr' />";
            }

            $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', array(15, 5, 15, 5));
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->writeHTML( $img );
            //Close and output PDF document
            $response = 
                $this->response->withHeader( 'Content-Disposition', 'inline;filename=PDF_'.date("d-m-Y H:i:s") )
                ->withHeader( 'Content-type', 'application/pdf' );
    
            $response->write( $html2pdf->Output() );
            
            return $response;



            $response->write($img);
            $end_time = microtime(true);
            $duration = $end_time - $start_time;
            $hours = (int)($duration/60/60);
            $minutes = (int)($duration/60)-$hours*60;
            $seconds = (int)$duration-$hours*60*60-$minutes*60; 
            echo "Tiempo empleado para cargar esta pagina: <strong>" . $hours.' horas, '.$minutes.' minutos y '.$seconds.' segundos.</strong>';
            
            return $response->withHeader('Content-Type', 'text/html');

        } catch(PDOException $e){
            throw $e;
        }
    });

    $app->get('/escanear/{id}', function(Request $request, Response $response , array $args){
        try{
            // Get DB Object
            $db = $this->db;
            $basePath = $request->getUri()->getBasePath();
            $fecha = date("d/m/Y h:i:s");
            $ticket = $args['id'] ?? 0 ;
            $lat = $request->getParam('lat') ?? 0 ;
            $lon = $request->getParam('lon') ?? 0 ;
            return $this->renderer->render($response, 'index.phtml', 
                [
                    "basePath" => $basePath,
                    "fecha" => $fecha,
                    "ticket" => $ticket,
                    "lat" => $lat,
                    "lon" => $lon
                ]
            );

        } catch(PDOException $e){
            throw $e;
        }
    });

    $app->get('/numerosAleatorios', function(Request $request, Response $response , array $args){
        try{
            // Get DB Object
            $classGenerarQr = New GenerarQr();
            $arrayNumeros = $classGenerarQr->generarNumerosAleatorios(1,9999,20);
            print_r($arrayNumeros);
            echo '<br>';
            // unset($arrayNumeros[0]);
            // unset($arrayNumeros[1]);
            // echo '<br>';
            // print_r($arrayNumeros);
            echo '<br>';
            echo '<br>';
            echo '<br>';
            echo '<br>';
            echo array_shift($arrayNumeros);
            echo '<br>';
            print_r($arrayNumeros);
            echo '<br>';
            echo '<br>';
            echo '<br>';
            echo '<br>';
            echo array_shift($arrayNumeros);
            echo '<br>';
            print_r($arrayNumeros);
            echo '<br>';
            echo array_shift($arrayNumeros);
            echo '<br>';
            print_r($arrayNumeros);
            
        } catch(PDOException $e){
            throw $e;
        }
    });

});










// //acceso al sistema por app
// $app->get('/qr2', function(Request $request, Response $response){
//     try{
//         $writer = new PngWriter();

//         // Create QR code
//         // $qrCode = QrCode::create('https://hoy.com.py')
//         $qrCode = QrCode::create('https://local.quattropy.com/emprendimiento/s1/public/rifas/api/app/ventas/escanear/2000')
//             ->setEncoding(new Encoding('UTF-8'))
//             ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
//             ->setSize(160)
//             ->setMargin(5)
//             ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
//             ->setForegroundColor(new Color(0, 0, 0))
//             ->setBackgroundColor(new Color(255, 255, 255));
        
//         // Create generic logo
//         $logo = Logo::create(__DIR__.'/../../../../public/comodin.png')
//             ->setResizeToWidth(30);
        
//         // Create generic label
//         // $label = Label::create('Millones de Premios!!!')
//             // ->setTextColor(new Color(255, 0, 0));
//         $result = $writer->write($qrCode, $logo, $label);
//         $type = $result->getMimeType();
//         $imgageQr = $result->getString();

//         // /   Save it to a file
//         //    $result->saveToFile(__DIR__.'/qrcode.png');

//         $format = 'ticket';
//         $widthFormat = 267;
//         $heightFormat = 573;
//         // $widthFormat = 1080;
//         // $heightFormat = 1675;
//         $imageHead = __DIR__.'/../../../../public/tickets2.png';
//          // configuracion de codigo qr
//             $widthX = 0;
//             $heightY = 170;
//             $positionX = 45;
//             $positionY =  140;
//             $porcentaje = 1;
//         //------------------------  

//         $agente = 56456;
//         $sizeTextagente = 20;
//         $positionXagente = 90;
//         $positionYagente =  50;

//         $contacto = "2020/01/02";
//         $sizeTextcontacto = 12;
//         $positionXcontacto = 120;
//         $positionYcontacto =  540;

      

//         //Creamos la base de la imagen donde colocaremos luego las otras dos
//         $baseimagen = ImageCreateTrueColor($widthFormat,$heightFormat);
//         //Le damos un color a la base, en este caso se utiliza el negro
//         $white = ImageColorAllocate($baseimagen, 255, 255, 255);
//         $black = ImageColorAllocate($baseimagen, 0, 0, 0);
//         // imagefill($baseimagen, 0, 0, $white);

//         //Cargamos la primera imagen(cabecera)
//         $logo = ImageCreateFromPng($imageHead);
//         //Unimos la primera imagen con la imagen base
//         imagecopymerge($baseimagen, $logo, 0, 0, 0, 0, $widthFormat, $heightFormat, 100);
//         //Cargamos la segunda imagen(cuerpo)
//         // $ts_viewer = ImageCreateFromPng("./1.png");
//         // El archivo
//         $nombre_archivo = $imgageQr;

//         // Tipo de contenido
//         // Obtener nuevas dimensiones
//         // list($ancho, $alto) = getimagesizefromstring ($nombre_archivo);
//         // $nuevo_ancho = $ancho * $porcentaje;
//         // $nuevo_alto = $alto * $porcentaje;
//         // print_r(getimagesizefromstring($nombre_archivo));
//         list($ancho, $alto) = getimagesizefromstring($nombre_archivo);
//         $nuevo_ancho = $ancho * $porcentaje;
//         $nuevo_alto = $alto * $porcentaje;

//         // Redimensionar
//         $imagen_p = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
//         $imagen = imagecreatefromstring($imgageQr);
//         imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
        
//         //Juntamos la segunda imagen con la imagen base
    
//         imagecopymerge($baseimagen, $imagen_p, $positionX , $positionY, $widthX, $widthX, $heightY, $heightY, 100);



//          // Replace path by your own font path
//          $font = __DIR__.'/../../../../public/arial.ttf';
//          // Add some shadow to the text
//          imagettftext($baseimagen, $sizeTextagente, 0, $positionXagente, $positionYagente, $black, $font, $agente);
//          imagettftext($baseimagen, $sizeTextcontacto, 0, $positionXcontacto, $positionYcontacto , $black, $font, $contacto);
        

//         imagestring($baseimagen, 2, 540, 10, 111, $black); 

        
//         ob_start();
//         imagepng($baseimagen);
//         $image = ob_get_clean();

//         $response->write($image);
//         return $response->withHeader('Content-Type', 'image/png');

//     } catch(PDOException $e){
//         throw $e;
//     }
// });