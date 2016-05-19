<?php 

/****************************************************************************************
Created by	:	Prem 
Created on	:	08-09-2012
Purpose		:	To Manage Admin Profile
*****************************************************************************************/
class myProfile extends modelclass
	{
		public function myProfileListing()
			{ 
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
						$sql						=	"SELECT A .* ,B.*, CONCAT( B.first_name,  ' ', B.last_name ) AS name, C.role_name,D.country_name,
													E.state_name,F.city_name,concat(G.timezone_location,' ',G.gmt) as timezone,
													date_format(now(), concat(mysql_date_format,' ', mysql_time_format) )as timeformat 
													FROM tbluser_login AS A
													LEFT JOIN tblusers AS B ON A.login_id = B.login_id
													LEFT JOIN tbluser_roles AS C ON A.user_role = C.role_id LEFT JOIN tblcountries AS D on B.country_id=D.country_id
													LEFT JOIN tblstates AS E on B.state_id=E.state_id LEFT JOIN tblcities AS F on B.city_id=F.city_id 
													LEFT JOIN tbltime_zones AS G on B.time_zone_id=G.id LEFT JOIN tbllookup_user_timestamp AS H on B.time_format_id=H.id
													WHERE A.is_deleted =0 AND A.login_id =".$_SESSION['log_id'];								
														
				$this->addData(array("sql"=>$sql),"post","",false);
				$spage				 		=	$this->create_paging("n_page",$sql);
				$data				 		=	end($this->getdbcontents_sql($spage->finalSql()));
				//print_r($data);
				if(!$data)						$this->setPageError("No records found !");
				return array("data"=>$data,"spage"=>$spage);
			}
			
		public function myProfileAddinfoform()
			{
				$data		=	$this->getData("request");
				if(!isset($data['country_id']) && empty($data['country_id']))	// Select Default United states
					$data['country_id'] = '223';
				$dataArr=end($this->getdbcontents_sql("SELECT * FROM tblusers where login_id='".$_SESSION['log_id']."'"));
				//print_r($dataArr);	
				$countryID = $dataArr['country_id'];
				$stateID = $dataArr['state_id'];
				$cityID=$dataArr['city_id'];
				$timeZoneID = $dataArr['time_zone_id'];
				$timeFormatID=$dataArr['time_format_id'];
				
				$terObj			=	new territory();
				//Country drop down
				$countryData	=	$terObj->getAllCountries("1 order by country_name asc");	
				$countryData	=	$this->get_combo_arr("country_id",$countryData,"country_id","country_name","$countryID"," onchange='getStates(this.value,\"id_search_state\")' class='validate[required]' id='country_id'","Select Country");
				
				//State drop down				
				$stateData	=	$terObj->getAllStates("country_id = ".$data['country_id']." order by state_name asc");
				$stateData	=	$this->get_combo_arr("state_id",$stateData,"state_id","state_name","$stateID"," onchange='getCities(this.value,\"id_search_city\")' class='validate[required]' id='state_id' style = 'width:182px;' ","Select State");
				
				//City drop down
				$cityData	=	$terObj->getAllCities("1 order by city_name asc");
				$cityData	=	$this->get_combo_arr("city_id",$cityData,"city_id","city_name","cityID","","Select City");
				
				$timeZone = new timeZone();
				$timeZoneList = $timeZone->getAllTimeZone();
				$timeZoneList = $this->get_combo_arr("time_zone_id",$timeZoneList,"id","timezone","$timeZoneID","class='validate[required]' id='timeZoneId' style = 'width:260px;' ","Select TimeZone");
				/*$this->print_r($timeZoneList);
				exit;*/
				
				$timeFormatList = $this->getdbcontents_sql("SELECT id, date_format(now(), concat(mysql_date_format,' ', mysql_time_format) )as timeformat FROM `tbllookup_user_timestamp`");
				$timeFormatList = $this->get_combo_arr("time_format_id",$timeFormatList,"id","timeformat","$timeFormatID","class='validate[required]' id='timeFormatId' style = 'width:260px;' ","Select TimeFormat");
				
				//$this->print_r($timeFormatList);
				
				$dob=explode("-",$dataArr['dob']);
				$days = $this->get_combo_int('sel_day',1,31,$dob[2],"class='' onchange='validateDobDay();' id='sel_day'",'Day');
//				$this->print_r($days);
				$months = $this->get_combo_months('sel_month',$dob[1],"class='validate[required]' onchange='validateDobMonth();' id='sel_month'",'Month');
//				$this->print_r($months);
				$years = $this->get_combo_year('sel_year',$dob[0],"class='validate[required]' onchange='validateDobYear();' id='sel_year'",'Year',90,2);
//				$this->print_r($years);
				
				$searchData["country_combo"]	=	$countryData; 
				$searchData["state_combo"]		=	$stateData; 
				$searchData["city_combo"]		=	$cityData; 
				$searchData["timeZoneList"]	    =	$timeZoneList; 
				$searchData["timeFormatList"]	=	$timeFormatList;
				$searchData["instruments"]	    =	$instuments;
				
				$searchData["days"]	    =	$days;
				$searchData["months"]	=	$months;
				$searchData["years"]	=	$years;
				
				$searchData["profile_image"]	    =	$dataArr['profile_image'];
				$searchData["gender"]	    =	$dataArr['gender'];
				$searchData["userType"]	    =	$data['userType']; //From Post Variable
				
				//echo $_SESSION['resMessage'];
				$searchData["lmtError"]	    =	isset($_SESSION['resMessage']) && $_SESSION['resMessage'] == 2 ? 1 : 0;
				//echo ' >'.$searchData["lmtError"];
				
				unset($_SESSION['resMessage']);
				
				$searchData['curYear'] = date('Y');
				
				//$this->print_r($searchData);exit;
				return array("searchdata"=>$searchData, "data"=>$this->getHtmlData($data), "state_id"=>$stateID);
			}
		public function myProfileSubmit()
			{
				$data		=	$this->getData("post");
				$file		=	$this->getData("files");

				if($file['image']['name'])
					{
						$upObj			=	$this->create_upload(10,"jpg,png,jpeg,gif,swf");
						$adimg			=	$upObj->copy("image","images/profile",2);
						if($adimg)			$upObj->img_resize("100","120","images/profile/thumbs");
						else 				$this->setPageError($upObj->get_status());
						$this->addData(array("profile_image"=>"images/profile/".$adimg),"request");
					}
										
				$data		=	$this->getData("request");
				$userData	=	$this->populateDbArray("tblusers",$data);
				$userData['dob'] = $data['sel_year'].'-'.$data['sel_month'].'-'.$data['sel_day'];
				if($_SESSION['user_group'] == '-1')
					{
						$flag=$this->getdbcontents_sql("SELECT * FROM  tblusers WHERE login_id =".$_SESSION['log_id']);
						if(!sizeof($flag))
						{
						$userData['login_id'] = $_SESSION['log_id'];
						$userID =	$this->db_insert('tblusers',$userData);
						}
					}
				$userID =	$this->db_update('tblusers',$userData,$this->dbSearchCond("=","login_id",$_SESSION['log_id']));											
				if($userID)
					{				
						$this->clearData("Submit");					
						$this->setPageError("Data Saved Successfully");			
						return $this->executeAction(true, "Listing");
					}
					else{	
						$this->setPageError("Data couldn't save try again!");			
						return $this->executeAction(true, "Listing");
					}				
			}
		public function myProfileStatecombo() //Function for display state combo for seleted country used in ajex
			{
				$data = 	$this->getData("get");
				$cid=$data['cid'];
				$sql		=	"select distinct state_id,state_name from tblstates where country_id ='$cid'";
				$statearr	=	$this->getdbcontents_sql($sql);
				$state_combo			=	$this->get_combo_arr("state_id", $statearr, "state_id", "state_name","","id='state_id'","Select State");
				ob_clean();
				echo $state_combo;
				exit;
			}
		public function myProfileCitycombo() //Function for display city combo for seleted state used in ajex
			{
				$data = 	$this->getData("get");
				$sid=$data['sid'];
				$sql		=	"select distinct city_id,city_name from tblcities where state_id ='$sid'";
				$cityarr	=	$this->getdbcontents_sql($sql);
				$city_combo			=	$this->get_combo_arr("city_id", $cityarr, "city_id", "city_name","","id='city_id'","Select City");
				ob_clean();
				echo $city_combo;
				exit;
			}						
		public function adminProfileCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function adminProfileReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		public function adminProfileAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		
		public function adminProfileEditform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new adminProfile;
				$data				=	$memberObj->getadminProfileDetails($data['id']);
				return array("data"=>$this->getHtmlData($data));
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