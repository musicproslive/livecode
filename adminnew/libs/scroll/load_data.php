<?php
require("../../includes/database_rules.php");
require("../../includes/db.php");
require("../../library/dbclass.php");
require("../../library/siteclass.php");
require("../../library/modelclass.php");


$last_msg_id	=	$_GET['last_msg_id'];
$action			=	$_GET['action'];

if($action <> "get")
{
?>
<link rel="stylesheet" href="9lessons.css" type="text/css" />
<script type="text/javascript" src="jquery-1.2.6.pack.js"></script>
	
	<script type="text/javascript">
	$(document).ready(function(){
			
		function last_msg_funtion() 
		{ 
		   
           var ID=$(".message_box:last").attr("id");
			$('div#last_msg_loader').html('<img src="bigLoader.gif">');
			$.post("load_data.php?action=get&last_msg_id="+ID,
			
			function(data){
				if (data != "") {
				$(".message_box:last").after(data);			
				}
				$('div#last_msg_loader').empty();
			});
		};  
		
		$(window).scroll(function(){
			if  ($(window).scrollTop() == $(document).height() - $(window).height()){
			   last_msg_funtion();
			}
		}); 
		
	});
	</script>


<!--part one  -->
		<?php
		
		$cls		=	 new sdbclass();
		
		$sql		=	"SELECT * FROM messages ORDER BY mes_id DESC LIMIT 5";
		$sql 		= 	$cls->db_query($sql,0);
		
		
		while($row=mysql_fetch_array($sql))
				{
				$msgID= $row['mes_id'];
				$msg= $row['msg'];
		
		?>
		<div id="<?php echo $msgID; ?>"  align="left" class="message_box" >
		<span class="number"><?php echo $msgID; ?></span><?php echo $msg; ?> 
		</div>		
		<?php
				}
		?>
		

<div id="last_msg_loader"></div>

<?php
}
else
{
 ?>
 <!--part two  -->
<?php

$last_msg_id=$_GET['last_msg_id'];
$cls		=	 new sdbclass();

 $sql="SELECT * FROM messages WHERE mes_id < '$last_msg_id' ORDER BY mes_id DESC LIMIT 5";
 $sql = $cls->db_query($sql,0);
 $last_msg_id="";

		while($row=mysql_fetch_array($sql))
		{
		$msgID= $row['mes_id'];
		$msg= $row['msg'];
	?>
		
		<div id="<?php echo $msgID; ?>"  align="left" class="message_box" >
			<span class="number"><?php echo $msgID; ?></span><?php echo $msg; ?> 
		</div>
<?php
}
?>


	
	<?php 	}
		?>	