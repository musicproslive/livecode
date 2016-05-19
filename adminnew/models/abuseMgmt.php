<?php 
/**************************************************************************************
Created By 	:	Prem Pranav
Created On	:	27-09-2012
Description	:	Manage User Abuses 
**************************************************************************************/
class abuseMgmt extends modelclass
	{
		public function abuseMgmtListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				return;
			}
				public function abuseMgmtFetch(){
					// Connect to MySQL database
					$page 			= 	0;	// The current page
					$sortname 		= 	'id';	 // Sort column
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
					$sortSql				 = 	"order by $sortname $sortorder";
					$searchSql 				 = 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
					
					// Get total count of records
					$sql					= 	"SELECT count(*) 
													FROM tbluser_abuses AS A 
													LEFT JOIN tblusers AS RA ON A.reported_by = RA.user_id 
													LEFT JOIN tblusers AS RB ON A.reported_against=RB.user_id WHERE A.is_deleted=0 $searchSql";							
					$result 				= 	$this->db_query($sql,0);
					$row 					= 	mysql_fetch_array($result);
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
					$sql					= 	"SELECT concat(RA.first_name,' ',RA.last_name) AS reported_against,concat(RB.first_name,' ',RB.last_name) AS reported_by, A.id as id, A.abuse_report,DATE_FORMAT(A.report_time,'".$_SESSION['DATE_FORMAT']['M_DATE']." ".$_SESSION['DATE_FORMAT']['M_TIME']."') AS report_time
													FROM tbluser_abuses AS A 
													LEFT JOIN tblusers AS RA ON A.reported_by = RA.user_id 
													LEFT JOIN tblusers AS RB ON A.reported_against=RB.user_id 
													WHERE A.is_deleted=0 $searchSql $sortSql $limitSql";
					
					$results 				= 	$this->db_query($sql,0);
					$i			=	$pageStart;
					while ($row = mysql_fetch_assoc($results)) 
						{
							$i++;
			
							$row['action']	="<a href=\"abuseMgmt.php?actionvar=Delete&id=".base64_encode($row['id'])."\" class=\"Second_link\" style=\"color:blue\"> 		<img src=\"images/delete.gif\" alt=\"Delete Data\" title=\"Delete Data\"/></a>";
							
							$data['rows'][] = array
						(
					'id' => $row['id'],
					'cell' => array($i, $row['reported_against'],$row['reported_by'], $row['abuse_report'],$row['report_time'],$row['action'])
						);
					}
					$r =json_encode($data);
					ob_clean();
					echo  $r;
					exit;
					}
			
		public function abuseMgmtDelete()
			{
				$data		=	$this->getData("get");
				$id			=	base64_decode($data['id']);
				$query 		= 	"UPDATE tbluser_abuses SET is_deleted=1 WHERE id=$id";
				if(mysql_query($query))
					{
						$this->setPageError(" Report Deleted !");
					}
				else
					{
						$this->setPageError(" Report Couldn't Delete. try again!");		
					}
				$this->executeAction(true,"Listing",true);	
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