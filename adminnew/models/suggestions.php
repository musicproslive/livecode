<?php 
/****************************************************************************************
Created by	:	Suneesh 
Created on	:	2-12-2011
Purpose		:	To Manage Buglist/Suggestions
*****************************************************************************************/
class suggestions extends modelclass
	{
		public function suggestionsListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return;
			}
		public function suggestionsStatuschange()
			{  
				$details	=	$this->getData("request");
				$id 		= 	$details['id'];
				$sql		=	"select * from tblbug_report where bug_id=".$id;
				$data		=	end($this->getdbcontents_sql($sql));
				if($data['status']==1)
					{		
						$dataUpdate	=	array();
					    $dataUpdate['status']	=	"0";
						$this->db_update("tblbug_report",$dataUpdate,"bug_id =$id",1);
						$this->setPageError("Status changed successfully");
						return $this->executeAction(false,"Listing",true);
					}
				else 
					{  
						$dataUpdate	=	array();
						$dataUpdate['status']	=	"1";
						$this->db_update("tblbug_report",$dataUpdate,"bug_id =$id",1);
						$this->setPageError("Status changed successfully");
						return $this->executeAction(false,"Listing",true);
					}
					
					
					
					
			}
			
		public 	function suggestionsFetch(){
				// Connect to MySQL database
				$page = 0;	// The current page
				$sortname = 'status';	 // Sort column
				$sortorder = 'asc';	 // Sort order
				$qtype = '';	 // Search column
				$query = '';	 // Search string
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
				$searchSql			=	"WHERE 1";
				
				if(!empty($_GET['field']) && !empty($_GET['keyword']))
					{
						$searchSql	.=	" AND `".$_GET['field']."`='".$_GET['keyword']."%' ";
					}
				// Setup sort and search SQL using posted data
				$sortSql			 = 	" order by $sortname $sortorder";
				$searchSql 			.= 	($qtype != '' && $query != '') ? " AND  `$qtype` LIKE '%$query%'" : '';
				// Get total count of records
				$sql 				= 	"SELECT count(*) FROM `tblbug_report` AS b LEFT JOIN tblusers AS u ON u.user_id = b.user_id ".$searchSql;
				$result				= 	$this->db_query($sql,0);
				$row 				= 	mysql_fetch_array($result);
				$total 				= 	$row[0];
				// Setup paging SQL
				$pageStart 			= 	($page-1)*$rp;
				if($pageStart<0)
					{
						$pageStart	=	0;
					}
				$limitSql 			= 	"limit $pageStart, $rp";
				// Return JSON data
				$data				= 	array();
				$data['page'] 		= 	$page;
				$data['qtype'] 			= 	$qtype;
				$data['query'] 			= 	$query;
				$data['total']		= 	$total;
				$data['rows'] 		= 	array();
				$sql 				= 	"SELECT b.*,concat(u.first_name,' ',u.last_name) AS name, DATE_FORMAT(created_on,'".$_SESSION["DATE_FORMAT"]["M_DATE"]." ". $_SESSION["DATE_FORMAT"]["M_TIME"]."') AS created_on FROM tblbug_report AS b LEFT JOIN tblusers AS u ON u.user_id = b.user_id " .$searchSql." ".$sortSql." ".$limitSql;
				//file_put_contents("file.txt",$sql);
				$results 			= 	$this->db_query($sql,0);
				$i					=	0;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						
						if ($row['status']==0)
						{
							$row['status']		=	"<a href=\"suggestions.php?actionvar=Statuschange&id=".$row['bug_id']."\" class=\"Second_link\" onclick=\"return status()\">
							<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to change the status to be solved\"></a>";
						}
						else
						{					
							$row['status']		=	"<a href=\"suggestions.php?actionvar=Statuschange&id=".$row['bug_id']."\" class=\"Second_link\" onclick=\"return status()\">
							<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to change the status to fixed\"></a>";
						}							
						
						$row['view']	=	"<a href=\"suggestions.php?actionvar=Viewform&id=".$row['bug_id']."\" class=\"Second_link\">
											<img src=\"../images/view.png\" border=\"0\" title=\"View Details\"></a>";			
					
		/*				$row['delete']	=	"<a href=\"suggestions.php?actionvar=Deletedata&id=".$row['bug_id']."\" class=\"Second_link\" onclick=\"return delall()\">
											<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";
		*/				$data['rows'][] =	 array
							(
								'id' => $row['user_id'],
								'cell' => array($i, $row['subject'], html_entity_decode($row['description']), $row['name'],$row['created_on'],$row['view'],$row['status'])
							);
					}
					 //file_put_contents("file.txt",$text.$sql);
					 ob_clean();
					$r 					=	json_encode($data);
					echo  $r;
					exit;
				} 
		public function suggestionsViewform()
			{
				$data				=	$this->getData("get");
				$data				=	$this->getBugDetails($data['id']);
				return array("data"=>$data);
			}
		public function suggestionsCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function getBugDetails($res)
			{
				$sql				=	"SELECT * FROM tblbug_report where bug_id=".$res;
			    $result				=	end($this->getdbcontentshtml_sql($sql));
				return $result;
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
				$methodName	=		(method_exists($this,$this->getMethodName()))? $this->getMethodName($default=false):
				$this->getMethodName($default=true);
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