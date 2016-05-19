<?php

//#define ("DB_USER", 'livemusic');
//#define ("DB_PASSWORD", 'music_live');
//#define ("DB_HOST", 'localhost');
define ("DB_USER", 'music_pro');
define ("DB_PASSWORD", '"music_20!6');
define ("DB_HOST", 'musicprolive.csucrfp51gwm.us-west-2.rds.amazonaws.com');
define ("DB_NAME", 'music_pro_live');

class DB_Connect {

	/* data members */

	private $connection;

	private $db_name;

	static $instances = 0;

/*

   // //////////////////////////////////////////////////////////////////

   // constructor

   // //////////////////////////////////////////////////////////////////

*/

	function __construct ($hostname = DB_HOST,

		$username = DB_USER,

		$password = DB_PASSWORD,

		$db_name  = DB_NAME)

	{

		if (self::$instances == 0) {

			$this->connection = mysql_connect ($hostname, $username, $password) or

			die (mysql_error () . " Error no:" . mysql_errno ());



			self::$instances = 0;



			$this->db_name = $db_name;

		} else {

			$msg = "Close the existing instance of the " . "MYSQL";



			die ($msg);

		}

	}

/*

   // //////////////////////////////////////////////////////////////////

   // destructor

   // //////////////////////////////////////////////////////////////////

*/

	function __destruct ()

	{

		$this->close ();

	}

/*

   // //////////////////////////////////////////////////////////////////

   // methods

   // //////////////////////////////////////////////////////////////////

*/

	function create_result ($query)

	{

		$record_set = new Result ($query, $this->db_name, $this->connection);

		return $record_set;

	}



	function get_connection ()

	{

		return $this->connection;

	}



	function get_link_info(){

		return mysql_info ($this->connection);

	}



	function get_version_number ()

	{

		/* mysql_get_server_info */

		return mysql_get_server_info ();

	}



	function close()

	{

		self::$instances = 0;



		if (isset($this->connection)) {

			mysql_close ($this->connection);



			unset ($this->connection);

		}

	}

}


?>
