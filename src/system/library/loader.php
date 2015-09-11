<?php
/**
 * Loader Class
 *
 * This is pretty much the workhorse of the framework, making sure everything is loaded or can be loaded
 *
 * @package		Framework
 * @author		Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright	Copyright (c) 2011 - 2012 Design Develop Realize
 * @license		http://www.designdeveloprealize.com/products/framework/license.html
 * @link		http://www.designdeveloprealize.com
 * @since		Version 0.1
 */
class loader
{

    /**
	 * Array of configuration settings
	 * @var array
	 */
    public $config = array();

    /**
	 * 
	 */
    public $lang;

    
    public $loaded = array(); //array of loaded classes
    public $error;

    /**
     * Constructor
     *
     * Setup the loader class and make it ready!
     *
     * @access public
     */
    public function __construct()
    {
        //Lets Make sure we support the php version
        if(floor(phpversion()) < 5)
        {
            trigger_error("Sorry but this system does not support anything " .
            "older then PHP 5, your version is: " . phpversion(), E_USER_ERROR);
        }

        //Check/Load config
        if(empty($this->config))
        {
            $this->setConfig();
        }

        if(empty($this->lang))
        {
            $this->setLang();
        }
    }

    /**
     * __autoload function
     *
     * Magic function to clean up any stray file calls
     * @access public
     * @param  string The file that was called
     */
    public function __autoload($file)
    {
        //is it a controller?
        if(file_exists('system/application/controllers/' . $file . '_controller.php'))
        {
            //$this->loadController($file);
        }
        //Core?
        elseif(file_exists('system/library/' . $file . '.php'))
        {
            $this->loadCore($file);
        }
        //It can't be a helper can it?
        elseif(file_exists('system/helpers/' . $file . '.php'))
        {
            $this->loadHelper($file);
        }
        // Okay, so we have decided, it is either a herp-a-durp gun or a model...
        elseif(file_exists('system/application/models/' . $file . '_model.php'))
        {
            $this->loadModel($file);
        } else {
            //DANGER! DANGER! IT IS A TRAP! #cockpunch was here
            die("FUCKS SAKE $file");
        }
    }

    /**
     * loadCore function
     *
     * This is used to load any core classes
     *
     * @access public
     * @param  string/array Core file(s) to load
     * @return object the class' object
     */
    public function loadCore($toLoad, $params = false)
    {
        //important task first, check if it is more then 1 or not
        if(is_array($toLoad))
        {
            //more then one so lets go to the task!
            foreach($toLoad as $file)
            {
                if(file_exists('system/library/' . $file . '.php'))
                {
                    require_once('system/library/' . $file . '.php');

                    if($params)
                    {
                        $this->loaded[$file] = new $file($params);
                    } else {

                        $this->loaded[$file] = new $file;
                    }
                } else {
                    trigger_error("Core File $file does not exist");
                }
            }
        } else {
            //Phew, less work, it is only one!
            if(file_exists('system/library/' . $toLoad . '.php'))
            {
                require_once('system/library/' . $toLoad . '.php');

                if($params)
                {
                    $this->loaded[$toLoad] = new $toLoad($params);
                } else {
                    $this->loaded[$toLoad] = new $toLoad;
                }
            } else {
            	trigger_error("Core File $file does not exist");
            }
        }
    }

    /**
     * loadModel function
     *
     * Publicly accessible so that any Model data can be accessed from application
     *
     * @access public
     * @param  string  model to load
     * @return ?
     */
    public function loadModel($load)
    {
        $load = $load . '_model';

        if(file_exists("system/application/models/{$load}.php"))
        {
            require_once("system/application/models/{$load}.php");

            return new $load;
        } else {
            return false;
        }
    }

    /**
     * loadDatabase function
     *
     * Databases are needed too often to be used in loadCore so use this
     *
     * @access public
     * @param  string Type of Database to load, leave blank for config one
     * @return object resource set
     */
    public function loadDatabase($type = '')
    {
        //config or passed variable?
        if($type == '')
        {
            $type = "db_" . $this->config['db']['type'];
        }

        //include the root and type
        //require_once('system/library/database.php');
        require_once('system/library/' . $type . '.php');

        if($type === 'db_pdo')
        {
            return $this->loaded['database'] = new $type("mysql:host=" . 
                   $this->config['db']['hostname'] . ";port=3306;dbname=" .
                   $this->config['db']['schema'], $this->config['db']['username'], 
                   $this->config['db']['password']);
        } else {
            return $this->loaded['database'] = new $type($this->config['db']);
        }
    }

    private function loadHelper() {}
    public function helper() {}

    /**
     * setLang function
     *
     * Sets up either the default language for the site OR the language passed in the param
     *
     * @access public
     * @param  string Two letter code of the langauge file that is wanted, leave blank for config
     * @return boolean
     */
    public function setLang($wanted = '')
    {
        if($wanted == '')
        {
            //Load language file from the configuration setting
        } else {
            //attempt to load the wanted language
            if($wanted = 1/* load_lang here*/)
            {
                $this->lang = $wanted;

                return true;
            } else {
                //it didnt work, set message for error
                return false;
            }
        }
    }

    /**
     * getLang function
     *
     * returns the currently selected language
     *
     * @access public
     * @return string
     */
    public function getLang()
    {
        return $this->lang;    
    }

    /**
     * getConfig function
     *
     * returns the configuration array
     *
     * @access public
     * @return array
     */
    public function getConfig()
    {
        return $this->config;    
    }

    /**
     *
     * setConfig function
     *
     * pull all of the configuration information and stores it in $this->config
     *
     * @access private
     */
    private function setConfig()
    {
        $config = array();

        $tmp = opendir('system/application/config/');

        while(($filename = readdir($tmp)) !== false)
        {
            if(!($filename == '.' || $filename == '..'))
            {
                if(include('system/application/config/' . $filename))
                {
                    foreach($config as $key => $value)
                    {
                        $this->config[$key] = $value;
                    }
                }
            }
        }

        unset($tmp);
        unset($filename);
        unset($config);
    }
}
