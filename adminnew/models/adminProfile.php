<?php 

/****************************************************************************************
Created by	:	Arun 
Created on	:	07-07-2011
Purpose		:	To Manage Admin Profile
*****************************************************************************************/
class adminProfile extends modelclass
	{
		public function adminProfileListing()
			{ 
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				if($_SESSION['user_group'] == '-1')//for super admin
					{
						$sql						=	"select u.*,l.* ,concat(u.first_name,' ',u.last_name) as name 
														from tbluser_login as l left join tblusers as u 
														on l.login_id=u.login_id where l.is_deleted=0 
														and l.user_group=-1 and l.login_id=".$_SESSION['log_id'];								
					}
				$this->addData(array("sql"=>$sql),"post","",false);
				
				
					$timezObj 		=  	new timeZone();
					$timeformat		= 	$timezObj->getAllTimeFormat();	
				
				$query				=	"SELECT `time_format_id` FROM `tblusers` WHERE `user_id`=".$_SESSION["sess_admin"];
				$temp				=	end($this->getdbcontents_sql($query));
				$selected_user		=	$temp["time_format_id"];
				
			
				
				$spage				 		=	$this->create_paging("n_page",$sql);
				$data				 		=	$this->getdbcontents_sql($spage->finalSql());
				if(!$data)						$this->setPageError("No records found !");
				$searchData					=	$this->getHtmlData($this->getData("post","Search",false));
				return array("data"=>$data,"spage"=>$spage,"searchdata"=>$searchData,"selected_user"=>$selected_user,'timeformat'=>$timeformat);
			}
		public function adminProfileSearch()
			{
				$this->executeAction(false,"Listing",true);
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
			
		public function adminProfileUpdatedata()
			{
				$details	=	$this->getData("request");
				
			
				if($details['user_name']==$details['confm_user_name'])
					{
						if($details['new_user_pwd']==$details['confirm_pwd']&& $details['new_user_pwd']!='')
							{
								$dataIns	  =	$this->populateDbArray("tblusers",$details);
								$updateStatus =	$this->db_update("tblusers",$dataIns,"user_id='".$details['id']."'",1);
								if($details['new_user_pwd'])
									$details['user_pwd']	=	md5($details['new_user_pwd']);
								$dataIns1	  =	$this->populateDbArray("tbluser_login",$details);
								//print_r($dataIns1);exit;
								$updateStatus1=	$this->db_update("tbluser_login",$dataIns1,"login_id='".$details['id']."'",0);
								
								if(($updateStatus) && ($updateStatus1))
									{	
										$this->setPageError("Updated Successfully");
										return $this->executeAction(false,"Listing",true);			
									}
							}
							
							else
								{
									$this->setPageError("Password and confirm password must be same");
									$this->executeAction(false,"Listing",true);
								}
					}
				else
					{
						$this->setPageError("Email and confirm email must be same");
						$this->executeAction(false,"Listing",true,true);
					}
			}
		public function getadminProfileDetails($membersId="",$args="1")
			{	
				$sql					=	"SELECT l.*,u.*,concat(u.first_name,' ',u.last_name) as name FROM 
											tbluser_login as l left join tblusers as u on l.login_id=u.login_id ".
											$sqlFilter["selc"]." ".$sqlFilter["join"]." 
											where l.is_deleted=0 and l.user_group=0 and l.login_id=".$_SESSION['log_id'];
				if($_SESSION['user_group']=='-1')//for super admin
					{
						$sql						=	"select u.*,l.* from tbluser_login as l left join tblusers as u 
														on l.login_id=u.login_id where l.is_deleted=0 
														and l.user_group=-1 and l.login_id=".$_SESSION['log_id'];								
					}							
										
				$result					=	end($this->getdbcontents_sql($sql,0));
				return $result;
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