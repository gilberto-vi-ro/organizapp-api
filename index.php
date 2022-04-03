<?php

	/* Cargar librerias */
	require_once 'config.php';
	//include_once "../library/FileManager.php";
	include_once LIBRARY_PATH."tool.php";
	require_once LIBRARY_PATH.'autoload.php';

	/* Instanciamos la api */
	$api = new Core;
	/* Iniciamos la api*/
	$api->render();



?>