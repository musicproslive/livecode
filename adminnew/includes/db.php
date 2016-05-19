<?php
/**************************************************************************************
Created by :Sreeraj
Created on :25-10-2010
Purpose    :Database details page
**************************************************************************************/

/*$cfg ["DB_SERVER"] = "lmt-rds-dev.czqzpdk3xide.us-east-1.rds.amazonaws.com";
$cfg ["DB_USER"] = "lmt_rds_dev"; 
$cfg ["DB_PASSWORD"] = "v3VrCBtuQR2XMPA"; 
$cfg ["DB"] = "livemusic_dev";
*/

/* Point the live Db */
$cfg ["DB_SERVER"] = "musicprolive.csucrfp51gwm.us-west-2.rds.amazonaws.com";
$cfg ["DB_USER"] = "music_pro"; 
$cfg ["DB_PASSWORD"] = "music_20!6"; 
$cfg ["DB"] = "music_pro_live";

/*
$cfg["DB_SERVER"]	=	"lmt-rds-prod.czqzpdk3xide.us-east-1.rds.amazonaws.com";
$cfg["DB_USER"]		=	"lmt_rds_prod";
$cfg["DB_PASSWORD"]	=	"W23p82Pm76R9C5Q";
$cfg["DB"]			=	"livemusic_live";	*/

/*
$cfg["DB_SERVER"]	=	"lmt-rds-prod.czqzpdk3xide.us-east-1.rds.amazonaws.com";
$cfg["DB_USER"]		=	"lmt_rds_prod";
$cfg["DB_PASSWORD"]	=	"W23p82Pm76R9C5Q";
$cfg["DB"]			=	"livemusic_live";*/

/* $con = mysql_pconnect ( $cfg ["DB_SERVER"], $cfg ["DB_USER"], $cfg ["DB_PASSWORD"] ) or die ( "Cannot connect to server".mysql_error() );
mysql_select_db ( $cfg ["DB"], $con ) or die ( "Cannot connect to Database" ); */

$con = mysql_connect ( $cfg ["DB_SERVER"], $cfg ["DB_USER"], $cfg ["DB_PASSWORD"] ) or die ( "Cannot connect to server".mysql_error() );
mysql_select_db ( $cfg ["DB"], $con ) or die ( "Cannot connect to Database ".mysql_error() );

/*
 * try{ $dbConnection = new PDO('mysql:dbname='.$cfg["DB"].';host='.$cfg["DB_SERVER"].';charset=utf8'.,$cfg["DB_USER"],$cfg["DB_PASSWORD"]); } catch(PDOException $e) { echo $e->getMessage(); } $dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); if ($dbConnection->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') { $stmt = $dbConnection->prepare('select * from tblusers', array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true)); } else { die("my application only works with mysql; I should use \$stmt->fetchAll() instead"); }
 */
?>
