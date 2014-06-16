<?php
/**
 * OneWorld Platform
 *
 * @copyright Copyright (c) 2013 Oneworld UK (http://oneworld.org/)
 */
 
/**
 *  Entry point into all OneWorld applications
 */
 

/*  Change directory to web root so that all files path names are relative
    to it */
    
chdir( dirname ( __DIR__ ) );

/*  Decline static file requests back to the PHP built-in webserver 
   (taken from skeleton ZF2 app - not sure if necessary) */
 
if ( php_sapi_name() === 'cli-server' && 
     is_file( __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) ) {
    return false;
}

/*  Setup autoloading, so bootstrapping the loading of Zend Framework   */

require 'init_autoloader.php';

/*  Start the application, referencing its config file. This could
    contain logic to startup different applications if there were
    several on one (virtual) host */

Zend\Mvc\Application::init( 
    require 'applications/BTRG/config/application.config.php' )->run();
