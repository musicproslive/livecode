<?php
/**************************************************************************************
Created by :
Created on :2010-11-16
Purpose    :Index Model Page
**************************************************************************************/
class index extends modelclass
	{
		public function indexListing()
			{
				//echo $_SESSION['adminLoginRedirectLink'];exit;
				return $this->executeAction(false,"Signinform",true);				
			}	
		public function indexforgotPassword()
			{
				return true;
			}
		public function createnewpassword($dataIns)
			{
				if(trim($data['emailid']))
				  {
					$sql		=	"select cu.* ,cc.country_name,cs.state_name,ct.category,ul.user_name,ul.user_pwd 
									from tblusers as cu 
									left join tblcountries as cc on cu.country_id=cc.country_id 
									left join tblstates as cs on cu.state_id=cs.state_id 
									left join tbluser_category as ct on cu.user_category_id = ct.category_id 
									left join tbluser_login as ul on cu.login_id=ul.login_id 
									where ul.user_name='".$data['emailid']."'";
					$dataArra	=	end($this->getdbcontents_sql($sql));
					$this->db_update("tbluser_login",$dataIns,"login_id ='".$dataArra['login_id']."'",1);	
					return $this->executeAction(false,"Signinform",true);
				  }
			}		
				
		public function indexSigninform()
			{
				$userObj	=	new adminUser();								
				if($userObj->check_session())	$this->redirectPage($this->getLink("","adminHome.php",false));
				//else return $this->executeAction(false,"Listing",ROOT_URL.'index.php?actionvar=Signinform');
				
				//return true;
			}
		public function indexSubmit()
			{
				$data		=	$this->getData("post");	
				
				$userObj	=	new adminUser();
				$arr		=	$userObj->validateAdminUser($data['username'],$data['password']);
				//echo "sss";
				print_r($arr);
				exit();
				if(count($arr)>0)	
					{
						//echo "eee";
						$userid								=	$arr[0]["login_id"];
						$_SESSION[$userObj->get_sessname()]	=	$userid;
					    $_SESSION['user_group']				=	$arr[0]["user_group"];
						$_SESSION['user_role']				=	$arr[0]["user_role"];
						$_SESSION['DATE_FORMAT']			=	array("P_DATE"=>$arr["time"]["php_date_format"],
															"P_TIME"=>$arr["time"]["php_time_format"],
															"M_DATE"=>$arr["time"]["mysql_date_format"],
															"M_TIME"=>$arr["time"]["mysql_time_format"]);
															
						
						
						$this->clearData();
						
						
							$this->redirectPage($this->getLink("","dashboard.php",false));
						
							//exit();
					}
				else
					{						
						$this->setPageError("Username or password incorrect");
						$this->executeAction(true,"Signinform",true);
					}	
			}
		public function indexSend()
			{
				ob_clean();
				$data		=	$this->getData("get");
				//$newpassword	=	new index;
					
					$sql		=	"SELECT login_id FROM tbluser_login where user_name='".$_GET['emailID']."'";
				    $result		=	end($this->getdbcontents_sql($sql,0));
					if(!empty($result))
						{	
									$loginid	= 	base64_encode(serialize($result['login_id']));
									$subject 	= 	'Live Music Tutor Forgot Password';
								    $msg 		= 	"Please visit the following URL for resetting your password : ".ROOT_URL."admin/resetPassword.php?id=".$loginid;
									$success	=	$this->sendSmtpMail($_GET['emailID'], $subject, $msg, 'livemusic@gmail.com');
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
			}
		public function indexReset()
			{
				$data		=	$this->getData("post");
				$newpassword	=	new index;
				if(trim($data['emailid']))
				  {
					$userObj	=	new adminUser();
					$sql		=	"select cu.* ,cc.country_name,cs.state_name,ct.category,ul.user_name,ul.user_pwd 
									from tblusers as cu 
									left join tblcountries as cc on cu.country_id=cc.country_id 
									left join tblstates as cs on cu.state_id=cs.state_id 
									left join tbluser_category as ct on cu.user_category_id = ct.category_id 
									left join tbluser_login as ul on cu.login_id=ul.login_id 
									where ul.user_name='".$data['emailid']."'";
					$dataArra	=	end($this->getdbcontents_sql($sql));
					//print_r($dataArra['first_name']);exit;
					if(!$dataArra)
						{	
							$this->setPageError("Sorry! Invalid email");
							$this->executeAction(false,"forgotPassword",true);
						}
					else
						{	
							$to			=	$data['emailid'];
							$from		=	GLB_SITE_EMAIL;
							$subject	=	"Password recovery mail";
							$cmsObj		=	new cms();
							
							$fname		=	 $dataArra['first_name'];
							$lname		=	 $dataArra['last_name'];
							$pname		=	 $fname." ".$lname;
						
							$pemail		=	 $dataArra['user_name'];
							$password	=	$this->createRandom(8);
							$newpass	=	md5($password);
							
				$data['']				=	$data;
				$data['user_name']		=	$newpass;
				$dataIns				=	$this->populateDbArray("tbluser_login",$data);
				$this->db_update("tbluser_login",$dataIns,"login_id ='".$dataArra['login_id']."'",1);	
				
							$message	 =	"Dear ".$pname.",<br/><br/>";
							$message	.=	"We have received a Forgot Password request from your Account.<br/><br/>";
							$message	.=	"Your login details are:<br/>";
							$message	.=	"UserName: ".$pemail."<br/>";
							$message	.=	"Password: ".$password."<br/><br/><br/>";
							//$message	.=	" <a href='".ROOT_URL."admin/index.php?name=".$pname."' target='_blank'>Click here to Login.</a><br/><br/>";
							$message	.=	"Regards,<br/>";
							$message	.=	"Administrator(LiveMusic).";
							//echo 	$message;exit; 
							$send		 =	$this->sendmail($to,$from,$subject,$message);
							if($send)
								{
									$this->setPageError("A mail has been to your email address");
									$this->executeAction(true,"Signinform",true);
								}
							else
							{
									$this->setPageError("Sorry, Please try again");
									$this->executeAction(true,"Signinform",true);
							}
								
						}	
				}
				else
				{
					$this->setPageError("Sorry! Invalid email");
					$this->executeAction(true,"Listing",true);
				}
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
