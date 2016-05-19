<?php
/****************************************************************************************
Created by	:	Arun
Created on	:	03-09-2011
Purpose		:	Group Album Gallery
*************** ***********************************************************************/
class disapprovedUserlist extends modelclass
	{
		public function disapprovedUserlistListing()
			{ 
			return ;
			}
			
		public function disapprovedUserlistFetch(){
			
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
			
			
			
			//$alb	= 	new albumlist();
			//$results = $alb -> getDisapprovedusers();
			$sql			=	"SELECT count(*) FROM `tblreview_content` as trc LEFT JOIN `tbluser_login` as ul on ul.user_name = trc.imageuser" ;
			
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
			$sql			=	"SELECT trc.rev_image_id, trc.rev_image_file,trc.imageuser,trc.rev_date_time,trc.rev_status ,trc.deletestatus, trc.deleteimagecomment, trc.deleteoption, trc.totaldeleted, ul.is_deleted, ul.user_name, ul.login_id FROM `tblreview_content` as trc LEFT JOIN `tbluser_login` as ul on ul.user_name = trc.imageuser JOIN (SELECT n.rev_image_id,n.imageuser, MAX(n.rev_date_time) AS max_note_date FROM tblreview_content n where n.deletestatus=0 GROUP BY n.imageuser) y ON y.imageuser = trc.imageuser AND y.max_note_date = trc.rev_date_time where trc.deletestatus=0 $searchSql $sortSql $limitSql" ;
			
			$results 				= 	$this->db_query($sql,0);
			$i			=	$pageStart;
			while ($row = mysql_fetch_assoc($results)){
				//echo 'imageuser '.$row['imageuser'];
				$i++;
				
				if ($row['is_deleted']==0)
				{
					$row['delete']	="<a href=\"disapprovedUserlist.php?actionvar=Block&lid=".$row['login_id']."&is_deleted=1\" class=\"Second_link\"  onclick=\"return delall()\">
							<img src=\"../images/active.gif\" border=\"0\" title=\"Click here to block user\"></a>";				
				}
			else{
					$row['delete']	="<a href=\"disapprovedUserlist.php?actionvar=Unblock&lid=".$row['login_id']."&is_deleted=0\" class=\"Second_link\" >
							<img src=\"../images/inactive.gif\" border=\"0\" title=\"Click here to unblock user\"></a>";
				}
				$data['rows'][] = array(
				'id' => $row['id'],
				'cell' => array($i, $row['imageuser'],$row['rev_date_time'], $row['rev_image_file'], $row['deleteimagecomment'], $row['deleteoption'], $row['totaldeleted'],$row['delete'])
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
		
		public function disapprovedUserlistBlock()
			{		
				$data				=	$this->getData("get");
				$details	=	$this->getData("request");
				$alb	= 	new albumlist();
				$allData1 = $alb -> getImageUser($details['lid']);
				$allData = $alb -> blockUsers($allData1[0]['user_name']);
				$this->redirectAction($err,"Listing","disapprovedUserlist.php");
			}	
			
		public function disapprovedUserlistUnblock()
			{					
				$data				=	$this->getData("get");
				$details	=	$this->getData("request");
				$alb	= 	new albumlist();
				$allData1 = $alb -> getImageUser($details['lid']);
				$allData = $alb -> UnblockUsers($allData1[0]['user_name']);
				$this->redirectAction($err,"Listing","disapprovedUserlist.php");	
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