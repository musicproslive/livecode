<?php 
/****************************************************************************************
Created by	:	Arun 
Created on	:	13-07-2011
Purpose		:	To Manage Courses
******************************************************************************************/
class setcoursePrice extends modelclass
	{
		public function setcoursePriceListing()
		{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return;
			
		}
		
		public function setcoursePriceFetch(){//edited by Bhaskar
			
				 // Connect to MySQL database
					$page 			= 	0;	// The current page
					$sortname 		= 	'ul.created';	 // Sort column
					$sortorder	 	= 	'desc';	 // Sort order
					$qtype 			= 	'';	 // Search column
					$query 			= 	'';	 // Search string
					// Get posted data
				if (isset($_POST['page'])) 
					{
						$page 		= 	mysql_real_escape_string($_POST['page']);
					}
				if (isset($_POST['sortname'])) 
					{
						$sortname 	= 	mysql_real_escape_string($_POST['sortname']);
					}
				if (isset($_POST['sortorder'])) 
					{		
						$sortorder 	= 	mysql_real_escape_string($_POST['sortorder']);		
					}
				if (isset($_POST['qtype'])) 
					{
						$qtype 		= 	trim(mysql_real_escape_string($_POST['qtype']));
					}
				if (isset($_POST['query'])) 
					{
						$query		=	str_replace(' ','',($_POST['query']));
						$query 		= 	trim(mysql_real_escape_string($query));
					}
				if (isset($_POST['rp'])) 
					{
						$rp 		= 	mysql_real_escape_string($_POST['rp']);
					}
				if(empty($rp))
					{
						$rp			=	LMT_SITE_ADMIN_PAGE_LIMIT;
					}
			//	$searchSql			=	" and  user_category_id=1 ";
				if(!empty($_GET['field']) && !empty($_GET['keyword']))
					{
						$searchSql		.=	" AND `".$_GET['field']."`='".$_GET['keyword']."%' ";
					}
				// Setup sort and search SQL using posted data
				$sortSql				 = 	"order by  $sortname $sortorder ,cu.is_deleted ASC";
				$searchSql 				.= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				
				$date					=	date("Y-m-d H:i:s");
				$sql					= 	"SELECT count(*) from 
												tblcourse_prices AS pr 
													LEFT JOIN `tbllookup_instructor_level` as  tl 
														ON tl.`id`=pr.`instructor_level` 
													LEFT JOIN `tbllookup_course_duration` as  d  
														ON d.`id`=pr.`duration` 
													LEFT JOIN `tbllookup_course_type` AS ct 
														ON ct.`id`=pr.`course_type`
											 		LEFT JOIN `tblcurrency_type` AS c 
														ON c.`currency_id`=pr.`currency_type` 
													WHERE  1  $searchSql";
				$result 				= 	$this->db_query($sql,0);
				$row 					= 	mysql_fetch_array($result);
				$total					= 	$row[0];
				// Setup paging SQL
				$pageStart 				= 	($page-1)*$rp;
				if($pageStart<0)
				{
					$pageStart		=	0;
				}
				$limitSql 				= 	"limit $pageStart, $rp";
				// Return JSON data
				$data 					= 	array();
				$data['page'] 			= 	$page;
				$data['qtype'] 			= 	$qtype;
				$data['query'] 			= 	$query;
				$data['total'] 			= 	$total;
				$data['rows'] 			= 	array();
				
				/* $sql					=	" SELECT tpc.id, tpc.flag_book_name, tcd.time, tcc.type, cost, tpc.take_percent, tpc.status
												FROM tbl_pmmfb_cost as tpc
											   LEFT JOIN tbllookup_course_duration as tcd
												ON  duration = tcd.id 
											   LEFT JOIN tbllookup_course_type as tcc
												ON  course_type = tcc.id "; */
				$sql 					= "SELECT  tl.`level_name`,d.`time`,ct.type,c.`symbol`,pr.`cost`,pr.`id`
											From `tblcourse_prices` as pr
											LEFT JOIN `tbllookup_instructor_level` as  tl ON tl.`id`=pr.`instructor_level`
											LEFT JOIN `tbllookup_course_duration` as  d  ON d.`id`=pr.`duration` 
											LEFT JOIN `tbllookup_course_type` AS ct ON ct.`id`=pr.`course_type`
											LEFT JOIN `tblcurrency_type` AS c ON c.`currency_id`=pr.`currency_type` WHERE 1 order by id desc limit 0, 25";
				
// 		        echo "hello world!";
// 				$results 	= 	$this->db_query($sql,0);
				$re			=	$this->getdbcontents_sql($sql);
					//file_put_contents("file.txt",$sql);
					//file_put_contents("file1.txt",$results);
				//$data['results'] = $re[0]['id'];
 				$i			=	$pageStart;
				foreach($re as $row) 
					{	$i++;
						echo $row['id'];
						$row['edit']	=	"<a href=\"setcoursePrice.php?actionvar=Editform&id=".$row['id']."\" class=\"Second_link\">
											<img src=\"../images/edit.png\" border=\"0\" title=\"Edit Details\"></a>";
											
						$row['delete']	=	"<a href=\"setcoursePrice.php?actionvar=Deletedata&id=".$row['id']."\" class=\"Second_link\" onClick='return askDelete()'>
											<img src=\"../images/delete.png\" border=\"0\" title=\"Click here to delete\"></a>";
						
						 if($row["status"]==0)
							{
								$row["status"]="<a href=\"setcoursePrice.php?actionvar=ChangeStat&id=".$row['id']."\" class=\"Second_link\">Inactive</a>";
							}
						else
							{
								$row["status"]="<a href=\"setcoursePrice.php?actionvar=ChangeStat&id=".$row['id']."\" class=\"Second_link\">Active</a>";
							}
								
						$data['rows'][] = array
						(
							'id' => $row['id'],
							'cell' => array($i, $row['level_name'],$row['time']." Min",$row['type'],$row['cost']. $row['symbol'],"<a href='?actionvar=ViewForm&id=$row[id]' ><img src='../images/view.png' /></a>",$row["edit"],$row["delete"]) 
						);
					}
				
				
				
				$r =json_encode($data);
				ob_clean();
				echo  $r;
				exit;
		
		}
		
		public function setcoursePriceAddform(){//edited by Bhaskar
			
			//added by Bhaskar
			$FlagsObj= new catSubcatManagement();
			
			$sql					=		"SELECT * FROM `tbllookup_instructor_level`";
			$data["level"]			=		$this->getdbcontents_sql($sql);
			
			//added by Bhaskar
// 			$sql					=		"SELECT * FROM `tblinstrument_master`";
// 			$data["instruments"]	=		$this->getdbcontents_sql($sql);
			
			$sql					=		"SELECT * FROM `tbllookup_course_type`";
			$data["course_type"]	=		$this->getdbcontents_sql($sql);
			
			$sql					=		"SELECT * FROM `tbllookup_course_duration`";
			$data["time"]			=		$this->getdbcontents_sql($sql);
			
			$data["currency"]		=		$this->get_combo_arr("currency_type", $this->getdbcontents_cond("tblcurrency_type"), "currency_id", "symbol","currency_id");
			
			//added by Bhaskar
			//$data['flags']=$FlagsObj->getAllFlags(0);
			
			//echo '<pre>';
			//print_r($data);
			//echo '</pre>';die();
			
			
			return $data;
			exit;
		}
		
		public function setcoursePriceEditform(){//edited by Bhaskar
		
			$edit_id= $_GET['id'];
			$FlagsObj= new catSubcatManagement();
			$data = array();
			$sql					=		"SELECT * FROM `tbllookup_instructor_level`";
			$data["level"]			=		$this->getdbcontents_sql($sql);
			
		 	$sql					=		"SELECT * FROM `tbllookup_course_type`";
			$data["course_type"]	=		$this->getdbcontents_sql($sql);
			
// 			$sql					=		"SELECT * FROM `tblinstrument_master`";
// 			$data["instruments"]	=		$this->getdbcontents_sql($sql);
			
			$sql					=		"SELECT * FROM `tbllookup_course_duration`";
			$data["time"]			=		$this->getdbcontents_sql($sql);
			
			$data["currency"]		=		$this->get_combo_arr("currency_type", $this->getdbcontents_cond("tblcurrency_type"), "currency_id", "symbol","1");
			
			
			$sql					=		"SELECT * FROM `tblcourse_prices` WHERE `id`=".$_GET["id"];
			$data["data"]			=		$this->getdbcontents_sql($sql);
			
			//$data['flags']=$FlagsObj->getAllFlags(0);
			
			/* $sql					=	" SELECT tpc.`flag_book_name`, tcd.`time`, tcc.`type`, `cost`, tpc.`take_percent`, tpc.`status`, (select `flag_id` from `tbl_pmmfb_flags` where `flagbook_id` = tpc.`id`)as flag_id, tpi.`instructor_Level_id`, tppi.`instrument_id`, ct.`symbol`
												FROM `tbl_pmmfb_cost` as tpc
											   LEFT JOIN `tbllookup_course_duration` as tcd
												ON  `duration` = tcd.`id`
											   LEFT JOIN `tbllookup_course_type` as tcc
												ON  tpc.`course_type` = tcc.`id`
											   LEFT JOIN `tbl_pmmfb_instlevel` as tpi
											   	ON tpi.`flagbook_id` = tpc.`id`
											   LEFT JOIN `tbl_pmmfb_instruments` as tppi
											   	ON tppi.`flagbook_id` = tpc.`id`
											   LEFT JOIN `tblcurrency_type` as ct
											   	ON ct.`currency_id` = 1
												WHERE tpc.`id` = $edit_id "; */
			
			$sql 					= "SELECT  tl.`level_name`,d.`time`,ct.type,c.`symbol`,pr.`cost`,pr.`id`
												From `tblcourse_prices` as pr
												LEFT JOIN `tbllookup_instructor_level` as  tl ON tl.`id`=pr.`instructor_level`
												LEFT JOIN `tbllookup_course_duration` as  d  ON d.`id`=pr.`duration`
												LEFT JOIN `tbllookup_course_type` AS ct ON ct.`id`=pr.`course_type`
												LEFT JOIN `tblcurrency_type` AS c ON c.`currency_id`=pr.`currency_type` WHERE pr.id = $edit_id";
				
				//$sql		.=	$searchSql.$sortSql.$limitSql;	
				//file_put_contents("file.txt",$sql);
			$all_flagbook	= 	$this->getdbcontents_sql($sql);
			$result=array();
			$result['id'] = $edit_id;
			foreach ($all_flagbook as $row)
				{
					$result['level_name'] = $row['level_name'];
					$result['cost'] = $row['cost'];
					$result['course_type'] = $row['type'];
					$result['duration'] = $row['time'];
					$result['symbol'] = $row['symbol'];
								
								
				}
					
			$data['current_value']=$result;
			
			 
			/* echo '<pre>';
			print_r($result);
			echo '</pre>';die();  */
			return $data;
			//exit;
		}
		
		public function setcoursePriceViewform()
			{	//die("This is view page");
				$data				=	$_GET; //$this->getData("get");
				
				$sql				=	"SELECT  tl.`level_name`,d.`time`,ct.type,c.`symbol`,pr.`cost`,pr.`id`
									From `tblcourse_prices` as pr
									LEFT JOIN `tbllookup_instructor_level` as  tl ON tl.`id`=pr.`instructor_level`
									LEFT JOIN `tbllookup_course_duration` as  d  ON d.`id`=pr.`duration` 
									LEFT JOIN `tbllookup_course_type` AS ct ON ct.`id`=pr.`course_type`
									LEFT JOIN `tblcurrency_type` AS c ON c.`currency_id`=pr.`currency_type`
									WHERE  pr.`id`=".$data["id"];
				$data["data"]		=		end($this->getdbcontents_sql($sql));
				return $data;
			}
		public function setcoursePriceSavedata(){//edited by Bhaskar
								
				$data = array('instructor_level' => $_POST['instructor_level'], 'duration' => $_POST['duration'], 'course_type' => $_POST['course_type'], 'cost' => $_POST['cost'], 'currency_type'=> $_POST['currency_type'], 'status' => 1);
								
				//$dataIns	=	$this->populateDbArray("tbl_pmmfb_cost",$_POST);
				$ids=$this->db_insert("tblcourse_prices", $data);
				if( $ids == TRUE ) {
					$this->redirectAction(false,"Successfully added !!","Listing");
				}else {
					$this->redirectAction(false,"Operation Failed !!","Listing");
				} 
		
				
		}
		
	public function setcoursePriceUpdatedata() {//edited by Bhaskar
		
			$upd = $this->db_update("tblcourse_prices",array("instructor_level"=> $_POST['instructor_level'], "duration"=>$_POST['duration'] ,"course_type"=>$_POST['course_type'] ,"currency_type"=>$_POST['currency_type'] , "cost"=>$_POST['cost'] ),"id = ".$_POST['inst_id'], 0 );
			if($upd==TRUE) {
				$this->redirectAction(false,"Successfully updated !!","Listing");	
			}else {
				$this->redirectAction(false,"Update Failed !!","Listing");			
			}
		}
		
		
		
		
		public function setcoursePriceDeletedata()//edited by Bhaskar
			{	
				$delete_id	=	$_GET['id'];
			
				$sql="SELECT * FROM  tblcourse_prices WHERE flagbook_id	=	$delete_id";
				$NumRows=$this->getdbcount_sql($sql);
				
				if($NumRows>0)
					{
					$this->redirectAction(false,"This Book Is Set In Instructor Profile.Please First Remove It From All Instructor Profile !! ","Listing");
					}
				else
					{
				
						/* $details	=	$this->getData("get");
						$query		=	"DELETE FROM `tbl_pmmfb_cost` WHERE `id` = {$details['id']} LIMIT 1";
						$results 	= 	$this->db_query($query,0);	
						
						$query		=	"DELETE FROM `tbl_pmmfb_instruments` WHERE `flagbook_id` = {$details['id']} LIMIT 1";
						$results 	= 	$this->db_query($query,0);	
						
						$query		=	"DELETE FROM `tbl_pmmfb_instlevel` WHERE `flagbook_id` = {$details['id']} LIMIT 1";
						$results 	= 	$this->db_query($query,0);	
						
						$query		=	"DELETE FROM `tbl_pmmfb_flags` WHERE `flagbook_id` = {$details['id']} LIMIT 1";
						$results 	= 	$this->db_query($query,0);	 */
						$query		=	"DELETE FROM `tblcourse_prices` WHERE `id` = {$delete_id} ";
						$results	=	$this->db_query($query, 0);
						
						$this->redirectAction(false,"Course Prices sucessfully deleted ","Listing");
					}
			}
		public function approvedCourseReset()
			{
				$this->clearData("Search");
				$this->executeAction(false,"Listing",true,false);	
			}
		public function redirectAction($loadData=true,$errMessage,$action)	
			{	
				$this->setPageError($errMessage);
				$this->executeAction($loadData,$action,true);	
			}		
		public function __construct()
			{
				$this->setClassName();
				$this->tab_defaults_group	=	"tbluser_category";
			}
		
		public function getcourseDetails($membersId="",$args="1")
			{  
			$sql	=  "SELECT cu.*,lm.name,uu.first_name,ct.name as currencytype,ct.symbol,lo.level_name,
						DATE_FORMAT(cu.course_start_date,'%b %d %Y') AS course_start_date,
						DATE_FORMAT(cu.course_end_date,'%b %d %Y') AS course_end_date,
						DATE_FORMAT(cu.create_date,'%b %d %Y %h:%i %p') AS create_date,
						DATE_FORMAT(cu.default_start_time,'%h:%i %p') AS default_start_time,
						time.timezone_location
						from tblcourse_master as cu
						left join tblinstrument_master as lm on cu.course_instrument_id =lm.instrument_id 
						left join tblcurrency_type as ct on cu.currency_id=ct.currency_id
						left join tblusers as uu
						on cu.tutor_id =uu.user_id
						left join tbltime_zones AS time on time.id=cu.time_zone_id
						Left Join tbllookup_tutor_level as lo on uu.`expert_level`=lo.`id`
						where cu.is_deleted=0  
						and course_master_id='$membersId' and ".$args;
			
				$result	=	end($this->getdbcontents_sql($sql));
				$sql	= "SELECT uc.class_name,uc.class_date,uc.start_time,
							DATE_FORMAT(uc.class_date,'%b %d %Y') AS class_date,
						    DATE_FORMAT(uc.end_time,'%b %d %Y')  
							AS end_date,DATE_FORMAT(uc.end_time,'%h:%i %p')  
							AS end_time
							from tblcourse_master as cu
							left join tbluser_class as uc on uc.course_master_id = cu.course_master_id 
							where cu.is_deleted=0  
							and cu.course_master_id='$membersId' order by uc.class_date asc";
				$data	=	$this->getdbcontents_sql($sql,0);
				$result['classes'] = $data;
				return $result;
			}
		
		public function deletecourse($id)
			{
				$query 		= 	"UPDATE tblcourse_master  SET is_deleted='1' WHERE course_master_id='$id'";
				$result		=	$this->getdbcontents_sql($query);
				return $this->executeAction(false,"Listing",true);
			}
		
		public function getAll($stat="")
			{
				$data				=	$this->getdbcontents_cond("tblcurrency_type");
				return $data; 
			}
		
		public function setcoursePriceChangeStat()//added by Bhaskar
			{	$id=$_GET['id'];
				//echo $id;die();
				$sql="SELECT status FROM tbl_pmmfb_cost WHERE id=$id";
				$result=$this->getdbcontents_sql($sql,0);
				foreach($result as $row)
				{
					if($row['status']==0)
						{	$data['status']=1;
							$this->db_update("tbl_pmmfb_cost",$data,"id=".$id,0);
						}
					else
						{
							$data['status']=0;
							$this->db_update("tbl_pmmfb_cost",$data,"id=".$id,0);
						}
				
				
				
				}
				$this->redirectAction(false,"Status Successfully Changed !!","Listing");		
			}
		
		public function executeAction($loadData=true,$action="",$navigate=false,$sameParams=false,$newParams="",$excludParams="",$page="")
			{
				
				if(trim($action))	$this->setAction($action);//forced action
				$methodName	=		(method_exists($this,$this->getMethodName()))	? $this->getMethodName($default=false):$this->getMethodName($default=true);
				$this->actionToBeExecuted($loadData,$methodName,$action,$navigate,$sameParams,$newParams,$excludParams,$page);
				$this->actionReturn		=	call_user_func(array($this, $methodName));				
				$this->actionExecuted($methodName);
				return $this->actionReturn;
			}
		public function __destruct() 
			{
				parent::childKilled($this);
			}
	}