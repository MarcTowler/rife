<?php
/**
 * Framework
 *
 * @package Framework
 * @author  Marc Towler
 */
$start = microtime();
define('ENVIRONMENT', 'development');

/**
 * Setting Error reporting
 *
 * using the above ENVIRONMENT setting we work out what is the appropriate
 * amount of error reporting for the application
 */
switch(ENVIRONMENT)
{
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);

        break;

    case 'testing':
    case 'production':
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT &
                        ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        ini_set('display_errors', 0);

        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        echo("The application environment is not set correctly.");
}

require_once("system/bootstrap.php");

echo("\n<!-- Loaded in " . (microtime() - $start) . " seconds-->");