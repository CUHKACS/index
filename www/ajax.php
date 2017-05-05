<?php
require_once('./config.php');
require_once('./essential.php');

	$sql = $db->prepare("SELECT `series_id`,`title`, `author`, `location` FROM `series` ORDER BY `title` ASC");
	$sql->execute();
	$sql->store_result();
	$sql->bind_result($id,$title,$author,$location);
	while($sql->fetch()){
		echo $id.$title.$author.$location;
	}
	
	$sql->free_result();
}
elseif(isset($_GET["logout"])){
	if(session_destroy())
	{
		echo 'logged out';
	}
}
$db->close();
?>