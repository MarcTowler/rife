<?php
/**
 * Model Base Class
 *
 * Handles the nitty gritty parts of managing and using Model data
 *
 * @package		Framework
 * @author		Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright	Copyright (c) 2008 - 2011 Design Develop Realize
 * @license		http://www.designdeveloprealize.com/products/framework/license.html
 * @link		http://www.designdeveloprealize.com
 * @since		Version 0.1
 * @filesource
 */
class Model {
    public $db;
    public $loader;

    public function __construct()
    {
        global $loader;
        
        $this->loader = $loader;

        $this->db = $this->loader->loadDatabase();

    }
}