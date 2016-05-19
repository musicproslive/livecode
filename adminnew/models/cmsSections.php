<?php 
/****************************************************************************************
Created by	:	Suneesh 
Created on	:	21-Aug-2012
Purpose		:	To Manage CMS Sections
*****************************************************************************************/
class cmsSections extends modelclass
	{
		public function cmsSectionsListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				return;
			}
		public function cmsSectionsFetch()
			{
			
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
				// Setup sort and search SQL using posted data
				$sortSql				 = 	"order by $sortname $sortorder";
				$searchSql 				 = 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				// Get total count of records
				$sql					= 	"SELECT count(*) from tblcms_section where 1 $searchSql";
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
				$sql 					= 	"SELECT * from tblcms_section where 1 $searchSql $sortSql $limitSql";							
				$results 	= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
				if ($this->permissionCheck("Edit"))
				$row['edit']	=	"<a href=\"cmsSections.php?actionvar=Editform&id=".$row['id']."\" class=\"Second_link\">
									<img src=\"../images/edit.png\" border=\"0\" title=\"Edit CMS Details\"></a>";			
				if ($this->permissionCheck("Delete"))
				$row['delete']	=	"<a href=\"cmsSections.php?actionvar=Deletedata&id=".$row['id']."\" class=\"Second_link\" onclick = \"return delall()\">
									<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";
						$data['rows'][] = array
					(
				'id' => $row['id'],
				'cell' => array($i, $row['section'], $row['preference'],$row['edit'],$row['delete'])
					);
				}

				$r =	json_encode($data);
				ob_clean();
				echo  $r;
				exit;
			}
		public function cmsSectionsSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function cmsSectionstReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		public function cmsSectionsAddform()
			{
			}
		
		public function cmsSectionsEditform()
			{
				$data				=	$this->getcmsDetails($_GET['id']);
				return array("data"=>$this->getHtmlData($data));
			}
			
		public function cmsSectionsUpdatedata()
			{
				$details	=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tblcms_section",$details);
				$updateStatus=	$this->db_update("tblcms_section",$dataIns,"id='".$details['id']."'",1);
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
		public function cmsSectionsSavedata()
			{
				$data		=	$this->getData("post");
				$dataIns	=	$this->populateDbArray("tblcms_section",$data);
				if($this->db_insert("tblcms_section",$dataIns))	
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
			
		public function cmsSectionsDeletedata()
			{
				$qry		=	"Delete from tblcms_section where id =".$_GET['id'];
				if($this->db_query($qry))	
						$this->setPageError("Deleted Successfully");
				else
						$this->setPageError($this->getPageError());
						
				$this->executeAction(true,"Listing",true);
			}	
		
		public function cmsSectionsViewform()
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
		
		public function getcmsDetails($Id="",$args="1")
			{  
				$sql					=	"select * from tblcms_section where id='$Id' and ".$args;
				$result					=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function deletecms($id)
		{
			$qry		=	"delete from tblcms where id ='$id'";
			$result		=	$this->getdbcontents_sql($qry);
			return $this->executeAction(false,"Listing",true);return result;
		}
		
	}