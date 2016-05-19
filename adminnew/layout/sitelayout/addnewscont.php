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
				<style>
				    #profile_crop
					{
						border: 1px solid #ccc;
						width: 850px !important;
						height: 450px !important;
						position: relative;
						margin-top: 19px;
					}
				</style>
			   <div class="page-content">
       <div class="content">
      
				 <div class="row">
				 <div class="grid simple">
						<div class="grid-title no-border">
								<span class="semi-bold">
									<h4>Add News</h4>
								</span>
								
						</div>
						<div class="grid-body no-border">
						    <form id="addnews" name="addnews" method="post" action="" enctype="multipart/form-data">
							    <input type="hidden" value="<?php echo $_GET['id']?>" name="newsid">
							    <div class="row">
									<div class="form-group">			
								
										<label class="col-xs-10 form-label">News Title:</label>
										<div class="col-xs-9">
											<input type="text" class="text form-control"  id="title" name="title" 	placeholder="Enter News Title" value="<?php echo $news[0]['heading']?>">
											<label class="normal_text" id="titleInfo"></label>
										</div>
								    </div>		
								</div>
								
								<div class="row">
									<div class="form-group">			
								
										<label class="col-xs-10 form-label">Intro Text:</label>
										<div class="col-xs-9">
											<!--<input type="text" class="text form-control"  id="introtext" name="introtext" placeholder="Enter Few Lines of News Text" value="<?php //echo $news[0]['intro_text']?>">-->
											<textarea id="introtext" name="introtext" cols="30"><?php echo $news[0]['intro_text']?></textarea>
											<p class="help-block">You have <span id="counter_mobile"></span> characters left.</p>
											<label class="normal_text" id="introInfo"></label>
											
										</div>
										
									</div>	
								</div>
								
								<div class="row">
								    <div class="form-group">
									    <label class="col-xs-10 form-label">Date:</label>
									    <div class="col-xs-9">
									      <div class="input-append success date col-md-10 col-lg-6 no-padding">
										        <?php if($news[0]['date_added']!=""){?>
												 <input type="text" class="form-control" value="<?php echo date("m/d/Y",strtotime($news[0]['date_added']));?>" name="date_added">
												<?php }else{?>
												<input type="text" class="form-control" value="" name="date_added">
												<?php }?>	
												<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
										   </div>
									    </div> 
									</div>
								</div>
								
								<div class="row">
									<div class="form-group">			
								
										<label class="col-xs-10 form-label">News Content:</label>
										<div class="col-xs-9">
										   
											<textarea id="content" class="form-control" name="content" rows="20"><?php echo $news[0]['content']?></textarea>
										</div>
									</div>	
								</div>
								<div class="row">
									<div class="form-group">			
								
										<label class="col-xs-10 form-label">News Image:</label>
										<input type="hidden" value="" name="news_image" id="news_image">
										
										<div class="col-xs-9">
											<!--<input type="file" name="news_image">-->
											<div id="profile_crop" class="panel-body">
									   
										    </div>
										</div>
									</div>	
								</div>
								<div class="row">
									<div class="form-group">			
								
										<label class="form-label col-xs-10" for="Featured">Featured:</label>
										<div class="col-xs-offset-1 col-xs-9">
										    <input type="radio" value="1" <?php if($news[0]['featuted']==1) echo 'checked="checked"'?> name="featured">
											  <span>Yes</span>
										</div>	
										
										<div class="col-xs-offset-1 col-xs-9">
										    <input type="radio" value="0" <?php if($news[0]['featuted']==0) echo 'checked="checked"'?> name="featured">
											  <span>No</span>
										</div>
									</div>	
								</div>
								
								<div class="row">
								    <div class="form-group">
									    <div class="col-xs-4">
									        <button type="submit" class="submit btn btn-warning btn-cons" value="Save"> Save</button> &nbsp;
											 <a href="deletenews.php?newsid=<?php echo $_GET['id']?>" class="btn btn-danger btn-cons" > Delete</a>
										</div>	
										 
									</div>
								</div>
								
								
						       </div>
							</form>
						</div>
				</div>
						
				</div>
		</div>
	</div>
</div>
	<!--/PAGE -->
	<!-- JAVASCRIPTS -->
	<!-- Placed at the end of the document so the pages load faster -->             
  <?php include('layout/footer/footer.php');?>
  
  <script src="../classroom/lmtboot/assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script> 
<script src="../classroom/lmtboot/assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
<link href="../classroom/lmtboot/assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css"/> 
<script src="../classroom/lmtboot/assets/plugins/jquery-block-ui/jqueryblockui.js" type="text/javascript"></script> 
<link href="../classroom/lmtboot/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<link href="../classroom/lmtboot/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.css" rel="stylesheet" type="text/css" />
<script src="../classroom/lmtboot/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="../classroom/lmtboot/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
<script src="js/simplyCountable.js" type="text/javascript"></script>

<script>
   $(document).ready(function() {
			$('#content').wysihtml5();	
			$('.input-append.date').datepicker({
				autoclose: true,
				todayHighlight: true
	   });
	   $("#introtext").simplyCountable({counter:'#counter_mobile',maxCount:120});
   });
</script>
<link href="croppic/assets/css/croppic.css" rel="stylesheet">
<script src="croppic/assets/js/jquery.mousewheel.min.js"></script>
   	<script src="croppic/assets/croppic.js"></script>
    <script src="croppic/assets/js/main.js"></script>
	<script>
		var croppicHeaderOptions = {
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
		
	
		
		
	</script>
			