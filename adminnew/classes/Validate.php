<?php

class Validate {
    private $msgArray = array ();

    public function __construct () {
    }

    function validate_post (&$input_var, // set to the post value
        $post_name, // $_POST name of input
        $dsp_name, // display name of input &$err_var, // string where error message is output
        $min_len, // if non-zero, empty input not allowed
        $max_len, // if zero, any length allowed
        $regex_exp = null, // if set, input is checked against this
        $regex_err = null, // and this is the error message should not be null unless $regex_exp is null
        $do_trim = true, // trim the input
        $do_striptags = true, // strip the input
        $allowed_tags = null) { // allowed HTML tags, generally null
        // if min len is greater than zero, empty values are not allowed
        if (empty ($_POST [$post_name]) && $min_len > 0) {
            $err_var = "$dsp_name is required." . $_POST[$post_name];
            return false;
        }
        if (!empty ($_POST [$post_name])) {
            $input_var = $_POST [$post_name];
            $len = strlen ($input_var);
            if ($min_len > 0 && $len < $min_len) {
                $err_var = "$dsp_name must be between $min_len and $max_len.";
                return false;
            }

            if ($max_len > 0 && $len > $max_len) {
                $err_var = "$dsp_name must be between $min_len and $max_len.";
                return false;
            }
            if ($do_trim)
                $input_var = trim ($input_var);
            if ($do_striptags)
                $input_var = strip_tags ($input_var, $allowed_tags);
            if (!empty ($regex_exp)) {
                if (!preg_match ($regex_exp, $input_var)) {
                    $err_var = str_replace ("DSP_NAME", $dsp_name, $regex_err);
                    return false;
                }
            }
        }
        return true;
    }

    public function name ($name, $expected) {
        $err_msg = null;

        if (!empty ($name)) {
            if (!preg_match ("/^[a-zA-Z\s.\-_']+$/", $name)) {
                $err_msg = "Please use only letters and special characters (-, _, ') for your $expected.";
            }
        } else {
            $err_msg = "Please enter a $expected";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function title ($name, $expected) {
        $err_msg = null;
        if (!empty ($name)) {
            if (!preg_match ("/^[a-zA-Z0-9\s.\-_\(\)']+$/", $name)) {
                $err_msg = "Please use only letters, numbers and dash, underscore and parenthese for your $expected.";
            }
        } else {
            $err_msg = "Please enter a $expected";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function img_name ($img_name, $expected) {
        $err_msg = null;

        if (!empty ($img_name)) {
            if (!preg_match ("/^.*\.(jpg|jpeg|gif|JPG|png|PNG)$/", $img_name)) {
                $err_msg = "Please upload an image file type (jpg, jpeg, gif, png) for your $expected.";
            }
        } else {
            $err_msg = "Please enter a $expected";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function price ($price, $expected) {
        $err_msg = null;

        if (!empty ($price)) {
            if (!preg_match ("/^\d+(\.\d{2})?$/", $price)) {
                $err_msg = "Please enter a valid number (no DOLLAR SIGN) for your $expected.";
            }
        } else {
            $err_msg = "Please enter a $expected";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function username ($username, $expected) {
        $err_msg = null;

        if (!empty ($username)) {
            if (!preg_match ("/^[a-zA-Z][a-zA-Z0-9]+$/", $username)) {
                $err_msg = "Please use only letters and numbers for your $expected";
            }
        } else {
            $err_msg = "Please enter a $expected.";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

	public function dimensions ($target, $expected) {
		$err_msg = null;

		if (!empty ($target)) {
			if (!preg_match ("~^([1-9][\d]*([ ][1-9][\d]*/[1-9][\d]*)?)([\'\"]|[ ]*(in|ft|yds|yd|cm|m))([ ]*x[ ]*([1-9][\d]*([ ][1-9][\d]*/[1-9][\d]*)?)([\'\"]|[ ]*(in|ft|yds|yd|cm|m)))+$~", $target)) {
				$err_msg = "Examples for $expected: 24\" x 3' OR 6 cm by 1m ";
			}
		} else {
			$err_msg = "Please enter a $expected.";
		}

		if (!is_null ($err_msg)) {
			$this->msgArray[] = $err_msg;
		}

		return $err_msg;
	}

    public function posval ($username, $expected) {
        $err_msg = null;

        if (!empty ($username)) {
            if (!preg_match ("/^[1-9][0-9]+$/", $username)) {
                $err_msg = "Please use only numbers for your $expected";
            }
        } else {
            $err_msg = "Please enter a $expected.";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function phone ($phone, &$phone_db) {
        $err_msg = null;

        if (!empty ($phone)) {
            $phone_tmp = preg_replace ("/[^0-9]/", "", $phone);

            if (strlen ($phone_tmp) != 10) {
                $err_msg = "Please enter a valid phone number.";
            } else {
                $phone_db = $phone_tmp;
            }
        } else {
            $err_msg = "Please enter a valid phone number.";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function email ($email, $expected) {
        $err_msg = null;

        if (!empty ($email)) {
            if (!preg_match ("/^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/", $email)) {
                $err_msg = "That is not a valid $expected";
            }
        } else {
            $err_msg = "Please enter a $expected";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function promo_code ($name, $expected) {
        $err_msg = null;

        if (!empty ($name)) {
            if (!preg_match ("/^[A-Z][A-Z0-9]{4}$/", $name)) {
                $err_msg = "Please use only A-Z and 0-9 for your $expected. The code must start with a letter and be 5 characters long.";
            }
        } else {
            $err_msg = "Please enter a $expected";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function street ($name, $expected, $min = 0, $max = 50) {
        $err_msg = null;

        if (!empty ($name)) {
            if (!preg_match ("/^[a-zA-Z0-9\s.\-\#']+$/", $name)) {
                $err_msg = "Please use only A-Z and 0-9 for your $expected.";
            }else {
                $len = strlen ($name);
                if ($len < $min) {
                    $err_msg = "$expected must be between at least $min characters long.";
                }else {
                    if ($len > $max) {
                        $err_msg = "$expected can not be more than $max characters long.";
                    }
                }
            }
        } else {
            if ($min > 0) {
                $err_msg = "Please enter a $expected";
            }
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function city ($name, $expected, $min = 0, $max = 30) {
        $err_msg = null;

        if (!empty ($name)) {
            if (!preg_match ("/^[a-zA-Z\s.\-']+$/", $name)) {
                $err_msg = "Please use only A-Z and 0-9 for your $expected.";
            }else {
                $len = strlen ($name);
                if ($len < $min) {
                    $err_msg = "$expected must be between at least $min characters long.";
                }else {
                    if ($len > $max) {
                        $err_msg = "$expected can not be more than $max characters long.";
                    }
                }
            }
        } else {
            $err_msg = "Please enter a $expected";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function zip5 ($name, $expected) {
        $err_msg = null;

        if (!empty ($name)) {
            if (!preg_match ("/^[0-9]+$/", $name)) {
                $err_msg = "Please use only numbers for your $expected.";
            }else {
                $len = strlen ($name);
                if ($len != 5) {
                    $err_msg = "$expected must be 5 digits.";
                }
            }
        } else {
            $err_msg = "Please enter a $expected";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function compare ($compareArray, $expectedArray) {
        $err_msg = null;

        if ($compareArray[0] !== $compareArray[1]) {
            $err_msg = "The {$expectedArray[0]} and {$expectedArray[1]} do not match.";
        }

        if (!is_null ($err_msg)) {
            $this->msgArray[] = $err_msg;
        }

        return $err_msg;
    }

    public function validate () {
        if (count ($this->msgArray) > 0) {
            return $this->msgArray;
        } else {
            return false;
        }
    }

    public function addToErrors ($string) {
        $this->msgArray[] = $string;
    }

    public function createMsgString ($array) {
        $output = "\r\n<fieldset><legend>Form Errors</legend>\r\n";

        foreach ($array as $value) {
            $output .= "\t<span class=\"error_msg\"><p>$value</p></span>\r\n";
        }

        $output .= "</fieldset>\r\n";

        return $output;
    }
}

?>