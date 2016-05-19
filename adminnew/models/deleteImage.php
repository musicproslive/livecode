<?php
/****************************************************************************************
Created by	:	Arun
Created on	:	03-09-2011
Purpose		:	Group Album Gallery
*************** ***********************************************************************/
class deleteImage extends modelclass
	{
		
		public function redirectAction($errMessage,$action,$url){	
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
				//echo 'here actionReturn '.$methodName;
				//echo "<pre/>";
				//print_r( $this->actionReturn );
				$this->actionExecuted($methodName);
				return $this->actionReturn;
			}
		public function __destruct() 
			{
				parent::childKilled($this);
			}
	}