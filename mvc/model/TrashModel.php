<?php 


class TrashModel extends DB
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

	public function getType($id){
		try {

			$this->prepare("SELECT tipo FROM usuario WHERE id_usuario=$id ");
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

	
	
	public function getPathDefault($id){
		try {

			$this->prepare("SELECT path_name FROM usuario
							INNER JOIN carpeta
							ON 
							usuario.id_usuario = carpeta.id_usuario
							WHERE raiz=true AND usuario.id_usuario='$id' ");
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

	public function restoreFolderOfTrashInDB($oldPathname, $newPathName){
		try {
			$this->prepare("call restoreTrash('$oldPathname', '$newPathName');");
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

	public function restoreFileOfTrashInDB( $newname, $newExt, $idFile){
		try {
			
			$data["nombre"] = $newname;
			$data["extension"] = $newExt;
			$data["archivado"] = "0";
			$data["fecha_archivado"] = null;
			$where = "archivo.id_archivo = ".$idFile;
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

	public function deleteFolderInDB($pathName){
		try {
			$where = "carpeta.path_name like '$pathName%'";
			$res = $this->delete("carpeta", $where);
			return $this->response;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function deleteFileInDB($idFile){
		try {
			$where = "archivo.id_archivo = ".$idFile;
			$res = $this->delete("archivo", $where);
			return $this->response;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}


}