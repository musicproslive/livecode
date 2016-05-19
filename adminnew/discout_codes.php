<?php
require_once 'init.php';err_status("init.php included");
require "library/MysqlAdapter.php";
 
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
     if($_POST['dis_code']!="")
	 {
		 if($_POST['dis_id']!="")
		 {
			 $update_sql="update discount_amount_code set promocode='".$_POST['dis_code']."', percentage=".$_POST['percentage']." where id=".$_POST['dis_id'];
			 $db->ExecuteQuery($update_sql);
			 
		 }
		 else
		 {
			  $insert_sql="insert into discount_amount_code set promocode='".$_POST['dis_code']."', percentage=".$_POST['percentage']." , is_active=0, date_created=now() ";
			 $db->ExecuteQuery($insert_sql);
			 $insert='t';
			 
		 }
	 }
    require_once('layout/financelayout/discont.php');
	 if($_POST['dis_id']!="")
	   {
		   echo "<script>alert('Discount Code  Updated Successfully');</script>";
	   }
	    if($insert!="")
	   {
		   echo "<script>alert('Discount Code  Added Successfully');</script>";
	   }
 ?>
