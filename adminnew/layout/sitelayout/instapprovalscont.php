<!DOCTYPE html>
<style>
.profile-pic-dash {
    border-radius: 100px;
    display: inline-block;
    float: left;
    height: 35px;
    overflow: hidden;
    width: 35px;
}
.dataTables_length{
margin-top: 17px !important;
}

.dataTables_paginate{
margin-top: 17px !important;
}
.dataTables_info{
margin-top: 17px !important;
border-right: none !important;
}
</style>
<html lang="en">



<body>
	<!-- HEADER -->
	        <?php include('layout/header/header.php');?>
	<!--/HEADER -->
    
    <!-- PAGE -->
	<!-- BEGIN CONTAINER -->
	<div class="page-container row-fluid">
		
				<!-- SIDEBAR -->
				   <?php include('layout/header/sidebar.php');?>
				<!-- /SIDEBAR -->
			   <div class="page-content">
       <div class="content">
      
				 <div class="row">
				 <div class="grid simple">
						<div class="grid-title no-border">
								<span class="semi-bold">
									<h4><b>Professionals Approvals</b></h4>
								</span>
								
						</div>
						<div class="grid-body no-border">
						     <!--<a href="addnews.php" class="submit btn btn-warning btn-cons">
							 Add News</a>-->
							
							<table id="pending_list" class="dataTable display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<td >Name</td>
								
									<td></td>
									
								
								</tr>
							</thead>
							<tbody>
							</tbody>
							</table>
						</div>
				</div>
						
				</div>
		</div>
	</div>
</div>
	<!--/PAGE -->
	<!-- JAVASCRIPTS -->
	<!-- Placed at the end of the document so the pages load faster -->  

<style>
   table i
{
	color: #4F81BD;
	padding: 5px;
	font-size: 18px;
}
.dataTables_paginate a {

    background-color: #f7f7f7;
    border-color: #f0f0f0 !important;
    color: #2c3e50;
	margin-left: 0 !important;
}

.previous, .next {
	background-color: #f7f7f7 !important;
    border-color: #f0f0f0 !important;
    color: #2c3e50;
}
.dataTables_paginate a:hover, .previous:hover
{
 background-color: #00adef !important;
    border-color: #00adef !important;
    color: #fff !important;
/*
	background-color: #2c3e50 !important;
    color: #f1c40f !important;*/
}
.current {
background-color: #00adef !important;;
    border-color: #00adef !important;;
    color: #fff !important;;
   /* background-color: #2c3e50 !important;
    color: #f1c40f !important;*/
	
}
</style>	
  <?php include('layout/footer/pendingfooter.php');?>