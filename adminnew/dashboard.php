<?php
require_once 'init.php';err_status("init.php included");
ini_set("memory_limit","512M");

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
$month=date("m");
$year=date("Y");

$today_users_signup_sql="select count(*) as total from tbluser_login where user_role=4 and month(created)=".$month." and year(created)=".$year." and day(created)=".$day;
$today_users=$db->ExecuteQuery($today_users_signup_sql);

$today_ins_signup_sql="select count(*) as total from tbluser_login where user_role=3 and month(created)=".$month." and year(created)=".$year." and day(created)=".$day;
$today_ins=$db->ExecuteQuery($today_ins_signup_sql);


$total_users_signup_sql="select count(*) as total from tbluser_login where user_role=4 and is_deleted=0";
$total_users=$db->ExecuteQuery($total_users_signup_sql);

$total_kids_signup_sql="select user_id,round(DATEDIFF(CURRENT_DATE,tblusers.dob)/365) as age from tbluser_login inner join tblusers on tblusers.login_id=tbluser_login.login_id where user_role=4 and tbluser_login.is_deleted=0 having age>0 and age<=13 ";
$total_kids=$db->ExecuteQuery($total_kids_signup_sql);

$total_ins_signup_sql="select count(user_id) total from tblusers inner join tbluser_login on   tblusers.login_id=tbluser_login.login_id  where user_role=3 and tbluser_login.is_deleted=0";
$total_ins=$db->ExecuteQuery($total_ins_signup_sql);

//current lessons scheduled
$total_lessons_sche_sql="select tblcourses.*,COUNT(tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,tblcourse_prices.cost from tblcourses  left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourses.price_code=tblcourse_prices.id  where concat(`start_date`,0x20,`start_time`)>=NOW() and (course_status_id=1 or course_status_id=2) GROUP BY tblcourses.course_id having tot_enrolled_new=0";
$lessons_sche=$db->ExecuteQuery($total_lessons_sche_sql);
$current_less_she_amt=0.00;
foreach($lessons_sche as $sche)
{
  $current_less_she_amt+=$sche['cost'];	
}
$total_lessons_sche=0;
$total_lessons_sche=count($lessons_sche);
//current lesson booked
$current_lessons_book_sql="select tblcourses.*,COUNT(tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,tblcourse_prices.cost from tblcourses  left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourses.price_code=tblcourse_prices.id  where date(concat(`start_date`,0x20,`start_time`))>=date(NOW()) and (course_status_id=1 or course_status_id=2) GROUP BY tblcourses.course_id having tot_enrolled_new>0";
$current_lessons_book=$db->ExecuteQuery($current_lessons_book_sql);
$current_less_book_amt=0.00;
foreach($current_lessons_book as $book)
{
  
  if($book['course_type_id']==1||$book['course_type_id']==4 ||$book['course_type_id']==6)
	{
		$current_less_book_amt+=$book['cost'];	
	}	
	else
	{
		$current_less_book_amt+=$book['tot_enrolled_new']*$book['cost'];
	}	
}
$current_lessons_book_count=0;
$current_lessons_book_count=count($current_lessons_book);

//total lessons_sheduled entire table
$total_less_tab_sql="select tblcourses.*,COUNT(tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,tblcourse_prices.cost from tblcourses  left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourses.price_code=tblcourse_prices.id  GROUP BY tblcourses.course_id";
$total_less_tab=$db->ExecuteQuery($total_less_tab_sql);
$total_tab_amt=0.00;
foreach($total_less_tab as $total_tab)
{
	if($total_tab['course_type_id']==1 || $total_tab['course_type_id']==6 || $total_tab['course_type_id']==4)
	{
		$total_tab_amt+=$total_tab['cost'];	
	}	
	else
	{
		if($total_tab['tot_enrolled_new']>0)
		{
		  $total_tab_amt+=$total_tab['tot_enrolled_new']*$total_tab['cost'];
		}
		else
		{
			 $total_tab_amt+=$total_tab['cost'];
		}
	}	
}
$total_tab_count=0;
$total_tab_count=count($total_less_tab);


$total_lessons_booked_sql="select tblcourses.*,COUNT(tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,IFNULL(tblcourse_prices.cost,0.00) as cost,tblcourse_enrollment_transaction.transaction_code,IFNULL(tblcourse_enrollment_transaction.trans_amount,0.00) as trans from tblcourses  left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourse_prices.id=tblcourses.price_code left join tblcourse_enrollment_transaction on tblcourse_enrollments.enrolled_id=tblcourse_enrollment_transaction.enrolled_id  GROUP BY tblcourses.course_id having tot_enrolled_new>0";
$lessons_booked=$db->ExecuteQuery($total_lessons_booked_sql);
$total_lessons_booked=0;
$total_lessons_booked=count($lessons_booked);
$total_booked_amt=0.00;
for($i=0;$i<count($lessons_booked);$i++)
{
	if($lessons_booked[$i]['course_type_id']==1 || $lessons_booked[$i]['course_type_id']==4 || $lessons_booked[$i]['course_type_id']==6 )
	{
		if($lessons_booked[$i]['trans']>0)
		{
			$total_booked_amt+=$lessons_booked[$i]['trans'];
			//echo "kkk".$lessons_booked[$i]['trans'];
		}
		else
		{
		  $total_booked_amt+=$lessons_booked[$i]['cost'];
		}
	}
	else
	{
		
		$group_pay=$db->ExecuteQuery("select IFNULL(sum(trans_amount),0.00) as paid from tblcourse_enrollments left join tblcourse_enrollment_transaction on tblcourse_enrollments.enrolled_id=tblcourse_enrollment_transaction.enrolled_id  where course_id=".$lessons_booked[$i]['course_id']);
		if($group_pay[0]['paid']>0)
		{
			 $total_booked_amt+=$group_pay[0]['paid'];
			 	//echo "k1111".$lessons_booked[$i]['course_id']."/".$group_pay[0]['paid'];
		}
		else
		{
			 $total_booked_amt+=$lessons_booked[$i]['tot_enrolled_new']*$lessons_booked[$i]['cost'];
		}
	}
}

$total_lessons_paid_sql="select tblcourses.*,COUNT(tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,IFNULL(tblcourse_prices.cost,0.00) as cost,tblcourse_enrollment_transaction.transaction_code,IFNULL(tblcourse_enrollment_transaction.trans_amount,0.00) as trans from tblcourses   left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourse_prices.id=tblcourses.price_code left join tblcourse_enrollment_transaction on tblcourse_enrollments.enrolled_id=tblcourse_enrollment_transaction.enrolled_id where tblcourse_enrollments.paid_flag=1  GROUP BY tblcourses.course_id having tot_enrolled_new>0 ";
$lessons_paid=$db->ExecuteQuery($total_lessons_paid_sql);
$total_lessons_paid=0;
$total_lessons_paid=count($lessons_paid);
$total_paid_amt=0.00;
for($p=0;$p<count($lessons_paid);$p++)
{
	if($lessons_paid[$p]['course_type_id']==1 || $lessons_paid[$p]['course_type_id']==4 || $lessons_paid[$p]['course_type_id']==6)
	{
		if($lessons_paid[$p]['trans']>0)
		{
			$total_paid_amt+=$lessons_paid[$p]['trans'];
			//echo "kkk".$lessons_booked[$i]['trans'];
		}
		
	}
	else
	{
		
		$group_pay=$db->ExecuteQuery("select IFNULL(sum(trans_amount),0.00) as paid from tblcourse_enrollments left join tblcourse_enrollment_transaction on tblcourse_enrollments.enrolled_id=tblcourse_enrollment_transaction.enrolled_id  where course_id=".$lessons_paid[$p]['course_id']);
		if($group_pay[0]['paid']>0)
		{
			 $total_paid_amt+=$group_pay[0]['paid'];
			 	//echo "k1111".$lessons_booked[$i]['course_id']."/".$group_pay[0]['paid'];
		}
		
	}
	
}
$toatal_paid_sql="select tblusers.user_id,sum(trans_amount) as spent from tblcourse_enrollment_transaction inner join tblusers on tblusers.user_id=tblcourse_enrollment_transaction.user_id where   paid_flag=1 ";
$paid_tot=$db->ExecuteQuery($toatal_paid_sql);
$total_paid_amt=$paid_tot[0]['spent'];
$total_lessons_cancelled_sql="select tblcourses.*,COUNT(tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,tblcourse_prices.cost as cost from tblcourses  left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourse_prices.id=tblcourses.price_code  where  course_status_id=5  GROUP BY tblcourses.course_id";
$lessons_cancelled=$db->ExecuteQuery($total_lessons_cancelled_sql);
$total_lessons_cancelled=0;
$total_lessons_cancelled=count($lessons_cancelled);
$total_cancelled_amt=0.00;
for($j=0;$j<count($lessons_cancelled);$j++)
{
	$total_cancelled_amt+=$lessons_cancelled[$j]['cost'];
}

$total_lessons_completed_sql="select tblcourses.*,COUNT(tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,IFNULL(tblcourse_prices.cost,0.00) as cost,tblcourse_enrollment_transaction.transaction_code,IFNULL(tblcourse_enrollment_transaction.trans_amount,0.00) as trans from tblcourses   left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourse_prices.id=tblcourses.price_code left join tblcourse_enrollment_transaction on tblcourse_enrollments.enrolled_id=tblcourse_enrollment_transaction.enrolled_id where course_status_id=4  GROUP BY tblcourses.course_id";
$lessons_completed=$db->ExecuteQuery($total_lessons_completed_sql);
$total_lessons_completed=0;
$total_lessons_completed=count($lessons_completed);
$total_completed_amt=0.00;
for($k=0;$k<count($lessons_completed);$k++)
{
	if($lessons_completed[$k]['course_type_id']==1 || $lessons_completed[$k]['course_type_id']==4 || $lessons_completed[$k]['course_type_id']==6)
	{
		if($lessons_completed[$k]['trans']>0)
		{
			$total_completed_amt+=$lessons_completed[$k]['trans'];
			//echo "kkk".$lessons_booked[$i]['trans'];
		}
		
	}
	else
	{
		
		$group_pay=$db->ExecuteQuery("select IFNULL(sum(trans_amount),0.00) as paid from tblcourse_enrollments left join tblcourse_enrollment_transaction on tblcourse_enrollments.enrolled_id=tblcourse_enrollment_transaction.enrolled_id  where course_id=".$lessons_completed[$k]['course_id']);
		if($group_pay[0]['paid']>0)
		{
			 $total_completed_amt+=$group_pay[0]['paid'];
			 	//echo "k1111".$lessons_booked[$i]['course_id']."/".$group_pay[0]['paid'];
		}
		
	}
}

$month=date("m");
$year=date("Y");

$months_arr_ord=array('January','February','March','April','May','June','July','August','September','October','November','December');
$months_select="";
$months_select.="<select name='months_sel' id='months_sel' style='width:120px !important;' onchange='javascript:find_revenue();'>";
$months_select_new.="<select name='months_sel_new' id='months_sel_new' style='width:120px !important;' onchange='javascript:find_revenue_booked();'>";
$months_select.="<option value='0'>Select Month</option>";
$months_select_new.="<option value='0'>Select Month</option>";
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
	$months_select_new.="<option value='".$value."'".$selected.">".$months_arr_ord[$i]."</option>";
	
}

$months_select.="</select>";
$months_select_new.="</select>";
$years_select="";
$years_select.="<select name='years_sel'  id='years_sel' style='width:120px !important;' onchange='javascript:find_revenue();'>";
$years_select_new.="<select name='years_sel_new'  id='years_sel_new' style='width:120px !important;' onchange='javascript:find_revenue_booked();'>";
$years_select.="<option value='0'>Select Year</option>";
$years_select_new.="<option value='0'>Select Year</option>";
for($j=2012;$j<=$year;$j++)
{
	
	$selected="";
	if($year==$j)
	{
		$selected="selected=selected";
	}
	$years_select.="<option value='".$j."'".$selected.">".$j."</option>";
	$years_select_new.="<option value='".$j."'".$selected.">".$j."</option>";
	
}
$years_select.="</select>";
$years_select_new.="</select>";
$days_week=date("j");
if($days_week<=15)
{
	$week_cond=" and day(tblcourses.start_date)>=1 and day(tblcourses.start_date)<=15 ";
}
else
{
	$week_cond=" and day(tblcourses.start_date)>=16 and day(tblcourses.start_date)<=31 ";
}

$total_lessons_completed_biweek_sql="select tblcourses.*,COUNT(tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,tblcourse_prices.cost as cost,day(start_date)as day  from tblcourses  left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourse_prices.id=tblcourses.price_code  where  course_status_id=4 and month(tblcourses.start_date)=".$month." and year(tblcourses.start_date)=".$year."  GROUP BY tblcourses.course_id";

$lessons_completed_biweek=$db->ExecuteQuery($total_lessons_completed_biweek_sql);
$total_lessons_completed_biweek_period1=0;
$total_lessons_completed_biweek_period2=0;

$total_completed_biweek_p1_amt=0.00;
$total_completed_biweek_p2_amt=0.00;
$total_payout_biweek_p1=0.00;
$total_payout_biweek_p2=0.00;
for($bi=0;$bi<count($lessons_completed_biweek);$bi++)
{
	$total="";
	$total_cost="";
	
	if($lessons_completed_biweek[$bi]['course_type_id']==1 ||$lessons_completed_biweek[$bi]['course_type_id']==4 ||$lessons_completed_biweek[$bi]['course_type_id']==6 )
	{
		if($lessons_completed_biweek[$bi]['day']<=15)
		{
			$total_lessons_completed_biweek_period1+=1;
			$total_completed_biweek_p1_amt+=$lessons_completed_biweek[$bi]['cost'];
			$total=$lessons_completed_biweek[$bi]['cost']*0.6;
			$total_payout_biweek_p1+=$total;
		}
		else
		{
			$total_lessons_completed_biweek_period2+=1;
			$total_completed_biweek_p2_amt+=$lessons_completed_biweek[$bi]['cost'];
			$total=$lessons_completed_biweek[$bi]['cost']*0.6;
			$total_payout_biweek_p2+=$total;
		}
		
	}
	else
	{
		$total_cost=$lessons_completed_biweek[$bi]['tot_enrolled_new']*$lessons_completed_biweek[$bi]['cost'];
		if($lessons_completed_biweek[$bi]['day']<=15)
		{
			$total_lessons_completed_biweek_period1+=1;
			$total_completed_biweek_p1_amt+=$total_cost;
			if($lessons_completed_biweek[$bi]['tot_enrolled_new']<=4)
			{
			
				$total=$total_cost*0.7;
				$total_payout_biweek_p1+=$total;
			
			}
			elseif($lessons_completed_biweek[$bi]['tot_enrolled_new']<=8)
			{
				$total=$total_cost*0.6;
				$total_payout_biweek_p1+=$total;
			}
			elseif($lessons_completed_biweek[$bi]['tot_enrolled_new']>=8)
			{
				$total=$total_cost*0.5;
				$total_payout_biweek_p1+=$total;
			}
			
		}
		else
		{
			$total_lessons_completed_biweek_period2+=1;
			$total_completed_biweek_p2_amt+=$total_cost;
			if($lessons_completed_biweek[$bi]['tot_enrolled_new']<=4)
			{
			
				$total=$total_cost*0.7;
				$total_payout_biweek_p2+=$total;
			
			}
			elseif($lessons_completed_biweek[$bi]['tot_enrolled_new']<=8)
			{
				$total=$total_cost*0.6;
				$total_payout_biweek_p2+=$total;
			}
			elseif($lessons_completed_biweek[$bi]['tot_enrolled_new']>=8)
			{
				$total=$total_cost*0.5;
				$total_payout_biweek_p2+=$total;
			}
		}
		
	
		
	}
}
$lmt_earnings_p1=$total_completed_biweek_p1_amt-$total_payout_biweek_p1;
$lmt_earnings_p2=$total_completed_biweek_p2_amt-$total_payout_biweek_p2;
//chart starts
 $start_month=date('n')-1;
     // echo "st:".$start_month;
	 $months_arr_ord=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	 //$months_arr_ord=array_reverse($months_arr_ord);
	 	
	 for($i=6;$i>=0;$i--)
	 {
		//echo "i:".$i;
		 $months_arr[$i+1]="\"".$months_arr_ord[$start_month]."\"";
		 $months_arr_det[$start_month+1]['months']=$months_arr_ord[$start_month];
		 $months_arr_det[$start_month+1]['count']=0;
		  if($start_month==0)
		 {
			 $start_month=12;
			  $start_month--;
		 }
		 else{
		 $start_month--;
		 }
		 
	 }
$sql_completed_chart="select tblcourses.*,COUNT(tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,tblcourse_prices.cost as cost,date_format(tblcourses.start_date,'%b') as month_name from tblcourses  left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourse_prices.id=tblcourses.price_code  where  course_status_id=4 and 180>=DATEDIFF(now(),tblcourses.start_date)  GROUP BY tblcourses.course_id";
$completed_courses_chart=$db->ExecuteQuery($sql_completed_chart);


$rev_tot_chart=0.00;
foreach($completed_courses_chart as $completed_chart)
{
	$total_chart=0;
	$total_cost_chart=0;
	if(array_key_exists($completed_chart['month_name'],$rev_arr))
		{
			$total_chart=$rev_arr[$completed_chart['month_name']];
		}
	if($completed_chart['course_type_id']==1 || $completed_chart['course_type_id']==4 || $completed_chart['course_type_id']==6)
	{
		
		$total_chart+=$completed_chart['cost'];
		$rev_arr[$completed_chart['month_name']]=$total_chart;
	}
	else
	{
		$total_cost_chart=$completed_chart['tot_enrolled_new']*$completed_chart['cost'];
		if($completed_chart['tot_enrolled']<=4)
		{
			
			$total_chart+=$total_cost_chart;
			$rev_arr[$completed_chart['month_name']]=$total_chart;
			
		}
		elseif($completed_chart['tot_enrolled_new']<=8)
		{
			$total_chart+=$total_cost_chart;
			$rev_arr[$completed_chart['month_name']]=$total_chart;
		}
		elseif($completed_chart['tot_enrolled_new']>=8)
		{
			$total_chart+=$total_cost_chart;
			$rev_arr[$completed_chart['month_name']]=$total_chart;
		}
	}
}

  foreach($months_arr_det as $key=> $value)
 {
	 $month1="";
	 $month1=$months_arr_det[$key]['months'];
	
	 if($month1!="")
	 {
	 if(array_key_exists($month1,$rev_arr))
		{
			$months_arr_det[$key]['count']=number_format($rev_arr[$month1],2);
			
		}
		else
		{
			$months_arr_det[$key]['count']=0.00;
		}
	 }
 }
 
 if(count($completed_courses_chart)>0)
 {
	  $months_arr_det=array_reverse($months_arr_det);
	 $months_tu_charts=implode(",",array_reverse($months_arr));
	 
  $i=1;
     foreach ($months_arr_det as $key => $value)
	 {
		  $d1.="[".$i.", '". $months_arr_det[$key]['count'] ."']";
		
	     if($i!=7)
		 {
			 $d1.=",";
		 }
		
			 $ticks.="[".$i.",\"".$months_arr_det[$key]['months']."\"], '".$months_arr_det[$key]['count']."'";
		
			if($i!=7)
		 {
			 $ticks.=",";
		 }
		 $i++;
	 }
 }
  else{
	   $months_arr_det=array_reverse($months_arr_det);
		 $d1=" [1, 0],
            [2, 0],
            [3, 0],
            [4, 0],
            [5, 0],
            [6, 0],
            [7, 0],";
			for($i=1;$i<7;$i++)
			{
				$ticks.="[".$i.",\"".$months_arr_det[$key]['months']."\"], 0";
			if($i!=7)
		 {
			 $ticks.=",";
		 }
			}
	 } 
//chart_ends
//echo $ticks;
//set the view page name
include('header.php');
  
   require_once('layout/dashlayout/dashboardcont.php');
   
 include('footer.php');
?>
   