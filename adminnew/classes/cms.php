<?php
/**************************************************************************************
Created By 	:Lijesh
Created On	:Sep 07 2012
Purpose		:Handling cms data
**************************************************************************************/
class cms extends siteclass
	{
		public $cmsId	=	"";
		
		public function setCmsId($id)
			{
				if($id)	$this->cmsId	=	$id;
			}
			
		//CMS SECTION
		public function getCMSSectionData($args="1")
			{
				$sql					=	"SELECT * FROM tblcms_section WHERE $args";			
				$data					=	$this->getdbcontents_sql($sql);	
				return $data;
			}
		public function getCmsSectionList()
			{
				$sql						=	"SELECT * FROM tblcms_section";
				$data	=	$this->getdbcontents_sql($sql, false);
					
					foreach($data as $key=>$value){
						$section		.=$value["section"].",";
					}
					$section			=	trim($section,",");
					$sectionArr		 =	explode(",",$section);
					return $sectionArr;
			}
		public function getCMS($id)
			{
				$data	=	$this->getdbcontentshtml_cond('tblcms',"id=$id");
				//if (empty($data)) { echo "not set!"; } else { echo "set!";}
				return $data; 	
			}
				
		public function sendMailCMS($id,$to,$from,$subject,$vars,$priority=0, $form1)
			{ 
			//echo $id.'\n '.$to.'\n '.$from.'\n '.$subject.'\n '.$priority;
				$id = (int) $id;
				$cmsArr		=	end($this->getCMS($id));//fetching email template from CMS 
				
				$message	=	$cmsArr["description"];
												
				foreach($vars	as	$key=>$val)	{
					
				$message	=	str_replace($key, $val, $message); }
				//echo "<pre>"; print_r($message); echo "</pre>"; die;
				/* $fh = fopen("mail.txt", 'a');
 				fwrite($fh, "To: ".$to."From : ".$from."Subject : ".$subject."Message : ".$message);	 */
				if(empty($message)) { $message = '';}
				
				$success	=	$this->sendmail($to, $from, $subject, $message, $form1);
				return $success;
			}
		public function getContentTpl($id,$vars)
			{
				$cmsArr		=	end($this->getCMS($id));//fetching email template from CMS
				$message	=	$cmsArr["description"];								
				foreach($vars	as	$key=>$val)	
				$message	=	str_replace($key,$val,$message);				
				return $message;
			}
				
	}
?>
