<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	07-07-2011
Purpose		:	To Manage Instruments
*****************************************************************************************/
class instruments extends modelclass
	{
		public function instrumentsListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
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
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "name", "%".trim($searchData["keyword"])."%")." )";
					}
				if(trim($searchData["memberId"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "instrument_id", "%".trim($searchData["memberId"])."%")." )";
					}
				if($sortData['sortField'])
					{
						$orderBy			=	"order by ".$sortData["sortField"]." ".$sortData["sortMethod"];
					}
				
				if(trim($searchData["keyword"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "name", "%".trim($searchData["keyword"])."%")." )";
					}
				if(trim($searchData["memberId"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("=", "instrument_id","%".trim($searchData["memberId"])."%").")";	
					}	
				$sql						=	"SELECT * FROM `tblinstrument_master` ".$sqlFilter["selc"]." ".$sqlFilter["join"]." 
												where is_deleted=0".$sqlFilter["cond"].$sqlFilter["ord"]." ".$orderBy ;
				$this->addData(array("sql"=>$sql),"post","",false);
				//$spage				 		=	$this->create_paging("n_page",$sql,GLB_PAGE_CNT);
				$spage				 		=	$this->create_paging("n_page",$sql);
				$data				 		=	$this->getdbcontents_sql($spage->finalSql());
				if(!$data)						$this->setPageError("No records found !");
				$searchData					=	$this->getHtmlData($this->getData("post","Search",false));
		//		print_r($data);	
				return array("data"=>$data,"spage"=>$spage,"searchdata"=>$searchData);
			}
		public function instrumentsFetchInstruments()
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
				if (isset($_POST['rp'])) 
					{
						$rp 		= 	mysql_real_escape_string($_POST['rp']);
					}
				if(empty($rp))
					{
						$rp			=	LMT_SITE_ADMIN_PAGE_LIMIT;
					}
				// Setup sort and search SQL using posted data
				$sortSql				= 	"order by $sortname $sortorder";
				$searchSql 				= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				// Get total count of records
				$sql					= 	"SELECT count(*) from tblinstrument_master WHERE 1 $searchSql";
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
				/*$sql 					= 	"SELECT `instrument_id`,`name`,`description` from tblinstrument_master 
												WHERE 1 $searchSql	$sortSql $limitSql";*/
				$sql 					="SELECT tig.instrument_group_name, ti.`instrument_id`,ti.`name`,ti.`description` from tblinstrument_master as ti JOIN  tbl_instrument_groups as tig WHERE tig.instrument_group_id=ti.instrument_group_id $searchSql $sortSql $limitSql";
				$results 				= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
				$row['view']	="<a href=\"instruments.php?actionvar=Viewform&id=".$row['instrument_id']."\" class=\"Second_link\">
							<img src=\"../images/view.png\" border=\"0\" title=\"View Details\"></a>";
				
				$row['edit']	="<a href=\"instruments.php?actionvar=Editform&id=".$row['instrument_id']."\" class=\"Second_link\">
							<img src=\"../images/edit.png\" border=\"0\" title=\"Edit instrument Details\"></a>";			
					
				$row['delete']	="<a href=\"instruments.php?actionvar=Deletedata&id=".$row['instrument_id']."\" class=\"Second_link\" onclick = \"return delall()\">
							<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"> </a>";
							
						$data['rows'][] = array
					(
				'id' => $row['instrument_id'],
				'cell' => array($i, $row['name'], $row['instrument_group_name'],$this->getLimitedText($row['description'],75),$row['view'],$row['edit'],$row['delete'])
					);
				}
				$r =json_encode($data);
				ob_clean();
				echo  $r;
				exit;
			}
		public function instrumentsSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function instrumentsCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function instrumentsReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		public function instrumentsAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		
		public function instrumentsAddform()
			{
				$sql11					= 	"SELECT  instrument_group_id,instrument_group_name,dsp_order from  tbl_instrument_groups";
				$result_grp 				= 	$this->getdbcontents_sql($sql11,0);
				//$ks = $result_grp['instrument_group_name'];
				$data['kk'] = $result_grp;
				//echo "<pre>";print_r($data);
				return array("data"=>$data);
				
			}
		public function instrumentsEditform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new instruments;
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
				$sql11					= 	"SELECT instrument_group_id,instrument_group_name from  tbl_instrument_groups";
				$result_grp 				= 	$this->getdbcontents_sql($sql11,0);
				//$ks = $result_grp['instrument_group_name'];
				$data['kk'] = $result_grp;
				//echo" <pre>";print_r ($data);
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
			}
			
		public function instrumentsUpdatedata()
			{
				$files		=	$this->getData("files");
				$details	=	$this->getData("request");
				$dsp = $details['instrument_group_id'];
				$sql					= 	"SELECT  dsp_order from  tbl_instrument_groups where instrument_group_id =$dsp";
				$result_grp 				= 	$this->getdbcontents_sql($sql,0);
				//echo "<pre>"; print_r ($result_grp);
				$dsp_order = $result_grp[0]['dsp_order'];
				if($files['instrument_image']['name'])
					{
						$upObj		=	$this->create_upload(10,"jpg,png,jpeg,gif");
						$adimg		=	$upObj->copy("instrument_image","../images/instrument_master",2);
						if($adimg)		
						{
							$upObj->img_resize("180","270","../images/instrument_master/thumb");
							$upObj->img_resize("30","45","../images/instrument_master/thumb/icons");
						}
						else 			$this->setPageError($upObj->get_status());
						$this->addData(array("instrument_image"=>$adimg),"request");
					}
				$details	=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tblinstrument_master ",$details);
				$dataIns['dsp_order'] = $dsp_order;
				$updateStatus=	$this->db_update("tblinstrument_master ",$dataIns,"instrument_id='".$details['id']."'","instrument_group_id='".$details['instrument_group_id']."'",1);
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
		public function instrumentsSavedata()
			{
				$data		=	$this->getData("files");
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$utypeId	=	$userSess["usertype"];
				$uid	 	=	$userSess["id"];
				$files		=	$_FILES;
				/************************************************************************************/
				if ($files['instrument_image']['name']	=="")
					{
						$this->setPageError("Failed to upload Instrument Image!");
						return $this->executeAction(true,"Addform",true);
					}
				/************************************************************************************/
				if($files['instrument_image']['name'])
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
					}
				
				$memberObj	=	new instruments;
				$data		=	$this->getData("request");
				$dsp = $data['instrument_group_id'];
				$sql					= 	"SELECT  dsp_order from  tbl_instrument_groups where instrument_group_id =$dsp";
				$result_grp 				= 	$this->getdbcontents_sql($sql,0);
				//echo "<pre>"; print_r ($result_grp);
				$dsp_order = $result_grp[0]['dsp_order'];
				$dataIns	=	$this->populateDbArray("tblinstrument_master",$data);
				$dataIns['dsp_order'] = $dsp_order;
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
			
		public function instrumentsDeletedata()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new instruments;
				$data				=	$memberObj->deleteInstrument($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
					
			}	
		
		public function createInstrument($dataIns)
			{
					$creationSucces						=	$this->db_insert("tblinstrument_master",$dataIns);
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
			
		public function instrumentsViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new instruments;
				$data				=	$memberObj->getinstrumentDetails($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
			}
		
		public function getinstrumentDetails($membersId="",$args="1")
			{	
				$sql					=	"select ti.*, tig.instrument_group_name, tig.dsp_order from tblinstrument_master ti join tbl_instrument_groups tig on tig.instrument_group_id=ti.instrument_group_id  where instrument_id='$membersId' and ".$args;
				$result					=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function deleteInstrument($id)
			{
				$query 		= 	"UPDATE tblinstrument_master  SET is_deleted='1' WHERE instrument_id='$id'";	
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