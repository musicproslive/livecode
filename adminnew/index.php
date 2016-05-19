<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta charset="utf-8" />
<title>Admin Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta content="" name="description" />
<meta content="" name="author" />
<!-- BEGIN CORE CSS FRAMEWORK -->
<link href="../classroom/lmtboot/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="../classroom/lmtboot/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
<!-- END CORE CSS FRAMEWORK -->
<!-- BEGIN CSS TEMPLATE -->
<link href="../classroom/lmtboot/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/css/responsive.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
<!-- END CSS TEMPLATE -->
<?php 
require_once 'init.php';err_status("init.php included");

//$obj	=	loadModelClass(true);
//error_reporting(E_ALL);
	//ini_set("display_errors","1");
//$obj->executeAction();
session_start();
if($_POST['username']!="")
{
	$userObj	=	new adminUser();
    $arr		=	$userObj->validateAdminUser($_POST['username'],$_POST['password']);
	
	
	if($arr[0]["login_id"]!="")	
					{
						//echo "eee";
						$userid								=	$arr[0]["login_id"];
						
						 $_SESSION['admin_id']	= $userid;
						$_SESSION[$userObj->get_sessname()]	=	$userid;
					    $_SESSION['user_group']				=	$arr[0]["user_group"];
						$_SESSION['user_role']				=	$arr[0]["user_role"];
						$_SESSION['DATE_FORMAT']			=	array("P_DATE"=>$arr["time"]["php_date_format"],
															"P_TIME"=>$arr["time"]["php_time_format"],
															"M_DATE"=>$arr["time"]["mysql_date_format"],
															"M_TIME"=>$arr["time"]["mysql_time_format"]);
							
															
					    header("location:dashboard.php");	
                        exit();						
					}
					else
					{
	                    header("location:index.php");	
                        exit();		
					}	
															
}


?>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->

<body class="error-body no-top lazy"  data-original="../classroom/lmtboot/assets/img/work.jpg"  style="background-image: url('lmtboot/assets/img/work.jpg')"> 
<div class="container">
  <div class="row login-container animated fadeInUp">  
        <div class="col-md-7 col-md-offset-2 tiles white no-padding">
		 <div class="p-t-30 p-l-40 p-b-20 xs-p-t-10 xs-p-l-10 xs-p-b-10"> 
         <div class="row">
		 <div class="col-sm-3 col-xs-3 " style="padding-right: 0px; padding-left: 0px; width: 320px;">
		  <a href="../index.php">
				<img class="logo" height="74" data-src-retina="" data-src="../classroom/images/logo141.png" alt="" src="../classroom/images/logo141.png">
				</a>  </div>
						  <h2 class="normal"  style="margin-top: 34px;">Sign In </h2>
        
		   </div>
 
		
        </div>
		<div class="tiles grey p-t-20 p-b-20 text-black">
		<form id="" name="formName" class="animated fadeIn" method="post" action="#">    
                    <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
					 <!-- {if $err != ''}
					  <div style="padding-left: 16px;padding-bottom: 10px;color: #FF0000;">{$err}</div>
					  {/if}-->
                      <div class="col-md-6 col-sm-6 ">
                        <input name="username" id="usernameId" type="text"  class="form-control" placeholder="Username" required/>
                      </div>
                      <div class="col-md-6 col-sm-6">
                        <input name="password" id="passwordId" type="password"  class="form-control" placeholder="Password" required/>
                      </div>
                    </div>
				<div class="row p-t-10 m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
					<div class="control-group  col-md-10">
					 <button type="submit" name="actionvar" class="btn btn-warning btn-cons" id="">Sign In</button> 
				  </div>
				  <div class="control-group  col-md-10">
					<div class="checkbox checkbox check-success"> <a href="#">Forgot Password?</a>&nbsp;&nbsp;
					  <!--<input type="checkbox" id="checkbox1" value="1">
					  <label for="checkbox1">Keep me reminded </label>-->
					</div>
				  </div>
				  </div>
			  </form>
	
			<!--<form id="frm_register" class="animated fadeIn" style="display:none">
                    <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                      <div class="col-md-6 col-sm-6">
                        <input name="reg_username" id="reg_username" type="text"  class="form-control" placeholder="Username">
                      </div>
                      <div class="col-md-6 col-sm-6">
                        <input name="reg_pass" id="reg_pass" type="password"  class="form-control" placeholder="Password">
                      </div>
                    </div>	
                    <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                      <div class="col-md-12">
                        <input name="reg_mail" id="reg_mail" type="text"  class="form-control" placeholder="Mailing Address">
                      </div>
                    </div>	
                    <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                      <div class="col-md-6 col-sm-6">
                        <input name="txtUserName" id="txtUserName" type="text"  class="form-control" placeholder="First Name">
                      </div>
                      <div class="col-md-6 col-sm-6">
                        <input name="txtUserName" id="txtUserName" type="password"  class="form-control" placeholder="Last Name">
                      </div>
                    </div>	
                    <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                      <div class="col-md-12 ">
                        <input name="reg_email" id="reg_email" type="text"  class="form-control" placeholder="Email">
                      </div>
                    </div>						
			</form>-->
		
		</div>
      </div>   
  </div>
</div>
<!-- END CONTAINER -->
<!-- BEGIN CORE JS FRAMEWORK-->
<script src="../classroom/lmtboot/assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="../classroom/lmtboot/assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../classroom/lmtboot/assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="../classroom/lmtboot/assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="../classroom/lmtboot/assets/plugins/jquery-lazyload/jquery.lazyload.min.js" type="text/javascript"></script>
<script src="../classroom/lmtboot/assets/js/login_v2.js" type="text/javascript"></script>
<!-- BEGIN CORE TEMPLATE JS -->
<!-- END CORE TEMPLATE JS -->

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-63456415-1', 'auto');
  ga('send', 'pageview');

</script>

<script type="text/javascript">
$("#send").click(function(){
	if(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($("#emailid").val()))
		{				
			$.ajax({
			   type	: "GET",
			   url	: "index.php",
			   data	: "actionvar=Send&emailID="+$("#emailid").val(),
			   dataType: "html",
			   success: function(msg){
				   $("#err").show(); 	
				   $("#err").html(msg); 	
				}
			 });
		}
	else
		{	
			$("#emailid").addClass("error");
			$("#err").text("Valid email please");
			$("#err").addClass("error_text");
			$("#err").show();
			return false;
		}		
});
</script> 

</body>

</html>