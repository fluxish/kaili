<?php  if ( ! defined('ROOT')) exit('No direct script access allowed');

/**
 * Kaili Loader Class
 *
 *
 * @package		Kaili
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Luigi Marco Simonetti
 */
 
class Loader
{
    /**
     * Singleton instance of Loader
     * @var Loader
     */   
    private static $_instance = null;
    
    /**
     * Array of loaded libraries
     * @var array
     */
    private $_loaded_libraries = array();
    
    /**
     * Instance of Config class
     * @var Config
     */
    private $_config;
    
    /**
     * Array of default loaded libraries
     * @var array
     */
    private $_internal_autoload = array('input', 'output');
    
    
    
    /**
     * Create the loader for all classes 
     */
    private function __construct()
    {
        spl_autoload_register('Loader::__autoload');
        $this->_config = $this->library('config');
    }
    
    /**
     * Load an helper
     * @param string $helper the unique name of the helper 
     */
    public function helper($helper)
    {
        include_once(SYSTEM.DS.'helpers'.DS.$helper.'.helper.php');
    }
    
    /**
     * Load a class from the framework's library
     * @param string $lib the name of the class
     */
    public function library($lib)
    {
        if(!array_key_exists($lib, $this->_loaded_libraries)){
            $libName = ucwords($lib);
            $this->_loaded_libraries[$lib] = new $libName();
        }
        return $this->_loaded_libraries[$lib];
    }
    
    /**
     * Load an MVC controller
     * @param string $controller the name of the controller (for convention,
     *        in plural form)  
     * @param string $action the name of controller's action called 
     * @param array $url_segments an array of parameters give from the url
     */
    public function controller($controller, $action, $url_segments = array())
    {
        $controller_name = $controller;
        $action_name = strtolower($action);
        
        // creation of controller
        $controller = ucwords($controller_name);
	    $controller = new $controller();
	    
        // call action of the controller
	    if ((int)method_exists($controller_name, $action_name)) {
		    call_user_func_array(array($controller, $action_name), array_values($url_segments));
	    } 
    }
    
    /** 
     * Check if environment is development and display errors 
     */
    public function set_reporting() 
    {
        if($this->_config->item('development_environment') == true)
        {
            error_reporting(E_ALL);
            ini_set('display_errors','On');
            ini_set('html_errors', 'On');
        } 
        else{
            error_reporting(E_ALL & ~E_DEPRECATED);
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
            ini_set('error_log', ROOT.DS.'system'.DS.'tmp'.DS.'logs'.DS.'error.log');
        }
    }
    
    /**
     * Prepare the framework to the requests
     * @param function $pre_controller a closure to call before controller
     * @param function $post_controller a closure to call after controller
     */
    public function start($pre_controller = null, $post_controller = null) 
    {
        $this->set_reporting();
        $this->_autoload();
        
        // execute a pre-controller call function
        if(is_callable($pre_controller)) $pre_controller();
        
	    // call requested controller
	    $controller = $this->library('input')->get('controller');
	    $action = $this->library('input')->get('action');
	    $this->controller($controller, $action, $this->library('input')->get());
	    
	    // execute a post-controller call function
	    if(is_callable($post_controller)) $post_controller();
	    
	    // output all buffer
	    $this->library('output')->display();
    }
    
    /**
     * Autoload all classes selected in the config file autoload.php
     */
    private function _autoload()
    {
        // autoload internal libraries
        foreach($this->_internal_autoload as $lib){
            $this->library($lib);
        }
        
        // autoload libraries in autoload config file
        $libraries = $this->_config->item('libraries');
        foreach($libraries as $lib){
            $this->library($lib);
        }
        unset($libraries, $lib);
        
        // autoload helpers
        $helpers = $this->_config->item('helpers');
        foreach($helpers as $help){
            $this->helper($help);
        }
        unset($helpers, $help);
    }
    
    /**
     * Returns the singleton instance of the loader
     * @return Loader
     */
    public static function get_instance()
    {
        if(self::$_instance == null){
            self::$_instance = new Loader();
        }
        return self::$_instance;
    }
    
    /** 
     * Autoload any classes that are required
     * @param string $className the name of required class
     */
    public static function __autoload($className) 
    {
        // create the path from the name of the class
        $path = str_replace('_', DS, $className);
        
	    if(file_exists(SYSTEM.DS.'library'.DS.$path.EXT)) {
		    require_once(SYSTEM.DS.'library'.DS.$path.EXT);
	    }
        else if(file_exists(APPLICATION.DS.'controllers'.DS.$className.EXT)) {
		    require_once(APPLICATION.DS.'controllers'.DS.$className.EXT);
	    } 
	    else if(file_exists(APPLICATION.DS.'models'.DS.$className.EXT)) {
		    require_once(APPLICATION.DS.'models'.DS.$className.EXT);
	    } 
    }
}

/* End of file Loader.php */
/* Location: ./system/library/Loader.php */
