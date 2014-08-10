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
   //For each iteration I empty the db.
   //use the db as a temp cache of backer-groups
   	public function indexAction() {
   		$request = $this->getRequest();
   		
   		$filter       = (bool) $request->getParam('$filter',false);
   		$url = $request->getParam('url');

   		$cs = $this->getServiceLocator()->get('Crowfunder Service');
   		$group = $cs->getGroupDetails($url);
   		$owner = $cs->getGroupOwner($url);
   		$gm = $this->getServiceLocator()->get('BTRG\Model\Mapper\GroupMapper');
   		$bm = $this->getServiceLocator()->get('BTRG\Model\Mapper\BackerMapper');
   		$bgm = $this->getServiceLocator()->get('BTRG\Model\Mapper\BackerGroupMapper');
   		
   		//reset db
   		$gm->deleteAll();
   		$bm->deleteAll();
   		$bgm->deleteAll();
   		
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
   				if($group != null){
   				//check group restrictionss
   				if($filter){
   					$isvalid = $cs->checkValidGroup($group);
   				}else{
   					$isvalid = true;
   				}
   				if ($isvalid){
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
   		}
		//==== Co link ====//
		
   		$ngroups = $gm->getNonEqual($groupid);
   		$link = false;
   		foreach($ngroups as $ng){
   			$commonbacker = $bgm->getBackersByGroupId($ng->ID);
   			if ($commonbacker->count() > 1){
   				$links = true;
   				echo "\n- Group : ".$group["NAME"]." AND ".$ng->NAME." have 2 backers in common\n";
   			}
   		}
   		if (!$links) {
   			echo "\n We couldn't find any links \n";
   		}
    }
}