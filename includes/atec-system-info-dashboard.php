<?php
if (!defined( 'ABSPATH' )) { exit; }
if (!class_exists('ATEC_wpc_tools')) require_once('atec-wpc-tools.php');
if (!class_exists('ATEC_wp_memory')) require_once('atec-wp-memory.php');

function atec_createTable($title,$arr,$valid): void
{		  
	if ($title!='') atec_little_block($title);
	echo '<table class="atec-table atec-table-td-first atec-table-tiny atec-mb-10">';
  	foreach ($arr as $key => $value) { if ((empty($valid) || in_array($key,$valid)) && $value!='' && (gettype($value)!='array' || !empty($value))) { echo '<tr><td>'.esc_attr($key),'</td><td>'.esc_attr($value),'</td></tr>'; } }
	echo '</table>';
}

class ATEC_wpsi_results { function __construct() {
	
$wpc_tools=new ATEC_wpc_tools();
$mem_tools=new ATEC_wp_memory();

echo '
<div class="atec-page">';
	$mem_tools->memory_usage();
	atec_header(__DIR__,'wpsi','System Info');	

	echo '
	<div class="atec-main">';
	
		$url		= atec_get_url();
		$nonce	= wp_create_nonce(atec_nonce());
		$nav 	= atec_clean_request('nav');
		
		if ($nav=='') $nav='Status';

		$licenseOk=atec_check_license()===true;
		$arr=['#square-h Status','#server Server','#php Environment','#php phpinfo()','#php php.ini','#php Extensions','#php Variables','#sliders wp-config.php'];
		if (!(str_starts_with(strtolower(PHP_OS_FAMILY),'win'))) $arr[]='#wrench .htaccess';
		atec_nav_tab($url, $nonce, $nav, $arr, 4, !$licenseOk);
		
		echo '
		<div class="atec-g atec-border">
			<div class="atec-overflow">';
			atec_progress();
	
			if ($nav=='Info') { require_once('atec-info.php'); new ATEC_info(__DIR__); }
  			elseif ($nav=='Status') { require_once('atec-wpsi-systemStatus.php'); }
			elseif ($nav=='Server') { require_once('atec-server-info.php'); }
			elseif ($nav=='Environment') { require_once('atec-wpsi-parsePHPinfo.php'); }
			elseif ($nav=='phpinfo') { require_once('atec-wpsi-phpinfo.php'); }
			elseif ($nav=='php_ini') 
			{ 
				if (atec_pro_feature('`php.ini´ lists all options of the php.ini file and their values')) 
				{ @include_once('atec-wpsi-phpINI-pro.php'); atec_missing_class_check('ATEC_wpsi_phpINI'); }
			}
			elseif ($nav=='Extensions') 
			{ 
				if (atec_pro_feature('`Extension´ lists all active PHP extensions and checks whether recommended extensions are installed. Also shows version numbers of important extensions')) 
				{ @include_once('atec-wpsi-parseExtensions-pro.php'); atec_missing_class_check('ATEC_parseExtensions'); }
			}
			elseif ($nav=='Variables') 
			{ 
				if (atec_pro_feature('`Variables´ lists all PHP server variables and their values')) 
				{ @include_once('atec-wpsi-php-variables-pro.php'); atec_missing_class_check('ATEC_php_variables'); }
			}		
			elseif ($nav=='wp_config_php') 
			{ 
				if (atec_pro_feature('`WP-Config´ shows the content of the wordpress wp-config.php file')) 
				{ @include_once('atec-parseWPconfig-pro.php'); atec_missing_class_check('ATEC_parseWPconfig'); }
			}
			elseif ($nav=='_htaccess') 
			{ 
				if (atec_pro_feature('`.htaccess´ shows the content of the server .htaccess file')) 
				{ @include_once('atec-wpsi-htaccessParser-pro.php'); atec_missing_class_check('ATEC_htaccess_parser'); }
			}

		echo '</div>
		</div>
	</div>
</div>';

if (!class_exists('ATEC_footer')) require_once('atec-footer.php');

}}

new ATEC_wpsi_results;
?>