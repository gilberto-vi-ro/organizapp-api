<?php

	#Configuracion de acceso a la base de datos
	define('SGDB','mysql');
	define('DB_HOST','localhost');
	define('DB_PORT','3306'); # Default: 3306 -- Mac: 8889
	define('DB_NAME','organizapp');
	define('DB_USER','root');
	define('DB_PWD','');
	define('DB_CHARSET',"SET NAMES 'utf8'");


	#Ruta base de url
	define("BASE_URL", "http://127.0.0.1/program/organizapp-api/");
	#Ruta de la app
	define('ROOT_PATH', $_SERVER["DOCUMENT_ROOT"] . "/program/organizapp-api/");
	#Ruta de drive
	define('ROOT_DRIVE', $_SERVER["DOCUMENT_ROOT"] . "/program/organizapp/");
	#Ruta de URL drive
	define('URL_DRIVE', "http://127.0.0.1/program/organizapp/");

	#Paths API.
	define("CONTROLLER_PATH", ROOT_PATH. "mvc/controller/");
	define("MODEL_PATH", ROOT_PATH. "mvc/model/");
	define("LIBRARY_PATH", ROOT_PATH. "library/");

	#Ruta de Archivo cuando hay algun error en la api
	define("_FILE_ERR_PATH", ROOT_PATH. "mvc/view/error/error404.php");
	
	#remove Special Character Url
	define('F_URL', false);
	#remove Special Character Url
	define('DEBUG', false);
	#Controlador con que iniciara la api
	define("CONTROLLER_DEFAULT","HomeController");
	#Metodo con que iniciara la api
	define("METHOD_DEFAULT","index");

	#Quitar caracteristicas obseletas.
	error_reporting(E_ALL & ~E_DEPRECATED);
	
	#Configurar sona horaria.
	date_default_timezone_set('America/Mexico_City');
	setlocale(LC_ALL,"es_ES");
	#activar errores.
	ini_set("display_errors","1");
	
	



	