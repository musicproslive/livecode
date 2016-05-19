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

$month=$_REQUEST['month'];
$year=$_REQUEST['year'];
$completed_lessons_sql="select tblcourses.*,COUNT(distinct tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,tblcourse_prices.cost as cost from tblcourses  left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourse_prices.id=tblcourses.price_code  where  (course_status_id=1 or course_status_id=2) and month(tblcourses.start_date)=".$month." and year(tblcourses.start_date)=".$year." GROUP BY tblcourses.course_id having tot_enrolled_new>0";

$complete_lessons=$db->ExecuteQuery($completed_lessons_sql);

$individiual_complted=0;
$indi_amt=0.00;
$group_complted=0;
$group_amt=0.00;
$master_completed=0;
$master_amt=0.00;

for($i=0;$i<count($complete_lessons);$i++)
{
	$group_cost=0.00;
	$group_tot=0.00;
	if($complete_lessons[$i]['course_type_id']==1 || $complete_lessons[$i]['course_type_id']==6)
	{
		$individiual_complted+=1;
		$indi_amt+=$complete_lessons[$i]['cost'];
	}
	elseif($complete_lessons[$i]['course_type_id']==4)
	{
		$group_complted+=1;
		$group_amt+=$complete_lessons[$i]['cost'];
		/*if($complete_lessons[$i]['tot_enrolled_new']>0)
		{
		  $group_cost=$complete_lessons[$i]['tot_enrolled_new']*$complete_lessons[$i]['cost'];
		}
		else
		{
			 $group_cost=$complete_lessons[$i]['cost'];
		}
		if($complete_lessons[$i]['tot_enrolled_new']<=4)
		{
			
			
			$group_amt+=$group_cost;
			
		}
		elseif($complete_lessons[$i]['tot_enrolled_new']<=8)
		{
			
			$group_amt+=$group_cost;
		}
		elseif($complete_lessons[$i]['tot_enrolled_new']>=8)
		{
			
			$group_amt+=$group_cost;
		}*/
		
	}
	else
	{
		$master_completed+=1;
		$master_amt+=$complete_lessons[$i]['cost'];
	}
		
}
$grand_completed=0;
$grand_completed=$individiual_complted+$group_complted+$master_completed;

$grand_amt=0.00;
$grand_amt=$indi_amt+$group_amt+$master_amt;

$output="<tr>								  
			<td>Phone Meeting</td>
			<td>".number_format($individiual_complted,0,"",",")."</td>
			<td>$".number_format($indi_amt,2,".",",")."</td>
		</tr>
		<tr>
		    <td>In Person Meeting</td>
			<td>".number_format($group_complted,0,"",",")."</td>
			<td>$".number_format($group_amt,2,".",",")."</td>
		</tr>
		<tr>
		  <td>Master Meeting</td>
		  <td>".number_format($master_completed,0,"",",")."</td>
		  <td>$".number_format($master_amt,2,".",",")."</td>
		</tr>
		<tr>
		  <td>Total</td>
		  <td>".number_format($grand_completed,0,"",",")."</td>
		 <td>$".number_format($grand_amt,2,".",",")."</td>
	    </tr>";

echo $output;

?>