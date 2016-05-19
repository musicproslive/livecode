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
					array( 'db' => 'percentage', 'dt' => 'percentage' ),
					
					array( 'db' => 'is_active', 'dt' => 'is_active' ),
					array( 'db' => 'count', 'dt' => 'count' )
					
					
				
				);
$dtColumns= array( 0=> 'promocode', 1=> 'percentage',2=> 'is_active',3=> 'count');

$display_cols=array( 0=> 'promocode', 1=> 'percentage',2=> 'is_active',3=> 'count',4=> 'view');

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
	$sorting_column=" percentage ";
	$sorting=" order by ".$sorting_column." ".$sorting_type;
}
if($_REQUEST['order'][0]['column']==2)
{
	$sorting_column=" is_active ";
	$sorting=" order by ".$sorting_column." ".$sorting_type;
}
if($_REQUEST['order'][0]['column']==3)
{
	$sorting_column=" count ";
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

$sql="select id,promocode,is_active,date_created,IFNULL(count,'') as member_used,percentage from discount_amount_code";
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
		$active_button="";
		$delete_button="";
		$edit_button="";
		$row=array();
		for($i=0;$i<count($display_cols);$i++)
		{
			if($display_cols[$i]=="promocode")
			{
				$row[]=$promo_code['promocode'];
			}
			elseif($display_cols[$i]=="is_active")
			{
				if($promo_code['is_active']==0)
				{
					$row[]="Active";
				}
				else
				{
					$row[]="InActive";
				}
				
			}
			elseif($display_cols[$i]=="count")
			{
				$row[]=$promo_code['member_used'];
			}
			elseif($display_cols[$i]=="percentage")
			{
				$row[]=$promo_code['percentage'];
			}
			elseif($display_cols[$i]=="view")
			{
				$edit_button='<button type="button" class="btn btn-warning btn-xs btn-mini" onclick="javascript:edit_promo('.$promo_code['id'].','.$promo_code['percentage'].',\''.$promo_code['promocode'].'\')">Edit</button>';
				
				$delete_button='<button type="button" class="btn btn-danger btn-xs btn-mini" onclick="javascript:delete_promo('.$promo_code['id'].')">Delete</button>';
				
				if($promo_code['is_active']==0)
				{
					$active_button='<button type="button" class="btn btn-warning btn-xs btn-mini" onclick="javascript:status_promo('.$promo_code['id'].',1)">Deactivate</button>';
				}
				else
				{
					$active_button='<button type="button" class="btn btn-warning btn-xs btn-mini" onclick="javascript:status_promo('.$promo_code['id'].',0)">Activate</button>';
				}
				
				$row[]=$edit_button." ".$delete_button." ".$active_button;
				
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
