<?php 
if(defined("INCLUDED")) { 
	$sibylSQL = mysqli_connect ( "localhost", "sibyl", "", "sibyl" );
	if (mysqli_connect_errno ()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error ();
		exit ( 1 );
	}
	$memberQuery = mysqli_fetch_row ( mysqli_query ($sibylSQL,
			"SELECT `uid`,`email`,`reg_date`,`permissions` FROM `users` WHERE `uid` = '" . SIBYL::$activeUID . "'") );
	
	// Handel change password request
	$changeRes = NULL;
	if($_SERVER ['REQUEST_METHOD'] == "POST") {
		$editUkey = filter_input(INPUT_POST, 'sibyleditukey', FILTER_VALIDATE_REGEXP,
				array("options"=>array("regexp"=>"/^([A-Z0-9]|\+|\/)+={0,2}$/i")));
		$editUid = filter_input(INPUT_POST, 'sibyledituid', FILTER_VALIDATE_REGEXP,
				array("options"=>array("regexp"=>"/^([0-9]{10}|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4})$/i")));
		
		if($editUkey && $editUid && $editUid == SIBYL::$activeUID) {
			mysqli_query ( $sibylSQL, "UPDATE `users` SET `password` = '$editUkey' WHERE `uid` = '$editUid'" );
			$changeRes = 'Successfully changed password.';
		} else {
			$changeRes = 'Change password failed.';
		}
	}
	
?>
<div class="panel-body">
	<table style="width:550px;" class="table">
	<tr><td>User ID:</td><td><?php echo $memberQuery[0];?></td></tr>
	<tr><td>Email Address:</td><td><?php echo $memberQuery[1];?></td></tr>
	<tr><td>Registration Date (YYYY-MM-DD):</td><td><?php echo $memberQuery[2];?></td></tr>
	<tr><td>Password:</td><td id="pw">
			<?php if($changeRes) {
				echo '<p>' . $changeRes . '</p>';
			} else {
				echo '<a onclick="changePass()">Change</a>';
			} ?>
		</td></tr>
	<tr><td>Account Permissions:</td><td><?php echo $memberQuery[3];?></td></tr>
	</table>
</div>
<script id="pw-template" type="text/html">
	<form id="pw-form" action="@@thispage@" method="post" style="display:none;">
		<input type="text" name="sibyledituid" value="<?php echo SIBYL::$activeUID; ?>" required><br>
		<input type="text" name="sibyleditukey" required><br>
	</form> 
	<input type="password" id="ukey" placeholder="New Password" required><br>
	<input type="password" id="re-ukey" placeholder="Confirm Password" required><br>
	<a onclick="restorePass()">Cancel</a> <a onclick="submitPass()">Confirm</a>
</script>
<script>
	var backup;
	function pageInit() {}
	function changePass() {
		backup = $('#pw').html();
		$('#pw').html($('#pw-template').html().replace('@@thispage@', location.href));
    }
    function restorePass() {
    	$('#pw').html(backup);
    }
    function submitPass() {
        if(document.getElementById('ukey').checkValidity() && document.getElementById('re-ukey').checkValidity()) {
            if($('#ukey').val() === $('#re-ukey').val()) {
                $('input[name=sibyleditukey]').val(btoa($('#ukey').val()));
            	$('#pw-form').submit();
            } else {
                alert('Passwords do not match.');
            }
        } else {
            alert('Password fields cannot be empty.');
        }
    }
</script>
<?php 
	mysqli_close($sibylSQL);
} /* Close include check */ ?>