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
				
					array( 'db' => 'heading', 'dt' => 'title','db'=>'date_added','dt'=>'date_added' ),
					
				
				);
$dtColumns= array( 0=> 'title', 1=> 'introtext',2=>'featured',3=>'edit');

$display_cols=array( 0=> 'title', 1=> 'introtext',2=>'date_added',3=>'featured',4=>'edit');

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
	$sorting_column=" heading ";
}
if($_REQUEST['order'][0]['column']==2)
{
	$sorting_column=" date_added ";
}
$sorting=" order by ".$sorting_column." ".$sorting_type;

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
if($searching_array!=null)
{
	$having=" having (".implode(" or ", $searching_array).")";
}

$start=$_REQUEST['start'];
$length=$_REQUEST['length'];
$limit=" limit ".$start.",".$length;

$sql="select * from tbl_news_new ";
$newses=$db->ExecuteQuery($sql);
$count = count($newses);
$sql.=" ".$sorting;		
$sql.= $limit;

$newes_all=$db->ExecuteQuery($sql);
if($count>0)
{
	foreach($newes_all as $news)
	{
		$row=array();
		for($i=0;$i<count($display_cols);$i++)
		{
			if($display_cols[$i]=="title")
			{
				$row[]=$news['heading'];
			}
			elseif($display_cols[$i]=="introtext")
			{
				$row[]=$news['intro_text'];
			}
			elseif($display_cols[$i]=="date_added")
			{
				$row[]=date("m/d/Y",strtotime($news['date_added']));
			}
			elseif($display_cols[$i]=="featured")
			{
				if($news['featuted']==1)
				  $row[]="Yes";
			    else
				   $row[]="No";
			}
			elseif($display_cols[$i]=="edit")
			{
				
				  $row[]='<a href="addnews.php?id='.$news['id'].'" class="submit btn btn-warning btn-cons">
							 Edit</a>';
			   
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
