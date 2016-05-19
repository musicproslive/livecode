<?php
/**************************************************************************************
Created by :Lijesh 
Created on :Sep - 06 - 2012
Purpose    :Cource Listing of Tutor
**************************************************************************************/
require_once 'init.php';err_status("init.php included");
error_reporting(E_ALL);
//Required library files
require "library/MysqlAdapter.php";
 
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();
//Top Advertisement Management in header

$obj	=	loadModelClass(true,"stdCourses.php");

$enrl_status=$_REQUEST['enrl_status'];
$user_id=$_REQUEST['user_id'];
$server_offset = LMT_SERVER_TIME_ZONE_OFFSET;
$timeZone = new timeZone();
$myTimeZone = $timeZone->getMyTimeZoneOffset($user_id);
$myTimeZone = !empty($myTimeZone) ? $myTimeZone[0]['gmt'] : '+00:00';
$formats=$db->ExecuteQuery("select * from tbllookup_user_timestamp where id=".$_REQUEST['time_format_id']);
$mtime=$formats[0]['mysql_time_format'];
$mdate=$formats[0]['mysql_date_format'];
/*sorting*/
$columns = array(
				
					array( 'db' => 'insname', 'dt' => 'insname' ),
				
					array( 'db' => 'type',  'dt' => 'type' ),
					array( 'db' => 'start_date',  'dt' => 'start_date' ),
					array( 'db' => 'start_time',  'dt' => 'start_time' ),
					array( 'db' => 'time',  'dt' => 'time' ),
					array( 'db' => 'cost',  'dt' => 'cost' ),
					
				);
$dtColumns= array( 0=> 'insname',1=> 'type',2=> 'start_date',3=> 'start_time',4=> 'time',5=> 'cost');
if($enrl_status==1)
	{
		$display_cols=array(0=> 'insname',1=> 'type', 2=> 'start_date',3=> 'start_time',4=> 'time',5=> 'cost');
	}
	else if($enrl_status==3)
	{
		$display_cols=array(0=> 'insname',1=> 'type',2=> 'start_date',3=> 'start_time',4=> 'time',5=> 'video_link',6=> 'note_id');
	}
	else
	  {
		$display_cols=array(0=> 'insname',1=> 'type', 2=> 'start_date',3=> 'start_time',4=> 'time',5=> 'cost');	
	  }
$sorting_column="";
$sorting_type="";
//print_r($_REQUEST['order']);
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


/* sorting */

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

if($_REQUEST['order'][0]['column']==0)
{
	$sorting_column=" insname ";
}

elseif($_REQUEST['order'][0]['column']==1)
{
	$sorting_column=" type ";
}
elseif($_REQUEST['order'][0]['column']==2)
{
	$sorting_column=" C.start_date ";
}
elseif($_REQUEST['order'][0]['column']==3)
{
	$sorting_column=" C.start_date ";
}
elseif($_REQUEST['order'][0]['column']==4)
{
	$sorting_column=" time ";
}
elseif($_REQUEST['order'][0]['column']==5)
{
	$sorting_column=" cost ";
}
if($sorting_column==" C.start_date ")
{
	$sorting=" order by ".$sorting_column." ".$sorting_type." , C.start_time"." ".$sorting_type;
}
else
{
$sorting=" order by ".$sorting_column." ".$sorting_type;
}
$start=$_REQUEST['start'];
$length=$_REQUEST['length'];
$limit=" limit ".$start.",".$length;


			if($enrl_status==1)
			{
				$server_time_zone=strtotime(date('Y-m-d H:i:s'));
				$time_Ser=date('Y-m-d H:i:s');
				$condition = " AND (E.enrolled_status_id=1) and $server_time_zone<UNIX_TIMESTAMP(CONCAT(C.start_date, ' ',C.start_time))+D.time*60 and  '$time_Ser'<=DATE_ADD(CONCAT(C.start_date, ' ', C.start_time),INTERVAL D.time MINUTE)" ;	
			}
			elseif($enrl_status==3)
			{
				$server_time_zone=strtotime(date('Y-m-d H:i:s'));
				$time_Ser=date('Y-m-d H:i:s');
			    $condition =" AND C.course_status_id=4";	
			}
			elseif($enrl_status==2)
			{
				$server_time_zone=strtotime(date('Y-m-d H:i:s'));
				$time_Ser=date('Y-m-d H:i:s');
			    $condition = " AND (C.course_status_id=2 or E.enrolled_status_id=2)";	
			}
			elseif($enrl_status==4)
			{
				$server_time_zone=strtotime(date('Y-m-d H:i:s'));
				$time_Ser=date('Y-m-d H:i:s');
				$condition = " AND C.course_status_id=1 and  '$time_Ser'>=DATE_ADD(CONCAT(C.start_date, ' ', C.start_time),INTERVAL D.time MINUTE)" ;	
			}
			else
			{
			
			$condition = !empty($enrl_status) ? " AND E.enrolled_status_id = {$enrl_status}" : "";	
			}
			
		$cls	=	new userCourse();						
		
		$sql3   	= $cls->getStudentsCourseHistory($user_id, $mdate, $mtime,$condition);
		
		//echo $sql3;
		
		 $recent_she = $cls->getStudentRecentCourseShedule($user_id, LMT_SERVER_TIME_ZONE_OFFSET, $myTimeZone, date('Y-m-d H:i:s'), $mdate, $mtime);
		// print_r($recent_she);
		 $i=0;
		foreach($recent_she as $recent)
		{
			$recent_sheduled[$i]=$recent['course_code'];
			$i++;
		}
		
		
		//print_r($obj->getdbcontents_sql($sql3,0));
		
		$booked_less_tot = $obj->getdbcontents_sql($sql3,0);
			$count = count($booked_less_tot);
			$sql3.=" ".$sorting;		
			$sql3.= $limit;
		
		//$count = count($booked_less_tot);
		//echo "query:".$sql3;
		
	
		
		$booked_less=$obj->getdbcontents_sql($sql3,0);

    	$recent_sheduled_res =$recent_sheduled;
		



	if (count($booked_less)>0)
	{
	
			foreach($booked_less as $data)
			
			{
				$row=array();
				$profile_image="";
				$insname="";
				$instrument_name="";
				$type="";
				$start_date="";
				$start_time="";
				$time="";
				$cost="";
				$course_type="";
				$video_link="";
				$note_id="";
				
				for($i=0;$i<count($display_cols);$i++)
			{
				$profile_image="";
				$insname="";
				$instrument_name="";
				$type="";
				$start_date="";
				$start_time="";
				$time="";
				$cost="";
				$course_type="";
				$video_link="";
				$note_id="";
				
				
				
				if($enrl_status==1)
				{
				
					
					if (strtotime(date('Y-m-d H:i:s'))<$data['course_server_time'])
					{
						
	
		
		 if($display_cols[$i]=="insname")
		{
			 $profile_image ='<div class="profile-pic-dash"><img width="35" height="35" data-src="../classroom/images/profile/profileImage/'.$data["profile_image"].' alt="" src="../classroom/images/profile/profileImage/'.$data["profile_image"].'"></div><div style="margin-left: 50px !important; margin-top: 8px !important;">'.$data["insname"].'</div>';
			
			$row[]=$profile_image;
			
		}
		else if($display_cols[$i]=="instrument_name")
		{
			$instrument_name =$data["instrument_name"];
			$row[]=$instrument_name;
			
		}
		
		else if($display_cols[$i]=="type")
		{
			$type =$data["type"];
			$row[]=$type;
						
			
		}
		else if($display_cols[$i]=="start_date")
		{
			$start_date ='<span class="mnt-bg" title="'.$data["time"].' Minute Course">'.$data["start_date"].'  </span>';
			$row[]=$start_date;
			
		}
		else if($display_cols[$i]=="start_time")
		{
			$start_time =$data["start_time"];
						$row[]=$start_time;
			
		}
		else if($display_cols[$i]=="time")
		{
			$time =$data["time"];
						$row[]=$time." Minutes";
			
		}
		else if($display_cols[$i]=="cost")
		{
			$cost =$data["symbol"].''.$data["cost"];
						$row[]=$cost;
			
		}
		
			
			else if($display_cols[$i]=="video_link")
		{
			
			
			if($data["video_id"]!="")
							{
								$video_url=base64_encode(serialize($_REQUEST['user_code']))&base64_encode(serialize($data['course_code']))&$data['video_link']."&Recorded";
								$video_link ='<a href="javascript:void(0)" rel="'.$video_url.'" class="viewVideos" title="live class" onclick=javascript:viewvideos(\''.$data['course_code'].'\'); ><i class="fa fa-video-camera" alt="Video" title="Click here to view video(s)"></i></a>';
							}
							else
							{
								/*$video_link ='<i class="fa fa-video-camera" alt="No Video" title="No Videos"></i>';*/
								$video_link ='<p> N/A</p>';
											
							}
							
							$row[]=$video_link;	
						
			
		}
			else if($display_cols[$i]=="note_id")
		{
			
			if($data["note_id"]!="")
							{
												$notes_url= $obj->getLink("ViewNotes","",true,$obj->getConcat("ccode=",base64_encode(serialize($data["course_code"]))));
												$note_id ='<a href="'.$notes_url.'" class="Second_link"><i class="fa fa-file-text-o" alt="Notes" title="Notes"></i></a>';
							}
							else
							{
											
								/*	$note_id ='<i class="fa fa-file-text" alt="No Notes" title="No Notes"></i>';*/
								$note_id ='<p> N/A</p>';
							}
											
							$row[]=$note_id;
							
							
						
			
					}
					}
				}
				else{
					
					
		 if($display_cols[$i]=="insname")
		{
			 $profile_image ='<div class="profile-pic-dash"><img width="35" height="35" data-src="../classroom/images/profile/profileImage/'.$data["profile_image"].' alt="" src="../classroom/images/profile/profileImage/'.$data["profile_image"].'"></div><div style="margin-left: 50px !important; margin-top: 8px !important;">'.$data["insname"].'</div>';
			
			$row[]=$profile_image;
			
		}
		else if($display_cols[$i]=="instrument_name")
		{
			$instrument_name =$data["instrument_name"];
			$row[]=$instrument_name;
			
		}
		
		else if($display_cols[$i]=="type")
		{
			$type =$data["type"];
			$row[]=$type;
						
			
		}
		else if($display_cols[$i]=="start_date")
		{
			$start_date ='<span class="mnt-bg" title="'.$data["time"].' Minute Course">'.$data["start_date"].'  </span>';
			$row[]=$start_date;
			
		}
		else if($display_cols[$i]=="start_time")
		{
			$start_time =$data["start_time"];
						$row[]=$start_time;
			
		}
		else if($display_cols[$i]=="time")
		{
			$time =$data["time"];
						$row[]=$time." Minutes";
			
		}
		else if($display_cols[$i]=="cost")
		{
			$cost =$data["symbol"].''.$data["cost"];
						$row[]=$cost;
			
		}
		
			
			else if($display_cols[$i]=="video_link")
		{
			
			
			if($data["video_id"]!="")
							{
								$video_url=base64_encode(serialize($_REQUEST['user_code']))&base64_encode(serialize($data['course_code']))&$data['video_link']."&Recorded";
								$video_link ='<a href="javascript:void(0)" rel="'.$video_url.'" class="viewVideos" title="live class" onclick=javascript:viewvideos(\''.$data['course_code'].'\'); ><i class="fa fa-video-camera" alt="Video" title="Click here to view video(s)"></i></a>';
							}
							else
							{
								/*$video_link ='<i class="fa fa-video-camera" alt="No Video" title="No Videos"></i>';*/
								$video_link ='<p> N/A</p>';
											
							}
							
							$row[]=$video_link;	
						
			
		}
			else if($display_cols[$i]=="note_id")
		{
			
			if($data["note_id"]!="")
							{
												/*$notes_url= $obj->getLink("ViewNotes","",true,$obj->getConcat("ccode=",base64_encode(serialize($data["course_code"]))));*/
												
												$note_id ='<a href="#" onclick = javascript:setnotesData("'.base64_encode(serialize($data["course_code"])).'") class="Second_link"><i class="fa fa-file-text-o" alt="Notes" title="Notes"></i>
											</a>';
							}
							else
							{
											
							/*	$note_id ='<i class="fa fa-file-text" alt="No Notes" title="No Notes"></i>';*/
								$note_id ='<p> N/A</p>';
							}
											
							$row[]=$note_id;
							
							
						
			
					}
					
				}
				
					
						
							
			}
				$data_new[]=$row;	
		}			
							
				
	}	
else
{
 $row=array();
 $data_new['empty']=$row;
}
	
	$return_result= array(
	
	"recordsTotal"    => $count,
	"recordsFiltered" => $count,
	"data"            => $data_new,
	
);
echo json_encode($return_result,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
die();

		
	//echo $cont;
?>