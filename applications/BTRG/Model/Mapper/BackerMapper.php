<?php

namespace BTRG\Model\Mapper;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;


class BackerMapper extends AbstractTableGateway {
	protected $table = 'backer';

	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	
	public function addBacker($values){
		$this->insert($values);
		return $this->lastInsertValue;
	}
	
	public function getBackerById($id){
		$select = new Select($this->table);
		$select->where(array('ID = '.$id));		
	
		$resultSet = $this->selectWith($select);

		if($resultSet->count() < 1){
			return null;
		}
		
		return $resultSet->current();	

	}
	
	public function getBackerByName($name){
		$select = new Select($this->table);
		$select->where(array("NAME = '".$name."'"));
	
		$resultSet = $this->selectWith($select);
	
		if($resultSet->count() < 1){
			return null;
		}
		return $resultSet->current();
	}
	
	public function getAll(){
		$resultSet = $this->select(function (Select $select) {
			$select->order('ID ASC');
		});
		return $resultSet;
	}
	
	
	public function deleteAll(){
		$this->delete(array('ID > 0'));
	}
}
