<?php 
/****************************************************************************************
Created by	:	Prem Pranav 
Created on	:	25-09-2012
Purpose		:	To track User's major Activity ( User log table)
******************************************************************************************/
class userLogTable extends modelclass
	{
		public function userLogTableListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return ;
			}
			
		public function userLogTableFetch()
			{
				$page 			= 	1;	// The current page
				$sortname 		= 	'UL.log_time';	 // Sort column
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
				$sortSql				 = 	" order by $sortname $sortorder ";
				$searchSql 				 = 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '$query%'" : '';
				// Get total count of records
				$sql					 = 	"SELECT count(*) FROM tbluser_log as UL";
				if($searchSql){
					$sql				.=	" LEFT JOIN tblusers as U on UL.user_id=U.user_id
											LEFT JOIN tbluser_login as L on L.login_id=U.login_id
											LEFT JOIN tbluser_roles as R on R.role_id=L.user_role
											LEFT JOIN tbldefaults AS D ON D.group_id = ".LMT_LOG_GROUP." AND UL.log_id = D.value
											where 1 $searchSql";							
				}
				
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
				
				$procedure				=	"CREATE PROCEDURE pdruser_log_table(IN AdminDateFormat VARCHAR(200), IN GroupId INT, IN SearchSql VARCHAR(200), IN SortSql VARCHAR(200), IN LimitSql VARCHAR(200)) 
											BEGIN 
											DECLARE _sqlQuery VARCHAR(2046);
											SET _sqlQuery = CONCAT('SELECT UL.ip,DATE_FORMAT (UL.log_time,\'',AdminDateFormat,'\' ) AS log_time,L.user_name,R.role_name,D.caption
											FROM tbluser_log as UL 
											LEFT JOIN tblusers as U on UL.user_id=U.user_id
											LEFT JOIN tbluser_login as L on L.login_id=U.login_id
											LEFT JOIN tbluser_roles as R on R.role_id=L.user_role
											LEFT JOIN tbldefaults AS D ON UL.log_id = D.value AND D.group_id = ',GroupId,'
											WHERE 1 ', SearchSql,' ',SortSql,' ',LimitSql);
											SET @sqlQuery = _sqlQuery;
											PREPARE dynquery FROM @sqlQuery;
											EXECUTE dynquery;
											DEALLOCATE PREPARE dynquery;
											END;";				
				if (!mysqli_query($this->con,"DROP PROCEDURE IF EXISTS pdruser_log_table") ||!mysqli_query($this->con,$procedure))
					{
						echo "Stored procedure creation failed: (" . mysqli_errno($this->con) . ") " . mysqli_error($this->con);
					}	
				
				if (!($results	=	mysqli_query($this->con,"CALL pdruser_log_table('".$_SESSION["DATE_FORMAT"]["M_DATE"].",".$_SESSION["DATE_FORMAT"]["M_TIME"]."',".LMT_LOG_GROUP.",'".$searchSql."','".$sortSql."','".$limitSql."')"))) 
					{
						echo " CALL failed: (" . mysqli_errno($this->con) . ") " . mysqli_error($this->con);
					}
											
				/*$sql 					= 	"SELECT UL.ip,DATE_FORMAT(UL.log_time,'". $_SESSION["DATE_FORMAT"]["M_DATE"]." , ".$_SESSION["DATE_FORMAT"]["M_TIME"]."') AS log_time,L.user_name,R.role_name,D.caption
											FROM tbluser_log as UL 
											LEFT JOIN tblusers as U on UL.user_id=U.user_id
											LEFT JOIN tbluser_login as L on L.login_id=U.login_id
											LEFT JOIN tbluser_roles as R on R.role_id=L.user_role
											LEFT JOIN tbldefaults AS D ON UL.log_id = D.value AND D.group_id = ".LMT_LOG_GROUP."
											WHERE 1	$searchSql	$sortSql $limitSql";																
				$results 				= 	$this->db_query($sql,0);*/
				
				$i			=	$pageStart;
				while ($row = mysqli_fetch_assoc($results)) 
					{
						$i++;
						
						$data['rows'][] = array
					(
				'id' => $row['id'],
				'cell' => array($i, $row['user_name'],$row['role_name'], $row['caption'], $row['log_time'],$row['ip'])
					);
				}
				ob_clean();
				$r =json_encode($data);
				
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