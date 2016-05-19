<?php
/**************************************************************************************
Created by :hari krishna S
Created on :8th march 2011
Purpose    :Create Menu 
**************************************************************************************/
class createMenu extends modelclass
	{
		public function createMenuListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				$sql 			=	"select * from tblmenu where preference= 1";
				$spage			=	$this->create_paging("n_page",$sql,5);
				$data			=	$this->getdbcontents_sql($spage->finalSql());
				if(!$data)			$this->setPageError("No records found !");
				return array("data"=>$data,"spage"=>$spage,"searchdata"=>$searchData);
			}
		public function createMenuFetchMenus()
			{
				// Connect to MySQL database
				$page = 0;	// The current page
				$sortname = 'menuName';	 // Sort column
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
						$rp			=	LMT_SITE_ADMIN_PAGE_LIMIT ;
					}
			
				// Setup sort and search SQL using posted data
				$sortSql			 = 	" order by $sortname $sortorder";
				$searchSql 			.= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				// Get total count of records
				$sql 				= 	"SELECT count(*) FROM `tblmenu` WHERE 1 ".$searchSql;
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
				$sql 				= 	"SELECT * from tblmenu WHERE 1 " .$searchSql."".$sortSql." ".$limitSql;
				$results 			= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						if ($row['status']==0)
						{
							$row['status']		=	"<a href=\"createMenu.php?actionvar=Stauschange&id=".$row['id']."\" class=\"Second_link\">
							<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to activate\"></a>";
						}
						else
						{					
							$row['status']		=	"<a href=\"createMenu.php?actionvar=Stauschange&id=".$row['id']."\" class=\"Second_link\">
							<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to Inactivate\"></a>";
						}						
						$row['edit']	=	"<a href=\"createMenu.php?actionvar=Editform&id=".$row['id']."\" class=\"Second_link\">
											<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";			
					
							$data['rows'][] =	 array
							(
								'id' => $row['id'],
								'cell' => array($i, $row['menuName'], $row['dateAdded'],$row['status'],$row['edit'])
							);
					}
				$r 					=	json_encode($data);
				ob_clean();
				echo  $r;
				exit;
			}
		public function createMenuCreatenew()
			{
				$data			=	$this->getData("post");
				return array("data"=>$data);
			}
		public function createMenuEditform()
			{
				$data			=	$this->getData("get");
				$sql			=	"select * from tblmenu where id=".$data['id'];
				$data			=	end($this->getdbcontents_sql($sql));
				return array("data"=>$data);
			}
		public function createMenuStauschange()
			{
				$data			=	$this->getData("get");
				$id				=	$data['id'];
				$sql			=	"select status from  tblmenu where id=".$data['id'];
				$data			=	end($this->getdbcontents_sql($sql));
				
				if($data['status']==1)
					{
						$sql	=	"UPDATE tblmenu SET status=0 WHERE id=".$id;
						$data	=	mysql_query($sql);
						if($data)	$this->setPageError("Status changed successfully");
						else $this->setPageError("Sorry! could not change status");
						$this->executeAction(false,"Listing",true);
					}
				else
					{
						$sql	=	"UPDATE tblmenu SET status=1 WHERE id=".$id;
						$data	=	mysql_query($sql);
						if($data)	$this->setPageError("Status changed successfully");
						else $this->setPageError("Sorry! could not change status");
						$this->executeAction(false,"Listing",true);
					}
				
			}
		public function createMenuUpdate()
			{
				$data				=	$this->getData("post");
				$details			=	$this->getData("request");
				$dataIns			=	$this->populateDbArray("tblmenu",$details);
				$update				=	$this->db_update("tblmenu",$dataIns,"id='".$details['id']."'");
				if($update)
					{
						$this->setPageError("Updated successfully");
						$this->executeAction(false,"Listing",true);	
					}	
				else 
					{
						$this->setPageError("Sorry! could not Update");
						$this->executeAction(false,"Editform",true);	
					}
			}
		public function createMenuAddmenu()
			{
				//echo "===================================";exit;
				$data				=	$this->getData("post");
				$data['dateAdded']	=	date("Y-m-d H:i:s");
				$sql				=	"SELECT max(`preference`) as max FROM `tblmenu` WHERE 1";
				$preference			=	end($this->getdbcontents_sql($sql));
				$data['preference']	=	$preference['max']+1;
				$dataIns			=	$this->populateDbArray("tblmenu",$data);
				$insertStatus		=	$this->db_insert("tblmenu",$dataIns);
				if($insertStatus) 
					{
						$this->setPageError("Inserted successfully");
						$this->executeAction(false,"Listing",true);
					}
				else
					{
						$this->setPageError($this->getPageError());
						$this->executeAction(true,"Createnew",true);
					}	
			}
		public function createMenuDeletedata()
			{
				$data				=	$this->getData("get");
				print_r($data);exit;
				$dataIns			=	$this->populateDbArray("tblmenu",$data);
				$update				=	$this->db_update("tblmenu",$dataIns,"id='".$details['id']."'");
				if($update)
					{
						$this->setPageError("Updated successfully");
						$this->executeAction(false,"Listing",true);	
					}	
				else 
					{
						$this->setPageError("Sorry! could not Update");
						$this->executeAction(false,"Editform",true);	
					}
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
