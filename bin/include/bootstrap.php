<?php

// Define path to application directory
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH,
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

include_once "Zend/Loader/Autoloader.php";

$loader = Zend_Loader_Autoloader::getInstance();

// we need this custom namespace to load our custom class
//$loader->registerNamespace('Octopus_');

if(!isset ($cliOptions)) $cliOptions = array();
$cliOptions = array_merge($cliOptions,
    array(
        'env|e-s'    => 'defines application environment (defaults to "production")',
        'help|h'     => 'displays usage information'
    )
);

// define application options and read params from CLI
$getopt = new Zend_Console_Getopt($cliOptions);

try {
    $getopt->parse();    
} catch (Zend_Console_Getopt_Exception $e) {
    
    // Bad options passed: report usage
    echo $e->getUsageMessage();
    die();
}

// show help message in case it was requested or params were incorrect (module, controller and action)
if ($getopt->getOption('h')) {
    echo $getopt->getUsageMessage();    
    die();
}

// initialize values based on presence or absence of CLI options
$env      = $getopt->getOption('e');

define('APPLICATION_ENVIRONMENT', (null === $env) ? 'production' : $env);

// CONFIGURATION - Setup the configuration object
// The Zend_Config_Ini component will parse the ini file, and resolve all of
// the values for the given section.  Here we will be using the section name
// that corresponds to the APP's Environment
$configuration = new Zend_Config_Ini(
    APPLICATION_PATH . '/configs/application.ini',
    APPLICATION_ENVIRONMENT
);

//save to registry
Zend_Registry::set('config', $configuration);

// Create application, bootstrap, and run
$application = new Zend_Application(
    $env,
    $configuration
);
$application->bootstrap(array('db'));