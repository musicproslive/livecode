<?php
/****************************************************************************************
Created by	:	Arun
Created on	:	03-09-2011
Purpose		:	Group Album Gallery
*************** ***********************************************************************/
class reviewContent extends modelclass
	{
		public function reviewContentListing()
			{					
				$alb	= 	new albumlist();
				$allData = $alb -> getAllNewsfeedData();
				
			//$ipaddress = getenv("HTTP_CLIENT_IP");
			//Echo "Your IP is $ipaddress!";
				return 	$allData;			
			}		
		
		public function reviewContentApporve(){
			if(!empty($_POST)){
				$alb	= 	new albumlist();
				$result = $alb -> getImageData( $_POST['imageId'] );				
				//print_r($_POST['imageId']);
				//print_r ($result);
				
				$data['rev_image_id']	=	$_POST['imageId'];
				$data['rev_image_file']=$result[0]['rev_image_file'];
				$data['rev_file_type']=$result[0]['rev_file_type'];
				$data['rev_date_time']	=	date("Y-m-d H:i:s");
				$data['rev_album_id']=$result[0]['rev_album_id'];
				
				/* Approved by code (Login user name)*/
				$userObj	=	new adminUser();
				$allData = $userObj -> check_session();
				$userSess 	=	end($userObj->get_user_data());				
				$_SESSION['log_id']	=	$userSess['login_id'];
				$uname= $userSess[user_name];				
				$data['approved_by']	=	$uname;				
			/*	$approveBox = 0;
				$caption = "Disapproved";
				//$deletestatus = 0;
				if($_POST['app_img'] == 0){
					$approveBox = 1;
					$caption = "Approved";
					//$deletestatus = 1;
				}
				$data['rev_status']	=		$approveBox; */
				
				$data['rev_status']	=		1;
				$data['deleteimagecomment']=$_POST['deleteComment'];
				$data['deleteoption']=$_POST['deleteoptions'];				
				$data['deletestatus']=$result[0]['deletestatus'];
			//	$data['caption'] = $caption;
				$data['caption'] = 'Approved';				
				$data['imageuser']=$result[0]['imageuser'];	
				$data['image_owner_id']=0;				
				$data['totaldeleted']=$result[0]['totaldeleted'];				
				$data['rev_ip']=$result[0]['rev_ip'];
				//echo"<pre>"; 
				//print_r ($data);
				$insertStatus		=	$this->db_insert("tblreview_content",$data, 0);
				//echo"inserted";	

			
			}else{
				
			}		
			$this->redirectAction($err,"Listing","reviewContent.php");	
		}
		
		public function reviewContentDelete(){
				//print_r ($_POST['imgId']);
				//$imgId = explode("-", $_POST['imgId']);
				//print_r ($imgId);
				//echo $imgId[1];
				//echo $imgId[2];
				//$imgId1 = $imgId[1];
				$imagType = $_POST['imagType'];
				$alb	= 	new albumlist();
				$result = $alb -> getImageData( $_POST['imagId'] );				
				$imgId1 = $_POST['imagId'];
				$imgDel = $_POST['imgDel']+1;				
				$imguser = $result[0]['imageuser'];
				$imguserloginid = $result[0]['image_owner_id'];
				$result1 = $alb -> updateImagetotaldeleted( $imguser,$imgDel );
				$result2 = $alb -> getImageData( $_POST['imagId'] );
				if ($imagType == "pictureimage"){
					$result3 = $alb -> updateProfileimage( $imguserloginid );
				}
				
				/* Approved by code (Login user name)*/
				$userObj	=	new adminUser();
				$allData = $userObj -> check_session();
				$userSess 	=	end($userObj->get_user_data());	
				$_SESSION['log_id']	=	$userSess['login_id'];
				$uname= $userSess[user_name];				
				//$data['approved_by']	=	$uname;
				
				$data['rev_image_id']=  $imgId1;
				$data['rev_image_file']=$result2[0]['rev_image_file'];
				$data['rev_file_type']=$result2[0]['rev_file_type'];
				$data['rev_date_time']	=	date("Y-m-d H:i:s");
				$data['rev_album_id']=$result2[0]['rev_album_id'];
				//$data['approved_by']=$result2[0]['approved_by'];
				$data['approved_by']	=	$uname;
				$data['rev_status']	=	0;
				$data['deleteimagecomment']=$_POST['deleteComment'];
				$data['deleteoption']=$_POST['deleteoptions'];
				$data['deletestatus']	=	0;
				$data['caption']	=	"Deleted";
				/*if ($_POST['deleteoptions'] == "Child Pornography"){
				$data['caption']	=	"Child Pornography";
				}*/
				$data['imageuser'] = $result2[0]['imageuser'];
				$data['image_owner_id']=0;
				$data['totaldeleted']=  $result2[0]['totaldeleted'];			
				//$data['totaldeleted']=  $imgDel;
				$data['rev_ip']=$result2[0]['rev_ip'];					
				echo "Image is deleted..";
				$insertStatus		=	$this->db_insert("tblreview_content",$data, 0);
				//print_r ($data);
					/*Email Code*/
					$to = $data['imageuser'];
					//print_r($to);
					$subject = "HTML email";
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
					// More headers
					$headers .= 'From: kapilsot@gmail.com' . "\r\n";
					$message = $_POST['deleteComment'];
					//print_r ($headers);
					mail($to,$subject,$message,$headers);
				//	if(mail($to,$subject,$message,$headers)){
					
				//	}else{
					//echo "mail not sent";
				//	}					
				 //$this->redirectAction($err,"Listing","reviewContent.php");
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