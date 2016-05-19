<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	15-08-2011
Purpose		:	videoView
*****************************************************************************************/
class videoView extends modelclass
	{
		public function videoViewListing()
			{ 	
			  	 $data['name']	=	 $_GET['id'];
				 $data['type']	=	$_GET['type'];
				 return array("data"=>$data);
			}
		public function videoViewSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function videoViewCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function videoViewReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		public function videoViewBack()
			{
				header("Location:videoView.php?id=".$_SESSION['albid']);exit;
			}
		public function videoViewAddformbtn()
			{
				$this->executeAction(false,"Addform",true);	
			}	
		
		public function videoViewViewform()
			{	
				$data				=	$this->getData("get");
				$memberObj			=	new videoView;
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
		public function videoViewStauschange()
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
						header("Location:videoView.php?id=".$_SESSION['albid']);exit;
					}
				else 
					{
						$dataUpdate	=	array();
						$dataUpdate['is_blocked']	=	"0";
						$this->db_update("tblalbum ",$dataUpdate,"album_id =$id",0);
						header("Location:videoView.php?id=".$_SESSION['albid']);exit;
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