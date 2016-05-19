<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	30-08-2011
Purpose		:	To Manage vod
*****************************************************************************************/
include("../classes/vodlist.php");
class vod extends modelclass
	{
		public function vodListing()
			{ 
				$_SESSION['vid']	=	$_GET['id'];
			}
		public function vodSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function vodCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function vodReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		
		public function vodAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		
		public function vodDeletedata()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new vodlist;//including class
				$data				=	$memberObj->deleteVod($data['id']);
				return array("data"=>$data);
				header("Location:vod.php?id=".$_SESSION['vid']);exit;
			}	
			
		public function vodViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new vodlist;//including class
				$data1				=	$memberObj->getvodDetails($data['id']);
				return array("data"=>$data1);
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