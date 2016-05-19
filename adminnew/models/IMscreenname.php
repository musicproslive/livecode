<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	22-07-2011
Purpose		:	To Manage IM screen name
*****************************************************************************************/
class IMscreenname extends modelclass
	{
		public function IMscreennameListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return;
			}
			
		public function IMscreennameFetch(){
					// Connect to MySQL database
				$page = 0;	// The current page
				$sortname = 'im_id';	 // Sort column
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
				$sql = "SELECT count(*)	from tblimcode_master   $searchSql";
				
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
				 $sql = "SELECT *
				from tblimcode_master 
				$searchSql
				$sortSql
				$limitSql";
				$results = $this->db_query($sql,0);
				
				
				
				$i=0;
				while ($row = mysql_fetch_assoc($results)) {
				$i++;
				$row['view']	="<a href=\"IMscreenname.php?actionvar=Viewform&id=".$row['im_id']."\" class=\"Second_link\">
							<img src=\"../images/view.png\" border=\"0\" title=\"View Details\"></a>";
				
				if($this->permissionCheck("Edit")){
					$row['edit']	="<a href=\"IMscreenname.php?actionvar=Editform&id=".$row['im_id']."\" class=\"Second_link\">
							<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";			
				}
				if($this->permissionCheck("Delete")){
				$row['delete']	="<a href=\"IMscreenname.php?actionvar=Deletedata&id=".$row['im_id']."\" class=\"Second_link\" onclick=\"return delall()\">
							<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";
				}
				$data['rows'][] = array(
				'id' => $row['im_id'],
				'cell' => array($i, $row['im_name'],$row['edit'],$row['delete'])
				);
				}
				
				$r =json_encode($data);
				ob_clean();
				echo  $r;
				exit;
				
		}
		
		public function IMscreennameSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function IMscreennameCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function IMscreennameReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		
		public function IMscreennameAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		
		public function IMscreennameAddform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new IMscreenname;
				$data				=	$memberObj->getallscreenname($data['id']);
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
				
			}
							
		public function IMscreennameEditform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new IMscreenname;
				$data				=	$memberObj->getallscreenname($data['id']);
				
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
			}
			
		public function IMscreennameUpdatedata()
			{
				$files		=	$this->getData("files");
				$details	=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tblimcode_master ",$details);
				$updateStatus=	$this->db_update("tblimcode_master ",$dataIns,"im_id='".$details['id']."'",1);
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
		public function IMscreennameSavedata()
			{
				
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$utypeId	=	$userSess["usertype"];
				$uid	 	=	$userSess["id"];
				
				$memberObj	=	new IMscreenname;
				$data		=	$this->getData("request");
				//$data['created']	=	date('Y-m-d H:i:s');
				$dataIns	=	$this->populateDbArray("tblimcode_master",$data);
				//print_r($dataIns);exit;
				if(!$this->getPageError())
					{
						if($memberObj->createinterest($dataIns))	
							{
								$this->setPageError("Inserted Successfully");
								$this->clearData("Savedata");
								$this->clearData("Addform");						
								return $this->executeAction(false,"Listing",true);
							}
						else
							{
								$this->setPageError($this->getPageError());
								$this->executeAction(true,"Addform",true);
							}
					}
			}
			
		public function IMscreennameDeletedata()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new IMscreenname;
				$data				=	$memberObj->deleteIMscreenname($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
					
			}	
		
		public function createinterest($dataIns)
			{
					$creationSucces						=	$this->db_insert("tblimcode_master",$dataIns);
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
			
		public function IMscreennameViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new IMscreenname;
				$data				=	$memberObj->getallscreenname($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
			}
		
		public function getallscreenname($membersId="",$args="1")
			{	
				$sql					=	"select * from tblimcode_master where im_id='$membersId' and ".$args;
				$result					=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function deleteIMscreenname($id)
		{
		
			$query 		= 	"UPDATE tblimcode_master SET is_deleted='1' WHERE im_id='$id'";	
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