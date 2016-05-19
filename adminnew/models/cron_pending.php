<?php 
/****************************************************************************************
Created by	:	BHASKAR
Created on	:	26 sept 2013
Purpose		:	For Daily Enrollment Report Management
******************************************************************************************/
class cron_pending extends modelclass
	{
		
		public function cron_pendingFetch(){
			
					$page 			= 	0;	// The current page
					$sortname 		= 	'ctq.processing_status';	 // Sort column
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
					 				 FROM tblcc_transaction_queue";//$cond $searchSql ";

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

				
				 $curSrvrTime = date('Y-m-d H:i:s');
				 
				 $sql		= "SELECT ctq.cc_transaction_queue_id, ctq.processing_status,
						 ce.enrolled_id, ce.enrollment_code, ce.enrolled_status_id,
						 es.status enroll_status,
				         c.course_id, c.course_code, c.title, c.start_date, c.start_time, cd.time AS course_duration, c.description,
				         concat(c.start_date, ' ', c.start_time) AS course_date_time, c.course_status_id,
				         cs.status AS course_status,
				         cp.cost AS course_amount,
				         ct.currency_id, ct.code AS currency_code,
				         u.user_id, u.first_name, u.last_name,uc.card_no,
				         u.time_zone_id, SUBSTRING(tz.gmt, 5, 6) AS gmt_timezone, u.time_format_id,
				         u.is_deleted user_delete_state, ut.mysql_date_format, ut.mysql_time_format,
				         ul.user_name login_email, ul.is_deleted user_login_delete_state, ul.authorized,
				         uc.orbital_profile_id, TIMESTAMPDIFF(HOUR, '$curSrvrTime', concat(c.start_date, ' ', c.start_time)) diffHour
						FROM     tblcc_transaction_queue ctq
						         JOIN tblcourse_enrollments ce
						         	ON ce.enrolled_id = ctq.enrollment_id
						         JOIN tbllookup_enrolled_status es
						         	ON es.id = ce.enrolled_status_id
						         JOIN tblcourses c
						         	ON c.course_id = ce.course_id
						         JOIN tbllookup_course_status cs
						         	ON cs.id = c.course_status_id
						         JOIN tblcourse_prices cp
						         	ON cp.id = c.price_code
						         JOIN tbllookup_course_duration cd
						         	ON cd.id = c.duration
						         JOIN tblcurrency_type ct
						         	ON ct.currency_id = cp.currency_type
						         JOIN tblusers u
						         	ON u.user_id = ce.student_id
						         JOIN tbluser_login ul
						         	ON ul.login_id = u.login_id
						         JOIN tbllookup_user_timestamp ut
						         	ON ut.id = u.time_format_id
						         JOIN tbltime_zones tz
						         	ON tz.id = u.time_zone_id
						         LEFT JOIN tblusers_ccs uc
						         	ON uc.user_id = u.user_id
						AND uc.is_active = 1 AND uc.is_deleted = 0
						ORDER BY ctq.processing_status DESC";
				 
				
				//	 $sortSql	 =	" GROUP BY E.enrolled_id ". $sortSql;	
					 $sql		.=	" ".$limitSql;  // ".$searchSql." ".$sortSql." 	
				     $results 	 = 	$this->db_query($sql,0);
				     
					$i			 =	$pageStart;
					while ($row = mysql_fetch_assoc($results)) 
						{ 
							$amount = $row['course_amount']." ". $row['currency_code'];
							$name =  $row['first_name'] ." ".$row['last_name'];
							if ($row['diffHour'] < 0 ) { $rHour = "-NA-"; }else{ $rHour =$row['diffHour']." hours"; }
							if ($row['processing_status'] == "TBP") {
								$row['processing_status'] = "Pending";
							}elseif ( $row['processing_status']=="SUCC") {
								$row['processing_status'] ="Success";}
							$i++;
								$data['rows'][] = array(
								'id' => $row['course_id'],
								'cell' => array( $i, $row['cc_transaction_queue_id'], $row['enroll_status'], $row['processing_status'], 
												 $row['title'], $row['description'], $row['course_duration'], $row['course_date_time'],
												$rHour, $amount, $row['course_status'],
												$name, $row['card_no'], $row['login_email'] )
							);
						}
					$r =json_encode($data);
					ob_clean();
					echo  $r;
					exit;
					
		
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