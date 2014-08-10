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
   		$owner = $cs->getGroupOwner($url);
   		$gm = $this->getServiceLocator()->get('BTRG\Model\Mapper\GroupMapper');
   		$bm = $this->getServiceLocator()->get('BTRG\Model\Mapper\BackerMapper');
   		$bgm = $this->getServiceLocator()->get('BTRG\Model\Mapper\BackerGroupMapper');
   		
   		$gresult = $gm->getGroupByUrl($group["URL"]);
   		
   		if($gresult == null){
   			$groupid = $gm->addGroup($group);
   		}else{
   			$groupid = $gresult->ID;
   		}
   		
   		$backers = $cs->getGroupBakckers($url.'backers/',$owner);
   		
   		foreach($backers as $backer){
   			
   			$bresult = $bm->getBackerByName($backer["NAME"]);
   			if($bresult == null){
   				$backerid = $bm->addBacker($backer);
   			}else{
   				$backerid = $bresult->ID;
   			}
   			
   			$backeridgroupid = array("BACKERID" => $backerid, "GROUPID" => $groupid);
   			$bgm->addBackerGroup($backeridgroupid);
   			
   			$backergroups = $cs->getBackerGroups($backer["URL"]);
   			
   			foreach($backergroups as $backergroup){
   				$group = $cs->getGroupDetails($backergroup["URL"]);
   				//check group restrictionss
   				if ($cs->checkValidGroup($group)){
   					//check that group doesn't exist
   					$gresult = $gm->getGroupByUrl($backergroup["URL"]);
   					if ($gresult == null){
   						//we add the new group to the db
   						$ngroupid = $gm->addGroup($group);
   					}else{
   						$ngroupid = $gresult->ID;
   					}
   					//chect if that relation exists
   					$gbresult = $bgm->getRelationID($ngroupid,$backerid);
   					if ($gbresult == null) {
   						//we add the new relation to the db
   						$backeridgroupid = array("BACKERID" => $backerid, "GROUPID" => $ngroupid);
   						$bgm->addBackerGroup($backeridgroupid);
   					}
   					//if it exists we don't do anything.
   				}
   			}
   		}

   		$message = "don't mind me";
   		return array('message' => $message);
    }
}