<?php

/**
 * No need to touch the code in here
 */

require dirname( dirname( __FILE__ ) ) . '/config/forms.php';
//require_once('captcha/ayah_1.1.4/ayah.php');

if ( !function_exists( 'generate_form' ) ) {
	function generate_form($connection, $form_id ) {
		global $forms;
		global $aux_data;
		global $lookup_data;
		load_lookup_data ($connection);

		if ( !is_array( $forms[$form_id] ) || empty( $forms[$form_id] ) ) return false;

		global $requirements;
		global $post;

//		$ayah = AYAH_PUBLISHER_KEY;
//		$ayah = (empty($ayah) || $ayah == 'xxx');
//		if ( !$ayah && !empty($forms[$form_id]['captcha']['ayah']) ) {
//			$integration = new AYAH();
//			$passed = !empty($_POST[$form_id]) ? $integration->scoreResult() : true;
//
//			if ( !$passed ) $requirements[$form_id] = array(
//				'error' => true,
//				'error_type' => 'warning',
//				'error_message' => 'Incorrect captcha! Please try again'
//			);
//		}

		if ( !empty( $requirements[$form_id]['error'] ) ) {
			if (empty ($requirements[$form_id]['error_message'])) {
				$requirements[$form_id]['error_message'] = "Please correct any input errors.";
			}
			if (true == $requirements [$form_id]['file']) {
				$requirements[$form_id]['error_message'] .= "<br /><strong>Please re-upload your Resume.</strong>";
			}
			display_warning_message( $requirements[$form_id]['error_message'], $requirements[$form_id]['error_type'] );
		}

		load_auxillary_data ($connection);

		foreach ( $forms[$form_id] as $field_id => $field ) :
			if ( empty( $field ) || empty( $field['type'] ) ) continue;

			$defaults = array(
				'label'     => '',
				'value'    => null,
				'values'   => array (),
				'desc'     => '',
				'pholder'  => '',
				'class'    => '',
				'tip'      => '',
				'css'      => '',
				'type'     => '',
				'required' => false,
				'source'   => '',
				'options'  => array(),
				'default'  => '',
				'query'    => '',
				'restrict' => array(),
				'table'    => '',
				'code'     => '',
				'error_message' => '',
				'validate' => '',
				'location' => '',
				'filter'   => FILTER_SANITIZE_STRING,
				'flags'    => null,
			);
		extract( shortcode_atts ($defaults, $field) );

		$restrict_defaults = array(
			'min'  => 0,
			'max'  => '',
			'step' => 'any',
		);
		$restrict = shortcode_atts( $restrict_defaults, $restrict );

		if (array_key_exists ($field_id, $aux_data)) {
			$label = $aux_data [$field_id]['od_question'];
			$desc  = $aux_data [$field_id]['od_desc'];
		}

		$required_fields = !empty( $requirements[$form_id]['ids'] ) ? array_values( $requirements[$form_id]['ids'] ) : array();
		$error_class = !empty( $required_fields ) && in_array( $field_id, $required_fields ) ? 'error' : '';

		$title = ( $required ? '<span class="required">*</span> ' : '' ) . $label;
		if (!empty ($post[$field_id])) {
			if (is_array ($post[$field_id])) {
				$values = $post[$field_id];
			} else {
				$value = $post[$field_id];
			}
		}
		$name  = $field_id;
		$id    = $form_id . "-" . $field_id;
		$description = $desc ? sprintf( '<span class="help-block">%s</span>', $desc ) : '';


		switch ( $type ) :

		case 'instruction': ?>
				<!--div class="control-group">
					<label for="<?php echo $field_id; ?>"><?php echo $title; ?></label>
				</div-->

			<?php break;

		case 'tel':
		case 'number':
		case 'email' :
		case 'url'   :
		case 'date'  :
		case 'text'  :  ?>
			<?php if ($id =='advanced-firstname'):?>
			<?php echo "<div id='first_step' style='margin-top:20px;'>"?>
			<?php endif; ?>
		<div class="control-group <?php echo $error_class; ?>">
		<div class="tbdescription"><?php echo $description; ?></div>
		<label class="control-label"  for="<?php echo $field_id; ?>"><?php echo $title; ?></label>

		<div class="controls">

				<input name="<?php echo $name; ?>" id="<?php echo $id; ?>" type="
				<?php echo $type; ?>"<?php if ( $type == 'number' ): ?>
				min="<?php echo $restrict['min']; ?>"
				max="<?php echo $restrict['max']; ?>"
				step="<?php echo $restrict['step']; ?>" <?php endif; ?>
				class="regular-text <?php echo $class; ?>"
				placeholder="<?php echo $pholder; ?>"<?php if ( $value ) : ?>
				value="<?php echo $value; ?>"<?php endif; ?>/>


			</div>

			<?php if (!(empty ($requirements[$form_id][$field_id]['error_message']))): ?>
				<div class="controls">
					<p class="form_error"><?php echo $requirements[$form_id][$field_id]['error_message'] ?></p>
				</div>
			<?php endif; ?>

			</div>
			<?php if ($id =='advanced-by'):?>
			<?php echo "<input class='light-orange light-orange' style='float:right;height:32px;width:100px;border-radius:5px;' type='submit' name='submit_first' id='submit_first' value='NEXT'/>"?>
			<?php echo "</div>"?>
			<?php echo "<div id='second_step' style='margin-top:20px;'>"?>
			<?php endif; ?>



<?php break;

case 'checkbox': ?>

	<div class="control-group <?php echo $error_class; ?> checkboxGroup <?php echo $id; ?>" >
		  <div class="description"><?php echo $description; ?></div>
		  <label class="control-label" for="<?php echo $id; ?>"><?php echo $title; ?></label><br/><br/>
			<div class="controls">
				<?php if (isset ($source) && 'db' == $source) {
					$options = load_options ($connection, $query, $options);
				}
					 if (count($options) > 1) {
						$is_array = "[]";
					 } else {
					 	$is_array = null;
					 }
				?>
              <?php foreach ( $options as $key => $opt_val ) : ?>
					<input type="checkbox" name="<?php echo $name . $is_array; ?>"
					id="<?php echo $id . "-" . sanitize_title( $key ); ?>"
					class="<?php echo $class; ?>"
					value="<?php echo $key; ?>"
							<?php if (in_array ($key, $values)) {
									echo ('checked');
								  }
					              else {
					              		if (is_array ($default)) {
					              			if (in_array ($key, $default)) {
					              				echo ('checked');
					              			}
					              		}
					              	    else {
					              			if ($key == $default) {
					              				echo ('checked');
					              			}
					              	    }
					              }
							?>
						   />
							<?php echo $opt_val; ?><br />

				<?php endforeach;?>
			</div>
			<?php if (!(empty ($requirements[$form_id][$field_id]['error_message']))): ?>
				<div class="controls">
					<p class="form_error"><?php echo $requirements[$form_id][$field_id]['error_message'] ?></p>
				</div>
			<?php endif; ?>
			</div>
			<?php if ($id . "-" . sanitize_title( $key ) =='advanced-tfe-6'):?>
			<?php echo "<input class='back light-orange' type='button' value='BACK' />"?>
			<?php echo "<input class='light-orange' style='float:right;height:32px;width:100px;border-radius:5px;'  type='submit' name='submit_second' id='submit_second' value='NEXT' />"?>
			<?php echo "</div>"?>
			<?php echo "<div id='third_step' style='margin-top:20px;'>"?>
			<?php endif; ?>

		<?php break;

		case 'checkbox-group': ?>

		  <div class="control-group <?php echo $error_class; ?> allCheckbox"  style="width:300px; float:left;">
          <div class="description"><?php echo $description; ?></div>
		  <label class="control-label" for="<?php echo $id; ?>"><?php echo $title; ?></label>
			<div class="controls">
				<?php if (isset ($source) && array_key_exists ($source, $lookup_data)) {
							$options = $lookup_data [$source];
						} else {
							echo "<p class=\"error\">UNKNOWN SOURCE $source.</p>";
							exit;
						}
				?>
              <?php
				$cur_group_name = null;
				foreach ( $options as $key => $group_info ) :
					if ($group_info ['group_name'] != $cur_group_name):
						$cur_group_name = $group_info ['group_name'];
				?>
						<?php echo ("<p class=\"group_name\"> $cur_group_name </p>");?>
				<?php endif; ?>

					<input type="checkbox" name="<?php echo $name . '[]'; ?>" id="<?php echo $id . "-" . sanitize_title( $key ); ?>" class="<?php echo $class; ?>" value="<?php echo $key; ?>"
							<?php if (in_array ($key, $values)) {
								echo ('checked');
							}
              	else {
              		if (is_array ($default)) {
              			if (in_array ($key, $default)) {
              				echo ('checked');
              			}
              		}
              		else {
              			if ($key == $default) {
              				echo ('checked');
              			}
              		}
              	}
              	?>
							/>
				<?php echo $group_info['name']; ?><br />

				<?php endforeach;  ?>
			</div>
			<?php if (!(empty ($requirements[$form_id][$field_id]['error_message']))): ?>
				<div class="controls">
					<p class="form_error"><?php echo $requirements[$form_id][$field_id]['error_message'] ?></p>
				</div>
			<?php endif; ?>
		  </div>
			<?php if ($id . "-" . sanitize_title( $key ) =='advanced-tins-59'):?>
			<?php echo "<input class='back light-orange' type='button' value='BACK' />"?>
			<?php echo "<input class='light-orange' style='float:right;height:32px;width:100px;border-radius:5px;' type='submit' name='submit_third' id='submit_third' value='NEXT' />"?>
			<?php echo "</div>"?>
			<?php echo "<div id='fourth_step' style='margin-top:20px;'>" ?>
			<?php endif; ?>
		<?php break;

	case 'select':

		$selected = $value ? $value : $default; ?>

		  <div class="control-group <?php echo $error_class; ?>">
			<label class="control-label" for="<?php echo $id; ?>"><?php echo $title; ?></label>
			<div class="controls">

				<select id="<?php echo $id; ?>"
				class="<?php echo $class; ?>"
				style="<?php echo $css; ?>"
				name="<?php echo $name; ?>">

				<?php if (isset ($source) && 'db' == $source) {
					$options = load_options ($connection, $query, $options);
				}
				?>
				<?php foreach ( $options as $key => $opt_val ) : ?>
						<option value="<?php echo $key; ?>" <?php echo selected( $selected, $key, 'selected' ); ?>>
						<?php echo $opt_val; ?>
						</option>
				<?php endforeach; ?>
				</select>
			</div>
			<?php if (!(empty ($requirements[$form_id][$field_id]['error_message']))): ?>
				<div class="controls">
					<p class="form_error"><?php echo $requirements[$form_id][$field_id]['error_message'] ?></p>
				</div>
			<?php endif; ?>
		</div>
		<script>$(function() {$("# <?php echo $id; ?>").select2({ width: '314px', class: 'select_ul' });});</script>



		<?php break;

	case 'radio': ?>

		<?php $selected = $value ? $value : $default; ?>

		  <div class="control-group <?php echo $error_class; ?>allgroup <?php echo $id . '-' . sanitize_title( $key ); ?>">
		  <div class="description"><?php echo $description; ?></div>
		  <label class="control-label"><?php echo $title; ?></label>
			<div class="controls">
				<?php if (isset ($source) && 'db' == $source) {
					$options = load_options ($connection, $query, $options);
				}
				?>

				<?php foreach ( $options as $key => $opt_val ) : ?>

					<label class="radio">
					<input type="radio" name="<?php echo $name; ?>"
					id="<?php echo $id . '-' . sanitize_title( $key ); ?>"
					value="<?php echo $key; ?>"
				    class="<?php echo $class; ?>"
							<?php if ( $value == $key ) echo "checked"  ?>/> <?php echo $opt_val; ?>

					</label>

				<?php endforeach; ?>

		  </div>
			<?php if (!(empty ($requirements[$form_id][$field_id]['error_message']))): ?>
				<div class="controls">
					<p class="form_error"><?php echo $requirements[$form_id][$field_id]['error_message'] ?></p>
				</div>
			<?php endif; ?>

		  </div>

			<?php if ($id . '-' . sanitize_title( $key ) =='advanced-mnt-37'):?>
			<div id="last_box">Please verify your application data now. Once you submit this page, you will not be able to go back and make changes.<br/><br/>
			On the next page you will be given the opportunity to upload your resume (if you have one), as well as choose the best contact time for LiveMusicTutor to reach you.<br/><br/>
			<span style="font-weight:bold">PLEASE NOTE: Your application is NOT complete until you click the "SEND" button on the next page.</span>
			</div>
			<?php echo "<input class='back light-orange' type='button' value='BACK' />"?>

			<?php echo "<input class='light-green' style='float:right;height:32px;width:150px;border-radius:5px;'  type='submit' name='submit_fourth' id='submit_fourth' value='Submit and Finalize' />"?>
			<?php echo "</div>"?>
			<?php echo "<div id='fifth_step' style='margin-top:20px;'>"?>
			<?php endif; ?>
		<?php break;

	case 'textarea': ?>

		  <div class="control-group <?php echo $error_class; ?>">
			<label class="control-label" for="<?php echo $id; ?>"><?php echo $title; ?></label>
			<div class="controls">
				<textarea name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="large-text <?php echo $class; ?>" style="<?php if ( $css ) echo $css; else echo 'width:300px;'; ?>" placeholder="<?php echo $pholder; ?>" rows="3"><?php echo ( $value ) ? $value : $std; ?></textarea>
						<?php echo $description; ?>
			</div>
			<?php if (!(empty ($requirements[$form_id][$field_id]['error_message']))): ?>
				<div class="controls">
					<p class="form_error"><?php echo $requirements[$form_id][$field_id]['error_message'] ?></p>
				</div>
			<?php endif; ?>

		</div>


		<?php break;

	case 'file': ?>

		  <div class="control-group <?php echo $error_class; ?>" id="file_upload_div">
			<label class="control-label" for="<?php echo $id; ?>"><?php echo $title; ?></label>
			<div class="controls">
				<input type="file" name="<?php echo $name; ?>"
				id="<?php echo $id; ?>"
		        class="<?php echo $class; ?>"
				style="<?php if ( $css ) echo $css;  ?>"
				placeholder="<?php echo $pholder; ?>" />
				<div id="file_help_text"><?php echo $description; ?><br/>If you have problems uploading the resume please send it to staffing@livemusictutor.com</div>
			</div>
			<?php if (!(empty ($requirements[$form_id][$field_id]['error_message']))): ?>
				<div class="controls">
					<p class="form_error"><?php echo $requirements[$form_id][$field_id]['error_message'] ?></p>
				</div>
			<?php endif; ?>

		</div>

<?php if ($id =='advanced-call-18'):?>
	<?php echo "</div>"?>
			<?php endif; ?>
			<?php break;

	default:
		// code...
		break;
		endswitch;

		endforeach;

		// Output the token
		if ( empty( $_SESSION['jigowatt']['adv-html-form'][$form_id]['token'] ) ) $_SESSION['jigowatt']['adv-html-form'][$form_id]['token'] = md5( uniqid( mt_rand(), true ) );
		?><input type="hidden" name="<?php echo 'token'; ?>" value="<?php echo $_SESSION['jigowatt']['adv-html-form'][$form_id]['token']; ?>">
		<?php

	}
}

if ( !function_exists( 'selected' ) ) {
	function selected( $input, $std, $type ) {
		return ( $input === $std ) ? $type . "='{$type}'" : '';
	}
}
if ( !function_exists( 'shortcode_atts' ) ) {
	function shortcode_atts( $pairs, $atts ) {
		$atts = (array)$atts;
		$out = array();
		foreach ( $pairs as $name => $default ) {
			if ( array_key_exists( $name, $atts ) )
				$out[$name] = $atts[$name];
			else
				$out[$name] = $default;
		}
		return $out;
	}
}
if ( !function_exists( 'display_warning_message' ) ) {
	function display_warning_message( $message, $type = 'warning' ) {
?>

	<div class="alert alert-<?php echo $type; ?>">
	  <button type="button" class="close" data-dismiss="alert">ﾃ�/button>
	  <?php echo $message; ?>
	</div>

	<?php
	}
}

if ( !function_exists ('load_options')) {
	function load_options($connection, $query, $options) {
		$options_result = $connection->create_result ($query);
		$tmp = array ();
		foreach ($options as $key_val => $val) {
			$tmp [$key_val] = $val;
		}
		while ($options_row = $options_result->get_row ()) {
			$tmp [$options_row [0]] = $options_row [1];
		}
		return ($tmp);
	}
}


/**
 * Sanitizes titles intended for SQL queries.
 *
 * Specifically, HTML and PHP tag are stripped. The return value
 * is not intended as a human-readable title.
 *
 * @param string  $title The string to be sanitized.
 * @return    string    The sanitized title.
 */
if ( !function_exists( 'sanitize_title' ) ) {
	function sanitize_title( $title ) {

		$title = strtolower( $title );
		$title = preg_replace( '/&.+?;/', '', $title ); // kill entities
		$title = str_replace( '.', '-', $title );
		$title = preg_replace( '/[^%a-z0-9 _-]/', '', $title );
		$title = preg_replace( '/\s+/', '-', $title );
		$title = preg_replace( '|-+|', '-', $title );
		$title = trim( $title, '-' );

		return $title;

	}
}