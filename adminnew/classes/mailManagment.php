<?php
/**************************************************************************************
Created By 	:ARVIND SOMU	
Created On	:05-07-2011
Purpose		:User Management
**************************************************************************************/
class mailManagment extends siteclass
	{
		function getUserId($emaiId){
			$query			=	"SELECT u.`user_id` FROM `tblusers` u   JOIN `tbluser_login` l WHERE l.`user_name`='".$emaiId."' AND l.`login_id`=u.`login_id`";
			$resultArry		=	$this->getdbcontents_sql($query, 0);		
			return $resultArry[0]["user_id"];
		}
		
		// to insert mail 
		
		function insertMail($values,$attachment,$save,$parent="0",$thread="0"){
			$att		=	0;
			if($attachment>0){
				$att		=	1;
			}
			$userId			=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];
			$id				=	$this->db_insert("tblmail_content",array("subject"=>$values["txtSubject"],"message"=>$values["txtMessage"],"from_id"=>$userId,"is_attached"=>$att,"is_saved"=>$save,"parent_id"=>$parent,"thread_id"=>$thread,"created_date"=>date("Y-m-d H:i:s")),"0");
			return $id;
		}

		function updateMail($values,$attachment,$save){
			//$att		=	0;
			if($attachment)
				$dataIns["is_attached"]	=	1;		
				
			$dataIns["from_id"]	 		=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];
			$dataIns["subject"]	 		=	$values["txtSubject"];
			$dataIns["message"]	 		=	$values["txtMessage"];
			$dataIns["is_saved"] 		=   $save; 
			$dataIns["created_date"]	=	date("Y-m-d H:i:s");
					
			$id				=	$this->db_update("tblmail_content",$dataIns,"mail_id=".$values['mail_id']);
			return $id;
		}
		
		// to insert mail send 
		
		function sendMail($maiId,$toId,$mode,$parent="0",$thread="0"){			
			//Send from other user profile.
/*			if(isset($_SESSION['visitedProfile']) && !empty($_SESSION['visitedProfile']))
				$toId = $_SESSION['visitedProfile'];
*/				
			$id	 =	$this->db_insert("tblmail_to",array("mail_id"=>$maiId,"to_id"=>$toId,"created_date"=>date("Y-m-d H:i:s"),"thread_id"=>$thread,"parent_id"=>$parent),"0");
			return $id;
		}
		
		// for function insrting  bcc
		
		function sendMailBcc($maiId,$toId){
				$id				=	$this->db_insert("tblmail_to",array("mail_id"=>$maiId,"to_id"=>$toId),"0");
				return $id;
		}
		
		//----------------------------- for inbox section -------------------------------//
		
		function getInbox($id,$serverOffset = '+00:00', $userOffset = '+00:00', $dtFmt = '%b %d %Y %h:%i %p'){
		

			$query				=	"SELECT thread_id FROM `tblmail_to` WHERE to_id = $id AND is_deleted = 0 GROUP BY thread_id  ORDER BY created_date DESC";

			$spage				=	$this->create_paging("n_page",$query,$per_page=10);
			
			$resultArry	=	array();
			$resultArry[1]		=	$spage;				
			$resultArry[0]		=	$this->getdbcontents_sql($spage->finalSql(),0);
			foreach($resultArry[0] as $key=>$val)
				{	
				    $sql =	"SELECT ST.*,C.*,T.status,T.mail_to_id,U.`first_name`,U.`last_name`,U.`profile_image`,T.created_date,
							 DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN tblmail_to  T ON T.mail_id = C.mail_id LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id` LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = C.`mail_id` WHERE T.thread_id=".$val['thread_id']."  AND C.is_saved = 0 AND T.to_id = $id AND T.`is_deleted` = 0 ORDER BY C.created_date DESC LIMIT 0,1";
				    $dataIn	=	end($this->getdbcontentshtml_sql($sql,0));
					//print_r($dataIn);
					$sql =	"SELECT  ST.*,C.*, U.`first_name`,U.`last_name`,U.`profile_image`,C.created_date,
							 DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id` LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = C.`mail_id` WHERE C.thread_id=".$val['thread_id']." AND C.is_saved = 0 AND ST.is_deleted =0 AND C.`from_id` = $id ORDER BY C.created_date DESC LIMIT 0,1";
				    $dataOut	=	end($this->getdbcontentshtml_sql($sql,0));
					//print_r($dataOut);
					
					if($dataOut['created_date'] > $dataIn['created_date'])
						$data[] = $dataOut;
					else if($dataIn['created_date'] > $dataOut['created_date'])
						$data[]	= $dataIn;
					else if($dataIn['created_date'] == $dataOut['created_date'] && (!empty($dataIn) || !empty($dataOut)))
						$data[]	= $dataIn;
				}
				
				if($data){
					$spage->endingRow = count($data);
				
					usort($data, function($a, $b) {
					if ($a['created_date']==$b['created_date']) return 0;
					return ($a['created_date']<$b['created_date'])?1:-1;;
					});
					foreach($data as $key=>$val)
						{
							$toCodes	=	explode(",",$val['to_users']);
							$toCodes	=	implode("','",$toCodes);
							$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$toCodes."')";	
							$toUsers	=	$this->getdbcontents_sql($sql,0);
							$newtoUsers	=	array();
							foreach($toUsers as $key1=>$valto){
							$newtoUsers[] =	$valto['name'];
							}
							$toUsers	=	implode(',',$newtoUsers);
							if($toUsers)
							$data[$key]['toUsers']	=	$toUsers;



							$ccCodes	=	explode(",",$val['to_users_ccc']);
							$ccCodes	=	implode("','",$ccCodes);
							$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$ccCodes."')";	
							$ccCodes	=	$this->getdbcontents_sql($sql,0);
							$newccUsers	=	array();
							foreach($ccCodes as $key2=>$valcc){
							$newccUsers[] =	$valcc['name'];
							}
							$ccUsers	=	implode(',',$newccUsers);
							if($ccUsers)
							$data[$key]['ccUsers']	=	$ccUsers;


							$bccCodes	=	explode(",",$val['to_users_bcc']);
							$bccCodes	=	implode("','",$bccCodes);
							$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$bccCodes."')";	
							$bccCodes	=	$this->getdbcontents_sql($sql,0);
							$newbccUsers	=	array();
							foreach($bccCodes as $key3=>$valbcc){
							$newbccUsers[] =	$valbcc['name'];
							}
							$bccUsers	=	implode(',',$newbccUsers);
							if($bccUsers)
							$data[$key]['bccUsers']	=	$bccUsers;

						}
				 
				 }

			$resultArry[0]	=	$data;
			return $resultArry;		
		}
		
		// for viewing mail 
		
		function getInboxView($thread_id,$userId,$serverOffset = '+00:00', $userOffset = '+00:00', $dtFmt = '%b %d %Y %h:%i %p'){
		

			$sql	=	"SELECT  ST.*,C.*,T.mail_to_id,U.`first_name`,U.`last_name`,U.`profile_image`,L.`user_name`,T.created_date,DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN tblmail_to  T ON T.mail_id = C.mail_id LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id` LEFT JOIN tbluser_login L ON L.`login_id` = U.`login_id`  LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = T.`mail_id` WHERE T.thread_id=".$thread_id." AND C.is_saved = 0 AND T.to_id = $userId AND T.`is_deleted` = 0 ORDER BY C.created_date DESC";
			
			$dataIn	=	$this->getdbcontentshtml_sql($sql,0);
			$sql	=	"SELECT  ST.*,C.*, U.`first_name`,U.`last_name`,U.`profile_image`,L.`user_name`,C.created_date,DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id` LEFT JOIN tbluser_login L ON L.`login_id` = U.`login_id` LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = C.`mail_id` WHERE C.thread_id=".$thread_id."  AND C.is_saved = 0  AND ST.is_deleted =0 AND C.`from_id` = $userId ORDER BY C.created_date DESC";
			
			$dataOut =	$this->getdbcontentshtml_sql($sql,0);

			$data	 =	array_merge($dataIn,$dataOut);
			
			usort($data, function($a, $b) {
			if ($a['created_date']==$b['created_date']) return 0;
			return ($a['created_date']<$b['created_date'])?1:-1;;
			});

			$frnds	=	new friendsManagement();
			
			foreach ($data as $key=>$val)
				{
					if($val['is_attached'] == 1){
						$query		=	"SELECT * FROM `tblmail_attachments` WHERE `mail_id`=".$val["mail_id"];
						$rec		=	$this->getdbcontents_sql($query, 0);
						$data[$key]["attached"]=	$rec;	
					}
				
						$toCodes	=	explode(",",$val['to_users']);
						$toCodes	=	implode("','",$toCodes);
						$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$toCodes."')";	
						$toUsers	=	$this->getdbcontents_sql($sql,0);
						$newtoUsers	=	array();
						foreach($toUsers as $key1=>$valto){
						$newtoUsers[] =	$valto['name'];
						}
						$toUsers	=	implode(',',$newtoUsers);
						if($toUsers)
						$data[$key]['toUsers']	=	$toUsers;



						$ccCodes	=	explode(",",$val['to_users_ccc']);
						$ccCodes	=	implode("','",$ccCodes);
						$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$ccCodes."')";	
						$ccCodes	=	$this->getdbcontents_sql($sql,0);
						$newccUsers	=	array();
						foreach($ccCodes as $key2=>$valcc){
						$newccUsers[] =	$valcc['name'];
						}
						$ccUsers	=	implode(',',$newccUsers);
						if($ccUsers)
						$data[$key]['ccUsers']	=	$ccUsers;


						$bccCodes	=	explode(",",$val['to_users_bcc']);
						$bccCodes	=	implode("','",$bccCodes);
						$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$bccCodes."')";	
						$bccCodes	=	$this->getdbcontents_sql($sql,0);
						$newbccUsers	=	array();
						foreach($bccCodes as $key3=>$valbcc){
						$newbccUsers[] =	$valbcc['name'];
						}
						$bccUsers	=	implode(',',$newbccUsers);
						if($bccUsers)
						$data[$key]['bccUsers']	=	$bccUsers;

						$data[$key]['from_json']	=	$frnds->getFriendsEmail_Json_ById($val['from_id']);


				}
			return $data;

		}
		// Count Number of unread mail 
		function unreadMailCount($id)
		{
			$query	=	'SELECT * FROM tblmail_to WHERE status = 0 AND to_id = '.$id;
			$unreadMessage	=	mysql_query($query);
			$count			=	mysql_num_rows($unreadMessage);
			return $count;
		}
		// ----------------------------- for trash box----------------------------------------//
		
		function getInboxTrash($id,$serverOffset = '+00:00', $userOffset = '+00:00', $dtFmt = '%b %d %Y %h:%i %p'){

			$query				=	"SELECT C.thread_id FROM `tblmail_content` C LEFT JOIN `tblmail_send_to` ST ON ST.mail_id = C.mail_id WHERE C.from_id = $id AND ST.is_deleted = 1 GROUP BY C.thread_id UNION SELECT thread_id FROM `tblmail_to` WHERE to_id = $id AND is_deleted = 1 GROUP BY thread_id";
			
			$spage				=	$this->create_paging("n_page",$query,$per_page=10);
			
			$resultArry	=	array();
			$resultArry[1]		=	$spage;				
			$resultArry[0]		=	$this->getdbcontents_sql($spage->finalSql(),0);
			//print_r($resultArry[0]);exit;
			foreach($resultArry[0] as $key=>$val)
				{	
				    $sql =	"SELECT ST.*,C.*,T.status,T.mail_to_id,U.`first_name`,U.`last_name`,U.`profile_image`,T.created_date,
							 DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN tblmail_to  T ON T.mail_id = C.mail_id LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id` LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = C.`mail_id` WHERE T.thread_id=".$val['thread_id']." AND T.to_id = $id AND T.`is_deleted` = 1 ORDER BY C.created_date DESC LIMIT 0,1";
				    $dataIn	=	end($this->getdbcontentshtml_sql($sql,0));
					//print_r($dataIn);
					$sql =	"SELECT  ST.*,C.*, U.`first_name`,U.`last_name`,U.`profile_image`,C.created_date,
							 DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id` LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = C.`mail_id` WHERE C.thread_id=".$val['thread_id']." AND ST.is_deleted =1 AND C.`from_id` = $id ORDER BY C.created_date DESC LIMIT 0,1";
				    $dataOut	=	end($this->getdbcontentshtml_sql($sql,0));
					//print_r($dataOut);
					
					if($dataOut['created_date'] > $dataIn['created_date'])
						$data[] = $dataOut;
					else if($dataIn['created_date'] > $dataOut['created_date'])
						$data[]	= $dataIn;
					else if(($dataIn['created_date'] == $dataOut['created_date']) && (!empty($dataIn) || !empty($dataOut))		&& (!empty($dataIn['created_date']) || !empty($dataOut['created_date'])))
						$data[]	= $dataIn;
				}
				
				if($data){
					$spage->endingRow = count($data);
				
					usort($data, function($a, $b) {
					if ($a['created_date']==$b['created_date']) return 0;
					return ($a['created_date']<$b['created_date'])?1:-1;;
					});
				 }

					foreach($data as $key=>$val)
						{
							$toCodes	=	explode(",",$val['to_users']);
							$toCodes	=	implode("','",$toCodes);
							$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$toCodes."')";	
							$toUsers	=	$this->getdbcontents_sql($sql,0);
							$newtoUsers	=	array();
							foreach($toUsers as $key1=>$valto){
							$newtoUsers[] =	$valto['name'];
							}
							$toUsers	=	implode(',',$newtoUsers);
							if($toUsers)
							$data[$key]['toUsers']	=	$toUsers;



							$ccCodes	=	explode(",",$val['to_users_ccc']);
							$ccCodes	=	implode("','",$ccCodes);
							$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$ccCodes."')";	
							$ccCodes	=	$this->getdbcontents_sql($sql,0);
							$newccUsers	=	array();
							foreach($ccCodes as $key2=>$valcc){
							$newccUsers[] =	$valcc['name'];
							}
							$ccUsers	=	implode(',',$newccUsers);
							if($ccUsers)
							$data[$key]['ccUsers']	=	$ccUsers;


							$bccCodes	=	explode(",",$val['to_users_bcc']);
							$bccCodes	=	implode("','",$bccCodes);
							$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$bccCodes."')";	
							$bccCodes	=	$this->getdbcontents_sql($sql,0);
							$newbccUsers	=	array();
							foreach($bccCodes as $key3=>$valbcc){
							$newbccUsers[] =	$valbcc['name'];
							}
							$bccUsers	=	implode(',',$newbccUsers);
							if($bccUsers)
							$data[$key]['bccUsers']	=	$bccUsers;

						}

			$resultArry[0]	=	$data;
			return $resultArry;		
			
		}
		
		
		//-- function for trash box
		
		function getTrashView($thread_id,$userId,$serverOffset = '+00:00', $userOffset = '+00:00', $dtFmt = '%b %d %Y %h:%i %p'){
		
		

			$sql	=	"SELECT  ST.*,C.*, U.`first_name`,U.`last_name`,U.`profile_image`,L.`user_name`,T.created_date,DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN tblmail_to  T ON T.mail_id = C.mail_id LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id` LEFT JOIN tbluser_login L ON L.`login_id` = U.`login_id`  LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = T.`mail_id` WHERE T.thread_id=".$thread_id." AND C.is_saved = 0  AND T.to_id = $userId AND T.`is_deleted` = 1 ORDER BY C.created_date DESC";
			
			$dataIn	=	$this->getdbcontentshtml_sql($sql,0);
			//print_r($dataIn);
			$sql	=	"SELECT  ST.*,C.*, U.`first_name`,U.`last_name`,U.`profile_image`,L.`user_name`,C.created_date,DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id` LEFT JOIN tbluser_login L ON L.`login_id` = U.`login_id` LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = C.`mail_id` WHERE C.thread_id=".$thread_id." AND C.is_saved = 0 AND ST.is_deleted =1 AND C.`from_id` = $userId ORDER BY C.created_date DESC";
			
			$dataOut =	$this->getdbcontentshtml_sql($sql,0);
			//print_r($dataOut);

			$data	 =	array_merge($dataIn,$dataOut);
			//print_r($data);exit;
			
			usort($data, function($a, $b) {
			if ($a['created_date']==$b['created_date']) return 0;
			return ($a['created_date']<$b['created_date'])?1:-1;;
			});
			
			$frnds	=	new friendsManagement();
			
			foreach ($data as $key=>$val)
				{
					if($val['is_attached'] == 1){
						$query		=	"SELECT * FROM `tblmail_attachments` WHERE `mail_id`=".$val["mail_id"];
						$rec		=	$this->getdbcontents_sql($query, 0);
						$data[$key]["attached"]=	$rec;	
					}
							$toCodes	=	explode(",",$val['to_users']);
							$toCodes	=	implode("','",$toCodes);
							$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$toCodes."')";	
							$toUsers	=	$this->getdbcontents_sql($sql,0);
							$newtoUsers	=	array();
							foreach($toUsers as $key1=>$valto){
							$newtoUsers[] =	$valto['name'];
							}
							$toUsers	=	implode(',',$newtoUsers);
							if($toUsers)
							$data[$key]['toUsers']	=	$toUsers;



							$ccCodes	=	explode(",",$val['to_users_ccc']);
							$ccCodes	=	implode("','",$ccCodes);
							$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$ccCodes."')";	
							$ccCodes	=	$this->getdbcontents_sql($sql,0);
							$newccUsers	=	array();
							foreach($ccCodes as $key2=>$valcc){
							$newccUsers[] =	$valcc['name'];
							}
							$ccUsers	=	implode(',',$newccUsers);
							if($ccUsers)
							$data[$key]['ccUsers']	=	$ccUsers;


							$bccCodes	=	explode(",",$val['to_users_bcc']);
							$bccCodes	=	implode("','",$bccCodes);
							$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$bccCodes."')";	
							$bccCodes	=	$this->getdbcontents_sql($sql,0);
							$newbccUsers	=	array();
							foreach($bccCodes as $key3=>$valbcc){
							$newbccUsers[] =	$valbcc['name'];
							}
							$bccUsers	=	implode(',',$newbccUsers);
							if($bccUsers)
							$data[$key]['bccUsers']	=	$bccUsers;
				
							$data[$key]['from_json']	=	$frnds->getFriendsEmail_Json_ById($val['from_id']);
				
				}
			return $data;
		
		
		}

		function getSendMail($id,$serverOffset = '+00:00', $userOffset = '+00:00', $dtFmt = '%b %d %Y %h:%i %p'){				

			$query				=	"SELECT C.thread_id FROM `tblmail_content` C LEFT JOIN `tblmail_send_to` ST ON ST.mail_id = C.mail_id WHERE C.from_id = $id AND ST.is_deleted = 0 GROUP BY C.thread_id ORDER BY C.created_date DESC";


			$spage				=	$this->create_paging("n_page",$query,$per_page=10);
			$resultArry	=	array();
			$resultArry[1]		=	$spage;				
			$resultArry[0]		=	$this->getdbcontents_sql($spage->finalSql(),0);
			foreach($resultArry[0] as $key=>$val)
				{	
					$sql	=	"SELECT  ST.*,C.*, T.mail_to_id,U.`first_name`,U.`last_name`,U.`profile_image`,T.created_date,DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN tblmail_to  T ON T.mail_id = C.mail_id LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id`  LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = C.`mail_id` WHERE T.thread_id=".$val['thread_id']." AND C.is_saved = 0 AND T.to_id = $id AND T.`is_deleted` = 0 ORDER BY C.created_date DESC LIMIT 0,1";
				    $dataIn	=	end($this->getdbcontentshtml_sql($sql,0));
					//print_r($dataIn);
					$sql	=	"SELECT  ST.*,C.*, U.`first_name`,U.`last_name`,U.`profile_image`,C.created_date,DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id` LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = C.`mail_id`  WHERE C.thread_id=".$val['thread_id']." AND C.is_saved = 0  AND ST.is_deleted =0  AND C.`from_id` =$id ORDER BY C.created_date DESC LIMIT 0,1";
				    $dataOut	=	end($this->getdbcontentshtml_sql($sql,0));
					//print_r($dataOut);
					if($dataOut['created_date'] > $dataIn['created_date'])
						$data[] = $dataOut;
					else if($dataIn['created_date'] > $dataOut['created_date'])
						$data[]	= $dataIn;
					else if($dataIn['created_date'] == $dataOut['created_date'] && (!empty($dataIn) || !empty($dataOut)))
						$data[]	= $dataIn;
					//print_r($data); 
				}
			
				if($data){
					$spage->endingRow = count($data);
				
					usort($data, function($a, $b) {
					if ($a['created_date']==$b['created_date']) return 0;
					return ($a['created_date']<$b['created_date'])?1:-1;;
					});
				 }

			foreach($data as $key=>$val)
				{
					$toCodes	=	explode(",",$val['to_users']);
					$toCodes	=	implode("','",$toCodes);
					$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$toCodes."')";	
					$toUsers	=	$this->getdbcontents_sql($sql,0);
					$newtoUsers	=	array();
					foreach($toUsers as $key1=>$valto){
					$newtoUsers[] =	$valto['name'];
					}
					$toUsers	=	implode(',',$newtoUsers);
					if($toUsers)
					$data[$key]['toUsers']	=	$toUsers;



					$ccCodes	=	explode(",",$val['to_users_ccc']);
					$ccCodes	=	implode("','",$ccCodes);
					$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$ccCodes."')";	
					$ccCodes	=	$this->getdbcontents_sql($sql,0);
					$newccUsers	=	array();
					foreach($ccCodes as $key2=>$valcc){
					$newccUsers[] =	$valcc['name'];
					}
					$ccUsers	=	implode(',',$newccUsers);
					if($ccUsers)
					$data[$key]['ccUsers']	=	$ccUsers;


					$bccCodes	=	explode(",",$val['to_users_bcc']);
					$bccCodes	=	implode("','",$bccCodes);
					$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$bccCodes."')";	
					$bccCodes	=	$this->getdbcontents_sql($sql,0);
					$newbccUsers	=	array();
					foreach($bccCodes as $key3=>$valbcc){
					$newbccUsers[] =	$valbcc['name'];
					}
					$bccUsers	=	implode(',',$newbccUsers);
					if($bccUsers)
					$data[$key]['bccUsers']	=	$bccUsers;

				}

			//print_r($data);exit;
			$resultArry[0]	=	$data;
			return $resultArry;		
		}

		// to insert into send list
		
		function insertToSendList($emails,$emailsBcc,$emailsCcc,$mailId){
			$id				=	$this->db_insert("tblmail_send_to",array("mail_id"=>$mailId,"to_users"=>$emails,"to_users_bcc"=>$emailsBcc,"to_users_ccc"=>$emailsCcc),0);
			return $id;
		}

		function updateToSendList($emails,$emailsBcc,$emailsCcc,$mailId){
			$id				=	$this->db_update("tblmail_send_to",array("to_users"=>$emails,"to_users_bcc"=>$emailsBcc,"to_users_ccc"=>$emailsCcc),"mail_id=".$mailId,0);
			return $id;
		}

		// for viewing the send mail 
		
		function getSendMailView($thread_id,$userId,$serverOffset = '+00:00', $userOffset = '+00:00', $dtFmt = '%b %d %Y %h:%i %p'){

			$sql	=	"SELECT  ST.*,C.*, T.mail_to_id,U.`first_name`,U.`last_name`,U.`profile_image`,L.`user_name`,T.created_date,DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN tblmail_to  T ON T.mail_id = C.mail_id LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id` LEFT JOIN tbluser_login L ON L.`login_id` = U.`login_id`  LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = T.`mail_id` WHERE T.thread_id=".$thread_id." AND T.to_id = $userId AND T.`is_deleted` = 0 ORDER BY C.created_date DESC";
			
			$dataIn	=	$this->getdbcontentshtml_sql($sql,0);
			//print_r($dataIn);
			$sql	=	"SELECT  ST.*,C.*, U.`first_name`,U.`last_name`,U.`profile_image`,L.`user_name`,C.created_date,DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM tblmail_content C LEFT JOIN `tblusers` U  ON U.`user_id` = C.`from_id` LEFT JOIN tbluser_login L ON L.`login_id` = U.`login_id` LEFT JOIN tblmail_send_to ST ON ST.`mail_id` = C.`mail_id` WHERE C.thread_id=".$thread_id." AND ST.is_deleted =0  AND C.`from_id` =$userId  ORDER BY C.created_date DESC";
			
			$dataOut =	$this->getdbcontentshtml_sql($sql,0);
						//print_r($dataOut);exit;


			$data	 =	array_merge($dataIn,$dataOut);

			//Removing duplicate arrays
			$temp_array = array();
			foreach ($data as &$v) {
				if (!isset($temp_array[$v['mail_id']]))
					$temp_array[$v['mail_id']] =& $v;
			}
		
			$data = array_values($temp_array);

			//Sort by date
			usort($data, function($a, $b) {
			if ($a['created_date']==$b['created_date']) return 0;
			return ($a['created_date']<$b['created_date'])?1:-1;;
			});

			$frnds	=	new friendsManagement();
			foreach ($data as $key=>$val)
				{
					if($val['is_attached'] == 1){
						$query		=	"SELECT * FROM `tblmail_attachments` WHERE `mail_id`=".$val["mail_id"];
						$rec		=	$this->getdbcontents_sql($query, 0);
						$data[$key]["attached"]=	$rec;	
					}
				
				$toCodes	=	explode(",",$val['to_users']);
				$toCodes	=	implode("','",$toCodes);
				$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$toCodes."')";	
				$toUsers	=	$this->getdbcontents_sql($sql,0);
				$newtoUsers	=	array();
				foreach($toUsers as $key1=>$valto){
				$newtoUsers[] =	$valto['name'];
				}
				$toUsers	=	implode(',',$newtoUsers);
				if($toUsers)
				$data[$key]['toUsers']	=	$toUsers;



				$ccCodes	=	explode(",",$val['to_users_ccc']);
				$ccCodes	=	implode("','",$ccCodes);
				$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$ccCodes."')";	
				$ccCodes	=	$this->getdbcontents_sql($sql,0);
				$newccUsers	=	array();
				foreach($ccCodes as $key2=>$valcc){
				$newccUsers[] =	$valcc['name'];
				}
				$ccUsers	=	implode(',',$newccUsers);
				if($ccUsers)
				$data[$key]['ccUsers']	=	$ccUsers;


				$bccCodes	=	explode(",",$val['to_users_bcc']);
				$bccCodes	=	implode("','",$bccCodes);
				$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$bccCodes."')";	
				$bccCodes	=	$this->getdbcontents_sql($sql,0);
				$newbccUsers	=	array();
				foreach($bccCodes as $key3=>$valbcc){
				$newbccUsers[] =	$valbcc['name'];
				}
				$bccUsers	=	implode(',',$newbccUsers);
				if($bccUsers)
				$data[$key]['bccUsers']	=	$bccUsers;
				
				$data[$key]['from_json']	=	$frnds->getFriendsEmail_Json_ById($val['from_id']);
				
				}
		
		
			
			return $data;
		}
		
		function getDraftMailView($id){
			$query			=	"SELECT * FROM `tblmail_send_to` st JOIN `tblmail_content` c  WHERE c.`mail_id`=st.`mail_id` AND c.`mail_id`=$id";
			$resultArry		=	$this->getdbcontentshtml_sql($query, 0);
			$user			=	new userManagement();
			$frnds			=	new friendsManagement();
			
			$To				=	explode(",",$resultArry[0]['to_users']);
			$Cc				=	explode(",",$resultArry[0]['to_users_ccc']);
			$Bcc			=	explode(",",$resultArry[0]['to_users_bcc']);
			foreach($To as $key=>$val)
					 $resultArry[0]['to_json']	=	$frnds->getFriendsEmail_Json_ById($user->getUserId($val));
			
			foreach($Cc as $key=>$val)
					 $resultArry[0]['cc_json']	=	$frnds->getFriendsEmail_Json_ById($user->getUserId($val));
			
			foreach($Bcc as $key=>$val)
					 $resultArry[0]['bcc_json']	=	$frnds->getFriendsEmail_Json_ById($user->getUserId($val));

			foreach ($resultArry as $key=>$val)
				{
					if($val['is_attached'] == 1){
						$query		=	"SELECT * FROM `tblmail_attachments` WHERE `mail_id`=".$val["mail_id"];
						$rec		=	$this->getdbcontents_sql($query, 0);
						$resultArry[$key]["attached"]=	$rec;	
					}
				}
			return $resultArry;
		
		}
		
		function getSendtoMails($threadId,$userId){
			$query	=	"SELECT ST.send_to_id FROM tblmail_send_to AS ST LEFT JOIN tblmail_content C ON C.mail_id = ST.mail_id WHERE C.from_id = $userId AND C.thread_id = $threadId";
			$data	=	$this->getdbcontents_sql($query);
			foreach ($data as $key=>$val){
				$result[]	=	$val['send_to_id'];
			}
			$result	=	implode(",",$result);
			return $result;
		}
		
		// function to add the attchments 
		function insertAttachments($id,$att){ 
			$att	=	explode(",",$att);
			$id				=	$this->db_insert("tblmail_attachments",array("mail_id"=>$id,"attachment"=>$att[0],"origunal_name"=>$att[1]),0);
			return $id;
		}
		function getAttachmentById($id){
			$query	=	"SELECT `attachment`,`origunal_name` FROM `tblmail_attachments` WHERE `attachment_id`=".$id;
			$resultArry		=	$this->getdbcontents_sql($query, 0);
			return $resultArry;		
		}
		
		//-- function for get draft 
		
		function getDraftMail($id,$serverOffset = '+00:00', $userOffset = '+00:00', $dtFmt = '%b %d %Y %h:%i %p'){
			$query				=	"SELECT c.*, u.`first_name`,u.last_name,u.`profile_image`,st.*,DATE_FORMAT(CONVERT_TZ(CONCAT(C.`created_date`), '$serverOffset','$userOffset'),'$dtFmt') AS display_date FROM `tblmail_content` c JOIN `tblmail_send_to` st JOIN `tblusers` u  WHERE c.`from_id`=$id AND c.`mail_id`=st.`mail_id` AND st.`is_deleted`=0 AND c.`is_saved`=1 AND c.from_id = u.user_id ORDER BY st.`send_to_id` DESC  ";
			$spage				=	$this->create_paging("n_page",$query,$per_page=10);
			$resultArry[1]		=	$spage;			
			$resultArry[0]		=	$this->getdbcontents_sql($spage->finalSql(),0);
			
			//print_r($resultArry[0]);exit;
			foreach($resultArry[0] as $key=>$val)
				{
					$toCodes	=	explode(",",$val['to_users']);
					$toCodes	=	implode("','",$toCodes);
					$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$toCodes."')";	
					$toUsers	=	$this->getdbcontents_sql($sql,0);
					$newtoUsers	=	array();
					foreach($toUsers as $key1=>$valto){
					$newtoUsers[] =	$valto['name'];
					}
					$toUsers	=	implode(',',$newtoUsers);
					if($toUsers)
					$resultArry[0][$key]['toUsers']	=	$toUsers;



					$ccCodes	=	explode(",",$val['to_users_ccc']);
					$ccCodes	=	implode("','",$ccCodes);
					$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$ccCodes."')";	
					$ccCodes	=	$this->getdbcontents_sql($sql,0);
					$newccUsers	=	array();
					foreach($ccCodes as $key2=>$valcc){
					$newccUsers[] =	$valcc['name'];
					}
					$ccUsers	=	implode(',',$newccUsers);
					if($ccUsers)
					$resultArry[0][$key]['ccUsers']	=	$ccUsers;


					$bccCodes	=	explode(",",$val['to_users_bcc']);
					$bccCodes	=	implode("','",$bccCodes);
					$sql		=	"SELECT CONCAT(U.`first_name`,' ',U.`last_name`) AS name FROM `tblusers` U WHERE U.`user_code` IN ('".$bccCodes."')";	
					$bccCodes	=	$this->getdbcontents_sql($sql,0);
					$newbccUsers	=	array();
					foreach($bccCodes as $key3=>$valbcc){
					$newbccUsers[] =	$valbcc['name'];
					}
					$bccUsers	=	implode(',',$newbccUsers);
					if($bccUsers)
					$resultArry[0][$key]['bccUsers']	=	$bccUsers;
			
				}
			//$content			=	$resultArry[0];
			return $resultArry;		
		}
		function createThread($mailId){
			return $this->db_update("tblmail_content",array("thread_id"=>$mailId),"mail_id=$mailId");
			
		}
	
		function sendMailGeneral($cmsId,$from,$toIDs,$subject,$varArr,$type)//$toarray in array format
			{	
			
				$toarray			=	explode(',',$toIDs);
				$cls				=	new userManagement();
				$cms  				= 	new cms();
				foreach($toarray as $to)
				{	
				/*
					Get User's setings status based on setting Type.
				*/
					$constant			=	$cls->getUserSettingsName($to, $type);
					
					if($constant)
					{			
						 //$frommail		=	end($cls->getUserName($from));
						$tomail		=	$cls->getUserName($to);
						//$tomail['user_name'];
						$subject 						=   $subject;	
						$varArr["{TPL_NAME}"]			=	$tomail['name'];
						$send =	$cms->sendMailCMS($cmsId,$tomail['user_name'],LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5); 																
					}
				}
			}
		function sendMailAdmin($cmsId,$toIds,$from,$subject,$varArr,$type)
			{
				$toArry			=	explode(",",$toIds);
				
				$cms			=	new cms;
				$uMgmt			=	new userManagement;
				foreach($toArry as $to)
					{
						$toMail		=	$uMgmt->getUserName($to);
						$send =	$cms->sendMailCMS($cmsId,$toMail['user_name'],$from,$subject,$varArr,5);	
					} 
			}
		function getTutorMailId($courseCode)
			{
				$sql	 =  "SELECT Login.user_name FROM tbluser_login AS Login LEFT JOIN tblusers AS Users ON 
							 Login.login_id = Users.login_id LEFT JOIN tblcourses AS Course ON 
							 Users.user_id = Course.instructor_id WHERE Course.course_code = '$courseCode' ";
				$result	 =  end($this->getdbcontents_sql($sql,0));
				return $result['user_name'];			 
			}
		function getStudentIDs($courseId)
			{
				$sql	=  "SELECT student_id FROM tblcourse_enrollments WHERE course_id = $courseId";
				$StudIDs	 =  $this->getdbcontents_sql($sql,0);
				$studString  = "";
				foreach($StudIDs as $key=>$val)
						$studString	.=	$val['student_id'].",";
				return rtrim($studString,',');
			}	
		function sendMailPanic($toIDs,$subject,$message)//$toarray in array format
			{	
				$toarray			=	explode(',',$toIDs);
				$cms  				= 	new cms();
				$cls				=	new userManagement();
				foreach($toarray as $to)
					{
						
							$tomail							=	$cls->getUserName($to);
							//print_r($tomail);exit;
							$subject 						=   $subject;
							$varArr["{TPL_URL}"]			=	ROOT_URL;	
							$varArr["{TPL_NAME}"]			=	$tomail['name'];
							$varArr["{TPL_MESSAGE}"]		=	$message;
							$send =	$cms->sendMailCMS(LMT_MAIL_TPL_CS_PANIC,$tomail['user_name'],LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5); 																
					}
				$subject 						=   $subject;
				$varArr["{TPL_URL}"]			=	ROOT_URL;	
				$varArr["{TPL_NAME}"]			=	"Administrator";
				$varArr["{TPL_MESSAGE}"]		=	$message;
				//mail to all selected admin	
						$toIds	=	$userMgmt->getAllTplAdmin(LMT_MAIL_TPL_CS_PANIC);
						$send	=	$mailMgmt->sendMailAdmin(LMT_MAIL_TPL_CS_PANIC,$toIds,LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5);
				//$send =	$cms->sendMailCMS(LMT_MAIL_TPL_CS_PANIC,LMT_ADMIN_CS_MAIL_ID1,LMT_SITE_ADMIN_MAIL_ID,$subject,$varArr,5); 				
			}
	
	}
?>