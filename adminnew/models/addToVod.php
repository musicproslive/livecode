<?php
/**************************************************************************************
Created by : Prem Pranav
Created on : 9th October 2012
Purpose    : Add Taught Courses To VOD 
************************************** ************************************************/
class addToVod extends modelclass
	{
		public function addToVodListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				$user						=	new userManagement();
				$tutor			=	 $user->getInstructors();
				$instmnt		=	 new instrument();
				$instrument_list=	 $instmnt->getAllInstruments();
				$courses		=	 new userCourse();
				$course_list	=	$courses->getAllTaughtCourses();
				
				unset($_SESSION["txtCourse"]);
				unset($_SESSION["txtInst"]);
				unset($_SESSION['instrument_id']);
							
				if(!empty($_POST["txtInst"])){
					$_SESSION["txtInst"]	=	trim($_POST["txtInst"]);
				}
				if(!empty($_POST["txtCourse"])){
					$_SESSION["txtCourse"]	=	trim($_POST["txtCourse"]);
				}
				if(!empty($_POST['instrument_id'])){
				$_SESSION["instrument_id"]		=	$_POST["instrument_id"];
				}		
				
				$txtInst		=	$_POST["txtInst"];
				$txtCourse		=	$_POST["txtCourse"];
				$instrument_id	=	$_POST["instrument_id"];
			
				return array("tutor"=>$tutor,"instrument"=>$instrument_list,"course"=>$course_list,"txtInst"=>$txtInst,"txtCourse"=>$txtCourse,"instrument_id"=>$instrument_id);
			}
		public function addToVodFetch(){
		
				// Connect to MySQL database
				$page 			= 	0;	// The current page
				$sortname 		= 	'V.video_taken';	 // Sort column
				$sortorder	 	= 	'desc';	 // Sort order
				$qtype 			= 	'';	 // Search column
				$query 			= 	'';	 // Search string
				// Get posted data
				if (isset($_POST['page'])) 
					{
						if($_POST['page']==1 && isset($_SESSION['PAGE'][$this->getPageName()][$this->getAction()]) && empty($_POST['query']))
							{
								if($_SESSION['PAGE'][$this->getPageName()][$this->getAction()] == 2 && $this->previousAction ==	$this->currentAction)	$page	=	1;
								else $page		=	$_SESSION['PAGE'][$this->getPageName()][$this->getAction()];
									
							}
						else
							{
								$page 				= 	mysql_real_escape_string($_POST['page']);
								$_SESSION['PAGE'][$this->getPageName()][$this->getAction()]	=	$page;
							}
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
				if(isset($_SESSION['QUERY'][$this->getPageName()][$this->getAction()]))
							{
								if(trim(mysql_real_escape_string($_POST['query'])) == '' && $this->previousAction ==	$this->currentAction)
									{
										// User is assiging query keyword as empty 
										$query	=	'';
										$_SESSION['QUERY'][$this->getPageName()][$this->getAction()]	=	$query;
									}
								else
									{
										//User is Refreshing page or coming back to viewed page 
										$query	=	$_SESSION['QUERY'][$this->getPageName()][$this->getAction()];
										$qtype	=	$_SESSION['QTYPE'][$this->getPageName()][$this->getAction()];
									}
							}	
						if (!empty($_POST['query'])) 
							{
								$query 		= 	trim(mysql_real_escape_string($_POST['query']));
								$_SESSION['QUERY'][$this->getPageName()][$this->getAction()]	=	$query;
								$_SESSION['QTYPE'][$this->getPageName()][$this->getAction()]	=	trim(mysql_real_escape_string($_POST['qtype']));
							}
				if (isset($_POST['rp'])) 
					{
						$rp 		= 	mysql_real_escape_string($_POST['rp']);
					}
				if(empty($rp))
					{
						$rp			=	LMT_SITE_ADMIN_PAGE_LIMIT;
					}
					
				// Setup sort and search SQL using posted data
				$sortSql				 = 	" order by $sortname $sortorder ";
				$searchSql 				 = 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '$query%'" : '';
				// Get total count of records
				
				$instructor		=	$_SESSION['txtInst'];
				$course			=	$_SESSION['txtCourse'];
				$instrument_id	=	$_SESSION['instrument_id'];
				
				$sql			= 	"SELECT count(*) FROM tblcourse_videos AS V 
												LEFT JOIN tblcourses AS C ON V.course_id=C.course_id AND V.video_owner_id=C.instructor_id 
												LEFT JOIN tblusers AS U ON C.instructor_id=U.user_id
												LEFT JOIN tblinstrument_master AS I ON I.instrument_id=C.instrument_id
												WHERE C.course_status_id=4 AND is_active_vod=0 $searchSql";
												if($_SESSION['txtInst'])
												$sql	.=" AND concat(U.first_name,' ',U.last_name) LIKE '%$instructor%' ";
												if($_SESSION['txtCourse'])
												$sql	.=" AND C.title LIKE '%$course%'";
												if($_SESSION['instrument_id'])
												$sql	.=" AND C.instrument_id=$instrument_id ";
												
			// C.title='$course' AND concat(U.first_name,' ',U.last_name)='$instructor',C.instrument_id=$instrument_id,
										
												
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
				
				$sql 					= 	"SELECT C.course_id,C.course_code, C.title,I.name,CONCAT(U.first_name,' ',U.last_name) AS instructor_name,V.video_link,
												DATE_FORMAT(V.video_taken,'".$_SESSION["DATE_FORMAT"]["M_DATE"]." , ".$_SESSION["DATE_FORMAT"]["M_TIME"]."') AS video_taken
												FROM tblcourse_videos AS V 
												LEFT JOIN tblcourses AS C ON V.course_id=C.course_id AND V.video_owner_id=C.instructor_id 
												LEFT JOIN tblusers AS U ON C.instructor_id=U.user_id
												LEFT JOIN tblinstrument_master AS I ON I.instrument_id=C.instrument_id
												WHERE C.course_status_id=4 AND is_active_vod=0 ";
												if($_SESSION['txtInst'])
												$sql	.=" AND concat(U.first_name,' ',U.last_name) LIKE '%$instructor%' ";
												if($_SESSION['txtCourse'])
												$sql	.=" AND C.title LIKE '%$course%'";
												if($_SESSION['instrument_id'])
												$sql	.=" AND C.instrument_id=$instrument_id ";
												
												$sql	.= "$searchSql	$sortSql $limitSql";
											
				//file_put_contents("file.txt",$sql);
				$query="SELECT U.user_code FROM tbluser_login AS L LEFT JOIN tblusers AS U ON L.login_id=U.login_id WHERE L.login_id=".$_SESSION['log_id'];
				$admin =end($this->getdbcontents_sql($query,0));
				
				$results 				= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						 $row['play']='<a href="javascript:void(0);" onclick="viewVideos(\''.base64_encode(serialize($admin["user_code"])).'&'.base64_encode(serialize($row["course_code"])).'&'.$row["video_link"].'&Recorded\')" class="vdo_link"><img src="../images/right_arrow.png" border="0" title="Click here to play" ></a>';
						 
					
						$row['action']='<a href="addToVod.php?actionvar=Add&id='.$row['course_id'].'" ><img src="images/addnew.png" title="Move this video to VOD" ></a>';
						$data['rows'][] = array
					(
				'id' => $row['id'],
				'cell' => array($i, $row['title'], $row['name'], $row['instructor_name'], $row['video_taken'], $row['play'], $row['action'])
					);
				}
				ob_clean();
				$r =json_encode($data);
				
				echo  $r;
				exit;
		}	
		public function addToVodAdd()
			{
				$data=$this->getData("request");
				$sql			=	"select * from  tblcurrency_type where is_deleted=0";
				$list		=	$this->getdbcontents_sql($sql);
				$listCombo	=	$this->get_combo_arr("currency_id",$list,"currency_id","symbol","1","style='width:50px;' ","Select Currency");
				return array("dropDown"=>$listCombo,"course_id"=>$data['id']);
			}
		public function addToVodSave()
			{
				$data				=	$this->getData("request");
				//print_r($data);exit;
				if($data['currency_id'] =='')
				{	
					$this->setPageError("Please Enter Currency Type");
					$this->executeAction(true,"Add",true,true);
				}
				
				$data['created_date']	=	date("Y-m-d H:i:s");
				$sql="SELECT user_id FROM tblusers AS U LEFT JOIN tbluser_login AS L ON L.login_id=U.login_id WHERE L.login_id=".$_SESSION['log_id'];
				$user=end($this->getdbcontents_sql($sql,0));
				$data['created_by']	=	$user['user_id'];
				$data['status']=1;
				$dataIns			=	$this->populateDbArray("tblcourse_vod",$data);
				$insertStatus		=	$this->db_insert("tblcourse_vod",$dataIns, 0);
				if($insertStatus) 
					{
						$sql="UPDATE tblcourses SET is_active_vod=1 WHERE course_id=".$data['course_id'];
						$data	=	mysql_query($sql);
						//$dataIns=array("is_active_vod"=>1);
						//$updateStatus		=	$this->db_update("tblcourses", $dataIns, $this->dbSearchCond("=","course_id",$data['course_id']));
						$this->setPageError("Your Video Moved To VOD");
						$this->executeAction(false,"Listing",true);
					}
				else
					{
						$this->setPageError("Problem in sending video to VOD List Try Again!");
						 $this->executeAction(true,"Add",true);
					}	
			}
		public function addToVodSearch()
			{
				$this->executeAction(false,"Listing",true);
			}	
		public function addToVodReset()
			{
				$this->clearData("Search");
				header("Location: addToVod.php");
				exit;	
			}
		public function addToVodCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}	
		public function __construct()
			{
				$this->setClassName();
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
