<?php
require_once 'init.php';err_status("init.php included");

require "library/MysqlAdapter.php";
 
ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();

$sql="delete from tbl_news_new where id=".$_GET['newsid'];
$db->ExecuteQuery($sql);
header("location:news.php");
exit();


?>