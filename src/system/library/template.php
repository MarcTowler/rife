<?php
/**
 * Template Class
 *
 * Handles all the template based tasks - Thanks to tuts+ for the helping tutorial
 *  by Jason Lengstorf
 *
 * @package		Framework
 * @author		Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright	Copyright (c) 2008 - 2011 Design Develop Realize
 * @license		http://www.designdeveloprealize.com/products/framework/license.html
 * @link		http://www.designdeveloprealize.com
 * @since		Version 0.1
 * @filesource
 */
class Template
{

    /**
     * Stores the location of the template file
     * @var string
     */
    public $template_file,

    /**
     * Stores the entries to be parsed in the template
     * @var array
     */
           $entries = array();

    /**
     * Stores the contents of the template file
     * @var string
     */
    private $_template;

    /**
     * Generates markup by inserting entry data into the template file
     * 
     * @param array $extra  Extra data for the header/footer
     * @return string       The HTML with entry data inserted into the template
     */
    public function generate_markup($extra=array())
    {
        $this->_load_template();
        return $this->_parse_template($extra);
    }

    /**
     * Loads a template file with which markup should be formatted
     *
     * @return string The contents of the template file
     */
    private function _load_template()
    {
        // Check for a custom template
        $template_file = 'system/application/views/' . $this->template_file . '.php';
        if( file_exists($template_file) && is_readable($template_file) )
        {
            $path = $template_file;
        }

        // Look for a system template
        else if( file_exists($default_file = 'system/application/views/constants/header.php')
                && is_readable($default_file) )
        {
            $path = $default_file;
        }

        // If the default template is missing, throw an error
        else
        {
            throw new Exception( 'No default template found' );
        }

        if(file_exists('system/application/views/constants/header.php') && is_readable('system/application/views/constants/header.php'))
		{
			$this->_template = file_get_contents('system/application/views/constants/header.php');
		}
        // Load the contents of the file and return them
        
        $this->_template .= file_get_contents($path);
		
		if(file_exists('system/application/views/constants/footer.php') && is_readable('system/application/views/constants/footer.php'))
		{
			$this->_template .= file_get_contents('system/application/views/constants/footer.php');
		}
    }

    /**
     * Separates the template into header, loop, and footer for parsing
     *
     * @param array $extra      Additional content for the header/footer
     * @return string           The entry markup
     */
    private function _parse_template($extra = NULL)
    {
        // Create an alias of the template file property to save space
        $template = $this->_template;

        // Remove any PHP-style comments from the template
        $comment_pattern = array('#/\*.*?\*/#s', '#(?<!:)//.*#');
        $template = preg_replace($comment_pattern, NULL, $template);

        // Extract the main entry loop from the file
        $loop_pattern = '#.*{loop}(.*?){/loop}.*#is';
        $entry_template = preg_replace($loop_pattern, "$1", $template);

        // Extract the header from the template if one exists
        $header = trim(preg_replace('/^(.*)?{loop.*$/is', "$1", $template));
        if( $header===$template )
        {
            $header = NULL;
        }

        // Extract the footer from the template if one exists
        $footer = trim(preg_replace('#^.*?{/loop}(.*)$#is', "$1", $template));
        if( $footer===$template )
        {
            $footer = NULL;
        }

        // Define a regex to match any template tag
        $tag_pattern = '/{(\w+)}/';

        // Curry the function that will replace the tags with entry data
        $callback = $this->_curry('Template::replace_tags', 2);

        // Process each entry and insert its values into the loop
        $markup = NULL;
        for( $i=0, $c=count($this->entries); $i<$c; ++$i )
        {
            $markup .= preg_replace_callback(
                            $tag_pattern,
                            $callback(serialize($this->entries[$i])),
                            $entry_template
                        );
        }

        // If extra data was passed to fill in the header/footer, parse it here
        if( is_object($extra) )
        {
            foreach( $extra as $key=>$props )
            {
                $$key = preg_replace_callback(
                            $tag_pattern,
                            $callback(serialize($extra->$key)),
                            $$key
                        );
            }
        }

        // Return the formatted entries with the header and footer reattached
        return $header . $markup . $footer;
    }

    /**
     * Replaces template tags with the corresponding entry data
     *
     * @param string $entry     A serialized entry object
     * @param array $params     Parameters for replacement
     * @param array $matches    The match array from preg_replace_callback()
     * @return string           The replaced template value
     */
    public static function replace_tags($entry, $matches)
    {
        // Unserialize the object
        $entry = unserialize($entry);

        // Make sure the template tag has a matching array element
        if( property_exists($entry, $matches[1]) )
        {
            // Grab the value from the Entry object
            return $entry->{$matches[1]};
        }

        // Otherwise, simply return the tag as is
        else
        {
            return "{".$matches[1]."}";
        }
    }

    /**
     * A currying function
     *
     * Currying allows a function to be called incrementally. This means that if
     * a function accepts two arguments, it can be curried with only one
     * argument supplied, which returns a new function that will accept the
     * remaining argument and return the output of the original curried function
     * using the two supplied parameters.
     *
     * Example:

        function add($a, $b)
        {
         return $a + $b;
        }

        $func = $this->_curry('add', 2);

        $func2 = $func(1); // Stores 1 as the first argument of add()

        echo $func2(2); // Executes add() with 2 as the second arg and outputs 3

     * @param string $function  The name of the function to curry
     * @param int $num_args     The number of arguments the function accepts
     * @return mixed            Function or return of the curried function
     */
    private function _curry($function, $num_args)
    {
        return create_function('', "
            // Store the passed arguments in an array
            \$args = func_get_args();

            // Execute the function if the right number of arguments were passed
            if( count(\$args)>=$num_args )
            {
                return call_user_func_array('$function', \$args);
            }

            // Export the function arguments as executable PHP code
            \$args = var_export(\$args, 1);

            // Return a new function with the arguments stored otherwise
            return create_function('','
                \$a = func_get_args();
                \$z = ' . \$args . ';
                \$a = array_merge(\$z,\$a);
                return call_user_func_array(\'$function\', \$a);
            ');
        ");
    }

}