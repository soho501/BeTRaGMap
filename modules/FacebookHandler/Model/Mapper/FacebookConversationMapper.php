<?php 
namespace FacebookHandler\Model\Mapper;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use FacebookHandler\Model\Entity\FacebookConversation;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class FacebookConversationMapper extends AbstractTableGateway {
	protected $table = 'facebookconversation';
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
		$this->resultSetPrototype = new HydratingResultSet();
		$this->resultSetPrototype->setObjectPrototype(new FacebookConversation());
		$this->initialize();
	}
	
	
	public function storeConversation($facebookConversation){
		$values = array();
		$values = $facebookConversation->getArrayCopy($uppercase = true);
		
		$select = new Select();
		$select->from($this->table)
				->where('CONVERSATIONID = "'.$values['CONVERSATIONID'].'"');
		$resultSet = $this->selectWith($select);
		if($resultSet->count() > 0){
			$this->update($values,array('CONVERSATIONID' => $values['CONVERSATIONID']));
		}
		else{
			$this->insert($values);
		}
		
		return $this->getLastInsertValue();
	}
		
	public function getLatestConversation($pageid){
		$select = new Select($this->table);
		$select->order('UPDATEDDATE DESC');
		$resultSet = $this->selectWith($select);
		
		return $resultSet->current();
	}

}

