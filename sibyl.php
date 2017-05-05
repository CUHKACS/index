<?php

/* Usage Examples:
 * 
 * Check if current user is an ACSOC member:
 * if(SIBYL::checkFlag(SIBYL::FLAG_MEMBER)) {}
 * 
 */


abstract class SIBYL {
	// Permission Flags (flags higher than 0x00800000 requires local network connection)
	const FLAG_MEMBER			= 0x00000010;
	const FLAG_ANIVOC			= 0x00800000;
	const FLAG_COMMITTEE		= 0x01000000;
	const FLAG_FINENCIAL		= 0x40000000;
	const FLAG_SYSTEM_ADMIN		= 0x80000000;

  // ACSoc IP match
  const ACSOC_IP_REGEX = "/123\.255\.66\.131/";

	// Permission Flags in an array
	public static $pFlags;
	// Exported SQL connection
	public static $sibylSQL;
	// Authenticated UID (null if not logged in), escaped for SQL usage.
	public static $activeUID = NULL;
	// Permissions for current user
	public static $userPermission = 0;
	// Degraded permissions for current user (due to non-local connection)
	public static $userDegraded = 0;
	// Display messege (response from SIBYL system)
	public static $displayMessege = "";
	
	// Check permission flag(s) for current user
	public static function checkFlag($flag) {
		if(is_null(self::$activeUID)) return FALSE;
		if (self::$userPermission & $flag) return TRUE;
		else return FALSE;
	}
	
	// Check degraded permission flag(s) for current user
	public static function checkdDFlag($flag) {
		if(is_null(self::$activeUID)) return FALSE;
		if (self::$userDegraded & $flag) return TRUE;
		else return FALSE;
	}

	// MAIN ***********************************************************************************
	// NOTE: Do not call this outside sibyl.php
	public static function mainFunction() {
		// Get permission flag array
		$sibylClass = new ReflectionClass('SIBYL');
		self::$pFlags = $sibylClass->getConstants();
		
		// Connect to DB
		self::$sibylSQL = mysqli_connect ( "localhost", "sibyl", "", "sibyl" );
		if (mysqli_connect_errno ()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error ();
			exit ( 1 );
		}
		
		// Check auth status
		if(isset($_COOKIE['sibylkey'])) {
			$sibylKey = filter_input(INPUT_COOKIE, 'sibylkey', FILTER_VALIDATE_REGEXP,
					array("options"=>array("regexp"=>"/^[A-F0-9]{32}$/i")));
			if($sibylKey) {
				$authQuery = mysqli_fetch_row ( mysqli_query ( self::$sibylSQL,
						"SELECT `sessions`.`uid`,`ip`,`activity`,`permissions` FROM `sessions` LEFT JOIN `users` ON
						`sessions`.`uid` = `users`.`uid` WHERE `token` = '$sibylKey'" ) );
				if($authQuery[0]) {
					$timeRemain = strtotime($authQuery[2]) + 1800/*30 mins*/ - strtotime('now');
					if($authQuery[1] == $_SERVER['REMOTE_ADDR'] && $timeRemain > 0) {
						mysqli_query ( self::$sibylSQL, "UPDATE `sessions` SET `activity` = '" . date("Y-m-d H:i:s") . "' WHERE `token` = '$sibylKey'" );
						self::$activeUID = $authQuery[0];
						$localNetwork = preg_match(self::ACSOC_IP_REGEX, $_SERVER['REMOTE_ADDR']);
						for($i = 0; $i < 32; $i++) {
							$flag = intval($authQuery[3]) & (1<<$i);
							if($i > 23 && !$localNetwork) {
								self::$userDegraded |= $flag;
							} else {
								self::$userPermission |= $flag;
							}
						}
						self::$displayMessege = "Logged in as: " . $authQuery[0];
					} else {
						mysqli_query ( self::$sibylSQL, "DELETE FROM `sessions` WHERE `token` = '$sibylKey'" );
						self::$displayMessege = "Session timed out"; /* or IP changed */
					}
				}
			}
		}
		
		// Handel login/logout request
		if($_SERVER ['REQUEST_METHOD'] == "POST") {
			if(isset($_POST['sibyllogout']) && !is_null(self::$activeUID)) {
				mysqli_query ( self::$sibylSQL, "DELETE FROM `sessions` WHERE `uid` = '" . self::$activeUID . "'" );
				self::$activeUID = NULL;
				self::$userPermission = 0;
				self::$userDegraded = 0;
				self::$displayMessege = "Logout Successful!";
			} else {
				$req_uid = filter_input(INPUT_POST, 'sibyluid', FILTER_VALIDATE_REGEXP,
						array("options"=>array("regexp"=>"/^([0-9]{10}|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4})$/i")));
				$req_password = filter_input(INPUT_POST, 'sibylukey', FILTER_VALIDATE_REGEXP,
						array("options"=>array("regexp"=>"/^([A-Z0-9]|\+|\/)+={0,2}$/i")));
				if($req_uid && $req_password) {
					$req_uid = mysqli_real_escape_string ( self::$sibylSQL, $req_uid );
					$authQuery = mysqli_fetch_row ( mysqli_query ( self::$sibylSQL,
							"SELECT `password`,`permissions` FROM `users` WHERE `uid` = '$req_uid'" ) );
					if($authQuery[0] == $req_password) {
						mysqli_query(self::$sibylSQL, "DELETE FROM `sessions` WHERE uid = '$req_uid'");
						$sessionUID = $req_uid;
						$sessionIP = $_SERVER['REMOTE_ADDR'];
						$sessionTime = date("Y-m-d H:i:s");
						$sessionToken = md5($sessionUID . $sessionIP . $sessionTime);
						mysqli_query(self::$sibylSQL, "INSERT INTO `sessions` (`uid`,`token`,`ip`,`activity`)
						VALUES ('$sessionUID','$sessionToken','$sessionIP','$sessionTime')");
						setcookie('sibylkey', $sessionToken, time()+86400, '/');
						self::$activeUID = $sessionUID;
						$localNetwork = preg_match(self::ACSOC_IP_REGEX, $_SERVER['REMOTE_ADDR']);
						for($i = 0; $i < 32; $i++) {
							$flag = intval($authQuery[1]) & (1<<$i);
							if($i > 23 && !$localNetwork) {
								self::$userDegraded |= $flag;
							} else {
								self::$userPermission |= $flag;
							}
						}
						self::$displayMessege = "Logged in as: " . $sessionUID;
					} else {
						self::$displayMessege = "Invalid email/SID or password.";
					}
				}
			}
		}
		
	} /* Close mainFunction() */
	
} /* Close SIBYL class */

SIBYL::mainFunction();
mysqli_close(SIBYL::$sibylSQL);

?>

