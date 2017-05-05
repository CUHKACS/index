<?php
require_once('../sibyl.php');
require_once('./config.php');
require_once('./essential.php');

if(!is_null(SIBYL::$activeUID) && isset($_POST['sibyluid'])) {
	header('Location: '.$_SERVER['PHP_SELF']);
}
if(is_null(SIBYL::$activeUID) && isset($_POST['sibyllogout'])) {
	header('Location: '.$_SERVER['PHP_SELF']);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>CUHKACS Member Management System</title>
<link href="reset.css" rel="stylesheet" type="text/css" />
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<script type="text/javascript" src="/jquery-2.1.1.min.js"></script>

<?php
if(is_null(SIBYL::$activeUID)) {
	?>
	<form id="login-form" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>" name="login-form">
	<input type="text" name="sibyluid" placeholder="<?=$lang["Message"]["SID"]?>"
			pattern="^([0-9]{10}|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4})$" autocomplete="off" autofocus required />
	<input type="password"  name="sibylupass" placeholder="<?=$lang["Message"]["Password"]?>" autocomplete="off" required />
	<input type="hidden" name="sibylukey" />
	<input type="submit" value="<?=$lang["Message"]["Login"]?>" />
	</form>
	<?php echo SIBYL::$displayMessege;?>
	<br>
	<script type="text/javascript">
	$('#login-form').submit(function(e){
		$('input[name=sibylukey]').val(btoa($('input[name=sibylupass]').val()));
	});
	</script>
<?php
}
else{
	?>
	<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" name="logout-form" id="logout-form">
	<?php echo SIBYL::$displayMessege; ?>
	<input type="text" style="display:none;" name="sibyllogout" value="1" required />
	<input type="submit" value="<?=$lang["Message"]["Logout"]?>" />
	</form>
	<?php
	if(!SIBYL::checkFlag(SIBYL::FLAG_COMMITTEE)){
		echo $lang["Message"]["NoPermission"];
	}
	else{

		$sql = "SELECT * FROM `member` ORDER BY `reg_date` DESC";
		if(!$result = $db->query($sql)){
			die('There was an error running the query [' . $db->error . ']');
		}
		else{
			echo '<table id="memberlist" class="table">';
			echo '<tr>';
			echo '<td>'.$lang['Member']['SID'].'</td>';
			echo '<td>'.$lang['Member']['NameEnglish'].'</td>';
			echo '<td>'.$lang['Member']['NameChinese'].'</td>';
			echo '<td>'.$lang['Member']['Gender'].'</td>';
			echo '<td>'.$lang['Member']['Birth'].'</td>';
			echo '<td>'.$lang['Member']['Email'].'</td>';
			echo '<td>'.$lang['Member']['Phone'].'</td>';
			echo '<td>'.$lang['Member']['College'].'</td>';
			echo '<td>'.$lang['Member']['Major'].'</td>';
			echo '<td>'.$lang['Member']['Admission'].'</td>';
			echo '<td>'.$lang['Member']['ExpectedGrad'].'</td>';
			echo '<td>'.$lang['Member']['RegDate'].'</td>';
			echo '<td>'.$lang['Member']['IsActive'].'</td>';
			echo '<td>&nbsp;</td>';
			echo '</tr>';

				echo '<tr>';
				echo '<td colspan="13"></td>';
				echo '<td><a href="edit.php">'.$lang["Message"]["Add"].'</a></td>';
				echo '</tr>';

			while($row = $result->fetch_assoc()){
				echo '<tr>';
				foreach($row as $key => $val){
					if($key == 'gender')
						echo '<td>'.$lang['Message'][$val].'</td>';
					elseif($key == 'college')
						echo  '<td>'.$lang['College'][$val].'</td>';
					elseif($key == 'major')
						echo  '<td>'.$val.' - '.$lang['Major'][$val].'</td>';
					elseif($key == 'active')
						echo  '<td>'.$lang['Message'][$val].'</td>';
					else
						echo '<td>'.$val.'</td>';
				}
				echo '<td><a href="edit.php?sid='.$row['sid'].'">'.$lang["Message"]["Edit"].'</a></td>';
				echo '</tr>';
			}
			echo '</table>';
			$result->free();
		}
	}
}
?>
</body>
</html>
