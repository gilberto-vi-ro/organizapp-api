<?php 


class HomeModel extends DB
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
							WHERE path_name='$pathName' AND usuario.id_usuario='$idUser' ");
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

	public function getPathFileInDB($idFile){
		try {
			$this->prepare("SELECT carpeta.path_name FROM carpeta
							INNER JOIN archivo
							ON 
							carpeta.id_carpeta = archivo.id_carpeta
							WHERE archivo.id_archivo=? ");
			$this->bindParam(1,$idFile);
			$this->execute();
			return $this->fetchAll(PDO::FETCH_OBJ)[0]->path_name;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function reInsertFileInDB($file, $idFolder){
		try {
			#=====================exists tarea=========================
			$this->prepare(" SELECT id_archivo FROM archivo WHERE id_carpeta= ? AND nombre = ? AND extension = ? ; "); 
			$this->bindParam(1,$idFolder);
			$this->bindParam(2,$file['name']);
			$this->bindParam(3,$file['extension']);
			$this->execute();
			if ($this->rowCount()>0){
				$GLOBALS["id_file"] = $this->fetchAll(PDO::FETCH_OBJ)[0]->id_archivo;
				return 2;
			}
				
			#=====================insert file=========================
			$data['nombre'] = $file['name'];
			$data['size'] = $file['size'];
			$data['extension'] = $file['extension'];
			$data['descripcion'] = "...";
			$data['archivado'] = 0;
			$data['fecha_archivado'] = null;
			$data['id_carpeta'] = $idFolder;
			$this->insert("archivo", $data);
			$GLOBALS["id_file"] = $this->getMaxFile();
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

	public function addNewTask($data, $idFolder){
		try {
			#=====================exists tarea=========================
			$this->prepare(" SELECT * FROM view_get_task WHERE id_carpeta='$idFolder' AND tarea_nombre =? ; "); 
			$this->bindParam(1,$data['name']);
			$this->execute();
			if ($this->rowCount()>0)
				return 2;
			
			#======================transaction========================
			$this->beginTransaction();
			#=====================insert task=========================
			$dataTask['nombre'] = $data['name'];
			$dataTask['fecha_entrega'] = $data['delivery_date'];
			$dataTask['estado'] = $data['status'];
			$dataTask['descripcion'] = $data['description'];
			$dataTask['prioridad'] = $data['priority'];
			$dataTask['id_carpeta'] = $idFolder;
			$dataTask['id_archivo'] = 1000;
			$this->insert("tarea", $dataTask );
			
			#======================in transaction========================
			if ($this->response)
				$this->commit();
			else
				$this->rollback();

			#=====================error=========================
			/*if (!$this->response){
				print_r($this->error); exit();
			}*/
			#=====================return=========================
			return $this->response;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function editStatusTaskInDB($data){
		try {
			#=====================edit task=========================
			$dataTask['estado'] = $data['estado'];
			$where = "tarea.id_tarea=".$data['id_tarea'];
			$this->update("tarea", $dataTask , $where);
			#=====================return=========================
			return $this->response;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function editTaskInDB($data){
		try {
			#=====================exists tarea=========================
			$this->prepare(" SELECT * FROM view_get_task WHERE id_carpeta=? AND tarea_nombre = ? AND id_tarea != ? ; ");
			$this->bindParam(1,$data['id_carpeta']);
			$this->bindParam(2,$data['name']);
			$this->bindParam(3,$data['id_tarea']);
			$this->execute();
			if ($this->rowCount()>0)
				return 2;
			
			#=====================edit task=========================
			$this->checkDeliveryDateNotification($data['delivery_date'],$data['id_tarea']);
			$dataTask['nombre'] = $data['name'];
			$dataTask['fecha_entrega'] = $data['delivery_date'];
			$dataTask['estado'] = $data['status'];
			$dataTask['descripcion'] = $data['description'];
			$dataTask['prioridad'] = $data['priority'];
			if ($GLOBALS["id_file"]!= null)
				$dataTask['id_archivo'] = $GLOBALS["id_file"];
			$where = "tarea.id_tarea=".$data['id_tarea'];
			$this->update("tarea", $dataTask , $where);
			
			#=====================return=========================
			return $this->response;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function checkDeliveryDateNotification($deliveryDate, $idTask){
		try {

			$this->prepare("SELECT DATE(fecha_entrega) AS fecha FROM tarea WHERE  id_tarea = ? ");
			$this->bindParam(1, $idTask);
			$this->execute();
			$date = $this->fetch()["fecha"];
			$deliveryDate = explode("T", $deliveryDate)[0];
			if ($date != $deliveryDate ){
				$where = "notificacion.id_tarea = '$idTask'";
				$this->delete("notificacion", $where);
				return $this->response;
			}
			else
				return false;

			
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	public function deleteTaskInDB($id){
		try {
			$where = "tarea.id_tarea = '$id'";
			$this->delete("tarea", $where);
			return $this->response;
			
		} catch (PDOException $e) {
			setMsg( "error", $e->getMessage(), __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
			print_r( json_encode(getMsg()));
			exit();
		}
	}

	private function getMaxTask(){
		try {
			return $this->getMaxId( "id_tarea" , "tarea");
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

	public function getTaskPending($pathname, $priority = "", $search = "",$range){
		try {
			$range = explode("::",$range);
			$range1 = $range[0];
			$range2 = $range[1];
			$search = stringScape($search);
			$query = " SELECT * FROM view_get_task WHERE tarea_estado=1 AND carpeta_path_name like '$pathname%' AND carpeta_archivado = 0 ";
			if ($priority == 0 ){
				$query.=" AND tarea_prioridad > 0 ";
				if ($search != "")
					$query.="AND (tarea_nombre like '%$search%' OR tarea_descripcion like '%$search%' OR tarea_fecha_entrega like '%$search%') ";
			}
			else if ($priority != ""){
				$query.=" AND tarea_prioridad = $priority ";
				if ($search != "")
				$query.="AND (tarea_nombre like '%$search%' OR tarea_descripcion like '%$search%' OR tarea_fecha_entrega like '%$search%') ";
			}
			else 
				$query.=" ";
			
			$this->prepare($query." AND tarea_fecha_entrega BETWEEN '$range1' AND '$range2 23:59:59' "); 
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

	public function getTaskDone($pathname, $priority = "", $search = "",$range){
		try {
			$range = explode("::",$range);
			$range1 = $range[0];
			$range2 = $range[1];
			$search = stringScape($search);
			$query = " SELECT * FROM view_get_task WHERE tarea_estado=2 AND carpeta_path_name like '$pathname%' AND carpeta_archivado = 0 ";
			if ($priority == 0 ){
				$query.=" AND tarea_prioridad > 0 ";
				if ($search != "")
					$query.="AND (tarea_nombre like '%$search%' OR tarea_descripcion like '%$search%' OR tarea_fecha_entrega like '%$search%') ";
			}
			else if ($priority != ""){
				$query.=" AND tarea_prioridad = $priority ";
				if ($search != "")
				$query.="AND (tarea_nombre like '%$search%' OR tarea_descripcion like '%$search%' OR tarea_fecha_entrega like '%$search%') ";
			}
			else 
				$query.=" ";
	
			$this->prepare($query." AND tarea_fecha_entrega BETWEEN '$range1' AND '$range2 23:59:59' ");
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

	public function getTaskDelivered($pathname, $priority = "", $search = "",$range){
		try {
			$range = explode("::",$range);
			$range1 = $range[0];
			$range2 = $range[1];
			$search = stringScape($search);
			$query = " SELECT * FROM view_get_task WHERE tarea_estado=3 AND carpeta_path_name like '$pathname%' AND carpeta_archivado = 0 ";
			if ($priority == 0 ){
				$query.=" AND tarea_prioridad > 0 ";
				if ($search != "")
					$query.="AND (tarea_nombre like '%$search%' OR tarea_descripcion like '%$search%' OR tarea_fecha_entrega like '%$search%') ";
			}
			else if ($priority != ""){
				$query.=" AND tarea_prioridad = $priority ";
				if ($search != "")
				$query.="AND (tarea_nombre like '%$search%' OR tarea_descripcion like '%$search%' OR tarea_fecha_entrega like '%$search%') ";
			}
			else 
				$query.=" ";
			
			$this->prepare($query." AND tarea_fecha_entrega BETWEEN '$range1' AND '$range2 23:59:59' ");
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


	

}