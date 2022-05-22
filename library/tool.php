<?php 

    /**
    * @category    Herramienta principal.
    * @package     library/tool
    * @author      Gilberto Villarreal Rodriguez  <Gil_yeung@outlook.com>
    * @link        https://sitegl.com/
    * @license     https://www.sitegl.com/framework-gl/?license New  License Open Source
    * @description Emplear metodos o funciones en general es decir para todos los controllers.
    * @see         link de documentacion
    * @since       Fecha de elaboracion: 13/2/2019
    * @version     3.0.0 API
    */
    
   
 	/**
	* Redirecciona la pagina.
    * @param string $url url a la que sera redireccionado
    * @param int $time tiempo de espera en segundos
    * @param bool $header = true, establecer true para redireccionar con php y false para redireccionar con js.
	* 
	*/
	function href( $url, $time = null , $header = true) : void
	{
		if ($time == null || $time <= 0) {
			if ($header) {
				header("Location: " . $url);
			}else{
				echo "<script>window.location.href='".$url."';</script> ";
			}
			
		}else{
			if ($header) {
				header("refresh:".$time.";url=" . $url);
			}else{
				$time = $time*1000;
				echo "<script> function contar(){ window.location.href='".$url."'; }
                           setInterval('contar()',".$time.");
                    </script>";
			}
		}
		exit();
	}


	/**
    * Agregar devolver una sesion
    * @param string $key nombre de la sesion
    * @param string $value valor de la sesion
    * @return string  la sesion
	* @return array las sesionoes
	*/
	
	function session($key = "*all*", $value = "*null*") 
	{
		if ($value === "*null*" )
		{
			if ( $key === "*all*" )
					return $_SESSION;
			if ( isset($_SESSION[$key]) )
				return $_SESSION[$key] ; 
			else
				return 0;
		} 
		else
			$_SESSION[$key] = $value;
			
	}

	

	/**
    * Crea una imagen que este en base64
    * @param string $filepath nombre y ruta de archivo sin extencion a guardar
    * @param string $base64_string img base64
	* @return string nombre y ruta del archivo
	*/
	function base64ToImage($filepath, $base64_string) 
	{
		$data = explode(',', $base64_string);
		$img = base64_decode($data[1]);
		$type = substr( $data[0] , 11, -7 );
		$filename = $filepath.".".$type;

		if (file_exists(ROOT_PATH.$filename))
			unlink(ROOT_PATH.$filename);
		if (file_put_contents( ROOT_PATH.$filename , $img ) ) 
			return $filename;
		else
			return 'Tool >>> Error at create '.$filename;
		 
	}

	/**
    * Crea un n° de serie y encripta datos para enviar una cadena determinada.
    * @param array or estring data
	* @return array or estring encriptado.
	*/
	function encode($_get)
	{
		$data = serialize($_get); //n° serie para enviar un n° de cadena de datos. 
		$data = urlencode($data);
		$data = base64_encode($data);  
		return $data;
	}

	/**
    * Cuenta el n° de serie y desencripta datos para recivir la misma cadena.
    * @param array or string data
	* @return array or string desencriptado.
	*/
	function decode($_get)
	{
        $data = base64_decode($_get); //n° serie para recivir un de n° cadena de datos.
		$data = urldecode($data);
        $data = unserialize($data);
        return $data;
    }

   /**
    * Genera una contraseña aleatoria.
    * @param int $length Longitud de caracter
	* @return string $password
	*/
    function generatePassword($length = 8)
    {
		$pattern = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            $max = strlen($pattern)-1;
            $password = '';
            for ($x =0 ; $x < $length; $x++) {
                $position = rand(0, $max);
                $password .= substr($pattern, $position, 1);
            }
            return $password;
	}

	/**
    * Genera una token aleatoria.
	* @return string $token
	*/
	function generateToken($length = 8)
	{
        return generatePassword($length);
	}

	/**
	* Elimina los caracteres especiales de una cadena.
	* @param	string	 $value			Cadena a evaluar.
	* @return	string	 $stringClean	Cadena limpia de caracteres especiales.
	*/
	function stringClean($value) 
	{
		$stringClean =  str_replace(
					        array( "¨", "º", "~", ";",
					               "#", "|", "!", "¡",
					               "%", "(", ")", "$",
					               "¿", "<?", "[",  "]",
					               "{", "}", ">", "<", 
					               "´", "`", "'", "\"",
					             ),
					        '',
					        $value
					    );

		return $stringClean;
	}

	/**
	* Escapa caracteres especiales de una cadena.
	* @param	string	 $value			Cadena a evaluar.
	* @return	string	 $string	Cadena escapada de caracteres especiales.
	*/
	function stringScape($val){
        $string = str_replace(["\\","'",'"'],["\\\\","\\'",'\\"'],$val); 
        return $string;
    }

	/**
	* Devuelve la fecha o fecha y hora del momento.
	* @param  string $format  formato de fecha.
	* @return string $date 	Formato de fecha.
	* @return string $dateTime 	Formato de fecha y hora.
	*/
	function now($format="Y-m-d H:i:s")
	{
		return date($format);
	}

	/**
	* Devuelve la fecha o fecha y hora para manana.
	* @param  string $format  formato de fecha.
	* @return string $date 	Formato de fecha.
	* @return string $dateTime 	Formato de fecha y hora.
	*/
	function tomorrow($format="Y-m-d H:i:s")
	{
		$currentDate = date($format);
		//sumo 1 día
		return date($format,strtotime($currentDate."+ 1 days")); 
	}
	/**
	* Escapa caracteres especiales de una cadena para la base de datos.
	* @param	string	 $value			Cadena a evaluar.
	* @return	string	 $string	Cadena escapada de caracteres especiales para la base de datos.
	*/
	function stringScapeDB($val){
        $string = str_replace("'","\'",$val);
        return $string;
    }

	/**
    * Agregar un mensage global
    * @param string $key clave del mensaje
    * @param string $value valor del mensaje
    * @return string el mensage
	* @return array los mensajes
	*/
	
	function msg($key = "*all*", $value = "*null*") 
	{
		if (!isset($GLOBALS["msg"]))
			$GLOBALS["msg"] = array();
		if (is_array($key)){
			$GLOBALS["msg"]["response"][] = $key;
		}
		else if ($value === "*null*" )
		{
			if ( $key === "*all*" )
					return $GLOBALS["msg"];
			if ( isset($GLOBALS["msg"][$key]) )
				return $GLOBALS["msg"][$key] ; 
			else
				return 0;
		} 
		else
			$GLOBALS["msg"][$key] = $value;
	}

	function setMsg($type, $msg, $where = null, $line = null) {
		msg([
				"type"=>$type, 
				"msg"=> $msg,
				"where"=> $where,
				"line"=> $line
			]);
	}

	function getMsg() {
		return msg();
	}


