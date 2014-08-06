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
class CrowfunderController extends AbstractActionController
{
   
   	public function indexAction() {
   		$cs = $this->getServiceLocator()->get('Crowfunder Service');
   		$cs->getGroupDetails("http://www.crowdfunder.co.uk/cambrian-wildwood-project/");
    }
}