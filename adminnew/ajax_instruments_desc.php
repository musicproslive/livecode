<?php
require_once 'init.php';err_status("init.php included");
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
require "library/MysqlAdapter.php";
$db = new MysqlAdapter();
$sql_ins_names="select IFNULL(description_new,'') as description_new, IFNULL(desc_title,'') as desc_title from tblinstrument_master  where instrument_id=".$_REQUEST['ins_id'];
$insturments=$db->ExecuteQuery($sql_ins_names);
if(count($insturments)>0)
{
	$output['title']=$insturments[0]['desc_title'];
	$output['description_new']=$insturments[0]['description_new'];
}
else
{
	$output['title']="";
	$output['description_new']="";
}

echo json_encode($output);


?>