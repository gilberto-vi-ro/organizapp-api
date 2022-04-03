<?php 


class NotificationModel extends DB
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

	public function getTaskExpired(){
		try {

			$this->prepare("SELECT id_tarea, DATE(fecha_entrega) as fecha_entrega FROM tarea WHERE fecha_entrega<=? AND estado !=3 ");
			$this->bindParam(1,tomorrow("Y-m-d")." 23:59:59");
			$this->execute();
			if ($this->rowCount()>0)
				return $this->fetchAll(PDO::FETCH_OBJ);
			else
				return null;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function insertNotification( $idTask, $idMsg ){
		try {
			#=====================exists tarea=========================
			$this->prepare(" SELECT id_notificacion FROM notificacion WHERE id_tarea = ? AND id_mensaje = ? ; "); 
			$this->bindParam(1,$idTask);
			$this->bindParam(2,$idMsg);
			$this->execute();
			if ($this->rowCount()>0)
				return 2;
			#=====================insert task=========================
			$dataNotification['visto'] = 0;
			$dataNotification['eliminado'] = 0;
			$dataNotification['tipo'] = 1;
			$dataNotification['fecha_registro'] = now("Y-m-d");
			$dataNotification['id_tarea'] = $idTask;
			$dataNotification['id_mensaje'] = $idMsg;
			$this->insert("notificacion", $dataNotification );
			
			#=====================error=========================
			if (!$this->response){
				print_r($this->error); exit();
			}
			#=====================return=========================
			return $this->response;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function getNotification($idUsuario, $range){
		try {
			$range = explode("::",$range);
			$range1 = $range[0];
			$range2 = $range[1];
			$query = " SELECT * FROM view_get_notification WHERE id_usuario=? AND notificacion_eliminado = 0 AND notificacion_fecha_registro BETWEEN '$range1' AND '$range2' ORDER BY id_notificacion DESC";
			
			$this->prepare($query);
			$this->bindParam(1,$idUsuario);
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

	public function seenNotification( $idNotification ){
		try {
			#=====================exists tarea=========================
			$this->prepare(" SELECT visto FROM notificacion WHERE visto = 1 AND id_notificacion = ? "); 
			$this->bindParam(1,$idNotification);
			$this->execute();
			if ($this->rowCount()>0)
				return 2;
		
			#=====================update task=========================
			$dataNotification['visto'] = 1 ;
			$were = "id_notificacion = $idNotification";
			$this->update("notificacion", $dataNotification, $were );
			
			#=====================error=========================
			if (!$this->response){
				print_r($this->error); exit();
			}
			#=====================return=========================
			return $this->response;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function countNotification($idUser){
		try {
			#=====================exists tarea=========================
			$this->prepare(" 
					SELECT id_notificacion FROM usuario
					INNER JOIN carpeta
					INNER JOIN tarea
					INNER JOIN notificacion
					ON usuario.id_usuario = carpeta.id_usuario AND 
					carpeta.id_carpeta = tarea.id_carpeta AND 
					tarea.id_tarea = notificacion.id_tarea
					WHERE visto = 0 AND eliminado = 0 AND usuario.id_usuario = ? "); 
			$this->bindParam(1,$idUser);
			$this->execute();
			return $this->rowCount();
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function deleteNotification($idNotification){
		try {
			$data["eliminado"] = 1;
			$where = "notificacion.id_notificacion = ".$idNotification;
			$res = $this->update("notificacion",$data, $where);
			return $this->response;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

}