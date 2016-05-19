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
									<h4><b>Members</b></h4>
								</span>
								
						</div>
						<div class="grid-body no-border">
							<div class="row">
							  
								
									
									 <!--<div class="tab-pane " id="lessons">-->
										<!--<div class="col-md-12">-->
										 
										  <input id="searchbox_professionals" type="text" style="width:100%" placeholder="Enter a name or keyword">
										  
											   <div class="col-md-12" style="">
												<table class="dataTable display" id="lessscheduled" >
													<tbody>
													<thead>
													<tr>
															
															
															<th>NAME</th>
															<th>E-MAIL</th>
															<th>BOOKED</th>
															<th>COMPLETED</th>
															<th>AMOUNT</th>
															<th>SINCE</th>
															
															
													</tr>
														</thead>
											   </table>	
												
											</div>
												<div class="clearfix">&nbsp;</div>
												
											</div>
									  
									 
									
									 
									 
								</div> <!--row end -->
								
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
.profile-pic-dash {
    border-radius: 100px;
    display: inline-block;
    float: left;
    height: 35px;
    overflow: hidden;
    width: 35px;
}
</style>	
<script src="../liveroom/lmtboot/assets/plugins/dropzone/dropzone.js" type="text/javascript"></script>
    <link href="../liveroom/lmtboot/assets/plugins/dropzone/css/dropzone.css" rel="stylesheet" type="text/css"/>
  <?php include('layout/footer/instfooter.php');?>
  <script src="../liveroom/lmtboot/assets/js/tabs_accordian.js" type="text/javascript"></script>
  <script src="../liveroom/croppic/assets/js/jquery.mousewheel.min.js"></script>
   	<script src="../liveroom/croppic/assets/croppic.js"></script>
    <script src="../liveroom/croppic/assets/js/main.js"></script>
	
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
						
						$('#w9_form_upload').val(data[0].name);
					
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
						
						$('#getting_paid').val(data[0].name);
					
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
						
						$('#resume_upload').val(data[0].name);
					
					});
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
 <script type="text/javascript">
	    $("#searchbox_professionals").on("keyup", function() {
		
		
		scheduled_lesson.search( $(this).val() ).draw(); 
		
	
		
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
		"responsive":true,
		"order": [[ 4, "desc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any students."
			},
		"ajax":"ajax_da_stu.php",
		
		columns: [  
					
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					{ "defaultContent":""}, 
					{"defaultContent":"" },
					
					
				],
		"dom": '<"top"lp>rt<"bottom"ip><"clear">',
		"bAutoWidth": false
		
	});


	var completed_lesson_rev =  $('#lesscompleted_rev').DataTable( {
		"iDisplayLength": 10,	
		"processing": true,
		"serverSide": true,
		"responsive":true,
		"order": [[ 3, "asc" ]],
		"oLanguage": {
			"sZeroRecords": "You currently don't have any lessons completed."
			},
		"ajax":"ajax_lessons_all_month.php?enrl_status=3",
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
			
			
				scheduled_lesson.ajax.url( 'ajax_da_stu.php').load();
				
			
			
				 
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
			   url	: "ajaxNotes.php",
			   data	: "ccode="+ccode,
			   dataType: "json",
			   success: function(result){
			  
			   var output = "";
				if(result[0].note_text!= ""){
					
  
							 output+="<div>";
							 output+="<div><p> Title:"+result[0].title+"</p></div>";
							 output+="<div><p> Instructor Name:"+result[0].instructor_name+"</p></div>";
							 output+="<div><p> Instrument Name:"+result[0].instrument_name+"</p></div>";
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