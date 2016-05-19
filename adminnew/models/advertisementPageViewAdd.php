<?php  
/****************************************************************************************
Created by	:	Arvind  
Created on	:	04-08-2011
Purpose		:	To Manage Publsihed Video
******************************************************************************************/
class advertisementPageViewAdd extends modelclass
	{
		public function advertisementPageViewAddListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName(),'advertisementPage.php','advertisementPageView.php'));
				return array("id"=>$_GET['id']);
			
			}
				
		public function advertisementPageViewAddSubmit()
			{
				$cls		=	 new advertisementManagement();
				$mode		=	$_POST['txtMode'];//google_add						
				if($cls->addAdv($_POST,$mode)){
					header("Location:advertisementPageView.php?id=".$_GET['id']);
					exit;
				}
				else{
					$this->setPageError("Please enter Mandatory fields ");	
				}	
			}
			
		public function redirectAction($errMessage,$action,$url)	
			{	
				$this->setPageError($errMessage);
				$this->clearData();
				$this->executeAction(true,$action,$url,true);	
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
	}