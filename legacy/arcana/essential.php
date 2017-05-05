<?php
$language = $config["lang_default"];
if(file_exists($config["lang_dir"].$language.'.ini')){
	$lang = parse_ini_file($config["lang_dir"].$language.'.ini', true);
}
else{
	$lang = array();
	//Language file not found
}

$db = new mysqli('localhost', 'arcana', '', 'acsoc');
if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}
mysqli_set_charset($db,"utf8");

date_default_timezone_set('Asia/Hong_Kong');
?>
