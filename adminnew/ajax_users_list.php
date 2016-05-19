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
					array( 'db' => 'admin_authorize', 'dt' => 'admin_authorize' ),
					array( 'db' => 'created', 'dt' => 'created_date')
					
				
				);
$dtColumns= array( 0=> 'insname', 1=> 'insemail',2=>'status');

$display_cols=array( 0=> 'insname', 1=> 'insemail',2=>'phone',3=>'date_created',4=>'status',5=>'view');

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
if($_REQUEST['order'][0]['column']==3)
{
	$sorting_column=" tblusers.created ";
	$sorting=" order by ".$sorting_column." ".$sorting_type.", insname asc";
}
if($_REQUEST['order'][0]['column']==4)
{
	$sorting_column=" dsp_order ";
	$sorting=" order by dsp_order ".$sorting_type.", insname asc";
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
if($_GET['type']=="kids")
{
	$sql="select concat(tblusers.first_name,' ',tblusers.last_name)as insname,tbluser_login.user_name, tbluser_login.login_id,IFNULL(tbluser_login.activation_pro,0) as activation_pro,tbluser_login.admin_authorize,tblusers.profile_image,IFNULL(tbluser_address.phone1,'')as phone_no,date_format(tblusers.created,'%m/%d/%Y') as created_date, tbluser_activation.uverified, IF(authorized=1, '1',0) as dsp_order, round(DATEDIFF(CURRENT_DATE,tblusers.dob)/365) as age from tblusers inner join tbluser_login on tblusers.login_id=tbluser_login.login_id left join tbluser_address on tblusers.user_id=tbluser_address.user_id left join tbluser_activation on tbluser_activation.login_id=tbluser_login.login_id where tbluser_login.user_role=4 and tbluser_login.is_deleted=0 having age>0 and age<=13";
	
}
elseif($_GET['type']=="verteran")
{
	$sql="select concat(tblusers.first_name,' ',tblusers.last_name)as insname,tbluser_login.user_name, tbluser_login.login_id,IFNULL(tbluser_login.activation_pro,0) as activation_pro,tbluser_login.admin_authorize,tblusers.profile_image,IFNULL(tbluser_address.phone1,'')as phone_no,date_format(tblusers.created,'%m/%d/%Y') as created_date, tbluser_activation.uverified, IF(authorized=1, '1',0) as dsp_order, IFNULL(round(DATEDIFF(CURRENT_DATE,tblusers.dob)/365),14) as age, tblusers.verteran  from tblusers inner join tbluser_login on tblusers.login_id=tbluser_login.login_id left join tbluser_address on tblusers.user_id=tbluser_address.user_id left join tbluser_activation on tbluser_activation.login_id=tbluser_login.login_id where tbluser_login.user_role=4 and tbluser_login.is_deleted=0 having age>13 and verteran=1";
}
else
{
$sql="select concat(tblusers.first_name,' ',tblusers.last_name)as insname,tbluser_login.user_name, tbluser_login.login_id,IFNULL(tbluser_login.activation_pro,0) as activation_pro,tbluser_login.admin_authorize,tblusers.profile_image,IFNULL(tbluser_address.phone1,'')as phone_no,date_format(tblusers.created,'%m/%d/%Y') as created_date, tbluser_activation.uverified, IF(authorized=1, '1',0) as dsp_order, IFNULL(round(DATEDIFF(CURRENT_DATE,tblusers.dob)/365),14) as age from tblusers inner join tbluser_login on tblusers.login_id=tbluser_login.login_id left join tbluser_address on tblusers.user_id=tbluser_address.user_id left join tbluser_activation on tbluser_activation.login_id=tbluser_login.login_id where tbluser_login.user_role=4 and tbluser_login.is_deleted=0 having age>13";
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
				$row[]='<div class="profile-pic-dash"><img width="35" height="35" data-src="../classroom/images/profile/profileImage/'.$data["profile_image"].'" alt="" src="../classroom/images/profile/profileImage/'.$instructor["profile_image"].'"></div><div style="margin-left: 50px !important; margin-top: 8px !important;">'.$instructor["insname"].'</div>';
			}
			elseif($display_cols[$i]=="insemail")
			{
				$row[]=$instructor['user_name'];
			}
			elseif($display_cols[$i]=="date_created")
			{
				$row[]=$instructor['created_date'];
			}
			elseif($display_cols[$i]=="phone")
			{
				$row[]=$instructor['phone_no'];
			}
			elseif($display_cols[$i]=="status")
			{
				$verified_query="select * from tbluser_activation where login_id=".$instructor['login_id'];
				$verified=$db->ExecuteQuery($verified_query);
				if($instructor['admin_authorize']==0)
				{
				  if($verified[0]['uverified']==1)
				  {					  
					$row[]='Pending';
				  }
                  else
				  {
					$row[]='Not Approved';
				  }					  
				}
			    else
				{
				  $row[]="Approved";	 
				}
			}
			elseif($display_cols[$i]=="view")
			{
				
				  $row[]='<a href="viewuser.php?login_id='.$instructor['login_id'].'" class="btn btn-warning "  >View</a>';
			   
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
