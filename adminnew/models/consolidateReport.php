<?php 
/****************************************************************************************
Created by	:	PREM PRANAV 
Created on	:	09-01-2013
Purpose		:	Consolidated Report For Enrollment
****************************************************************************************/
class consolidateReport extends modelclass
	{
		public function consolidateReportListing()
			{
				//LMT_COURSE_STATUS_OPEN;
				
					$sql		=	"SELECT count(e.`enrolled_id`) as totalEnrolled, cur.`symbol`,SUM(p.`trans_amount`) as amountTotal
										 FROM `tblcourse_enrollments` e 
										 LEFT JOIN  `tblcourses` c ON c.`course_id`= e.`course_id`
										 LEFT JOIN `tblcourse_enrollment_transaction` p ON p.`enrolled_id`= e.`enrolled_id`
										 LEFT JOIN `tblcurrency_type` cur ON cur.`currency_id`=p.`currency_id`		
										WHERE   c.`course_status_id` !=".LMT_COURSE_STATUS_CANCELLED."
											AND e.`enrolled_status_id` !=".LMT_CS_ENR_CANCELLED;
					$results 	= 	end($this->getdbcontents_sql($sql,0));
					//$this->print_r($results);exit;
					return array("data"=>$results);						
											
			}
		
		public function redirectAction($loadData=true,$errMessage,$action)	
			{	
				$this->setPageError($errMessage);
				$this->executeAction($loadData,$action,true);	
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