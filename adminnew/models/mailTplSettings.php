<?php
/**************************************************************************************
Created By 	:	PREM PRANAV
Created On	:	26-11-2012
Description	:	Mail Template Permission for Admin 
**************************************************************************************/

class mailTplSettings extends modelclass{

		public function mailTplSettingsListing()
			{
				$clsU			=	new userManagement();
				$admin			=	$clsU->getAllAdmin();
				return			array("admin"=>$admin);		
				
			}
		public function mailTplSettingsSettingForm()
			{
				$data			=	$this->getData("post");
				$id				=	$data['sel_admin'];
				$clsU			=	new userManagement();
				$admin			=	$clsU->getAllAdmin();
				$data			=	$clsU->getAdminTplSettings($id);		
				return			array("data"=>$data,"admin"=>$admin,"sel_admin"=>$id,"new"=>$new);
			}
		
		public function mailTplSettingsSave()
			{
				$this->print_r($_POST);
				$id		=	$_POST['sel_admin'];
				// Make new Entry for new template.not have entry in tbladmin_mail_template.
				$i=0;
				foreach($_POST['settings'] as $val)
					{
						if($_POST['tpl_status'][$i] != '0' && $_POST['tpl_status'][$i] != '1')
							{
								$data['user_id']		=	$id;
								$data['created_on']		=	 date("Y-m-d H:i:s");
								$data['tpl_id']			=	$val;
								if(in_array($val, $_POST['mail']))
									$data['is_deleted']		=	'0';
								else
									$data['is_deleted']		=	'1';
								$result	=	$this->db_insert("tbladmin_mail_template",$data,0);
							}
						$i++;	
					}					
				// Activating all selected control into Database 	
				foreach($_POST['mail'] as $val)
					{		
						$array		=	array("is_deleted"=>0);
						$result		=	$this->db_update("tbladmin_mail_template",$array,"user_id=".$id." and tpl_id=".$val,1);
					}
				//Inactivating all unselected control into database.
				if(empty($_POST['mail'])) $_POST['mail'] = array();
				foreach((array_diff($_POST['settings'], $_POST['mail'])) as $val=>$key)
					{		
						$array		=	array("is_deleted"=>1);
						$result		=	$this->db_update("tbladmin_mail_template",$array,"user_id=".$id." and tpl_id=".$val,1);
					}
				if($result)
					{	
							$this->setPageError("Your Settings Successfully saved !");
							$this->executeAction(true,"SettingForm","",true,true,"","","mailTplSettings.php");		
					}
				else
							$this->redirectAction("Sorry Found some errors. Please try again!","Listing","mailTplSettings.php");							
			
			}
		public function mailTplSettingsCancel()
			{
				$this->executeAction(false,"Listing","mailTplSettings.php");		
			}
		public function mailTplSettingsReset()
			{
				$this->clearData("Listing");
				$this->clearData("SettingForm");
				$this->executeAction(false,"Listing","mailTplSettings.php");		
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