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
$day=date("d");
if($_POST['months_sel']=="")
{
	$month=date("m");
}
else
{
	$month=$_POST['months_sel'];
}
if($_POST['years_sel']=="")
{
	$year=date("Y");
}
else
{
	$year=$_POST['years_sel'];
}


$months_arr_ord=array('January','February','March','April','May','June','July','August','September','October','November','December');
$months_select="";
$months_select.="<select name='months_sel' id='months_sel'  >";
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
$years_select.="<select name='years_sel'  id='years_sel' >";
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
if($_POST['payperiod']!="")
{
	$days_week=$_POST['payperiod'];
}
else
{
	$days_week=date("j");
}
if($days_week<=15)
{
	$week_cond=" and day(tblcourses.start_date)>=1 and day(tblcourses.start_date)<=15 ";
}
else
{
	$week_cond=" and day(tblcourses.start_date)>=16 and day(tblcourses.start_date)<=31 ";
}

$total_lessons_completed_biweek_sql="select tblcourses.*,COUNT(tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,tblcourse_prices.cost as cost from tblcourses  left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourse_prices.id=tblcourses.price_code  where  course_status_id=4 and month(tblcourses.start_date)=".$month." and year(tblcourses.start_date)=".$year." ".$week_cond."  GROUP BY tblcourses.course_id";

$lessons_completed_biweek=$db->ExecuteQuery($total_lessons_completed_biweek_sql);
$total_lessons_completed_biweek=0;
$total_lessons_completed_biweek=count($lessons_completed_biweek);
$total_completed_biweek_amt=0.00;
$total_payout_biweek=0.00;
for($bi=0;$bi<count($lessons_completed_biweek);$bi++)
{
	$total="";
	$total_cost="";
	
	if($lessons_completed_biweek[$bi]['course_type_id']==1 || $lessons_completed_biweek[$bi]['course_type_id']==4 || $lessons_completed_biweek[$bi]['course_type_id']==6)
	{
		$total=$lessons_completed_biweek[$bi]['cost']*0.6;
		$total_payout_biweek+=$total;
		$total_completed_biweek_amt+=$lessons_completed_biweek[$bi]['cost'];
	}
	else
	{
		$total_cost=$lessons_completed_biweek[$bi]['tot_enrolled_new']*$lessons_completed_biweek[$bi]['cost'];
		$total_completed_biweek_amt+=$total_cost;
		if($lessons_completed_biweek[$bi]['tot_enrolled_new']<=4)
		{
			
			$total=$total_cost*0.7;
			$total_payout_biweek+=$total;
			
		}
		elseif($lessons_completed_biweek[$bi]['tot_enrolled_new']<=8)
		{
			$total=$total_cost*0.6;
			$total_payout_biweek+=$total;
		}
		elseif($lessons_completed_biweek[$bi]['tot_enrolled_new']>=8)
		{
			$total=$total_cost*0.5;
			$total_payout_biweek+=$total;
		}
	}
}
$lmt_earnings=$total_completed_biweek_amt-$total_payout_biweek;


include('header.php');
  
   require_once('layout/financelayout/payrollcont.php');
   
 include('footer.php');