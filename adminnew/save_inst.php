<?php
require_once 'init.php';err_status("init.php included");


//error_reporting(E_ALL);
//Required library files
require "library/MysqlAdapter.php";
 
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();
//Top Advertisement Management in header
if($_POST['login_id']!="")
{
	//update user table
	$first_name="";
	$last_name="";
	$dob="";
	$gender="";
	$college="";
	$country_id=0;
	$timezone_id=0;
	$about_me="";
	$welcome_video="";
	$email="";
	$vanity_url="";
	$phone="";
	$fb="";
	$tw="";
	$yt="";
	$li="";
	$ot="";
	
	$first_name=$_POST['txtFirstName'];
	$last_name=$_POST['txtLastName'];
	$dob=$_POST['dob'];
	$college=$_POST['college'];
	$gender=$_POST['gender'];
	$country_id=$_POST['country'];
	$timezone_id=19;
	$about_me=$_POST['txtAboutMe'];
	$welcome_video=$_POST['welcome_video'];
	$org_loc=$_POST['org_loc'];
	if($welcome_video!="")
	{
		if (strpos($welcome_video,"embed")===false)
		{
			$welcome_video="https://www.youtube.com/embed/".$welcome_video;
		}
		
	}
	
	$update_tblusers="update tblusers set first_name='".$first_name."',
	                                      last_name='".$last_name."',";
	 if($dob!="")
			{
				$date_arr=explode("/",$dob);
				
			   $update_tblusers.="dob='".$date_arr[2]."-".$date_arr[0]."-".$date_arr[1]."',";
			}   
	if($_POST['ins_id_hid']!="")
	 {
		  $update_tblusers.="  referal_id=".$_POST['ins_id_hid'].",";
	 }
     if($_POST['ref_percent']!="")
     {
		  $update_tblusers.="  referral_per=".$_POST['ref_percent'].",";
	 }		 
	 $update_tblusers.="  gender='".$gender."',
	  college='".$college."',
	 
	  time_zone_id=".$timezone_id.",
	  about_me='".addslashes($about_me)."',
	    org_loc='".$org_loc."',
	  welcome_video_link='".$welcome_video."'
	  where user_id=".$_POST['user_id'];
	
	$db->ExecuteQuery($update_tblusers);
	
	//update tbluser_login
	$email=$_POST['txtUserName'];
	$vanity_url=$_POST['txtPerUrl'];
	$update_login="update tbluser_login set user_name='".$email."', Vanity_URL='".$vanity_url."' where login_id=".$_POST['login_id'];
		$db->ExecuteQuery($update_login);
    
    //tbluser_address
   	  $phone=str_replace(")","",str_replace("(","",str_replace("-","",$_POST['txtPhoneNumber'])));
	  $find_address_exist=$db->ExecuteQuery("select * from tbluser_address where user_id=".$_POST['user_id']);
	  if(count($find_address_exist)>0)
	  {
		  $update_phone="update tbluser_address set phone1='".$phone."' where user_id=".$_POST['user_id'];
	  }
	  else
	  {
		  $update_phone="insert into tbluser_address set phone1='".$phone."' , user_id=".$_POST['user_id'];
	  }
	  
		$db->ExecuteQuery($update_phone);
		
	//tbl_social_media_links	
	  $fb=$_POST['txtsocialfb'];
	  $tw=$_POST['txtsocialtw'];
	  $yt=$_POST['txtsocialyu'];
	  $li=$_POST['txtsocialli'];
	  $ot=$_POST['txtsocialot'];
	  $select_social=$db->ExecuteQuery("select * from tbl_social_media_links where user_id=".$_POST['user_id']);
	  if(count($select_social)>0)
	  {
		  
	 
	  $update_social="update tbl_social_media_links set facebook='".$fb."', twitter='".$tw."', youtube_channel='".$yt."',
	                              linkedin='".$li."',url='".$ot."' where user_id=".$_POST['user_id'];
	  }
	  else
	  {
		  $update_social="insert into tbl_social_media_links set facebook='".$fb."', twitter='".$tw."', youtube_channel='".$yt."',
	                              linkedin='".$li."',url='".$ot."'";
	  }
								  
					  
	  							  
	$db->ExecuteQuery($update_social);
		
		
	if(isset($_POST['levs']))
{
	$sel_ins=$_POST['ins'];
	$sel_lev=$_POST['levs'];
	for($jv=0;$jv<count($sel_lev);$jv++)
		{
			unset($exp);
			$exp=explode("_",$sel_lev[$jv]);
			$selected_instumas[$jv]=$exp[1];
		}
	//first remove the unchecked elements
	$all_ins_prev=$db->ExecuteQuery("select instrument_id from tbluser_instruments where user_id=".$_POST['user_id']." and is_deleted=0");
	$all_levs_prev=$db->ExecuteQuery("select distinct(concat(level_id,'_', instrument_id))as ins from tbl_instructor_ins_levels where instructor_id=".$_POST['user_id']." and level_id!=7");
	//print_r(array_unique($selected_instumas));
	for($k=0;$k<count($all_ins_prev);$k++)
		{
			
			$id=$all_ins_prev[$k]['instrument_id'];
			//echo "insidg:".$all_ins_prev[$k]['instrument_id']."<br>";
			
			if(!in_array(intval($all_ins_prev[$k]['instrument_id']),$selected_instumas))
			{
				$up_sql="update  tbluser_instruments set is_deleted=1 where user_id=".$_POST['user_id']." and
			                                               instrument_id=".$all_ins_prev[$k]['instrument_id']."
														   ";
	            //echo "sql:".$up_sql."<br>";													   
					$db->ExecuteQuery($up_sql);
			}
		}
		
	
	for($j=0;$j<count($sel_lev);$j++)
		{
			unset($exp);
			$exp=explode("_",$sel_lev[$j]);
			$selected_ins[$j]=$exp[1];
			
			$find_ins_sql="select instrument_id from tbluser_instruments where user_id=".$_POST['user_id']." and is_deleted=0 and instrument_id=".$exp[1];
			//if($exp[1]==2)
			  //echo "sql:".$find_ins_sql;
			$finds=$db->ExecuteQuery($find_ins_sql);
			if($finds[0]['instrument_id']=="")
			{
					$ins_sql="insert into tbluser_instruments set user_id=".$_POST['user_id'].",
			                                               instrument_id=".$exp[1].",
														   is_deleted=0,created=now()";
						//if($exp[1]==2)								   
						// echo "ins_sql;".$ins_sql	;							   
					$db->ExecuteQuery($ins_sql);
			}
			else
			{
				$ins_sql="update  tbluser_instruments set is_deleted=0 where user_id=".$_POST['user_id']." and 
			                                               instrument_id=".$exp[1]
														  ;
						//if($exp[1]==2)								   
						// echo "ins_sql;".$ins_sql	;							   
					$db->ExecuteQuery($ins_sql);
			}
			
			
		}
		
	//exit();
}
if($_POST['w9_form_upload']!="")
{
	$chk_w9="select * from tbluser_activation where login_id=".$_POST['login_id'];
	$w9_det=$db->ExecuteQuery($chk_w9);
	if(count($w9_det)>0)
	//if($w9_det[0]['w9form_name']!="" && $w9_det[0]['w9form_name']!='NULL')
	{
		$update_sql="update tbluser_activation set w9form_name='".$_POST['w9_form_upload']."', w9verified=1 where login_id=".$_POST['login_id'];
		
		
		$db->ExecuteQuery($update_sql);
	}
	else
	{
		$ins_w9_sql="insert into tbluser_activation set w9form_name='".$_POST['w9_form_upload']."', w9verified=1 , login_id=".$_POST['login_id'];
		
		$db->ExecuteQuery($ins_w9_sql);
	}
}
if($_POST['getting_paid']!="")
{
	$chk_dd="select * from tbluser_activation where login_id=".$_POST['login_id'];
	$dd_det=$db->ExecuteQuery($chk_dd);
	if(count($dd_det)>0)
	{
		$update_sql="update tbluser_activation set dfform_name='".$_POST['getting_paid']."', dfverified=1 where login_id=".$_POST['login_id'];
		
		$db->ExecuteQuery($update_sql);
	}
	else
	{
		$ins_dd_sql="insert into tbluser_activation set dfform_name='".$_POST['w9_form_upload']."', dfverified=1 , login_id=".$_POST['login_id'];
		
		$db->ExecuteQuery($ins_dd_sql);
	}
	
}
if($_POST['resume_upload']!="")
{
	$chk_res="select * from tbl_profile_other_data where user_id=".$_POST['res_user_id']." and other_data_code='res'";
	//echo $chk_res;
	$res_det=$db->ExecuteQuery($chk_res);
	if(count($res_det)>0)
	//if($res_det[0]['value']!="" && $dd_det[0]['value']!='NULL')
	{
		$update_sql="update tbl_profile_other_data set value='".$_POST['resume_upload']."' where user_id=".$_POST['res_user_id']." and other_data_code='res'";
		//echo "res".$update_sql;
		$db->ExecuteQuery($update_sql);
	}
	else
	{
		$ins_res_sql="insert into tbl_profile_other_data set value='".$_POST['resume_upload']."', user_id=".$_POST['res_user_id'].", other_data_code='res'";
		$db->ExecuteQuery($ins_res_sql);
	}
	
}


if($_POST['save']=="approve")
{
	  $approve="update tbluser_login set admin_authorize=1, activation_pro=1,is_first=0 where login_id=".$_POST['login_id'];
	  $db->ExecuteQuery($approve);
}	
if($_POST['save']=="reset")
{
	 $reset="update tbluser_login set authorized=0,accepted_date='' where login_id=".$_POST['login_id'];
	 $db->ExecuteQuery($reset);
}
}
//exit();
header("location:viewinstructor.php?login_id=".$_POST['login_id']);
exit();
?>