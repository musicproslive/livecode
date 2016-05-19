<?php 
/****************************************************************************************
Created by	:	Lijesh
Created on	:	Apr-11-2012
Purpose		:	Set day light saving time.
****************************************************************************************/
class dlTimeZone extends modelclass
	{
		public function dlTimeZoneListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return;
			}
		public function dlTimeZoneFetchTimeZones()
			{
				$page = 0;	// The current page
				$sortname = 'timezone_location';	 // Sort column
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
				$sortSql			= 	" order by $sortname $sortorder";
				$searchSql 			= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				// Get total count of records
				$sql 				= 	"SELECT count(*) FROM `tbltime_zones` WHERE 1 ".$searchSql;
				
				$result				= 	$this->db_query($sql, 0);
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
				$sql 				= 	"select id, Concat(timezone_location, gmt) as timezone, prev_val, is_active FROM tbltime_zones WHERE 1 $searchSql $sortSql $limitSql";		
				$results 			= 	$this->db_query($sql, 0);
				$i					=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						
						if(empty($row['prev_val']))
							$row['action']	=	"<a href=\"dlTimeZone.php?actionvar=SetDL&id=".$row['id']."\" class=\"Second_link\"><img title=\"Set  Daylight Saving\" src=\"images/clock.png\"></a>";
						else
							$row['action']	=	"<a href=\"dlTimeZone.php?actionvar=ResetDL&id=".$row['id']."\" class=\"Second_link\"><img title=\"Reset Daylight Saving\" src=\"images/refresh.png\" width=\"20\" height=\"20\"></a>";
							
						if($row['is_active'])
							$row['update']	="<a href=\"dlTimeZone.php?actionvar=UpdateStatus&id=".base64_encode(serialize($row['id']))."&mode=0\" class=\"Second_link\">
											<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to block timezone\" onclick = \"return delall()\"></a>";
						else
							$row['update']	="<a href=\"dlTimeZone.php?actionvar=UpdateStatus&id=".base64_encode(serialize($row['id']))."&mode=1\" class=\"Second_link\">
											<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to activate timezone\" onclick = \"return delall()\"></a>";					
																		
						$data['rows'][] =	 array
							(
								'id' => $row['id'],
								'cell' => array($i, $row['timezone'], $row['prev_val'], $row['action'], $row['update'])
							);
					}
				$r 	=	json_encode($data);
				ob_clean();
				echo  $r;
				exit;
			}
		public function dlTimeZoneSetDL()
			{ 
				$data = $this->getData('get');
				return $data;
				$this->print_r($data);exit;
				
			}
		
		public function dlTimeZoneResetDL()
			{
				$data = $this->getData('get');	
				$timeZoneObj = new timeZone();			
				$timeZone = reset($timeZoneObj->getTimeZone($data['id']));
				//$this->print_r($timeZone);exit;
				if(!empty($timeZone))
					{
						$tzVal['gmt'] = $timeZone['prev_val'];
						$tzVal['prev_val'] = '';
						
						if($this->db_update('tbltime_zones',$tzVal, "id=".$data['id'], 1))
							$this->setPageError('Daylight saving time reset successfully');
						else
							$this->setPageError('Invalid operation...please try again');
					}					
				return $this->executeAction(false,"Listing",true);		
				$this->print_r($data);exit;
			}
			
		public function dlTimeZoneSave()
			{ 
				$data = $this->getData('post');
				$timeZoneObj = new timeZone();
				$timeZone = reset($timeZoneObj->getTimeZone($data['time_zone_id']));				
				if(!empty($timeZone))
					{	
						$timestamp = strtotime($timeZone['gmt']);
						if($data['operator'] == '-' && $timeZone['sign'] == '-')
							$sign = '+';
						else if($timeZone['sign'] == '-')
							$sign = $timeZone['sign'];
						else
							$sign = $data['operator'];	
							
						$timestamp = strtotime($sign.$data['hour'].' hour', $timestamp);
						$tzVal['gmt'] = '(GMT'.$timeZone['sign'].date('H:i', strtotime($sign.$data['minute'].' minute', $timestamp)).')';
						$tzVal['prev_val'] = '(GMT'.$timeZone['sign'].$timeZone['gmt'].')';
						
						if($this->db_update('tbltime_zones',$tzVal, "id=".$data['time_zone_id'], 0))
							$this->setPageError('Successfully set daylight saving');
						else
							$this->setPageError('Invalid operation...please try again');						
						
					}
				return $this->executeAction(false,"Listing",true);	
				$this->print_r($tzVal);		
				exit;
				
			}
		
		function dlTimeZoneUpdateStatus()
			{
				$data				=	$this->getData("get");					
				$id                 = 	unserialize(base64_decode($data['id']));				
				$sql				=	"UPDATE tbltime_zones SET is_active = {$data['mode']} WHERE id = $id";
				$result				=	$this->db_query($sql, 0);
				
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