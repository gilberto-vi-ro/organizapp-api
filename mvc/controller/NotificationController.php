<?php 
	/**
	* 
	*/
	class NotificationController
	{
		private $NotificationModel;
		private $idUser;

		function __construct()
		{   
			$this->NotificationModel = new NotificationModel();
			$this->idUser = session("id_usuario");
		}

		public  function index () 
	    {   
			if (!session('id_usuario')){
				$error["type"] = "error";
				$error["msg"] = "nologin";
				echo json_encode($res["response"] = $error);exit();
			}
			
	        $getImg = $this->getImg();
    		$getName = $this->getName();
    		$getCode = session('id_usuario');
    		$pathDefault =  $this->getPathDefault();

			$array["type"] =  "success";
			$array["img-perfil"] =  $getImg;
			$array["nombre-perfil"] = $getName;
			$array["id-perfil"] =  $getCode;
			$array["path-perfil"] =  $pathDefault;

			echo json_encode($res["response"] = $array);
	    }

	    public  function getImg () 
	    {   
	   		$idUser = $this->idUser;

	        $dataUser = $this->NotificationModel->getUser($idUser);
	        $userImg = $dataUser->img;
	   	 	if ($userImg == null) 
                $img = BASE_URL."public/img/icon/user.png"; 
            else 
                $img = $userImg; 
	
	        return $img; 
	    }
	    public  function getName() 
	    {   
	   		$idUser = $this->idUser;

	        $dataUser = $this->NotificationModel->getUser($idUser);
	        $nameUser = $dataUser->nombre_completo;
	 
	        return $nameUser; 
	    }

	    private  function pathIgnored($path) 
	    {   
			$search = $this->idUser;
			if (!stripos($path, $search) ) 
			 	return true;
			 
    		return $this->FileManager->pathIgnored($path);
	    }

	    public  function getPathDefault() 
	    {   
	   		$idUser = $this->idUser;
	   		$dp = $this->NotificationModel->getPathDefault($idUser);
	   	
	   		$path = ($dp)? $dp  : "drive/".$idUser ;
	        return $path;  
	    }

		public  function createNotification() 
	    {   
			
			$idMsg = 0;$idTask = 0;
	   		$data = $this->NotificationModel->getTaskExpired();
			if ($data!=null){
				foreach ($data as $task){
					
					if (date($task->fecha_entrega) < now("Y-m-d"))
						$idMsg = 1000;
					else if (date($task->fecha_entrega) == now("Y-m-d"))
						$idMsg = 1001;
					else if (date($task->fecha_entrega) == tomorrow("Y-m-d"))
						$idMsg = 1002;
					$res = $this->NotificationModel->insertNotification( $task->id_tarea, $idMsg );
					if ($res == 2 ) echo '';
					else if ($res) {
						setMsg( "success","La notificacion se inserto en la BD." ); 
					}else{
						setMsg( "error","ocurrio un error al insertar notificacion en la BD.",  __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() ); 
					}
				}
			}
			
			//print_r( json_encode(getMsg()) );
			//exit();
	    }

		public  function seenNotification($idNotification) 
	    {   
			$res = $this->NotificationModel->seenNotification( $idNotification );
			
			if ($res) {
				setMsg( "success","La notificacion ha sido vista en la BD." ); 
			}else{
				setMsg( "error","ocurrio un error al ver notificacion en la BD.",  __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() ); 
			}
				
			print_r( json_encode(getMsg()) );
			exit();
	    }

		public  function countNotification() 
	    {   
			$this->createNotification();
			$res = $this->NotificationModel->countNotification();
			echo $res;
			exit();
	    }

		public  function deleteNotification($item) 
	    {   
			foreach ( $item as $key) {
				//borramos en la bd
				$res = $this->NotificationModel->deleteNotification($key->id_notificacion);
				
				if ($res) setMsg( "success","Se elimino correctamente en la BD." );
				else {setMsg( "error","ocurrio un error al eliminar en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
				}
			}
			print_r( json_encode(getMsg()));
			exit();
	    }

		public  function listAllNotification($range) 
	    {   
	   		
	   		$res = $this->NotificationModel->getNotification($range);
			$list = [];
   			$result = [];
   			if ($res)
   				$list = [ 'success' => true, 'results' => $res ];
   			else 
   				$list = [ 'success' => true, 'results' => $result ];
    		echo json_encode($list);
	   		exit(); 
	    }

    }

 ?>