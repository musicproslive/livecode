<?php 
/****************************************************************************************
Created by	:	PREM PRANAV  
Created on	:	07-12-2012
Purpose		:	For Daily Enrollment Report Management
******************************************************************************************/
class dailyreport extends modelclass
	{
		public function dailyreportListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName(), "monthelyreport.php", "yearlyreport.php" ));
				
				$data			=	 $_GET;
				unset($_SESSION["search"]);			
				if(!empty($data["year"]))
					  $_SESSION["search"]["year"]	=	 $data["year"];
				if(!empty($data["month"]))
					  $_SESSION["search"]["month"]	=	 $data["month"];	 
			
			}
		public function dailyreportFetch(){
			
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
							$rp			= LMT_SITE_ADMIN_PAGE_LIMIT;
						}
					
					// Setup sort and search SQL using posted data
					$sortSql				 = 	" order by $sortname $sortorder";
					$searchSql 				= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
					
					//this condition is for getting record of selected month and year
					$cond	=	" ";
					if(isset($_SESSION["search"]["month"])){
						 $cond			.=	" AND DATE_FORMAT(E.created_on,'%c') =".$_SESSION["search"]["month"];
					}
					
					if(isset($_SESSION["search"]["year"])){
						 $cond			.=	" AND DATE_FORMAT(E.created_on,'%Y') =".$_SESSION["search"]["year"];
					}		 
					
				 // Setup paging SQL for that we need count of record
				 $sql		=		"SELECT  COUNT(*)
					 				 FROM tblcourses C
									 LEFT JOIN tblcourse_enrollments E ON E.course_id=C.course_id
									 LEFT JOIN tblcourse_enrollment_transaction P ON P.enrolled_id=E.enrolled_id
									 LEFT JOIN tblcurrency_type CUR ON CUR.currency_id=P.currency_id
									 LEFT JOIN tblusers  INS  ON C.instructor_id=INS.user_id	
									 LEFT JOIN tblusers  u  ON E.student_id=u.user_id							
							 		 WHERE  C.course_status_id !=".LMT_COURSE_STATUS_CANCELLED." AND E.enrolled_status_id !=".LMT_CS_ENR_CANCELLED." $cond $searchSql ";			 
					$result 				= 	$this->db_query($sql,0);
					$row 					= 	mysql_fetch_array($result);
					$total					=	$row[0];
						
					$pageStart 				= 	($page-1)*$rp;
					if($pageStart<0)
						{
							$pageStart		=	0;
						}
					$limitSql 				= 	" limit $pageStart, $rp";
					
					// Return JSON data
					$data 					= 	array();
					$data['page'] 			= 	$page;
					$data['qtype'] 			= 	$qtype;
					$data['query'] 			= 	$query;
					$data['total'] 			= 	$total;
					$data['rows'] 			= 	array();

				 $sql		=		"SELECT  P.trans_amount as cost,C.title, DATE_FORMAT(E.created_on,'".$_SESSION["DATE_FORMAT"]["M_DATE"]." ".$_SESSION["DATE_FORMAT"]["M_TIME"]."')  as  created_on, DATE_FORMAT(CONCAT(C.start_date,' ', C.start_time),'".$_SESSION["DATE_FORMAT"]["M_DATE"]." ".$_SESSION["DATE_FORMAT"]["M_TIME"]."')  as  course_start_time, CUR.symbol,CONCAT(INS.first_name,' ',INS.last_name) as instructor,CONCAT(u.first_name,' ',u.last_name) as student, E.enrolled_id, E.paid_flag,E.refund_flag
					 				 
					 				 FROM tblcourses C
									 LEFT JOIN tblcourse_enrollments E ON E.course_id=C.course_id
									 LEFT JOIN tblcourse_enrollment_transaction P ON P.enrolled_id=E.enrolled_id
									 LEFT JOIN tblcurrency_type CUR ON CUR.currency_id=P.currency_id
									 LEFT JOIN tblusers  INS  ON C.instructor_id=INS.user_id	
									 LEFT JOIN tblusers  u  ON E.student_id=u.user_id							
							 		 WHERE C.course_status_id !=".LMT_COURSE_STATUS_CANCELLED." AND E.enrolled_status_id !=".LMT_CS_ENR_CANCELLED." $cond ";
						
					 $sortSql	 =	" GROUP BY E.enrolled_id ". $sortSql;	
					 $sql		.=	" ".$searchSql." ".$sortSql." ".$limitSql;	
				     $results 	 = 	$this->db_query($sql,0);
					 
					$i			 =	$pageStart;
					while ($row = mysql_fetch_assoc($results)) 
						{
							$paid=	'<img src="../images/doller-inactive.png" alt="no note" title="not Payed">';
							if($row['paid_flag']==1 && $row["refund_flag"]==0){
								$paid=	'<img src="../images/doller.png" alt="Paid" title="Paid" title="Paid">';
							}
							if(!isset($row['cost']))
							$price	=	"--NA--";
							else
							$price	=$row['symbol']." ".$row['cost'];
							$cancel	=	'<a href="dailyreport.php?actionvar=CancelEnroll&enrolled_id='.base64_encode(serialize($row['enrolled_id'])).'"> <img src="images/delete.gif" alt="cancel link" onclick="return delall()" title="cancel enrollment"></a>';							
								$i++;
								$data['rows'][] = array(
								'id' => $row['course_id'],
								'cell' => array($i,$paid, $row['student'],$row['title'],$row['instructor'],$row['created_on'],$row['course_start_time'],$price,$cancel)
							);
						}
					$r =json_encode($data);
					ob_clean();
					echo  $r;
					exit;
					
		
			}
		public function dailyreportCancelEnroll()
			{
				$data			=	$this->getData("get");
				$cls			=	new	userCourse();
				$cms			=	new cms();
				$mailMgmt		=	new mailManagment();
				$userMgmt	 	=	new userManagement();
				$sql			=	'SELECT E.student_id,CONCAT(U.first_name," ",U.last_name) As username,TZ.gmt, L.user_name AS useremail, T.mysql_date_format, T.mysql_time_format FROM tblcourse_enrollments AS E 
				LEFT JOIN tblusers AS U ON E.student_id = U.user_id 
				LEFT JOIN tbluser_login AS L ON U.login_id = L.login_id 
				LEFT JOIN tbllookup_user_timestamp AS T ON U.time_format_id = T.id
				LEFT JOIN tbltime_zones AS TZ ON TZ.id	=	U.time_zone_id
				WHERE E.enrolled_id = '.unserialize(base64_decode($data['enrolled_id'])).'';
				$enrollment		=	end($this->getdbcontents_sql($sql));	
				$userID			= 	$enrollment['student_id'];
				$userName		=	$enrollment['username'];
				$userEmail		=	$enrollment['useremail'];
				$userDateFormat	=	$enrollment['mysql_date_format'];
				$userTimeFormat	=	$enrollment['mysql_time_format'];
				$userTimeZone	=	substr($enrollment['gmt'],4,6);
				$admin		=	end($this->getdbcontents_sql('SELECT U.user_id FROM tbluser_login AS L LEFT JOIN tblusers AS U ON L.login_id=U.login_id WHERE L.login_id='.$_SESSION['sess_admin'].''));
				$adminID	=	$admin['user_id'];
				$paidAmount = $cls->getCourseTransactionAmount($userID, unserialize(base64_decode($data['enrolled_id'])));
				$courseDate = $cls->getCourseDate($userID, unserialize(base64_decode($data['enrolled_id'])));		
				$cancellationDate = strtotime('-'.	
LMT_CANCEL_TICKET_BEFORE, strtotime($courseDate['start_date'].' '.$courseDate['start_time']));			
			$cancellationAmount = ((strtotime(date('Y-m-d H:i:s')) >=  $cancellationDate)) ? (($paidAmount * LMT_CANCELLATION_AMOUNT)/100) : 0;
			
													
				do//create random code.
				{
					$randCode	=	$this->createRandom(LMT_RANDOM_CODE_LIMIT);
				}			
				while($this->getdbcount_sql("SELECT cancel_code FROM tblcourse_cancel_transaction WHERE cancel_code = '$randCode'") > 0);
				
				$cancelTrans = array();
				$cancelTrans['cancel_code'] = $randCode;
				$cancelTrans['trans_time'] = date('Y-m-d H:i:s');
				$cancelTrans['user_id'] = $userID;
				$cancelTrans['enrolled_id'] = unserialize(base64_decode($data['enrolled_id']));
				$cancelTrans['course_amount'] = $paidAmount;
				$cancelTrans['deduction'] = $cancellationAmount;
				$cancelTrans['refunded_amount'] = ($cancelTrans['course_amount'] - $cancelTrans['deduction']);
				$cancelTrans['currency_id'] = 1;
									
			if($this->db_insert('tblcourse_cancel_transaction', $cancelTrans))
				{
					$dataIns['enrolled_status_id']	=   LMT_CS_ENR_CANCELLED;
					$dataIns['status_reason_id']	=	LMT_CS_ENR_CANCEL_REASON_ADMIN; // Student cancellation
					$dataIns['status_changed_by']	=	$adminID;
					if($this->db_update('tblcourse_enrollments', $dataIns,"enrolled_id =".unserialize(base64_decode($data['enrolled_id']))))
						{
											
							$enrollmentDetail = $cls->getEnrollmentCancelMailDet(unserialize(base64_decode($data['enrolled_id'])), $userDateFormat, $userTimeFormat, "", LMT_SERVER_TIME_ZONE_OFFSET, $userTimeZone);
							
							$subject 						=  'Live Music Tutor Cancelled Enrollment';
							$varArr["{TPL_URL}"]			=	ROOT_URL;	
							$varArr["{TPL_NAME}"]	    	=  $userName;									
							$varArr["{TPL_CS_CODE}"]		=  $enrollmentDetail['course_code'];
							$varArr["{TPL_ENR_CODE}"]		=  $enrollmentDetail['enrollment_code'];
							$varArr["{TPL_TRANS_REF_CODE}"]	= $cancelTrans['cancel_code'];
							$varArr["{TPL_TITLE}"]			= $enrollmentDetail['title'];
							$varArr["{TPL_START_DATE}"]		= $enrollmentDetail['start_date'];
							$varArr["{TPL_START_TIME}"]		= $enrollmentDetail['start_time'];
							$varArr["{TPL_REFUND_AMOUNT}"]	= ($cancelTrans['course_amount'] - $cancelTrans['deduction']);
							
							$send =	$cms->sendMailCMS(LMT_ENR_CANCEL_TO_STUD_TPL, $userEmail, LMT_SITE_ADMIN_MAIL_ID, $subject, $varArr, 5); 	
							
							//Mail to instructor
							$mailDetails					=	$cls->getMailingDetails(unserialize(base64_decode($data['enrolled_id'])),LMT_SERVER_TIME_ZONE_OFFSET);
							$subject 						=  'Live Music Tutor Cancelled Enrollment';	
							$varArr["{TPL_URL}"]			=	ROOT_URL;
							$varArr["{TPL_NAME}"]			=  $mailDetails['name'];
							$varArr["{TPL_STUD_NAME}"]		=   $userName;
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
							$toIds	=	$userMgmt->getAllTplAdmin(LMT_MAIL_TPL_CANCEL_ENR_ADMIN);
							$send	=	$mailMgmt->sendMailAdmin(LMT_MAIL_TPL_CANCEL_ENR_ADMIN,$toIds,LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5);
							//mail to superadmin
							$send =	$cms->sendMailCMS(LMT_MAIL_TPL_CANCEL_ENR_ADMIN,LMT_ADMIN_CS_MAIL_ID,LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5);
							$this->clearData();			
							$this->redirectAction(false,"Enrollment has been cancelled successfuly", "Listing");
						}
					
				}
			else
				{
					$this->setPageError($this->getDbErrors());	
				}
			}
		public function redirectAction($loadData=true,$errMessage,$action)	
			{	
				$this->setPageError($errMessage);
				$this->executeAction($loadData,$action,true);	
			}		
		public function __construct()
			{
				$this->setClassName();
					$this->month	=	$_GET['month'];
					$this->year		=	$_GET['year'];
			}
		public function executeAction($loadData=true,$action="",$navigate=false,$sameParams=false,$newParams="",$excludParams="",$page="")
			{
				
				if(trim($action))	$this->setAction($action);//forced action
				$methodName	=		(method_exists($this,$this->getMethodName()))	? $this->getMethodName($default=false):$this->getMethodName($default=true);
				$this->actionToBeExecuted($loadData,$methodName,$action,$navigate,$sameParams,$newParams,$excludParams,$page);
				$this->actionReturn		=	call_user_func(array($this, $methodName));				
				$this->actionExecuted($methodName);
				return $this->actionReturn;
			}
		public function __destruct() 
			{
				parent::childKilled($this);
			}
	}