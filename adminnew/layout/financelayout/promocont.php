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
								<h4><b> Promocodes </b></h4>
								<br />
								 <div class="col-md-6"><button type="button" class="btn btn-warning" id="add_promo" onclick="javascript:add_promo()">Add Promo Code</button></div>
								 <br />
								 <table class="dataTable display" id="lesscomp" >
										<tbody>
										<thead>
											<tr>
												
												<th>Promo Code</th>
												<th>Status</th>
												<th>Email Address</th>
												
												
												
												
											</tr>
										</thead>
								</table>
									</div>
									</div>
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
		"ajax":"ajax_promo_list.php",
		
		columns: [  
					
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
function add_promo()
{
	$.ajax({
		url:"ajax_functions.php?process=add_promo",
		success:function(response)
		{
			alert(response);
			booked_lesson.ajax.url( 'ajax_promo_list.php').load();
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