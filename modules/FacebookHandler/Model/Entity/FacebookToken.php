<?php
namespace Facebookhandler\Model\Entity;

use \Exception;

class FacebookToken {
	protected $id;
	protected $appid;
	protected $pagename;
	protected $token;
	protected $expiredate;
	protected $updateddate;
	
	public function __construct(array $options = null) {
		if (is_array($options)) {
			$this->populate($options);
		}
	}
	
	public function __set($name, $value) {
		$method = 'set' . $name;
		if (!method_exists($this, $method)) {
			throw new Exception('Invalid Method '.$method);
		}
		$this->$method($value);
	}
	
	public function __get($name) {
		$method = 'get' . $name;
		if (!method_exists($this, $method)) {
			throw new Exception('Invalid Method '.$method);
		}
		return $this->$method();
	}
		
	public function getArrayCopy($uppercase = false){
		if($uppercase === false){
			return get_object_vars($this);
		}else{
			$values = array();
			foreach(get_object_vars($this) as $key => $value){
				$values[strtoupper($key)] = $value;
			}
			return $values;
		}
	}
	
	//the Hydratator will call first this function.
	public function exchangeArray($data)
	{
		foreach($data as $field => $value){
			$attribute = strtolower($field);
			$method = 'get' . ucfirst($attribute);
			if (method_exists($this,$method)){
				$this->$attribute = $value;
			}
		}
	}
	
	public function getId(){
		//Probably we will need to add desencription here
		return $this->id;
	}
	
	public function setId($id){
		//Probably we will need to add encription here
		$this->id = $id;
	}
	
	public function getAppid(){
		return $this->appid;
	}
	
	public function setAppid($appid){
		$this->appid = $appid;
	}
	
	public function getPagename(){
		return $this->pagename;
	}
	
	public function setPagename($pagename){
		$this->pagename = $pagename;
	}
	
	public function getToken(){
		return $this->token;
	}
	
	public function setToken($token){
		$this->token = $token;
	}
	
	public function getExpiredate(){
		return $this->expiredate;
	}
	
	public function setExpiredate($expiredate){
		$this->expiredate = $expiredate;
	}
	
	public function getUpdateddate(){
		return $this->updateddate;
	}
	
	public function setUpdateddate($updateddate){
		$this->updateddate = $updateddate;
	}
}