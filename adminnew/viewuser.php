<?php
require_once 'init.php';err_status("init.php included");

if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
//error_reporting(E_ALL);
//Required library files
require "library/MysqlAdapter.php";
 
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();
//Top Advertisement Management in header

$user_det_sql="select tblusers.*,tbluser_login.*, tbluser_address.*,date_format(dob,'%m/%d/%Y')as dobs,date_format(accepted_date,'%m/%d/%Y %h:%i:%s')as acc,tbl_social_media_links.*,tblusers.user_id as userid from tblusers inner join tbluser_login on tblusers.login_id=tbluser_login.login_id left join tbluser_address on tblusers.user_id=tbluser_address.user_id left join tbl_social_media_links on tbl_social_media_links.user_id=tblusers.user_id  where tbluser_login.login_id=".$_GET['login_id'];
$user_det=$db->ExecuteQuery($user_det_sql);
$countries=$db->ExecuteQuery("select * from tblcountries order by dsp_order asc");
$timezones=$db->ExecuteQuery("select * from tbltime_zones where is_active=1");
$user_info=$db->ExecuteQuery("select * from user_info where email='".$user_det[0]['user_name']."'");
$other_profile_data="select * from tbl_profile_other_data where user_id=".$user_info[0]['user_id']." and other_data_code='res'";
$odata=$db->ExecuteQuery($other_profile_data);
$sql="select * from tbluser_activation where login_id=".$_GET['login_id'];
$docs=$db->ExecuteQuery($sql);
//instruments_query
$inst_sql="select instrument_id from tbluser_instruments where user_id=".$user_det[0]['userid']." and is_deleted=0";
$inst_list=$db->ExecuteQuery($inst_sql);
for($i=0;$i<count($inst_list);$i++)
{
	$inst_list_arr[$i]=$inst_list[$i]['instrument_id'];
}

$all_ins_sql="select instrument_id, name, instrument_image from tblinstrument_master where is_deleted = 0 order by name asc";
$all_ins=$db->ExecuteQuery($all_ins_sql);


$advance_approved_levels=$db->ExecuteQuery("select tbl_approve_levels.*,tblinstrument_master.name from tbl_approve_levels left join tblinstrument_master on tblinstrument_master.instrument_id=tbl_approve_levels.instrument_id where approved=0 and instructor_id=".$user_det[0]['userid']);
$ins=$db->ExecuteQuery($sql);
    require_once('layout/accountlayout/viewusercont.php');
 ?>