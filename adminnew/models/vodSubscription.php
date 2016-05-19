<?php
/**************************************************************************************
Created by : Prem Pranav
Created on : 9th October 2012
Purpose    :  list All videos under VOD
************************************** ************************************************/
class vodSubscription extends modelclass
	{
		public function vodSubscriptionListing()
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
		
		public function vodSubscriptionFetch(){
		
				// Connect to MySQL database
				$page 			= 	0;	// The current page
				$sortname 		= 	'VT.trans_time';	 // Sort column
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
				
				$sql			= 	"SELECT count(*) 
												FROM tblcourse_vod_transaction AS VT 
												LEFT JOIN `tblcourse_vod_transaction_courses`  AS VTC ON VT.id = VTC.course_vod_transaction_id
												LEFT JOIN tblcourse_vod AS V ON VTC.vod_id=V.id 
												LEFT JOIN tblcourses AS C ON V.course_id=C.course_id  
												LEFT JOIN tblusers AS U1 ON C.instructor_id=U1.user_id
												LEFT JOIN tblinstrument_master AS I ON I.instrument_id=C.instrument_id
												LEFT JOIN tblusers AS  U2 ON VT.user_id=U2.user_id
												LEFT JOIN tblcurrency_type AS CT ON CT.currency_id=VT.currency_id
												WHERE 1 $searchSql";
												if($_SESSION['txtInst'])
												$sql	.=" AND concat(U1.first_name,' ',U1.last_name) LIKE '%$instructor%' ";
												if($_SESSION['txtCourse'])
												$sql	.=" AND C.title LIKE '%$course%'";
												if($_SESSION['instrument_id'])
												$sql	.=" AND C.instrument_id=$instrument_id ";
												 
			//C.title='$course' AND concat(U.first_name,' ',U.last_name)='$instructor',C.instrument_id=$instrument_id,
										
												
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
				$sql 					= 	"SELECT C.title,I.name,CONCAT(U1.first_name,' ',U1.last_name) AS instructor_name,VT.trans_amount, CT.symbol,
												CONCAT(U2.first_name,' ',U2.last_name) AS purchased_by, VT.paid_profile, VT.tx_ref_num,
												DATE_FORMAT(VT.trans_time,'".$_SESSION["DATE_FORMAT"]["M_DATE"]." , ".$_SESSION["DATE_FORMAT"]["M_TIME"]."') AS transaction_time
												FROM tblcourse_vod_transaction AS VT 
												LEFT JOIN `tblcourse_vod_transaction_courses`  AS VTC ON VT.id = VTC.course_vod_transaction_id
												LEFT JOIN tblcourse_vod AS V ON VTC.vod_id=V.id 
												LEFT JOIN tblcourses AS C ON V.course_id=C.course_id  
												LEFT JOIN tblusers AS U1 ON C.instructor_id=U1.user_id
												LEFT JOIN tblinstrument_master AS I ON I.instrument_id=C.instrument_id
												LEFT JOIN tblusers AS  U2 ON VT.user_id=U2.user_id
												LEFT JOIN tblcurrency_type AS CT ON CT.currency_id=VT.currency_id
												WHERE 1 ";
												if($_SESSION['txtInst'])
												$sql	.=" AND concat(U1.first_name,' ',U1.last_name) LIKE '%$instructor%' ";
												if($_SESSION['txtCourse'])
												$sql	.=" AND C.title LIKE '%$course%'";
												if($_SESSION['instrument_id'])
												$sql	.=" AND C.instrument_id=$instrument_id ";
												
												$sql	.= "$searchSql	$sortSql $limitSql";
											
				
				$results 				= 	$this->db_query($sql,0);
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						
						$data['rows'][] = array
					(
				'id' => $row['id'],
				'cell' => array($i, $row['title'], $row['name'], $row['instructor_name'], $row['symbol'].$row['trans_amount'], $row['purchased_by'],$row['paid_profile'],$row['tx_ref_num'],$row['transaction_time'])
					);
				}
				ob_clean();
				$r =json_encode($data);
				
				echo  $r;
				exit;
		}	
		public function vodSubscriptionSearch()
			{
				$this->executeAction(false,"Listing",true);
			}	
		public function vodSubscriptionReset()
			{
				$this->clearData("Search");
				header("Location: vodVideos.php");
				exit;	
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
