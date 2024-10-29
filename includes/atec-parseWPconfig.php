<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_parseWPconfig { function __construct() {		

global $wp_filesystem;
WP_Filesystem();

$configPath=rtrim(get_home_path(),'/').'/wp-config.php';
$config='';
if ($wp_filesystem->exists($configPath)) $config=$wp_filesystem->get_contents($configPath);

atec_little_block('Config file (wp_config.php)');
echo '<h4>Path: ', esc_url($configPath), '</h4>';

if ($config!='')
{
	$config = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $config);
	$config = preg_replace('/\/\*\*#\@\+\n\s(\*\s[^\n]*)\n[\s\S]*?(\n\s\*\/)/',"/*$1*/", $config);
	$config = preg_replace('/\/\*\*#\@\-\*\/\n/', '', $config);
	$config = preg_replace('/\/\*\*\n\s(\*\s[^\n]*)\n[\s\S]*?(\n\s\*\/)/',"/*$1*/", $config);				
	echo '<div class="atec-code" id="atec_wp_config">',esc_html($config),'</div>';
	atec_reg_inline_script('parseWPconfig', 'parseWPconfig();', true);
}
else echo '<p class="atec-red">Can not read .htaccess.</p>';
}}

new ATEC_parseWPconfig();
?>
