<?php   

/**
* @category    FileManager.
* @package     libary
* @author      Gilberto Villarreal Rodriguez  <Gil_yeung@outlook.com>
* @link        https://myproyecto.com/
* @license     License Open Source
* @description Clase para gestionar archivos.
* @see         link de documentacion
* @since       01/2/2021
* @version     3.3.0 API
* @phpversion > 7.0
*/


class FileManager
{
	#Opciones de Seguridad
	private $allow_delete = true; // Establezca en falso para deshabilitar la opcion Eliminar.
	private $allow_upload = true; // Establecer en verdadero para permitir cargar archivos
	private $allow_create_folder = true; // Establecer en falso para deshabilitar la creación de carpetas
	private $allow_direct_link = false; // Establecer en falso para permitir solo descargas y no enlaces directos
	private $allow_show_folders = true; // Establecer en falso para ocultar todos los subdirectorios

	private $path_ignore = ["libary","mvc","public","api","","null",null,"/",".",".."];// debe ser una matriz. Path que no estan permitidos
	private $disallowed_extensions = ['php','js','css','html'];  // debe ser una matriz. Extensiones no permitidas para cargar
	private $hidden_extensions = ['php','js','css','html']; // debe ser una matriz de extensiones de archivo en minúsculas. Extensiones ocultas en el índice del directorio

	#para cambiar el size de carga y de subida debes modificar php.ini con los siguientes valores luego restart server o xampp.
	/*
	upload_max_filesize = 1G
	post_max_size = 1G
	max_execution_time = 1000
	max_input_time = 1000 
	*/
	
	#variables de la clase
	private $MAX_UPLOAD_SIZE;
	private $path;
	private $default_path;
	public $response;
	public $_msg = [];

	/**
	* begin
	* @param string $path :  path default.
	*/
	public function __construct($path = "drive/"){
		// debe estar en UTF-8 o `basename` no funciona
		//setlocale(LC_ALL,'en_US.UTF-8');
		setlocale(LC_ALL,'es_MX.UTF-8');
		$this->default_path = $path;
		$this->tmpDir=$this->tmpDir(); 
		$this->MAX_UPLOAD_SIZE = min($this->asBytes(ini_get('post_max_size')), $this->asBytes(ini_get('upload_max_filesize')));
	}

	/**
	* devuelve archivos y carpetas
	*/
	public function listAll()
	{
		if (is_dir(ROOT_DRIVE.$this->path)) {
			$directory = ROOT_DRIVE.$this->path;
			$result = [];
			$files = array_diff(scandir($directory), ['.','..']);
			foreach ($files as $entry) if (!$this->isIgnored($entry, $this->allow_show_folders, $this->hidden_extensions)) {
				$item = $directory . $entry;
				
				$stat = stat($item);
				(is_dir($item)) ? $myFolder = $this->getInfoFolder($item) : "";
			        $result[] = [
			        	'ctime' => $stat['ctime'],
			        	'mtime' => $stat['mtime'],
			        	'size' => is_dir($item) ? $myFolder["bytes"] : $stat['size'],
			        	'name' => basename($item),
			        	'path' => str_replace(ROOT_DRIVE,'',dirname($item)."/" ),
			        	'path_name' => str_replace(ROOT_DRIVE,'',$item),
			        	'info' => ( is_dir($item) ) ? [ "is_dir" => true, "dirs" => $myFolder["dirs"], "files" => $myFolder["files"] ]
			        				: [ "is_dir" => false, "extension" => $this->_getExtension($item) ] ,
			        	'is_deleteable' => $this->allow_delete && ((!is_dir($item) && is_writable($directory)) ||
		                                                           (is_dir($item) && is_writable($directory) &&  $this->isRecursivelyDeleteable($item))),
			        	'is_readable' => is_readable($item),
			        	'is_writable' => is_writable($item),
			        	'is_executable' => is_executable($item),
			        ];
		    }
		    $list = ['type' => 'success', 'is_writable' => is_writable($this->path), 'path_name' => $this->path, 'data' =>$result];

		} else {
			$this->setMsg(__CLASS__."->".__FUNCTION__,"Not a Directory",(new Exception(""))->getLine(),412);
			$result = [ 'msg' =>  'Not a Directory', 'code' =>  412];
			$list = ['type' => 'error', 'is_writable' => is_writable($this->path), 'path_name' => $this->path, 'data' =>$result];
		}
		

		//print_r($list);
		return $list;
	}

	/**
	* devuelve archivos y carpetas en papelera
	*/
	public function listAllTrash()
	{
		if (is_dir(ROOT_DRIVE.$this->path)) {
			$directory = ROOT_DRIVE.$this->path;
			$result = [];
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
			$i=0;
			foreach ($files as $entry)  {
			
				if ( $entry->getFilename() == ".." || $i==0 ){
					$i++;
					continue; // pasamos a la siguiente vuelta
				}
				if ( $entry->isFile() )
	        		$item = $entry->getPathname();//obtenemos el pathname
	        	if ( $entry->isDir() ) { 
		         	$item = $entry->getPath();//obtenemos el path
		        }
		         
	         	if (!$this->isIgnored($item, $this->allow_show_folders, $this->hidden_extensions) && $this->isTrash($item) ){
	         		$item = str_replace("\\", "/" , $item );
					(is_dir($item)) ? $myFolder = $this->getInfoFolder($item) : "";
				        $result[] = [
				        	'ctime' => $entry->getCTime(),
				        	'mtime' => $entry->getMTime(),
				        	'size' =>  is_dir($item) ? $myFolder["bytes"] : $entry->getSize(),
				        	'name' => basename($item),
				        	'path' => str_replace(ROOT_DRIVE,'',dirname($item)."/" ),
			        		'path_name' => str_replace(ROOT_DRIVE,'',$item),
				        	'info' => ( is_dir($item) ) ? [ "is_dir" => true, "dirs" => $myFolder["dirs"], "files" => $myFolder["files"] ]
				        				: [ "is_dir" => false, "extension" => $this->_getExtension($item) ] ,
				        	'is_deleteable' => $this->allow_delete && ((!is_dir($item) && is_writable($directory)) ||
			                                                           (is_dir($item) && is_writable($directory) &&  $this->isRecursivelyDeleteable($item))),
				        	'is_readable' => is_readable($item),
				        	'is_writable' => is_writable($item),
				        	'is_executable' => is_executable($item),
				        ];
		    	}

		    }
		    $list = ['type' => 'success', 'is_writable' => is_writable($this->path), 'path_name' => $this->path, 'data' =>$result];

		} else {
			$this->setMsg(__CLASS__."->".__FUNCTION__,"Not a Directory",(new Exception(""))->getLine(),412);
			$result = [ 'msg' =>  'Not a Directory', 'code' =>  412];
			$list = ['type' => 'error', 'is_writable' => is_writable($this->path), 'path_name' => $this->path, 'data' =>$result];
		}
		//print_r($list);
		return $list;
	}

	/**
	* devuelve archivos y carpetas buscadas por el nombre o path
	*/
	public function listSearch($search, $trash = false)
	{
		if (is_dir(ROOT_DRIVE.$this->path)) {
			$directory = ROOT_DRIVE.$this->path;
			$result = [];
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
			$i=0;
			foreach ($files as $entry)  {
			
				if ( $entry->getFilename() == ".." || $i==0 ){
					$i++;
					continue; // pasamos a la siguiente vuelta
				}
				if ( $entry->isFile() )
	        		$item = $entry->getPathname();//obtenemos el pathname
	        	if ( $entry->isDir() ) { 
		         	$item = $entry->getPath();//obtenemos el path
		        }
		         
	         	if ($trash) 
	         		$filter = ( !$this->isIgnored($item, $this->allow_show_folders, $this->hidden_extensions) && $this->searchTrash($item, $search) && $this->isTrash($item) );
	         	else 
	         		$filter = ( !$this->isIgnored($item, $this->allow_show_folders, $this->hidden_extensions) && $this->search($item, $search) );
	         	if ( $filter ){

	         		$item = str_replace("\\", "/" , $item );
					(is_dir($item)) ? $myFolder = $this->getInfoFolder($item) : "";
				         $result[] = [
				        	'ctime' => $entry->getCTime(),
				        	'mtime' => $entry->getMTime(),
				        	'size' =>  is_dir($item) ? $myFolder["bytes"] : $entry->getSize(),
				        	'name' => basename($item),
				        	'path' => str_replace(ROOT_DRIVE,'',dirname($item)."/" ),
			        		'path_name' => str_replace(ROOT_DRIVE,'',$item),
				        	'info' => ( is_dir($item) ) ? [ "is_dir" => true, "dirs" => $myFolder["dirs"], "files" => $myFolder["files"] ]
				        				: [ "is_dir" => false, "extension" => $this->_getExtension($item) ] ,
				        	'is_deleteable' => $this->allow_delete && ((!is_dir($item) && is_writable($directory)) ||
			                                                           (is_dir($item) && is_writable($directory) &&  $this->isRecursivelyDeleteable($item))),
				        	'is_readable' => is_readable($item),
				        	'is_writable' => is_writable($item),
				        	'is_executable' => is_executable($item),
				        ];
		    	}

		    }
		    $list = ['type' => 'success', 'is_writable' => is_writable($this->path), 'path_name' => $this->path, 'data' =>$result];

		} else {
			$this->setMsg(__CLASS__."->".__FUNCTION__,"Not a Directory",(new Exception(""))->getLine(),412);
			$result = [ 'msg' =>  'Not a Directory', 'code' =>  412];
			$list = ['type' => 'error', 'is_writable' => is_writable($this->path), 'path_name' => $this->path, 'data' =>$result];
		}
		//print_r($list);
		return $list;
	}

	/**
	* elimina definitivamente un archivo o carpeta.
	* @param string $pathname : pathname.
	*/
	public function delete($pathname)
	{
		$pathname = $this->convertToPathname($pathname);
		if($this->allow_delete) {
			$r = $this->rmrf($pathname);
			if ($r) return true;
		}
		$this->setMsg(__CLASS__."->".__FUNCTION__,"error! could not be deleted: ".$pathname,(new Exception(""))->getLine());
		return false;
	}

	/**
	* renombra un archivo o carpeta.
	* @param string $oldPathname : pathname anterior.
	* @param string $newPathname : pathname nuevo.
	*/
	public function rename($oldPathname, $newPathname )
	{
		$oldPathname = $this->convertToPathname($oldPathname);
		$newPathname = $this->convertToPathname($newPathname);
		$r = @rename(ROOT_DRIVE.$oldPathname, ROOT_DRIVE.$newPathname);
		if ($r)  {
			// Success.
			@touch(ROOT_DRIVE.$newPathname);//establecer mTime y aTime del item a now()
			return true;
		} else {
			// Error rename failed.
			$this->setMsg(__CLASS__."->".__FUNCTION__, "error! could not be renamed: ".$oldPathname." to ".$newPathname,(new Exception(""))->getLine());
			return false;
		}
	}

	/**
	* mueve un archivo o carpeta.
	* @param string $oldPathname : pathname anterior.
	* @param string $newPathname : pathname nuevo.
	*/
	public function move($oldPathname, $newPathname )
	{
		$oldPathname = $this->convertToPathname($oldPathname);
		$newPathname = $this->convertToPathname($newPathname);
		$r = @rename(ROOT_DRIVE.$oldPathname, ROOT_DRIVE.$newPathname);
		if ($r)  {
			// Success.
			@touch(ROOT_DRIVE.$newPathname);//establecer mTime y aTime del item a now()
			return true;
		} else {
			// Error move failed.
			$this->setMsg(__CLASS__."->".__FUNCTION__, "error! could not be moved: ".$oldPathname." to ".$newPathname,(new Exception(""))->getLine());
			return false;
		}
	}
	/**
	* crea un directorio o carpeta
	* @param string $dirname : path y nombre.
	*/
	public function createDir($dirname)
	{
		if ( $this->allow_create_folder) {
			// no permita acciones fuera de la raíz. También filtramos las barras para atrapar argumentos como './../fuera'
			
			$dir = str_replace('/', '', $dirname);
			if(substr($dir, 0, 2) === '..'){
				$this->setMsg(__CLASS__."->".__FUNCTION__,"error! could not create directory: $dirname ",(new Exception(""))->getLine());
				return false;
			}
			   
			
			$r = @mkdir(ROOT_DRIVE.$this->path.$dir, 0777, true);
			if ( !$r ) 
				$this->setMsg(__CLASS__."->".__FUNCTION__,"error! could not create directory:".$this->path.$dir,(new Exception(""))->getLine());
				
			return $r;
		} else
			return false;
	}

	public function uploadFile($_FILES_)
	{
		if ( $this->allow_upload ) {
			foreach($this->disallowed_extensions as $ext){
				if(preg_match(sprintf('/\.%s$/',preg_quote($ext)), $_FILES_['file_data']['name'])){
					$this->setMsg(__CLASS__."->".__FUNCTION__, "Files of this type are not allowed.",(new Exception(""))->getLine(), 403);
					return false;
				}
			}
			$res = @move_uploaded_file($_FILES_['file_data']['tmp_name'], ROOT_DRIVE.$this->path.$_FILES_['file_data']['name']);
			if(!$res){
				$this->setMsg(__CLASS__."->".__FUNCTION__,"error! could not load file: ".$_FILES_['file_data']['tmp_name']." to ".$this->path.$_FILES_['file_data']['name'],(new Exception(""))->getLine());
				return false;
			}
			return $res;
			exit();
		} 
	}

	public function downloadFolder($pathname)
	{	
		$pathname = $this->convertToPathname($pathname);
	
		if (is_dir(ROOT_DRIVE.$pathname)) {
			if ( file_exists(ROOT_DRIVE.$pathname.".zip") ) {
				$this->download(ROOT_DRIVE.$pathname.".zip");
				return true ;
			}
			$nameZip = $this->createZipFolder($pathname);
			if(!$this->download($nameZip)){
				$this->setMsg(__CLASS__."->".__FUNCTION__,"error! could not download the folder: ".$nameZip,(new Exception(""))->getLine());
				return false;
			}else return true;
		}else {
			$this->setMsg(__CLASS__."->".__FUNCTION__,"error! is not a valid directory: ".$pathname,(new Exception(""))->getLine() );
			return false;
		}
	}

	public function downloadFile($pathname)
	{
		$pathname = $this->convertToPathname($pathname);	
		if (file_exists(ROOT_DRIVE.$pathname)) {
			return $this->download(ROOT_DRIVE.$pathname);
		}else {
			$this->setMsg(__CLASS__."->".__FUNCTION__,"error! the file does not exists: ".$pathname,(new Exception(""))->getLine());
			echo "error! no se encontro: $pathname";
			return false;
		}
	}

	/**
	* get name :  devuelve el nombre de un pathname
	* @param string $pathname
	* @return string $name
	*/
	public function getName($pathname)
	{
		$pathname = $this->convertToPathname($pathname);
		$array = explode("/", $pathname, FILTER_SANITIZE_URL);
		return $name = end($array); 
	}

	/**
	* setPathname convierte una cadena a pathname
	* @param string $path : pathname specific.
	*/
	public function setPathname( $path )
	{
		if ($this->pathIgnored($path))
			$this->path = $this->default_path;
		else
		    $this->path = $this->convertToPathname($path);
	}

	/**
	* getPathname devuelve el pathname
	* @return string $pathname
	*/
	public function getPathname()
	{
			return $this->path;
	}

	/**
	* setPath convierte una cadena a path
	* @param string $path : path specific.
	*/
	public function setPath( $path )
	{
		if ($this->pathIgnored($path))
			$this->path = $this->default_path;
		else
		    $this->path = $this->convertToPath($path);
	}
	
	/**
	* getPath devuelve el path
	* @param string $pathname : pathname specific.
	* @return string $path | $this->path
	*/
	public function getPath($pathname = null)
	{
		if($pathname === null){
			return $this->path;
		}else{
			$pathname = $this->convertToPathname($pathname);
			$array = explode('/', $pathname);
			unset($array[ count($array ) - 1 ] );
			$path =  implode('/', $array);
			return $path."/";
		}
			
	}

	/**
	* devuelve valores de configuracion
	* @return array $config
	*/
	public function getValuesConfig()
	{	
		$values = [];

		$values['MAX_UPLOAD_SIZE'] = $this->MAX_UPLOAD_SIZE;
		$values['allow_upload'] = $this->allow_upload ? true : false;
		$values['allow_direct_link'] = $this->allow_direct_link ? true : false;
		return ['type' => 'success','value' => $values];
		
	}

	/**
	* asigna un mensaje
	* @param string $were : clase y metodo.
	* @param string $msg : mensaje.
	* @param string $line : linea del error o msg.
	* @param string $pathname : codigo de  error.
	*/
	public function setMsg($were, $msg,  $line, $code = 0 ) {
		//http_response_code($code);
		$error = ['where'=> $were, 'msg' => $msg,'line'=>$line, 'code' =>intval($code)];
		$this->_msg[] = $error;
	}

	/**
	* devuelve los mensajes por array o por clave
	* @return array 
	* @return string 
	*/
	public function getMsg($index = null) {
		if ($index === null)
			return $this->_msg;
		else
			return $this->_msg[0][$index];

	}

	public function hideExtension($array)
	{
		$this->hidden_extensions = $array;
	}
	public function noUploadExtension($array)
	{
		$this->disallowed_extensions = $array;
	}

	public function pathIgnore($array)
	{
		$this->path_ignore = $array;
	}

 	public function convertToPath($path){
    	$path = str_replace("//", "/", $path);
    	$lastStr = substr($path, -1);
    	if($lastStr == "/" || $path == "")
    		return $path;
    	else 
    		return $path."/";
    }

	public function convertToPathname($pathname){
    	$pathname = str_replace("//", "/", $pathname);
    	$lastStr = substr($pathname, -1);
    	
		if ($pathname == "")
			return $pathname;
		else if($lastStr == "/" )
    		return substr($pathname,0, -1);
    	else 
    		return $pathname;
    }

	/**
	* path ignored
	* @param string $path : path especifico.
	* @return bool.
	*/
	public function pathIgnored($path) 
    {   $ignore = str_replace("/", "", $this->path_ignore );
    	$path = str_replace("/", "", $path);
    	if ( in_array($path, $ignore) ) {
			return true;
		}
		return false;
    }

	/**
	* get  extension of file
	* @param string $pathname : pathname | name of file.
	* @return string extension.
	*/
	public function getExtension($pathname){
		$pathname = $this->convertToPathname($pathname);
		if (!stripos($pathname, ".") ) return "unknown";
		$ext = explode(".", filter_var(rtrim($pathname), FILTER_SANITIZE_URL));
		return $ext = strtolower(end($ext)); 
	}


	/*==============================private functions===================================
	===================================================================================*/

	

	/**
	* 
	*/
	private function tmpDir()
	{
		$tmp_dir =  ROOT_PATH.$this->default_path."/";
		if(DIRECTORY_SEPARATOR==='\\') $tmp_dir = str_replace('/',DIRECTORY_SEPARATOR,$tmp_dir);
		$tmp = $tmp_dir;

		if($tmp === false)
			$this->setMsg(__CLASS__."->".__FUNCTION__,'Archivo o Carpeta no encontrada',(new Exception(""))->getLine(),404);
		if(substr($tmp, 0,strlen($tmp_dir)) !== $tmp_dir)
			$this->setMsg(__CLASS__."->".__FUNCTION__,"Forbidden",(new Exception(""))->getLine(),403);
		if(strpos($this->default_path."/", DIRECTORY_SEPARATOR) === 0)
			$this->setMsg(__CLASS__."->".__FUNCTION__,"Forbidden",(new Exception(""))->getLine(),403);
	}


	

	private function asBytes($ini_v) {
		$ini_v = trim($ini_v);
		$s = ['g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10];
		return intval($ini_v) * ($s[strtolower(substr($ini_v,-1))] ?: 1);
	}

	/**
	 * borra carpetas o archivos definitivamente
	 * @param string  $dir
	 * @return bool
	 */

	private function rmrf($dir) {
		if(is_dir(ROOT_DRIVE.$dir)) {
			$files = array_diff(scandir(ROOT_DRIVE.$dir), ['.','..']);
			foreach ($files as $file)
				$this->rmrf("$dir/$file");
			$r = @rmdir(ROOT_DRIVE.$dir);
			if ($r) return true; else return false;
		} else {
			$r = @unlink(ROOT_DRIVE.$dir);
			if ($r) return true; else return false;
		}
	}

	/**
	* Descargar archivo
	* @param string $fileName
	* @return bool
	*/
	private function download($fileName) {

	    if (file_exists($fileName)){
	      
	    	ob_clean();
		    	header('Content-Description: File Transfer');
		        header('Content-Type: application/octet-stream');
		        header('Content-Disposition: attachment; filename='.basename($fileName));
		        header('Content-Transfer-Encoding: binary');
		        header('Expires: 0');
		        header('Cache-Control: must-revalidate');
		        header('Pragma: public');
		        header('Content-Length: ' . filesize($fileName));
		    ob_end_clean();
         	flush();
	        readfile($fileName);
	        return true;
	    }else{
	    	$this->setMsg(__CLASS__."->".__FUNCTION__, $fileName." ¡Nose encontro el archivo!",(new Exception(""))->getLine());
	        return false;
	    }
	}


	private function isRecursivelyDeleteable($d) {
		$stack = [$d];
		while($dir = array_pop($stack)) {
			if(!is_readable($dir) || !is_writable($dir))
				return false;
			$files = array_diff(scandir($dir), ['.','..']);
			foreach($files as $file) if(is_dir($file)) {
				$stack[] = "$dir/$file";
			}
		}
		return true;
	}



	private function isIgnored($entry, $allow_show_folders, $hidden_extensions) {
		if ($entry === basename(__FILE__)) {
			return true;
		}

		if (is_dir($entry) && !$allow_show_folders) {
			return true;
		}

		$ext_dir = explode(".", filter_var(rtrim($entry), FILTER_SANITIZE_URL));
		$ext_dir_end = end($ext_dir);
		if (is_dir($entry) && in_array($ext_dir_end, $hidden_extensions)) {
			return true;
		}

		$ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
		if (in_array($ext, $hidden_extensions)) {
			return true;
		}

		return false;
	}

	private function isTrash( $entry, $search = ".trash" ) {
		if ($search == "" ||  $search == null) return true;

		$explode_entry = explode(DIRECTORY_SEPARATOR, filter_var(rtrim($entry), FILTER_SANITIZE_URL));
		$my_entry = end($explode_entry);
		if (strpos($my_entry, $search) ) return true;
		
		return false;
	}

	private function searchTrash( $pathname, $search) {

		$path = dirname($pathname);
		$name = str_replace($path, "", $pathname);
		$name = str_replace(".trash", "", $name);

		if ($search == "" ||  $search == null) return true;
		//strpos(haystack, needle);
		if (stripos($name, $search) ) return true;
		return false;
	}

	private function search( $pathname, $search) {
		
		$path = dirname($pathname);
		$name = str_replace($path, "", $pathname);
		
		if ($search == "" ||  $search == null) return true;
		//strpos(haystack, needle);
		if (stripos($name, ".trash") || stripos( $pathname, ".trash") ) return false;
		if (stripos($name, $search) ) return true;
		
		return false;
	}

	/**
	 * crea un zip de una carpeta o archivo
	 * @param string  $path
	 * @param string  $name
	 * @return string ruta y nombre del zip creado
	 */
	private function createZipFolder($pathname){
		/*$nopermitidos = array('php','css','html');
		//if (!in_array($extension, $nopermitidos)) {}*/
		 
		$zip = new ZipArchive();
		// Ruta absoluta
		//$name = str_replace(['/', '\\'], "", $name);
		$nameZip = ROOT_DRIVE.$pathname.".zip";// .zip *
		$folderPath = ROOT_DRIVE.$pathname;

		if (!$zip->open($nameZip, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
		    exit("Error al abrir ZIP en $nameZip");
		}// Si no hubo problemas, continuamos
		// Crear un iterador recursivo que tendrá un iterador recursivo del directorio
		$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($folderPath), RecursiveIteratorIterator::LEAVES_ONLY
		);
		$i = 0;
		foreach ($files as $file) {
			
		    // No queremos agregar los directorios, pues los nombres de estos se agregarán cuando se agreguen los archivos
		    if ($file->isDir()) {
		        continue;
		    }
		    $path = $file->getRealPath();
		    $filename = substr($path, strlen($folderPath) + 1);
		    $zip->addFile($path, $filename);
		    $i++;
		}
	
		$res = $zip->close();	// No olvides cerrar el archivo
		if ($res) { 
			if ($i == 0) echo ": Lo sentimos,\n La carpeta esta vacia o no contiene archivos.";
			else echo "Carpeta zipeada";
		} else
			echo "Error al zipear Carpeta";

		return $nameZip;
	}


	/**
	 * Get the file extension
	 * @param string  $filename
	 * @return string $extension
	 */
	private function _getExtension($filename){
		return  $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	}


	/**
	 * Get the directory size , folder and files
	 * @param string $directory
	 * @return array
	 */
	private function  getInfoFolder($pathDir) {
	   
	    $dir = -1;
	    $file = 0;

	    $bytesTotal = 0;
	    $pathDir = realpath($pathDir);
	    if($pathDir!==false && $pathDir!='' && file_exists($pathDir)){
	        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathDir)) as $object){

	        	if ( $object->getFilename() == ".." )
					continue; // pasamos a la siguiente vuelta

	        	if ($object->isFile()) {
	        		$bytesTotal += $object->getSize();
	         		$file++;
	         	}
	        	if ($object->isDir()) {
		         	$dir++;
	         	}
	        }
	    }
	    return ["bytes" => $bytesTotal, "files" =>  $file, "dirs" =>  $dir];
	}


}

?>