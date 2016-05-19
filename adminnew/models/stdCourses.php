<?php
/**************************************************************************************
Created by :Lijesh 
Created on :Sep - 06 - 2012
Purpose    :Students Course history
**************************************************************************************/

class stdCourses extends modelclass{
	
		
	public function stdCoursesListing(){			
		$cls	=	new userCourse();						
		$return['enrStatus'] = $cls->getEnrollmentStats();			
		$sql   	= $cls->getStudentsCourseHistory($_SESSION['USER_LOGIN']['LMT_USER_ID'], $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_DATE'], $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_TIME'], "", LMT_SERVER_TIME_ZONE_OFFSET, $_SESSION['USER_LOGIN']['TIMEZONE_ID']);		
     
		$return['spage']				 		=	$this->create_paging("n_page",$sql,10);						
		$return['courses']				 		=	$this->getdbcontents_sql($return['spage']->finalSql(), 0);
		$return['userCalendar'] = $cls->getStudentsSideCalendar($_SESSION['USER_LOGIN']['LMT_USER_ID'], $_SESSION["USER_LOGIN"]["TIMEZONE_ID"], LMT_SERVER_TIME_ZONE_OFFSET);	
		$return['serverTime']	= strtotime(date('Y-m-d H:i:s'));
		//$this->print_r($return['courses']);exit;	
		return $return;
	}
	
	function stdCoursesSearch($enrl_status)
		{
			
			$data  =	$this->getData("post", "", true);	
			$valObj	=	new dataValidation;
			 /*if(!$valObj->validateDate($data['end_date']) || !$valObj->validateDate($data['start_date']))
				{
					ob_clean();
					$this->setPageError("OOps!! Seems your data is vulnerable,we can't process your request");
					$this->executeAction(false,"Listing","userHome.php",true,false);
				}*/
			$return['data'] = $data;
			$cls	=	new userCourse();				
			$return['enrStatus'] = $cls->getEnrollmentStats();
			if($enrl_status==1)
			{
				$server_time_zone=strtotime(date('Y-m-d H:i:s'));
				$time_Ser=date('Y-m-d H:i:s');
				$condition = " AND E.enrolled_status_id=".$enrl_status." and $server_time_zone <UNIX_TIMESTAMP(CONCAT(C.start_date, ' ',
						C.start_time))+D.time*60 and  '$time_Ser'<=DATE_ADD(CONCAT(C.start_date, ' ', C.start_time),INTERVAL D.time MINUTE)" ;	
			}
			elseif($enrl_status==4)
			{
				$server_time_zone=strtotime(date('Y-m-d H:i:s'));
				$time_Ser=date('Y-m-d H:i:s');
				$condition = " AND E.enrolled_status_id=1 and  '$time_Ser'>=DATE_ADD(CONCAT(C.start_date, ' ', C.start_time),INTERVAL D.time MINUTE)" ;	
			}
			else
			{
			$condition = !empty($enrl_status) ? " AND E.enrolled_status_id = {$enrl_status}" : "";	
			}
			
			//$condition		  .=  !empty($data['start_date']) ? " AND ".$this->dbSearchCond(">=", "UNIX_TIMESTAMP(SUBSTR(CONVERT_TZ(C.start_date,'".LMT_SERVER_TIME_ZONE_OFFSET."','".$_SESSION['USER_LOGIN']['TIMEZONE_ID']."'),1,10))",strtotime($data["start_date"])) : "";
			//$condition		  .=  !empty($data['end_date']) ? " AND ".$this->dbSearchCond("<=", "UNIX_TIMESTAMP(SUBSTR(CONVERT_TZ(C.start_date,'".LMT_SERVER_TIME_ZONE_OFFSET."','".$_SESSION['USER_LOGIN']['TIMEZONE_ID']."'),1,10))",strtotime($data["end_date"])) : "";
			
			$sql   = $cls->getStudentsCourseHistory($_SESSION['USER_LOGIN']['LMT_USER_ID'], $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_DATE'], $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_TIME'], $condition);
			
			$return['spage']	=	$this->create_paging("n_page",$sql,10);		
            
			$return['courses']	=	$this->getdbcontents_sql($return['spage']->finalSql(),0);	
			return $return;
		}
		
		
	public function stdCoursesCancel(){		
			$data							=	$this->getData("post", "", true);			
			$cls							=	new	userCourse();
			$cms							=	new cms();
			$mailMgmt	 					=	new mailManagment();
			$userMgmt					 	=	new userManagement();			
			$paidAmount = $cls->getCourseTransactionAmount($_SESSION['USER_LOGIN']['LMT_USER_ID'], unserialize(base64_decode($data['enrolled_id'])));
			$courseDate = $cls->getCourseDate($_SESSION['USER_LOGIN']['LMT_USER_ID'], unserialize(base64_decode($data['enrolled_id'])));		
			$cancellationDate = strtotime('-'.	
LMT_CANCEL_TICKET_BEFORE." hours", strtotime($courseDate['start_date'].' '.$courseDate['start_time']));			
			$cancellationAmount = ((strtotime(date('Y-m-d H:i:s')) >=  $cancellationDate)) ? (($paidAmount * LMT_CANCELLATION_AMOUNT)/100) : 0;
			
													
			do//create random code.
			{
				$randCode	=	$this->createRandom(LMT_RANDOM_CODE_LIMIT);
			}			
			while($this->getdbcount_sql("SELECT cancel_code FROM tblcourse_cancel_transaction WHERE cancel_code = '$randCode'") > 0);
			
			$cancelTrans = array();
			$cancelTrans['cancel_code'] = $randCode;
			$cancelTrans['trans_time'] = date('Y-m-d H:i:s');
			$cancelTrans['user_id'] = $_SESSION['USER_LOGIN']['LMT_USER_ID'];
			$cancelTrans['enrolled_id'] = unserialize(base64_decode($data['enrolled_id']));
			$cancelTrans['course_amount'] = $paidAmount;
			$cancelTrans['deduction'] = $cancellationAmount;
			$cancelTrans['refunded_amount'] = ($cancelTrans['course_amount'] - $cancelTrans['deduction']);
			$cancelTrans['currency_id'] = 1;
									
			if($this->db_insert('tblcourse_cancel_transaction', $cancelTrans))
				{
					$dataIns['enrolled_status_id']	=   LMT_CS_ENR_CANCELLED;
					$dataIns['status_reason_id']	=	LMT_CS_ENR_CANCEL_REASON_STUD; // Student cancellation
					$dataIns['status_changed_by']	=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];
					
					if($this->db_update('tblcourse_enrollments', $dataIns,"enrolled_id =".unserialize(base64_decode($data['enrolled_id']))))
						{
							//User Log
							$logObj  =	new userLog();
							$logObj->setUserAction($_SESSION['USER_LOGIN']['LMT_USER_ID'], LMT_COURSE_CANCEL_STUDENT, 1);				
							$enrollmentDetail = $cls->getEnrollmentCancelMailDet(unserialize(base64_decode($data['enrolled_id'])), $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_DATE'], $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_TIME'], "", LMT_SERVER_TIME_ZONE_OFFSET, $_SESSION['USER_LOGIN']['TIMEZONE_ID']);
							
							$subject 					=  'Live Music Tutor  Cancelled Enrollment';
							$varArr["{TPL_URL}"]			=	ROOT_URL;	
							$varArr["{TPL_NAME}"]	    =  $_SESSION['USER_LOGIN']['USER_NAME'];									
							$varArr["{TPL_CS_CODE}"]	=  $enrollmentDetail['course_code'];
							$varArr["{TPL_ENR_CODE}"]	=  $enrollmentDetail['enrollment_code'];
							$varArr["{TPL_TRANS_REF_CODE}"]	= $cancelTrans['cancel_code'];
							$varArr["{TPL_TITLE}"]	= $enrollmentDetail['title'];
							$varArr["{TPL_START_DATE}"]	= $enrollmentDetail['start_date'];
							$varArr["{TPL_START_TIME}"]	= $enrollmentDetail['start_time'];
							$varArr["{TPL_REFUND_AMOUNT}"]	= ($cancelTrans['course_amount'] - $cancelTrans['deduction']);
							$send =	$cms->sendMailCMS(LMT_ENR_CANCEL_TO_STUD_TPL, $_SESSION['USER_LOGIN']['LMT_USER_EMAIL'], LMT_SITE_ADMIN_MAIL_ID, $subject, $varArr, 5); 	
							
							$mailDetails					=	$cls->getMailingDetails(unserialize(base64_decode($data['enrolled_id'])),LMT_SERVER_TIME_ZONE_OFFSET);
							//Mail to instructor
							$subject 						=  'Live Music Tutor Cancelled Enrollment';	
							$varArr["{TPL_URL}"]			=	ROOT_URL;
							$varArr["{TPL_NAME}"]			=  $mailDetails['name'];
							$varArr["{TPL_STUD_NAME}"]		=   $_SESSION['USER_LOGIN']['USER_NAME'];
							$varArr["{TPL_TITLE}"]		    =	$mailDetails['title'];
							$varArr["{TPL_DESC}"]		    =	$mailDetails['description'];
							$varArr["{TPL_CREATED_ON}"]		=	date($mailDetails['php_date_format']." ".$mailDetails['php_time_format'],strtotime($mailDetails['created_on']));
							$varArr["{TPL_START_DATE}"]		=	date($mailDetails['php_date_format'],strtotime($mailDetails['course_start']));
							$varArr["{TPL_START_TIME}"]		=	date($mailDetails['php_time_format'],strtotime($mailDetails['course_time']));
							$varArr["{TPL_DURATION}"]		=	$cls->getCourseDuration($mailDetails['duration']);
							$varArr["{TPL_MAX}"]			=	$mailDetails['max_students'];
							$varArr["{TPL_MIN}"]			=	$mailDetails['min_required'];
							
							$send =	$cms->sendMailCMS(LMT_MAIL_TPL_CANCEL_ENR_INST,$mailDetails['user_name'],LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5); 																
							//Mail to administrator
							$subject 						=  'Live Music Tutor Cancelled Enrollment';
							$varArr["{TPL_URL}"]			=	ROOT_URL;										
							$varArr["{TPL_NAME}"]			=  'Administrator';
							
							$varArr["{TPL_STUD_NAME}"]		=   $_SESSION['USER_LOGIN']['USER_NAME'];
							$varArr["{TPL_TITLE}"]		    =	$mailDetails['title'];
							$varArr["{TPL_DESC}"]		    =	$mailDetails['description'];
							$varArr["{TPL_CREATED_ON}"]		=	date(LMT_ADMIN_DATE_FORMAT." ".LMT_ADMIN_TIME_FORMAT,strtotime($mailDetails['created_on']));
							$varArr["{TPL_START_DATE}"]		=	date(LMT_ADMIN_DATE_FORMAT,strtotime($mailDetails['start_date']));
							$varArr["{TPL_START_TIME}"]		=	date(LMT_ADMIN_TIME_FORMAT,strtotime($mailDetails['start_time']));
							$varArr["{TPL_DURATION}"]		=	$cls->getCourseDuration($mailDetails['duration']);
							$varArr["{TPL_MAX}"]			=	$mailDetails['max_students'];
							$varArr["{TPL_MIN}"]			=	$mailDetails['min_required'];
						//mail to all selected admin	
						$toIds	=	$userMgmt->getAllTplAdmin(LMT_MAIL_TPL_CANCEL_ENR_ADMIN);
						$send	=	$mailMgmt->sendMailAdmin(LMT_MAIL_TPL_CANCEL_ENR_ADMIN,$toIds,LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5);
						//mail to superadmin
					//$send =	$cms->sendMailCMS(LMT_MAIL_TPL_CANCEL_ENR_ADMIN,LMT_ADMIN_CS_MAIL_ID,LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5);
						}
					
				
				}
			else
				{
					$this->setPageError($this->getDbErrors());	
				}	

			$this->clearData();			
			$this->redirectAction("Your course has been cancelled successfuly", "Listing", "stdCourses.php");	
		}
		
	public function stdCoursesViewNotes()
		{			
			$data	=	$this->getData("get", "", true);
			$ccode=unserialize(base64_decode($data['ccode']));
			$cls	=	new userCourse();
			$sql=$cls->getStudentsClassNotes($ccode,$_SESSION['USER_LOGIN']['LMT_USER_ID'], $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_DATE'], $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_TIME'],LMT_SERVER_TIME_ZONE_OFFSET, $_SESSION['USER_LOGIN']['TIMEZONE_ID']);
			$spage			=	$this->create_paging("n_page",$sql,10);
			$note_data				=	$this->getdbcontents_sql($spage->finalSql());
			if(!$note_data)		$this->setPageError("No records found !");
			return array("note_data"=>$note_data,"spage"=>$spage);
		}
		
	public function stdCoursesViewVideo()
		{			
			$data  	=	$this->getData("get", "", true);
			$ccode	=	unserialize(base64_decode($data['ccode']));
			$cls	=	new userCourse();
			$sql	=	$cls->getStudentsClassVideos($ccode,$_SESSION['USER_LOGIN']['LMT_USER_ID'], $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_DATE'], $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_TIME'],LMT_SERVER_TIME_ZONE_OFFSET, $_SESSION['USER_LOGIN']['TIMEZONE_ID']);
			$spage	=	$this->create_paging("s_page",$sql);
			$video_data				=	$this->getdbcontents_sql($spage->finalSql(),0);
			if(!$video_data)		$this->setPageError("No records found !");
			return array("video_data"=>$video_data,"spage"=>$spage);
		}
	public function stdRecentSheduled()
	{
		$cls	=	new userCourse();
		$return['recentShedule'] = $cls->getStudentRecentCourseShedule($_SESSION['USER_LOGIN']['LMT_USER_ID'], LMT_SERVER_TIME_ZONE_OFFSET, $_SESSION['USER_LOGIN']['TIMEZONE_ID'], date('Y-m-d H:i:s'), $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_DATE'], $_SESSION['USER_LOGIN']['DATE_FORMAT']['M_TIME']);
		
		return $return;
	}
	public function __construct()
		{
			$this->setClassName();
		}
	
	public function redirectAction($errMessage,$action,$url)	
		{	
			$this->setPageError($errMessage);
			$this->clearData();
			$this->executeAction(true,$action,$url,true);	
		}	
			
	public function executeAction($loadData=true,$action="",$ufURL="",$navigate=false,$sameParams=false,$newParams="",$excludParams="",$page="")
		{
			if(trim($action))	$this->setAction($action);//forced action
			$methodName	=		(method_exists($this,$this->getMethodName()))	? $this->getMethodName($default=false):$this->getMethodName($default=true);
			$this->actionToBeExecuted($loadData,$methodName,$action,$navigate,$sameParams,$newParams,$excludParams,$page,$ufURL);
			$this->actionReturn		=	call_user_func(array($this, $methodName));				
			$this->actionExecuted($methodName);
			return $this->actionReturn;
		}
	public function __destruct() 
		{
			parent::childKilled($this);
		}
}

?>