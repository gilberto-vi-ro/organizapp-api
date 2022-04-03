<?php 


class FolderModel extends DB
{
	public function existIdUser($id){
		try {

			$this->prepare("SELECT id_usuario FROM usuario WHERE id_usuario=? ");
			$this->bindParam(1, $id);
			$this->execute();

			if ($this->rowCount()>0)
				return true;
			else
				return false;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function getUser($id){
		try {

			$this->prepare("SELECT nombre_completo, img FROM usuario WHERE id_usuario=$id ");
			$this->execute();
		
			if ($this->rowCount()>0)
				return $this->fetchAll(PDO::FETCH_OBJ)[0];
			else
				return false;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	
	}

	public function getPathDefault($idUser){
		try {

			$this->prepare("SELECT path_name FROM usuario
							INNER JOIN carpeta
							ON 
							usuario.id_usuario = carpeta.id_usuario
							WHERE raiz=true AND usuario.id_usuario='$idUser' ");
			$this->execute();
			if ($this->rowCount()>0)
				return $this->fetchAll(PDO::FETCH_OBJ)[0]->path_name;
			else
				return false;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	
	}

	public function getIdFolder($pathName, $idUser){
		try {
			$this->prepare("SELECT carpeta.id_carpeta FROM usuario
							INNER JOIN carpeta
							ON 
							usuario.id_usuario = carpeta.id_usuario
							WHERE path_name=? AND usuario.id_usuario=? ");
			$this->bindParam(1,$pathName);
			$this->bindParam(2,$idUser);
			$this->execute();
			if ($this->rowCount()>0)
				return $this->fetchAll(PDO::FETCH_OBJ)[0]->id_carpeta;
			else
				return false;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function createFolderInDB($array){
		try {
			$data['path'] = $array['path'];
			$data['path_name'] = $array['path'].$array['name'];
			$data['nombre'] = $array['name'];
			$data['descripcion'] = "...";
			$data['raiz'] = 0;
			$data['archivado'] = 0;
			$data['fecha_archivado'] = null;
			$data['id_usuario'] = $array['id_user'];

			$dataSearch['path'] = $data['path'];
			$dataSearch['nombre'] = $data['nombre'];

			if( $this->existsData("carpeta",$dataSearch )->response )
				return 2;//si ya existe
		
			$this->insert("carpeta",$data);

			return $this->response;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function reInsertFileInDB($file, $idFolder){
		try {
			#=====================exists tarea=========================
			$this->prepare(" SELECT * FROM archivo WHERE id_carpeta= ? AND nombre = ? AND extension = ? ; "); 
			$this->bindParam(1,$idFolder);
			$this->bindParam(2,$file['name']);
			$this->bindParam(3,$file['extension']);
			$this->execute();
			if ($this->rowCount()>0)
				return 2;
			#=====================insert file=========================
			$data['nombre'] = $file['name'];
			$data['size'] = $file['size'];
			$data['extension'] = $file['extension'];
			$data['descripcion'] = "...";
			$data['archivado'] = 0;
			$data['fecha_archivado'] = null;
			$data['id_carpeta'] = $idFolder;
			$this->insert("archivo", $data);
			
			#=====================error=========================
			// if (!$this->response){
			// 	print_r($this); exit();
			// }
			#=====================return=========================
			return $this->response;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	private function getMaxFile(){
		try {
			return $this->getMaxId( "id_archivo" , "archivo");
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}


	public function updatePathFolderInDB($oldPathName, $newPathName){
		try {
			
			$this->prepare("call updatePathname( '$oldPathName', '$newPathName' );");
			$this->execute();
			if ($this->rowCount()>0)
				return true;
			else
				return false;
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function updatePathFileInDB($idFile,$newPathName){
		try {
			
			$this->prepare("SELECT id_carpeta FROM carpeta WHERE path_name = ? ;");
			$this->bindParam(1,$newPathName);
			$this->execute();
			$newIdFolder = $this->fetch()["id_carpeta"];

			$data['id_carpeta'] = $newIdFolder;
			$where = "id_archivo = '$idFile' ;";
			$this->update("archivo",$data,$where);
			if ($this->rowCount()>0)
				return true;
			else
				return false;
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function renameFolderInDB($oldPathName, $newPathName){
		try {
			#=====================exists folder=========================
			$this->prepare("SELECT * FROM carpeta WHERE path_name = ?  ; "); 
			$this->bindParam(1, $newPathName);
			$this->execute();
			if ($this->rowCount()>0)
				return 2;
			#=====================update pathname folder=========================
			$this->prepare("call updatePathname( '$oldPathName', '$newPathName' );");
			$this->execute();
			if ($this->rowCount()>0)
				return true;
			else
				return false;
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function renameFileInDB($idFolder, $newname, $newExt, $idFile){
		try {
			#=====================exists archivo=========================
			$this->prepare("SELECT * FROM archivo WHERE id_carpeta = ? AND nombre = ? AND extension = ? AND id_archivo !=? ; "); 
			$this->bindParam(1,$idFolder);
			$this->bindParam(2,$newname);
			$this->bindParam(3,$newExt);
			$this->bindParam(4,$idFile);
			$this->execute();
			if ($this->rowCount()>0)
				return 2;
			#=====================rename archivo=========================
			$data["nombre"] = $newname;
			$data["extension"] = $newExt;
			$where = "archivo.id_archivo=".$idFile;
			$this->update("archivo", $data , $where);
			#=====================error=========================
			if (!$this->response){
				print_r($this); exit();
			}
			#=====================return=========================
			return $this->response;
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}
	public function getIdFile($oldName, $oldExt, $idFolder){
		try {
			$PDO = DB::conn();
			$query = $PDO->prepare("SELECT id_archivo FROM archivo WHERE nombre = ?  AND extension = ? AND id_carpeta = ? ; ");
			$query->bindParam(1, $oldName);
			$query->bindParam(2, $oldExt);
			$query->bindParam(3, $idFolder);
			$query->execute();
			return $query->fetch()["id_archivo"];

		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function updateFileToTrashInDB( $newname, $newExt, $idFile){
		try {
			
			$data["nombre"] = $newname;
			$data["extension"] = $newExt;
			$data["archivado"] = "1";
			$data["fecha_archivado"] = now();
			$where = "archivo.id_archivo=".$idFile;
			$this->update("archivo", $data , $where);
			#=====================error=========================
			if (!$this->response){
				print_r($this); exit();
			}
			#=====================return=========================
			return $this->response;
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function updateFolderToTrashInDB($pathname){
		try {
			$this->prepare("call updateToTrash('$pathname', '".$pathname.".trash"."');");
			$this->execute();
			if ($this->rowCount()>0)
				return true;
			else
				return false;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}



}