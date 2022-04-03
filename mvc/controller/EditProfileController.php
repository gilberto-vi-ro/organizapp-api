<?php 
	/**
	* 
	*/
	class EditProfileController	{
		
		/*

		function __construct()
		{
		
		}
		*/
		public  function index () 
	    {   
	        $idUser = $_REQUEST["id_user"];
			$EditProfileModel = new EditProfileModel();
			if ( !$this->EditProfileModel->existIdUser($idUser)){
				echo json_encode(["data"=>"El id no existe"]);exit();
			}	
			
	        $getImg = $this->getImg($idUser);
    		$getName = $this->getName($idUser);
    		$getCode = $idUser;
    		$pathDefault =  "drive/".$idUser."/";

			$data = ["data"=>["img"=>$getImg, "nombre"=> $getName,"id"=>$getCode, "path"=>  $pathDefault ]];
			
			echo json_encode($data);exit();
	    }
	
		public function updateProfile()
		{
			$EditProfileModel = new EditProfileModel();
			$idUser = $_REQUEST["id_user"];
			if ( !$EditProfileModel->existIdUser($idUser)){
				echo json_encode(["data"=>"El id no existe"]);exit();
			}

			$nameImg = "null";
			if ($_REQUEST["img_changed"]) {
	        	$nameImg = $this->moveFile(ROOT_DRIVE."public/img/user/");
	        }
	        
	        $dataBD["nombre_completo"] = $_REQUEST["name_c"];
	        $dataBD["pwd"] =  password_hash( $_REQUEST["new_pwd"], PASSWORD_DEFAULT );
 			$dataBD["img"] = "public/img/user/".$nameImg;

	        if ($_REQUEST["new_pwd"]=="default") {
	        	unset($dataBD["pwd"]);
	        }
	        if (!$_REQUEST["img_changed"]) {
	        	unset($dataBD["img"]);
				
	        }

	        
	        if ( $EditProfileModel->updateProfile( $dataBD, $idUser) )
				setMsg( "error","Datos actualizados", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
	        else
				setMsg( "error","Ocurrio un error al Actualizar", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );

				

			echo( json_encode(getMsg()) );exit();
		}

		private  function moveFile ($moveFilePath) 
	    {   
	        #========================= validate if exist img ========================
	        foreach ($_FILES as $keys_name)
	        {
	           $filename = $keys_name["name"];
	           $filetype = $keys_name["type"];
	           $source = $keys_name["tmp_name"];
	           $n = $keys_name["error"];
	           if (!is_uploaded_file($source))
	               return "error: ".$n; 
	           if (!copy($source, $moveFilePath.$filename))
	               return "null"; 
	        }
	        #================================ end img ================================
	        return $filename; 
	    }


	    public  function getImg ($idUser) 
	    {   

	   		$EditProfileModel = new EditProfileModel();
	        $dataUser = $EditProfileModel->getUser($idUser);
	        $userImg = $dataUser->img;
	   	 	if ($userImg == null) 
                $img = URL_DRIVE."public/img/icon/user.png"; 
            else 
                $img = $userImg; 
	
	        return $img; 
	    }
	    public  function getName($idUser) 
	    {   

	   		$EditProfileModel = new EditProfileModel();
	        $dataUser = $EditProfileModel->getUser($idUser);
	        $nameUser = $dataUser->nombre_completo;
	 
	        return $nameUser; 
	    }
		
	}

 ?>