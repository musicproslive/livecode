<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	15-08-2011
Purpose		:	audioView
*****************************************************************************************/
class audioView extends modelclass
	{
		public function audioViewListing()
			{ 	
			  	 $data['name']	=	$_GET['id'];
				 $data['title']	=	$_GET['title']; 
				 return array("data"=>$data);
			}
		public function audioViewSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function audioViewCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function audioViewReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		public function audioViewBack()
			{
				header("Location:audioView.php?id=".$_SESSION['albid']);exit;
			}
		public function audioViewAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		
		public function audioViewViewform()
			{	
				$data				=	$this->getData("get");
				$memberObj			=	new audioView;
				if($data['type']==0)
					{
						$data				=	$memberObj->getalbumDetails($data['id'],"type='image'");
					}
				else
					{
						$data				=	$memberObj->getalbumDetails($data['id'],"type='video'");
					}
				return array("data"=>$this->getHtmlData($data));
			}
		
		public function getalbumDetails($membersId="",$args="1")
			{	
			    $sql					=	"select * from tblalbum_image where album_id='$membersId' and ".$args;
				$result					=	$this->getdbcontents_sql($sql);
				return $result;
			}
		public function audioViewStauschange()
			{
				$details	=	$this->getData("get");
				$sql		=	"select * from tblalbum where album_id=".$details['id']."";
				$data		=	$this->getdbcontents_sql($sql);

			    $id			=	$data[0]['album_id'];
				if($data[0]['is_blocked']==0)
					{			
						$dataUpdate	=	array();
						$dataUpdate['is_blocked']	=	"1";
						$this->db_update("tblalbum ",$dataUpdate,"album_id =$id",0);
						header("Location:audioView.php?id=".$_SESSION['albid']);exit;
					}
				else 
					{
						$dataUpdate	=	array();
						$dataUpdate['is_blocked']	=	"0";
						$this->db_update("tblalbum ",$dataUpdate,"album_id =$id",0);
						header("Location:audioView.php?id=".$_SESSION['albid']);exit;
					}
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