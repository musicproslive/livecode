<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta charset="utf-8" />
<title>Admin Panel</title>
<link rel="SHORTCUT ICON" href="<?php echo ROOT_URL;?>images/favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta content="" name="description" />
<meta content="" name="author" />

<!--~~~~~~~~~~<PLEASE DON'T DELETE THIS CODE>~~~~~~~~~~~~~~~~~-->
<!-- Internet Explorer HTML5 enabling code: -->
<!--[if IE]>
        <script src="resources/html5.js"></script>

        <style type="text/css">
        .clear {
          zoom: 1;
          display: block;
        }
        </style>

        <![endif]-->
<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="resources/style_ie8.css" />
<![endif]-->
<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="resources/style_ie7.css" />
<![endif]-->

<script type="../classroom/application/javascript">var _prum={id:"51670333e6e53d4059000001"};var PRUM_EPISODES=PRUM_EPISODES||{};PRUM_EPISODES.q=[];PRUM_EPISODES.mark=function(b,a){PRUM_EPISODES.q.push(["mark",b,a||new Date().getTime()])};PRUM_EPISODES.measure=function(b,a,b){PRUM_EPISODES.q.push(["measure",b,a,b||new Date().getTime()])};PRUM_EPISODES.done=function(a){PRUM_EPISODES.q.push(["done",a])};PRUM_EPISODES.mark("firstbyte");(function(){var b=document.getElementsByTagName("script")[0];var a=document.createElement("script");a.type="text/javascript";a.async=true;a.charset="UTF-8";a.src="//rum-static.pingdom.net/prum.min.js";b.parentNode.insertBefore(a,b)})();</script>

<script src="../classroom/js/bootstrap/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo ROOT_URL;?>../classroom/js/jquery.form.jsv2.36.js"></script>
<script type="text/javascript" src="<?php echo ROOT_URL;?>../classroom/js/newsFeed/swfobject.js"></script>
<script type="text/javascript" src="<?php echo ROOT_URL;?>../classroom/js/lmtClock.js"></script>
<link href="../classroom/css/bootstrap/custom.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="<?php echo ROOT_URL;?>../classroom/css/bootstrap/jquery-ui.css">
<!--
<script src="<?php //echo ROOT_URL;?>js/jquery-ui.js"></script>-->
<script type="text/javascript" src="<?php echo ROOT_URL;?>../classroom/js/scrollable/jquery.scrollExtend.js"></script>

<link rel="stylesheet" href="<?php echo ROOT_URL;?>../classroom/css/bootstrap/jquery.fancybox.css">


<link href="../classroom/lmtboot/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen"/>
<!-- BEGIN CORE CSS FRAMEWORK -->
<link href="../classroom/lmtboot/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/plugins/jquery-slider/css/jquery.sidr.light.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="../classroom/lmtboot/assets/plugins/jquery-datatable/css/jquery.dataTables.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen"/>
<!-- END CORE CSS FRAMEWORK -->

<!-- BEGIN CSS TEMPLATE -->

<link href="../classroom/lmtboot/assets/css/themes/coporate/style.css" rel="stylesheet" type="text/css"/>

<link href="../classroom/lmtboot/assets/css/themes/coporate/responsive.css" rel="stylesheet" type="text/css"/>
<link href="../classroom/lmtboot/assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
<style>
    @media(max-device-width: 667px) 
  { 
         .logo
		 {
			 margin-top:0px !important;
		 }
		 #portrait-chat-toggler
		 {
			 display:none !important;
		 }
}
</style>

<!-- END CSS TEMPLATE -->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="">
<!-- BEGIN HEADER -->
<div class="header navbar navbar-inverse ">
  <!-- BEGIN TOP NAVIGATION BAR -->
  <div class="navbar-inner">
    <div class="header-seperation">
      <ul class="nav pull-left notifcation-center" id="main-menu-toggle-wrapper" style="display:none">
        <li class="dropdown"> <a id="main-menu-toggle" href="#main-menu"  class="" >
          <div class="iconset top-menu-toggle-dark"></div>
          </a> </li>
      </ul>
      <!-- BEGIN LOGO -->
      <a href="#"><img src="../classroom/images/logo141.png" class="logo" alt=""  data-src="../classroom/images/logo141.png" data-src-retina="../classroom/images/logo141.png"   height="60" /></a>
      <!-- END LOGO -->
      <ul class="nav pull-right notifcation-center">
       
    
        <li class="dropdown" id="portrait-chat-toggler" style="display:none"> <a href="#sidr" class="chat-menu-toggle">
          <div class="iconset top-chat-white "></div>
          </a> </li>
      </ul>
    </div>
    <!-- END RESPONSIVE MENU TOGGLER -->
    <div class="header-quick-nav" >
      <!-- BEGIN TOP NAVIGATION MENU -->
      <div class="pull-left">
        <ul class="nav quick-section">
          <li class="quicklinks"> <a href="#" class="" id="layout-condensed-toggle" >
            <div class="iconset top-menu-toggle-dark"></div>
            </a> </li>
        </ul>

      </div>
      <!-- END TOP NAVIGATION MENU -->
      <!-- BEGIN CHAT TOGGLER -->
      <div class="pull-right">
        <div class="chat-toggler"> 
			<a href="#" class="dropdown-toggle" id="profile-sub" data-placement="bottom"  data-toggle="dropdown">
          <div class="user-details">
            <div class="username"><span class="bold">Admin</span> </div>
          </div>
		  	<div class="iconset top-down-arrow"></div>
		   </a>
		
			  <ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="profile-sub">
              <li><a href="signout.php"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Log Out&nbsp;&nbsp;</a></li>
            </ul>
         
         
          <div class="profile-pic"> <img src="images/BirdLogo.jpg"  alt="" data-src="images/BirdLogo.jpg" data-src-retina="images/BirdLogo.jpg" width="35" height="35" /> </div>
        </div>

      </div>
      <!-- END CHAT TOGGLER -->
    </div>
    <!-- END TOP NAVIGATION MENU -->
  </div>
  <!-- END TOP NAVIGATION BAR -->
</div>
<!-- END HEADER -->


