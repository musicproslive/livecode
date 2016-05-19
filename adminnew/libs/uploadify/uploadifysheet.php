<?php
ob_start();
session_start();
//ini_set('memory_limit', '256M');
//ini_set('upload_max_filesize', '256M');
//ini_set('post_max_size', '256M');
//ini_set('max_execution_time', '300');
//ini_set('max_input_time', '300');


/**/

//print_r($_SESSION);
require("../../includes/database_rules.php");
require("../../includes/db.php");
require("../../library/dbclass.php");
require("../../library/siteclass.php");
require("../../library/modelclass.php");

$cls		=	 new sdbclass();
$util		=	 new siteclass();
//$mod		=	 new modelclass();
	
	$targetFolder = '/Uploads/sheetmusic/'; // Relative to the root
	

if (!empty($_FILES['Filedata']['tmp_name'])) 
	{
		list($width, $height, $type, $attr) = @getimagesize($_FILES["Filedata"]["tmp_name"]);	
		if($_FILES['Filedata']['size']/ 1000000 >1)
			{
				echo "1";
				exit;
			}
		 else
			{	
				$filename		=	"LMT_".strtotime(date("Y-m-d h:i:s")).trim(microtime(1));
				$ext			=	explode(".",$_FILES['Filedata']['name']);
				$ext			=	$ext[count($ext)-1];
				if($ext !='pdf')
					{			
						echo "2";
						exit;
					}
				$tempFile 	= $_FILES['Filedata']['tmp_name'];		
				$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder . '/';
				$targetFile =  str_replace('//','/',$targetPath) . $filename.".".$ext;
				move_uploaded_file($tempFile,$targetFile);
				//str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
				echo $_FILES['Filedata']['name']."/".$filename.".".$ext;
				
				exit;
			}
	}
?>