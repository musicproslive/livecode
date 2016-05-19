<?php
/**************************************************************************************
Created By 	:Arvind 
Created On	:02-08-2011
Purpose		:Video on Demand 
**************************************************************************************/
class videoOdemand extends siteclass{
	function getVodRecords($id){// to get the vod added by an is 
			$query		=	"SELECT d.*,i.*,cur.* FROM `tblvideo_ondemand` d JOIN `tblinstrument_master` i JOIN `tblcurrency_type` cur
					  WHERE `cur`.currency_id=d.`currency_id` AND 
					  	i.`instrument_id`=d.`instrument_id` ";
						
						if(!empty($id)){
							$query		.=	" AND d.`tutor_id`=$id "	;
						}
			$resultArry		=	$this->getdbcontents_sql($query,0);		
		return $resultArry;
	
	}

	function getCurrency(){
		$query			=	"SELECT  * FROM `tblcurrency_type`";
		$resultArry		=	$this->getdbcontents_sql($query,0);					
		return $resultArry;
	}
	
	function getCompletdVideos($id){
		$userId			=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];
		$sql			=	"SELECT * FROM `tbluser_class` WHERE `course_master_id`=$id ";
		$resultArry		=	$this->getdbcontents_sql($sql,0);					
		return $resultArry;
		


	}
	
	function setVod($data)
		{
			$insertFlag = 0;
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tblcourse_vod',$data, 1);
			if($this->id)
				{						
					if($this->db_update("tblcourse_master",array("vod_request"=>1),"course_master_id=".$data['course_id'],0))
						$insertFlag = 1;										
				}			
			if(!$insertFlag)
				{
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());					
					return false;
				}
			else
				return true;	
		}
	function getVideoOnDemand($data,$userId){				
				 
				 $query		=	"SELECT vod.`id`,vod.`sale_price`,course.`title`,ins.`name` AS instrument_name,ins.`instrument_image`,
				 				 CONCAT(user.first_name,' ',user.last_name) AS instructor_name, currency.symbol,CEILING(SUM(R.`rating`)/COUNT(R.`rated_by`)) AS rating 
				 				 FROM  `tblcourse_vod` vod JOIN `tblcourses` course ON vod.`course_id` = course.`course_id`
								 LEFT JOIN `tblusers` user ON user.`user_id` = course.`instructor_id`
								 LEFT JOIN `tblcourse_prices` price ON price.`id` = course.`price_code` 
								 LEFT JOIN `tblcurrency_type` currency ON currency.`currency_id` =  price.`currency_type`
								 LEFT JOIN `tblinstrument_master` ins ON ins.`instrument_id` = course.`instrument_id`
								 LEFT JOIN `tblcourse_ratings` R  ON  R.`course_id`= vod.`course_id`
								 WHERE vod.`status`=1 AND vod.`id` NOT IN (SELECT vod_id FROM tblcourse_vod_transaction_courses WHERE user_id = $userId)";	
				 
				 if(!empty($data["instrument_id"])){
					$query	.=	" AND ins.`instrument_id`= ".$data["instrument_id"];
					
				}
				if(!empty($data["txtCourse"])){
					$query	.=	" AND course.`title` like '%".trim($data["txtCourse"])."%'";
				}
				
				if(!empty($data["txtTut"])){
					$query	.=	" AND CONCAT(user.`first_name`,' ',user.last_name) LIKE '%".trim($data["txtTut"])."%'";
				}
					$query	.=	" GROUP BY vod.`course_id`";
				$result[1]				=	$this->create_paging("n_page",$query,$per_page=10);
				$result[0]				= 	$this->getdbcontents_sql($result[1]->finalSql(),0);
				
				//echo $query;exit;
				//print_r($result[0]);exit;
				return $result;
				
	}
	
	function getVideoOnDemandCart($videoId){
		if(!empty($videoId)){
				 $query		=	"SELECT vod.`id`,vod.`sale_price`,course.`title`, course.`description`,ins.`name` AS instrument_name,ins.`instrument_image`,
				 				 CONCAT(user.first_name,' ',user.last_name) AS instructor_name, currency.symbol, currency.currency_id 
				 				 FROM  `tblcourse_vod` vod JOIN `tblcourses` course ON vod.course_id = course.course_id
								 LEFT JOIN `tblusers` user ON user.user_id = course.instructor_id 
								 LEFT JOIN `tblcourse_prices` price ON price.id = course.price_code 
								 LEFT JOIN `tblcurrency_type` currency ON currency.currency_id =  price.currency_type
								 LEFT JOIN `tblinstrument_master` ins ON ins.instrument_id = course.instrument_id
								 WHERE vod.`status`=1";	

			$query		.=	" AND vod.`id` IN( ".$videoId." )";
			$result 	= 	$this->getdbcontents_sql($query);
			//($result);
			return $result;
		}	
	}
	
	function updateSubscription($id){
			$this->db_query("UPDATE `tblcourse_vod` SET `number_of_subscription`=`number_of_subscription`+1 WHERE `course_video_id`=$id",0);
	}
	
	function addStudentSubscription($userId,$courseId){
		$this->db_insert("tblvod_subscription",array("course_video_id"=>$courseId,"student_id"=>$userId,"created_on"=>date("Y-m-d H:i:s")),0);
	}
	
	function getVideoOnDemandSelected($data,$userId){
		// trans.`trans_amount`
				 $query		=	"SELECT trans.`id`,vod.sale_price as trans_amount,course.`title`,course.`course_code`,
				 				 ins.`name` AS instrument_name,ins.`instrument_image`,videos.video_link,
				 				 CONCAT(user.first_name,' ',user.last_name) AS instructor_name, currency.symbol,
								 CEILING(SUM(R.`rating`)/COUNT(R.`rated_by`)) AS rating 								 
				 				 FROM  `tblcourse_vod_transaction` trans
								 LEFT JOIN `tblcourse_vod_transaction_courses`  AS VTC ON trans.id = VTC.course_vod_transaction_id
								 LEFT JOIN `tblcourse_vod`  vod ON VTC.vod_id = vod.id
								 LEFT JOIN `tblcourses` course ON vod.course_id = course.course_id
								 LEFT JOIN `tblcourse_videos` videos ON videos.course_id = course.course_id AND videos.video_owner_id = course.instructor_id
								 LEFT JOIN `tblusers` user ON user.user_id = course.instructor_id 
								 LEFT JOIN `tblcourse_ratings` R  ON  R.`course_id`= vod.`course_id`
								 LEFT JOIN `tblcourse_prices` price ON price.id = course.price_code 
								 LEFT JOIN `tblcurrency_type` currency ON currency.currency_id =  price.currency_type
								 LEFT JOIN `tblinstrument_master` ins ON ins.instrument_id = course.instrument_id
								 WHERE vod.`status`=1 AND trans.`user_id`= $userId";	
				 
				 if(!empty($data["instrument_id"])){
					$query	.=	" AND ins.`instrument_id`= ".$data["instrument_id"];
					
				}
				if(!empty($data["txtCourse"])){
					$query	.=	" AND course.`title` like '%".trim($data["txtCourse"])."%'";
				}
				
				if(!empty($data["txtTut"])){
					$query	.=	" AND CONCAT(user.`first_name`,' ',user.last_name) LIKE '%".trim($data["txtTut"])."%'";
				}
					$query	.=	" GROUP BY vod.`course_id`";
								
				$result[1]				=	$this->create_paging("n_page",$query,$per_page=10);
				$result[0]				= 	$this->getdbcontents_sql($result[1]->finalSql(),0);
			
				return $result;
	}
		
	
}
?>