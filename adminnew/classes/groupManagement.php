<?php
/**************************************************************************************
Created By 	: Lijesh
Created On	: 19-08-2011
Purpose		: Group Management
**************************************************************************************/
class groupManagement extends siteclass{
	
	function getGroupName($groupId)
		{
			$sql		=	"SELECT group_name from tblgroup where group_id=$groupId";
			$group		=	end($this->getdbcontents_sql($sql));
			$groupName	=	$group['group_name'];
			return $groupName;	
			
		}
		
	function addNewGroup($groupData, $members)
		{
			$insertFlag = 1;
			$this->dbStartTrans();
			$groupID =	$this->db_insert('tblgroup',$groupData, 0);
			if($groupID)
				{	
					if(!empty($members)){
						$membersList = explode(',', $members);					
						foreach($membersList as $memberID)
							{
								if(!empty($memberID)){
									$data = array();
									$data['group_id'] = $groupID;
									$data['user_id'] = $memberID;
									$data['created_on'] = "escape now() escape";
									if(!$this->db_insert('tblgroup_members',$data, 0))
										{
											$insertFlag = 0;
											break;
										}
								}	
							}
					}																	
				}
			else
				$insertFlag = 0;
			if(!$insertFlag)		
				{
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());					
					return false;
				}
			else
				return true;	
		}
	
	public function addNewMember($data)
		{
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tblgroup_members',$data, 0);
			if(!$this->id)
				{						
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());						
					return false;					
				}
			else
				return $this->id;	
		}		
		
	/*function getMyGroups($userID)
		{
			$sql 		=	"SELECT group_name,group_id, DATE_FORMAT(created_on, '%b %d %Y %h:%i %p') AS created_on 
							FROM tblgroup WHERE group_owner_id = $userID";
			$resultArry	=	$this->getdbcontents_sql($sql, 0);	
			return $resultArry;
		}
	
	function getMyFriendsGroups($userID)
		{
			$sql 			= 	"SELECT G.group_name,Member.group_id ,DATE_FORMAT(G.created_on, '%b %d %Y %h:%i %p') AS created_on 
								FROM tblgroup_members AS Member 
								LEFT JOIN tblgroup AS G ON  Member.group_id = G.group_id
								WHERE Member.user_id = $userID";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);	
			return $resultArry;
		}*/
		
	/*
		Get group details with my own.
	*/	
	function getMyGroups($userID)
		{
			$sql 		=	"SELECT group_id, group_name, SUBSTRING(group_description, 1, 100) AS group_description,  
							DATE_FORMAT(created_on, '%b %d %Y %h:%i %p') AS created_on 
							FROM tblgroup WHERE group_owner_id = $userID 
							UNION 
							SELECT Member.group_id, G.group_name, SUBSTRING(G.group_description, 1, 100) AS group_description,  
							DATE_FORMAT(G.created_on, '%b %d %Y %h:%i %p') AS created_on 
							FROM tblgroup_members AS Member 
							LEFT JOIN tblgroup AS G ON  Member.group_id = G.group_id
							WHERE Member.user_id = $userID";
			//echo $sql; exit;				
			//$resultArry	=	$this->getdbcontents_sql($sql, 0);	
			return $sql;
		}	
		
	function getGroupdetails($ID)
		{
			$sql 			= 	"SELECT group_name,group_id, DATE_FORMAT(created_on, '%b %d %Y %h:%i %p') AS created_on 
								FROM tblgroup WHERE group_id = $ID";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);	
			return $resultArry;
		}
		
	function getGroupMembers($groupID,$userId)
		{
			$sql = 	"SELECT G.group_id,G.group_name, G.group_name,group_owner_id, DATE_FORMAT(G.created_on, '%b %d %Y %h:%i %p') 
					AS created_on, Member.user_id, User.first_name, User.last_name, User.profile_image, R.role_access_key 
					FROM tblgroup_members AS Member 
					LEFT JOIN tblgroup AS G ON  Member.group_id = G.group_id
					LEFT JOIN tblusers AS User ON Member.user_id = User.user_id 
					LEFT JOIN tbluser_login as L ON User.`login_id`=L.`login_id`
					LEFT JOIN tbluser_roles as R ON L.`user_role`= R.`role_id`
					WHERE User.is_deleted=0 AND L.is_deleted=0 AND Member.group_id = $groupID AND (L.`user_role` = ".LMT_INS_ROLE_ID." OR L.`user_role` = ".LMT_STUD_ROLE_ID." )
					AND User.first_name IS NOT NULL AND User.user_id !=$userId ORDER BY CONCAT(User.first_name, '', User.last_name) ";
			$resultArry		=	$this->getdbcontents_sql($sql,0);	
			return $resultArry;
		}	
		
	function getFriendsGroup($userID, $friendID)
		{
			$sql = "SELECT Grp.group_name, DATE_FORMAT(Grp.created_on, '%b %d %Y %h:%i %p') AS created_on, Mem.group_id FROM tblgroup AS Grp 
					LEFT JOIN tblgroup_members AS Mem ON Grp.group_id = Mem.group_id AND Mem.user_id = $userID 
					WHERE Grp.group_owner_id = $friendID AND Mem.user_id IS NOT NULL 
					
					UNION 
					SELECT Grp.group_name, DATE_FORMAT(Grp.created_on, '%b %d %Y %h:%i %p') AS created_on, Mem.group_id FROM tblgroup AS Grp 
					LEFT JOIN tblgroup_members AS Mem ON Grp.group_id = Mem.group_id AND Mem.user_id = $friendID 
							WHERE Grp.group_owner_id = $userID AND Mem.user_id IS NOT NULL";
					//$resultArry		=	$this->getdbcontents_sql($sql, 0);	
			return $sql;		
		}
		
	function getGroupMemberID($groupID)
		{
			$sql = "SELECT 	user_id FROM tblgroup_members WHERE group_id = $groupID ";

			$resultArry		=	$this->getdbcontents_sql($sql, 0);	
			return $resultArry;	
		}
	
	function getGroupOwner($groupID, $ownerID)
		{
			$sql = "SELECT 	group_owner_id FROM tblgroup WHERE group_id = $groupID AND group_owner_id = $ownerID";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);	
			if(!empty($resultArry))
				return true;
			else
				return false;	
		}	
		
	function getMyGroupsN($userID)
		{
			$sql 		=	"SELECT group_id, group_name, SUBSTRING(group_description, 1, 100) AS group_description,  
							DATE_FORMAT(created_on, '%b %d %Y %h:%i %p') AS created_on 
							FROM tblgroup WHERE group_owner_id = $userID 
							UNION 
							SELECT Member.group_id, G.group_name, SUBSTRING(G.group_description, 1, 100) AS group_description,  
							DATE_FORMAT(G.created_on, '%b %d %Y %h:%i %p') AS created_on 
							FROM tblgroup_members AS Member 
							LEFT JOIN tblgroup AS G ON  Member.group_id = G.group_id
							WHERE Member.user_id = $userID";
			//echo $sql; exit;				
			$resultArry	=	$this->getdbcontents_sql($sql, 0);	
			return $resultArry;
		}	
				
}
?>