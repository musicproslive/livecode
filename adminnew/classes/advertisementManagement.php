<?php
/**************************************************************************************
Created By 	:Arvind somu 
Created On	:26-08-2011
Purpose		:Advertisement Management
**************************************************************************************/

class advertisementManagement extends siteclass
	{
		function addAdv($values,$mode)
		{
				//echo "hello";print_r ($values,$mode); die();
			$array	=	array("refference_name"=>$_GET['id'],"google_add"=>$values["txtgoogleAdsence"],"advt_mode"=>$mode);			
			if($mode==2)
			{
			
					$ext			=	explode(".",$_FILES['fileAdd']['name']);
					$ext			=	$ext[count($ext)-1];
					$filename		=	"Adv_".strtotime(date("Y-m-d h:i:s")).trim(microtime(1)).".".$ext;
					$path			=	 dirname(__FILE__)."\\".$filename;
					$path			=	 str_replace("classes","Uploads\\advertisement",$path);
					$dirname		=	 str_replace("classes","Uploads\\advertisement",dirname(__FILE__)."\\");
					
					if($_FILES['fileAdd']['name'])
					{
						$upObj			=	$this->create_upload(10,"jpg,png,jpeg,gif,swf");
						$adimg			=	$upObj->copy("fileAdd",$dirname,1,$filename);
						if($adimg)			$upObj->img_resize("190","120",$dirname);
						else 				$this->setPageError($upObj->get_status());
					}
					//move_uploaded_file($_FILES['fileAdd']['tmp_name'],$path);
					$array["image_path"]		=	$filename;
					$array["title"]				=	$values["txtTitle"];
					$array["url"]				=	$values["txtUrl"];
					if(empty($array["image_path"]) || empty($array["title"]) || empty($array["url"])){
						$this->setPageError("Please enter Mandatory fields ");
						return false;
					}	
			}
			
			if($mode==1){
				if(empty($values["txtgoogleAdsence"])){
					$this->setPageError("Please enter Mandatory fields ");
					return false;
				}
			}
			
			$array["height"]				=	$values["height"];
			$array["width"]					=	$values["width"];
			$this->db_insert("tbladvertisement_listing",$array,0);
			$this->setPageError("Sucessfully Added");
			return true;
		}
		
		function addAdvchild($values,$mode)
		{
			
			$array	=	array("refference_name"=>$_GET['id'],"google_add"=>$values["txtgoogleAdsence"],"advt_mode"=>$mode);			
			if($mode==2)
			{
			
					$ext			=	explode(".",$_FILES['fileAdd']['name']);
					$ext			=	$ext[count($ext)-1];
					$filename		=	"Adv_".strtotime(date("Y-m-d h:i:s")).trim(microtime(1)).".".$ext;
					$path			=	 dirname(__FILE__)."\\".$filename;
					$path			=	 str_replace("classes","Uploads\\advertisement",$path);
					$dirname		=	 str_replace("classes","Uploads\\advertisement",dirname(__FILE__)."\\");
					
					if($_FILES['fileAdd']['name'])
					{
						$upObj			=	$this->create_upload(10,"jpg,png,jpeg,gif,swf");
						$adimg			=	$upObj->copy("fileAdd",$dirname,1,$filename);
						if($adimg)			$upObj->img_resize("190","120",$dirname);
						else 				$this->setPageError($upObj->get_status());
					}
					//move_uploaded_file($_FILES['fileAdd']['tmp_name'],$path);
					$array["image_path"]		=	$filename;
					$array["title"]				=	$values["txtTitle"];
					$array["url"]				=	$values["txtUrl"];
					if(empty($array["image_path"]) || empty($array["title"]) || empty($array["url"])){
						$this->setPageError("Please enter Mandatory fields ");
						return false;
					}	
			}
			
			if($mode==1){
				if(empty($values["txtgoogleAdsence"])){
					$this->setPageError("Please enter Mandatory fields ");
					return false;
				}
			}
			
			//$array["height"]				=	$values["height"];
			//$array["width"]					=	$values["width"];
			//print_r ($array); die();
			$this->db_insert("tblchildadvertisement_listing",$array,0);
			$this->setPageError("Sucessfully Added");
			return true;
		}
		
		// fynction to get the adv by id
		
		function listAdvById($id) 
		{
			$query			=	"SELECT `advt_mode`,`image_path` FROM `tbladvertisement_listing` WHERE `advertisementlisting_id`=$id";
			$resultArray	=	$this->getdbcontents_sql($query,1);	
			return $resultArray;
		}
		function listAdvchildById($id) 
		{
			$query			=	"SELECT `advt_mode`,`image_path` FROM `tblchildadvertisement_listing` WHERE `advertisementlisting_id`=$id";
			$resultArray	=	$this->getdbcontents_sql($query,1);	
			return $resultArray;
		}
		
		// function to un set images for advitsment 
		
		function unsetImage($name)
		{
			 $path	=	str_replace("classes","Uploads\\advertisement\\",dirname(__FILE__).$name);
			 unlink($path);
		}
		
		// function to get  the  advismet list 
		
		function getAddByRefference($refName)
		{
		
			$query			=	"SELECT * FROM `tbladvertisement_listing` WHERE `refference_name`='$refName' AND `status`=1 ";
			$resultArray	=	$this->getdbcontents_sql($query,0);	
			/*if(empty($resultArray))
			{
				$resultArray[0]["status"]			=	1;
				$resultArray[0]["google_add"]		=	"<img src=\"images/no-advertise.jpg\" />";
			}*/
			foreach ($resultArray as $key=>$val)
				 $resultArray[$key]["google_add"]		=	html_entity_decode($val["google_add"]);
			return $resultArray;
		}
		function getAddchildByRefference($refName)
		{
		
			$query			=	"SELECT * FROM `tblchildadvertisement_listing` WHERE `refference_name`='$refName' AND `status`=1 ";
			$resultArray	=	$this->getdbcontents_sql($query,0);	
			/*if(empty($resultArray))
			{
				$resultArray[0]["status"]			=	1;
				$resultArray[0]["google_add"]		=	"<img src=\"images/no-advertise.jpg\" />";
			}*/
			foreach ($resultArray as $key=>$val)
				 $resultArray[$key]["google_add"]		=	html_entity_decode($val["google_add"]);
			return $resultArray;
		}
	}
	?>