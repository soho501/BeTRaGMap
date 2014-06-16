<?php

namespace FacebookHandler\Model\Service;

use \Exception;
use \MessageIO\Model\Service\MessageIOInterface;


class MessageService extends \Common\Model\ServiceLocatorAware implements \MessageIO\Model\Service\MessageIOInterface
{
	/**
	 * Method that process the messages.
	 * @see \MessageIO\Model\Service\MessageIOInterface::processMessage()
	 */
	public function processMessage($data, $messageType = null){
							
		$config = $this->getServiceLocator()->get('Config');
		return true;
	}
}