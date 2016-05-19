<?php
/**************************************************************************************
Created By 	: Lijesh
Created On	:12-07-2011
Purpose		:Class for Course
**************************************************************************************/

class userCourse extends siteclass
	{
		function getAllTaughtCourses()
			{
				$sql="SELECT title FROM tblcourses WHERE course_status_id=4 AND is_active_vod=0";
				$result		=	$this->getdbcontents_sql($sql);
				return $result;
			}

		function getUsersCCdetails($userId, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $serverOffset = '+00:00', $studentOffset = '+00:00')
			{
			 $sql="SELECT UC.id, UC.orbital_profile_id, UC.card_no, UC.expiry_date, UC.is_active, DATE_FORMAT(CONVERT_TZ(UC.created_date, '$serverOffset', '$studentOffset'), '$dtFmt $tmFmt') AS added_date
					  FROM tblusers_ccs AS UC WHERE UC.user_id = $userId AND UC.is_deleted = 0";
				$result		=	$this->getdbcontents_sql($sql, 0);
				return $result;
			}
			
		function getUsersActiveCCdetails($userId, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $serverOffset = '+00:00', $studentOffset = '+00:00')
			{
				
			 $sql="SELECT UC.id, UC.orbital_profile_id, UC.card_no, UC.expiry_date, UC.is_active, DATE_FORMAT(CONVERT_TZ(UC.created_date, '$serverOffset', '$studentOffset'), '$dtFmt $tmFmt') AS added_date
				FROM tblusers_ccs AS UC WHERE UC.user_id = $userId AND is_active = 1 AND UC.is_deleted = 0";
				$result		=	$this->getdbcontents_sql($sql, 0);
				return $result;
			}
							
		function getUsersCCdetailsById($ccId)
			{
				$sql="SELECT UC.id, UC.orbital_profile_id, UC.card_no, UC.expiry_date, UC.is_active
					  FROM tblusers_ccs AS UC WHERE UC.id = $ccId";
				$result		=	$this->getdbcontents_sql($sql);
				return $result;
			}

		function getOrbitalProfileCode($userId)
			{
				$sql="SELECT orbital_profile_id FROM tblusers_ccs WHERE user_id = $userId AND is_active = 1 AND is_deleted = 0";
				$result		=	reset($this->getdbcontents_sql($sql, 0));
				if(!empty($result))
					return $result['orbital_profile_id'];
				else
					return 0;
			}

		function getCCOrbitalProfileCode($ccid)
			{
				$sql="SELECT orbital_profile_id FROM tblusers_ccs WHERE id = $ccid";
				$result		=	reset($this->getdbcontents_sql($sql, 0));
				if(!empty($result))
					return $result['orbital_profile_id'];
				else
					return 0;
			}

		function getCourseDetailStdView($courseCode, $serverOffset = '+00:00', $studentOffset = '+00:00', $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p')
			{
				$sql = "SELECT C.course_id, C.course_code, C.title, C.description,
						DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ' ,C.start_time), '$serverOffset', '$studentOffset'), '$dtFmt')
						AS start_date, DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ' ,C.start_time),
						'$serverOffset', '$studentOffset'), '$tmFmt') AS start_time, CT.type AS cs_type, D.time AS duration,
						P.cost, I.name AS instrument_name, I.instrument_image, CY.symbol, CY.currency_id,
						U.first_name AS ins_first_name, U.last_name AS ins_last_name
						FROM tblcourses AS C
						LEFT JOIN tbllookup_course_type AS CT ON C.course_type_id = CT.id
						LEFT JOIN tbllookup_course_duration AS D ON C.duration = D.id
						LEFT JOIN tblcourse_prices AS P ON C.price_code = P.id
						LEFT JOIN tblinstrument_master AS I ON C.instrument_id = I.instrument_id
						LEFT JOIN tblusers AS U ON C.instructor_id = U.user_id
						LEFT JOIN tblcurrency_type AS CY ON P.currency_type = CY.currency_id
						WHERE C.course_code = '$courseCode'";
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				return $resultArry;
			}

	/*
			@userId: Selected Tutor Id
			@instrumentId: Selected Instrument Id
			Description: Students active course listing. The date time will be listed beased on students timezone.
		*/
	function studentsCourseViewSql($insId, $stdId, $instrumentId = 0, $serverOffset = '+00:00', $studentOffset = '+00:00', $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p')
		{
			$condition = " 1";

			$condition .= !empty($insId) ? " AND C.instructor_id = $insId" : '';

			$condition .= !empty($instrumentId) ? " AND C.instrument_id = $instrumentId": '';

			/* DATE_FORMAT(CONVERT_TZ(CONCAT(CURDATE(), ' ' ,addtime(CURTIME(), '04:00:00')), '$serverOffset', '$studentOffset'),	'$tmFmt') AS today_time,
			DATE_FORMAT(CONVERT_TZ(CONCAT(CURDATE(), ' ' ,addtime(CURTIME(), '04:00:00')), '$serverOffset', '$studentOffset'),	'$dtFmt') AS today_date, */
			
		    $sql = "SELECT C.course_code, C.title,C.description, 
		    	DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ' ,C.start_time),	'$serverOffset', '$studentOffset'), '$dtFmt') AS start_date,
				DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ' ,C.start_time), '$serverOffset', '$studentOffset'),	'$tmFmt') AS start_time, 
				DATE_FORMAT(CONVERT_TZ(CONCAT(CURDATE(), ' ' , CURTIME()), '$serverOffset', '$studentOffset'),	'$tmFmt') AS today_time,
				DATE_FORMAT(CONVERT_TZ(CONCAT(CURDATE(), ' ' , CURTIME()), '$serverOffset', '$studentOffset'),	'$dtFmt') AS today_date,
				UNIX_TIMESTAMP(CONCAT(C.start_date, ' ' ,C.start_time)) AS unix_tcourse, 
				E.enrolled_id AS is_enrolled,T.type, D.time, P.cost, I.name
					AS instrument_name, I.instrument_image, CY.symbol
					FROM tblcourses AS C
					LEFT JOIN tblcourse_enrollments AS E ON C.course_id = E.course_id AND E.student_id = $stdId
					AND enrolled_status_id =". LMT_CS_ENR_ENROLLED."
					LEFT JOIN tbllookup_course_type AS T ON C.course_type_id = T.id
					LEFT JOIN tbllookup_course_duration AS D ON C.duration = D.id
					LEFT JOIN tblcourse_prices AS P ON C.price_code = P.id
					LEFT JOIN tblinstrument_master AS I ON C.instrument_id = I.instrument_id
					LEFT JOIN tblcurrency_type AS CY ON P.currency_type = CY.currency_id
					WHERE $condition AND C.num_enrolled < C.max_students AND
					(C.course_status_id = ".LMT_COURSE_STATUS_OPEN."	OR C.course_status_id = ".LMT_COURSE_STATUS_FULL.")
					AND CONCAT(C.start_date, ' ' ,C.start_time) > '".date('Y-m-d H:i:s')."'
					ORDER BY CONCAT(C.start_date, ' ' ,C.start_time) ASC, I.name";
//echo $sql;
			return $sql;
		}


	function isStdEnrolled($courseId, $studentId)
		{
			$sql = "SELECT COUNT(*) AS enr_count FROM tblcourse_enrollments WHERE course_id = $courseId
					AND student_id = $studentId AND enrolled_status_id = ".LMT_CS_ENR_ENROLLED;
			$resultArry		=	reset($this->getdbcontents_sql($sql, 0));
			return $resultArry['enr_count'];
		}

	function getCourseId($courseCode)
		{
			$sql = "SELECT course_id FROM tblcourses WHERE course_code = '$courseCode'";
			$resultArry		=	reset($this->getdbcontents_sql($sql, false));
			return $resultArry['course_id'];
		}

	function enrollCourse($csData)
		{
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tblcourse_enrollments', $csData, 0);
			if(!$this->id)
				{
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());
					return false;
				}
			else
				return $this->id;
		}
	function getEnrolledCourseId($enrollCode)
		{
			$sql = "SELECT course_id FROM tblcourse_enrollments WHERE enrollment_code = '$enrollCode'";
			$resultArry		=	reset($this->getdbcontents_sql($sql, false));
			return $resultArry['course_id'];
		}
	function rescheduleEnrollment($csData,$cond="false")
		{

			$this->dbStartTrans();
			if(!$this->db_update('tblcourse_enrollments', $csData, $cond,0))
				{

					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());
					return false;
				}
			else
				{

					$sql = "SELECT enrolled_id FROM tblcourse_enrollments WHERE $cond";
					$resultArry		=	reset($this->getdbcontents_sql($sql, false));
					return $resultArry['enrolled_id'];
				}
		}

	function processCourseTransaction($trnData)
		{
			$this->dbStartTrans();
			$this->id =	$this->db_insert('tblcourse_enrollment_transaction', $trnData, 0);
			if(!$this->id)
				{
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());
					return false;
				}
			else
				return $this->id;
		}

	function restoreCourseEnrollment($enrollId)
		{
			$this->dbDelete_cond('tblcourse_enrollments', "enrolled_id = $enrollId");
		}

	function getEnrollmentStats()
		{
			$sql	=	"SELECT * FROM  tbllookup_enrolled_status";
			$result	=	$this->getdbcontents_sql($sql, 0);
			return $result;
		}

	function getStudentsCourseHistory($studId, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
		{	//Commented by Bhaskar
			 $sql1	=	"SELECT E.enrolled_id,E.enrolled_status_id, E.paid_flag,E.panic_flag,E.enrollment_code,C.course_code, C.title, C.course_code,C.course_status_id,
						I.name as instrument_name, I.instrument_image, DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ',
						C.start_time), '$serverOffset', '$studentOffset'), '$dtFmt') AS start_date,
						DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset',
						'$studentOffset'), '$tmFmt') AS start_time, UNIX_TIMESTAMP(CONCAT(C.start_date, ' ',
						C.start_time))+D.time*60 AS course_server_time, P.cost,D.time, V.id AS video_id, V.archive_id as video_link, N.id AS note_id, R.symbol,
						ctq.processing_status AS cc_pymnt_process_status
						FROM tblcourse_enrollments AS E
						LEFT JOIN tblcourses AS C ON E.course_id = C.course_id
						LEFT JOIN tblusers AS U ON C.instructor_id = U.user_id
						LEFT JOIN tblcourse_prices AS P ON C.price_code =  P.id
						LEFT JOIN tblinstrument_master AS I on C.instrument_id=I.instrument_id
						LEFT JOIN tbllookup_course_duration AS D ON D.id = P.duration
						LEFT JOIN tblcourse_notes AS N ON N.course_id = C.course_id AND N.note_owner_id = $studId
						AND N.note_status = 0
						LEFT JOIN tblcourse_archives AS V ON V.course_id = C.course_id
						LEFT JOIN tblcurrency_type AS R ON R.currency_id = P.currency_type
						LEFT JOIN tblcc_transaction_queue ctq ON ctq.enrollment_id = E.enrolled_id
						WHERE student_id = $studId $condition GROUP BY C.course_id ORDER BY C.start_date DESC, C.start_time DESC, E.enrolled_status_id ASC, E.created_on DESC"; 
			//updated by bhaskar
			 $sql2	=	"SELECT E.enrolled_id,E.enrolled_status_id, E.paid_flag,E.panic_flag,E.enrollment_code,C.course_code, C.title, C.course_code,C.course_status_id,
							I.name as instrument_name, I.instrument_image, DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ',
							C.start_time), '$serverOffset', '$studentOffset'), '$dtFmt') AS start_date,
							DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset',
							'$studentOffset'), '$tmFmt') AS start_time, UNIX_TIMESTAMP(CONCAT(C.start_date, ' ',
							C.start_time))+D.time*60 AS course_server_time, P.cost,D.time, V.id AS video_id, V.archive_id as video_link, N.id AS note_id, R.symbol
							FROM tblcourse_enrollments AS E
							LEFT JOIN tblcourses AS C ON E.course_id = C.course_id
							LEFT JOIN tblusers AS U ON C.instructor_id = U.user_id
							LEFT JOIN tblcourse_prices AS P ON C.price_code =  P.id
							LEFT JOIN tblinstrument_master AS I on C.instrument_id=I.instrument_id
							LEFT JOIN tbllookup_course_duration AS D ON D.id = P.duration
							LEFT JOIN tblcourse_notes AS N ON N.course_id = C.course_id AND N.note_owner_id = $studId
							AND N.note_status = 0
							LEFT JOIN tblcourse_archives AS V ON V.course_id = C.course_id
							LEFT JOIN tblcurrency_type AS R ON R.currency_id = P.currency_type
							WHERE student_id = $studId $condition GROUP BY C.course_id ORDER BY C.start_date DESC, C.start_time DESC, E.enrolled_status_id ASC, E.created_on DESC"; 
			
			 $table = tblcc_transaction_queue;
			 if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$table."'"))==1)
			 { 
			 	$sqls = $sql1;
			 } else { //echo "$table does not exist"; 
			 	$sqls = $sql2; }
			$result	=	$this->getdbcontents_sql($sqls, 0);
			
			//return $result;
			return $sqls;
		}

		public function getStudentRecentCourseShedule($studId, $serverOffset = '+00:00', $studentOffset = '+00:00', $serverTime, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p')
			{
			    $sql	=	"SELECT E.enrolled_status_id, E.paid_flag, C.title, C.course_code,
							DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset',
							'$studentOffset'), '$dtFmt') AS start_date, DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date,
							' ', C.start_time), '$serverOffset', '$studentOffset'), '$tmFmt')
							 AS start_time, D.time, V.id AS video_id , N.id AS note_id,
	 						 (UNIX_TIMESTAMP(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset', '$studentOffset')) - UNIX_TIMESTAMP(NOW())) as starts_in
							 FROM tblcourse_enrollments AS E
							 LEFT JOIN tblcourses AS C ON E.course_id = C.course_id
							 LEFT JOIN tblusers AS U ON C.instructor_id = U.user_id
							 LEFT JOIN tbllookup_course_duration AS D ON D.id = C.duration
							 LEFT JOIN tblcourse_videos AS V ON V.course_id = C.course_id AND V.video_owner_id = $studId
							 AND V.video_status = 0
							 LEFT JOIN tblcourse_notes AS N ON N.course_id = C.course_id AND N.note_owner_id = $studId
							 AND N.note_status = 0
							 WHERE student_id = $studId AND E.enrolled_status_id = ".LMT_CS_ENR_ENROLLED."
							 AND E.paid_flag = 1 AND E.panic_flag = 0 AND '$serverTime' >=
							 DATE_SUB(CONCAT(C.start_date, ' ', C.start_time),
							 INTERVAL ".LMT_STUD_LIVE_CLASS_BEFORE." MINUTE) AND '$serverTime' <=
							 DATE_ADD(CONCAT(C.start_date, ' ', C.start_time), INTERVAL D.time MINUTE)";
				
				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				return $resultArry;
			}

		public function getStudentsClassNotes($cCode,$noteOwnerId,$dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $serverOffset = '+00:00', $studentOffset = '+00:00')
			{
			    $sql	=	$sql="SELECT A.note_text, DATE_FORMAT(CONVERT_TZ(A.note_taken, '$serverOffset','$studentOffset'), '$dtFmt') AS note_taken_date,
									 DATE_FORMAT(CONVERT_TZ(A.note_taken, '$serverOffset', '$studentOffset'), '$tmFmt') AS note_taken_time, B.title,
									 concat(G.first_name,' ',G.last_name) AS note_owner_name,
									 concat(C.first_name,' ',C.last_name) as instructor_name, D.name as instrument_name

										FROM tblcourse_notes AS A
										LEFT JOIN tblcourses AS B on A.course_id=B.course_id
										LEFT JOIN tblusers AS C on B.instructor_id=C.user_id
										LEFT JOIN tblusers AS G on  A.note_owner_id=G.user_id
										LEFT JOIN tblinstrument_master AS D on B.instrument_id=D.instrument_id
										WHERE A.note_owner_id = $noteOwnerId AND B.course_code='$cCode' AND note_status=0";
				return $sql;
			}
		public function getStudentsClassVideos($cCode,$videoOwnerId,$dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $serverOffset = '+00:00', $studentOffset = '+00:00')
			{
				$sql = "SELECT A.video_link, DATE_FORMAT(CONVERT_TZ(A.video_taken, '$serverOffset','$studentOffset'), '$dtFmt') AS video_taken_date,
						 DATE_FORMAT(CONVERT_TZ(A.video_taken, '$serverOffset', '$studentOffset'), '$tmFmt') AS video_taken_time, B.title, B.course_code,
						 concat(C.first_name,' ',C.last_name) as instructor_name,concat(D.first_name,' ',D.last_name) AS video_owner_name, E.name as instrument_name
						 FROM tblcourse_videos AS A
						 LEFT JOIN tblcourses AS B on A.course_id=B.course_id
						 LEFT JOIN tblusers AS C on B.instructor_id=C.user_id
						 LEFT JOIN tblusers AS D on A.video_owner_id=D.user_id
						 LEFT JOIN tblinstrument_master AS E on B.instrument_id=E.instrument_id
						 WHERE (A.video_owner_id = $videoOwnerId OR A.video_owner_id=B.instructor_id) AND B.course_code='$cCode' AND video_status=0";
				return $sql;
			}
			public function getTutorsClassNotes($cCode,$noteOwnerId,$dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $serverOffset = '+00:00', $studentOffset = '+00:00')
			{
			    $sql	=	$sql="SELECT A.note_text, DATE_FORMAT(CONVERT_TZ(A.note_taken, '$serverOffset','$studentOffset'), '$dtFmt') AS note_taken_date,
									 DATE_FORMAT(CONVERT_TZ(A.note_taken, '$serverOffset', '$studentOffset'), '$tmFmt') AS note_taken_time,
									  B.title,concat(G.first_name,' ',G.last_name) AS note_owner_name,
									 concat(C.first_name,' ',C.last_name) as instructor_name, D.name as instrument_name

										FROM tblcourse_notes AS A
										LEFT JOIN tblcourses AS B on A.course_id = B.course_id
										LEFT JOIN tblusers AS C on B.instructor_id=C.user_id
										LEFT JOIN tblusers AS G on  A.note_owner_id=G.user_id
										LEFT JOIN tblinstrument_master AS D on B.instrument_id=D.instrument_id
										LEFT JOIN tblcourse_enrollments AS F ON F.course_id=B.course_id
										WHERE (A.note_owner_id = $noteOwnerId OR A.note_owner_id=F.student_id) AND B.course_code='$cCode' AND note_status=0";

				return $sql;
			}
		public function getTutorsClassVideos($cCode,$videoOwnerId,$dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $serverOffset = '+00:00', $studentOffset = '+00:00')
			{
			    $sql="SELECT A.video_thumb_link, DATE_FORMAT(CONVERT_TZ(A.video_taken, '$serverOffset','$studentOffset'), '$dtFmt') AS video_taken_date,
									 DATE_FORMAT(CONVERT_TZ(A.video_taken, '$serverOffset', '$studentOffset'), '$tmFmt') AS video_taken_time, B.title,
									 concat(C.first_name,' ',C.last_name) as instructor_name,concat(D.first_name,' ',D.last_name) AS video_owner_name, E.name as instrument_name

										FROM tblcourse_videos AS A
										LEFT JOIN tblcourses AS B on A.course_id=B.course_id
										LEFT JOIN tblusers AS C on B.instructor_id=C.user_id
										LEFT JOIN tblusers AS D on A.video_owner_id=D.user_id
										LEFT JOIN tblinstrument_master AS E on B.instrument_id=E.instrument_id
										LEFT JOIN tblcourse_enrollments AS F ON F.course_id=B.course_id
										WHERE (A.video_owner_id = $videoOwnerId OR A.video_owner_id=F.student_id) AND B.course_code='$cCode' AND video_status=0";
				return $sql;
			}

		public function getInstructorRecentCourseShedule($insId, $serverOffset = '+00:00', $insOffset = '+00:00', $serverTime, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p')
			{
				$sql	=	"SELECT C.title, C.course_code, C.course_status_id, C.course_type_id, C.panic_flag,
								 DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset', '$insOffset'), '$dtFmt') AS start_date,
								 DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset', '$insOffset'), '$tmFmt') AS start_time, 
								 D.time, V.id AS video_id , N.id AS note_id,
						 		(UNIX_TIMESTAMP(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset', '$insOffset')) - UNIX_TIMESTAMP(CONVERT_TZ(NOW(),'$serverOffset', '$insOffset'))) as starts_in
				 	 FROM tblcourses AS C
						 LEFT JOIN tblusers AS U ON C.instructor_id = U.user_id
						 LEFT JOIN tbllookup_course_duration AS D ON D.id = C.duration
						 LEFT JOIN tblcourse_videos AS V ON V.course_id = C.course_id AND V.video_owner_id = $insId
						 AND V.video_status = 1
						 LEFT JOIN tblcourse_notes AS N ON N.course_id = C.course_id AND N.note_owner_id = $insId
						 AND N.note_status = 1

						 WHERE C.instructor_id = $insId AND C.course_status_id  != ".LMT_COURSE_STATUS_CANCELLED."
						 AND C.course_status_id  != ".LMT_COURSE_STATUS_TAUGHT."
						 AND '$serverTime' >= DATE_SUB(CONCAT(C.start_date, ' ', C.start_time),
						 INTERVAL ".LMT_INS_LIVE_CLASS_BEFORE." MINUTE) 
						 AND '$serverTime' <= DATE_ADD(CONCAT(C.start_date, ' ', C.start_time),INTERVAL D.time MINUTE)";
//				echo $sql;
				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				return $resultArry;
			}


		function getTutorCourses($insId,$insOffset = '+00:00', $serverOffset = '+00:00', $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $cond="")
			{
				/* $sql	=	"SELECT C.title,C.course_code,C.course_status_id ,
							DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset',
							'$insOffset'),'$dtFmt') AS start_date, DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ',
							C.start_time), '$serverOffset', '$insOffset'), '$tmFmt') AS start_time ,C.panic_flag,P.cost,D.time ,
							V.id AS video_id, V.archive_id as video_link, N.id AS note_id, R.symbol, C.max_students, COUNT(E.enrolled_id)
							AS tot_enrolled, I.name AS instrument_name, I.instrument_image
							 FROM tblcourses AS C LEFT JOIN tblcourse_prices AS P ON C.price_code =  P.id
							 LEFT JOIN tblinstrument_master AS I on C.instrument_id=I.instrument_id
							 LEFT JOIN tbllookup_course_duration AS D ON D.id = P.duration
							 LEFT JOIN tblcourse_archives AS V ON V.course_id = C.course_id
							 LEFT JOIN tblcourse_notes AS N ON N.course_id = C.course_id AND N.note_owner_id = $insId
							 AND N.note_status = 0
							 LEFT JOIN tblcourse_enrollments AS E ON C.course_id = E.course_id
							 AND enrolled_status_id != ".LMT_CS_ENR_CANCELLED."
							 LEFT JOIN tblcurrency_type AS R
							 ON R.currency_id = P.currency_type
							 WHERE instructor_id = $insId $cond
							 GROUP BY C.course_id
							 ORDER BY C.start_date ASC, C.start_time ASC, C.created_on ASC, C.course_status_id ASC";*/
				$tz1 = new timeZone();
				$courseTime = date('Y-m-d', $tz1->convertTime($serverOffset, $insOffset, date('Y-m-d H:i:s')));
			   
			    $sql	=	"SELECT C.title,C.course_code,C.course_status_id ,
							DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset','$insOffset'),'$dtFmt') AS start_date, 
							DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset', '$insOffset'), '$tmFmt') AS start_time ,
							C.panic_flag,P.cost,D.time ,V.id AS video_id, V.archive_id as video_link, N.id AS note_id, R.symbol, C.max_students, 
							COUNT(E.enrolled_id) AS tot_enrolled, I.name AS instrument_name, I.instrument_image
								FROM tblcourses AS C 
									LEFT JOIN tblcourse_prices AS P 
										ON C.price_code =  P.id
									LEFT JOIN tblinstrument_master AS I 
										on C.instrument_id=I.instrument_id
									LEFT JOIN tbllookup_course_duration AS D 
										ON D.id = P.duration
									LEFT JOIN tblcourse_archives AS V 
										ON V.course_id = C.course_id
									LEFT JOIN tblcourse_notes AS N 
										ON N.course_id = C.course_id 
								AND N.note_owner_id = $insId
								AND N.note_status = 0
									LEFT JOIN tblcourse_enrollments AS E 
										ON C.course_id = E.course_id
								AND enrolled_status_id != ".LMT_CS_ENR_CANCELLED."
									LEFT JOIN tblcurrency_type AS R
										ON R.currency_id = P.currency_type
								WHERE instructor_id = $insId $cond 
								GROUP BY C.course_id
								ORDER BY C.start_date ASC, C.start_time ASC, C.created_on ASC, C.course_status_id ASC";
				return $sql;
// AND concat(`start_date`,0x20,`start_time`)>=NOW()
			}

		function courseEnrolledStatus($courseId)
			{
				$sql = "SELECT max_students, num_enrolled FROM tblcourses WHERE course_id = $courseId";
				$resultArry	=	reset($this->getdbcontents_sql($sql, 0));
				return $resultArry;
			}

		function enrolledCount($courseId, $enrollStatus)
			{
				$sql = "SELECT count(*) as enrolled FROM tblcourse_enrollments WHERE course_id = $courseId
						AND enrolled_status_id != $enrollStatus";
				$resultArry	=	reset($this->getdbcontents_sql($sql, 0));
				return $resultArry['enrolled'];
			}

		function getMaxEnrollment($courseId)
			{
				$sql = "SELECT max_students FROM tblcourses WHERE course_id = $courseId";
				$resultArry	=	reset($this->getdbcontents_sql($sql, 0));
				return $resultArry['max_students'];
			}

		function updateCourseTypeStatus($courseId, $data)
			{
				if($this->db_update('tblcourses',$data,"course_id=$courseId"))
					return true;
				else
					return false;
			}


	/*-----------------------------------------------------------------------------------------------------*/

		public function checkTutorPermission($userID)
			{
				$sql = "SELECT Login.admin_authorize FROM tblusers AS User
						LEFT JOIN tbluser_login AS Login ON User.login_id = Login.login_id
						WHERE User.user_id = $userID";
				$resultArry		=	reset($this->getdbcontents_sql($sql, false));
				//print_r($resultArry);exit;
				if($resultArry['admin_authorize'] == 0)
					return false;
				else
					return true;
			}
		public function delete($id){
				if($this->db_update("tblcourse_master",array("is_deleted"=>1),"course_master_id=$id",1)){
				return true;
				}else{
				$this->setPageError($this->getDbErrors());
				return false;
				}
			}
		public function addNewCourse($courseDetails)
			{
				$this->dbStartTrans();
				$this->id =	$this->db_insert('tblcourse_master',$courseDetails, 0);
				if(!$this->id)
					{
						$this->dbRollBack();
						$this->setPageError($this->getDbErrors());
						return false;
					}
				else
					return $this->id;
			}
		public function getCourseDateRange($courseID)
			{
				$sql = "SELECT course_master_id, course_start_date, course_end_date, number_of_class FROM tblcourse_master WHERE course_master_id = $courseID AND  is_closed = 0 AND is_deleted = 0";
				//is_published = 0 AND is_approved = 0 AND
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				return $resultArry;
			}
/*	public function getCourseDetails($courseID)
		{
			$sql = "SELECT course_master_id, course_title, course_start_date, course_end_date, default_start_time, class_duration FROM tblcourse_master WHERE course_master_id = $courseID AND  is_closed = 0 AND is_deleted = 0";
			//AND is_published = 0 AND is_approved = 0
			$resultArry		=	$this->getdbcontents_sql($sql, false);
			return $resultArry;
		}

*/	public function getCourseDetail($courseCode)
		{
			$sql = "SELECT title,description FROM tblcourses WHERE course_code = '$courseCode' ";
			$resultArry		=	end($this->getdbcontents_sql($sql, false));
			return $resultArry;
		}
	public function updateCourse($courseDetail, $id)
		{
			$data =	$this->db_update('tblcourse_master',$courseDetail,"course_master_id=$id");
			if(!$data)
				{
					$this->setPageError($this->getDbErrors());
					return false;
				}
				else return true;
		}
	public function getNumberofClass($courseID)
		{
			$sql = "SELECT number_of_class FROM tblcourse_master WHERE course_master_id = $courseID AND is_deleted = 0";
			$resultArry		=	$this->getdbcontents_sql($sql, false);
			return $resultArry;
		}

	/*
		@input: $course ID

	*/
	public function getCourseDetailView($courseID)
		{
			$sql = "SELECT CourseMaster.course_master_id, CourseMaster.course_title, CourseMaster.course_description,
					CourseMaster.number_of_class, class_duration, DATE_FORMAT(CourseMaster.course_start_date, '%b %d %Y')
					AS course_start_date, DATE_FORMAT(CourseMaster.course_end_date, '%b %d %Y') AS course_end_date,
					DATE_FORMAT(CourseMaster.default_start_time, '%h:%i %p') AS default_start_time, CourseMaster.max_attendance,
					CourseMaster.course_fee, DATE_FORMAT(CourseMaster.subscription_upto, '%b %d %Y %h:%i %p') AS subscription_upto,
					InstrumentMaster.instrument_image, CurrencyType.symbol, TimeZone.timezone_location
					FROM tblcourse_master AS CourseMaster
					LEFT JOIN tblinstrument_master AS InstrumentMaster ON
					CourseMaster.course_instrument_id =  InstrumentMaster.instrument_id
					LEFT JOIN tblcurrency_type AS CurrencyType ON CourseMaster.currency_id = CurrencyType.currency_id
					LEFT JOIN tbltime_zones AS TimeZone ON CourseMaster.time_zone_id = TimeZone.id
					WHERE CourseMaster.course_master_id = $courseID AND CourseMaster.is_deleted = 0";
			$resultArry		=	$this->getdbcontents_sql($sql, false);
			return $resultArry;
		}

	function getLastClass($courseID)
		{
			$sql = "SELECT DATE_FORMAT(CONCAT(class_date, ' ', start_time), '%b %d %Y %h:%i %p') AS subc_date
					FROM  tbluser_class WHERE course_master_id = $courseID ORDER BY CONCAT(class_date, '', start_time) LIMIT 0, 1";
			$resultArry		=	$this->getdbcontents_sql($sql, false);
			return $resultArry;
		}

	/*
		@visitorOffset => Student's time zone.
		Course details in student login.
	*/
	public function getStudentCourseView($courseID, $visitorOffset = '+00:00')
		{
			$sql = "SELECT CourseMaster.course_title, CourseMaster.tutor_id, CourseMaster.course_description, CourseMaster.number_of_class, CourseMaster.class_duration, DATE_FORMAT(CourseMaster.course_start_date, '%b %d %Y') AS course_start_date, DATE_FORMAT(CourseMaster.course_end_date, '%b %d %Y') AS course_end_date, DATE_FORMAT(CourseMaster.default_start_time, '%h:%i %p') AS default_start_time, CourseMaster.max_attendance, CourseMaster.course_fee, DATE_FORMAT(CourseMaster.subscription_upto, '%b %d %Y %h:%i %p') AS subscription_upto, InstrumentMaster.instrument_image, CurrencyType.symbol, TimeZone.timezone_location, SUBSTRING(TimeZone.gmt,5,6) AS gmt, DATE_FORMAT((CONVERT_TZ(CourseMaster.subscription_upto,(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = CourseMaster.time_zone_id),'$visitorOffset')), '%b %d %Y %h:%i %p') as studLocalSubscriptionTime, DATE_FORMAT((CONVERT_TZ(Concat(CourseMaster.course_start_date,' ', CourseMaster.default_start_time),(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = CourseMaster.time_zone_id),'$visitorOffset') ),'%h:%i %p') as studDefaultStartTime  FROM tblcourse_master AS CourseMaster
			LEFT JOIN tblinstrument_master AS InstrumentMaster ON  CourseMaster.course_instrument_id =  InstrumentMaster.instrument_id
			LEFT JOIN tblcurrency_type AS CurrencyType ON CourseMaster.currency_id = CurrencyType.currency_id
			LEFT JOIN tbltime_zones AS TimeZone ON CourseMaster.time_zone_id = TimeZone.id
			WHERE CourseMaster.course_master_id = $courseID AND CourseMaster.is_deleted = 0";
			$resultArry		=	$this->getdbcontents_sql($sql, false);
			return $resultArry;
		}

	public function getSubscribedCourses($userID, $limit = 0)
		{
			$sql = "SELECT Subscriber.course_id,  Subscriber.transaction_id, CourseMaster.*, Currency.symbol,Currency.code, InstrumentMaster.instrument_image FROM tblmusic_subscriber AS Subscriber
			LEFT JOIN  tblcourse_master AS CourseMaster ON Subscriber.course_id = CourseMaster.course_master_id
			LEFT JOIN tblinstrument_master AS InstrumentMaster ON  CourseMaster.course_instrument_id =  InstrumentMaster.instrument_id
			LEFT JOIN tblcurrency_type AS Currency ON CourseMaster.currency_id = Currency.currency_id WHERE  Subscriber.student_id = $userID AND CourseMaster.is_closed = 0 ORDER BY created_date DESC";
			if($limit){
				$sql	.=	" LIMIT 0, $limit";

			}else{
				$rec["splage"]		=	$this->create_paging("n_page",$sql);
			}


		//	echo $sql;

			$rec[0]				=	$this->getdbcontents_sql($sql, 0);
			if($limit){
				$rec	=		$rec[0];
			}

			return $rec;
		}

	public function getStudentCompletedCourses($userID, $limit = 0)
		{
			$sql = "SELECT Subscriber.course_id,  Subscriber.transaction_id, CourseMaster.course_master_id,CourseMaster.course_instrument_id,CourseMaster.course_title,CourseMaster.course_description, DATE_FORMAT(CourseMaster.course_start_date, '%b %d %Y') AS course_start_date, DATE_FORMAT(CourseMaster.course_end_date, '%b %d %Y') AS course_end_date, Currency.symbol,Currency.code, InstrumentMaster.instrument_image FROM tblmusic_subscriber AS Subscriber
			LEFT JOIN  tblcourse_master AS CourseMaster ON Subscriber.course_id = CourseMaster.course_master_id
			LEFT JOIN tblinstrument_master AS InstrumentMaster ON  CourseMaster.course_instrument_id =  InstrumentMaster.instrument_id
			LEFT JOIN tblcurrency_type AS Currency ON CourseMaster.currency_id = Currency.currency_id WHERE  Subscriber.student_id = $userID AND CourseMaster.is_closed = 1 ORDER BY created_date DESC";
			if($limit){
				$sql	.=	" LIMIT 0, $limit";

			}else{
				$rec["splage"]		=	$this->create_paging("n_page",$sql);
			}


		//	echo $sql;

			$rec[0]				=	$this->getdbcontents_sql($sql, 0);
			if($limit){
				$rec	=		$rec[0];
			}

			return $rec;
		}

	/*
		Get Students Class List
		Each course may have different time zone.
		Convert all class datetime to students local time zone, then only we can show his calendar with todays shedule.
	*/
	public function getStudentCalendar($studentID, $studentOffset = '+00:00', $serverOffset = '+00:00')
		{
			$tz = new timeZone();
			$studTime = date('Y-m-d', $tz->convertTime($serverOffset, $studentOffset, date('Y-m-d H:i:s')));
			$sql = "SELECT Subscribe.course_id, Course.title, CONVERT_TZ(CONCAT(Course.start_date,' ', Course.start_time),
					'".LMT_SERVER_TIME_ZONE_OFFSET."', '$studentOffset') AS  class_date
					FROM tblcourse_enrollments AS Subscribe
					LEFT JOIN tblcourses AS Course ON Subscribe.course_id = Course.course_id
					WHERE Subscribe.enrolled_status_id !=".LMT_CS_ENR_CANCELLED." AND Subscribe.student_id = $studentID AND CONVERT_TZ(Course.start_date,
					'".LMT_SERVER_TIME_ZONE_OFFSET."', '$studentOffset') >= '$studTime'";
			$resultArry		=	$this->getdbcontents_sql($sql,0);
			return $resultArry;
		}


	function getStudentsSideCalendar($studentID, $studentOffset = '+00:00', $serverOffset = '+00:00')
		{
			$courseDates = $this->getStudentCalendar($studentID, $studentOffset, $serverOffset);
			$dateReturn = array();
			foreach($courseDates as $date)
				{
					$year = date('Y', strtotime($date['class_date']));
					$month = date('m', strtotime($date['class_date']));
					$day = date('d', strtotime($date['class_date']));
					$dateReturn [] = "[$year,$month,$day, '{$date['title']}']";
				}
			$return = "[".implode(',', $dateReturn)."]";//echo $return;exit;
			return $return;
		}



		function getTutorSideCalendar($tutorId, $tutorOffset = '+00:00', $serverOffset = '+00:00')
			{
				$courseDates = $this->getTutorCalendar($tutorId, $tutorOffset, $serverOffset);
				$dateReturn = array();
				foreach($courseDates as $date)
					{
						$year = date('Y', strtotime($date['class_date']));
						$month = date('m', strtotime($date['class_date']));
						$day = date('d', strtotime($date['class_date']));
						$dateReturn [] = "[$year,$month,$day, '{$date['title']}']";
					}
				$return = "[".implode(',', $dateReturn)."]";
				return $return;
				echo $return;exit;
			}

		/*
			Get Tutor Class List
			Each course may have different time zone.
			Convert all class datetime to tutors local time zone, then only we can show his calendar with todays shedule.
		*/
		public function getTutorCalendar($tutorID, $tutorOffset = '+00:00', $serverOffset = '+00:00')
		{
			//$today = date('Y-m-d');
			$tz = new timeZone();
			$tutTime = date('Y-m-d', $tz->convertTime($serverOffset, $tutorOffset, date('Y-m-d H:i:s')));

			$sql = "SELECT Course.title,Course.course_id as class_id,
					CONVERT_TZ(CONCAT(Course.start_date,' ', Course.start_time), '$serverOffset', '$tutorOffset')
					AS class_date, cd.time AS class_duration
					FROM tblcourses AS Course
					JOIN tbllookup_course_status AS cs
                      ON cs.id = Course.course_status_id
                    JOIN tbllookup_course_duration as cd
                      ON cd.id = Course.duration					
					WHERE Course.instructor_id = $tutorID 
					  AND cs.status != 'CANCELLED' 
					  AND CONVERT_TZ(Course.start_date, '$serverOffset', '$tutorOffset') >= '$tutTime'  
			     ORDER BY Course.start_date ASC";

			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;
		}

		/*
			Get Tutor Course Class List
			Each course may have different time zone.
			Convert all class datetime to tutors local time zone, then only we can show his calendar with todays shedule.
		*/
		public function getTutorCourseCalendar($courseID, $tutorOffset = '+00:00')
		{
			$sql = "SELECT Course.course_title, Class.class_id, Class.class_name, CONVERT_TZ(CONCAT(Class.class_date,' ', Class.start_time), (SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = Course.time_zone_id), '$tutorOffset') AS class_date
					FROM tblcourse_master AS Course
					LEFT JOIN tbluser_class AS Class ON Class.course_master_id = Course.course_master_id
					WHERE Course.course_master_id = $courseID order by Class.class_date ASC";
			$resultArry		=	$this->getdbcontents_sql($sql, false);
			return $resultArry;
		}

		/*
			Get tutors recent class shedule detaills.
		*/
		public function getTutorRecentClassShedule($tutorID, $serverOffset = '+00:00', $tutorDate, $numOfDays)
			{
				$today = date('Y-m-d H:i:s');
				$sql = "SELECT Course.course_master_id, Course.course_title, Class.class_name, Instrument.instrument_image,
					DATE_FORMAT(CONCAT(Class.class_date,' ', Class.start_time), '%b %d %Y %h:%i %p')
					AS class_date,DATE_FORMAT(CONCAT(Class.end_time), '%b %d %Y %h:%i %p') AS end_time,
					 TIMESTAMPDIFF(SECOND,CONVERT_TZ('$today', '$serverOffset', '+00:00'),
					CONVERT_TZ(CONCAT(Class.class_date,' ', Class.start_time),
					(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = Course.time_zone_id), '+00:00')) AS timeDiff,
					TIMESTAMPDIFF(SECOND,Class.end_time,CONCAT(Class.class_date,' ',
					Class.start_time)) AS classDuration,Class.class_id
					FROM tblcourse_master AS Course
					LEFT JOIN tbluser_class AS Class ON Class.course_master_id = Course.course_master_id
					LEFT JOIN tblinstrument_master AS Instrument ON Course.course_instrument_id = Instrument.instrument_id
					WHERE Course.tutor_id = $tutorID AND Course.is_approved = 1 AND Course.is_closed = 0 AND
					(Class.class_date >= '$tutorDate' OR Class.end_time >= '$tutorDate' )AND Class.class_date <= DATE_ADD('$tutorDate', INTERVAL $numOfDays DAY)
					ORDER BY CONCAT(Class.class_date,' ', Class.start_time) ASC";

				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				//	$this->print_r($resultArry);
				return $resultArry;
			}


	public function getTutorClassShedule($tutorID, $serverOffset = '+00:00', $tutorOffset = '+00:00', $date)
			{
				$today = date('Y-m-d H:i:s');
				$sql = "SELECT Course.course_master_id, Course.course_title, Class.class_name, Instrument.instrument_image,
					DATE_FORMAT(CONVERT_TZ(CONCAT(Class.class_date,' ', Class.start_time),
					(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = Course.time_zone_id), '$tutorOffset'),
					'%b %d %Y %h:%i %p')
					AS class_date, TIMESTAMPDIFF(SECOND,CONVERT_TZ('$today', '$serverOffset', '+00:00'),
					CONVERT_TZ(CONCAT(Class.class_date,' ', Class.start_time),
					(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = Course.time_zone_id), '+00:00')) AS timeDiff,
					TIMESTAMPDIFF(SECOND, CONCAT(Class.class_date,' ', Class.end_time),CONCAT(Class.class_date,' ', Class.start_time)) AS classDuration
					FROM tblcourse_master AS Course
					LEFT JOIN tbluser_class AS Class ON Class.course_master_id = Course.course_master_id
					LEFT JOIN tblinstrument_master AS Instrument ON Course.course_instrument_id = Instrument.instrument_id

					WHERE Course.tutor_id = $tutorID AND Course.is_approved = 1 AND Course.is_closed = 0 AND
					Class.class_date = '$date'
					ORDER BY CONCAT(Class.class_date,' ', Class.start_time) ASC";

					$rec["splage"]		=	$this->create_paging("n_page",$sql);
					$rec[0]				=	$this->getdbcontents_sql($sql, 0);
					if($limit){
						$rec	=		$rec[0];
					}

					return $rec;
			}

	public function getStudentRecentClassShedule($studentID, $serverOffset = '+00:00', $studentOffset = '+00:00', $studLocalDate, $numOfDays)
			{
				$today = date('Y-m-d H:i:s');

				$sql = "SELECT Subscribe.course_id,Course.course_master_id, Course.course_title,
					Class.class_id,Class.class_name, Instrument.instrument_image,
					DATE_FORMAT(CONVERT_TZ(CONCAT(Class.class_date,' ', Class.start_time),
					(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = Course.time_zone_id), '$studentOffset'),
					'%b %d %Y %h:%i %p') AS class_date, DATE_FORMAT(CONVERT_TZ(CONCAT(Class.end_time),
					(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = Course.time_zone_id), '$studentOffset'),
					'%b %d %Y %h:%i %p') AS end_date,

					TIMESTAMPDIFF(SECOND,CONVERT_TZ('$today', '$serverOffset', '+00:00'),
					CONVERT_TZ(CONCAT(Class.class_date,' ', Class.start_time),
					(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = Course.time_zone_id), '+00:00')) AS timeDiff,
					TIMESTAMPDIFF(SECOND,Class.end_time,CONCAT(Class.class_date,' ',
					Class.start_time)) AS classDuration ,DATE_FORMAT(CONCAT(Class.end_time), '%b %d %Y %h:%i %p') AS end_time
					FROM tblmusic_subscriber AS Subscribe
					LEFT JOIN tblcourse_master AS Course ON Subscribe.course_id = Course.course_master_id
					LEFT JOIN tbluser_class AS Class ON Class.course_master_id = Course.course_master_id
					LEFT JOIN tblinstrument_master AS Instrument ON Course.course_instrument_id = Instrument.instrument_id

					WHERE Subscribe.student_id = $studentID AND Subscribe.is_deleted = 0 AND
					CONVERT_TZ(CONCAT(Class.class_date,' ', Class.start_time), (SELECT SUBSTRING(gmt,5,6) as gmt
					FROM tbltime_zones WHERE id = Course.time_zone_id), '+00:00') AND
					(Class.class_date >= '$studLocalDate' OR Class.end_time >= '$studLocalDate') AND Class.class_date <=
					DATE_ADD('$studLocalDate', INTERVAL $numOfDays DAY)
					ORDER BY CONCAT(Class.class_date,' ', Class.start_time) ASC";

				$resultArry		=	$this->getdbcontents_sql($sql, 0);
				return $resultArry;
			}

	/*
		Generate clone of completed course.
		System will reshedule date and time of completed course and its classes with server time.
		Date and time difference of new generated course will be equel to the existing course.
	*/
	function cloneCourse($courseDet, $classDet){
			$this->dbStartTrans();
			$courseID =	$this->db_insert('tblcourse_master',$courseDet, 0);

			if(!$courseID)
				{
					$this->dbRollBack();
					$this->setPageError($this->getDbErrors());
					return false;
				}
			else
				{
					foreach($classDet as $classIndex => $classDetail)
						{
							//$classDet[$classIndex]['course_master_id'] = $courseID;
							$classDetail['course_master_id'] = $courseID;
							$status = $this->db_insert('tbluser_class',$classDetail, 0);
							if(!$status)
								{
									$this->dbRollBack();
									$this->setPageError($this->getDbErrors());
									return false;
								}
						}
				}
			return true;
		}

		public function getCourseVideos($courseID)
			{
				$sql = "SELECT Course.course_master_id , Course.course_title, Course.course_fee, Instrument.name,
						Instrument.instrument_image, Currency.currency_id, Currency.symbol, Class.class_name
						FROM tblcourse_master AS Course
						LEFT JOIN tblinstrument_master AS Instrument ON Course.course_instrument_id = Instrument.instrument_id
						LEFT JOIN tblcurrency_type AS Currency ON Course.currency_id = Currency.currency_id
						LEFT JOIN tbluser_class AS Class ON Course.course_master_id = Class.course_master_id
						WHERE Course.course_master_id = $courseID";
				$resultArry		=	$this->getdbcontents_sql($sql, false);
				return $resultArry;
			}

	/*
		Get Tutors Subscription Detils
	*/
	public function getSubscriptionDetails($courseID)
		{
			$sql = "SELECT User.user_id, User.first_name, User.last_name, User.profile_image
					FROM tblmusic_subscriber Subc
					LEFT JOIN tblusers AS User ON Subc.student_id = User.user_id
					WHERE Subc.course_id = $courseID";
			$resultArry		=	$this->getdbcontents_sql($sql, 0);
			return $resultArry;
		}

	//------------------------- Arvind -----------------------------//
	function selectCources($condition,$limit, $instrumentID = 0){

	//echo $_SESSION['InstrumentsSelected'];exit;
		$userId		=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];
		if($_SESSION['visited']==1){
			$limit		=	0;
			$userId		=	$_SESSION['visitedProfile'];
			$InstrumentsSelected 		=	$_SESSION['InstrumentsSelected'];
		}


			$sql		=	"SELECT i.instrument_id, i.name,i.instrument_image,c.course_master_id, c.course_instrument_id, c.course_title, SUBSTRING(c.course_description, 1, 75) AS course_description, c.number_of_class, c.class_duration, DATE_FORMAT(c.course_start_date, '%b %d %Y') AS course_start_date, DATE_FORMAT(c.course_end_date, '%b %d %Y') AS course_end_date, c.default_start_time, c.max_attendance,c.`vod_request`, c.course_fee, c.subscription_upto, c.course_detail_file, c.update_request, cur.`symbol`,cur.`code` FROM  `tblcourse_master` c JOIN `tblinstrument_master` i JOIN `tblcurrency_type` cur  WHERE cur.`currency_id`=c.`currency_id` AND  c.`tutor_id`=$userId AND  c.`course_instrument_id`=i.`instrument_id` AND c.`is_deleted`=0";

			if($instrumentID)
				{
					// Instrument based search from home page.
					$sql .= " AND i.`instrument_id` = $instrumentID";
				}


			if(!empty($_SESSION['InstrumentsSelected'])){
				$sql	.=	" AND c.`is_approved` =1 AND   c.`is_closed` =0   AND  i.`instrument_id`=".$_SESSION['InstrumentsSelected']." AND CONVERT_TZ(c.subscription_upto,(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = c.time_zone_id),'+00:00') >  CONVERT_TZ('".date('Y-m-d H:i:s')."','".LMT_SERVER_TIME_ZONE_OFFSET."', '+00:00')";
			}

			if($condition	==	1){// for New Courses ordering by created date
				$sql		.=	"  AND c.`is_published` =0  AND c.`is_approved` =0  ORDER By c.`course_master_id` DESC";
			}


			if($condition	== 2){// published Cources
				$sql		.=	" AND c.`is_published` =1 AND c.`is_approved` =0   ORDER By c.`course_start_date` ASC ";
			}

			if($condition	== 3){//Approved Courses
				$sql		.=	" AND c.`is_published` =1 AND c.`is_closed` =0  AND c.`is_approved` =1  ORDER By c.`course_start_date` ASC ";
			}

			if($condition	== 4){//Completed Courses
				 $sql		.=	" AND c.`is_closed` = 1   ORDER By c.`course_end_date` ASC ";
			}

			if($limit){
				$sql	.=	" LIMIT 0, $limit";

			}else{
				$rec["splage"]		=	$this->create_paging("n_page",$sql);
			}




			$rec[0]				=	$this->getdbcontents_sql($sql,0);
			//echo '<br>';
			if($limit){
				$rec	=		$rec[0];
			}

			return $rec;

		}



	function selectCourceSql($condition,$limit, $instrumentID = 0){
		$userId		=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];
		//echo $_SESSION['InstrumentsSelected'];
		$cond		=   "";
		if($_SESSION['visited']==1){
			$userId		=	$_SESSION['visitedProfile'];
			$InstrumentsSelected 		=	$_SESSION['InstrumentsSelected'];
			$cond .= "AND c.update_request != 1 AND c.update_request != 2
					AND c.update_request != 5";

		}

		 	$sql =	"SELECT i.instrument_id, i.name,i.instrument_image,c.course_master_id, c.course_instrument_id,
					c.course_title, SUBSTRING(c.course_description, 1, 75) AS course_description, c.number_of_class,
					c.class_duration, DATE_FORMAT(c.course_start_date, '%b %d %Y') AS course_start_date,
					DATE_FORMAT(c.course_end_date, '%b %d %Y') AS course_end_date, c.default_start_time, c.max_attendance 	,
					c.course_fee, c.subscription_upto, c.course_detail_file, c.update_request, cur.symbol,cur.code
					FROM  tblcourse_master AS c
					LEFT JOIN tblinstrument_master i ON c.course_instrument_id=i.instrument_id
					LEFT JOIN tblcurrency_type AS cur ON cur.currency_id=c.currency_id
					WHERE  c.tutor_id=$userId AND c.is_deleted=0 $cond";

			if($instrumentID)
				{
					// Instrument based search from home page.
					$sql .= " AND i.`instrument_id` = $instrumentID";
				}


			if(!empty($_SESSION['InstrumentsSelected']))
				{
				/*
				Pls Dont Delete it
				$sql	.=	" AND c.`is_approved` =1 AND   c.`is_closed` =0   AND  c.`course_instrument_id`=".$_SESSION['InstrumentsSelected']." AND CONVERT_TZ(c.subscription_upto,(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = c.time_zone_id),'+00:00') >  CONVERT_TZ('".date('Y-m-d H:i:s')."','".LMT_SERVER_TIME_ZONE_OFFSET."', '+00:00')";*/

				/*$sql	.=	" AND c.`is_approved` =1 AND   c.`is_closed` =0   AND  c.`course_instrument_id`=".$_SESSION['InstrumentsSelected']." AND CONVERT_TZ(c.course_end_date,(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = c.time_zone_id),'+00:00') >  CONVERT_TZ('".date('Y-m-d H:i:s')."','".LMT_SERVER_TIME_ZONE_OFFSET."', '+00:00')";*/

				/*
					Subscription expires on before 15 min of last class
				*/
				$sql	.=	" AND c.`is_approved` =1 AND   c.`is_closed` =0   AND  c.`course_instrument_id`=".$_SESSION['InstrumentsSelected']." AND CONVERT_TZ((SELECT SUBTIME(CONCAT(class_date, ' ', start_time), '00:15:00') FROM tbluser_class WHERE course_master_id = c.course_master_id ORDER BY CONCAT(class_date, ' ', start_time) DESC LIMIT 0, 1),(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = c.time_zone_id),'+00:00') >  CONVERT_TZ('".date('Y-m-d H:i:s')."','".LMT_SERVER_TIME_ZONE_OFFSET."', '+00:00')";

				}
			else if($_SESSION['visited'] == 1)
				{
					/*
					Pls Dont Delete it
					$sql	.=	" AND c.`is_approved` =1 AND   c.`is_closed` =0  AND CONVERT_TZ(c.subscription_upto,(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = c.time_zone_id),'+00:00') >  CONVERT_TZ('".date('Y-m-d H:i:s')."','".LMT_SERVER_TIME_ZONE_OFFSET."', '+00:00')";*/

					/*$sql	.=	" AND c.`is_approved` =1 AND   c.`is_closed` =0  AND CONVERT_TZ(c.course_end_date,(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = c.time_zone_id),'+00:00') >  CONVERT_TZ('".date('Y-m-d H:i:s')."','".LMT_SERVER_TIME_ZONE_OFFSET."', '+00:00')";*/

					$sql	.=	" AND c.`is_approved` =1 AND   c.`is_closed` =0  AND CONVERT_TZ((SELECT SUBTIME(CONCAT(class_date, ' ', start_time), '00:15:00') FROM tbluser_class WHERE course_master_id = c.course_master_id ORDER BY CONCAT(class_date, ' ', start_time) DESC LIMIT 0, 1),(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones WHERE id = c.time_zone_id),'+00:00') >  CONVERT_TZ('".date('Y-m-d H:i:s')."','".LMT_SERVER_TIME_ZONE_OFFSET."', '+00:00')";
				}

			if($condition	==	1){// for New Courses ordering by created date
				$sql		.=	"  AND c.`is_published` =0  AND c.`is_approved` =0  ORDER By c.`course_master_id` DESC";
			}


			if($condition	== 2){// published Cources
				$sql		.=	" AND c.`is_published` =1 AND c.`is_approved` =0   ORDER By c.`course_start_date` ASC ";
			}

			if($condition	== 3){//Approved Courses
				$sql		.=	" AND c.`is_published` =1 AND c.`is_closed` =0  AND c.`is_approved` =1  ORDER By c.`course_start_date` ASC ";
			}

			if($condition	== 4){//Completed Courses
				 $sql		.=	" AND c.`is_closed` = 1   ORDER By c.`course_end_date` ASC ";
			}

			// chages made for ver2//

				$sql		=	"SELECT c.*,d.`time`,t.`type`,t.`max_students`,cu.`symbol`,
					cu.`code`,u.`first_name`,u.`last_name`,p.`cost`,DATE_FORMAT(CONVERT_TZ(c.start_time,'".LMT_SERVER_TIME_ZONE_OFFSET."', '".$_SESSION["USER_LOGIN"]["TIMEZONE_ID"]."'),'".$_SESSION["USER_LOGIN"]["DATE_FORMAT"]["M_DATE"]."')  AS  start_date ,DATE_FORMAT(CONVERT_TZ(c.start_time,'".LMT_SERVER_TIME_ZONE_OFFSET."', '".$_SESSION["USER_LOGIN"]["TIMEZONE_ID"]."'),'".$_SESSION["USER_LOGIN"]["DATE_FORMAT"]["M_TIME"]."') as start_time
				FROM `tblcourses` c
				LEFT JOIN tblinstrument_master i ON c.instrument_id=i.instrument_id
				LEFT JOIN `tblcourse_prices` p 	 ON c.price_code=p.id
				LEFT JOIN `tbllookup_course_duration` d	ON d.`id`=c.`duration`
				LEFT JOIN `tbllookup_course_type` t ON p.`course_type`=t.`id`
				LEFT JOIN `tblcurrency_type` cu ON  p.`currency_type`=cu.`currency_id`
				LEFT JOIN `tblusers` u ON u.`user_id`=c.`instructor_id`
				WHERE p.`status`=1 AND c.`cancelled_flag`=0 AND c.`num_enrolled` < c.`max_students` AND c.`course_status_id`=".LMT_COURSE_STATUS_OPEN;

			//------- end--------///
			//print_r($_SESSION);
			//echo $sql;exit;
			return $sql;


		}

	function getLoginInfo($id){
					$query			=	"SELECT u.*,l.* from `tbluser_login` l JOIN `tblusers` u  WHERE l.`login_id`=u.`login_id` AND u.`user_id`=$id";
					$result			=	$this->getdbcontents_sql($query, 0);
					return $result;
		}


	function getSubscibers($courseID)
		{
			$query	=	"SELECT m.transaction_id,DATE_FORMAT(m.created_date,'%b %d %Y %h:%i %p') as created_date,
								c.course_title, c.course_fee,concat(u.first_name,' ',u.last_name) as name,
								u.profile_image,c.course_fee, t.symbol
								FROM tblmusic_subscriber as m
								LEFT JOIN tblcourse_master as c on c.course_master_id=m.course_id
								LEFT JOIN tblusers as u on m.student_id=u.user_id
								LEFT JOIN tblcurrency_type as t
								on c.currency_id=t.currency_id
								WHERE  c.course_master_id = $courseID AND c.is_approved = 1 AND c.is_closed = 0 AND c.is_published = 1 AND c.is_deleted = 0";
			$result	 =	$this->getdbcontents_sql($query, 0);
			return $result;
		}
	function getAllCourseTypes()
		{
			$sql	=	"SELECT id,type FROM tbllookup_course_type";
			$result	=	$this->getdbcontents_sql($sql,0);
			//$this->print_r($result);exit;
			return $result;
		}
	function getAllPricedCourseTypes($userId)
		{
				$query				=	"SELECT `instructor_level` FROM `tblusers` WHERE `user_id`= $userId";
				$rec				=	end($this->getdbcontents_sql($query));
				$type				=	$rec["instructor_level"];
				$sql				=	"SELECT id,type FROM tbllookup_course_type WHERE id IN (SELECT course_type FROM tblcourse_prices WHERE status = 1 AND instructor_level =".$rec["instructor_level"].")";
				$result	=	$this->getdbcontents_sql($sql);
			return $result;
		}
	function getDurationMinutes($id)
		{
			$sql	=	"SELECT time FROM tbllookup_course_duration WHERE id =$id";
			$result	=	end($this->getdbcontents_sql($sql,0));
			return $result['time'];
		}
	function getPriceValue($id)
		{
			$sql	=	"SELECT P.cost,C.symbol FROM tblcourse_prices  AS P LEFT JOIN tblcurrency_type AS C
						 ON P.currency_type = C.currency_id WHERE id =$id";
			$result	=	end($this->getdbcontents_sql($sql,0));
			return $result['symbol']." ".$result['cost'];
		}
	/*function getMyCourses($id,$cond="")
		{
			$sql	=	"SELECT C.title,C.course_code,C.course_status_id , DATE_FORMAT(start_date,'".$_SESSION['USER_LOGIN']['DATE_FORMAT']['M_DATE']."') AS start_date,
						 DATE_FORMAT(start_time,'".$_SESSION['USER_LOGIN']['DATE_FORMAT']['M_TIME']."') AS start_time ,P.cost,D.time , V.id AS video_id , N.id AS note_id, R.symbol,
						 C.max_students, COUNT(E.enrolled_id) AS tot_enrolled FROM tblcourses AS C LEFT JOIN tblcourse_prices AS P ON C.price_code =  P.id LEFT JOIN tbllookup_course_duration AS D
						 ON D.id = P.duration LEFT JOIN tblcourse_videos AS V ON V.course_id = C.course_id AND V.video_owner_id = ".$_SESSION['USER_LOGIN']['LMT_USER_ID']."
						 AND V.video_status = 1 LEFT JOIN tblcourse_notes AS N ON N.course_id = C.course_id AND N.note_owner_id = ".$_SESSION['USER_LOGIN']['LMT_USER_ID']."
						 AND N.note_status = 1 LEFT JOIN tblcourse_enrollments AS E ON C.course_id = E.course_id LEFT JOIN tblcurrency_type AS R
						 ON R.currency_id = P.currency_type WHERE instructor_id = $id $cond GROUP BY C.course_id ORDER BY C.created_on DESC";

			return $sql;

		}	*/

	function getCourseStats()
		{
			$sql	=	"SELECT * FROM  tbllookup_course_status";
			$result	=	$this->getdbcontents_sql($sql,0);
			return $result;
		}
	function getEnrollStats()
		{
			$sql	=	"SELECT * FROM  tbllookup_enrolled_status";
			$result	=	$this->getdbcontents_sql($sql,0);
			return $result;
		}
	function getCourseDetails($cCode)
		{
			$sql	=  "SELECT course_id,title,description,course_code,course_type_id,instrument_id,duration,price_code,DATE_FORMAT(start_date,'%m/%d/%Y') AS course_start_date,
						start_time AS course_start_time,duration,price_code,max_students,min_required FROM tblcourses WHERE course_code = '$cCode'";
			$result = end($this->getdbcontents_sql($sql,0));
			return $result;
		}
	function getCourseType($id)
		{
			$sql	=  "SELECT type FROM  tbllookup_course_type WHERE id = '$id'";
			$result = end($this->getdbcontents_sql($sql,0));
			return $result['type'];
		}
	function getCourseCancelReasons($id)
		{
			$sql	=  "SELECT id,reason FROM  tbllookup_course_status_reason WHERE status_id = ".LMT_COURSE_STATUS_CANCELLED;
			$result = $this->getdbcontents_sql($sql);
			return $result;
		}
	function getCourseCloseReasons($id)
		{
			$sql	=  "SELECT id,reason FROM  tbllookup_course_status_reason WHERE status_id = ".LMT_COURSE_STATUS_CLOSED;
			$result = $this->getdbcontents_sql($sql);
			return $result;
		}
	function getEnrollCancelReasons($id)
		{
			$sql	=  "SELECT id,reason FROM  tbllookup_enrolled_status_reason WHERE status_id = ".LMT_CS_ENR_CANCELLED;
			$result = $this->getdbcontents_sql($sql,0);
			return $result;
		}
	/*function getCourseSubscribers($Id,$cond="")
		{
			$cls		=	new userCourse();
			$courseId	=	$cls->getCourseId($Id);

			$sql		=	"SELECT E.enrolled_status_id, E.paid_flag, V.id AS video_id , N.id AS note_id,
							 DATE_FORMAT(E.created_on,'".$_SESSION['USER_LOGIN']['DATE_FORMAT']['M_DATE']."') AS subscribed_date,
							 U.user_id,concat(U.first_name,' ',U.last_name) AS name
							 FROM tblcourse_enrollments AS E
							 LEFT JOIN tblcourses AS C ON E.course_id = C.course_id
							 LEFT JOIN tblusers AS U ON U.user_id = E.student_id
							 LEFT JOIN tblcourse_videos AS V ON V.course_id = C.course_id AND V.video_owner_id = U.user_id
							 AND V.video_status = 1
							 LEFT JOIN tblcourse_notes AS N ON N.course_id = C.course_id AND N.note_owner_id = U.user_id
							 AND N.note_status = 1
							 WHERE E.course_id = $courseId $cond ORDER BY E.created_on DESC";

			//$result	=	$this->getdbcontents_sql($sql, 0);
			//return $result;

			return $sql;
		}*/
	function insCourseSummary($Id,$fields="",$cond="",$groupBy="")
		{
			$sql	=  "SELECT  SUM(T.`trans_amount`) as cost,COUNT(E.`enrolled_id`) as count ,
						$fields ,cur.`symbol`
						FROM `tblcourse_enrollments` E
						LEFT JOIN `tblcourses` C ON C.`course_id`=E.`course_id`
						LEFT JOIN `tblcourse_enrollment_transaction` T ON T.enrolled_id = E.enrolled_id
						LEFT JOIN `tblcurrency_type` cur ON cur.`currency_id`=T.`currency_id`
						WHERE E.enrolled_status_id != ".LMT_CS_ENR_CANCELLED." AND C.course_type_id !=".LMT_COURSE_STATUS_CANCELLED." AND
						C.`instructor_id`=".$_SESSION['USER_LOGIN']['LMT_USER_ID']."$cond
						GROUP BY $groupBy ORDER BY E.created_on DESC";
			return $sql;
		}
	function getMyAllCourses($userId)
		{
				$query		=	"SELECT c.* FROM `tblcourses` c  WHERE  c.`course_status_id` !=".LMT_COURSE_STATUS_CANCELLED." AND c.`instructor_id`=".$userId;
				$rec		=	$this->getdbcontents_sql($query);
				return $rec;
		}
	function getCourseDuration($id)
		{
				$query		=	"SELECT time FROM tbllookup_course_duration  WHERE  id =".$id;
				$rec		=	end($this->getdbcontents_sql($query,0));
				return $rec['time'];
		}
	function getSubscribersEmailIds($serOffset,$courseId)
		{
				$sql		=	"SELECT E.enrolled_id,U.user_id,concat(U.first_name,' ',U.last_name) AS name,L.user_name,
								 T.php_date_format,T.php_time_format,
								 CONVERT_TZ(CONCAT(C.start_date,' ',C.start_time),'$serOffset',(SELECT
								 SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones AS Z WHERE Z.id = U.time_zone_id))
								 AS course_start
								 FROM tblcourse_enrollments AS E
								 LEFT JOIN tblcourses AS C ON E.course_id = C.course_id
								 LEFT JOIN tblusers AS U ON U.user_id = E.student_id
								 LEFT JOIN tbluser_login AS L ON U.login_id = L.login_id
								 LEFT JOIN tbllookup_user_timestamp AS T ON T.id = U.time_format_id
								 WHERE E.course_id = $courseId"	;
				$result		=	$this->getdbcontents_sql($sql,0);
				return $result;
		}
	function getCourseBasics($courseCode)
		{
				$sql		=	"SELECT course_id,title,description,start_date,start_time,created_on,
								 duration,max_students,min_required
								 FROM tblcourses WHERE course_code = '".$courseCode."'";
				$result		=	end($this->getdbcontents_sql($sql));
				return $result;
		}
	function getMailingDetails($enrollId,$serOffset)
		{
				$sql		=	"SELECT C.course_code,C.title,C.description,C.start_date,C.start_time,C.created_on,
								 C.duration,C.max_students,C.min_required,
								 U.user_id,concat(U.first_name,' ',U.last_name) AS name,L.user_name,
								 T.php_date_format,T.php_time_format,
								 DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date,' ',C.start_time),'$serOffset',(SELECT SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones AS Z WHERE Z.id = U.time_zone_id)),'%h:%i %p')
								 AS course_start
								 FROM tblcourse_enrollments AS E
								 LEFT JOIN tblcourses AS C ON E.course_id = C.course_id
								 LEFT JOIN tblusers AS U ON U.user_id = C.instructor_id
								 LEFT JOIN tbluser_login AS L ON U.login_id = L.login_id
								 LEFT JOIN tbllookup_user_timestamp AS T ON T.id = U.time_format_id
								 WHERE E.enrolled_id = $enrollId"	;
				$result		=	end($this->getdbcontents_sql($sql));
				return $result;
		}
	function getTutorMailingDetails($courseCode,$serOffset)
		{
				$sql		=	"SELECT C.course_code,C.title,C.description,C.start_date,C.start_time,C.created_on,
								 C.duration,C.max_students,C.min_required,
								 U.user_id,concat(U.first_name,' ',U.last_name) AS name,L.user_name,
								 T.php_date_format,T.php_time_format,
								 CONVERT_TZ(CONCAT(C.start_date,' ',C.start_time),'$serOffset',(SELECT
								 SUBSTRING(gmt,5,6) as gmt FROM tbltime_zones AS Z WHERE Z.id = U.time_zone_id))
								 AS course_start
								 FROM tblcourses AS C
								 LEFT JOIN tblusers AS U ON U.user_id = C.instructor_id
								 LEFT JOIN tbluser_login AS L ON U.login_id = L.login_id
								 LEFT JOIN tbllookup_user_timestamp AS T ON T.id = U.time_format_id
								 WHERE C.course_code = '$courseCode'"	;
				$result		=	end($this->getdbcontents_sql($sql));
				return $result;
		}
	function getCourseSubscribers($cId,$cond="",$serOffset,$instOffset,$dtFmt)
		{
			$courseId	=	$this->getCourseId($cId);

			$sql		=	"SELECT E.enrolled_status_id, E.paid_flag, V.id AS video_id ,V.archive_id as video_link, N.id AS note_id,E.panic_flag,
							 DATE_FORMAT(CONVERT_TZ(E.created_on, '$serOffset','$instOffset'),'$dtFmt') AS subscribed_date,
							 C.course_code, U.user_id, U.user_code,concat(U.first_name,' ',U.last_name) AS name
							 FROM tblcourse_enrollments AS E
							 LEFT JOIN tblcourses AS C ON E.course_id = C.course_id
							 LEFT JOIN tblusers AS U ON U.user_id = E.student_id
							 LEFT JOIN tblcourse_archives AS V ON V.course_id = C.course_id
							 LEFT JOIN tblcourse_notes AS N ON N.course_id = C.course_id AND N.note_owner_id = U.user_id
							 AND N.note_status = 0
							 WHERE E.course_id = $courseId $cond ORDER BY E.created_on DESC";

			return $sql;
		}
	function checkCourseOverlap($uId, $startDate, $startTime, $cond = "")
		{

			$sql		=	"SELECT C.course_id FROM tblcourses AS C
							LEFT JOIN tbllookup_course_duration AS D ON D.id = C.duration
							WHERE C.instructor_id = $uId AND
							UNIX_TIMESTAMP('".$startDate." ".$startTime."') >= UNIX_TIMESTAMP(CONCAT(C.start_date,'
							',C.start_time)) AND UNIX_TIMESTAMP('".$startDate." ".$startTime."') <
							UNIX_TIMESTAMP(DATE_ADD(CONCAT(C.start_date,' ',C.start_time),INTERVAL D.time MINUTE)) $cond";

		    $result 	=	$this->getdbcontents_sql($sql, 0);
			return $result;
		}
	function getSheetMusic($cCode ="",$serverOffset = '+00:00', $insOffset = '+00:00', $dtFmt = '%b %d %Y')
		{
			$courseId	=	$this->getCourseId($cCode);
			$sql		=	"SELECT id,sheet_name,real_name, DATE_FORMAT(CONVERT_TZ(created_on, '$serverOffset',
							'$insOffset'),'$dtFmt') AS created_date FROM tblcourse_sheetmusic
			 				 WHERE course_id=$courseId";
			$result		=	$this->getdbcontents_sql($sql,0);
			return $result;
		}
	function checkCourseRating($userId, $courseId)
		{
			$sql = "SELECT id FROM tblcourse_ratings
					WHERE rated_by = $userId AND course_id = $courseId";
			$resultArry	 =	$this->getdbcontents_sql($sql, false);
			if(!empty($resultArry))
				return true;
			else
				return false;
		}
	function getCourseInfo($courseCode,$serverOffset = '+00:00', $userOffset = '+00:00', $dtFmt = '%b %d %Y')
		{
			$sql = "SELECT CS.title, CONCAT(USER.first_name, ' ',USER.last_name) AS tutor_name, USER.profile_image, INST.name,
					INST.instrument_image,DATE_FORMAT(CONVERT_TZ(concat(CS.start_date,' ',CS.start_time), '$serverOffset','$userOffset'),'$dtFmt') AS course_date
					FROM tblcourses AS CS
					LEFT JOIN tblusers AS USER ON CS.instructor_id = USER.user_id
					LEFT JOIN tblinstrument_master  AS INST ON CS.instrument_id = INST.instrument_id
					WHERE CS.course_code = '$courseCode'";
			$resultArry		=	$this->getdbcontents_sql($sql,0);
			return $resultArry;
		}
	function getCourseTitles($uId)
		{
			$sql	=	"SELECT CONCAT(\"'\",title,\"'\",',') as title  FROM tblcourses
						 WHERE instructor_id = $uId LIMIT 0,15";
			$result	=	$this->getdbcontentshtml_sql($sql,0);
			foreach($result as $key=>$value){
				$title		.=	$value["title"];
			}
			return trim($title,",");
		}
	function getInstructorId($courseId)
		{
			$sql	=	"SELECT instructor_id FROM tblcourses WHERE course_id = $courseId";
			$result	=	end($this->getdbcontents_sql($sql,0));
			return $result['instructor_id'];
		}

	function getCourseTransactionAmount($userId, $enrId)
		{
			$sql	=	"SELECT trans_amount FROM tblcourse_enrollment_transaction  WHERE user_id = $userId AND enrolled_id = $enrId";
			$result	=	end($this->getdbcontents_sql($sql, 0));
			return (!empty($result)) ? $result['trans_amount'] : 0;
		}

	function getCourseDate($userId, $enrId)
		{
			$sql	=	"SELECT CS.start_date, CS.start_time FROM tblcourse_enrollments AS ENR
						LEFT JOIN tblcourses AS CS ON ENR.course_id = CS.course_id
						WHERE ENR.student_id = $userId AND ENR.enrolled_id = $enrId";
			$result	=	end($this->getdbcontents_sql($sql, 0));
			return $result;
		}

	function getCancelCourseDet($userId, $enrId)
		{
			$sql	=	"SELECT CS.course_code, CS.title, CS.start_date, CS.start_time FROM tblcourse_enrollments AS ENR
						LEFT JOIN tblcourses AS CS ON ENR.course_id = CS.course_id
						WHERE ENR.student_id = $userId AND ENR.enrolled_id = $enrId";
			$result	=	end($this->getdbcontents_sql($sql, 0));
			return $result;
		}

	function getOrderId($userId, $enrId)
		{
			$sql	=	"SELECT id FROM tblcourse_enrollment_transaction  WHERE user_id = $userId AND enrolled_id = $enrId";
			$result	=	end($this->getdbcontents_sql($sql, 0));
			return (!empty($result)) ? $result['id'] : 0;
		}

	function getEnrollmentMailDet($courseId, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
		{
			$sql	=	"SELECT C.course_code, C.title, C.description, 
						DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ',C.start_time), '$serverOffset', '$studentOffset'), '$dtFmt') AS start_date, 
						DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ',C.start_time), '$serverOffset','$studentOffset'), '$tmFmt') AS start_time, 
						D.time
						FROM tblcourses AS C
						LEFT JOIN tbllookup_course_duration AS D ON D.id = C.duration
						WHERE C.course_id = $courseId";
			$result	=	end($this->getdbcontents_sql($sql, 0));
			return $result;
		}

	function getEnrollmentCancelMailDet($enrollId, $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p', $condition = "", $serverOffset = "+00:00",$studentOffset = "+00:00")
		{
			$sql	=	"SELECT E.enrollment_code, C.course_code, C.title, DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ',
						C.start_time), '$serverOffset', '$studentOffset'), '$dtFmt') AS start_date, DATE_FORMAT(CONVERT_TZ(CONCAT(C.start_date, ' ', C.start_time), '$serverOffset',
						'$studentOffset'), '$tmFmt') AS start_time
						FROM tblcourse_enrollments AS E
						LEFT JOIN tblcourses AS C ON E.course_id = C.course_id
						WHERE E.enrolled_id = $enrollId";
			$result	=	end($this->getdbcontents_sql($sql, 0));
			return $result;
		}

	function getCourseCancelTransactionDet($transactionID)
		{
			$sql	=	"SELECT * FROM tblcourse_cancel_transaction  WHERE id = $transactionID";
			$result	=	end($this->getdbcontents_sql($sql, 0));
			return $result;
		}
	function chkUserExist($email)
		{
			$sql   		=	"SELECT login_id FROM tbluser_login WHERE user_name = '$email'";
			$result		=	end($this->getdbcontents_sql($sql,0));
			return $result['login_id'];
		}

	function checkCourseStatus($courseId)
		{
			$sql   		=	"SELECT course_status_id FROM tblcourses WHERE course_id = '$courseId'";
			$result		=	end($this->getdbcontents_sql($sql,0));
			return $result['course_status_id'];
		}

	function checkUserCoursePanic($userId,$courseId)
		{
			$sql   		=	"SELECT student_id FROM tblcourse_panic WHERE course_id = '$courseId' AND student_id = '$userId'";
			$result		=	end($this->getdbcontents_sql($sql,0));
			return $result['student_id'];
		}
	function chkCoursePriceSet($data)
		{
			$sql		=	"SELECT id FROM tblcourse_prices  WHERE instructor_level = ".$data['instructor_level']." AND course_type =".$data['course_type']." AND currency_type =".$data['currency_type'];
			if($this->getdbcontents_sql($sql,0))
				return true;
			return false;

		}
}
?>