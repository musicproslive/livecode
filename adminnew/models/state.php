<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	04-08-2011
Purpose		:	state Lists
******************************************************************************************/
class state extends modelclass
	{
		public function stateListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName(),'country.php'));
				
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$data		=	$this->getData("get");
				$_SESSION['stateId']	=	$_GET['sid'];
				if ($_GET['sid']=='')
					{
						header("Location: country.php");exit;
					}
				$searchData				=	$this->getData("post","Search");
				$sortData				=	$this->getData("request","Search");
				$searchData['sortData']	=	$this->getData("request","Search");
				if($sortData['sortField'])
					{
						$orderBy			=	"order by ".$sortData["sortField"]." ".$sortData["sortMethod"];
					}
				
				if(trim($searchData["keyword"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "first_name", "%".trim($searchData["keyword"])."%")." )";
					}
				if(trim($searchData["memberId"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("=", "user_id",trim($searchData["memberId"]))." or ".$this->dbSearchCond("=", "nm.email",trim($searchData["memberId"])).")";	
					}
				
				$sql						=	"SELECT cc.*,cs.state_name from tblcountries as cc
												left join  tblstates as cs on cc.country_id = cs.country_id 
												WHERE  cc.country_id = ".$_SESSION['stateId']." AND cs.country_id IS NOT NULL".$sqlFilter["cond"].$sqlFilter["ord"]." ".$orderBy ;
											
				$this->addData(array("sql"=>$sql),"post","",false);
				$spage				 		=	$this->create_paging("n_page",$sql);
				$data				 		=	$this->getdbcontents_sql($spage->finalSql());
				//$this->print_r($spage);exit;
				if(!$data)						$this->setPageError("No records found !");
				$searchData					=	$this->getHtmlData($this->getData("post","Search",false));
			}
		public function stateFetchStates()
			{
				$sid			=	$_SESSION['stateId'];		//Getting selected country id 
				$page 			= 	0;	// The current page
				$sortname 		= 	'cs.state_name';	 // Sort column
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
						$rp			=LMT_SITE_ADMIN_PAGE_LIMIT;
					}
					
				// Setup sort and search SQL using posted data
				$sortSql				 = 	" order by $sortname $sortorder ";
				$searchSql 				 = 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				// Get total count of records
				$sql					= 	"SELECT count(*) from tblcountries as cc
											LEFT JOIN  tblstates as cs on cc.country_id=cs.country_id Where cc.country_id = $sid   
											$searchSql";
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
				$sql 					= 	"SELECT cc.*,cs.state_name,cs.state_code,cs.state_id from tblcountries as cc
											LEFT JOIN  tblstates as cs on cc.country_id=cs.country_id Where cc.country_id = $sid   
											$searchSql $sortSql $limitSql";				
				$results 				= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						$row['edit']	="<a href=\"state.php?actionvar=Editform&id=".$row['state_id']."\" class=\"Second_link\">
											<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";
					
						$row['delete']	="<a href=\"state.php?actionvar=Deletedata&id=".$row['state_id']."\" class=\"Second_link\">
											<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\" onclick = \"return delall()\"></a>";
						
						$data['rows'][] = array
						
					(
				'id' => $row['state_id'],
				'cell' => array($i, $row['state_name'],$row['state_code'],$row['edit'],$row['delete'])
					);
				}
				$r =json_encode($data);
				ob_clean();
				echo  $r;
				exit;
			}
		public function stateEditform()
			{	
				$data				=	$this->getData("get");
				$data				=	$this->getStateName($data['id']);
				return array("data"=>$this->getHtmlData($data));
			}
		public function stateAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		
		public function stateAddform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new state;
				$data				=	$memberObj->getstateDetails($data['id']);
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
		public function stateSavedata()
			{
				$data				=	$this->getData("request");
				$country			=	$this->getData("get");
				$data['country_id']	=	$country['sid'];				
				$dataIns			=	$this->populateDbArray("tblstates",$data);	
				if(!$this->getPageError())
					{
						if($this->createState($dataIns))	
							{
								$this->setPageError("Inserted Successfully");
								$this->clearData("Savedata");
								$this->clearData("Addform");									
								header("Location: state.php?sid={$country['sid']}");			
								//return $this->executeAction(false,"Listing",true);								
							}
						else
							{
								$this->setPageError($this->getPageError());
								$this->executeAction(true,"Addform",true);
							}
					}
			}
		
		public function getStateName($id)
			{
					$sql						=	"SELECT * FROM tblstates WHERE state_id=$id";
					$result						=	end($this->getdbcontents_sql($sql));
					return $result;
			}
		public function createState($dataIns)
			{
					$creationSucces						=	$this->db_insert("tblstates",$dataIns);
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
		public function stateUpdatedata()
			{
				$data		=	$this->getData("post");
				$data['state_id']	=	$data['sid'];
				$data['state_name'] = $data['state_name'];
				$dataIns			=	$this->populateDbArray("tblstates",$data);
				$result				=	$this->db_update("tblstates",$dataIns,"state_id=".$data['sid'],0);	
				if($result)
					{
								$this->setPageError("Updated Successfully");
								header("Location: state.php?sid=".$data['country_id']);exit;
					}
				else
					{
						$this->setPageError($this->getPageError());
						$this->executeAction(true,"Editform",true);
					}
					
			}
		public function stateDeletedata()
			{
				$data				=	$this->getData("get");
				$result				=	$this->deletestate($data['id']);
				
				if($result)	
					{
						$this->setPageError("Deleted Successfully");
						return $this->executeAction(false,"Listing",true);
					}
				else
					{
						$this->setPageError($this->getPageError());
						$this->executeAction(true,"Listing",true);
					}
			}	
			
		public function deletestate($id)
			{
					$sql				=	"DELETE FROM tblstates WHERE state_id = $id";
					$result				=	$this->db_query($sql,1);
					if($result)
						{
							return $result;	
						}	
					else
						{
								$this->setPageError($this->getdbErrors());
								return false;
						}		
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
		
		public function getstateDetails($membersId="",$args="1")
			{  
				$sql	=	"select cu.* ,cc.country_name,cs.state_name from tblusers as cu 
							left join tblcountries as cc on cu.country_id=cc.country_id 
							left join tblstates as cs on cu.state_id=cs.state_id  
							where user_id='$membersId' and ".$args;
				$result	=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
/*		public function deletetutor($id)
			{
				$query 		= 	"UPDATE tblusers SET is_deleted='1' WHERE user_id='$id'";
				$result		=	$this->getdbcontents_sql($query);
				return $this->executeAction(false,"Listing",true);
			}
		
		public function getAll($stat="")
			{
				$data				=	$this->getdbcontents_cond("tbluser_category");
				return $data; 
			}
*/	}