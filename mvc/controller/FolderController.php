<?php 
	/**
	* 
	*/
	class FolderController
	{
		private  $FileManager;
		private  $FolderModel;

		function __construct()
		{   
			$this->FolderModel = new FolderModel();
			$this->FileManager = new FileManager($this->getPathDefault());
			$this->FileManager->hideExtension(['php','trash']);
			
		}

		public  function index () 
	    {   
	        $idUser =  $_REQUEST["id_user"];
			if ( !$this->FolderModel->existIdUser($idUser)){
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
	        $dataUser = $this->FolderModel->getUser($idUser);
	        $userImg = $dataUser->img;
	   	 	if ($userImg == null) 
                $img = URL_DRIVE."public/img/icon/user.png"; 
            else 
                $img = $userImg; 
	
	        return $img; 
	    }
	    public  function getName($idUser) 
	    {   

	        $dataUser = $this->FolderModel->getUser($idUser);
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

	    public  function showFile() 
	    {   
			$pathname = $_REQUEST["pathname"];
			$extension = $_REQUEST["extension"];
	    	if ( $this->pathIgnored($pathname) ) {
	    		$pathname = $this->getPathDefault();
	    	}

	    	$res = "null";
	    	switch ($extension) {
	    		case 'png':
	    			$res = URL_DRIVE.$pathname; 
	    			break;

	    		case 'jpg':
	    			$res = URL_DRIVE.$pathname; 
	    			break;

	    		case 'gif':
	    			$res = URL_DRIVE.$pathname; 
	    			break;

	    		case 'pdf':
	    			$res = URL_DRIVE.$pathname;
	    			break;
	    		case 'movil-pdf':
	    			$res = URL_DRIVE.$pathname;
	    			break;

	    		case 'mp3':
	    			$res = URL_DRIVE.$pathname;
	    			break;

	    		case 'mp4':
	    			$res = URL_DRIVE.$pathname;
	    			break;

	    		case 'webm':
	    			$res = URL_DRIVE.$pathname;
	    			break; 
	    		case 'txt':
	    			$text = file_get_contents(ROOT_DRIVE.$pathname); 
					$text = nl2br($text); 
	    			$res = $text;
	    			break;  

	    		default:
	    			$res = "Archivo no soportado.";
	    			break;
	    	}

	    	print_r($res);

	        exit();
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
			$idFolder = $this->FolderModel->getIdFolder( $this->FileManager->convertToPathname($pathFolder) , $_REQUEST["id_user"]);
			$oldName = $this->FileManager->getName( $oldPathname);
			$oldExt = $this->FileManager->getExtension($oldName);
			$idFile = $this->FolderModel->getIdFile($oldName,$oldExt,$idFolder);
			return $idFile;
		}
		public  function upload($pathname, $_FILE_) 
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
			$idFolder = $this->FolderModel->getIdFolder($pathname, $_REQUEST["id_user"]);
			$res = $this->FolderModel->reInsertFileInDB( $file, $idFolder );
			if ($res) {
				setMsg( "success","Se guardo correctamente en la BD." );
				//agregamos en FileManager
				$res2 = $this->FileManager->uploadFile( $_FILE_ );
				if ($res2) setMsg( "success","Se cargo correctamente en el Gestor." );
				else setMsg( "error",$this->FileManager->getMsg("msg"), $this->FileManager->getMsg("where") ,$this->FileManager->getMsg("line") );
			}
			else setMsg( "error","ocurrio un error al guardar en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			
			print_r( json_encode(getMsg()) );
	        exit();
	    }

		public  function download() 
	    {   
			$pathname = $_REQUEST["pathname"];
			$isDir = $_REQUEST["is_dir"];
	    	if ( $this->pathIgnored($pathname) ) {
	    		$pathname = $this->getPathDefault();
	    	}
		
	   		if ($isDir){
	   			echo "Espere mientras zipiamos la carpeta...";
				   $file['name']= $this->FileManager->getName($pathname).".zip";
				   $file['size']=0;
				   $file['extension']="zip";
				   $myPathname = $this->FileManager->getPath($pathname);
				   $idFolder = $this->FolderModel->getIdFolder(substr($myPathname,0,-1), $this->idUser);
				   $res = $this->FolderModel->reInsertFileInDB( $file, $idFolder );
				   if ($res) echo"Se guardo correctamente en la BD";
				   else echo "ocurrio un error al guardar en la BD";
	   			$this->FileManager->downloadFolder($pathname);
	   		}
	   		else{
	   			echo "Espere mientras preparomos el archivo...";
	   			$this->FileManager->downloadFile($pathname);
	   		}
	        exit();
	    }

	    public  function createFolder() 
	    {   
			$pathname = $_REQUEST["pathname"];
			$name = $_REQUEST["name"];
	    	if ( $this->pathIgnored($pathname) ) {
	    		$pathname = $this->getPathDefault();
	    	}

			$data['path'] = $this->FileManager->convertToPath($pathname);
			$data['name'] = $name;
			$data['id_user'] = $_REQUEST["id_user"];
			$res = $this->FolderModel->createFolderInDB($data);

			if($res==2) {setMsg( "error","La carpeta ya existe en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );}
			else if($res==0) {setMsg( "error","Ocurrio un error al crear en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );}
			else{
				setMsg( "success","La carpeta se creo en la BD.") ;
				$this->FileManager->setPath($pathname);
				$res2 = $this->FileManager->createDir($name);
				if ($res2) setMsg( "success","La carpeta se creo en el Gestor." );
				else setMsg( "error",$this->FileManager->getMsg("msg"), $this->FileManager->getMsg("where") ,$this->FileManager->getMsg("line") );
			}
			print_r( json_encode(getMsg()) );
	        exit();
	    }

	    public  function rename() 
	    {   
			$oldPathname = $_REQUEST["oldPathname"];
			$newname = $_REQUEST["newName"];
			if ($oldPathname == "drive/".$_REQUEST["id_user"]."/" || $this->pathIgnored($oldPathname)){
				setMsg( "error","No se puede renombrar la carpeta raiz", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			}else{
				//renombramos en la bd
				$path = $this->FileManager->getPath($oldPathname);
				$newPathname = $path.$newname;
				if( file_exists(ROOT_DRIVE.$oldPathname) ) {
					if( is_dir(ROOT_DRIVE.$oldPathname) ) 
					$res = $this->FolderModel->renameFolderInDB($oldPathname, $newPathname);
					else {
						$idFolder = $this->FolderModel->getIdFolder( $this->FileManager->convertToPathname($path) , $_REQUEST["id_user"]);
						$newExt = $this->FileManager->getExtension($newname);
						$res = $this->FolderModel->renameFileInDB($idFolder, $newname, $newExt, $this->getIdFile($oldPathname));
					}

					if ($res === 2) setMsg( "error","El nombre ya existe en la BD.",  __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() ); 
					else if ($res) {
						setMsg( "success","Se renombro correctamente en la BD." );
						//renombramos en el FileManager
						$res2 = $this->FileManager->rename($oldPathname, $newPathname);

						if ($res2) setMsg( "success","Se renombro correctamente en el Gestor." );
						else setMsg( "error",$this->FileManager->getMsg("msg"), $this->FileManager->getMsg("where") ,$this->FileManager->getMsg("line") );
					}
					else setMsg( "error","ocurrio un error al renombrar en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
				}else{
					setMsg( "error","Not found ", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
				}
				
			}

			print_r( json_encode(getMsg()) );
			exit();
	    }

	    public  function move() 
	    {   
			$_newPathname = $_REQUEST["newPathname"];
			$item = $_REQUEST["item"];
	   		foreach ( $item as $key => $value) {
				//renombramos en la bd
				$oldPathname = $this->FileManager->convertToPathname($value["path_name"]);
				$newPathname = $this->FileManager->convertToPath($_newPathname).$value["name"] ;
				
				if( file_exists(ROOT_DRIVE.$oldPathname) ) {
					if ( is_dir(ROOT_DRIVE.$oldPathname) )
						$res = $this->FolderModel->updatePathFolderInDB($oldPathname, $newPathname);
					else
						$res = $this->FolderModel->updatePathFileInDB( $this->getIdFile($oldPathname), $this->FileManager->convertToPathname($_newPathname) );
					

					if ($res) setMsg( "success","Se movio correctamente en la BD." );
					else {setMsg( "error","ocurrio un error al mover en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );		continue;
					}
					//renombramos en el FileManager
					$res2 = $this->FileManager->move( $oldPathname , $newPathname );
					if ($res2) setMsg( "success","Se movio correctamente en el Gestor." );
					else setMsg( "error",$this->FileManager->getMsg("msg"), $this->FileManager->getMsg("where") ,$this->FileManager->getMsg("line") );
				}else{
					setMsg( "error","$oldPathname : not found.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );	continue;
				}
			}
			print_r( json_encode(getMsg()) );
			exit();
	    }

	    public  function delete( ) 
	    {  
			$item = $_REQUEST["item"];
			foreach ( $item as $key => $value) {
				//renombramos en la bd
				$pathName = $this->FileManager->convertToPathname($value["path_name"]);
				
				if( file_exists(ROOT_DRIVE.$pathName) ) {
					if (is_dir(ROOT_DRIVE.$pathName) )
						$res=$this->FolderModel->updateFolderToTrashInDB($pathName );
					else{
						$name = $this->FileManager->getName($pathName);
						//$ext = $this->FileManager->getExtension($name);
						$res = $this->FolderModel->updateFileToTrashInDB($name.".trash", "trash", $this->getIdFile($pathName));
					} 
					if ($res) setMsg( "success","Se elimino correctamente en la BD." );
					else {setMsg( "error","ocurrio un error al eliminar en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );	continue;
					}
					
					//renombramos en el FileManager
					$res2 = $this->FileManager->rename( $pathName, $pathName.".trash" );

					if ($res2) setMsg( "success","Se elimino correctamente en el Gestor." );
					else setMsg( "error",$this->FileManager->getMsg("msg"), $this->FileManager->getMsg("where") ,$this->FileManager->getMsg("line") );
				}else{
					setMsg( "error","not found.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );	continue;
				}
			}
			print_r( json_encode(getMsg()) );
			exit();
	    }


	

	    public  function listAll ()
	    {   
			$pathname = $_REQUEST["pathname"];
	    	if ( $this->pathIgnored($pathname) ) {
	    		$pathname = $this->getPathDefault();
	    	}
   			$this->FileManager->setPath($pathname);
    		echo json_encode($this->FileManager->listAll());
	   		exit();
	   		
	    }

	    public  function search ()
	    {   
			$pathname = $_REQUEST["pathname"];
			$search = $_REQUEST["search"];
	    	if ( $this->pathIgnored($pathname) ) {
	    		$pathname = $this->getPathDefault();
	    	}
   			$this->FileManager->setPath($pathname);
   			$this->FileManager->hideExtension(['php','trash']);
    		echo json_encode($this->FileManager->listSearch($search));
	   		exit();
	   		
	    }

	    public  function getValues() 
	    {   
    		json_encode($this->FileManager->getValuesConfig()) ;
	    	exit();
	    }



	}
