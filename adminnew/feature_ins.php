<?php
require_once 'init.php';err_status("init.php included");
if(!isset($_SESSION)){
	ob_start();
	session_set_cookie_params(10*60*60,"/","livemusictutor.com",false,true);//,"","",true,true
	ini_set('session.gc_maxlifetime', '36000');
	ini_set('max_upload_filesize', 8388608);
	session_start();
}
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
    require_once('layout/sitelayout/feinstcont.php');
 ?>