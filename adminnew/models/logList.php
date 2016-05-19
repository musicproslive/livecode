<?php
/****************************************************************************************
Created by	:	Arun
Created on	:	03-09-2011
Purpose		:	Group Album Gallery
*************** ***********************************************************************/
class logList extends modelclass
	{
		/*public function logListListing()
			{					
				$alb	= 	new albumlist();
				$allData = $alb -> getLog();
				return 	$allData;			
			}	*/
				
		public function logListListing()
			{ 
			return ;
			}
			
		public function logListFetch(){
			//echo 'hello';
			$page 			= 	1;	// The current page
			$sortname 		= 	'trc.rev_date_time';	 // Sort column
			$sortorder	 	= 	'desc';	 // Sort order
			$qtype 			= 	'';	 // Search column
			$query 			= 	'';	 // Search string
				
				
			if (isset($_POST['page'])){
				$page 		= 	mysql_real_escape_string($_POST['page']);
			}
			if (isset($_POST['sortname'])) {
				$sortname 	= 	mysql_real_escape_string($_POST['sortname']);
			}
			if (isset($_POST['sortorder'])) {		
				$sortorder 	= 	mysql_real_escape_string($_POST['sortorder']);		
			}
			if (isset($_POST['qtype'])) {
				$qtype 		= 	trim(mysql_real_escape_string($_POST['qtype']));
			}
			if (isset($_POST['query'])){
				$query 		= 	trim(mysql_real_escape_string($_POST['query']));
			}
			if (isset($_POST['rp'])) {
				$rp 		= 	mysql_real_escape_string($_POST['rp']);
			}
			if(empty($rp)){
				$rp			=	LMT_SITE_ADMIN_PAGE_LIMIT;
			}
				
			$sortSql				 = 	" order by $sortname $sortorder ";
			$searchSql 				.= 	($qtype != '' && $query != '') ? " AND  `$qtype` LIKE '$query%'" : '';	
			
			//print_r($searchSql);
			
			//$alb	= 	new albumlist();
			//$results = $alb -> getDisapprovedusers();
			$sql			=	"SELECT count(trc.id) FROM `tblreview_content` trc LEFT JOIN `tblnews_feed` tnf on tnf.feed_id = trc.rev_image_id LEFT JOIN tblalbum_image a ON a.image_id = trc.rev_image_id AND a.album_id = trc.rev_album_id" ;
			
			/*$sql					= 	"SELECT count(*) FROM tbluser_log as UL 
									LEFT JOIN tblusers as U on UL.user_id=U.user_id
									LEFT JOIN tbluser_login as L on L.login_id=U.login_id
									LEFT JOIN tbluser_roles as R on R.role_id=L.user_role
									LEFT JOIN tbldefaults AS D ON UL.log_id = D.value AND D.group_id = ".LMT_LOG_GROUP." where 1 $searchSql";*/
			
			$result 				= 	$this->db_query($sql,0);
			$row 					= 	mysql_fetch_array($result);
			$total					= 	$row[0];
			// Setup paging SQL
			$pageStart 				= 	($page-1)*$rp;
			if($pageStart<0){
				$pageStart		=	0;
			}
			$limitSql 				= 	"limit $pageStart, $rp";
			// Return JSON data
			$data 					= 	array();
			$data['page'] 			= 	$page;
			$data['total'] 			= 	$total;
			$data['rows'] 			= 	array();
			$sql			=	"SELECT trc.id,trc.approved_by,trc.rev_status, trc.deletestatus,trc.caption, trc.rev_date_time, tnf.feed_id, tnf.feed_file, a.image,trc.rev_image_file, trc.rev_file_type, u.created, trc.deleteimagecomment, trc.deleteoption, trc.rev_ip, tnf.created_on, trc.rev_album_id FROM `tblreview_content` trc LEFT JOIN `tblnews_feed` tnf on tnf.feed_id = trc.rev_image_id LEFT JOIN `tblusers` u on trc.rev_image_id = 1110000+u.login_id LEFT JOIN tblalbum_image a ON a.image_id = trc.rev_image_id AND a.album_id = trc.rev_album_id $searchSql $sortSql $limitSql" ;
			
			$results 				= 	$this->db_query($sql,0);
			//print_r($results);
			$i			=	$pageStart;
			while ($row = mysql_fetch_assoc($results)){
				//echo 'imageuser '.$row['imageuser'];
				$i++;
				if ($row['rev_file_type'] == 'image'){
					$row['view'] = "<div id='basic-modal'><a class='basic' id='{$row['id']}' style='cursor: pointer;' onclick=\"return delall({$row['id']})\">click here</a></div><div id='basic-modal-content{$row['id']}' style='display:none;'><center><img src='../Uploads/album/{$row['rev_image_file']}' alt='demmy' width='590px' height='343px'/></center></div>";
					$row['uploaded'] = '';
				}
				else if ( $row['rev_file_type'] == 'pictureimage' ){
					$row['view'] = "<div id='basic-modal'><a class='basic' id='{$row['id']}' style='cursor: pointer;' onclick=\"return delall({$row['id']})\">click here</a></div><div id='basic-modal-content{$row['id']}' style='display:none;'><center><img src='../images/profile/profileImage/{$row['rev_image_file']}' alt='demmy' width='590px' height='343px'/></center></div>";
					$row['uploaded'] = $row['created'];
				}
				else {
					$row['view'] = "<div id='basic-modal'><a class='basic' id='{$row['id']}' style='cursor: pointer;' onclick=\"return delall({$row['id']})\">click here</a></div><div id='basic-modal-content{$row['id']}' style='display:none;'><center><img src='../Uploads/newsfeed/images/{$row['rev_image_file']}' alt='demmy' width='590px' height='343px'/></center></div>";
					$row['uploaded'] = $row['created_on'];
				}
				$data['rows'][] = array(
				'id' => $row['id'],
				'cell' => array($i, $row['approved_by'],$row['caption'], $row['rev_date_time'], $row['rev_image_file'],$row['deleteimagecomment'],$row['deleteoption'],$row['rev_ip'],$row['uploaded'],$row['view'])
				);
			}
			
			ob_clean();
			
			$r =json_encode($data);
			
			echo  $r;
			exit;
				
				//return 	$allData;				
			}
				
		public function __construct()
			{
				$this->setClassName();
			}
		
			public function redirectAction($errMessage,$action,$url){	
			$this->setPageError($errMessage);
			$this->clearData();
			$this->executeAction(true,$action,$url,true);	
		}
		
		public function executeAction($loadData=true,$action="",$ufURL="",$navigate=false,$sameParams=false,$newParams="",$excludParams="",$page="")
			{
				if(trim($action))	$this->setAction($action);//forced action
				$methodName	=		(method_exists($this,$this->getMethodName()))	? $this->getMethodName($default=false):$this->getMethodName($default=true);
				$this->actionToBeExecuted($loadData,$methodName,$action,$navigate,$sameParams,$newParams,$excludParams,$page,$ufURL);
				$this->actionReturn		=	call_user_func(array($this, $methodName));	
				//echo 'here actionReturn '.$methodName;
				//echo "<pre/>";
				//print_r( $this->actionReturn );
				$this->actionExecuted($methodName);
				return $this->actionReturn;
			}
		public function __destruct() 
			{
				parent::childKilled($this);
			}
	}