<?php
/**************************************************************************************
Created By 	:	Sreeraj
Created On	:	25-10-2010
Purpose		:	Initial functions
****************************************************************************************/
ob_start();
ini_set ( 'session.save_path', $_SERVER ["DOCUMENT_ROOT"] . '/adminnew/session_dir' );
session_start();
if(!isset($_SESSION['MY_SERVER_GENERATED_THIS_SESSION']))
{
	session_unset();
	session_destroy();
	session_write_close();
	session_start();
	//session_regenerate_id(true);
}

$_SESSION['MY_SERVER_GENERATED_THIS_SESSION'] = true;


if($_SERVER['HTTP_HOST']=="192.168.0.17:8081") //local
	{
		//livemusictutor
		$projectPath	=	$_SERVER["DOCUMENT_ROOT"]."/livemusictutor/";
		$runningPath	=	$_SERVER["DOCUMENT_ROOT"]."/livemusictutor/admin/";
	}
else
	{
		$projectPath	=	$_SERVER["DOCUMENT_ROOT"]."/adminnew/";
		$runningPath	=	$_SERVER["DOCUMENT_ROOT"]."/adminnew/";
	}
//error handling
set_error_handler("customError");//setting error handler
error_reporting(~E_NOTICE);
//error_reporting(E_ALL);
//ini_set("display_errors","1");
//including necessary files

require_once	($projectPath.'config.php');						err_status("Config Included");
require_once	($projectPath.'includes/db.php');					err_status("Db connected");
require_once	($projectPath.'libs/smarty/libs/Smarty.class.php');	err_status("Smarty Class Included");
require_once	($projectPath.'library/dbclass.php');				err_status("Db Class dbclass.php Included");
require_once	($projectPath.'library/siteclass.php');				err_status("site Class siteclass.php Included");
require_once	($projectPath.'library/modelclass.php');			err_status("Model Class modelclass.php Included");
require_once 	($projectPath.'includes/includedfiles.php');		err_status("includedfiles.php included");
require_once 	($projectPath.'includes/database_rules.php');		err_status("database_rules.php included");

//smarty object creation
$smarty					= 	new Smarty;								err_status("Smarty class object 'smarty' created");
$smarty->compile_check	= 	true;

//dbclasss and siteclass
$cls_db		=	new sdbclass;	err_status("Db class object 'cls_db' created");
$cls_site	=	new siteclass("livemusictutor.com","sess_podadmin","cls_db","vod_admin");	err_status("site class object 'cls_site' created");

//time zone settings
if(WHERE_AM_I	==	"online")
	{
		date_default_timezone_set(LMT_SERVER_TIME_ZONE);//setting time zone ---> Pacific/Honolulu
		mysql_query("SET time_zone = '".LMT_SERVER_TIME_ZONE_OFFSET."'");//MY SQL

	}
else
	{
		//time zaone settings for php and mysql
		date_default_timezone_set(LMT_SERVER_TIME_ZONE);//setting time zone ---> Pacific/Honolulu
		mysql_query("SET time_zone = '".LMT_SERVER_TIME_ZONE_OFFSET."'");//MY SQL
	}

//setting error status
if($_REQUEST['debug']	==	"1") $_SESSION['debug']	=	"1";
if($_REQUEST['debug']	==	"0") $_SESSION['debug']	=	"0";


$smarty->assign("cls_db",$cls_db);
$smarty->assign("cls_site",$cls_site);


function header_view($title="")
	{
		if(!$title)	$title	=	"";
		define("_HEAD_TITLE",$title);
		include("header.php");
	}
function customError($error_level,$error_message,$error_file,$error_line,$error_context)
 	{
	 	if($error_level	<>	8)
	 		{
				 if($_SESSION['debug'])
			 		{
						echo "<br><b>error_level:</b> : $error_level";
					 	echo "<br><b>error_message:</b> : $error_message";
					 	echo "<br><b>error_file:</b> : $error_file";
					 	echo "<br><b>error_line:</b> : $error_line";
					}
			}
	}
function err_status($msg)
	{
		if($_SESSION['debug'])	echo "<br>".$msg;
	}
function loadModelClass($session=true,$page="")
	{
		$modFolder	=	"models";
		if(!trim($page))	$page	=	siteclass::getPageName();
		$fileArray	=	pathinfo($page);
		require_once($modFolder."/".$fileArray["basename"]);
		$obj		=	 new $fileArray["filename"];
//	print_r($fileArray);
		if($session	==	true)
			{
				$obj	=	$obj->getSessionObj()?$obj->getSessionObj():$obj;
				$obj->clearAction();
				if($obj->getRealAction())
					{
						$obj->findAction();
					}
			}
		return $obj;
	}
?>