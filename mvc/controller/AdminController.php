<?php 
	/**
	* 
	*/
	class AdminController	{
		
		private $AdminModel;

		function __construct()
		{
			$this->AdminModel = new AdminModel();
		}
		
		public  function index () 
	    {   
	        $idUser = $_REQUEST["id_user"];
			if ( !$this->AdminModel->existIdUser($idUser)){
				echo json_encode(["data"=>"El id no existe"]);exit();
			}	
			
	        $getImg = $this->getImg($idUser);
    		$getName = $this->getName($idUser);
    		$getCode = $idUser;
    		$pathDefault =  "drive/".$idUser."/";

			$data = ["data"=>["img"=>$getImg, "nombre"=> $getName,"id"=>$getCode, "path"=>  $pathDefault ]];
			
			echo json_encode($data);exit();
	    }
	
		public function listUsers()
		{
			$mydata = $this->AdminModel->listUsers();
			echo json_encode(["data"=>$mydata]);
	       
		}

		public function deleteUser()
		{
			$id = $_REQUEST["id_user"];
			$AdminModel = new AdminModel();
			if ( !$this->AdminModel->existIdUser($id)){
				echo json_encode(["data"=>"El id no existe"]);exit();
			}

			if ( $this->isAdmin($id) )
				setMsg( "success","No se puede eliminar al admin.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			

			if ($AdminModel->deleteUser($id)>0) {
				$FileManager = new FileManager("drive");
				$FileManager->delete( "drive/".$id."/" );
				setMsg( "success","Usuario eliminado.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			}
			else
				setMsg( "error","Ocurrio un error al eliminar.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );

			echo( json_encode(getMsg()) );exit();
		}

		private function isAdmin($id)
		{
		
			$obj = $this->AdminModel->getType($id);

	      	if ( $obj->tipo == 0)
	      		return true;
	      	else
	      		return false;
	      	
		}

		public  function getImg ($idUser) 
	    {   
	        $dataUser = $this->AdminModel->getUser($idUser);
	        $userImg = $dataUser->img;
	   	 	if ($userImg == null) 
                $img = URL_DRIVE."public/img/icon/user.png"; 
            else 
                $img = $userImg; 
	
	        return $img; 
	    }
	    public  function getName($idUser) 
	    {   
	        $dataUser = $this->AdminModel->getUser($idUser);
	        $nameUser = $dataUser->nombre_completo;
	 
	        return $nameUser; 
	    }

		
	}

 ?>