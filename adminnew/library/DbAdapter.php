<?php


/**
 * @author David Mans, mans.david@gmail.com
 *
 *
 */
abstract class DbAdapter  
{
	abstract protected function Connect();
	
	abstract public function ExecuteQuery($sql);
	
	abstract public function ExecuteNonQuery($sql);
	
	abstract public function Close();
}


?>