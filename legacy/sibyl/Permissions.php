<?php if(defined("INCLUDED")) { ?>
<div class="panel-body" style="text-align:center;">
	<table style="margin:0px auto;">
		<tr><th class="pside"></th>
		<th class="phead">0x80</th><th class="phead">0x40</th><th class="phead">0x20</th><th class="phead">0x10</th>
		<th class="phead">0x08</th><th class="phead">0x04</th><th class="phead">0x02</th><th class="phead">0x01</th></tr>
		
		<tr><td class="pside">0x1000000<br />(LAN only)</td>
		<td class="plflag pflag" id="f-2147483648"></td><td class="plflag pflag" id="f-1073741824"></td>
		<td class="plflag pflag" id="f-536870912"></td><td class="plflag pflag" id="f-268435456"></td>
		<td class="plflag pflag" id="f-134217728"></td><td class="plflag pflag" id="f-67108864"></td>
		<td class="plflag pflag" id="f-33554432"></td><td class="plflag pflag" id="f-16777216"></td></tr>
		
		<tr><td class="pside">0x10000</td>
		<td class="pflag" id="f-8388608"></td><td class="pflag" id="f-4194304"></td>
		<td class="pflag" id="f-2097152"></td><td class="pflag" id="f-1048576"></td>
		<td class="pflag" id="f-524288"></td><td class="pflag" id="f-262144"></td>
		<td class="pflag" id="f-131072"></td><td class="pflag" id="f-65536"></td></tr>
		
		<tr><td class="pside">0x100</td>
		<td class="pflag" id="f-32768"></td><td class="pflag" id="f-16384"></td>
		<td class="pflag" id="f-8192"></td><td class="pflag" id="f-4096"></td>
		<td class="pflag" id="f-2048"></td><td class="pflag" id="f-1024"></td>
		<td class="pflag" id="f-512"></td><td class="pflag" id="f-256"></td></tr>
		
		<tr><td class="pside">0x1</td>
		<td class="pflag" id="f-128"></td><td class="pflag" id="f-64"></td>
		<td class="pflag" id="f-32"></td><td class="pflag" id="f-16"></td>
		<td class="pflag" id="f-8"></td><td class="pflag" id="f-4"></td>
		<td class="pflag" id="f-2"></td><td class="pflag" id="f-1"></td></tr>
	</table>
</div>
<script>
	function pageInit() {
	<?php foreach(SIBYL::$pFlags as $name => $value) {
		echo "$('#f-$value').html('$name');";
	}?>
	}
</script>

<?php } /* Close include check */ ?>

<!-- END OF FILE ----------------------------------------------------------------------------------->

