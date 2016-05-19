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
			  <div class="col-md-12">
				 <div class="grid simple">
						<div class="grid-title no-border">
							<!--	<span class="semi-bold">
									<h4>Dashboard</h4>
								</span>-->
								<div class="row">
								<div class="col-md-12">
								<h4><b> Discount Codes </b></h4>
								 <br />
								 <div class="col-md-6"><button type="button" class="btn btn-warning" id="add_promo" onclick="javascript:add_promo()">Add Discount Code</button></div>
								 <br />
								 
								 <table class="dataTable display" id="lesscomp" >
										<tbody>
										<thead>
											<tr>
												
												<th>Discount Code</th>
												<th>%</th>
												<th>Status</th>
												<th>Used</th>
												<th></th>
												
												
												
											</tr>
										</thead>
								</table>
									</div>
									</div>
									</div>
								</div>
								</div>
								
				</div>
				<div class="modal fade" id="promocodes"  style="display: none;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4><b><span id="promo_head">Add Discount Code</b></h4>
								</div>
								<div class="modal-body">
								  <form method="post" >
								     <div class="row">
									  <label class="col-xs-5 form-label">Discount Code</label>
									  <div class="col-xs-8 ">
									      <input type="text" name="dis_code" value="" class="text form-control" id="dis_code">
									  </div>
									 </div>
									  <div class="row">
									  <label class="col-xs-5 form-label">Percentage</label>
									  <div class="col-xs-8 ">
									      <input type="text" name="percentage" value="" class="text form-control" id="percentage">
									  </div> 
									 </div>
									 <input type="hidden" name="dis_id" id="dis_id" value="">
									 <div class="row">
									 
									  <div class="col-xs-8 " style="margin-top: 10px;">
									     <button type="submit" class="btn btn-warning">Save</button>
									  </div>
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
  <?php include('layout/footer/footer.php');?>
    
  	<script type="text/javascript" src="../classroom/css/datatable/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="../classroom/css/datatable/css/jquery.dataTables.css" />
  	<script type="text/javascript" src="../classroom/css/datatable/extensions/TableTools/js/dataTables.tableTools.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="../classroom/css/datatable/extensions/TableTools/css/dataTables.tableTools.css" />
<script type="text/javascript" src="../classroom/lmttheme/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="../liveroom/css/datatable/media/css/dataTables.responsive.css">
		<script type="text/javascript" src="../liveroom/css/datatable/media/js/dataTables.responsive.js" charset="UTF-8"></script>

<script >
var booked_lesson =  $('#lesscomp').DataTable( {
	    "dom": 'T<"clear">lfrtip',
        "tableTools": {
            "sSwfPath": "../classroom/css/datatable/swf/copy_csv_xls_pdf.swf",
			"aButtons": [
			   
                {
                    "sExtends": "copy",
                    "fnClick": function ( nButton, oConfig, flash ) {
                        /* Copy to clipboard with the page title */
                        this.fnSetText( flash, "Promo Codes\n\n"+ 
                            this.fnGetTableData(oConfig) );
                    }
                },
				 {
                    "sExtends": "csv",
					"sFileName":"bi-weekly payroll.csv",
                    "fnClick": function ( nButton, oConfig, flash ) {
                        /* Copy to clipboard with the page title */
                          this.fnSetText( flash, "Promo Codes\n\n"+ 
                            this.fnGetTableData(oConfig) );
                    }
                },
				{
                    "sExtends": "xls",
					"sFileName":"bi-weekly payroll.xls",
                    "fnClick": function ( nButton, oConfig, flash ) {
                        /* Copy to clipboard with the page title */
                          this.fnSetText( flash, "Promo Codes\n\n"+ 
                            this.fnGetTableData(oConfig) );
                    }
                },
				{
                    "sExtends": "pdf",
					"sFileName":"Promo Codes.pdf",
                    "sTitle": "Promo Codes"
                },
				
				
				{
                    "sExtends": "print",
				    
					
                },
            ]
        },
		"iDisplayLength": 10,
		"processing": true,
		"serverSide": true,
		"responsive":true,
		"order": [[ 0, "asc" ]],
		"oLanguage": {
			"sZeroRecords": "No data available."
			},
		"ajax":"ajax_discount_list.php",
		
		columns: [  
					
					null,
					null,
					null,
					null,
					{"bSortable": false}
					
					
					
				],
		
		"bAutoWidth": false
		
	});
$(document).ready(function() {	





	$("#searchbox_professionals").on("keyup", function() {
		
		
		booked_lesson.search( $(this).val() ).draw(); 
		
	
		
	});
});
function loadins()
{
	booked_lesson.ajax.url( 'ajax_biwee_ins.php?month=<?php echo $month?>&year=<?php echo $year?>&days=<?php echo $days_week?>' ).load();
}
function edit_promo(id,percentage,code)
{
	$("#promo_head").html('');
	$("#promo_head").html('Edit Discount Code');
	$("#dis_code").val('');
	$("#dis_code").val(code);
	$("#percentage").val('');
	$("#percentage").val(percentage);
	$("#dis_id").val('');
	$("#dis_id").val(id);
	$("#promocodes").modal('show');
}
function add_promo()
{
	$("#promo_head").html('');
	$("#promo_head").html('Add Discount Code');
	$("#dis_code").val('');
	
	$("#percentage").val('');
	
	$("#dis_id").val('');
	
	$("#promocodes").modal('show');
}
function delete_promo(id)
{
	$.ajax({
		url:"ajax_functions.php?process=delete_dis&id="+id,
		success:function(response){
			alert(response);
			booked_lesson.ajax.url( 'ajax_discount_list.php' ).load();
		}
	});
}
function status_promo(id,status)
{
	$.ajax({
		url:"ajax_functions.php?process=status_dis&id="+id+"&status="+status,
		success:function(response){
			alert(response);
			booked_lesson.ajax.url( 'ajax_discount_list.php' ).load();
		}
	});
}
 
   </script>
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
#lesscomp_wrapper
{
 margin-top: 15px !important;
}

</style>	