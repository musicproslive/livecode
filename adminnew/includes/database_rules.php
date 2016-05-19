<?php
/**************************************************************************************
Created by :Lijesh
Created on :15-07-2011
Purpose    :defining database rules
**************************************************************************************/
$dBaseRules	=	array();

//Lijesh >>
$dBaseRules["tblcourse_master"]["course_title"]	= array(array("emptyCheck",0,0,"Please enter course title"));
$dBaseRules["tblcourse_master"]["course_instrument_id"]	= array(array("emptyCheck",0,0,"Please select an instrument"));
$dBaseRules["tblcourse_master"]["course_start_date"] =	array(array("emptyCheck",0,0,"Please enter start date"),array("dateCheck","","","Please enter a valid start date"));
$dBaseRules["tblcourse_master"]["course_end_date"]	= array(array("emptyCheck",0,0,"Please enter end date"),array("dateCheck","","","Please enter a valid end date"));
$dBaseRules["tblcourse_master"]["number_of_class"]	=	array(array("emptyCheck",0,0,"Please enter number of classes"),array("numberCheck",1,1000,"Please enter a valid number classes"));
$dBaseRules["tblcourse_master"]["class_duration"]	= array(array("emptyCheck",0,0,"Please enter class duration"));
$dBaseRules["tblcourse_master"]["default_start_time"]	= array(array("emptyCheck",0,0,"Please enter default start time"));
$dBaseRules["tblcourse_master"]["max_attendance"]	=	array(array("emptyCheck",0,0,"Please enter maximum attendance"),array("numberCheck",1,100,"Please enter valid attendance"));
$dBaseRules["tblcourse_master"]["course_fee"]	= array(array("emptyCheck",0,0,"Please enter course fee"));

$dBaseRules["tbluser_login"]["user_name"]	=	array(array("emptyCheck",'0','0',"Please enter your username as email address"),array("uniqueCheck","","","Email already exists. Please provide another email address"),array("emailCheck",6,0,"Please enter a valid email address") );

$dBaseRules["tbluser_class"]["class_name"]	= array(array("emptyCheck",0,0,"Please enter class title"));
$dBaseRules["tbluser_class"]["description"]	= array(array("emptyCheck",0,0,"Please enter class description"));

$dBaseRules["tblevents"]["event_name"]	= array(array("emptyCheck",0,0,"Please enter event name"));
$dBaseRules["tblevents"]["event_start"]	= array(array("emptyCheck",0,0,"Please enter start date"));

// Lijesh <<


/*$dBaseRules["pod_coupon"]["subcategory_id"]					=	array(array("emptyCheck",0,0,"Please select a sub-category"),array("idCheck",1,0,"Please select a sub-category"));
$dBaseRules["pod_coupon"]["name"]							=	array(array("emptyCheck",0,0,"Please enter a coupon name"),array("nameCheck",2,200,"Please enter a valid coupon name"));
$dBaseRules["pod_coupon"]["caption"]						=	array(array("emptyCheck",0,0,"Please enter a coupon caption"),array("captionCheck",2,500,"Please enter a valid coupon caption"));
//$dBaseRules["vod_deal"]["description"]					=	array(array("emptyCheck",0,0,""),array("descriptCheck",10,0,""));
//$dBaseRules["pod_coupon"]["cost"]							=	array(array("emptyCheck",0,0,"Please enter a coupon cost"),array("priceCheck",1,15,"Please enter a valid coupon cost"));
$dBaseRules["pod_coupon"]["activation_date"]				=	array(array("emptyCheck",0,0,"Please enter an activation date"),array("dateCheck","","","Please enter a valid activation date"));
$dBaseRules["pod_coupon"]["coupon_days"]					=	array(array("emptyCheck",0,0,"Please enter number of coupon days"),array("numberCheck",1,15,"Please enter a valid number of coupon days"));
//$dBaseRules["vod_deal"]["rules"]							=	array(array("descriptCheck",1,0,"Please enter valid rules"));
//$dBaseRules["vod_deal"]["highlights"]						=	array(array("descriptCheck",1,0,"Please enter valid highlights"));
$dBaseRules["pod_coupon"]["image"]							=	array(array("emptyCheck",0,0,"Please upload a coupon image"),array("imageCheck",4,0,"Please upload a valid coupon image"));
$dBaseRules["pod_coupon"]["expired_date"]					=	array(array("emptyCheck",0,0,"Please enter expiry date"));
$dBaseRules["pod_coupon"]["coupon_expiry_date"]				=	array(array("emptyCheck",0,0,"Please enter coupon expiry date"),array("dateCheck","","","Please enter a valid coupon expiry date"));
$dBaseRules["pod_coupon"]["vod_deal_id"]					=	array(array("idCheck",1,0,"Please select a deal"));*/


?>
