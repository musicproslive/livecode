<?php
/**************************************************************************************
Created by :Lijesh 
Created on :Sep - 06 - 2012
Purpose    :Cource Listing of Tutor
**************************************************************************************/
require_once 'init.php';err_status("init.php included");
error_reporting(E_ALL);
//Required library files
require "library/MysqlAdapter.php";
 
//ini_set("display_errors","1");
//Establish a new DB object
$db = new MysqlAdapter();
//Top Advertisement Management in header

$obj=loadModelClass(true,"configureCourse.php");

$course_id=$_GET['course_id'];
$inst_id=$_GET['inst_id'];
$obj->endCourse($course_id,$inst_id);
?>