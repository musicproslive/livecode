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
			  <div class="col-md-6">
				 <div class="grid simple">
						<div class="grid-title no-border">
							<!--	<span class="semi-bold">
									<h4>Dashboard</h4>
								</span>-->
								<div class="row">
								<div class="col-md-12">
								<h4><b> Usage Statistics <b></h4>
								<table class="table  ">
                                               
                                                <tbody>
                                                    <tr>
                                                        <td style="border-top: medium none; !important">Today’s Member Sign Ups</td>
                                                        <td style="border-top: medium none; !important"><?php echo number_format($today_users[0]['total'],0,"",",");?></td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td>Today’s Professional Sign Ups</td>
                                                        <td><?php echo number_format($today_ins[0]['total'],0,"",",");?></td>
                                                     
                                                    </tr>
													<tr>
														<td style="border-top: medium none; !important">
														</td>
														<td style="border-top: medium none !important;">
														</td>
													</tr>
                                                    <tr>
                                                        <td style="border-top: medium none; !important">Total Members</td>
                                                        <td style="border-top: medium none; !important"><?php echo number_format($total_users[0]['total'],0,"",",");?></td>
                                                       
                                                    </tr>
													 <!--<tr>
                                                        <td>Total Kids</td>
                                                        <td ><?php //echo number_format(count($total_kids),0,"",",");?></td>
                                                       
                                                    </tr>-->
													 <tr>
                                                        <td>Total Professionals</td>
                                                        <td ><?php echo number_format($total_ins[0]['total'],0,"",",");?></td>
                                                       
                                                    </tr>
													<tr>
														<td style="border-top: medium none !important;">
														</td>
														<td style="border-top: medium none !important;">
														</td>
													</tr>
													<tr>
                                                        <td style="border-top: medium none; !important">Current Meetings Scheduled</td>
                                                        <td style="border-top: medium none; !important"><?php echo number_format($total_lessons_sche,0,"",",");?> [$<?php echo number_format($current_less_she_amt,2,".",",");?>]</td>
                                                       
                                                    </tr>
													<tr>
                                                        <td style="">Current Meetings Booked</td>
                                                        <td style=""><?php echo number_format($current_lessons_book_count,0,"",",");?> [$<?php echo number_format($current_less_book_amt,2,".",",");?>]</td>
                                                       
                                                    </tr>
													<tr>
                                                        <td style="">Total Meetings Scheduled</td>
                                                        <td style=""><?php echo number_format($total_tab_count,0,"",",");?> [$<?php echo number_format($total_tab_amt,2,".",",");?>]</td>
                                                       
                                                    </tr>
													 <tr>
                                                        <td>Total Meetings Booked</td>
                                                        <td ><?php echo number_format($total_lessons_booked,0,"",",");?> [$<?php echo number_format($total_booked_amt,2,".",",");?>]</td>
                                                       
                                                    </tr>
													 <tr>
                                                        <td>Total Meetings Paid</td>
                                                        <td ><?php echo number_format($total_lessons_paid,0,"",",");?> [$<?php echo number_format($total_paid_amt,2,".",",");?>]</td>
                                                       
                                                    </tr>
													 <tr>
                                                        <td>Total Meetings Completed</td>
                                                        <td ><?php echo number_format($total_lessons_completed,0,"",",");?> [$<?php echo number_format($total_completed_amt,2,".",",");?>]</td>
                                                       
                                                    </tr>
													<tr>
                                                        <td>Total Meetings Cancelled</td>
                                                        <td ><?php echo number_format($total_lessons_cancelled,0,"",",");?></td>
                                                       
                                                    </tr>
                                                </tbody>
                                            </table>
									</div>
									</div>
									</div>
								</div>
								</div>
								<div class="col-md-6">
									<div class="grid simple">
										<div class="grid-title no-border">
											<div class="row">
											<div class="col-md-12">
												<h4><b>Revenue</b></h4>
							                    <div id="placeholder" style="width:100%;height:515px;"></div>
											</div>
											</div>
										</div>
									  </div>
								</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="grid simple">
							<div class="grid-title no-border">
								<div class="row">
									<div class="col-md-12">
									    
											<h4 style="width:180px !important"><b>Meetings Completed</b></h4>
											&nbsp;<?php echo $months_select;?>&nbsp;<?php echo $years_select?>
										
									
											<table class="table  ">
												<thead>
													<tr>
													   
														<th>Type</th>
														<th>Completed</th>
														<th>Total</th>
													</tr>
												</thead>
												<tbody id="month_rev_comp">
													
												</tbody>
											</table>
									</div>
									</div>
									</div>
								</div>
								</div>
								<div class="col-md-6">
						<div class="grid simple">
							<div class="grid-title no-border">
								<div class="row">
									<div class="col-md-12">
									    
											<h4 style="width:180px !important"><b>Meetings Booked</b></h4>
											&nbsp;<?php echo $months_select_new;?>&nbsp;<?php echo $years_select_new?>
										
									
											<table class="table  ">
												<thead>
													<tr>
													   
														<th>Type</th>
														<th>Booked</th>
														<th>Total</th>
													</tr>
												</thead>
												<tbody id="month_rev_booked">
													
												</tbody>
											</table>
									</div>
									</div>
									</div>
								</div>
								</div>
		</div>
		       <div class="row">
			         <div class="col-md-6">
								<div class="grid simple">
										<div class="grid-title no-border">
											<div class="row">
											<div class="col-md-12">
												<h4 style="width:100% !important;"><b>Bi-Weekly Payroll Summary (Period 1) </b></h4>
								
								<table class="table  ">
                                             
                                                <tbody>
                                                    <tr>
                                                        <td style="border-top: medium none; !important">Professional Revenue</td>
                                                        <td style="border-top: medium none; !important">$<?php echo number_format($total_completed_biweek_p1_amt,2,".",",");?></td>
                                                       
                                                    </tr>
                                                    <tr>
                                                        <td>Professional Payout</td>
                                                        <td>$<?php echo number_format($total_payout_biweek_p1,2,".",",");?></td>
                                                        
                                                    </tr>
                                                   <tr>
                                                        <td>MPL Earnings</td>
                                                        <td>$<?php echo number_format($lmt_earnings_p1,2,".",",");?></td>
                                                        
                                                    </tr>
													<tr>
                                                        <td>Completed Meeting</td>
                                                        <td><?php echo number_format($total_lessons_completed_biweek_period1,0,"",",");?></td>
                                                        
                                                    </tr>
                                                </tbody>
                                            </table>
								</div>
								</div>
						</div>
				</div>
						
				</div><!-- Suummary End -->
				<div class="col-md-6">
								<div class="grid simple">
										<div class="grid-title no-border">
											<div class="row">
											<div class="col-md-12">
												<h4 style="width:100% !important;"><b>Bi-Weekly Payroll Summary (Period 2)</b></h4>
								
								<table class="table  ">
                                             
                                                <tbody>
                                                    <tr>
                                                        <td style="border-top: medium none; !important">Professional Revenue</td>
                                                        <td style="border-top: medium none; !important">$<?php echo number_format($total_completed_biweek_p2_amt,2,".",",");?></td>
                                                       
                                                    </tr>
                                                    <tr>
                                                        <td>Professional Payout</td>
                                                        <td>$<?php echo number_format($total_payout_biweek_p2,2,".",",");?></td>
                                                        
                                                    </tr>
                                                   <tr>
                                                        <td>MPL Earnings</td>
                                                        <td>$<?php echo number_format($lmt_earnings_p2,2,".",",");?></td>
                                                        
                                                    </tr>
													<tr>
                                                        <td>Completed Meetings</td>
                                                        <td><?php echo number_format($total_lessons_completed_biweek_period2,0,"",",");?></td>
                                                        
                                                    </tr>
                                                </tbody>
                                            </table>
								</div>
								</div>
						</div>
				</div>
						
				</div><!-- Suummary End -->
			   </div>
	</div>
</div>
</div>
	<!--/PAGE -->
	<!-- JAVASCRIPTS -->
	<!-- Placed at the end of the document so the pages load faster -->             
  <?php include('layout/footer/footer.php');?>
   <script src="../classroom/lmtboot/assets/plugins/jquery-flot/jquery.flot.js"></script>
<script src="../classroom/lmtboot/assets/plugins/jquery-flot/jquery.flot.time.min.js"></script>
<script src="../classroom/lmtboot/assets/plugins/jquery-flot/jquery.flot.selection.min.js"></script>
<script src="../classroom/lmtboot/assets/plugins/jquery-flot/jquery.flot.animator.min.js"></script>
<script src="../classroom/lmtboot/assets/plugins/jquery-flot/jquery.flot.orderBars.js"></script>
<script src="../classroom/lmtboot/assets/plugins/jquery-sparkline/jquery-sparkline.js"></script>
<script src="../classroom/lmtboot/assets/plugins/jquery-easy-pie-chart/js/jquery.easypiechart.min.js"></script>
<style>
  .table td
  {
	  font-weight:normal !important;
  }
</style>
  <script type="text/javascript">
     $(document).ready(function() {
		 get_month_revenue(<?php echo $month?>,<?php echo $year?>);
		 get_month_booked(<?php echo $month?>,<?php echo $year?>);
	 });
	 
     function get_month_revenue(month,year)
	 {
		 $.ajax({
			   type	: "GET",
			   url	: "ajax_dash_month_rev.php?month="+month+"&year="+year,
			  
			   dataType: "html",
			   success: function(result){
				   $("#month_rev_comp").html("");
				   $("#month_rev_comp").html(result);
			   }
		 });
	 }
	 function get_month_booked(month,year)
	 {
		 $.ajax({
			   type	: "GET",
			   url	: "ajax_month_booked.php?month="+month+"&year="+year,
			  
			   dataType: "html",
			   success: function(result){
				   $("#month_rev_booked").html("");
				   $("#month_rev_booked").html(result);
			   }
		 });
	 }
	 function find_revenue()
	 {
		 get_month_revenue($("#months_sel").val(),$("#years_sel").val());
	 }
	 function find_revenue_booked()
	 {
		 get_month_booked($("#months_sel_new").val(),$("#years_sel_new").val());
	 }
	

$(document).ready(function() {	




	var d2 = [ ];
	var d1 = [<?php echo $d1 ?>];
	var plot = $.plotAnimator($("#placeholder"), [
			{  	label: "Label 1",
				data: d2, 	
				lines: {				
					fill: 0.6,
					lineWidth: 0,				
				},
				color:['#f89f9f']
			},{ 
				data: d1, 
				animator: {steps: 60, duration: 1000, start:0}, 		
				lines: {lineWidth:2},	
				shadowSize:0,
				color: '#00adef'
			},{
				data: d1, 
				points: { show: true, fill: true, radius:6,fillColor:"#00adef",lineWidth:3 },	
				color: '#fff',				
				shadowSize:0
			}
		],{	xaxis: {
		tickLength: 0,
		tickDecimals: 0,
		min:2,
		ticks:[<?php echo $ticks;?>],

				font :{
					lineHeight: 13,
					style: "normal",
					weight: "bold",
					family: "sans-serif",
					variant: "small-caps",
					color: "#6F7B8A"
				}
			},
			yaxis: {
				ticks: 3,
                tickDecimals: 0,
				tickColor: "#f0f0f0",
				font :{
					lineHeight: 13,
					style: "normal",
					weight: "bold",
					family: "sans-serif",
					variant: "small-caps",
					color: "#6F7B8A"
				}
			},
			grid: {
				backgroundColor: { colors: [ "#fff", "#fff" ] },
				borderWidth:1,borderColor:"#f0f0f0",
				margin:0,
				minBorderMargin:0,							
				labelMargin:20,
				hoverable: true,
				clickable: true,
				mouseActiveRadius:6
			},
			legend: { show: false}
		});


	$("#placeholder").bind("plothover", function (event, pos, item) {
				if (item) {
					var x = item.datapoint[0].toFixed(2),
						y = item.datapoint[1].toFixed(2);
					var x1=	parseInt(item.datapoint[0])
                      var months = [<?php echo $months_tu_charts?>];
					  
					$("#tooltip").html( " Revenue for " + months[x1-1] + " = $" + item.datapoint[1])
						.css({top: item.pageY+5, left: item.pageX+5})
						.fadeIn(200);
				} else {
					$("#tooltip").hide();
				}
	
		});
	
	$("<div id='tooltip'></div>").css({
			position: "absolute",
			display: "none",
			border: "1px solid #fdd",
			padding: "2px",
			"background-color": "#fee",
			"z-index":"99999",
			opacity: 0.80
	}).appendTo("body");
	$("#mini-chart-orders").sparkline([1,4,6,2,0,5,6,4], {
		type: 'bar',
		height: '30px',
		barWidth: 6,
		barSpacing: 2,
		barColor: '#00adef',
		negBarColor: '#00adef'});

	$("#mini-chart-other").sparkline([1,4,6,2,0,5,6,4], {
		type: 'bar',
		height: '30px',
		barWidth: 6,
		barSpacing: 2,
		barColor: '#0aa699',
		negBarColor: '#0aa699'});	
	
	$('#ram-usage').easyPieChart({
		lineWidth:9,
		barColor:'#00adef',
		trackColor:'#e5e9ec',
		scaleColor:false
    });
	$('#disk-usage').easyPieChart({
		lineWidth:9,
		barColor:'#7dc6ec',
		trackColor:'#e5e9ec',
		scaleColor:false
    });
	
		$(".xAxis .tickLabel").each(function(i){
	    var res=isNumeric($(this).html());
	   if(res==true)
	   {
	      $(this).remove();
	   }
	});
	
	
	
});	
function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
  </script>