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



if (!empty($_FILES['Filedata']['tmp_name'])) {

			list($width, $height, $type, $attr) = @getimagesize($_FILES["Filedata"]["tmp_name"]);

			$flag		=	1;
			/*if($width>500 || $height>500){
				echo "2";
				exit;
			}
			if($width<100 || $height<100){
				echo 1;
				exit;
			}*/


	$filename		=	"Vod_".strtotime(date("Y-m-d h:i:s")).trim(microtime(1));
	$ext			=	explode(".",$_FILES['Filedata']['name']);
	$ext			=	$ext[count($ext)-1];
	$_FILES['Filedata']['name']=$filename.".".$ext;

	$tempFile 	= $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
	$_SESSION['imageSaved']	=	 "images/temp/".$_FILES['Filedata']['name'];
	move_uploaded_file($tempFile,$targetFile);
	str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
	echo  $_SESSION['imageSaved'];
	exit;
}
?>