<?php

namespace BTRG\Model\Mapper;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;


class CategoryGroupMapper extends AbstractTableGateway {
	protected $table = 'categorygroup';

	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	
	public function addCategoryGroup($values){
		$this->insert($values);
		return $this->lastInsertValue;
	}
	
	public function getCategoryByGroupId($groupid){
		$select = new Select($this->table);
		$select->where(array('GROUPID = '.$groupid));		
	
		$resultSet = $this->selectWith($select);

		if($resultSet->count() < 1){
			return null;
		}
		return $resultSet;	
	}
	
	public function getGroupByCategoryId($categoryid){
		$select = new Select($this->table);
		$select->where(array('CATEGORYID = '.$categoryid));		
	
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
