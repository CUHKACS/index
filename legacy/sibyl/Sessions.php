<?php if(defined("INCLUDED")) { 
	$sibylSQL = mysqli_connect ( "localhost", "sibyl", "", "sibyl" );
	if (mysqli_connect_errno ()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error ();
		exit ( 1 );
	}
?>
<table class="panel-body table table-condensed" style="text-align: center;">
	<tr>
		<td>UID</td>
		<td>Token</td>
		<td>IP</td>
		<td>Last Activity</td>
	</tr>
	<?php
		$records = mysqli_query ( $sibylSQL, "SELECT `uid`,`token`,`ip`,`activity` FROM `sessions`" );
		while ( $row = mysqli_fetch_array ( $records ) ) {
			echo "<tr> <td>{$row['uid']}</td>";
			echo "<td>{$row['token']}</td>";
			echo "<td>{$row['ip']}</td>";
			echo "<td>{$row['activity']}</td></tr>";
		}
	?>
</table>
<script>
	function pageInit() {}
</script>

<?php 
	mysqli_close($sibylSQL);
} /* Close include check */ ?>

<!-- END OF FILE ----------------------------------------------------------------------------------->

