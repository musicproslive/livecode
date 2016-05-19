<?php  
/****************************************************************************************
Created by	:	Arvind  
Created on	:	04-08-2011
Purpose		:	To Manage advertisement
******************************************************************************************/
class childadvertisementPageView extends modelclass
	{
		public function childadvertisementPageViewListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName(),'childadvertisementpage.php'));
				if(!isset($_GET['flag']))
				$flag	=	"OFF";
				else
				$flag	=	$_GET['flag'];
				return array("flag"=> $flag);
			}
		public function childadvertisementPageViewFetch()
			{
				// Connect to MySQL database
				$page = 0;	// The current page
				$sortname = '';	 // Sort column
				$sortorder = '';	 // Sort order
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
						$rp			=	10;
					}
				//$searchSql			=	"  where l.is_deleted=0 and l.user_group=0  ";
				
				
						$searchSql	.=	" AND `refference_name`='".$_SESSION['mode']."' ";
				
				// Setup sort and search SQL using posted data
				if(!empty($sortname)){
					$sortSql			 = 	" order by $sortname $sortorder";
				}
				$searchSql 			.= 	($qtype != '' && $query != '') ? " AND  `$qtype` LIKE '%$query%'" : '';
				// Get total count of records
				$sql 				= 	"SELECT count(*) FROM `tblchildadvertisement_listing` WHERE `is_deleted`=0 ".$searchSql;
				$result				= 	$this->db_query($sql,0);
				$row 				= 	mysql_fetch_array($result);
				$total 				= 	$row[0];
				// Setup paging SQL
				$pageStart 			= 	($page-1)*$rp;
				if($pageStart<0)
					{
						$pageStart	=	0;
					}
				$limitSql 			= 	" limit $pageStart, $rp";
				// Return JSON data
				$data				= 	array();
				$data['page'] 		= 	$page;
				$data['qtype'] 		= 	$qtype;
				$data['query'] 		= 	$query;
				$data['total']		= 	$total;
				$data['rows'] 		= 	array();
				$sql 				= 	"SELECT * FROM `tblchildadvertisement_listing` WHERE `is_deleted`=0 " .$searchSql."".$sortSql;

				//$sql	.=	" ORDER BY `created_date` DESC";
				 $sql	.=	$limitSql;
				$results 			= 	$this->db_query($sql,0);
				$i					=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;			
						$row['delete']	=	"<a href=\"childadvertisementpage.php?actionvar=Deletedata&id=".$row['advertisement_id']."\" class=\"Second_link\" onclick=\"return askDelete()\"\">
											<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\" \"></a>";
						
						$row['view']	=	"<a href=\"childadvertisementPageView.php?id=".$row['refference_name']."\" class=\"Second_link\" >
											<img src=\"../images/view.png\" border=\"0\" title=\"Click here to delete\" ></a>";
						$row['mode']	=	"Nornal Add";
						if($row["advt_mode"]==1){
							$row['mode']	= "Google Add";
						}
						$row['stat']		=	"<a href=\"childadvertisementPageView.php?type=0&actionvar=Stauschange&advid=".$row['advertisementlisting_id']."\" class=\"Second_link\">
							<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to disapprove\"></a>";
							
							
						if($row["status"]==0){
							$row['stat']	= 	"<a href=\"childadvertisementPageView.php?type=1&actionvar=Stauschange&advid=".$row['advertisementlisting_id']."\" class=\"Second_link\">
							<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to approve\"></a>";
						}
						
							$row['delete']	= 	"<a href=\"childadvertisementPageView.php?actionvar=Delete&advid=".$row['advertisementlisting_id']."\" class=\"Second_link\" onclick=\"return askDelete()\"\">
											<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\" \"></a>";					
						if($_GET['flag'] == 'ON')
							$view	=	$this->getAdvertisement($row['advertisementlisting_id']);
						else
							$view	=	'<a href="childadvertisementPageView.php?actionvar=Viewform&id='.$row['advertisementlisting_id'].'"><img src="../images/edit.gif" border="0" title="Edit add details"></a>';	
						
						$data['rows'][] =	 array
							(
								'id' => $row['advertisementlisting_id'],
								'cell' => array($i,$row['mode'], $row['refference_name'],$row['stat'],$view, $row['delete'])
							);
					}
					
					ob_clean();
					
				$r 					=	json_encode($data);
				echo  $r;
				exit;
					exit;
			}
			
		public function childadvertisementPageViewStauschange(){
				
				$this->db_update("tblchildadvertisement_listing",array("status"=>$_GET['type']),"advertisementlisting_id=".$_GET['advid'],1);				
				header("Location:childadvertisementPageView.php?id=".$_SESSION['mode']);	
				exit;	
			}
		
		public function childadvertisementPageViewViewform(){
				$query		=	"SELECT * FROM `tblchildadvertisement_listing` WHERE `advertisementlisting_id`=".$_GET["id"];
				$rec		=	end($this->getdbcontents_sql($query));
				return $rec;
				exit;
			}
		public function childadvertisementPageViewSave()
			{
				$array	=	array();	
				$array['refference_name']	=	$_POST['refference_name'];
				$array['advt_mode']	=	$_POST['txtMode'];			
					
				if($_POST['txtMode'] == 1){
					if(empty($requestData["txtgoogleAdsence"])){
						$this->setPageError("Please enter Mandatory fields ");
						return false;
					}
					$array['google_add']	=	$_POST['txtgoogleAdsence'];
				}
					
				if($_POST['txtMode'] == 2)
					{
						if($_FILES['fileAdd']['name'])
						{
							$ext			=	explode(".",$_FILES['fileAdd']['name']);
							$ext			=	$ext[count($ext)-1];
							$filename		=	"Adv_".strtotime(date("Y-m-d h:i:s")).trim(microtime(1)).".".$ext;
							$path			=	 dirname(__FILE__)."\\".$filename;
							$path			=	 str_replace("classes","Uploads\\advertisement",$path);
							$dirname		=	 str_replace("classes","Uploads\\advertisement",dirname(__FILE__)."\\");
							$upObj			=	$this->create_upload(10,"jpg,png,jpeg,gif,swf");
							$adimg			=	$upObj->copy("fileAdd",$dirname,1,$filename);
							if($adimg)			$upObj->img_resize("190","120",$dirname);
							else 				$this->setPageError($upObj->get_status());
							$array["image_path"]		=	$filename;
						}
						$array["title"]				=	$_POST["txtTitle"];
						$array["url"]				=	$_POST["txtUrl"];	
					}
				$array["height"]				=	$_POST["height"];
				$array["width"]					=	$_POST["width"];
				if($this->db_update("tblchildadvertisement_listing",$array,$this->dbSearchCond('=','advertisementlisting_id',$_POST['id']),0))
					{
						$this->setPageError("Data Sucessfully Updated");
						$this->executeAction(true,"Listing",true,false,"id=".$_POST['refference_name']);
						
					}
				else
					{
						$this->setPageError($this->getDbErrors());
						$this->executeAction(true,"Listing",true,false,"id=".$_POST['refference_name']);
						return;
					}
			}
		public function getAdvertisement($id)
			{
				$query		=	"SELECT * FROM `tblchildadvertisement_listing` WHERE `advertisementlisting_id`= $id";
				$data		=	end($this->getdbcontents_sql($query));
				if( $data['advt_mode']==2){
						$Ads	=	'<a href="'.$data['url'].'" target="_blank" ><img src="../uploads/advertisement/'.$data['image_path'].'" border="0" title="'.$data['title'].'"></a>';
					}
				else
					{
						$Ads	=	html_entity_decode($data['google_add']);
					}
				return $Ads;
			}
		public function childadvertisementPageViewDelete(){
			$cls			=	 new advertisementManagement();
			$rec			=	$cls->listAdvchildById($_GET['advid']);
			print_r($rec);
			if($rec[0]["advt_mode"]==2){
					$cls->unsetImage($rec[0]["image_path"]);
			}			
			$this->dbDelete_cond("tblchildadvertisement_listing","advertisementlisting_id=".$_GET['advid'],1);				
			header("Location:childadvertisementPageView.php?id=".$_SESSION['mode']);	
			exit;	
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