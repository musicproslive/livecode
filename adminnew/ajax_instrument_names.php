<?php
require_once 'init.php';err_status("init.php included");
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
require "library/MysqlAdapter.php";
$db = new MysqlAdapter();
$sql_ins_names="select instrument_id, name from tblinstrument_master  where (name like '%".$_REQUEST['term']."%')";
$insturments=$db->ExecuteQuery($sql_ins_names);
foreach($insturments as $instrument)
{
	$row['id']=$instrument['instrument_id'];
	$row['label']=$instrument['name'];
	$row['value']=$instrument['name'];
	$output[]=$row;
}

echo json_encode($output);


?>