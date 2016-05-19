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
				
					array( 'db' => 'promocode', 'dt' => 'promocode' ),
					array( 'db' => 'code_status', 'dt' => 'code_status' ),
					
					
				
				);
$dtColumns= array( 0=> 'promocode', 1=> 'code_status');

$display_cols=array( 0=> 'promocode', 1=> 'status',2=>'email_address');

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
	$sorting_column=" date_created ";
	$sorting=" order by ".$sorting_column." ".$sorting_type;
}
if($_REQUEST['order'][0]['column']==1)
{
	$sorting_column=" code_status ";
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
	$having=" having (promocode like '%".$_REQUEST['search']['value']."%')";
}


$start=$_REQUEST['start'];
$length=$_REQUEST['length'];
$limit=" limit ".$start.",".$length;

$sql="select promocode,code_status,date_created,IFNULL(member_used,'') as member_used from promocodes";
$sql.=" ".$having;
$promo_codes=$db->ExecuteQuery($sql);
$count = count($promo_codes);

$sql.=" ".$sorting;		
$sql.= $limit;

$promo_codes_all=$db->ExecuteQuery($sql);

if($count>0)
{
	 foreach($promo_codes_all as $promo_code)
	{
		$verified_query="";
		$row=array();
		for($i=0;$i<count($display_cols);$i++)
		{
			if($display_cols[$i]=="promocode")
			{
				$row[]=$promo_code['promocode'];
			}
			elseif($display_cols[$i]=="status")
			{
				if($promo_code['code_status']==0)
				{
					$row[]="Active";
				}
				else
				{
					$row[]="InActive";
				}
				
			}
			elseif($display_cols[$i]=="email_address")
			{
				
				
				if($promo_code['member_used']!="")
				{
					$find_email_sql="select user_name from tbluser_login inner join tblusers  on tblusers.login_id=tbluser_login.login_id where tblusers.user_id=".$promo_code['member_used'];
					$found_user=$db->ExecuteQuery($find_email_sql);
					$row[]=$found_user[0]['user_name'];
				}
				else
				{
					$row[]=$promo_code['member_used'];
				}
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
