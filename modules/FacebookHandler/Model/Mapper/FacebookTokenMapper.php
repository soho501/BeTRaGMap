<?php 
namespace FacebookHandler\Model\Mapper;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use FacebookHandler\Model\Entity\FacebookToken;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class FacebookTokenMapper extends AbstractTableGateway {
	protected $table = 'facebooktoken';
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
		$this->resultSetPrototype = new HydratingResultSet();
		$this->resultSetPrototype->setObjectPrototype(new FacebookToken());
		$this->initialize();
	}
	
	
	public function storeToken($facebooktoken){
		$values = array();
		$values = $facebooktoken->getArrayCopy($uppercase = true);
		
		if($values['ID'] == null){
			$values['UPDATEDDATE'] = date('Y-m-d H:i:s');
			$this->insert($values);
		}else{
			$values['UPDATEDDATE'] = date('Y-m-d H:i:s');
			$this->update($values,array('id' => $values['ID']));
		}
		
		return $this->getLastInsertValue();
	}
		
	public function loadToken($appid){
		$select = new Select($this->table);
		$select->where(array('APPID = '.$appid));
		$resultSet = $this->selectWith($select);
		
		return $resultSet->current();
	}

}

