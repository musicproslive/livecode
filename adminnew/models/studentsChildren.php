 <?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	07-07-2011
Purpose		:	To Manage Tutors
******************************************************************************************/
class studentsChildren extends modelclass
	{
		public function studentsChildrenListing()
			{ 
				/********************************************************************/
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$data		=	$this->getData("get");
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
				
				$sql						=	"select cu.* ,cc.country_name,cs.state_name,ct.category,ul.admin_authorize,lo.`level_name`
												from tblusers as cu 
												left join tblcountries as cc on cu.country_id=cc.country_id 
												left join tblstates as cs on cu.state_id=cs.state_id 
												left join tbluser_category as ct on cu.user_category_id = ct.category_id
												left join tbluser_login as ul on cu.login_id=ul.login_id
												left Join tbllookup_tutor_level as lo on cu.`expert_level`=lo.`id` 
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
		
		public function studentsChildrenFetch()
		{		
				// Connect to MySQL database
				$page 			= 	0;	// The current page
				$sortname 		= 	'ul.created';	 // Sort column
				$sortorder	 	= 	'desc';	 // Sort order
				$qtype 			= 	'';	 // Search column
				$query 			= 	'';	 // Search string
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
						$page 		= 	mysql_real_escape_string($_POST['page']);
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
				if (isset($_POST['query'])) 
					{
						$query		=	str_replace(' ','',($_POST['query']));
						$query 		= 	trim(mysql_real_escape_string($query));
					}
				if (isset($_POST['rp'])) 
					{
						$rp 		= 	mysql_real_escape_string($_POST['rp']);
					}
				if(empty($rp))
					{
						$rp			=	LMT_SITE_ADMIN_PAGE_LIMIT;
					}
			//	$searchSql			=	" and  user_category_id=1 ";
				if(!empty($_GET['field']) && !empty($_GET['keyword']))
					{
						$searchSql		.=	" AND `".$_GET['field']."`='".$_GET['keyword']."%' ";
					}
				// Setup sort and search SQL using posted data
				$sortSql				 = 	"order by  $sortname $sortorder ,cu.is_deleted ASC";
				$searchSql 				.= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				// Get total count of records
				$sql					= 	"SELECT count(cu.`user_id`) from tblusers  cu 
												LEFT JOIN tbluser_login as ul on cu.login_id=ul.login_id
												LEFT JOIN tbluser_roles as ct on ul.user_role = ct.role_id
												 where  ct.role_access_key='TUTOR_ROLE'  $searchSql";
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
				$data['total'] 			= 	$total;
				$data['rows'] 			= 	array();
				/*$sql 					= 	"SELECT `id`,`title`,`description` from tblcms  
											$searchSql	$sortSql $limitSql";*/
				$sql						=  "SELECT cu.user_id,cu.first_name,cu.social_media, cu.last_name, cu.gender,cc.country_name,cs.state_name,ul.admin_authorize,cu.age_group,
												ul.login_id,ul.authorized,ul.user_name,lo.`level_name`,ul.is_deleted, DATE_FORMAT(ul.created,'".$_SESSION["DATE_FORMAT"]["M_DATE"]."')  AS created_on
												from tblusers as cu 
												LEFT JOIN tblcountries as cc on cu.country_id=cc.country_id 
												LEFT JOIN tblstates as cs on cu.state_id=cs.state_id 
												LEFT JOIN tbluser_login as ul on cu.login_id=ul.login_id
												LEFT JOIN tbluser_roles as ct on ul.user_role = ct.role_id
												Left Join tbllookup_instructor_level as lo on cu.`instructor_level`=lo.`id` 
												where cu.age_group= 1 $searchSql $sortSql $limitSql";							
				//file_put_contents("file.txt",$sql);
				
				$results 	= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						if ($row['gender']=="F")	$row['gender']='Female';
							else	$row['gender']='Male';	
						if ($row['admin_authorize']==0)
						{
							$row['admin_authorize']		=	"<a href=\"studentsChildren.php?actionvar=Statuschange&lid=".$row['login_id']."&admin_authorize=1\" class=\"Second_link\">
							<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to activate\"></a>";
						}
						else
						{					
							$row['admin_authorize']		=	"<a href=\"studentsChildren.php?actionvar=Statuschange&lid=".$row['login_id']."&admin_authorize=0\" class=\"Second_link\">
							<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to activate\"></a>";
						}
						if ($row['authorized']==0)
						{
							$row['authorized']		=	"<a href=\"studentsChildren.php?actionvar=MailAuthorize&lid=".$row['login_id']."&authorized=1\" class=\"Second_link\">
							<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to mail authentication\"></a>";
						}
						else
							$row['authorized']		=	"<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to activate\">";
					/*$row['group']	="<a href=\"groupList.php?id=".$row['user_id']."\" class=\"Second_link\">
							<img src=\"../images/group_icon.jpg\" border=\"0\" title=\"Group Details\" width=\"30\" height=\"30\"></a>";
						
					$row['event']	=	"<a href=\"eventView.php?id=".$row['user_id']."\" class=\"Second_link\">
											My Event</a>";
					$row['album']	=	"<a href=\"albumView.php?id=".$row['user_id']."\" class=\"Second_link\">
											My Album</a>";*/
					$row['view']	="<a href=\"studentsChildren.php?actionvar=Viewform&id=".$row['user_id']."\" class=\"Second_link\">
							<img src=\"../images/view.png\" border=\"0\" title=\"View Details\"></a>";
							
							$row['userprofile']	= "<a href=\"userProfile.php?id=".base64_encode(serialize($row['user_id']))."\" class=\"Second_link\" target=\"_blank\"><img src=\"images/profile-icon.jpg\" border=\"0\" title=\"User Profile\" width=\"25\" height=\"25\"></a>";
							$row['videoquality']	= "<a href=\"studentsChildren.php?actionvar=CameraSetting&id=".base64_encode(serialize($row['user_id']))."\" class=\"Second_link\"><img src=\"images/webcam_icon.gif\" border=\"0\" title=\"Video Quality Settings\" width=\"25\" height=\"25\"></a>";		
					if ($row['is_deleted']==0)
						{
							$row['delete']	="<a href=\"studentsChildren.php?actionvar=Deletedata&lid=".$row['login_id']."&is_deleted=1\" class=\"Second_link\"  onclick=\"return delall()\">
									<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to block user\"></a>";				
						}
					else
							$row['delete']	="<a href=\"studentsChildren.php?actionvar=Deletedata&lid=".$row['login_id']."&is_deleted=0\" class=\"Second_link\" >
									<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to unblock user\"></a>";
										
					if ($row['social_media'] == 0)
				{
					$row['social_media']	="<a href=\"studentsChildren.php?actionvar=Enablesocial&lid=".$row['login_id']."&social_media=0\" class=\"Second_link\"  onclick=\"return social()\">
							<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to enable\"></a>";				
				}
			else{
					$row['social_media']	="<a href=\"studentsChildren.php?actionvar=Disablesocial&lid=".$row['login_id']."&social_media=1\" class=\"Second_link\" >
							<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to disable\"></a>";
				}
						
					$row['edit']	=	'<a href="studentsChildren.php?actionvar=EditProfileForm&uid='.base64_encode(serialize($row['user_id'])).'"><img src="images/edit.gif" title="edit Information"></a>';
				/*	if(empty($row['level_name'])){
						$row['level_name']		=	"- Not Set -";			
					}*/
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
						
						$data['rows'][] = array
					(
				'id' => $row['user_id'],
				'cell' => array($i, $row['first_name'].' '.$row['last_name'],$row['user_name'], /* $row['level_name']."  <a href='tutor.php?id=".$row['user_id']."&actionvar=Editform'><img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>", */  $row['age_group'], $row['country_name'],$row['state_name'], $row['gender'], $row['created_on'], $row['authorized'],$row['admin_authorize'],$row['view'],$row['userprofile']/* ,$row['videoquality'] */ ,$row['delete'],$row['edit'],$row['social_media'])
					);
				}
		
					//print_r($data);
					$r =json_encode($data);
					
					ob_clean();
					echo  $r;
					exit;	
			
			}
			
			public function studentsChildrenEnablesocial()
			{		
				$data				=	$this->getData("get");
				$details	=	$this->getData("request");
				$alb	= 	new albumlist();
				$allData = $alb -> activeSocialmedia($details['lid']);
				
				$this->redirectAction($err,"Listing","studentsChildren.php");
			}	
			
		public function studentsChildrenDisablesocial()
			{		
				$data				=	$this->getData("get");
				$details	=	$this->getData("request");
				$alb	= 	new albumlist();
				$allData = $alb -> inactiveSocialmedia($details['lid']);
			
				$this->redirectAction($err,"Listing","studentsChildren.php");
			}	
			
		public function studentsChildrenCameraSetting()
			{
				$id				=	unserialize(base64_decode($_GET['id']));
				$clsU			=	new userManagement();
				$data			=	$clsU->getUserVideoSettings($id);
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
						return			array("data"=>$data,"login"=>$login,"new"=>$new);
		
			}
		public function studentsChildrenSave()
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
				//print_r($data);exit;
						$result		=	$this->db_update("tblusers_video_settings",$data,"user_id=".$id,1);
				
				if($result)
					{	
							$this->setPageError("Successfully updated");
							$this->executeAction(true,"Listing","studentsChildren.php");		
					}
				else
							$this->redirectAction("Sorry some errors occured.Please try again","Listing","studentsChildren.php");							
		
		}
	
	public function studentsChildrenSubmit()
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
						$this->redirectAction("You have successfully submitted the details","Listing","studentsChildren.php");							
			}
		}			
		public function studentsChildrenSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function studentsChildrenCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function studentsChildrenReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		public function studentsChildrenAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}
		public function getAllLevels(){
			$query		=	"SELECT * FROM `tbllookup_instructor_level` ";//WHERE `status`"
			$result		=	$this->getdbcontents_sql($query);
			return $result;
		}	
		
		/*public function tutorAddform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new tutor;
				$data				=	$memberObj->gettutorDetails($data['id']);
				$terrObj			= 	new territory(); 
				$country_combo		=	$this->get_combo_arr("sel_country",$terrObj->getAllCountries("status='1' order by preference"),"id","country",$data["sel_country"],"'valtype='emptyCheck-please select a country' onchange=\"getcombo(this.value,'stateDivId');\"");
				$stateArry			=	$terrObj->getAllStates("sel_country=".$data['sel_country']." and status='1' order by preference");
				$state_combo		=	$this->get_combo_arr("sel_state",$terrObj->getAllStates(" country_id=".$data['sel_country']." and status='1' order by preference"),"id","state",$data["sel_state"],"'valtype='emptyCheck-please select a country' onchange=\"getcities(this.value,'cityDivId');\"");
				$city_combo			=	$this->get_combo_arr("sel_city",$terrObj->getAllcities(" state_id=".$data['sel_state']." and status='1' order by preference"),"id","city",$data["sel_city"],"'valtype='emptyCheck-please select a city'");
				$combo				=	array();
				
				$el					=	$this->getAllLevels();
				
				$combo['country']	=	$country_combo;
				$combo['state']		=	$state_combo;
				$combo['city']		=	$city_combo;
				
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid,"expert_level"=>$el);
			}*/
		
		public function studentsChildrenEditform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new studentsChildren;
				$data				=	$memberObj->gettutorDetails($data['id']);
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
				$el					=	$this->getAllLevels();
				
				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid,"expert_level"=>$el);
			}
			
		public function studentsChildrenUpdatedata()
			{
				$files		=	$this->getData("files");
				$details	=	$this->getData("request");
				$details	=	$this->getData("request");
				$dataIns	=	$this->populateDbArray("tblusers ",$details);
				$updateStatus=	$this->db_update("tblusers",$dataIns,"user_id='".$details['id']."'",1);
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
		public function studentsChildrenSavedata()
			{
				$data		=	$this->getData("files");
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				$utypeId	=	$userSess["usertype"];
				$uid	 	=	$userSess["id"];
				$files=$_FILES;
				$memberObj	=	new studentsChildren;
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
		
		public function studentsChildrenStatuschange()
			{
				//echo "Ok"; exit;
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
			}//public function executeAction($loadData=true,$action="",$navigate=false,$sameParams=false,$newParams="",$excludParams="",$page="")
		
		public function studentsChildrenMailAuthorize()
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
					
		public function studentsChildrenDeletedata()
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
			
		public function studentsChildrenViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new studentsChildren;
				$data				=	$memberObj->gettutorDetails($data['id']);
				
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
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
		public function studentsChildrenCreateProfileForm()
			{
				$data		=	$this->getData("request");
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
					$searchData["age_group"]	= 1;
					$searchData["userType"]	    = 1; //For Instructor
					
					$searchData["lmtError"]	    =	isset($_SESSION['resMessage']) && $_SESSION['resMessage'] == 2 ? 1 : 0;
					
					unset($_SESSION['resMessage']);
					
					$searchData['curYear'] = date('Y');
					
					return array("searchdata"=>$searchData, "data"=>$this->getHtmlData($data));		
			}
		public function studentsChildrenGetstatecombo()
			{
				ob_clean();
				$data				=	$this->getData("request", "", true);				
				$terObj				=	new territory();
				$stateData			=	$terObj->getAllStates("country_id=".$data["cid"]);
				echo $stateData		=	$this->get_combo_arr("state_id",$stateData,"state_id","state_name",$searchData["sel_state"],"id='state_id' style = 'width:182px;' ","Select State");
				exit;

			}
			
		public function studentsChildrenGetcitycombo()
			{
				ob_clean();
				$data				=	$this->getData("request", "", true);
				$terObj				=	new territory();
				$cityData			=	$terObj->getAllCities("state_id=".$data["cid"]);
				echo $cityData		=	$this->get_combo_arr("city_id",$cityData,"city_id","city_name",$searchData["sel_city"],"","Select City");
				exit;
			}
		public function studentsChildrenCreateProfile()
			{
				
				$data		=	$this->getData("request", "", true);
				
				$valObj		=	new dataValidation();
				if($valObj->validateName($data['first_name']) && $valObj->validateName($data['last_name']) && $valObj->validateEmail($data['user_name']) && $valObj->validateEmail($data['confirm_email']) && $valObj->validatePassword($data['user_pwd'],5,18) && $valObj->validatePassword($data['confirmPassword'],5,18))
					{
						$loginData = $this->populateDbArray("tbluser_login",$data);
						
						$loginData['user_pwd'] = md5($loginData['user_pwd']);
						
						$userData	=	$this->populateDbArray("tblusers",$data);
						$userData['dob'] = $data['sel_year'].'-'.$data['sel_month'].'-'.$data['sel_day'];
						$userData['profile_image'] = 'profile-no-img.png';
					
					//set age group related property 						
								$loginData['privacy_policy']  = 1; 
								$loginData['user_role']       = LMT_INS_ROLE_ID; 
								$userData['instructor_level'] = 1;
								$userData['age_group'] 		  = 1;
																	
						$userObj 	= 	new userManagement();
						$loginID	=	$userObj->insertUserDetails($loginData, $userData, $data['instruments']);
						if($loginID)
							{	
							$this->clearData('CreateProfile');				
								$subject 						=  'Live Music Tutor Registration';	
								$varArr["{TPL_NAME}"]			=	$data['first_name']." ".$data['last_name'];
								$varArr["{TPL_ACTION_URL}"]		=	ROOT_URL.'userAuth.php?id='.base64_encode($loginID);
								$varArr["{TPL_URL}"]			=	ROOT_URL;						
								$cms  = new cms();
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
						$this->setPageError("Your Input is not valid please enter valid input !");			
						return $this->executeAction(true,"CreateProfileForm",true,true,false,false,"");
					}
					
			}
		public function studentsChildrenBackToListing()
			{
				$this->clearData("");
				$this->executeAction(false,"Listing",true,false,false,false,"");
			}
		public function studentsChildrenEditProfileForm()
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
					$searchData["age_group"]	= 0;
					$searchData["userType"]	    = 1; //For Instructor
					
					$searchData["lmtError"]	    =	isset($_SESSION['resMessage']) && $_SESSION['resMessage'] == 2 ? 1 : 0;
					
					unset($_SESSION['resMessage']);
					
					$searchData['curYear'] = date('Y');
					
					return array("searchdata"=>$searchData,"userInstruments"=>$userInstruments);		
			}
		public function studentsChildrenUpdateProfile()
			{
				
				$data		=	$this->getData("request", "", true);
				$data['user_id']	=	unserialize(base64_decode($data['uid']));
				$valObj		=	new dataValidation();
				if($valObj->validateName($data['first_name']) && $valObj->validateName($data['last_name']) && $valObj->validateEmail($data['user_name']))
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
						$this->setPageError("Your Input is not valid please enter valid input !");			
						return $this->executeAction(true,"EditProfileForm",true,true,false,false,"");
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
				$sql	=	"select ROUND(AVG(R.rating)) AS inst_rating, cu.* ,cc.country_name,cs.state_name from tblusers as cu 
							left join tblcountries as cc on cu.country_id=cc.country_id 
							left join tblstates as cs on cu.state_id=cs.state_id 
							LEFT JOIN tblcourses AS C on C.instructor_id=cu.user_id
                            LEFT JOIN tblcourse_ratings AS R ON R.course_id=C.course_id
							where R.is_deleted=0 AND user_id='$membersId' and ".$args;
							//echo $sql; exit;
				$result	=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function getAll($stat="")
			{
				$data				=	$this->getdbcontents_cond("tbluser_category");
				return $data; 
			}
	}