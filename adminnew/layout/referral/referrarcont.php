
 
<!DOCTYPE html>
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
									<h4><b>Referrers</b></h4>
								</span>
								
						</div>
						<div class="grid-body no-border">
						        <div >
								   <form action="addreferrals.php" name="instrument" method="post"  >
								        <input type="hidden" name="user_id" value="<?php echo $referrer_det[0]['user_id'];?>">
										<input type="hidden" name="login_id" value="<?php echo $referrer_det[0]['login_id'];?>">
								        <div class="row" >
										    <label class="col-xs-9 form-label">First Name</label>
											<div class="col-xs-4 input-with-icon  right">
												<input type="text" name="firstname" value="<?php echo $referrer_det[0]['first_name'];?>" class="text form-control" id="firstname" >
											</div>
											
										</div>
										<div class="spacer-single-form"></div>
										<div class="row" >
										    <label class="col-xs-9 form-label">Last Name</label>
											<div class="col-xs-4">
												<input type="text" name="lastname" value="<?php echo $referrer_det[0]['last_name'];?>" class="text form-control" id="lastname" >
											</div>
										</div>
										<div class="spacer-single-form"></div>
										<div class="row" >
										    <label class="col-xs-9 form-label">Email Address</label>
											<div class="col-xs-4">
												<input type="text" name="email" value="<?php echo $referrer_det[0]['user_name'];?>" class="text form-control" id="email" >
											</div>
										</div>
										<div class="spacer-single-form"></div>
										<div class="row" >
										    <label class="col-xs-9 form-label">Password</label>
											<div class="col-xs-4">
												<input type="password" name="password" value="" class="text form-control" id="password" >
											</div>
										</div>
										<div class="spacer-single-form"></div>
										
										<div class="" style="margin-top:15px !important;">
										    <button class="btn btn-warning btn-cons" value="configure" name="configure" type="submit">Add</button>
										</div>
								   </form>
								</div>
								<div>
								   
									
										 
										  
										  
											  <div class="row row_less" >
											   <div class="col-md-12" style="">
												<table class="dataTable display" id="lessscheduled" >
													<tbody>
													<thead>
													<tr>
															
															
															<th>NAME</th>
															<th>EMAIL</th>
															
															<th>JOIN DATE</th>
															
															<th></th>
															
													</tr>
														</thead>
											   </table>	
												
											</div>
											</div>
											<div class="clearfix">&nbsp;</div>
												
											
											
											
										
											
											
											
								</div>
								
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
.spacer-single-form {
    clear: both;
    display: block;
    height: 15px;
    width: 100%;
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
  <?php include('layout/footer/reffooter.php');?>
  <script type="text/javascript" src="../classroom/css/datatable/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="../classroom/css/datatable/css/jquery.dataTables.css" />
<script src="../classroom/lmtboot/assets/js/tabs_accordian.js" type="text/javascript"></script>
<script src="./classroom/lmtboot/assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../liveroom/css/datatable/media/css/dataTables.responsive.css">
		<script type="text/javascript" src="../liveroom/css/datatable/media/js/dataTables.responsive.js" charset="UTF-8"></script>
<script>
$(document).ready(function() {
	//getLessons(1,1) ;
	$("#instuname").autocomplete({
			  source: function(request, response) {
				$.ajax({				  
				  url: "ajax_intructor_names.php",
				  dataType: "json",
				  data: request,                    
				  success: function (data) {
					// No matching result
					if (data == null) {
					  //alert('No entries found!');
					   $("#instuname").val('');
					  
					  $("#instuname").autocomplete("close");
					}
					else {
					  response(data);
					}
				  }});
				},
				
			  dataType: "json",
			  autoFill: false,      
			  scroll: false,
			  minLength: 2,
			  cache: false,
			  width: 100,
			  delay: 500,           
			  select: function(event, ui) { 
			  $("#ins_id_hid").val('');
			   $("#ins_id_hid").val(ui.item.id);
			  $.ajax({
				 url:"ajax_instruments_user.php?ins_id="+ui.item.id,
				 success: function(data)
				 {
					 $("#instrument").empty();
					 $("#instrument").append(data);
				 }				 
                 				 
			  });
				//eventuallydosomething(ui.item.value);
				$("#instuname").autocomplete("close");
			  } 
			});
			
			
			/*$("#duration").on("change", function(){
			$.ajax({
				 url:"ajax_price_list.php?duration="+$(this).val(),
				 success: function(data)
				 {
					 var split_data=data.split(",");
					 $("#fee_dis").empty();
					 $("#fee_dis").append("$"+split_data[1]);
					 $("#fee_id").val('');
					  $("#fee_id").val(split_data[0]);
					 $("#instrument").append(data);
				 }				 
                 				 
			  });
		});*/
		$("#fee_id").focusout(function() {
			var prices = $("#fee_id").val();
			prices = parseFloat(prices);
			prices=prices.toFixed(2);
			$("#fee_id").val(prices);
	
			});
});

var scheduled_lesson =  $('#lessscheduled').DataTable( {
		"iDisplayLength": 10,
		"processing": true,
		"serverSide": true,
		"responsive":true,
		"order": [[ 2, "desc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any referrars."
			},
		"ajax":"ajax_referrers_list.php",
		
		columns: [  
					
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					{ "defaultContent":""}
					
					
					
				],
		"dom": '<"top"lp>rt<"bottom"ip><"clear">',
		"bAutoWidth": false
		
	});


	var completed_lesson_rev =  $('#lesscompleted_rev').DataTable( {
		"iDisplayLength": 10,	
		"processing": true,
		"serverSide": true,
		"order": [[ 3, "desc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings completed."
			},
		"ajax":"ajax_lessons_all_master_month.php?enrl_status=3",
		columns: [  
					
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
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
			user_id=$("#user_id").val();
			time_format_id=$("#time_format_id").val();
			user_code=$("#user_code").val();
			if(enrl_status==1)
			{
				scheduled_lesson.ajax.url( 'ajax_lessons_all_master.php?enrl_status=1&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code).load();
				
			}
			else if(enrl_status==2)
			{
				booked_lesson.ajax.url( 'ajax_lessons_all_master.php?enrl_status=2&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code ).load();
				
			}
			else if(enrl_status==3)
			{
				completed_lesson.ajax.url( 'ajax_lessons_all_master.php?enrl_status=3&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code ).load();
			}
			else if(enrl_status==4)
			{
				cancelled_lesson.ajax.url( 'ajax_lessons_all_master.php?enrl_status=4&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code ).load();
			}
			else if(enrl_status==5)
			{
				expired_lesson.ajax.url( 'ajax_lessons_all_master.php?enrl_status=5&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code ).load();
			}
			
				 
			}
		function validate_inst()
        {
			
			var instuname=$("#instuname").val();
			
            var instrument=$("#instrument").val();
            var duration=$("#duration").val();
            var fee_id=$("#fee_id").val();
            var date_added=$("#date_added").val();
            var hour=$("#hour").val();		
            var min=$("#min").val();
			var level=$("#level").val();
			var msg="";
            if(instuname=="")
            {
				msg="Please Enter Professional Name.\n";
				
			}	
            if(instrument==""||instrument=="0")
            {
				msg+="Please Enter Instrument Name. \n";
			}
			 if(duration==""||duration=="0")
            {
				msg+="Please select the Duration. \n";
			}
			if(level==""||level=="0")
			{
				msg+="Please select the Level. \n";
			}
			if(fee_id=="")
            {
				msg+="Please enter the Fees. \n";
			}
			if(date_added=="")
            {
				msg+="Please select date.\n";
			}
			if(hour==""||hour=="0")
            {
				msg+="Please select hour.\n";
			}
			if(min==""||min=="-1")
            {
				msg+="Please select minutes.\n";
			}
			if(msg!="")
			{
				alert(msg);
				return false;
			}
			else
			{
				return true;
			}
			
		}
       function end_lesson(course_id,inst_id)
	   {
		   $.ajax({
				 url:"ajax_end_less.php?course_id="+course_id+"&inst_id="+inst_id,
				 success: function(data)
				 {
					alert("Lesson Ended Successfully");
					window.location="masterlesson.php";
				 }				 
                 				 
			  });
	   }	   
</script>