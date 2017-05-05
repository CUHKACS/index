<?php
if(!isset($_COOKIE["lang"])){
	setcookie("lang", $config["lang_default"], time()+60*60*24*30, "/");	
	$language = $config["lang_default"];
}
else{
	$language = $_COOKIE["lang"];
}

if(file_exists($config["lang_dir"].$language.'.ini')){
	$lang = parse_ini_file($config["lang_dir"].$language.'.ini', true);
}
else{
	$lang = array();
	//Language file not found
}

date_default_timezone_set('Asia/Hong_Kong');

$db = new mysqli($config["db_address"], $config["db_user"], $config["db_pass"], $config["db_schema"]);
if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}
mysqli_set_charset($db,"utf8");

function location_to_bookcase($location){
	return preg_replace('/(\d+).(\d+)/i','$1.$2',$location);
}

?>
