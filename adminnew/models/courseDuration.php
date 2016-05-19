<?php 
/****************************************************************************************
Created by	:	Suneesh 
Created on	:	11-03-2013
Purpose		:	To Manage Course Duration
******************************************************************************************/
class courseDuration extends modelclass
	{
		public function courseDurationListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
/*				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
*/				return;
				
			}
		public function courseDurationFetch()
			{
					$page 			= 	0;	// The current page
					$sortname 		= 	'id';	 // Sort column
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
					$sortSql				 = 	" order by $sortname $sortorder";
					$searchSql 				 = 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
					
					$sql					= 	"SELECT count(*) from tbllookup_course_duration   WHERE 1 $searchSql ";
					$result 				= 	$this->db_query($sql,0);
					$row 					= 	mysql_fetch_array($result);
					$total					= 	$row[0];
					
					// Setup paging SQL
					$pageStart 				= 	($page-1)*$rp;
					if($pageStart<0)
						{
							$pageStart		=	0;
						}
					$limitSql 				= 	" limit $pageStart, $rp";
					$data 					= 	array();
					$data['page'] 			= 	$page;
					$data['qtype'] 			= 	$qtype;
					$data['query'] 			= 	$query;
					$data['total'] 			= 	$total;
					$data['rows'] 			= 	array();
			
					
					$sql		=	"SELECT * FROM `tbllookup_course_duration` WHERE 1 $searchSql $sortSql $limitSql";
					$results 	= 	$this->db_query($sql,0);
					$i			=	$pageStart;
					while ($row = mysql_fetch_assoc($results)) 
						{
							//file_put_contents("file.txt",$row);
							$i++;
							$sp		=	'';
							
							$data['rows'][] = array(
							'id' => $row['id'],
							'cell' => array($i, $row['time']." Minutes","<a href='courseDuration.php?actionvar=Editform&type_id=".$row["id"]."'><img src='../images/edit.png' border='0' title='Edit Details'></a>")
						);
					}
					
					$r =json_encode($data);
					ob_clean();
					echo  $r;
					exit;
					
		}
	public function courseDurationEditform()
		{	
			$data					=	$this->getData("get");
			$sql					=	"SELECT * FROM `tbllookup_course_duration` WHERE `id`=".$data["type_id"];
			$rec["data"]			=	$this->getdbcontents_sql($sql);
			return $rec;
			exit;
		}
		public function courseDurationAddform(){
			
		}
		
		
		public function courseDurationSavedata(){
				$details	=	$this->getData("POST");
				$dataIns	=	$this->populateDbArray("tbllookup_course_duration",$details);
				$this->db_insert("tbllookup_course_duration",$dataIns,0);
				$this->redirectAction(false,"Successfully added !!","Listing");		
				
		}
		
		public function courseDurationUpdatedata(){
				
				$details	=	$this->getData("POST");
				$dataIns	=	$this->populateDbArray("tbllookup_course_duration",$details);
				$this->db_update("tbllookup_course_duration",$dataIns,"id=".$_GET['type_id'],0);	
				$this->redirectAction(false,"Successfully updated !!","Listing");	
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
	}
