<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	22-07-2011
Purpose		:	To Manage activities
*****************************************************************************************/
class activities extends modelclass
	{
		public function activitiesListing()
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
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "activity", "%".trim($searchData["keyword"])."%")." )";
					}
				if(trim($searchData["memberId"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "activity_id", "%".trim($searchData["memberId"])."%")." )";
					}
				if($sortData['sortField'])
					{
						$orderBy			=	"order by ".$sortData["sortField"]." ".$sortData["sortMethod"];
					}
				
				if(trim($searchData["keyword"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "activity", "%".trim($searchData["keyword"])."%")." )";
					}
				if(trim($searchData["memberId"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("=", "activity_id","%".trim($searchData["memberId"])."%").")";	
					}	
				$sql						=	"SELECT * FROM `tblactivity_master` ".$sqlFilter["selc"]." ".$sqlFilter["join"]." 
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
		public function activitiesFetch(){
			
		// Connect to MySQL database
				$page = 0;	// The current page
				$sortname = 'activity';	 // Sort column
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
				if(empty($rp))
				{
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
				$sql = "SELECT count(*) FROM `tblactivity_master` ".$searchSql;
				
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
				$sql = "SELECT *,DATE_FORMAT(created,'".$_SESSION["DATE_FORMAT"]["M_DATE"]." ". $_SESSION["DATE_FORMAT"]["M_TIME"]."') AS date
				from tblactivity_master" . 
				$searchSql." ".$sortSql." ".$limitSql;
				 //.$limitSql;
				 //$path		=	dirname(__FILE__);
				// $test		=	file_get_contents("file.txt");
				//file_put_contents("file.txt",$test.$sql);
				$results = $this->db_query($sql,0);
				$i=0;
				while ($row = mysql_fetch_assoc($results)) {
				$i++;
				
				$row['edit']	="<a href=\"activities.php?actionvar=Editform&id=".$row['activity_id']."\" class=\"Second_link\">
							<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";			
					
				$row['delete']	="<a href=\"activities.php?actionvar=Deletedata&id=".$row['activity_id']."\" class=\"Second_link\" onclick=\"return delall()\">
							<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";
				$data['rows'][] = array(
				'id' => $row['activity_id'],
				'cell' => array($i, $row['activity'], $row['date'],$row['edit'],$row['delete'])
				);
				}
				ob_clean();
				$r =json_encode($data);
				echo  $r;
				exit;
	
			
		}
		
		public function activitiesSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function activitiesCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function activitiesReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		
		public function activitiesAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		
		public function activitiesAddform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new activities;
				$data				=	$memberObj->getactivities($data['id']);
				$terrObj			= 	new territory(); 
				$country_combo		=	$this->get_combo_arr("sel_country",$terrObj->getAllCountries("status='1' order by preference"),"id","country",$data["sel_country"],"'valtype='emptyCheck-please select a country' onchange=\"getcombo(this.value,'stateDivId');\"");
				$stateArry			=	$terrObj->getAllStates("sel_country=".$data['sel_country']." and status='1' order by preference");
				$state_combo		=	$this->get_combo_arr("sel_state",$terrObj->getAllStates(" country_id=".$data['sel_country']." and status='1' order by preference"),"id","state",$data["sel_state"],"'valtype='emptyCheck-please select a country' onchange=\"getcities(this.value,'cityDivId');\"");
				$city_combo			=	$this->get_combo_arr("sel_city",$terrObj->getAllcities(" state_id=".$data['sel_state']." and status='1' order by preference"),"id","city",$data["sel_city"],"'valtype='emptyCheck-please select a city'");
				$combo				=	array();
				$combo['country']	=	$country_combo;
				$combo['state']		=	$state_combo;
				$combo['city']		=	$city_combo;
				
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
				
			}
							
		public function activitiesEditform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new activities;
				$data				=	$memberObj->getactivities($data['id']);
				
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
			}
			
		public function activitiesUpdatedata()
			{
				$files		=	$this->getData("files");
				$details	=	$this->getData("request");
				if($files['instrument_image']['name'])
					{
						$upObj		=	$this->create_upload(10,"jpg,png,jpeg,gif");
						$adimg		=	$upObj->copy("instrument_image","../images/instument_master",2);
						if($adimg)		$upObj->img_resize("180","270","../images/instument_master/thumb");
						else 			$this->setPageError($upObj->get_status());
						$this->addData(array("instrument_image"=>$adimg),"request");
					}
				$details			   =	$this->getData("request");
				$details['created_by'] =	$_SESSION['sess_admin'];
				$details['modified']   =	date('Y-m-d H:i:s');
				$dataIns			   =	$this->populateDbArray("tblactivity_master ",$details);
				$updateStatus          =	$this->db_update("tblactivity_master ",$dataIns,"activity_id='".$details['id']."'",1);
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
		public function activitiesSavedata()
			{
				$data				=	$this->getData("post");
				$data['created']	=	date('Y-m-d H:i:s');
				$data['modified']	=	date('Y-m-d H:i:s');	
				$data['created_by']  =	$_SESSION['sess_admin'];
				$data['modified_by'] =	$_SESSION['sess_admin'];
				$dataIns			 =	$this->populateDbArray("tblactivity_master",$data);
				if(!$this->getPageError())
					{	
						if($this->createactivities($dataIns))	
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
			
		public function activitiesDeletedata()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new activities;
				$data				=	$memberObj->deleteactivities($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
					
			}	
		
		public function createactivities($dataIns)
			{
					$creationSucces						=	$this->db_insert("tblactivity_master",$dataIns,0);
					if($creationSucces)
						{
							return $creationSucces;	
						}	
					else
						{
								$this->setPageError($this->getdbErrors());
								$this->executeAction(true,"Addform",true);
								return false;
						}		
			}	
			
		public function activitiesViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new activities;
				$data				=	$memberObj->getactivities($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
			}
		
		public function getactivities($membersId="",$args="1")
			{	
				$sql					=	"select * from tblactivity_master where activity_id='$membersId' and ".$args;
				$result					=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function deleteactivities($id)
		{
			$query 		= 	"UPDATE tblactivity_master SET is_deleted='1' WHERE activity_id='$id'";	
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