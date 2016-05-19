<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	04-08-2011
Purpose		:	Country Lists
******************************************************************************************/
class country extends modelclass
	{
		public function countryListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				/********************************************************************/
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$data		=	$this->getData("get");
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
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("like", "first_name", "%".trim($searchData["keyword"])."%")." )";
					}
				if(trim($searchData["memberId"]))			
					{
						$sqlFilter["cond"]	.=	" and (".$this->dbSearchCond("=", "user_id",trim($searchData["memberId"]))." or ".$this->dbSearchCond("=", "nm.email",trim($searchData["memberId"])).")";	
					}
				
				$sql						=	"select * from tblcountries where 1".$sqlFilter["cond"].$sqlFilter["ord"]." ".$orderBy ;
				$this->addData(array("sql"=>$sql),"post","",false);
				$spage				 		=	$this->create_paging("n_page",$sql);
				$data				 		=	$this->getdbcontents_sql($spage->finalSql());
				
				if(!$data)						$this->setPageError("No records found !");
				$searchData					=	$this->getHtmlData($this->getData("post","Search",false));
				
				//$this->print_r($data);exit;
				
				return array("data"=>$data,"spage"=>$spage);
			}
		public function countryFetchCountry()
			{
				// Connect to MySQL database
				$page 			= 	0;	// The current page
				$sortname 		= 	'country_name';	 // Sort column
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
						$rp			= LMT_SITE_ADMIN_PAGE_LIMIT ;
					}

				// Setup sort and search SQL using posted data
				$sortSql				 = 	"order by $sortname $sortorder";
				$searchSql 				.= 	($qtype != '' && $query != '') ? " AND  `$qtype` LIKE '%$query%'" : '';
				
				// Get total count of records
				$sql					= 	"SELECT count(*) from tblcountries where 1  $searchSql";
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
				$sql 					= 	"SELECT * from tblcountries where 1  $searchSql $sortSql $limitSql";							
				$results 				= 	$this->db_query($sql,0);
				
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						$row['edit']	="<a href=\"country.php?actionvar=Editform&id=".$row['country_id']."\" class=\"Second_link\">
											<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";
						if($row['is_active'])
							$row['delete']	="<a href=\"country.php?actionvar=Deletedata&id=".base64_encode(serialize($row['country_id']))."&mode=0\" class=\"Second_link\">
											<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to block country\" onclick = \"return delall()\"></a>";
						else
							$row['delete']	="<a href=\"country.php?actionvar=Deletedata&id=".base64_encode(serialize($row['country_id']))."&mode=1\" class=\"Second_link\">
											<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to activate country\" onclick = \"return delall()\"></a>";					
						
						$row['state']	=	"<a href=\"state.php?sid=".$row['country_id']."\" class=\"Second_link\">
											 <img src=\"../images/inner_details.png\" border=\"0\" title=\"Click here to view states\"></a>";
						
						$data['rows'][] = array
							(
								'id' => $row['country_id'],
								'cell' => array($i, $row['country_name'],$row['state'],$row['edit'],$row['delete'])
							);
				}
				$r =json_encode($data);
				ob_clean();
				echo  $r;
				exit;
			}
		public function countryAddform()
			{
			}
		public function countryEditform()
			{	
				$data				=	$this->getData("get");
				$data				=	$this->getCountryName($data['id']);
				return array("data"=>$this->getHtmlData($data));
			}
		public function countrySavedata()
			{
				$data		=	$this->getData("post");
				$dataIns	=	$this->populateDbArray("tblcountries",$data);	
				
				if(!$this->getPageError())
					{
						if($this->createCountry($dataIns))	
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
			
		public function createCountry($dataIns)
			{
					$creationSucces						=	$this->db_insert("tblcountries",$dataIns);
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
			
		public function countryDeletedata()
			{
				$data				=	$this->getData("get");						
				$result				=	$this->deletecountry(unserialize(base64_decode($data['id'])), $data['mode']);
				
				if($result)	
					{
						$this->setPageError("Successfully Updated");
						return $this->executeAction(false,"Listing",true);
					}
				else
					{
						$this->setPageError($this->getPageError());
						$this->executeAction(true,"Listing",true);
					}
			}	
			
		public function deleteCountry($id, $mode)
			{
					$sql				=	"UPDATE tblcountries SET is_active = $mode WHERE country_id = $id";
					$result				=	$this->db_query($sql, 0);
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
		public function getCountryName($id)
			{
					$sql						=	"SELECT * FROM tblcountries WHERE country_id=$id";
					$result						=	end($this->getdbcontents_sql($sql));
					return $result;
			}
		public function countryUpdatedata()
			{
				$data		=	$this->getData("post");

				$data['country_id']	=	$data['cid'];
				$data['country_name'] = $data['country_name'];
				$dataIns			=	$this->populateDbArray("tblcountries",$data);
				$result				=	$this->db_update("tblcountries",$dataIns,"country_id=".$data['cid'],1);	
				if($result)
					{
								$this->setPageError("Updated Successfully");
								return $this->executeAction(false,"Listing",true);
					}
				else
					{
						$this->setPageError($this->getPageError());
						$this->executeAction(true,"Editform",true);
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