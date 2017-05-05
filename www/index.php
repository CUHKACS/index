<?php
require_once('../sibyl.php');
require_once('./config.php');
require_once('./essential.php');
include 'header.php';
?>
<div id="topright">

<a href="/admin.php"><?=$lang["Button"]["Admin"]?></a>

<select id="lang">
<?php
if ($handle = opendir('./lang')) {
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
?>
</select>
</div>

<a href="/"><img id="logo" src="/image/logo.png" alt="logo" /></a><br>
<br>
<input id="search" type="text" placeholder="<?=$lang["Message"]["EnterToSearch"]?>" autocomplete="off" autofocus /><br>
<br>
<script type="text/javascript">
$('#lang').on('change', function() {
  $.cookie("lang", $('#lang').val(), {path: '/', expires: 30 });
  window.location.reload();
});

$("#search").bind("keypress", {}, keypressInBox);

function keypressInBox(e) {
  var code = (e.keyCode ? e.keyCode : e.which);
  if (code == 13) {
    e.preventDefault();
    window.location = "/search/"+encodeURIComponent($("#search").val());
  }
};
</script>
<?php
$url = explode("/", preg_replace("/([?].*)/","",$_SERVER['REQUEST_URI']));
//print_r($url);
for($i=0; $i<4; $i++){
  $uri[$i] = isset($url[$i]) ? $url[$i] : "";
}
switch ($uri[1]) {
case "series":
  $target = urldecode($uri[2]);
  $sql = $db->prepare("SELECT * FROM `item_view` WHERE `title` = ? ORDER BY `language` ASC,`volume` ASC");
  $sql->bind_param('s', $target);
  break;
case "id":
  $target = intval($uri[2]);
  $sql = $db->prepare("SELECT * FROM `item_view` WHERE `series_id` = ? AND `status` != 'deleted' ORDER BY `volume` ASC");
  $sql->bind_param('i', $target);
  break;
case "title":
  $target = '%'.urldecode($uri[2]).'%';
  $sql = $db->prepare("SELECT `series_id`,`title`, `author`, `location` FROM `series` WHERE `title` LIKE ? AND `status` = 'deleted' ORDER BY `title` ASC");
  $sql->bind_param('s', $target);
  break;
case "author":
  $target = '%'.urldecode($uri[2]).'%';
  $sql = $db->prepare("SELECT `series_id`,`title`, `author`, `location` FROM `series` WHERE `author` LIKE ? ORDER BY `title` ASC");
  $sql->bind_param('s', $target);
  break;
case "location":
  $target = $uri[2];
  $sql = $db->prepare("SELECT `series_id`,`title`, `author`, `location` FROM `series` WHERE `location` = ? ORDER BY `title` ASC");
  $sql->bind_param('s', $target);
  break;
case "search":
  $target = '%'.urldecode($uri[2]).'%';
  if(urldecode($uri[2]) == '%')
    $target = '%\%%';
  $sql = $db->prepare("SELECT `series_id`,`title`, `author`, `location` FROM `series` WHERE `author` LIKE ? OR `title` LIKE ? ORDER BY `title` ASC");
  $sql->bind_param('ss', $target, $target);
  break;
default:

  break;
}
if($uri[1] == "series" || $uri[1] == "id"){
  $sql->execute();
  $sql->store_result();
  $sql->bind_result($item_id,$series_id,$title,$author,$volume,$language,$location,$status,$barcode,$entry_date);
  echo "<div class=\"block\">";
  echo "<table id=\"list\" class=\"table\">";

  if(SIBYL::checkFlag(SIBYL::FLAG_COMMITTEE))
    echo '<tr><td class="title">'.$lang["Field"]["Title"].'</td><td class="volume">'.$lang["Field"]["Volume"].'</td><td class="author">'.$lang["Field"]["Author"].'</td><td class="language">'.$lang["Field"]["Language"].'</td><td class="location">'.$lang["Field"]["Location"].'</td><td class="status">'.$lang["Field"]["Status"].'</td><td></td></tr>';
  else
    echo '<tr><td class="title">'.$lang["Field"]["Title"].'</td><td class="volume">'.$lang["Field"]["Volume"].'</td><td class="author">'.$lang["Field"]["Author"].'</td><td class="language">'.$lang["Field"]["Language"].'</td><td class="location">'.$lang["Field"]["Location"].'</td><td class="status">'.$lang["Field"]["Status"].'</td></tr>';

  while($sql->fetch()){
    if(SIBYL::checkFlag(SIBYL::FLAG_COMMITTEE))
      echo "<tr><td><a href=\"/series/".urlencode($title)."\">$title</a></td><td>$volume</td><td><a href=\"/author/$author\">$author</a></td><td>$language</td><td><a href=\"/location/$location\">".location_to_bookcase($location).'</a></td><td>'.$lang["BookStatus"][$status]."</td><td><a href=\"/edit.php?id=$series_id\">".$lang["Button"]["Edit"]."</a></td></tr>";
    else
      echo "<tr><td><a href=\"/series/".urlencode($title)."\">$title</a></td><td>$volume</td><td><a href=\"/author/$author\">$author</a></td><td>$language</td><td><a href=\"/location/$location\">".location_to_bookcase($location).'</a></td><td>'.$lang["BookStatus"][$status].'</td></tr>';
  }
  echo "</table>";
  echo "</div>";
  $sql->free_result();
}
elseif($uri[1] == "title" || $uri[1] == "author" || $uri[1] == "location" || $uri[1] == "search"){
  $sql->execute();
  $sql->store_result();
  $sql->bind_result($series_id,$title,$author,$location);
  echo "<div class=\"block\">";
  echo '<table class="table">';
  if(SIBYL::checkFlag(SIBYL::FLAG_COMMITTEE))
    echo '<tr><td class="id">'.$lang["Field"]["ID"].'</td><td class="title">'.$lang["Field"]["Title"].'</td><td class="author">'.$lang["Field"]["Author"].'</td><td class="location">'.$lang["Field"]["Location"].'</td><td></td></tr>';
  else
    echo '<tr><td class="id">'.$lang["Field"]["ID"].'</td><td class="title">'.$lang["Field"]["Title"].'</td><td class="author">'.$lang["Field"]["Author"].'</td><td class="location">'.$lang["Field"]["Location"].'</td></tr>';

  while($sql->fetch()){
    if(SIBYL::checkFlag(SIBYL::FLAG_COMMITTEE))
      echo "<tr><td><a href=\"/id/".$series_id."\">$series_id</a></td><td><a href=\"/series/".urlencode($title)."\">$title</a></td><td><a href=\"/author/$author\">$author</a></td><td><a href=\"/location/$location\">".location_to_bookcase($location)."</a></td><td><a href=\"/edit.php?id=$series_id\">".$lang["Button"]["Edit"]."</a></td></tr>";
    else
      echo "<tr><td><a href=\"/id/".$series_id."\">$series_id</a></td><td><a href=\"/series/".urlencode($title)."\">$title</a></td><td><a href=\"/author/$author\">$author</a></td><td><a href=\"/location/$location\">".location_to_bookcase($location)."</a></td></tr>";
  }
  echo "</table>";
  echo "</div>";

  $sql->free_result();
}
else{
?>
  <div class="block">
<?php
  $sql = $db->prepare("SELECT COUNT(*) FROM `item_view`");
  $sql->execute();
  $sql->store_result();
  $sql->bind_result($count_item);
  $sql->fetch();
  echo $lang["Message"]["ItemCount"].$count_item.$lang["Message"]["ItemUnit"];
  echo "<br>";
  $sql->free_result();

  $sql = $db->prepare("SELECT COUNT(*) FROM `series`");
  $sql->execute();
  $sql->store_result();
  $sql->bind_result($count_series);
  $sql->fetch();
  echo $lang["Message"]["SeriesCount"].$count_series.$lang["Message"]["SeriesUnit"];
  $sql->free_result();
?>
  </div>
  <br>
  <br>
  <div class="block">
<?php
  $sql = $db->prepare("SELECT `frequency`, `series_id`, `title`, `author`, `location` FROM `hot_series_view` LIMIT 0,10");
  $sql->execute();
  $sql->store_result();
  $sql->bind_result($frequency,$series_id,$title,$author,$location);
  if($sql->num_rows > 0){
    echo $lang["Message"]["HotItems"].'<br>';
    echo '<table class="table">';
    echo '<tr><td class="title">'.$lang["Field"]["Title"].'</td><td class="author">'.$lang["Field"]["Frequency"].'</td><td class="author">'.$lang["Field"]["Author"].'</td><td class="location">'.$lang["Field"]["Location"].'</td></tr>';
    while($sql->fetch()){
      echo "<tr><td><a href=\"/series/".urlencode($title)."\">$title</a></td><td>$frequency</td><td><a href=\"/author/$author\">$author</a></td><td><a href=\"/location/$location\">".location_to_bookcase($location)."</a></td></tr>";
    }
    echo "</table>";
  }
  else{
    echo $lang["Message"]["NoNewItem"];
  }
  $sql->free_result();
?>
  </div>
  <br>
  <br>
  <div class="block">
<?php
  $one_month_ago = strftime("%Y-%m-%d", time()-60*60*24*30);
  $sql = $db->prepare("SELECT `title`, `author`, `volume`, `language`, `location`, `entry_date` FROM `item_view` WHERE `entry_date` > ? AND `status` != 'deleted' ORDER BY `entry_date` DESC");
  $sql->bind_param('s', $one_month_ago);
  $sql->execute();
  $sql->store_result();
  $sql->bind_result($title,$author,$volume,$language,$location,$entry_date);
  if($sql->num_rows > 0){
    echo $lang["Message"]["NewItems"].'<br>';
    echo '<table class="table">';
    echo '<tr><td class="title">'.$lang["Field"]["Title"].'</td><td class="volume">'.$lang["Field"]["Volume"].'</td><td class="author">'.$lang["Field"]["Author"].'</td><td class="language">'.$lang["Field"]["Language"].'</td><td class="location">'.$lang["Field"]["Location"].'</td><td class="entrydate">'.$lang["Field"]["DateAdded"].'</td></tr>';
    while($sql->fetch()){
      echo "<tr><td><a href=\"/series/".urlencode($title)."\">$title</a></td><td>$volume</td><td><a href=\"/author/$author\">$author</a></td><td>$language</td><td><a href=\"/location/$location\">".location_to_bookcase($location)."</a></td><td>$entry_date</td></tr>";
    }
    echo "</table>";
  }
  else{
    echo $lang["Message"]["NoNewItem"];
  }
  $sql->free_result();
?>
  </div>
  <br>
<?php
}
?>
<br>
<div class="block">
索書號指南<br>
<br>
主分類<br>
01　少年漫畫<br>
02　運動、偵探<br>
03　青年漫畫<br>
04　青年、少女、女性向<br>
05　少女、女性向<br>
06　成人、獵奇<br>
07　未分類<br>
08　輕小說<br>
09　紳士、日文(請向當值幹事查詢)<br>
10　熱門漫畫<br>
11　熱門漫畫<br>
<br>
主分類後的小數點表示存放層架<br>
</div>
<br>
<br>
<div class="block">
借閱守則<br>
<div style="width:500px;">
<ol>
<li>每人一次最多可借閱四本書籍，為期三日兩夜，以工作天計算。</li>
<li>逾期還書，每日每本罰款一元正。若會員在還書期限三天後仍未還書，幹事會成員會以電話通知該會員還書。</li>
<li>任何會員在仍未還清欠款前，本會將會暫時凍結其會員借書（包括續借）的權利，直至該會員補交欠款為止。</li>
<li>續借次數最多三次，續借工作交由當值幹事會成員負責。有意續借的會員目前可直接向當值幹事會成員提出申請，或在本會Facebook專頁提出續借申請。網上續借方法必需經由本會的幹事會成員確定並回復續借成功，續借才生效。</li>
<li>借出項目如有遺失，會員要親身通報本會，並要自行購買所遺失的項目，歸還本會。若有特別原因不能購買所遺失的項目（如遺失的是孤本或絕版的書籍），會員需向本會作出解釋及作出賠償。（遺失項目原價的150%）若會員在通報遺失項目時己愈期，其罰款依然需繳交，相應的罰款則截至通報當日計算。</li>
<li>若書本受到弄污，會員應在還書時提出。若當值幹事會成員認為，弄污情度嚴重，處理手法則與遺失漫借出項目相同。</li>
<li>未成年會員不得借閱被淫穢物品審裁處裁定為第二類的項目（包括漫畫和輕小說，下稱第二類項目）。當會員提出申請借出第二類項目時，當值幹事會成員必需經由會員系統確認會員的年齡。在無法確認提出申請借出第二類項目的會員年齡的情況下，當值幹事會成員不能為該會員辦理借書手續，否則有可能觸犯本港相關法例。</li>
<li>如有任何爭議，本會將保留最終決定權。</li>
</ol>
</div>
</div>

</div>
<?php
include 'footer.php';
?>
