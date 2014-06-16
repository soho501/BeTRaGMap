<?php
namespace FacebookHandler;

use Zend\Mvc\MvcEvent;
use Zend\Log\Writer\Stream;
use Zend\Log\Writer;
use Zend\Log\Logger;
use Zend\EventManager\StaticEventManager;
use ZendHttpRequest as HttpRequest;
use ZendViewModelJsonModel;
use ZendViewModelModelInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface,
Zend\ModuleManager\Feature\ConfigProviderInterface,
Zend\ModuleManager\Feature\ConsoleUsageProviderInterface,
Zend\Console\Adapter\AdapterInterface as Console;

class Module implements
    AutoloaderProviderInterface,
    ConsoleUsageProviderInterface {
	
	public function getAutoloaderConfig() {
		return array(
				'Zend\Loader\ClassMapAutoloader' => array(
						__DIR__ . '/autoload_classmap.php',
				),
				'Zend\Loader\StandardAutoloader' => array('namespaces' =>
						array( __NAMESPACE__ => __DIR__ ,),
				),
		);
	}
	
	public function onBootstrap(MvcEvent $e) {
		$logger     = new Logger;
		$filterInfo = new \Zend\Log\Filter\Priority(Logger::INFO);
		$filterErr  = new \Zend\Log\Filter\Priority(Logger::ERR);
		$writerInfo = new Stream('logs/facebookhandler_'. date('Y_m_d') .'.log');
		$writerErr  = new Stream('logs/err_facebookhandler_'. date('Y_m_d') .'.log');
		
		$logger->addWriter($writerInfo);
		$logger->addWriter($writerErr);
		
		$writerInfo->addFilter($filterInfo);
		$writerErr->addFilter($filterErr);
		
		$events = StaticEventManager::getInstance();
		$events->attach('*', 'log', function($event) use ($logger) {
			$target   = get_class($event->getTarget());
			$message  = $event->getParam('message', 'No message provided');
			$priority = (int) $event->getParam('priority', Logger::INFO);
			$message  = sprintf('%s: %s', $target, $message);
			$logger->log($priority, $message);
		});
	}
    
    
    public function getServiceConfig() {
    	return array(
    			'invokables' => array(
    					'Message Service' => 'FacebookHandler\Model\Service\MessageService',
    					'Facebook Service' => 'FacebookHandler\Model\Service\FacebookService',
    			),
    			'factories' => array(
    			    'FacebookHandler\Model\Mapper\FacebookTokenMapper' => function($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$facebookmapper = new \FacebookHandler\Model\Mapper\FacebookTokenMapper($dbAdapter);
    						return $facebookmapper;
    				},
    				'FacebookHandler\Model\Mapper\FacebookConversationMapper' => function($sm) {
    					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					$facebookmapper = new \FacebookHandler\Model\Mapper\FacebookConversationMapper($dbAdapter);
    					return $facebookmapper;
    				},
    				'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
    			)
    	);
    }
    
    public function getConfig()
    {
    	return include __DIR__ . '/config/module.config.php';
    }
  
    public function getConsoleUsage(Console $console)
    {
    	return array(
    			// Describe available commands
    			'fetch'    => 'Fetch messages',
       			 array( '--verbose|-v',     '(optional) turn on verbose mode'        ),
    		);
    }
}

