<?php
/****************************************************************************************
Created by	:	Arun
Created on	:	03-09-2011
Purpose		:	Group Album Gallery
*************** ***********************************************************************/
class disapprovedContent extends modelclass
	{
		public function disapprovedContentListing()
			{					
				$alb	= 	new albumlist();
				$allData = $alb -> getDispprovedImages();
				return 	$allData;				
			}		 
		
		
		public function disapprovedContentApporve(){
			if(!empty($_POST)){
				if(!empty($_POST)){
				$alb	= 	new albumlist();
				$result = $alb -> getImageData( $_POST['imageId'] );				
				//print_r($_POST['imageId']);
				//print_r ($result);
				$data['id']="";
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
				$approveBox = 0;
				$caption = "Disapproved";
				//$deletestatus = 0;
				if($_POST['approveBox'] == "on"){
					$approveBox = 1;
					$caption = "Approved";
					//$deletestatus = 1;
				}
				$data['rev_status']	=		$approveBox;
				$data['deleteimagecomment']=$_POST['deleteComment'];
				$data['deleteoption']=$_POST['deleteoptions'];				
				$data['deletestatus']=$result[0]['deletestatus'];
				$data['caption'] = $caption;				
				$data['imageuser']=$result[0]['imageuser'];				
				$data['totaldeleted']=$result[0]['totaldeleted'];				
				$data['rev_ip']=$result[0]['rev_ip'];
				//echo"<pre>"; 
				//print_r ($data);
				$insertStatus		=	$this->db_insert("tblreview_content",$data, 0);
				//echo"inserted";				
			}else{
				
			}		
			$this->redirectAction($err,"Listing","disapprovedContent.php");	
		}
	}
		
		public function disapprovedContentDelete(){
				//print_r ($_POST['imgId']);
				//$imgId = explode("-", $_POST['imgId']);
				//print_r ($imgId);
				//echo $imgId[1];
				//echo $imgId[2];
				//$imgId1 = $imgId[1];
				$alb	= 	new albumlist();
				$result = $alb -> getImageData( $_POST['imagId'] );
				$imgId1 = $_POST['imagId'];
				$imgDel = $_POST['imgDel']+1;				
				$imguser = $result[0]['imageuser'];
				$result1 = $alb -> updateImagetotaldeleted( $imguser,$imgDel );
				$result2 = $alb -> getImageData( $_POST['imagId'] );
				
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
				$data['imageuser'] = $result2[0]['imageuser'];
				$data['totaldeleted']=  $result2[0]['totaldeleted'];			
				//$data['totaldeleted']=  $imgDel;
				$data['rev_ip']=$result2[0]['rev_ip'];					
				echo "Image is deleted";
				$insertStatus		=	$this->db_insert("tblreview_content",$data, 0);
				//print_r ($data);
					/*Email Code*/
					$to = $data['rev_image_id'];
					//print_r($to);
					$subject = "HTML email";
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
					// More headers
					$headers .= 'From: <kapilsot@gmail.com>' . "\r\n";
					$message = " here ";
					//mail($to,$subject,$message,$headers);
					if(mail($to,$subject,$message,$headers)){
					
					}else{
					//echo "mail not sent";
					}					
				 //$this->redirectAction($err,"Listing","reviewContent.php");
				}
				
				/*public function reviewContentSend()
					{
						ob_clean();
						$data		=	$this->getData("get");
						//$newpassword	=	new index;
							
							$sql		=	"kuljitkaushik@gmail.com";
						   // $result		=	end($this->getdbcontents_sql($sql,0));
							if(!empty($sql))
								{	
											//$loginid	= 	base64_encode(serialize($result['login_id']));
											$subject 	= 	'Live Music Tutor Forgot Password';
											$msg 		= 	"Please visit the following URL for resetting your password : ".ROOT_URL."admin/resetPassword.php?id=".$sql;
											$success	=	$this->sendSmtpMail($sql, $subject, $msg, 'livemusic@gmail.com');
											if($success){
												echo 'Password Has Been Sent To Your Email';exit;
												}
											else{
												echo 'Sorry your request failed';exit;	
												}	
										
									}					
							else
								{
									echo 'This email address is not associated with your account.';exit;
								}	
					}*/
					
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