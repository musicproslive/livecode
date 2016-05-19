<?php 
/****************************************************************************************
Created by	:	Suneesh 
Created on	:	21-Aug-2012
Purpose		:	To Manage Default Groups 
*****************************************************************************************/
class defaultGroups extends modelclass
	{
		public function defaultGroupsListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return;
			}
		public function defaultGroupsFetchGroup()
			{
					// Connect to MySQL database
					$page 			= 	0;	// The current page
					$sortname 		= 	'preference';	 // Sort column
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
					$searchSql		=	" WHERE  1 ";
					if(!empty($_GET['field']) && !empty($_GET['keyword']))
						{
							$searchSql		.=	" AND `".$_GET['field']."`='".$_GET['keyword']."%' ";
						}
					// Setup sort and search SQL using posted data
					$sortSql				 = 	"order by `$sortname` $sortorder";
					$searchSql 				.= 	($qtype != '' && $query != '') ? " AND  `$qtype` LIKE '%$query%'" : '';
					// Get total count of records
					//$sql = "SELECT count(*)	from tbllogin_tracker   $searchSql";
					$sql					= 	"SELECT count(*) from tbldefaults_group";
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
					$sql 					= 	"SELECT * from tbldefaults_group $searchSql $sortSql $limitSql";
			
					$results 	= 	$this->db_query($sql,0);
					$i			=	$pageStart;
					while ($row = mysql_fetch_assoc($results)) 
						{
							$i++;
					if ($this->permissionCheck("Edit"))
					$row['edit']	=	"<a href=\"defaultGroups.php?actionvar=Editform&id=".$row['id']."\" class=\"Second_link\">
										<img src=\"../images/edit.png\" border=\"0\" title=\"Edit CMS Details\"></a>";			
					if ($this->permissionCheck("Delete"))
					$row['delete']	=	"<a href=\"defaultGroups.php?actionvar=Deletedata&id=".$row['id']."\" class=\"Second_link\" onclick = \"return delall()\">
										<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";
							$data['rows'][] = array
						(
					'id' => $row['id'],
					'cell' => array($i, $row['group'], $row['preference'],$row['edit'],$row['delete'])
						);
					}
					$r =	json_encode($data);
					ob_clean();
					echo  $r;
					exit;
			}
		public function defaultGroupsSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function defaultGroupstReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		public function defaultGroupsAddform()
			{
			}
		
		public function defaultGroupsEditform()
			{
				$data				=	$this->getdefaultDetails($_GET['id']);
				return array("data"=>$this->getHtmlData($data));
			}
			
		public function defaultGroupsUpdatedata()
			{
				$details	=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tbldefaults_group",$details);
				$updateStatus=	$this->db_update("tbldefaults_group",$dataIns,"id='".$details['id']."'",1);
				if($updateStatus)
					{
						$this->setPageError("Updated Successfully");
						$this->clearData();
						$this->clearData("Editform");						
						return $this->executeAction(false,"Listing",true);			
					}
				else
					{
						$this->setPageError($this->getDbErrors());
						$this->executeAction(false,"Editform",true,true);
					}			
			}
		public function defaultGroupsSavedata()
			{
				$data		=	$this->getData("post");
				$dataIns	=	$this->populateDbArray("tbldefaults_group",$data);
				if($this->db_insert("tbldefaults_group",$dataIns))	
					{
						$this->setPageError("Inserted Successfully");
						$this->clearData("Savedata");
						$this->clearData("Addform");						
						$this->executeAction(false,"Listing",true);
					}
				else
					{
						$this->setPageError($this->getPageError());
						$this->executeAction(true,"Addform",true);
					}
			}
			
		public function defaultGroupsDeletedata()
			{
				$qry		=	"Delete from tbldefaults_group where id =".$_GET['id'];
				if($this->db_query($qry))	
						$this->setPageError("Deleted Successfully");
				else
						$this->setPageError($this->getPageError());
						
				$this->executeAction(true,"Listing",true);
			}	
		
		public function defaultGroupsViewform()
			{
				$data				=	$this->getData("get");
				$data				=	$this->getcmsDetails($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
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
				//echo $this->getMethodName();
				//exit;
				return $this->actionReturn;
			}
		public function __destruct() 
			{
				parent::childKilled($this);
			}
		
		public function getdefaultDetails($Id="",$args="1")
			{  
				$sql					=	"select * from tbldefaults_group where id='$Id' and ".$args;
				$result					=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
	
	}