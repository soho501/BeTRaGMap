<?php

namespace BTRG;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\StaticEventManager;

class Module {

    public function onBootstrap( MvcEvent $e )
    {
       //TODO
    }
    
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array( __NAMESPACE__ => __DIR__ ,),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
  
    public function getServiceConfig() 
    {
    	return array(
    		'invokables' => array(
    			'Fbscraper Service' => 'BTRG\Model\Service\FbscraperService',
    			'Crowfunder Service' => 'BTRG\Model\Service\CrowfunderService',	
    			)
    	);
    }

    
    //public function getViewHelperConfig()   {}
}

