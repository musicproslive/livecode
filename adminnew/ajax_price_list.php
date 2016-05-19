<?php
require_once 'init.php';err_status("init.php included");
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
require "library/MysqlAdapter.php";
$db = new MysqlAdapter();
$sql_price_list="select id,cost from tblcourse_prices where course_type=3 and duration=".$_REQUEST['duration']." and status=1";
$prices=$db->ExecuteQuery($sql_price_list);
$output=$prices[0]['id'].",".$prices[0]['cost'];
	

echo $output;
?>