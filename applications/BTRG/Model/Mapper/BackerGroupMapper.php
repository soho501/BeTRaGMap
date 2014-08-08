<?php

namespace BTRG\Model\Mapper;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;


class BackerGroupMapper extends AbstractTableGateway {
	protected $table = 'backergroup';

	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	
	public function addBackerGroup($values){
		$this->insert($values);
		return $this->lastInsertValue;
	}
	
	public function getBackersByGroupId($groupid){
		$select = new Select($this->table);
		$select->where(array('GROUPID = '.$groupid));		
	
		$resultSet = $this->selectWith($select);

		if($resultSet->count() < 1){
			return null;
		}
		
		return $resultSet;	

	}
	
	public function getGroupsByBackerId($backerid){
		$select = new Select($this->table);
		$select->where(array('BACKERID = '.$backerid));
	
		$resultSet = $this->selectWith($select);
	
		if($resultSet->count() < 1){
			return null;
		}
	
		return $resultSet;
	
	}
	
	public function getRelationID($groupid,$backerid){
		$select = new Select($this->table);
		$select->where(array('GROUPID ='.$groupid,'BACKERID = '.$backerid));
	
		$resultSet = $this->selectWith($select);
	
		if($resultSet->count() < 1){
			return null;
		}
	
		return $resultSet;
	
	}
	
	public function getAll(){
		$resultSet = $this->select(function (Select $select) {
			$select->order('ID ASC');
		});
		return $resultSet;
	}
}
