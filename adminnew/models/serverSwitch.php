<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	07-07-2011
Purpose		:	To Manage Tutors
******************************************************************************************/
class serverSwitch extends modelclass
	{
		public function serverSwitchListing()
			{ 
				$path	 			=	 ROOT_ABSOLUTE_PATH."xml".DIRECTORY_SEPARATOR."Config.xml";//dirname(__FILE__);
				$data				=	 file_get_contents($path);
				$data				=	 simplexml_load_string($data);
				$res["server"]		=	 $data->Server["Value"];
				$res["bandwidth"]	=	 $data->StreamingBandwidth["Value"];
				$res["quality"]		=	 $data->StreamingQuality["Value"];
				return $res;
			}
			
		public function serverSwitchUpdatedata()
			{
				
				$data	='<?xml version="1.0" encoding="iso-8859-1"?>
							<AppConfig>
							<Root Path="musictutor" />
							<Image Path="images" />
							<User  Path="webServices/userProfile.php"/>
							<Favourites Path= "webServices/UserFavourites.php"/>
							<Help Path= "webServices/Help.php" />
							<Subscription Path= "webServices/Subscription.php" />
							<StreamingQuality Value="'.$_POST["quality"].'" />
							<StreamingBandwidth Value="'.$_POST["bandwidth"].'" />								
							<Server Value="'.$_POST["server"].'"/>
							</AppConfig>';				
				$path	 	=	 ROOT_ABSOLUTE_PATH."xml".DIRECTORY_SEPARATOR."Config.xml"; //dirname(__FILE__);
				$data		=	 file_put_contents($path,$data);		
						
				$this->setPageError("Updated Successfully");
				$this->clearData();
				return $this->executeAction(false,"Listing",true);			
					
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