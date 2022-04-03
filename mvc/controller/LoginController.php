<?php 
	/**
	* 
	*/
	class LoginController	{
		
		private $LoginModel;

		function __construct()
		{
			$this->LoginModel = new LoginModel();
		}
		
		/** login */
		public function login()
		{
			$myData = $this->LoginModel->userExist($_POST);
	        if ($myData && $myData['usuario'] === $_POST['username']){
	           
	            if ( !password_verify(  $_POST['pwd'], $myData['pwd']) ){
					setMsg( "error","Contraseña incorrecta" );
					echo json_encode(getMsg(),JSON_UNESCAPED_UNICODE);exit();
				}

	            # if user and paswword is true(init  session) 
	            session_start();


	            session('data_user', $myData);
	            session('id_usuario', $myData["id_usuario"]);

	            $token = md5( uniqid( mt_rand(),true ) );
	            session( 'token', $token);
				
				$this->LoginModel->setLastTime($myData["id_usuario"]);
	            if ($myData["tipo"]==0)
	            {
					msg("response",[
						"type"=>"success", 
						"id"=> $myData["id_usuario"],
						"msg"=> "Bienvenido como admin",
						"where"=> null,
						"line"=> null 
					]);
					echo json_encode(getMsg(),JSON_UNESCAPED_UNICODE);exit();
				}
	            else
				{
					msg("response",[
						"type"=>"success", 
						"id"=> $myData["id_usuario"],
						"msg"=> "Bienvenido como usuario",
						"where"=> null,
						"line"=> null 
					]);
					echo json_encode(getMsg(),JSON_UNESCAPED_UNICODE);exit();
				}
	        }
	        else{
				setMsg( "error","Usuario incorrecto" );
				echo json_encode(getMsg(),JSON_UNESCAPED_UNICODE);exit();
	        }
		}

		public function registerUser () 
		{
			$_POST["pwd"] = password_hash( $_POST["pwd"], PASSWORD_DEFAULT );
			$response = $this->LoginModel->register($_POST);
		    if ($response == 1){
		    	$this->createFolder();
				setMsg( "success","Usuario registrado correctamente" );
				echo json_encode(getMsg(),JSON_UNESCAPED_UNICODE);exit();
		    }
		   	else if( $response ==2 ){
			    setMsg( "error","El usuario ya existe" );
				echo json_encode(getMsg(),JSON_UNESCAPED_UNICODE);exit();
			}
		   	else{
			   setMsg( "error","Ocurrio un error inesperado" );
			   echo json_encode(getMsg(),JSON_UNESCAPED_UNICODE);exit();
			}
		}

		public  function createFolder() 
	    {   
	    	$FileManager = new FileManager();
			$FileManager->hideExtension(['php','trash']);
	   		$FileManager->setPath("drive/");
			$FileManager->createDir($this->LoginModel->getMaxUser());
	    }

	}


 ?>