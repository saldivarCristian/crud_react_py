<?php

date_default_timezone_set('America/Asuncion');
// BASES DE DATOS -------------------
	define("DB_USER", "root");
	define("DB_PSW", "");
    define("DB_BASE", "emprendimientos");
    define("DB_CON", "emprendimientos");
	define('ESTADO_SERVIDOR_MBO', '0');   
	define('MENSAJE_SERVIDOR_MBO', 'En mantenimineto. Intentelo en unos minutos.');
//-----------------------------------

// COMUNES ---------------------------
	define("DOMAIN",@$_SERVER['HTTP_HOST']);
	define("PATH","/emprendimiento/s1/public"); // Path to main index from host
	define("HOST", "https://".DOMAIN.(DOMAIN=='localhost' || DOMAIN == 'local.quattropy.com' ?PATH:''));
	define("SUCURSAL_DEFECTO",1);
    define("URLS",[
        'linkImageClub'   =>  "https://".$_SERVER['HTTP_HOST'].'/emprendimiento/s1/public/img/fanaticos/lg/clubes/',
		'linkImageClubDesafios'   =>  "https://".$_SERVER['HTTP_HOST'].'/emprendimiento/s1/public/img/desafios/lg/clubes/',                           
        'linkImageAuspiciante'   =>  "https://".$_SERVER['HTTP_HOST'].'/emprendimiento/s1/public/img/calautos/lg/autos/',
        'linkImageFutbolista'   =>  "https://".$_SERVER['HTTP_HOST'].'/emprendimiento/s1/public/img/calautos/lg/futbolistas/',
        'linkImageAuto'   =>  "https://".$_SERVER['HTTP_HOST'].'/emprendimiento/s1/public/img/calautos/lg/autos/',
		'linkImagePolitico'   =>  "https://".$_SERVER['HTTP_HOST'].'/emprendimiento/s1/public/img/votapoliticos/lg/politicos/',
        'linkImageGrupos'   =>  "https://".$_SERVER['HTTP_HOST'].'/emprendimiento/s1/public/img/desafios/lg/grupos/'                               
    ]);
    //     define("URLS",[
    //     'linkImageClub'   =>  "https://".$_SERVER['HTTP_HOST'].'/s1_cerro/public/img/fanaticos/lg/clubes/',                           
    //     'linkImageAuspiciante'   =>  "https://".$_SERVER['HTTP_HOST'].'/s1_cerro/public/img/fanaticos/lg/auspiciantes/',
    //     'linkImageFutbolista'   =>  "https://".$_SERVER['HTTP_HOST'].'/s1_cerro/public/img/fanaticos/lg/futbolistas/'                              
    // ]);
    
    // define("URLS",[
    //     'linkImageClub'   =>  "https://".$_SERVER['HTTP_HOST'].'/s1_guairena/public/img/fanaticos/lg/clubes/',                           
    //     'linkImageAuspiciante'   =>  "https://".$_SERVER['HTTP_HOST'].'/s1_guairena/public/img/fanaticos/lg/auspiciantes/',
    //     'linkImageFutbolista'   =>  "https://".$_SERVER['HTTP_HOST'].'/s1_guairena/public/img/fanaticos/lg/futbolistas/'                              
    // ]);

//------------------------------------


// PARTICULARES ---------------------------
	define("COMPANY", "Emprendimientos Virtuales");
	define("MISSION","Sistema Integral de Gesti&oacute;n");
	define("TIMEZONE", "America/Asuncion");
	define("EMAIL","");
	define("PHONE","");
	define("FACEBOOK","");
	define("COPYRIGHT","2017");
	define("DEVELOPER","");
	define("DEVWEBPAGE","");
	// define("MONDEDA","Gs");
	define("DECIMALES","0");
	define("COD_GRP_PRO","3");
	//define("MONEDA_BASE","1000"); // UN MIL, UN DOLLAR , UN PESO  ### ESTO ESTA EN DEFAULT.PHP se usan para hacer los cierres ###
	define('MONEDA', array(
        0,',','.','Gs' // Paraguay
        // 2,',','.','$' // Argentina
        )
	);
	define("FECHA","d/m/Y");
	define("HORA","H:i:s");
//-----------------------------------------
//MIDDLEWARE
define("KEY", "123");
define(
    "MIDDLEWARE" , 
    [	
        "path" =>[
            "/api",
            "/cliente",
            "/admin",
            "/desafios",
            "/rifas/api"
        ], /* or ["/api", "/admin"] */
        "ignore" => [
            "/cliente/desafio/st/login",
            "/admin/login",
            "/admin/votapoliticos/informes",
            "/admin/prode/informes",
            "/desafio/paginas/configuraciones",
            "/rifas/api/app/",
            "/rifas/api/app/ventas/escanear"
        ], 
    ]
);
//-----------------------------------------
//RUTAS 
define("RUTAS",[
    '/logica/clientes/rifas/'
]);
//-----------------------------------------
//CONFIGURACION DE PROYECTO FIRA 
    define(
        "RIFA" , [
            "SOPORTE" =>  [
                "LLAMADA" => '+595982110190',
                "EMAIL" => 'saldivarcristian@hotmail.com',
                "MENSAJE_TEXTO" => '+595982110190',
                "MENSAJE_WSP" => '+595982110190'
            ]
        ]
    );
//-----------------------------------------


