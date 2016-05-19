<?php
require_once 'init.php';err_status("init.php included");
  $_SESSION = array(); // destroy all $_SESSION data
 
				setcookie("PHPSESSID", "", time() - 3600, "/");
				setcookie("PHPSESSID","",time()-3600,"/");
				if (ini_get("session.use_cookies")) {
						$params = session_get_cookie_params();
						setcookie(session_name(), '', time() - 42000,
								$params["path"], $params["domain"],
								$params["secure"], $params["httponly"]
							);
					}
				session_unset ();
				session_destroy ();
				//session_start ();
				session_write_close();
				//session_regenerate_id ( true );
				
				unset($_COOKIE['PHPSESSID']);
				session_destroy();
				
				header("location:index.php");
				?>