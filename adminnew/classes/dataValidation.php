<?php
/**************************************************************************************
Created By 	:	PREM PRANAV
Created On	:	NOV-30-2012
Purpose		:	Validate Input 
**************************************************************************************/

class dataValidation extends siteclass
	{
		function lengthCheck($str,$min="0",$max="0")
			{
				if ($min <= strlen(trim($str))) {
					if ($max <=0) {
						return true;
					} else {
						if (strlen(trim($str)) <= $max) {
							return true;
						}
						else {
//							echo "<h1> Invalid LENGTH lengthCheck '$str': min = $min, max = $max</h1>";
//							exit;
							return false;
						}
					}
				}
				else {
					return true;
				}
			}

		function validateInt($str,$min="0",$max="0")
			{
								
				if (!empty($str) and !is_null($str)) {
					if ($this->lengthCheck($str,$min,$max))	{
						if (preg_match('|^[0-9]*$|',$str)) {
							return true;
						}
						else {
//							echo "<h1>NO MATCH validateInt '$str'</h1>";
//							exit;
							return false;
						}
					}
					else {
//						echo "<h1>BAD LENDTH validateInt '$str' min = $min / max = $max</h1>";
//						exit;
						return false;
					}
				}
				else {
					return true;
				}	
			}
		function validateDate($str,$min="0",$max="0")
		{
			$flag	=	0;					
				if(!empty($str) and !is_null($str))
					{								
						if($this->lengthCheck($str,$min,$max))	
							{
								if (preg_match('|^[0-9,/]{0,10}$|',$str))$flag=1;
							}							
						if($flag==0)	return false;
						else	return true;	
					}
					return true;
		}
		
		function validateName($str,$min="0",$max="0")
			{
								
				if (!empty($str) and !is_null($str)) {	
					if ($this->lengthCheck($str,$min,$max)) {
						if (preg_match('|^\s*[a-zA-Z0-9,_\.\s]+\s*$|',$str)) {
							return true;
						} else {
//							echo "<h1>NO MATCH validateName '$str'</h1>";
//							exit;
							return false;
						}
					} else {
//						echo "<h1>bad length validateName '$str' min = $min / max = $max</h1>";
//						exit;
						return false;
					}
				}
				return true;			
			}
		function validateFirstName ($str) {
			if (!empty($str)) {
				if (preg_match ("/^([a-zA-Z]{1,30}[\- \']{0,1}){1,5}$/", $str)) {
					return true;
				} else {
					return false;
				}
			}
			return false;
		}

		function validateLastName ($str) {
			if (!empty($str)) {
				if (preg_match ("/^([a-zA-Z]{1,30}[\- \']{0,1}){1,5}[ ]?([JS][Rr]\.?|III|IV)?$/", $str)) {
					return true;
				} else {
					return false;
				}
			}
			return false;
		}
		function validateEmail ($str, $min = "0", $max = "0")
			{			
				 if (!empty($str) and !is_null($str)) {					
//								$rg = '#^([\w-\+\.]+@([\w-]+\.)+[\w-]{2,9})?$#';
				 	if ($this->lengthCheck($str,$min,$max)) {
						$rg = '/^[-a-zA-Z0-9~!$%^&*_=+}{\'?]+(\.[-a-zA-Z0-9~!$%^&*_=+}{\'?]+)*@([a-zA-Z0-9_][-a-zA-Z0-9_]*(\.[-a-zA-Z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-zA-Z][a-zA-Z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/';
						if (preg_match($rg,$str)) {
							return true;
						}	
						else {
//							echo "<h1>NO MATCH validateEmail '$str'</h1>";
//							exit;
							return false;	
						} 
					} else {
						return false;
					}
				} else {
					return true;			
				}
			}
		
		function validatePassword($str, $min = "0", $max = "0")
			{
				if (!empty($str) and !is_null($str)) {
//					if (preg_match('|^[0-9a-zA-Z _/\:\;\-\=\+\#\*\^\[\]\$!@%\(\)\'\"&\,\.)]*$|',$str)) {
					if (preg_match ('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).{6,18}$/', $str)) {
						return true;
					}
					else {
//						echo "<h1>NO MATCH validatePassword '$str'</h1>";
//						exit;
						return false;
					}
				}
				return false;		
			}

		function validateAddress($str,$min="0",$max="0")
			{				
				if (!empty($str) and !is_null($str)) {
					if ($this->lengthCheck($str,$min,$max)) {										
						if (preg_match('|^[a-zA-Z0-9\s\\\r\\\n\:\;\.\-\,/\#\'\"\_]*$|',$str)) {
							return true;
						} 
						else {
//							echo "<h1>NO MATCH validateAddress '$str'</h1>";
//							exit;
							return false;
						}
					}
					else {
//						echo "<h1>BAD LENDTH validateAddress '$str' min = $min / max = $max</h1>";
//						exit;
						return false;
					}
				}
				return true;			
			}
	
	//for edit profile null entry is also valid 
	
		function validateLang($str,$min="0",$max="0")
			{
				$flag=0;				
				if(!empty($str) and !is_null($str))
					{					
						if($this->lengthCheck($str,$min,$max))
							{										
								//if(preg_match('|^[a-zA-Z0-9,\s- ]*$|',$str))	$flag	=	1;
								if(preg_match('/[^,;a-zA-Z0-9_-]|[,;]$/s',$str))	$flag	=	1;
							}
						//exit($flag);					
						if($flag==0)	return false;
						else 		return true;	
					}
					return true;			
			}
		function validateAboutMe($str,$min="0",$max="0")
			{
				$flag=0;				
				if(!empty($str) and !is_null($str))
					{					
						if($this->lengthCheck($str,$min,$max))
							{							
								if(preg_match('|^[a-zA-Z0-9\\\r\\\n\\s\\:\\;\\.\\-\\,/\\#\\\'\\"\\_]*$|',$str) )	$flag	=	1;
							}				
						if($flag == 0)	 return false; 
						else	return true;	
					}
					return true;			
			}
			
		function validateUrl($url){
			
			if(empty($url)) {
				return true;
			}else {
				$flag=0;
				if(!empty($url) and !is_null($url)){
					if (preg_match("#^https?://.+#", $url) )  $flag	 =	1;
				}
				if($flag == 0)	 return false;
				else 	return true;
			}
			
		}
		
		function validateSerializedData($str,$min="0",$max="0")
			{
				$flag=0;				
					if(!empty($str) and !is_null($str))
						{					
							if($this->lengthCheck($str,$min,$max))
								{							
									if(preg_match('/^[a-zA-Z0-9= _\s]*$/',$str) )	$flag	=	1;
								}				
							if($flag == 0)	 return false; 
							else	return true;	
						}
						return true;	
			}
		// check all array element whether data is vulnerable.recursively it will test array element inside array.
		function osInjection($data)
			{
				foreach($data	as	$key=>$val)	
					{
						if(is_array($val) || empty($val)) 
							{																
								if($this->osInjection($val));
								else	return false;
								continue;
							}	
						/*echo 'Key: '.$key.'>> Value: '.$val."<br />";
						if($this->validateName($key))
							echo "name ok<br />";
						else
							echo "Invalid name";	
						
						if(preg_match('|^[^\\|\\;\\$\\(\\)<>]*$|', $val))
							echo "value ok<br />";
						else
							echo "Invalid value<br>";	
							
						if(!preg_match('/ping|waitfor|delay|benchmark/',$key))
							echo "ping value ok<br>";
						else
							echo "Invalid value ping<br>";	
							
						if(!preg_match('/ping|waitfor|delay|benchmark/',$val))
							echo "benchmark value ok<br>";
						else
							echo "Invalid value benchmark<br>";	*/
							
						if($this->validateName($key) && preg_match('|^[^\\|\\;\\$\\(\\)\\"\\\'<>]*$|', $val) && !preg_match('/[\s]?(ping )|(waitfor )|(delay )|(benchmark )/',$key) && !preg_match('/[\s]?(ping )|(waitfor )|(delay )|(benchmark )/',$val)){
							//echo "condition ok <br>";
						}
						else{	
							//exit('error....');
							return false;
						}	
					}
					//exit;
					return true;
			}
				
									
	}
?>
