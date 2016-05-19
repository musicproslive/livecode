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
				
					array( 'db' => 'refname', 'dt' => 'refname' ),
					array( 'db' => 'refemail', 'dt' => 'refemail' ),
					array( 'db' => 'created', 'dt' => 'created_date')
					
				
				);
$dtColumns= array( 0=> 'refname', 1=> 'refemail',2=>'created');

$display_cols=array( 0=> 'refname', 1=> 'refemail',2=>'date_created',3=>'view');

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
	$sorting_column=" refname ";
	$sorting=" order by ".$sorting_column." ".$sorting_type;
}
if($_REQUEST['order'][0]['column']==2)
{
	$sorting_column=" tblusers.created ";
	$sorting=" order by ".$sorting_column." ".$sorting_type.", refname asc";
}
if($_REQUEST['order'][0]['column']==4)
{
	$sorting_column=" dsp_order ";
	$sorting=" order by dsp_order ".$sorting_type.", refname asc";
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

$start=$_REQUEST['start'];
$length=$_REQUEST['length'];
$limit=" limit ".$start.",".$length;

$sql="select concat(tblusers.first_name,' ',tblusers.last_name)as refname,tbluser_login.user_name as refemail, tbluser_login.login_id,IFNULL(tbluser_login.activation_pro,0) as activation_pro,tbluser_login.admin_authorize,tblusers.profile_image,IFNULL(tbluser_address.phone1,'')as phone_no,date_format(tblusers.created,'%m/%d/%Y') as created_date, tbluser_activation.uverified, IF(admin_authorize=1, '2',(SELECT CASE WHEN uverified=1 THEN '0' ELSE '1' END)) as dsp_order, tblusers.user_id from tblusers inner join tbluser_login on tblusers.login_id=tbluser_login.login_id left join tbluser_address on tblusers.user_id=tbluser_address.user_id left join tbluser_activation on tbluser_activation.login_id=tbluser_login.login_id where tbluser_login.user_role=7 and tbluser_login.is_deleted=0 group by tbluser_login.login_id";
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
			if($display_cols[$i]=="refname")
			{
				$row[]='<div class="profile-pic-dash"><img width="35" height="35" data-src="../classroom/images/profile/profileImage/'.$data["profile_image"].'" alt="" src="../classroom/images/profile/profileImage/'.$instructor["profile_image"].'"></div><div style="margin-left: 50px !important; margin-top: 8px !important;">'.$instructor["refname"].'</div>';
			}
			elseif($display_cols[$i]=="refemail")
			{
				$row[]=$instructor['refemail'];
			}
			elseif($display_cols[$i]=="date_created")
			{
				$row[]=$instructor['created_date'];
			}
			
			elseif($display_cols[$i]=="view")
			{
				
				  $row[]='<a href="addreferrals.php?ref_id='.$instructor['user_id'].'" class="btn btn-warning "  >Edit</a>';
			   
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
