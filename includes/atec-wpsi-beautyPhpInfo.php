<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_beautyPhpInfo { function __construct() {

ob_start();
// @codingStandardsIgnoreStart
// atec-system-info is an admin-tool, therefore phpinfo() is needed.
phpinfo(48);	
// @codingStandardsIgnoreEnd	

$phpinfo = ob_get_clean();
$phpinfo = preg_replace('#^.*<body>(.*)</body>.*$#s', '$1', $phpinfo);
$phpinfo = str_replace(['<table>','class="e"'], ['<table class="atec-table atec-table-tiny atec-table-td-first atec-mb-20">','class="e atec-nowrap"'], $phpinfo);
$phpinfo = str_replace(['<font','</font>','<h2>','</h2>'],['<span','</span>','<div class="atec-head"><h3>','</h3></div>'], $phpinfo);
$phpinfo = preg_replace('#>(on|enabled|active)#i', '><span style="color:#090">$1</span>', $phpinfo);
$phpinfo = preg_replace('#>(off|disabled)#i', '><span style="color:#f00">$1</span>', $phpinfo);

echo '<div id="phpinfo" style="display:none;">', esc_html($phpinfo), '</div>';

?>
	<script>
	phpinfo=jQuery("#phpinfo"); html=phpinfo.html();
	html = html.replaceAll('&lt;', '<'); 
	html = html.replaceAll('&gt;', '>');
	html = html.replaceAll('&quot;', '"');
	html = html.replaceAll('&#039;', "'");
	html = html.replaceAll('&amp;', "&");
	
	html = html.replaceAll('%20', " ");
	html = html.replaceAll('%7C', "| ");
	html = html.replaceAll('%3D', "=");
	html = html.replaceAll('|', "| ");
	html = html.replaceAll('%26', "&<br>");
	phpinfo.html(html).show();
	</script>
<?php
	
}}

new ATEC_beautyPhpInfo();
?>