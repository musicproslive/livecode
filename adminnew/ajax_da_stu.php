<?php
/**************************************************************************************
Created by :Lijesh 
Created on :Sep - 06 - 2012
Purpose    :Cource Listing of Tutor
**************************************************************************************/
require_once 'init.php';err_status("init.php included");
ini_set("memory_limit","512M");

//error_reporting(E_ALL);
//Required library files
require "library/MysqlAdapter.php";
 
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();
//Top Advertisement Management in header

/*sorting*/
$columns = array(
				
					array( 'db' => 'insname', 'dt' => 'insname' ),
					array( 'db' => 'user_name', 'dt' => 'user_name' ),
					array( 'db' => 'created', 'dt' => 'created')
					
				
				);
$dtColumns= array( 0=> 'insname', 1=> 'user_name',2=>'created');

$display_cols=array( 0=> 'insname', 1=> 'insemail',2=>'booked',3=>'completed',4=>'amount',5=>'since');

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
	$sorting=" order by ".$sorting_column." ".$sorting_type.", insname asc";
}
if($_REQUEST['order'][0]['column']==1)
{
	$sorting_column=" user_name ";
	$sorting=" order by ".$sorting_column." ".$sorting_type.", insname asc";
}
if($_REQUEST['order'][0]['column']==2)
{
	$sorting_column=" booked ";
	$sorting=" order by ".$sorting_column." ".$sorting_type.", insname asc";
}
if($_REQUEST['order'][0]['column']==3)
{
	$sorting_column=" comp ";
	$sorting=" order by ".$sorting_column." ".$sorting_type.", insname asc";
}
if($_REQUEST['order'][0]['column']==4)
{
	$sorting_column=" spent ";
	$sorting=" order by ".$sorting_column." ".$sorting_type.", insname asc";
}
if($_REQUEST['order'][0]['column']==5)
{
	$sorting_column=" tblusers.created ";
	$sorting=" order by ".$sorting_column." ".$sorting_type.", insname asc";
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
	$having=" and tblusers.first_name like '%".$_REQUEST['search']['value']."%' or tblusers.last_name like '%".$_REQUEST['search']['value']."%' or user_name like '%".$_REQUEST['search']['value']."%' ";
}

$start=$_REQUEST['start'];
$length=$_REQUEST['length'];
$limit=" limit ".$start.",".$length;
if($_GET['type']=="kids")
{
	$sql="select concat(tblusers.first_name,' ',tblusers.last_name)as insname,
       tbluser_login.user_name,
	   tblusers.user_id, 
	   tbluser_login.login_id,
	   IFNULL(tbluser_login.activation_pro,0) as activation_pro,
	   tbluser_login.admin_authorize,
	   tblusers.profile_image,
	   IFNULL(tbluser_address.phone1,'')as phone_no,
	   date_format(tblusers.created,'%m/%d/%Y') as created_date, 
	   tbluser_activation.uverified, 
	   IF(authorized=1, '1',0) as dsp_order, 
	   round(DATEDIFF(CURRENT_DATE,tblusers.dob)/365) as age,
	   tblusers.first_name,tblusers.last_name,IFNULL(enr.booked,0) as booked, 
	   IFNULL(enr_comp.done,0) as comp, IFNULL(enr_spent.spent,0.00)as spent
	     from tblusers 
		  inner join tbluser_login on tblusers.login_id=tbluser_login.login_id 
		  left join tbluser_address on tblusers.user_id=tbluser_address.user_id 
		  left join tbluser_activation on tbluser_activation.login_id=tbluser_login.login_id 
		  left join (select tblusers.user_id,count(enrolled_id) as booked from tblcourse_enrollments inner join tblusers on tblusers.user_id=tblcourse_enrollments.student_id group by tblusers.user_id) as enr on enr.user_id=tblusers.user_id 
		  left join (select tblusers.user_id,count(enrolled_id) as done from tblcourse_enrollments inner join tblusers on tblusers.user_id=tblcourse_enrollments.student_id where  enrolled_status_id=3 group by tblusers.user_id) as enr_comp on enr_comp.user_id=tblusers.user_id 
		  left join (select tblusers.user_id,sum(trans_amount) as spent from tblcourse_enrollment_transaction inner join tblusers on tblusers.user_id=tblcourse_enrollment_transaction.user_id where   paid_flag=1 group by tblusers.user_id) as enr_spent on enr_spent.user_id=tblusers.user_id 
		 where tbluser_login.user_role=4 and tbluser_login.is_deleted=0 group by tblusers.user_id having age>0 and age<=13";
	
}
else
{
$sql="select concat(tblusers.first_name,' ',tblusers.last_name)as insname,
       tbluser_login.user_name,
	   tblusers.user_id, 
	   tbluser_login.login_id,
	   IFNULL(tbluser_login.activation_pro,0) as activation_pro,
	   tbluser_login.admin_authorize,
	   tblusers.profile_image,
	   IFNULL(tbluser_address.phone1,'')as phone_no,
	   date_format(tblusers.created,'%m/%d/%Y') as created_date, 
	   tbluser_activation.uverified, 
	   IF(authorized=1, '1',0) as dsp_order, 
	   IFNULL(round(DATEDIFF(CURRENT_DATE,tblusers.dob)/365),14) as age,
	   tblusers.first_name,tblusers.last_name,IFNULL(enr.booked,0) as booked, 
	   IFNULL(enr_comp.done,0) as comp, IFNULL(enr_spent.spent,0.00)as spent
	     from tblusers 
		  inner join tbluser_login on tblusers.login_id=tbluser_login.login_id 
		  left join tbluser_address on tblusers.user_id=tbluser_address.user_id 
		  left join tbluser_activation on tbluser_activation.login_id=tbluser_login.login_id 
		  left join (select tblusers.user_id,count(enrolled_id) as booked from tblcourse_enrollments inner join tblusers on tblusers.user_id=tblcourse_enrollments.student_id group by tblusers.user_id) as enr on enr.user_id=tblusers.user_id 
		  left join (select tblusers.user_id,count(enrolled_id) as done from tblcourse_enrollments inner join tblusers on tblusers.user_id=tblcourse_enrollments.student_id where  enrolled_status_id=3 group by tblusers.user_id) as enr_comp on enr_comp.user_id=tblusers.user_id 
		  left join (select tblusers.user_id,sum(trans_amount) as spent from tblcourse_enrollment_transaction inner join tblusers on tblusers.user_id=tblcourse_enrollment_transaction.user_id where   paid_flag=1 group by tblusers.user_id) as enr_spent on enr_spent.user_id=tblusers.user_id 
		 where tbluser_login.user_role=4 and tbluser_login.is_deleted=0 group by tblusers.user_id having age>13";
}
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
		$row=array();
		for($i=0;$i<count($display_cols);$i++)
		{
			if($display_cols[$i]=="insname")
			{
				$row[]='<div class="profile-pic-dash"><img width="35" height="35" data-src="../classroom/images/profile/profileImage/'.$data["profile_image"].' alt="" src="../classroom/images/profile/profileImage/'.$instructor["profile_image"].'"></div><div style="margin-left: 50px !important; margin-top: 8px !important;">'.$instructor["insname"].'</div>';
			}
			elseif($display_cols[$i]=="insemail")
			{
				$row[]=$instructor['user_name'];
			}
			elseif($display_cols[$i]=="since")
			{
				$row[]=$instructor['created_date'];
			}
			elseif($display_cols[$i]=="booked")
			{
				$tot_booked=$instructor['booked'];
				
				/*$booked=$db->ExecuteQuery("select count(enrolled_id) as booked  from tblcourse_enrollments where student_id=".$instructor['user_id']);
				$tot_booked=$booked[0]['booked'];*/
				$row[]=$tot_booked;
			}
			elseif($display_cols[$i]=="completed")
			{
				$tot_compt=$instructor['comp'];
				
				/*$compt=$db->ExecuteQuery("select count(enrolled_id) as completed  from tblcourse_enrollments where student_id=".$instructor['user_id']." and enrolled_status_id=3");
				$tot_compt=$compt[0]['completed'];*/
				$row[]=$tot_compt;
			}
			elseif($display_cols[$i]=="amount")
			{
				
				 $tot_spent="$".number_format($instructor['spent']);
				
				/*$spent=$db->ExecuteQuery("select sum(trans_amount) as spent  from tblcourse_enrollment_transaction where user_id=".$instructor['user_id']." and paid_flag=1");
				$tot_spent="$".number_format($spent[0]['spent'],2);*/
				$row[]=$tot_spent;
			   
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
echo json_encode($return_result,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
die();
