<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_php_variables { function __construct() {

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
atec_reg_script('atec_wpsi',__DIR__,'atec-wpsi.min.js','1.0.0');
atec_reg_inline_script('', 'beautifyPhpinfo();');

}}

new ATEC_php_variables();
?>