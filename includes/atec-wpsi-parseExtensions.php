<?php
if (!defined( 'ABSPATH' )) { exit; }

if (!class_exists('ATEC_wpc_tools')) require_once('atec-wpc-tools.php');

class ATEC_parseExtensions { 

function createIcon($icon): string { return plugins_url( '/assets/img/system/'.$icon.'-icon.svg', __DIR__ ); }
	
function __construct() {	

$tools=new ATEC_wpc_tools();

atec_little_block('PHP Extensions');

echo '
	<div class="atec-g atec-g-14 atec-overflow block_style">';
		$arr=get_loaded_extensions();
		natcasesort($arr);
		foreach ($arr as $a) 
		{ 
			$array = array('Zend OPcache','apcu','memcached','redis','sqlite3','zip','brotli');
			echo '<span class="atec-dilb" style="padding: 2px 5px; white-space:nowrap;">
			<span class="',esc_attr(atec_dash_class('yes')),'"></span>';
				echo in_array($a,$array)?'<font style="font-weight:bold;" color="green">':'<font>';
				echo(esc_attr($a));
				echo '</font>
			</span>';
		}
echo '
	</div>

	<div class="atec-g atec-g-25 block_style" style="padding: 10px;">';

		if ( extension_loaded('apcu') ) {
			echo '<div>', '<img class="atec-sys-icon" src="', esc_url($this-> createIcon('memory')), '">', '<b>APCu:</b> ver. ', esc_attr(phpversion('apcu')), ' ';
            $tools->enabled(apcu_enabled());
            echo '</div>';
		}

		if ( extension_loaded('curl') && function_exists( 'curl_version')) {
			echo '<div>', '<img class="atec-sys-icon" src="', esc_url($this-> createIcon('curl')), '">', '<b>CURL:</b> ver. ', esc_attr(@curl_version()['version']), '</div>';
		}

		if ( extension_loaded('gd') && function_exists( 'gd_info')) {
			echo '<div>', '<img class="atec-sys-icon" src="', esc_url($this-> createIcon('gd')), '">', '<b>GD:</b> ver. ', esc_attr(gd_info()['GD Version']), '</div>';
		}
		
		if ( extension_loaded('imagick') && class_exists( 'Imagick')) {
			preg_match('/ImageMagick\s([\d\.-]*)/', Imagick::getVersion()['versionString'], $match);
			if (isset($match[1])) echo '<div>', '<img class="atec-sys-icon" src="', esc_url($this-> createIcon('imagick')), '">', '<b>Imagick:</b> ver. ', esc_attr($match[1]), '</div>';
		}

		if ( extension_loaded('memcached') && class_exists( 'Memcached')) {
			$m = new Memcached();
			$m->addServer('localhost', 11211);
			$mem = $m->getStats();
			if ($mem) $mem = $mem['localhost:11211'];
			else $mem['version']='failed';
			echo '<div>', '<img class="atec-sys-icon" src="', esc_url($this-> createIcon('memcached')), '">', '<b>Memcached:</b> ', 'ver. ', esc_attr($mem['version']), ' ';
            $tools->enabled($mem['version']!=='failed');
            echo '</div>';
		}

		global $wpdb;
		// @codingStandardsIgnoreStart
		$mysqlVersion = $wpdb->db_version();
		// @codingStandardsIgnoreEnd
		echo '<div>', '<img class="atec-sys-icon" src="', esc_url($this-> createIcon('mysql')), '">', '<b>MySQL:</b> ver. ', esc_attr($mysqlVersion), '</div>';

		if ( function_exists('opcache_get_configuration')) {
			$opcache_enabled=opcache_get_configuration()['directives']['opcache.enable'];
			echo '<div>', '<img class="atec-sys-icon" src="', esc_url($this-> createIcon('memory')), '">', '<b>Opcode-Cache:</b> ', ($opcache_enabled?esc_attr__('enabled','atec-system-info'):esc_attr__('disabled','atec-system-info')), ' ';
            $tools->enabled($opcache_enabled);
            echo '</div>';
		}

		if ( extension_loaded('redis') && class_exists( 'Redis')) {
			$redis = new Redis();
			try { 
				$redis->connect('localhost', 6379);
				if (is_object($redis)) 	{ echo '<div>', '<img class="atec-sys-icon" src="', esc_url($this-> createIcon('redis')), '">', '<b>Redis:</b> ver. ', esc_attr($redis->info('server')['redis_version']), '</div>'; }
			}
			catch (Exception $e) {}
		}
		
		if ( extension_loaded('sqlite3') && class_exists( 'SQLite3')) {
			echo '<div>', '<img class="atec-sys-icon" src="', esc_url($this-> createIcon('sqlite')), '">', '<b>SQLite3:</b> ver. ', esc_attr(SQLite3::version()['versionString']), '</div>';
		}

	echo '
	</div>';

}}

new ATEC_parseExtensions;
?>