<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	07-07-2011
Purpose		:	To Manage Tutors
******************************************************************************************/
class cancelmonthelyreport extends modelclass
	{
		public function cancelmonthelyreportListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName(),"cancelyearlyreport.php"));
				return;
				
			}
		public function cancelmonthelyreportFetch(){
			
					// Connect to MySQL database;
					$page 			= 	0;	// The current page
					$sortname 		= 	'c.course_id';	 // Sort column
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
					$searchSql 				.= 	($qtype != '' && $query != '') ? " AND  `$qtype` LIKE '%$query%'" : '';
					
					$sql					= 	"SELECT count(distinct MONTH(e.`created_on`)) FROM `tblcourse_cancel_transaction` p  
										LEFT JOIN `tblcourse_enrollments` e ON p.`enrolled_id`=e.`enrolled_id`
										LEFT JOIN  `tblcourses` c ON e.`course_id`=c.`course_id`	
										WHERE p.is_refunded=1  AND e.`enrolled_status_id` =".LMT_CS_ENR_CANCELLED." $searchSql ";
					$result 				= 	$this->db_query($sql,1);
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
					
					$sql		=	"SELECT count(e.`enrolled_id`) as total,SUM(p.`refunded_amount`) as amount, MONTHNAME(e.`created_on`) as month,
										YEAR(e.`created_on`) as year,MONTH(e.`created_on`) as m
										FROM `tblcourse_cancel_transaction` p  
										LEFT JOIN `tblcourse_enrollments` e ON p.`enrolled_id`=e.`enrolled_id`
										LEFT JOIN  `tblcourses` c ON e.`course_id`=c.`course_id`	
										WHERE p.is_refunded=1  AND e.`enrolled_status_id` =".LMT_CS_ENR_CANCELLED ;
					
				 	$sql		.=	"$searchSql GROUP BY MONTH(e.`created_on`)  $sortSql $limitSql";
					$results 	= 	$this->db_query($sql,1);
					
					$i			=	$pageStart;
					while ($row = mysql_fetch_assoc($results)) 
						{
							$details		=	"<a href='canceldailyreport.php?year={$row['year']}&month={$row['m']}'><img src='images/inner_details.png' alt='View details' ></a>";
							$i++;
							$data['rows'][] = array(
							'id' => $row['course_id'],
							'cell' => array($i,$row["month"].",".$row['year'],$row['total'],$row['symbol']."$ ".$row['amount'],$details)
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