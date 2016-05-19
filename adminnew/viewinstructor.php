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
if($_POST['login_id']!="")
{
	$sql_up="update tbluser_login set admin_authorize=1,activation_pro=1 where login_id=".$_GET['login_id'];
	$db->ExecuteQuery($sql_up);
	header("location:instructor.php");
	exit();
}
$user_det_sql="select tblusers.*,tbluser_login.*, tbluser_address.*,date_format(dob,'%m/%d/%Y')as dobs,date_format(accepted_date,'%m/%d/%Y %h:%i:%s')as acc,tbl_social_media_links.*,tblusers.user_id as userid,IF(DATEDIFF(now(),last_feature_tran_date)>=0 and tblusers.featured=1 and DATEDIFF(now(),last_feature_tran_date)<=30,1,0)  as diff from tblusers inner join tbluser_login on tblusers.login_id=tbluser_login.login_id left join tbluser_address on tblusers.user_id=tbluser_address.user_id left join tbl_social_media_links on tbl_social_media_links.user_id=tblusers.user_id  where tbluser_login.login_id=".$_GET['login_id'];
$user_det=$db->ExecuteQuery($user_det_sql);
$referrals=$db->ExecuteQuery("select concat(first_name,' ',last_name) as name,referral_per from tblusers where user_id=".$user_det[0]['referal_id']);
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
foreach($all_ins as $ins)
{
	if(in_array($ins['instrument_id'],$inst_list_arr))
	{
		$ins['sel']="t";
	}
	else
	{
		$ins['sel']="n";
	}
	
	$all_ins_listed[]=$ins;
}

	$month=date('m');

	$year=date('Y');
	
	$months_arr_ord=array('January','February','March','April','May','June','July','August','September','October','November','December');
$months_select="";
$months_select.="<select name='months_sel' id='months_sel'>";
$months_select.="<option value='0'>Select Month</option>";
for($i=0;$i<count($months_arr_ord);$i++)
{
	$value=0;
	$value=$i+1;
	$selected="";
	if($value<=9)
	{
		$value="0".$value;
	}
	if($month==$value)
	{
		$selected="selected=selected";
	}
	$months_select.="<option value='".$value."'".$selected.">".$months_arr_ord[$i]."</option>";
	
}
$months_select.="</select>";
$years_select="";
$years_select.="<select name='years_sel'  id='years_sel'>";
$years_select.="<option value='0'>Select Year</option>";
for($j=2012;$j<=$year;$j++)
{
	
	$selected="";
	if($year==$j)
	{
		$selected="selected=selected";
	}
	$years_select.="<option value='".$j."'".$selected.">".$j."</option>";
	
}
$years_select.="</select>";


$advance_approved_levels=$db->ExecuteQuery("select tbl_approve_levels.*,tblinstrument_master.name from tbl_approve_levels left join tblinstrument_master on tblinstrument_master.instrument_id=tbl_approve_levels.instrument_id where approved=0 and instructor_id=".$user_det[0]['userid']);
$ins=$db->ExecuteQuery($sql);
    require_once('layout/accountlayout/viewinstcont.php');
 ?>