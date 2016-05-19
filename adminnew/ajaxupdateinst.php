<?php
require_once 'init.php';err_status("init.php included");
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
require "library/MysqlAdapter.php";
$db = new MysqlAdapter();
$sql_inst="select * from tblinstrument_master where instrument_id=".$_REQUEST['inst_id'];
$instrument=$db->ExecuteQuery($sql_inst);
$is_deleted=0;
$is_deleted_update=0;
$is_deleted=$instrument[0]['is_deleted'];
if($is_deleted==0)
{
	$is_deleted_update=1;
}
$db->ExecuteQuery("update tblinstrument_master set is_deleted=".$is_deleted_update." where instrument_id=".$_REQUEST['inst_id']);
if($is_deleted_update==1)
{
	echo "This instrument removed successfully.";
}
else
{
	echo "This instrument added successfully.";
}
?>