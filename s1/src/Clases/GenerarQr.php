<?php
namespace Clases;
use \PDO;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class GenerarQr 
{

    public function generarTicketV1($tipo,$uriTarget,$logo,$label,$imagenFondo,$font,$nro,$monto,$ciudad,$fecha,$nro1,$nro2)
    {

        $writer = new PngWriter();
         // Create QR code
        $qrCode = QrCode::create($uriTarget)
        ->setEncoding(new Encoding('UTF-8'))
        ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
        ->setSize(160)
        ->setMargin(5)
        ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->setForegroundColor(new Color(0, 0, 0))
        ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        // $logo = Logo::create($logo)
        //     ->setResizeToWidth(30);

        // Create generic label
        // $label = Label::create($label)
        //     ->setTextColor(new Color(255, 0, 0));
        $result = $writer->write($qrCode);
        $type = $result->getMimeType();
        $imgageQr = $result->getString();

        // /   Save it to a file
        //    $result->saveToFile(__DIR__.'/qrcode.png');

        $format = 'ticket';
        $widthFormat = 259;
        $heightFormat = 571;
        // $widthFormat = 1080;
        // $heightFormat = 1675;
        $imageHead = $imagenFondo;
        // configuracion de codigo qr
            $widthX = 0;
            $heightY = 170;
            $positionX = 45;
            $positionY =  130;
            $porcentaje = 1;
        //------------------------  


        //Creamos la base de la imagen donde colocaremos luego las otras dos
        $baseimagen = ImageCreateTrueColor($widthFormat,$heightFormat);
        //Le damos un color a la base, en este caso se utiliza el negro
        // $white = ImageColorAllocate($baseimagen, 255, 255, 255);
        $black = ImageColorAllocate($baseimagen, 0, 0, 0);

        //Cargamos la primera imagen(cabecera)
        $logo = ImageCreateFromPng($imageHead);
        //Unimos la primera imagen con la imagen base
        imagecopymerge($baseimagen, $logo, 0, 0, 0, 0, $widthFormat, $heightFormat, 100);
        
        //Cargamos la segunda imagen(cuerpo)
        // $ts_viewer = ImageCreateFromPng("./1.png");
        // El archivo
        $nombre_archivo = $imgageQr;

        // Tipo de contenido
        // Obtener nuevas dimensiones
        list($ancho, $alto) = getimagesizefromstring($nombre_archivo);
        $nuevo_ancho = $ancho * $porcentaje;
        $nuevo_alto = $alto * $porcentaje;

        // Redimensionar
        $imagen_p = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
        $imagen = imagecreatefromstring($imgageQr);
        imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);

        //Juntamos la segunda imagen con la imagen base

        imagecopymerge($baseimagen, $imagen_p, $positionX , $positionY, $widthX, $widthX, $heightY, $heightY, 100);

        // Replace path by your own font path
        $font = $font;
        // Add some shadow to the text
        $nro = $nro;
        $sizeTextagente = 16;
        $positionXagente = 35;
        $positionYagente =  60;
        imagettftext($baseimagen, $sizeTextagente, 0, $positionXagente, $positionYagente, $black, $font, $nro);

        $fecha = $fecha;
        $sizeTextcontacto = 14;
        $positionXcontacto = 110;
        $positionYcontacto =  530;
        imagettftext($baseimagen, $sizeTextcontacto, 0, $positionXcontacto, $positionYcontacto , $black, $font, $fecha);

        $nro1 =$nro1;
        $sizeTextcontacto = 25;
        $positionXcontacto = 90;
        $positionYcontacto =  365; 
        imagettftext($baseimagen, $sizeTextcontacto, 0, $positionXcontacto, $positionYcontacto , $black, $font, $nro1);
        $nro2 = $nro2;
        $sizeTextcontacto = 25;
        $positionXcontacto = 90;
        $positionYcontacto =  410;
        imagettftext($baseimagen, $sizeTextcontacto, 0, $positionXcontacto, $positionYcontacto , $black, $font, $nro2);

        imagestring($baseimagen, 2, 540, 10, 111, $black); 

        if( $tipo == "imagen"){
            ob_start();
            imagepng($baseimagen);
            $image = ob_get_clean();
        }
        if( $tipo == "url"){
            ob_start();
            imagepng($baseimagen);
            $image = ob_get_clean();
            $image = $this->getDataUri($image);
        }

        return $image;
    }

    public function getDataUri($image)
    {
        return 'data:image/png;base64,'.base64_encode($image);
    }

    public static function generarNumerosAleatorios($inicio,$fin,$cantidadBolillas){
        if($inicio > $fin) return [];
        $numeros = range($inicio, $fin);
        shuffle($numeros);
        $rowNumeros = [];
        $ban = 0;
        foreach ($numeros as $numero) {
            if($ban <= $cantidadBolillas){
                $numero = strlen($numero) == strlen($fin) ?  $numero : str_repeat( "0", ( strlen($fin) - strlen($numero) ) ).$numero;
                array_push($rowNumeros, $numero );
                $ban++;
            }
        }
        return $rowNumeros;
    }

}