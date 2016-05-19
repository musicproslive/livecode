<?php
 require_once 'init.php';err_status("init.php included");
 //error_reporting(E_ALL);
//Required library files
require "library/MysqlAdapter.php";
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter(); 
$obj=loadModelClass(true,"stdCourses.php");
  if($_REQUEST['action']=="fet")
  {  
		do//create random code.
		{
						$randCode	=	$obj->createRandom(LMT_RANDOM_CODE_LIMIT);
		}
		while($obj->getdbcount_sql("SELECT transaction_id FROM tbl_feature_transactions WHERE transaction_id = '$randCode'") > 0);
		
		
		$trans = array();
		$trans['transaction_id'] = $randCode;
		$trans['trans_time'] = date('Y-m-d H:i:s');
		$trans['ins_id']    = $_REQUEST['ins_id'];
		$trans['amount'] = 0.00;
		$trans['paid_profile']=0;
		$trans['paid']=1;
		$trans['dis_amt']=0.00;
	    $trans['transcode']=$_SESSION['admin_id'];
		$trans['response']="admin";
		
		$obj->dbStartTrans();
		$obj->id =	$obj->db_insert('tbl_feature_transactions', $trans, 0);
			if(!$obj->id)
				{
					$obj->dbRollBack();
					$message="Transaction Fails.";
					$succ=0;
					echo $message;
					exit();
				}
				else
				{
					$user_id_up=$_REQUEST['ins_id'];
					$obj->db_update('tblusers', array('featured' => 1,'last_feature_tran_date'=>date('Y-m-d H:i:s')),"user_id=$user_id_up");
					$message="Profile featured successfully.";
					$succ=0;
					echo $message;
					exit();
				}
  }
  if($_REQUEST['action']=="unfet")
  { 
                    $user_id_up=$_REQUEST['ins_id'];
					$obj->db_update('tblusers', array('featured' => 0),"user_id=$user_id_up");
					$message="Profile unfeatured successfully.";
					$succ=0;
					echo $message;
					exit();
  }
  ?>