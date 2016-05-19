<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	16-07-2011
Purpose		:	To Manage entertainment Section
*****************************************************************************************/
class entertainment extends modelclass
	{
		public function entertainmentListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return;

			}
			
		public function entertainmentFetch(){
			
		// Connect to MySQL database
		$page 			= 	0;	// The current page
		$sortname	 	= 	'file_name';	 // Sort column
		$sortorder 		= 	'asc';	 // Sort order
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
				$rp 		=	 mysql_real_escape_string($_POST['rp']);
			}
		if(empty($rp))
			{
				$rp			=LMT_SITE_ADMIN_PAGE_LIMIT;
			}
		$searchSql			=	" WHERE  `is_deleted`='0'  ";
		if(!empty($_GET['field']) && !empty($_GET['keyword']))
			{
				$searchSql	.=	" AND `".$_GET['field']."`='".$_GET['keyword']."%' ";
			}
		// Setup sort and search SQL using posted data
		$sortSql 			= 	"order by $sortname $sortorder";
		$searchSql 			.= 	($qtype != '' && $query != '') ? " AND  `$qtype` LIKE '%$query%'" : '';
		// Get total count of records
		$sql 				= 	"SELECT count(*)	from tblentertainment   $searchSql";
		$result 			= 	$this->db_query($sql,0);
		$row 				=	mysql_fetch_array($result);
		$total 				= 	$row[0];
		// Setup paging SQL
		$pageStart			= 	($page-1)*$rp;
		if($pageStart<0)
			{
				$pageStart	=	0;
			}
		$limitSql 			= 	"limit $pageStart, $rp";
		// Return JSON data
		$data 				= 	array();
		$data['page'] 		= 	$page;
		$data['qtype'] 			= 	$qtype;
		$data['query'] 			= 	$query;
		$data['total'] 		= 	$total;
		$data['rows'] 		= 	array();
		$sql 				= 	"SELECT *,DATE_FORMAT(created_date, '".$_SESSION["DATE_FORMAT"]["M_DATE"]." ". $_SESSION["DATE_FORMAT"]["M_TIME"]."') AS created_date from tblentertainment
								$searchSql $sortSql $limitSql";
								
		
		$results 			= 	$this->db_query($sql,0);
//		print_r($results);
		$i					=	0;
		while ($row = mysql_fetch_assoc($results)) 
			{
				$i++;
				if($row['type']=='video')
				{
/*				$row['play']	="<a href=\"entertainment.php?actionvar=Editform&id=".$row['id']."\" class=\"Second_link\">
					<img src=\"../images/right_arrow.png\" border=\"0\" title=\"Click here to play\"></a>";
*/
				$row['play']	="<a href=\"videoView.php?&type=0&id=".$row['file_name']."\" class=\"Second_link\" >
					<img src=\"../images/right_arrow.png\" border=\"0\" title=\"Click here to play\"></a>";
				}
				if($row['type']=='Audio')
				{
				$row['play']	="<a href=\"audioView.php?id=".$row['file_name']."&title=".$row['movietitle']."\" class=\"Second_link\">
					<img src=\"../images/right_arrow.png\" border=\"0\" title=\"Click here to play\"></a>";
				
//				$row['play']	="<a href=\"../Uploads/general/Audio/".$row['file_name']."\" class=\"Second_link\" onclick=\"arv()\">play</a>";
				}	
				if($row['type']=='Youtube Url')
				{
				$row['play']	="<a href=\"videoView.php?&type=1&id=".$row['file_name']."\" class=\"Second_link\">
					<img src=\"../images/right_arrow.png\" border=\"0\" title=\"Click here to play\"></a>";
				}
				
				
				if ($row['Status']==1)
				{
					$row['Status']		=	"<a href=\"entertainment.php?actionvar=Stauschange&id=".$row['id']."\" class=\"Second_link\">
					<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to activate\"></a>";
				}
				else
				{					
					$row['Status']		=	"<a href=\"entertainment.php?actionvar=Stauschange&id=".$row['id']."\" class=\"Second_link\">
					<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to Inactivate\"></a>";
				}
		
				$row['edit']	="<a href=\"entertainment.php?actionvar=Editform&id=".$row['id']."\" class=\"Second_link\">
					<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";			
			
				$row['delete']	="<a href=\"entertainment.php?actionvar=Deletedata&id=".$row['id']."\" class=\"Second_link\" onclick = \"return delall()\">
					<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";
				$data['rows'][] 	= 	array
					(
						'id' => $row['id'],
						'cell' => array($i, $row['type'],$row['movietitle'], $row['created_date'],$row['Status'],$row['delete'],$row['play'])
					);
			}
		$r =json_encode($data);
		ob_clean();
		echo  $r;
		exit;
			
		}
		public function entertainmentSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function entertainmentCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function entertainmentReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		public function entertainmentAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		
		public function entertainmentAddform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new entertainment;
				$data				=	$memberObj->getentertainment($data['id']);
				$terrObj			= 	new territory(); 
				$country_combo		=	$this->get_combo_arr("sel_country",$terrObj->getAllCountries("status='1' order by preference"),"id","country",$data["sel_country"],"'valtype='emptyCheck-please select a country' onchange=\"getcombo(this.value,'stateDivId');\"");
				$stateArry			=	$terrObj->getAllStates("sel_country=".$data['sel_country']." and status='1' order by preference");
				$state_combo		=	$this->get_combo_arr("sel_state",$terrObj->getAllStates(" country_id=".$data['sel_country']." and status='1' order by preference"),"id","state",$data["sel_state"],"'valtype='emptyCheck-please select a country' onchange=\"getcities(this.value,'cityDivId');\"");
				$city_combo			=	$this->get_combo_arr("sel_city",$terrObj->getAllcities(" state_id=".$data['sel_state']." and status='1' order by preference"),"id","city",$data["sel_city"],"'valtype='emptyCheck-please select a city'");
				$combo				=	array();
				$combo['country']	=	$country_combo;
				$combo['state']		=	$state_combo;
				$combo['city']		=	$city_combo;
				
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
				
			}
		
		public function entertainmentEditform()
			{	
				$data				=	$this->getData("get");
				$memberObj			=	new entertainment;
				$data				=	$memberObj->getentertainment($data['id']);//print_r($data);exit;
/*				$terrObj			= 	new territory(); 
				$country_combo		=	$this->get_combo_arr("sel_country",$terrObj->getAllCountries("status='1' order by preference"),"id","country",$data["sel_country"],"'valtype='emptyCheck-please select a country' onchange=\"getcombo(this.value,'stateDivId');\"");
				$stateArry			=	$terrObj->getAllStates("sel_country=".$data['sel_country']." and status='1' order by preference");
				$state_combo		=	$this->get_combo_arr("sel_state",$terrObj->getAllStates(" country_id=".$data['sel_country']." and status='1' order by preference"),"id","state",$data["sel_state"],"'valtype='emptyCheck-please select a country' onchange=\"getcities(this.value,'cityDivId');\"");
				$city_combo			=	$this->get_combo_arr("sel_city",$terrObj->getAllcities(" state_id=".$data['sel_state']." and status='1' order by preference"),"id","city",$data["sel_city"],"'valtype='emptyCheck-please select a city'");
				$combo				=	array();
				$combo['country']	=	$country_combo;
				$combo['state']		=	$state_combo;
				$combo['city']		=	$city_combo;
*/				return array("data"=>$this->getHtmlData($data));
			}
			
		public function entertainmentUpdatedata()
			{
				$files		=	$this->getData("files");
				$details	=	$this->getData("request");
				if($files['file_name1']['name']!="")
					{
						$filename	=	$files['file_name1']['name'];
						$filetype	=	"video";
						$file_date	=	"file_name1";
					}
				else if($files['file_name2']['name']!="")
					{
						$filename	=	$files['file_name2']['name'];
						$filetype	=	"Audio";
						$file_date	=	"file_name2";
					}
				else if($_REQUEST['file_name3']!="")
					{
						$filename	=	$_REQUEST['file_name3'];
						$filetype	=	"Youtube Url";
						$file_date	=	"file_name3";
					}
				
				if($files[$file_date]['name'])
					{
						$upObj		=	$this->create_upload();
						//20,"jpg,png,jpeg,gif,video/x-flv,application/force-download"
						$adimg		=	$upObj->copy($file_date,"../images/entertainment",2);
						//print_r($this->setPageError($upObj->get_status()));exit;
						//if($adimg)		$upObj->img_resize("180","270","../images/instument_master/thumb");
						//else 			$this->setPageError($upObj->get_status());
						$this->addData(array("file_name"=>$adimg),"request");
					}
				$date		=	 date("Y-m-d");
				$memberObj	=	new entertainment;
				$data		=	$this->getData("request");
				$data['']	=	$data;
				$data['file_name']		=	$filename;
				$data['type']			=	$filetype;
				$data['created_date']	=	$date;
				//print_r($data);exit;
				$dataIns	=	$this->populateDbArray("tblentertainment",$data);	
				
				$updateStatus=	$this->db_update("tblentertainment",$dataIns,"id ='".$details['id']."'",1);
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
		public function entertainmentSavedata()
			{ 	
				//echo "<pre>";print_r($_FILES);exit;
				$data		=	$this->getData("files");
				$data1		=	$this->getData("request");				
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$utypeId	=	$userSess["usertype"];
				$uid	 	=	$userSess["id"];
				$files		=	$_FILES;
				
				$data		=	$this->getData("request");
				$video		=	$data['movietitle1'];
				$audio		=	$data['movietitle2'];
				$youtube	=	$data['movietitle3'];
				
				if($files['file_name1']['name']!="")
					{						
						$filename	=	$files['file_name1']['name'];
						$filetype	=	"video";
						$file_date	=	"file_name1";
						$name		=	$data['movietitle1'];	
					}
				else if($files['file_name2']['name']!="")
					{
						$filename	=	$files['file_name2']['name'];
						$filetype	=	"Audio";
						$file_date	=	"file_name2";
						$name		=	$data['movietitle2'];	
					}
				else if($_REQUEST['file_name3']!="")
					{
						$filename	=	$_REQUEST['file_name3'];
						$filetype	=	"Youtube Url";
						$file_date	=	"file_name3";
						$name		=	$data['movietitle3'];
					}
				
/*Check the file types and Upload File into corresponding folder */

				if($files['file_name1']['type']	== "application/octet-stream" || $files['file_name1']['type']	=="video/mp4" ||$files['file_name1']['type']	== "video/x-flv"||$files['file_name1']['type']	== "video/flv")
					{ 
						if($files['file_name1']['size'] >5000000)
							{
								$this->setPageError("Maximum uploading size is 5 MB. Try uploading a small video.");
								$this->executeAction(true,"Addform",true);
							}
							$type		=	$files['file_name1']['type'];
							$upObj		=	$this->create_upload();
							$adimg		=	$upObj->copy($file_date,"../Uploads/general/Video/",2);
							$this->addData(array("file_name"=>$adimg),"request");
					}
				elseif($files['file_name2']['type']	== "application/force-download" || $files['file_name2']['type']	=="audio/mp3" || $files['file_name2']['type']	=="audio/3gp" || $files['file_name2']['type']	== "audio/x-m4a" || $files['file_name2']['type']	== "audio/x-ms-wma" ||$files['file_name2']['type']	=="audio/mpeg")
					{ 
			  			if($files['file_name2']['size'] > 5000000)
							{	
								$this->setPageError("Maximum uploading size is 5 MB. Try uploading a small audio.");
								$this->executeAction(true,"Addform",true);
							}	
							$type		=	$files['file_name2']['type'];
							$upObj		=	$this->create_upload();
							$adimg		=	$upObj->copy($file_date,"../Uploads/general/Audio/",2);
							$this->addData(array("file_name"=>$adimg),"request");
					}
				else if($file_date	!=	"file_name3")
					{			
						$this->setPageError(" This file is not supported! Please upload some other files.");
						$this->executeAction(true,"Addform",true);
					}
				
				
/* Put/save Upload information into database  */
					$date		=	 date('Y-m-d H:i:s');
					$memberObj	=	new entertainment;
					$data['']				=	$data;
					$data['file_name']		=	$adimg;			//file name
					if($_REQUEST['file_name3']!="")
					{
						$data['file_name']		=	$filename;			//file name
					}
					$data['type']			=	$filetype;		//file type
					$data['created_date']	=	$date;
					$data['movietitle']		=	$name;
					$data['created_by']		=	$_SESSION['log_id'];	
					$dataIns	=	$this->populateDbArray("tblentertainment",$data);	
				
					if(!$this->getPageError())
						{
							if($memberObj->createentertainment($dataIns))	
								{	
									$this->setPageError("Inserted Successfully");
									$this->executeAction(true,"Listing",true);
								}
							else
								{
									$this->setPageError($this->getPageError());
									$this->executeAction(true,"Addform",true);
								}
						}
			}
			
		public function entertainmentDeletedata()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new entertainment;
				$data				=	$memberObj->deleteentertainment($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
					
			}	
		
		public function createentertainment($dataIns)
			{
					$creationSucces						=	$this->db_insert("tblentertainment",$dataIns);
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
			
		public function entertainmentViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new entertainment;
				$data				=	$memberObj->getentertainment($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
			}
		
		public function entertainmentStauschange()
			{
				$details	=	$this->getData("request");
				$permission	=	new entertainment;
				$sql		=	"select * from tblentertainment
								where id=".$details['id']."";
				$data		=	$this->getdbcontents_sql($sql);
				//print_r($data);exit;
				$id			=	$data[0]['id'];
				if($data[0]['Status']==0)
					{			
						$dataUpdate	=	array();
						$dataUpdate['Status']	=	"1";
						$this->db_update("tblentertainment ",$dataUpdate,"id =$id",1);
						return $this->executeAction(false,"Listing",true);
					}
				else 
					{
						$dataUpdate	=	array();
						$dataUpdate['Status']	=	"0";
						$this->db_update("tblentertainment ",$dataUpdate,"id =$id",1);
						return $this->executeAction(false,"Listing",true);
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
				//echo $this->getMethodName();
				//exit;
				return $this->actionReturn;
			}
		public function __destruct() 
			{
				parent::childKilled($this);
			}
		
		public function getentertainment($membersId="",$args="1")
			{	
				$sql					=	"select * from tblentertainment where id='$membersId' and ".$args;
				$result					=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function deleteentertainment($id)
		{
		 	$query 		= 	"UPDATE tblentertainment SET is_deleted=1 WHERE id='$id'";	
			$result		=	$this->getdbcontents_sql($query);
			return $this->executeAction(false,"Listing",true);return result;
		}
		
	}