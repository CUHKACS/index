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

        if(isset($_POST["item_id"])){
                $item_id = intval($_POST['item_id']);
                        $sid = intval($_POST['sid']);
                        $date_due = mysqli_real_escape_string($db,$_POST['due_date']);
                        $sql = $db->prepare("INSERT INTO `index`.`borrow` (`id`, `item_id`, `sid`, `date_borr`, `date_due`, `date_return`) VALUES (NULL, ?, ?, ?, ?, '0000-00-00');");
                        $sql->bind_param('iiss', $item_id, $sid, date("Y-m-d"), $date_due);
                        $sql->execute();
                        $sql = $db->prepare("UPDATE `item` SET status='on-loan' WHERE `item_id`=?;");
                        $sql->bind_param('i', $item_id);
                        $sql->execute();
                        setcookie('last_sid', $sid, 0, '/');
                        header('Location: /admin.php');
        }


	if(isset($_GET["return"])){
		$id = intval($_GET['return']);
		$item_id = intval($_GET['item']);

		$sql = $db->prepare("UPDATE `borrow` SET `date_return`=? WHERE `id`=? ;");
		$sql->bind_param('si', date("Y-m-d"), $id);
		$sql->execute();
		$sql = $db->prepare("UPDATE `item` SET status='on-shelf' WHERE `item_id`=?;");
		$sql->bind_param('i', $item_id);
		$sql->execute();
		header('Location: /admin.php');
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
if(strftime("%w",time()) == "4")
	date_add($date,date_interval_create_from_date_string("4 days"));
elseif(strftime("%w",time()) == "5")
	date_add($date,date_interval_create_from_date_string("3 days"));
else
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
	<input type="button" value="<?=$lang["Button"]["NewItem"]?>" onClick="window.location='/edit.php'" />
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
		?>
		<br>
		<form id="borrow-form" method="post" target="_self">
		<?=$lang["Field"]["DateReturn"]?> <input id="due_date" name="due_date" type="date" value="<?=$due_date?>" required autocomplete="off" />
		<?=$lang["Field"]["SID"]?> <input id="sid" name="sid" type="number" min="1000000000" max="9999999999" placeholder="<?=$lang["Field"]["SID"]?>" value=<?php if(isset($_COOKIE["last_sid"])) echo $_COOKIE["last_sid"]; ?> autocomplete="off" required <?php if(!isset($_COOKIE["last_sid"])) echo "autofocus" ?> />
		<?=$lang["Field"]["ID"]?> <input id="item_id" name="item_id" type="text" placeholder="<?=$lang["Field"]["Barcode"]?> / <?=$lang["Field"]["Title"]?> / <?=$lang["Field"]["Author"]?>" autocomplete="off" <?php if(isset($_COOKIE["last_sid"])) echo "autofocus" ?> />
		<input type="submit" value="<?=$lang["Button"]["Submit"]?>">
		</form>
<script type="text/javascript">
var selected;
$(function() {
	$("#item_id").autocomplete({
		source: "search.php",
		minLength: 1,
		select: function( event, ui ) {
			selected = ui.item;
		}
	});
});

$("#borrow-form").submit(function(event){
	if($("input#item_id").val().match(/^\d+$/) && selected)
		return;
	else
		event.preventDefault();
});
</script>
		<br>
		<?php
		$sql = $db->prepare("SELECT * FROM `borrow_view` ORDER BY `date_borr` DESC, `title` DESC, `volume` DESC LIMIT 0,100");
		$sql->execute();
		$sql->store_result();
		$sql->bind_result($id,$sid,$date_borr,$date_due,$date_return,$item_id,$series_id,$volume,$entry_date,$barcode,$language,$status,$title,$author,$location);
		echo "<div class=\"block\">";
		echo "<table class=\"table\">";
		echo '<tr><td>'.$lang["Field"]["SID"].'</td><td>'.$lang["Field"]["DateBorrow"].'</td><td>'.$lang["Field"]["DateDue"].'</td><td>'.$lang["Field"]["DateReturn"].'</td><td class="title">'.$lang["Field"]["Title"].'</td><td class="volume">'.$lang["Field"]["Volume"].'</td><td class="author">'.$lang["Field"]["Author"].'</td><td class="language">'.$lang["Field"]["Language"].'</td><td class="location">'.$lang["Field"]["Location"].'</td><td class="status">'.$lang["Field"]["Status"].'</td></tr>';
		while($sql->fetch()){
			if($date_return == '0000-00-00')
				$date_return = '<a href="?return='.$id.'&item='.$item_id.'">'.$lang["Message"]["ReturnItem"].'</a>';
			echo "<tr><td><a href=\"/arcana/edit.php?sid=$sid\" target=\"_blank\">$sid</a></td><td>$date_borr</td><td>$date_due</td><td>$date_return</td><td><a href=\"/series/".urlencode($title)."\">$title</a></td><td>$volume</td><td><a href=\"/author/$author\">$author</a></td><td>$language</td><td><a href=\"/location/$location\">".location_to_bookcase($location).'</a></td><td>'.$lang["BookStatus"][$status].'</td></tr>';
		}
		echo "</table>";
		echo "</div>";
		$sql->free_result();
		?>
		</div>
		<?php
	}
}
include 'footer.php';
?>
