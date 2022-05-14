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
	        $dataUser = $this->NotificationModel->getUser($idUser);
	        $userImg = $dataUser->img;
	   	 	if ($userImg == null) 
                $img = BASE_URL."public/img/icon/user.png"; 
            else 
                $img = $userImg; 
	
	        return $img; 
	    }
	    public  function getName($idUser) 
	    {   
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
			$idUser = $_REQUEST["id_user"];
			$dp = $this->NotificationModel->getPathDefault($idUser);
			$path = ($dp)? $dp  : "drive/$idUser/";
	        return $path; 
	    }

		public  function createNotification() 
	    {   
			
			$idMsg = 0;$idTask = 0;
	   		$data = $this->NotificationModel->getTaskExpired();
			if (empty($data) ){
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
			}else{
				setMsg( "success","no hay notificacion por crear."); 
			}
			
			print_r( json_encode(getMsg()) );
			exit();
	    }

		public  function seenNotification() 
	    {   
			$idNotification = $_REQUEST["id_notification"];
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
			$idUser = $_REQUEST["id_user"];
			//$this->createNotification();
			$res = $this->NotificationModel->countNotification($idUser);
			msg("response",[
				"type"=>"success", 
				"total"=> $res,
				"where"=> null,
				"line"=> null 
			]);
			print_r( json_encode(getMsg()) );
			exit();
	    }

		public  function deleteNotification() 
	    {   
			$item = $_REQUEST['item'];
			foreach ( $item as $key) {
				//borramos en la bd
				$res = $this->NotificationModel->deleteNotification($key["id_notification"]);
				
				if ($res) setMsg( "success","Se elimino correctamente en la BD." );
				else {setMsg( "error","ocurrio un error al eliminar en la BD.", __CLASS__."->".__FUNCTION__ , (new Exception(""))->getLine() );
				}
			}
			print_r( json_encode(getMsg()));
			exit();
	    }

		public  function listAllNotification() 
	    {   
			$range = $_REQUEST["range"];
			$idUser = $_REQUEST["id_user"];
	   		$res = $this->NotificationModel->getNotification($idUser,$range);
			$list = [];
   			$result = [];
   			if ($res)
   				$list = [ 'type' => 'success', 'data' => $res ];
   			else 
   				$list = [ 'type' => 'error', 'data' => $result ];
    		echo json_encode($list);
	   		exit(); 
	    }

    }

 ?>