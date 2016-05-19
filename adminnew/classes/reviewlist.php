<?php
/***********************************************************************************
Created by :	Arun
Created on :	18/08/2011
Purpose    :	Album Page
/***********************************************************************************/
class reviewlist extends siteclass
	{
		function getAllNewsfeedData(){
		/*  $query= "SELECT a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time, x.totaldeleted FROM tblreview_content x LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND yy.max_note_date1 = x.rev_date_time ORDER BY rev_date_time DESC" ;
		*/
			
			$query= "SELECT u.profile_image, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time, x.imageuser, x.totaldeleted FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND yy.max_note_date1 = x.rev_date_time ORDER BY rev_date_time DESC" ;
			
		//	$query	=	"SELECT a.*,x.status,x.deletestatus ,x.image_id,x.date_time,x.album_id from tblalbum_image a JOIN tblreview_content x ON a.image_id = x.image_id AND a.album_id = x.album_id JOIN (SELECT n.image_id, MAX(n.date_time) AS max_note_date FROM tblreview_content n GROUP BY n.image_id) y ON y.image_id = x.image_id AND y.max_note_date = x.date_time where a.album_id = $id and a.type = 'image' ORDER BY a.posted_on DESC";
			
			
			$resultArry		=	$this->getdbcontents_sql($query,0);
			//echo "<pre/>";
			//print_r( $query );
			return $resultArry;
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
		
		function getApprovedImages($id){
			//$query			=	"SELECT c.feed_id, x.rev_status,x.deletestatus ,x.totaldeleted, c.feed_file,x.rev_date_time FROM tblnews_feed c JOIN tblreview_content x ON x.rev_image_id = c.feed_id JOIN (SELECT n.rev_image_id,n.rev_status,MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id ) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time where x.rev_status=1";
			
			$query			=	"SELECT u.profile_image,u.created, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time, x.imageuser,x.deleteimagecomment, x.totaldeleted FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND yy.max_note_date1 = x.rev_date_time where x.rev_status=1 ORDER BY rev_date_time DESC" ;
			$resultArry		=	$this->getdbcontents_sql($query,0);
			
			return $resultArry;
		}
		
		function getDispprovedImages($id){
			//$query			=	"SELECT c.feed_id, x.rev_status,x.deletestatus ,x.totaldeleted, c.feed_file,x.rev_date_time FROM tblnews_feed c JOIN tblreview_content x ON x.rev_image_id = c.feed_id JOIN (SELECT n.rev_image_id,n.rev_status,MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id ) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time where x.rev_status=0";
			
			$query			=	"SELECT u.profile_image, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time,x.deleteimagecomment, x.imageuser, x.totaldeleted FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND yy.max_note_date1 = x.rev_date_time where x.rev_status=0 ORDER BY rev_date_time DESC" ;
			$resultArry		=	$this->getdbcontents_sql($query,0);
			
			return $resultArry;
		}
		
		function getDelImages($id){
			//$query			=	"SELECT c.feed_id, x.rev_status,x.deletestatus ,x.totaldeleted, c.feed_file,x.rev_date_time FROM tblnews_feed c JOIN tblreview_content x ON x.rev_image_id = c.feed_id JOIN (SELECT n.rev_image_id,n.rev_status,MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id ) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time where x.deletestatus=0";
			
			$query			=	"SELECT u.profile_image,u.created, a.image_id, a.image, c.feed_id, c.feed_file,x.rev_image_id,x.rev_image_file, x.rev_file_type,x.rev_album_id, x.rev_status, x.deletestatus , x.rev_date_time, x.imageuser,x.deleteimagecomment, x.totaldeleted FROM tblreview_content x LEFT JOIN tblusers u ON 1110000+u.login_id = x.rev_image_id JOIN (SELECT nnn.rev_image_id, MAX(nnn.rev_date_time) AS max_note_date3 FROM tblreview_content nnn GROUP BY nnn.rev_image_id) yyy ON yyy.rev_image_id = x.rev_image_id AND yyy.max_note_date3 = x.rev_date_time LEFT JOIN tblalbum_image a ON a.image_id = x.rev_image_id AND a.album_id = x.rev_album_id JOIN (SELECT n.rev_image_id, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n GROUP BY n.rev_image_id) y ON y.rev_image_id = x.rev_image_id AND y.max_note_date = x.rev_date_time LEFT JOIN tblnews_feed c ON c.feed_id = x.rev_image_id JOIN (SELECT nn.rev_image_id, MAX(nn.rev_date_time) AS max_note_date1 FROM tblreview_content nn GROUP BY nn.rev_image_id) yy ON yy.rev_image_id = x.rev_image_id AND yy.max_note_date1 = x.rev_date_time where x.deletestatus=0 ORDER BY rev_date_time DESC " ;
			
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
		
		function updateReviewProfileImage($data,$data1){
				//$data = "img";
				//$data1= 506;
			$query			=	"update `tblreview_content` set rev_image_file  ='".$data."'  WHERE rev_image_id = 111000+$data1";
			$resultArry		=	$this->getdbcontents_sql($query,0);
			//print_r ($query);
			return $resultArry;
		}
		
	}
?>