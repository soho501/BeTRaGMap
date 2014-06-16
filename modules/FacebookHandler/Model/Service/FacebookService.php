<?php

namespace FacebookHandler\Model\Service;

use \Exception;
use FacebookHandler\Model\Entity\FacebookToken;
use FacebookHandler\Model\Entity\FacebookConversation;
use Facebook\FacebookAuthorizationException;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Zend\Json\Json;



class FacebookService extends \Common\Model\ServiceLocatorAware
{
	private static $_FacebookTokenMapper = null;
	private static $_FacebookConversationMapper = null;
	
	public function storeToken($appid, $pagename, $token, $expiredate){
		self::$_FacebookTokenMapper = $this->getServiceLocator()->get( 'FacebookHandler\Model\Mapper\FacebookTokenMapper' );
		
		$facebooktoken = new FacebookToken();
		$facebooktoken->appid = $appid;
		$facebooktoken->pagename = $pagename;
		$facebooktoken->token = $token;
		$facebooktoken->expiredate = $expiredate;
		
		$storedtoken = self::$_FacebookTokenMapper->loadToken($appid);
		if ($storedtoken != null){
			//if we have a token for this app then we update this token.
			$facebooktoken->id = $storedtoken->id;
		}		
		return self::$_FacebookTokenMapper->storeToken($facebooktoken);
		
	}
	
	public function loadToken($appid){
		self::$_FacebookTokenMapper = $this->getServiceLocator()->get( 'FacebookHandler\Model\Mapper\FacebookTokenMapper' );
		return self::$_FacebookTokenMapper->loadToken($appid);
	}
	
	public function fetchMessages($session){
		self::$_FacebookConversationMapper = $this->getServiceLocator()->get( 'FacebookHandler\Model\Mapper\FacebookConversationMapper' );
		$ms = $this->getServiceLocator()->get('Message Service');
		
		$config = $this->getServiceLocator()->get('Config');
		
		//Get latest conversation from db
		$fbconversation = self::$_FacebookConversationMapper->getLatestConversation($config['facebook']['pageId']);
		if ($fbconversation == null){
			$fbconversation = new FacebookConversation();
			//we create a new obejct with a random date...
			$fbconversation->updateddate = new \DateTime('2001-01-01T01:00:00+01:00');
		}else{
			$fbconversation->updateddate = new \DateTime($fbconversation->updateddate);
		}
		
		//First we get the converastion ids and updated date
		$offset = 0;
		$updatedConversations = array();
		$data = $this->getConvestaions($session,$config['facebook']['pageId'],$offset);
		
		
		while (count($data) > 0){
			foreach($data as $conversation){
				if (new \DateTime($conversation->updated_time) > $fbconversation->updateddate){
					if (!isset($updatedConversations[$conversation->id])){
						$updatedConversations[$conversation->id] = $conversation->updated_time;
					}
				}else{
					//we should stop here as the other messages are older;
					$data = null;
				}	 
			}
			if((isset($graphArray['paging'])) && ($data != null)){
				$offset++;
				$data = $this->getConvestaions($session,$config['facebook']['pageId'], $offset);
			}else{
				$data = null;
			}
		} 
		
		//Now we get the messages for every conversation
		$newmessages = array();
		
		
		if(count($updatedConversations)>0){
			foreach ($updatedConversations as $conversationid => $date) {
				$data = $this->getMessages($session,$conversationid);
					foreach($data as $message){
						if (new \DateTime($message->created_time) > $fbconversation->updateddate){
							if($message->from->name != $config['facebook']['pageName']){
								$newmessages[] = array (
											'conversationid' => $conversationid ,
											'message' => $message->message, 
											'time_created' => $message->created_time,
											'facebookid' => $message->from->id);
							}
						} 
					}
			}
		}
		return $newmessages;
	}
	
	/**
	 * Send Messages to the LAL Platform, and stores the messages as they are successfully 
	 * sended.
	 */
	public function sendMessagestoLAL($messages){
		if (self::$_FacebookConversationMapper == null){
			self::$_FacebookConversationMapper = $this->getServiceLocator()->get( 'FacebookHandler\Model\Mapper\FacebookTokenMapper' );
		}
		
		$error = array();
		foreach($messages as $message){
			$config = $this->getServiceLocator()->get('Config');
			$sender = $this->getServiceLocator()->get('Sender Service');
			
			$params = array();
			$params['facebookid']      = $message['facebookid'];
			$params['content']    = $message['message'];
			$params['routetype']  = "facebook";
			$params['messageref'] = $message['conversationid'];
			$params['uri'] 		  = $config['facebookhandler']['uri'];
			
			$jsonResponse = $sender->sendMessage($params,$config['lalapp']['uri']);
			
			$reponse = Json::decode($jsonResponse);
						
			
			if(isset($reponse->Error))
			{
				$error[] = array($message['conversationid'] => $reponse->Error);
			}
			else{
				$fbconv = new FacebookConversation();
				$fbconv->conversationid = $message['conversationid'];
				$fbconv->fbuserid = $message['facebookid'];
				$fbconv->pageid = $config['facebook']['pageId'];
				$fbconv->updateddate = $message['time_created'];
				self::$_FacebookConversationMapper->storeConversation($fbconv);
				//TODO : SEND AKNOWLEGMENT?
			}
		}
		if (count($error) > 0){
			return array('Error' => $error);
		}
		else {
			return array('Success' => 'All the message where processed');
		}
	}
	
	/**
	 * Send the message to facebook and returns something..
	 * @param unknown $data
	 */
	public function sendMessagestoFacebook($session, $data){
		//TODO send message to facebook.
		$request = new FacebookRequest($session, 'POST', '/'.$data['messageref'].'/messages', array('message' => $data['content']));
		$response = $request->execute();
		//TODO PROCESS SOMENTHING HERE...
		return array('Success' => 'message sent succesfully');
	}
	
	/**
	 * Returns array of data with covnersations id and date
	 * @param unknown $offset
	 */
	private function getConvestaions($session, $pageid, $offset){
		$request = new FacebookRequest($session, 'GET', '/'.$pageid.'/conversations?fields=id&offset='.$offset);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$graphArray = $graphObject->asArray();
		return isset($graphArray['data']) ? $graphArray['data'] : null;
	}
	
	/**
	 * Returns array of data with message id,from,date and content
	 * @param unknown $offset
	 */
	private function getMessages($session,$conversationid ){
		$conv_messages = array();
		$request = new FacebookRequest($session, 'GET', '/'.$conversationid.'?fields=messages.fields(id,from,message)');
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$graphArray = $graphObject->asArray();
		$messages = $graphArray['messages'];
		$conv_messages = $messages->data;
		
		while (isset($messages->paging)){
			$next = str_replace("https://graph.facebook.com/v2.0","",$messages->paging->next);
			$request = new FacebookRequest($session, 'GET', $next);
			$response = $request->execute();
			$graphObject = $response->getGraphObject();
			$graphArray = $graphObject->asArray();
			
			if(isset($graphArray['messages'])){
				$messages = $graphArray['messages'];
				$conv_messages = array_merge($conv_messages, $messages->data);
			}else{
				$messages = null;
			}
		}
		return $conv_messages;
	}
}