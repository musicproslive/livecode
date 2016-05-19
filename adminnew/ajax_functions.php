<?php
/**************************************************************************************
Created by :Lijesh 
Created on :Sep - 06 - 2012
Purpose    :Cource Listing of Tutor
**************************************************************************************/
require_once 'init.php';err_status("init.php included");
ini_set("memory_limit","512M");

//error_reporting(E_ALL);
//Required library files
require "library/MysqlAdapter.php";
 
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();
//Top Advertisement Management in header
$request = $_REQUEST['process'];

if($request=="add_promo")
{
	$promocode=strrand(6);
	$insert_sql_promo="insert into promocodes set promocode='".$promocode."', code_status=0, date_created=now()";
	$db->ExecuteQuery($insert_sql_promo);
	echo "Promocode added successfully";
}
if($request=="delete_dis")
{
	$db->ExecuteQuery("delete from discount_amount_code where id=".$_REQUEST['id']);
		echo "Discount Code deleted  successfully";
}
if($request=="status_dis")
{
	$db->ExecuteQuery("update  discount_amount_code set is_active=".$_REQUEST['status']." where id=".$_REQUEST['id']);
	if($_REQUEST['status']==0)
		echo "Discount Code Activated successfully";
	else
		echo "Discount Code Deactivated successfully";
}
function strrand($length,$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
{
   // Required Variables
   $string = '';
   // Loop
   for($i = 0; $i <= $length-1; $i++)
      $string .= $chars[rand(0,strlen($chars)-1)];
   // Return our random string.
   return $string;
}
?>