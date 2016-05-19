<?php 
/****************************************************************************************
Created by	:	PREM PRANAV
Created on	:	16/11/2012
Purpose		:	To Manage Payment Refund of canceled Course
******************************************************************************************/
class courseCanceled extends modelclass
	{
		public function courseCanceledListing()
			{ 
				//Distroying Page Number and Search String Session value for all other page.
				$cls			=	new adminUser();
				$cls->unsetSelectionRetention(array($this->getPageName()));
				return ;
			}
		public function courseCanceledFetch()
			{
				$page 			= 	0;	// The current page
				$sortname 		= 	'e.created_on';	 // Sort column
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
				$sortSql				 = 	" order by $sortname $sortorder";
				$searchSql 				 = 	($qtype != '' && $query != '') ? " AND  $qtype LIKE '%$query%'" : '';
				$cond					 =	" AND    e.`enrolled_status_id` =".LMT_CS_ENR_CANCELLED;
				$sql					= 	"SELECT count(*) 
								 FROM `tblcourses` c
								 LEFT JOIN `tblcourse_enrollments` e ON e.`course_id`=c.`course_id`
								 LEFT JOIN `tblcourse_cancel_transaction` p ON p.`enrolled_id`=e.`enrolled_id`
								 LEFT JOIN `tblcurrency_type` cur ON cur.`currency_id`=p.`currency_id`
								 LEFT JOIN `tblusers`  ins  ON c.`instructor_id`=ins.`user_id`	
								 LEFT JOIN `tblusers`  u  ON e.`student_id`=u.`user_id`								
								 WHERE p.is_refunded=0 $searchSql $cond";		 
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
			
			
				$sql		=		"SELECT  p.*,p.`refunded_amount` as cost,c.`title`,
								  e.`created_on`,cur.`symbol`,CONCAT(ins.`first_name`,' ',ins.`last_name`) as instructor,CONCAT(u.`first_name`,' ',u.`last_name`) as student		   
								 
								,DATE_FORMAT(e.created_on,'".$_SESSION["DATE_FORMAT"]["M_DATE"]."')  AS created_on		   
							
								  
								  FROM `tblcourses` c
								 LEFT JOIN `tblcourse_enrollments` e ON e.`course_id`=c.`course_id`
								 LEFT JOIN `tblcourse_cancel_transaction` p ON p.`enrolled_id`=e.`enrolled_id`
								 LEFT JOIN `tblcurrency_type` cur ON cur.`currency_id`=p.`currency_id`
								 LEFT JOIN `tblusers`  ins  ON c.`instructor_id`=ins.`user_id`	
								 LEFT JOIN `tblusers`  u  ON e.`student_id`=u.`user_id`								
								 WHERE p.is_refunded=0 $cond $searchSql $sortSql $limitSql ";			   
				$results 	= 	$this->db_query($sql,0);
				
				$i			=	$pageStart;
				while ($row = mysql_fetch_assoc($results)) 
					{
						$i++;
						$row['refund']	=	'<a href="courseCanceled.php?actionvar=Refund&transID='.base64_encode(serialize($row['id'])).'"  onclick="if(confirm(\'Do you want to refund ?\'))<blockquote></blockquote>return true; return false;" class="Second_link"><img src="images/refund-icon.png" title="Payment Refund" width="25" height="25"></a>';
						$data['rows'][] = array(
						'id' => $row['course_id'],
						'cell' => array($i, $row['student'],$row['title'],$row['instructor'],$row['created_on'],$row['symbol']." ".$row['course_amount'],$row['symbol']." ".$row['deduction'],$row['symbol']." ".$row['cost'],$row['refund'])
					);
				}
				$r =json_encode($data);
				ob_clean();
				echo  $r;
				exit;
				
		
		}
		public function courseCanceledRefund()
			{
				$data	=	$this->getData("get");
				$cls = new userCourse();												
				$return['transDet'] = $transDet = $cls->getCourseCancelTransactionDet(unserialize(base64_decode($data['transID'])));				
				$return['paidAmount'] = $cls->getCourseTransactionAmount($transDet['user_id'], $transDet['enrolled_id']);
				$return['courseDet']  =  $cls->getCancelCourseDet($transDet['user_id'], $transDet['enrolled_id']);		
										
				$return['cancellationAmount'] = $transDet['deduction'];
				
				$return['cancellationPercent'] = LMT_CANCELLATION_AMOUNT;
				return $return;				
			}
		
		function courseCanceledRefundConfirm()
			{
				$data	=	$this->getData("post");
				$cls = new userCourse();
				$orbObj = new orbital();
				//$this->print_r($data);exit;
				$paidAmount = $cls->getCourseTransactionAmount($data['userId'], $data['enrollmentId']);
				
				$orbObj = new orbital();
				$orbital = array();
				$orbital['user_id']  = $data['userId'];//$_SESSION['USER_LOGIN']['LMT_USER_ID']
				
				$orbital['orbital_profile_id']  = $cls->getOrbitalProfileCode($data['userId']);
				
				$orbital['order_id']  = $cls->getOrderId($data['userId'], unserialize(base64_decode($data['enrolled_id'])));
				$orbital['amount']  = (($paidAmount - $cancellationAmount) * 100);
				
				if($orbObj->refundConsumerPayment($orbital))	
					{
						$this->db_update('tblcourse_cancel_transaction', array('deduction' => ($paidAmount - $data['refundAmount']), 'refunded_amount' => $data['refundAmount'], 'paid_profile' => $orbital['orbital_profile_id'], 'payment_response' => serialize($orbObj->getCurrentResponse()), 'tx_ref_num' => $orbObj->txRefNum, 'is_refunded' => 1), "id = {$data['cancelTransId']}");
						$this->clearData();			
						$this->redirectAction(true,"Successfully refunded", "Listing");
					}
				else
					{
						$this->redirectAction(true,"Sorry...Unable to process credit card", "Listing");
					}	
			}
			
		public function courseCanceledSearch()
			{
				$this->executeAction(false,"Listing",true);
			}
		public function courseCanceledCancel()
			{
				$this->clearData();
				$this->executeAction(false,"Listing",true,false);	
			}
		public function courseCanceledReset()
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