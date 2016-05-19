<?php
require_once 'init.php';err_status("init.php included");
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
//error_reporting(E_ALL);
//ini_set("display_errors","1");
require "library/MysqlAdapter.php";
 
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();
if($_POST['instuname']!="")
{
	$sql_ins="insert into tblinstrument_master set instrument_group_id=8, name='".$_POST['instuname']."', dsp_order=2, is_deleted=0, created=now(),created_by=".$_SESSION['admin_id'];
	$db->ExecuteQuery($sql_ins);
}
$sql_list="SELECT instrument_id AS key_val, name AS val, is_deleted FROM tblinstrument_master  ORDER BY val asc";

$instruments=$db->ExecuteQuery($sql_list);

    require_once('layout/sitelayout/instrumentscont.php');
	if($_POST['instuname']!="")
	{
		echo "<script> alert('Category added successfully.')</script>";
	}
 ?>