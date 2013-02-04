<?php

namespace Foo;

class Service {
	private $dao;

	public function __construct(Dao $dao){
		$this->dao = $dao;
	}
	
	public function callDaoGet(){
		return $this->dao->get();
	}

	public function runSql($sql){
		return $this->dao->runSql($sql);
	}

	public function addToTable($table){
		$data = $this->dao->get();
		if($data){
			$this->dao->insert($table, $data);
		}
	}

	public function runMySql(){
		$sql = 'select fail from status';
		$this->dao->runSql($sql);
	}

	public function curlRequest(){

	}

	public function multiplyCurlRequestAnswerBy($times){
		$integer = $this->curlRequest();
		return $times * $integer;
	}
}