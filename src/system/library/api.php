<?php
/**
 * The API for the framework, this talks to the database, external applications and will acts as a gopher
 *
 * @category       Framework
 * @package        API
 * @author         Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright      Copyright (c) 2012 Design Develop Realize
 * @license        http://www.designdeveloprealize.com/products/framework/license.html
 * @link           http://www.designdeveloprealize.com
 * @since          Framework Version 0.2
 * @version        0.1
 * @filesource
 */

class api {

	/**
	 * HTTP status codes
	 * @var array
	 */
	private $codes = array(
        100 => 'Continue',
	    101 => 'Switching Protocols',
	    200 => 'OK',
	    201 => 'Created',
	    202 => 'Accepted',
	    203 => 'Non-Authoritative Information',
	    204 => 'No Content',
	    205 => 'Reset Content',
	    206 => 'Partial Content',
	    300 => 'Multiple Choices',
	    301 => 'Moved Permanently',
	    302 => 'Found',
	    303 => 'See Other',
	    304 => 'Not Modified',
	    305 => 'Use Proxy',
	    306 => '(Unused)',
	    307 => 'Temporary Redirect',
	    400 => 'Bad Request',
	    401 => 'Unauthorized',
	    402 => 'Payment Required',
	    403 => 'Forbidden',
	    404 => 'Not Found',
	    405 => 'Method Not Allowed',
	    406 => 'Not Acceptable',
	    407 => 'Proxy Authentication Required',
	    408 => 'Request Timeout',
	    409 => 'Conflict',
	    410 => 'Gone',
	    411 => 'Length Required',
	    412 => 'Precondition Failed',
	    413 => 'Request Entity Too Large',
	    414 => 'Request-URI Too Long',
	    415 => 'Unsupported Media Type',
	    416 => 'Requested Range Not Satisfiable',
	    417 => 'Expectation Failed',
	    500 => 'Internal Server Error',
	    501 => 'Not Implemented',
	    502 => 'Bad Gateway',
	    503 => 'Service Unavailable',
	    504 => 'Gateway Timeout',
	    505 => 'HTTP Version Not Supported'
	);

    /**
	 * Production/development mode boolean
	 * 
	 * @var boolean
	 */
	protected $production_mode;

    /**
	 * Constructor will setup our environment and make sure we are running ok
	 * 
	 * @param tbc
	 */
	public function __construct($prod = false)
	{
		$this->production_mode = $prod;
	}

    /**
	 * Destructor will look at saving the last request and logging access from user
	 * 
	 * @return void
	 */
	public function __destruct()
	{
	}

    /**
	 * Main handling function for the API
	 * 
	 * @throws RestException when the API controller is missing
	 * @throws RestException sends error messages to client
	 */
    public function parse()
	{
	}

    /**
	 * error_handler will build and return appropriate fail data
	 * 
	 * @param $code The error code
	 * @param tbc
	 * @return tbc
	 */
	public function error_handler($code, $tbc)
	{
	}
}