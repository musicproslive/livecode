<?php 
/**************************************************************************************
 Created By 	:ss
Created On	:05-07-2011
Purpose		:User Management
**************************************************************************************/

class userManagement extends siteclass
{

	public function insertUserDetails($loginData, $userData, $instruments)
	{
		//print_r($userData);
		$insertFlag = 1;
		$loginData['user_group'] = 1; // User Entry.
		$loginData['created'] = date('Y-m-d H:i:s');
		$userData['created'] =  "escape now() escape";
		$this->dbStartTrans();
		$loginID =	$this->db_insert('tbluser_login',$loginData);

		if($loginID)
		{
			do//create random code.
			{
				$randCode				=	$this->createRandom(25);
			}
			while($this->getdbcount_sql("SELECT * FROM tblusers WHERE user_code='$randCode'") > 0);

			$userData['user_code'] = $randCode;
			$userData['login_id']  = $loginID;
			$userID 			   = $this->db_insert('tblusers',$userData);
			/*
			 $rev_a['rev_image_id'] = 111000+$userData['login_id'];
			$rev_a['rev_image_file'] = 'profile-no-img.png';
			$rev_a['rev_file_type'] = 'pictureimage';
			$rev_a['rev_date_time'] = date('Y-m-d H:i:s');
			$rev_a['imageuser'] = $userData['user_name'];
			$rev_a['image_owner_id'] = $userData['login_id'];
			$rev_a['rev_image_id'] = $this->getClientIP();
			*/

			if($userID)
			{
				if($userData['age_group']==1)
				{
					$insArr = array(
							'user_id' => $userID,
							'picture_video' => 0,
							'im_screen_no' => 0,
							'wall_post_permition' => 0,
							'post_by_me' => 0,
							'basic_info' => 0,
							'show_address' => 0,
							'show_education_work' => 0,
							'activity_interest' => 0,
							'contact_info' => 0
					);
					$this->db_insert("tblusers_privacy_control",$insArr,0);
				}
				else
				{
					$this->db_insert("tblusers_privacy_control",array("user_id"=>$userID),0);
					// Set Default Privacy Setting
				}
				$instrumentList = array();
				if(!empty($instruments))
				{
					foreach($instruments as $instrument)
					{
						$instrumentList ['user_id'] = $userID;
						$instrumentList ['instrument_id'] = $instrument;
						$instrumentList ['created'] = "escape now() escape";
						$instrumentID = $this->db_insert('tbluser_instruments',$instrumentList);
						if(!$instrumentID){
							$insertFlag = 0;
							break;
						}
					}
				}
			}else{
				$insertFlag = 0;
			}
		}
		else{
			$insertFlag = 0;
		}
		if(!$insertFlag)
		{
			$this->dbRollBack();
			$this->setPageError($this->getDbErrors());
			return false;
		}
		else	return $loginID;
	}
		
	function getClientIP()
	{
		return $_SERVER['REMOTE_ADDR'];
	}
		
		
	public function userEmailAuth($loginID)
	{
		$sql = "UPDATE tbluser_login set authorized = 1 WHERE login_id = $loginID";
		return $this->db_query($sql, 0);
		exit;
	}
		
	// Fetch User Details Based On emailID
	public function getUser($emailID)
	{
		$sql = "SELECT login_id, user_pwd FROM tbluser_login WHERE user_name = '$emailID' AND user_group > 0 AND authorized = 1 AND privacy_policy = 1 AND is_deleted = 0";
		$resultArry		=	$this->getdbcontents_sql($sql, false);
		return $resultArry;
	}

	public function updateUserPassword($emailID, $newPwd)
	{
		$sql = "UPDATE tbluser_login SET user_pwd = '$newPwd' WHERE user_name = '$emailID'";
		return $this->db_query($sql);
	}

	public function chkUserLogin($userName, $password)
	{
		$password = md5($password);
		//$sql =	"CALL pdruser_login('".$userName."','".$password."')";
		$sql = "SELECT login_id, authorized, admin_authorize FROM tbluser_login WHERE user_name = '$userName'
		AND user_pwd = '$password' AND privacy_policy = 1 AND is_deleted = 0";
		//echo $sql;
//			exit;
		$resultArry		=	$this->getdbcontents_sql($sql, 0);
		mysqli_next_result($this->con);
		return $resultArry;
	}

	public function chkUserLoginFromOut($loginId)
	{
		$password = md5($password);
		$sql = "SELECT login_id, authorized, admin_authorize FROM tbluser_login WHERE login_id = $loginId
		AND authorized = 1 AND privacy_policy = 1 AND is_deleted = 0";
		$resultArry		=	$this->getdbcontents_sql($sql, 0);
		return $resultArry;
	}

		
	/*
	 Remote Admin Login
	*/
	public function chkRemoteUserLogin($userName, $password)
	{
		$sql = "SELECT login_id, admin_authorize FROM tbluser_login WHERE user_name = '$userName' AND
		user_pwd = '$password' AND user_group > 0";
		$resultArry		=	$this->getdbcontents_sql($sql, 0);
		return $resultArry;
	}

	public function getUserPassword($userID)
	{
		$sql = "SELECT L.login_id, L.user_name, L.user_pwd, L.user_role as user_category_id
		FROM tblusers AS U
		LEFT JOIN tbluser_login AS L ON U.login_id = L.login_id
		WHERE user_id = $userID";
		$resultArry	 =	$this->getdbcontents_sql($sql, 0);
		return $resultArry;
	}
		
	/*
	 Get user login details
	*/
	public function getLoginUser($loginID)
	{
		$sql = "SELECT User.user_id,User.user_code, User.first_name, User.last_name,User.profile_image ,
		User.time_zone_id, User.age_group, UL.user_name, Role.role_access_key,
		T.php_date_format, T.php_time_format, T.mysql_date_format, T.mysql_time_format
		FROM tblusers AS User
		LEFT JOIN tbluser_login AS UL ON User.login_id = UL.login_id
		LEFT JOIN tbluser_roles AS Role ON UL.user_role  = Role.role_id
		LEFT JOIN tbllookup_user_timestamp AS T ON User.time_format_id = T.id
		WHERE User.login_id = $loginID AND  User.is_deleted = 0";
		$resultArry		=	$this->getdbcontents_sql($sql, 0);
		return $resultArry;
	}
		
	/*
	 remote Login Admin
	*/
	public function getRemoteLoginUser($loginID, $userCategory)
	{
		$sql = "SELECT User.user_id, User.first_name, User.last_name,User.profile_image ,
		Role.role_id, Role.role_access_key
		FROM tblusers AS User

		LEFT JOIN tbluser_roles AS Role ON Role.role_id  != ''
		WHERE User.login_id = $loginID AND Role.role_id = $userCategory";
		$resultArry		=	$this->getdbcontents_sql($sql, 1);
		return $resultArry;
	}

	public function getUserDetails($userID)
	{
		$sql = "SELECT User.user_id, User.first_name, User.about_me, User.last_name,User.profile_image ,
		User.time_zone_id, Role.role_access_key
		FROM tblusers AS User
		LEFT JOIN tbluser_login AS UL ON User.login_id = UL.login_id
		LEFT JOIN tbluser_roles AS Role ON UL.user_role  = Role.role_id
		WHERE User.user_id = $userID";
		$resultArry		=	$this->getdbcontents_sql($sql, 0);
		return $resultArry;
	}

	function getRegisterdInstruments(){
		$userId			=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];
		if(!empty($_SESSION['visited'])){
			$userId			= $_SESSION['visitedProfile'];
		}
			
		/*$sql			=	"SELECT m.instrument_id, m.name, m.instrument_image FROM `tbluser_instruments` u JOIN `tblinstrument_master` m ON u.`instrument_id`=m.`instrument_id`  WHERE m.is_deleted = 0 AND u.is_deleted = 0 AND  u.`user_id`=".$userId; */
			
		$sql	=	"CALL pdr_usermanagement_getregisteredinstruments(".$userId.")";

		$result			=	$this->getdbcontents_sql($sql, 0);
		mysqli_next_result($this->con);
			
		return $result;
	}

	function updateProfileImage($path){
		$sql 		= "UPDATE tblusers SET profile_image = '$path' WHERE user_id =".$_SESSION['USER_LOGIN']['LMT_USER_ID'];
		return $this->db_query($sql);
	}

	function getUserInfo($id, $dtFmt = '%b %d %Y'){
		$query			=	"SELECT User.*, DATE_FORMAT(User.dob,'$dtFmt') AS dob, Country.country_name 
		FROM tblusers AS User
		LEFT JOIN tblcountries AS Country 
		ON User.country_id = Country.country_id
		WHERE user_id = $id";
		$result			=	$this->getdbcontents_sql($query, false);
		return $result;
	}

	function getUserdob($id){
		$query			=	"SELECT dob FROM tblusers WHERE user_id=$id";
		$result			=	$this->getdbcontents_sql($query, false);

		return $result;
	}

	function getLoginInfo($id){
		$query			=	"SELECT l.* from `tbluser_login` l JOIN `tblusers` u  WHERE l.`login_id`=u.`login_id` AND u.`user_id`=$id";
		$result			=	$this->getdbcontents_sql($query, 0);
		return $result;
	}

	function getCityById($state){
		$query			=	"SELECT * FROM `tblcities` WHERE `state_id`=$state";
		$result			=	$this->getdbcontents_sql($query, 0);
		return $result;
	}

	function getUserAddress($id){
		$query		=	"SELECT * FROM `tbluser_address` WHERE `user_id`=$id";
		$result			=	$this->getdbcontents_sql($query, 0);
		return $result;
	}

	function chkUserIntrest($id){
		$query	=	"SELECT * FROM `tbluser_interest` WHERE `user_id`=$id";
		$result			=	$this->getdbcontents_sql($query, 0);
		if(count($result)>0){
			return true;
		}else{
			return false;
		}
			
	}

	function chkUserAddress($id){
		$query	=	"SELECT * FROM `tbluser_address` WHERE `user_id`=$id";
		$result			=	$this->getdbcontents_sql($query, 0);
		if(count($result)>0){
			return true;
		}else{
			return false;
		}
			
	}

	function checkUserPassword($userID, $password)
	{
		$query	=	"SELECT Login.user_pwd FROM tblusers AS User
		LEFT JOIN tbluser_login AS Login ON User.login_id=Login.login_id
		WHERE user_id = $userID AND Login.user_pwd = '$password' ";
		//return $query;
		$result	 =	$this->getdbcontents_sql($query, 0);
		return $result;
	}

	function getUserIntrest($id){
		$query			=	"SELECT m.`interest`,m.`interest_id`,i.`user_interest_id` FROM `tblinterest_master` m JOIN `tbluser_interest` i WHERE m.`interest_id`=i.`interest_id` AND i.`user_id`=$id ";
		$result			=	$this->getdbcontents_sql($query, 0);
		return $result;
	}

	function getTotaIntrest($id,$values){
		$query		=	"SELECT `interest`,`interest_id` FROM `tblinterest_master`  ORDER BY `interest` ASC";
			
		if(!empty($value)){
			$query			=	"SELECT m.`interest`,m.`interest_id`,i.`user_interest_id` FROM `tblinterest_master` m JOIN `tbluser_interest` i ";

			$query		.=	" WHERE m.`interest_id`=i.`interest_id` AND i.`user_id`=$id ";
			$query			.=	" ORDER BY m.`interest` ASC";
		}
			
		$result			=	$this->getdbcontents_sql($query, 0);
		return $result;

	}

	function getUserActivity($id){
		$query			=	"SELECT m.`activity`,m.`activity_id`,i.`user_activity_id` FROM `tblactivity_master` m JOIN `tbluser_activity` i WHERE m.`activity_id`=i.`activity_id` AND i.`user_id`=$id ";
		$result			=	$this->getdbcontents_sql($query, 0);
		return $result;
	}

	function getTotalActivity(){
		$query		=	"SELECT `activity`,`activity_id` FROM `tblactivity_master`WHERE `is_deleted`=0  ORDER BY `activity` ASC";
		$result			=	$this->getdbcontents_sql($query, 0);
		return $result;
	}

	function getCountryCode(){
		$query		=	"SELECT * FROM `tblcountry_code` WHERE is_active = 1";
		$result	=	$this->getdbcontents_sql($query,0);
		return $result;
	}

	function getImRecords(){
		$query		=	"SELECT * FROM `tblimcode_master` WHERE `is_deleted`!=1";
		$rec		=	$this->getdbcontents_sql($query,"0");
		return $rec;
	}

	function getProfection($id){
		$query		=	"SELECT * FROM `tbluser_employment` WHERE `user_id`=$id";
		$rec		=	$this->getdbcontents_sql($query,"0");
		return $rec;
	}

	function uerLanguage($id){
		$query			=	"SELECT m.*,c.* FROM `tbllanguage_master` m JOIN `tbluser_languages` c WHERE m.`language_id`=c.`language_master_id` AND c.`user_id`=$id";
		$rec		=	$this->getdbcontents_sql($query,"0");
		return $rec;

	}

	function getEmployeDetails($id){

		$query	=	"SELECT * FROM `tbluser_employment` WHERE `user_id`=$id";
		$rec	=	$this->getdbcontents_sql($query);
		return $rec;
	}
	function getSocialMediaLinks($uid) {
		$uid = (int)$uid;
		$query = "SELECT `facebook` , `twitter` , `youtube_channel`, `linkedin`, `url`
				from tbl_social_media_links WHERE `user_id` = $uid";
		$links = $this->getdbcontents_sql($query, 0);
		return $links;
	}

	// to get t6he permition for the user

	function getPermition($id){
		$query	=	"SELECT * FROM `tblusers_privacy_control` WHERE `user_id`=$id";
		$rec	=	$this->getdbcontents_sql($query, 0);
		return $rec;
	}

	// function to get the friend details
	function getFriendDetails($userId,$visitedProfile){

		$query			=	"SELECT * FROM `tblfriends_request` WHERE `user_id`=$userId  AND `friends_id`=$visitedProfile OR `user_id`=$visitedProfile  AND `friends_id`=$userId ";
		$rec			=	$this->getdbcontents_sql($query,0);
		return $rec;
	}


	function requestForFriend($userId,$visiteId){
		$this->db_insert("tblfriends_request",array("user_id"=>$userId,"friends_id"=>$visiteId,"requested_date"=>date("Y-m-d H:i:s"),"status"=>1),0);

	}
	// for Friends
	function getTutorCourses($userId){
		$query			=	"SELECT course_title,course_master_id FROM `tblcourse_master` WHERE `tutor_id`=$userId  AND is_approved=1";
		$rec			=	$this->getdbcontents_sql($query,0);
		return $rec;
	}//for getting tutor courses
	function getTutorCoursesNew($userId){
		$query			=	"SELECT course_title,course_master_id FROM `tblcourse_master` WHERE `tutor_id`=$userId  AND is_approved = 1 AND is_closed = 0 AND is_published = 1 AND is_deleted = 0 ";
		$rec			=	$this->getdbcontents_sql($query,0);
		return $rec;
	}//for getting tutor courses
	function getTutorCoursesOld($userId){
		$query			=	"SELECT course_title,course_master_id FROM `tblcourse_master` WHERE `tutor_id`=$userId  AND is_approved = 1 AND is_closed = 1 AND is_published = 1 AND is_deleted = 0 ";
		$rec			=	$this->getdbcontents_sql($query,0);
		return $rec;
	}//for getting tutor courses
	function getSubscriptionDetailsNew($userId){
		$query			=	"SELECT m.transaction_id,DATE_FORMAT(m.created_date,'%b %d %Y %h:%i %p') as created_date,c.course_title,c.course_fee,concat(u.first_name,' ',u.last_name) as name,u.profile_image,c.course_fee,t.symbol from tblmusic_subscriber as m
		LEFT JOIN tblcourse_master as c on c.course_master_id=m.course_id
		LEFT JOIN tblusers as u on m.student_id=u.user_id
		LEFT JOIN tblcurrency_type as t
		on c.currency_id=t.currency_id WHERE c.tutor_id=$userId AND c.is_approved = 1 AND c.is_closed = 0 AND c.is_published = 1 AND c.is_deleted = 0 ";

		return $query;
	}
	function getSubscriptionDetailsOld($userId){
		$query			=	"SELECT m.transaction_id,DATE_FORMAT(m.created_date,'%b %d %Y %h:%i %p') as created_date,c.course_title,c.course_fee,concat(u.first_name,' ',u.last_name) as name,u.profile_image,c.course_fee,t.symbol from tblmusic_subscriber as m
		LEFT JOIN tblcourse_master as c on c.course_master_id=m.course_id
		LEFT JOIN tblusers as u on m.student_id=u.user_id
		LEFT JOIN tblcurrency_type as t
		on c.currency_id=t.currency_id WHERE c.tutor_id=$userId AND c.is_approved = 1 AND c.is_closed = 1 AND c.is_published = 1 AND c.is_deleted = 0 ";
		/*$rec			=	$this->getdbcontents_sql($query,1)*/;
		return $query;
	}
	function getTotalSubscriptionfeeNew($userId)
	{
		$query			=	"SELECT SUM(c.course_fee) as fee from tblmusic_subscriber as m LEFT JOIN tblcourse_master as c on c.course_master_id=m.course_id LEFT JOIN tblusers as u on m.student_id=u.user_id LEFT JOIN tblcurrency_type as t on c.currency_id=t.currency_id
		WHERE c.tutor_id=$userId AND c.is_approved = 1 AND c.is_closed = 0 AND c.is_published = 1 AND c.is_deleted = 0 ";
		$totalfee		=	$this->getdbcontents_sql($query,0);
		return $totalfee[0]['fee'];
	}
	function getTotalSubscriptionfeeOld($userId)
	{
		$query			=	"SELECT SUM(c.course_fee) as fee from tblmusic_subscriber as m LEFT JOIN tblcourse_master as c on c.course_master_id=m.course_id LEFT JOIN tblusers as u on m.student_id=u.user_id LEFT JOIN tblcurrency_type as t on c.currency_id=t.currency_id
		WHERE c.tutor_id=$userId AND c.is_approved = 1 AND c.is_closed = 1 AND c.is_published = 1 AND c.is_deleted = 0 ";
		$totalfee		=	$this->getdbcontents_sql($query,0);
		return $totalfee[0]['fee'];
	}
		
	function getFriendsInstruments($id){
		
		$userId			=	implode(',', $id);
		$sql			=	"SELECT m.instrument_id, m.name, m.instrument_image, u.`user_id` 
								FROM `tbluser_instruments` u 
								JOIN `tblinstrument_master` m  
								WHERE u.`instrument_id`=m.`instrument_id` 
								AND  u.`user_id` IN (".$userId.")";
		$result			=	$this->getdbcontents_sql($sql, false);
		$instruments = array();
		foreach($result as $instrument){
			$instruments[$instrument['user_id']] [] = $instrument['instrument_image'];
		}
		return $instruments;
	}
	
	function getAllInstrumentsofInstr($id){
		
		$sql			=	"SELECT m.instrument_id, m.name, m.instrument_image, u.`user_id`
								FROM `tbluser_instruments` u
								JOIN `tblinstrument_master` m
								WHERE u.`instrument_id`=m.`instrument_id`
								AND  u.`user_id` = ".$id." AND u.is_deleted = 0";
		$result			=	$this->getdbcontents_sql($sql);
		return $result;
	}
	
	function getUserSettings($userId)
	{
		$query			=	"SELECT s.detail_id,s.settings_name,u.is_deleted FROM tbluser_settings AS u LEFT JOIN tblsettings_detail AS s ON u.setting_detail_id = s.detail_id  WHERE u.user_id=$userId AND s.is_deleted=0 ";
			
		$result		=	$this->getdbcontents_sql($query,0);
		return $result;
	}
	function getNewUserSettings()
	{
		$query			=	"SELECT detail_id,settings_name FROM tblsettings_detail WHERE is_deleted='0'";
		$result		=	$this->getdbcontents_sql($query,0);
		return $result;
	}
	function getAllTplAdmin()
	{   $sql   = "SELECT u.login_id FROM  tbluser_login as l 
					LEFT JOIN tblusers as u 
						on l.login_id=u.login_id 
					LEFT JOIN  `tbluser_roles` as g 
						on l.`user_role`=g.`role_id` 
					WHERE 1 AND u.is_deleted=0 
					AND l.is_deleted=0 
					AND g.role_id=2";
		//$sql	=	"SELECT user_id from tbladmin_mail_template  commented by bhaskar on 12 Aug 2013
		//WHERE tpl_id=$tplId AND is_deleted=0";
		$arry	=	$this->getdbcontents_sql($sql,0);
		 foreach($arry as $key=>$value){
			$admin		.=	$value["login_id"]; //user_id
			$admin		.=	",";
		} 
		return trim($admin,",");

	}
	function getAllAdmin()
	{
		$query	=	"SELECT U.user_id, L.user_name FROM tbluser_login AS L
		INNER JOIN tblusers AS U ON L.login_id=U.login_id
		LEFT JOIN tbluser_roles AS UR ON UR.role_id=L.user_role
		WHERE UR.role_id=2";
		$admin =$this->getdbcontents_sql($query,0);
		return $admin;
	}

	function getAdminTplSettings($userId)
	{
		/*$query	=	"SELECT MT.tpl_id,CS.section,C.title,MT.is_deleted
		 FROM tbladmin_mail_template AS MT
		LEFT JOIN tblcms AS C ON C.id = MT.tpl_id
		LEFT JOIN tblcms_section AS CS ON CS.id=C.section_id
		WHERE MT.user_id=$userId AND C.status=1 ORDER BY C.section_id ASC";*/
		$query	=	"SELECT C.id as tpl_id, CS.section, C.title, MT.is_deleted
		FROM tblcms AS C
		LEFT JOIN tblcms_section AS CS ON CS.id=C.section_id
		LEFT JOIN tbladmin_mail_template AS MT ON C.id = MT.tpl_id AND MT.user_id= $userId
		WHERE C.status=1 AND C.section_id IN (".LMT_MAIL_CS.",".LMT_MAIL_SN.",".LMT_MAIL_CC.",".LMT_MAIL_REG.",".LMT_MAIL_FS.") ORDER BY C.section_id ASC";
		$result		=	$this->getdbcontents_sql($query,0);
		return $result;
	}
	function getUserSettingsName($userId,$type)
	{
		$query			=	"SELECT s.constant FROM tbluser_settings AS u LEFT JOIN tblsettings_detail AS s ON u.setting_detail_id = s.detail_id WHERE u.user_id=$userId AND s.constant='$type' AND u.is_deleted = 0";
		$result		    =	end($this->getdbcontents_sql($query,0));
		return $result;
	}
	function getUserVideoSettings($userId)
	{
		$query			=	"SELECT * FROM tblusers_video_settings WHERE user_id=$userId";
		$result		=	end($this->getdbcontents_sql($query,0));
		return $result;
	}
	function getUserName($id)
	{
		$query			=	"SELECT l.user_name, CONCAT(u.first_name,' ',u.last_name) AS name FROM tbluser_login AS l LEFT JOIN tblusers AS u ON u.login_id = l.login_id WHERE l.login_id=".$id;
		$result		    =	end($this->getdbcontents_sql($query,0));
		return $result;
	}
	function getTutormailaddress($id)
	{
		$query 			=	"SELECT l.user_name FROM  tbluser_login AS l 
								LEFT JOIN tblusers AS u 
								ON l.login_id = u.login_id 
								WHERE u.user_id = $id";
		$result			=	end($this->getdbcontents_sql($query,0));
		return $result['user_name'];
	}
		
	function addNewBug($data)
	{
		$this->dbStartTrans();
		$this->id =	$this->db_insert('tblbug_report',$data, 0);
		if(!$this->id)
		{
			$this->dbRollBack();
			$this->setPageError($this->getDbErrors());
			return false;
		}
		else
			return $this->id;
	}
		
	// following codded on ver2
	function getMyAllCourses($userId){
		$query		=	"SELECT c.* FROM `tblcourses` c  WHERE  c.`course_status_id` !=".LMT_COURSE_STATUS_CANCELLED." AND c.`instructor_id`=".$userId;
		$rec		=	$this->getdbcontents_sql($query);
		return $rec;
	}

	function getCourseTutorsSql($instrumentId, $country = "", $insName = "")
	{
		$condition = !empty($instrumentId) ? " AND C.instrument_id = $instrumentId" : "";
		$condition .= !empty($country) ? " AND U.country_id = $country" : "";
		$condition .= !empty($insName) ? " AND CONCAT(U.first_name, ' ', last_name) LIKE '%$insName%'" : "";
			
		$sql = "SELECT U.user_id, U.first_name, U.last_name, U.profile_image, CN.country_name, COUNT(C.course_id)
		AS course_count
		FROM tblusers AS  U
		LEFT JOIN tblcountries AS CN ON U.country_id = CN.country_id
		LEFT JOIN tbluser_login AS UL ON U.login_id = UL.login_id
		LEFT JOIN tblcourses AS C ON U.user_id = C.instructor_id
		WHERE U.is_deleted =0 AND UL.is_deleted=0 AND UL.user_role=".LMT_INS_ROLE_ID." AND (course_status_id =".LMT_COURSE_STATUS_OPEN." OR course_status_id =".LMT_COURSE_STATUS_FULL.") AND CONCAT(C.start_date, ' ' ,C.start_time) > '".date('Y-m-d H:i:s')."' $condition
		GROUP BY U.user_id
		HAVING COUNT(C.course_id) > 0";
		return $sql;
	}
	function getUserId($userCode)
	{
		$sql = "SELECT user_id FROM tblusers WHERE user_code = '$userCode'";
		$resultArry		=	reset($this->getdbcontents_sql($sql, false));
		return $resultArry['user_id'];
	}
	function getUserCode($Id)
	{
		$sql = "SELECT user_code FROM tblusers WHERE user_id = $Id";
		$resultArry		=	reset($this->getdbcontents_sql($sql, false));
		return $resultArry['user_code'];
	}
	function getInstructors()
	{
		$query	=	"SELECT CONCAT(\"'\",u.`first_name`,' ',u.`last_name`,\"'\",',') as name  FROM tblusers u
		JOIN `tbluser_login` l WHERE l.`login_id`=u.`login_id` and l.`user_role`=".LMT_INS_ROLE_ID;
		$tut	=	$this->getdbcontents_sql($query,0);
			
		foreach($tut as $key=>$value){
			$tutor		.=	$value["name"];
		}
		return trim($tutor,",");
	}

	function addNewCC($ccData)
	{
		if($this->db_insert('tblusers_ccs', $ccData))
			return true;
		else
		{
			$this->dbRollBack();
			$this->setPageError($this->getDbErrors());
			return false;
		}
	}
	// end ver 2
	function getUserDetail($userid)
	{	//edit by milan
		$sql	=	'SELECT UL.login_id ,U.age_group,U.first_name,U.last_name,U.country_id,U.state_id,U.time_zone_id,U.dob,U.gender,UL.user_name AS username FROM tblusers AS U
		LEFT JOIN tbluser_login AS UL ON U.login_id=UL.login_id
		WHERE U.user_id='.$userid;
			
		$resultArray	=	end($this->getdbcontents_sql($sql));
		//print_r($resultArray);die();
		// user's instrument list
		$instSql	='SELECT instrument_id FROM tbluser_instruments WHERE user_id='.$userid.' AND is_deleted=0';
		$instArr	=	$this->getdbcontents_sql($instSql);
		$str	= "";
		foreach($instArr as $data)
			$str	.=	$data['instrument_id'].",";
			
		$resultArray['instruments']	=	explode(",",trim($str,","));
		//print_r($resultArray);die();
			
		return $resultArray;
	}
	function getFeaturedItems($userId)
	{
		$sql		=	"SELECT id,type,path,priority FROM tblfeatured_items WHERE status = 1 AND user_id = $userId ORDER BY priority";
		$result		=   $this->getdbcontents_sql($sql);
		return	$result;
	}
	function deleteFeaturedItem($id)
	{
		$sql		=	"SELECT type,path FROM tblfeatured_items WHERE id = $id";
		$data		=	end($this->getdbcontents_sql($sql));
		if($data['type'] == 1)
		{
			@unlink("Uploads/Featured/".$data['path']);
			@unlink("Uploads/Featured/thumbs/".$data['path']);
		}
		$sql		=	"DELETE FROM tblfeatured_items WHERE id=$id";
		$result		=   $this->db_query($sql);
		return	$result;
	}


	function getInstructorsList($search)
	{
		//select using instrument name
		$sql1 = "select instrument_id from tblinstrument_master WHERE name LIKE '%$search%'"; //get instrument id from instrument name
		$sql2 = "select DISTINCT user_id from tbluser_instruments where instrument_id IN ($sql1)"; //get user_id from instrument id;
		$sql3 = "select user_id,first_name, last_name, profile_image, about_me from tblusers where user_id IN ($sql2) AND age_group=0";
		//echo $sql3;
		//$sql		=	"SELECT type,path FROM tblfeatured_items WHERE id = $id";
		$data		=	$this->getdbcontents_sql($sql3);
		return	$data;

	}

	function getCourse($user_id, $id_instrument ,$list=false, $serverOffset = '+00:00', $studentOffset = '+00:00', $dtFmt = '%b %d %Y', $tmFmt = '%h:%i %p'	)
	{  	//edited by @xlinkerz 
		
		
		$sql = "SELECT name, tc.course_id, course_code, title, tc.description,
			    DATE_FORMAT(CONVERT_TZ(CONCAT(tc.start_date, ' ' ,tc.start_time), '$serverOffset', '$studentOffset'), '$dtFmt') AS start_date,
			    DATE_FORMAT(CONVERT_TZ(CONCAT(tc.start_date, ' ' ,tc.start_time), '$serverOffset', '$studentOffset'),	'$tmFmt') AS start_time, 
			    DATE_FORMAT(CONVERT_TZ(CONCAT(CURDATE(), ' ' , CURTIME()), '$serverOffset', '$studentOffset'),	'$tmFmt') AS today_time,
				DATE_FORMAT(CONVERT_TZ(CONCAT(CURDATE(), ' ' , CURTIME()), '$serverOffset', '$studentOffset'),	'$dtFmt') AS today_date,
			    tcd.time, tp.cost
				FROM tblcourses as tc
				LEFT JOIN tblinstrument_master as ti 
					ON ti.instrument_id = tc.instrument_id 
				LEFT JOIN tbllookup_course_duration as tcd
					ON tcd.id =tc.duration 
				LEFT JOIN tblcourse_prices as tp
					ON tp.id = tc.price_code
				where instructor_id = $user_id
				AND ti.instrument_id = $id_instrument
				AND concat(`start_date`,0x20,`start_time`)>=NOW() 
				GROUP BY tc.course_id
				 ORDER BY tc.start_date ASC, tc.start_time ASC, tc.created_on ASC, tc.course_status_id ASC";
		  //remedy for request from a loop and without a loop
		  if($list==1){
					$data = $this->getdbcontents_sql($sql);

					return $data;  
		  }else{
					$result['paging']['spage']		=	$this->create_paging("n_page",$sql,5);
					$result['instructors']	=	$this->getdbcontents_sql($result['paging']['spage']->finalSql(),0);
					if (!empty ($_GET['n_page'])) {
						$page_get_code = $_GET['n_page'];
						$page_get_info = explode ('-', $page_get_code);
						$page_get_number = $page_get_info [0];
						$page_get_num_items = $page_get_info [1];
						$page_item_offset = (($page_get_number - 1) * $page_get_num_items) + 1;
					} else {
						$page_get_number = 1;
						$page_get_num_items = 5;
						$page_item_offset = 1;
					}
					$result ['page_number'] = $page_get_number;
					$result ['page_num_items'] = $page_get_num_items;
					$result ['page_item_offset'] = $page_item_offset;
					return $result;
		  }
	}
	
	function getInstRatings($id) {
		$sql	=	"SELECT ROUND(AVG(R.rating)) AS inst_rating
				from tblusers as cu
				LEFT JOIN tblcourses AS C
				on C.instructor_id=cu.user_id
				LEFT JOIN tblcourse_ratings AS R
				ON R.course_id=C.course_id
				where R.is_deleted=0 AND user_id= $id ";
		
		$rating	=	$this->getdbcontents_sql($sql);
		return $rating;
	}
	
	function getCountries(){
		$sql= "SELECT * FROM `tblcountries` WHERE 1 ORDER BY `dsp_order`, `country_name`";
		$result = $this->getdbcontents_sql($sql);
		return $result;
	}
	//added by milanmilan
	function allInstructorWithInstrumentId($instrumentId, $flagId= '', $country='')
	{
		$sql="SELECT user_id FROM tbluser_instruments WHERE is_deleted  = 0 AND instrument_id=".$instrumentId;
		$searched_instructor_id_instrument = $this->getdbcontents_sql($sql);
		$i=0;

		foreach($searched_instructor_id_instrument as $key=>$val)
		{
			foreach($val as $k=>$v)
			{
				$searched_instructor_id_instrument1[++$i]=$v;
			}
		}

		if(!empty($flagId))
		{	
			$count = count($flagId);
			if($count > 0){
				$flagId = implode(", ", $flagId);
			}else {
				$flagId = implode("", $flagId);
			}
			
			$searched_instructor_id_flag	=	array();
			$search_flag_array	=	$flagId; 
			
			$sql="SELECT instructor_id FROM  tbl_pmm_instructor_lookup WHERE assoc_flag_id IN (".$search_flag_array.")";
			
			$result = $this->getdbcontents_sql($sql);
			
			foreach($result as $row) { $searched_instructor_id_flag[]=$row['instructor_id']; }
			
			$final_searched_instructor_id = array_intersect($searched_instructor_id_flag, $searched_instructor_id_instrument1);

		}elseif(!empty ($country)){
			$sql1 = "SELECT `user_id` from tblusers WHERE `country_id`=".$country;
			$result1 = $this->getdbcontents_sql($sql1);
			
			foreach($result1 as $row1) { $searched_instructor_id_flag1[]=$row1['user_id']; }
			
			$final_searched_instructor_id = array_intersect($searched_instructor_id_flag1, $searched_instructor_id_instrument1);

		}elseif(!empty($country) && !empty($flagId)){
			$count = count($flagId);
			if($count > 0){
				$flagId = implode(",", $flagId);
			}else {
				$flagId = implode("", $flagId);
			}
				
			$searched_instructor_id_flag	=	array();
			$search_flag_array	=	$flagId;
				
			$sql="SELECT instructor_id FROM  tbl_pmm_instructor_lookup WHERE assoc_flag_id IN (".$search_flag_array .")";
			$result = $this->getdbcontents_sql($sql);
			
				
			foreach($result as $row) { $searched_instructor_id_flag[]=$row['instructor_id']; }
			
			
			
			$sql1 = "SELECT `user_id` from tblusers WHERE `country_id`=".$country;
			$result1 = $this->getdbcontents_sql($sql1);
				
			foreach($result1 as $row1) { $searched_instructor_id_flag1[]=$row1['user_id']; }
			
			$final_ids = array_intersect($searched_instructor_id_flag1, $searched_instructor_id_flag1);
			
			$final_searched_instructor_id = array_intersect($final_ids, $searched_instructor_id_instrument1);
			
		}
		else
		{
			$final_searched_instructor_id=$searched_instructor_id_instrument1;
		}
		
		$sql					=  "SELECT cu.user_id from tblusers as cu
															LEFT JOIN tbluser_login as ul 
																on cu.login_id=ul.login_id
															LEFT JOIN tbluser_roles as ct 
																on ul.user_role = ct.role_id
															Left Join tbllookup_instructor_level as lo on cu.`instructor_level`=lo.`id`
															where ct.role_access_key='TUTOR_ROLE'";
		$res 	= 	$this->getdbcontents_sql($sql);
		foreach ($res as $key => $val){
			$res1[$key]= $val['user_id'];
		}
		$final_searched_instructor_id_final = array_intersect($final_searched_instructor_id, $res1);
		//echo "<pre>"; print_r($final_searched_instructor_id); die; echo "<pre>"; print_r($res1); die;
		return $final_searched_instructor_id_final;


	}
//modification @xlinkerz
public function getUserDetailInIds($idarray, $id_instrument)
	{	
		if( !is_array($idarray) ) {
			$ids = $idarray; 
		}else { 
			$ids=implode(',',$idarray); 
		}
	
	 	$sql="SELECT tb.user_id,first_name, last_name, profile_image, about_me, tg.user_name , sm.facebook , twitter , youtube_channel, linkedin, url
				from tblusers as tb
				LEFT JOIN tbluser_login as tg
					on tg.login_id = tb.login_id
				LEFT JOIN tbl_social_media_links as sm
					ON sm.id = tb.social_media_links
				where tb.user_id IN ($ids) 
				AND age_group=0
					ORDER BY instructor_level DESC"; 
						
		$result['paging']['spage']		=	$this->create_paging("n_page",$sql,5);
        

        if( !empty($_GET['id']) ){
        	$result1['instructors']	= $this->getdbcontents_sql($sql);; 
        	
        } else {
        	$result1['instructors']	=	$this->getdbcontents_sql($result['paging']['spage']->finalSql(),0);
        	
        }
                      
		//echo "<pre>"; print_r($result1); echo "</pre>"; die;
        if(empty($result1['instructors'])) { $result = '';}
		
			$i = 0;
			foreach ($result1['instructors'] as $d) {
				$result['inst_data'][$i] = $d;
				
				if (!empty($result['inst_data'][$i][about_me])) {
					$result['inst_data'][$i][about_me] = substr($result['inst_data'][$i][about_me], 0, 150).'...';
				}
				$Rating = $this->getInstRatings($result['inst_data'][$i][user_id]); 
				$result['inst_data'][$i]['auth_instr'] = $this->getAllInstrumentsofInstr($result['inst_data'][$i][user_id]);
				$flag_id = $this->get_associated_flag_ids($result['inst_data'][$i][user_id]);
				$result['inst_data'][$i]['assoc_flags'] = $this->get_assoc_flag_names($flag_id);
				$rating_var = '';
				if ( (int)$Rating[0][inst_rating] == '1') {
					$rating_var ="<span><li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li></span> ";//Very poor 
				}
				elseif ( (int)$Rating[0][inst_rating] == '2') {
					$rating_var = "<span><li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_inactive.gif' /></li>
					<li><img src='images/star_inactive.gif' /></li>
					<li><img src='images/star_inactive.gif' /></li></span> ";//Not that bad
				}elseif ((int)$Rating[0][inst_rating] == '3' ) {
					$rating_var="<li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_inactive.gif' /></li>
					<li><img src='images/star_inactive.gif' /></li> ";//Average
				}elseif((int)$Rating[0][inst_rating] == '4' ) {
					$rating_var = "<span><li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_inactive.gif' /></li>
					</span>";//Good
				}elseif ((int)$Rating[0][inst_rating] == '5' ) {
					$rating_var = "<li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_active.gif' /></li>
					<li><img src='images/star_active.gif' /></li>";// Perfect
				}else {
					$rating_var = "
					<li><img src='images/star_inactive.gif' /></li>
					<li><img src='images/star_inactive.gif' /></li>
					<li><img src='images/star_inactive.gif' /></li>
					<li><img src='images/star_inactive.gif' /></li>
					<li><img src='images/star_inactive.gif' /></li>
					";//not rated yet!
				}
				
				$result['inst_data'][$i][ratings] = $rating_var;
				$courseDetail = $this->getCourse($result['inst_data'][$i][user_id], $id_instrument, 1);
				
				if (!empty($courseDetail)) {
					$result['inst_data'][$i]['image']="green_marble_30.png";
					$result['inst_data'][$i]['has_courses'] = 1;
				}else {
					$result['inst_data'][$i]['image']="blue_marble_30.png";
					$result['inst_data'][$i]['has_courses'] = 1;
				}
				$result['inst_data'][$i]['instrument_id'] = $id_instrument;
				$i++; 
			} 
			//sorting array with course availibilty
// 			 usort($result['inst_data'], function($a, $b) {        
// 				 return strlen($b['image']) - strlen($a['image']);
// 						});
			
			if(empty($result['inst_data'])){
					$result =  NULL;
			}
 		return	$result;
 		
	}
	
	public function getInstructorsListForInstrument ($instrument_id, $flag_ids = '', $country_id ='') {

		if ($instrument_id > 0) {

			if (!empty ($flag_ids)) {
				$flag_list = implode (", ", $flag_ids);
				$flag_var = ", IFNULL(fs.has_flags, 0) AS has_flags";
				$flag_join = " LEFT JOIN (SELECT f.instructor_id, COUNT(f.instructor_id) AS has_flags 
									FROM tbl_pmm_instructor_lookup AS f
									WHERE f.assoc_flag_id IN ($flag_list)
									GROUP BY f.instructor_id) AS fs
								ON u.user_id = fs.instructor_id ";
				$flag_where = " AND has_flags > 0 ";
				$flag_order_by = " has_flags DESC, ";
			} else {
				$flag_list = NULL;
				$flag_var = NULL;
				$flag_join = NULL;
				$flag_where = NULL;
				$flag_order_by = NULL;
			}

			$sql = "SELECT u.user_id, u.first_name, u.last_name, u.profile_image, u.modified,
							LEFT(u.about_me, 150) AS about_me, tg.user_name,
						IFNULL(t.has_courses, 0) AS has_courses, IFNULL(r.inst_rating, 0) AS inst_rating,
							sm.facebook, sm.twitter, sm.youtube_channel, sm.linkedin, sm.url
							$flag_var
					FROM tbluser_instruments AS ui, tblusers AS u
					LEFT JOIN tbluser_login as tg ON
						tg.login_id = u.login_id
					LEFT JOIN (SELECT c.instructor_id, count(c.instructor_id) AS has_courses 
						FROM tblcourses AS c
						WHERE c.instrument_id = $instrument_id
							AND concat(c.start_date, ' ', c.start_time) > NOW() 
							AND c.start_date < NOW() + interval 1 month
						GROUP BY c.instructor_id) AS t 
						ON u.user_id = t.instructor_id
					LEFT JOIN (SELECT c.instructor_id, ROUND(AVG(cr.rating)) AS inst_rating 
						FROM tblcourse_ratings AS cr, tblcourses AS c
						WHERE c.course_id = cr.course_id
						GROUP BY c.instructor_id) AS r
						ON u.user_id = r.instructor_id
					LEFT JOIN tbl_social_media_links AS sm
						ON sm.id = u.social_media_links
						$flag_join
					WHERE ui.instrument_id = $instrument_id
						AND ui.is_deleted = 0
						AND ui.user_id = u.user_id
						AND u.instructor_level > 0
						AND u.is_deleted = 0
						$flag_where";

			if (!empty ($country_id)) {
				$sql .= " AND u.country_id = $country_id ";
			}

			$sql .= " ORDER BY has_courses DESC, $flag_order_by u.profile_image, inst_rating, u.modified, u.first_name, u.last_name";

//echo "<pre>";
//var_dump ($sql);
//echo "</pre>";

			$result['paging']['spage']		=	$this->create_paging("n_page", $sql, 5);
        

	        if( !empty($_GET['id']) ){
	        	$sql_result	= $this->getdbcontents_sql($sql);; 
	        	
	        } else {
	        	$sql_result	=	$this->getdbcontents_sql($result['paging']['spage']->finalSql(),0);
	        }
        
        	if (empty($sql_result)) { 
        		$result = NULL;
        	} else {

   				$i = 0;
				$final_inst_list = array ();
				foreach ($sql_result as $cur_inst) {
					$final_inst_list[$i] = $cur_inst;

					if (!empty($cur_inst[about_me])) {
						$final_inst_list[$i][about_me] = $cur_inst[about_me] . '...';
					}
					$final_inst_list[$i]['auth_instr'] = $this->getAllInstrumentsofInstr($cur_inst[user_id]);
					$flag_id = $this->get_associated_flag_ids($cur_inst[user_id]);
					$final_inst_list[$i]['assoc_flags'] = $this->get_assoc_flag_names($flag_id);

					$rating_var = '';
					if ( (int)$cur_inst[inst_rating] == '1') {
							$rating_var ="<span><li><img src='images/star_active.gif' /></li>
								<li><img src='images/star_inactive.gif' /></li>
								<li><img src='images/star_inactive.gif' /></li>
								<li><img src='images/star_inactive.gif' /></li>
								<li><img src='images/star_inactive.gif' /></li></span> ";//Very poor 
					} elseif ( (int)$cur_inst[inst_rating] == '2') {
						$rating_var = "<span><li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li></span> ";//Not that bad
					} elseif ((int)$cur_inst[inst_rating] == '3' ) {
						$rating_var="<li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li> ";//Average
					} elseif((int)$cur_inst[inst_rating] == '4' ) {
						$rating_var = "<span><li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li>
						</span>";//Good
					} elseif ((int)$cur_inst[inst_rating] == '5' ) {
						$rating_var = "<li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_active.gif' /></li>
						<li><img src='images/star_active.gif' /></li>";// Perfect
					} else {
						$rating_var = "
						<li><img src='images/star_inactive.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li>
						<li><img src='images/star_inactive.gif' /></li>
						";//not rated yet!
					}
					$final_inst_list[$i][ratings] = $rating_var;
					
					if ($cur_inst[has_courses] > 0) {
						$final_inst_list[$i]['image']="green_marble_30.png";
						$final_inst_list[$i]['image_text'] = "Courses Available";
					}else {
						$final_inst_list[$i]['image']="blue_marble_30.png";
						$final_inst_list[$i]['image_text'] = "Contact For Courses";
					}
					$final_inst_list[$i]['instrument_id'] = $instrument_id;
					$i++; 
				}
				$result ['inst_data'] = $final_inst_list;
			} 	        
		} else {
			$result = NULL;
		}
		return $result;
	}
	
	//@author @modifications by @xlinkerz
	public function getUserAndCourseDetailInIds($idarray)
	{  
		$unique = array_unique($idarray);
		$ids =join(',',$unique);
		$sql="SELECT DISTINCT tu.profile_image,  tu.first_name, tu.last_name, tu.about_me, tc.instructor_id, tc.course_status_id
		from tblusers as tu
		RIGHT JOIN tblcourses as tc 
		ON tu.user_id = tc.instructor_id
		where tu.user_id IN ($ids) 
			AND age_group=0
			ORDER BY  tu.instructor_level ASC ";
		$data	=	$this->getdbcontents_sql($sql);
		$insDetails = array();
		foreach ($data as  $d) {
			$k = $d['instructor_id'];
			if( !isset( $insDetails[$k] )) {
				$insDetails[$k] = $d;
			}
			$insDetails[$k]['courses'][] = $d['course_status_id'];
		}
		$i=0;
		foreach($insDetails as $detail)
		{
			$result[$i]= $detail;
			$cCheck =$detail['courses'];
			$k = count($cCheck)-1;
			$courseDetail = $this->getCourse($result[$i][instructor_id]);
			if (!empty($courseDetail)) {
				$result[$i]['image']="green_marble_30.png";
			}else {
				$result[$i]['image']="blue_marble_30.png";
			}
			unset($result[$i]['courses']);
			unset($result[$i]['course_status_id']);
			$i++;
		}
		return	$result;
	}

	//added by Bhaskar for instructor flag relation
	public function insertUserDetails_flags($data)
	{
		if(is_array($data['assoc_flag_id'])) {
			foreach( $data['assoc_flag_id'] as $flag ) {
				$data['instructor_id']	= $data['instructor_id'];
				$data['assoc_flag_id'] = $flag;
				$this->db_insert("tbl_pmm_instructor_lookup",$data,0);
			}
			
		}else {
			$this->db_insert("tbl_pmm_instructor_lookup",$data,0);
		}
	}

	public function get_associated_flag_ids($id)
	{
		$sql="SELECT assoc_flag_id FROM tbl_pmm_instructor_lookup WHERE instructor_id =". $id."";
		$result=$this->getdbcontents_sql($sql, false);
		$result1=array(); $i=0;
		foreach($result as $row)
		{	if($row['assoc_flag_id']!="")
			{
			 $result1[$i]=$row['assoc_flag_id'];
			 $i++;	
			}
		}
		
		return $result1;
		
	}
	//added by @xlinkerz end
	
	public function get_assoc_flag_names( $flagIds, $pId= NULL) {
		if ( is_array($flagIds) )	{ $flagIds = implode(',', $flagIds); } 
		$args = NULL; $needle = ', ';
		if( !empty($pId)) { $args = " and pid = ".$pId; $needle="<br/>"; }
		$sql = "SELECT name
					FROM `tbl_flag`
					WHERE id
					IN ( ".$flagIds.")".$args;
		$result = $this->getdbcontents_sql($sql);
		
		$result = array_map(function($a){return $a['name'];}, $result);
		if ( count($result) > 1 ) { $result = implode($needle, $result); }else { $result = implode('', $result); }
		return $result;
		
	}

}
?>
