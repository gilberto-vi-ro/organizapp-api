<?php 
	/**
	* 
	*/
	class HomeController
	{
		private $FileManager;
		private $HomeModel;
		//private $idUser;

		function __construct()
		{
			$this->HomeModel = new HomeModel();
			$this->FileManager = new FileManager("drive/1000");
			$this->FileManager->hideExtension(['php','trash']);
			///$this->idUser = session("id_usuario");
		}

		public  function index () 
	    {   
			$idUser =  $_REQUEST["id_user"];
			if ( !$this->HomeModel->existIdUser($idUser)){
				echo json_encode(["data"=>"El id no existe"]);exit();
			}	
			
	        $getImg = $this->getImg($idUser);
    		$getName = $this->getName($idUser);
    		$getCode = $idUser;
    		$pathDefault =  $this->getPathDefault($idUser);

			$data = ["data"=>["img"=>$getImg, "nombre"=> $getName,"id"=>$getCode, "path"=>  $pathDefault ]];
			
			echo json_encode($data);
	    }

	    public  function getImg ($idUser) 
	    {   
	        $dataUser = $this->HomeModel->getUser($idUser);
	        $userImg = $dataUser->img;
	   	 	if ($userImg == null) 
                $img = URL_DRIVE."public/img/icon/user.png"; 
            else 
                $img = $userImg; 
	
	        return $img; 
	    }
	    public  function getName($idUser) 
	    {   
	        $dataUser = $this->HomeModel->getUser($idUser);
	        $nameUser = $dataUser->nombre_completo;
	 
	        return $nameUser; 
	    }

	    public  function getPathDefault() 
	    {   
			$idUser = $_REQUEST["id_user"];
	   		$dp = $this->FolderModel->getPathDefault($idUser);
			$path = ($dp)? $dp  : "drive/$idUser/";
	        return $path; 
	    }

	    private  function pathIgnored($path) 
	    {   
			$search = $_REQUEST["id_user"];
			if (!stripos($path, $search) ) 
			 	return true;
			 
    		return $this->FileManager->pathIgnored($path);
	    }

		public  function getPathFile() 
	    {   
			$idFile = $_REQUEST["id_file"];
    	    $path = $this->HomeModel->getPathFileInDB($idFile);
			if ($path!==null)
				msg("response",[
					"type"=>"success", 
					"path"=> $path,
					"where"=> null,
					"line"=> null 
				]);
			else
				setMsg( "error","El id no existe",  __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() ); 
			print_r( json_encode(getMsg()) );
			exit();
	    }

		/*====================================================
		FILE MANAGER
		====================================================*/
		public  function upload($pathname, $idFolder, $_FILE_ ) 
	    {   
			// print_r($_FILE_);
			$file["name"] = $_FILE_["file_data"]["name"];
			$file["extension"] = $this->FileManager->getExtension($_FILE_["file_data"]["name"]);
			$file["size"] = $_FILE_["file_data"]["size"];
			//print_r($file["extension"]);
	    	if ( $this->pathIgnored($pathname) ) {
	    		$pathname = $this->getPathDefault();
	    	}
	   		$this->FileManager->setPath($pathname);
			//agregamos en la BD
			$res = $this->HomeModel->reInsertFileInDB( $file, $idFolder );
			if ($res) {
				setMsg( "success","Se guardo correctamente en la BD." );
				//agregamos en FileManager
				$res2 = $this->FileManager->uploadFile( $_FILE_ );
				if ($res2) setMsg( "success","Se cargo correctamente en el Gestor." );
				else setMsg( "error",$this->FileManager->getMsg("msg"), $this->FileManager->getMsg("where") ,$this->FileManager->getMsg("line") );
			}
			else setMsg( "error","ocurrio un error al guardar en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			
			print_r( json_encode(getMsg()) );
	        //exit();
	    }

	    public  function addNewTask() 
	    {   
			$data = $_REQUEST;
	    	$pathname = $this->FileManager->convertToPathname($data['path_name']);
	    	$idFolder = $this->HomeModel->getIdFolder($pathname,  $_REQUEST["id_user"]);
	    	$data["id_folder"] = $idFolder;
		
	    	$res = $this->HomeModel->addNewTask($data, $idFolder);
			if ($res === 2) setMsg( "error","El nombre de la tarea ya existe en la BD.",  __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() ); 
			else if ($res) {
	    		setMsg( "success","La tarea se agrego en la BD." ); 
	    	}else{
	    		setMsg( "error","ocurrio un error al agregar la tarea en la BD.",  __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() ); 
	    	}
			print_r( json_encode(getMsg()) );
	    	exit();
	    }

		public  function editStatusTask() 
	    {   
			$data = $_REQUEST;
			//renombramos en la bd
			$res = $this->HomeModel->editStatusTaskInDB($data);
			if ($res) {
	    		setMsg( "success","La tarea se actualizó en la BD." ); 
	    	}else{
	    		setMsg( "error","ocurrio un error al editar la tarea en la BD.",  __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() ); 
	    	}
			print_r( json_encode(getMsg(),JSON_UNESCAPED_UNICODE) );
			exit();
	    }
		public  function editTask() 
	    {   
			$data = $_REQUEST;
			$GLOBALS["id_file"]=null;
			if (isset($_FILES["file_data"])){
				if ( $_FILES["file_data"]["name"] != null || $_FILES["file_data"]["name"] != "" )
					$this->upload($data["pathname"],$data["id_carpeta"], $_FILES);
			}
			//renombramos en la bd
			$res = $this->HomeModel->editTaskInDB($data);
			if ($res === 2) setMsg( "error","La tarea ya existe en la BD.",  __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() ); 
			else if ($res) {
	    		setMsg( "success","La tarea se edito en la BD." ); 
	    	}else{
	    		setMsg( "error","ocurrio un error al editar la tarea en la BD.",  __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() ); 
	    	}
			print_r( json_encode(getMsg()) );
			exit();
	    }

		public  function deleteTask() 
	    {   
			$item = $_REQUEST['item'];
			foreach ( $item as $key => $value) {
				//borramos en la bd
				$res = $this->HomeModel->deleteTaskInDB($value["id_tarea"]);
				if ($res) setMsg( "success","Se elimino correctamente en la BD." );
				else setMsg("error","ocurrio un error al eliminar en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			}
			print_r( json_encode(getMsg()) );
			exit();
	    }

	    /*=========================================================================
	    LIST
	    ===========================================================================*/

	    public  function listFolder() 
	    {   
			$path = $idUser = $_REQUEST["path"];
	    	if ( $this->pathIgnored($path) ) {
	    		$path = $this->getPathDefault();
	    	}
   			$this->FileManager->setPath($path);
    		echo json_encode($this->FileManager->listAll());
	   		exit();
	   		
	    }

	    public  function listTaskPending() 
	    {   
			$pathname = $_REQUEST["path"];
			$priority = $_REQUEST["priority"];
			$search = $_REQUEST["search"];
			$range = $_REQUEST["range"];
			
	    	if ( $this->pathIgnored($pathname) ) {
	    		$pathname = $this->getPathDefault();
	    	}
			$pathname = $this->FileManager->convertToPathname($pathname);
   			$list = [];
   			$result = [];
   			$res=$this->HomeModel->getTaskPending($pathname, $priority, $search, $range);
   			if ($res)
   				$list = [ 'type' => "success", 'path' => $pathname, 'data' => $res ];
   			else 
   				$list = [ 'type' => "error", 'path' => $pathname, 'data' => $result ];
    		echo json_encode($list);

	   		exit();
	   		
	    }
	    public  function listTaskDone() 
	    {   
			$pathname = $_REQUEST["path"];
			$priority = $_REQUEST["priority"];
			$search = $_REQUEST["search"];
			$range = $_REQUEST["range"];

	    	if ( $this->pathIgnored($pathname) ) {
	    		$pathname = $this->getPathDefault();
	    	}
			$pathname = $this->FileManager->convertToPathname($pathname);
	    	$list = [];
   			$result = [];
   			$res=$this->HomeModel->getTaskDone($pathname, $priority, $search, $range);
   			if ($res)
   				$list = [ 'type' => "success", 'path' => $pathname, 'data' => $res ];
   			else 
   				$list = [ 'type' => "error", 'path' => $pathname, 'data' => $result ];
    		echo json_encode($list);

	   		exit();
	   		
	    }
	    public  function listTaskDelivered() 
	    {   
			$pathname = $_REQUEST["path"];
			$priority = $_REQUEST["priority"];
			$search = $_REQUEST["search"];
			$range = $_REQUEST["range"];
	    	if ( $this->pathIgnored($pathname) ) { 
	    		$pathname = $this->getPathDefault();
	    	}
			$pathname = $this->FileManager->convertToPathname($pathname);
   			$list = [];
   			$result = [];
   			$res=$this->HomeModel->getTaskDelivered($pathname, $priority, $search, $range);
   			if ($res)
   				$list = [ 'type' => "success", 'path' => $pathname, 'data' => $res ];
   			else 
   				$list = [ 'type' => "error", 'path' => $pathname, 'data' => $result ];
    		echo json_encode($list);

	   		exit();
	   		
	    }
		
	}

 ?>