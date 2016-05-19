<!DOCTYPE html>
<html lang="en">



<body>
	<!-- HEADER -->
	        <?php include('layout/header/header.php');?>
	<!--/HEADER -->
    
    <!-- PAGE -->
	<!-- BEGIN CONTAINER -->
	<style>
	    .spacer-single-form {
			clear: both;
			display: block;
			height: 15px;
			width: 100%;
			}
	</style>
	<div class="page-container row-fluid">
		
				<!-- SIDEBAR -->
				   <?php include('layout/header/sidebar.php');?>
				<!-- /SIDEBAR -->
				<link href="../lmttheme/css/location.css" rel="stylesheet" type="text/css" />
			   <div class="page-content">
       <div class="content">
      
				 <div class="row">
				 <form method="post" name="instructor" action="save_inst.php">
				 <input type="hidden" name="user_id" value="<?php echo $user_det[0]['userid']?>" id="user_id">
				 <div class="grid simple">
						<div class="grid-title no-border">
								<span class="semi-bold">
									<h4><b>Meetings</b></h4>
								</span>
								
						</div>
						<div class="grid-body no-border">
						    <div class="row">
							     <input id="searchbox_professionals" type="text" class="form-control" style="width:100%" placeholder="Enter a name or keyword">
								 
								 
								
								
							</div>
							 <div class="spacer-single-form"></div>
							<div class="row">
								    <div class="form-group">
									    <label class="form-label">Start Date:</label>
									    <div class="">
									      <div class="input-append success startdate col-md-6 col-lg-3 no-padding">
										        
												<input type="text" id="st_date" class="form-control" value="" name="date_start">
											
												<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
										   </div>
									    </div> 
									</div>
							</div>
							<div class="spacer-single-form"></div>
							<div class="row">
								    <div class="form-group">
									    <label class="form-label">End Date:</label>
									    <div class="">
									      <div class="input-append success enddate col-md-6 col-lg-3 no-padding">
										        
												<input type="text" id="ed_date" class="form-control" value="" name="date_start">
											
												<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
										   </div>
									    </div> 
									</div>
							</div>
							<!--<div class="row">
								    <div class="form-group">
									    <label class="col-xs-10 form-label">End Date:</label>
									    <div class="col-xs-9">
									      <div class="input-append success enddate col-md-10 col-lg-6 no-padding">
										        
												<input type="text" class="form-control" value="" name="date_end">
											
												<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
										   </div>
									    </div> 
									</div>
							</div>-->

							<div class="spacer-single-form"></div>
							<div class="row">
							
							<input type="button" value="Find Meetings" class="btn btn-warning" id="findless">
							</div>
							<div class="spacer-single-form"></div>
							<div class="row">
							 
								
									
									 <!--<div class="tab-pane " id="lessons">-->
										<!--<div class="col-md-12">-->
										  <ul class="nav nav-tabs" id="tab-01">
											 <li class="active" id="1" onclick="javascript:getLessons(1,1);"><a href="#scheduled_1">Scheduled</a></li>
											<li id="2" onclick="javascript:getLessons(1,2);"><a href="#booked_2">Booked</a></li>
											<li id="3" onclick="javascript:getLessons(1,3);"><a href="#completed_3">Completed</a></li>
											<li id="4" onclick="javascript:getLessons(1,4);"><a href="#cancelled_4">Cancelled</a></li>
											<li id="5" onclick="javascript:getLessons(1,5);"><a href="#expired_5">Expired</a></li>
										  </ul>
										  <div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
										  <div class="tab-content" id="tab-content">
										  <input type="hidden" name="user_code" id="user_code" value="<?php echo $user_det[0]['user_code']?>">
										   <input type="hidden" name="time_format_id" id="time_format_id" value="<?php echo $user_det[0]['time_format_id']?>">
										  <div class="tab-pane active" id="scheduled_1" >
											  <div class="row row_less" >
											   <div class="col-md-12" style="">
												<table class="dataTable display" id="lessscheduled" >
													<tbody>
													<thead>
													<tr>
															
															
															<th>NAME</th>
															<!--<th>CATEGORY</th>-->
															<th>MEETING TYPE</th>
															<th>MEETING CODE</th>
															<th>DATE</th>
															<th>TIME</th>
															<th>DURATION</th>
															<th>COST</th>
															<th></th>
													</tr>
														</thead>
											   </table>	
												
											</div>
											</div>
											<div class="clearfix">&nbsp;</div>
												
											</div>
											
											<div class="tab-pane" id="booked_2">
											  <div class="row row_less" >
											   <div class="col-md-12" style="">
											   
												<table class="dataTable display" id="lessbooked" >
													<tbody>
													<thead>
													<tr>
															
															<th>NAME</th>
															<th>MEMBERS</th>
															<!--<th>CATEGORY</th>-->
															<th>MEETING TYPE</th>
															<th>MEETING CODE</th>
															<th>DATE</th>
															<th>TIME</th>
															<th>DURATION</th>
															<th>COST</th>
															<th>SHEET MUSIC</th>
															<th></th>
													</tr>
														</thead>
											   </table>	
											
											</div>
											</div>
											<div class="clearfix">&nbsp;</div>
												
											</div>
											<div class="tab-pane" id="completed_3">
											  <div class="row row_less">
												<div class="col-md-12" style="">
												  
												  <table class="dataTable display" id="lesscompleted" >
													<tbody>
													<thead>
														<tr>
															<th>NAME</th>
															<th>MEMBERS</th>
															<!--<th>CATEGORY</th>-->
															<th>MEETING TYPE</th>
															<th>MEETING CODE</th>
															<!--<th>COST</th>-->
															<th>DATE</th>
															<th>TIME</th>
															<th>DURATION</th>
															<!--<th align="left">Music</th>-->
															<th>VIDEOS</th>
															<th>NOTES</th>
															
															
														</tr>
													</thead>
													</table>
												</div>
											  </div>
											  <div class="clearfix">&nbsp;</div>
												
											</div>
											<div class="tab-pane" id="cancelled_4">
											  <div class="row row_less" >
												<div class="col-md-12" style="">
												  
												<table class="dataTable display" id="lesscancelled">
													<tbody>
													 <thead>
														<tr>
															
															
															<th>NAME</th>
															<!--<th>CATEGORY</th>-->
															<th>MEETING TYPE</th>
															<th>MEETING CODE</th>
															<th>DATE</th>
															<th>TIME</th>
															<th>DURATION</th>
															<th>COST</th>
															<!--<th>ABORT</th>-->
															
														</tr>
														</thead>
														</table>
														</div>
												</div>
											  
											  <div class="clearfix">&nbsp;</div>
											  </div>
											
											<div class="tab-pane " id="expired_5">
											  <div class="row row_less" >
											  <div class="col-md-12" style="">
												<table class="dataTable display" id="lessexpired" >
													<tbody>
													 <thead>
														<tr>
															
															
															<th>NAME</th>
															<!--<th>CATEGORY</th>-->
															<th>MEETING TYPE</th>
															<th>MEETING CODE</th>
															<th>DATE</th>
															<th>TIME</th>
															<th>DURATION</th>
															<th>COST</th>
															
														</tr>
													</thead>
													</table>
													
											  </div>
											 </div>
											 <div class="clearfix">&nbsp;</div>
												
											</div>			 
												
										  </div>
										<!--</div>-->
									   
									 <!--</div>-->
									 
									  
									 
									
									 
									 
								</div>
								
							</div>
							<input type="hidden" id="month_rev" name="month_rev" value=<?php echo $month?> >
							<input type="hidden" id="year_rev" name="year_rev" value=<?php echo $year?> >
							
									 
						</div>
						
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
	<!--/PAGE -->
	<!-- JAVASCRIPTS -->
	<!-- Placed at the end of the document so the pages load faster -->  

	
	
	<!--- Lesson Tab -->
	    <div class="modal fade" id="videos"  style="display: none;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							  <div class="modal-dialog">
								<div class="modal-content">
								  <div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								  <h4 class="modal-title" id="myModalLabel"><b>Video</b></h4>
									
								  </div>
								  <div class="modal-body">
								  </div>
								</div>  
							</div>	
					</div>		


		<div id="form-content" class="modal  fade " role="dialog" aria-hidden="true">
		<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h3>Notes</h3>
					</div>
					<div class="modal-body">
			
			
					</div>
				</div>
			</div>
		</div>
		<div id="students_name" class="modal  fade " role="dialog" aria-hidden="true">
		<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4><b>Students Name</b></h4>
					</div>
					<div class="modal-body">
					<div class="stuname">
					
					</div>
			
					</div>
				</div>
			</div>
		</div>
	
	
	
	
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
.spacer-single-form {
    clear: both;
    display: block;
    height: 15px;
    width: 100%;
}
#profile_crop
{
   border: 1px solid #ccc;
    width: 300px !important;
	 height: 300px !important;
    position: relative;
    margin-top: 19px;
}
.profile-pic-dash {
    border-radius: 100px;
    display: inline-block;
    float: left;
    height: 35px;
    overflow: hidden;
    width: 35px;
}
</style>	

  <?php include('layout/footer/instfooter.php');?>
  <script src="../liveroom/lmtboot/assets/js/tabs_accordian.js" type="text/javascript"></script>
  <script src="../liveroom/croppic/assets/js/jquery.mousewheel.min.js"></script>
   

	
<link href="../liveroom/lmtboot/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<link href="../liveroom/lmtboot/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.css" rel="stylesheet" type="text/css" />
<script src="../liveroom/lmtboot/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="../liveroom/lmtboot/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>

	
	<script>
	
		
		
		
	
	    function delete_ins()
		{
			var user_id=$("#user_id").val();
			var login_id=$("#login_id").val();
		  $.ajax({
			url: "delete_ins.php?user_id="+user_id+"&login_id="+login_id,
			type: "GET",
			
		
		   success: function(result)
		   {
			   alert("Instructor Deleted");
		       window.location="instructor.php";
		   }
		   })
		   
		}
		
		
		function featureop(action,userid)
	{
			$.ajax({
			   type	: "GET",
			   url	: "ajax_feature.php?action="+action+"&ins_id="+userid,
			   success: function(result){
			       alert(result);
				   window.location="viewinstructor.php?login_id=<?php echo $_GET['login_id']?>";
				}
				
			 });
	}
		
 
  

	</script>
	
	
	
	<!--lesson scripts--->
	
	<script type="text/javascript" src="../liveroom/css/datatable/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="../liveroom/css/datatable/css/jquery.dataTables.css" />
<link rel="stylesheet" type="text/css" href="../liveroom/css/datatable/media/css/dataTables.responsive.css">
		<script type="text/javascript" src="../liveroom/css/datatable/media/js/dataTables.responsive.js" charset="UTF-8"></script>
<script>
$(".viewVideos").click(function(){
	var str = $(this).attr("rel");
	var n	= str.split("&");
	$("#userId").val(n[0]);
	$("#ccodeId").val(n[1]);
	$("#videoId").val(n[2]);
	$("#actionId").val(n[3]);
	$("#viewVideos").submit();
});
</script>
<script>
$(document).ready(function() {
      
	  $(".various5").fancybox({
	   maxWidth : 800,
	   maxHeight : 600,
	   fitToView : false,
	   width  : '50%',
	   height  : '60%',
	   autoSize : false,
	   closeClick : false,
	   openEffect : 'none',
	   closeEffect : 'none'
	  });
	  
	 /*  $('.input-append.enddate').datepicker({
				autoclose: true,
				todayHighlight: true
	   });*/
	 getLessons(1,1) ;
	 var datepicker = $.fn.datepicker.noConflict(); // return $.fn.datepicker to previously assigned value
	$.fn.bootstrapDP = datepicker;   
	 $('#tab-02 li').click(
					function(event)
					{
						var type=jQuery(this).attr("id");
						getLessons(1,type);
					}
				);
	 });

	 $("#less_tab").click(function(){
		 $("#scheduled_1").attr("class","active");
		 getLessons(1,1);
	 });
	 $("#findless").click(function(){
		var sel_tab=$("#tab-01 li.active").attr("id");
		 getLessons(1,sel_tab);
		
	 });
	  	$('.startdate input').datepicker({
				autoclose: true,
				todayHighlight: true,
				dateFormat: "yy-mm-dd",
				
	   });
	   $('.enddate input').datepicker({
				autoclose: true,
				todayHighlight: true,
				dateFormat: "yy-mm-dd",
	   });
	  	/*$('#calender_course').datepicker({
						
				  		dateFormat: "yy-mm-dd",
				             changeMonth:true, 
				             changeYear:true,
				             yearRange:"-100:+1",
				  		    onSelect :  function(datetext, inst) {
							
				  			$("#finddate").val(datetext);
							
				  		}					
				  	});*/
</script>
<script type="text/javascript">
	    $("#searchbox_professionals").on("keyup", function() {
		
		var sel_tab=$("#tab-01 li.active").attr("id");
		
		getLessons(1,sel_tab);
	
		
	});
	</script>

<script>
var scheduled_lesson =  $('#lessscheduled').DataTable( {
		"iDisplayLength": 10,
		"processing": true,
		"serverSide": true,
		"responsive":true,
		"order": [[ 4, "desc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings scheduled."
			},
		"ajax":"ajax_lessons_all.php?enrl_status=1",
		
		columns: [  
					
					{ "defaultContent":""}, 
					
					{ "defaultContent":"" }, 
					{ "defaultContent":"" }, 
					{ className: "hidden-phone hidden-tablet","defaultContent":"" }, 
					{ className: "center hidden-phone hidden-tablet","defaultContent":""},
					{"defaultContent":"" },
					{"defaultContent":"" },
					{ "defaultContent":""}
					
				],
		"dom": '<"top"lp>rt<"bottom"ip><"clear">',
		"bAutoWidth": false
		
	});

var booked_lesson =  $('#lessbooked').DataTable( {
		"iDisplayLength": 10,
		"processing": true,
		"serverSide": true,
		"responsive":true,
		"order": [[ 5, "asc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings booked."
			},
		"ajax":"ajax_lessons_all.php?enrl_status=2",
	    columns: [  
					{ "defaultContent":""}, 
					
					{ "defaultContent":""}, 
					{ className: "hidden-phone hidden-tablet","defaultContent":"" }, 
					{ "defaultContent":"" }, 
					{ className: "hidden-phone hidden-tablet","defaultContent":"" }, 
					{ className: "center hidden-phone hidden-tablet","defaultContent":""},
					{className: "hidden-phone hidden-tablet","defaultContent":"" },
					{ "defaultContent":""},
					{ "defaultContent":""},
					{ "defaultContent":""}
					
					
				],
		"dom": '<"top"lp>rt<"bottom"ip><"clear">',
		"bAutoWidth": false
		
	});
	var completed_lesson =  $('#lesscompleted').DataTable( {
		"iDisplayLength": 10,	
		"processing": true,
		"serverSide": true,
		"responsive":true,
		"order": [[ 5, "desc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings completed."
			},
		"ajax":"ajax_lessons_all.php?enrl_status=3",
		columns: [  
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					
					{ "defaultContent":"" }, 
					{ className: "hidden-phone hidden-tablet","defaultContent":"" }, 
					{ className: "hidden-phone hidden-tablet","defaultContent":"" }, 
					{ className: "center hidden-phone hidden-tablet","defaultContent":"" },
					{ "defaultContent":"" },
					{ "bSortable": false,"defaultContent":"" },
					{ "bSortable": false,"defaultContent":"" }
				],
		"dom": '<"top"lp>rt<"bottom"ip><"clear">',
		"bAutoWidth": false
		
	});
	var cancelled_lesson =  $('#lesscancelled').DataTable( {
		"iDisplayLength": 10,
		"processing": true,
		"serverSide": true,
		"responsive":true,
		"order": [[4, "desc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings cancelled."
			},
		"ajax":"ajax_lessons_all.php?enrl_status=4",
		columns: [  
					
					{ "defaultContent":""}, 
					{"defaultContent":""}, 
					
					{ "defaultContent":"" }, 
					{ "defaultContent":"" }, 
					{ className: "center hidden-phone hidden-tablet","defaultContent":""},
					{ "defaultContent":"" },
					{ "defaultContent":"" },
					
					
				],
		"dom": '<"top"lp>rt<"bottom"ip><"clear">',
		"bAutoWidth": false
		
	});
	var expired_lesson =  $('#lessexpired').DataTable( {
		"iDisplayLength": 10,
		"processing": true,
		"serverSide": true,
		"responsive":true,
		"order": [[ 4, "desc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings expired"
			},
		"ajax":"ajax_lessons_all.php?enrl_status=5",
		columns: [  
					
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					
					{ "defaultContent":"" }, 
					{ "defaultContent":"" }, 
					{ className: "center hidden-phone hidden-tablet","defaultContent":""},
					{ "defaultContent":""},
					{"defaultContent":""}
					
					
				],
		"dom": '<"top"lp>rt<"bottom"ip><"clear">',
		"bAutoWidth": false
		
	});
	var completed_lesson_rev =  $('#lesscompleted_rev').DataTable( {
		"iDisplayLength": 10,	
		"processing": true,
		"serverSide": true,
		"responsive":true,
		"order": [[ 3, "desc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings completed."
			},
		"ajax":"ajax_lessons_all_month.php?enrl_status=3",
		columns: [  
					
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					{ "defaultContent":"" }, 
					{ "defaultContent":"" }, 
					{ className: "center hidden-phone hidden-tablet","defaultContent":"" },
					{ "defaultContent":"" },
					{ "bSortable": false,"defaultContent":"" },
					{ "bSortable": false,"defaultContent":"" }
				],
		"dom": '<"top"lp>rt<"bottom"ip><"clear">',
		"bAutoWidth": false
		
	});

</script>
<link href="https://vjs.zencdn.net/4.12/video-js.css" rel="stylesheet">
<script src="https://vjs.zencdn.net/4.12/video.js"></script>
<script type="text/javascript">
   function getLessons(page,enrl_status)
			{
			//alert(enrl_status);
			if(enrl_status!=1)
			{
				$("#scheduled_1").attr("class","tab-pane");
			}
			var user_id="";
			var time_format_id="";
			var user_code="";
			var find_date_ser="";
			var end_date_ser="";
			find_date_ser=$("#st_date").val();
			end_date_ser=$("#ed_date").val();
			
			//find_date_ser=$("#finddate").val();
			user_id=$("#user_id").val();
			time_format_id=$("#time_format_id").val();
			user_code=$("#user_code").val();
			var ser_string="";
			 ser_string=$("#searchbox_professionals").val();
			if(enrl_status==1)
			{
				scheduled_lesson.ajax.url( 'ajax_lessons_all.php?enrl_status=1&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code+"&start_date="+find_date_ser+"&start_date_ed="+end_date_ser+"&ser_string="+ser_string).load();
				
			}
			else if(enrl_status==2)
			{
				booked_lesson.ajax.url( 'ajax_lessons_all.php?enrl_status=2&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code+"&start_date="+find_date_ser+"&start_date_ed="+end_date_ser+"&ser_string="+ser_string).load();
				
			}
			else if(enrl_status==3)
			{
				completed_lesson.ajax.url( 'ajax_lessons_all.php?enrl_status=3&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code+"&end_date="+find_date_ser+"&end_date_ed="+end_date_ser+"&ser_string="+ser_string).load();
			}
			else if(enrl_status==4)
			{
				cancelled_lesson.ajax.url( 'ajax_lessons_all.php?enrl_status=4&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code+"&start_date="+find_date_ser+"&start_date_ed="+end_date_ser+"&ser_string="+ser_string ).load();
			}
			else if(enrl_status==5)
			{
				expired_lesson.ajax.url( 'ajax_lessons_all.php?enrl_status=5&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code+"&start_date="+find_date_ser+"&start_date_ed="+end_date_ser+"&ser_string="+ser_string).load();
			}
			
				 
			}
	function getLessonsbymonth(page,enrl_status)
	{
		var user_id="";
			var time_format_id="";
			var user_code="";
			var month_rev="";
			var year_rev="";
			month_rev=$("#month_rev").val();
			year_rev=$("#year_rev").val();
			user_id=$("#user_id").val();
			time_format_id=$("#time_format_id").val();
			user_code=$("#user_code").val();
			completed_lesson_rev.ajax.url( 'ajax_lessons_all_month.php?enrl_status=3&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code+"&month="+month_rev+"&year="+year_rev ).load();
	}
function setPagination(perpage,total)
			{	
						
				$('#tab-content .active .pagination').pagination({
					items: total,
					itemsOnPage: perpage,
					onPageClick: function(pageNumber, event)
					{
						var type=jQuery("#tab-01 li.active").attr("id");
						getLessons(pageNumber,type);
					}
				});
				
			}			

function gotowait(rel)
{
	var str = rel;
	var n	= str.split("/");
	$("#userCodeWR").val(n[0]);
	$("#userIdWR").val(n[1]);
	$("#ccodeIdWR").val(n[2]);
	$("#actionIdWR").val(n[3]);
	$("#course_type_id").val(n[4]);
	$("#enterwaitingroomOT").submit();
	
}
function gotolive(rel)
{
	var str = rel;
	var n	= str.split("/");
	$("#userCodeOT").val(n[0]);
	
	$("#userIdOT").val(n[1]);
	
	$("#ccodeIdOT").val(n[2]);
	
	$("#actionIdOT").val(n[3]);
	
	$("#enterliveClassOT").submit();
	
}

function gotogroup(rel)
{
	var str = rel;
	var n	= str.split("/");
		$("#userCodeGroupOT").val(n[0]);
	$("#userIdGroupOT").val(n[1]);
	$("#ccodeIdGroupOT").val(n[2]);
	$("#actionIdGroupOT").val(n[3]);
	$("#enterliveClassGroupOT").submit();
}

function setnotesData(ccode)
	{
	   
		
		$.ajax({
			   type	: "GET",
			   url	: "ajaxNotes.php?course_id="+ccode,
			  
			  
			   success: function(result){
			  
			   var output = "";
				output=result;
				$("#form-content .modal-body").html(output);
				$("#form-content").modal('show');
				}
				
			 });
				
	}
	function setstudentData(ccode)
	{
	   
		
		$.ajax({
			   type	: "GET",
			   url	: "../liveroom/ajax_users_enrolled.php",
			   data	: "course_id="+ccode,
			   dataType: "json",
			   success: function(result){
			  
			   var output = "";
				
				
				$("#students_name .modal-body .stuname").html(result);
				$("#students_name").modal('show');
				}
				
			 });
				
	}
	function uploadmusic(ccid)
	{
	   $("#ccid").val(ccid);
	   $("#music_sheet_upload").modal('show');
	   
	}
	function viewvideos(ccode)
	{
	  
	    $.ajax({
			   type	: "GET",
			   url	: "../liveroom/ajax_view_lesson.php",
			   data	: "cccode="+ccode,
			   dataType: "json",
			   success: function(result){
			  
			   var output = "";
				
				
				$("#videos .modal-body").html(result);
				$("#videos").modal('show');
				}
				
			 });
	}
	
	
</script>
<link href="https://vjs.zencdn.net/4.12/video-js.css" rel="stylesheet">
<script src="https://vjs.zencdn.net/4.12/video.js"></script>
