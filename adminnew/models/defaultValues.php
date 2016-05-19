<?php 
/****************************************************************************************
Created by	:	Suneesh 
Created on	:	21-08-2011
Purpose		:	To Manage defaults values
*****************************************************************************************/
class defaultValues extends modelclass
	{
		public function defaultValuesListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				$defObj				=	new defaults;
				$group				=	$defObj->getDefaultGroupList();
				unset($_SESSION["group"]);unset($_SESSION['searchKey']);
				if(!empty($_POST['sel_group'])){
					$_SESSION["group"]		=	trim($_POST["sel_group"]);
				}
				if(!empty($_POST['txtKey'])){
					$_SESSION["searchKey"]		=	trim($_POST["txtKey"]);
				}	
					$sel_group	=	trim($_POST['sel_group']);	
					$txtKey	=	trim($_POST["txtKey"]);
				return array("group"=>$group,"sel_group"=>$sel_group,"txtKey"=>$txtKey);
				
			}
		public function defaultValuesFetch()
		{	
				// Connect to MySQL database
				$page = 0;	// The current page
				$sortname = 'D.name';	 // Sort column
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
				if(!empty($_GET['field']) && !empty($_GET['keyword']))
					{
						$searchSql	=	" AND `".$_GET['field']."`='".$_GET['keyword']."%' ";
					}
				// Setup sort and search SQL using posted data
				$sortSql			 = 	"order by $sortname $sortorder";
				$searchSql 			.= 	($qtype != '' && $query != '') ? " AND  `$qtype` LIKE '%$query%'" : '';
				// Get total count of records
				$sql 				= 	"SELECT count(*) FROM tbldefaults AS D LEFT JOIN tbldefaults_group AS G 
										 ON D.group_id = G.id WHERE  1";
										 
				if(!empty($_SESSION["group"])){
					$sql			.=	 " AND G.group ='".$_SESSION["group"]."'";
				}
				if(!empty($_SESSION["searchKey"])){
					$sql			.=	 " AND ( D.name LIKE '%".$_SESSION["searchKey"]."%' OR D.value LIKE '%".$_SESSION["searchKey"]."%')";
				}
				$sql				.=	$searchSql;
		
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
				$sql 				= 	"SELECT D.*,G.group FROM tbldefaults AS D LEFT JOIN tbldefaults_group AS G 
										 ON D.group_id = G.id WHERE  1 ";
				 
				 if(!empty($_SESSION["group"])){
					$sql			.=	 " AND G.group ='".$_SESSION["group"]."'";
				}
				if(!empty($_SESSION["searchKey"])){
					$sql			.=	 " AND (D.name LIKE '%".$_SESSION["searchKey"]."%' OR D.value LIKE '%".$_SESSION["searchKey"]."%')";
				}
				$sql				.=	$searchSql." ".$sortSql." ".$limitSql;
				
				$results 			 = 	$this->db_query($sql,0);
				$i					 =	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						$row['edit']	=	"<a href=\"defaultValues.php?actionvar=Editform&id=".$row['id']."\" class=\"Second_link\">
											<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";			
					
						$row['delete']	=	"<a href=\"defaultValues.php?actionvar=Deletedata&id=".$row['id']."\" class=\"Second_link\" onclick=\"return delall()\">
											<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";
						$data['rows'][] =	 array
							(
								'id' => $row['id'],
								'cell' => array($i,$row['group'],$row['name'], $row['value'],$row['caption'],$row['edit'],$row['delete'])
							);
					}
				ob_clean();
				$r 					=	json_encode($data);
				echo  $r;
				exit;
		}
		
		public function defaultValuesSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function defaultValuesCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function defaultValuesReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		public function defaultValuesAddform()
			{
				$defaults			=	new defaults();
				$data				=	$defaults->getDefaultGroups();
				return array("group"=>$data);
			}
							
		public function defaultValuesEditform()
			{
				$defaults			=	new defaults();
				$data				=	$this->getData("get");
				$data				=	$this->getalldefaults($data['id']);
				
				//This Username should be syncronized with Info admin user 
				if($data['name'] == "LMT_ADMIN_MAILID")
				{
					
					$_SESSION['defaultID']=$data['id'];
					$_SESSION['oldInfoUserName']	=	$data['value'];
				}
				$group				=	$defaults->getDefaultGroups();
				
				return array("data"=>$data,"group"=>$group);
			}
			
		public function defaultValuesUpdatedata()
			{
				$details	=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tbldefaults ",$details);
				$updateStatus =	$this->db_update("tbldefaults ",$dataIns,"id='".$details['id']."'",0);
				if($updateStatus)
					{
						//This Username should be syncronized with Info admin user
						if($details['name']=="LMT_ADMIN_MAILID")
							{
								$flag	= $this->updateAdminUsername($details['value']);
								if($flag)
									$this->setPageError("Default value and Admin username assosiated with this both updated !");
								else	$this->setPageError("Before updating this value, Create an admin user with current username !");
							}
						else	$this->setPageError("Default value Updated Successfully !");
						
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
		public function updateAdminUsername($userName)
			{
				$oldUserName	=	$_SESSION["oldInfoUserName"];
				//This Username should be syncronized with Info admin user. Updating admin username as that of constant LMT_ADMIN_MAILID.
				$flag	=	$this->db_update('tbluser_login', array("user_name"=>$userName),"user_name = '$oldUserName'",0);
				if($flag) 
					{	
						unset($_SESSION['oldInfoUserName']);unset($_SESSION['defaultID']);						
						return true;					
					}
				else
					{
						$this->rollBackUpdate();
						unset($_SESSION['oldInfoUserName']);unset($_SESSION['defaultID']);
						//$this->setPageError($this->getDbErrors());						
						return false;
					}
			}
		public function rollBackUpdate()
			{
				$this->db_update("tbldefaults ",array("value"=>$_SESSION['oldInfoUserName']),"id='".$_SESSION['defaultID']."'",1);
				
			}
			
		public function defaultValuesSavedata()
			{
				$memberObj	=	new defaultValues;
				$data		=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tbldefaults",$data);
				if(!$this->getPageError())
					{
						if($memberObj->createdefaults($dataIns))	
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
			
		public function defaultValuesDeletedata()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new defaultValues;
				$data				=	$memberObj->deletedefaults($data['id']);
				return array("data"=>$this->getHtmlData($data));
					
			}	
		
		public function createdefaults($dataIns)
			{
					$creationSucces						=	$this->db_insert("tbldefaults",$dataIns);
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
			
		public function defaultValuesViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new defaultValues;
				$data				=	$memberObj->getalldefaults($data['id']);
				return array("data"=>$this->getHtmlData($data));
			}
		
		public function getalldefaults($membersId="",$args="1")
			{	
				$sql					=	"select * from tbldefaults where id='$membersId' and ".$args;
				$result					=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function deletedefaults($id)
		{
			$query 		= 	"delete from tbldefaults WHERE id='$id'";	
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