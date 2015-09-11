<?php

$config = parse_ini_file('/config/application.ini', true);

//Need to setup a few config bits
define('BASE_DIR', $config['site']['Base_Dir']);
date_default_timezone_set($config['time']['default_timezone']);

require_once('library/loader.php');

$loader = new loader();