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
class DashboardController extends AbstractActionController
{
   
   	public function indexAction() {
   		$url = "http://www.crowdfunder.co.uk/cambrian-wildwood-project/";
   		$cs = $this->getServiceLocator()->get('Crowfunder Service');
   		$group = $cs->getGroupDetails($url);
   		print_r($group);
   		$owner = $cs->getGroupOwner($url);
   		print_r($owner);
   		//TODO $groupid = insertinDB($values);
   		$backers = $cs->getGroupBakckers($url.'backers/',$owner);
   		print_r($backers);
   		// foeachbackers -> insertarbacker in DB, createlink group backer.
   		// foreachbacker -> getbackerGroups(withrestrictions), insert link group backer.

   		$message = "don't mind me";
   		return array('message' => $message);
    }
}