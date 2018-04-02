<?php

class Connect {

	private $userdb	= "root";
	private $passdb = "theReal@dmin85!";
	private $hostdb = "localhost";
	private $namedb = "krisp";
	
	public function __construct(){
		
	}
	
	public function conecta(){
		$conecta = new PDO("mysql:host=".$this->hostdb.";dbname=".$this->namedb."", "".$this->userdb."", "".$this->passdb."");
		$conecta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $conecta;
		
	}

	public function executeSQL($query) {
		$result = $this->conecta()->query($query);
		return $result;
	}
	
}