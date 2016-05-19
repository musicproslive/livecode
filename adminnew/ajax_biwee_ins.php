<?php 

/**************************************************************************************
Created by :Lijesh 
Created on :Sep - 06 - 2012
Purpose    :Cource Listing of Tutor
**************************************************************************************/
require_once 'init.php';err_status("init.php included");


//error_reporting(E_ALL);
//Required library files
require "library/MysqlAdapter.php";
 
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();
//Top Advertisement Management in header

/*sorting*/
$columns = array(
				
					array( 'db' => 'insname', 'dt' => 'insname' )
					
					
					
				
				);
$dtColumns= array( 0=> 'insname');

$display_cols=array(0=> 'insname', 1=> 'completed',2=>'revenue',3=>'payout',4=>'earnings');

$sorting_column="";
$sorting_type="";

for ( $i=0, $ien=count($_REQUEST['order']) ; $i<$ien ; $i++ ) {
	// Convert the column index into the column data property
	$columnIdx = intval($_REQUEST['order'][$i]['column']);
	$requestColumn = $_REQUEST['columns'][$columnIdx];

	$columnIdx = array_search( $requestColumn['data'], $dtColumns );
	$column = $columns[ $columnIdx ];

	if ( $requestColumn['orderable'] == 'true' ) {
		$sorting_type = $_REQUEST['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC';
		$sorting_column = $column['db'];
	}
	
}
if($_REQUEST['order'][0]['column']==0)
{
	$sorting_column=" insname ";
	$sorting=" order by ".$sorting_column." ".$sorting_type;
}




/* searching */

$searching_array=array();
$having_array=array();
for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++ ) {
	$requestColumn = $_REQUEST['columns'][$i];
	$columnIdx = array_search( $requestColumn['data'], $dtColumns );
	$column = $columns[ $columnIdx ];

	$search_column = $column['db'];
	$search_value = $_REQUEST['search']['value'];
	
	if ( $requestColumn['searchable'] == 'true' && $search_value != '' ) {
		
		$searching_array[]=" ".$search_column." like '%".$search_value."%' ";
	}
}
/* searching */
$having="";
if($_REQUEST['search']['value']!=null)
{
	$having=" having (insname like '%".$_REQUEST['search']['value']."%' or user_name like '%".$_REQUEST['search']['value']."%')";
}
$month=$_REQUEST['month'];
$year=$_REQUEST['year'];
$days_week=$_REQUEST['days'];
		if($days_week<=15)
		{
			$week_cond=" and day(tblcourses.start_date)>=1 and day(tblcourses.start_date)<=15 ";
		}
		else
		{
			$week_cond=" and day(tblcourses.start_date)>=16 and day(tblcourses.start_date)<=31 ";
		}
$sql="select count(course_id) as comp,instructor_id,concat(tblusers.first_name,' ',tblusers.last_name) as insname,tblusers.user_id,tbluser_login.user_name from tblcourses left join tblusers on tblusers.user_id=tblcourses.instructor_id left join tbluser_login on tbluser_login.login_id=tblusers.login_id where course_status_id=4 and month(tblcourses.start_date)=".$month." and year(tblcourses.start_date)=".$year." ".$week_cond." group by instructor_id";
$start=$_REQUEST['start'];
$length=$_REQUEST['length'];
$limit=" limit ".$start.",".$length;
$sql.=" ".$having;
$instructors=$db->ExecuteQuery($sql);
$count = count($instructors);

$sql.=" ".$sorting;		
$sql.= $limit;

$instructors_all=$db->ExecuteQuery($sql);


if($count>0)
{
	 foreach($instructors_all as $instructor)
	{
		$verified_query="";
		$revenues="";
		unset($revenues_exp);
		$row=array();
		$completed=0;
		$comp_amt=0.00;
		$payout=0.00;
		$earnings=0.00;
		$revenues=getrevenues($db,$instructor['user_id'],$month,$year,$days_week);
		$revenues_exp=explode("/",$revenues);
		$completed=$revenues_exp[0];
		$comp_amt=$revenues_exp[1];
		$payout=$revenues_exp[2];
		$earnings=$revenues_exp[3];
		for($i=0;$i<count($display_cols);$i++)
		{
			if($display_cols[$i]=="insname")
			{
				$row[]=$instructor["insname"];
			}
			elseif($display_cols[$i]=="completed")
			{
				$row[]=$completed;
			}
			elseif($display_cols[$i]=="revenue")
			{
				$row[]="$".number_format($comp_amt,2,".",",");
			}
			elseif($display_cols[$i]=="payout")
			{
				$row[]="$".number_format($payout,2,".",",");
			}
			elseif($display_cols[$i]=="earnings")
			{
				$row[]="$".number_format($earnings,2,".",",");
			}
			
		}
	$data[]=$row;	
	}	
}
else
{
	$row=array();
	$data['empty']=$row;
}

$return_result= array(
	
	"recordsTotal"    => $count,
	"recordsFiltered" => $count,
	"data"            => $data,
	
	
);
echo json_encode($return_result);
die();

function getrevenues($db,$ins_id,$month,$year,$days_week)
{
		
		if($days_week<=15)
		{
			$week_cond=" and day(tblcourses.start_date)>=1 and day(tblcourses.start_date)<=15 ";
		}
		else
		{
			$week_cond=" and day(tblcourses.start_date)>=16 and day(tblcourses.start_date)<=31 ";
		}

		$total_lessons_completed_biweek_sql="select tblcourses.*,COUNT(tblcourse_enrollments.enrolled_id) AS tot_enrolled_new,tblcourse_prices.cost as cost from tblcourses  left join tblcourse_enrollments on tblcourse_enrollments.course_id=tblcourses.course_id left join tblcourse_prices on tblcourse_prices.id=tblcourses.price_code  where instructor_id=".$ins_id." and course_status_id=4 and month(tblcourses.start_date)=".$month." and year(tblcourses.start_date)=".$year." ".$week_cond."  GROUP BY tblcourses.course_id";
		//echo $total_lessons_completed_biweek_sql;
		
		

		$lessons_completed_biweek=$db->ExecuteQuery($total_lessons_completed_biweek_sql);
		$total_lessons_completed_biweek=0;
		$total_lessons_completed_biweek=count($lessons_completed_biweek);
		$total_completed_biweek_amt=0.00;
		$total_payout_biweek=0.00;
		for($bi=0;$bi<count($lessons_completed_biweek);$bi++)
		{
			$total="";
			$total_cost="";
			
			if($lessons_completed_biweek[$bi]['course_type_id']==1 || $lessons_completed_biweek[$bi]['course_type_id']==4 ||$lessons_completed_biweek[$bi]['course_type_id']==6)
			{
				$total_completed_biweek_amt+=$lessons_completed_biweek[$bi]['cost'];
				$total=$lessons_completed_biweek[$bi]['cost']*0.6;
				$total_payout_biweek+=$total;
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
		  $return=$total_lessons_completed_biweek."/".$total_completed_biweek_amt."/".$total_payout_biweek."/".$lmt_earnings;
		  return $return;
}