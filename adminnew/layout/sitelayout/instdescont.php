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
					.spacer-single-form {
						clear: both;
						display: block;
						height: 15px;
						width: 100%;
										}
				</style>
			   <div class="page-content">
       <div class="content">
      
				 <div class="row">
				 <div class="grid simple">
						<div class="grid-title no-border">
								<span class="semi-bold">
									<h4>Instrument Descriptions</h4>
								</span>
								
						</div>
						<div class="grid-body no-border">
						    <form id="addnews" name="addnews" method="post" action="" enctype="multipart/form-data">
							    <input type="hidden" value="<?php echo $_GET['id']?>" name="instru_id" id="instru_id">
							    <div class="row">
									<div class="form-group">			
								
										<label class="col-xs-10 form-label">Instrument Name:</label>
										<div class="col-xs-9">
											<input type="text" class="text form-control"  id="insname" name="insname" 	placeholder="Enter Instrument Name" value="<?php echo $news[0]['heading']?>">
											<label class="normal_text" id="titleInfo"></label>
										</div>
								    </div>		
								</div>
								
								<div class="spacer-single-form"></div>
								<div class="row">
									<div class="form-group">			
								
										<label class="col-xs-10 form-label">Description Title:</label>
										<div class="col-xs-9">
											<input type="text" class="text form-control"  id="desctitle" name="descname" 	placeholder="Enter Title Here" value="<?php echo $news[0]['heading']?>">
											<label class="normal_text" id="titleInfo"></label>
										</div>
								    </div>		
								</div>
							
								<div class="spacer-single-form"></div>
								<div class="row">
									<div class="form-group">			
								
										<label class="col-xs-10 form-label">Description:</label>
										<div class="col-xs-9">
										   
											<textarea id="content" class="form-control" name="content" rows="20"><?php echo $news[0]['content']?></textarea>
										</div>
									</div>	
								</div>
								
								
								<br />
								<div class="row">
								    <div class="form-group">
									    <div class="col-xs-4">
									        <button type="submit" class="submit btn btn-warning btn-cons" value="Save"> Save</button> &nbsp;
											
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
	   $("#insname").autocomplete({
			  source: function(request, response) {
				$.ajax({				  
				  url: "ajax_instrument_names.php",
				  dataType: "json",
				  data: request,                    
				  success: function (data) {
					// No matching result
					if (data == null) {
					  //alert('No entries found!');
					   $("#insname").val('');
					  
					  $("#insname").autocomplete("close");
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
			  $("#instru_id").val('');
			   $("#instru_id").val(ui.item.id);
			  $.ajax({
				 url:"ajax_instruments_desc.php?ins_id="+ui.item.id,
				 dataType: "json",
				 success: function(data)
				 {
					 //alert(data.description_new);
					 $("#desctitle").val('');
					 $("#desctitle").val(data.title);
					 $("#content").data("wysihtml5").editor.setValue('');
					 $("#content").data("wysihtml5").editor.setValue(data.description_new);
					 
				 }				 
                 				 
			  });
				//eventuallydosomething(ui.item.value);
				$("#insname").autocomplete("close");
			  } 
			});
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
			