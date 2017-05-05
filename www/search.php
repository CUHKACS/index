<?php
require_once('../sibyl.php');
require_once('./config.php');
require_once('./essential.php');
$target = '%'.urldecode($_GET["term"]).'%';
$barcode = $_GET["term"];
$sql = $db->prepare("SELECT `item_id`,`title`,`author`,`volume`,`barcode` FROM `item_view` WHERE `author` LIKE ? OR `title` LIKE ? OR `barcode` = ? ORDER BY `title`,`volume` ASC");
$sql->bind_param('sss', $target, $target, $barcode);

$sql->execute();
$sql->store_result();
$sql->bind_result($item_id,$title,$author,$volume,$barcode);
		
if(SIBYL::checkFlag(SIBYL::FLAG_COMMITTEE)){
	$rows = array();
	while($sql->fetch()){
		$rows[] = array("id" => $item_id, "label" => "$title - $volume", "value" => $item_id);
	}
	print json_encode($rows);
	$sql->free_result();
}
?>
