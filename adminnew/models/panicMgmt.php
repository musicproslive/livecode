<?php 
/**************************************************************************************
Created By 	:	Prem Pranav
Created On	:	25-03-2013
Description	:	Panic Class Management
**************************************************************************************/
class panicMgmt extends modelclass
	{
		public function panicMgmtListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				return;
			}
		public function panicMgmtFetch()
			{
				// Connect to MySQL database
				$page 			= 	0;	// The current page
				$sortname 		= 	'CP.created_on';	 // Sort column
				$sortorder	 	= 	'desc';	 // Sort order
				$qtype 			= 	'';	 // Search column
				$query 			= 	'';	 // Search string
				// Get posted data
			if (isset($_POST['page'])) 
				{
					if($_POST['page']==1 && isset($_SESSION['PAGE'][$this->getPageName()][$this->getAction()]) && empty($_POST['query']))
						{
							if($_SESSION['PAGE'][$this->getPageName()][$this->getAction()] >= 2 && $this->previousAction ==	$this->currentAction)	$page	=	1;
							else $page		=	$_SESSION['PAGE'][$this->getPageName()][$this->getAction()];
								
						}
					else
						{
							$page 				= 	mysqli_real_escape_string($this->con,$_POST['page']);
							$_SESSION['PAGE'][$this->getPageName()][$this->getAction()]	=	$page;
						}
				}
			if (isset($_POST['sortname'])) 
				{
					$sortname 	= 	mysqli_real_escape_string($this->con,$_POST['sortname']);
				}
			if (isset($_POST['sortorder'])) 
				{		
					$sortorder 	= 	mysqli_real_escape_string($this->con,$_POST['sortorder']);		
				}
			if (isset($_POST['qtype'])) 
				{
					$qtype 		= 	trim(mysqli_real_escape_string($this->con,$_POST['qtype']));
				}
			if(isset($_SESSION['QUERY'][$this->getPageName()][$this->getAction()]))
				{
					if(trim(mysqli_real_escape_string($this->con,$_POST['query'])) == '' && $this->previousAction ==	$this->currentAction)
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
					$query 		= 	trim(mysqli_real_escape_string($this->con,$_POST['query']));
					$_SESSION['QUERY'][$this->getPageName()][$this->getAction()]	=	$query;
					$_SESSION['QTYPE'][$this->getPageName()][$this->getAction()]	=	trim(mysqli_real_escape_string($this->con,$_POST['qtype']));
				}
			if (isset($_POST['rp'])) 
				{
					$rp 		= 	mysqli_real_escape_string($this->con,$_POST['rp']);
				}
			if(empty($rp))
				{
					$rp			=	LMT_SITE_ADMIN_PAGE_LIMIT;
				}
						
				// Setup sort and search SQL using posted data
				$sortSql				 = 	"order by $sortname $sortorder";
				$searchSql 				 = 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				
				// Get total count of records
				$sql					= 	"SELECT count(*) 
												FROM tblcourse_panic AS CP
												LEFT JOIN tblcourses AS C ON C.course_id = CP.course_id
												LEFT JOIN tblusers AS Ins ON C.Instructor_id = Ins.user_id	
												LEFT JOIN tblusers AS I ON CP.instructor_id = I.user_id 
												LEFT JOIN tblusers AS S ON CP.student_id = S.user_id 
												WHERE 1 $searchSql";							
				$result 				= 	$this->db_query($sql,0);
				$row 					= 	mysqli_fetch_array($result);
				$total					= 	$row[0];
				// Setup paging SQL
				$pageStart 				= 	($page-1)*$rp;
				if($pageStart<0)
					{
						$pageStart		=	0;
					}
				$limitSql 				= 	"limit $pageStart, $rp";
				// Return JSON data
				$data 					= 	array();
				$data['page'] 			= 	$page;
				$data['qtype'] 			= 	$qtype;
				$data['query'] 			= 	$query;
				$data['total'] 			= 	$total;
				$data['rows'] 			= 	array();
				$sql					= 	"SELECT   CP.instructor_id AS InstId,CP.student_id AS StudId,concat(S.first_name,' ',S.last_name) AS Student, C.title,CONCAT(Ins.first_name,' ',Ins.last_name) AS Instructor, CONCAT(I.first_name,' ',I.last_name) AS Instructor_panic,CP.reason, DATE_FORMAT(CP.created_on,'".$_SESSION['DATE_FORMAT']['M_DATE']." ".$_SESSION['DATE_FORMAT']['M_TIME']."') AS report_time
												FROM tblcourse_panic AS CP
												LEFT JOIN tblcourses AS C ON C.course_id = CP.course_id
												LEFT JOIN tblusers AS Ins ON C.Instructor_id = Ins.user_id
												LEFT JOIN tblusers AS I ON CP.instructor_id = I.user_id 
												LEFT JOIN tblusers AS S ON CP.student_id = S.user_id 
												WHERE 1 $searchSql $sortSql $limitSql";
				$results 				= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysqli_fetch_assoc($results)) 
					{
						$i++;
						if($row['StudId'] <>0 && $row['InstId'] <> 0)
							{
								$row['reported_by']			=	$row['Instructor_panic'];
								$row['reported_against']	=	$row['Student'];	
							}
						else if($row['StudId']<>0)
							{
								$row['reported_by']			=	$row['Student'];
								$row['reported_against']	=	"Course";
							}
						else
							{
								$row['reported_by']			=	$row['Instructor_panic'];
								$row['reported_against']	=	"Course";
							}
						
						$data['rows'][] = array
					(
				'id' => $row['id'],
				'cell' => array($i, $row['reported_by'],$row['reported_against'], $row['title'],$row['Instructor'],$row['reason'],$row['report_time'])
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