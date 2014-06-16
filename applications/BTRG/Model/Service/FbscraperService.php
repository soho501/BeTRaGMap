<?php

namespace BTRG\Model\Service;

use \Exception;

class FbscraperService extends \Common\Model\ServiceLocatorAware
{
	private static $_fburl = 'https://www.facebook.com/';
	
	
	private function __initialize() {
		//Todo initialize Mapper
	}

	public function logingfb(){
		$config = $this->getServiceLocator()->get('Config');
		
		/*
		 * Grab login page and parameters
		*/
		$loginpage = $this->grab_home();
		$form_action = $this->parse_action($loginpage);
		$inputs = $this->parse_inputs($loginpage);
		$post_params = "";
		foreach ($inputs as $input) {
			switch ($input->getAttribute('name')) {
				case 'email':
					$post_params .= 'email=' . urlencode($config['scraper']['email']) . '&';
					break;
				case 'pass':
					$post_params .= 'pass=' . urlencode($config['scraper']['pass']) . '&';
					break;
				default:
					$post_params .= $input->getAttribute('name') . '=' . urlencode($input->getAttribute('value')) . '&';
			}
		}
		//echo "[i] Using these login parameters: $post_params";
		/*
		* Login using previously collected form parameters
		 */
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_COOKIEJAR, $config['scraper']['cookies']);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $config['scraper']['cookies']);
		curl_setopt($ch, CURLOPT_USERAGENT, $config['scraper']['uagent']);
		curl_setopt($ch, CURLOPT_URL, $form_action);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
		$loggedin = curl_exec($ch);
		//echo $loggedin;
		curl_close($ch);
		/*
		* Check if location checking is turned on or you have to verify location
		*/
		if (strpos($loggedin, "machine_name") || strpos($loggedin, "/checkpoint/") || strpos($loggedin, "submit[Continue]")) {
			echo "\n[i] Found a checkpoint...\n";
        	checkpoint($loggedin);
		    echo "\n[i] Checkpoints passed...\n";
    	}
    	return true;
	}
	
	public function getFbGroupInfo(){
		
	}
	
	public function getFbGroupUsers($groupid){
		$config = $this->getServiceLocator()->get('Config');
		
		
		$url = 'https://m.facebook.com/browse/group/members/?id='.$groupid;
		$next = true;
		$count = 0;
		
		while($next == true){
			$html = $this->__gethtml($url);
			$dom = new \DOMDocument;
			@$dom->loadxml($html);
			$divs = $dom->getElementsByTagName('div');
			$next = false;
			foreach($divs as $div){
				$id = $div->getAttribute('id');
				if (preg_match('/member_(.*)/', $id, $matches))
				{
					echo $matches[1]."\n";
				}else if(preg_match('/m_more_.*/', $id)){
					$a = $div->getElementsByTagName('a');
					$next_url = $a->item(0)->getAttribute('href');
					$url = 'https://m.facebook.com'.$next_url;
					$next = true;
					sleep(1);
				}
			}
		}

	}
	
	public function getFbUserGroups(){
	
	}
	
	/*
	 * @return form input field names & values
	*/
	private function parse_inputs($html) {
		$dom = new \DOMDocument;
		@$dom->loadxml($html);
		$inputs = $dom->getElementsByTagName('input');
		return($inputs);
	}
	
	/*
	 * @return form action url
	*/
	private function parse_action($html) {
		$dom = new \DOMDocument;
		@$dom->loadxml($html);
		$form_action = $dom->getElementsByTagName('form')->item(0)->getAttribute('action');
		if (!strpos($form_action, "//")) {
			$form_action = "https://m.facebook.com$form_action";
		}
		return($form_action);
	}
	
	/*
	 * grab and return the homepage
	*/
	function grab_home() {
		$config = $this->getServiceLocator()->get('Config');
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_COOKIEJAR, $config['scraper']['cookies']);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $config['scraper']['cookies']);
		curl_setopt($ch, CURLOPT_USERAGENT, $config['scraper']['uagent']);
		curl_setopt($ch, CURLOPT_URL, 'https://m.facebook.com/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$html = curl_exec($ch);
		//echo $html;
		curl_close($ch);
		return($html);
	}
	
	/*
	 * pass checkpoints
	*/
	private function checkpoint($html) {
		$config = $this->getServiceLocator()->get('Config');
		
		$form_action = $this->parse_action($html);
		$inputs = $this->parse_inputs($html);
		$post_params = "";
		foreach ($inputs as $input) {
			switch ($input->getAttribute('name')) {
				case "":
					break;
				case "submit[I don't recognize]":
					break;
				case "submit[Don't Save]":
					break;
				case "machine_name":
					$post_params .= 'machine_name=' . urlencode( $config['scraper']['device_name']) . '&';
					break;
				default:
					$post_params .= $input->getAttribute('name') . '=' . urlencode($input->getAttribute('value')) . '&';
			}
		}
		if ($GLOBALS['debug']) {
			echo "\nCheckpoint form action: $form_action\n";
			echo "\nCheckpoint post params: $post_params\n";
		}
		//Verify the machine
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_COOKIEJAR, $config['scraper']['cookies']);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $config['scraper']['cookies']);
			curl_setopt($ch, CURLOPT_USERAGENT, $config['scraper']['uagent']);
			curl_setopt($ch, CURLOPT_URL, $form_action);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
			$home = curl_exec($ch);
			curl_close($ch);
	
			if (strpos($home, "machine_name") || strpos($home, "/checkpoint/") || strpos($home, "submit[Continue]")) {
        		echo "\n[i] Solving another checkpoint...\n";
	        	checkpoint($home);
    		}
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
