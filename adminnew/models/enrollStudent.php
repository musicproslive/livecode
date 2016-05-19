<?php 
/****************************************************************************************
Created by	:	PREM PRANAV
Created on	:	31-01-2013
Purpose		:	To Enroll Students For Course
****************************************************************************************/
class enrollStudent extends modelclass
	{
		public function enrollStudentListing()
			{ 	
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				return;
			}
		public function enrollStudentFetchStudents(){
				$page 			= 	0;	// The current page
				$sortname 		= 	'U.first_name';	 // Sort column
				$sortorder	 	= 	'ASC';	 // Sort order
				$qtype 			= 	'';	 // Search column
				$query 			= 	'';	 // Search string
				// Get posted data
				if (isset($_POST['page'])) 
					{
						if($_POST['page']==1 && isset($_SESSION['PAGE'][$this->getPageName()][$this->getAction()]) && empty($_POST['query']))
							{
								if($_SESSION['PAGE'][$this->getPageName()][$this->getAction()] == 2 && $this->previousAction ==	$this->currentAction)	$page	=	1;
								else $page		=	$_SESSION['PAGE'][$this->getPageName()][$this->getAction()];
									
							}
						else
							{
								$page 				= 	mysql_real_escape_string($_POST['page']);
								$_SESSION['PAGE'][$this->getPageName()][$this->getAction()]	=	$page;
							}
					}
				if (isset($_POST['sortname'])) 
					{
						$sortname 	= 	mysql_real_escape_string($_POST['sortname']);
					}
				if (isset($_POST['sortorder'])) 
					{		
						$sortorder 	= 	mysql_real_escape_string($_POST['sortorder']);		
					}
				if (isset($_POST['qtype'])) 
					{
						$qtype 		= 	trim(mysql_real_escape_string($_POST['qtype']));
					}	
				if(isset($_SESSION['QUERY'][$this->getPageName()][$this->getAction()]))
					{
						if(trim(mysql_real_escape_string($_POST['query'])) == '' && $this->previousAction ==	$this->currentAction)
							{
								// User is assiging query keyword as empty 
								$query	=	'';
								$_SESSION['QUERY'][$this->getPageName()][$this->getAction()]	=	$query;
							}
						else
							{
								//User is Refreshing page or coming back to viewed page 
								$query	=	$_SESSION['QUERY'][$this->getPageName()][$this->getAction()];
								$qtype	=	$_SESSION['QTYPE'][$this->getPageName()][$this->getAction()];
							}
					}	
				if (!empty($_POST['query'])) 
					{
						$query 		= 	trim(mysql_real_escape_string($_POST['query']));
						$_SESSION['QUERY'][$this->getPageName()][$this->getAction()]	=	$query;
						$_SESSION['QTYPE'][$this->getPageName()][$this->getAction()]	=	trim(mysql_real_escape_string($_POST['qtype']));
					}
				if (isset($_POST['rp'])) 
					{
						$rp 		= 	mysql_real_escape_string($_POST['rp']);
					}
				if(empty($rp))
					{
						$rp			=	LMT_SITE_ADMIN_PAGE_LIMIT;
					}
				// Setup sort and search SQL using posted data
				$sortSql				= 	" order by $sortname $sortorder ";
				$searchSql 				= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				
				// Setup paging SQL
				$pageStart 				= 	($page-1)*$rp;
				if($pageStart<0)
					{
						$pageStart		=	0;
					}
				$limitSql 				= 	" limit $pageStart, $rp ";
				
				// Get total count of records
				$sql					=  "SELECT U.user_id,U.gender,U.first_name, U.last_name,UL.user_name,DATE_FORMAT(U.dob,'".$_SESSION["DATE_FORMAT"]["M_DATE"]."')  AS dob						FROM tblusers as U  									
											LEFT JOIN tbluser_login as UL on UL.login_id=U.login_id
											LEFT JOIN tbluser_roles AS UR ON UR.role_id = UL.user_role 
											where UR.role_access_key = 'STUDENT_ROLE' AND U.is_deleted=0 AND UL.is_deleted=0 $searchSql";							
				$results 	= 	$this->db_query($sql,0);
				$total		= 	mysql_num_rows($results);
				//Getting all records 
				$sql		.=	$sortSql.$limitSql;
				$results 	= 	$this->db_query($sql,0);
				
				// Return JSON data to Template 
				$data 					= 	array();
				$data['page'] 			= 	$page;
				$data['qtype'] 			= 	$qtype;
				$data['query'] 			= 	$query;
				$data['total'] 			= 	$total;
				$data['rows'] 			= 	array();
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						if($row['gender'] == "M")	$gender = "Male";
						if($row['gender'] == "F")	$gender	=	"Female";
						$row['enroll']	=	'<a href="enrollStudent.php?actionvar=EnrollmentList&uid='.base64_encode(serialize($row['user_id'])).'" title="Re-schedule class for this student"><img src="images/inner_details.png" alt="Re-schedule" /></a>';
						$data['rows'][] = array
						(
							'id' => $row['user_id'],
							'cell' => array($i, $row['first_name'].' '.$row['last_name'],$row['user_name'],$row['dob'],$gender,$row['enroll'])
						);
					}
					$r =json_encode($data);
					ob_clean();
					echo  $r;
					exit;
		
				}
			public function enrollStudentEnrollmentList()
				{
					return array("uid"=>$_GET['uid']);
				}	
			public function enrollStudentFetchEnrollments(){	
					$page 			= 	0;	// The current page
					$sortname 		= 	'E.created_on';	 // Sort column
					$sortorder	 	= 	'desc';	 // Sort order
					$qtype 			= 	'';	 // Search column
					$query 			= 	'';	 // Search string
					// Get posted data
					if (isset($_POST['page'])) 
					{
						if($_POST['page']==1 && isset($_SESSION['PAGE'][$this->getPageName()][$this->getAction()]) && empty($_POST['query']))
							{
								if($_SESSION['PAGE'][$this->getPageName()][$this->getAction()] == 2 && $this->previousAction ==	$this->currentAction)	$page	=	1;
								else $page		=	$_SESSION['PAGE'][$this->getPageName()][$this->getAction()];
									
							}
						else
							{
								$page 				= 	mysql_real_escape_string($_POST['page']);
								$_SESSION['PAGE'][$this->getPageName()][$this->getAction()]	=	$page;
							}
					}
					if (isset($_POST['sortname'])) 
						{
							$sortname 	= 	mysql_real_escape_string($_POST['sortname']);
						}
					if (isset($_POST['sortorder'])) 
						{		
							$sortorder 	= 	mysql_real_escape_string($_POST['sortorder']);		
						}
					if (isset($_POST['qtype'])) 
						{
							$qtype 		= 	trim(mysql_real_escape_string($_POST['qtype']));
						}
					if(isset($_SESSION['QUERY'][$this->getPageName()][$this->getAction()]))
						{
							if(trim(mysql_real_escape_string($_POST['query'])) == '' && $this->previousAction ==	$this->currentAction)
								{
									// User is assiging query keyword as empty 
									$query	=	'';
									$_SESSION['QUERY'][$this->getPageName()][$this->getAction()]	=	$query;
								}
							else
								{
									//User is Refreshing page or coming back to same page 
									$query	=	$_SESSION['QUERY'][$this->getPageName()][$this->getAction()];
									$qtype	=	$_SESSION['QTYPE'][$this->getPageName()][$this->getAction()];
								}
						}	
					if (!empty($_POST['query'])) 
						{
							
							$query 		= 	trim(mysql_real_escape_string($_POST['query']));
							$_SESSION['QUERY'][$this->getPageName()][$this->getAction()]	=	$query;
							$_SESSION['QTYPE'][$this->getPageName()][$this->getAction()]	=	trim(mysql_real_escape_string($_POST['qtype']));
						}
					if (isset($_POST['rp'])) 
						{
							$rp 		= 	mysql_real_escape_string($_POST['rp']);
						}
					if(empty($rp))
						{
							$rp			=	LMT_SITE_ADMIN_PAGE_LIMIT;
						}
			
					// Setup sort and search SQL using posted data
					$sortSql				= 	" order by $sortname $sortorder ";
					$searchSql 				= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
					
					// Setup paging SQL
					$pageStart 				= 	($page-1)*$rp;
					if($pageStart<0)
						{
							$pageStart		=	0;
						}
					$limitSql 				= 	" limit $pageStart, $rp ";
					
					// Get total count of records
					$sql		=	"SELECT E.enrolled_id,E.enrollment_code,C.title, CONCAT(U.first_name,' ',U.last_name) as instructor,IM.name AS instrument, DATE_FORMAT(E.created_on,'".$_SESSION["DATE_FORMAT"]["M_DATE"]." ". $_SESSION["DATE_FORMAT"]["M_TIME"]."') as  enrolled_on,E.enrolled_status_id
						FROM tblcourse_enrollments AS E
						LEFT JOIN  tblcourses C 				ON E.course_id=C.course_id
						LEFT JOIN tblusers U 					ON U.user_id=C.instructor_id
						LEFT JOIN tblinstrument_master IM 		ON IM.instrument_id= C.instrument_id										
						WHERE E.student_id=".unserialize(base64_decode($_GET['uid']))." AND E.enrolled_status_id !=".LMT_CS_ENR_CANCELLED." $searchSql";	
					$results 	= 	$this->db_query($sql,0);
					$total		= 	mysql_num_rows($results);
					
					//Getting all records 
					$sql		.=	$sortSql.$limitSql;
					$results 	= 	$this->db_query($sql,0);
					
					// Return JSON data to Template 
					$data 					= 	array();
					$data['page'] 			= 	$page;
					$data['qtype']			=	$qtype;
					$data['query']			=	$query;
					$data['total'] 			= 	$total;
					$data['rows'] 			= 	array();
		
					$i			=	$pageStart;
					while ($row = mysql_fetch_assoc($results)) 
						{
							if($row['enrolled_status_id']=="1")	$enrollStatus="Not Completed";
							if($row['enrolled_status_id']=="2")	$enrollStatus="Cancelled";
							if($row['enrolled_status_id']=="3")	$enrollStatus="Completed";
							$i++;
							$row['enroll_reschedule']	=	'<a href="enrollStudent.php?actionvar=CourseList&uid='.$_GET['uid'].'&enrollCode='.base64_encode(serialize($row['enrollment_code'])).'" title="Re-schedule this to another course" ><img src="images/re-schedule.png" alt="Enroll Course" /></a>';
							$data['rows'][] = array
							(
								'id' => $row['enrolled_id'],
								'cell' => array($i, $row['title'],$row['instructor'],$row['instrument'],$row['enrolled_on'],$enrollStatus,$row['enroll_reschedule'])
							);
						}
						$r =json_encode($data);
						ob_clean();
						echo  $r;
						exit;
		
				}
			public function enrollStudentCourseList()
				{
					return array("uid"=>$_GET['uid'],"enrollCode"=>$_GET['enrollCode']);
				}
			public function enrollStudentFetchCourses(){
			 
					$page 			= 	0;	// The current page
					$sortname 		= 	'C.title';	 // Sort column
					$sortorder	 	= 	'asc';	 // Sort order
					$qtype 			= 	'';	 // Search column
					$query 			= 	'';	 // Search string
					// Get posted data
					if (isset($_POST['page'])) 
						{
							if($_POST['page']==1 && isset($_SESSION['PAGE'][$this->getPageName()][$this->getAction()]) && empty($_POST['query']))
								{
									if($_SESSION['PAGE'][$this->getPageName()][$this->getAction()] == 2 && $this->previousAction ==	$this->currentAction)	$page	=	1;
									else $page		=	$_SESSION['PAGE'][$this->getPageName()][$this->getAction()];
										
								}
							else
								{
									$page 				= 	mysql_real_escape_string($_POST['page']);
									$_SESSION['PAGE'][$this->getPageName()][$this->getAction()]	=	$page;
								}
						}
					if (isset($_POST['sortname'])) 
						{
							$sortname 	= 	mysql_real_escape_string($_POST['sortname']);
						}
					if (isset($_POST['sortorder'])) 
						{		
							$sortorder 	= 	mysql_real_escape_string($_POST['sortorder']);		
						}
					if (isset($_POST['qtype'])) 
						{
							$qtype 		= 	trim(mysql_real_escape_string($_POST['qtype']));
						}
					if(isset($_SESSION['QUERY'][$this->getPageName()][$this->getAction()]))
						{
							if(trim(mysql_real_escape_string($_POST['query'])) == '' && $this->previousAction ==	$this->currentAction)
								{
									// User is assiging query keyword as empty 
									$query	=	'';
									$_SESSION['QUERY'][$this->getPageName()][$this->getAction()]	=	$query;
								}
							else
								{
									//User is Refreshing page or coming back to same page 
									$query	=	$_SESSION['QUERY'][$this->getPageName()][$this->getAction()];
									$qtype	=	$_SESSION['QTYPE'][$this->getPageName()][$this->getAction()];
								}
						}	
					if (!empty($_POST['query'])) 
						{
							
							$query 		= 	trim(mysql_real_escape_string($_POST['query']));
							$_SESSION['QUERY'][$this->getPageName()][$this->getAction()]	=	$query;
							$_SESSION['QTYPE'][$this->getPageName()][$this->getAction()]	=	trim(mysql_real_escape_string($_POST['qtype']));
						}
					if (isset($_POST['rp'])) 
						{
							$rp 		= 	mysql_real_escape_string($_POST['rp']);
						}
					if(empty($rp))
						{
							$rp			=	LMT_SITE_ADMIN_PAGE_LIMIT;
						}
			
					// Setup sort and search SQL using posted data
					$sortSql				= 	" order by $sortname $sortorder ";
					$searchSql 				= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
					
					// Setup paging SQL
					$pageStart 				= 	($page-1)*$rp;
					if($pageStart<0)
						{
							$pageStart		=	0;
						}
					$limitSql 				= 	" limit $pageStart, $rp ";
					
					// Get total count of records
					$sql		=	"SELECT C.course_code,C.title, CONCAT(U.first_name,' ',U.last_name) as instructor,IM.name AS instrument, DATE_FORMAT(C.start_date,'".$_SESSION["DATE_FORMAT"]["M_DATE"]."')  as  start_date,DATE_FORMAT(C.start_time,'". $_SESSION["DATE_FORMAT"]["M_TIME"]."') as start_time, C.max_students,(select count(enrolled_id)  FROM tblcourse_enrollments WHERE course_id = C.course_id AND enrolled_status_id !=".LMT_CS_ENR_CANCELLED.") as total_enrolled,C.max_students, CD.time,CT.type,CP.cost, CUT.symbol
						FROM tblcourses C
						LEFT JOIN tblusers U 					ON U.user_id=C.instructor_id
						LEFT JOIN tblinstrument_master IM 		ON IM.instrument_id= C.instrument_id
						LEFT JOIN tblcourse_prices CP 	 		ON CP.id = C.price_code
						LEFT JOIN tbllookup_course_duration CD	ON CD.id= C.duration	
						LEFT JOIN tbllookup_course_type CT 	ON CT.id = C.course_type_id
						LEFT JOIN  tblcurrency_type CUT 		ON CUT.currency_id = CP.currency_type										
						WHERE C.num_enrolled < C.max_students AND CONCAT(C.start_date,' ',C.start_time) > '".date('Y-m-d H:i:s')."' $searchSql";
					$results 	= 	$this->db_query($sql,0);
					$total		= 	mysql_num_rows($results);
					
					//Getting all records 
					$sql		.=	$sortSql.$limitSql;
					$results 	= 	$this->db_query($sql,0);
					
					// Return JSON data to Template 
					$data 					= 	array();
					$data['page'] 			= 	$page;
					$data['qtype'] 			= 	$qtype;
					$data['query'] 			= 	$query;
					$data['total'] 			= 	$total;
					$data['rows'] 			= 	array();
		
					//Getting user Information for popup confirmation 
					$userInfo	=	end($this->getdbcontents_sql('SELECT CONCAT(first_name," ",last_name) AS name FROM tblusers WHERE user_id = '.unserialize(base64_decode($_GET['uid']))));
					$name	=	$userInfo['name'];
		
					$i			=	$pageStart;
					while ($row = mysql_fetch_assoc($results)) 
						{
							$i++;
							$row['enroll_course']	=	'<a href="enrollStudent.php?actionvar=EnrollCourse&uid='.$_GET['uid'].'&enrollCode='.$_GET['enroll_code'].'&ccode='.base64_encode(serialize($row['course_code'])).'&paymentFlag=0'.'" title=" Without Paying to Instructor " onclick= "return reScheduleAlert(\''.$name.'\'); " ><img src="images/enroll_cs.gif" alt="Enroll free" /></a>&nbsp;&nbsp;<a href="enrollStudent.php?actionvar=EnrollCourse&uid='.$_GET['uid'].'&ccode='.base64_encode(serialize($row['course_code'])).'&enrollCode='.$_GET['enrollCode'].'&paymentFlag=1'.'" title="Re-schedule with paying to Instructor" onclick= "return reScheduleAlert(\''.$name.'\'); " ><img src="images/enrollCourse.jpg" height="20" width="20" alt="Enroll with payment" /></a>';
							$data['rows'][] = array
							(
								'id' => $row['user_id'],
								'cell' => array($i, $row['title'],$row['instructor'],$row['instrument'],$row['start_date'],$row['start_time'],$row['symbol']." ".$row['cost'],$row['total_enrolled']."/".$row['max_students'],$row['enroll_course'])
							);
						}
						$r =json_encode($data);
						ob_clean();
						echo  $r;
						exit;
		
				}
				
			public function enrollStudentEnrollCourse()
				{
					$data 		= $this->getData('get');
					$cls  		=	new userCourse();
					$cms  		=	new cms();
					$mailMgmt	=	new mailManagment();
					$userMgmt	=	new userManagement();			
					$csId 		= 	$cls->getCourseId(unserialize(base64_decode($data['ccode'])));	
					$user_id	=	unserialize(base64_decode($data['uid']));
					$enrollCode	=	unserialize(base64_decode($data['enrollCode']));
					$enrolledStatus = $cls->courseEnrolledStatus($csId);
					if(!($cls->isStdEnrolled($csId, $user_id)))
						{
							// When payment is to be done to Instructor we are updating old enrollment to rescheduled enrollment
							$csData =	array();
							$enrId 	=	"";
							if($_GET['paymentFlag'] == "1")
								{
									$csData['rescheduled_from'] = $cls->getEnrolledCourseId($enrollCode);					
									$csData['course_id'] = $csId;					
									//$csData['created_on'] = date('Y-m-d H:i:s');
									if(!$enrId = $cls->rescheduleEnrollment($csData,"enrollment_code='$enrollCode'"))
										{
											$this->setPageError($this->getDbErrors());																
											return $this->executeAction(true, "Listing");
										}
								}
							else 
								{								
									//create random code for course enrollment.
									do
									{
											$randCode	=	$this->createRandom(LMT_RANDOM_CODE_LIMIT);
									}			
									while($this->getdbcount_sql("SELECT enrollment_code FROM tblcourse_enrollments WHERE enrollment_code = '$randCode'") > 0);
									$csData['enrollment_code'] = $randCode;					
									$csData['course_id'] = $csId;					
									$csData['student_id'] = $user_id;
									$csData['created_on'] = date('Y-m-d H:i:s');
									if($enrId = $cls->enrollCourse($csData))
										{
											if($enrolledStatus['num_enrolled'] == ($enrolledStatus['max_students'] - 1))
												$cls->updateCourseTypeStatus($csId, array('course_status_id' => LMT_COURSE_STATUS_FULL));
										}
									else
										{
											$this->restoreEnrollment($csId);
											$this->setPageError($this->getDbErrors());																
											return $this->executeAction(true, "Listing");
										}
								}
								
								//Getting User Information for sending mail 
								$sql	=	'SELECT CONCAT(U.first_name," ",U.last_name) As username,TZ.gmt, L.user_name AS useremail, T.mysql_date_format, T.mysql_time_format FROM  tblusers AS U 
								LEFT JOIN tbluser_login AS L ON U.login_id = L.login_id 
								LEFT JOIN tbllookup_user_timestamp AS T ON U.time_format_id = T.id
								LEFT JOIN tbltime_zones AS TZ ON TZ.id	=	U.time_zone_id
								WHERE U.user_id='.$user_id.'';
								
								$userInfo			=	end($this->getdbcontents_sql($sql));	
								$userName		=	$userInfo['username'];
								$userEmail		=	$userInfo['useremail'];
								$userDateFormat	=	$userInfo['mysql_date_format'];
								$userTimeFormat	=	$userInfo['mysql_time_format'];
								$userTimeZone	=	substr($userInfo['gmt'],4,6); //Time zone format + 07:00
								
								//Sending mail to student			
								$courseDetail = $cls->getEnrollmentMailDet($csId, $userDateFormat, $userTimeFormat, "", LMT_SERVER_TIME_ZONE_OFFSET, $userTimeZone);
								$subject 						=  'Live Music Tutor Course Reschedule Receipt';
								$varArr["{TPL_URL}"]			=	ROOT_URL;
								$varArr["{TPL_NAME}"]	   		=  	$userName;
								$varArr["{TPL_CS_CODE}"]		=  	$courseDetail['course_code'];
								$varArr["{TPL_ENR_CODE}"]		=  	$csData['enrollment_code'];
								$varArr["{TPL_TITLE}"]			=	$courseDetail['title'];
								$varArr["{TPL_START_DATE}"]		= 	$courseDetail['start_date'];
								$varArr["{TPL_START_TIME}"]		= 	$courseDetail['start_time'];
								$varArr["{TPL_DURATION}"]		= 	$courseDetail['time'];											
								$varArr["{TPL_DESC}"]	    	= 	$courseDetail['description'];
								$varArr["{TPL_CS_AMOUNT}"]		= "0";
								$varArr["{TPL_TRANS_REF_CODE}"]	= " NA ";
								
								$send =	$cms->sendMailCMS(LMT_NEW_ENR_TPL, $userEmail, LMT_SITE_ADMIN_MAIL_ID, $subject, $varArr, 5); 
										
								//Sending mail to instructor
								$mailDetails					=	$cls->getMailingDetails($enrId,LMT_SERVER_TIME_ZONE_OFFSET);
								$subject 						=  'Live Music Tutor Course Enrollment';	
								$varArr["{TPL_URL}"]			=	ROOT_URL;
								$varArr["{TPL_NAME}"]			=  	$mailDetails['name'];
								$varArr["{TPL_STUD_NAME}"]		=   $userName;
								$varArr["{TPL_TITLE}"]		    =	$mailDetails['title'];
								$varArr["{TPL_DESC}"]		    =	$mailDetails['description'];
								$varArr["{TPL_CREATED_ON}"]		=	date($mailDetails['php_date_format']." ".$mailDetails['php_time_format'],strtotime($mailDetails['created_on']));
								$varArr["{TPL_START_DATE}"]		=	date($mailDetails['php_date_format'],strtotime($mailDetails['course_start']));
								$varArr["{TPL_START_TIME}"]		=	date($mailDetails['php_time_format'],strtotime($mailDetails['course_time']));
								$varArr["{TPL_DURATION}"]		=	$cls->getCourseDuration($mailDetails['duration']);
								$varArr["{TPL_MAX}"]			=	$mailDetails['max_students'];
								$varArr["{TPL_MIN}"]			=	$mailDetails['min_required'];
								
								$send =	$cms->sendMailCMS(LMT_NEW_ENR_INS,$mailDetails['user_name'],LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5); 																
								//Sending mail to administrator
								$subject 						=  'Live Music Tutor Course Enrollment';
								$varArr["{TPL_URL}"]			=	ROOT_URL;										
								$varArr["{TPL_NAME}"]			=  'Administrator';
								
								$varArr["{TPL_STUD_NAME}"]		=   $userName;
								$varArr["{TPL_TITLE}"]		    =	$mailDetails['title'];
								$varArr["{TPL_DESC}"]		    =	$mailDetails['description'];
								$varArr["{TPL_CREATED_ON}"]		=	date(LMT_ADMIN_DATE_FORMAT." ".LMT_ADMIN_TIME_FORMAT,strtotime($mailDetails['created_on']));
								$varArr["{TPL_START_DATE}"]		=	date(LMT_ADMIN_DATE_FORMAT,strtotime($mailDetails['start_date']));
								$varArr["{TPL_START_TIME}"]		=	date(LMT_ADMIN_TIME_FORMAT,strtotime($mailDetails['start_time']));
								$varArr["{TPL_DURATION}"]		=	$cls->getCourseDuration($mailDetails['duration']);
								$varArr["{TPL_MAX}"]			=	$mailDetails['max_students'];
								$varArr["{TPL_MIN}"]			=	$mailDetails['min_required'];
								//mail to all selected admin	
								$toIds	=	$userMgmt->getAllTplAdmin(LMT_NEW_ENR_ADMIN);
								$send	=	$mailMgmt->sendMailAdmin(LMT_NEW_ENR_ADMIN,$toIds,LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5);
								
								//mail to superadmin
								$send =	$cms->sendMailCMS(LMT_NEW_ENR_ADMIN,LMT_ADMIN_MAILID,LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5);
									
								//User Log
								$logObj  =	new userLog();
								$logObj->setUserAction($user_id, LMT_COURSE_ENROLLED, 1);
								$this->redirectAction("Course rescheduled successfully ","Listing","enrollStudent.php");																
						}
					else
						{
							$this->setPageError("This course is already enrolled");
							$this->executeAction(false,"","enrollStudent.php?actionvar=CourseList&uid=".$data['uid'],true,false);					
						}						
				}
			public 	function restoreEnrollment($courseId, $enrollmentId = 0, $transactionId = 0)
				{
					if($courseId)
							$this->db_update('tblcourses', array('num_enrolled' => 'num_enrolled - 1'), "course_id = $courseId");
						
					if($enrollmentId)
							$this->dbDelete_cond('tblcourse_enrollments', "enrolled_id  = $enrollmentId");	
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