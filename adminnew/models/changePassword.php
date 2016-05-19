<?php
/**************************************************************************************
Created by :hari krishna S
Created on :2010-11-22
Purpose    :Contact us  Page
**************************************************************************************/
class changePassword extends modelclass
	{
		public function changePasswordChange()
			{
				$data			=	$this->getData("post");
				$count			=	0;
				foreach($data as $val)
					{
						if(is_null($val) OR empty($val)) $count++;
						if(strlen($data['newPass'])<4 or strlen($data['newPass'])>15)
							{
								$this->setPageError("Please enter a valid password with 4-15 characters");
								$this->executeAction(false,"Listing",true);
								$count++;
							}
						if($data['newPass']		!=	$data['confNewpass'])
							{
								$this->setPageError("Your passwords don't match");
								$this->executeAction(false,"Listing",true);
								$count++;
							}
					}
				
				if($count==0)
					{
						$adminNew			=	new adminUser();
						$logedin			=	end($adminNew->get_user_data());
						$userid				= 	$logedin['login_id'];		
						$query				=	$this->dbSearchCond("=", "login_id",$userid);
						$sql				=	"select user_pwd from  tbluser_login where".$query  ;
						$pass				=	end($this->getdbcontents_sql($sql));
						$oldPass=md5($data['oldPass']);
						if($pass['user_pwd']==$oldPass)
							{
								$newpass	=		md5($data['confNewpass']);
								$sqlupdate	=		"UPDATE tbluser_login SET user_pwd = '$newpass' WHERE login_id='$userid'";
								$change		=		mysql_query($sqlupdate);
								if($change)
									{
										$this->setPageError("Password changed Successfully");
										$this->executeAction(false,"Listing",true);
									}
								else
									{
										$this->setPageError("Error changing password! please try again");
										$this->executeAction(false,"Listing",true);
									}	
							}
						else 
							{
								$this->setPageError("Your old password is wrong");
								$this->executeAction(false,"Listing",true);
							}
				}
			}	
		
		public function changePasswordCancel()
			{
				
				header('Location:myProfile.php?actionvar=Listing');
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
