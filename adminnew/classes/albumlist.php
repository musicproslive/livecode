<?php
/***********************************************************************************
Created by :	Arun
Created on :	18/08/2011
Purpose    :	Album Page
/***********************************************************************************/
class albumlist extends siteclass
	{
		public function getAlbum($aid)
			{
				$iid						=	$_GET['id'];
				$id							=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];	
				$sql						=	"select cp.*,cu.image,cu.type from tblalbum_image as cu 
												left join tblalbum_comment as cp on cp.image_id=cu.image_id
												left join tblalbum as al on cu.album_id=al.album_id 
												where al.album_owner_id = $id and cu.album_id=$iid 
												ORDER BY cu.posted_on DESC limit 0,5";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}
		public function getFullAlbum($aid)
			{
				$iid						=	$_GET['id'];
				$id							=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];	
				$sql						=	"select cp.*,cu.image,cu.type from tblalbum_image as cu 
												left join tblalbum_comment as cp on cp.image_id=cu.image_id
												left join tblalbum as al on cu.album_id=al.album_id 
												where al.album_owner_id = $id and cu.album_id=$iid 
												ORDER BY cu.posted_on DESC";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}	
		
		public function getAlbumDet($aid)
			{
				$sql						=	"SELECT album_title, album_desc FROM tblalbum WHERE album_id = $aid";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return reset($resultArry);
			}
			
		
		public function getAlbumImageCover($aid)
			{
				
				$sql =	"SELECT A.*,trc.rev_status,trc.deletestatus,trc.rev_date_time, count(AI.album_id) AS tot FROM tblalbum AS A LEFT JOIN tblalbum_image AS AI ON A.album_id = AI.album_id AND type = 'image' LEFT JOIN tblreview_content AS trc ON trc.rev_image_id = AI.image_id AND trc.rev_album_id = AI.album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = trc.rev_image_id AND y.max_note_date = trc.rev_date_time WHERE A.album_owner_id = $aid AND trc.deletestatus !=0 AND trc.deletestatus != 0  AND album_type= 0 GROUP BY A.album_id";
				
				$resultArry	=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}
		
		public function getAlbumVideoCover($aid)
			{
				$sql						=	"SELECT A.*, count(AI.album_id) AS tot, MAX(substr(AI.image, (length(AI.image)-10), 11)) as youtubekey FROM tblalbum AS A 
												LEFT JOIN tblalbum_image AS AI ON A.album_id = AI.album_id  AND type = 'video'  
												WHERE A.album_owner_id = $aid  AND album_type= 1 GROUP BY A.album_id";
				$resultArry					=	$this->getdbcontents_sql($sql, 0);
				return $resultArry;
			}	
			
		public function getAlbumLikeStatus($likeID)
			{
				$sql						=	"SELECT album_id FROM tblalbum_like WHERE like_id = $likeID";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}
			
		public function getAlbumImageLikeStatus($likeID)
			{
				$sql						=	"SELECT image_id FROM tblalbum_image_like WHERE like_id = $likeID";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}	
			
		public function getAlbumImageCommentLikeStatus($likeID)
			{
				$sql						=	"SELECT comment_id FROM tblalbum_image_comment_like WHERE like_id = $likeID";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}		
						
		public function getAlbumLike($albumID)
			{
				$sql						=	"SELECT like_id,user_id FROM tblalbum_like WHERE album_id = $albumID";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}
			
			
		public function getAlbumImageLike($imageID)
			{
				$sql						=	"SELECT like_id,user_id FROM tblalbum_image_like WHERE image_id = $imageID";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}	
			
		public function getAlbumImageCommentLike($imageID, $userID)
			{
				$sql						=	"SELECT like_id FROM tblalbum_image_comment_like WHERE image_id = $imageID AND user_id = $userID";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return reset($resultArry);
			}	
				
		public function createAlbum($id)
			{
				//$id							=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];	
				$sql						=	"SELECT * from tblalbum where album_owner_id=".$id;
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				//$sqlcount					=	"SELECT COUNT(*) FROM tblalbum_image where album_id =".$resultArry[0]['album_id'];
				return $resultArry;
			}
			
		public function createAlbumGallery($id)
			{	
				//$sql						=	"SELECT * from tblalbum_image where album_id = $id and type = 'image' ORDER BY posted_on DESC";
				
				$sql						=	"SELECT a.*,x.rev_status,x.rev_date_time from tblalbum_image a LEFT JOIN tblreview_content x ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id,MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x	.rev_image_id AND y.max_note_date = x.rev_date_time where a.album_id = $id and a.type = 'image' AND x.rev_status !=0 ORDER BY a.posted_on DESC";
			//	print_r($sql);
			//	$sql						=	"SELECT a.*,x.status,x.deletestatus ,x.image_id,x.date_time,x.album_id from tblalbum_image a JOIN tblreview_content x ON a.image_id = x.image_id AND a.album_id = x.album_id JOIN (SELECT n.image_id, MAX(n.date_time) AS max_note_date FROM tblreview_content n GROUP BY n.image_id) y ON y.image_id = x.image_id AND y.max_note_date = x.date_time where a.album_id = $id and a.type = 'image' ORDER BY a.posted_on DESC";
				
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}
			
		public function getAlbumVideoGallery($id)
			{
				$sql						=	"SELECT *, substr(`image`, (length(`image`)-10), 11) as youtubekey from tblalbum_image where album_id = $id and type = 'video' ORDER BY posted_on DESC";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}	
			
		public function albumImage($albID)
			{
				$sql	=	"SELECT image_id, image from tblalbum_image where album_id = $albID AND type = 'image' ORDER BY posted_on DESC";
				$resultArry	 =	$this->getdbcontents_sql($sql, 0);
				$albumImage = array();
				foreach($resultArry as $image)
					{
						$albumImage[$image['image_id']] = $image['image'];
					}
				return $albumImage;
			}
		
		public function albumVideos($albID)
			{
				$sql	=	"SELECT image_id, image from tblalbum_image where album_id = $albID AND type = 'video' ORDER BY posted_on DESC";
				$resultArry	 =	$this->getdbcontents_sql($sql, 0);
				$albumImage = array();
				foreach($resultArry as $image)
					{
						$albumImage[$image['image_id']] = $image['image'];
					}
				return $albumImage;
			}		
			
		public function getAlbumTitle($id)
			{
				$sql						=	"SELECT album_title, album_image from tblalbum where album_id=".$id;
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}	
		public function countGallery($id)
			{
				$id							=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];
				$sql						=	"SELECT COUNT(*) FROM tblalbum where album_type=0 AND album_owner_id =".$id;
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				//print_r($resultArry);exit;
				return $resultArry;
			}	
		public function getCommentList($id)
			{
				$sql	=	"SELECT cm.*, u.user_id, u.first_name, u.last_name, u.profile_image 
							 FROM tblalbum_comment AS cm 
							 LEFT JOIN tblusers AS u ON cm.commented_by = u.user_id
							 WHERE album_id = $id ORDER BY cm.posted_on ASC 
							LIMIT 0 , 20";
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				//print_r($resultArry);exit;
				return $resultArry;
			}
			
		public function getImageCommentList($id)
			{
				/*$sql	=	"SELECT cm.*, u.user_id, u.first_name, u.last_name, u.profile_image 
							 FROM tblalbum_image_comments AS cm 
							 LEFT JOIN tblusers AS u ON cm.commented_by = u.user_id
							 WHERE comment_id = $id ORDER BY cm.commneted_date ASC 
							LIMIT 0 , 20";
				$resultArry					=	$this->getdbcontents_sql($sql,0);*/
							
			$sql = "SELECT Comment.image_comment_id, Comment.comment, Comment.image_id, TIMESTAMPDIFF(SECOND, Comment.commneted_date, now()) AS diff_second, TIMESTAMPDIFF(MINUTE, Comment.commneted_date, now()) AS diff_minute, TIMESTAMPDIFF(HOUR, Comment.commneted_date, now()) AS diff_hour, TIMESTAMPDIFF(DAY, Comment.commneted_date, now()) AS diff_day, CommentLike.like_id AS like_status,  User.user_id,User.login_id ,User.first_name, User.last_name, User.profile_image
					FROM tblalbum_image_comments AS Comment
					LEFT JOIN tblalbum_image_comment_like AS CommentLike ON Comment.image_comment_id = CommentLike.comment_id
					LEFT JOIN tblusers AS User ON Comment.commented_by = User.user_id					
					WHERE Comment.image_id = $id ORDER BY Comment.commneted_date DESC";
				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				//echo "<pre>"; print_r($sql);exit;
				return $resultArry;
			}
				
		public function updateAlbumtitle($data1,$data)
			{
				$sql						=	"update `tblalbum` set album_title ='".$data1['album_title']."' 
												WHERE album_id=".$data['id'];
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}
		public function deleteComment($albID)
			{
				$sql						=	"DELETE FROM  tblalbum_comment
												WHERE comment_id=".$albID;
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}				
		public function getAlbumImage($iID)
			{	//to list albun 
				$sql						=	"SELECT * from tblalbum_image where image_id=".$iID;
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}	
		public function updateAlbumImage($iID)
			{	//to set as albun image
				$sql1						=	"SELECT * from tblalbum_image where image_id=".$iID;
				$resultArry					=	$this->getdbcontents_sql($sql1,0);
				$sql						=	"update `tblalbum` set album_image ='".$resultArry[0]['image']."' 
												WHERE album_id=".$resultArry[0]['album_id'];
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}
		public function deleteImage($imgID, $type)
			{	//to delete albun image
				$delImg						=	"select * from tblalbum_image where image_id=".$imgID;
				$delImgArry					=	$this->getdbcontents_sql($delImg,0);
				if($type == 'LMT_IMG')
					@unlink("Uploads/Album/".$delImgArry[0]['image']); //Remove image from folder
				else
					@unlink("Uploads/Album/albumVideos".$delImgArry[0]['image']); //Remove image from folder
					
				//@unlink("Uploads/Album/thumbs/".$delImgArry[0]['image']); //Remove image from thumb folder
				//"delete from tblalbum_image where image_id=".$imgID;
				if($this->dbDelete_cond('tblalbum_image', "image_id=".$imgID))
					return true;
				else
					return false;	
			}
		public function deleteFullAlbum($delID)
			{	//to delete albun 
				$img	 	= 	"select * FROM `tblalbum_image` WHERE album_id='".$delID."'";
				$query 		= 	"delete FROM `tblalbum` WHERE album_id=".$delID;
				$query1 	= 	"delete FROM `tblalbum_image` WHERE album_id='".$delID."'";
				$result		=	$this->getdbcontents_sql($img);
				foreach($result as $key => $value)
					{
						@unlink("Uploads/Album/".$value['image']); //Remove image from folder
						//@unlink("Uploads/Album/thumbs/".$value['image']); //Remove image from thumb folder
					}
				if($this->dbDelete_cond('tblalbum_image', "album_id=".$delID) && $this->dbDelete_cond('tblalbum_comment', "album_id=".$delID) && $this->dbDelete_cond('tblalbum', "album_id=".$delID))
					return true;
				else
					return false;
			}		
		public function deleteAlbum($delID)
			{	//to delete albun 
				$img	 	= 	"select * FROM `tblalbum_image` WHERE album_id='".$delID."'";
				$query 		= 	"delete FROM `tblalbum` WHERE album_id=".$delID;
				$query1 	= 	"delete FROM `tblalbum_image` WHERE album_id='".$delID."'";
				$result		=	$this->getdbcontents_sql($img);
				foreach($result as $key => $value)
					{
						@unlink("Uploads/Album/".$value['image']); //Remove image from folder
						@unlink("Uploads/Album/thumbs/".$value['image']); //Remove image from thumb folder
					}
				$result		=	$this->getdbcontents_sql($query);
				$result1	=	$this->getdbcontents_sql($query1);
				return $resultArry;
			}
		function getAllGroup($ID)
			{
				$sql 			= 	"SELECT * from tblgroup_album where group_owner_id = $ID";
				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				return $resultArry;
			}	
		public function deletegroupAlbum($delID)
			{	//to delete groupalbun [Base]
				$img	 	= 	"select `group_image` FROM `tblgroup_album` WHERE id='".$delID."'";
				$query 		= 	"delete FROM `tblgroup_album` WHERE id=".$delID;
				//$query1 	= 	"delete FROM `tblalbum_image` WHERE album_id='".$delID."'";
				$result		=	$this->getdbcontents_sql($img);
				foreach($result as $key => $value)
					{
						@unlink("Uploads/events/Album/".$value['group_image']); //Remove image from folder
						@unlink("Uploads/events/Album/thumbs/".$value['group_image']); //Remove image from thumb folder
					}
				$result		=	$this->getdbcontents_sql($query);
				//$result1	=	$this->getdbcontents_sql($query1);
				return $resultArry;
			}
		public function getGroupAlbum($ID)
			{	//to get all group albums
				$sql 			= 	"SELECT * from tblgroup_image where tblgroup_album_id = $ID";
				$resultArry		=	$this->getdbcontents_sql($sql, 0);	
				return $resultArry;
			}
		public function deleteAlbumgroup($delID)
			{	//to delete groupalbun [inner page]
				$imgsrc	 	= 	"select `image` FROM `tblgroup_image` WHERE image_id='".$delID."'";
				$imgdel	 	= 	"delete  FROM `tblgroup_image` WHERE image_id='".$delID."'";
				$result		=	$this->getdbcontents_sql($imgsrc);
				foreach($result as $key => $value)
					{
						@unlink("Uploads/events/Album/".$value['image']); //Remove image from folder
						@unlink("Uploads/events/Album/thumbs/".$value['image']); //Remove image from thumb folder
					}
				$result			=	$this->getdbcontents_sql($imgdel);
				return $result;
			}
		public function updateGroupTitle($data)
			{	//to set as group albun Title
				$sql						=	"update `tblgroup_album` set group_title ='".$data['group_title']."' 
												WHERE id=".$data['imageId'];
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}
		public function updateAlbumgroup($data)
			{	//to set as group albun Image[Head Image]
				$sql1						=	"SELECT image_id,image,tblgroup_album_id from  tblgroup_image 
												where image_id=".$data['id'];
				$resultArry					=	$this->getdbcontents_sql($sql1,0);
				$sql						=	"update `tblgroup_album` set group_image ='".$resultArry[0]['image']."' 
												WHERE id=".$resultArry[0]['tblgroup_album_id'];
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}	
		// for video in user gallery 
		
		function getvideoAlbumList($id){
			$query						=	"SELECT * FROM `tblalbum` WHERE `album_type`=1 AND `album_owner_id`=$id ORDER BY `posted_on` DESC";
			$resultArry					=	$this->getdbcontents_sql($query,0);
			return $resultArry;
		}	
		
		function getVideoRec($albumId){
			$query		=	"SELECT * FROM `tblalbum_image` WHERE `album_id`=$albumId ";//AND `type`=1
			$resultArry	=	$this->getdbcontents_sql($query,0);
			return $resultArry;
		}
		
		function getComments($id){
			$query	=		"SELECT c.`comment`,u.`profile_image`,u.`first_name` from `tblalbum_comment` c JOIN `tblusers` u  
							WHERE u.`user_id`=c.`commented_by` AND c.`album_id`=$id ORDER BY c.`posted_on` DESC";
			$resultArry	=	$this->getdbcontents_sql($query,0);
			return $resultArry;
		}
		
		
		// for getting comments on videos 
		function getVideoComments($id){
			$query			=	"SELECT c.`comment`,u.`profile_image`,u.`first_name` 
								from `tblalbum_image_comments` c JOIN `tblusers` u  WHERE u.`user_id`=c.`commented_by` 
								AND c.`image_id`=$id ORDER BY c.`commneted_date` DESC";
			$resultArry		=	$this->getdbcontents_sql($query,0);
			return $resultArry;
		}
		function getImageComments($id){
			$query			=	"SELECT c.`comment`,u.`profile_image`,u.`first_name` 
								from `tblalbum_comment` c JOIN `tblusers` u  WHERE u.`user_id`=c.`commented_by` 
								AND c.`image_id`=$id ORDER BY c.`commneted_date` DESC";
			$resultArry		=	$this->getdbcontents_sql($query,0);
			return $resultArry;
		}	
		
		function getAlbumData($id){
			$query			=	"SELECT album_id, album_owner_id, album_title, album_image, DATE_FORMAT(posted_on, '%b %d %Y') AS posted_on FROM `tblalbum` WHERE `album_id`= $id" ;
			$resultArry		=	$this->getdbcontents_sql($query,0);
			return $resultArry;
		}
		
		function getCount($id){
			$query			=	"SELECT count(`image_id`) FROM `tblalbum_image` WHERE `album_id`= $id";
			$resultArry		=	$this->getdbcontents_sql($query,0);
			return $resultArry[0]["count(`image_id`)"];
		}
		public function getPopimage($id)
			{
				$sql						=	"SELECT * from tblalbum_image where image_id=".$id;
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}	
		public function getPopimageList($id)
			{
				$sql						=	"SELECT * from tblalbum_image where album_id=".$id;
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}	
		public function deleteAlbumpop($imgID)
			{	//to delete albun image
				$delImg						=	"select * from tblalbum_image where image_id=".$imgID;
				$delImgArry					=	$this->getdbcontents_sql($delImg,0);
				@unlink("Uploads/Album/".$delImgArry[0]['image']); //Remove image from folder
				@unlink("Uploads/Album/thumbs/".$delImgArry[0]['image']); //Remove image from thumb folder
				$sql						=	"delete from tblalbum_image where image_id=".$imgID;
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}
		public function updateAlbumicon($iID)
			{	//to set as albun image
				$sql1						=	"SELECT * from tblalbum_image where image_id=".$iID;
				$resultArry					=	$this->getdbcontents_sql($sql1,0);
				$sql						=	"update `tblalbum` set album_image ='".$resultArry[0]['image']."' 
												WHERE album_id=".$resultArry[0]['album_id'];
				$resultArry					=	$this->getdbcontents_sql($sql,0);
				return $resultArry;
			}
		public function getVideoAlbum($id){
				
				$query		=	"SELECT `image_id`,`image` FROM `tblalbum_image` WHERE `album_id`=$id";
				$rec		=	$this->getdbcontents_sql($query,0);
				
				for($i=0;$i<count($rec);$i++){
					$id			=	 $rec[$i]["image_id"];	
					$query		=	"DELETE  FROM  `tblalbum_image_comments` WHERE `image_id`=$id";
					//$path		=	
					$path		=	str_replace("classes","Uploads",dirname(__FILE__))."/album/".$rec[$i]["image"];//unlink(ROOT_URL."Uploads/album/".$path);
					//$this->db_query($query,0);			
					exit;	
				}
		}
		
		public function getImagesOfGroup($id){
			$query		=	"SELECT * FROM `tblgroup_news_feed` WHERE `group_id`=$id AND `feed_type`='LMT_PHOTO'";
			$resultArry	=	$this->getdbcontents_sql($query,0);
			return $resultArry;
		}
		
		/// function for iamege grouping 
		
		public function getImagesOfGroupNew($id){
			$query		=	"SELECT * FROM `tblgroup_news_feed` WHERE `group_id`=$id AND `feed_type`='LMT_PHOTO'";
			$resultArry	=	$this->getdbcontents_sql($query,0);
			for($i=0;$i<count($resultArry);$i++){
				$data[$resultArry[$i]["group_feed_id"]]		=	$resultArry[$i]["feed_file"];
			}
			return $data;
		}
		
		// to get the image form group depending on the id 
		public function getImagesOfGroupById($id){
			$query			=	"SELECT * FROM `tblgroup_news_feed` WHERE `group_feed_id`=$id";
			$resultArry		=	$this->getdbcontents_sql($query,0);
			return $resultArry;
		}
		
		public function getImagesOfGroupByIdComments($id){
			$query			=	"SELECT gc.*,u.`first_name`,u.`last_name`,u.`user_id`,u.`profile_image` FROM `tblgroup_news_feed_comment` gc
									JOIN `tblusers` u   WHERE gc.`group_feed_id`=$id AND `commented_by`=u.`user_id`";
			$resultArry		=	$this->getdbcontents_sql($query,0);
			return $resultArry;
		}
		
		/// function to  get vidoe type  
		
		public function getVideoOfGroupNew($id){
			$query		=	"SELECT * FROM `tblgroup_news_feed` WHERE `group_id`=$id AND `feed_type`='LMT_VIDEO'";
			$resultArry	=	$this->getdbcontents_sql($query,0);			
			return $resultArry;
		}
		
		// function get the video 
		
		public function getVideogroup($id){
			$query			=	"SELECT * FROM `tblgroup_news_feed` WHERE `group_feed_id`=$id ORDER BY `group_feed_id` DESC";
			$resultArry		=	$this->getdbcontents_sql($query,0);					
			return $resultArry;
		}
		
		public function getCommentLsitGroup($id){		
			$query			=	"SELECT gc.*,u.`first_name`,u.`last_name`,u.`user_id`,u.`profile_image` FROM `tblgroup_news_feed_comment` gc
									JOIN `tblusers` u   WHERE gc.`group_feed_id`=$id AND `commented_by`=u.`user_id`";
			$resultArry		=	$this->getdbcontents_sql($query,0);			
			return $resultArry;
		}
		
	function getAllNewsfeedData($offset=0,$count=0){
		/*  $query= "SELECT a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time, x.totaldeleted FROM tblreview_content x LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND yy.max_note_date1 = x.rev_date_time ORDER BY rev_date_time DESC" ;
		*/
			
			if($count!=0)
			{
				$query= "SELECT u.profile_image, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, 
				x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time, x.imageuser, x.totaldeleted 
				FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id 
				JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY 
				nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT 
				JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN 
				(SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y 
				ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON 
				c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM 
				tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND 
				yy.max_note_date1 = x.rev_date_time where rev_status = 2 AND deletestatus = 1 ORDER BY rev_date_time DESC LIMIT $offset,$count" ;
			}
			else
			{
				$query= "SELECT u.profile_image, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, 
				x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time, x.imageuser, x.totaldeleted 
				FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id 
				JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY 
				nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT 
				JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN 
				(SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y 
				ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON 
				c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM 
				tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND 
				yy.max_note_date1 = x.rev_date_time where rev_status = 2 AND deletestatus = 1 ORDER BY rev_date_time DESC" ;
			}
		//	$query	=	"SELECT a.*,x.status,x.deletestatus ,x.image_id,x.date_time,x.album_id from tblalbum_image a JOIN tblreview_content x ON a.image_id = x.image_id AND a.album_id = x.album_id JOIN (SELECT n.image_id, MAX(n.date_time) AS max_note_date FROM tblreview_content n GROUP BY n.image_id) y ON y.image_id = x.image_id AND y.max_note_date = x.date_time where a.album_id = $id and a.type = 'image' ORDER BY a.posted_on DESC";
			
			
			$resultArry		=	$this->getdbcontents_sql($query,0);			
			return $resultArry;
		}
		
		function getTotalNewsfeedData(){
			
			
				$query= "SELECT u.profile_image, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, 
				x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time, x.imageuser, x.totaldeleted 
				FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id 
				JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY 
				nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT 
				JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN 
				(SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y 
				ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON 
				c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM 
				tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND 
				yy.max_note_date1 = x.rev_date_time where rev_status = 2 AND deletestatus = 1 ORDER BY rev_date_time DESC" ;
			
			$resultArry		=	$this->getdbcontents_sql($query,0);	
			
			
				
			return count($resultArry);
		}
		
		function getImageData($id){
			//"SELECT * FROM referencestories WHERE created IN(SELECT MAX(created) FROM referencestories GROUP BY custid)";
			
			$query			=	"SELECT * FROM `tblreview_content` as trc WHERE rev_date_time IN (SELECT MAX(rev_date_time) FROM tblreview_content as n where n.rev_image_id = $id GROUP BY rev_image_id)";
			$resultArry		=	$this->getdbcontents_sql($query,0);
			//print_r ($resultArry);
			return $resultArry;
		}
		
	/*	public function updateImageStatus($data1,$data){
	
			$sql =	"update `tblreview_content` set rev_status = '".$data1['rev_status']."', approved_by ='".$data1['approved_by']."' WHERE id=".$data;
			$resultArry	=	$this->getdbcontents_sql($sql,0);
			return $resultArry;
		}*/
		
		/*public function insertImageStatus($data1,$data){
	
			$sql =	"update `tblreview_content` set status = '".$data1['status']."', approved_by ='".$data1['approved_by']."' WHERE id=".$data;
			$sql1 = "insert into `tblreview_content` values('image_id','date_time',''".$data1['approved_by'].",'".$data1['status']."','deleteimagecomment','deleteoption','deletestatus')";
			$resultArry	=	$this->getdbcontents_sql($sql,0);
			return $resultArry;
		}*/
		
	/*	public function updateImagedelStatus($data1,$data){
			//$sql ="update `tblreview_content` set deletestatus = 0 , deleteimagecomment = '".$data1['deleteComment']."' , deleteoption ='".$data1['deleteoptions']."' WHERE id=".$data;
		//	echo "<pre>";
			//print_r($data1);
			$sql ="INSERT INTO tblreview_content VALUES ('','".$data1['rev_image_id']."','".$data1['rev_date_time']."' ,'".$data1['approved_by']."' ,0,'".$data1['deleteComment']."','".$data1['deleteoptions']."' ,0,'".$data1['imageuser']."','".$data1['totaldeleted']."')";
			
			//print_r ($sql);
			$resultArry					=	$this->getdbcontents_sql($sql,0);
			return $resultArry;
		}*/
		
		/*public function updateImagedelStatus2($data,$data1){
			
			//$sql ="INSERT INTO tblreview_content VALUES ('','".$data1['image_id']."','".$data1['date_time']."' ,'".$data1['approved_by']."' ,0,'".$data1['deleteComment']."','".$data1['deleteoptions']."' ,0,'".$data1['imageuser']."','".$data1['totaldeleted']."')";
			
			$sql = "update `tblreview_content` set totaldeleted = '".$data."' WHERE imageuser='".$data1."'";
			
			//print_r ($sql);
			$resultArry					=	$this->getdbcontents_sql($sql,0);
			return $resultArry;
		}
		*/
		public function getImageUsername($data){
				
			$sql =	"SELECT tul.user_name,tul.login_id,tnf.feed_owner_id FROM `tbluser_login` as  tul join `tblnews_feed` as tnf on tul.login_id = tnf.feed_owner_id where tnf.feed_id =".$data;
			
			$resultArry =	$this->getdbcontents_sql($sql,0);			
			return $resultArry;
		}
		
		function getApprovedImages($offset=0,$count=0){
			//$query			=	"SELECT c.feed_id, x.rev_status,x.deletestatus ,x.totaldeleted, c.feed_file,x.rev_date_time FROM tblnews_feed c JOIN tblreview_content x ON x.rev_image_id = c.feed_id JOIN (SELECT n.rev_image_id,n.rev_status,MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id ) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time where x.rev_status=1";
			
			if($count!=0)
			{
				$query			=	"SELECT u.profile_image,u.created, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time, x.imageuser,x.deleteimagecomment, x.totaldeleted FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND yy.max_note_date1 = x.rev_date_time where x.rev_status=1 AND x.rev_image_file != '' AND (x.deletestatus=1 OR x.deletestatus='') ORDER BY rev_date_time DESC LIMIT $offset,$count" ;
			}
			else
			{
				$query			=	"SELECT u.profile_image,u.created, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time, x.imageuser,x.deleteimagecomment, x.totaldeleted FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND yy.max_note_date1 = x.rev_date_time where x.rev_status=1 ORDER BY rev_date_time DESC" ;
			}
			$resultArry		=	$this->getdbcontents_sql($query,0);
			return $resultArry;
		}
		
		function getTotalApprovedImages(){
			$query			=	"SELECT u.profile_image,u.created, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time, x.imageuser,x.deleteimagecomment, x.totaldeleted FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND yy.max_note_date1 = x.rev_date_time where x.rev_status=1 ORDER BY rev_date_time DESC" ;
			
			$resultArry		=	$this->getdbcontents_sql($query,0);
			return count($resultArry);
			
			
		}
		
		function getDispprovedImages($id){
			//$query			=	"SELECT c.feed_id, x.rev_status,x.deletestatus ,x.totaldeleted, c.feed_file,x.rev_date_time FROM tblnews_feed c JOIN tblreview_content x ON x.rev_image_id = c.feed_id JOIN (SELECT n.rev_image_id,n.rev_status,MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id ) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time where x.rev_status=0";
			
			$query			=	"SELECT u.profile_image, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time,x.deleteimagecomment, x.imageuser, x.totaldeleted FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND yy.max_note_date1 = x.rev_date_time where x.rev_status=0 ORDER BY rev_date_time DESC" ;
			$resultArry		=	$this->getdbcontents_sql($query,0);
			
			return $resultArry;
		}
		
		function getDelImages($id){
			//$query			=	"SELECT u.profile_image,u.created, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus ,x.deleteoption, x.rev_date_time, x.imageuser,x.deleteimagecomment,x.rev_ip, x.caption, x.totaldeleted FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND 	y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND yy.max_note_date1 = x.rev_date_time where x.deletestatus=0 ORDER BY rev_date_time DESC " ;
			
			$query	="SELECT u.profile_image,u.created, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,
			x.rev_image_file, x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus ,x.deleteoption, x.rev_date_time, x.imageuser,x.deleteimagecomment,x.rev_ip, x.caption, x.totaldeleted FROM tblreview_content x LEFT JOIN tblusers u 
			ON 1110000+u.login_id = x.rev_image_id LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND 
			a.album_id = x.rev_album_id LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id where x.deletestatus=0 ORDER BY rev_date_time DESC " ;
			
			$resultArry		=	$this->getdbcontents_sql($query,0);
			
			return $resultArry;
		}
		
		function getLog(){
			$query			=	"SELECT trc.id,trc.approved_by,trc.rev_status, trc.deletestatus, trc.rev_date_time, tnf.feed_id, tnf.feed_file, a.image, trc.deleteimagecomment, trc.deleteoption, trc.rev_ip, tnf.created_on, trc.rev_album_id FROM `tblreview_content` trc LEFT JOIN `tblnews_feed` tnf on tnf.feed_id = trc.rev_image_id LEFT JOIN tblalbum_image a ON a.image_id = trc.rev_image_id AND a.album_id = trc.rev_album_id ORDER BY rev_date_time DESC";
			$resultArry		=	$this->getdbcontents_sql($query,0);
			//print_r($query);
			return $resultArry;
		}
		
		function getDisapprovedusers(){
			//$sql					= 	"SELECT count(trc.deletestatus) from `tblreview_content` as trc WHERE 				imageuser='""'" AND trc.deletestatus= 1;
			
			$query			=	"SELECT trc.rev_image_id, trc.rev_image_file,trc.imageuser,trc.rev_date_time,trc.rev_status ,trc.deletestatus, trc.deleteimagecomment, trc.deleteoption,trc.totaldeleted,ul.is_deleted,ul.user_name FROM `tblreview_content` as trc LEFT JOIN `tbluser_login` as ul on ul.user_name = trc.imageuser JOIN (SELECT n.rev_image_id,n.imageuser, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n where n.deletestatus=0 GROUP BY n.imageuser) y ON y.imageuser = trc.imageuser AND y.max_note_date = trc.rev_date_time where trc.deletestatus=0 ORDER BY rev_date_time DESC" ;
			$resultArry		=	$this->getdbcontents_sql($query,0);
			//echo "<pre>";
			//print_r ($resultArry);
			return $resultArry;
		}
		
		function blockUsers($data){
			//$query			=	"SELECT ul.user_name, ul.is_deleted, trc.imageuser FROM tbluser_login as ul join tblreview_content as trc WHERE ul.user_name = trc.imageuser" ;
			
			$query			=	"update `tbluser_login` set is_deleted = 1  WHERE user_name='".$data."'";
			$resultArry		=	$this->getdbcontents_sql($query,0);
			//print_r ($query);
			return $resultArry;
		}		
		
		function UnblockUsers($data){
			
			$query			= "update `tbluser_login` set is_deleted = 0  WHERE user_name='".$data."'";
			
			$resultArry		=	$this->getdbcontents_sql($query,0);	
			print_r ($query);
			return $resultArry;
		}
		
		public function updateImagetotaldeleted($imguser,$imgDel){
			
			$sql = "update `tblreview_content` set totaldeleted = '".$imgDel."' WHERE imageuser='".$imguser."'";
			
			//print_r ($sql);
			$resultArry					=	$this->getdbcontents_sql($sql,0);
			return $resultArry;
		}
		
		public function getImageUser($data){
		
			$sql1 =	"SELECT tul.user_name FROM `tbluser_login` as  tul where tul.login_id =".$data;
			
			$resultArry1 =	$this->getdbcontents_sql($sql1,0);			
			return $resultArry1;
		}
		
		public function getNewsData(){
		
			$sql1 =	"SELECT * FROM tblnews_feed";
			
			$resultArry1 =	$this->getdbcontents_sql($sql1,0);			
			return $resultArry1;
		}
		
		public function getUserloginData(){
		
			$sql1 =	"SELECT * FROM tblusers";
			
			$resultArry1 =	$this->getdbcontents_sql($sql1,0);			
			return $resultArry1;
		}
		
		public function getUsnm($id){
		
			$sql =	"SELECT tul.user_name FROM `tbluser_login` as  tul where tul.login_id =".$id;
			
			$resultArry =	$this->getdbcontents_sql($sql,0);
				//print_r ($resultArry);
			return $resultArry;
		}
		
		public function getAlbmData(){
		
			$sql1 =	"SELECT * FROM tblalbum_image";
			
			$resultArry1 =	$this->getdbcontents_sql($sql1,0);			
			return $resultArry1;
		}
		
		
		function activeSocialmedia($data){
			
			
			$query			=	"update `tblusers` set social_media = 1  WHERE login_id=$data";
			
			$resultArry		=	$this->getdbcontents_sql($query,0);
			//print_r ($query);
			return $resultArry;
		}	
		
		function inactiveSocialmedia($data){
			
			
			$query			=	"update `tblusers` set social_media = 0  WHERE login_id=$data";
			
			$resultArry		=	$this->getdbcontents_sql($query,0);
			//print_r ($query);
			return $resultArry;
		}

		function getLoginid($data){
			//$data=412;
			$query			=	"select login_id from tblusers where user_id=$data";
			$resultArry		=	$this->getdbcontents_sql($query,0);			
			return $resultArry;		
		}
		
		function getUserdata($data){
			//$data=412;
			$query			=	"select imageuser,totaldeleted from tblreview_content where image_owner_id=$data";
			$resultArry		=	$this->getdbcontents_sql($query,0);			
			return $resultArry;		
		}
		
		function updateReviewProfileImage($data,$data1){
				//$data = "img";
				//$data1= 506;
			$query			=	"update `tblreview_content` set rev_image_file  ='".$data."', rev_status=2, deletestatus = 1  WHERE rev_image_id = 1110000+$data1";
			$resultArry		=	$this->getdbcontents_sql($query,0);
			//print_r ($query);die();
			return $resultArry;
		}	
		
		function insertnewReviewProfileImage($data){			
				$img_id = 1110000 + $data['image_owner_id'];				
				$rev_date_time = date("Y-m-d H:i:s");				
				$rev_a['rev_image_id'] = $img_id;				
				$rev_a['rev_date_time'] = date("Y-m-d H:i:s");	
				$rev_a['caption'] 		= 	'profile-image updated';			
				$rev_a['image_owner_id'] 	= $data['image_owner_id'];	
				$rev_a['rev_ip'] 		= $this->getClientIP();
				
				/*$query			=	"INSERT  `tblreview_content` set rev_image_file  ='".$data."', rev_status=2, deletestatus = 1  WHERE rev_image_id = 1110000+$data1";*/
			
				$query = "INSERT INTO tblreview_content VALUES ('','".$rev_a['rev_image_id']."','".$data['rev_image_file']."','pictureimage','".$rev_a['rev_date_time']."' ,'','',2,'','','1','".$rev_a['caption']."','".$data['imageuser']."','".$rev_a['image_owner_id']."','".$data['totaldeleted']."','".$rev_a['rev_ip']."' )";
				
				//echo "<pre>";	print_r ($query);die();
				$resultArry		=	$this->getdbcontents_sql($query,0);				
				return $resultArry;
		}
		
		function getImageRevStatus($data){				
			/* $query			=	"select rev_status from `tblreview_content` WHERE rev_image_id = $data";*/
			$query			=	"select trc.rev_status,trc.rev_date_time,trc.rev_image_id from `tblreview_content` as trc JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = trc.rev_image_id AND y.max_note_date = trc.rev_date_time where trc.rev_image_id = $data";	
			$resultArry		=	$this->getdbcontents_sql($query,0);
			//print_r ($query);die();
			return $resultArry;
		}	
		
		function getClientIP()
			{
				return $_SERVER['REMOTE_ADDR'];
			}
		
		public	function updateProfileimage($data){
				//$data = "img";
				//$data1= 506;
			$query			=	"update `tblusers` set profile_image ='no-profile-image' WHERE login_id = $data";
			$resultArry		=	$this->getdbcontents_sql($query,0);
			//print_r ($query);
			return $resultArry;
		}
	}
?>