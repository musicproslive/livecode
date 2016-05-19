<?php
/**************************************************************************************
Created By 	:Suneesh
Created On	:2010-12-1	
Purpose		:Master Tables
**************************************************************************************/
class eventManagement extends siteclass
	{

/*-----------------------------------LIJ------------------------------------------------------*/
		
		/*
			Get User's event and events in which user is included.
		*/
		function getEventName($eventId)
		{
			$sql		=	"SELECT event_name from tblevents where event_id=$eventId";
			$event		=	end($this->getdbcontents_sql($sql));
			$eventName	=	$event['event_name'];
			return $eventName;
		}
		function getGroupEventName($eventId,$groupId)
		{
			$sql		=	"SELECT event_name from tblgroup_events where event_id =$eventId AND event_group_id=$groupId ";
			$event		=	end($this->getdbcontents_sql($sql));
			$eventName	=	$event['event_name'];
			return $eventName;
		}
		
		function getEventList($userID)
			{
				$sql = "SELECT E.event_id, E.event_owner_id, DATE_FORMAT(E.event_start, '%b %d %Y %h:%i %p') AS event_start, 
						DATE_FORMAT(E.event_end, '%b %d %Y %h:%i %p') AS event_end, E.event_name, E.location, E.event_image, 
						SUBSTRING(E.event_personal_message, 1, 100) AS event_personal_message, 
						DATE_FORMAT(E.created_date, '%b %d %Y %h:%i %p') AS created_date, Member.is_accept, User.first_name, 
						User.last_name, User.profile_image 
						FROM tblevents AS E 
						LEFT JOIN tblevent_members AS Member ON E.event_id = Member.event_id AND Member.user_id = $userID 
						LEFT JOIN tblusers AS User ON E.event_owner_id = User.user_id
						WHERE event_owner_id = $userID 
						UNION
						SELECT E.event_id, E.event_owner_id, DATE_FORMAT(E.event_start, '%b %d %Y %h:%i %p') AS event_start, 
						DATE_FORMAT(E.event_end, '%b %d %Y %h:%i %p') AS event_end, E.event_name, E.location, E.event_image, 
						SUBSTRING(E.event_personal_message, 1, 100) AS event_personal_message, 
						DATE_FORMAT(E.created_date, '%b %d %Y %h:%i %p') AS created_date, Member.is_accept, User.first_name, 
						User.last_name, User.profile_image  
						FROM tblevent_members AS Member 
						LEFT JOIN tblevents AS E ON  Member.event_id = E.event_id
						LEFT JOIN tblusers AS User ON E.event_owner_id = User.user_id
						WHERE Member.user_id = $userID AND Member.is_accept = 2
						ORDER BY event_start ASC"; 
					//$dataArr		=	$this->getdbcontents_sql($sql, 0);
					return $sql;
			}
		function getEventRequestList($userID)
			{
				$sql = "SELECT E.event_id, E.event_owner_id, DATE_FORMAT(E.event_start, '%b %d %Y %h:%i %p') AS event_start, 
						DATE_FORMAT(E.event_end, '%b %d %Y %h:%i %p') AS event_end, E.event_name, E.location, E.event_image, 
						SUBSTRING(E.event_personal_message, 1, 100) AS event_personal_message, 
						DATE_FORMAT(E.created_date, '%b %d %Y %h:%i %p') AS created_date, Member.is_accept, User.first_name,  
						User.last_name, User.profile_image
						FROM tblevent_members AS Member 
						LEFT JOIN tblevents AS E ON  Member.event_id = E.event_id 
						LEFT JOIN tblusers AS User ON E.event_owner_id = User.user_id						  
						WHERE Member.user_id = $userID AND Member.is_accept = 1";
				$dataArr		=	$this->getdbcontents_sql($sql, 0);
				return $dataArr;		
			}	
		function getFriendsEvent($userID, $friendID)
			{
				$sql = "SELECT E.event_id, E.event_owner_id, DATE_FORMAT(E.event_start, '%b %d %Y %h:%i %p') AS event_start, 
						DATE_FORMAT(E.event_end, '%b %d %Y %h:%i %p') AS event_end, E.event_name, E.location, E.event_image, 
						SUBSTRING(E.event_personal_message, 1, 100) AS event_personal_message, 
						DATE_FORMAT(E.created_date, '%b %d %Y %h:%i %p') AS created_date, User.first_name,  
						User.last_name, User.profile_image  
						FROM tblevents AS E
						LEFT JOIN tblevent_members AS Mem ON E.event_id = Mem.event_id AND Mem.user_id = $userID 
						LEFT JOIN tblusers AS User ON E.event_owner_id = User.user_id 
						WHERE E.event_owner_id = $friendID AND Mem.user_id IS NOT NULL 					
						UNION 
						SELECT E.event_id, E.event_owner_id, DATE_FORMAT(E.event_start, '%b %d %Y %h:%i %p') AS event_start, 
						DATE_FORMAT(E.event_end, '%b %d %Y %h:%i %p') AS event_end, E.event_name, E.location, E.event_image, 
						SUBSTRING(E.event_personal_message, 1, 100) AS event_personal_message, 
						DATE_FORMAT(E.created_date, '%b %d %Y %h:%i %p') AS created_date, User.first_name,  
						User.last_name, User.profile_image 
						FROM tblevents AS E 
						LEFT JOIN tblevent_members AS Mem ON E.event_id = Mem.event_id AND Mem.user_id = $friendID 
						LEFT JOIN tblusers AS User ON E.event_owner_id = User.user_id
						WHERE E.event_owner_id = $userID AND Mem.user_id IS NOT NULL ORDER BY event_start ASC";
				//$resultArry		=	$this->getdbcontents_sql($sql, 0);
				return $sql;		
			}
		
		
		function getGroupEventList($userID, $groupID)
			{
				$sql = "SELECT E.event_id, E.event_group_id, E.event_owner_id, DATE_FORMAT(E.event_start, '%b %d %Y %h:%i %p') AS event_start, 
						DATE_FORMAT(E.event_end, '%b %d %Y %h:%i %p') AS event_end, E.event_name, E.location, E.event_image, 
						SUBSTRING(E.event_personal_message, 1, 100) AS event_personal_message, 
						DATE_FORMAT(E.created_date, '%b %d %Y %h:%i %p') AS created_date, Member.is_accept, User.first_name, 
						User.last_name, User.profile_image  
						FROM tblgroup_events AS E 
						LEFT JOIN tblgroup_event_members AS Member ON E.event_id = Member.event_id AND Member.user_id = $userID 
						LEFT JOIN tblusers AS User ON E.event_owner_id = User.user_id
						WHERE event_owner_id = $userID AND event_group_id = $groupID 
						UNION
						SELECT E.event_id, E.event_group_id, E.event_owner_id, DATE_FORMAT(E.event_start, '%b %d %Y %h:%i %p') AS event_start, 
						DATE_FORMAT(E.event_end, '%b %d %Y %h:%i %p') AS event_end, E.event_name, E.location, E.event_image, 
						SUBSTRING(E.event_personal_message, 1, 100) AS event_personal_message, 
						DATE_FORMAT(E.created_date, '%b %d %Y %h:%i %p') AS created_date, Member.is_accept, User.first_name, 
						User.last_name, User.profile_image 
						FROM tblgroup_event_members AS Member 
						LEFT JOIN tblgroup_events AS E ON  Member.event_id = E.event_id
						LEFT JOIN tblusers AS User ON E.event_owner_id = User.user_id
						WHERE E.event_group_id = $groupID AND Member.user_id = $userID AND (Member.is_accept = 1 OR Member.is_accept = 2) 
						ORDER BY event_start ASC "; 
				//$dataArr =	$this->getdbcontents_sql($sql, 0);
				return $sql;
			}
			
		function addNewEvent($eventData, $memberList)
			{
				$insertFlag = 1;				
				$this->dbStartTrans();	
				//$this->print_r($loginData);			
				$eventID =	$this->db_insert('tblevents',$eventData);
				if($eventID)
					{
						$eventMem['event_id'] = $eventID;
						$eventMem['created_on'] = "escape now() escape";
						$memberList = explode(',', $memberList);
						foreach($memberList as $memberID)
							{
								$eventMem['user_id'] = $memberID;								
								if(!$this->db_insert('tblevent_members',$eventMem))
									{
										$insertFlag = 0;
										break;
									}
							}
							
							
					}	
					else{
							$insertFlag = 0;
						}									
					if(!$insertFlag) 
						{
							$this->dbRollBack();
							$this->setPageError($this->getDbErrors());
							return false;
						}
					else	return $eventID;
			}
			
			function addNewGroupEvent($eventData, $memberList)
			{
				$insertFlag = 1;				
				$this->dbStartTrans();	
				//$this->print_r($loginData);			
				$eventID =	$this->db_insert('tblgroup_events',$eventData);
				if($eventID)
					{
						$eventMem['event_id'] = $eventID;
						$eventMem['created_on'] = "escape now() escape";
						$memberList = explode(',', $memberList);
						foreach($memberList as $memberID)
							{
								$eventMem['user_id'] = $memberID;								
								if(!$this->db_insert('tblgroup_event_members',$eventMem))
									{
										$insertFlag = 0;
										break;
									}
							}
							
							
					}	
					else{
							$insertFlag = 0;
						}									
					if(!$insertFlag) 
						{
							$this->dbRollBack();
							$this->setPageError($this->getDbErrors());
							return false;
						}
					else	return $eventID;
			}
		
		function updateEvent($data, $frindsList, $prvFrndList)
			{
				if($this->db_update("tblevents",$data,"event_id=".$data['event_id'], 1))
					{
						$prvFrndList = explode(',', $prvFrndList);
						$frindsList = explode(',', $frindsList);
						$insertList = array_diff($frindsList, $prvFrndList);
						//print_r($insertList);exit;
						$insertFlag = 1;
						if(!empty($insertList))
							{
								$eventMem['event_id'] = $data['event_id'];
								$eventMem['created_on'] = "escape now() escape";
								foreach($insertList as $memberID)
									{
										$eventMem['user_id'] = $memberID;								
										if(!$this->db_insert('tblevent_members',$eventMem, 1))
											{
												$insertFlag = 0;
												break;
											}
									}
						}	
						if($insertFlag)
							{
								$deleteList = array_diff($prvFrndList, $frindsList);
								$deleteList = implode(',', $deleteList);
								if(!empty($deleteList))
									if(!$this->dbDelete_cond('tblevent_members', "event_id = ".$data['event_id']." AND user_id IN($deleteList)", 0))
										$insertFlag = 0;
							}	
						if(!$insertFlag) 
						{
							$this->dbRollBack();
							$this->setPageError($this->getDbErrors());
							return false;
						}	
					}
				return true;	
			}
		
		function updateGroupEvent($data, $frindsList, $prvFrndList)
			{
				if($this->db_update("tblgroup_events",$data,"event_id=".$data['event_id'], 1))
					{
						$prvFrndList = explode(',', $prvFrndList);
						$frindsList = explode(',', $frindsList);
						$insertList = array_diff($frindsList, $prvFrndList);
						//print_r($insertList);exit;
						$insertFlag = 1;
						if(!empty($insertList))
							{
								$eventMem['event_id'] = $data['event_id'];
								$eventMem['created_on'] = "escape now() escape";
								foreach($insertList as $memberID)
									{
										$eventMem['user_id'] = $memberID;								
										if(!$this->db_insert('tblgroup_event_members',$eventMem, 0))
											{
												$insertFlag = 0;
												break;
											}
									}
						}	
						if($insertFlag)
							{
								$deleteList = array_diff($prvFrndList, $frindsList);
								$deleteList = implode(',', $deleteList);
								if(!empty($deleteList))
									if(!$this->dbDelete_cond('tblgroup_event_members', "event_id = ".$data['event_id']." AND user_id IN($deleteList)", 0))
										$insertFlag = 0;
							}	
						if(!$insertFlag) 
						{
							$this->dbRollBack();
							$this->setPageError($this->getDbErrors());
							return false;
						}	
					}
				return true;	
			}	
		
		function inviteGroupEventMembers($eventID, $userIDList)
			{
				$insertFlag = 1;
				$eventMem['event_id'] = $eventID;
				$eventMem['created_on'] = "escape now() escape";
				$userIDList = explode(',', $userIDList);				
				foreach($userIDList as $memberID)
					{
						$eventMem['user_id'] = $memberID;								
						if(!$this->db_insert('tblgroup_event_members',$eventMem, 0))
							{
								$insertFlag = 0;
								break;
							}
					}
				if(!$insertFlag) 
					{
						$this->dbRollBack();
						$this->setPageError($this->getDbErrors());
						return false;
					}
				return true;		
			}
		function inviteEventMembers($eventID, $userIDList)
			{
				$insertFlag = 1;
				$eventMem['event_id'] = $eventID;
				$eventMem['created_on'] = "escape now() escape";
				$userIDList = explode(',', $userIDList);				
				foreach($userIDList as $memberID)
					{
						$eventMem['user_id'] = $memberID;								
						if(!$this->db_insert('tblevent_members',$eventMem, 0))
							{
								$insertFlag = 0;
								break;
							}
					}
				if(!$insertFlag) 
					{
						$this->dbRollBack();
						$this->setPageError($this->getDbErrors());
						return false;
					}
				return true;		
			}	
		
		function getEventMembers($eventID)
		{
			$sql = 	"SELECT Member.user_id, User.first_name, User.last_name, User.profile_image, Role.role_access_key
					FROM tblgroup_event_members AS Member 
					LEFT JOIN tblgroup_events AS E ON  Member.event_id = E.event_id
					LEFT JOIN tblusers AS User ON Member.user_id = User.user_id
					LEFT JOIN tbluser_category AS UserCategory ON User.user_category_id = UserCategory.category_id AND 
					UserCategory.is_deleted = 0 
					LEFT JOIN tbluser_roles AS Role ON UserCategory.role_id  = Role.role_id
					WHERE Member.event_id = $eventID";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);	
			return $resultArry;
		}
		
		function getEventMembersID($eventID)
			{
				$sql = 	"SELECT user_id FROM tblevent_members WHERE event_id = $eventID";
				$resultArry		=	$this->getdbcontents_sql($sql, 0);	
				$return = array();
				if(!empty($resultArry))
					{
						foreach($resultArry AS $userID)
							{
								$return [] = $userID['user_id'];
							}
					}
				return $return;
			}
			
		function getGroupEventMembersID($eventID)
			{
				$sql = 	"SELECT user_id FROM tblgroup_event_members WHERE event_id = $eventID";
				$resultArry		=	$this->getdbcontents_sql($sql, 0);	
				$return = array();
				if(!empty($resultArry))
					{
						foreach($resultArry AS $userID)
							{
								$return [] = $userID['user_id'];
							}
					}
				return $return;
			}			
/*-----------------------------------JIL------------------------------------------------------*/													
		
		public function getAllDetails($userID)
			{	
			    $sql			=	"SELECT event_id, event_owner_id, DATE_FORMAT(event_start, '%b %d %Y %h:%i %p') AS event_start, DATE_FORMAT(event_end, '%b %d %Y %h:%i %p') AS event_end, event_name, location, detailed_address, event_image, event_personal_message, DATE_FORMAT(created_date, '%b %d %Y %h:%i %p') AS created_date FROM tblevents WHERE event_owner_id = $userID order by created_date desc"; 
				$dataArr		=	$this->getdbcontents_sql($sql, 0);
				return $dataArr;
			}
							
		public function getAllDetailsGroup($groupID = 0)
			{	
			    $sql			=	"SELECT * FROM tblgroup_events WHERE event_group_id=$groupID ORDER BY created_date DESC"; 
				$dataArr		=	$this->getdbcontents_sql($sql);
				return $dataArr;
			}
		public function getEventByID($eventID = 0)
			{
			    $sql			=	"SELECT event_id, event_owner_id, DATE_FORMAT(event_start, '%b %d %Y %h:%i %p') AS event_start, DATE_FORMAT(event_end, '%b %d %Y %h:%i %p') AS event_end, event_name, location, detailed_address, event_image, event_personal_message, created_date  FROM tblevents where event_id=$eventID"; 
				$dataArr		=	$this->getdbcontents_sql($sql);
				return $dataArr;
			}
		public function getGroupEventByID($groupID = 0)
			{
			    $sql			=	"SELECT * FROM tblgroup_events where event_id=$groupID"; 
				$dataArr		=	$this->getdbcontents_sql($sql, 0);
				return $dataArr;
			}
		public function getAllprivacy()
			{
			    $sql			=	"SELECT * FROM tbluser_privacy"; 
				$dataArr		=	$this->getdbcontents_sql($sql);
				return $dataArr;
			}
		public function deleteEvent($args)
			{
				$deleteFlag = 1;				
				$this->dbStartTrans();							
				if($this->dbDelete_cond('tblevents',"event_id = '$args'", 1))
					{	
						if(!$this->dbDelete_cond('tblevent_members',"event_id = '$args'", 1))
							$deleteFlag = 0;
					}
				else
					{
						$deleteFlag = 0;
					}												
				if(!$deleteFlag) 
					{
						$this->dbRollBack();
						$this->setPageError($this->getDbErrors());
						return false;
					}
				return true;
			}	
		public function deleteGroupEvent($eventID = 0)
			{
			    $sql			=	"DELETE  FROM tblgroup_events where event_id=$eventID"; 
				$dataArr		=	$this->getdbcontents_sql($sql, 1);
				return $dataArr;
			}		
	}
?>