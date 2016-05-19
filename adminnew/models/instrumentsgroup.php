<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	07-07-2011
Purpose		:	To Manage Instruments Group
*****************************************************************************************/
class instrumentsgroup extends modelclass
	{
	  public function instrumentsgroupListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				
				//print_r ($data);
				/********************************************************************/
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				//print_r($userSess);exit;
				/********************************************************************/
				$searchData				=	$this->getData("post","Search");
				$sortData				=	$this->getData("request","Search");
				$searchData['sortData']	=	$this->getData("request","Search");
				//print_r($searchData);exit;			
				if($sortData['sortField'])
					{
						$orderBy			=	"order by ".$sortData["sortField"]." ".$sortData["sortMethod"];
					}
				
				if(trim($searchData["keyword"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "instrument_group_name", "%".trim($searchData["keyword"])."%")." )";
					}
				if(trim($searchData["memberId"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "instrument_group_id", "%".trim($searchData["memberId"])."%")." )";
					}
				if($sortData['sortField'])
					{
						$orderBy			=	"order by ".$sortData["sortField"]." ".$sortData["sortMethod"];
					}
				
				if(trim($searchData["keyword"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "instrument_group_name", "%".trim($searchData["keyword"])."%")." )";
					}
				if(trim($searchData["memberId"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("=", "instrument_group_id","%".trim($searchData["memberId"])."%").")";	
					}	
				$sql						=	"SELECT * FROM `tbl_instrument_groups` ".$sqlFilter["selc"]." ".$sqlFilter["join"]." 
												".$sqlFilter["cond"].$sqlFilter["ord"]." ".$orderBy ;
				$this->addData(array("sql"=>$sql),"post","",false);
				//$spage				 		=	$this->create_paging("n_page",$sql,GLB_PAGE_CNT);
				$spage				 		=	$this->create_paging("n_page",$sql);
				$data				 		=	$this->getdbcontents_sql($spage->finalSql());
				if(!$data)						$this->setPageError("No records found !");
				$searchData					=	$this->getHtmlData($this->getData("post","Search",false));
				return array("data"=>$data,"data1"=>$data1,"spage"=>$spage,"searchdata"=>$searchData);
			}
		public function instrumentsgroupFetchInstruments()
			{
				$page 			= 	0;	// The current page
				$sortname 		= 	'name';	 // Sort column
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
				if (isset($_POST['rrp'])) 
					{
						$rrp 		= 	mysql_real_escape_string($_POST['rrp']);
					}
				if(empty($rrp))
					{
						$rrp			=	LMT_SITE_ADMIN_PAGE_LIMIT;
					}
				// Setup sort and search SQL using posted data
				//$sortSql				= 	"order by $sortname $sortorder";
				//$searchSql 				= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				// Get total count of records
				$sql					= 	"SELECT count(*) from  tbl_instrument_groups";
				$result 				= 	$this->db_query($sql,0);
				//print_r ($result);
			
				$row 					= 	mysql_fetch_array($result);
				$total					= 	$row[0];
				// Setup paging SQL
				$pageStart 				= 	($page-1)*$rrp;
				if($pageStart<0)
					{
						$pageStart		=	0;
					}
				$limitSql 				= 	"limit $pageStart, $rrp";
				// Return JSON data
				$data 					= 	array();
				$data['page'] 			= 	$page;
				//$data['qtype'] 			= 	$qtype;
				//$data['query'] 			= 	$query;
				$data['total'] 			= 	$total;
				$data['rows'] 			= 	array();
				$sql 					= 	"SELECT `instrument_group_id`,`instrument_group_name`,`dsp_order` from tbl_instrument_groups";
				$results 				= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
				$row['view']	="<a href=\"instrumentsgroup.php?actionvar=Viewform&id=".$row['instrument_group_id']."\" class=\"Second_link\">
							<img src=\"../images/view.png\" border=\"0\" title=\"View Details\"></a>";
				
				$row['edit']	="<a href=\"instrumentsgroup.php?actionvar=Editform&id=".$row['instrument_group_id']."\" class=\"Second_link\">
							<img src=\"../images/edit.png\" border=\"0\" title=\"Edit instrument Details\"></a>";			
					
				$row['delete']	="<a href=\"instrumentsgroup.php?actionvar=Deletedata&id=".$row['instrument_group_id']."\" class=\"Second_link\" onclick = \"return delall()\">
							<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"> </a>";
							
						$data['rows'][] = array
					(
				'id' => $row['instrument_group_id'],
				'cell' => array($i, $row['instrument_group_name'],$row['dsp_order'], $row['view'],$row['edit'],$row['delete'])
					);
				}
				$r =json_encode($data);
				ob_clean();
				echo  $r;
				exit;
			}
		public function instrumentsgroupSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function instrumentsgroupCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function instrumentsgroupReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		public function instrumentsgroupAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		
		public function instrumentsgroupAddform()
			{
				$sql11					=  "SELECT MAX( dsp_order ) FROM tbl_instrument_groups";
				$result_grp 				= 	$this->getdbcontents_sql($sql11,0);
				//echo "<pre>";print_r($result_grp);
				$ks = $result_grp[0]['MAX( dsp_order )'];
				$data[0]['ks'] = $ks+1;
				//echo "<pre>";print_r($ks);
				return array("data"=>$data);
			}
		public function instrumentsgroupEditform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new instrumentsgroup;
				//print_r ($memberObj);
				$data				=	$memberObj->getinstrumentDetails($data['id']);
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
			
		public function instrumentsgroupUpdatedata()
			{
				//$files		=	$this->getData("files");
				$details	=	$this->getData("request");
				$details	=	$this->getData("request");
				$dataIns	=	$this->populateDbArray(" tbl_instrument_groups ",$details);
				$updateStatus=	$this->db_update(" tbl_instrument_groups ",$dataIns,"instrument_group_id='".$details['id']."'",1);
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
		public function instrumentsgroupSavedata()
			{
				//$data		=	$this->getData("files");
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$utypeId	=	$userSess["usertype"];
				$uid	 	=	$userSess["id"];
				//$files		=	$_FILES;
				/************************************************************************************/
				/*if ($files['instrument_image']['name']	=="")
					{
						$this->setPageError("Failed to upload Instrument Image!");
						return $this->executeAction(true,"Addform",true);
					}
				/************************************************************************************/
				/*if($files['instrument_image']['name'])
					{
						$upObj		=	$this->create_upload(10,"jpg,png,jpeg,gif");
						$adimg		=	$upObj->copy("instrument_image","../images/instrument_master",2);
						if($adimg)		
						{
							$upObj->img_resize("180","270","../images/instrument_master/thumb");
							$upObj->img_resize("30","45","../images/instrument_master/thumb/icons");
						}
						else 			$this->setPageError("Invalid Format");
						$this->addData(array("instrument_image"=>$adimg),"request");
					}*/
				
				$memberObj	=	new instrumentsgroup;
				$data		=	$this->getData("request");
				$dataIns	=	$this->populateDbArray(" tbl_instrument_groups",$data);
				//print_r($dataIns);exit;
				if(!$this->getPageError())
					{
						if($memberObj->createInstrument($dataIns))	
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
			
		public function instrumentsgroupDeletedata()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new instrumentsgroup;
				$data				=	$memberObj->deleteInstrument($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
					
			}	
		
		public function createInstrument($dataIns)
			{
					$creationSucces						=	$this->db_insert(" tbl_instrument_groups",$dataIns);
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
			
		public function instrumentsgroupViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new instrumentsgroup;
				$data				=	$memberObj->getinstrumentDetails($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
			}
		
		public function getinstrumentDetails($membersId="")
			{	
				$sql					=	"select * from  tbl_instrument_groups where instrument_group_id='$membersId'";
				$result					=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function deleteInstrument($id)
			{
				/*$query 		= 	"UPDATE  tbl_instrument_groups WHERE instrument_group_id='$id'";	*/
					$query		= "delete from tbl_instrument_groups WHERE instrument_group_id='$id'";
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