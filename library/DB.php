<?php 

	/**
    * @category    Base de datos.
    * @package     library/DB
    * @author      Gilberto Villarreal Rodriguez  <Gil_yeung@outlook.com>
    * @link        https://myproyecto.com/
    * @license     License Open Source
    * @description Clase para conectar a la base de datos y ejecutar consultas PDO.
    * @see         link de documentacion
    * @since       01/2/2019
    * @version     4.1.2
    */

 

	class DB
	{
		
		static protected $sgdb=SGDB;
		static protected $host=DB_HOST;
		static protected $port=DB_PORT;
		static protected $name=DB_NAME;
		static protected $user=DB_USER;
		static protected $password=DB_PWD;
		static protected $charset=DB_CHARSET;

		public $dbh;
		public $stmt;
		public $bind;
		public $response;
		public $error;

		public function __construct(){
			//$this->dbh=DB::conn();
			$this->dbh=$this->conn();
		}

		/**
		* Establece la conexion con un SGDB
		*/
		static public function conn()
		{
			/* Configurar conexion*/
			$dsn = ''.self::$sgdb.':host='.self::$host.";port=".self::$port.';dbname='.self::$name; 
			$option = array(
				PDO::ATTR_PERSISTENT=>true, 
				PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
			);
			/* Crear una instancia de PDO*/
			try {
				$dbh = new PDO($dsn, self::$user, self::$password, $option);
				$dbh->exec(self::$charset);
			} catch (PDOException $e) {
					echo $e->getMessage(); 
				exit();
			}
			return $dbh;
		}

		/**
		* Ejecuta la consulta
		*/
		public function query($statement, $fetch = PDO::FETCH_OBJ )
		{	
			$this->stmt = $this->dbh->query( $statement, $fetch );
			return $this->stmt;
		}
	
		/**
		* Prepara la consulta
		*/
		public function prepare($statement, $driver_option = array() )
		{	
			$this->stmt = $this->dbh->prepare( $statement, $driver_option );
		}

		/**
		* Vinculamos la consulta con bind
		*/
		public function bindParam($parameter, $value, $type = null)
		{
			switch (is_null($type)) {
				case is_int($value):
					$type=PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type=PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type=PDO::PARAM_NULL;
					break;

				default:
					$type=PDO::PARAM_STR;
					break;
			}
			
			$this->stmt->bindParam($parameter, $value, $type);
		}

		/**
		* Vinculamos la consulta con bind
		*/
		public function bindValue($parameter, $value, $type = null)
		{
			switch (is_null($type)) {
				case is_int($value):
					$type=PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type=PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type=PDO::PARAM_NULL;
					break;

				default:
					$type=PDO::PARAM_STR;
					break;
			}
			$this->stmt->bindValue($parameter, $value, $type);
		}

		/**
		* Inicializa la transaccion
		*/
		public function beginTransaction()
		{	
			return $this->dbh->beginTransaction();
		}
		/**
		* Comprueba si la transaccion esta activa
		*/
		public function inTransaction()
		{	
			return $this->dbh->inTransaction();
		}
		/**
		* Fuerza a que los cambios se almacenen en la base de datos
		*/
		public function commit()
		{
			return $this->dbh->commit();
		}
		/**
		* Vuelve atras la transaccion
		*/
		public function rollback()
		{	
			return $this->dbh->rollback();
		}

		/**
		* Ejecuta la consulta
		*/
		public function execute()
		{	
			return $this->stmt->execute();
		}

		/**
		* Ejecuta la consulta y devuelve el numero de filas afectadas
		*/
		public function exec($statement)
		{	
			return $this->dbh->exec($statement);
		}

		/**
		* Obtener solo un registro
		*/
		public function fetch($fetch=0)
		{
			//$fetch=PDO::FETCH_ASSOC;
			//$fetch=PDO::FETCH_OBJ;
			//$fetch=PDO::FETCH_BOTH;
			//$fetch=PDO::FETCH_BOUND;
			//$fetch=PDO::FETCH_LAZY;
			//$fetch=PDO::FETCH_NUM;
			return $this->stmt->fetch($fetch);
		}

		/**
		* Obtener todos los registros
		*/
		public function fetchAll($fetch=0)
		{
			//$fetch=PDO::FETCH_ASSOC;
			//$fetch=PDO::FETCH_OBJ;
			return $this->stmt->fetchAll($fetch);
		}     

		/**
		* Obtener la cantidad de filas con el metodo rowCount
		*/
		public function rowCount()
		{
			return $this->stmt->rowCount();
		}

		/**
		* Devuelve el ultimo identificador o AUTO_INCREMENT 
		* despues de insertar o actualizar una tabla
		*/
		public function lastInsertId( $expression = null )
		{
			return $this->dbh->lastInsertId($expression);
		}

		/**
		* Devuelve el ultimo identificador o AUTO_INCREMENT 
		* despues de insertar o actualizar una tabla
		*/
		public function lastInsertId2( $expression = null)
		{
			$this->prepare("select LAST_INSERT_ID($expression)");
			$this->execute();
			return $this->fetch()[0];
			
		}
		
		/**
		* Devuelve el maximo identificador de una tabla
		*/
		public function getMaxId( $colum , $table )
		{
			$this->prepare("select MAX($colum) AS max_$colum from $table");
			$this->execute();
			return $this->fetch()[0];
		}

		/**
		* Cerrar la conexion
		*/
		public function close()
		{	
			//$this->stmt->closeCursor();/* opcional en MySQL */
			$this->stmt = null;
			$this->dbh = null;
		}
		/*================================================================================
												CRUD
		==================================================================================*/
		/**
		* Existe datos (Serach)
		* @param string $table : nombre de la tabla
		* @param array $data : datos o columnas a verificar
		*/
		public function existsData( $table,$data )
		{	
			try 
			{      
				//$this = new DB();
				$colum="";
	 			foreach ($data as $key => $value){
			   		 $colum.=" ".$key."=? AND";
				}
				$colum=substr($colum, 0,-3);
				$this->prepare("SELECT * FROM ".$table." WHERE ".$colum." LIMIT 1" );
				
				$i=1;
				foreach ($data as $key => $value){
			   		$this->bindParam($i,$value);
			   		$this->bind .= "bindParam($i,$value); \n";
			   		$i++;
				}
				
		        $this->execute();
		        ($this->rowCount()>0) ? $response = 1  :  $response = 0;
				$this->response=$response;
				return $this;		
			} 
			catch (PDOException $e) {
				$this->response=0;
				$this->error=$e->getMessage();
				$this->close();
		    	return $this;
			}
		}
		

		/**
		* Insertar datos (Create)
		* @param string $table : nombre de la tabla
		* @param array $data : datos o columnas que se desea insertar
		*/
		public function insert( $table, $data)
		{
			try {
				//$db=new DB();
			
	 			$colum=""; $colum_val="";
	 			foreach ($data as $key => $value){
			   		 $colum.=" `".$key."`,";
			   		 $colum_val.="?,";
				}
				$colum=substr($colum,0,-1);
				$colum_val=substr($colum_val,0,-1);
				
				$this->prepare("INSERT INTO ".$table." (".$colum.") VALUES (".$colum_val.");");
				$i=1;
				foreach ($data as $key => $value){
			   		$this->bindParam($i,$value);
			   	    $this->bind .= "bindParam($i,$value); \n";
			   		$i++;
				}
				
				$this->execute();
				($this->rowCount()>0) ? $response = 1  :  $response = 0;
				$this->response=$response;
				return $this;		     
			} 
			catch (PDOException $e) {
				$this->response=0;
				$this->error=$e->getMessage();
				//$this->close();
		    	return $this;
			}
			
		}


		/**
		* Ver datos  (Read)
		* @param string $table : nombre de la tabla
		* @param string $colum : datos o columnas a ver, separar por coma:,
		* @param string $action  : ejeuta una condicion o axion
		*/
		public function show( $table, $colum='*', $action='' )
		{
			try {
				//$db=new DB();
				$this->prepare("SELECT ".$colum." FROM ".$table." ".$action.";");
				$this->execute();
				$this->response=$this->fetchAll();
				return $this;		     
			} 
			catch (PDOException $e) {
				$this->response=0;
				$this->error=$e->getMessage();
				//$this->close();
		    	return $this;
			}
		}

		
		/**
		* Actualizar datos (Update)
		* @param string $table : nombre de la tabla
		* @param array $data : datos o columnas que se desea Actualizar
		* @param string $where : ejeuta una condicion
		*/
		public function update( $table, $data, $where)
		{
			try {
				//$db=new DB();
			
	 			$colum="";
	 			foreach ($data as $key => $value){
			   		 $colum.=" ".$key."=?,";
				}
				$colum=substr($colum,0,-1);
				
				$this->prepare("UPDATE ".$table." SET ".$colum." WHERE ".$where.";");
				$i=1;
				foreach ($data as $key => $value){
			   		$this->bindParam($i,$value);
			   	    $this->bind .= "bindParam($i,$value); \n";
			   		$i++;
				}
				
				$this->execute();
				($this->rowCount()>0) ? $response = 1  :  $response = 0;
				$this->response=$response;
				return $this;		     
			} 
			catch (PDOException $e) {
				$this->response=0;
				$this->error=$e->getMessage();
				//$this->close();
		    	return $this;
			}
		}


		/**
		* Borrar datos (Delete)
		* @param string $table : nombre de la tabla
		* @param string $where : ejeuta una condicion
		*/
		public function delete($table, $where)
		{
			
			try {
			
				//$db=new DB();
				$this->stmt = "DELETE FROM ".$table." WHERE ".$where.";";
				$this->response = $this->exec($this->stmt);
			    return $this;
		    } 
		    catch (PDOException $e) {
		    	$this->response=0;
				$this->error=$e->getMessage();
				//$this->close();
		    	return $this;
		    }
		}

	}