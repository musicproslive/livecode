<?php
require_once 'init.php';err_status("init.php included");
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
 require_once('layout/dataanalysis/stutrackcont.php');
 ?>