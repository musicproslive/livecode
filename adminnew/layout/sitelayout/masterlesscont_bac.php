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
									<h4><b>Master Lesson</b></h4>
								</span>
								
						</div>
						<div class="grid-body no-border">
						        <div >
								   <form action="masterlesson.php" name="instrument" method="post" onsubmit="return validate_inst();" >
								        <div class="row" >
										    <label class="col-xs-9 form-label">Instructor Name</label>
											<div class="col-xs-4">
												<input type="text" name="instuname" value="" class="text form-control" id="instuname" >
											</div>
										</div>
										<div class="spacer-single-form"></div>
										<div class="row" >
										    <label class="col-xs-9 form-label">Instrument</label>
											<div class="col-xs-4">
												<select name="instrument" id="instrument" class="form-control">
												    <option value="0">Select Instrument</option>
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
													 <option value="<?php echo $duration['id']?>"><?php echo $duration['time']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="spacer-single-form"></div>
										<div class="row">
											<div class="form-group">
												<label class="col-xs-10 form-label">Date:</label>
													<div class="col-xs-9">
														<div class="input-append success date col-md-10 col-lg-6 no-padding">
										       
												          <input type="text" class="form-control" value="" name="date_added">
												
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
													    
													 <option value="<?php echo $i?>"><?php echo $hrt?></option>
													<?php } ?>
												</select>
												&nbsp;&nbsp;
												<select name="min" id="min" class="col-md-2" style="margin-right:10px !important">
												    <option value="0">Select Min</option>
													<?php for($j=0;$j<=50;$j+=10){
														$mins=$j;
														if($j<=0){
															$mt="0".$j;
														}
														?>
													    
													 <option value="<?php echo $j?>"><?php echo $mt?></option>
													<?php } ?>
												</select>
												<select name="amorpm" id="amorpm" class="col-md-2">
												    <option value="am">AM</option>
													<option value="pm">PM</option>
												</select>
											</div>
										</div>
										
										<div class="" style="margin-top:15px !important;">
										    <button class="btn btn-warning btn-cons" value="configure" name="configure" type="submit">Configure</button>
										</div>
								   </form>
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
 background-color: #f0ad4e !important;
    border-color: #eea236 !important;
    color: #fff !important;
/*
	background-color: #2c3e50 !important;
    color: #f1c40f !important;*/
}
.current {
background-color: #f0ad4e !important;;
    border-color: #eea236 !important;;
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
</style>	
  <?php include('layout/footer/masterfooter.php');?>