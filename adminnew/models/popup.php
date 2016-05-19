<?php
/**************************************************************************************
Created by :Lijesh
Created on :16-08-2011
Purpose    :Tutor class shedule In detail
**************************************************************************************/

class popup extends modelclass{

	public function popupListing()
		{				
			return array("msg"=>$_GET['msg']);
		}
	public function popupCourseCloseForm()
		{	
			$cls			=	new userCourse();
			$reasons		=	$cls->getCourseCloseReasons();
			return array("reasons"=>$reasons,"course_code"=>unserialize(base64_decode($_GET['id'])));
		}
	public function __construct()
		{
			$this->setClassName();
		}
	
	public function redirectAction($errMessage,$action,$url)	
		{	
			$this->setPageError($errMessage);
			$this->clearData();
			$this->executeAction(true,$action,$url,true);	
		}	
			
	public function executeAction($loadData=true,$action="",$ufURL="",$navigate=false,$sameParams=false,$newParams="",$excludParams="",$page="")
		{
			if(trim($action))	$this->setAction($action);//forced action
			$methodName	=		(method_exists($this,$this->getMethodName()))	? $this->getMethodName($default=false):$this->getMethodName($default=true);
			$this->actionToBeExecuted($loadData,$methodName,$action,$navigate,$sameParams,$newParams,$excludParams,$page,$ufURL);
			$this->actionReturn		=	call_user_func(array($this, $methodName));				
			$this->actionExecuted($methodName);
			return $this->actionReturn;
		}
	public function __destruct() 
		{
			parent::childKilled($this);
		}
	
}

?>