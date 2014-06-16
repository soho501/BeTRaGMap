<?php

namespace FacebookHandler\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\EventManager\EventManagerInterface;
use Zend\Json\Json;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use \Exception;

/**
 * This is the entry point for out public API, for the message in the platform
 * @author ebelinchon
 *
 */

class FacebookRestController extends AbstractRestfulController {
	
	public function getList()
	{
		$response = $this->getResponse();
		$response->setStatusCode(405);

		$result = array(
			'Error' => array(
				'HTTP Status' => '405',
				'Message' => 'This methods is not allowed ',	
			),
		);
		
		return new JsonModel($result);
	}
	
	public function create($data){
		try{
			$message = null;
			
			$fs = $this->getServiceLocator()->get('Facebook Service');
			$config = $this->getServiceLocator()->get('Config');
			
			$facebooktoken = $fs->loadToken($config['facebook']['AppId']);
			
			$this->getEventManager()->trigger('log', $this, array(
					'priority' => \Zend\Log\Logger::ERR,
					'message' =>  "Access token : ".$facebooktoken->token,
			));
			
			if($facebooktoken == null){
				$result = array("Error" => "No facebooktoken on the database!");
			}
			else {
			
				$session = new FacebookSession($facebooktoken->token);
				$session->setDefaultApplication($config['facebook']['AppId'], $config['facebook']['Secret']);
				$debugInfo = $session->getSessionInfo();
				if (!$debugInfo->isValid()){
					$result = array("Error" => "No valid facebooktoken!!");
				}
				else {
						$this->getEventManager()->trigger('log', $this, array(
								'priority' => \Zend\Log\Logger::ERR,
								'message' =>  print_r($data,true),
						));
							
						if((isset($data['messageref']))&&(isset($data['content']))){
							$message = $fs->sendMessagestoFacebook($session, $data);
							$result = $message;
						}
						else{
							$result = array("Error" => "Invalid paramters");
						}
				}
			}
			return new JsonModel(array('result' => $result));
		}
		catch(\Exception $e){
			$result = array(
					'Error' => array(
						'HTTP Status' => '400',
						'Message' => $e->getMessage(),					),
			);
			return new JsonModel($result);	
		}
	}
}

