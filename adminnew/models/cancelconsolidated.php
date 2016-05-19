<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	07-07-2011
Purpose		:	To Manage Tutors
******************************************************************************************/
class cancelconsolidated extends modelclass
	{
		
	
		public function cancelconsolidatedListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				$data			=	 $_POST;
				unset($_SESSION["startDate"]);
				unset($_SESSION["endDate"]);			
				if(!empty($data["txtStartDate"])){
					$_SESSION["startDate"]		=	$data["txtStartDate"];
				}
				if(!empty($data["txtEndDate"])){
					$_SESSION["endDate"]	=	$data["txtEndDate"];
				}
			return array("s_time"=>$data["txtStartDate"],"e_time"=>$data["txtEndDate"]);
			}
		public function cancelconsolidatedFetch(){
			
					$page 			= 	0;	// The current page
					$sortname 		= 	'e.created_on';	 // Sort column
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
							$rp			=	LMT_SITE_ADMIN_PAGE_LIMIT;
						}
					// Setup sort and search SQL using posted data
					$sortSql				 = 	" order by $sortname $sortorder";
					$searchSql 				 = 	($qtype != '' && $query != '') ? " AND   $qtype  LIKE '%$query%'" : '';
					
					$cond			 =	" AND    e.enrolled_status_id =".LMT_CS_ENR_CANCELLED;
					if(!empty($_SESSION["startDate"])){
						 $cond		.=	 " AND UNIX_TIMESTAMP(p.trans_time)>".strtotime($_SESSION["startDate"]);
					}
					if(!empty($_SESSION["endDate"])){
						 $cond		.=	 " AND UNIX_TIMESTAMP(p.trans_time)<".strtotime($_SESSION["endDate"]);
					}
					$sql					= 	"SELECT count(*) FROM `tblcourse_cancel_transaction` p 
									 LEFT JOIN `tblcourse_enrollments` e ON p.`enrolled_id`= e.`enrolled_id` 
									 LEFT JOIN  `tblcourses` c ON e.`course_id`=c.`course_id`
									 LEFT JOIN `tblcurrency_type` cur ON cur.`currency_id`=p.`currency_id`
									 LEFT JOIN `tblusers`  ins  ON c.`instructor_id`=ins.`user_id`	
									 LEFT JOIN `tblusers`  u  ON e.`student_id`=u.`user_id`									 
									 LEFT JOIN `tbllookup_instructor_level`  l  ON l.`id`=ins.`instructor_level`									 									 LEFT JOIN `tbllookup_course_type` ct ON ct.`id`=c.`course_type_id`			
									 WHERE p.is_refunded=1 $cond $searchSql";				 				 
					$result 				= 	$this->db_query($sql,0);
					$row 					= 	mysql_fetch_array($result);
					$total					= 	$row[0];
					// Setup paging SQL
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
				
				
			 	 $sql		=		"SELECT  ct.`type`,l.`level_name`,p.*,p.`refunded_amount` as cost,c.`title`,
					 			 	  e.`created_on`,cur.`symbol`,CONCAT(ins.`first_name`,' ',ins.`last_name`) as instructor,CONCAT(u.`first_name`,' ',u.`last_name`) as student		   
					 				,  DATE_FORMAT(e.created_on,'".$_SESSION["DATE_FORMAT"]["M_DATE"]."')  AS created_on		   
					 					FROM `tblcourse_cancel_transaction` p 
										 LEFT JOIN `tblcourse_enrollments` e ON p.`enrolled_id`= e.`enrolled_id` 
										 LEFT JOIN  `tblcourses` c ON e.`course_id`=c.`course_id`
										 LEFT JOIN `tblcurrency_type` cur ON cur.`currency_id`=p.`currency_id`
										 LEFT JOIN `tblusers`  ins  ON c.`instructor_id`=ins.`user_id`	
										 LEFT JOIN `tblusers`  u  ON e.`student_id`=u.`user_id`									 
										 LEFT JOIN `tbllookup_instructor_level`  l  ON l.`id`=ins.`instructor_level`									 									 	 LEFT JOIN `tbllookup_course_type` ct ON ct.`id`=c.`course_type_id`			
										 WHERE p.is_refunded=1 $cond $searchSql $sortSql $limitSql ";
					$results 	= 	$this->db_query($sql,0); 
					
					$i			=	$pageStart;
					while ($row = mysql_fetch_assoc($results)) 
						{
							$i++;
							
							if(empty($row["level_name"])){
								$row["level_name"]	=	"Beginner";
							
							}
							$data['rows'][] = array(
							'id' => $row['course_id'],
							'cell' => array($i, $row['student'],$row['title'],$row['type'],$row['instructor'],$row["level_name"],$row['created_on'],$row['symbol']." ".$row['course_amount'],$row['symbol']." ".$row['deduction'],$row['symbol']." ".$row['cost'])
						);
					}
					$r =json_encode($data);
					ob_clean();
					echo  $r;
					exit;
					
		
			}
		public function cancelconsolidatedReset()
			{
				$this->clearData("Search");
				$this->clearData("Listing");
				$this->executeAction(false,"Listing",true,false);	
			}
		public function redirectAction($loadData=true,$errMessage,$action)	
			{	
				$this->setPageError($errMessage);
				$this->executeAction($loadData,$action,true);	
			}		
		public function __construct()
			{
				$this->setClassName();
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