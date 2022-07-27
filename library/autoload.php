<?php

	/**
	* @category    Cargar librerias.
	* @package     Library/autoload
	* @author      Gilberto Villarreal Rodriguez  <Gil_yeung@outlook.com>
    * @link        https://myproyecto.com/
    * @license     License Open Source
	* @description El objetivo es cargar los achivos que se requieran dependiendo de las clases.
    * @see     	   link de documentacion
    * @since       12/2/2019
	* @version     3.1.0
	*/

	
	/* Funcion para determinar si existe dicha clase */ 
	spl_autoload_register(function($class) 
	{	

			$path1 = LIBRARY_PATH.$class.".php";
			$path2 = CONTROLLER_PATH.$class.".php";
			$path3 = MODEL_PATH.$class.".php";
			
			if(file_exists($path1))
				require_once $path1;
			elseif (file_exists($path2))
				require_once $path2;
			elseif (file_exists($path3))
				require_once $path3;
			else{
				die ('>>> Autoload: "'.$class.'" Class not found');
			}
	});




?>