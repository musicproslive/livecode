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
	$timezone_id=$_POST['time_zone_id'];
	$about_me=$_POST['txtAboutMe'];
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
	 $update_tblusers.="  gender='".$gender."',
	  college='".$college."',
	 
	  time_zone_id=".$timezone_id.",
	  org_loc='".$org_loc."',
	  about_me='".addslashes($about_me)."'
	  
	  where user_id=".$_POST['user_id'];
	   
	$db->ExecuteQuery($update_tblusers);
	
	//update tbluser_login
	$email=$_POST['txtUserName'];
	$vanity_url=$_POST['txtPerUrl'];
	$update_login="update tbluser_login set user_name='".$email."', Vanity_URL='".$vanity_url."' where login_id=".$_POST['login_id'];
		$db->ExecuteQuery($update_login);
    
    //tbluser_address
   	  $phone=str_replace(")","",str_replace("(","",str_replace("-","",$_POST['txtPhoneNumber'])));
	  $update_phone="update tbluser_address set phone1='".$phone."' where user_id=".$_POST['user_id'];
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
	//print_r($sel_lev);
	$get_all_prev="select instrument_id from  tbluser_instruments where user_id=".$_POST['user_id']." and is_deleted=0";
	$all_prev=$db->ExecuteQuery($get_all_prev);
	for($i=0;$i<count($all_prev);$i++)
	{
		if(!in_array($all_prev['instrument_id'],$sel_lev))
			{
				$update_sql="update tbluser_instruments set is_deleted=0  where  user_id=".$_POST['user_id']." and instrument_id=".$all_prev['instrument_id'];
				$db->ExecuteQuery($update_sql);
			}
	}
	for($j=0;$j<count($sel_lev);$j++)
		{
			unset($exp);
			//$exp=explode("_",$sel_lev[$j]);
			
			$find_ins_sql="select instrument_id from tbluser_instruments where user_id=".$_POST['user_id']." and is_deleted=0 and instrument_id=".$sel_lev[$j];
			
			
			
			$finds=$db->ExecuteQuery($find_ins_sql);
			if($finds[0]['instrument_id']=="")
			{
					$ins_sql="insert into tbluser_instruments set user_id=".$_POST['user_id'].",
			                                               instrument_id=".$sel_lev[$j].",
														   is_deleted=0,created=now()";
					$db->ExecuteQuery($ins_sql);
			}
			
		}
	//exit();
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
header("location:viewuser.php?login_id=".$_POST['login_id']);
exit();
?>