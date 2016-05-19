<?php

/**************************************************************************************
Done On :26-10-2010
@Author	:Lijesh
Purpose	:Initial variables
**************************************************************************************/
define ( 'WHERE_AM_I', "online" );
$rootURL = "http://www.musicproslive.com/admin/";
$host = "http://www.musicproslive.com/admin/";
$absPath = $_SERVER ["DOCUMENT_ROOT"] . "/admin/";
$rootURLhttps = "https://www.musicproslive.com/admin/";
$httpsHost = "https://www.musicproslive.com/admin/";

//$rootURL = $host = $rootURLhttps = $httpsHost = "http://54.88.208.77/";

//$rootURL = "http://54.88.121.84/";
//$host = "http://54.88.121.84/";
//$rootURLhttps = "http://54.88.121.84/";
//$httpsHost = "http://54.88.121.84/";

if(trim(strtolower($_SERVER["HTTPS"]))	==	"on")	

	{
		$rootURL = $rootURLhttps;
		$host    = $httpsHost;
		define('ROOT_URL', $rootURLhttps);	
		
		if($_SERVER["HTTP_HOST"] == "64.37.53.22")	
			{
				header("location: $httpsHost");
				exit;	
			}	
	}

if(!defined("ROOT_URL"))	define('ROOT_URL',$rootURL);

define('ROOT_ADMIN_URL', $rootURL);

define('ROOT_HOST', $host);

define('ROOT_CURRENT_URL', ROOT_HOST.$_SERVER['REQUEST_URI']);

define('ROOT_ABSOLUTE_PATH', $absPath);

// ini values

ini_set ( "magic_quotes_gpc", "Off" );

// Vendor paging Vars

$vpageArr = array (
		
		"nextCaption" => "Next",
		
		"prevCaption" => "Previous",
		
		"containerTag" => "li" 
)
;

// Customer paging Vars

$cpageArr = array (
		
		"nextCaption" => "Next",
		
		"prevCaption" => "Previous",
		
		"containerTag" => "li" 
)
;
function getCurrentPageName() 

{
	$scriptName = $_SERVER ["SCRIPT_FILENAME"];
	
	$scriptArray = explode ( "/", $scriptName );
	
	return end ( $scriptArray );
}

?>
