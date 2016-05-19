<?php 
/****************************************************************************************
Created by	:	Prem Pranav 
Created on	:	26-09-2012
Purpose		:	To Manage Course Status
****************************************************************************************/
class courseStatus extends modelclass
	{
		public function courseStatusListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
		
				$searchData				=	$this->getData("request");
				$rec["status"]				=	$this->getdbcontents_sql("SELECT * FROM tbllookup_course_status");
				$rec["sel_status"]			=	$_POST["sel_status"];
				unset($_SESSION["sel_status"]);
				
				if(!empty($_POST["sel_status"]))
					{
						$_SESSION["sel_status"]	=$_POST["sel_status"];
					}
					
				return $rec;
			}
			
		public function courseStatusFetch()
			{
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
				if(isset($_SESSION['sel_status']))
				$searchSql 				= 	"AND SR.status_id = ".$_SESSION['sel_status'];
				
				// Get total count of records
				$sql					= 	"SELECT count(*) from tbllookup_course_status_reason AS SR LEFT JOIN tbllookup_course_status AS S ON S.id = SR.status_id WHERE 1 $searchSql";
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
				$sql 					= 	"SELECT SR.id,S.status,S.description,SR.reason 
											from tbllookup_course_status_reason AS SR LEFT JOIN tbllookup_course_status AS S ON S.id = SR.status_id 
											WHERE 1	$searchSql	$sortSql $limitSql";
											
				
				$results 				= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
		
						$row['action']	="<a href=\"courseStatus.php?actionvar=Edit&id=".$row['id']."\" class=\"Second_link\" style=\"color:blue\" title=\"Click here to view the details\"><img src=\"images/edit.gif\" alt=\"Edit Data\" title=\"Edit Data\"/></a>";
						
						$data['rows'][] = array
					(
				'id' => $row['id'],
				'cell' => array($i, $row['status'],$row['description'], $row['reason'], $row['action'])
					);
				}
				ob_clean();
				$r =json_encode($data);
				
				echo  $r;
				exit;
			}
					
		public function courseStatusAdd()
			{
				$data=$this->getData("request");
				if(empty($data['sel_status']))
				{
					$this->setPageError("Please Select a Status to Add Reason !");
					$this->executeAction(true,"Listing",true);	
				}
				else
				{
				$dataArr			=	end($this->getdbcontents_sql("SELECT *
									from tbllookup_course_status WHERE id=".$data['sel_status']));
				}
				return array("data"=>$dataArr);
			}
		public function courseStatusSave()
			{
				$data				=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tbllookup_course_status_reason",$data);
				$id= $this->db_insert("tbllookup_course_status_reason",$dataIns);
				if($id)	
				  {
						$this->setPageError("Data Inserted Successfully");
						$this->clearData("Save");
						$this->clearData("Add");
						$this->executeAction(true,"Listing",false);
				  }
				else
				 {
					 $this->setPageError("Data Insertion Unsuccessfull");
				 	$this->executeAction(true,"Add",false);
				 }

			}		
		public function courseStatusEdit()
			{
				$data=$this->getData("request");
				$dataArr			=	end($this->getdbcontents_sql("SELECT SR.*,S.status from tbllookup_course_status_reason AS SR LEFT JOIN tbllookup_course_status AS S on SR.status_id=S.id WHERE SR.id=".$data['id']));
				return array("data"=>$dataArr);
			}
		public function courseStatusUpdate()
			{
				$data				=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tbllookup_course_status_reason",$data);
				$id= $this->db_update("tbllookup_course_status_reason",$dataIns,$this->dbSearchCond("=","id",$data['id']));
				if($id)	
				  {
						$this->setPageError("Data Updated Successfully");
						$this->clearData("Update");
						$this->clearData("Edit");
						$this->executeAction(true,"Listing",false);
				  }
				else
				 {
					 $this->setPageError("Data Update Unsuccessfull");
				 	$this->executeAction(true,"Edit",false);
				 }
			}
		public function courseStatusReset()
			{
				$this->clearData("Search");
				header("Location: courseStatus.php");
				exit;	
			}
		public function courseStatusCancel()
			{
				$this->clearData("Add");
				header("Location:courseStatus.php");
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
				$this->tab_defaults_group	=	"tbluser_category";
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