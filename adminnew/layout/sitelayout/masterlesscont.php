<?php
  //time calculation for edit course_details
  if($course_details[0]['start_time']!="")
  {
	  $explode_time=explode(":",$course_details[0]['start_time']);
	  if($explode_time[0]>12)
	  {
		  $hours=($explode_time[0]%12);
		  if($hours>0 && $hours<=9)
		  {
			  $hours="0".$hours;
		  }
		  $mins=$explode_time[1];
		  $amorpms="pm";
	  }
	  else
	  {
		  $hours=$explode_time[0];
		  $mins=$explode_time[1];
		  $amorpms="am";
	  }
	  
  }
?>
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
									<h4><b>Master Meetings</b></h4>
								</span>
								
						</div>
						<div class="grid-body no-border">
						        <div >
								   <form action="masterlesson.php" name="instrument" method="post" onsubmit="return validate_inst();" >
								        <div class="row" >
										    <label class="col-xs-9 form-label">Professional Name</label>
											<div class="col-xs-4 input-with-icon  right">
												<input type="text" name="instuname" value="<?php echo $course_details[0]['first_name'].' '.$course_details[0]['last_name'];?>" class="text form-control" id="instuname" >
											</div>
											<input type="hidden" name="ins_id_hid" id="ins_id_hid" value="<?php echo $course_details[0]['user_id']?>">
											<input type="hidden" name="course_id" id="course_id" value="<?php echo $course_details[0]['course_id']?>">
											<input type="hidden" name="price_id" id="price_id" value="<?php echo $course_details[0]['price_code']?>">
										</div>
										<div class="spacer-single-form"></div>
										<div class="row" >
										    <label class="col-xs-9 form-label">Category</label>
											<div class="col-xs-4">
												<select name="instrument" id="instrument" class="form-control">
												    <option value="0">Select Category</option>
													<?php if(count($instruments)>0){
														   foreach($instruments as $instrument)
															{?>
																<option value='<?php echo $instrument['instrument_id']?>'  <?php if($instrument['instrument_id']==$course_details[0]['instrument_id']) echo "selected"?> ><?php echo $instrument['name']?></option>
													   <?php } ?>

													<?php }?>
													
												</select>
											</div>
										</div>
										<div class="spacer-single-form"></div>
										<div class="row" >
										    <label class="col-xs-9 form-label">Level</label>
											<div class="col-xs-4">
												<select name="level" id="level" class="form-control">
												    <option value="0">Select Level</option>
													 <option value="1" <?php if ($course_details[0]['course_type_level']==1) echo "selected" ?> >Children</option>
													 <option value="2" <?php if ($course_details[0]['course_type_level']==2) echo "selected" ?> >Beginner</option>
													 <option value="3" <?php if ($course_details[0]['course_type_level']==3) echo "selected" ?> >Intermediate</option>
													 <option value="4" <?php if ($course_details[0]['course_type_level']==4) echo "selected" ?>>Advanced</option>
												</select>
											</div>
										</div>
										<div class="spacer-single-form"></div>
										<div class="row" >
										    <label class="col-xs-9 form-label">Duration</label>
											<div class="col-xs-4">
												<select name="duration" id="duration" class="form-control">
												    <option value="0">Select Duration</option>
													<?php foreach($durations as $duration){?>
													 <option value="<?php echo $duration['id']?>"  <?php if ($course_details[0]['duration']==$duration['id']) echo "selected" ?> ><?php echo $duration['time']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="spacer-single-form"></div>
										<div class="row" >
										     <label class="col-xs-9 form-label">Fees</label>
											 <div class="col-xs-4  input-group " id="fee_dis" style="padding-left: 15px;">
											    <span class="input-group-addon">$</span>
											    <input type="text" name="fee_id" id="fee_id" value="<?php echo $course_details[0]['cost']?>" >
											 </div>
											
											 <input type="hidden" name="min_stu" id="min_stu" value="<?php echo $min_max[0]['min_students']?>" >
											 <input type="hidden" name="max_stu" id="max_stu" value="<?php echo $min_max[0]['max_students']?>" >
										</div>
										<div class="spacer-single-form"></div>
										<div class="row">
											<div class="form-group">
												<label class="col-xs-10 form-label">Date</label>
													<div class="col-xs-9">
														<div class="input-append success date col-md-10 col-lg-6 no-padding">
										       
												          <input type="text" class="form-control" value="<?php  if($course_details[0]['start_date']!="") echo date("m/d/Y",strtotime($course_details[0]['start_date']));?>" name="date_added" id="date_added">
												
												            <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
										                 </div>
									                 </div> 
									         </div>
								        </div>
										<div class="spacer-single-form"></div>
										<div class="row" >
										    <label class="col-xs-9 form-label">Time</label>
											<div class="col-xs-9">
												<select name="hour" id="hour" class="col-md-3" style="margin-right:10px !important;">
												    <option value="0">Select Hour</option>
													<?php for($i=1;$i<=12;$i++){
														$hrt=$i;
														if($i<=9){
															$hrt="0".$i;
														}
														?>
													    
													 <option value="<?php echo $i?>" <?php if($hours==$hrt) echo "selected"?>><?php echo $hrt?></option>
													<?php } ?>
												</select>
												&nbsp;&nbsp;
												<select name="min" id="min" class="col-md-2" style="margin-right:10px !important">
												    <option value="-1">Select Min</option>
													<?php for($j=0;$j<=55;$j+=5){
														$mt=$j;
														if($j<=5){
															$mt="0".$j;
														}
														?>
													    
													 <option value="<?php echo $j?>" <?php if($mins==$mt) echo "selected"?>><?php echo $mt?></option>
													<?php } ?>
												</select>
												<select name="amorpm" id="amorpm" class="col-md-2">
												    <option value="am" <?php if($amorpms=="am") echo "selected"?>>AM</option>
													<option value="pm" <?php if($amorpms=="pm") echo "selected"?> >PM</option>
												</select>
											</div>
										</div>
										<div class="spacer-single-form"></div>
										<div class="row" >
										    <label class="col-xs-9 form-label">Channel Link</label>
											<div class="col-xs-4 ">
												<input type="text" name="channel_link" value="<?php echo $course_details[0]['channel_link'];?>" class="text form-control" id="channel_link" >
											</div>
										</div>	
										<div class="" style="margin-top:15px !important;">
										    <button class="btn btn-warning btn-cons" value="configure" name="configure" type="submit">Configure</button>
										</div>
								   </form>
								</div>
								<div>
								    <h4><b>Meetings</b></h4>
									
										 
										  <input type="hidden" name="user_code" id="user_code" value="<?php echo $user_det[0]['user_code']?>">
										   <input type="hidden" name="time_format_id" id="time_format_id" value="<?php echo $user_det[0]['time_format_id']?>">
										  
											  <div class="row row_less" >
											   <div class="col-md-12" style="">
												<table class="dataTable display" id="lessscheduled" >
													<tbody>
													<thead>
													<tr>
															
															
															<th>NAME</th>
															<th>CATEGORY</th>
															
															<th>DATE</th>
															<th>TIME</th>
															<th>DURATION</th>
															<th>COST</th>
															<th>STATUS</th>
															<th></th>
															<th></th>
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
  <?php include('layout/footer/masterfooter.php');?>
  <script type="text/javascript" src="../classroom/css/datatable/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="../classroom/css/datatable/css/jquery.dataTables.css" />
<script src="../classroom/lmtboot/assets/js/tabs_accordian.js" type="text/javascript"></script>
<script src="./classroom/lmtboot/assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../liveroom/css/datatable/media/css/dataTables.responsive.css">
		<script type="text/javascript" src="../liveroom/css/datatable/media/js/dataTables.responsive.js" charset="UTF-8"></script>
<script>
$(document).ready(function() {
	getLessons(1,1) ;
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
			"sZeroRecords": "You currently don't have any meetings scheduled."
			},
		"ajax":"ajax_lessons_all_master.php?enrl_status=1",
		
		columns: [  
					
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					{ className: "hidden-phone hidden-tablet","defaultContent":"" }, 
					{ className: "hidden-phone hidden-tablet","defaultContent":"" }, 
					{ className: "center hidden-phone hidden-tablet","defaultContent":""},
					{"defaultContent":"" },
					{"defaultContent":"" },
					{"defaultContent":"" },
					{"defaultContent":"" },
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