<?php
/**************************************************************************************
Created By 	: Lijesh
Created On	: 17-08-2011
Purpose		: News Feed
**************************************************************************************/
class newsFeed extends siteclass{
	
	function addNewsFeed($newsFeedDetail)
		{			
			
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tblnews_feed',$newsFeedDetail, 0);
			//$imageuser = $_SESSION['USER_LOGIN']['USER_NAME'];
			/*$data = $_SESSION['USER_LOGIN']['LMT_USER_ID'];	
			$sql =	"SELECT tul.user_name FROM `tbluser_login` as  tul where tul.login_id =".$data;
			$user_name =	$this->getdbcontents_sql($sql,0);
			$imageuser = $user_name[0]['user_name'];*/
			$data = $_SESSION['USER_LOGIN']['LMT_USER_ID'];			
			$sql =	"SELECT us.login_id FROM tblusers as us where us.user_id  =".$data;
			$login_id =	$this->getdbcontents_sql($sql,0);			
			$us_nam =	"SELECT tul.user_name FROM `tbluser_login` as  tul where tul.login_id =".$login_id[0]['login_id'];
			$user_name =	$this->getdbcontents_sql($us_nam,0);
			$imageuser = $user_name[0]['user_name'];		
			$sql1 =	"SELECT trc.totaldeleted FROM `tblreview_content` as trc where trc.imageuser ='".$imageuser."'";
			$totaldeleted =	$this->getdbcontents_sql($sql1,0);
			//print_r ($totaldeleted);			
			$rev_a['rev_image_id'] = $this->id;
			$rev_a['rev_image_file'] = $newsFeedDetail['feed_file'];
			$rev_a['rev_file_type'] = $newsFeedDetail['feed_type'];
			//$rev_a['image_owner_id'] = $newsFeedDetail['feed_owner_id'];
			//$img_id = $this->id;
			//echo $img_id;
			$rev_a['rev_date_time'] = date("Y-m-d H:i:s");
			$rev_a['rev_status'] = 2;
			//$rev_a['imageuser'] = $_SESSION['USER_LOGIN']['LMT_USER_ID'];
			$rev_a['imageuser'] = $imageuser;
			$rev_a['totaldeleted'] = $totaldeleted[0]['totaldeleted'];
			$rev_a['rev_ip'] 		= $this->getClientIP();
			//print_r ($rev_a);
			$this->rid = $this->db_insert('tblreview_content',$rev_a, 0);
			if(!$this->rid)
				{						
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());									
					return false;					
				}
			else
				return $this->id;	
		}
	function getClientIP()
			{
				return $_SERVER['REMOTE_ADDR'];
			}	
	
			
	function addGroupNewsFeed($newsFeedDetail)
		{			
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tblgroup_news_feed',$newsFeedDetail, 0);
			if(!$this->id)
				{						
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());									
					return false;					
				}
			else
				return $this->id;	
		}	
	
	/*
		Get Home Page news Feed
	*/
	function getNewsFeed($userID, $page = 0, $limit = 0, $loginID = 0)
		{
			$userID = implode(',', $userID);//print_r ($userID);
			/*$sql = "SELECT News.feed_id, News.feed_type, News.feed_comment, News.feed_content, News.feed_file, SUBSTRING(News.feed_file, 1, (LOCATE('.', News.feed_file)-1)) AS file_name, SUBSTRING(News.feed_content, (LOCATE('=', News.feed_content)+1)) AS youtube_id, News.created_on, TIMESTAMPDIFF(SECOND, News.created_on, now()) AS diff_second, TIMESTAMPDIFF(MINUTE, News.created_on, now()) AS diff_minute, TIMESTAMPDIFF(HOUR, News.created_on, now()) AS diff_hour, TIMESTAMPDIFF(DAY, News.created_on, now()) AS diff_day, User.user_id, User.first_name, User.last_name, NewsLike.like_id AS like_status,
				    User.profile_image,  News.feed_owner_id, News.feed_to_id, CONCAT(Visited.first_name,' ',Visited.last_name) AS VisitedName 
					FROM tblnews_feed AS News 
					LEFT JOIN tbllike_news_feed AS NewsLike ON News.feed_id = NewsLike.feed_id AND NewsLike.user_id = $loginID 
					LEFT JOIN tblusers AS User ON News.feed_owner_id = User.user_id 
					LEFT JOIN tblusers AS Visited ON News.feed_to_id = Visited.user_id 
					WHERE News.feed_owner_id IN($userID) ORDER BY  News.created_on DESC";*/			
					
			$sql = "SELECT News.feed_id, News.feed_type, News.feed_comment, News.feed_content, News.feed_file, SUBSTRING(News.feed_file, 1, (LOCATE('.', News.feed_file)-1)) AS file_name, SUBSTRING(News.feed_content, (LOCATE('=', News.feed_content)+1)) AS youtube_id, News.created_on, TIMESTAMPDIFF(SECOND, News.created_on, now()) AS diff_second, TIMESTAMPDIFF(MINUTE, News.created_on, now()) AS diff_minute, TIMESTAMPDIFF(HOUR, News.created_on, now()) AS diff_hour, TIMESTAMPDIFF(DAY, News.created_on, now()) AS diff_day, User.user_id, User.first_name, User.last_name, NewsLike.like_id AS like_status,
				    User.profile_image,  News.feed_owner_id, News.feed_to_id, CONCAT(Visited.first_name,' ',Visited.last_name) AS VisitedName,
					x.rev_status,x.rev_date_time
					FROM tblnews_feed AS News 
					LEFT JOIN tblreview_content AS x ON News.feed_id = x.rev_image_id
					JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time
					LEFT JOIN tbllike_news_feed AS NewsLike ON News.feed_id = NewsLike.feed_id AND NewsLike.user_id = $loginID 
					LEFT JOIN tblusers AS User ON News.feed_owner_id = User.user_id 
					LEFT JOIN tblusers AS Visited ON News.feed_to_id = Visited.user_id 
					WHERE News.feed_owner_id IN($userID) AND x.rev_status !=0 ORDER BY  News.created_on DESC";
				
			$page =  $page * $limit;		
			if($limit)		
				$sql .= " LIMIT $page, $limit";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}
		
	/*
		Get Home Page news Feed
	*/
	function getNewsFeedImage($userID, $page = 0, $limit = 0)
		{
			$userID = implode(',', $userID);
			
			$sql = "SELECT News.feed_id, News.feed_file, User.user_id, User.first_name, User.last_name, User.profile_image, 
					x.rev_status, x.rev_image_id, x.rev_date_time
					FROM tblnews_feed AS News LEFT JOIN tblreview_content AS x ON News.feed_id = x.rev_image_id JOIN 
					(SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y 
					ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time				
					LEFT JOIN tblusers AS User ON News.feed_owner_id = User.user_id 
					WHERE News.feed_owner_id IN($userID) AND feed_type = 'LMT_PHOTO' AND rev_status != 0
					ORDER BY  News.created_on DESC";
					//print_r ($sql);
			$page =  $page * $limit;		
			if($limit)		
				$sql .= " LIMIT $page, $limit";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}
		
	/*
		Get Home Page news Feed
	*/
	function getNewsFeedVideos($userID, $page = 0, $limit = 0)
		{
			$userID = implode(',', $userID);
			$sql = "SELECT News.feed_id, News.feed_file, SUBSTRING(News.feed_content, (LOCATE('=', News.feed_content)+1)) 
					AS youtube_id, User.user_id, User.first_name, User.last_name, User.profile_image
					FROM tblnews_feed AS News 					
					LEFT JOIN tblusers AS User ON News.feed_owner_id = User.user_id 
					WHERE News.feed_owner_id IN($userID) AND (feed_type = 'LMT_VIDEO' OR feed_type = 'LMT_URL') 
					ORDER BY  News.created_on DESC";
			$page =  $page * $limit;		
			if($limit)		
				$sql .= " LIMIT $page, $limit";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}		
		
	/*
		Get Group news Feed
	*/
	function getGroupNewsFeed($userID, $groupID, $page = 0, $limit = 0, $loginID = 0)
		{
			$userID = implode(',', $userID);
			$sql = "SELECT News.group_feed_id, News.feed_type, News.feed_comment, News.feed_content, News.feed_file, SUBSTRING(News.feed_file, 1, (LOCATE('.', News.feed_file)-1)) AS file_name, SUBSTRING(News.feed_content, (LOCATE('=', News.feed_content)+1)) AS youtube_id, News.created_on, TIMESTAMPDIFF(SECOND, News.created_on, now()) AS diff_second, TIMESTAMPDIFF(MINUTE, News.created_on, now()) AS diff_minute, TIMESTAMPDIFF(HOUR, News.created_on, now()) AS diff_hour, TIMESTAMPDIFF(DAY, News.created_on, now()) AS diff_day, User.user_id, User.first_name, User.last_name, NewsLike.like_id AS like_status, User.profile_image
					FROM tblgroup_news_feed AS News 
					LEFT JOIN tbllike_group_news_feed AS NewsLike ON News.group_feed_id = NewsLike.group_feed_id AND NewsLike.user_id = $loginID 
					LEFT JOIN tblusers AS User ON News.feed_owner_id = User.user_id 
					WHERE News.feed_owner_id IN($userID) AND News.group_id = $groupID ORDER BY  News.created_on DESC";
			$page =  $page * $limit;
			if($limit)		
				$sql .= " LIMIT $page, $limit";
			$resultArry		=	$this->getdbcontents_sql($sql,0);			
			return $resultArry;		
		}	
	
	
	/*
		Array : $newsFeedID
		Return count of number of news feed like
	*/
	function getGroupLikeCount($newsFeedID)
		{
			$newsFeedID = implode(',', $newsFeedID);
			$sql = "SELECT group_feed_id, count(like_id) AS totalLike FROM tbllike_group_news_feed WHERE group_feed_id IN($newsFeedID) GROUP BY group_feed_id";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			$data = array();
			foreach($resultArry as $likeIndex => $comment)
				{
					$data[$comment['group_feed_id']] = $resultArry[$likeIndex];
				}
			
			return $data;
		}
	
	/*
		Get news feed users likes
		Var : $feedID
	*/	
	function getGroupUsersLikes($feedID)
		{
			$sql = "SELECT LK.user_id, USER.first_name, USER.last_name, USER.profile_image, college  
					FROM tbllike_group_news_feed AS LK 
					LEFT JOIN tblusers AS USER ON LK.user_id = USER.user_id
					WHERE LK.group_feed_id = $feedID ORDER BY CONCAT(USER.first_name, ' ' ,USER.last_name)";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}	
	
	function getMyFeed($feedID, $permission = 1, $limit = 0)
		{			
			$sql = "SELECT News.feed_id, News.feed_type, News.feed_comment, News.feed_content, SUBSTRING(News.feed_content, (LOCATE('=', News.feed_content)+1)) AS youtube_id, News.feed_file, News.created_on, User.first_name, User.last_name,User.login_id,User.profile_image
					FROM tblnews_feed AS News 
					LEFT JOIN tblusers AS User ON News.feed_owner_id = User.user_id 
					WHERE News.feed_id = $feedID";
			if($limit)		
				$sql .= " LIMIT $limit";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}	
		
	function getMyGroupFeed($feedID, $permission = 1, $limit = 0)
		{			
			$sql = "SELECT News.group_feed_id, News.feed_type, News.feed_comment, News.feed_content, SUBSTRING(News.feed_content, (LOCATE('=', News.feed_content)+1)) AS youtube_id, News.feed_file, News.created_on, User.first_name, User.last_name, User.profile_image
					FROM tblgroup_news_feed AS News 
					LEFT JOIN tblusers AS User ON News.feed_owner_id = User.user_id 
					WHERE News.group_feed_id = $feedID";
			if($limit)		
				$sql .= " LIMIT $limit";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}
		
	/*
		Set Home News Feed
	*/	
	function setNewsFeedComment($commentDet)
		{
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tblnews_feed_comment',$commentDet, 1);exit;
			if(!$this->id)
				{						
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());					
					return false;					
				}
			else
					return $this->id;	
		}
		
	
	/*
		Set Group News Feed
	*/	
	function setGroupNewsFeedComment($commentDet)
		{
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tblgroup_news_feed_comment',$commentDet, 0);
			if(!$this->id)
				{						
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());					
					return false;					
				}
			else
					return $this->id;	
		}	
		
		
		
	/*
	`@newsFeedID : Array
	*/	
	function getNewsFeedComments($newsFeedID, $loginID = 0)
		{
			$newsFeedID = implode(',', $newsFeedID);
			$sql = "SELECT Comment.comment_id, Comment.comment, Comment.news_feed_id, TIMESTAMPDIFF(SECOND, Comment.created_on, now()) AS diff_second, TIMESTAMPDIFF(MINUTE, Comment.created_on, now()) AS diff_minute, TIMESTAMPDIFF(HOUR, Comment.created_on, now()) AS diff_hour, TIMESTAMPDIFF(DAY, Comment.created_on, now()) AS diff_day, CommentLike.like_id AS like_status,  User.user_id, User.first_name, User.last_name, 
					User.profile_image
					FROM tblnews_feed_comment AS Comment
					LEFT JOIN tbllike_news_feedcomment AS CommentLike ON Comment.comment_id = CommentLike.comment_id 
					AND CommentLike.user_id = $loginID 
					LEFT JOIN tblusers AS User ON Comment.commented_by = User.user_id
					WHERE Comment.news_feed_id IN($newsFeedID) ORDER BY Comment.news_feed_id, Comment.created_on DESC";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			$data = array();
			$commentID = array();
			foreach($resultArry as $commentIndex => $comment)
				{
					$data['comments'][$comment['news_feed_id']][] = $resultArry[$commentIndex];
					$commentID [] = $comment['comment_id'];
				}
			$data['commentLikes'] = $this->getCommentsLikeCount($commentID);	
			//$this->print_r($data);exit;
			return $data;
		}
	
	/*
		Array : $commentID
		Return count of number of news feed comment like
	*/
	function getCommentsLikeCount($commentID)
		{
			$commentID = implode(',', $commentID);
			$sql = "SELECT comment_id, count(like_id) AS totalLike FROM tbllike_news_feedcomment WHERE comment_id IN($commentID) GROUP BY comment_id";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			$data = array();
			foreach($resultArry as $likeIndex => $comment)
				{
					$data[$comment['comment_id']] = $resultArry[$likeIndex];
				}
			
			return $data;
		}
		
	/*
		Get news feed users comment likes
		Var : $commentID
	*/	
	function getUsersCommentLikes($commentID)
		{
			$sql = "SELECT LK.user_id, USER.first_name, USER.last_name, USER.profile_image, college  
					FROM tbllike_news_feedcomment AS LK 
					LEFT JOIN tblusers AS USER ON LK.user_id = USER.user_id
					WHERE LK.comment_id = $commentID ORDER BY CONCAT(USER.first_name, ' ' ,USER.last_name)";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}
	
	/*
		Array : $newsFeedID
		Return count of number of news feed like
	*/
	function getLikeCount($newsFeedID)
		{
			$newsFeedID = implode(',', $newsFeedID);
			$sql = "SELECT feed_id, count(like_id) AS totalLike FROM tbllike_news_feed WHERE feed_id IN($newsFeedID) GROUP BY feed_id";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			$data = array();
			foreach($resultArry as $likeIndex => $comment)
				{
					$data[$comment['feed_id']] = $resultArry[$likeIndex];
				}
			
			return $data;
		}	
	
	/*
		Get news feed users likes
		Var : $feedID
	*/	
	function getUsersLikes($feedID)
		{
			$sql = "SELECT LK.user_id, USER.first_name, USER.last_name, USER.profile_image,User.login_id, college  
					FROM tbllike_news_feed AS LK 
					LEFT JOIN tblusers AS USER ON LK.user_id = USER.user_id
					WHERE LK.feed_id = $feedID ORDER BY CONCAT(USER.first_name, ' ' ,USER.last_name)";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}
	
	
	/*
		$newsFeedID => Var
	*/
	function getSingleFeedComments($newsFeedID, $loginID = 0)
		{			
			$sql = "SELECT Comment.comment_id, Comment.comment, Comment.news_feed_id, TIMESTAMPDIFF(SECOND, Comment.created_on, now()) AS diff_second, TIMESTAMPDIFF(MINUTE, Comment.created_on, now()) AS diff_minute, TIMESTAMPDIFF(HOUR, Comment.created_on, now()) AS diff_hour, TIMESTAMPDIFF(DAY, Comment.created_on, now()) AS diff_day, CommentLike.like_id AS like_status,  User.user_id, User.first_name, User.last_name, 
					User.profile_image
					FROM tblnews_feed_comment AS Comment
					LEFT JOIN tbllike_news_feedcomment AS CommentLike ON Comment.comment_id = CommentLike.comment_id 
					AND CommentLike.user_id = $loginID 
					LEFT JOIN tblusers AS User ON Comment.commented_by = User.user_id
					WHERE Comment.news_feed_id = $newsFeedID ORDER BY Comment.news_feed_id, Comment.created_on DESC";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);			
			return $resultArry;
		}	
		
	/*
		Feed Comment = Feed Image/ Video Commets.
		@input : Var
	*/
	function getNewsFeedAlbumComments($newsFeedID)
		{			
			$sql = "SELECT Comment.comment_id, Comment.comment, Comment.news_feed_id, TIMESTAMPDIFF(SECOND, Comment.created_on, now()) AS diff_second, TIMESTAMPDIFF(MINUTE, Comment.created_on, now()) AS diff_minute, TIMESTAMPDIFF(HOUR, Comment.created_on, now()) AS diff_hour, TIMESTAMPDIFF(DAY, Comment.created_on, now()) AS diff_day, CommentLike.like_id AS like_status,  User.user_id, User.first_name, User.last_name, 
					User.profile_image
					FROM tblnews_feed_comment AS Comment
					LEFT JOIN tbllike_news_feedcomment AS CommentLike ON Comment.comment_id = CommentLike.comment_id
					LEFT JOIN tblusers AS User ON Comment.commented_by = User.user_id
					WHERE Comment.news_feed_id IN($newsFeedID) ORDER BY Comment.news_feed_id, Comment.created_on DESC";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);			
			return $resultArry;
		}	
		
	
	/*
	`@newsFeedID : Array
	*/	
	function getGroupFeedComments($newsFeedID, $loginID = 0)
		{
			$newsFeedID = implode(',', $newsFeedID);
			$sql = "SELECT Comment.comment_id, Comment.comment, Comment.group_feed_id, TIMESTAMPDIFF(SECOND, Comment.created_on, now()) AS diff_second, TIMESTAMPDIFF(MINUTE, Comment.created_on, now()) AS diff_minute, TIMESTAMPDIFF(HOUR, Comment.created_on, now()) AS diff_hour, TIMESTAMPDIFF(DAY, Comment.created_on, now()) AS diff_day, CommentLike.like_id AS like_status,  User.user_id, User.first_name, User.last_name, 
					User.profile_image
					FROM tblgroup_news_feed_comment AS Comment
					LEFT JOIN tbllike_group_news_feedcomment AS CommentLike ON Comment.comment_id = CommentLike.comment_id AND CommentLike.user_id = $loginID 
					LEFT JOIN tblusers AS User ON Comment.commented_by = User.user_id
					WHERE Comment.group_feed_id IN($newsFeedID) ORDER BY Comment.group_feed_id, Comment.created_on DESC";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			$data = array();
			$commentID = array();
			
			foreach($resultArry as $commentIndex => $comment)
				{
					$data['comments'][$comment['group_feed_id']][] = $resultArry[$commentIndex];
					$commentID [] = $comment['comment_id'];
				}
			$data['commentLikes'] = $this->getGroupCommentsLikeCount($commentID);
			//$this->print_r($data);exit;
			return $data;
		}	
	
	/*
		Array : $commentID
		Return count of number of news feed comment like
	*/
	function getGroupCommentsLikeCount($commentID)
		{
			$commentID = implode(',', $commentID);
			$sql = "SELECT comment_id, count(like_id) AS totalLike FROM tbllike_group_news_feedcomment WHERE comment_id IN($commentID) GROUP BY comment_id";			
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			$data = array();
			foreach($resultArry as $likeIndex => $comment)
				{
					$data[$comment['comment_id']] = $resultArry[$likeIndex];
				}
			
			return $data;
		}
	
	/*
		Get news feed users comment likes
		Var : $commentID
	*/	
	function getGroupUsersCommentLikes($commentID)
		{
			$sql = "SELECT LK.user_id, USER.first_name, USER.last_name, USER.profile_image, college  
					FROM tbllike_group_news_feedcomment AS LK 
					LEFT JOIN tblusers AS USER ON LK.user_id = USER.user_id
					WHERE LK.comment_id = $commentID ORDER BY CONCAT(USER.first_name, ' ' ,USER.last_name)";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}
		
	/*
		$newsFeedID => Var
	*/
	function getSingleGroupFeedComments($newsFeedID)
		{						
			$sql = "SELECT Comment.comment_id, Comment.comment, Comment.group_feed_id, TIMESTAMPDIFF(SECOND, Comment.created_on, now()) AS diff_second, TIMESTAMPDIFF(MINUTE, Comment.created_on, now()) AS diff_minute, TIMESTAMPDIFF(HOUR, Comment.created_on, now()) AS diff_hour, TIMESTAMPDIFF(DAY, Comment.created_on, now()) AS diff_day, CommentLike.like_id AS like_status,  User.user_id, User.first_name, User.last_name, 
					User.profile_image
					FROM tblgroup_news_feed_comment AS Comment
					LEFT JOIN tbllike_group_news_feedcomment AS CommentLike ON Comment.comment_id = CommentLike.comment_id
					LEFT JOIN tblusers AS User ON Comment.commented_by = User.user_id
					WHERE Comment.group_feed_id = $newsFeedID ORDER BY Comment.group_feed_id, Comment.created_on DESC";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);			
			return $resultArry;
		}	
		/*
			Get news feed like status
		*/
		function getFeedLikeStatus($likeID)
			{
				$sql = "SELECT feed_id FROM tbllike_news_feed WHERE like_id  = $likeID";
				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				return $resultArry;
			}
			
		/*
			Get Group news feed like status
		*/
		function getGroupFeedLikeStatus($likeID)
			{
				$sql = "SELECT group_feed_id FROM tbllike_group_news_feed WHERE like_id  = $likeID";
				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				return $resultArry;
			}	
			
		/*
			Get news feed coment like status
		*/
		function getFeedCommentLikeStatus($likeID)
			{
				$sql = "SELECT comment_id FROM tbllike_news_feedcomment WHERE like_id  = $likeID";
				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				return $resultArry;
			}	
		
		
		/*
			Get news feed coment like status
		*/
		function getGroupFeedCommentLikeStatus($likeID)
			{
				$sql = "SELECT comment_id FROM tbllike_group_news_feedcomment WHERE like_id  = $likeID";
				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				return $resultArry;
			}
			
			
		/*
			Store news feed like info
		*/
		function likeNewsFeed($data)
		{
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tbllike_news_feed',$data, 0);
			if(!$this->id)
				{						
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());					
					return false;					
				}
			else
				return $this->id;	
		}
		
		/*
			Store Group news feed like info
		*/
		function likeGroupNewsFeed($data)
		{
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tbllike_group_news_feed',$data, 0);
			if(!$this->id)
				{						
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());					
					return false;					
				}
			else
				return $this->id;	
		}
		
		/*
			Store news feed comment like info
		*/
		function likeNewsFeedComment($data)
		{
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tbllike_news_feedcomment',$data, 0);
			if(!$this->id)
				{						
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());					
					return false;					
				}
			else
				return $this->id;	
		}
		
	/*
			Store Group news feed comment like info
		*/
		function likeGroupNewsFeedComment($data)
		{
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tbllike_group_news_feedcomment',$data, 0);
			if(!$this->id)
				{						
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());					
					return false;					
				}
			else
				return $this->id;	
		}
		
	public function getFeedImageLikeStatus($feedID, $userID)
			{
				$sql						=	"SELECT like_id FROM tbllike_news_feed WHERE feed_id = $feedID AND user_id = $userID";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return reset($resultArry);
			}
			
	public function getFeedLikeStatusDet($feedID, $userID)
			{
				$sql						=	"SELECT like_id FROM tbllike_news_feed WHERE feed_id = $feedID AND user_id = $userID";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return reset($resultArry);
			}		
	
	function getNewsFeedImageName($feedId)
		{			
			$sql = "SELECT feed_file FROM tblnews_feed WHERE feed_id = $feedId";							
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}
			
	function getGroupNewsFeedImageName($feedId)
		{			
			$sql = "SELECT feed_file FROM tblgroup_news_feed WHERE group_feed_id = $feedId";							
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}		

	function getRevstatus($log_id)
		{			
			$sql = "SELECT x.rev_status, x.rev_image_id, x.rev_date_time FROM tblreview_content x JOIN (SELECT n.rev_image_id, 
			MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON 
			y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time WHERE x.rev_image_id = 1110000+$log_id";	
				//print_r ($sql);die();
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;		
		}
}
?>