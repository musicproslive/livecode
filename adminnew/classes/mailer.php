<?php


require dirname( dirname( __FILE__ ) ) . '/config/config.php';

//if ( !defined ('AYAH_PUBLISHER_KEY') ) define( 'AYAH_PUBLISHER_KEY', $captcha['ayah']['publisher_key'] );
//if ( !defined ('AYAH_SCORING_KEY') )   define( 'AYAH_SCORING_KEY'  , $captcha['ayah']['scoring_key'] );

//require_once dirname( dirname( __FILE__ ) ) . "/classes/DB_Connect.php";
require 'generator.php';

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
$tblusers_post = "INSERT INTO
					user_info (first_name, last_name, phone, email, street, city, state, zip, referred_by)
					VALUES ('$firstname', '$lastname', '$phone', '$email', '$street', '$city', '$state','$zip', '$referred')";
$result1 = $connection->create_result($tblusers_post);	//order executes
$user_id = $result1->get_insert_id ();
print_r($firstname);

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
							$vals = array ($user_id, $code, $value);
							$input_result = save_to_db ($connection, $table, $cols, $vals);
						}
					}
				} else {
					if (!empty ($post[$input_id])) {
						$vals = array ($user_id, $code, $post[$input_id]);
						$input_result = save_to_db ($connection, $table, $cols, $vals);
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
	echo("<div class='alert alert-warning' style='width:400px; margin:0 auto;'>
	  <button type='button' class='close' data-dismiss='alert'>×</button>
	  <strong>Oops!</strong> Looks like an error has occured. <br /> Our developers are on it. Sit tight!
	</div>"); mysql_error();
}

//if ( !function_exists( 'save_to_db' ) ) {
	function save_to_db( $connection, $table, $cols, $vals ) {
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
		return ($connection->create_result ($insert));
	}
//}