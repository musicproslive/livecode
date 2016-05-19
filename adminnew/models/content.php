<?php 
/****************************************************************************************
Created by	:	Suneesh 
Created on	:	21-08-2012
Purpose		:	To Manage contents
*****************************************************************************************/
class content extends modelclass
	{
		public function contentListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				$cmsObj				=	new cms;
				$section				=	$cmsObj->getCmsSectionList();
				unset($_SESSION["section"]);unset($_SESSION['searchKey']);
				if(!empty($_POST['sel_section'])){
					$_SESSION["section"]		=	trim($_POST["sel_section"]);
				}
				if(!empty($_POST['txtKey'])){
					$_SESSION["searchKey"]		=	trim($_POST["txtKey"]);
				}	
					$sel_group	=	trim($_POST['sel_section']);	
					$txtKey	=	trim($_POST["txtKey"]);
				return array("section"=>$section,"sel_section"=>$sel_group,"txtKey"=>$txtKey);
			}
		public function contentFetch()
		{
					$page 			= 	0;	// The current page
					$sortname 		= 	'title';	 // Sort column
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
					if(!empty($_GET['field']) && !empty($_GET['keyword']))
						{
							$searchSql		=	" AND `".$_GET['field']."`='".$_GET['keyword']."%' ";
						}
					// Setup sort and search SQL using posted data
					$sortSql				 = 	"order by $sortname $sortorder";
					$searchSql 				.= 	($qtype != '' && $query != '') ? " AND  `$qtype` LIKE '%$query%'" : '';
					// Get total count of records
					$sql					= 	"SELECT count(*) FROM tblcms AS C
												 LEFT JOIN tblcms_section AS S ON C.section_id = S.id WHERE  C.status ='1'";
					if(!empty($_SESSION["section"])){
				   		 	$sql			.=	 " AND S.section ='".$_SESSION["section"]."'";
				    	}
						if(!empty($_SESSION["searchKey"])){
				   		 	$sql			.=	 " AND  C.title LIKE '%".$_SESSION["searchKey"]."%'";
				    	}
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
					$sql 					= 	"SELECT C.id,C.title,C.description,S.section FROM tblcms AS C
												 LEFT JOIN tblcms_section AS S ON C.section_id = S.id
												 WHERE  C.status ='1'";
					if(!empty($_SESSION["section"])){
				   		 	$sql			.=	 " AND S.section ='".$_SESSION["section"]."'";
				    	}
						if(!empty($_SESSION["searchKey"])){
				   		 	$sql			.=	 " AND  C.title LIKE '%".$_SESSION["searchKey"]."%'";
				    	}
						$sql				 .="$searchSql	$sortSql $limitSql";									
					$results 	= 	$this->db_query($sql,0);
					$i			=	0;
					while ($row = mysql_fetch_assoc($results)) 
						{
							$i++;
					$row['view']	=	"<a href=\"content.php?actionvar=Viewform&id=".$row['id']."\" class=\"Second_link\">
										<img src=\"../images/view.png\" border=\"0\" title=\"View Details\"></a>";
					
					$row['edit']	=	"<a href=\"content.php?actionvar=Editform&id=".$row['id']."\" class=\"Second_link\">
										<img src=\"../images/edit.png\" border=\"0\" title=\"Edit CMS Details\"></a>";			
						
					$row['delete']	=	"<a href=\"content.php?actionvar=Deletedata&id=".$row['id']."\" class=\"Second_link\" onclick = \"return delall()\">
										<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";
							$data['rows'][] = array
						(
					'id' => $row['id'],
					'cell' => array($i, $row['section'], $row['title'], $this->getLimitedText($row['description'],75),$row['view'],$row['edit'],$row['delete'])
						);
					}
					ob_clean();
					$r =	json_encode($data);
					echo  $r;
					exit;
		}
		
		
		public function contentSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function contentCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function contentReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		public function contentBack()
			{
				$this->clearData("Viewform");
				$this->executeAction(false,"Listing",true,false);	
			}	
		public function contentAddform()
			{
				$cms	=	new cms();
				$data['section']	=	$cms->getCMSSectionData("status=1");
				//print_r($data);exit;
				return array("data"=>$data);
			}
		
		public function contentEditform()
			{
				$cms	=	new cms();
				$data				=	$this->getcmsDetails($_GET['id']);
				$data['section']	=	$cms->getCMSSectionData("status=1");
				//return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
				return array("data"=>$data,"combo"=>$combo,"sagent"=>$sid);
			}
			
		public function contentUpdatedata()
			{
				$files		=	$this->getData("files");
				$details	=	$this->getData("request");
				$details	=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tblcms ",$details);
				$updateStatus=	$this->db_update("tblcms ",$dataIns,"id='".$details['id']."'",1);
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
		public function contentSavedata()
			{

				$data				=	$this->getData("request");
				$data["date_added"] = date('Y-m-d H:i:s');
				$dataIns			=	$this->populateDbArray("tblcms",$data);	
				
				if(!$this->getPageError())
					{
						if($this->createCms($dataIns))	
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
			
		public function contentDeletedata()
			{
				$qry		=	"delete from tblcms where id =".$_GET['id'];
				if($this->db_query($qry))	
						$this->setPageError("Deleted Successfully");
				else
						$this->setPageError("Sorry, We are experiencing some technical difficulties. Please try again after some time");
				return $this->executeAction(false,"Listing",true);
					
			}	
		
		public function createCms($dataIns)
			{
					//print_r($dataIns);exit;
					$creationSucces						=	$this->db_insert("tblcms",$dataIns);
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
			
		public function contentViewform()
			{
				$data				=	$this->getData("get");
				//print_r($data);exit;
				$memberObj			=	new content;
				$data				=	$memberObj->getcmsDetails($data['id']);
				$detail =$data['description'];
				return array("data"=>$this->getHtmlData($data),"detail"=>$detail);
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
				//echo $this->getMethodName();
				//exit;
				return $this->actionReturn;
			}
		public function __destruct() 
			{
				parent::childKilled($this);
			}
		
		public function getcmsDetails($Id="",$args="1")
			{  
				$sql					=	"select * from tblcms where id='$Id' and ".$args;
				$result					=	end($this->getdbcontentshtml_sql($sql));
				return $result;
			}
		
		public function deletecms($id)
		{
			$qry		=	"delete from tblcms where id ='$id'";
			$result		=	$this->getdbcontents_sql($qry);
			return $this->executeAction(false,"Listing",true);return result;
		}
		
	}