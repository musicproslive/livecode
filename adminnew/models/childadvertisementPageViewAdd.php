<?php  
/****************************************************************************************
Created by	:	Arvind  
Created on	:	04-08-2011
Purpose		:	To Manage Publsihed Video
******************************************************************************************/
class childadvertisementPageViewAdd extends modelclass
	{
		public function childadvertisementPageViewAddListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName(),'childadvertisementpage.php','childadvertisementPageView.php'));
				return array("id"=>$_GET['id']);
			
			}
				
		public function childadvertisementPageViewAddSubmit()
			{
				$cls		=	 new advertisementManagement();
				$mode		=	$_POST['txtMode'];//google_add	
//print_r ($_POST);die();				
				if($cls->addAdvchild($_POST,$mode)){
					header("Location:childadvertisementPageView.php?id=".$_GET['id']);
					
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