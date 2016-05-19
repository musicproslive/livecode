<?php
ob_start();
session_start();
require("../../includes/database_rules.php");
require("../../includes/db.php");
require("../../library/dbclass.php");
require("../../library/siteclass.php");
require("../../library/modelclass.php");

$cls		=	 new sdbclass();
$util		=	 new siteclass();
//$mod		=	 new modelclass();
	
/*echo '<pre>';	
print_r($_POST);
print_r($_GET);*/

if (!empty($_FILES['Filedata']['tmp_name'])) {	
			$originalName		=	$_FILES['Filedata']['name'];				
			list($width, $height, $type, $attr) = @getimagesize($_FILES["Filedata"]["tmp_name"]);			
			$flag		=	1;	
			$filename		=	"Album_".strtotime(date("Y-m-d h:i:s")).trim(microtime(1));
			$ext			=	explode(".",$_FILES['Filedata']['name']);
			$ext			=	$ext[count($ext)-1];
			$_FILES['Filedata']['name']=$filename.".".$ext;
		
			$tempFile 	= $_FILES['Filedata']['tmp_name'];		
			$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
			$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
			move_uploaded_file($tempFile,$targetFile);
			//str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
			echo  $filename.".".$ext;
			exit;
}

?>