<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @copyright Copyright (c) 2005-2013 OneWorld UK ( http://oneworld.org )
 */

/*  Load Zend AutoLoader Factory to load an autoloader  */

include __DIR__ . '/library/zf2/Zend/Loader/AutoloaderFactory.php';

/*  Load the standard autoloader */

Zend\Loader\AutoloaderFactory::factory(
    array( 'Zend\Loader\StandardAutoloader' => array(
           'autoregister_zf' => true )
        ));

/*  Check autoloader was loaded OK */

if ( !class_exists( 'Zend\Loader\AutoloaderFactory' ) ) 
    throw new RuntimeException( 'Unable to load ZF2. ' );
