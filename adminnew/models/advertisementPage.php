<?php  
/****************************************************************************************
Created by	:	Arvind  
Created on	:	04-08-2011
Purpose		:	To Manage advertisement
******************************************************************************************/
class advertisementPage extends modelclass
	{
		public function advertisementPageListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return;
			}
		public function advertisementPageFetchAdvertisements()
			{
				$page = 0;	// The current page
				$sortname = '';	 // Sort column
				$sortorder = '';	 // Sort order
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
				
				
				$sortSql		= 	" order by $sortname $sortorder";
				$searchSql 			= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				// Get total count of records
				$sql 				= 	"SELECT count(*) FROM `tbladvertisement_pages` WHERE `is_deleted`=0 ".$searchSql;
				$result				= 	$this->db_query($sql,0);
				$row 				= 	mysql_fetch_array($result);
				$total 				= 	$row[0];
				// Setup paging SQL
				$pageStart 			= 	($page-1)*$rp;
				if($pageStart<0)
					{
						$pageStart	=	0;
					}
				$limitSql 			= 	" limit $pageStart, $rp";
				// Return JSON data
				$data				= 	array();
				$data['page'] 		= 	$page;
				$data['qtype'] 		= 	$qtype;
				$data['query'] 		= 	$query;
				$data['total']		= 	$total;
				$data['rows'] 		= 	array();
				$sql 				= 	"SELECT * FROM `tbladvertisement_pages` WHERE `is_deleted`=0 " .$searchSql." ".$sortSql." ".$limitSql;
				$results 			= 	$this->db_query($sql,0);
				$i					=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;			
										
						$row['sideAds']	=	"<a href=\"advertisementPageView.php?id=".$row['refference_name']."\" class=\"Second_link\" >
											<img src=\"../images/view.png\" border=\"0\" title=\"Click here to add details\" ></a>";
						$row['topAds']	=	"<a href=\"advertisementPageView.php?id=".$row['refference_name']."_TOP\" class=\"Second_link\" >
											<img src=\"../images/view.png\" border=\"0\" title=\"Click here to add details\" ></a>";
						$data['rows'][] =	 array
							(
								'id' => $row['advertisement_id'],
								'cell' => array($i,$row['page_name'], $row['refference_name'], $row['topAds'],$row['sideAds'])
							);
					}
					
				$r 					=	json_encode($data);
				ob_clean();
				echo  $r;
				exit;
			}
		public function advertisementPageAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		public function advertisementPagesave()
			{
				$data					=	$_POST;
				$ads					=	array();
				$ads['page_name']		=	$data['page_name'];
				$ads['refference_name']	=	$data['refference_name'];
				$dataIns1				=	$this->populateDbArray("tbladvertisement_pages",$ads);
				$adsID					=	$this->db_insert("tbladvertisement_pages",$dataIns1);
			}
		
		public function advertisementPageDeletedata()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new advertisementPage;
				$data				=	$memberObj->deleteadvertisement($data['id']);
				return array("data"=>$this->getHtmlData($data));
			}
		public function deleteadvertisement($id)
			{
				$query 		= 	"UPDATE tbladvertisement_pages  SET is_deleted='1' WHERE advertisement_id='$id'";	
				$result		=	$this->getdbcontents_sql($query);
				return $this->executeAction(false,"Listing",true);return result;
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