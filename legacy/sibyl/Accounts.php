<?php if (defined("INCLUDED")) {
// Connect DB
$sibylSQL = mysqli_connect ( "localhost", "sibyl", "", "sibyl" );
if (mysqli_connect_errno ()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error ();
	exit ( 1 );
}

// Handel edit request
if($_SERVER ['REQUEST_METHOD'] == "POST" && SIBYL::checkFlag(SIBYL::FLAG_SYSTEM_ADMIN)) {
	$editPermissions = filter_input(INPUT_POST, 'sibyleditpermit', FILTER_VALIDATE_INT,
		array("options"=>array("min_range"=>0, "max_range"=>0xffffffff)));
	$editUid = filter_input(INPUT_POST, 'sibyledituid', FILTER_VALIDATE_REGEXP,
		array("options"=>array("regexp"=>"/^([0-9]{10}|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4})$/i")));
	if($editPermissions && $editUid) {
		mysqli_query ( $sibylSQL, "UPDATE `users` SET `permissions` = '$editPermissions' WHERE `uid` = '$editUid'" );
	}
}
?>

<table class="panel-body table table-condensed"
	style="text-align: center;">
	<?php $rowno = 0; ?>
	<tr>
		<td>UID</td>
		<td>Email</td>
		<td>Permissions</td>
		<td>Reg. Date</td>
		<td>Action</td>
	</tr>
	<?php
		$records = mysqli_query ( $sibylSQL, "SELECT `uid`,`email`,`permissions`,`reg_date` FROM `users`" );
		while ( $row = mysqli_fetch_array ( $records ) ) {
			$rowno ++;
			echo "<tr id=\"row-$rowno\"><td>{$row['uid']}</td>";
			echo "<td>{$row['email']}</td>";
			echo "<td id=\"row-$rowno-p\">{$row['permissions']}</td>";
			echo "<td>{$row['reg_date']}</td>";
			echo "<td id=\"row-$rowno-a\"><a onclick=\"editRow($rowno)\">Edit</a></td></tr>";
		}
	?>
</table>
<!-- Template for permission table -->
<script id="flag-template" type="text/html">
	<table style="margin:0px auto;">
		<tr><td @@31@></td><td @@30@></td><td @@29@></td><td @@28@></td>
			<td @@27@></td><td @@26@></td><td @@25@></td><td @@24@></td></tr>	
		<tr><td @@23@></td><td @@22@></td><td @@21@></td><td @@20@></td>
			<td @@19@></td><td @@18@></td><td @@17@></td><td @@16@></td></tr>
		<tr><td @@15@></td><td @@14@></td><td @@13@></td><td @@12@></td>
			<td @@11@></td><td @@10@></td><td @@9@></td><td @@8@></td></tr>
		<tr><td @@7@></td><td @@6@></td><td @@5@></td><td @@4@></td>
			<td @@3@></td><td @@2@></td><td @@1@></td><td @@0@></td></tr>
	</table>
	<div style="font-family:monospace;">@@permit@</div>
</script>
<!-- Form template for permission edit request -->
<script id="edit-form-template" type="text/html">
	 <form id="edit-form" action="@@thispage@" method="post" style="display:none;">
		<input type="text" name="sibyledituid" value="@@uid@"><br>
		<input type="number" name="sibyleditpermit" value="@@permissions@"><br>
	</form> 
</script>
<script>
	var rowBackupData = [];
	// Make permission table editable
	function editRow(rowNumber) {
		rowBackupData['row-'+rowNumber] = $('#row-'+rowNumber).html();
		$('#row-'+rowNumber+'-a').html('<a onclick=\"restoreRow('+rowNumber+')\">Cancel</a>'+
			'<br /><a onclick=\"updateRow('+rowNumber+')\">Confirm</a>');
		var flagTemplate = $('#flag-template').html();
		var flags = $('#row-'+rowNumber+'-p div').html();
		flagTemplate = flagTemplate.replace('@@permit@', flags);
		for(var i = 0; i < 32; i++) {
			if(flags & (1<<i)) {
				flagTemplate = flagTemplate.replace('@@'+i+'@', 'id="f-'+rowNumber+'-'+i+
					'" onclick="toggleFlag('+rowNumber+','+i+')" class="flag setFlag"');
			} else {
				flagTemplate = flagTemplate.replace('@@'+i+'@', 'id="f-'+rowNumber+'-'+i+
					'" onclick="toggleFlag('+rowNumber+','+i+')" class="flag"');
			}
		}
		$('#row-'+rowNumber+'-p').html(flagTemplate);
	}
	// restore original permission table
	function restoreRow(rowNumber) {
		$('#row-'+rowNumber).html(rowBackupData['row-'+rowNumber]);
	}
	// send update request
	function updateRow(rowNumber) {
		var permissions = parseInt($('#row-'+rowNumber+'-p div').html());
		var uid = $('#row-'+rowNumber+' td:first-child').html();
		$('body').append($('#edit-form-template').html().replace('@@thispage@', location.href)
				.replace('@@uid@', uid).replace('@@permissions@', permissions));
		$('#edit-form').submit();
	}
	// toggle flag in permission table
	function toggleFlag(row,flag) {
		var permissions = parseInt($('#row-'+row+'-p div').html());
		$('#row-'+row+'-p div').html((permissions^(1<<flag))>>>0);
		$('#f-'+row+'-'+flag).toggleClass('setFlag');
	}
	// Render permission tables from template
	function pageInit() {
		var flagTemplate, flags, i, j;
		for(j = 1; j <= <?php echo $rowno;?>; j++) {
			flagTemplate = $('#flag-template').html();
			flags = parseInt($('#row-'+j+' td:nth-child(3)').html());
			flagTemplate = flagTemplate.replace('@@permit@', flags);
			for (i = 0; i < 32; i++) {
				if(flags & (1<<i)) {
					flagTemplate = flagTemplate.replace('@@'+i+'@', 'class="flag setFlag"');
				} else {
					flagTemplate = flagTemplate.replace('@@'+i+'@', 'class="flag"');
				}
            }
			$('#row-'+j+' td:nth-child(3)').html(flagTemplate);
		}
	}
</script>
<?php 
	mysqli_close($sibylSQL);
} /* Close include check */ ?>

<!-- END OF FILE ----------------------------------------------------------------------------------->
