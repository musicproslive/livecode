<?php

require 'DbAdapter.php';

/**
 * Wrapper class for handling PHP5 built in mysqli functions. The 
 * constructor will handle the connection to the database using 
 * the values set in the ini file.  The $_dbConnection property
 * will contain the mysqli object.  Results from queries will be 
 * stored in the $_dbResult property.  Any errors received will 
 * be stored in the $_dbError property.  Although already contained
 * in the mysqli object, the methods GetInsertId and GetAffectedRows
 * were included for quick access to those properties.  For SELECT
 * statements, GetAffectedRows will function the same as the mysqli 
 * method mysqli_num_rows. 
 * 
 * @author David Mans, mans.david@gmail.com
 *
 */
class MysqlAdapter extends DbAdapter 
{
	/**
	 * @var mysqli
	 */
	protected $_dbConnection;
	
	/**
	 * @var string
	 */
	protected $_dbError;
	
	/**
	 * @var mysqli_result
	 */
	protected $_dbResult;
	
	/**
	 * Basic constructor for connecting to the database and setting up the 
	 * mysqli object.  If an error is set durring the connect method, an
	 * exception is thrown and caught by the exception handler.  For sake 
	 * of consistency, the database character set is established.
	 * 
	 * @throws Exception
	 */
	function __construct() 
	{
		//Since we aren't using an ini file, we define the connection constants here
		define('DB_HOST', "musicprolive.csucrfp51gwm.us-west-2.rds.amazonaws.com");//"lmt-rds-dev.cevrjnwyogc1.us-east-1.rds.amazonaws.com");
		define('DB_USER',  "music_pro");//"lmtdev-server");
		define('DB_PASS',  "music_20!6");//"Asdfg1234%^&*");
		define('DB_NAME', "music_pro_live");
		define('DEFAULT_CHARSET', "utf8");
		
		/*define('DB_HOST', "lmt-rds-prod.czqzpdk3xide.us-east-1.rds.amazonaws.com");
		define('DB_USER', "lmt_rds_prod");
		define('DB_PASS', "W23p82Pm76R9C5Q");
		define('DB_NAME', "livemusic_live");
		define('DEFAULT_CHARSET', "utf8");*/
		
		//Execute the db connection
		$this->Connect();

		//Verify if there was an error during connection
		if($this->_dbConnection->connect_error)
			throw new Exception($this->_dbConnection->connect_error);
			
		//Set the default database character set.  This will be important for escaping strings	
		$this->_dbConnection->set_charset(DEFAULT_CHARSET);

	}
	
	/**
	 * Getter method for the $_dbConnection property.
	 * 
	 * @return mysqli $_dbConnection
	 */
	public function GetDbConnection()
	{
		return $this->_dbConnection;
	}
	
	/**
	 * Getter method for the $_dbError property.
	 * 
	 * @return string $_dbError
	 */
	public function GetDbError()
	{
		return $this->_dbError;
	}
	
	/**
	 * Getter method for the $_dbResult property.
	 * 
	 * @return mysqli_result $_dbResult
	 */
	public function GetDbResult()
	{
		return $this->_dbResult;
	}
	
	/**
	 * Getter method for the insert_id property of the mysqli object.  This is only 
	 * applicable if the SQL statement was an INSERT or UPDATE and the id column 
	 * must have an AUTO_INCREMENT attribute. Otherwise, the value return will be zero.
	 */
	public function GetInsertId()
	{
		return $this->_dbConnection->insert_id;
	}
	
	/**
	 * Getter method for the affected_rows property of the mysqli object.
	 */
	public function GetAffectedRows()
	{
		return $this->_dbConnection->affected_rows;
	}

	/**
	 * Basic mysqli connection method.  The constants are derived from the
	 * application ini file.
	 */
	protected function Connect() 
	{
		//Connect to the database and set the mysqli object
		$this->_dbConnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	}

	/**
	 * Method for executing queries that return a data set (SELECT, SHOW, DESCRIBE, EXPLAIN)
	 * a mysqli_result object will be returned.  Once the result has been fetched
	 * it will be stored in the $_dbResult property.  The result is then parsed into an 
	 * array and returned.  This negates a while loop for reading and manipulating the data.
	 * To interact directly with the mysqli_result object.  Use the getter method GetDbResult.
	 * 
	 * @param string $sql
	 * @param bool $fetchArray
	 * @return array $response
	 */
	public function ExecuteQuery($sql) 
	{
		//Ensure an empty string wasn't passed
		if(is_string($sql) && strlen($sql) > 0)
		{
			//Execute the query
			$query = $this->_dbConnection->query($sql);
			
			//Verify that the result is a mysqli_result object
			if($query instanceof mysqli_result)
			{
				//Assign the result to the property
				$this->_dbResult = $query;
				
				//Set the return array
				$response = array();
				
				//Loop through the result
				while($row = $query->fetch_assoc())
				{
					//Assign the row to the array
					array_push($response, $row);
				}
				
				//Return the array
				return $response;
				
			}
			else 
			{
				//Set the error message
				$this->_dbError = $this->_dbConnection->error;
				
				//Keep the result property null
				$this->_dbResult = null;
			}
		}
		else
		{	
			//Throw an exception if an invalid parameter is passed
			throw new Exception("Invalid query parameter supplied");
		}

	}

	/**
	 * Method for executing SQL statements to the database (INSERT, UPDATE, REPLACE, etc).
	 * The exepected return from the database will be boolean.  
	 * 
	 * @param string $sql
	 */
	public function ExecuteNonQuery($sql) 
	{
		//Verify the SQL statement variable is indeed a string and not empty
		if(is_string($sql) && strlen($sql) > 0)
		{
			//Execute the sql statement
			$response = $this->_dbConnection->query($sql);
			
			//Verify the response is boolean
			if(is_bool($response))
			{
				return $response;
			}
			else 
			{
				//Set the error message
				$this->_dbError = $this->_dbConnection->error;
				
				return false;
			}
		}
		else 
		{
			//Throw an exception if an invalid parameter is passed
			throw new Exception("Invalid query parameter supplied");
		}

	}

	/**
	 * Method for closing the mysqli database connection.  Executing the
	 * mysqli close method returns a boolean, true for success and false
	 * on failure.  On failure, the error will be assigned to the 
	 * $_dbError property.
	 * 
	 * @return boolean $close
	 */
	public function Close() 
	{
		//Assign and execute the mysqli close method
		$close = $this->_dbConnection->close();
		
		//Check for a false return
		if(!$close)
		{
			//Assign the error
			$this->_dbError = $this->_dbConnection->error;
		}
		
		return $close;
	}
	
	/**
	 * Method for escaping strings to be used in SQL statements.  
	 * 
	 * @example $db = new MysqlAdapter();
	 * 			$var = "string";
	 * 			$sql = sprintf("SELECT * FROM table WHERE field = '%s'", $db->EscapeString($var));
	 * 
	 * @param string $input
	 */
	public function EscapeString($input)
	{
		//Set the escaped string
		$output = $this->_dbConnection->real_escape_string($input);
		
		return $output;
	}
}


?>