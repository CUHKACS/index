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
if(SIBYL::checkFlag(SIBYL::FLAG_COMMITTEE)){

	if(isset($_POST["title"]) && isset($_GET["id"])){ //edit series
		$series_id = intval($_GET["id"]);
		$title = mysqli_real_escape_string($db,$_POST['title']);
		$author = mysqli_real_escape_string($db,$_POST['author']);
		$location = mysqli_real_escape_string($db,$_POST['location']);
		$sql = $db->prepare("UPDATE `series` SET title=?, author=?, location=? WHERE `series_id`=?;");
		$sql->bind_param('sssi', $title, $author, $location, $series_id);
		$sql->execute();
		header('Location: /edit.php?id='.$series_id);
	}
	
	if(isset($_POST["title"]) && !isset($_GET["id"])){ //add series
		$title = mysqli_real_escape_string($db,$_POST['title']);
		$author = mysqli_real_escape_string($db,$_POST['author']);
		$location = mysqli_real_escape_string($db,$_POST['location']);
		$sql = $db->prepare("INSERT INTO `index`.`series` (`series_id`, `title`, `author`, `location`) VALUES (NULL, ?, ?, ?);");
		$sql->bind_param('sss', $title, $author, $location);
		$sql->execute();
		$sql = $db->prepare("SELECT `series_id` FROM `series` ORDER BY `series_id` DESC LIMIT 0,1");
		$sql->execute();
		$sql->store_result();
		$sql->bind_result($series_id);
		$sql->fetch();
		header('Location: /edit.php?id='.$series_id);
	}

	if(isset($_POST["volume"]) && isset($_GET["id"])){ //add item
		$series_id = intval($_GET["id"]);
		$volume = intval($_POST['volume']);
		$languages = mysqli_real_escape_string($db,$_POST['languages']);
		$barcode = mysqli_real_escape_string($db,$_POST['barcode']);
		$sql = $db->prepare("INSERT INTO `index`.`item` (`item_id`, `series_id`, `volume`, `entry_date`, `barcode`, `language`, `status`) VALUES (NULL, ?, ?, NULL, ?, ?, 'on-shelf');");
		$sql->bind_param('iiss', $series_id, $volume, $barcode, $languages);
		$sql->execute();
		header('Location: /edit.php?id='.$series_id);
	}
}

include 'header.php';
?>
<div id="topright">

<select id="lang">
<?php
if ($handle = opendir($config["lang_dir"])) {
    while (false !== ($entry = readdir($handle))) {
		if($entry != "." && $entry != ".."){
			$lang_item = substr($entry,0,5);
			if($lang_item == $language)
				echo '<option value="'.substr($entry,0,5).'" selected>'.$lang["Language"][substr($entry,0,5)]."</option>\n";
			else
				echo '<option value="'.substr($entry,0,5).'">'.$lang["Language"][substr($entry,0,5)]."</option>\n";
		}
    }
    closedir($handle);
}

$date=date_create();
date_add($date,date_interval_create_from_date_string("2 days"));
$due_date = date_format($date,"Y-m-d");
?>
</select>
<script type="text/javascript">
$('#lang').on('change', function() {
	$.cookie("lang", $('#lang').val(), {path: '/', expires: 30 });
	window.location.reload();
});
</script>
<?php
if(!is_null(SIBYL::$activeUID)) {
	?>
	<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" name="logout-form" id="logout-form">
	<?php echo SIBYL::$displayMessege;?>
	<input type="text" style="display:none;" name="sibyllogout" value="1" required />
	<input type="submit" value="<?=$lang["Button"]["Logout"]?>" />
	</form>
	<?php
}
?>


</div>

<a href="/"><img id="logo" src="/image/logo.png" alt="logo" /></a><br>
<br>

<?php
if(is_null(SIBYL::$activeUID)){
	?>
	<form id="login-form" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>" name="login-form">
	<input type="text" name="sibyluid" placeholder="<?=$lang["Field"]["SID"]?>"
			pattern="^([0-9]{10}|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4})$" autocomplete="off" autofocus required />
	<input type="password" name="sibylupass" placeholder="<?=$lang["Message"]["Password"]?>" autocomplete="off" autofocus required />
	<input type="hidden" name="sibylukey" />
	<input type="submit" value="<?=$lang["Button"]["Login"]?>" />
	</form>
	<?php echo SIBYL::$displayMessege;?>
	<script type="text/javascript">
	$('#login-form').submit(function(e){
		$('input[name=sibylukey]').val(btoa($('input[name=sibylupass]').val()));
	});
	</script>
	<?php
}
else{
	if(!SIBYL::checkFlag(SIBYL::FLAG_COMMITTEE)){
		echo $lang["Message"]["NoPermission"];
	}
	else{
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

        if(isset($_GET["delete"])){
		$item_id = intval($_GET["delete"]);
		$status = 'deleted';
                $sql = $db->prepare("UPDATE `item` SET `status`=? WHERE `item_id`=? ;");
		$sql->bind_param('si', $status, $item_id);
                $sql->execute();
                header('Location: /edit.php?id='.intval($_GET["id"]));
        }

		if(isset($_GET["id"])){
			$sql = $db->prepare("SELECT * FROM `series` WHERE `series_id` = ".intval($_GET["id"]));
			$sql->execute();
			$sql->store_result();
			$sql->bind_result($series_id,$title,$author,$location);
			$sql->fetch();
		}
		?>
		<br>
		<form id="series-form" method="post" target="_self">
		<input id="series_id" name="series_id" type="number" placeholder="ID" value="<?=$series_id ?>" disabled required autocomplete="off" />
		<input id="title" name="title" type="text" placeholder="<?=$lang["Field"]["Title"]?>" value="<?=$title ?>" required autocomplete="off" />
		<input id="author" name="author" type="text" placeholder="<?=$lang["Field"]["Author"]?>" value="<?=$author ?>" required autocomplete="off" />
		<input id="location" name="location" type="text" placeholder="<?=$lang["Field"]["Location"]?>" value="<?=$location ?>" required autocomplete="off" />
		<input type="submit" value="<?=$lang["Button"]["Submit"]?>">
		</form>
		<br>
		<?php
		if(isset($_GET["id"])){
			$sql = $db->prepare("SELECT * FROM `item` WHERE `status` <> 'deleted' AND `series_id` = ".intval($_GET["id"]));
			$sql->execute();
			$sql->store_result();
			$sql->bind_result($id,$series_id,$volume,$entry_date,$barcode,$languages,$status);
			echo "<div class=\"block\">";
			echo "<table id=\"list\" class=\"table\">";
			echo '<tr><td class="id">ID</td><td class="volume">'.$lang["Field"]["Volume"].'</td><td>'.$lang["Field"]["Language"].'</td><td class="entrydate">'.$lang["Field"]["DateAdded"].'</td><td class="status">'.$lang["Field"]["Status"].'</td><td class="barcode">'.$lang["Field"]["Barcode"].'</td><td>'.$lang['Field']['Remove'].'</td></tr>';
			while($sql->fetch()){
				echo "<tr><td>$id</td><td>$volume</td><td>$languages</td><td>$entry_date</td><td>".$lang["BookStatus"][$status]."</td><td>$barcode</td><td><a href=\"?id=$series_id&delete=$id\">X</a></td></tr>";
			}
			echo "</table>";
			echo "</div>";
			$sql->free_result();
			?>
			<form id="item-form" method="post" target="_self">
			<input id="volume" name="volume" type="number" placeholder="Volume" value="<?=intval($volume)+1 ?>" style="width:50px" required autocomplete="off" />
			<input id="languages" name="languages" type="text" placeholder="<?=$lang["Field"]["Language"]?>" value="<?=$languages ?>" required autocomplete="off" />
			<input id="barcode" name="barcode" type="text" placeholder="<?=$lang["Field"]["Barcode"]?>" value="" autocomplete="off" autofocus />
			<input type="submit" value="<?=$lang["Button"]["Submit"]?>">
			</form>
			<?php
		}
		?>
		</div>
		<?php
	}
}
include 'footer.php';
?>
