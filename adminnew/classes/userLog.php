<?php
/**************************************************************************************
Created By 	:	Suneesh
Created On	:	Sep-25-2012
Purpose		:	User Log
**************************************************************************************/

class userLog extends siteclass
	{
		function setUserAction($userID, $logID, $userType, $requestFrom = 1)
			{
				$data['log_id'] 	= $logID;
				$data['user_id'] 	= $userID;
				$data['user_type']	= $userType;
				$data['log_time'] 	= date('Y-m-d H:i:s');
				$data['ip'] 		= $this->getClientIP();	//site class function
				//print_r($data);exit;
				if($_SESSION['sess_admin'] && $_SESSION['USER_LOGIN'])	
						$data['access_type'] = '2';
				else
						$data['access_type'] = '1';
				$this->db_insert('tbluser_log',$data);
			}
		function getClientIP()
			{
				return $_SERVER['REMOTE_ADDR'];
			}	
	}
?>
