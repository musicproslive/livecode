<?php
ob_start();
session_start();
ob_clean();

/**
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008-2009 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$targ_w = 209;
	$targ_h = 286;
	$jpeg_quality = 90;
	$data		=	explode("/",$_SESSION['imageSaved']);
	$file		=	$data[count($data)-1];	
	$src = "../../images/temp/$file";//$_SESSION['imageSaved'];
	$img_r = imagecreatefromjpeg($src);
	$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
	
	$data	=	imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],$targ_w,$targ_h,$_POST['w'],$_POST['h']);
	//header('Content-type: image/jpeg');	
	imagejpeg($dst_r,$src,$jpeg_quality);
	header("Location:../../editProfileImage.php");
}

// If not a POST request, display page below:

?><html>
	<head>

		<link rel="stylesheet" href="../css/jquery.Jcrop.css" type="text/css" />
		<link rel="stylesheet" href="demo_files/demos.css" type="text/css" />

		<script language="Javascript">

			$(function(){

				$('#cropbox').Jcrop({
					aspectRatio: 1,
					onSelect: updateCoords
				});

			});

			function updateCoords(c)
			{
				$('#x').val(c.x);
				$('#y').val(c.y);
				$('#w').val(c.w);
				$('#h').val(c.h);
			};

			function checkCoords()
			{
				if (parseInt($('#w').val())) return true;
				alert('Please select a crop region then press submit.');
				return false;
			};

		</script>

	</head>

	<body>

	<div id="outer">
	<div class="jcExample">
	<div class="article">

		<h1>Live Music - Image Cropper </h1>

		<!-- This is the image we're attaching Jcrop to -->
		<img src="<?php echo $_SESSION['imageSaved']?>" id="cropbox" />
<?php if(!empty($_SESSION['imageSaved'])){?>
		<!-- This is the form that our event handler fills -->
		<form action="libs/croper/crop.php" method="post" onSubmit="return checkCoords();">
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />
			<input type="submit" value="Crop Image" />
		</form>
<?php }else{ ?>
<div class="row" style="color:#FF0000;font-weight:bold;">No image Browsed </div>
<?php }  ?>
	

