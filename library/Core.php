<?php  

    /**
    * @category    Clase  principal.
    * @package     framegl/Core
    * @author      Gilberto Villarreal Rodriguez  <Gil_yeung@outlook.com>
    * @link        https://myproyecto.com/
    * @license     License Open Source
    * @description Core tiene como objetivo Mapear la url ingresada en el navegador por eslash=/
    *               0- controlador
    *               1- metodo
    *               2- parametro
    *               ejemplo: /articulo/actualizar/4
    * @see         link de documentacion
    * @since       01/1/2019
    * @version     4.2.0
    */
    
    class Core
    {

        protected $_controller = CONTROLLER_DEFAULT;
        protected $_method = METHOD_DEFAULT;
        protected $_params = [];
        protected $_file;


        /**
        * Genera el controller, el metodo y el o los parametros
        */
        public function __construct()
        {
           /* Obtener la url */
            $getUrl = $this->getUrl();
            $url = is_null($getUrl) ? array($this->_controller,$this->_method) : $getUrl;
     
            $this->_file = CONTROLLER_PATH.ucfirst($url[0]) . ".php";
            $this->_controller = ucfirst($url[0]);
            unset($url[0]);
            if(isset($url[1])) {
                $this->_method = $url[1];
                unset($url[1]);
            }
            $this->_params = $url ? array_values($url) : [];

        }

        /**
        * Convierte la url en trozos pormedeio del slash=/
        * @return Array
        */
        public function getUrl()
        {
            if(isset($_GET["url"])){
                $url= explode("/", filter_var(rtrim($_GET["url"], "/"), FILTER_SANITIZE_URL));
                if (F_URL) 
                    return stringClean($url); 
                else
                    return $url;
            }
            /*else{
                if (DEBUG) {
                    die('<center>  "Se esperaba GET ?url=tuclase/elmetoddo/param"<center>');
                }
            }*/
        
        }

        /**
        * Verifica que exista el archivo, controlador y metodo.
        * @param string $file archivo del controlador
        * @param string $controller controlador
        * @param string $method archivo metodo
        */
        private function verify($file, $controller, $method)
        {
            /*if(!file_exists($file) || !method_exists($controller, $method)) {
                    include_once _FILE_ERR_PATH ;
                    exit();
            }*/
            if(file_exists($file) ) {
                require_once $file;
                if(!class_exists($controller)) {
                    if (DEBUG) 
                        die('Core >>> Uncaught Error: "'.$controller.'" Class not found in "'.$file.'"' ) ;
                }
                if(!method_exists($controller , $method) ){
                    include_once _FILE_ERR_PATH ;
                    exit();
                }
            }else{
                include_once _FILE_ERR_PATH ;
                exit();
            }
        }
     
        /**
        * Iniciar el controlador/método que se ha llamado pormedio de la url.
        */
        public function render()
        {
            $this->verify($this->_file, $this->_controller, $this->_method);
            call_user_func_array([new $this->_controller, $this->_method], $this->_params);
        }
     
         
    }/*close class*/