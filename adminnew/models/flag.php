<?php 
/**************************************************************************************
Created By 	:	Milan
Created On	:	27-09-2012
Description	:	Manage User Abuses 
**************************************************************************************/
class flag extends modelclass
	{	
		public function flagListing()
			{	$obj = new catSubcatManagement();
				$result=array();
				
				if(isset($_GET['del_id']))
				{
					$message=$obj->delete_cat_sub($_GET['del_id']);
					//echo '<pre>';
					//print_r($message);
					//echo '</pre>';//die();
					
					
					if(isset($_SESSION['isassociated_flagbook']))
						{	
							unset($_SESSION['isassociated_flagbook']);
							$result['associated_flagsbook']=$message['associated_flagbook'];
							$result['message']=$message['message'];
							
						}
					else
						{
							$result['message']=$message;
						}
				
				

				}
				if(isset($_POST['submit']))
					{	
						unset($_POST['submit']);
						
						$message=$obj->addCatSubcat($_POST);
						$result['message']=$message;
		
					}
				
				$alldata=$obj->getAllFlags(0);
				$result['alldata']=	$alldata;
				
				$dropdown_data=$obj->getAllFlags(1);
				$result['dropdown_data']=	$dropdown_data;
				
				return $result;
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