<?php
require_once 'init.php';err_status("init.php included");
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
require "library/MysqlAdapter.php";
$db = new MysqlAdapter();
$sql_ins_names="select user_id, concat(first_name,' ',last_name) as name from tblusers inner join tbluser_login on tblusers.login_id=tbluser_login.login_id where (first_name like '%".$_REQUEST['term']."%' or last_name like '%".$_REQUEST['term']."%') and (tbluser_login.user_role=3 or tbluser_login.user_role=7)";
$insturctors=$db->ExecuteQuery($sql_ins_names);
foreach($insturctors as $instructor)
{
	$row['id']=$instructor['user_id'];
	$row['label']=$instructor['name'];
	$row['value']=$instructor['name'];
	$output[]=$row;
}

echo json_encode($output);


?>