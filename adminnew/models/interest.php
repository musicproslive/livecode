<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	22-07-2011
Purpose		:	To Manage interest
*****************************************************************************************/
class interest extends modelclass
	{
		public function interestListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				$searchData				=	$this->getData("post","Search");
				$sortData				=	$this->getData("request","Search");
				$searchData['sortData']	=	$this->getData("request","Search");
				if($sortData['sortField'])
					{
						$orderBy			=	"order by ".$sortData["sortField"]." ".$sortData["sortMethod"];
					}
				
				if(trim($searchData["keyword"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "interest", "%".trim($searchData["keyword"])."%")." )";
					}
				if(trim($searchData["memberId"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "interest_id ", "%".trim($searchData["memberId"])."%")." )";
					}
				if($sortData['sortField'])
					{
						$orderBy			=	"order by ".$sortData["sortField"]." ".$sortData["sortMethod"];
					}
				
				if(trim($searchData["keyword"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "interest", "%".ltrim($searchData["keyword"])."%")." )";
					}
				if(trim($searchData["memberId"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("=", "interest_id ","%".trim($searchData["memberId"])."%").")";	
					}	
				$sql						=	"SELECT * FROM `tblinterest_master` ".$sqlFilter["selc"]." ".$sqlFilter["join"]." 
												where is_deleted=0".$sqlFilter["cond"].$sqlFilter["ord"]." ".$orderBy ;
				$this->addData(array("sql"=>$sql),"post","",false);
				//$spage				 		=	$this->create_paging("n_page",$sql,GLB_PAGE_CNT);
				$spage				 		=	$this->create_paging("n_page",$sql);
				$data				 		=	$this->getdbcontents_sql($spage->finalSql());
				if(!$data)						$this->setPageError("No records found !");
				$searchData					=	$this->getHtmlData($this->getData("post","Search",false));
				//print_r($data);	
				return array("data"=>$data,"spage"=>$spage,"searchdata"=>$searchData);
			}
		public function interestFetch(){
		
		
				

		// Connect to MySQL database
		$page = 0;	// The current page
		$sortname = 'interest';	 // Sort column
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
		if (isset($_POST['sortname'])) {
		$sortname = mysql_real_escape_string($_POST['sortname']);
		}
		if (isset($_POST['sortorder'])) {		
		$sortorder = mysql_real_escape_string($_POST['sortorder']);		
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
		if (isset($_POST['rp'])) {
		$rp = mysql_real_escape_string($_POST['rp']);
		}
		if(empty($rp)){
			$rp	=	LMT_SITE_ADMIN_PAGE_LIMIT;
		}
		
	
		$searchSql		=	" WHERE  `is_deleted`='0'  ";
		
		if(!empty($_GET['field']) && !empty($_GET['keyword'])){
			$searchSql		.=	" AND `".$_GET['field']."`='".$_GET['keyword']."%' ";
		}
		// Setup sort and search SQL using posted data
		$sortSql = "order by $sortname $sortorder";
		$searchSql .= ($qtype != '' && $query != '') ? " AND  `$qtype` LIKE '%$query%'" : '';
		// Get total count of records
		$sql = "SELECT count(*) FROM `tblinterest_master` ".$searchSql;
		
		$result = $this->db_query($sql,0);
		
		$row = mysql_fetch_array($result);
		$total = $row[0];
		// Setup paging SQL
		$pageStart = ($page-1)*$rp;
		if($pageStart<0){
			$pageStart=	0;
		}
		$limitSql = "limit $pageStart, $rp";
		// Return JSON data
		$data = array();
		$data['page'] = $page;
		$data['qtype'] 			= 	$qtype;
		$data['query'] 			= 	$query;
		$data['total'] = $total;
		$data['rows'] = array();
		$sql = "SELECT *,DATE_FORMAT(created,'".$_SESSION["DATE_FORMAT"]["M_DATE"]." ". $_SESSION["DATE_FORMAT"]["M_TIME"]."') AS created
		from tblinterest_master" . 
		$searchSql.""
		.$sortSql." ".$limitSql;
			 //.$limitSql;
		 
		 //file_put_contents("file.txt",$sql);
		 
		 
		$results = $this->db_query($sql,0);//	exit;
		
		
		
		$i=$pageStart;
		while ($row = mysql_fetch_assoc($results)) {
		$i++;
		/*$row['view']	="<a href=\"interest.php?actionvar=Viewform&id=".$row['interest_id']."\" class=\"Second_link\">
					<img src=\"../images/view.png\" border=\"0\" title=\"View Details\"></a>";*/
		
		$row['edit']	="<a href=\"interest.php?actionvar=Editform&id=".$row['interest_id']."\" class=\"Second_link\">
					<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";			
			
		$row['delete']	="<a href=\"interest.php?actionvar=Deletedata&id=".$row['interest_id']."\" class=\"Second_link\" onclick=\"return delall()\">
					<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";						
		$data['rows'][] = array(
		'id' => $row['interest_id'],
		'cell' => array($i, $row['interest'], $row['created'],$row['edit'],$row['delete'])
		);
		}
		$r =json_encode($data);
		ob_clean();
		echo  $r;
		exit;
			
		}
		
		public function interestSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function interestCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function interestReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		
		public function interestAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		
		public function interestAddform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new interest;
				$data				=	$memberObj->getallinterest($data['id']);
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
				
			}
							
		public function interestEditform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new interest;
				$data				=	$memberObj->getallinterest($data['id']);
				
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
			}
			
		public function interestUpdatedata()
			{
				$files					 =	$this->getData("files");
				$details				 =	$this->getData("request");
				$details['modified']	 =	date('Y-m-d H:i:s');
				$details['modified_by']  =	$_SESSION['sess_admin'];
				
				$dataIns	=	$this->populateDbArray("tblinterest_master ",$details);
				$updateStatus=	$this->db_update("tblinterest_master ",$dataIns,"interest_id='".$details['id']."'",1);
				if($updateStatus)
					{
						$this->setPageError("Updated Successfully");
						$this->clearData();
						$this->clearData("Editform");						
					   $this->executeAction(false,"Listing",true);			
					}
				else
					{
						$this->setPageError($this->getDbErrors());
						$this->executeAction(false,"Editform",true,true);
					}			
			}
		public function interestSavedata()
			{
				$data		=	$this->getData("files");
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$utypeId	=	$userSess["usertype"];
				$uid	 	=	$userSess["id"];
				
				$memberObj	=	new interest;
				$data		=	$this->getData("request");
				$data['created']	 =	date('Y-m-d H:i:s');
				$data['modified']	 =	date('Y-m-d H:i:s');
				$data['created_by']  =	$_SESSION['sess_admin'];
				$data['modified_by'] =	$_SESSION['sess_admin'];
				$dataIns	=	$this->populateDbArray("tblinterest_master",$data);
				//print_r($dataIns);exit;
				if(!$this->getPageError())
					{
						if($memberObj->createinterest($dataIns))	
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
			}
			
		public function interestDeletedata()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new interest;
				$data				=	$memberObj->deleteinterest($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
					
			}	
		
		public function createinterest($dataIns)
			{
					$creationSucces						=	$this->db_insert("tblinterest_master",$dataIns);
					if($creationSucces)
						{
							return $creationSucces;	
						}	
					else
						{
								$this->setPageError($this->getdbErrors());
								return false;
						}		
			}	
			
		public function interestViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new interest;
				$data				=	$memberObj->getallinterest($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
			}
		
		public function getallinterest($membersId="",$args="1")
			{	
				$sql					=	"select * from tblinterest_master where interest_id ='$membersId' and ".$args;
				$result					=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function deleteinterest($id)
		{
			$query 		= 	"UPDATE tblinterest_master SET is_deleted='1' WHERE interest_id='$id'";	
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