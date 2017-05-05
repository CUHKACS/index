<?php 
$sid = filter_input(INPUT_POST, 'sid', FILTER_VALIDATE_REGEXP,
	array("options"=>array("regexp"=>"/^[0-9]{10}$/")));
$ename = filter_input(INPUT_POST, 'ename', FILTER_VALIDATE_REGEXP,
		array("options"=>array("regexp"=>"/^[a-zA-Z ]{3}$/")));
if($sid && $ename) {
	$acsocSQL = mysqli_connect ( "localhost", "sibyl", "", "acsoc" );
	if (mysqli_connect_errno ()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error ();
		exit ( 1 );
	}
	$memberQuery = mysqli_fetch_row ( mysqli_query ($acsocSQL, "SELECT `name_english`,`email` FROM `member` WHERE `sid` = '$sid'") );
	if(preg_match("/.*$ename.*/i", $memberQuery[0])){
		$memberInfo = array();
		$memberInfo['sid'] = $sid;
		$memberInfo['name'] = $memberQuery[0];
		if($memberQuery[1]) {
			$memberInfo['email'] = $memberQuery[1];
		}
		echo json_encode($memberInfo);
	}
}
?>