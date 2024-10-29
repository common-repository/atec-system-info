<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_htaccess_parser { 

private function formatHtaccess($htaccess) { return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $htaccess); }
	
function __construct() {	

global $wp_filesystem;
WP_Filesystem();
$htaccesPath	= ABSPATH.'.htaccess';
$htaccess			= '';
atec_little_block('Config file (.htaccess)');
echo '<h4>Path: ', esc_url($htaccesPath), '</h4>';

if ($wp_filesystem->exists($htaccesPath)) $htaccess=$wp_filesystem->get_contents($htaccesPath);
if (!$htaccess) echo '<p class="atec-red">Can not read the file or file is empty.</p>';
else
{
	$htaccess = $this->formatHtaccess($htaccess);
	echo '<div class="code" id="htaccess">', esc_html($htaccess), '</div>';
}

$htaccesPath=__DIR__.'/htaccess.txt';
if ($wp_filesystem->exists($htaccesPath)) $htaccess=$wp_filesystem->get_contents($htaccesPath);
if ($htaccess)
{
	$htaccess = $this->formatHtaccess($htaccess);
	echo '<br>';
	atec_help('recommended','Recommended htaccess');
	echo '
	<div id="recommended_help" class="atec-help">
		<p class="atec-bold atec-mb-5 atec-mt-0">Recommended htaccess:</p>
		<div class="code" id="htaccess_txt">', esc_html($htaccess), '</div>
	</div>';
}

atec_reg_script('atec_wpsi',__DIR__,'atec-wpsi.min.js','1.0.0');
atec_reg_inline_script('', 'formatHtaccess("htaccess"); formatHtaccess("htaccess_txt");');

}}

new ATEC_htaccess_parser();
