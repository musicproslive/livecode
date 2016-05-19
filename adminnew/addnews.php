<?php
require_once 'init.php';err_status("init.php included");

require "library/MysqlAdapter.php";
 
ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();
  if($_POST['title']!="")
  {
	  
	    $_POST['content']=str_replace('"','"',str_replace('"','"', $_POST['content']));
		$_POST['content']=str_replace('\'',"'", $_POST['content']);
		//$_POST['bio']=str_replace('é',"&eacute;",$_POST['bio']);
		$_POST['content']=preg_replace('/[^(\x20-\x7F)]*/','',  $_POST['content']);
	  $image_name="";
	  
	  if($_POST["news_image"]!="")
	  {
	  $image_name = $_POST["news_image"];
	 
	  /*$file_type = $_FILES["news_image"]["type"];
	  $file_size = $_FILES["news_image"]["size"];
	  $file_temp = $_FILES["news_image"]["tmp_name"];
	  move_uploaded_file ($file_temp, "../classroom/news/".$image_name);*/
	  
	  }
	  if($_POST['newsid']!="")
	  {
		    $date=date("Y-m-d H:i:s",strtotime($_POST['date_added']));
		   $insert_sql="update tbl_news_new set heading='".addslashes($_POST['title'])."',
	                                            intro_text='".addslashes($_POST['introtext'])."',
												content='".addslashes($_POST['content'])."',
												featuted=".$_POST['featured'].",
												date_added='".$date."'";
			if($image_name!="")
			{
				$insert_sql.=" ,image='".$image_name."'";
			}				
														$insert_sql.=" where id=".$_POST['newsid'];
	  }
	  else
	  {
		  $date=date("Y-m-d H:i:s",strtotime($_POST['date_added']));
	  $insert_sql="insert into tbl_news_new set heading='".addslashes($_POST['title'])."',
	                                            intro_text='".addslashes($_POST['introtext'])."',
												content='".addslashes($_POST['content'])."',
												featuted=".$_POST['featured'].",
												date_added='".$date."'";
								if($image_name!="")
								{									
											$insert_sql.=",image='".$image_name."'";
								}			
	  }											
	  
	  
	   $db->ExecuteQuery($insert_sql);
	   if($_POST['title']!=""&&$_POST['newsid']=="")
		{
			header("location:news.php");
			exit();
		}
	 
  }
    $news_vals="select * from tbl_news_new where id=".$_GET['id'];
	$news=$db->ExecuteQuery($news_vals);
   require_once('layout/sitelayout/addnewscont.php');
     if($_POST['newsid']!="")
	   {
		   echo "<script>alert('News Updated Successfully');window.location='news.php'</script>";
	   }

?>
   