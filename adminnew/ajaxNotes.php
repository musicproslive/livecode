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

$obj	=	loadModelClass(true,"stdCourses.php");
$sql3="select concat(tblusers.first_name,' ',tblusers.last_name) as name,note_text,DATE_FORMAT(note_taken,'%m/%d/%Y %r') note_taken from tblcourse_notes left join tblusers on tblcourse_notes.note_owner_id=tblusers.user_id  where tblcourse_notes.course_id=".$_REQUEST['course_id'];
$notes_all=$obj->getdbcontents_sql($sql3,0);
foreach($notes_all as $notes)
{
	$notes_response.="<div>
	                    <p>Taken By:".$notes['name']."</p>
						<p>Time    :".$notes['note_taken']."</p>
						<p>Notes   :".$notes['note_text']."</p>
					   </div>	";
}
echo  $notes_response;
die();
						
						
						
