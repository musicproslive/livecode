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
$duration_sql="SELECT * FROM tbllookup_course_duration AS d
								WHERE d.id IN (SELECT DISTINCT (duration) 
												FROM tblcourse_prices AS p
												WHERE p.status = 1) ";
$durations=$db->ExecuteQuery($duration_sql);
$obj	=	loadModelClass(true,"configureCourse.php");
//get the lesson details


if($_POST['ins_id_hid']!="")
{
	if($_POST['course_id']!="")
	{
		$data['course_id']=$_POST['course_id'];
	}
	if($_POST['price_id']!="")
	{
		$data['priceid']=$_POST['price_id'];
	}
	$data['title']=$_POST['instuname'];
	$data['instructor_id']=$_POST['ins_id_hid'];
	$data['instrument_id']=$_POST['instrument'];
	$data['course_type_id']=3;
	$data['channel_link']=$_POST['channel_link'];
	$start_date=explode("/",$_POST['date_added']);
	$data['start_date']=$start_date[2]."-".$start_date[0]."-".$start_date[1];
	$hour=$_POST['hour'];
	$minute=$_POST['min'];
	if($minute<=5)
	{
		$minute="0".$minute;
	}
	if($_POST['amorpm']=="pm")
	{
		if($hour<12)
		{
			$hour+=12;
		}
		if($hour<=9)
		{
			$hour="0".$hour;
		}
	}
	$data['start_time']=$hour.":".$minute.":00";
	$data['duration']=$_POST['duration'];
	
	$data['price_code']=$_POST['fee_id'];
	$data['max_students']=$_POST['max_stu'];
	$data['min_required']=$_POST['min_stu'];
	$data['course_status_id']=1;
	$data['course_type_level']=$_POST['level'];
	if($_POST['course_id']!="")
	{
		
		
		$result=$obj->udateCourse($data);
	}
	else
	{
		$result=$obj->insertCourse($data);
	}
	
	$_GET['courseid']=$data['course_id'];
}
	
if($_GET['courseid']!="")
{
	$sql_get_courses="select * from tblcourses left join tblusers on tblcourses.instructor_id=tblusers.user_id left join tblcourse_prices on tblcourse_prices.id= tblcourses.price_code where course_id=".$_GET['courseid'];
	$course_details=$db->ExecuteQuery($sql_get_courses);
	$sql_ins_names="select distinct(tbluser_instruments.instrument_id),tblinstrument_master.name from tbluser_instruments left join tblinstrument_master on tbluser_instruments.instrument_id=tblinstrument_master.instrument_id and tblinstrument_master.is_deleted=0 where user_id=".$course_details[0]['user_id']." and tbluser_instruments.is_deleted=0 order by name asc";
	$instruments=$db->ExecuteQuery($sql_ins_names);
	
}
$min_max=$db->ExecuteQuery("select max_students, min_students from tbllookup_course_type where id=".$course_details[0]['course_type_id']);											
require_once('layout/dataanalysis/editlesscont.php');
if($result!="")
{
	echo "<script> alert('".$result."')</script>";
}
?>