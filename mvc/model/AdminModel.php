<?php 


class AdminModel extends DB
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

	public function listUsers(){
		try {

			$this->prepare("SELECT * FROM usuario
							INNER JOIN carpeta
							ON 
							usuario.id_usuario = carpeta.id_usuario
							WHERE raiz=true; ");
			$this->execute();
			if ($this->rowCount()>0)
				return $this->fetchAll(PDO::FETCH_OBJ);
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

	public function deleteUser($id){
			
		
    	$where="id_usuario = '".$id."'";
    	$object=$this->delete("usuario",$where);
    	//print_r($object);
    	//exit();
		return $object->response;

	
	}


}