<?php 
/****************************************************************************************
Created by	:	Suneesh 
Created on	:	1-12-2011
Purpose		:	To Manage FAQ
*****************************************************************************************/
class faq extends modelclass
	{
		public function faqListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return;
			}
			
			
		public function  faqFetch(){
			
		// Connect to MySQL database
		$page = 0;	// The current page
		$sortname = 'created_on';	 // Sort column
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
		$sql 				= 	"SELECT count(*) FROM `tblfaq` ".$searchSql;
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
		$sql 				= 	"SELECT *,DATE_FORMAT(created_on,'".$_SESSION["DATE_FORMAT"]["M_DATE"]." ". $_SESSION["DATE_FORMAT"]["M_TIME"]."') AS created_on FROM tblfaq  " .$searchSql."".$sortSql;
		 //.$limitSql;
		$results 			= 	$this->db_query($sql,0);
		$i					=	0;
		while ($row = mysql_fetch_assoc($results)) 
			{
				$i++;
				
				if ($row['is_deleted']==1)
				{
					$row['status']		=	"<a href=\"faq.php?actionvar=Statuschange&id=".$row['faq_id']."\" class=\"Second_link\">
					<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to activate\"></a>";
				}
				else
				{					
					$row['status']		=	"<a href=\"faq.php?actionvar=Statuschange&id=".$row['faq_id']."\" class=\"Second_link\">
					<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to Inactivate\"></a>";
				}							
				
				$row['edit']	=	"<a href=\"faq.php?actionvar=Editform&id=".$row['faq_id']."\" class=\"Second_link\">
									<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";			

				$row['view']	=	"<a href=\"faq.php?actionvar=Viewform&id=".$row['faq_id']."\" class=\"Second_link\">
									<img src=\"../images/view.png\" border=\"0\" title=\"View Details\"></a>";			
			
				$row['delete']	=	"<a href=\"faq.php?actionvar=Deletedata&id=".$row['faq_id']."\" class=\"Second_link\" onclick=\"return delall()\">
									<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";
				$data['rows'][] =	 array
					(
						'id' => $row['user_id'],
						'cell' => array($i, $row['faq'], $row['faq_answer'],$row['created_on'],$row['view'],$row['status'],$row['edit'],$row['delete'])
					);
			}
			ob_clean();
		$r 					=	json_encode($data);
		echo  $r;
		exit;
		
		}
		public function faqStatuschange()
			{  
				$details	=	$this->getData("request");
				$id 		= 	$details['id'];
				$sql		=	"select * from tblfaq where faq_id=".$id;
				$data		=	end($this->getdbcontents_sql($sql));
				if($data['is_deleted']==1)
					{		
						$dataUpdate	=	array();
					    $dataUpdate['is_deleted']	=	"0";
						$this->db_update("tblfaq",$dataUpdate,"faq_id =$id",1);
						$this->setPageError("Status changed successfully");
						return $this->executeAction(false,"Listing",true);
					}
				else 
					{  
						$dataUpdate	=	array();
						$dataUpdate['is_deleted']	=	"1";
						$this->db_update("tblfaq",$dataUpdate,"faq_id =$id",1);
						$this->setPageError("Status changed successfully");
						return $this->executeAction(false,"Listing",true);
					}
			}
		public function faqViewform()
			{
				$data				=	$this->getData("get");
				$data				=	$this->getFaqDetails($data['id']);
				return array("data"=>$data);
			}
		public function faqCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function faqAddform()
			{
			}
	
		public function faqSavedata()
			{
				$data					=	$this->getData("post");
				$dataIns				=	$this->populateDbArray("tblfaq",$data);
				$dataIns['created_on']	=	date('Y-m-d H:i:s');
				$usertbl				=	$this->db_insert("tblfaq",$dataIns);
					if($usertbl)	
						{
							$this->setPageError("Inserted Successfully");
							$this->clearData("Savedata");
							$this->clearData("Addform");						
							return $this->executeAction(false,"Listing",true);
						}
					else
						{	
							$this->setPageError($this->getPageError());
							$this->executeAction(true,"Listing",true);
						}
			}
		public function faqDeletedata()
			{	
				$data				=	$this->getData("get");
				$data				=	$this->deleteFaq($data['id']);
			}
		public function faqEditform()
			{
				$data				=	$this->getData("get");
				$data				=	$this->getFaqDetails($data['id']);//print_r($data);exit;
				return array("data"=>$data);
			}
				
		public function faqUpdatedata()
			{
				$data					=	$this->getData("post");
				$dataIns				=	$this->populateDbArray("tblfaq",$data);
				//print_r($dataIns);exit;
				$updateStatus			=	$this->db_update("tblfaq",$dataIns,"faq_id='".$data['faq_id']."'",1);
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
		public function getFaqDetails($res)
			{
				$sql				=	"SELECT * FROM tblfaq where faq_id=".$res;
			    $result				=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		public function deleteFaq($id)
			{	
		  		$query 		= 	"DELETE FROM tblfaq  WHERE faq_id = $id";
				$result		=	$this->getdbcontents_sql($query);
				return $this->executeAction(false,"Listing",true);
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