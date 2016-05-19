<?php 
/****************************************************************************************
Created by	:	Lijesh 
Created on	:	Jan-09-2012
Purpose		:	Manage Users Wall Post
******************************************************************************************/
class userProfile extends modelclass
	{
		public function userProfileListing()
			{ 
			
				$data		=	$this->getData("get");
				$userObj = new userManagement();
				$user = reset($userObj->getUserPassword(unserialize(base64_decode($_GET['id']))));
				//$this->print_r($user);exit;
				$loginFlag = 1;
				if(!empty($user))
					{
						$loginData = $userObj->chkRemoteUserLogin($user['user_name'], $user['user_pwd']);
						
						if(!empty($loginData))
							{
								//$userData = reset($userObj->getRemoteLoginUser($user['login_id'], $user['user_category_id']));
								
									$userData = reset($userObj->getLoginUser($user['login_id']));
								//$this->print_r($userData);exit;
								if(!empty($userData))
									{		
										$_SESSION['USER_LOGIN']['LMT_USER_ID'] 		= $userData['user_id'];
										$_SESSION['USER_LOGIN']['LMT_USER_EMAIL'] 	  = $userData['user_name'];
										$_SESSION['USER_LOGIN']['LMT_USER_CODE'] 	  = $userData['user_code'];
										$_SESSION['USER_LOGIN']['LMT_USER_ROLE'] 	= $userData['role_access_key'];
										$_SESSION['USER_LOGIN']['USER_NAME'] 		= $userData['first_name'].' '.$userData['last_name'];
										$_SESSION['USER_LOGIN']['USER_PROFILE_IMAGE']	=	$userData['profile_image'];
										$_SESSION['USER_LOGIN']['USER_COURSE_ACCESS'] 	= 	$loginData['admin_authorize'];
										
										$_SESSION['USER_LOGIN']['DATE_FORMAT']['P_DATE'] = $userData['php_date_format'];
										$_SESSION['USER_LOGIN']['DATE_FORMAT']['P_TIME'] = $userData['php_time_format'];
										$_SESSION['USER_LOGIN']['DATE_FORMAT']['M_DATE'] = $userData['mysql_date_format'];
										$_SESSION['USER_LOGIN']['DATE_FORMAT']['M_TIME'] = $userData['mysql_time_format'];
										
										//$_SESSION['USER_LOGIN']['DATE_FORMAT']	    = 	$_SESSION['DATE_FORMAT'];
																																																																						

										
										$this->clearData();
										//$this->print_r($_SESSION['USER_LOGIN']);exit;
										//$this->executeAction(false,"Listing",ROOT_URL."userHome.php",true,true);
										/*echo 'success';
										echo ROOT_URL;exit;*/
										//exit;
										header('Location: ../userHome.php');
									}
								else
									{	
										$loginFlag = 0;															
									}
							}
							else
								{	
									$loginFlag = 0;																																
								}
					}
					else
						{
							$loginFlag = 0;		
						}
				if(!$loginFlag)
					{
						$this->setPageError("Unable to login...Please try again");																		
						$this->executeAction(true,"Listing",ROOT_URL."index.php",true,true);
					}		
				exit;
				/*if(isset($data['category']))
					{
						if(count($loginData) == 1)
							{
								$loginData = reset($loginData);
																
								$userData = $userObj->getLoginUser($loginData['login_id'], $data['category']);
								if(count($userData) == 1)
									{		
										$_SESSION['USER_LOGIN']['LMT_USER_ID'] 		= $userData[0]['user_id'];
										$_SESSION['USER_LOGIN']['LMT_USER_ROLE'] 	= $userData[0]['role_access_key'];
										$_SESSION['USER_LOGIN']['USER_NAME'] 		= $userData[0]['first_name'].' '.$userData[0]['last_name'];
										$_SESSION['USER_LOGIN']['USER_PROFILE_IMAGE']	=	$userData[0]['profile_image'];
										$_SESSION['USER_LOGIN']['USER_COURSE_ACCESS'] 	= 	$loginData['admin_authorize'];
										
										
										
										$visit					=	array();
										$visit['user_id']		=	$_SESSION['USER_LOGIN']['LMT_USER_ID'];
										$visit['login_time']	=	date('Y-m-d H:i:s');
										$visit['user_IP']		=	$_SERVER['REMOTE_ADDR'];
										$visitdata				=	$this->populateDbArray("tbllogin_tracker",$visit);
										$trackSucces			=	$this->db_insert("tbllogin_tracker",$visitdata);
										
										
										
										$this->clearData();
										//$this->print_r($_SESSION['USER_LOGIN']);exit;
										$this->executeAction(false,"Listing",ROOT_URL."userHome.php",true,true);
									}
								else
									{	
										//$_SESSION['resMessage'] = 2; // 0 Error					
										$this->setPageError("Please enter your valid login details");
										$this->executeAction(true,"Listing",ROOT_URL."index.php",true,true);		
									}
							}
							else
								{									
									//$_SESSION['resMessage'] = 2; // 0 Error													
									$this->setPageError("Please enter your valid login details");
									//$this->redirectAction("Incorrect Email or password","Listing","index.php");
									
									$this->executeAction(true,"Listing",ROOT_URL."index.php",true,true);		
								}													
						
					}
				else
					{	
						//$_SESSION['resMessage'] = 2; // 0 Error					
						$this->setPageError("Please select user type");												
						$this->setPageError("Please enter your valid login details");							
						$this->executeAction(true,"Listing",ROOT_URL."index.php",true,true);		
					}*/
				
				
				
				/*$cls					=	new userManagement();
				$clsFiends				=	new friendsManagement();
				$id					=	$_GET['id'];		
				
				$frndIdList = $clsFiends->getFriendsIDList($id);
				$idList = array_merge($frndIdList, array($id));					
								
				$data = $this->getFeedAndComments($idList, $id);
				//$this->print_r($data);exit;	
				return $data;*/
			}

		
		
		/*
			Get Feed And Its Comment
			@idList : Array()
		*/	
		/*function getFeedAndComments($idList, $userID)
			{
				$newsFeedObj = new newsFeed();
				$data["newsFeed"] = $newsFeedObj->getNewsFeed($idList, 0, 5, $userID);
				
				//$this->print_r($data["newsFeed"]);exit;
				$feedID = array();
				if(!empty($data["newsFeed"]))
					{
						foreach($data["newsFeed"] as $newsFeed)
							{
								$feedID [] = $newsFeed['feed_id'];
								
								
								if($newsFeed['feed_type'] == 'LMT_PHOTO')
									$_SESSION['LMT_ALBUM_IMAGE'][$newsFeed['feed_id']] = $newsFeed['feed_file'];
							}
					}
				$data  = 	array_merge($data, $newsFeedObj->getNewsFeedComments($feedID, $userID));
				$data["likes"]	 = $newsFeedObj->getLikeCount($feedID);
				//$this->print_r($data);exit;
				return $data;
			}*/	
		
		public function redirectAction($loadData=true,$errMessage,$action)	
			{	
				$this->setPageError($errMessage);
				$this->executeAction($loadData,$action,true);	
			}		
		public function __construct()
			{
				$this->setClassName();
				//$this->tab_defaults_group	=	"tbluser_category";
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
		
		public function getstudentsDetails($membersId="",$args="1")
			{  
				$sql	=	"select cu.*,DATE_FORMAT(cu.dob,'%b %d %Y ') AS dob ,cc.country_name,cs.state_name from tblusers as cu 
							left join tblcountries as cc on cu.country_id=cc.country_id 
							left join tblstates as cs on cu.state_id=cs.state_id  
							where user_id='$membersId' and ".$args;
				$result	=	end($this->getdbcontents_sql($sql));
				return $result;
			}
		
		public function deletestudents($id)
			{
				$query 		= 	"UPDATE tblusers SET is_deleted='1' WHERE user_id='$id'";
				$result		=	$this->getdbcontents_sql($query);
				return $this->executeAction(false,"Listing",true);
			}
		
		public function getAll($stat="")
			{
				$data				=	$this->getdbcontents_cond("tbluser_category");
				return $data; 
			}
	}