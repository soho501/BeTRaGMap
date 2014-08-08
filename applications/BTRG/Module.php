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
    		'factories' => array(
    				'BTRG\Model\Mapper\GroupMapper' => function($sm) {
    					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					$mapper = new Model\Mapper\GroupMapper($dbAdapter);
    					return $mapper;
    				},
    				'BTRG\Model\Mapper\BackerMapper' => function($sm) {
    					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					$mapper = new Model\Mapper\BackerMapper($dbAdapter);
    					return $mapper;
    				},
    				'BTRG\Model\Mapper\CategoryMapper' => function($sm) {
    					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					$mapper = new Model\Mapper\CategoryMapper($dbAdapter);
    					return $mapper;
    				},
    				'BTRG\Model\Mapper\CategoryGroupMapper' => function($sm) {
    					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					$mapper = new Model\Mapper\CategoryGroupMapper($dbAdapter);
    					return $mapper;
    				},
    				'BTRG\Model\Mapper\BackerGroupMapper' => function($sm) {
    					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					$mapper = new Model\Mapper\BackerGroupMapper($dbAdapter);
    					return $mapper;
    				},
    		),
    		'invokables' => array(
    			'Fbscraper Service' => 'BTRG\Model\Service\FbscraperService',
    			'Crowfunder Service' => 'BTRG\Model\Service\CrowfunderService',	
    			)
    	);
    }

    
    //public function getViewHelperConfig()   {}
}

