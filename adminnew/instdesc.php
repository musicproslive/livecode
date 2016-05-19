<?php
require_once 'init.php';err_status("init.php included");
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
require "library/MysqlAdapter.php";
$db = new MysqlAdapter();
if($_POST['instru_id']!="")
{
	$desc=$_POST['content'];
	$title=$_POST['descname'];
	$sql="update tblinstrument_master set description_new='".$desc."',desc_title='".$title."' where instrument_id=".$_POST['instru_id'];
	$db->ExecuteQuery($sql);
	
}

 require_once('layout/sitelayout/instdescont.php');
 if($_POST['instru_id']!="")
{
	 echo "<script>alert('Instrument Descriptions  Updated Successfully');</script>";
}

 ?>