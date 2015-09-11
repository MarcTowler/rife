<?php
/**
 * Parser Class
 *
 * Handles all of our variable substitution replacement
 *
 * @package		Framework
 * @author		Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright	Copyright (c) 2008 - 2011 Design Develop Realize
 * @license		http://www.designdeveloprealize.com/products/framework/license.html
 * @link		http://www.designdeveloprealize.com
 * @since		Version 0.1
 * @filesource
 */
class parser {
	
	private $store;
	
    public function __construct/*parse*/($template, $data, $return = false)
    {
        //Lets load up our template script
        require_once('template.php');

        $te = new template();
        $template = $te->get_template($template);

        //Did it work or does it not exist?
        if(false === $template)
        {
            trigger_error('Parser::__construct(): Template(' . $template . 'is missing');
        }

        $template = $this->parse_tags($data);


        //if this is not the end of the checks or other files are added then we
        //need to append it to the output
        if($return === false)
        {
            $te->append_data($template);
        } else {
            $te->output($template);
        }
    }

    /**
	 * parse_tags function
	 *
	 * Parse a tag
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
    private function parse_tags($tags = array())
    {
    	if(sizeof($tags) > 0)
		{
			foreach($tags as $tag => $data)
			{
				$data = @(file_exists($data)) ? $this->parse($data) : $data;
				$this->store = str_replace('{' . $tag . '}', $data, $this->store);
			}
		} else {
			die("No tags designated for replacement.");
		}
    }

   /**
    * multi_match function
    *
	 * Matches a pair
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	mixed
	 */
	function multi_match($string, $variable)
	{
		if(!preg_match("|{" . $variable . "}(.+?){" . '/' . $variable . "}|s", $string, $match))
		{
			return false;
		}

		return $match;
	}
}