<?php

namespace BTRG\Model\Mapper;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;


class CategoryMapper extends AbstractTableGateway {
	protected $table = 'category';

	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	
	public function addCategroy($values){
		$this->insert($values);
		return $this->lastInsertValue;
	}
	
	public function getCategoryById($id){
		$select = new Select($this->table);
		$select->where(array('ID = '.$id));		
	
		$resultSet = $this->selectWith($select);

		if($resultSet->count() < 1){
			return null;
		}
		
		return $resultSet->current();	

	}
	
	public function getCategoryByName($name){
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
}
