<?php 
	/**
	* 
	*/
	class TrashController
	{
		private $FileManager;
		private $TrashModel;

		function __construct()
		{   
			$this->TrashModel = new TrashModel();
			$this->FileManager = new FileManager("drive/".$this->getPathDefault());
			$this->FileManager->hideExtension(['php','trash']);
		}

		public  function index () 
	    {   
	        $idUser = $_REQUEST["id_user"];
			if ( !$this->HomeModel->existIdUser($idUser)){
				echo json_encode(["data"=>"El id no existe"]);exit();
			}	
			
	        $getImg = $this->getImg($idUser);
    		$getName = $this->getName($idUser);
    		$getCode = $idUser;
    		$pathDefault =  "drive/".$idUser."/";

			$data = ["data"=>["img"=>$getImg, "nombre"=> $getName,"id"=>$getCode, "path"=>  $pathDefault ]];
			
			echo json_encode($data);exit();
	    }

	    public  function getImg ($idUser) 
	    {   
	        $dataUser = $this->TrashModel->getUser($idUser);
	        $userImg = $dataUser->img;
	   	 	if ($userImg == null) 
                $img = URL_DRIVE."public/img/icon/user.png"; 
            else 
                $img = $userImg; 
	
	        return $img; 
	    }

	    public  function getName($idUser) 
	    {   

	        $dataUser = $this->TrashModel->getUser($idUser);
	        $nameUser = $dataUser->nombre_completo;
	 
	        return $nameUser; 
	    }

	    private  function pathIgnored($path) 
	    {   
			$search =  $_REQUEST["id_user"];;
			if (!stripos($path, $search) ) 
			 	return true;
			 
    		return $this->FileManager->pathIgnored($path);
	    }

	    public  function getPathDefault() 
	    {   
			$idUser = $_REQUEST["id_user"];
	   		$dp = $this->TrashModel->getPathDefault($idUser);
	   	
			$path = ($dp)? $dp  : "drive/$idUser/";
	        return $path; 
	    }

		/*====================================================
		FILE MANAGER
		====================================================*/
		/**
		* get id file : return id of database of file
		* @param string $oldPathname old pathname of file
		* @return string $idFile id of file
		*/
		private function getIdFile($oldPathname){
			$pathFolder = $this->FileManager->getPath($oldPathname);
			$idFolder = $this->TrashModel->getIdFolder( $this->FileManager->convertToPathname($pathFolder) ,  $_REQUEST["id_user"] );
			$oldName = $this->FileManager->getName( $oldPathname);
			$oldExt = $this->FileManager->getExtension($oldName);
			$idFile = $this->TrashModel->getIdFile($oldName,$oldExt,$idFolder);

			return $idFile;
		}

	    public  function restoreTrash() 
	    {   
			$item = $_REQUEST["item"];
			function replaceTrash($value){
				return str_replace(".trash", "", $value);
			}
	   		
			foreach ( $item as $key => $value) {
				//renombramos en la bd
				$pathName = $this->FileManager->convertToPathname( $value["path_name"] );
				
				if( file_exists(ROOT_DRIVE.$pathName) ) {
					if (is_dir(ROOT_DRIVE.$pathName) )
						$res = $this->TrashModel->restoreFolderOfTrashInDB($pathName, replaceTrash($pathName));
					else{
						$name = $this->FileManager->getName(replaceTrash($pathName));
						$ext = $this->FileManager->getExtension($name);
						$res = $this->TrashModel->restoreFileOfTrashInDB($name, $ext, $this->getIdFile($pathName));
					} 

					if ($res) setMsg( "success","Se restauro correctamente en la BD." );
					else {setMsg( "error","ocurrio un error al restaurar en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() ); continue;
					}
					
					//renombramos en el FileManager
					$res2 = $this->FileManager->rename( $pathName , replaceTrash($pathName) );

					if ($res2) setMsg( "success","Se restauro correctamente en el Gestor." );
					else setMsg( "error","ocurrio un error al restaurar en el Gestor.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
				}else{
					setMsg( "error","$pathName : pathname not found.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );	continue;
				}
			}
			print_r( json_encode(getMsg()));
			exit();
	    }

	    public  function deleteTrash() 
	    {   
			$item = $_REQUEST["item"];
			
			foreach ( $item as $key => $value) {
				//borramos en la bd
				$pathName = $this->FileManager->convertToPathname( $value["path_name"]  );
				if( file_exists(ROOT_DRIVE.$pathName) ) {
					if (is_dir(ROOT_DRIVE.$pathName) )
						$res = $this->TrashModel->deleteFolderInDB($pathName);
					else
						$res = $this->TrashModel->deleteFileInDB($this->getIdFile($pathName));	
					

					if ($res) setMsg( "success","Se elimino correctamente en la BD." );
					else {setMsg( "error","ocurrio un error al eliminar en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );		continue;
					}
					//borramos en el FileManager
					$res2=$this->FileManager->delete( $value["path_name"] );

					if ($res2) setMsg( "success","Se elimino correctamente en el Gestor." );
					else setMsg( "error","ocurrio un error al eliminar en el Gestor.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
				}else{
					setMsg( "error","$pathName : pathname not found.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );	continue;
				}
			}
			print_r( json_encode(getMsg()));
			exit();
	    }

	    public  function listAllTrash () 
	    {   
			$pathname = $_REQUEST["pathname"];
	    	if ( $this->pathIgnored($pathname) ) {
	    		$pathname = $this->getPathDefault();
	    	}
   			$this->FileManager->setPath($pathname);
   			$this->FileManager->hideExtension(['php']);
    		echo json_encode($this->FileManager->listAllTrash());
	   		exit();
	   		
	    }

	    public  function searchTrash () 
	    {   
			$pathname = $_REQUEST["pathname"];
			$search = $_REQUEST["search"];
	    	if ( $this->pathIgnored($pathname) ) {
	    		$pathname = $this->getPathDefault();
	    	}
   			$this->FileManager->setPath($pathname);
   			$this->FileManager->hideExtension(['php']);
    		echo json_encode($this->FileManager->listSearch($search,true));
	   		exit();
	   		
	    }

	    public  function getValues() 
	    {   
    		echo json_encode($this->FileManager->getValuesConfig()) ;
	    	exit();
	    }

	}

 ?>

