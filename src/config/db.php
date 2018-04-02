<?php

class db{

    //connection properties
    private $dbuser = 'root';
    private $dbpass = 'theReal@dmin85!';
    private $dbhost ='localhost';
    private $dbname = 'krisp';

    //connect to database
    public function connect(){
        //create new connection object
        $dbConnStr = "dbname=$this->dbname;mysql:host=$this->dbhost";
        $dbConn = new PDO($dbConnStr, $this->dbuser, $this->dbpass);
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return dbConn;
    }
}