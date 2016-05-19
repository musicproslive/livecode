<?php
	class privacyControlLib extends siteclass {
	
		public function getPrivacy($id){
			$query			=	"SELECT * FROM `tblusers_privacy_control` WHERE `user_id`=$id ";
			$resultArry		=	$this->getdbcontents_sql($query, 0);					
			return $resultArry;
		}
	}
?>