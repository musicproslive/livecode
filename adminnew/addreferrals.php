<?php
require_once 'init.php';err_status("init.php included");
if($_SESSION['admin_id']=="")
{
	   header("location:index.php");	
       exit();	
}
require "library/MysqlAdapter.php";
 
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();



$obj	=	loadModelClass(true,"configureCourse.php");

if($_POST['firstname']!="")
{
	do //create random code.
	{
		$randCode				=	$obj->createRandom(25);
	}
	while($obj->getdbcount_sql("SELECT * FROM tblusers WHERE user_code='$randCode'") > 0);
	$user_code = $randCode;
	$log_id = "SELECT login_id FROM tblusers WHERE login_id=(SELECT MAX(login_id) FROM tblusers)";
	$login = mysql_query($log_id);
	$row = mysql_fetch_array($login);
	foreach($row as $r => $v){
	  $login_id1 = $v;
	}
	$login_id = $login_id1+1;
	$enc_pass=md5($_POST['password']);
	$date = date('Y-m-d H:i:s');
	$time=19;
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$conemail = $_POST['email'];
	if($_POST['user_id']!="")
	{
		$tblusers_post = "UPDATE tblusers set first_name='".$firstname."', last_name='".$lastname."' where user_id=".$_POST['user_id'];
		//echo "sql_user:".$tblusers_post;
		$db->ExecuteQuery($tblusers_post);		
	}
	else
	{
		$tblusers_post = "INSERT INTO
					tblusers (user_code,login_id,first_name, last_name,  created,  state_id,country_id, time_zone_id) VALUES ('$user_code','$login_id','$firstname', '$lastname','$date',  '6', '223', '$time')";
		//echo "sql_user:".$tblusers_post;
		$db->ExecuteQuery($tblusers_post);		
    }	
    if($_POST['login_id']!="")
	{
		
		$tblusers_post1 = "UPDATE tbluser_login set user_name='".$conemail."'";
		if($_POST['password']!="")
		{
			$tblusers_post1.=" , user_pwd='".$enc_pass."'";
		}
		$tblusers_post1.=" where login_id=".$_POST['login_id'];
							
		$db->ExecuteQuery($tblusers_post1);		
	}
    else
    {		
	$tblusers_post1 = "INSERT INTO
					tbluser_login (login_id, user_name, user_pwd,user_group,authorized,user_role,admin_authorize,privacy_policy,created)
					VALUES ('$login_id','$conemail','$enc_pass','1','1','7','1','1','$date')";
	//echo "sql_user_log:".$tblusers_post1;
	$db->ExecuteQuery($tblusers_post1);	
    }	
}
$referrer_det = $db->ExecuteQuery("select tblusers.first_name,tblusers.last_name,tbluser_login.user_name,tblusers.login_id,tblusers.user_id from tblusers inner join tbluser_login on tbluser_login.login_id=tblusers.login_id where user_id=".$_GET['ref_id']);

//get the lesson details
require_once('layout/referral/referrarcont.php');
?>