<?php
/**************************************************************************************
Created by :Arun
Created on :26/07/2011
Purpose    :Admin Home Model Page
******************* *******************************************************************/
class adminHome extends modelclass 
	{
		public function adminHomeListing()
			{
				$userObj	=	new adminUser();
				$userSess 	=	end($userObj->get_user_data());	
				//print_r($userSess);exit;
				//$data1							=	$this->getContactUs();
				$data2							=	$this->getMembersDet();
				
				return array("data1"=>$data1,"data2"=>$data2);
			}
		public function adminHomeupdateData()
			{
				ob_clean();
				$data1								=	array();
				$data1["contactus"]			=	$this->getContactUs();
				$data1["members"]			=	$this->getMembersDet();
				
				echo json_encode($data1);
				exit;
			}
		
	public function getMembersDet()
			{
				$date							= 	date("Y-m-d") ;
				$sql							=	"select count(*) as insmaster from tblinstrument_master  where is_deleted=0";
				$data							=	end($this->getdbcontents_sql($sql));
		        $members['insmaster']			=	$data['insmaster'];
				
				$sql							=	"select count(*) as tutor from tblusers AS U LEFT JOIN tbluser_login as L ON L.login_id =	U.login_id where U.is_deleted=0 AND L.user_role=3";
				$memtoday						=	end($this->getdbcontents_sql($sql));
				$members['tutor']				=	$memtoday['tutor'];
				
				$sql							=	"select count(*) as student from tblusers AS U LEFT JOIN tbluser_login as L ON L.login_id =	U.login_id where U.is_deleted=0 AND L.user_role=4";
				$memtoday						=	end($this->getdbcontents_sql($sql));
				$members['student']				=	$memtoday['student'];
				
				$sql							=	"select count(*) as fun from tblentertainment where is_deleted=0";
				$memtoday						=	end($this->getdbcontents_sql($sql));
				$members['fun']					=	$memtoday['fun'];
				
				$sql							=	"select count(*) as courses from tblcourses WHERE 1 ";
				$memtoday						=	end($this->getdbcontents_sql($sql));
				$members['courses']				=	$memtoday['courses'];
				//print_r($members['tutor']);
			return $members;
			
			}
		public function __construct()
			{
				define('LOGGEDIN_ID',"where 1");
				define('LOGGEDIN_ADMIN_USER',$logedin['usertype']);
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
