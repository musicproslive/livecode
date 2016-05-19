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
				<link href="../lmttheme/css/location.css" rel="stylesheet" type="text/css" />
			   <div class="page-content">
       <div class="content">
      
				 <div class="row">
				 <form method="post" name="instructor" action="save_inst.php">
				 <input type="hidden" name="user_id" value="<?php echo $user_det[0]['userid']?>" id="user_id">
				 <div class="grid simple">
						<div class="grid-title no-border">
								<span class="semi-bold">
									<h4><b>View Professional</b></h4>
								</span>
								
						</div>
						<div class="grid-body no-border">
							<div class="row">
							   <ul class="nav nav-tabs" id="tab-01">
									<li class="active" id="1"><a href="#account_info">Account Info</a></li>
									<li id="less_tab"><a href="#lessons">Meetings</a></li>
									<li id="bill_tab" onclick="javascript:getLessonsbymonth(1,3);"><a href="#billing">Revenue</a></li>
									<li id="4"><a href="#welcome">Welcome Video</a></li>
									<!--<li id="5"><a href="#profile_pic">Profile Picture</a></li>-->
									<li id="5"><a href="#instruments">Categories</a></li>
									<li id="5"><a href="#documents">Documents</a></li>
									<li id="5"><a href="#upload">Upload Documents</a></li>
								</ul>
								<div class="tab-content" id="tab-content">
								     <div class="tab-pane active" id="account_info">
									    <div class="col-md-6">
											 <div class="grid simple">
												<div class="grid-title no-border">
													<span class="semi-bold">
														<h4><b>Basic Information</b></h4>
													</span>
									                <div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">First Name</label>
                                                            <div class="col-xs-8">
															   <input id="txtFirstName" class="text form-control" type="text" value="<?php echo $user_det[0]['first_name'];?>" name="txtFirstName">
															</div>															
														 </div>	
												    </div>
													<div class="spacer-single-form"></div> 
													 <div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">Last Name</label>
                                                            <div class="col-xs-8">
															   <input id="txtLastName" class="text form-control" type="text" value="<?php echo $user_det[0]['last_name'];?>" name="txtLastName">
															</div>															
														 </div>	
												    </div>
														<div class="spacer-single-form"></div> 
													 <div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">Date of Birth</label>
                                                            <div class="col-xs-8">
															   <input id="" class="text form-control" type="text" value="<?php echo $user_det[0]['dobs'];?>" name="dob">
															</div>															
														 </div>	
												    </div>
														<div class="spacer-single-form"></div> 
													 <div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">Gender</label>
                                                            <div class="col-xs-8">
																<div class="col-xs-1">
															   <input type="radio" id="radio1" name="gender" value="M" class="radio" <?php if($user_det[0]['gender']=='M') echo "checked"?>>
															   </div>
															   <div class="col-xs-2">
															   <span>Male</span>
															   </div>
															   <div class="col-xs-1">
															   
																<input type="radio" id="radio1" name="gender" value="f" class="radio" <?php if($user_det[0]['gender']=='f') echo "checked"?>>
																</div>
																<div class="col-xs-2">
																<span>Female</span>
																</div>
															</div>															
														 </div>	
												    </div>
														<div class="spacer-single-form"></div> 
													<div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">Email</label>
                                                            <div class="col-xs-8">
															   <input id="txtUserName" class="text form-control" type="text" value="<?php echo $user_det[0]['user_name'];?>" name="txtUserName">
															</div>															
														 </div>	
												    </div>
														<div class="spacer-single-form"></div> 
													<div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">Phone</label>
                                                            <div class="col-xs-8">
															   <input id="txtPhoneNumber" class="text form-control" type="text" value="<?php if($user_det[0]['phone1']!=""&&$user_det[0]['phone1']!='NULL')echo $user_det[0]['phone1'];?>" name="txtPhoneNumber">
															</div>															
														 </div>	
												    </div>
											
														<div class="spacer-single-form"></div> 
													<div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">College</label>
                                                            <div class="col-xs-8">
															   <input id="college" class="text form-control" type="text" value="<?php if($user_det[0]['college']!=""&&$user_det[0]['college']!='NULL')echo $user_det[0]['college'];?>" name="college">
															</div>															
														 </div>	
												    </div>
														<div class="spacer-single-form"></div> 
													<!--<div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">City</label>
                                                            <div class="col-xs-8">
															   <input id="city" class="text form-control" type="text" value="" name="city">
															</div>															
														 </div>	
												    </div>-->
														<div class="spacer-single-form"></div> 
													<!--<div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">Country</label>
                                                            <div class="col-xs-8">
															    <select id="country" class=" form-control"  value="" name="country">
																<option value="0">Select Country</option>
															   <?php //for($i=0;$i<count($countries);$i++){?>
															     <option value="<?php //echo $countries[$i]['country_id'] ?>"<?php //if($user_det[0]['country_id']==$countries[$i]['country_id']) echo 'selected'?>><?php //echo $countries[$i]['country_name']?></option>
															   <?php //}?>
															   </select>
															   
															</div>															
														 </div>	
												    </div>-->
													<div class="row">
														 <div class=" col-md-7">
															<label >Location</label>
														 </div> 
														 <div class=" col-md-8">
															<input id="pac-input_org" class="form-control" type="text" placeholder="Location" name="org_loc" value="<?php echo  $user_det[0]['org_loc'];?> ">
															
														  </div>
							                        </div>
													
													<div class="spacer-single-form"></div> 
													<!--<div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">Time Zone </label>
                                                            <div class="col-xs-8">
															   <select class="form-control" id="time_zone_id" name="time_zone_id">
															     <option value="0">Select Time Zone</option>
																 
																	<?php //for($i=0;$i<count($timezones);$i++){?>
															     <option value="<?php //echo $timezones[$i]['id'] ?>"<?php //if($user_det[0]['time_zone_id']==$timezones[$i]['id']) echo 'selected'?>><?php //echo $timezones[$i]['timezone_location']."".$timezones[$i]['gmt'];?></option>
															   <?php //}?>
												
																</select>
															</div>															
														 </div>	
												    </div>
													<div class="spacer-single-form"></div> -->
													<div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">Personal URL </label>
                                                            <div class="col-xs-8">
															   <input id="txtPerUrl" class="text form-control" type="text" value="<?php echo $user_det[0]['Vanity_URL'];?>" name="txtPerUrl">
															</div>															
														 </div>	
												    </div>
													<div class="spacer-single-form"></div> 
													
													<div class="row">
													    <div class="form-group">
														    <label class="col-xs-6 form-label">Referred By</label>
                                                            <div class="col-xs-8">
															   <input type="text" name="instuname" value="<?php echo $referrals[0]['name']?>" class="text form-control" id="instuname">
															   <input type="hidden" name="ins_id_hid" id="ins_id_hid" value="<?php //echo $course_details[0]['user_id']?>">
															</div>															
														 </div>
												      </div>
													  <div class="spacer-single-form"></div>
													  <div class="row">
													    <div class="form-group">
														    <label class="col-xs-6 form-label">Referral(%) </label>
                                                            <div class="col-xs-8">
															   <input type="text" name="ref_percent" value="<?php echo $user_det[0]['referral_per']?>" class="text form-control" id="ref_percent">
															   
															</div>															
														 </div>
												      </div>
													  <div class="spacer-single-form"></div>
													<div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">Accepted Date </label>
                                                            <div class="col-xs-8">
															  <?php echo $user_det[0]['acc']?> &nbsp; <button type="submit" name="save" value="reset" id="reset" class="btn btn-warning btn-xs btn-mini">Reset</button>
															</div>															
														 </div>	
												    </div>
													<div class="spacer-single-form"></div> 
												
													<!--<div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">Time Format</label>
                                                            <div class="col-xs-8">
															   <select class="form-control" id="time_format_id" name="time_format_id">
																					<option selected="selected" value="1">Thu Jul 16, 2015 08:54 AM</option>
																					<option value="4">Thu 16th Jul 2015 08:54 AM</option>
																					<option value="5">Thursday 16th July 2015 08:54 AM</option>
																					<option value="6">16/07/2015 08:54 AM</option>
											
																</select>
															</div>															
														 </div>	
												    </div>
													<div class="spacer-single-form"></div> -->
												</div>
												<div class="grid-body no-border">
												  
												</div>
											</div> 
										</div>	
										 <div class="col-md-6">
											 <div class="grid simple">
												<div class="grid-title no-border">
													<span class="semi-bold">
														<h4><b>Optional Information</b></h4>
													</span>
									                <div class="row">
													     <div class="form-group">
														    <label class="col-xs-6 form-label">About me</label>
                                                            <div class="col-xs-12">
															   <textarea id="txtAboutMe" class="form-control" name="txtAboutMe" rows="10"><?php echo $user_det[0]['about_me'];?></textarea>
															   <br>
															   <p>You have <span id="counter"></span> characters left.</p>
															</div>	
															 
														 </div>	
												    </div>
													<div class="row">
													     <div class="form-group">
								
																<div style="margin-top: 16px ! important; margin-left: 16px;" class="col-xs-12 input-group">					
										
																	<span class="input-group-addon warning">
																	<i class="fa fa-facebook"></i>
																	</span> 
																	<input type="text" value="<?php if($user_det[0]['facebook']!=""&&$user_det[0]['facebook']!='NULL') echo $user_det[0]['facebook']?>" placeholder="http://facebook.com" id="txtsocial0" class="form-control" name="txtsocialfb">
																</div>
														  </div>
												    </div>
													
													<div class="row">
													     <div class="form-group">
								
																<div style="margin-top: 16px ! important; margin-left: 16px;" class="col-xs-12 input-group">					
										
																	<span class="input-group-addon warning">
																	<i class="fa fa-twitter"></i>
																	</span> 
																	<input type="text" value="<?php if($user_det[0]['twitter']!=""&&$user_det[0]['twitter']!='NULL') echo $user_det[0]['twitter']?>" placeholder="http://twitter.com" id="txtsocial0" class="form-control" name="txtsocialtw">
																</div>
														  </div>
												    </div>
													
                                                        <div class="row">
													     <div class="form-group">
								
																<div style="margin-top: 16px ! important; margin-left: 16px;" class="col-xs-12 input-group">					
										
																	<span class="input-group-addon warning">
																	<i class="fa fa-youtube"></i>
																	</span> 
																	<input type="text" value="<?php if($user_det[0]['youtube_channel']!=""&&$user_det[0]['youtube_channel']!='NULL') echo $user_det[0]['youtube_channel']?>" placeholder="http://www.youtube.com" id="txtsocial0" class="form-control" name="txtsocialyu">
																</div>
														  </div>
												      </div>
													
													<div class="row">
													     <div class="form-group">
								
																<div style="margin-top: 16px ! important; margin-left: 16px;" class="col-xs-12 input-group">					
										
																	<span class="input-group-addon warning">
																	<i class="fa fa-linkedin-square"></i>
																	</span> 
																	<input type="text" value="<?php if($user_det[0]['linkedin']!=""&&$user_det[0]['linkedin']!='NULL') echo $user_det[0]['linkedin']?>" placeholder="http://www.linkedin.com" id="txtsocial0" class="form-control" name="txtsocialli">
																</div>
														  </div>
												      </div>
													
													<div class="row">
													     <div class="form-group">
								
																<div style="margin-top: 16px ! important; margin-left: 16px;" class="col-xs-12 input-group">					
										
																	<span class="input-group-addon warning">
																	Other
																	</span> 
																	<input type="text" value="<?php if($user_det[0]['url']!=""&&$user_det[0]['url']!='NULL') echo $user_det[0]['url']?>" placeholder="http://example-site.com" id="txtsocial0" class="form-control" name="txtsocialot">
																</div>
														  </div>
												      </div>
													
													<div class="spacer-single-form"></div>												
												</div>
												<div class="grid-body no-border">
												  
												</div>
											</div> 
										</div>	
									 </div> 
									 <div class="tab-pane " id="billing">
									     <div>
											
												 <div class="col-md-3">
												  <?php echo $months_select; ?>
												 </div>
												 <div class="col-md-3">
													<?php echo $years_select ?>
												 </div>
												 <div class="col-md-3">
													<button  type="button" value="Submit" class="btn btn-warning btn-cons" id="findless"  > Submit</button>
												 </div>
											
										</div>
										<br />
									    <table class="dataTable display" id="lesscompleted_rev" >
													<tbody>
													<thead>
														<tr>
															
															<th>MEMBERS</th>
															<!--<th>CATEGORY</th>-->
															<th>MEETING TYPE</th>
															<!--<th>COST</th>-->
															<th>DATE</th>
															<th>TIME</th>
															<th>DURATION</th>
															<!--<th align="left">Music</th>-->
															<th>COST</th>
															<th>PAYOUT</th>
															
															
														</tr>
													</thead>
													</table>
									 </div>
									 <div class="tab-pane " id="lessons">
										<div class="col-md-12">
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
															
															
															
															<!--<th>CATEGORY</th>-->
															<th>MEETING TYPE</th>
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
															
															
															<th>MEMBERS</th>
															<!--<th>CATEGORY</th>-->
															<th>MEETING TYPE</th>
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
															
															<th>MEMBERS</th>
															<!--<th>CATEGORY</th>-->
															<th>MEETING TYPE</th>
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
															
															
															
															<!--<th>CATEGORY</th>-->
															<th>MEETING TYPE</th>
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
															
															
															
															<!--<th>CATEGORY</th>-->
															<th>MEETING TYPE</th>
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
										</div>
									   
									 </div>
									 <div class="tab-pane " id="welcome">
									        <div class="form-group">
													<p for="Instruments" class=" form-label">To add a video to your profile, upload a video on YouTube. Afterwards, go to the video link and copy and paste the last part of the link in the box, as shown below. The video will appear on your TalkMusik profile page.</p>
														<br>
														<img src="../classroom/images/YouTubeInstructions.jpg">
											</div>
											<div class="form-group">
												     <div class="col-xs-12 input-group">
													    
													     <input type="text" name="welcome_video" class="form-control" id="welcome_video" placeholder="Welcome Video" value="<?php if($user_det[0]['welcome_video_link']!=""&&$user_det[0]['welcome_video_link']!='NULL') echo $user_det[0]['welcome_video_link']?>">
													 </div>
												</div>
									    
									 </div>
									  <div class="tab-pane " id="instruments">
									       <div class="col-md-12">
											    
					
			
													<div class="control-group control-group  checkboxGroup advanced-agt panel panel-default">
											  
															<div class="controls">
																
																<table  width="25%">
																<thead>
																<tr>
																	<th>CATEGORY </th>
																	
																	
																	<th> </th>
																	<!--<th>Level 2 <br> Beginner </th>
																	<th>Level 3 <br> Intermediate  </th>
																	<th>Level 4 <br> Advanced  </th>-->
																	<!--<th>Level 5 <br> Expert </th>-->
																</tr>	
																	
																</thead>
																
																<tbody>
														
															
															       <?php 
																       foreach($all_ins_listed as $all_ins){?>
																	     <tr style="border-top: solid 1px #ddd;">
																          <td><?php echo $all_ins['name']?></td>
																		   <td>	
																				<input type="checkbox" name="levs[]" id="" class="" value="1_<?php echo $all_ins['instrument_id']?> " <?php if ($all_ins['sel']=="t" ) echo 'checked="checked"' ?> />
																			</td>
																			  <!--<td>	
																				<input type="checkbox" name="levs[]" id="" class="" value="2_<?php //echo $all_ins['instrument_id']?> " <?php //if ($all_ins['sel']=="t" && in_array("2",$all_ins['levels'])) echo 'checked="checked"' ?> />
																			</td>
																			 <td>	
																				<input type="checkbox" name="levs[]" id="" class="" value="3_<?php //echo $all_ins['instrument_id']?> " <?php //if ($all_ins['sel']=="t" && in_array("3",$all_ins['levels'])) echo 'checked="checked"' ?> />
																			</td>
																			 <td>	
																				<input type="checkbox" name="levs[]" id="" class="" value="4_<?php //echo $all_ins['instrument_id']?> " <?php //if ($all_ins['sel']=="t" && in_array("4",$all_ins['levels'])&&$all_ins['approved']==1) echo 'checked="checked"' ?> />
																			</td>-->
																		</tr>	
																   <?php } ?>
																
																</tbody>
																</table>
																<br/>
																<?php if (count($advance_approved_levels)>0) {
																	  
																	?>
																<span class="semi-bold">
																	<h4><b>ADVANCED APPROVALS</b></h4>
																</span>
															 <table class="dataTable display" id="lesscompleted" >
															
																<thead>
																	<tr>
																		
																	
																		<th>CATEGORY</th>
																		<th></th>
																		<th></th>
																		<th></th>
																		<th></th>
																	
																		
																		
																	</tr>
																</thead>
																	<tbody>
																	<?php foreach($advance_approved_levels as $advance_levels){?>
																		<tr style="border-top: solid 1px #ddd;">
																		
																	
																			<td><?php echo $advance_levels['name']?></td>
																			<td>
																			<?php if($advance_levels['video1']!=""&&$advance_levels['video1']!='NULL'){?>
																			<a href="<?php echo $advance_levels['video1']?>"target="_blank"><i class="fa fa-video-camera" title="Click here to view video(s)" alt="Video"></i></a>
																		   <?php } else{?>
																			N/A
																			<?php }?>
																			</td>
																			<td><?php if($advance_levels['video2']!=""&&$advance_levels['video2']!='NULL'){?>
																			<a href="<?php echo $advance_levels['video2']?>"target="_blank"><i class="fa fa-video-camera" title="Click here to view video(s)" alt="Video"></i></a>
																		   <?php } else{?>
																			N/A
																			<?php }?></td>
																			<td><?php if($advance_levels['video3']!=""&&$advance_levels['video3']!='NULL'){?>
																			<a href="<?php echo $advance_levels['video3']?>"target="_blank"><i class="fa fa-video-camera" title="Click here to view video(s)" alt="Video"></i></a>
																		   <?php } else{?>
																			N/A
																			<?php }?></td>
																			<td><?php if($advance_levels['filename']!=""&&$advance_levels['filename']!='NULL'){?>
																			<a href="<?php echo '../classroom/Uploads/certificates/'.$advance_levels['filename']?>"target="_blank"><i class="fa fa-file-text-o" title="Click here to view video(s)" alt="Video"></i></a>
																		   <?php } else{?>
																			N/A
																			<?php }?></td>
																			
																
																	
																		
																		
																		</tr>
																		 <?php }?>
																	</tbody>
																</table>
																	  <?php }?>
															</div>
												
												</div>
												<!-- <div class="form-group">
													   <div class="col-xs-10" > 	
															<input type="submit"  name="actionvar" class="submit btn btn-warning btn-cons pull-left"  value="Save" id="updateEducation" /> 
																		 
														
													   </div>
												   </div>-->
												
											</div>
									 </div>
									 <!--<div class="tab-pane " id="profile_pic">
									       <div class="col-md-12">
											   <div class="col-md-5">
												   
													<div class="user-info-wrapper text-center">
														<div style="width: 200px; height: 200px;" class="profile-wrapper">
															<img width="200" height="200" src="" class="img-responsive" id="current_pp">
														</div>
													</div>	
											   </div>
							 
											   <div class="col-md-6">
													<div style="padding-top: 29px;" class="panel panel-default">
														<div class="panel-heading"> ADD/EDIT AND CROP PROFILE PICTURE </div>
														<div class="panel-body" id="profile_crop">
													   
														<div class="cropControls cropControlsUpload"> <i class="cropControlUpload"></i> </div><form style="display: none; visibility: hidden;" class="profile_crop_imgUploadForm">  <input type="file" id="profile_crop_imgUploadField" name="img">  </form></div>
													</div>   
							        
												</div>
											</div>
									 </div>-->
									 <div class="tab-pane " id="documents">
									     
										<div class="col-md-12">
											<div class="col-md-6">
												
												<h4>W-9 Form</h4>
												<iframe src="https://docs.google.com/gview?url=https://www.musicproslive.com/liveroom/Uploads/W9/<?php echo $docs[0]['w9form_name']?>&embedded=true" style="width:100%; height:500px;" frameborder="0"></iframe>
												
											</div>
											<div class="col-md-6">
												<h4>Getting Paid</h4>
												<iframe src="https://docs.google.com/gview?url=https://www.musicproslive.com/liveroom/Uploads/directdepo/<?php echo $docs[0]['dfform_name']?>&embedded=true" style="width:100%; height:500px;" frameborder="0"></iframe>
											</div>
											
										</div>
										<br />
										<div class="col-md-12">
										    <div class="col-md-6">
												<h4>Additional Doc</h4>
												<iframe src="https://docs.google.com/gview?url=https://www.musicproslive.com/liveroom/files/resumes/<?php echo $odata[0]['value']?>&embedded=true" style="width:100%; height:500px;" frameborder="0"></iframe>
											</div>
										      <br/>
											<input type="hidden" name="login_id" value="<?php echo $_GET['login_id']?>" id="login_id" > 
												<!--<div class="col-md-6">
											<button type="submit" class="btn btn-cons btn-warning"  style="margin-top: 15px;">Approve</button>
											</div> --> 
										</div>
									
									 </div>
									 <div class="tab-pane " id="upload">
									    <div class="col-md-12">
											<div class="col-md-6">
												
												<h4>W-9 Form</h4>
												<input id="w9_form_upload" name="w9_form_upload" type="hidden">
												<div id="w9Dropzone" class="dropzone" >
											    </div> 
												
											</div>
											<div class="col-md-6">
												<h4>Getting Paid</h4>
												<input id="getting_paid" name="getting_paid" type="hidden">
												<div id="ddDropzone" class="dropzone" >
											    </div> 
											</div>
											
										</div>
										<br />
										<div class="col-md-12">
										    <div class="col-md-6">
												<h4>Additional Doc</h4>
												<input id="resume_upload" name="resume_upload" type="hidden">
												<input id="res_user_id" name="res_user_id" type="hidden" value="<?php echo $user_info[0]['user_id'];?>" >
												<input id="user_email" name="user_email" type="hidden" value="<?php echo $user_det[0]['user_name'];?>">
												<div id="documentDropzone" class="dropzone">
												</div> 
											</div>
										      <br/>
											
												<!--<div class="col-md-6">
											<button type="submit" class="btn btn-cons btn-warning"  style="margin-top: 15px;">Approve</button>
											</div> --> 
										</div>
									 </div>
									
									 
									 
								</div>
								
							</div>
							<input type="hidden" id="month_rev" name="month_rev" value=<?php echo $month?> >
							<input type="hidden" id="year_rev" name="year_rev" value=<?php echo $year?> >
							 <div class="row">

										  <button type="submit" name="save" value="save" class="btn btn-warning btn-cons">Save</button>
										  <?php if($user_det[0]['admin_authorize']==0){?>
										  <button type="submit" name="save" value="approve" class="btn btn-warning btn-cons">Approve</button>
										  <?php } ?>
										  <a href="https://www.livemusictutor.com/adminnew/instructor.php" class="btn btn-danger btn-cons">Cancel</a>
							
										  <button type="button" name="delete"  id="delete" class="btn btn-danger btn-cons" onclick="javascript:delete_ins();">Delete</button>
										   
										   <?php if($user_det[0]['diff']==0){?>
										  <button type="button" name="feature_bt" class="btn btn-warning btn-cons" onclick="javascript:featureop('fet','<?php echo $user_det[0]['userid']?> ');">Feature</button>
										  <?php }else{ ?>
									         <button type="button" name="feature_bt" value="feature_bt" class="btn btn-warning btn-cons" onclick="javascript:featureop('unfet','<?php echo $user_det[0]['userid']?> ');">Unfeature</button>
										  <?php } ?>
										
							</div>
									 
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
						<h4><b>Members Name</b></h4>
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
</style>	
<script src="../classroom/lmtboot/assets/plugins/dropzone/dropzone.js" type="text/javascript"></script>
    <link href="../classroom/lmtboot/assets/plugins/dropzone/css/dropzone.css" rel="stylesheet" type="text/css"/>
  <?php include('layout/footer/instfooter.php');?>
  <script src="../classroom/lmtboot/assets/js/tabs_accordian.js" type="text/javascript"></script>
  <script src="../classroom/croppic/assets/js/jquery.mousewheel.min.js"></script>
   	<!--<script src="../classroom/croppic/assets/croppic.js"></script>
    <script src="../classroom/croppic/assets/js/main.js"></script>-->
	<script>
	
		/*var croppicHeaderOptions = {
				//uploadUrl:'img_save_to_file.php',
				cropData:{
					"dummyData":1,
					"dummyData2":"asdas"
				},
				
				cropUrl:'img_crop_to_file.php',
				
				customUploadButtonId:'cropContainerHeaderButton',
				modal:false,
				processInline:true,
				loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> ',
				onBeforeImgUpload: function(){ console.log('onBeforeImgUpload') },
				onAfterImgUpload: function(){ console.log('onAfterImgUpload') },
				onImgDrag: function(){ console.log('onImgDrag') },
				onImgZoom: function(){ console.log('onImgZoom') },
				onBeforeImgCrop: function(){ console.log('onBeforeImgCrop') },
				onAfterImgCrop:function(){ console.log('onAfterImgCrop') },
				onError:function(errormessage){ console.log('onError:'+errormessage) }
		}	
		var croppic = new Croppic('croppic', croppicHeaderOptions);
		
		

		
	
		var croppicContaineroutputMinimal = {
				uploadUrl:'img_save_to_file.php',
				cropUrl:'img_crop_to_file.php', 
				
				modal:false,
				doubleZoomControls:false,
			    rotateControls: false,
				loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> ',
				onAfterImgCrop:function(){
				               },
				onError:function(errormessage){alert("Error"); }
		}
		var cropContaineroutput = new Croppic('profile_crop', croppicContaineroutputMinimal);
		
	
		*/
		
	
	    function delete_ins()
		{
			var user_id=$("#user_id").val();
			var login_id=$("#login_id").val();
		  $.ajax({
			url: "delete_ins.php?user_id="+user_id+"&login_id="+login_id,
			type: "GET",
			
		
		   success: function(result)
		   {
			   alert("Professional Deleted");
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
		
 
    if ( $('#w9Dropzone').length)
		{
			Dropzone.options.documentDropzone = false;
			
			$("div#w9Dropzone").dropzone(
			{ 
				url: "../liveroom/insdocs/php/index.php",
				
				paramName: "files", 
				maxFilesize: 100,
				maxFiles: 1,
				acceptedFiles: ".docx,.doc,.pdf",
				init: function() {
					this.on("success", function(file, data) { 
					
						$('#w9_form_upload').val(data);
					
					});
				}
			});
		}
		if ( $('#ddDropzone').length)
		{
			Dropzone.options.documentDropzone = false;
			
			$("div#ddDropzone").dropzone(
			{ 
				url: "../liveroom/insdd/php/index.php",
				paramName: "files", 
				maxFilesize: 100,
				maxFiles: 1,
				acceptedFiles: ".docx,.doc,.pdf",
				init: function() {
					this.on("success", function(file, data) { 
						
						$('#getting_paid').val(data);
					
					});
				}
			});
		}
		if ( $('#documentDropzone').length)
		{
			Dropzone.options.documentDropzone = false;
			
			$("div#documentDropzone").dropzone(
			{ 
				url: "../liveroom/server/php/index.php",
				paramName: "files", 
				maxFilesize: 100,
				maxFiles: 1,
				acceptedFiles: ".docx,.doc,.pdf",
				init: function() {
					this.on("success", function(file, data) { 
						
						$('#resume_upload').val(data);
					
					});
				}
			});
		}

	</script>
	
	
	
	<!--lesson scripts--->
	
	<script type="text/javascript" src="../classroom/css/datatable/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="../classroom/css/datatable/css/jquery.dataTables.css" />
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
	 
	 getLessons(1,1) ;
	 $("#instuname").autocomplete({
			  source: function(request, response) {
				$.ajax({				  
				  url: "ajax_intructor_names_ref.php",
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
			 
				//eventuallydosomething(ui.item.value);
				$("#instuname").autocomplete("close");
			  } 
			});
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
		 var month_rev=$("#months_sel").val();
		 var year_rev=$("#years_sel").val();
		 
		 $("#month_rev").val(month_rev);
		 $("#year_rev").val(year_rev);
		 getLessonsbymonth(1,3);
	 });
	
</script>


<script>
var scheduled_lesson =  $('#lessscheduled').DataTable( {
		"iDisplayLength": 10,
		"processing": true,
		"serverSide": true,
		"order": [[ 1, "asc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings scheduled."
			},
		"ajax":"ajax_lessons_inst.php?enrl_status=1",
		
		columns: [  
					
					
					{ "defaultContent":""}, 
					
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
		"order": [[ 2, "asc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings booked."
			},
		"ajax":"ajax_lessons_inst.php?enrl_status=2",
		
		columns: [  
					
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					
					{ className: "hidden-phone hidden-tablet","defaultContent":"" }, 
					{ className: "center hidden-phone hidden-tablet","defaultContent":""},
					{"defaultContent":"" },
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
		"order": [[ 2, "asc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings completed."
			},
		"ajax":"ajax_lessons_inst.php?enrl_status=3",
		columns: [  
					
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					
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
		"order": [[1, "asc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings cancelled."
			},
		"ajax":"ajax_lessons_inst.php?enrl_status=4",
		columns: [  
					
					
					{"defaultContent":""}, 
					
					{ className: "hidden-phone hidden-tablet","defaultContent":"" }, 
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
		"order": [[ 1, "asc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings expired"
			},
		"ajax":"ajax_lessons_inst.php?enrl_status=5",
		columns: [  
					
					
					{ "defaultContent":""}, 
					
					{ className: "hidden-phone hidden-tablet","defaultContent":"" }, 
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
		"order": [[ 2, "asc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any meetings completed."
			},
		"ajax":"ajax_lessons_inst_month.php?enrl_status=3",
		columns: [  
					
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
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
<script type="text/javascript" src="../liveroom/js/jquery.simplyCountable.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	 $('#txtAboutMe').simplyCountable({
    counter:            '#counter',
    countType:          'characters',
    maxCount:           650,
   strictMax:          true,
    countDirection:     'down',
   
});
});
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
				scheduled_lesson.ajax.url( 'ajax_lessons_inst.php?enrl_status=1&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code).load();
				
			}
			else if(enrl_status==2)
			{
				booked_lesson.ajax.url( 'ajax_lessons_inst.php?enrl_status=2&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code ).load();
				
			}
			else if(enrl_status==3)
			{
				completed_lesson.ajax.url( 'ajax_lessons_inst.php?enrl_status=3&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code ).load();
			}
			else if(enrl_status==4)
			{
				cancelled_lesson.ajax.url( 'ajax_lessons_inst.php?enrl_status=4&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code ).load();
			}
			else if(enrl_status==5)
			{
				expired_lesson.ajax.url( 'ajax_lessons_inst.php?enrl_status=5&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code ).load();
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
			completed_lesson_rev.ajax.url( 'ajax_lessons_inst_month.php?enrl_status=3&user_id='+user_id+"&time_format_id="+time_format_id+"&user_code="+user_code+"&month="+month_rev+"&year="+year_rev ).load();
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
			   url	: "ajaxNotes.php",
			   data	: "ccode="+ccode,
			   dataType: "json",
			   success: function(result){
			  
			   var output = "";
				if(result[0].note_text!= ""){
					
  
							 output+="<div>";
							 output+="<div><p> Title:"+result[0].title+"</p></div>";
							 output+="<div><p> Professional Name:"+result[0].instructor_name+"</p></div>";
							 output+="<div><p> Category Name:"+result[0].instrument_name+"</p></div>";
							 output+="<div><p> Date:"+result[0].note_taken_date+"</p></div>";
							 output+="<div><p> Time:"+result[0].note_taken_time+"</p></div>";
							 output+="<div><p> Notes:"+result[0].note_text+"</p></div>";
							 output+="</div>";
					
						
					}
					else
					{
					
						output+="<div>";
					 output+="<div>";
					 output+="<strong>No Notes found</strong>";
					 output+="</div>";
					 output+="</div>";
					 }  
				
				$("#form-content .modal-body").html(output);
				$("#form-content").modal('show');
				}
				
			 });
				
	}
	function setstudentData(ccode)
	{
	   
		
		$.ajax({
			   type	: "GET",
			   url	: "ajax_users_enrolled.php",
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
			   url	: "ajax_view_lesson.php",
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
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>

<script language="javascript">
    function initialize_org() {
        	 var input = /** @type {HTMLInputElement} */(
        		      document.getElementById('pac-input_org'));
        	 var autocomplete = new google.maps.places.Autocomplete(input);
        	 var searchBox = new google.maps.places.SearchBox(
        			    /** @type {HTMLInputElement} */(input));

        			  // [START region_getplaces]
        			  // Listen for the event fired when the user selects an item from the
        			  // pick list. Retrieve the matching places for that item.
        	  google.maps.event.addListener(searchBox, 'places_changed', function() {
        			    var places = searchBox.getPlaces();

        			    if (places.length == 0) {
        			      return;
        			    }
        			    

        			      // For each place, get the icon, place name, and location.
        			      markers = [];
        			      var bounds = new google.maps.LatLngBounds();
        			      for (var i = 0, place; place = places[i]; i++) {
        			        var image = {
        			          url: place.icon,
        			          size: new google.maps.Size(71, 71),
        			          origin: new google.maps.Point(0, 0),
        			          anchor: new google.maps.Point(17, 34),
        			          scaledSize: new google.maps.Size(25, 25)
        			        };
        			       

        			        bounds.extend(place.geometry.location);
        			      }
                      
        	  })

         }
 google.maps.event.addDomListener(window, 'load', initialize_org);		 
</script>	