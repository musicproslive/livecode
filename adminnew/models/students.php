<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	07-07-2011
Purpose		:	To Manage students
******************************************************************************************/
class students extends modelclass
	{
		public function studentsListing()
			{ 				
				
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				
				/********************************************************************/
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());
				$data		=	$this->getData("get");	
				//$listGrp	=	$data['id'];
				/********************************************************************/
				$listGrp 	= 	0;
				if($data['cid']!="")
					{
						$_SESSION['listGrp']	=	$data['cid'];
					}
				else
					{
						$listGrp		=	0;
					}
					$listGrp			=	$_SESSION['listGrp'];
				/********************************************************************/
				$searchData				=	$this->getData("post","Search");
				$sortData				=	$this->getData("request","Search");
				$searchData['sortData']	=	$this->getData("request","Search");
				//print_r($searchData);exit;
				$searchCombo			=	$this->get_combo_arr("sel_search_group", $this->getAll("1"), "category_id", "category", $listGrp," onchange='javascript:this.form.submit();' ");			
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
				
				$sql						=	"select cu.* ,cc.country_name,cs.state_name,ct.category,ul.admin_authorize
												from tblusers as cu 
												left join tblcountries as cc on cu.country_id=cc.country_id 
												left join tblstates as cs on cu.state_id=cs.state_id 
												left join tbluser_category as ct on cu.user_category_id = ct.category_id
												left join tbluser_login as ul on cu.login_id=ul.login_id
												where ct.category_id=$listGrp and cu.is_deleted=0".$sqlFilter["cond"].$sqlFilter["ord"]." ".$orderBy ;
				$this->addData(array("sql"=>$sql),"post","",false);
				//$spage				 		=	$this->create_paging("n_page",$sql,GLB_PAGE_CNT);
				$spage				 		=	$this->create_paging("n_page",$sql);
				$data				 		=	$this->getdbcontents_sql($spage->finalSql());
				
				//if(!$data)						$this->setPageError("No records found !");
				$searchData					=	$this->getHtmlData($this->getData("post","Search",false));
				$searchData["searchCombo"]	=	$searchCombo;
				return array("data"=>$data,"spage"=>$spage,"searchdata"=>$searchData);
			}
		
				
		public function studentsSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function studentsCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function studentsReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		public function studentsAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}

		public function studentsFetch(){
			
			// Connect to MySQL database
			$page 			= 	0;	// The current page
			$sortname 		= 	'ul.created';	 // Sort column
			$sortorder	 	= 	'desc';	 // Sort order
			$qtype 			= 	'';	 // Search column
			$query 			= 	'';	 // Search string
			//$listGrp 	= 	2;
			//$data		=	$mod->getData("get");	
					//$listGrp	=	$data['id'];
					/********************************************************************/
					//$listGrp 	= 	0;
					if($data['cid']!="")
						{
							$_SESSION['listGrp']	=	$data['cid'];
						}
					else
						{
							$listGrp		=	0;
						}
						$listGrp			=	$_SESSION['listGrp'];
					/********************************************************************/
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
			
			//$searchSql			=	" and  user_category_id=2 ";
			//$searchSql1			=	" user_category_id=2 and cu.is_deleted=0";
			
			if(!empty($_GET['field']) && !empty($_GET['keyword']))
				{
					$searchSql		.=	" AND `".$_GET['field']."`='".$_GET['keyword']."%' ";
				}
			// Setup sort and search SQL using posted data
			$sortSql				 = 	"order by $sortname $sortorder";
			$searchSql 				.= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
			// Get total count of records
			$sql					= 	"SELECT count(cu.`user_id`) from tblusers cu
											  LEFT JOIN tbluser_login as ul on cu.login_id=ul.login_id
											  LEFT JOIN tbluser_roles as ct on ul.user_role = ct.role_id
										where ct.role_access_key='STUDENT_ROLE' AND cu.is_deleted=0
										";
			$result 				= 	$this->db_query($sql,1);
			$row 					= 	@mysql_fetch_array($result);
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
			//$searchSql				=	" user_category_id=2 and cu.is_deleted=0";
			$sql					=  "SELECT cu.user_id,cu.first_name, cu.last_name,cu.gender,cu.age_group,DATE_FORMAT(cu.dob,'".$_SESSION["DATE_FORMAT"]["M_DATE"]."')  AS dob, cc.country_name, cs.state_name, ul.authorized, ul.admin_authorize, ul.user_name, ul.login_id,  ul.is_deleted, DATE_FORMAT(ul.created,'".$_SESSION["DATE_FORMAT"]["M_DATE"]."')  AS created_on, tt.timezone_location, tt.gmt 
										from tblusers as cu 
										LEFT JOIN tblcountries as cc on cu.country_id=cc.country_id 
										LEFT JOIN tblstates as cs on cu.state_id=cs.state_id 									
										LEFT JOIN tbluser_login as ul on cu.login_id=ul.login_id
										LEFT JOIN tbluser_roles as ct on ul.user_role = ct.role_id
										LEFT JOIN tbltime_zones as tt on cu.time_zone_id=tt.id
										where ct.role_access_key='STUDENT_ROLE' AND cu.is_deleted=0 $searchSql $sortSql $limitSql ";							
		
			$results 	= 	$this->db_query($sql,0);//exit;
			//file_put_contents("file.txt",$sql);
			$i			=	$pageStart;
			while ($row = mysql_fetch_assoc($results)) 
				{
					$i++;
				if($this->permissionCheck("Status")){
					if ($row['authorized']==0)
					{
						$row['authorized']		=	"<a href=\"students.php?actionvar=Statuschange&lid=".$row['login_id']."&authorized=1\" class=\"Second_link\">
						<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to Activate\"></a>";
					}
					else
					{					
						$row['authorized']		=	"<a href=\"students.php?actionvar=Statuschange&lid=".$row['login_id']."&authorized=0\" class=\"Second_link\"><img src=\"../images/active.gif\" border=\"0\" title=\"Click here to Inactivate\"></a>";
					}
				}
				/*************Sex SELECTion***********/
					if ($row['gender']=="F")	$row['gender']='Female';
					else		$row['gender']='Male';	
			
				/************Age SELECTion***************/	
					
					if($row['age_group']==3)
						{
							$row['age_group']	=	"Adults";
						}
					else if($row['age_group']==2)
						{
							$row['age_group']	=	"Teens";	
						}
					else if($row['age_group']==1) 
						{
							$row['age_group']	=	"Kids";
						}
					else
						{
							$row['age_group']	=	"invalid";
						}
						
				/***************************************/
				if ($row['admin_authorize']==0)
					{
						$row['admin_authorize']		=	"<a href=\"students.php?actionvar=ChangeStatus&lid=".$row['login_id']."&admin_authorize=1\" class=\"Second_link\">
						<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to activate\"></a>";
					}
					else
					{					
						$row['admin_authorize']		=	"<a href=\"students.php?actionvar=ChangeStatus&lid=".$row['login_id']."&admin_authorize=0\" class=\"Second_link\">
						<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to activate\"></a>";
					}
				/*
				To activate it pls uncomment it <<<<<<
				$row['group']	="<a href=\"groupList.php?id=".$row['user_id']."\" class=\"Second_link\">
						<img src=\"../images/group_icon.jpg\" border=\"0\" title=\"Group Details\" height=\"30\" ></a>";*/
						if($this->permissionCheck("View")){
				$row['view']	="<a href=\"students.php?actionvar=Viewform&id=".$row['user_id']."\" class=\"Second_link\">
						<img src=\"../images/view.png\" border=\"0\" title=\"View Details\"></a>";
						}
						if($this->permissionCheck("Delete")){
				if ($row['is_deleted']==0)
					{
						$row['delete']	="<a href=\"students.php?actionvar=Deletedata&lid=".$row['login_id']."&is_deleted=1\" class=\"Second_link\"  onclick=\"return delall()\">
								<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to block user\"></a>";				
					}
				else
						$row['delete']	="<a href=\"students.php?actionvar=Deletedata&lid=".$row['login_id']."&is_deleted=0\" class=\"Second_link\" >
								<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to unblock user\"></a>";				
					
				/*
					To activate it pls uncomment <<<<<<<<
				$row['event']	= "<a href=\"eventsView.php?id=".$row['user_id']."\" class=\"Second_link\">
						My Events</a>";
				$row['album']	= "<a href=\"albumsView.php?mode=Stud&id=".$row['user_id']."\" class=\"Second_link\">
						My Album</a>";	*/
	
						}
						if($this->permissionCheck("Edit")){
								$row['userprofile']	= "<a href=\"userProfile.php?id=".base64_encode(serialize($row['user_id']))."\" class=\"Second_link\" target=\"_blank\"><img src=\"images/profile-icon.jpg\" border=\"0\" title=\"User Profile\" width=\"25\" height=\"25\"></a>";
								$row['videoquality']	= "<a href=\"students.php?actionvar=CameraSetting&id=".base64_encode(serialize($row['user_id']))."\" class=\"Second_link\"><img src=\"images/webcam_icon.gif\" border=\"0\" title=\"Video Quality Settings\" width=\"25\" height=\"25\"></a>";					
								$row['cc_info']			=	"<a href=\"students.php?actionvar=CreditCard&id=".base64_encode(serialize($row['user_id']))."\" class=\"Second_link\"><img src=\"images/credit_card.jpg\" border=\"0\" title=\"Credit Card Information\" width=\"30\" height=\"35\"></a>";	
								$row['edit']	=	'<a href="students.php?actionvar=EditProfileForm&uid='.base64_encode(serialize($row['user_id'])).'"><img src="images/edit.gif" title="edit Information"></a>';
						}
				$data['rows'][] = array
				(
			'id' => $row['user_id'],
			'cell' => array($i, $row['first_name'].' '.$row['last_name'],$row['user_name'], $row['country_name'],$row['state_name'], $row['age_group'], $row['dob'],$row['gender'],$row['timezone_location'], $row['created_on'],$row['authorized'],$row['admin_authorize'],$row['view'],$row['userprofile']/*,$row['videoquality']*/,$row['cc_info'],$row['delete'],$row['edit'])
				);
			}
			$r =json_encode($data);
			ob_clean();
			echo  $r;
			exit;
		
		}
		 public function studentsChangeStatus()
			{
				$details	=	$this->getData("request");
				if($details['lid'])
					{	
						$dataUpdate	=	array();
						$dataUpdate['admin_authorize']	=	$details['admin_authorize'];
						if($this->db_update("tbluser_login",$dataUpdate,"login_id =".$details['lid']))
							$this->setPageError("Status changed successfully");
						else
							$this->setPageError("Sorry, some technical problem occured");
					}
						return $this->executeAction(false,"Listing",true);
			}
		public function studentsCreditCard()
			{
				$data			=	$this->getData("get"); 
				$id				=	unserialize(base64_decode($data['id']));
				$ccObj			=	new userCourse ;
				$userObj		=	new userManagement;
				$credit_card    =   $ccObj->getUsersCCdetails($id);
				$user			=	$userObj->getUserName($id);
				$user['id']=$id;
				return 	array("credit_card"=>$credit_card,"user"=>$user);
			}
		public function studentsUpdateUserCC()
			{
				$data = $this->getData('post');
				$data['rdccs'] = reset($data['rdccs']);
				
				$this->db_update('tblusers_ccs', array('is_active' => 0), "user_id = ".$data['id']);
				$this->db_update('tblusers_ccs', array('is_active' => 1), "id = ".$data['rdccs']);
				$this->clearData();
				$this->addData(array("id"=>base64_encode(serialize($data['id']))),"get");
				$this->redirectAction(true,"Credit card has been changed successfully","CreditCard");
				//$this->print_r($data);exit;
			}
		public function studentsDeleteUserCC()
			{
				$data = $this->getData('post');
				//print_r($data); exit;
				$orbObj = new orbital();
				$csObj = new userCourse();
				if($orbObj->deletePaymentAccount($csObj->getOrbitalProfileCode($data['id']), $data['id']))
					{
						$this->db_update('tblusers_ccs', array('is_deleted' => 1), "id={$data['cc_id']}");
						$this->clearData();
						$this->addData(array("id"=>base64_encode(serialize($data['id']))),"get");
						$this->redirectAction(true,"Credit card has been removed successfully", "CreditCard");
					}
				else
					{
						$this->clearData();
						$this->addData(array("id"=>base64_encode(serialize($data['id']))),"post");
						$this->redirectAction(true,"Sorry... Unable to delete your CC", "CreditCard");
					}	
				
			}
		/* this code is for video quality setting interface
		 public function studentsCameraSetting()
			{
				$id				=	unserialize(base64_decode($_GET['id']));
				$clsU			=	new userManagement();
				$data			=	$clsU->getUserVideoSettings($id);
				$user			=	$userObj->getUserName($id);
				  $new			=	'0';
				if(empty($data))
					{	
						//$data		=	$clsU->getNewUserVideoSettings($id);
						$data['user_id']		=	$id;	
						$data['band_width']	=	100;
						$data['quality']	=	100;
						$data['width']	=	100;
						$data['height']	=	100;
						$data['frame_rate']	=	30;	
						$data['priority']=0;				
								$new		=	'1';	
					}
					
						$login			=	$clsU->getLoginInfo($id);
						return			array("data"=>$data,"login"=>$login,"new"=>$new,"username"=>$user['name']);
		
			}
			*/
		public function studentsSave()
			{
				
				$id			=	$_POST['id'];
				$data['user_id']		=	$id;
				$data['created_on']		=	 date("Y-m-d H:i:s");
				//$data['is_deleted']		=	'0';
				$data['priority']		=	empty($_POST['priority'])?0:1;	
				$data['band_width']	=	$_POST['bandwidth'];
				$data['quality']	=	$_POST['quality'];
				$data['width']	=	$_POST['capturewidth'];
				$data['height']	=	$_POST['captureheight'];
				$data['frame_rate']	=	$_POST['framerate'];
				$data=$this->populateDbArray("tblusers_video_settings",$data);
						$result		=	$this->db_update("tblusers_video_settings",$data,"user_id=".$id,1);
				
				if($result)
					{	
							$this->setPageError("Successfully updated");
							$this->executeAction(true,"Listing","students.php");		
					}
				else
							$this->redirectAction("Sorry some errors occured.Please try again","Listing","students.php");							
		
		}
	
	public function studentsSubmit()
		{
			$id			=	$_POST['id'];
			$data['user_id']		=	$id;
			$data['created_on']		=	 date("Y-m-d H:i:s");
			//$data['is_deleted']		=	'0';	
			$data['band_width']		=	$_POST['bandwidth'];
			$data['priority']		=	empty($_POST['priority'])?0:1;
			$data['quality']		=	$_POST['quality'];
			$data['width']			=	$_POST['capturewidth'];
			$data['height']			=	$_POST['captureheight'];
			$data['frame_rate']		=	$_POST['framerate'];
				$data=$this->populateDbArray("tblusers_video_settings",$data);			
				$result	=	$this->db_insert("tblusers_video_settings",$data,0);
			if($result)
			{
						$this->redirectAction("You have successfully submitted the details","Listing","students.php");							
			}
		}	
		public function studentsAddform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new students;
				$data				=	$memberObj->getstudentsDetails($data['id']);
/*				$terrObj			= 	new territory(); 
				$country_combo		=	$this->get_combo_arr("sel_country",$terrObj->getAllCountries("status='1' order by preference"),"id","country",$data["sel_country"],"'valtype='emptyCheck-please select a country' onchange=\"getcombo(this.value,'stateDivId');\"");
				$stateArry			=	$terrObj->getAllStates("sel_country=".$data['sel_country']." and status='1' order by preference");
				$state_combo		=	$this->get_combo_arr("sel_state",$terrObj->getAllStates(" country_id=".$data['sel_country']." and status='1' order by preference"),"id","state",$data["sel_state"],"'valtype='emptyCheck-please select a country' onchange=\"getcities(this.value,'cityDivId');\"");
				$city_combo			=	$this->get_combo_arr("sel_city",$terrObj->getAllcities(" state_id=".$data['sel_state']." and status='1' order by preference"),"id","city",$data["sel_city"],"'valtype='emptyCheck-please select a city'");
				$combo				=	array();
				$combo['country']	=	$country_combo;
				$combo['state']		=	$state_combo;
				$combo['city']		=	$city_combo;
*/				
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
			}
		
		public function studentsEditform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new students;
				$data				=	$memberObj->getstudentsDetails($data['id']);
				$terrObj			= 	new territory(); 
				$country_combo		=	$this->get_combo_arr("country",$terrObj->getAllCountries(),"country_id","country_name",$data["country_name"],"'valtype='emptyCheck-please select a country' onchange=\"getcombo(this.value,'stateDivId');\"");
				$stateArry			=	$terrObj->getAllStates("country_id=".$data['country_id']."");
				$state_combo		=	$this->get_combo_arr("state",$terrObj->getAllStates(" country_id=".$data['country_name']." and status='1' order by preference"),"state_id","state_name",$data["state_name"],"'valtype='emptyCheck-please select a country' onchange=\"getcities(this.value,'cityDivId');\"");
				$city_combo			=	$this->get_combo_arr("city",$terrObj->getAllcities(" state_id=".$data['state_id'].""),"city_id","city_name",$data["city_name"],"'valtype='emptyCheck-please select a city'");
				//print_r($stateArry);exit;
				$combo				=	array();
				$combo['country']	=	$country_combo;
				$combo['state']		=	$state_combo;
				$combo['city']		=	$city_combo;
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
			}
			
		public function studentsUpdatedata()
			{
				$files		=	$this->getData("files");
				$details	=	$this->getData("request");
				$details	=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tblusers ",$details);
				$updateStatus=	$this->db_update("tblusers ",$dataIns,"user_id='".$details['id']."'",1);
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
		public function studentsSavedata()
			{
				$data		=	$this->getData("files");
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$utypeId	=	$userSess["usertype"];
				$uid	 	=	$userSess["id"];
				$files=$_FILES;
				$memberObj	=	new students;
				$data		=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tblusers",$data);	
				
				if(!$this->getPageError())
					{
						if($memberObj->createMember($dataIns))	
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
		
		public function getAllCountries()
			{
				$query 		= 	"select * from tblcountries";
				$result		=	$this->getdbcontents_sql($query);
				return $this->executeAction(false,"Listing",true);
			}

		public function studentsStatuschange()
			{
				$details	=	$this->getData("request");
				if($details['lid'])
					{	
						$dataUpdate	=	array();
						$dataUpdate['authorized']	=	$details['authorized'];
						if($this->db_update("tbluser_login",$dataUpdate,"login_id =".$details['lid']))
							$this->setPageError("Status changed successfully");
						else
							$this->setPageError("Sorry, some technical problem occured");
					}
						return $this->executeAction(false,"Listing",true);
			}


		public function updatestatus($dataIns)
			{
				$creationSucces		=	$this->db_update("tbluser_login ",$dataIns,"login_id='".$data['login_id']."'",1);
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
					
		public function studentsDeletedata()
			{
				$data				=	$this->getData("get");
				$details	=	$this->getData("request");
				if($details['lid'])
					{	
						$dataUpdate	=	array();
						$dataUpdate['is_deleted']	=	$details['is_deleted'];
						if($this->db_update("tbluser_login",$dataUpdate,"login_id =".$details['lid']))
							$this->setPageError("Status changed successfully");
						else
							$this->setPageError("Sorry, some technical problem occured");
					}
						return $this->executeAction(false,"Listing",true);
			}	
			
		public function studentsViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new students;
				$data				=	$memberObj->getstudentsDetails($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
			}
		public function studentsCreateProfileForm()
			{
				if(!isset($data['country_id']) )	// Select Default United states
						$data['country_id'] = '223';
				$countryID = $data['country_id'];
					$stateID = $data['state_id'];
					$timeZoneID = $data['time_zone_id'];
					
					$terObj			=	new territory();
					//Country drop down
					$countryData	=	$terObj->getAllCountries("1 order by country_name asc");	
					$countryData	=	$this->get_combo_arr("country_id",$countryData,"country_id","country_name","$countryID"," onchange='getStates(this.value,\"id_search_state\")' class='validate[required]' id='country_id'","Select Country");
					
					//State drop down				
					$stateData	=	$terObj->getAllStates("country_id = ".$data['country_id']." order by state_name asc");
					$stateData	=	$this->get_combo_arr("state_id",$stateData,"state_id","state_name","$stateID"," id='state_id' style = 'width:182px;' ","Select State");
													
					$timeZone = new timeZone();
					$timeZoneList = $timeZone->getAllTimeZone();
					$timeZoneList = $this->get_combo_arr("time_zone_id",$timeZoneList,"id","timezone","$timeZoneID","class='validate[required]' id='timeZoneId' style = 'width:260px;' ","Select TimeZone");
									
					$instrument = new instrument();
					$instuments = $instrument -> getAllInstruments();				
					
					$days = $this->get_combo_int('sel_day',1,31,$data['sel_day'],"class='' onchange='validateDobDay();' id='sel_day'",'Day');
					$months = $this->get_combo_months('sel_month',$data['sel_month'],"class='validate[required]' onchange='validateDobMonth();' id='sel_month'",'Month');
	
					$years = $this->get_combo_year('sel_year',$data['sel_year'],"class='validate[required]' onchange='validateDobYear();' id='sel_year'",'Year',90,2);
	
					
					$searchData["country_combo"]	=	$countryData; 
					$searchData["state_combo"]		=	$stateData; 
					$searchData["timeZoneList"]	    =	$timeZoneList; 
					$searchData["instruments"]	    =	$instuments;
					
					$searchData["days"]	    =	$days;
					$searchData["months"]	=	$months;
					$searchData["years"]	=	$years;
					$searchData["age_group"]	= 0;
					$searchData["userType"]	    = 2; //For Instructor
					
					$searchData["lmtError"]	    =	isset($_SESSION['resMessage']) && $_SESSION['resMessage'] == 2 ? 1 : 0;
					
					unset($_SESSION['resMessage']);
					
					$searchData['curYear'] = date('Y');
					
					return array("searchdata"=>$searchData, "data"=>$this->getHtmlData($data));		
			}
		public function studentsGetstatecombo()
			{
				ob_clean();
				$data				=	$_REQUEST; //$this->getData("request", "", true);				
				$terObj				=	new territory();
				$stateData			=	$terObj->getAllStates("country_id=".$data["cid"]);
				echo $stateData		=	$this->get_combo_arr("state_id",$stateData,"state_id","state_name",$searchData["sel_state"],"id='state_id' style = 'width:182px;' ","Select State");
				exit;

			}
			
		public function studentsGetcitycombo()
			{
				ob_clean();
				$data				=	$this->getData("request", "", true);
				$terObj				=	new territory();
				$cityData			=	$terObj->getAllCities("state_id=".$data["cid"]);
				echo $cityData		=	$this->get_combo_arr("city_id",$cityData,"city_id","city_name",$searchData["sel_city"],"","Select City");
				exit;
			}
		public function studentsCreateProfile()
			{
				$data		=	$this->getData("request", "", true);
				
				$valObj		=	new dataValidation();
				$errors = array ();
				if (!$valObj->validateFirstName($data['first_name'])) {
					$errors [] = "Invalid first name.";
				}
				if (!$valObj->validateLastName($data['last_name'])) {
					$errors [] = "Invalid last name.";
				}
				if (!$valObj->validateEmail($data['user_name'])) {
					$errors [] = "Invalid email address.";
				}
				if ($data ['user_name'] != $data ['confirm_email']) {
					$errors [] = "Confirmation Email does not match.";
				}
				if (!$valObj->validatePassword($data['user_pwd'])) {
					$errors [] = "Invalid password.";
				}
				if ($data ['user_pwd'] != $data ['confirmPassword']) {
					$errors [] = "Confirmation password does not match.";
				}
				if (empty ($errors)) {
						$loginData = $this->populateDbArray("tbluser_login",$data);
						
						$loginData['user_pwd'] = md5($loginData['user_pwd']);
						
						$userData	=	$this->populateDbArray("tblusers",$data);
						$userData['dob'] = $data['sel_year'].'-'.$data['sel_month'].'-'.$data['sel_day'];
						$userData['profile_image'] = 'profile-no-img.png';
					
					//set age group related property 						
						if($data['age_group'] == 1)
							$loginData['admin_authorize'] = 0;
						else
							$loginData['admin_authorize'] = 1; 
							
						$loginData['privacy_policy']  = 1; 
						$loginData['user_role']       = LMT_STUD_ROLE_ID;
																	
						$userObj 	= 	new userManagement();
						$loginID	=	$userObj->insertUserDetails($loginData, $userData, $data['instruments']);
						if($loginID)
							{					
								$subject 						=  'Live Music Tutor Registration';	
								$varArr["{TPL_NAME}"]			=	$data['first_name']." ".$data['last_name'];
								$varArr["{TPL_ACTION_URL}"]		=	ROOT_URL.'userAuth.php?id='.base64_encode($loginID);
								$varArr["{TPL_URL}"]			=	ROOT_URL;						
								$cms  = new cms();
								if($data['age_group'] == 1)
									$send =	$cms->sendMailCMS(LMT_CHILD_REGISTRATION,$data['user_name'],LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5);
								else
									$send =	$cms->sendMailCMS(LMT_REGISTRATION_MAIL,$data['user_name'],LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5); 																
																							
								$this->setPageError("Profile Created Successfully !");
								return $this->executeAction(false,"Listing",true,false,false,false,"");
								$this->popPageError();
							}
						else
							{	
								$this->setPageError($userObj->getPageError());			
								return $this->executeAction(true,"CreateProfileForm",true,true,false,false,"");
							}
					}
				else
					{	

						$page_error = "";
						$first = true;
						foreach ($errors as $err_msg) {
							if ($first) {
								$first = false;
							} else {
								$page_error .= "<br />";
							}
							$page_error .= $err_msg;
						}
						$this->setPageError($page_error);
						return $this->executeAction(true,"CreateProfileForm",true,true,false,false,"");
					}
					
			}
		public function studentsBackToListing()
			{
				$this->clearData("");
				$this->executeAction(false,"Listing",true,false,false,false,"");
			}
		public function studentsEditProfileForm()
			{
					$data	=	$this->getData("get");
					$user_id	=	unserialize(base64_decode($data['uid']));
					$userMgmt	=	new userManagement();
					$userData	=	$userMgmt->getUserDetail($user_id);

					$countryID = $userData['country_id'];
					$stateID = $userData['state_id'];
					$timeZoneID = $userData['time_zone_id'];
					
					$terObj			=	new territory();
					//Country drop down
					$countryData	=	$terObj->getAllCountries("1 order by country_name asc");	
					$countryData	=	$this->get_combo_arr("country_id",$countryData,"country_id","country_name",$countryID," onchange='getStates(this.value,\"id_search_state\")' class='validate[required]' id='country_id'","Select Country");
					
					//State drop down				
					$stateData	=	$terObj->getAllStates("country_id = ".$countryID);
					$stateData	=	$this->get_combo_arr("state_id",$stateData,"state_id","state_name",$stateID," id='state_id' style = 'width:182px;' ","Select State");
					
					//Time Zone drop down 							
					$timeZone = new timeZone();
					$timeZoneList = $timeZone->getAllTimeZone();
					$timeZoneList = $this->get_combo_arr("time_zone_id",$timeZoneList,"id","timezone",$timeZoneID,"class='validate[required]' id='timeZoneId' style = 'width:260px;' ","Select TimeZone");
									
					$instrument = new instrument();
					$instuments = $instrument -> getAllInstruments();				
					$userInstruments	=	$userData['instruments'];
					$dob	=	explode("-",$userData['dob']);
					$dobDays	=	$dob[2];$dobMonth	=	$dob[1];$dobYear	=	$dob[0];
					$days = $this->get_combo_int('sel_day',1,31,$dobDays,"class='' onchange='validateDobDay();' id='sel_day'",'Day');
					$months = $this->get_combo_months('sel_month',$dobMonth,"class='validate[required]' onchange='validateDobMonth();' id='sel_month'",'Month');
					$years = $this->get_combo_year('sel_year',$dobYear,"class='validate[required]' onchange='validateDobYear();' id='sel_year'",'Year',90,2);
					$searchData["age_group"]	=	$userData['age_group'];
					$searchData["first_name"]	=	$userData['first_name'];
					$searchData["last_name"]	=	$userData['last_name'];
					$searchData["username"]		=	$userData['username'];
					$searchData["country_combo"]	=	$countryData; 
					$searchData["state_combo"]		=	$stateData; 
					$searchData["timeZoneList"]	    =	$timeZoneList; 
					$searchData["instruments"]	    =	$instuments;
					
					$searchData["days"]	    =	$days;
					$searchData["months"]	=	$months;
					$searchData["years"]	=	$years;
					$searchData['gender']	=	$userData['gender'];
					$searchData["userType"]	    = 2; //For Student
					
					$searchData["lmtError"]	    =	isset($_SESSION['resMessage']) && $_SESSION['resMessage'] == 2 ? 1 : 0;
					
					unset($_SESSION['resMessage']);
					
					$searchData['curYear'] = date('Y');
					
					return array("searchdata"=>$searchData,"userInstruments"=>$userInstruments);		
			}
		public function studentsUpdateProfile()
			{
				
				$data		=	$this->getData("request", "", true);
				$data['user_id']	=	unserialize(base64_decode($data['uid']));
				$valObj		=	new dataValidation();
				$errors = array ();
				if (!$valObj->validateFirstName($data['first_name'])) {
					$errors [] = "Invalid first name. '" . $data['first_name'] . "'";
				}
				if (!$valObj->validateLastName($data['last_name'])) {
					$errors [] = "Invalid last name. '" . $data['last_name'] . "'";
				}
				if (empty ($errors))
					{
						$userData	=	$this->populateDbArray("tblusers",$data);
						$instruments	=	$data['instruments'];
						$userData['dob'] = $data['sel_year'].'-'.$data['sel_month'].'-'.$data['sel_day'];
																	
						$flag	=	$this->db_update("tblusers",$userData,$this->dbSearchCond("=","user_id",$data['user_id']));
						if($flag)
							{	
								$instrumentList = array();
								if(!empty($instruments))
									{
					if($this->db_update('tbluser_instruments',array("is_deleted"=>1),$this->dbSearchCond("=","user_id",$data['user_id']),0))
										{
											foreach($instruments as $instrument)
												{
														$instrumentList ['user_id'] = $data['user_id'];										
														$instrumentList ['instrument_id'] = $instrument;
														$instrumentList ['created'] = "escape now() escape";												
														$instrumentID = $this->db_insert('tbluser_instruments',$instrumentList);
												}
										}
									}				 																
								$this->clearData('UpdateProfile');
								$this->setPageError("Profile Updated Successfully !");
								return $this->executeAction(false,"Listing",true,false,false,false,"");
							}
						else
							{	
								$this->setPageError($userObj->getPageError());			
								return $this->executeAction(true,"EditProfileForm",true,true,false,false,"");
							}
					}
				else
					{	
						$page_error = "";
						$first = true;
						foreach ($errors as $err_msg) {
							if ($first) {
								$first = false;
							} else {
								$page_error .= "<br />";
							}
							$page_error .= $err_msg;
						}
						$this->setPageError($page_error);
						return $this->executeAction(true,"EditProfileForm",true,true,false,false,"");
					}
					
			}
		public function studentsResetPwd()
			{
				$data		=	$this->getData("post");
				$valObj		=	new dataValidation();
				$userMgmt	=	new userManagement();
				$loginArr	=	end($userMgmt->getLoginInfo(unserialize(base64_decode($data['uid']))));
				$loginId	=	$loginArr['login_id'];
				if($valObj->validatePassword($data['userPwd'],5,18))
					{
					$data['userPwd'] = md5($data['userPwd']);
					if($this->db_update('tbluser_login',array("user_pwd"=>$data['userPwd']),$this->dbSearchCond("=","login_id",$loginId),0))
							{
								$cms	=	new cms();
								$sql	=	"SELECT CONCAT(U.first_name,' ',U.last_name) AS name, UL.user_name FROM tbluser_login AS UL
									 LEFT JOIN tblusers AS U ON U.login_id = UL.login_id  where UL.login_id=".$loginId;
								$result		=	end($this->getdbcontents_sql($sql));
								$subject 	= 	'Live Music Tutor Update Password';
								$varArr["{TPL_NAME}"]			=	$result['name'];
								$varArr["{TPL_URL}"]			=	ROOT_URL;
								$success =	$cms->sendMailCMS(LMT_MAIL_UPDATE_PASSWORD,$result['user_name'],LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5);
								$this->addData(array("uid"=>$data['uid']),"get");
								$this->setPageError("Password has been Changed !");
								return $this->executeAction(true,"EditProfileForm",false,true);
							}
						else
							{	
								$this->setPageError($userObj->getPageError());
								return $this->executeAction(true,"EditProfileForm",false,true);
							}
					}
				else
					{	
						$this->setPageError("Your Input is not valid please enter valid input !");			
						return $this->executeAction(true,"EditProfileForm",false,true);
					}	
			}		
		public function createMember($dataIns)
			{
					$creationSucces						=	$this->db_insert("tblusers",$dataIns);
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
		
		public function redirectAction($loadData=true,$errMessage,$action,$url="")	
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
		
		public function getstudentsDetails($membersId="",$args="1")
			{  
				$sql	=	"select cu.*,DATE_FORMAT(cu.dob,'%b %d %Y ') AS dob ,cc.country_name,cs.state_name from tblusers as cu 
							left join tblcountries as cc on cu.country_id=cc.country_id 
							left join tblstates as cs on cu.state_id=cs.state_id  
							where user_id='$membersId' and ".$args;
				$result	=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function getAll($stat="")
			{
				$data				=	$this->getdbcontents_cond("tbluser_category");
				return $data; 
			}
	}