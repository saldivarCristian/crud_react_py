<?php
// ruta para negocio fanatico
    $thefolder = __DIR__."/logica/clientes/fanaticos/";
    if ($handler = opendir($thefolder)) {
        while (false !== ($file = readdir($handler))) {
            if( $file != "" ){
                @include_once($thefolder.$file);
            }
        }
        closedir($handler);
    }
// fin

// ruta para admin
    $thefolder = __DIR__."/logica/admin/general/";
    if ($handler = opendir($thefolder)) {
        while (false !== ($file = readdir($handler))) {
            if( $file != "" ){
                @include_once($thefolder.$file);
            }
        }
        closedir($handler);
    }
    $thefolder = __DIR__."/logica/admin/fanaticos/";
    if ($handler = opendir($thefolder)) {
        while (false !== ($file = readdir($handler))) {
            if( $file != "" ){
                @include_once($thefolder.$file);
            }
        }
        closedir($handler);
    }
// fin
// ruta para negocio desfios
$thefolder = __DIR__."/logica/clientes/desafios/";
if ($handler = opendir($thefolder)) {
    while (false !== ($file = readdir($handler))) {
        if( $file != "" ){
            @include_once($thefolder.$file);
        }
    }
    closedir($handler);
}
// fin

$thefolder = __DIR__."/logica/admin/desafios/";
if ($handler = opendir($thefolder)) {
    while (false !== ($file = readdir($handler))) {
        if( $file != "" ){
            @include_once($thefolder.$file);
        }
    }
    closedir($handler);
}
// fin

$thefolder = __DIR__."/logica/admin/rifas/";
if ($handler = opendir($thefolder)) {
    while (false !== ($file = readdir($handler))) {
        if( $file != "" ){
            @include_once($thefolder.$file);
        }
    }
    closedir($handler);
}
// fin

$thefolder = __DIR__."/logica/admin/calautos/";
if ($handler = opendir($thefolder)) {
    while (false !== ($file = readdir($handler))) {
        if( $file != "" ){
            @include_once($thefolder.$file);
        }
    }
    closedir($handler);
}
// fin
// // ruta para negocio califica autos
// $thefolder = __DIR__."/logica/clientes/calautos/";
// if ($handler = opendir($thefolder)) {
//     while (false !== ($file = readdir($handler))) {
//         if( $file != "" ){
//             @include_once($thefolder.$file);
//         }
//     }
//     closedir($handler);
// }
// fin
// ruta para negocio califica autos
$thefolder = __DIR__."/logica/clientes/calautos/";
if ($handler = opendir($thefolder)) {
    while (false !== ($file = readdir($handler))) {
        if( $file != "" ){
            @include_once($thefolder.$file);
        }
    }
    closedir($handler);
}
// // fin

$thefolder = __DIR__."/logica/admin/votapoliticos/";
if ($handler = opendir($thefolder)) {
    while (false !== ($file = readdir($handler))) {
        if( $file != "" ){
            @include_once($thefolder.$file);
        }
    }
    closedir($handler);
}


// ruta para negocio califica autos
$thefolder = __DIR__."/logica/clientes/votapoliticos/";
if ($handler = opendir($thefolder)) {
    while (false !== ($file = readdir($handler))) {
        if( $file != "" ){
            @include_once($thefolder.$file);
        }
    }
    closedir($handler);
}
// // fin

// $thefolder = __DIR__."/logica/admin/prode/";
// if ($handler = opendir($thefolder)) {
//     while (false !== ($file = readdir($handler))) {
//         if( $file != "" ){
//             @include_once($thefolder.$file);
//         }
//     }
//     closedir($handler);
// }


// ruta para negocio califica autos
// $thefolder = __DIR__."/logica/clientes/prode/";
// if ($handler = opendir($thefolder)) {
//     while (false !== ($file = readdir($handler))) {
//         if( $file != "" ){
//             @include_once($thefolder.$file);
//         }
//     }
//     closedir($handler);
// }
// // fin


    foreach (RUTAS as $key => $value) {
        $thefolder = __DIR__.$value;
        if ($handler = opendir($thefolder)) {
            while (false !== ($file = readdir($handler))) {
                if( $file != "" ){
                    @include_once($thefolder.$file);
                }
            }
            closedir($handler);
        }
    }