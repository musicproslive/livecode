<?php
require_once 'init.php';err_status("init.php included");
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
//echo "test:".unserialize(base64_decode("czoxMDoiaWcycmduZ3l4ZiI7"));
 require_once('layout/dataanalysis/viewlesscont.php');
 ?>