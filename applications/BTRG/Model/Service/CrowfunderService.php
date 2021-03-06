<?php

namespace BTRG\Model\Service;

use \Exception;

class CrowfunderService extends \Common\Model\ServiceLocatorAware
{
	private static $_cburl = 'http://www.crowdfunder.co.uk/';
	private static $_badwords = array('Community Interest company', 'CIC', 'Foundation', 'Charity', 'Social enterprise', 'Cooperative', 'Co-op', 'Co-operative');
	
	
	private function __initialize() {
		//Todo initialize Mapper
	}

	public function getGroupDetails($groupurl){
		$html = $this->__gethtml($groupurl);
		$values = array();
		$dom = new \DOMDocument;
		@$dom->loadHTML($html);
		
		$h1s = $dom->getElementsByTagName('h1');
		$h1 = $h1s->item(0);
		$as = $h1->getElementsByTagName('a');
		
		if ($as->length == 0){
			//This is a page not found or a deleted group
			return null;
		}
		$a = $as->item(0);
		$values["NAME"] = $a->nodeValue;
		$values["URL"] = $groupurl;
		$values["FUNDEDDATE"] = '0000-00-00 00:00:00';
		$values["DESCRIPTION"] = "";
		$divs = $dom->getElementsByTagName('div');
		foreach($divs as $div){
			$class = $div->getAttribute('class');
			
			if (preg_match('/status-funded/', $class, $matches)){
				 if(preg_match('/(\w{3}\s\d{2},\s\d{4})/', $div->nodeValue, $datematch)){
				 	$timezone = new \DateTimeZone('Europe/Berlin');
				 	$date = new \DateTime($datematch[1],$timezone);
				 	$values["FUNDEDDATE"] = $date->format('Y-m-d H:i:s');
				 }else{
				 	$values["FUNDEDDATE"] = '0000-00-00 00:00:00';
				 }
			}
			
			if (preg_match('/project-body/', $class, $matches)){
				$ps = $div->getElementsByTagName('p');
				
				foreach ($ps as $p){
					$values["DESCRIPTION"] .= $p->nodeValue;
				}
				
			}
			
			if (preg_match('/item tgts/', $class, $matches)){
					$item_tags = $div->getElementsByTagName('span');
					foreach($item_tags as $item){
						$type = $item->getAttribute('class');
						if ($type == 'sofar'){
							$nodevalue = str_replace(',', '', $item->nodeValue);
							$values["RAISED"] = filter_var($nodevalue, FILTER_SANITIZE_NUMBER_INT);
						}else if($type == 'target'){
							$nodevalue = str_replace(',', '', $item->nodeValue);
							$values["TARGET"] = filter_var($nodevalue, FILTER_SANITIZE_NUMBER_INT);
						}else if($type == 'backers'){
							$nodevalue = str_replace(',', '', $item->nodeValue);
							$values["BACKERS"] = filter_var($nodevalue, FILTER_SANITIZE_NUMBER_INT);
						}else if($type == 'days-left'){
							$nodevalue = str_replace(',', '', $item->nodeValue);
							$values["DAYS"] = filter_var($nodevalue, FILTER_SANITIZE_NUMBER_INT);
						}
					}
			}
		}
		return $values;
	}
	
	public function getGroupBakckers($url,$owner){
		$backers = array();
		$values = array();
		
		$html = $this->__gethtml($url);
		$dom = new \DOMDocument;
		@$dom->loadHTML($html);
		
		$as = $dom->getElementsByTagName('a');
		
		foreach($as as $a){
			$class = $a->getAttribute('class');
			if (preg_match('/fname/', $class, $matches)){
				if($owner["NAME"] !=  str_replace(">","",$a->getAttribute('title'))){
					$values["NAME"] = $a->getAttribute('title');
					$values["URL"]  = $a->getAttribute('href');
					$backers[] = $values;
				}
			}
 		}
		return $backers;
	}
	
	public function getGroupOWner($groupurl){
		$html = $this->__gethtml($groupurl);
		$values = array();
		$dom = new \DOMDocument;
		@$dom->loadHTML($html);
		
		$divs = $dom->getElementsByTagName('div');
		foreach($divs as $div){
			$class = $div->getAttribute('class');
			if (preg_match('/author visible-md visible-lg/', $class, $matches)){
				$as = $div->getElementsByTagName('a');
				foreach($as as $a){
					$class = $a->getAttribute('class');
					if (preg_match('/fname/', $class, $matches)){
						$values["NAME"] = str_replace(">","",$a->getAttribute('title'));
						$url = str_replace( "http://www.crowdfunder.co.uk","",$a->getAttribute('href'));
						$values["URL"] = str_replace("?","",$url);
					}
				}
			}
		}
		return $values;
	}
	
	public function getBackerGroups($backerurl){
		$html = $this->__gethtml("http://www.crowdfunder.co.uk/".$backerurl);
		$groups = array();
		$dom = new \DOMDocument;
		@$dom->loadHTML($html);
		
		$divs = $dom->getElementsByTagName('div');
		foreach($divs as $div){
			$class = $div->getAttribute('id');
			if (preg_match('/project(.*)/', $class, $matches)){
				$as = $div->getElementsByTagName('a');
				foreach($as as $a){
					$class = $a->getAttribute('class');
					if (preg_match('/project-thumb/', $class, $matches)){
						$values["NAME"] = str_replace(">","",$a->getAttribute('title'));
						$url = $a->getAttribute('href');
						$values["URL"] = str_replace("?","",$url);
						$groups[] = $values;
					}
				}
			}
		}
		return $groups;
	}
	
	public function checkValidGroup($group){
		$valid = true;
		if ($group["TARGET"] > $group["RAISED"]){
			$valid = false;
		}
		$now = new \DateTime();
		$currentyear = $now->format("Y");
		$fundeddate = new \DateTime($group["FUNDEDDATE"]);
		$fundedyear = $fundeddate->format("Y");
		$fundedyear += 2;
		if ($fundedyear < $currentyear){
			$valid = false;
		}
		
		foreach(self::$_badwords as $badword){
			if (preg_match('/'.$badword.'/', $group["DESCRIPTION"], $matches)){
				$valid = false;
			}
		}
		
		return $valid;
	}
	
	public function __gethtml($url){
		$config = $this->getServiceLocator()->get('Config');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_COOKIEJAR, $config['scraper']['cookies']);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $config['scraper']['cookies']);
		curl_setopt($ch, CURLOPT_USERAGENT, $config['scraper']['uagent']);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$html = curl_exec($ch);
		curl_close($ch);
		return($html);
	}
	
}
