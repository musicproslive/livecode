<?php
/**************************************************************************************
Created By 	:ARVIND SOMU	
Created On	:05-07-2011
Purpose		:User Management
**************************************************************************************/

class friendsManagement extends siteclass
	{
		function getNewFriendRequest($id){
			$query			=	"SELECT u.user_id, u.first_name, u.last_name, u.profile_image, r.`friend_request_id` from `tblfriends_request` r JOIN `tblusers` u   WHERE r.`friends_id`=$id AND r.`status`=1 AND u.`user_id`=r.`user_id`";
			$resultArry		=	$this->getdbcontents_sql($query, 0);	
			//Notification count purpose by suneesh dont delete
			for($i=0;$i<count($resultArry);$i++)
				{
				 $this->db_update("tblfriends_request",array("viewed"=>1),"friend_request_id=".$resultArry[$i]['friend_request_id'],0);
				}
			return $resultArry;
		}
		
		function approveFriend($mode,$id){
		$query			=	"SELECT * FROM `tblfriends_request` WHERE `friend_request_id`=$id";
		$rec			=	$this->getdbcontents_sql($query,0);	
		$insertFlag = 1;
		
		if($mode==2){
			$this->dbStartTrans();
			if(!$this->db_insert("tblfriends_accepted",array("user_id"=>$rec[0]["user_id"],"friends_id"=>$rec[0]["friends_id"],"accepted_date"=>date("Y-m-d H:i:s")),0))
				$insertFlag = 0;
		}
			if($insertFlag){ 
					if(!$this->db_update("tblfriends_request",array("status"=>$mode),"friend_request_id=$id",0))
						$insertFlag = 0;
						//Mail
						$mail	 =	new mailManagment();
						$subject =  "Friends approval";
						$var["{TPL_URL}"]			=	ROOT_URL;
						$var['{TPL_FRIEND}'] =  $_SESSION['USER_LOGIN']['USER_NAME'];
						$mail->sendMailGeneral(LMT_MAIL_TPL_FRD_ACT,$rec[0]["friends_id"],$rec[0]["user_id"],$subject,$var,'LMT_MAIL_FRND_ACT');
				}
			if(!$insertFlag)
				{
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());
					return false;
				}
			return true;			
		}
		
		function rejectFriendRequest($requestID)
			{
				if($this->dbDelete_cond('tblfriends_request', "friend_request_id = ".$requestID, 0))
					return true;
				else
					return false;
			}
		
		// for getting the frinds list using id 
		
		function getFriendsList($id,$mode){//$mode==1 for tutors and mose 2 for friends 
			$friendsID	=	 $this->getFriendsList_comma($id);
			$query			=	"SELECT u.`first_name`,u.`last_name`,u.`profile_image`,role.`role_name`,u.`user_id` 
										 FROM `tblusers` u 
										 LEFT JOIN `tbluser_login` AS L ON L.login_id = u.login_id 
										 LEFT JOIN `tbluser_roles` As role ON role.role_id = L.user_role
										 WHERE u.`user_id` IN ($friendsID) AND u.`user_id` != $id  AND u.`is_deleted`=0 AND L.is_deleted=0 AND role.admin_group=1 ";					
			if($mode==1){
					$query	.=	" AND role.`role_name`='Tutor' ";
				}
			if($mode==2){
					$query	.=	" AND role.`role_name`='Student' ";
				}
			$query				.= 	" ORDER BY u.first_name ASC";
/*			$rec["splage"]		=	$this->create_paging("n_page",$query,10);
			
			$this->spage		=	$rec["splage"];
			$this->totalCount	=	$rec["splage"]->s_stable_links;
*/			
			$return				=	$this->getdbcontents_sql($query,0);
			return $return;
		}
		
		/*
			Get Friends of friends
			
			$id => visited profile ID
			$userID => Login ID
		*/
		function getFriendsOfFriends($id, $userID){//$mode==1 for tutors and mose 2 for friends 
			$friendsID	=	 $this->getFriendsList_comma($id);
			$query			=	"SELECT u.`first_name`,u.`last_name`,u.`profile_image`,u.`user_id`, FRQT.friend_request_id 
								AS request, FACT.friend_request_id AS accepted FROM `tblusers` u 
								LEFT JOIN tblfriends_request AS FRQT ON (u.user_id = FRQT.user_id AND FRQT.friends_id = $userID) 
								OR (u.user_id = FRQT.friends_id AND  FRQT.user_id = $userID)
								LEFT JOIN tblfriends_accepted AS FACT ON (u.user_id = FACT.user_id AND FACT.friends_id = $userID) 
								OR (u.user_id = FACT.friends_id AND  FACT.user_id = $userID)  
								WHERE u.`user_id` IN ($friendsID) AND u.`user_id` != $id AND u.`is_deleted`=0";			
			$query				.= 	" ORDER BY u.first_name ASC";
			
			$rec["splage"]		=	$this->create_paging("n_page",$query,25);
			
			$this->spage		=	$rec["splage"];
			$this->totalCount	=	$rec["splage"]->s_stable_links;
			
			$return							=	$this->getdbcontents_sql($rec["splage"]->finalSql(), 0);//exit;
			return $return;
		}
		
		function getNotFriendsList($id,$mode,$name)
			{
				$friendsID	=	 $this->getFriendsList_comma($id);
				if(empty($friendsID)) $friendsID = '0';
				$query	=	"SELECT u.`profile_image`,u.`user_id`,u.`first_name`,u.`last_name`,u.`age_group`, u.`social_media` FROM `tblusers` AS u LEFT JOIN `tbluser_login` AS l ON 1 
							LEFT JOIN tbluser_roles AS R ON R.role_id=l.user_role 
							WHERE u.`is_deleted`=0 AND l.is_deleted=0 AND  l.`authorized`=1 AND u.`login_id`=l.`login_id` AND R.`admin_group`=1  
							AND u.`user_id` NOT IN ($friendsID) AND u.`user_id` != $id ";
							//print_r($query);
						
				if($mode==1){	$query	.=	" AND R.role_name = 'Tutor' ";			}
				/*if($mode==2){	$query	.=	" AND R.role_name = 'Student' AND u.`age_group` AND u.`social_media`";		}*/
				if($mode==2){	$query	.=	" AND R.role_name = 'Student'";		}
				if(!empty($name)){	$query	.=	" AND  lower(concat_ws('',u.first_name,u.last_name)) like '$name%'";		}
				
				$query				.= 	" ORDER BY u.first_name ASC";
				$return							=	$this->getdbcontents_sql($query);
				return $return;
				
	}
			
		function getRequestedFriends($id,$userid)
			{
				$query	=	"SELECT `friend_request_id` FROM `tblfriends_request` 
								WHERE status=1 AND (`user_id`=".$userid." AND `friends_id`=$id) 	
												OR ( `friends_id`=".$userid." AND `user_id`=$id )	 ";
							$friends =	end($this->getdbcontents_sql($query, 0));	
					return $friends;
			}
		function getFriendsList_comma($id){
			$query			=	"SELECT `friends_id`,`user_id` FROM `tblfriends_accepted` f WHERE f.`is_blocked` != 1 AND (f.`user_id`=$id OR f.`friends_id`=$id)";
			
			$rec			=	$this->getdbcontents_sql($query, 0);
			$friendsID		=	"";
			for($i=0;$i<count($rec);$i++){	
				if($rec[$i]["user_id"]==$id){	
					$friendsID	.=$rec[$i]["friends_id"].",";
				}else{
					$friendsID	.=$rec[$i]["user_id"].",";
				}
			}
			$friendsID	=	substr($friendsID,0,(strlen($friendsID)-1));	
			return $friendsID;
		}
		
		/*
			Get Friends ID List
			@id: User ID
			Return : Array
		*/
		function getFriendsIDList($id){
			$query			=	"SELECT `friends_id`,`user_id` FROM `tblfriends_accepted` f WHERE  f.`is_blocked`!= 1 AND (f.`user_id`=$id OR f.`friends_id`=$id )";
			
			$rec			=	$this->getdbcontents_sql($query,0);
			$friendsID		=	array();			
			for($i=0;$i<count($rec);$i++){	
				if($rec[$i]["user_id"]==$id){	
					$friendsID []	= $rec[$i]["friends_id"];
				}else{
					$friendsID	[] = $rec[$i]["user_id"];
				}
			}
			return $friendsID;
		}
		
		 function getFriendsEmail_comma($friends){
				
				$query			=	"SELECT l.`user_name`, concat(u.`first_name`,' ',u.`last_name`) AS name FROM `tbluser_login` l JOIN `tblusers` u LEFT JOIN tbluser_roles AS Role ON l.user_role = Role.role_id  WHERE u.`user_id` IN ($friends) AND u.`login_id`=l.`login_id` AND l.is_deleted =0 AND u.is_deleted = 0 AND Role.admin_group=1 AND Role.role_access_key IS NOT NULL";
				$rec			=	$this->getdbcontents_sql($query, 0);
				for($i=0;$i<count($rec);$i++){
					$friendsID		.=	"'".$rec[$i]["name"]."'<".$rec[$i]["user_name"].">,";
				}
				$friendsID	=	substr($friendsID,0,(strlen($friendsID)-1));
				return $friendsID;
			}
	
		 function getFriendsEmail_Json($name){
				
				$query			=	"SELECT u.`user_code` AS id,l.`user_name`, concat(u.`first_name`,' ',u.`last_name`) AS name,u.`profile_image`,u.`age_group`,c.`country_name`,s.`state_name` FROM `tbluser_login` l LEFT JOIN `tblusers` u ON u.`login_id` = l.`login_id` LEFT JOIN tbluser_roles AS Role ON l.user_role = Role.role_id LEFT JOIN  tblcountries c ON c.`country_id` = u.`country_id` LEFT JOIN tblstates s ON s.`state_id` = u.`state_id` WHERE (concat(u.`first_name`,' ',u.`last_name`) like '%$name%' OR l.`user_name` like '%$name%')  AND u.`login_id`=l.`login_id` AND l.is_deleted =0 AND u.social_media=1 AND u.is_deleted = 0 AND Role.admin_group=1 AND Role.role_access_key IS NOT NULL ORDER BY concat(u.`first_name`),l.`user_name` DESC LIMIT 0,15";
				$rec			=	$this->getdbcontents_sql($query, 0);
				
				return json_encode($rec);
			}
	
		 function getFriendsEmail_Json_ById($id){
				
				$query			=	"SELECT u.`user_code` AS id,l.`user_name`, concat(u.`first_name`,' ',u.`last_name`) AS name,u.`profile_image`,c.`country_name`,s.`state_name` FROM `tbluser_login` l LEFT JOIN `tblusers` u ON u.`login_id` = l.`login_id` LEFT JOIN tbluser_roles AS Role ON l.user_role = Role.role_id LEFT JOIN  tblcountries c ON c.`country_id` = u.`country_id` LEFT JOIN tblstates s ON s.`state_id` = u.`state_id` WHERE u.`user_id` = $id";
				$rec			=	end($this->getdbcontents_sql($query,0));
				
				return json_encode($rec);
			}
	
		function getFriends($idList)
			{
				$idList = implode(',', $idList);
				$query =	"SELECT user_id, first_name, last_name, profile_image FROM tblusers WHERE user_id IN($idList)";
				$resultArry		=	$this->getdbcontents_sql($query, 0);	
				return $resultArry;
			}
		function getFriendsSession($idList)
			{
				$idList = implode(',', $idList);
				$query =	"SELECT User.user_id, User.first_name, User.last_name, User.profile_image, Role.role_access_key 
							FROM tblusers AS User 
							LEFT JOIN tbluser_login AS UL ON User.login_id = UL.login_id 		  
							LEFT JOIN tbluser_roles AS Role ON UL.user_role  = Role.role_id	
							WHERE User.user_id IN($idList) AND User.is_deleted = 0 AND UL.is_deleted=0 AND Role.admin_group=1 AND Role.role_access_key IS NOT NULL 
							ORDER BY CONCAT(User.first_name, ' ',User.last_name)";
				//echo $query;exit;
				return $this->getdbcontents_sql($query, 0);				
			}
		function getCountryList(){
			$query			=	"SELECT `country_name`,`country_id` from `tblcountries` ";
			$resultArry		=	$this->getdbcontents_sql($query, 0);	
			return $resultArry;
		}
		
		// for feinds search 
		
		function getFriendsSearch($data,$friends){
			$query			=	"SELECT u.`first_name`,u.`last_name`,u.`profile_image`,cat.`category`,u.`user_id` FROM `tblusers` u JOIN `tbluser_category` cat  WHERE u.`user_id` IN ($friends)  AND u.`is_deleted`=0 AND u.`user_category_id`=cat.`category_id`";
			
			if(!empty($data["name"])){
				$query	.=	"AND  lower(concat_ws('',u.first_name,u.last_name)) like '".$data["name"]."%' ";
			}
			$rec["splage"]			=	$this->create_paging("n_page",$query,25);	
			$this->splash			=	$rec["splage"];	
			
			$resultArry				=	$this->getdbcontents_sql($rec["splage"]->finalSql(),0);
			return $resultArry;
		}
		function getFriendsSearchNew($data,$friends){
					$query			=	"SELECT u.`first_name`,u.`last_name`,u.`profile_image`,role.`role_name`,u.`user_id` 
										 FROM `tblusers` u 
										 LEFT JOIN `tbluser_login` AS L ON L.login_id = U.login_id 
										 LEFT JOIN `tbluser_roles` As role ON role.role_id = L.user_role
										 WHERE u.`user_id` IN ($friends)  AND u.`is_deleted`=0 AND role.admin_group=1 ";
			//echo $query;exit;
			if(!empty($data["name"])){
				$query	.=	"AND  lower(concat(u.first_name,' ',u.last_name)) like '%".$data["name"]."%' ";
			}
			
			if(!empty($data["usertype"]) && ($data["usertype"]=='Student' || $data["usertype"]=='Tutor')){
				$query	.=	"AND role.`role_name` ='".$data['usertype']."'";
			}
			
			$rec["splage"]			=	$this->create_paging("n_page",$query,25);	
			$this->splash			=	$rec["splage"];	
			
			$resultArry				=	$this->getdbcontents_sql($rec["splage"]->finalSql(),0);
			return $resultArry;
		}
	  function getFriendsIdfromSession($data)
	  	{
			foreach($data as $key=>$val)
				{
					$frnds[]	=	$val['user_id'];
				}
			return $frnds;
		}
	 function getFriendsInstruments($id)
		{							
			$userId			=	implode(',', $id);
			$sql			=	"SELECT m.instrument_id, m.name, m.instrument_image, u.`user_id` FROM `tbluser_instruments` u JOIN `tblinstrument_master` m  WHERE u.`instrument_id`=m.`instrument_id` AND  u.`user_id` IN (".$userId.")";
			$result			=	$this->getdbcontents_sql($sql, false);	
			$instruments = array();
			foreach($result as $instrument){
				$instruments[$instrument['user_id']] [] = $instrument['instrument_image'];
			}
			return $instruments;
		}
		
	function getFriendID($userID, $friendID)
		{										
			$sql		=	"SELECT * FROM tblfriends_request WHERE (user_id = $userID AND friends_id = $friendID) OR (user_id = $friendID AND friends_id = $userID)";
			$result			=	$this->getdbcontents_sql($sql, 0);	
			
			return $result;
		}	
	function blockFriends($userID, $friendID, $blocked_by)
		{	
		   $sql			=	"UPDATE tblfriends_accepted SET is_blocked = 1,blocked_by = $blocked_by WHERE (user_id = $userID AND friends_id = $friendID) OR (user_id = $friendID AND friends_id = $userID)";		  
			$result			=	mysql_query($sql);	
			return $result;
		}
	function friendsBlocked($id)
		{	
			//$friendsID	=	 $this->getFriendsList_comma($id);
			$query	    		=	"SELECT u.`profile_image`,u.`user_id`,u.`first_name`,u.`last_name` ,f.`blocked_by`
										FROM `tblusers` u 
										LEFT JOIN tbluser_login AS L ON u.login_id	=	L.login_id 
										JOIN `tblfriends_accepted` AS f ON (u.user_id = f.user_id OR u.user_id = f.friends_id)  
										WHERE (f.user_id = $id OR f.friends_id = $id) AND u.user_id != $id AND f.is_blocked =1 AND u.is_deleted=0 AND L.is_deleted=0 ";
			$query			   .= 	" ORDER BY u.first_name ASC";
			$rec["splage"]		=	$this->create_paging("n_page",$query,25);
			
			$this->spage		=	$rec["splage"];
			$this->totalCount	=	$rec["splage"]->s_stable_links;
			
			$return				=	$this->getdbcontents_sql($rec["splage"]->finalSql(),0);
			/*echo '<pre>';
			print_r($return);exit;*/
			return $return;
		}
	function unblockFriends($userID, $friendID)
		{
		     $sql			=	"UPDATE tblfriends_accepted SET is_blocked = 0 WHERE (user_id = $userID AND friends_id = $friendID) OR (user_id = $friendID AND friends_id = $userID)";
			 $result			=	mysql_query($sql);	
			 return $result;
		}
	
	/*
		Site members details complared with name, country, state, college, occupation, about me, category				
		$userID => Login ID
		$searchKey => key val to search
		$limit => Number of records
	*/	
	function searchPeople($userID, $searchKey = "", $limit = 0)	
		{
			$searchKey = strtolower($searchKey);			
			$sql = "SELECT UR.role_name, USER.first_name,USER.last_name,USER.profile_image,USER.user_id, 
					SUBSTRING(USER.about_me, 1, 105) AS about_me, COUNTRY.country_name, STATE.state_name, 
					ULOGIN.user_group, FREQ.friend_request_id AS request 			 
					FROM tblusers AS USER 	
					LEFT JOIN tbluser_login AS ULOGIN ON USER.login_id = ULOGIN.login_id 
					LEFT JOIN tbluser_roles  AS UR ON ULOGIN.user_role = UR.role_id 				
					LEFT JOIN tblcountries AS COUNTRY ON USER.country_id = COUNTRY.country_id
					LEFT JOIN tblstates AS STATE ON USER.state_id = STATE.state_id
					LEFT JOIN tbluser_employment AS E ON E.user_id = USER.user_id
					LEFT JOIN tblfriends_request AS FREQ ON (USER.user_id = FREQ.user_id AND FREQ.friends_id = $userID) OR 
					(USER.user_id = FREQ.friends_id AND FREQ.user_id = $userID) AND FREQ.status != 3 
					WHERE (LOWER(UR.role_name) LIKE '%$searchKey%' OR 
					LOCATE(LOWER(UR.role_name), '$searchKey') > 0 OR 
					LOWER(concat_ws(' ',USER.first_name,USER.last_name)) LIKE '%$searchKey%' OR 
					LOCATE(LOWER(concat_ws(' ',USER.first_name,USER.last_name)), '$searchKey') > 0 OR 
					LOWER(USER.college) LIKE '%$searchKey%' OR 					
					LOWER(E.employer) LIKE '%$searchKey%' OR 					
					LOWER(E.occupation) LIKE '%$searchKey%' OR 					
					LOWER(COUNTRY.country_name) LIKE '%$searchKey%' OR 
					LOCATE(LOWER(COUNTRY.country_name), '$searchKey') > 0 OR
					LOWER(STATE.state_name) LIKE '%$searchKey%' OR 
					LOCATE(LOWER(STATE.state_name), '$searchKey') > 0 OR
					LOWER(USER.about_me) LIKE '%$searchKey%') AND (
					USER.user_id != $userID AND ULOGIN.user_group = 1 AND USER.is_deleted = 0) 
					GROUP BY User.user_id";
			
			//if(!empty($searchKey)) like '".$data["name"]."%' "
				//$sql .= " LIMIT 0, $limit";	
			$sql .= " ORDER BY CONCAT(USER.first_name, ' ', USER.last_name) ASC";			
			if($limit)
				$sql .= " LIMIT 0, $limit";	
			
						
			$resultArry		=	$this->getdbcontents_sql($sql,0);					
			return $resultArry;
		}
		
		
	/*
		Site course details				
		$userID => Login ID
		$searchKey => key val to search
		$limit => Number of records
	*/	
	function searchCourses($userID, $searchKey = "", $limit = 0)	
		{
			$searchKey = strtolower($searchKey);			
			$sql = "SELECT CS.course_id, CS.title, SUBSTRING(CS.description, 1, 105) AS course_description, 
					USER.first_name, USER.last_name, USER.user_id, 
					INST.instrument_id, INST.instrument_image, FREQ.friend_request_id AS request 	
					FROM tblcourses AS CS 
					LEFT JOIN tblinstrument_master AS INST ON CS.instrument_id = INST.instrument_id
					LEFT JOIN tblusers AS USER ON CS.instructor_id = USER.user_id 
					LEFT JOIN tblcountries AS COUNTRY ON USER.country_id = COUNTRY.country_id
					LEFT JOIN tblfriends_request AS FREQ ON (USER.user_id = FREQ.user_id AND FREQ.friends_id = $userID) OR 
					(USER.user_id = FREQ.friends_id AND FREQ.user_id = $userID) AND FREQ.status != 3 
					WHERE (LOWER(concat_ws(' ',USER.first_name,USER.last_name)) LIKE '%$searchKey%' OR 
					LOCATE(LOWER(concat_ws(' ',USER.first_name,USER.last_name)), '$searchKey') > 0 OR					
					LOWER(CS.title) LIKE '%$searchKey%' OR 
					LOCATE(LOWER(CS.title), '$searchKey') > 0 OR 
					LOWER(CS.description) LIKE '%$searchKey%' OR 					
					LOCATE(LOWER(CS.description), '$searchKey') > 0 OR
					LOWER(COUNTRY.country_name) LIKE '%$searchKey%' OR 
					LOCATE(LOWER(COUNTRY.country_name), '$searchKey') > 0)
					AND ( USER.user_id != $userID)
					GROUP BY CS.course_id";
			
			//if(!empty($searchKey)) like '".$data["name"]."%' "
				//$sql .= " LIMIT 0, $limit";	
			//$sql .= " ORDER BY CONCAT(USER.first_name, ' ', USER.last_name) ASC";			
			if($limit)
				$sql .= " LIMIT 0, $limit";	
			
						
			$resultArry		=	$this->getdbcontents_sql($sql, 0);					
			return $resultArry;
		}
	}
?>
