<?php if(defined("INCLUDED")) {

	$response = 0;
	if($_SERVER ['REQUEST_METHOD'] == "POST") {
		$newEmail = filter_input(INPUT_POST, 'sibylnewemail', FILTER_VALIDATE_REGEXP,
			array("options"=>array("regexp"=>"/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i")));
		$newPassword = filter_input(INPUT_POST, 'sibylnewukey', FILTER_VALIDATE_REGEXP,
			array("options"=>array("regexp"=>"/^([A-Z0-9]|\+|\/)+={0,2}$/i")));
		$newSID = filter_input(INPUT_POST, 'sibylnewsid', FILTER_VALIDATE_REGEXP,
			array("options"=>array("regexp"=>"/^[0-9]{10}$/")));
		$newCheck = filter_input(INPUT_POST, 'sibylnewcheck', FILTER_VALIDATE_REGEXP,
			array("options"=>array("regexp"=>"/^[a-zA-Z ]{3}$/")));
		if($newEmail && $newPassword){
			$sibylSQL = mysqli_connect ( "localhost", "sibyl", "", "sibyl" );
			if (mysqli_connect_errno ()) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error ();
				exit ( 1 );
			}
			$newEmail = mysqli_real_escape_string ($sibylSQL, $newEmail);
			$newDate = date("Y-m-d");
			if($newSID && $newCheck) {
				$existQuery = mysqli_fetch_row ( mysqli_query ($sibylSQL,
						"SELECT COUNT(*) FROM `users` WHERE `uid` = '$newSID'") );
				if(intval($existQuery[0]) == 0){
					$acsocSQL = mysqli_connect ( "localhost", "sibyl", "", "acsoc" );
					if (mysqli_connect_errno ()) {
						echo "Failed to connect to MySQL: " . mysqli_connect_error ();
						exit ( 1 );
					}
					$memberQuery = mysqli_fetch_row ( mysqli_query ($acsocSQL,
						"SELECT `name_english` FROM `member` WHERE `sid` = '$newSID'") );
					if(preg_match("/.*$newCheck.*/i", $memberQuery[0])){
						mysqli_query ($sibylSQL, "INSERT INTO `users` (`uid`,`email`,`password`,`permissions`,`reg_date`)
							VALUES ('$newSID','$newEmail','$newPassword','16','$newDate')");
						$response = 2;
					}
					mysqli_close($acsocSQL);
				} else {
					$response = 1;
				}
			} else {
				$existQuery = mysqli_fetch_row ( mysqli_query ($sibylSQL,
					"SELECT COUNT(*) FROM `users` WHERE `uid` = '$newEmail'") );
				if(intval($existQuery[0]) == 0){
					mysqli_query ($sibylSQL, "INSERT INTO `users` (`uid`,`email`,`password`,`permissions`,`reg_date`)
							VALUES ('$newEmail','$newEmail','$newPassword','0','$newDate')");
					$response = 2;
				} else {
					$response = 1;
				}
			}
			mysqli_close($sibylSQL);
		}	
	}
	
?>
<div class="panel-body">
	<?php if($response == 1) {?>
		<h4>Error: User ID "<?php if($newSID){echo $newSID;} else {echo $newEmail;} ?>" already exist!</h4>
		<a>Forgot your password?</a> <!-- TODO: Recover PWD -->
	<?php } elseif($response == 2) { ?>
		<h4>Thank you for registering with CUHKACS, <?php if($newSID){echo $newSID;} else {echo $newEmail;} ?>!</h4>
		<a onclick="profilePage()">Take me to my profile page!</a>
		<script>
		function profilePage() {
			$('input[name=sibyluid]').val('<?php if($newSID){echo $newSID;} else {echo $newEmail;} ?>');
			$('input[name=sibylukey]').val('<?php echo $newPassword;?>');
			$('#login-form').attr('action', '?page=2');
			$('#login-form').submit();
		}
		</script>
	<?php } else { ?>
	<h4>Register a new account:</h4>
	<input type="text" style="display:none;" form="new-form" name="sibyllogout" value="1" required />
	<div class="checkbox" id="cuhkacs-member">
		<label> <input type="checkbox" onchange="isMember(this)"> I'm a CUHKACS member </label>
	</div>
	<div class="form-group">
		<input type="text" class="form-control" id="email" name="sibylnewemail" placeholder="Email"
			pattern="^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$" form="new-form" required>
	</div>
	<div class="form-group">
		<input type="password" class="form-control" id="ukey" placeholder="Password" required>
	</div>
	<div class="form-group">
		<input type="password" class="form-control" id="re-ukey" placeholder="Confirm Password" required>
	</div>
	<input type="text" class="form-control" name="sibylnewukey" form="new-form" style="display:none;">
	<a onclick="submitForm()">Submit</a>
	<?php } ?>
</div>
<script id="sid-template" type="text/html">
	<div class="form-group" id="sid">
		<input type="text" class="form-control" placeholder="CUHK SID" id="sid-input"
			pattern="^[0-9]{10}$" name="sibylnewsid" form="new-form" required>
	</div>
	<div class="form-group" id="ename">
		<div class="input-group">
			<input type="text" class="form-control" placeholder="3 consecutive characters from your english name" 
				pattern="^[a-zA-Z ]{3}$" name="sibylnewcheck" form="new-form" onchange="enameChange()" id="ename-input" required>
			<div class="input-group-addon">...</div>
		</div>
		<p class="help-block">e.g. '<span style="color:#F00;">k t</span>', '<span style="color:#0C0;">sz </span>' 
			and '<span style="color:#00F;">fun</span>' are all valid inputs for the name 
			'Mo<span style="color:#F00;">k T</span><span style="color:#0C0;">sz </span><span style="color:#00F;">Fun</span>g'</p>
	</div>
	<div class="checkbox" id="cuhkacs-email">
		<label> <input type="checkbox" onchange="useEmail(this)"> Use the email registered with CUHKACS </label>
	</div>
</script>
<script>
	var cuhkacsMember = {
			sid: null,
			name: null,
			email: null
		};
	function pageInit() {
		$('.panel-body').append('<form method="post" style="display:none;" action="'+
			location.href +'" name="new-form" id="new-form"></form>');
	}
	function submitForm() {
		if(document.getElementById('new-form').checkValidity() && document.getElementById('ukey').checkValidity()) {
			if($('#ukey').val() === $('#re-ukey').val()) {
				$('input[name=sibylnewukey]').val(btoa($('#ukey').val()));
				$('#new-form').submit();
			} else {
				alert('Passwords do not match!');
			}
		} else {
			alert('Invalid Input!');
		}
	}
	function isMember(self) {
	    if(self.checked) {
	        $('#cuhkacs-member').after($('#sid-template').html());
	    } else {
	    	$('#sid,#ename,#cuhkacs-email').remove();
	    }
	}
	function useEmail(self) {
	    if(self.checked && cuhkacsMember.email) {
	    	$('#email').val(cuhkacsMember.email);
	    } else if(self.checked){
	    	self.checked = false;
	    } else {
	    	$('#email').val('');
	    }
	}
	function enameChange() {
	    if(document.getElementById('sid-input').checkValidity() && document.getElementById('ename-input').checkValidity()) {
		    $.post('memberQuery.php', {
			    	sid: $('#sid-input').val(),
			    	ename: $('#ename-input').val()
			    }, function(data,status) {
	                if(status === 'success'){
		                var memberInfo = JSON.parse(data);
	                	$('#ename div div').html(memberInfo.name);
	                	cuhkacsMember.sid = memberInfo.sid;
	                	cuhkacsMember.name = memberInfo.name;
	                	if(memberInfo.email) {
	                		cuhkacsMember.email = memberInfo.email;
	                	}
	                } else {
	                	$('#ename div div').html('Error, refresh and try again');
	                }
                });
	    } else {
	    	$('#ename div div').html('Invalid name or SID');
	    }
	}
</script>
<?php } /* Close include check */ ?>
