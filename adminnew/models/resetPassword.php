<?php
/**************************************************************************************
Created by :
Created on :2010-11-16
Purpose    :Index Model Page
**************************************************************************************/
class resetPassword extends modelclass
	{
		public function resetPasswordListing()
			{
				$data		=	$this->getData("get");
				$loginid	=	unserialize(base64_decode($data['id']));
				$sql		=	"SELECT user_pwd,login_id FROM tbluser_login WHERE login_id=".$loginid;
				$data		=	end($this->getdbcontents_sql($sql));
				return array("data"=>$data);
			}	
		public function resetPasswordSubmit()
			{
				ob_clean();
				$data		=	$this->getData("get");
				$loginid	=	$_REQUEST['id'];
				$password	=	md5($_REQUEST[newpwd]);
		  	    $sql		=	"UPDATE tbluser_login SET user_pwd='$password' where login_id=".$loginid;
				$result			=	mysql_query($sql);
				if($result)
					{
						echo "Your password has been changed successfully";exit;	
						
					}
				else
					{					
	
						echo "Sorry some problem occured.Please retry again";exit;
					}
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
