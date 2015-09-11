<?php
/**
 * Controller Base Class
 *
 * Loads up the nitty gritty controller features to access loader, db etc
 *
 * @package        Framework
 * @author         Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright      Copyright (c) 2008 - 2011 Design Develop Realize
 * @license        http://www.designdeveloprealize.com/products/framework/license.html
 * @link           http://www.designdeveloprealize.com
 * @since          Version 0.1
 * @filesource
 */
class frontController {

    public $template;
    public $toParse = '';
    public $coreParse = array();
    public $model;
    public $loader;

    public function __construct()
    {
        global $loader;
        $this->loader = $loader;
        $this->model  = $this->loader->loadcore('model');


        //Need to get this served up by the loader class
        require_once('system/library/template.php');
		$this->template = new Template;
		
        $this->coreParse = array (
            'path_to_app' => $this->loader->config['site']['baseURL'] . '/system/application',
            'site_name'   => $this->loader->config['site']['name'],
            'path_to_css' => $this->loader->config['site']['baseURL'] . '/system/application/views/constants'
        );
    }

    protected function variables(array $list)
    {
        $this->toParse = array_merge($this->coreParse, $list);
    }

    protected function parse($file, $data)
    {
    	//$file = 'system/application/views/' . $file;
        //quick hax
        if('' === $this->toParse)
        {
            $data = $this->coreParse;
        }
        $this->template->template_file = $file;
		$this->template->entries[] = (object)$data;

		echo $this->template->generate_markup(); 
    }

    protected function autoload_model()
    {
        $name = get_class($this);

        return $this->loader->loadModel($name);
    }

    protected function load_user()
    {
    	$this->loader->loadCore('acl');

        return $this->loader->loaded['acl'];
    }
}
