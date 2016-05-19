<?php
/**************************************************************************************
Created By 	:
Created On	:16-07-2011
Purpose		:Class for Course 
**************************************************************************************/

class userClass extends siteclass
	{
	
		public function addNewClass($classDetails)
			{
				$this->dbStartTrans();				
				$this->id =	$this->db_insert('tbluser_class',$classDetails, false);				
				if(!$this->id)
					{						
						$this->dbRollBack();
						$this->setPageError($this->getDbErrors());
						return false;					
					}
				else
					return $this->id;	
			}
			
		public function updateClass($classDetail, $id)
		{			
			$data =	$this->db_update('tbluser_class',$classDetail,"class_id=$id");
			if(!$data) 	
				{
					$this->setPageError($this->getDbErrors());
					return false;
				}		
				else return true;
		}		
			
		public function getTutorClasses($courseID)
			{
				$sql = "SELECT class_id, class_name , class_date, start_time FROM tbluser_class WHERE course_master_id = $courseID AND is_deleted = 0";
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				return $resultArry;
			}
		public function getClassCount($courseID)
			{
				$sql = "SELECT count(class_id) AS classCount FROM tbluser_class WHERE course_master_id = $courseID AND is_deleted = 0";
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				return $resultArry;
			}
		public function getClassDetail($courseID)
			{
				$sql = "SELECT class_id, course_master_id, class_name, description,DATE_FORMAT(end_time, '%b %d %Y')
						AS end_date,start_time, DATE_FORMAT(class_date, '%b %d %Y') AS class_date, 
						DATE_FORMAT((CONCAT(class_date, ' ', start_time)), '%b %d %Y %h:%i %p') AS startDateTime, 
						DATE_FORMAT(start_time, '%h:%i %p') AS startTime , DATE_FORMAT(end_time, '%h:%i %p') AS end_time, 
						about_class_file 
						FROM tbluser_class WHERE course_master_id = $courseID AND is_deleted = 0  
						ORDER BY class_date asc";
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				return $resultArry;
			}
			
		/*public function getClassDetail($courseID, $serverOffset = '+00:00', $userOffset = '+00:00')
			{
				$sql = "SELECT CL.class_id, CL.course_master_id, CL.class_name, CL.description, 
					DATE_FORMAT(CONVERT_TZ(CONCAT(CL.class_date,' ', CL.start_time),  '$userOffset', 
					(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = CS.time_zone_id)), 
					'%b %d %Y %h:%i %p') AS startDateTime,
						DATE_FORMAT(CONVERT_TZ(CONCAT(CL.class_date,' ', CL.start_time),  '$userOffset', 
					(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = CS.time_zone_id)), 
					'%h:%i %p') AS startTime, 
					DATE_FORMAT(CONVERT_TZ(CONCAT(CL.class_date,' ', CL.end_time),  '$userOffset', 
					(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = CS.time_zone_id)), 
					'%h:%i %p') AS end_time, about_class_file
						FROM tbluser_class AS CL
						LEFT JOIN tblcourse_master AS CS ON CL.course_master_id = CS.course_master_id
						
						WHERE CL.course_master_id = $courseID AND CL.is_deleted = 0  
						ORDER BY CL.class_date asc";
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				return $resultArry;
			}	*/
		
		public function getCClassDetail($classID)
			{
				$sql = "SELECT class_id, class_name, description, DATE_FORMAT((CONCAT(class_date, ' ', start_time)), '%b %d %Y %h:%i %p')
					   AS startDateTime, DATE_FORMAT(start_time, '%h:%i %p') AS startTime , DATE_FORMAT(class_date, '%b:%d %Y') AS class_date,
					   DATE_FORMAT(end_time, '%b:%d %Y') AS end_date,
					   DATE_FORMAT(end_time, '%h:%i %p') AS end_time, about_class_file FROM tbluser_class WHERE class_id = $classID";
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				return $resultArry;
			}	
		
		/*
			Get class details based on students time zone.
		*/	
		public function getStudentsClassDetail($courseID, $courseOffset = '+00:00', $visitorOffset = '+00:00')
			{
				$sql = "SELECT class_id, course_master_id, class_name, description,DATE_FORMAT(end_time, '%b %d %Y')
						AS end_date,start_time, DATE_FORMAT(class_date, '%b %d %Y') AS class_date,
						DATE_FORMAT((CONCAT(class_date, ' ', start_time)), '%b %d %Y %h:%i %p') AS startDateTime, 
						DATE_FORMAT(start_time, '%h:%i %p') AS startTime , DATE_FORMAT(end_time, '%h:%i %p') AS end_time, 
						about_class_file, DATE_FORMAT((CONVERT_TZ(Concat(class_date,' ', start_time), '$courseOffset', 
						'$visitorOffset') ),'%b %d %Y %h:%i %p') as classDateTime  
						FROM tbluser_class 
						WHERE course_master_id = $courseID AND is_deleted = 0  order by class_date asc";
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				return $resultArry;
			}	
			
		/*public function getStudentsClassDetail($courseID, $courseOffset = '+00:00', $visitorOffset = '+00:00', $tutorOffset = '+00:00')
			{//echo $visitorOffset; exit;
				//echo date('H:i:s');exit;
				$sql = "SELECT CL.class_id, CL.course_master_id, CL.class_name, CL.description, 
						DATE_FORMAT(CONVERT_TZ(CONCAT(CL.class_date,' ', CL.start_time),  '$tutorOffset', 
						(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = CS.time_zone_id)), 
						'%b %d %Y %h:%i %p') AS startDateTime,
						DATE_FORMAT(CONVERT_TZ(CONCAT(CL.class_date,' ', CL.start_time),  '$tutorOffset', 
						(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = CS.time_zone_id)), 
						'%h:%i %p') AS startTime, 
						DATE_FORMAT(CONVERT_TZ(CONCAT(CL.class_date,' ', CL.end_time),  '$tutorOffset', 
						(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = CS.time_zone_id)), 
						'%h:%i %p') AS end_time, CL.about_class_file, DATE_FORMAT((CONVERT_TZ(Concat(CL.class_date,' ',  
						CL.start_time), (SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = CS.time_zone_id), '$visitorOffset') ),'%b %d %Y %h:%i %p') as classDateTime  
						FROM tbluser_class AS CL 
						LEFT JOIN tblcourse_master AS CS ON CL.course_master_id = CS.course_master_id
						WHERE CL.course_master_id = $courseID AND CL.is_deleted = 0  order by CL.class_date asc";
				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				return $resultArry;
			}	*/
		public function getClassDetails($classID)
			{
				$sql = "SELECT *,DATE_FORMAT(SUBSTRING(end_time,1,10),'%b-%d-%Y') AS enddate,SUBSTRING(end_time,12) AS endtime FROM tbluser_class WHERE class_id = $classID AND is_deleted = 0";
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				return $resultArry;
			}
		public function getCourseClassDetails($ourseID)
			{
				$sql = "SELECT * FROM tbluser_class WHERE course_master_id = $ourseID AND is_deleted = 0";
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				return $resultArry;
			}	
		public function chechClassTimeExist($courseID, $classDate, $classTime)				
			{
				$sql = "SELECT class_id, class_name FROM tbluser_class WHERE course_master_id = $courseID AND class_date = '$classDate' AND concat(class_date,' ','$classTime') BETWEEN concat(class_date,' ',start_time) AND end_time";
				$resultArry		=	end($this->getdbcontents_sql($sql, false));
				return $resultArry;
			}
		public function getMusicsheets($classID)

			{	
				$sql 			= "SELECT sheet_name,real_name,id FROM tblmusic_sheet WHERE class_id = $classID ";
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				//print_r($resultArry);
				$sheet		=	array();
				for($i=0;$i<count($resultArry);$i++)
					{
						$sheet['name'][]		=	$resultArry[$i]['sheet_name'];	
						$sheet['sheet_id'][]	=	$resultArry[$i]['id'];
						$sheet['real_name'][]	=	$resultArry[$i]['real_name'];
					}						
				//print_r($sheet);
				return $sheet;
			}
		public function deleteSheetMusic($ID)
			{
			    $sql	=	"DELETE FROM tblmusic_sheet WHERE id = $ID";
				$result	=	$this->db_query($sql, false);
				return $result;
			}
		public function getClassUserInfo($classId)	
			{
				$sql = "SELECT CL.class_name, DATE_FORMAT(Concat(class_date,' ', start_time),'%b %d %Y %h:%i %p') as classStart, 
						CS.course_title, CONCAT(USER.first_name, ' ',USER.last_name) AS tutor_name, USER.profile_image, INST.name, 
						INST.instrument_image 
						FROM tbluser_class AS CL 
						LEFT JOIN tblcourse_master CS ON CL.course_master_id = CS.course_master_id 
						LEFT JOIN tblusers AS USER ON CS.tutor_id = USER.user_id 
						LEFT JOIN tblinstrument_master  AS INST ON CS.course_instrument_id = INST.instrument_id 
						WHERE CL.class_id = $classId";
				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				return $resultArry;
			}
		
		public function checkClassDate($courseID,$mode)
			{
				if($mode==1)
					$sql 		= "SELECT course_end_date FROM tblcourse_master WHERE course_master_id=$courseID";
				else
					$sql 		= "SELECT course_start_date FROM tblcourse_master WHERE course_master_id=$courseID";
				$result		= end($this->getdbcontents_sql($sql, 0));
				
				return $result;
				
			}		
	}	
?>