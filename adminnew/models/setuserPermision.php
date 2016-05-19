<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	20-08-2011
Purpose		:	adminPermision
*****************************************************************************************/
class setuserPermision extends modelclass
	{
		public function setuserPermisionListing()
			{
			//print_r($_SESSION);
				 $_SESSION['pid']	=	$_GET['id'];				 
				 $sql				=	"SELECT m.`menuName`,s.* FROM `tblsub_menu` s JOIN `tblmenu` m  WHERE s.`status`=1 AND s.`menuId`=m.`id` AND m.`status`=1 ORDER BY m.`dateAdded` ASC ";
				 $rec["data"]		=	$this->getdbcontents_sql($sql);				 
				 $query				=	"SELECT  * FROM `tbladmin_actions` WHERE `status`=1";
				 $rec["action"]		=	$this->getdbcontents_sql($query);
				 for($i=0;$i<count($rec["data"]);$i++){	
				 	$rec["data"][$i]["selected"]	=	0;		
				 	unset($arr);
				 	for($j=0;$j<count($rec["action"]);$j++){
				 		if($this->is_selected($rec["data"][$i]["id"],$rec["action"][$j]["id"],$_GET['id'])){
				 			$arr[$j]	=	1;	 			 
				 		}
				 		 $rec["data"][$i]["selected"]	=	$arr;
				 	}
				 }
				 //print_r($rec['data']);exit;
				 return $rec;
			}
		
		public function is_selected($pageId,$actionId,$userType){
			$query	=	"SELECT * FROM `tbladmin_page_actions` WHERE `pageid`=$pageId AND `actionid`=$actionId AND `user_type`=$userType";
			$rec	=	$this->getdbcontents_sql($query);
			if(count($rec)>0){
				return true;
			}else{
				return false;
			}
			exit;
		} 
		public function setuserPermisionSubmit()
			{
				 $details			=	$_POST;		
				 $sql				=	"SELECT m.`menuName`,s.* FROM `tblsub_menu` s JOIN `tblmenu` m  WHERE s.`status`=1 AND s.`menuId`=m.`id` AND m.`status`=1 ORDER BY m.`dateAdded` ASC ";
				 $data				=	$this->getdbcontents_sql($sql);		 
				 $query				=	"SELECT  * FROM `tbladmin_actions` WHERE `status`=1";
				 $action			=	$this->getdbcontents_sql($query);
				 $this->dbDelete_cond("tbladmin_page_actions","user_type =".$_SESSION['pid'],0);				 
				 for($i=0;$i<count($data);$i++){				 	
				 	for($j=0;$j<count($action);$j++){	 	
				 	 	 $fieldName		=	str_replace(" ","_",$data[$i]["name"])."-".$action[$j]["action"];
				 	 	if(isset($details[$fieldName])){
				 				$this->db_insert("tbladmin_page_actions",array("pageid"=>$data[$i]["id"],"actionid"=>$action[$j]["id"],"user_type"=>$_SESSION['pid']),0);				 		
				 		}
				 	}
				 }
				$this->setPageError("Updated Successfully !");
				header("Location:setuserPermision.php?id=".$_SESSION['pid']);exit;
			}
		public function adminPermisionPerchange()
			{
				$details	=	$this->getData("get");
				$id			=	$details['id'];
				$this->dbDelete_cond("tbladmin_permission","menu_id =$id",0);
				
			}
		public function setuserPermisionBack()
		{
			header('Location: adminPermision.php?actionvar=Listing');
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