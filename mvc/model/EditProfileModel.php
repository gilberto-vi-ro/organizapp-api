<?php 


class EditProfileModel extends DB
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
	
	public function updateProfile($dataUser, $id){
		try {

			$where = "id_usuario = '$id'";
			$this->update("usuario", $dataUser , $where );
			/*=====================error=========================*/
			/*if (!$this->response){
				print_r($this); exit();
			}*/
			/*=====================return=========================*/
			return $this->response;
			
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

}