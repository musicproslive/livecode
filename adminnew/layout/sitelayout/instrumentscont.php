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
									<h4><b>Categories</b></h4>
								</span>
								
						</div>
						<div class="grid-body no-border">
						        <div >
								   <form action="instlist.php" name="instrument" method="post" onsubmit="return validate_inst();" >
								        <div class="row" >
										    <label class="col-xs-8 form-label">Category Name</label>
											<div class="col-xs-6">
												<input type="text" name="instuname" value="" class="text form-control" id="instuname" >
											</div>
										</div>
										<div class="" style="margin-top:15px !important;">
										    <button class="btn btn-warning btn-cons" value="save" name="save" type="submit">Save</button>
										</div>
								   </form>
								</div>
								<br />
							    <div class="" style="margin-left: -15px;">
									<div class="control-group control-group checkboxGroup advanced-agt panel panel-default" >
										<div class="controls panel-body">
										 <?php $cnt=0;
										 foreach($instruments as $instrument){
										   if($cnt==0|| ($cnt%4)==0){
										 ?>
										    
										    <div class="row">
										   <?php } ?>
											 <div class="col-md-3">
											  <input id="<?php echo $instrument['key_val']?>" class="" type="checkbox" value="<?php echo $instrument['key_val']?>" name="insts" <?php if ($instrument['is_deleted']==0){?> checked="checked" <?php }?> onchange="javascript:update_inst(<?php echo $instrument['key_val']?>);"> <?php echo $instrument['val']?>
											 </div> 
											 <?php $cnt++; 
											 if(($cnt%4)==0||count($instruments)==$cnt){?>
											</div>
											 <?php }?>
										  <?php }?>	
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
</style>	
  <?php include('layout/footer/instlistfooter.php');?>