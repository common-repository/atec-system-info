<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_wpsi_phpinfo { function __construct() {		
	
atec_little_block('phpinfo() · Version: '.esc_attr(phpversion()));

atec_reg_inline_style('','
	TABLE:first-child { border: none; background:transparent !important; box-shadow: none; } 
	TABLE:first-child TR { display: none; }
	TABLE TR TD H2 { margin-bottom: 0; }
	A { text-decoration: none; }
	.block_ext { display: inline-block; padding: 2px 5px; white-space:nowrap; }');

ob_start();
// @codingStandardsIgnoreStart
// atec-system-info is an admin-tool, therefore phpinfo() is needed.
phpinfo(13);
// @codingStandardsIgnoreEnd
$phpinfo = ob_get_clean();
$phpinfo = preg_replace('#^.*<body>(.*)</body>.*$#s', '$1', $phpinfo);
$phpinfo = str_replace(['<table>','class="e"'], ['<table class="atec-table atec-table-tiny atec-table-td-first atec-mb-20">','class="e atec-nowrap"'], $phpinfo);
$phpinfo = str_replace(['<font','</font>','<h2>','</h2>'],['<span','</span>','<div class="atec-head"><h3>','</h3></div>'], $phpinfo);
$phpinfo = preg_replace('#>(on|enabled|active)#i', '><span style="color:#090">$1</span>', $phpinfo);
$phpinfo = preg_replace('#>(off|disabled)#i', '><span style="color:#f00">$1</span>', $phpinfo);
$phpinfo = preg_replace('#<img([^>.]*)>#s', '', $phpinfo);
$phpinfo = preg_replace('#<a href="http:\/\/www.php.net\/"><\/a>#s', '', $phpinfo);				

$links='<div class="atec-g atec-g-14 atec-overflow block_style atec-mb-10">';
preg_match_all('/<a\sname="[\w_]*"\shref="(#[\w_]*)">(.*)<\/a>/m', $phpinfo, $matches);				
foreach ($matches[0] as $a)
{
	$a=preg_replace('/<a\sname="[\w_]*"\shref="(#[\w_]*)">(.*)<\/a>/m', "<a href='#module_$2'>$2</a>", $a);
	$links.='<span class="block_ext" style="max-width: 100%;">'.$a.'</span>';					
}
$links.='</div>';
$phpinfo = str_replace('<h1>Configuration</h1>','<h2>Configuration</h2>'.$links, $phpinfo);
$phpinfo = preg_replace('#<hr \/>#', '<br>', $phpinfo);
$phpinfo = preg_replace('#<h1[\s\w="]*>(.*)<\/h1>#s', "<h2>$1</h2>", $phpinfo);

echo '<div id="phpinfo" style="display:none; margin-top: -10px;">', esc_html($phpinfo), '</div>';
atec_reg_script('atec_wpsi',__DIR__,'atec-wpsi.min.js','1.0.0');
atec_reg_inline_script('', 'phpinfo();');
	
}}

new ATEC_wpsi_phpinfo();
?>