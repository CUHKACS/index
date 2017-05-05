<?php 
	require '../sibyl.php'; 
	$pageno = intval(filter_input(INPUT_GET, 'page', FILTER_VALIDATE_REGEXP,
		array("options"=>array("regexp"=>"/^[1-5]$/"))));
	if(!$pageno
	||($pageno == 2 && is_null(SIBYL::$activeUID)
	||($pageno > 2 && !SIBYL::checkFlag(SIBYL::FLAG_SYSTEM_ADMIN)))) {
		$pageno = 1;
	}
	
?>
<html>
<head>
<title>SIBYL Admin Panel</title>
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<style>
.flag {
	width: 16px;
	height: 8px;
	border: 1px solid #000;
	background-color: #CCC;
}
.setFlag {
	background-color: #0FF;
}
.phead {
	width:100px;
	height:50px;
	text-align:center;
}
.pside {
	width:100px;
	height:50px;
}
.pflag {
	width:90px;
	height:40px;
	border:1px solid #000;
	background-color:#DDD;
	padding:5px;
	text-align:center;
	font-size:10pt;
}
.plflag {
	background-color:#FDD;
}
</style>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
</head>
<body onload="ptrLogin(<?php if(is_null(SIBYL::$activeUID)) echo "1"; ?>)">
	<div class="panel panel-default"
		style="margin: 20px auto; width: 1000px;">
		<div class="panel-heading" style="padding: 0px; border-bottom-style: none;">
			<div style="float:right;width:300px; padding:15px;" id="login-section">
			<?php echo "<p>" . SIBYL::$displayMessege . "</p>";?>
			</div>
			<div style="padding:15px;margin-bottom:20px;">
				<h4>Administration Panel</h4>
				<p>SIBYL - CUHKACS Authentication System</p>
			</div>
			<ul class="nav nav-tabs" role="tablist">
  			<?php 
  				$pageNames = array(
  						"Register",
  						"Profile",
  						"Accounts",
  						"Sessions",
  						"Permissions"
  				);
  				for($i = 1; $i <= 5; $i++) {
  					if($i == 1
  					||($i == 2 && !is_null(SIBYL::$activeUID)
  					||($i > 2 && SIBYL::checkFlag(SIBYL::FLAG_SYSTEM_ADMIN)))) {
  						if($i == $pageno) echo "<li class=\"active\"><a href=\"?page=" . $i . "\">";
  						else echo "<li><a href=\"?page=" . $i . "\">";
  						echo $pageNames[$i-1] . "</a></li>";
  					}
  				}
  			?>
			</ul> 
		</div>
		<?php 
			define('INCLUDED', '');
			include $pageNames[$pageno-1] . '.php';
		?>
	</div>
	<script id="login-template" type="text/html">
		<form method="post" style="display:none;" action="@@thisPage" name="login-form" id="login-form"></form>
		<input style="width:100%;" type="text" form="login-form" name="sibyluid" placeholder="SID or email"
				pattern="^([0-9]{10}|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4})$" required />
		<input style="width:100%;" type="password" id="dummypw" placeholder="Password" required />
		<input style="display:none;" type="text" form="login-form" name="sibylukey" />
		<p style="text-align:right;"><a onclick="submitLogin()">Login</a></p>
		<?php echo "<p style=\"text-align:right;\">" . SIBYL::$displayMessege . "</p>";?>
	</script>
	<script id="logout-template" type="text/html">
		<form method="post" style="display:none;" action="@@thisPage" name="logout-form" id="logout-form"></form>
		<input type="text" style="display:none;" form="logout-form" name="sibyllogout" value="1" required />
		<a onclick="submitLogout()">Logout</a>
	</script>
	<script>
		function ptrLogin(print) {
			if(print) {
				$('#login-section').html($('#login-template').html().replace('@@thisPage', location.href));
			} else {
				$('#login-section').append($('#logout-template').html().replace('@@thisPage', location.href));
			}
			pageInit();
		}
		function submitLogout() {
			$('#logout-form').submit();
		}
		function submitLogin() {
			if(document.getElementById('login-form').checkValidity() && document.getElementById('dummypw').checkValidity()) {
				$('input[name=sibylukey]').val(btoa($('#dummypw').val()));
				$('#login-form').submit();
			} else {
				alert('Invalid Input!');
			}
		}
	</script>
</body>
</html>

<!-- END OF FILE ----------------------------------------------------------------------------------->
