<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	07-07-2011
Purpose		:	To Manage Tutors
******************************************************************************************/
class yearlyreport extends modelclass
	{
		public function yearlyreportListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return;
				
			}
		public function yearlyreportFetch(){
			
					// Connect to MySQL database
					$page 			= 	0;	// The current page
					$sortname 		= 	'e.`created_on`';	 // Sort column
					$sortorder	 	= 	'desc';	 // Sort order
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
					$sortSql				= 	" order by $sortname $sortorder";
					$searchSql 				= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
					
					// Setup paging SQL
					$pageStart 				= 	($page-1)*$rp;
					if($pageStart<0)
						{
							$pageStart		=	0;
						}
					$limitSql 				= 	" limit $pageStart, $rp";

					$sql		=	"SELECT count(e.`enrolled_id`) as total,SUM(p.`trans_amount`) as amount,
											 YEAR(e.`created_on`) as year
										FROM `tblcourses` c 
										LEFT JOIN `tblcourse_enrollments` e ON e.`course_id`=c.`course_id`
										LEFT JOIN `tblcourse_enrollment_transaction` p ON p.`enrolled_id`=e.`enrolled_id`							
					  					WHERE  c.`course_status_id` !=".LMT_COURSE_STATUS_CANCELLED."
										AND e.`enrolled_status_id` !=".LMT_CS_ENR_CANCELLED;
					
					$sql		.=	" GROUP BY YEAR(e.`created_on`) $searchSql ";
					
					$total		 =	count($this->getdbcontents_sql($sql, 0));
					
					
					$data 					= 	array();
					$data['page'] 			= 	$page;
					$data['qtype'] 			= 	$qtype;
					$data['query'] 			= 	$query;
					$data['total'] 			= 	$total;
					$data['rows'] 			= 	array();
					
					$sql		.=	" ".$sortSql." ".$limitSql;
					$results 	= 	$this->db_query($sql,0);
					
					$i			=	$pageStart;
					while ($row = mysql_fetch_assoc($results)) 
						{
							$details		=	"<a href='monthelyreport.php?year={$row['year']}'><img src='images/inner_details.png' alt='View details' ></a>";
							$i++;
							$data['rows'][] = array(
							'id' => $row['course_id'],
							'cell' => array($i, $row['year'],$row['total'],$row['symbol']."$ ".$row['amount'],$details)
						);
					}
					$r =json_encode($data);
					ob_clean();
					echo  $r;
					exit;
					
		
		}
				
		public function tutorSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function tutorCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function tutorReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		public function tutorAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}
		public function getAllLevels(){
			$query		=	"SELECT * FROM `tbllookup_tutor_level` ";//WHERE `status`"
			$result		=	$this->getdbcontents_sql($query);
			return $result;
		}	
		
		public function tutorAddform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new tutor;
				$data				=	$memberObj->gettutorDetails($data['id']);
				$terrObj			= 	new territory(); 
				$country_combo		=	$this->get_combo_arr("sel_country",$terrObj->getAllCountries("status='1' order by preference"),"id","country",$data["sel_country"],"'valtype='emptyCheck-please select a country' onchange=\"getcombo(this.value,'stateDivId');\"");
				$stateArry			=	$terrObj->getAllStates("sel_country=".$data['sel_country']." and status='1' order by preference");
				$state_combo		=	$this->get_combo_arr("sel_state",$terrObj->getAllStates(" country_id=".$data['sel_country']." and status='1' order by preference"),"id","state",$data["sel_state"],"'valtype='emptyCheck-please select a country' onchange=\"getcities(this.value,'cityDivId');\"");
				$city_combo			=	$this->get_combo_arr("sel_city",$terrObj->getAllcities(" state_id=".$data['sel_state']." and status='1' order by preference"),"id","city",$data["sel_city"],"'valtype='emptyCheck-please select a city'");
				$combo				=	array();
				
				$el					=	$this->getAllLevels();
				
				$combo['country']	=	$country_combo;
				$combo['state']		=	$state_combo;
				$combo['city']		=	$city_combo;
				
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid,"expert_level"=>$el);
			}
		
		public function tutorEditform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new tutor;
				$data				=	$memberObj->gettutorDetails($data['id']);
				$terrObj			= 	new territory(); 
				$country_combo		=	$this->get_combo_arr("country",$terrObj->getAllCountries(),"country_id","country_name",$data["country_name"],"'valtype='emptyCheck-please select a country' onchange=\"getcombo(this.value,'stateDivId');\"");
				$stateArry			=	$terrObj->getAllStates("country_id=".$data['country_id']."");
				$state_combo		=	$this->get_combo_arr("state",$terrObj->getAllStates(" country_id=".$data['country_name']." and status='1' order by preference"),"state_id","state_name",$data["state_name"],"'valtype='emptyCheck-please select a country' onchange=\"getcities(this.value,'cityDivId');\"");
				$city_combo			=	$this->get_combo_arr("city",$terrObj->getAllcities(" state_id=".$data['state_id'].""),"city_id","city_name",$data["city_name"],"'valtype='emptyCheck-please select a city'");
				//print_r($stateArry);exit;
				$combo				=	array();
				$combo['country']	=	$country_combo;
				$combo['state']		=	$state_combo;
				$combo['city']		=	$city_combo;
				$el					=	$this->getAllLevels();
				
				$el					=	$this->getAllLevels();
				
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid,"expert_level"=>$el);
			}
			
		public function tutorUpdatedata()
			{
				$files		=	$this->getData("files");
				$details	=	$this->getData("request");
				$details	=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tblusers ",$details);
				$updateStatus=	$this->db_update("tblusers",$dataIns,"user_id='".$details['id']."'",1);
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
		public function tutorSavedata()
			{
				$data		=	$this->getData("files");
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$utypeId	=	$userSess["usertype"];
				$uid	 	=	$userSess["id"];
				$files=$_FILES;
				$memberObj	=	new tutor;
				$data		=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tblusers",$data);	
				
				if(!$this->getPageError())
					{
						if($memberObj->createMember($dataIns))	
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
		
		public function getAllCountries()
			{
				$query 		= 	"select * from tblcountries";
				$result		=	$this->getdbcontents_sql($query);
				return $this->executeAction(false,"Listing",true);
			}
		
		public function tutorStauschange()
			{
				$details	=	$this->getData("request");
				$permission	=	new tutor;
				$sql		=	"select cu .*,cp.admin_authorize from tblusers as cu
								left join tbluser_login as cp 
								on cu.login_id =cp.login_id 
								where user_id=".$details['id']."";
				$data		=	$this->getdbcontents_sql($sql);
				$id			=	$data[0]['login_id'];
				if($data[0]['admin_authorize']==1)
					{			
						$dataUpdate	=	array();
						$dataUpdate['admin_authorize']	=	"0";
						$this->db_update("tbluser_login",$dataUpdate,"login_id =$id",1);
						return $this->executeAction(false,"Listing",true);
					}
				else 
					{
						$dataUpdate	=	array();
						$dataUpdate['admin_authorize']	=	"1";
						$this->db_update("tbluser_login",$dataUpdate,"login_id =$id",1);
						return $this->executeAction(false,"Listing",true);
					}
			}
		
		public function updatestatus($dataIns)
			{
				$creationSucces		=	$this->db_update("tbluser_login",$dataIns,"login_id='".$data['login_id']."'",1);
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
					
		public function tutorDeletedata()
			{
				$data				=	$this->getData("get");
				$data				=	$this->deletetutor($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
			}	
			
		public function tutorViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new tutor;
				$data				=	$memberObj->gettutorDetails($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
			}
		
		public function createMember($dataIns)
			{
					$creationSucces						=	$this->db_insert("tblusers",$dataIns);
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
		
		public function gettutorDetails($membersId="",$args="1")
			{  
				$sql	=	"select cu.* ,cc.country_name,cs.state_name from tblusers as cu 
							left join tblcountries as cc on cu.country_id=cc.country_id 
							left join tblstates as cs on cu.state_id=cs.state_id  
							where user_id='$membersId' and ".$args;
				$result	=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function deletetutor($id)
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
	}
