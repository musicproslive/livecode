<?php
/**************************************************************************************
Created By 	:Lijesh
Created On	:05-07-2011
Purpose		:TimeZone Management
**************************************************************************************/

class timeZone extends siteclass
	{
		
		public function getAllTimeZone($args="1")
			{	
				$sql		=	"select id, Concat(timezone_location, gmt) as timezone from tbltime_zones where is_active = 1"; 
				$result		=	$this->getdbcontents_sql($sql);				
				return $result;
			}
		
			public function getAllTimeFormat($args="1"){
				$sql		=	"select id, DATE_FORMAT(now(), CONCAT(mysql_date_format, ' ', mysql_time_format)) as dateFormat from tbllookup_user_timestamp"; 
				$result		=	$this->getdbcontents_sql($sql);				
				return $result;
			}
			
		  function getTimeFormat($id)
		  	{
				$sql		=	"SELECT * FROM tbllookup_user_timestamp WHERE id = $id"; 
				$result		=	$this->getdbcontents_sql($sql);				
				return $result;
			}
			
		public function getTimeZone($id)
			{	
				$sql		=	"select id, Concat(timezone_location, gmt) as timezone, SUBSTRING(gmt,6,5) AS gmt, SUBSTRING(gmt,5,1) AS sign, prev_val from tbltime_zones where id = $id"; 
				$result		=	$this->getdbcontents_sql($sql);				
				return $result;
			}
			
		public function offset2num($offset) 
			{ 
				$n = (int)$offset; 
				$m = $n%100; 
				$h = ($n-$m)/100; 
				return $h*3600+$m*60; 
			} 	
		
		public function getMyLocalDatetime($serverOffset, $userOffset, $time = 0)
			{
				$serverOffset = str_replace(":", "", $serverOffset);
				$userOffset = str_replace(":", "", $userOffset);	
				//echo $userOffset	;				
				if($time){
					$local = strtotime($time);
					//echo $local;
				}	
				else	
					$local = time(); // Server Time
				$gmt = $local-$this->offset2num($serverOffset);  // Server To GMT
				$remote = $gmt+$this->offset2num($userOffset); // GMT to Local time
				return $remote;
			}
		
		/*
			Convert time from one TZ offset to another.
		*/
		public function convertTime($fromOffset, $toOffset, $time = 0)
			{
				$fromOffset = str_replace(":", "", $fromOffset);
				$toOffset = str_replace(":", "", $toOffset);	
				//echo $toOffset	;				
				if($time){
					$local = strtotime($time);
					//echo $local;
				}	
				else	
					$local = time(); // Server Time
				$gmt = $local-$this->offset2num($fromOffset);  // Server To GMT
				$remote = $gmt+$this->offset2num($toOffset); // GMT to Local time
				return $remote;
			}	
			
		/*
			Get User's time zone offset with out ':' symbol
		*/	
		public function getMyTimeZoneOff($userID)
			{
				$sql = "SELECT User.time_zone_id, REPLACE(SUBSTRING(TimeZone.gmt,5,6),':','') AS gmt FROM tblusers AS User 
						LEFT JOIN tbltime_zones AS TimeZone ON User.time_zone_id = TimeZone.id 
						WHERE User.user_id = $userID AND TimeZone.offset != 0";
				$result		=	$this->getdbcontents_sql($sql, false);				
				return $result;		
			}
			
		/*
			Get User's time zone offset with ':' symbol
		*/	
		public function getMyTimeZoneOffset($userID)
			{
				$sql = "SELECT SUBSTRING(TimeZone.gmt,5,6) AS gmt FROM tblusers AS User 
						LEFT JOIN tbltime_zones AS TimeZone ON User.time_zone_id = TimeZone.id 
						WHERE User.user_id = $userID AND TimeZone.offset != 0";
				$result		=	$this->getdbcontents_sql($sql, false);				
				return $result;		
			}	
			
		public function getTimeZoneLocation($userID)
			{
				$sql = "SELECT User.time_zone_id, TimeZone.timezone_location, SUBSTRING(TimeZone.gmt,5,6) AS gmt FROM tblusers AS User 
						LEFT JOIN tbltime_zones AS TimeZone ON User.time_zone_id = TimeZone.id 
						WHERE User.user_id = $userID";
				$result		=	$this->getdbcontents_sql($sql, false);				
				return $result;		
			}	
	}
?>
