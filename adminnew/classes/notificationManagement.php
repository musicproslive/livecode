<?php
/**************************************************************************************
Created By 	:Suneesh
Created On	:3-09-2011
Purpose		:Notification Management
**************************************************************************************/

class notificationManagement extends siteclass
	{
		public $visibilityLimit ;
		function __construct()
			{
				$this->visibilityLimit	=	0;
			}
//Feed Comment notification starts
		function getFeedCommentNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
					
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];
				//added user's login id	into friends list inorder to get feed owners list
				
				$query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name, count(distinct(c.comment_id)) as count, n.feed_owner_id, c.news_feed_id, c.comment_id, n.comments_viewed, i.profile_image,concat(i.first_name,' ',i.last_name) as commented_name,DATE_FORMAT(CONVERT_TZ(c.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on, c.created_on AS creation_time , max(c.commented_by) 
									 FROM tblnews_feed_comment as c 
									 LEFT JOIN tblnews_feed as n on c.news_feed_id = n.feed_id 
									 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
									 LEFT JOIN tblusers as i ON i.user_id = c.commented_by 
									 LEFT JOIN tblfriends_accepted as f ON ( (f.user_id = u.user_id OR f.friends_id = u.user_id) OR ( f.friends_id = i.user_id OR f.user_id=i.user_id ) ) 
									 WHERE (n.feed_owner_id in ($userid) OR c.commented_by IN($userid)) AND c.commented_by!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."'
									 AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.comments_viewed)='0' AND f.accepted_date <=c.created_on GROUP BY c.news_feed_id ORDER BY c.created_on DESC ";					 									 
				$resultArry		=	$this->getdbcontents_sql($query,0);
				
				if($resultArry)
					{
						$count	=	0;
						foreach($resultArry as $val)
							{
								if($this->visibilityLimit	==	LMT_SITE_USER_PAGE_LIMIT)
									return array_slice($resultArry,'0',$count);
									
								 $val['comments_viewed'] .= ",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];
								 $query				  	  =	 "UPDATE tblnews_feed SET comments_viewed='".$val['comments_viewed']."' WHERE feed_id=".$val['news_feed_id'];
								 $result				  =  mysql_query($query);
								 $this->visibilityLimit++;
								 $count++;
							}	
	
					}
				return $resultArry;
			}	
//Previous feed comment notification starts 

		function getPreviousFeedCommentNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
					
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user's login id	into friends list inorder to get feed owners list

			
						$query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name,count(distinct(c.comment_id)) as count, n.feed_owner_id, c.news_feed_id, c.comment_id, n.comments_viewed, i.profile_image, concat(i.first_name,' ',i.last_name) as commented_name,
											 DATE_FORMAT(CONVERT_TZ(c.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on, c.created_on AS creation_time ,max(c.commented_by) 
											 FROM tblnews_feed_comment as c 
											 LEFT JOIN tblnews_feed as n on c.news_feed_id = n.feed_id 
											 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
											 LEFT JOIN tblusers as i ON i.user_id = c.commented_by 
											 LEFT JOIN tblfriends_accepted as f ON ((f.user_id = u.user_id OR f.friends_id = u.user_id ) OR ( f.friends_id = i.user_id OR f.user_id=i.user_id ) ) 
											 WHERE (n.feed_owner_id in ($userid) OR c.commented_by IN($userid)) AND c.commented_by!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' AND f.accepted_date <=c.created_on GROUP BY c.news_feed_id ORDER BY c.created_on DESC LIMIT 0,3 "; 						 
						$resultArry		=	$this->getdbcontents_sql($query,0);
				return $resultArry;
			}	

//News feed like section starts
		function getFeedLikeNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
					
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user'slogin id	into friends list inorder to get feed owners list

			

				$query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name, count(distinct(l.like_id)) as count, n.feed_owner_id, l.feed_id, l.like_id, n.like_viewed, DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on, l.created_on AS creation_time ,i.profile_image,
									 concat(i.first_name,' ',i.last_name) as liked_name 
									 FROM tbllike_news_feed as l 
									 LEFT JOIN tblnews_feed as n on l.feed_id = n.feed_id  
									 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
									 LEFT JOIN tblusers as i ON i.user_id = l.user_id 
									 LEFT JOIN tblfriends_accepted as f ON ((f.user_id = u.user_id OR f.friends_id = u.user_id) OR ( f.friends_id = l.user_id OR f.user_id=l.user_id ) )
									 WHERE (n.feed_owner_id in ($userid) OR l.user_id in($userid) ) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."'
									 AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.like_viewed)='0' AND f.accepted_date <=l.created_on GROUP BY l.feed_id ORDER BY l.created_on LIMIT 0,3";
									  
				$resultArry		=	$this->getdbcontents_sql($query,0);
				if($resultArry)
					{
						$count	=0;
						foreach($resultArry as $val)
							{
								if($this->visibilityLimit	==	LMT_SITE_USER_PAGE_LIMIT)
									return array_slice($resultArry,'0',$count);
									
								 $val['like_viewed'] .= ",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];
								 $query				  	  =	 "UPDATE tblnews_feed SET like_viewed='".$val['like_viewed']."' WHERE feed_id=".$val['feed_id'];
								 $result				  =  mysql_query($query);
								 $this->visibilityLimit++;
								 $count++;
							}
					}
					
				return $resultArry;
			}
//News feed like section ends

//Previous News feed like section starts
		function getPreviousFeedLikeNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
					
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user'slogin id	into friends list inorder to get feed owners list

						$query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name,count(distinct(l.like_id)) as count, n.feed_owner_id, l.feed_id, l.like_id, n.like_viewed,DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,l.created_on AS creation_time , i.profile_image, concat(i.first_name,' ',i.last_name) as liked_name 
						FROM tbllike_news_feed as l 
						LEFT JOIN tblnews_feed as n on l.feed_id = n.feed_id 
						LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
						LEFT JOIN tblusers as i ON i.user_id = l.user_id 
						LEFT JOIN tblfriends_accepted as f ON ((f.user_id = u.user_id OR f.friends_id = u.user_id) OR ( f.friends_id = l.user_id OR f.user_id=l.user_id ) ) 
						WHERE (n.feed_owner_id in ($userid) OR l.user_id in($userid) ) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' AND f.accepted_date <=l.created_on GROUP BY l.feed_id  ORDER BY l.created_on DESC LIMIT 0,3";					 
						$resultArry		=	$this->getdbcontents_sql($query,0);
				return $resultArry;
			}
//Previous News feed like section ends

//News feed commments like section starts
		function getFeedCommentsLikeNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
					
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user'slogin id	into friends list inorder to get feed owners list


/*				$query			=	"SELECT concat(u.first_name,u.last_name) as comment_owner_name,count(l.comment_id) as count,n.commented_by,l.comment_id,l.like_id,n.like_viewed,
									 DATE_FORMAT(l.created_on, '%b %d %Y') AS created_onFROM tbllike_news_feedcomment as l LEFT JOIN tblnews_feed_comment as n on l.comment_id = n.comment_id 
									 AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.like_viewed)='0' LEFT JOIN tblusers as u ON n.commented_by = u.user_id WHERE n.commented_by in ($userid) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."'GROUP BY l.comment_id "; 
				
*/				
				$query			=	"SELECT concat(u.first_name,' ',u.last_name) as comment_owner_name,count(distinct(l.like_id)) as count,n.commented_by,l.comment_id,l.like_id,n.like_viewed,i.profile_image,
									 concat(i.first_name,' ',i.last_name) as liked_name,DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on, l.created_on AS creation_time 
									 FROM tbllike_news_feedcomment as l 
									 LEFT JOIN tblnews_feed_comment as n on l.comment_id = n.comment_id 
									 LEFT JOIN tblnews_feed AS NF ON NF.feed_id=n.news_feed_id
									 LEFT JOIN tblusers as O on NF.feed_owner_id =  O.user_id
									 LEFT JOIN tblusers as u ON n.commented_by = u.user_id 
									 LEFT JOIN tblusers as i ON i.user_id = l.user_id
									 LEFT JOIN tblfriends_accepted as f ON ((f.user_id=O.user_id OR f.friends_id=O.user_id) OR (f.user_id = u.user_id OR f.friends_id = u.user_id) OR (f.friends_id =i.user_id OR f.user_id=i.user_id) ) 
									 WHERE (NF.feed_owner_id in ($userid) OR n.commented_by in($userid) OR l.user_id in($userid) ) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."'
									 AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.like_viewed)='0' AND f.accepted_date <=l.created_on GROUP BY l.comment_id ORDER BY l.created_on DESC"; 

				$resultArry		=	$this->getdbcontents_sql($query,0);
				if($resultArry)
					{
						$count	=	0;
						foreach($resultArry as $val)
							{
								if($this->visibilityLimit	==	LMT_SITE_USER_PAGE_LIMIT)
								return array_slice($resultArry,'0',$count);
								
								 $val['like_viewed'] .= ",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];
								 $query				  	  =	 "UPDATE tblnews_feed_comment SET like_viewed='".$val['like_viewed']."' WHERE comment_id=".$val['comment_id'];
								 $result				  =  mysql_query($query);
								 $this->visibilityLimit++;
								 $count++;
							}
					}
					
				return $resultArry;
			}
//News feed comments like section ends

//Previous News feed commments like section starts
		function getPreviousFeedCommentsLikeNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
					
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user'slogin id	into friends list inorder to get feed owners list
						$query			=	"SELECT concat(u.first_name,' ',u.last_name) as comment_owner_name,count(distinct(l.like_id)) as count,n.commented_by,l.comment_id,l.like_id,n.like_viewed,i.profile_image,
									 concat(i.first_name,' ',i.last_name) as liked_name,DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on, l.created_on AS creation_time 
									 FROM tbllike_news_feedcomment as l 
									 LEFT JOIN tblnews_feed_comment as n on l.comment_id = n.comment_id 
									 LEFT JOIN tblnews_feed AS NF ON NF.feed_id=n.news_feed_id
									 LEFT JOIN tblusers as O on NF.feed_owner_id =  O.user_id
									 LEFT JOIN tblusers as u ON n.commented_by = u.user_id 
									 LEFT JOIN tblusers as i ON i.user_id = l.user_id
									 LEFT JOIN tblfriends_accepted as f ON ((f.user_id=O.user_id OR f.friends_id=O.user_id) OR (f.user_id = u.user_id OR f.friends_id = u.user_id) OR (f.friends_id =i.user_id OR f.user_id=i.user_id) ) 
									 WHERE (NF.feed_owner_id in ($userid) OR n.commented_by in($userid) OR l.user_id in($userid) ) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' AND f.accepted_date <=l.created_on GROUP BY l.comment_id  ORDER BY l.created_on DESC LIMIT 0,3";
						$resultArry		=	$this->getdbcontents_sql($query,0);
				return $resultArry;
			}
//Previous News feed comments like section ends

//Friends approved notification starts
		function getFriendsApprovedNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
					
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user'slogin id	into friends list inorder to get owners list

				$query			=	"SELECT concat(u.first_name,' ',u.last_name) as friend_name, f.user_id,f.friends_id,DATE_FORMAT(CONVERT_TZ(f.accepted_date, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS accepted_date , f.accepted_date AS creation_time, u.profile_image 
						FROM tblfriends_accepted as f  
						LEFT JOIN tblusers as u ON f.friends_id=u.user_id   
						where f.user_id=$id AND u.user_id !=$id AND notification=0 ORDER BY f.accepted_date DESC" ;
				//echo $query;exit;
				$resultArry		=	$this->getdbcontents_sql($query,0);
				if($resultArry)
					{
						$count 	=	0;
						foreach ($resultArry as $val)
							{
								if($this->visibilityLimit	==	LMT_SITE_USER_PAGE_LIMIT)
								return array_slice($resultArry,'0',$count);
								
								$query	=	"UPDATE tblfriends_accepted SET notification=1 WHERE friends_id=".$val['friends_id']." AND user_id=".$id;
								mysql_query($query);
								$this->visibilityLimit++;
								$count++;
							}
					}

				return $resultArry;
			}
//Friends approved notification ends
//Previous Friends approved notification starts
		function getPreviousFriendsApprovedNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
					
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user'slogin id	into friends list inorder to get owners list
						$query			=	"SELECT concat(u.first_name,' ',u.last_name) as friend_name, f.user_id,f.friends_id,DATE_FORMAT(CONVERT_TZ(f.accepted_date, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS accepted_date,f.accepted_date AS creation_time,u.profile_image 
						FROM tblfriends_accepted as f  
						LEFT JOIN tblusers as u ON  f.friends_id=u.user_id  
						where  f.user_id=$id AND u.user_id !=$id  ORDER BY f.accepted_date DESC LIMIT 0,3" ; 
						$resultArry		=	$this->getdbcontents_sql($query,0);
				
				return $resultArry;
			}
//Previous Friends approved notification ends
//Group feed created notification starts

		function  getGroupFeedNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				
				$obj			=	new groupManagement();
				$myGroup		=	$obj->getMyGroupsN($id);
				//print_r($myGroup);exit;
				$groupIdList	=	array();
				foreach ($myGroup as $group)
					{
						$groupIdList[]	=	$group['group_id'];
						$groupId		=	implode(",",$groupIdList);
						
					}
				
				$query			=	"SELECT distinct  user_id FROM tblgroup_members WHERE group_id in ($groupId) AND user_id!=$id";
				$result			=	$this->getdbcontents_sql($query,0);
				//print_r($result);
				$idList		    = 	array();
				foreach($result as $key)	
					{
						$idList [] 		= 	$key['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
				$query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name,n.feed_owner_id,n.group_feed_id,DATE_FORMAT(CONVERT_TZ(n.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on, n.created_on AS creation_time,g.group_name,n.feed_viewed,u.profile_image
									 FROM tblgroup_news_feed as n 
									 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
									 LEFT JOIN tblgroup as g ON n.group_id=g.group_id 
									 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id 
									 WHERE n.feed_owner_id in ($userid) AND n.group_id IN ($groupId) AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.feed_viewed) ='0'
									 AND GM.created_on <= n.created_on GROUP BY n.group_feed_id ORDER BY n.created_on DESC";
									 //echo $query;exit; 
				$resultArry		=	$this->getdbcontents_sql($query,0);
				if($resultArry)
					{
						$count	=	0;
						foreach($resultArry as $val)
							{
								 if($this->visibilityLimit	==	LMT_SITE_USER_PAGE_LIMIT)
								 return array_slice($resultArry,'0',$count);
								 
								 $val['feed_viewed'] .= ",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];
								 $query				  	  =	 "UPDATE tblgroup_news_feed SET feed_viewed='".$val['feed_viewed']."' WHERE group_feed_id=".$val['group_feed_id'];
								 $result				  =  mysql_query($query);
								 $this->visibilityLimit++;
								 $count++;
							}
					}
				
				return $resultArry;
			}
//Group feed created notification ends

//Previous Group feed created notification starts

		function  getPreviousGroupFeedNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				
				$obj			=	new groupManagement();
				$myGroup		=	$obj->getMyGroupsN($id);
				$groupIdList	=	array();
				foreach ($myGroup as $group)
					{
						$groupIdList[]	=	$group['group_id'];
						$groupId		=	implode(",",$groupIdList);
						
					}
				$query			=	"SELECT distinct  user_id FROM tblgroup_members WHERE group_id in ($groupId) AND user_id!=$id";
				$result			=	$this->getdbcontents_sql($query,0);
				$idList		    = 	array();
				foreach($result as $key)	
					{
						$idList [] 		= 	$key['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
						$query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name,n.feed_owner_id,n.group_feed_id,DATE_FORMAT(CONVERT_TZ(n.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on, n.created_on AS creation_time, g.group_name, n.feed_viewed, u.profile_image
									 FROM tblgroup_news_feed as n 
									 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
									 LEFT JOIN tblgroup as g ON n.group_id=g.group_id 
									 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id 
									 WHERE n.feed_owner_id in ($userid) AND n.group_id IN ($groupId) AND GM.created_on <= n.created_on GROUP BY n.group_feed_id ORDER BY n.created_on DESC LIMIT 0,3"; 									
						$resultArry		=	$this->getdbcontents_sql($query,0);
				return $resultArry;
			}
//Previous Group feed created notification ends

//Group news feed comment notification starts

		function getGroupFeedCommentNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$obj			=	new groupManagement();
				$myGroup		=	$obj->getMyGroupsN($id);
				$groupIdList	=	array();
				foreach ($myGroup as $group)
					{
						$groupIdList[]	=	$group['group_id'];
						$groupId		=	implode(",",$groupIdList);
						
					}
				
				$query			=	"SELECT distinct  user_id FROM tblgroup_members WHERE group_id in ($groupId)";
				$result			=	$this->getdbcontents_sql($query,0);
				$idList		    = 	array();
				foreach($result as $key)	
					{
						$idList [] 		= 	$key['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}

	
				$query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name,count(distinct(c.comment_id)) as count,n.feed_owner_id,c.group_feed_id,c.comment_id,g.group_name,n.comments_viewed,DATE_FORMAT(CONVERT_TZ(c.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,c.created_on AS creation_time,
									 i.profile_image,concat(i.first_name,' ',i.last_name) as commented_name 
									 FROM tblgroup_news_feed_comment as c 
									 LEFT JOIN tblgroup_news_feed as n on c.group_feed_id = n.group_feed_id 
									 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
									 LEFT JOIN tblgroup as g ON g.group_id = n.group_id 
									 LEFT JOIN tblusers as i ON i.user_id = c.commented_by 
									 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id 
									 WHERE n.feed_owner_id in ($userid) AND n.group_id IN ($groupId) AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.comments_viewed)='0' AND c.commented_by!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' 
									 AND GM.created_on <= c.created_on GROUP BY c.group_feed_id ORDER BY c.created_on DESC";
									// echo $query;exit; 
				$resultArry		=	$this->getdbcontents_sql($query,0);
				if($resultArry)
					{
						$count	=	0;
						foreach($resultArry as $val)
							{
								if($this->visibilityLimit	==	LMT_SITE_USER_PAGE_LIMIT)
								return array_slice($resultArry,'0',$count);
								
								 $val['comments_viewed'] .= ",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];
								 $query				  	  =	 "UPDATE tblgroup_news_feed SET comments_viewed='".$val['comments_viewed']."' WHERE group_feed_id=".$val['group_feed_id'];
								 $result				  =  mysql_query($query);
								 $this->visibilityLimit++;
								 $count++;
							}
					}
				return $resultArry;
			}	
//Group news feed comment notification ends

//Previous Group news feed comment notification starts

		function getPreviousGroupFeedCommentNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$obj			=	new groupManagement();
				$myGroup		=	$obj->getMyGroupsN($id);
				$groupIdList	=	array();
				foreach ($myGroup as $group)
					{
						$groupIdList[]	=	$group['group_id'];
						$groupId		=	implode(",",$groupIdList);
						
					}
				
				$query			=	"SELECT distinct  user_id FROM tblgroup_members WHERE group_id in ($groupId)";
				$result			=	$this->getdbcontents_sql($query,0);
				$idList		    = 	array();
				foreach($result as $key)	
					{
						$idList [] 		= 	$key['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}

	
						$query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name,count(distinct(c.comment_id)) as count,n.feed_owner_id,c.group_feed_id,c.comment_id,g.group_name,n.comments_viewed,DATE_FORMAT(CONVERT_TZ(c.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,c.created_on AS creation_time,
											 i.profile_image,concat(i.first_name,' ',i.last_name) as commented_name 
											 FROM tblgroup_news_feed_comment as c 
											 LEFT JOIN tblgroup_news_feed as n on c.group_feed_id = n.group_feed_id 
											 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
											 LEFT JOIN tblgroup as g ON g.group_id = n.group_id 
											 LEFT JOIN tblusers as i ON i.user_id = c.commented_by 
									 		 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id
											 WHERE n.feed_owner_id in ($userid) AND n.group_id IN ($groupId) AND c.commented_by!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' 
											 AND gm.created_on <=c.created_on GROUP BY c.group_feed_id ORDER BY c.created_on DESC LIMIT 0,3"; 
						$resultArry		=	$this->getdbcontents_sql($query,0);
				
				return $resultArry;
			}	
//Previous Group news feed comment notification ends

//Group News feed like section starts
		function getGroupFeedLikeNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$obj			=	new groupManagement();
				$myGroup		=	$obj->getMyGroupsN($id);
				$groupIdList	=	array();
				foreach ($myGroup as $group)
					{
						$groupIdList[]	=	$group['group_id'];
						$groupId		=	implode(",",$groupIdList);
						
					}
				
				$query			=	"SELECT distinct  user_id FROM tblgroup_members WHERE group_id in ($groupId)";
				$result			=	$this->getdbcontents_sql($query,0);
				$idList		    = 	array();
				foreach($result as $key)	
					{
						$idList [] 		= 	$key['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}

				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user'slogin id	into friends list inorder to get feed owners list


				$query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name,count(distinct(l.like_id)) as count,n.feed_owner_id,l.group_feed_id,l.like_id,n.like_viewed,g.group_name,DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,l.created_on AS creation_time,i.profile_image,concat(i.first_name,' ',i.last_name) AS liked_name 
									 FROM tbllike_group_news_feed as l 
									 LEFT JOIN tblgroup_news_feed as n on l.group_feed_id = n.group_feed_id   
									 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
									 LEFT JOIN tblgroup as g ON g.group_id = n.group_id 
									 LEFT JOIN tblusers as i ON i.user_id = l.user_id 
									 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id
									 WHERE n.feed_owner_id in ($userid) AND n.group_id IN ($groupId) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.like_viewed)='0' AND gm.created_on <=l.created_on GROUP BY l.group_feed_id ORDER BY l.created_on DESC"; 
					//echo $query; exit;				 
				$resultArry		=	$this->getdbcontents_sql($query,0);
				
				if($resultArry) 
					{
						$count	=	0;
						foreach($resultArry as $val)
							{
								if($this->visibilityLimit	==	LMT_SITE_USER_PAGE_LIMIT)
								return array_slice($resultArry,'0',$count);
								
								 $val['like_viewed'] .= ",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];
								 $query				  	  =	 "UPDATE tblgroup_news_feed SET like_viewed='".$val['like_viewed']."' WHERE group_feed_id=".$val['group_feed_id'];
								 $result				  =  mysql_query($query);
								$this->visibilityLimit++;
								$count++;
							}
					}
				return $resultArry;
			}
//Group News feed like section ends

//Previous Group News feed like section starts
		function getPreviousGroupFeedLikeNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$obj			=	new groupManagement();
				$myGroup		=	$obj->getMyGroupsN($id);
				$groupIdList	=	array();
				foreach ($myGroup as $group)
					{
						$groupIdList[]	=	$group['group_id'];
						$groupId		=	implode(",",$groupIdList);
						
					}
				
				$query			=	"SELECT distinct  user_id FROM tblgroup_members WHERE group_id in ($groupId)";
				$result			=	$this->getdbcontents_sql($query,0);
				$idList		    = 	array();
				foreach($result as $key)	
					{
						$idList [] 		= 	$key['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}

				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user'slogin id	into friends list inorder to get feed owners list

						$query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name,count(distinct(l.like_id)) as count,n.feed_owner_id,l.group_feed_id,l.like_id,n.like_viewed,g.group_name,DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,l.created_on AS creation_time,i.profile_image,concat(i.first_name,' ',i.last_name) AS liked_name 
											 FROM tbllike_group_news_feed as l 
											 LEFT JOIN tblgroup_news_feed as n on l.group_feed_id = n.group_feed_id  
											 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
											 LEFT JOIN tblgroup as g ON g.group_id = n.group_id 
											 LEFT JOIN tblusers as i ON i.user_id = l.user_id 
									 		 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id
											 WHERE n.feed_owner_id in ($userid) AND n.group_id IN ($groupId) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' AND gm.created_on <= l.created_on
											 GROUP BY l.group_feed_id ORDER BY l.created_on DESC LIMIT 0,3"; 
						$resultArry		=	$this->getdbcontents_sql($query,0);
				return $resultArry;
			}
//Previous Group News feed like section ends

//Group News feed commments like section starts
		function getGroupFeedCommentsLikeNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$obj			=	new groupManagement();
				$myGroup		=	$obj->getMyGroupsN($id);
				$groupIdList	=	array();
				foreach ($myGroup as $group)
					{
						$groupIdList[]	=	$group['group_id'];
						$groupId		=	implode(",",$groupIdList);
						
					}
				
				$query			=	"SELECT distinct  user_id FROM tblgroup_members WHERE group_id in ($groupId)";
				$result			=	$this->getdbcontents_sql($query,0);
				$idList		    = 	array();
				foreach($result as $key)	
					{
						$idList [] 		= 	$key['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}

					
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user'slogin id	into friends list inorder to get feed owners list

				$query			=	"SELECT concat(u.first_name,' ',u.last_name) as comment_owner_name,count(distinct(l.like_id)) as count,n.commented_by,l.comment_id,l.like_id,DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,l.created_on AS creation_time, n.like_viewed,g.group_name,f.group_id,i.profile_image,concat(i.first_name,' ',i.last_name) as liked_name 
									 FROM tbllike_group_news_feedcomment as l 
									 LEFT JOIN tblgroup_news_feed_comment as n on l.comment_id = n.comment_id 
									 LEFT JOIN tblgroup_news_feed as NF on n.group_feed_id = NF.group_feed_id
									 LEFT JOIN tblusers as u ON n.commented_by = u.user_id 
									 LEFT JOIN tblgroup_news_feed  as f ON f.group_feed_id = n.group_feed_id LEFT JOIN tblgroup as g on f.group_id = g.group_id 
									 LEFT JOIN tblusers as i ON i.user_id = l.user_id 
									 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id
									 WHERE n.commented_by in ($userid) AND NF.group_id IN ($groupId) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' 
									 AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.like_viewed)='0' AND GM.created_on <=l.created_on GROUP BY l.comment_id ORDER BY l.created_on DESC"; 
				$resultArry		=	$this->getdbcontents_sql($query,0);
				if($resultArry)
				{
					$count	=	0;
					foreach($resultArry as $val)
						{
							if($this->visibilityLimit	==	LMT_SITE_USER_PAGE_LIMIT)
								return array_slice($resultArry,'0',$count);
								
							 $val['like_viewed'] .= ",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];
							 $query				  	  =	 "UPDATE tblgroup_news_feed_comment SET like_viewed='".$val['like_viewed']."' WHERE comment_id=".$val['comment_id'];
							 $result				  =  mysql_query($query);
							$this->visibilityLimit++;
							$count++;
						}
				}
				
				return $resultArry;
			}
//Group News feed comments like section ends

//Group News feed commments like section starts
		function getPreviousGroupFeedCommentsLikeNotifications($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$obj			=	new groupManagement();
				$myGroup		=	$obj->getMyGroupsN($id);
				$groupIdList	=	array();
				foreach ($myGroup as $group)
					{
						$groupIdList[]	=	$group['group_id'];
						$groupId		=	implode(",",$groupIdList);
						
					}
				
				$query			=	"SELECT distinct  user_id FROM tblgroup_members WHERE group_id in ($groupId)";
				$result			=	$this->getdbcontents_sql($query,0);
				$idList		    = 	array();
				foreach($result as $key)	
					{
						$idList [] 		= 	$key['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}

					
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user'slogin id	into friends list inorder to get feed owners list

						$query			=	"SELECT concat(u.first_name,' ',u.last_name) as comment_owner_name,count(distinct(l.like_id)) as count,n.commented_by,l.comment_id,l.like_id,DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,l.created_on AS creation_time,
											 n.like_viewed,g.group_name,f.group_id,i.profile_image,concat(i.first_name,' ',i.last_name) as liked_name 
											 FROM tbllike_group_news_feedcomment as l 
											 LEFT JOIN tblgroup_news_feed_comment as n on l.comment_id = n.comment_id
											 LEFT JOIN tblgroup_news_feed as NF on n.group_feed_id = NF.group_feed_id 
											 LEFT JOIN tblusers as u ON n.commented_by = u.user_id 
											 LEFT JOIN tblgroup_news_feed  as f ON f.group_feed_id = n.group_feed_id 
											 LEFT JOIN tblgroup as g on f.group_id = g.group_id LEFT JOIN tblusers as i ON i.user_id = l.user_id
									 		 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id
											 WHERE n.commented_by in ($userid) AND NF.group_id IN ($groupId) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."'
											 AND GM.created_on <= l.created_on GROUP BY l.comment_id ORDER BY l.created_on DESC LIMIT 0,3"; 
		
						$resultArry		=	$this->getdbcontents_sql($query,0);

				
				return $resultArry;
			}
//Group News feed comments like section ends

//New events notification starts 
		function getNewEvents($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
				
			    $query			=	"SELECT DISTINCT(e.event_id),concat(u.first_name,u.last_name) as event_owner_name,
									 DATE_FORMAT(CONVERT_TZ(e.created_date, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_date ,e.created_date AS creation_time, e.viewed_by, u.profile_image 
									 FROM tblevents as e 
									 LEFT JOIN tblusers as u ON e.event_owner_id = u.user_id 
									 LEFT JOIN tblfriends_accepted as A ON (A.user_id = u.user_id OR A.friends_id = u.user_id)
									 WHERE e.event_owner_id in ($userid) AND e.event_owner_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",e.viewed_by)='0' AND A.accepted_date <= e.created_date Group By e.event_id ORDER BY e.created_date DESC";
				$resultArry		=	$this->getdbcontents_sql($query,0);
				if($resultArry)
				{
					$count	=	0;
					foreach($resultArry as $val)
						{
							if($this->visibilityLimit	==	LMT_SITE_USER_PAGE_LIMIT)
								return array_slice($resultArry,'0',$count);
								
							 $val['viewed_by'] 		 .= ",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];
						     $query				  	  =	 "UPDATE tblevents SET viewed_by ='".$val['viewed_by']."' WHERE event_id=".$val['event_id'];
							 $result				  =  mysql_query($query);
							 $this->visibilityLimit++;
							 $count++;
						}
				}
				return $resultArry;
			}

//New events notification ends
//Previous New events notification starts 
		function getPreviousNewEvents($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
				
				$query			=	"SELECT DISTINCT(e.event_id),concat(u.first_name,u.last_name) as event_owner_name,
							     	DATE_FORMAT(CONVERT_TZ(e.created_date, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_date, e.viewed_by, e.created_date AS creation_time, u.profile_image 
									FROM tblevents as e 
									LEFT JOIN tblusers as u ON e.event_owner_id = u.user_id 
									LEFT JOIN tblfriends_accepted as A ON (A.user_id = u.user_id OR A.friends_id = u.user_id) 
									WHERE e.event_owner_id in ($userid) AND e.event_owner_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."'  AND A.accepted_date <=e.created_date Group By e.event_id ORDER BY e.created_date DESC LIMIT 0,3";
							$resultArry		=	$this->getdbcontents_sql($query,0);

				return $resultArry;
			}

//Previous New events notification ends

//Group New events notification starts 
		function getGroupNewEvents($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$obj			=	new groupManagement();
				$myGroup		=	$obj->getMyGroupsN($id);
				$groupIdList	=	array();
				foreach ($myGroup as $group)
					{
						$groupIdList[]	=	$group['group_id'];
						$groupId		=	implode(",",$groupIdList);
						
					}
				
				$query			=	"SELECT distinct  user_id FROM tblgroup_members WHERE group_id in ($groupId)";
				$result			=	$this->getdbcontents_sql($query,0);
				$idList		    = 	array();
				foreach($result as $key)	
					{
						$idList [] 		= 	$key['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}

		
			    $query			=	"SELECT DISTINCT(e.event_id),concat(u.first_name,' ',u.last_name) as event_owner_name,
									 DATE_FORMAT(CONVERT_TZ(e.created_date, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_date, e.created_date AS creation_time, e.viewed_by, g.group_name, u.profile_image 
									 FROM tblgroup_events as e 
									 LEFT JOIN tblusers as u ON e.event_owner_id = u.user_id 
									 LEFT JOIN tblgroup as g ON e.event_group_id = g.group_id
									 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id
									 WHERE e.event_owner_id in ($userid) AND e.event_group_id IN ($groupId) AND e.event_owner_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",e.viewed_by)='0'
									 AND GM.created_on <=e.created_date Group BY e.event_id ORDER BY e.created_date DESC";
				
				$resultArry		=	$this->getdbcontents_sql($query,0);
				if($resultArry)
					{
						$count	=	0;
						foreach($resultArry as $val)
							{
								
								if($this->visibilityLimit	==	LMT_SITE_USER_PAGE_LIMIT)
								return array_slice($resultArry,'0',$count);
								
								 $val['viewed_by'] 		 .= ",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];
								 $query				  	  =	 "UPDATE tblgroup_events SET viewed_by ='".$val['viewed_by']."' WHERE event_id=".$val['event_id'];
								 $result				  =  mysql_query($query);
								$this->visibilityLimit++;
								$count++;
							}
					}
					
				return $resultArry;
			}

//Group New events notification ends

//Previous Group New events notification starts 
		function getPreviousGroupNewEvents($id, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
			{
				$obj			=	new groupManagement();
				$myGroup		=	$obj->getMyGroupsN($id);
				$groupIdList	=	array();
				foreach ($myGroup as $group)
					{
						$groupIdList[]	=	$group['group_id'];
						$groupId		=	implode(",",$groupIdList);
						
					}
				
				$query			=	"SELECT distinct  user_id FROM tblgroup_members WHERE group_id in ($groupId)";
				$result			=	$this->getdbcontents_sql($query,0);
				$idList		    = 	array();
				foreach($result as $key)	
					{
						$idList [] 		= 	$key['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}

						$query			=	"SELECT DISTINCT(e.event_id),concat(u.first_name,' ',u.last_name) as event_owner_name,
											 DATE_FORMAT(CONVERT_TZ(e.created_date, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_date , e.created_date AS creation_time, e.viewed_by, g.group_name, u.profile_image 
											 FROM tblgroup_events as e 
											 LEFT JOIN tblusers as u ON e.event_owner_id = u.user_id 
											 LEFT JOIN tblgroup as g ON e.event_group_id = g.group_id 
									 		 LEFT JOIN tblfriends_accepted as A ON (A.user_id = u.user_id OR A.friends_id = u.user_id) AND (A.friends_id IN (".$userid.") OR A.user_id IN (".$userid.")) 
											 WHERE e.event_owner_id in ($userid) AND e.event_owner_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."'  AND e.event_group_id IN ($groupId) AND A.accepted_date <= e.created_date GROUP BY e.event_id ORDER BY e.created_date DESC LIMIT 0,3";
						
						$resultArry		=	$this->getdbcontents_sql($query,0);

				return $resultArry;
			}

//Previous Group New events notification ends


		function getNotificationCount($id)
			{
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
					
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user's login id	into friends list inorder to get feed owners list


				//Count of news feed comment
				 $query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name, count(distinct(c.comment_id)) as count, n.feed_owner_id, c.news_feed_id, c.comment_id, n.comments_viewed, i.profile_image,concat(i.first_name,' ',i.last_name) as commented_name,
				DATE_FORMAT(CONVERT_TZ(c.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,  max(c.commented_by) 
									 FROM tblnews_feed_comment as c 
									 LEFT JOIN tblnews_feed as n on c.news_feed_id = n.feed_id 
									 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
									 LEFT JOIN tblusers as i ON i.user_id = c.commented_by 
									 LEFT JOIN tblfriends_accepted as f ON ( (f.user_id = u.user_id OR f.friends_id = u.user_id) OR ( f.friends_id = i.user_id OR f.user_id=i.user_id ) ) 
									 WHERE (n.feed_owner_id in ($userid) OR c.commented_by IN($userid)) AND c.commented_by!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."'
									 AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.comments_viewed)='0' AND f.accepted_date <=c.created_on GROUP BY c.news_feed_id ORDER BY c.created_on DESC "; 				
				 $result			=	mysql_query($query);
				 $count1			=	mysql_num_rows($result);
				
				//Count of news feed like
				 $query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name, count(distinct(l.like_id)) as count, n.feed_owner_id, l.feed_id, l.like_id, n.like_viewed, DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,i.profile_image,
									 concat(i.first_name,' ',i.last_name) as liked_name 
									 FROM tbllike_news_feed as l 
									 LEFT JOIN tblnews_feed as n on l.feed_id = n.feed_id  
									 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
									 LEFT JOIN tblusers as i ON i.user_id = l.user_id 
									 LEFT JOIN tblfriends_accepted as f ON ((f.user_id = u.user_id OR f.friends_id = u.user_id) OR ( f.friends_id = l.user_id OR f.user_id=l.user_id ) )
									 WHERE (n.feed_owner_id in ($userid) OR l.user_id in($userid) ) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."'
									 AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.like_viewed)='0' AND f.accepted_date <=l.created_on GROUP BY l.feed_id ORDER BY l.created_on LIMIT 0,3";
				$result			=	mysql_query($query);
				$count2			=	mysql_num_rows($result);
				
				//Count of news feed comment like

				
				$query			=	"SELECT concat(u.first_name,' ',u.last_name) as comment_owner_name,count(distinct(l.like_id)) as count,n.commented_by,l.comment_id,l.like_id,n.like_viewed,i.profile_image,
									 concat(i.first_name,' ',i.last_name) as liked_name,DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on 
									 FROM tbllike_news_feedcomment as l 
									 LEFT JOIN tblnews_feed_comment as n on l.comment_id = n.comment_id 
									 LEFT JOIN tblnews_feed AS NF ON NF.feed_id=n.news_feed_id
									 LEFT JOIN tblusers as O on NF.feed_owner_id =  O.user_id
									 LEFT JOIN tblusers as u ON n.commented_by = u.user_id 
									 LEFT JOIN tblusers as i ON i.user_id = l.user_id
									 LEFT JOIN tblfriends_accepted as f ON ((f.user_id=O.user_id OR f.friends_id=O.user_id) OR (f.user_id = u.user_id OR f.friends_id = u.user_id) OR (f.friends_id =i.user_id OR f.user_id=i.user_id) ) 
									 WHERE (NF.feed_owner_id in ($userid) OR n.commented_by in($userid) OR l.user_id in($userid) ) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."'
									 AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.like_viewed)='0' AND f.accepted_date <=l.created_on GROUP BY l.comment_id ORDER BY l.created_on DESC"; 
				$result			=	mysql_query($query);
			    $count3			=	mysql_num_rows($result);
				
				
				//Count of friends accept 
				
				$query			=	"SELECT concat(u.first_name,' ',u.last_name) as friend_name, f.user_id,f.friends_id,DATE_FORMAT(CONVERT_TZ(f.accepted_date, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS accepted_date ,u.profile_image 
						FROM tblfriends_accepted as f  
						LEFT JOIN tblusers as u ON f.friends_id=u.user_id   
						where f.user_id=$id AND u.user_id !=$id AND notification=0 ORDER BY f.accepted_date DESC"; 
				$result			=	mysql_query($query);
				 $count4			=	mysql_num_rows($result);
				
				//events count
				
				$idList		    = 	array();
				foreach($_SESSION['LMT_MY_FRIENDS'] as $userID)
					{
						$idList [] 		= 	$userID['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
				
				$query			=	"SELECT DISTINCT(e.event_id),concat(u.first_name,u.last_name) as event_owner_name,
									 DATE_FORMAT(CONVERT_TZ(e.created_date, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_date , e.viewed_by, u.profile_image 
									 FROM tblevents as e 
									 LEFT JOIN tblusers as u ON e.event_owner_id = u.user_id 
									 LEFT JOIN tblfriends_accepted as A ON (A.user_id = u.user_id OR A.friends_id = u.user_id)
									 WHERE e.event_owner_id in ($userid) AND e.event_owner_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",e.viewed_by)='0' AND A.accepted_date <= e.created_date Group By e.event_id ORDER BY e.created_date DESC";
				$result			=	mysql_query($query);
				
				 $count5     	=	mysql_num_rows($result);
				
				
				
				$obj			=	new groupManagement();
				$myGroup		=	$obj->getMyGroupsN($id);
				$groupIdList	=	array();
				foreach ($myGroup as $group)
					{
						$groupIdList[]	=	$group['group_id'];
						$groupId		=	implode(",",$groupIdList);
						
					}
				
				$query			=	"SELECT distinct  user_id FROM tblgroup_members WHERE group_id in ($groupId) AND user_id!=$id";
				$result			=	$this->getdbcontents_sql($query,0);
				$idList		    = 	array();
				foreach($result as $key)	
					{
						$idList [] 		= 	$key['user_id'];
						$userid			=	implode(",",$idList);	//friends list
					}
				//Count of news feed  group
				
				 $query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name,n.feed_owner_id,n.group_feed_id,DATE_FORMAT(CONVERT_TZ(n.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,g.group_name,n.feed_viewed,u.profile_image
									 FROM tblgroup_news_feed as n 
									 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
									 LEFT JOIN tblgroup as g ON n.group_id=g.group_id 
									 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id 
									 WHERE n.feed_owner_id in ($userid) AND n.group_id IN ($groupId) AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.feed_viewed) ='0'
									 AND GM.created_on <= n.created_on GROUP BY n.group_feed_id ORDER BY n.created_on DESC"; 
				$result			=	mysql_query($query);
				
				 $count6			=	mysql_num_rows($result); 
				$userid			= 	$userid.",".$_SESSION['USER_LOGIN']['LMT_USER_ID'];//added user's login id	into friends list inorder to get feed owners list
		
				//Count of news feed comment group
				
			    $query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name,count(distinct(c.comment_id)) as count,n.feed_owner_id,c.group_feed_id,c.comment_id,g.group_name,n.comments_viewed,DATE_FORMAT(CONVERT_TZ(c.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,
									 i.profile_image,concat(i.first_name,' ',i.last_name) as commented_name 
									 FROM tblgroup_news_feed_comment as c 
									 LEFT JOIN tblgroup_news_feed as n on c.group_feed_id = n.group_feed_id 
									 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
									 LEFT JOIN tblgroup as g ON g.group_id = n.group_id 
									 LEFT JOIN tblusers as i ON i.user_id = c.commented_by 
									 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id 
									 WHERE n.feed_owner_id in ($userid) AND n.group_id IN ($groupId) AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.comments_viewed)='0' AND c.commented_by!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' 
									 AND GM.created_on <= c.created_on GROUP BY c.group_feed_id ORDER BY c.created_on DESC"; 
				$result			=	mysql_query($query);
				$count7		    =	mysql_num_rows($result); 
				
				//Count of news feed  like group
				$query			=	"SELECT concat(u.first_name,' ',u.last_name) as feed_owner_name,count(distinct(l.like_id)) as count,n.feed_owner_id,l.group_feed_id,l.like_id,n.like_viewed,g.group_name,DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,i.profile_image,concat(i.first_name,' ',i.last_name) AS liked_name 
									 FROM tbllike_group_news_feed as l 
									 LEFT JOIN tblgroup_news_feed as n on l.group_feed_id = n.group_feed_id  
									 AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.like_viewed)='0' 
									 LEFT JOIN tblusers as u ON n.feed_owner_id = u.user_id 
									 LEFT JOIN tblgroup as g ON g.group_id = n.group_id 
									 LEFT JOIN tblusers as i ON i.user_id = l.user_id 
									 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id
									 WHERE n.feed_owner_id in ($userid) AND n.group_id IN ($groupId) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' AND gm.created_on <=l.created_on GROUP BY l.group_feed_id ORDER BY l.created_on DESC"; 
				
				$result			=	mysql_query($query);
			    $count8			=	mysql_num_rows($result);
				
				
				//Count of news feed comment like
				
			 	$query			=	"SELECT concat(u.first_name,' ',u.last_name) as comment_owner_name,count(distinct(l.like_id)) as count,n.commented_by,l.comment_id,l.like_id,DATE_FORMAT(CONVERT_TZ(l.created_on, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on,
									 n.like_viewed,g.group_name,f.group_id,i.profile_image,concat(i.first_name,' ',i.last_name) as liked_name 
									 FROM tbllike_group_news_feedcomment as l 
									 LEFT JOIN tblgroup_news_feed_comment as n on l.comment_id = n.comment_id 
									 LEFT JOIN tblgroup_news_feed as NF on n.group_feed_id = NF.group_feed_id
									 LEFT JOIN tblusers as u ON n.commented_by = u.user_id 
									 LEFT JOIN tblgroup_news_feed  as f ON f.group_feed_id = n.group_feed_id LEFT JOIN tblgroup as g on f.group_id = g.group_id 
									 LEFT JOIN tblusers as i ON i.user_id = l.user_id 
									 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id
									 WHERE n.commented_by in ($userid) AND NF.group_id IN ($groupId) AND l.user_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' 
									 AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",n.like_viewed)='0' AND GM.created_on <=l.created_on GROUP BY l.comment_id ORDER BY l.created_on DESC"; 


				$result			=	mysql_query($query);
			    $count9			=	mysql_num_rows($result);

				//Count of group event
				
			    $query			=	"SELECT DISTINCT(e.event_id),concat(u.first_name,' ',u.last_name) as event_owner_name,
									 DATE_FORMAT(CONVERT_TZ(e.created_date, '$serverOffset','$studentOffset'), '$dtFmt , $tmFmt') AS created_on, e.viewed_by, g.group_name, u.profile_image 
									 FROM tblgroup_events as e 
									 LEFT JOIN tblusers as u ON e.event_owner_id = u.user_id 
									 LEFT JOIN tblgroup as g ON e.event_group_id = g.group_id
									 LEFT JOIN tblgroup_members as GM ON GM.group_id=g.group_id
									 WHERE e.event_owner_id in ($userid) AND e.event_group_id IN ($groupId) AND e.event_owner_id!= '".$_SESSION['USER_LOGIN']['LMT_USER_ID']."' AND LOCATE(".$_SESSION['USER_LOGIN']['LMT_USER_ID'].",e.viewed_by)='0'
									 AND GM.created_on <=e.created_date Group BY e.event_id ORDER BY e.created_date DESC";
				$result			=	mysql_query($query);
			    $count10	    =	mysql_num_rows($result);

				//Count of friends request
				
			   $query			=	"SELECT u.user_id, u.first_name, u.last_name, u.profile_image, r.`friend_request_id` 
			   						 FROM `tblfriends_request` r JOIN `tblusers` u   
									 LEFT JOIN tblfriends_accepted as A ON (A.user_id = u.user_id OR A.friends_id = u.user_id) AND A.friends_id IN (".$userid.") 
									 WHERE r.`friends_id`=".$_SESSION['USER_LOGIN']['LMT_USER_ID']." AND r.`status`=1 AND u.`user_id`=r.`user_id` AND r.viewed =0
									 AND A.accepted_date <=r.requested_date Group By u.user_id ORDER BY r.requested_date DESC" ; 
				//echo $query; exit;		
				$result			=	mysql_query($query);
			    $count11	    =	mysql_num_rows($result);

			   $count			=	$count1+$count2+$count3+$count4+$count5+$count6+$count7+$count8+$count9+$count10+$count11;
				
				return	$count;
			
			}

}
?>
