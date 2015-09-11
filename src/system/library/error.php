<?php
/**
 * Error Handling Class
 * 
 * Allows the use of a custom error handler to control messages and logging
 *
 * @package		Framework
 * @author		Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright	Copyright (c) 2008 - 2011 Design Develop Realize
 * @license		http://www.designdeveloprealize.com/products/framework/license.html
 * @link		http://www.designdeveloprealize.com
 * @since		Version 0.1
 * @filesource
 */
class error {
    public $ip;
    public $show_user;
    public $show_developer;
    public $email;
    public $log_file;
    public $log_message;
    public $email_sent = false;
    public $error_codes;
    public $warning_codes;
    public $error_names;
    public $error_numbers;
    public $errno;
    public $errstr;
    public $errfile;
    public $errline;
    public $errcontext;

    /**
     * Construct Function
     *
     * @access public
     * @param integer  The IP address that generated the error
     * @param boolean  Switch to show general user's any errors or not
     * @param integer  Error display level for developers
     * @param string   Email address to send an error alert to
     * @param string   Path to log file to store error details
     */
    public function __contruct($ip = '127.0.0.1', $user = true, $dev = 2, $email, $log = 'test.com.txt')
    {
        $this->ip = $ip;
        $this->show_user = $user;
        $this->show_developer = $dev;
        $this->email = $email;
        $this->log_file = $log;
        $this->log_message = null;
        $this->error_codes = E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR;
        $this->warning_codes = E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING | E_USER_WARNING;

        $this->error_names = array ('E_ERROR', 'E_WARNING', 'E_PARSE', 'E_NOTICE',
                                    'E_COMPILE_ERROR', 'E_COMPILE_WARNING',
                                    'E_USER_ERROR', 'E_USER_WARNING',
                                    'E_USER_NOTICE', 'E_STRICT', 'E_RECOVERABLE_ERROR');

        for($i = 0, $j = 1, $num = count($this->error_names); $i < $num; $i++, $j = $j * 2)
        {
            $this->error_numbers[$j] = $this->error_names[$i];
        }
    }

    public function handle($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $this->errno = $errno;
        $this->errstr = $errstr;
        $this->errfile = $errfile;
        $this->errline = $errline;
        $this->errcontext = $errcontext;

        if($this->log_file)
        {
            $this->log_error_msg();
        }

        if($this->email)
        {
            $this->send_error_msg();
        }

        if($this->show_user)
        {
            $this->error_msg_basic();
        }

        if($this->show_developer > 0 && preg_match("/^$this->ip$/i", $_SERVER['REMOTE_ADDR']))
        {
            $this->error_msg_detailed();
        }

       //Don't execute PHP internal error handler
       return true;
    }

    public function error_msg_basic()
    {
        $message;

        if($this->errno && $this->error_codes)
        {
            $message .= "<strong>ERROR:</strong> There has been a slight issue";
        }
        elseif ($this->warning_codes)
        {
            $message .= "<strong>WARNING:</strong> Something is not quite right";
        }

        if($message && $this->email_sent)
        {
            $message .= " The developers have been notified.<br />\n";
        } else {
            $message .= "<br />\n";
        }

        echo $message;
    }

    public function error_msg_detailed()
    {
        //settings for error display...
        $silent    = (2 && $this->show_developer) ? true : false;
        $context   = (4 && $this->show_developer) ? true : false;
        $backtrace = (8 && $this->show_developer) ? true : false;

        switch(true)
        {
            case(16 && $this->show_developer):
                $color = 'white';
                break;
            case(32 && $this->show_developer):
                $color = 'black';
                break;
            default: 
                $color = 'red';
        }

        $message =  ($silent) ? "<!--\n" : '';

        $message .= "<pre style='color:$color;'>\n\n";
        $message .= "file: " . print_r($this->errfile, true) . "\n";
        $message .= "line: " . print_r($this->errline, true) . "\n\n";
        $message .= "code: " . print_r($this->error_numbers[$this->errno], true) . "\n";
        $message .= "message: " . print_r( $this->errstr, true) . "\n\n";
        $message .= ($context) ? "context: " . print_r($this->errcontext, true) . "\n\n" : '';
        $message .= ($backtrace) ? "backtrace: " . print_r(debug_backtrace(), true)."\n\n" : '';
        $message .= "</pre>\n";
        $message .= ($silent) ? "-->\n\n" : '';

        echo $message;
    }

    public function send_error_msg()
    {
        $message = "file: " . print_r($this->errfile, true) . "\n";
        $message .= "line: " . print_r($this->errline, true) . "\n\n";
        $message .= "code: " . print_r($this->error_numbers[$this->errno], true) . "\n";
        $message .= "message: " . print_r($this->errstr, true) . "\n\n";
        $message .= "log: " . print_r($this->log_message, true) . "\n\n";
        $message .= "context: " . print_r($this->errcontext, true) . "\n\n";
        //$message .= "backtrace: " . print_r($this->debug_backtrace(), true) . "\n\n";

        $this->email_sent = false;

        if(mail($this->email, 'Error: ' . $this->errcontext['SERVER_NAME'] . 
           $this->errcontext['REQUEST_URI'], $message, "From: error@" . 
           $this->errcontext['HTTP_HOST'] . "\r\n"))
        {
            $this->email_sent = true;
        }
    }
        
    public function log_error_msg()
    {
        $message =  "time: " . date("j M y - g:i:s A (T)", mktime()) . "\n";
        $message .= "file: " . print_r($this->errfile, true) . "\n";
        $message .= "line: " . print_r($this->errline, true) . "\n\n";
        $message .= "code: " . print_r($this->error_numbers[$this->errno], true) . "\n";
        $message .= "message: " . print_r($this->errstr, true) . "\n";
        $message .= "##################################################\n\n";

        if(!$fp = fopen($this->log_file, 'a+'))
        {
            $this->log_message = "Could not open/create file: $this->log_file 
            to log error."; 
            $log_error = true;
        }

        if(!fwrite($fp, $message))
        {
            $this->log_message = "Could not log error to file: $this->log_file. 
            Write Error."; 
            $log_error = true;
        }

        if(!$this->log_message)
        {
            $this->log_message = "Error was logged to file: $this->log_file.";

            fclose($fp); 
        }
    }
}