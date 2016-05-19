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

for($i=0;$i<50;$i++)
{
	$promocode=strrand(6);
	$insert_sql_promo="insert into promocodes set promocode='".$promocode."', code_status=0, date_created=now()";
	//echo $insert_sql_promo;
	$db->ExecuteQuery($insert_sql_promo);
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