<?php
/**************************************************************************************
Created by :hari krishna
Created on :8th march 2011
Purpose    :Create Sub Menu 
************************************** ************************************************/
class createSubMenu extends modelclass
	{
		public function createSubMenuListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				$sql 			=	"select  tblsub_menu.*, tblmenu.menuName from tblsub_menu 
									left join tblmenu on menuId=tblmenu.id where tblsub_menu.status=1";
				$spage			=	$this->create_paging("n_page",$sql,10);
				$data			=	$this->getdbcontents_sql($spage->finalSql());
				//print_r($data);
				if(!$data)			$this->setPageError("No records found !");
				return array("data"=>$data,"spage"=>$spage,"searchdata"=>$searchData);
			}
		public function createSubMenuFetchSubmenus()
			{
				// Connect to MySQL database
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
				$searchSql 			.= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				// Get total count of records
				$sql 				= 	"SELECT count(*) from tblsub_menu 
										LEFT JOIN tblmenu on tblsub_menu.menuId=tblmenu.id WHERE 1".$searchSql;
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
				$sql 				= 	"SELECT  tblsub_menu.*,tblmenu.menuName from tblsub_menu 
										LEFT JOIN tblmenu on tblsub_menu.menuId=tblmenu.id WHERE 1 " .$searchSql."".$sortSql." ".$limitSql ;
				$results 			= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						if ($row['status']==0)
						{
							$row['status']		=	"<a href=\"createSubMenu.php?actionvar=Stauschange&id=".$row['id']."\" class=\"Second_link\">
							<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to activate\"></a>";
						}
						else
						{					
							$row['status']		=	"<a href=\"createSubMenu.php?actionvar=Stauschange&id=".$row['id']."\" class=\"Second_link\">
							<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to Inactivate\"></a>";
						}						
						$row['edit']	=	"<a href=\"createSubMenu.php?actionvar=Editform&id=".$row['id']."\" class=\"Second_link\">
											<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";			
						$data['rows'][] =	 array
							(
								'id' => $row['id'],
								'cell' => array($i, $row['menuName'], $row['name'],$row['status'],$row['edit'])
							);
					}
				$r 					=	json_encode($data);
				ob_clean();
				echo  $r;
				exit;
			}
		public function createSubMenuCreatenew()
			{
				$data			=	$this->getData("post");
				return array("data"=>$data);
			}
		public function createSubMenuAddmenu()
			{
				$data				=	$this->getData("request");
//				$data['dateAdded']	=	"escape now() escape";
				$data['dateAdded']	=	date("Y-m-d H:i:s");
				$sql				=	"SELECT max(`preference`) as max FROM `tblsub_menu` WHERE 1";
				$preference			=	end($this->getdbcontents_sql($sql));
				$data['preference']	=	$preference['max']+1;
				$dataIns			=	$this->populateDbArray("tblsub_menu",$data);

				$insertStatus		=	$this->db_insert("tblsub_menu",$dataIns, 0);
				//exit;
				if($insertStatus) 
					{
						$this->setPageError("Inserted successfully");
						$this->executeAction(false,"Listing",true);
					}
				else
					{
						$this->setPageError($this->getPageError());
						$this->executeAction(true,"Addform",true);
					}	
			}
		public function createSubMenuEditform()
			{
				$data			=	$this->getData("get");
				$sql			=	"select id, menuName from  tblmenu where status=1";
				$menuList		=	$this->getdbcontents_sql($sql); 
				$menuId=implode(",",end($this->getdbcontents_sql("select menuId from tblsub_menu where id='".$data['id']."'")));
				$menuListCombo	=	$this->get_combo_arr("menuId",$menuList,"id","menuName",$menuId,"style='width:100px;' ","Any Menu");			
				$sql			=	"select * from  tblsub_menu as rsm left join tblmenu as rm on rsm.menuId=rm.id where rsm.id=".$data['id'];
				$data			=	end($this->getdbcontents_sql($sql));
				return array("data"=>$data,"dropDown"=>$menuListCombo);
			}
		public function createSubMenuStauschange()
			{
				$data			=	$this->getData("get");
				$id				=	$data['id'];
				$sql			=	"select status from tblsub_menu where id=".$data['id'];
				$data			=	end($this->getdbcontents_sql($sql));
				
				if($data['status']==1)
					{
						$sql	=	"UPDATE  tblsub_menu SET status=0 WHERE id=".$id;
						$data	=	mysql_query($sql);
						if($data)	$this->setPageError("Status changed successfully");
						else $this->setPageError("Sorry! could not change status");
						$this->executeAction(false,"Listing",true);
					}
				else
					{
						$sql	=	"UPDATE  tblsub_menu SET status=1 WHERE id=".$id;
						$data	=	mysql_query($sql);
						if($data)	$this->setPageError("Status changed successfully");
						else $this->setPageError("Sorry! could not change status");
						$this->executeAction(false,"Listing",true);
					}
			}
		public function createSubMenuUpdate()
			{
				$details			=	$this->getData("request");
				//print_r($details);exit;
				$dataIns			=	$this->populateDbArray("tblsub_menu",$details);
				$updateStatus		=	$this->db_update("tblsub_menu",$dataIns,"id='".$details['id']."'");
				//print_r($dataIns);exit;
				if($updateStatus)
					{
						$this->setPageError("Updated successfully");
						$this->executeAction(false,"Listing",true);	
					}	
				else 
					{
						$this->setPageError("error updating");
						$this->executeAction(false,"Editform",true);	
					}
			}
		public function createSubMenuAddform()
			{
				$sql			=	"select * from  tblmenu where status=1";
				$menuList		=	$this->getdbcontents_sql($sql);
				$menuListCombo	=	$this->get_combo_arr("menuId",$menuList,"id","menuName",$data["id"],"style='width:100px;' ","Any Menu");
				return array("dropDown"=>$menuListCombo);
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
