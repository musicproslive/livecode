<?php
require_once 'init.php';err_status("init.php included");


//error_reporting(E_ALL);
//Required library files
require "library/MysqlAdapter.php";
 
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();
if($_GET['login_id']!=""&&$_GET['user_id']!="")
{
	$chk_classes="select * from tblcourses where instructor_id=".$_GET['user_id'];
	$clases=$db->ExecuteQuery($chk_classes);
	if(count($clases)>0)
	{
		$db->ExecuteQuery("update tbluser_login set is_deleted=1 where login_id=".$_GET['login_id']);
		$db->ExecuteQuery("update  tblusers set is_deleted=1 where user_id=".$_GET['user_id']);
	}
	else
	{
	$db->ExecuteQuery("delete from tbluser_instruments where user_id=".$_GET['user_id']);
	$db->ExecuteQuery("delete from tbluser_login where login_id=".$_GET['login_id']);
	$db->ExecuteQuery("delete from tblusers where user_id=".$_GET['user_id']);
	}
}
