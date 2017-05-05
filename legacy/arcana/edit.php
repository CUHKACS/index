<?php
require_once('../sibyl.php');
require_once('./config.php');
require_once('./essential.php');
if(!SIBYL::checkFlag(SIBYL::FLAG_COMMITTEE)) {
	header('Location: ./');
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
<a href="./"><?=$lang['Message']['Back']?></a><br><br>
<form id="login" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
<?php

if(isset($_POST['sid'])){
	$name_english = mysqli_real_escape_string($db,$_POST['name_english']);
	$name_chinese = mysqli_real_escape_string($db,$_POST['name_chinese']);
	$gender = mysqli_real_escape_string($db,$_POST['gender']);
	$birth = mysqli_real_escape_string($db,$_POST['birth']);
	$email = mysqli_real_escape_string($db,$_POST['email']);
	$phone = mysqli_real_escape_string($db,$_POST['phone']);
	$college = mysqli_real_escape_string($db,$_POST['college']);
	$major = mysqli_real_escape_string($db,$_POST['major']);
	$admission = mysqli_real_escape_string($db,$_POST['admission']);
	$exp_grad = mysqli_real_escape_string($db,$_POST['exp_grad']);
	$active = mysqli_real_escape_string($db,$_POST['active']);
	$reg_date = mysqli_real_escape_string($db,$_POST['reg_date']);

	if(isset($_GET['sid'])){
		$sid = mysqli_real_escape_string($db,$_GET['sid']);
		$new_sid = mysqli_real_escape_string($db,$_POST['sid']);
		$sql = $db->prepare("UPDATE `member` SET sid=?,name_english=?,name_chinese=?,gender=?,birth=?,email=?,phone=?,college=?,major=?,admission=?,exp_grad=?,reg_date=?,active=? WHERE `sid` = ?");
		$sql->bind_param('isssssissiisss', $new_sid, $name_english, $name_chinese, $gender, $birth, $email, $phone, $college, $major, $admission, $exp_grad, $reg_date, $active, $sid);
		$sql->execute();
		header('Location: ./');
	}
	else{
		$sid = mysqli_real_escape_string($db,$_POST['sid']);
		$sql = $db->prepare("INSERT INTO `member` (sid, name_english, name_chinese, gender, birth, email, phone, college, major, admission, exp_grad, reg_date, active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$sql->bind_param('isssssissiiss', $sid, $name_english, $name_chinese, $gender, $birth, $email, $phone, $college, $major, $admission, $exp_grad, $reg_date, $active);
		$sql->execute();
		header('Location: ./');
	}
}
if(isset($_GET['sid'])){
	$sid = mysqli_real_escape_string($db,$_GET['sid']);
	$sql = $db->prepare("SELECT * FROM `member` WHERE `sid` = ?");
	$sql->bind_param('i', $sid);
	$sql->execute();
	$sql->store_result();
	$sql->bind_result($sid,$name_english,$name_chinese,$gender,$birth,$email,$phone,$college,$major,$admission,$exp_grad,$reg_date,$active);
	$sql->fetch();
}
else{
	$sid = $name_english = $name_chinese = $email = $phone = "";
	$gender = 'Male';
	$birth = '';
	$college = 'CC';
	$major = 'XXXX';
	$admission = '2014';
	$exp_grad = '2018';
	$reg_date = date("Y-m-d");
	$active = 'Y';
}
?>
<table id="editor" class="table">
<tr>
<td><?=$lang['Member']['SID']?></td>
<td><input name="sid" type="number" min="1000000000" max="9999999999" value="<?=$sid?>" required /></td>
</tr>
<tr>
<td><?=$lang['Member']['NameEnglish']?></td>
<td><input name="name_english" type="text" value="<?=$name_english?>" required /></td>
</tr>
<tr>
<td><?=$lang['Member']['NameChinese']?></td>
<td><input name="name_chinese" type="text" value="<?=$name_chinese?>" pattern="[^\u0000-\u007f]+" /></td>
</tr>
<tr>
<td><?=$lang['Member']['Gender']?></td>
<td>
<?php
	if($gender == 'Male'){
		echo '<input type="radio" name="gender" value="Male" checked>'.$lang['Message']['Male'];
		echo '<input type="radio" name="gender" value="Female">'.$lang['Message']['Female'];
	}
	else{
		echo '<input type="radio" name="gender" value="Male">'.$lang['Message']['Male'];
		echo '<input type="radio" name="gender" value="Female" checked>'.$lang['Message']['Female'];
	}
?>
</td>
</tr>
<tr>
<td><?=$lang['Member']['Birth']?></td>
<td><input name="birth" type="text" pattern="(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])" value="<?=$birth?>" required /> (YYYY-MM-DD)</td>
</tr>
<tr>
<td><?=$lang['Member']['Email']?></td>
<td><input name="email" type="email" value="<?=$email?>" required /></td>
</tr>
<tr>
<td><?=$lang['Member']['Phone']?></td>
<td><input name="phone" type="number" min="0" value="<?=$phone?>" required /></td>
</tr>
<tr>
<td><?=$lang['Member']['College']?></td>
<td><select name="college">
<?php
foreach($lang['College'] as $key => $val)
	if($key == $college) echo '<option value="'.$key.'" selected>'.$key.' - '.$val.'</option>';
	else echo '<option value="'.$key.'">'.$key.' - '.$val.'</option>';
?>
</select></td>
</tr>
<tr>
<td><?=$lang['Member']['Major']?></td>
<td><select name="major">
<?php
foreach($lang['Major'] as $key => $val)
	if($key == $major) echo '<option value="'.$key.'" selected>'.$key.' - '.$val.'</option>';
	else echo '<option value="'.$key.'">'.$key.' - '.$val.'</option>';
?>
</select></td>
</tr>
<tr>
<td><?=$lang['Member']['Admission']?></td>
<td><input name="admission" type="number" min="2010" max="2099" value="<?=$admission?>" required /></td>
</tr>
<tr>
<td><?=$lang['Member']['ExpectedGrad']?></td>
<td><input name="exp_grad" type="number" min="2010" max="2099" value="<?=$exp_grad?>" required /></td>
</tr>
<tr>
<td><?=$lang['Member']['RegDate']?></td>
<td><input name="reg_date" type="text" pattern="(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])" value="<?=$reg_date?>" required /> (YYYY-MM-DD)</td>
</tr>
<tr>
<td><?=$lang['Member']['IsActive']?></td>
<td>
<?php
	if($active == 'Y'){
		echo '<input type="radio" name="active" value="Y" checked>'.$lang['Message']['Y'];
		echo '<input type="radio" name="active" value="N">'.$lang['Message']['N'];
	}
	else{
		echo '<input type="radio" name="active" value="Y">'.$lang['Message']['Y'];
		echo '<input type="radio" name="active" value="N" checked>'.$lang['Message']['N'];
	}
?>
</td>
</tr>
<tr>
<td></td>
<td style="text-align:right"><input type="submit" value="<?=$lang['Message']['Submit']?>"></td>
</tr>
</table>

<?php
	if(isset($_GET['sid'])){
		$sql->free_result();
	}
?>
</form>
</body>
</html>
