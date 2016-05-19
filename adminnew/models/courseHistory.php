<?php
/****************************************************************************************
Created by	:	Arun
Created on	:	13-07-2011
Purpose		:	To Manage Courses
******************************************************************************************/
class courseHistory extends modelclass
	{
		public function courseHistoryListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				$user						=	new userManagement();
				$query						=	"SELECT * FROM `tbllookup_course_status`";
				$rec["status"]				=	$this->getdbcontents_sql($query);
				$rec["sel_status"]			=	$_POST["sel_status"];
				unset($_SESSION["tmp"]);unset($_SESSION["endDate"]);
				unset($_SESSION["startDate"]);unset($_SESSION["txtInst"]);
				$_SESSION["instid"]			=	'';
				if(!empty($_GET["instid"])){
					$_SESSION["instid"]			=	$_GET["instid"];
				}
				if(!empty($_POST['sel_status'])){
					$_SESSION["tmp"]		=	$_POST["sel_status"];
				}

				$rec["tutor"]		=	 $user->getInstructors();

					if(!empty($_POST["txtInst"])){
						$_SESSION["txtInst"]	=	trim($_POST["txtInst"]);
					}


				//created_on
				if(!empty($_POST["txtStartDate"]) || !empty($_POST["txtEndDate"])){

					if(!empty($_POST["txtStartDate"])){
						$_SESSION["startDate"]	=	($_POST["txtStartDate"]);
					}
					if(!empty($_POST["txtEndDate"])){
						$_SESSION["endDate"]	=	($_POST["txtEndDate"]);
					}
					$rec["s_time"]	=	$_POST["txtStartDate"];
					$rec["e_time"]	=	$_POST["txtEndDate"];

				}
				$rec["txtInst"]	=	$_POST["txtInst"];

				return $rec;

			}
		public function courseHistoryFetch()
			{
					// Connect to MySQL database
					$page 			= 	1;	// The current page
					$sortname 		= 	'c.course_id';	 // Sort column
					$sortorder	 	= 	'asc';	 // Sort order
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
					$sortSql				 = 	" order by CONCAT(c.start_date, c.start_time)  DESC, $sortname $sortorder";
					$searchSql 				= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';

					$sql					= 	"SELECT count(*)	FROM `tblcourses` c
					LEFT JOIN `tblusers` u 	ON u.`user_id`=c.`instructor_id`  WHERE 1 $searchSql";

					if(!empty($_SESSION["instid"])){
							$sql	.=	" AND c.instructor_id =  '".$_SESSION["instid"]."' ";
					}


		  			if(!empty($_SESSION["tmp"])){
				   		 $sql		.=	 " AND c.course_status_id =".$_SESSION["tmp"];
				    }

					if(!empty($_SESSION["txtInst"])){
						 $sql		.=	 " AND CONCAT(u.`first_name`,' ',u.`last_name`) LIKE '".trim($_SESSION["txtInst"])."%' ";
					}

					if(!empty($_SESSION["startDate"])){
						 $sql		.=	 " AND UNIX_TIMESTAMP(c.start_date)>=".strtotime($_SESSION["startDate"]);
					}
					if(!empty($_SESSION["endDate"])){
						$sql		.=	 " AND UNIX_TIMESTAMP(c.start_date)<".strtotime($_SESSION["endDate"]);
					}

					$result 				= 	$this->db_query($sql,0);
					$row 					= 	mysql_fetch_array($result);
					$total					= 	$row[0];
					// Setup paging SQL
					$pageStart 				= 	($page-1)*$rp;
					if($pageStart<0)
						{
							$pageStart		=	0;
						}
					$limitSql 				= 	" limit $pageStart, $rp";
					// Return JSON data
					$data 					= 	array();
					$data['page'] 			= 	$page;
					$data['qtype'] 			= 	$qtype;
					$data['query'] 			= 	$query;
					$data['total'] 			= 	$total;
					$data['rows'] 			= 	array();

					$sql		=	"SELECT c.`max_students` as totalMember,(select count(`enrolled_id`)  FROM `tblcourse_enrollments` WHERE `course_id` =c.`course_id` AND `enrolled_status_id` !=".LMT_CS_ENR_CANCELLED.") as precence,c.*,CONCAT(mu.`first_name`,' ',mu.`last_name`) as specialstudent,
					DATE_FORMAT(c.start_date,'".$_SESSION["DATE_FORMAT"]["M_DATE"]."')  as  start_date,
					DATE_FORMAT(c.start_time,'". $_SESSION["DATE_FORMAT"]["M_TIME"]."') as start_time ,d.`time`,t.`type`,t.`max_students`,cu.`symbol`,
					cu.`code`,u.`first_name`,u.`last_name`,p.`cost`,
						COUNT(V.`id`) AS video_id, COUNT(N.`id`) AS note_id,(select count(`id`)  FROM `tblcourse_sheetmusic` WHERE `course_id` =c.`course_id` )
						 As sheetMusic, COUNT(r.`rated_by`) as totalrated, SUM(r.`rating`) as totalR
					FROM `tblcourses` c
					LEFT JOIN `tblcourse_enrollments` e 	ON e.`course_id` =	c.`course_id`
					LEFT JOIN `tblinstrument_master` i 		ON i.instrument_id= c.instrument_id
					LEFT JOIN `tblcourse_prices` p 	 		ON p.id = c.price_code
					LEFT JOIN `tbllookup_course_duration` d	ON d.`id`=c.`duration`
					LEFT JOIN `tbllookup_course_type` t 	ON t.`id`=p.`course_type`
					LEFT JOIN `tblcurrency_type` cu 		ON cu.`currency_id` =p.`currency_type`
					LEFT JOIN `tblusers` u 					ON u.`user_id`=c.`instructor_id`

					LEFT JOIN `tblcourse_ratings` r  ON  r.`course_id`=c.`course_id`

					LEFT JOIN `tblcourse_master_student` ms ON  ms.`course_id` = c.`course_id`
					LEFT JOIN `tblusers` mu ON mu.`user_id`=ms.`student_id`

					LEFT JOIN `tblcourse_archives` AS V ON V.course_id = c.course_id
					LEFT JOIN `tblcourse_notes` AS N ON N.course_id = c.course_id

					WHERE  1 $searchSql";

					if(!empty($_SESSION["instid"])){
							$sql	.=	" AND c.`instructor_id` =  '".$_SESSION["instid"]."' ";
					}

					if(!empty($_SESSION["tmp"])){
				   		 $sql		.=	 " AND c.`course_status_id`=".$_SESSION["tmp"];
				    }

					if(!empty($_SESSION["txtInst"])){
						 $sql		.=	 " AND CONCAT(u.`first_name`,' ',u.`last_name`) LIKE '".trim($_SESSION["txtInst"])."%' ";
					}

					if(!empty($_SESSION["startDate"])){
						 $sql		.=	 " AND UNIX_TIMESTAMP(c.`start_date`)>=".strtotime($_SESSION["startDate"]);
					}

					if(!empty($_SESSION["endDate"])){
						$sql		.=	 " AND UNIX_TIMESTAMP(c.`start_date`)<".strtotime($_SESSION["endDate"]);
					}

					$sql		.=	"  GROUP BY c.`course_id`  ";

					$sql		.=	" ".$sortSql." ".$limitSql;

					$results 	= 	$this->db_query($sql, 0);


					$k		=	$i			=	$pageStart;

					while ($row = mysql_fetch_assoc($results))
						{
							$k++;
							$sp		=	'';
							$rateGif		=	"";

							$rate				=	ceil($row["totalR"]/$row["totalrated"]);
							for($i=0;$i<$rate;$i++){
								$rateGif		.=	"<img src='images/star_active.gif' />";
							}

							for($i=0;$i<(5%$rate);$i++){
								$rateGif		.=	"<img src='images/star_inactive.gif' />";
							}
							$courseStatus= "";
							if($row["course_status_id"] == LMT_COURSE_STATUS_CANCELLED)
								$courseStatus= '<img src="../images/course_cancelled.gif" alt="Cancelled" title="Course Cancelled"/>';
							else if($row["course_status_id"] == LMT_COURSE_STATUS_TAUGHT)
								$courseStatus= '<img src="../images/course_completed.png" alt="Taught" title="Course Taught"/>';
							else if($row["course_status_id"] == LMT_COURSE_STATUS_CLOSED)
								$courseStatus= '<img src="../images/course_closed.png" alt="Taught" title="Course Closed"/>';
							else
								$courseStatus= '<img src="../images/course_open.png" alt="Enrolled" title="Course Enrolled"/>';

							$sp		=	"<a href='courseHistory.php?actionvar=Editform&course=".$row['course_id']."'><img src='../images/view.png' /></a>";

							if($row['video_id']){
								$video_link='<img src="../images/video-active.png" alt="video" title="Video"';
								$video_link=$video_link.' onclick="liveClass(\''.base64_encode(serialize($row["user_code"])).'\',\''.base64_encode(serialize($row["course_code"])).'\',\''.$row["video_id"].'\',\'AdminDisp\')"/>';

							}
							else	$video_link='<img src="../images/video-not-active.png" alt="no video" title="no video"/>';

							if($row['note_id']) $note_link='<a href="courseHistory.php?actionvar=ViewNotes&ccode='.$row['course_code'].'" class="Second_link"><img src="../images/note.png" alt="note" title="note"/></a>';
							else	$note_link='<img src="../images/note-inactive.png" alt="no note" title="no notes"/>';

							$music		="";
							if(!empty($row["sheetMusic"])){
									$music		=	"<a href='courseHistory.php?actionvar=SheetMusic&course=".$row['course_id']."'><img src='../images/sheet.png' / title='Sheet Music'></a>";

							}
							$status		=	"";
							if($row["course_status_id"] == LMT_COURSE_STATUS_OPEN){

								$status		= '<a href="courseHistory.php?actionvar=BlockCourse&course_code='.base64_encode(serialize($row['course_code'])).'" onclick="return closeConfirm();" class="cancelCourse" ><img src="images/course_closed.png" border="0" title="Close Course"></a>';

							}
							$data['rows'][] = array(
							'id' => $row['course_id'],
							'cell' => array($k,$courseStatus,$video_link,$note_link,$music,$rateGif,$row['title'],"<a href='courseHistory.php?instid=".$row["instructor_id"]."'>".$row['first_name']." ".$row['last_name']."</a>",$row['start_date'],$row['start_time'],$row['time'],$row['symbol']." ".$row['cost'],$row["precence"]." / ".$row["totalMember"],$sp,$status)
						);
					}
					$r =json_encode($data);
					ob_clean();
					echo  $r;
					exit;

		}

		public function courseHistoryEditform(){

			$query			=	"SELECT u.*,CONCAT(ux.`first_name`,' ',ux.`last_name`) as name,u.`user_id`,ms.`student_id` as currentsp,e.enrolled_id
							 FROM `tblusers` u
							 LEFT JOIN `tblcourse_enrollments` e ON u.`user_id`=e.`student_id`
							  LEFT JOIN `tblusers` ux  ON ux.`user_id`=e.`student_id`
							 LEFT JOIN `tblcourse_master_student` ms  ON e.`course_id`=ms.`course_id`
							 WHERE
							 e.`course_id`=".$_GET['course']." AND  e.`enrolled_status_id` !=".LMT_CS_ENR_CANCELLED." ";

			$result["data"]	=	$this->getdbcontents_sql($query,0);
			return $result;


		}
	public function courseHistoryViewNotes()
		{
			return 0;

		}
	public function courseHistoryViewNotesJson()
		{
				// Connect to MySQL database
				$data			=	$this->getdata("get");
				$ccode			=	$data['ccode'];
				$page 			= 	0;	// The current page
				$sortname 		= 	'c.note_id';	 // Sort column
				$sortorder	 	= 	'asc';	 // Sort order
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
				$sortSql				= 	" order by $sortname $sortorder ";
				$searchSql 				= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';

				$sql 					= 	"SELECT count(*) FROM tblcourse_notes AS A
									LEFT JOIN tblcourses AS B on A.course_id=B.course_id
									LEFT JOIN tblusers AS C on B.instructor_id=C.user_id
									LEFT JOIN tblusers AS D on A.note_owner_id=D.user_id
									LEFT JOIN tblinstrument_master AS E on B.instrument_id=E.instrument_id
									LEFT JOIN tblcourse_enrollments AS F ON F.course_id=B.course_id
									WHERE B.course_code='$ccode' $searchSql";
				$result 				= 	$this->db_query($sql,0);
				$row 					= 	mysql_fetch_array($result);
				$total					= 	$row[0];

				// Setup paging SQL
				$pageStart 				= 	($page-1)*$rp;
				if($pageStart<0)
					{
						$pageStart		=	0;
					}
				$limitSql 				= 	" limit $pageStart, $rp";
				// Return JSON data
				$data 					= 	array();
				$data['page'] 			= 	$page;
				$data['qtype'] 			= 	$qtype;
				$data['query'] 			= 	$query;
				$data['total'] 			= 	$total;
				$data['rows'] 			= 	array();
				$sql="SELECT A.`note_status`,A.id as note_id,A.note_text,DATE_FORMAT(A.note_taken,'". $_SESSION["DATE_FORMAT"]["M_DATE"]." , ".$_SESSION["DATE_FORMAT"]["M_TIME"]."') AS notes_taken_time,
						B.title,B.`course_code`, concat(C.first_name,' ',C.last_name) as instructor_name,concat(D.first_name,' ',D.last_name) AS notes_owner_name, E.name as instrument_name FROM tblcourse_notes AS A
									LEFT JOIN tblcourses AS B on A.course_id=B.course_id
									LEFT JOIN tblusers AS C on B.instructor_id=C.user_id
									LEFT JOIN tblusers AS D on A.note_owner_id=D.user_id
									LEFT JOIN tblinstrument_master AS E on B.instrument_id=E.instrument_id
									LEFT JOIN tblcourse_enrollments AS F ON F.course_id=B.course_id
									WHERE B.course_code='$ccode' $searchSql GROUP BY A.`id` ORDER BY notes_taken_time DESC $limitSql";
				$results 	= 	$this->db_query($sql,1);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results))
					{
						if(empty($row['note_status'])){
							$activate		=	"<a href='courseHistory.php?actionvar=SaveNoteStatus&ccode=".$row['course_code']."&status=1&note_id=".$row['note_id']."'><img src='images/active.gif' title='Click to Lock Note' /></a>";
						}else{
							$activate		=	"<a href='courseHistory.php?actionvar=SaveNoteStatus&ccode=".$row['course_code']."&status=0&note_id=".$row['note_id']."'><img src='images/inactive.gif' title='Click to Unlock Note' /></a>";
						}

						$i++;
						$row['view']='<a href="courseHistory.php?actionvar=NoteText&id='.$row['note_id'].'"><img src="../images/view.gif" height="20" width="20"/></a>';
						$data['rows'][] = array(
						'id' => $row['note_id'],
						'cell' => array($i,$row['notes_owner_name'],$row['notes_taken_time'],$row['title'],"<a href='courseHistory.php?instid=".$row["instructor_id"]."'>".$row['instructor_name']."</a>",$this->getLimitedText($row['note_text'],25).$row['view'],$activate)
					);
				}

				$r =json_encode($data);
				ob_clean();
				echo  $r;
				exit;
		}

	public function courseHistorySaveNoteStatus(){
						$data				=	$this->getData("get");
						$dataUpdate			=	array();
						$dataUpdate['note_status']	=	$data["status"];
						$this->db_update("tblcourse_notes",$dataUpdate,"id ={$data['note_id']}",1);
						header("Location:courseHistory.php?actionvar=ViewNotes&ccode=".$data["ccode"]);
						exit;
	}
	public function courseHistoryNoteText()
		{
				$data				=	$this->getData("get");
				$note		=	end($this->getdbcontents_sql("SELECT A.note_text,B.course_code,B.title FROM tblcourse_notes AS A
																LEFT JOIN tblcourses AS B on A.course_id=B.course_id
																WHERE A.id=".$data['id']));
				$note_text =$note['note_text'];
				return array("data"=>$note,"description"=>$note_text);
		}
	public function courseHistoryViewVideos()
		{
			return;
		}
	public function courseHistoryViewVideosJson()
		{
					$data=$this->getdata("get");
					$ccode=$data['ccode'];
					$page 			= 	0;	// The current page
					$sortname 		= 	'c.video_id';	 // Sort column
					$sortorder	 	= 	'asc';	 // Sort order
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
					$sortSql				= 	" order by $sortname $sortorder ";
					$searchSql 				= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';

					$sql 					= 	"SELECT count(*) FROM tblcourse_videos AS A
										LEFT JOIN tblcourses AS B on A.course_id=B.course_id
										LEFT JOIN tblusers AS C on B.instructor_id=C.user_id
										LEFT JOIN tblusers AS D on A.video_owner_id=D.user_id
										LEFT JOIN tblinstrument_master AS E on B.instrument_id=E.instrument_id
										LEFT JOIN tblcourse_enrollments AS F ON F.course_id=B.course_id
										WHERE B.course_code='$ccode' $searchSql ";
					$result 				= 	$this->db_query($sql,0);
					$row 					= 	mysql_fetch_array($result);
					$total					= 	$row[0];
					// Setup paging SQL
					$pageStart 				= 	($page-1)*$rp;
					if($pageStart<0)
						{
							$pageStart		=	0;
						}
					$limitSql 				= 	" limit $pageStart, $rp";
					// Return JSON data
					$data 					= 	array();
					$data['page'] 			= 	$page;
					$data['qtype'] 			= 	$qtype;
					$data['query'] 			= 	$query;
					$data['total'] 			= 	$total;
					$data['rows'] 			= 	array();
					$sql="SELECT  A.`video_status`,A.id as video_id,A.video_thumb_link,A.video_link,DATE_FORMAT(A.video_taken,'". $_SESSION["DATE_FORMAT"]["M_DATE"]." , ".$_SESSION["DATE_FORMAT"]["M_TIME"]."') AS video_taken_time,
							B.title, B.`course_code`,concat(C.first_name,' ',C.last_name) as instructor_name,concat(D.first_name,' ',D.last_name) AS video_owner_name,
							D.`user_code` as usercode,E.name as instrument_name
										FROM tblcourse_videos AS A
										LEFT JOIN tblcourses AS B on A.course_id=B.course_id
										LEFT JOIN tblusers AS C on B.instructor_id=C.user_id
										LEFT JOIN tblusers AS D on A.video_owner_id=D.user_id
										LEFT JOIN tblinstrument_master AS E on B.instrument_id=E.instrument_id
										LEFT JOIN tblcourse_enrollments AS F ON F.course_id=B.course_id
										WHERE B.course_code='$ccode' $searchSql GROUP BY A.`id` ORDER BY video_taken_time DESC $limitSql";
				 	$results 	= 	$this->db_query($sql,0);
					$i			=	$pageStart;

					$query="SELECT U.user_code FROM tbluser_login AS L LEFT JOIN tblusers AS U ON L.login_id=U.login_id WHERE L.login_id=".$_SESSION['log_id'];
					$admin =end($this->getdbcontents_sql($query,0));
					$userCode		=	base64_encode(serialize($admin["user_code"]));//exit;


					while ($row = mysql_fetch_assoc($results))
						{


						if(empty($row['video_status'])){
								$activate		=	"<a href='courseHistory.php?actionvar=SaveVideoStatus&ccode=".$row['course_code']."&status=1&video_id=".$row['video_id']."'><img src='images/active.gif' title='Click to Lock Video' /></a>";
							}else{
								$activate		=	"<a href='courseHistory.php?actionvar=SaveVideoStatus&ccode=".$row['course_code']."&status=0&video_id=".$row['video_id']."'><img src='images/inactive.gif' title='Click to Unlock Video' /></a>";
							}
							$corseCode		=	base64_encode(serialize($row["course_code"]));
						 $d	=	'<img src="../images/video-active.png"   onclick="liveClass(\''.($userCode).'\',\''.($corseCode).'\',\''.$row["video_link"].'\',\'Recorded\')"/>';

							$i++;
							$data['rows'][] = array(
							'id' => $row['video_id'],
							'cell' => array($i,$row['video_owner_name'],$row['video_taken_time'],$row['title'],"<a href='courseHistory.php?instid=".$row["instructor_id"]."'>".$row['instructor_name']."</a>",$d,$activate)
						);
					}
					$r =json_encode($data);

					ob_clean();
					echo  $r;
					exit;
		}
	public function courseHistorySaveVideoStatus(){

						$data				=	$this->getData("get");
						$dataUpdate			=	array();
						$dataUpdate['video_status']	=	$data["status"];
						$this->db_update("tblcourse_videos",$dataUpdate,"id ={$data['video_id']}",1);
						header("Location:courseHistory.php?actionvar=ViewVideos&ccode=".$data["ccode"]);
						exit;
	}


		public function courseHistoryGetEnrollment()
			{
					$page 			= 	0;	// The current page
					$sortname 		= 	'c.video_id';	 // Sort column
					$sortorder	 	= 	'asc';	 // Sort order
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
				$sortSql				= 	" order by $sortname $sortorder ";
				$searchSql 				= 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';

				// Setup paging SQL
				$pageStart 				= 	($page-1)*$rp;
				if($pageStart<0)
					{
						$pageStart		=	0;
					}
				$limitSql 				= 	" limit $pageStart, $rp ";

				$sql		= 	"SELECT count(*) FROM `tblcourse_enrollments` e
								 LEFT JOIN `tblusers` u  ON u.`user_id`= e.`student_id`
								 LEFT JOIN `tblcourse_notes` N ON N.`note_owner_id`=e.`student_id`	AND N.`course_id`= e.`course_id`	AND  N.`note_status`=0
								 LEFT JOIN `tblcourse_videos` V ON V.`video_owner_id`=e.`student_id`	AND V.`course_id`=e.`course_id` AND V.`video_status`=0
								 WHERE  e.`course_id`=".$_GET['course'];
				$results 	= 	$this->db_query($sql,1);
				$row		=	mysql_fetch_array($results);
				$total		=	$row[0];

				$query		=	"SELECT u.first_name, u.`last_name`, e.`paid_flag`, e.`refund_flag`, e.enrolled_id, e.`enrolled_status_id`, DATE_FORMAT(e.created_on,'".$_SESSION["DATE_FORMAT"]["M_DATE"]." ".$_SESSION["DATE_FORMAT"]["M_TIME"]."')  as  `created_on`, Count(N.`id`) as note, Count(V.`id`) as video	 FROM `tblcourse_enrollments` e
								 LEFT JOIN `tblusers` u  ON u.`user_id`= e.`student_id`
								 LEFT JOIN `tblcourse_notes` N ON N.`note_owner_id`=e.`student_id`	AND N.`course_id`= e.`course_id`	AND  N.`note_status`=0
								 LEFT JOIN `tblcourse_videos` V ON V.`video_owner_id`=e.`student_id`	AND V.`course_id`=e.`course_id` AND V.`video_status`=0
								 WHERE  e.`course_id`=".$_GET['course']." GROUP BY e.`enrolled_id`  ORDER BY u.`first_name` $limitSql";
				$results 	= 	$this->db_query($query,0);

				// Return JSON data to Template
				$data 					= 	array();
				$data['page'] 			= 	$page;;
				$data['qtype'] 			= 	$qtype;
				$data['query'] 			= 	$query;
				$data['total'] 			= 	$total;

				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results))
					{
						$i++;

							if($row["enrolled_status_id"] == LMT_CS_ENR_CANCELLED)
								$courseStatus= '<img src="../images/course_cancelled.gif" alt="Cancelled" title="Enrollment Cancelled"/>';
							else if($row["enrolled_status_id"] == LMT_CS_ENR_COMPLETED)
								$courseStatus= '<img src="../images/course_completed.png" alt="Taught" title="Course Taught"/>';
							else
								$courseStatus= '<img src="../images/course_open.png" alt="Enrolled" title="Enrolled"/>';

						$sp		=	"";
						if(!empty($row['id'])){
							$activate		=	"<a href='courseHistory.php?actionvar=EditSubmit&course=".$_GET['course']."&add=0&cms=".$row['id']."'><img src='images/active.gif' /></a>";
						}else{
							$activate		=	"<a href='courseHistory.php?actionvar=EditSubmit&course=".$_GET['course']."&add=1&enrollment=".$row["enrolled_id"]."'><img src='images/inactive.gif' /></a>";

						}


						$paid=	'<img src="../images/doller-inactive.png" alt="no note" title="not Payed">';
						if($row['paid_flag']==1 && $row["refund_flag"]==0){
							$paid=	'<img src="../images/doller.png" alt="Paid" title="Paid" title="Paid">';
						}


						$note=	'<img src="../images/note.png" alt="no note" title="notes">';
						if(empty($row['note'])){
							$note=	'<img src="../images/note-inactive.png" alt="no note" title="no notes">';
						}

						$video=	'<img src="../images/video-active.png" alt="no video" title="video">';
						if(empty($row['video'])){
							$video=	'<img src="../images/video-not-active.png" alt="no video" title="no video">';
						}


						$data['rows'][] = array(
						'id' => $row['enrolled_id'],
						'cell' => array($i,$courseStatus,$paid,$note,$video, $row['first_name']." ".$row['last_name'],$row['created_on'],$activate)
					);
				}
				$r =json_encode($data);
				ob_clean();
				echo  $r;
				exit;
			}

		public function courseHistorySearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function courseHistoryCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);
			}
		public function courseHistoryBack()
			{
				$data=$this->getdata('request');
				$course=$data['course'];
				ob_clean();
				header('Location: courseHistory.php?actionvar=ViewNotes&ccode='.$course.' ');
			}
		public function courseHistoryReset()
			{
				$this->clearData("Search");
				header("Location:courseHistory.php");
				exit;
			}

		public function courseHistoryEditSubmit()
			{

				if(empty($_GET['add'])){
					$query	=	"DELETE FROM `tblcourse_master_student` WHERE `id` =".$_GET["cms"];
					$this->db_query($query,0);
				}else{
					$array		=	array(	"student_id"=>$_GET['course'],
											"course_id"=>$_GET['course'],
											"enrolled_id"=>$_GET['enrollment'],
											"created_date"=>date("Y-m-d H:i:s"),
											"created_by"=>$_SESSION["sess_admin"]
										);
						$this->db_insert("tblcourse_master_student",$array,0);
				}

				$this->setPageError("Successfully updated !");
				return $this->executeAction(true,"Editform",false);

			}

		public function courseDetailsAddformbtn()
			{
				$this->executeAction(false,"Addform",true);
			}

		public function courseDetailsAddform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new tutorlist;
				$data				=	$memberObj->gettutorDetails($data['id']);
				$terrObj			= 	new territory();
				$country_combo		=	$this->get_combo_arr("sel_country",$terrObj->getAllCountries("status='1' order by preference"),"id","country",$data["sel_country"],"'valtype='emptyCheck-please select a country' onchange=\"getcombo(this.value,'stateDivId');\"");
				$stateArry			=	$terrObj->getAllStates("sel_country=".$data['sel_country']." and status='1' order by preference");
				$state_combo		=	$this->get_combo_arr("sel_state",$terrObj->getAllStates(" country_id=".$data['sel_country']." and status='1' order by preference"),"id","state",$data["sel_state"],"'valtype='emptyCheck-please select a country' onchange=\"getcities(this.value,'cityDivId');\"");
				$city_combo			=	$this->get_combo_arr("sel_city",$terrObj->getAllcities(" state_id=".$data['sel_state']." and status='1' order by preference"),"id","city",$data["sel_city"],"'valtype='emptyCheck-please select a city'");
				$combo				=	array();
				$combo['country']	=	$country_combo;
				$combo['state']		=	$state_combo;
				$combo['city']		=	$city_combo;

				return array("data"=>$this->getHtmlData($data),"combo"=>$combo,"sagent"=>$sid);
			}

		public function courseDetailsStauschange()
			{
				$details	=	$this->getData("request");
				$permission	=	new courseDetails;
				$sql		=	"select * from tblcourse_master
								where course_master_id=".$details['id']."";
				$data		=	$this->getdbcontents_sql($sql);
				$id			=	$data[0]['course_master_id'];
				if($data[0]['is_approved']==1)
					{
						$dataUpdate	=	array();
						$dataUpdate['is_approved']	=	"0";
						$this->db_update("tblcourse_master ",$dataUpdate,"course_master_id =$id",1);
						return $this->executeAction(false,"Listing",true);
					}
				else
					{
						$dataUpdate	=	array();
						$dataUpdate['is_approved']	=	"1";
						$this->db_update("tblcourse_master ",$dataUpdate,"course_master_id =$id",1);
						return $this->executeAction(false,"Listing",true);
					}
			}

		public function descardedCourseDeletedata()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new descardedCourse;
				$data				=	$memberObj->deletecourse($data['id']);
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr));
			}

		public function courseDetailsViewform()
			{
				$data				=	$this->getData("get");
				$memberObj			=	new courseDetails;
				$data				=	$memberObj->getcourseDetails($data['id']);
				$searchCombo		=	$this->get_combo_arr("currency_id", $this->getAll("1"), "currency_id", "symbol","currency_id");
				$searchData					=	$this->getHtmlData($this->getData("post","Search",false));
				$searchData["searchCombo"]	=	$searchCombo;
				//print_r($data);exit;
				return array("data"=>$this->getHtmlData($data),"payData"=>$this->getHtmlData($payArr),"orderData"=>$this->getHtmlData($orderArr),"searchdata"=>$searchData);
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

			$sql	=	"SELECT cu.*,lm.name,uu.first_name,ct.name as currencytype,ct.symbol
							from tblcourse_master as cu
							left join tblinstrument_master as lm on cu.course_instrument_id =lm.instrument_id
							left join tblcurrency_type as ct on cu.currency_id=ct.currency_id
							left join tblusers as uu
							on cu.tutor_id =uu.user_id
							where cu.is_deleted=0
							and course_master_id='$membersId' and ".$args;
				$result	=	end($this->getdbcontents_sql($sql));
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
		public function courseHistorySheetMusic(){

		}
	public function courseHistorySheetMusicFetch()
		{

					$data=$this->getdata("get");
					$ccode=$data['ccode'];
					$page 			= 	0;	// The current page
					$sortname 		= 	'c.video_id';	 // Sort column
					$sortorder	 	= 	'asc';	 // Sort order
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
							$rp			= LMT_SITE_ADMIN_PAGE_LIMIT;
						}
					$courseId				=	$_GET["courseId"];
					$sql 					= 	"SELECT count(*) from tblcourse_sheetmusic WHERE `course_id`=$courseId";
					$result 				= 	$this->db_query($sql,0);
					$row 					= 	mysql_fetch_array($result);
					$total					= 	$row[0];
					// Setup paging SQL
					$pageStart 				= 	($page-1)*$rp;
					if($pageStart<0)
						{
							$pageStart		=	0;
						}
					$limitSql 				= 	" limit $pageStart, $rp";
					// Return JSON data
					$data 					= 	array();
					$data['page'] 			= 	$page;
					$data['qtype'] 			= 	$qtype;
					$data['query'] 			= 	$query;
					$data['total'] 			= 	$total;
					$data['rows'] 			= 	array();

					$sql		=	"SELECT *, DATE_FORMAT(created_on,'".$_SESSION["DATE_FORMAT"]["M_DATE"]."') as created_on FROM `tblcourse_sheetmusic` WHERE `course_id`=$courseId ";
					$results 	= 	$this->db_query($sql,0);//exit;
					$i			=	$pageStart;

					while ($row = mysql_fetch_assoc($results))
						{
							$download	= "<a href='courseHistory.php?actionvar=SheetMusicDownload&doc=".$row["id"]."'  style='Color:red'  >Download</a>";
							$i++;
							$data['rows'][] = array(
							'id' => $row['video_id'],
							'cell' => array($i,$row['real_name'],$row['created_on'],$download)
						);
					}
					$r =json_encode($data);
					ob_clean();
					echo  $r;
					exit;
		}
		public function courseHistoryBlockCourse()
			{
				$data							=	$this->getData("get");
				$data['course_code']			=	unserialize(base64_decode($data['course_code']));
				$cls							=	new userCourse();
				$cms							=	new cms();
				$courseDetails					=	$cls->getCourseBasics($data['course_code']);
				$dataIns['course_status_id']	=   LMT_COURSE_STATUS_CLOSED;
				$dataIns['status_reason_id']	=	LMT_CS_CLOSE_REASON_ADMIN;
				$dataIns['status_changed_by']	=	$_SESSION['sess_admin'];
				if($this->db_update('tblcourses', $dataIns,"course_code = '".$data['course_code']."'"))
					{
							$mailDetails					=	$cls->getTutorMailingDetails($data['course_code'],LMT_SERVER_TIME_ZONE_OFFSET);
							//Mail to instructor
							$subject 						=  'Live Music Tutor Course Closed';
							$varArr["{TPL_URL}"]			=	ROOT_URL;
							$varArr["{TPL_NAME}"]			=  $mailDetails['name'];
							$varArr["{TPL_MESSAGE}"]		=  'The following course has been cancelled by administrator';
							$varArr["{TPL_INST_NAME}"]		=   $mailDetails['name'];
							$varArr["{TPL_TITLE}"]		    =	$mailDetails['title'];
							$varArr["{TPL_DESC}"]		    =	$mailDetails['description'];
							$varArr["{TPL_CREATED_ON}"]		=	date($mailDetails['php_date_format']." ".$mailDetails['php_time_format'],strtotime($mailDetails['created_on']));
							$varArr["{TPL_START_DATE}"]		=	date($mailDetails['php_date_format'],strtotime($mailDetails['course_start']));
							$varArr["{TPL_START_TIME}"]		=	date($mailDetails['php_time_format'],strtotime($mailDetails['course_time']));
							$varArr["{TPL_DURATION}"]		=	$cls->getCourseDuration($mailDetails['duration']);
							$varArr["{TPL_MAX}"]			=	$mailDetails['max_students'];
							$varArr["{TPL_MIN}"]			=	$mailDetails['min_required'];

							$send =	$cms->sendMailCMS("35",$mailDetails['user_name'],LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5);

						//User Log
							//$logObj  =	new userLog();
							//$logObj->setUserAction($_SESSION['USER_LOGIN']['LMT_USER_ID'], LMT_COURSE_CANCEL, 1);

						$this->setPageError("This course has been successfuly closed");

					}
				else
						$this->setPageError($this->getDbErrors());
				return $this->executeAction(true,"Listing");
			}
			public function courseHistorySheetMusicDownload(){

					$query		=	"SELECT  * FROM `tblcourse_sheetmusic` WHERE `id`=".$_GET["doc"];
					$rec		=	 $this->getdbcontents_sql($query);
					$docName	=	$rec[0]["sheet_name"];
					$fileName	=	$rec[0]["real_name"];
					$fullPath	=	ROOT_ABSOLUTE_PATH."/Uploads/sheetmusic/".$docName;
					$fsize 		= filesize($fullPath);
					$ctype="application/pdf";
					header("Pragma: public"); // required
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: private",false); // required for certain browsers
					header("Content-Type: $ctype");
					header("Content-Disposition: attachment; filename=\"".basename($fileName)."\";" );
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".$fsize);
					ob_clean();
					flush();
					readfile( $fullPath );
					exit;


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
