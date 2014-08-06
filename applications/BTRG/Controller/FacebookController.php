<?php
/**
 * BTRG
 * GPLv3
 */

namespace BTRG\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use \Exception;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

/**
 *  Controller for dashboard page and related Ajax calls
 */
class FacebookController extends AbstractActionController
{
   
   	public function indexAction() {
   		$fs = $this->getServiceLocator()->get('Fbscraper Service');
   		if ($fs->logingfb()){
   			$fs->getFbGroupUsers('181312585243017');
   			echo "Done";
   			return array("message" => "Done");
   		}
    	else {
    		echo "Error!\n";
    		return array("message" => "Ups.. facebook got you!");
    	}
    }
}
