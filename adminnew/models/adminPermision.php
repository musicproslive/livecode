<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	20-08-2011
Purpose		:	adminPermision
*****************************************************************************************/
class adminPermision extends modelclass
	{
		public function adminPermisionListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				 $_SESSION['pid']	=	$_GET['id'];
				 
			}
		public function adminPermisionFetch(){
				
			$pId			=	$_SESSION['pid'];
			// Connect to MySQL database
			$page 			= 	0;	// The current page
			$sortname 		= 	'menuName';	 // Sort column
			$sortorder	 	= 	' asc';	 // Sort order
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
							$page 	= 	mysql_real_escape_string($_POST['page']);
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
			//$searchSql			=	" and  status =1";
			//$searchSql			=	" and  group_id='$gId'";
			if(!empty($_GET['field']) && !empty($_GET['keyword']))
				{
					$searchSql		.=	" AND `".$_GET['field']."`='".$_GET['keyword']."%' ";
				}
			// Setup sort and search SQL using posted data
			$sortSql				 = 	"order by $sortname $sortorder";
			$searchSql 				.= 	($qtype != '' && $query != '') ? " AND  `$qtype` LIKE '%$query%'" : '';
			// Get total count of records
			$sql					= 	"SELECT count(*) from tbluser_roles where 1 $searchSql";
			$result 				= 	$this->db_query($sql,0);
			$row 					= 	mysql_fetch_array($result);
			if(empty ($row))
			{
				$total				= 	0;
			}
			$total					= 	$row[0];
			// Setup paging SQL
			$pageStart 				= 	($page-1)*$rp;
			if($pageStart<0)
				{
					$pageStart		=	0;
				}
			$limitSql 				= 	"limit $pageStart, $rp";
			$searchSql1			=	"  status =1";
			// Return JSON data
			$data 					= 	array();
			$data['page'] 			= 	$page;
			$data['qtype'] 			= 	$qtype;
			$data['query'] 			= 	$query;
			$data['total'] 			= 	$total;
			$data['rows'] 			= 	array();
			/*$sql 					=	"SELECT M.menuName,M.id,P.permission_id from tblmenu as M LEFT JOIN 
										tbladmin_permission as P ON M.id=P.menu_id AND P.admin_id=$pId order by menuName asc ";*/

			$sql					=	"SELECT * FROM `tbluser_roles` where admin_group != 1 order by admin_group  ASC ";						
			//file_put_contents("file.txt",$sql);		
			$results	= 	$this->db_query($sql,0);
			$i			=	0;
			while ($row = mysql_fetch_assoc($results)) 
				{
					$i++;
				
				if ($row['status']==0)
					{
						$row['status']		=	"<a href=\"adminPermision.php?actionvar=Stauschange&id=".$row['id']."\" class=\"Second_link\">
						<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to activate\"></a>";
					}
					else
					{					
						$row['status']		=	"<a href=\"adminPermision.php?actionvar=Perchange&id=".$row['id']."\" class=\"Second_link\">
						<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to Inactivate\"></a>";
					}
						
					$data['rows'][] = array
				(
			'id' => $row['id'],
			'cell' => array($i, $row['role_name'],"<a href='setuserPermision.php?id=".$row["role_id"]."'>Set Permission</a>")
				);
			}
			$r =json_encode($data);
			ob_clean();
			echo  $r;
			exit;
			
				}
		public function adminPermisionAddform(){
		
			$query			=	"SELECT MAX(`admin_group`)  FROM `tbluser_roles` ";
			$data			=	end($this->getdbcontents_sql($query));
			$data["max"]	=	$data["MAX(`admin_group`)"]+1;	
			return $data;
		
		}
		
		public function  adminPermisionSubmit(){
					
				$details	=	$this->getData("get");
			    $id			=	$data[0]['id'];
				$dataUpdate	=	array();
				$dataUpdate["role_name"]=	$_POST["role_name"];
				$dataUpdate["role_access_key"]=	$_POST["role_access_key"];
				$dataUpdate["admin_group"]=	0;
				$this->db_insert("tbluser_roles", $dataUpdate, 0);
				header("Location:adminPermision.php");exit;
		}
		public function adminPermisionStauschange()
			{
				$details	=	$this->getData("get");
				$sql		=	"select id,menuName from tblmenu where id=".$details['id']."";
				$data		=	$this->getdbcontents_sql($sql);
				//print_r($details);exit;
			    $id			=	$data[0]['id'];
				$dataUpdate					=	array();
				$dataUpdate['admin_id']		=	$_SESSION['pid'];
				$dataUpdate['menu_id']		=	$data[0]['id'];
				$dataUpdate['created_on']	=	date('Y-m-d H:i:s');
				//$dataUpdate['status']		=	"1";
				$this->db_insert("tbladmin_permission ",$dataUpdate,"id =$id",0);
				header("Location:adminPermision.php?id=".$_SESSION['pid']);exit;
			}
		public function adminPermisionPerchange()
			{
				$details	=	$this->getData("get");
				$id			=	$details['id'];
				$this->dbDelete_cond("tbladmin_permission","menu_id =$id",0);
				header("Location:adminPermision.php?id=".$_SESSION['pid']);exit;
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