<?php

namespace app\database;

use Exception;
use mysqli;

/**
 * Class Connection
 */
class Connection {
	
	public $servername = "laradock_mysql_1";
	public $database = "myCar";
	public $username = "root";
	public $password = "root";
	
	private $connect = null;

    /**
     * get mysql database
     *
     * @return mysqli
     * @throws Exception
     */
	function getConnection(){

		$this->connect = new mysqli(
		    $this->servername,
            $this->username,
            $this->password,
            $this->database
        );
		
		if (empty($this->connect) || ($this->connect->connect_errno)){
		    throw new Exception( 'Connection Failed, '. $this->connect->connect_errno.' --Msg:'.mysqli_connect_error() );
		}

		return $this->connect;
	}

    /**
     * close database
     *
     * @return bool
     */
	function closeConnection(){
		return isset($this->connect) && mysqli_close($this->connect);
	}
}
