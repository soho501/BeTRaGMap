<?php
/**
 * MailController
 *
 * @copyright Copyright (c) 2013 Oneworld UK (http://oneworld.org/)
 */

namespace FacebookHandler\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;

class FacebookController extends AbstractActionController
{
	private static $_helper;
    
    public function fbloginAction(){
    	$fs = $this->getServiceLocator()->get('Facebook Service');
    	$config = $this->getServiceLocator()->get('Config');
    	
    	$facebooktoken = $fs->loadToken($config['facebook']['AppId']);
    	if($facebooktoken != null){
    		//check if the current token is valid.
    		$session = new FacebookSession($facebooktoken->token);
    		$session->setDefaultApplication($config['facebook']['AppId'], $config['facebook']['Secret']);
    		$debugInfo = $session->getSessionInfo();
    		if ($debugInfo->isValid()){
    			$viewmodel = new ViewModel();
    			$viewmodel->setVariables(array('message' => chop('The Facebook token is up to date')));
    			return $viewmodel;
    		}
    	}
    	
    	if(self::$_helper == null) $this->_initHelper();
    	
    	$scope = array('read_page_mailboxes','read_mailbox','manage_pages');
    	$loginUrl = self::$_helper->getLoginUrl($scope);
    	
    	$viewmodel = new ViewModel();
	    $viewmodel->setVariables(array('redirect' => $loginUrl));
	    return $viewmodel;
    	
    }
    
    public function fbloginredirectAction(){
    	if(self::$_helper == null) $this->_initHelper();
    	
    	$fs = $this->getServiceLocator()->get('Facebook Service');
    	$message = '';
    	$session = null;
    	$token = null;
    	
    	try {
    		$session = self::$_helper->getSessionFromRedirect();
    	} catch(FacebookRequestException $ex) {
    		$message = $ex->getMessage();
    		$viewmodel = new ViewModel();
    		$viewmodel->setVariables(array('error' => $message));
    		return $viewmodel;
    	} catch(\Exception $ex) {
    		$message = $ex->getMessage();
    		$viewmodel = new ViewModel();
    		$viewmodel->setVariables(array('error' => $message));
    		return $viewmodel;
    	}
    	if ($session) {
    		//exchange for longer token
    		$config = $this->getServiceLocator()->get('Config');
    		$session->setDefaultApplication($config['facebook']['AppId'], $config['facebook']['Secret']);
    		
    		$longSession = $session->getLongLivedSession();
    		
    		//now we need to exchange this token by a page token
    		$request = new FacebookRequest($longSession, 'GET', '/me/accounts');
    		$response = $request->execute();
    		$graphObject = $response->getGraphObject();
    		$data = $graphObject->asArray();
    		
    		foreach ($data['data'] as $page){
    			if (isset($page->name)){
    				if ($page->name == $config['facebook']['pageName']){
    					$token = $page->access_token;
    				}
    			}
    		}
    		
    		if($token == null){
    			$viewmodel = new ViewModel();
    			$viewmodel->setVariables(array('error' => chop("We haven't be able to retrieve a valid token")));
    			return $viewmodel;
    		}
    		
    		$pagesession = new FacebookSession($token);
    		$debugInfo = $pagesession->getSessionInfo();
    		$expireDate = $debugInfo->getExpiresAt();
    		
    		$fs->storeToken($config['facebook']['AppId'],$config['facebook']['pageName'],$token,$expireDate);
    		
    		$message = chop('The Facebook token is up to date');
    		$viewmodel = new ViewModel();
    		$viewmodel->setVariables(array('message' => $message));
    		return $viewmodel;
    	}
    }
    /**
     * fetching mails from a mail server
     */
	public function fetchAction()
	{
		$this->getEventManager()->trigger('log', $this, array(
				'priority' => \Zend\Log\Logger::INFO,
				'message' =>  'start fetching messages from facebook',
		));
		
		$fs = $this->getServiceLocator()->get('Facebook Service');
		$config = $this->getServiceLocator()->get('Config');
		 
		$facebooktoken = $fs->loadToken($config['facebook']['AppId']);
		
		if($facebooktoken == null){
			echo "ERROR : No facebooktoken on the database!";
		}
		else {
			
			$session = new FacebookSession($facebooktoken->token);
			$session->setDefaultApplication($config['facebook']['AppId'], $config['facebook']['Secret']);
			$debugInfo = $session->getSessionInfo();
			if (!$debugInfo->isValid()){
				echo "ERROR : No facebooktoken not valid!!";
			}
			else {
				$messages = $fs->fetchMessages($session);
				$response = $fs->sendMessagestoLAL($messages);
				if (isset($response['Error'])){
					$this->getEventManager()->trigger('log', $this, array(
							'priority' => \Zend\Log\Logger::INFO,
							'message' =>   print_r($response['Error'],true),
					));
					print_r($response['Error']);
				}
				elseif (isset($response['Success'])){
					$this->getEventManager()->trigger('log', $this, array(
							'priority' => \Zend\Log\Logger::INFO,
							'message' =>  $response['Success'],
					));
					echo $response['Success'];
				}
				
			}
		}
	}
	
	private function _initHelper(){
		$request = $this->getRequest();
    	$uri = $request->getUri();
    	$loginredirect = 'http://' . $uri->getHost().'/facebook/fbloginredirect';
    	
    	$config = $this->getServiceLocator()->get('Config');
    	
    	self::$_helper = new FacebookRedirectLoginHelper($loginredirect,$config['facebook']['AppId'],$config['facebook']['Secret']);
	}
	
}