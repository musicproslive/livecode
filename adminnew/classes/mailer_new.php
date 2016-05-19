<?php

require dirname( dirname( __FILE__ ) ) . '/config/config.php';

//if ( !defined ('AYAH_PUBLISHER_KEY') ) define( 'AYAH_PUBLISHER_KEY', $captcha['ayah']['publisher_key'] );
//if ( !defined ('AYAH_SCORING_KEY') )   define( 'AYAH_SCORING_KEY'  , $captcha['ayah']['scoring_key'] );

//require_once dirname( dirname( __FILE__ ) ) . "/classes/DB_Connect.php";
require 'generator_new.php';







$upload_errors = array(
	UPLOAD_ERR_OK           => "No errors.",
	UPLOAD_ERR_INI_SIZE     => "Larger than upload_max_filesize.",
	UPLOAD_ERR_FORM_SIZE    => "Larger than form MAX_FILE_SIZE.",
	UPLOAD_ERR_PARTIAL      => "Partial upload.",
	UPLOAD_ERR_NO_FILE      => "No file.",
	UPLOAD_ERR_NO_TMP_DIR   => "No temporary directory.",
	UPLOAD_ERR_CANT_WRITE   => "Can't write to disk.",
	UPLOAD_ERR_EXTENSION    => "File upload stopped by extension."
);

	
// Start a session for tokens and shtuff
if ( !session_id() ) session_start();

global $requirements;
$requirements = array();
global $aux_data;
load_auxillary_data ($connection);

// Do not continue if there wasn't a form submitted !
if ( empty( $_POST ) ) return false;


//Connect to DB
//mysql_connect("mysql.lmtutor.blackf.in","arbiterrule","rockymountain");//database connection
//mysql_select_db("lmt_tester");



$form_id = $_POST['form_id'];
$form = $forms[$form_id];

global $post;
$post = array ();
$requirements[$form_id]['file'] = false;
//echo "<pre> _POST " . var_dump ($_POST) .  "</pre>";

// Filter the user's input to prevent malicious activity

foreach ( $_POST as $input_id => $input_value ) {
	if (!empty ($form[$input_id]['filter'])) {
		$filter = $form[$input_id]['filter'];
	} else {
		$filter = FILTER_SANITIZE_STRING;
	}

	if (!empty ($form[$input_id]['flags'])) {
		$flags = $form[$input_id]['flags'];
	} else {
		$flags = null;
	}
	if (is_array ($_POST[$input_id])) {
		$tmp = $_POST [$input_id];
		foreach ($tmp as $i => $v){
			$post[$input_id][$i] = trim( filter_var( $v, $filter, $flags ) );
		}
	} else {
		$post[$input_id] = trim( filter_var( $_POST[$input_id], $filter, $flags ) );
	}
}

// Security
if ( $post['token'] != $_SESSION['jigowatt']['adv-html-form'][$form_id]['token'] ) return false;
unset( $_SESSION['jigowatt']['adv-html-form'][$form_id]['token'], $post['token'] );


foreach ( $form as $input_id => $input_value ) {
//echo "input_id:".$input_id."  input_value".$form[$input_id]['validate'];

	if (array_key_exists ($input_id, $aux_data)) {
		$field_name = $aux_data [$input_id]['other_data_name'];
	} else {
		$field_name = $form[$input_id]['label'];
	}
	// ==================================================================
	//
	// Check for required fields
	//
	// ------------------------------------------------------------------
	if ( !empty( $form[$input_id]['required'] ) ) {
		if ( empty( $post[$input_id] ) ) {
			$requirements[$form_id]['error'] = true;
			$requirements[$form_id]['error_message'] = '<strong>Missing Fields: </strong>Required fields are marked with an asterisk (<span class="required">*</span>).';
			$requirements[$form_id]['error_type'] = 'warning';
			$requirements[$form_id]['ids'][] = $input_id;
		}
	}

	$requirements[$form_id][$input_id]['error_message'] = '';
	switch( $form[$input_id]['validate']){
		case 'label':
			if (!empty ($post[$input_id])) {
				if (!preg_match ("/^[a-zA-Z][a-zA-Z0-9\s]+$/", $post[$input_id])) {
					$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
					$requirements[$form_id]['error'] = true;
				}
			} elseif (true == $form[$input_id]['required']) {
				$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
				$requirements[$form_id]['error'] = true;
			}
			break;
		
		case 'email':
			if (!empty ($post[$input_id])) {
				if (!preg_match ("/^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/", $post[$input_id])) {
					$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
					$requirements[$form_id]['error'] = true;
				}
			} elseif (true == $form[$input_id]['required']) {
				$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
				$requirements[$form_id]['error'] = true;
			}
			$mail =$post[$input_id];
			break;
			
		case 'conemail':
			if (!empty ($post[$input_id])) {
				if (!preg_match ("/^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/", $post[$input_id])) {
					$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
					$requirements[$form_id]['error'] = true;
				}
				if($post[$input_id]!= $mail)
				{
				$requirements[$form_id][$input_id]['error_message'] = "Email Not Matched " ;
					$requirements[$form_id]['error'] = true;
				}
				$select = mysql_query("SELECT `user_name` FROM `tbluser_login` WHERE `user_name` = '".$post[$input_id]."'") or exit(mysql_error());
				if(mysql_num_rows($select))
				{
    			$requirements[$form_id][$input_id]['error_message'] = "This is Email is already Exit.. Please try some other email..";
				$requirements[$form_id]['error'] = true;
				}
			} elseif (true == $form[$input_id]['required']) {
				$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
				$requirements[$form_id]['error'] = true;
			}
			break;
				
		case 'password':
			if (!empty ($post[$input_id])) {
				if (!preg_match ("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/", $post[$input_id])) {
					$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
					$requirements[$form_id]['error'] = true;
				}
			} elseif (true == $form[$input_id]['required']) {
				$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
				$requirements[$form_id]['error'] = true;
			}
			$pass =$post[$input_id];
			break;
			
			case 'conpassword':
			if (!empty ($post[$input_id])) {
				if (!preg_match ("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/", $post[$input_id])) {
					$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
					$requirements[$form_id]['error'] = true;
				}
			else if($post[$input_id]!= $pass)
				{
				$requirements[$form_id][$input_id]['error_message'] = "Password Not Matched " ;
					$requirements[$form_id]['error'] = true;
				}
			} elseif (true == $form[$input_id]['required']) {
				$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
				$requirements[$form_id]['error'] = true;
			}
			
			break;
			
		
	
		case 'phone':
			if (!empty ($post[$input_id])) {
				$phone_tmp = preg_replace ("/[^0-9]/", "", $post[$input_id]);

				if (strlen ($phone_tmp) != 10) {
					$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
					$requirements[$form_id]['error'] = true;
				} else {
					$post[$input_id] = $phone_tmp;
				}
			} elseif (true == $form[$input_id]['required']) {
				$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
				$requirements[$form_id]['error'] = true;
			}
			break;
			
		case 'street':
			if (!empty ($post[$input_id])) {
				if (!preg_match ("/^[a-zA-Z0-9\s.\-\#']+$/", $post[$input_id])) {
					$requirements[$form_id][$input_id]['error_message'] = "Please use only A-Z and 0-9 for your " . $post[$input_id] . ".";
					$requirements[$form_id]['error'] = true;
				}
			} elseif (true == $form[$input_id]['required']) {
				$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
				$requirements[$form_id]['error'] = true;
			}
			break;
		case 'dob':
			if (empty ($post[$input_id])) {
			
					$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid DOB " ;
					$requirements[$form_id]['error'] = true;
				
			}
			break;
			
		case 'city':
			if (!empty ($post[$input_id])) {
				if (!preg_match ("/^[a-zA-Z\s]+$/", $post[$input_id])) {
					$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
					$requirements[$form_id]['error'] = true;
				}
			} elseif (true == $form[$input_id]['required']) {
				$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
				$requirements[$form_id]['error'] = true;
			}
			break;
			
		case 'zip5':
			if (!empty ($post[$input_id])) {
				if (!preg_match ("/^[0-9]+$/", $post[$input_id])) {
					$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
					$requirements[$form_id]['error'] = true;
				} else {
					$len = strlen ($post[$input_id]);
					if ($len != 5) {
						$requirements[$form_id][$input_id]['error_message'] = $field_name . " must be 5 digits.";
						$requirements[$form_id]['error'] = true;
					}
				}
			} elseif (true == $form[$input_id]['required']) {
				$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid " . $field_name . ".";
				$requirements[$form_id]['error'] = true;
			}
			break;
			
	
		case 'doc_file':
			if (!empty ($_FILES[$input_id])) {
				if (UPLOAD_ERR_OK != $_FILES[$input_id]["error"] && UPLOAD_ERR_NO_FILE != $_FILES[$input_id]["error"]) {
					$requirements[$form_id][$input_id]['error_message'] = "Error: (" . $_FILES[$input_id]["error"] . ") " .
						$upload_errors [$_FILES [$input_id]["error"]];
					$requirements[$form_id]['error'] = true;
				} elseif (UPLOAD_ERR_OK == $_FILES[$input_id]["error"])	{
					$file_name = $_FILES[$input_id]["name"];
					$file_type = $_FILES[$input_id]["type"];
					$file_size = $_FILES[$input_id]["size"];
					$file_temp = $_FILES[$input_id]["tmp_name"];

					$matches = array ();
					if (!preg_match ("/^[a-zA-Z]+\.(docx|DOCX|pdf|PDF|doc|DOC|txt|TXT|rtf|RTF)$/", $file_name, $matches)) {
						$requirements[$form_id][$input_id]['error_message'] = "Only A-Z (upper or lower case) is allowed.<br />Only DOC, DOCX, PDF, TXT and RTF files are allowed.";
						$requirements[$form_id]['error'] = true;
					} else {
						/* generate an internal filename */
						/* this will be used in the DB and as the name of the file in our system */
						$ext = $matches [1];
						$post[$input_id] = 'R' . date( 'U' ) . rand( 1000, 9999 ) . "." . $ext;
						$requirements[$form_id]['file'] = true;
					}
				} elseif (true == $form[$input_id]['required']) {
					$requirements[$form_id][$input_id]['error_message'] = "Please upload a file.";
					$requirements[$form_id]['error'] = true;
				}
			}
			break;

		case 'url':
			if (!empty ($post[$input_id])) {
				if (!preg_match('/(https?:\/\/)?www\.[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]\/?/', $post[$input_id])) {
					$requirements[$form_id][$input_id]['error_message'] = "Please enter a valid URL for " . $field_name . ".";
					$requirements[$form_id]['error'] = true;
				}
			} elseif (true == $form[$input_id]['required']) {
				$requirements[$form_id][$input_id]['error_message'] = "Please enter a URL for " . $field_name . ".";
				$requirements[$form_id]['error'] = true;
			}
			break;
		default:
			;
	} // switch



}

// Don't continue if there was an error
if ( !empty( $requirements[$form_id]['error'] ) ) return false;



// Extract post values to easy variables
extract( $post );
//inserting data order

/* email functions */
function getCMS($id)
			{
				$data	=	getdbcontentshtml_cond('tblcms',"id=$id");
				//if (empty($data)) { echo "not set!"; } else { echo "set!";}
				return $data; 	
			}
				
		function sendMailCMS($id,$to,$from,$subject,$vars, $form1)
			{ 
			//echo $id.'\n '.$to.'\n '.$from.'\n '.$subject.'\n '.$priority;
				$id = (int) $id;
				$priority=0;
				$cmsArr		=	end(getCMS($id));//fetching email template from CMS 
				
				$message	=	$cmsArr["description"];
												
				foreach($vars	as	$key=>$val)	{
					
				$message	=	str_replace($key, $val, $message); }
				//echo "<pre>"; print_r($message); echo "</pre>"; die;
				/* $fh = fopen("mail.txt", 'a');
 				fwrite($fh, "To: ".$to."From : ".$from."Subject : ".$subject."Message : ".$message);	 */
				if(empty($message)) { $message = '';}
				
				$success	=	sendmail($to, $from, $subject, $message, $form1);
				return $success;
			}
		
		function getdbcontentshtml_cond($table,$cond="1",$echo=false)
			{
				$fn_sql		=	"select * from $table where $cond";
				$fn_res		=	db_query($fn_sql,$echo);
				$arrcnt		=	-1;
				$dataarr	=	array();
				while($temp	= mysql_fetch_assoc($fn_res))
					{
						foreach($temp	as $key=>$val)	$temp[$key]	=	html_entity_decode(stripslashes($val));
						$arrcnt++;
						$dataarr[$arrcnt]	=	$temp;
					}
				return $dataarr;
			}
			
		function db_query($qry,$echo=false)
			{
				if($echo)	echo $qry;
				$res	=	mysql_query($qry);
				setDbErrors(mysql_error());
				return $res;
			}
			
		function setDbErrors($var)
			{
				if(is_array($var)	&&	$var)
					{
						foreach($var	as $val)
							$this->dbErrors[]	=	trim($val);		
					}
				elseif(trim($var))	
					{
						$this->dbErrors[]	=	trim($var);				
					}
			}
			
		function sendmail($to,$from,$subject,$message,$from1="", $files="")
			{							
				ini_set("SMTP","localhost");
				ini_set("smtp_port","25");
				ini_set("sendmail_from",$from);
				
				$headers 		= 	"From: $from \r\n";
				$headers .= "Reply-To: $from1\r\n";
				$headers .= "X-Mailer: PHP/".phpversion();
				$semi_rand 		= 	md5(time());// boundary
				$mime_boundary 	= 	"==Multipart_Boundary_x{$semi_rand}x";

				// headers for attachment
				$headers 		.= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

				// multipart boundary
				$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
				$message .= "--{$mime_boundary}\n";
					
				if(is_array($files))// preparing attachments
					{
						for($x=0;$x<count($files);$x++)
							{
								$file 	=	fopen($files[$x],"rb");
								$data 	=	fread($file,filesize($files[$x]));
								$data 	=	chunk_split(base64_encode($data));
								$fname	=	end(explode("/",$files[$x]));
								fclose($file);
								$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$fname\"\n" .
											 "Content-Disposition: attachment;\n" . " filename=\"$files[$x]\"\n" .
											 "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
								$message .= "--{$mime_boundary}\n";
							}
					}
				$ok = mail($to, $subject, $message, $headers);// send
				//echo $to." ".$subject." ".$message." ".$headers;
				
				return $ok;
			}
			
		/*function sendMail($maiId,$toId,$mode,$parent="0",$thread="0")
			{			
				//Send from other user profile.
	/*			if(isset($_SESSION['visitedProfile']) && !empty($_SESSION['visitedProfile']))
					$toId = $_SESSION['visitedProfile'];
					
				$id	 =	$this->db_insert("tblmail_to",array("mail_id"=>$maiId,"to_id"=>$toId,"created_date"=>date("Y-m-d H:i:s"),"thread_id"=>$thread,"parent_id"=>$parent),"0");
				return $id;
			}*/
			
		function db_insert($table,$data=array(),$echo=false)
			{
				clearDbErrors();
				
				if($data)
					{						
						$valObj		=	siteclass::create_php_validation();
						
						if($valObj->dbValidateArray($table,$data))
							{
								$valuesArr	=	array();
								foreach($data	as	$key=>$val)	
									{
										
										if(strtolower(substr($val,-6))	==	"escape" &&	strtolower(substr($val,0,6))	==	"escape")
											{
												$val			=	str_replace("escape","",strtolower($val));
												$valuesArr[] 	=	$val;	
											}
										else
											{
												$val	=	 htmlentities(mysql_real_escape_string($val));												
												$valuesArr[] 	=	"'".$val."'";
											}
									}
									
								$keyData	=	array_keys($data);
								foreach($keyData	as $key=>$val)	$keyData[$key]	=	"`".trim($val)."`";
								$fields		=	implode(",",$keyData);
								$values		=	implode(",",$valuesArr);
								$sql		=	"insert into $table($fields) values($values)";
								$returnId	= 	(db_query($sql,$echo)) ? mysql_insert_id() : false;
								
								if($returnId)
									{
										$this->dbTrans[]	=	array("table"=>$table,"id"=>$returnId);
										return $returnId;
									}
								else
									{
										return false;
								}
							}
						else
							{
								setDbErrors($valObj->getError());
								return false;
							}
					}
			}
/* end of email function  */

function createRandom($no=8)
			 {
				$chars = "abcdefghijkmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWXYZ";
				srand((double)microtime()*1000000);
				$i = 1;
				$pass = '' ;
				while ($i <= $no)
					{
						$num = rand() % 33;
						$tmp = substr($chars, $num, 1);
						$pass = $pass . $tmp;
						$i++;
					}	
				return $pass;
			 }
			do//create random code.
			{
				$randCode				=	createRandom(25);
			}
			while(("SELECT * FROM tblusers WHERE user_code='$randCode'") > 0);

			$user_code = $randCode;
			$log_id = "SELECT login_id FROM tblusers WHERE login_id=(SELECT MAX(login_id) FROM tblusers)";
			$login = mysql_query($log_id);
			$row = mysql_fetch_array($login);
			foreach($row as $r => $v){
			$login_id1 = $v;
			}
			$login_id = $login_id1+1;
			
$enc_pass=md5($pass);
$date = date('Y-m-d H:i:s');
			
$tblusers_post = "INSERT INTO
					tblusers (user_code,login_id,first_name, last_name,  created,  state_id,dob, country_id, time_zone_id, gender)
					VALUES ('$user_code','$login_id','$firstname', '$lastname','$date',  '$state','$dob', '$country', '$time', '$gender')";
$result1 = $connection->create_result($tblusers_post);	//order executes
$user_id = $result1->get_insert_id ();

$tblusers_post1 = "INSERT INTO
					tbluser_login (login_id, user_name, user_pwd,user_group,authorized,user_role,admin_authorize,privacy_policy,created)
					VALUES ('$login_id','$conemail','$enc_pass','1','0','3','0','1','$date')";

$result11 = $connection->create_result($tblusers_post1);	//order executes
$user_id1 = $result11->get_insert_id ();

$tblusers_post4 = "INSERT INTO
     user_info (first_name, last_name,  email, street, city, state, zip, referred_by)
     VALUES ('$firstname', '$lastname', '$conemail', '$street', '$city', '$state','$zip', '$by')";
$result4 = $connection->create_result($tblusers_post4); //order executes
$user_id4 = $result4->get_insert_id ();

/*  ------ tbl address code ------*/

$state_name1= "select state_name from tblstates where  state_id='$state'";
$state_name2 = mysql_query($state_name1);
			$row1 = mysql_fetch_array($state_name2);
			foreach($row1 as $r1 => $v1){
			$state_name = $v1;
			}

$address = $street." ".$city." ".$state_name." United States ".$zip;

$tblusers_post2 = "INSERT INTO
					tbluser_address (user_id,address,email, country_code_id ,zip)
					VALUES ('$user_id','$address','$conemail','$country','$zip')";
$result3 = $connection->create_result($tblusers_post2);	//order executes
$user_id2 = $result3->get_insert_id ();

$address_id1= "select address_id from tbluser_address where  user_id='$user_id'";
$address_id2 = mysql_query($address_id1);
			$row2 = mysql_fetch_array($address_id2);
			foreach($row2 as $r2 => $v2){
			$address_id = $v2;
			}

$tblusers_post3 = "update tblusers SET address_id = '$address_id' where user_id = '$user_id'";
$result3 = $connection->create_result($tblusers_post3);	//order executes
$user_id3 = $result3->get_insert_id ();

/* ----------- End of Tbl_address code -----------*/

$subject =  'Live Music Tutor Registration'; 
       $varArr["{TPL_NAME}"]   = $firstname." ".$lastname;
       $varArr["{TPL_ACTION_URL}"]  ='https://classroom.livemusictutor.com/userAuth.php?id='.base64_encode($login_id);
       $varArr["{TPL_URL}"]   = 'https://classroom.livemusictutor.com/';
           
		   $nums=76;
		   $mai="info@livemusictutor.com";
       $send    = sendMailCMS($nums, $conemail ,$mai,$subject,$varArr,5);
	   
	   
       

if($user_id > 0){
	foreach ( $form as $input_id => $input_value ) {
		if (!empty ($form[$input_id]['table'])) {
			if ('tbl_profile_other_data' == $form[$input_id]['table'] ||
			    'tbl_profile_other_data_long' == $form[$input_id]['table']) {
				if (empty ($form[$input_id]['code'])) {
					Die ("CODE not set for $input_id.");
				}
				$table = $form[$input_id]['table'];
				$code = $form[$input_id]['code'];
				$cols = array ('user_id', 'other_data_code', 'value');

				if (is_array ($post[$input_id])) {
					foreach ($post[$input_id] as $key => $value) {
						if (!empty ($post[$input_id])) {
							$vals = array ($user_id4, $code, $value);
							$input_result = save_to_db ($connection, $table, $cols, $vals, $user_id);
						}
					}
				} else {
					if (!empty ($post[$input_id])) {
						$vals = array ($user_id4, $code, $post[$input_id]);
						$input_result = save_to_db ($connection, $table, $cols, $vals, $user_id);
					}
				}
			}
		}
		if (!empty ($form[$input_id]['location']) && !empty ($post[$input_id])) {
			move_uploaded_file ($file_temp, $form[$input_id]['location'] . "/" . $post[$input_id]);
			

		}
	}
	
	// Redirect user to the thank you page

	//header( 'Location: ' . $thank_you_page .$firstname);
	
	header("Location: thankyou.php?first_name=".$firstname." &last_name=".$lastname);
} else{
	echo "<div class='alert alert-warning col-sm-6 center-block' style='width:400px; margin:0 auto;'>
	  <button type='button' class='close' data-dismiss='alert'>×</button>
	  <strong>Oops!</strong> Looks like an error has occured. <br> Our developers are on it. Sit tight!
	</div>"; mysql_error();
}

//if ( !function_exists( 'save_to_db' ) ) {
	function save_to_db( $connection, $table, $cols, $vals, $user_id ) {
		$insert = "INSERT INTO $table (";
		$first = true;
		foreach ($cols as $col_name){
			if (true == $first) {
				$insert .= $col_name;
				$first = false;
			} else {
				$insert .= ", " . $col_name;
			}
		}
		$insert .= ") VALUES (";
		$first = true;
		foreach ($vals as $value){
			if (is_int ($value)) {
				$db_val = intval ($value);
			} elseif (is_string ($value)) {
				$db_val = "'" . mysql_real_escape_string($value) . "'";
			}
			if (true == $first) {
				$insert .= $db_val;
				$first = false;
			} else {
				$insert .= ", " . $db_val;
			}
		}
		$insert .= ")";
		
		/* by giri*/
		if($vals[1]=="dlst" || $vals[1]=="gst")
		{
			$sql_dlst='SELECT value FROM tbl_lookup_misc WHERE lookup_id='.$vals[2];
			$sql_dlst_result=mysql_query($sql_dlst);
			$sql_dlst_array = mysql_fetch_assoc($sql_dlst_result);
			
			$sql_admin_dlst="SELECT id from tbl_flag where name like '%".$sql_dlst_array['value']."%'";
			$sql_admin_dlst_result=mysql_query($sql_admin_dlst);
			$sql_admin_dlst_array = mysql_fetch_assoc($sql_admin_dlst_result);
			
			$tbl_pmm_instructor_lookup_sql="insert into tbl_pmm_instructor_lookup(instructor_id,assoc_flag_id) values(".$user_id.",".$sql_admin_dlst_array['id'].")";
			$connection->create_result ($tbl_pmm_instructor_lookup_sql);
		}
		if($vals[1]=="tins")
		{
			$sql_user_instruments="insert into tbluser_instruments(user_id, instrument_id, created) values(".$user_id.",".$vals[2].",'now()');";
			$user_instrument_result=$connection->create_result($sql_user_instruments);
		}
		
		if($vals[1]=="dage")
		{
			
			$tbl_pmm_instructor_lookup_sql="insert into tbl_pmm_instructor_lookup(instructor_id,assoc_flag_id) values(".$user_id.",".$vals[2].")";
			return ( $connection->create_result($tbl_pmm_instructor_lookup_sql) );
			
			
		}
		
		/*by giri */
		
		return ($connection->create_result ($insert));
	}

//}