<?php
/**************************************************************************************
Created By 	:	Lijesh
Created On	:	07-07-2011
Purpose		:	functions for groups
**************************************************************************************/

class defaults extends siteclass
	{
		// To Get Role Constants & Default Constants
		public function defineConstants()
			{ 
				$data		=		$this->getdbcontents_cond('tbluser_roles', false);	
				foreach ($data as $value)
					{
						define($value["role_access_key"],$value["role_access_key"]);						
					}
					
				$data		=		$this->getdbcontents_cond('tbluser_category', true);	
				//print_r($data);
				foreach ($data as $value)
					{
						define($value["glb_access_key"],$value["category_id"]);	
							/*echo $value["glb_access_key"];		
							echo $value["category_id"];	*/	
					}	
					//exit;
				$data		=		$this->getdbcontents_cond('tbldefaults');	
				foreach ($data as $value)
					{
						define($value["name"],$value["value"]);						
					}
			} 
		
		function getGlbAccessKey()
			{
				$sql = "SELECT * FROM tbldefaults";
				$data		=	$this->getdbcontents_sql($sql, false);
				foreach ($data as $value)
					{
						define($value["name"], $value["value"]);						
					}
			}
		function getDefaultGroups()
			{
				$sql 	= "SELECT * FROM tbldefaults_group";
				$data	=	$this->getdbcontents_sql($sql, false);
				return $data;
			}
			function getDefaultGroupList()
			{
				$sql 	= "SELECT * FROM tbldefaults_group";
				$data	=	$this->getdbcontents_sql($sql, false);
				
				foreach($data as $key=>$value){
					$group		.=$value["group"].",";
				}
				$group			=	trim($group,",");
				$groupArr		 =	explode(",",$group);
				return $groupArr;
			}
	}	
?>
