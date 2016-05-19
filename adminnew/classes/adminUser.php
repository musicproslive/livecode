<?php
/**************************************************************************************
Created By 	:	hari krishna
Created On	:	5th march 2011
Purpose		:	managing admin users
**************************************************************************************/
class adminUser extends siteclass 
	{ 
		public	$error			=	array();
		private	$user_session	=	"";
		private	$usertable		=	"";
		public 	$adminId		=	"";
		
		//constructor		
		function __construct($user_session="",$usertable="")
			{
				//parent::__construct();
				if(!$user_session)		$user_session	=	"sess_admin";
				if(!$usertable)			$usertable		=	"tbluser_login ";	//login table for all		
				$this->user_session						=	$user_session;
				$this->usertable						=	$usertable;
			}
		
		function setError($err)
			{
				$this->error[]	=	$err;
			}
			
		function getError()
			{
				if($this->error)	return implode("<br>",$this->error);
			}
		//methods for sessname
		function set_sessname($sessname)
			{
				$this->user_session	=	$sessname;
			}
		function get_sessname()
			{
				return $this->user_session;
			}
		//getting the members data(logged in user)
		function get_user_data()
			{
				
				
				if($_SESSION[$this->get_sessname()]	<>	"")
					{
						
						return $this->getdbcontents_id($this->usertable,$_SESSION[$this->get_sessname()]);
					}
			}
		//to check whether a user logged in or not
		function check_session()
			{
				//echo "sess:".$this->get_sessname();
				
				if($_SESSION[$this->get_sessname()]	<>	"")
					{
						return $_SESSION[$this->get_sessname()];
					}
			}
		 //return true if the provided details are correct
		public function validateAdminUser($username,$password)
			{				
				$chpass			=	md5($password);
				$cond			=	$this->dbSearchCond("=","user_name",$username)." and ".$this->dbSearchCond("=","user_pwd",$chpass)." and user_group < 1 and authorized='1' and admin_authorize=1 and privacy_policy='1' and is_deleted='0' ";
				
				
				
				$data			=	$this->getdbcontents_cond('tbluser_login', $cond, 0);
				
				
				$query			=	"SELECT t.* FROM `tblusers` u JOIN `tbllookup_user_timestamp` t WHERE t.`id`=u.`time_format_id` AND u.`login_id`=".$data[0]["login_id"];
				
				
				 $data["time"]	=	end($this->getdbcontents_sql($query,0));			
				
				if($data)			return $data;						
				else 				return false;			  
			}
		public function getDetails($id="")
			{	
				if(!$id)	$id					=	$this->adminid;
							$resultArry			=	$this->getdbcontents_cond('tbladmin_users',"id=".$id);
							return $resultArry;
			}
		//return the curresponding admin details from all related tables
		public function getAdminDetails($id="")
			{	
				if(!$id)	$id						=	$this->adminid;
				$resultArry	['admin_details']		=	$this->getdbcontents_cond('tbladmin_users',"id=".$id,true);
				$resultArry['vendor']				=	$this->getSalesTeamVendors($id);
				$resultArry['deals']				=	$this->getSalesTeamDeals($id);
				return $resultArry;
			}
		public function getAdminUsers($args="1")
			{	
				$sql				=	"select *,concat(fname,\" \",lname) as fullname from tbladmin_users where $args";
				$resultArry			=	$this->getdbcontents_sql($sql);	
				return $resultArry;
			}
		
		//*************************************LEFT MENU & PERMISSION********************************BY ANITH********//
			
		public function getAllUsertypes($args="")
			{
				$sql					=	"SELECT * FROM vod_admin_usertype WHERE $args";			
				$data					=	$this->getdbcontents_sql($sql);	
				return $data;
			}		
			
		public function getAllActions($args="")
			{
				$sql					=	"SELECT * FROM vod_admin_actions WHERE $args";			
				$data					=	$this->getdbcontents_sql($sql);	
				return $data;
			}	
			
		public function getAllPageActions($args="")
			{
				if($_SESSION['user_group'] != "-1"){
						$args 	.=	" AND `user_type`=".$_SESSION['user_group'];
				}
			 	$sql					=	"SELECT *  FROM tbladmin_page_actions WHERE `actionid` =1  AND $args";	
				$data					=	$this->getdbcontents_sql($sql);	
				return $data;
			}
		 public function get_pageActions($pageid)
			{
				
				$pageid		=	$this->getRealEscapeData($pageid);
				
				$actionarr	=	$this->getAllPageActions("pageid='$pageid'");
				
				foreach($actionarr as $key=>$val)  $actid[]	=	$val['id'];
					$res		=	implode(",",$actid);	
								
				return $res;
			}	
		public function getAllMenus($args="")
			{
				if($_SESSION['user_role'] == "1")//for Super Admin
					{
						$sql					=	"SELECT * FROM  tblmenu  WHERE $args";
					}
				else	//for Admin
					{	
					
					
					
					//$query		=	"SELECT * FROM `tbluser_roles` WHERE `admin_group`=0"
					$sql					=	"SELECT * FROM `tblmenu` WHERE `id` in 
						(SELECT distinct(m.`menuId`) as menu FROM `tblsub_menu` m JOIN `tbladmin_page_actions` p  
							WHERE m.`id`= p.`pageid` AND p.`user_type`={$_SESSION['user_role']} )";
					 /*$sql					=	"SELECT M.* FROM  tblmenu AS M  
												  LEFT JOIN tbladmin_permission AS P ON M.id = P.menu_id
												  WHERE $args";
					*/ 
					  /*$sql					=	"SELECT M.* FROM tblmenu as M 
												  LEFT JOIN tbladmin_permission As P 
												  on M.id = P.menu_id"; */
												  //WHERE P.admin_id=".$_SESSION['log_id']." ORDER BY preference ASC";	
					}
					  $data					=	$this->getdbcontents_sql($sql,0);
					 // $this->print_r($data);exit;
					  return $data;
			}
			
		public function getAllPages($args="")
			{
	            $sql					=	"SELECT * FROM tblsub_menu WHERE $args";	
				$data_1					=	$this->getdbcontents_sql($sql);		
				return $data_1;
			}
			
			public function getAllPages_New($args=""){
			/*$sql					=	"SELECT * FROM tblsub_menu WHERE $args";
			$sql					=	"SELECT * FROM `tblmenu` WHERE `id` in 
						(SELECT distinct(m.`menuId`) as menu FROM `tblsub_menu` m JOIN `tbladmin_page_actions` p  
							WHERE m.`id`= p.`pageid` AND p.`user_type`={$_SESSION['user_group']} )";
				*/	
			
			
			 $sql			=	"SELECT m.* FROM `tblsub_menu` m JOIN `tbladmin_page_actions` p WHERE m.status=1 AND m.`id`= p.`pageid` AND p.`actionid`=3 AND m.`menuId`=$args  AND p.`user_type`={$_SESSION['user_role']} ORDER BY `preference` ASC ";			
			  $data_1		=	$this->getdbcontents_sql($sql,0);
				
				return $data_1;
				
			}
			
		public function getPageDetails($args="1")	
			{
				$sql					=	"SELECT pg.*,act.id as actid FROM `tbladmin_pages` as pg, `vod_admin_page_actions` as
											 act WHERE pg.id=act.pageid and $args";			
				
				$data					=	$this->getdbcontents_sql($sql);	
				return $data;
			}
		public function getPageActionId($pageid,$actionid)
			{
				$sql		=		"SELECT * FROM `tbladmin_actions` WHERE `action` ='".$actionid."'";
				$pact		=		$this->getdbcontents_sql($sql);
				$pactid		=		$pact[0]["id"];				
				
				//$pact		=	$this->getPageDetails($this->dbSearchCond("=","pg.id",$pageid)." and ".$this->dbSearchCond("=","act.actionid",$aidctionid));
				
				return $pactid;
			}
		public function getPageActionDetails($args="1")	
			{
				$sql					=	"SELECT pg.*,act.action FROM `vod_admin_page_actions` as pg, `vod_admin_actions` as
											 act WHERE act.id=pg.actionid and $args";
		
				$data					=	$this->getdbcontents_sql($sql);	
				return $data;
			}
		public function getPermission($args="1")	
			{
			
				$sql					=	"SELECT * FROM tbladmin_permission  WHERE $args";			
				$data					=	$this->getdbcontents_sql($sql);	
				return $data;
			}
		
		public function insertMenu($dataArr)
			{
				$menuid			 =	$this->db_insert('tbladmin_menus',$dataArr);
				if(!$menuid)	
					{
						$this->setPageError($this->getDbErrors());
						return false;
					}
				else 		return $menuid;
			}
		
		public function insertPage($dataArr)
			{
				$this->dbStartTrans();
				$pageArr						=	$dataArr['page'];
				$actionArr						=	$dataArr['actions'];
				$pageid							=	$this->db_insert('tbladmin_pages',$pageArr);
				
				if($pageid)
					{
						foreach($actionArr as $key=>$val)
							{
								$act_arr['actionid']	=	$val;
								$act_arr['pageid']		=	$pageid;
								$result					=	$this->db_insert('vod_admin_page_actions',$act_arr);
								if(!$result)
									{	$this->dbRollBack();
										$this->setPageError($this->getDbErrors());
										return false;										
									}	
							}	
						return $pageid;
					}
				else
					{ 						
						$this->setPageError($this->getDbErrors());
						return false;
					}
		
			}
		public function insertPermission($dataArr)
			{
				$pid			 =	$this->db_insert('vod_admin_permission',$dataArr);							
				if(!$pid) 
					{
						$this->setPageError($this->getDbErrors());
						return false;
					}
				else return $pid;
			}
			
		public function updateMenu($dataArr,$id)
			{
				$data		=	$this->db_update('tbladmin_menus',$dataArr,"id=".$id);
				if(!$data) 		
					{
						$this->setPageError($this->getDbErrors());
						return false;
					}
				else return true;
			}
			
		public function updatePage($dataArr,$id)
			{
				$pageArr						=	$dataArr['page'];
				$actionArr						=	$dataArr['actions'];
				$data							=	$this->db_update('tblsub_menu',$pageArr,"id=".$id);
				if(!$data) 		
					{
						$this->setPageError($this->getDbErrors());
						return false;
					}
				else 
					{
						if($actionArr)
							{
								foreach($actionArr as $key=>$val)
									{
										$act_arr['actionid']	=	$val;
										$act_arr['pageid']		=	$id;
										$result					=	$this->db_insert('vod_admin_page_actions',$act_arr);
										if(!$result)
											{
												$this->setPageError($this->getDbErrors());
												return false;										
											}	
									}										
							}
						
						return true;
					}
			}	
			
		//************************************* PERMISSION ***********************************/
		public function head_menu($title,$id,$selected)
			{
				$selStyle	=	($id == $selected) ? 'LeftMenuHeadCurrent':'';
				return	 '<div class="LeftMenuHead  '.$selStyle.'">'.$title.'</div>';
			}
			    
		public function head_page($title,$link,$id,$selected)
			{
		
				$selStyle	=	($id == $selected) ? 'letnavCurrent':'';
				return	 '<a href="'.$link.'" class="letnav '.$selStyle.'">'.$title.'</a>';
			}
	  	public function get_usertype()
			{
				$aid		=	$this->get_user_data();
				$utypeid	=	$aid['0']['user_role'];		
				return $utypeid;
			}	
		 public function get_menuid($menu)
			{			
				//$menulist	=	$this->getAllMenus(" menuname='$menu' order by preference asc");
				
				$query		=	"SELECT * FROM `tblmenu` WHERE `menuName`='$menu'";
				$menulist	=	$this->getdbcontents_sql($query);					
				$mid		=	$menulist['0']['id'];	
				return $mid;
			}	
		 public function get_menutitle($menu)
			{
				//$menulist	=	$this->getAllMenus("menuName='$menu' order by preference asc");				
				$mtitle		=	$this->ucwords($menu);	
				return $mtitle;
			}
		public function get_singlePageId($pagenm)
			{
				$pageid		=	$this->getAllPages("link ='$pagenm'");
				$pid		=	$pageid['0']['id'];
				return $pid;
			}	
		 public function get_pageid($pagenm)
			{
				
				if($pagenm  != "json.php"){
					$_SESSION['pagename']		=	$pagenm;
				}else{
					$pagenm			=	$_SESSION['pagename'];
				}
				
				$pageid		=	$this->getAllPages("link like'$pagenm%'");	//order by length(`page`) desc
				if(count($pageid)>1)	
					{
						$qString	=	$this->getPageQueryString();
						if(!$qString)	$pid	=	$this->get_singlePageId($pagenm);							
						else 
							{
								/*$fullPage	=	$pagenm."?".$qString;								
								foreach($pageid as $key=>$val)
									{
										$pages	=	$val["page"];
										$result	=	stristr($fullPage,$pages);
										if($result)  return $pid	=	$val['id'];
										else  return $pid	=	$this->get_singlePageId($pagenm);										
									}*/
								$qArr		=	explode("&",$qString);
								$pageid		=	$this->getAllPages("page ='".$pagenm."?".$qArr[0]."'");	
												
								if(!$pageid)   $pid	=	$this->get_singlePageId($pagenm);
								else  $pid	=	$pageid['0']['id'];
							}
					}
				else	 $pid	=	$pageid['0']['id'];
				return $pid;
			}
				
		public function getpermission_page()
			{			
				$utypeid	=	$this->get_usertype();				
				$pagenm		=	$this->getPageName();
				$pid		=	$this->get_pageid($pagenm);	
				$actids		=	$this->get_pageActions($pid);						
				$status		=	$this->getPermission($this->dbSearchCond("=","usertypeid",$utypeid)." and ".$this->dbSearchCond("in","pactionid",$actids));
				if($status)		return "1";
				else			return "0";	
			}
					
		
		public function getmenu_permission($menu)
			{		
				//echo "==============";
				$utid		=	$this->get_usertype();	
				$mid		=	$this->get_menuid($menu);			
				$pageids	=	$this->getAllPages($this->dbSearchCond("=","menuid",$mid)." and penable=1 order by preference asc");
				
				foreach ($pageids  as $key =>	$val)
					{
						$pid			=	$val["id"];
						$actids			=	$this->get_pageActions($pid);						
						if($actids)
						$permstat		=	$this->getPermission($this->dbSearchCond("=","usertypeid",$utid)." and pactionid in ($actids)");
						if($permstat)
							{
								$flag	=	1;
								break;
							}
					}
				if($flag)		return "1";
				else			return "0";	
			}	
			
		public function getsubmenu_list($menu)
			{
				
				$utid		=	$this->get_usertype();
				
				$pagenm		=	$this->getPageName();
				$selpid		=	$this->get_pageid($pagenm);	 //selected Page
				$pageArr	=	$this->getAllPages(" id ='$selpid'");
				$selMenu	=	$pageArr[0]["menuId"];	 //selected Menu			
				$mid		=	$this->get_menuid($menu);
				
				$mtitles	=	$this->get_menutitle($menu);
				$pagelist	=	$this->getAllPages_New($mid);
				
				$str		.=   $this->head_menu($mtitles,$mid,$selMenu);
				
				if($pagelist) $str		.= 	'<div class="leftMenuBody">';
				foreach ($pagelist  as $key =>	$val)
					{
						$pid			=	$val["id"];
						$page			=	$val["link"];
						$ptitle			=	ucwords($val["name"]);
						$actids			=	$this->get_pageActions($pid);
						
												//$permstat		=	$this->getPermission($this->dbSearchCond("=","usertypeid",$utid)."and pactionid in ($actids) ");						
						if(1)
							{
								$str	.=	$this->head_page($ptitle,$page,$pid,$selpid);
							}
					}
					
				if($pagelist)	$str	.= '</div>';
				return $str;	
				
			}
		public function get_leftmenu()
			{	
				
				//$this->print_r($_SESSION);exit;
				if($_SESSION['user_role'] == "1")//for Super Admin
					{
						$menulist	=	$this->getAllMenus(" status=1 order by preference asc");
					}
				//$menulist	=	$this->getAllMenus(" status=1 order by preference asc");
				else
					{
						$menulist	=	$this->getAllMenus("M.status = 1 AND P.admin_id=".$_SESSION['user_role']." ORDER BY preference ASC");
					}
					
				//$this->print_r($menulist);exit;
				$list		=	'<div class="LeftMmenuList" id="idLeftMenu">';
				foreach($menulist as $key	=>	$val)
					{
						$menus		=	$val['menuName'];
						$list		.=	$this->getsubmenu_list($menus);
					}
					
				//$this->print_r($list);exit;	
				$list		.=	'</div>';
				$list		.=	'<script type="text/javascript">
							$("#idLeftMenu div.LeftMenuHead").click(function()
							{
								$(this).next("div.leftMenuBody").slideDown(200).siblings("div.leftMenuBody").slideUp("slow");
							});
							$("#idLeftMenu div.LeftMenuHeadCurrent").next("div.leftMenuBody").show();
	
							</script>';
				//$this->print_r($list);exit;
				return $list;
			}
		public function getpermission_options($action)
			{
			
			//print_r($_SESSION);
			 	$utid		=	$this->get_usertype();	
				$pagenm		=	$this->getPageName();
				$pid		=	$this->get_pageid($pagenm);	
			 	$pactid		=	$this->getPageActionId($pid,$action);			 	
			    $query		=	"SELECT * FROM `tbladmin_page_actions` WHERE `pageid`=$pid AND `actionid`=$pactid AND `user_type`=$utid";
			   	$data		=	$this->getdbcontents_sql($query,0);	
				if(count($data)>0)		return true;
				else			return false;
			}
		public function permissionMenuCheck($action,$redirect)
			{
				
				if($this->getpermission_options($action)) return true;
				else
					{
					echo $redirect;
						if($redirect)	
							{
								
								header("location:noPermission.php");exit;	
							}
						
						else  "<script>alert('You have no permition to $action')</script>";return false;
					}
			}
		public function getPagePermission()
			{
				$utid		=	$this->get_usertype();	
				$pagenm		=	$this->getPageName();
				$pid		=	$this->get_pageid($pagenm);	
				$pgArr		=	$this->getPageDetails("pg.id='$pid'"); 		
				
				foreach($pgArr as $key=>$val)  $pactid[]	=	$val['actid'];
				$pactids	=	implode(",",$pactid);
				
				if($pactids)					
						$status	=	$this->getPermission($this->dbSearchCond("=","usertypeid",$utid)." and pactionid in ($pactids)");				
				if($status)	return true;
				else			
					{	
						header("location:noPermission.php");exit;	
					}
			}
		public function unsetSelectionRetention($pageList)
			{
				// Saving selection retention property of some of the pages in temp
				foreach($pageList as $pageName)
					{
						$tempPage[$pageName]		=	$_SESSION['PAGE'][$pageName];
						$tempQuery[$pageName]		=	$_SESSION['QUERY'][$pageName]; 
						$tempQtype[$pageName]		=	$_SESSION['QTYPE'][$pageName];
					}
					
				// Distroying selection retention property of all Pages	
				unset($_SESSION['PAGE']);
				unset($_SESSION['QUERY']);
				unset($_SESSION['QTYPE']);
				
				// Getting  selection retention property of pages from temp  
				foreach($pageList as $pageName)
					{	
						$_SESSION['PAGE'][$pageName] =	$tempPage[$pageName];
						$_SESSION['QUERY'][$pageName] =	$tempQuery[$pageName];
						$_SESSION['QTYPE'][$pageName] =	$tempQtype[$pageName];
					}
			}	
		
	}
?>