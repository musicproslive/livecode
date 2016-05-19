<?php
/****************************************************************************************
Created by	:	Arun
Created on	:	13-07-2011
Purpose		:	To Manage Courses
******************************************************************************************/
class liveClass extends modelclass
	{
		public function liveClassListing()
			{
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return;
			}
		public function liveClassFetch()
			{
				$page 			= 	0;	// The current page
				$sortname 		= 	' C.start_time';	 // Sort column
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
				$sortSql				 = 	" order by $sortname $sortorder";
				$searchSql 				 = 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';

				$serverOffset 			= 	LMT_SERVER_TIME_ZONE_OFFSET;
				$dtFmt					=	$_SESSION["DATE_FORMAT"]["M_DATE"];
				$tmFmt					=	$_SESSION["DATE_FORMAT"]["M_TIME"];
				$insOffset				=	$_SESSION["USER_LOGIN"]["TIMEZONE_ID"];
				$serverTime				=	date('Y-m-d H:i:s');

				$sql	=	"SELECT C.title, C.course_code,
					 DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset', '$insOffset'), '$dtFmt') AS start_date,
					 DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset', '$insOffset'), '$tmFmt')
					 AS start_time, D.time, V.id AS video_id , N.id AS note_id
					 FROM tblcourses AS C
					 LEFT JOIN tblusers AS U ON C.instructor_id = U.user_id
					 LEFT JOIN tbllookup_course_duration AS D ON D.id = C.duration
					 LEFT JOIN tblcourse_videos AS V ON V.course_id = C.course_id AND V.video_owner_id !=''
					 AND V.video_status = 1
					 LEFT JOIN tblcourse_notes AS N ON N.course_id = C.course_id AND N.note_owner_id !=''
					 AND N.note_status = 1
					 WHERE  C.course_status_id  != ".LMT_COURSE_STATUS_CANCELLED."
					 AND C.course_status_id  != ".LMT_COURSE_STATUS_TAUGHT." AND C.panic_flag = 0 AND  '$serverTime' >= DATE_SUB(CONCAT(C.start_date, ' ', C.start_time), INTERVAL ".LMT_INS_LIVE_CLASS_BEFORE." MINUTE) AND '$serverTime' <= DATE_ADD(CONCAT(C.start_date, ' ', C.start_time), INTERVAL D.time MINUTE) $searchSql";

				$resultArry				=	$this->getdbcontents_sql($sql,0);
				$total					= 	count($resultArry);
				// Setup paging SQL
				$pageStart 				= 	($page-1)*$rp;
				if($pageStart<0)
					{
						$pageStart		=	0;
					}
				$limitSql 				= 	" limit $pageStart, $rp";
				$data 					= 	array();
				$data['page'] 			= 	$page;
				$data['qtype'] 			= 	$qtype;
				$data['query'] 			= 	$query;
				$data['total'] 			= 	$total;
				$data['rows'] 			= 	array();

				$sql	=	"SELECT C.title, C.course_code, C.course_type_id,
					 DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset', '$insOffset'), '$dtFmt') AS start_date,
					 DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset', '$insOffset'), '$tmFmt')
					 AS start_time, D.time, V.id AS video_id , N.id AS note_id
					 FROM tblcourses AS C

					 LEFT JOIN tblusers AS U ON C.instructor_id = U.user_id
					 LEFT JOIN tbllookup_course_duration AS D ON D.id = C.duration
					 LEFT JOIN tblcourse_videos AS V ON V.course_id = C.course_id AND V.video_owner_id !=''
					 AND V.video_status = 1
					 LEFT JOIN tblcourse_notes AS N ON N.course_id = C.course_id AND N.note_owner_id !='' AND N.note_status = 1
					 WHERE  C.course_status_id  != ".LMT_COURSE_STATUS_CANCELLED."
					 AND C.course_status_id  != ".LMT_COURSE_STATUS_TAUGHT." AND C.panic_flag = 0 AND  '$serverTime' >= DATE_SUB(CONCAT(C.start_date, ' ', C.start_time), INTERVAL ".LMT_INS_LIVE_CLASS_BEFORE." MINUTE) AND '$serverTime' <= DATE_ADD(CONCAT(C.start_date, ' ', C.start_time), INTERVAL D.time MINUTE) $searchSql $sortSql $limitSql";
				$results 	= 	$this->db_query($sql,0);
				$i			=	$pageStart;

				$AdminDetail	=	end($this->getdbcontents_sql("SELECT user_code FROM tblusers where login_id =".$_SESSION['sess_admin'],0));
				$AdminUserCode	=	$AdminDetail['user_code'];

				while ($row = mysql_fetch_assoc($results))
					{

						//file_put_contents("file.txt",$row);
						$i++;
						$sp		=	'';
						if($row["precence"])
							$sp		=	"<a href='courseHistory.php?actionvar=Editform&course=".$row['course_id']."'><img src='../images/view.png' /></a>";

						if($row['video_id']) $video_link='<a href="courseHistory.php?actionvar=ViewVideos&ccode='.$row['course_code'].'" class="Second_link"><img src="../images/video.png" alt="video" title="Video"/></a>';

						else	$video_link='<img src="../images/video-not-active.png" alt="no video" title="no video"/>';

						if($row['note_id']) $note_link='<a href="courseHistory.php?actionvar=ViewNotes&ccode='.$row['course_code'].'" class="Second_link"><img src="../images/note.png" alt="note" title="note"/></a>';

						else	$note_link='<img src="../images/note-inactive.png" alt="no note" title="no notes"/>';
					if($row['course_type_id']==1)
								$link='<a href="javascript:void(0)" onclick=goToLesson("'.base64_encode(serialize($AdminUserCode)).'/'.base64_encode(serialize($row['course_code'])).'/AdminView") class="liveClass" title="live class" >Enter Live Lesson</a>';
							else
								$link='<a href="javascript:void(0)" onclick=goToClass("'.base64_encode(serialize($AdminUserCode)).'/'.base64_encode(serialize($row['course_code'])).'/AdminView") class="liveClass" title="live class" >Enter Live Class</a>';






						$data['rows'][] = array(
						'id' => $row['id'],
						'cell' => array($i, $row['title'],$row['start_date'],$row['start_time'],$link)

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
	public function courseTypeEditform()
		{
			$data					=	$this->getData("get");
			$sql					=	"SELECT * FROM `tbllookup_course_type` WHERE `id`=".$data["type_id"];
			$rec["data"]			=	$this->getdbcontents_sql($sql);
			return $rec;
			exit;
		}
		public function courseTypeAddform(){

		}


		public function courseTypeViewform()
			{
				$data				=	$this->getData("get");

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
		public function courseTypeSavedata(){
				$details	=	$this->getData("POST");
				$tmp		=	(array)($details);
				$dataIns	=	$this->populateDbArray("tbllookup_course_type",$details);
				$this->db_insert("tbllookup_course_type",$dataIns,1);
				$this->redirectAction(false,"Successfully added !!","Listing");

		}

		public function courseTypeUpdatedata(){

				$details	=	$this->getData("POST");
				$tmp		=	(array)($details);
				$dataIns	=	$this->populateDbArray("tbllookup_course_type",$details);
				$this->db_update("tbllookup_course_type",$dataIns,"id=".$_GET['type_id'],0);
				$this->redirectAction(false,"Successfully updated !!","Listing");
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
