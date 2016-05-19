<?php 
/****************************************************************************************
Created by	:	Suneesh 
Created on	:	07-07-2011
Purpose		:	To Manage Admin
*****************************************************************************************/
class createAdmin extends modelclass
	{
		public function createAdminListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return;
			}
		public function createAdminStatuschange()
			{  
				$details	=	$this->getData("request");
				$id 		= 	$details['id'];
				$sql		=	"select * from tbluser_login where login_id=".$id;
				$data		=	end($this->getdbcontents_sql($sql));
				if($data['admin_authorize']==1)
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
		public function createAdminFetch()
			{
				$page = 0;	// The current page
				$sortname = 'name';	 // Sort column
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
				// Setup sort and search SQL using posted data
				$sortSql			 = 	" order by $sortname $sortorder";
				$searchSql 			 = 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				// Get total count of records
				$sql 				= 	"SELECT count(*) FROM  tbluser_login as l LEFT JOIN tblusers as u on l.login_id=u.login_id LEFT JOIN  `tbluser_roles` as g on l.`user_role`=g.`role_id` WHERE 1 AND u.is_deleted=0 AND l.is_deleted=0 AND g.role_id=2 ".$searchSql;
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
				$sql 				= 	"SELECT u.*,l.*,concat(u.first_name,' ',u.last_name) as name,g.`role_name` FROM  tbluser_login as l
												LEFT JOIN tblusers as u on l.login_id=u.login_id 
												LEFT JOIN  `tbluser_roles` as  g on l.`user_role`=g.`role_id` 
												WHERE 1 AND u.is_deleted=0 AND l.is_deleted=0 AND g.role_id=2 ".$searchSql." ".$sortSql." ".$limitSql;
				$results 			= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						if ($row['admin_authorize']==0)
						{
							$row['admin_authorize']		=	"<a href=\"createAdmin.php?actionvar=Statuschange&id=".$row['login_id']."\" class=\"Second_link\">
							<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to activate\"></a>";
						}
						else
						{					
							$row['admin_authorize']		=	"<a href=\"createAdmin.php?actionvar=Statuschange&id=".$row['login_id']."\" class=\"Second_link\">
							<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to Inactivate\"></a>";
						}							
						$row['per']	=	$row['role_name'];
						
						$row['edit']	=	"<a href=\"createAdmin.php?actionvar=Editform&id=".$row['user_id']."\" class=\"Second_link\">
											<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";			
					
						echo $row['delete']	=	"<a href=\"createAdmin.php?actionvar=Deletedata&id=".$row['login_id']."\" class=\"Second_link\" onclick=\"return delall()\">
										<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";
					print_r($row);	//exit;
						$data['rows'][] =	 array
							(
								'id' => $row['user_id'],
								'cell' => array($i, $row['name'], $row['user_name'],$row['admin_authorize'],$row['edit'],$row['per'],$row['delete'])
							);
					}
				$r 					=	json_encode($data);
				ob_clean();
				echo  $r;
				exit;
		
		}
		public function createAdminViewform()
			{
/*				$data				=	$this->getData("get");
				$data				=	$this->getadminDetails($data['id']);
				$countryid			=	$data['country_id'];
			   	$cname				=	$this->getCountryName($countryid);
				$data["country_name"]=	$cname;
				return array("data"=>$data);
*/			}
		public function getadminDetails($membersId="",$args="1")
			{	
			    $sql					=	"select u.*,l.* from  tblusers as l 
											left join tbluser_login as u on u.login_id=l.login_id 
											where l.user_id='$membersId' and ".$args;
				$result					=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		public function getTotalGroup(){
		
			$query		=	"SELECT * FROM `tbluser_roles` WHERE admin_group = 0 AND `is_deleted`=0";
			$result		=	$this->getdbcontents_sql($query);
			return $result;
		}
			
		public function createAdminSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function createAdminCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function createAdminReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		public function createAdminAddformbtn()
			{
				return $this->executeAction(false,"Addform",true);	
			}	
		
		public function createAdminAddform()
			{
				$data		=	$this->getData("request");
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid,"group"=>$this->getTotalGroup());
			}
	
		public function createAdminSavedata()
			{
				
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$utypeId	=	$userSess["usertype"];
				$uid	 	=	$userSess["id"];
				$memberObj	=	new adminUser;
				$data		=	$this->getData("request");

				if(($data['user_name'])==($data['confirm_username']))
					{		
							if(($data['user_pwd'])==($data['confirm_pwd']))
								{	
									if(strlen($data['user_pwd'])>=5 && strlen($data['user_pwd'])<=18 )		
										{	
											$data['user_pwd']		=	md5($data['user_pwd']);
											$data['created_by']		=	$userSess['login_id'];
											$data['privacy_policy']	=	'1';
											$data['authorized']		=	'1';
											$dataIns				=	$this->populateDbArray("tbluser_login",$data);
											$dataIns1				=	$this->populateDbArray("tblusers",$data);
											
											
											$dataIns[created]			=	date('Y-m-d H:i:s');	
											$dataIns1[created]			=	date('Y-m-d H:i:s');
											$dataIns['user_group']		=	0;
											$usid	=	$this->db_insert("tbluser_login",$dataIns,1);
											$dataIns1["user_code"]		=	$this->createRandom(10).$usid;
										
											if($usid)
											{	
												$dataIns1['login_id']	=	$usid;
												
												$userid		=	$this->db_insert("tblusers",$dataIns1,1);
													if($userid)	
													{
														$this->setPageError("Inserted Successfully");
														$this->clearData("Savedata");
														$this->clearData("Addform");					
														//exit;	
														return $this->executeAction(false,"Listing",true);
													}
												else
													{	
													//print_r($dataIns); echo "<br/>"; print_r($dataIns1);exit;
														$this->setPageError($this->getPageError());
														$this->executeAction(true,"Listing",true);
													}
											}
											else
											{
												$this->setPageError($this->getDbErrors());
												$this->executeAction(true,"Addform",true);
												
											}
										}
										else
										{
											$this->setPageError("password must be of length between 5 to 18 characters");
											return $this->executeAction(true,"Addform");
										}
								}
							else
								{
									$this->setPageError("password and confirm password must be same");
									return $this->executeAction(true,"Addform");
								}
						}
						else
								{
									$this->setPageError("Email and confirm email must be same.");
									return $this->executeAction(true,"Addform");
								}
					
			}
		public function createAdminDeletedata()
			{	
				$data				=	$this->getData("get");
				$data				=	$this->deleteAdmin($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
					
			}
			
		public function deleteAdmin($id)
			{	
		  	    $query 		= 	"UPDATE tbluser_login SET is_deleted=1 WHERE login_id = $id";
				$result		=	$this->getdbcontents_sql($query);
				return $this->executeAction(false,"Listing",true);
			}	
		public function createAdminEditform()
			{	
				$data				=	$this->getData("get");
				$memberObj			=	new createAdmin;
				$data				=	$memberObj->getadminDetails($data['id']);
				$array				=	 array("data"=>$data,"group"=>$this->getTotalGroup());
//				print_r($array);exit;
				return $array;
			
			}
				
		public function createAdminUpdatedata()
			{
				$data				=	$this->getData("post");
				
				if(($data['user_name'])==($data['confirm_username']) && !empty($data['user_name']))
					{
						if(strlen($data['user_pwd'])>=5 && strlen($data['user_pwd'])<=18 )	
							{	
							if(($data['user_pwd'])==($data['confirm_pwd']))
								{
									$dataIns				=	$this->populateDbArray("tblusers ",$data);
									$dataIns1				=	$this->populateDbArray("tbluser_login ",$data);
									print_r($dataIns1);
									$dataIns['modified']	=	date('Y-m-d H:i:s');
									$updateStatus			=	$this->db_update("tblusers ",$dataIns,"user_id='".$_GET['id']."'",0);
									$loginid				=	$this->getLoginId($_GET['id']);
									$dataIns1['user_pwd'];
									$dataIns1['user_pwd']	=	md5($dataIns1['user_pwd']);
									$dataIns1['modified']		=	date('Y-m-d H:i:s');
									$updateStatuslogin		=	$this->db_update("tbluser_login ",$dataIns1,"login_id='".$loginid."'",0);
									if($updateStatuslogin)
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
								 {
									$this->setPageError("password and confirm pasword must be same");
									return $this->executeAction(true,"Editform");
								 }	
							}
							{
									$this->setPageError("password must be of length between 5 to 18 characters");
									return $this->executeAction(true,"Editform");
							}
					 }
				else
					{
									$this->setPageError("Email and confirm email must be same.");
									return $this->executeAction(true,"Editform");
					}
							
			}
		public function getLoginId($res)
			{
				$sql				=	"SELECT login_id from tblusers where user_id=".$res;
			    $result				=	end($this->getdbcontents_sql($sql));
				$log				=	$result['login_id'];
				return $log;
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
