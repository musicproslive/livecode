<?php
require_once 'init.php';err_status("init.php included");
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
require "library/MysqlAdapter.php";
$db = new MysqlAdapter();
$sql_ins_names="select distinct(tbluser_instruments.instrument_id),tblinstrument_master.name from tbluser_instruments left join tblinstrument_master on tbluser_instruments.instrument_id=tblinstrument_master.instrument_id and tblinstrument_master.is_deleted=0 where user_id=".$_REQUEST['ins_id']." and tbluser_instruments.is_deleted=0 order by name asc";
$instruments=$db->ExecuteQuery($sql_ins_names);
foreach($instruments as $instrument)
{
	$output.="<option value='".$instrument['instrument_id']."'>".$instrument['name']."</option>";
}
echo $output;
?>