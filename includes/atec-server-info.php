<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_server_info { 
	
private function envExists($str): string { return isset($_SERVER[$str])?sanitize_text_field(wp_unslash
($_SERVER[$str])):''; }
private function offset2Str($tzOffset): string { return ($tzOffset>0?'+':'').$tzOffset; }
private function createIcon($icon): string { return plugins_url( '/assets/img/system/'.$icon.'-icon.svg', __DIR__ ); }

private function getGeo($ip): string
{
	$url			= 'https://ipinfo.io/'.$ip.'/json?token=274eb3cf12e5f5';
	$request	= wp_remote_get( $url );
	if (is_wp_error($request)) { return ''; }
    $geo = json_decode( wp_remote_retrieve_body( $request ) );
	return (isset($geo->city) && isset($geo->country))?($geo->city.' / '.$geo->country):'';
}

private function tblHeader($icon,$title,$arr): void
{
	echo '
	<div class="atec-mb-5">
		<div style="padding: 0 0 5px 10px;">',
			'<img class="atec-sys-icon" src="', esc_url($this->createIcon($icon)), '"><span class="atec-label">', esc_attr($title), '</span>',
		'</div>
		<table class="atec-table atec-table-td-first atec-mb-10">
			<thead>
				<tr>';
					foreach($arr as $a) { echo '<th>',esc_attr($a),'</th>'; }
				echo '
				</tr>
			</thead>
		<tbody>
		<tr>';
}

private function tblFooter(): void
{
	echo '</tr>
		</tbody>
	</table></div>';
}

function __construct() {	
	
$host	= sanitize_text_field(php_uname('n'));
$ip		= $this->envExists('SERVER_ADDR');
if ($ip!='') { $host .= ($host!==''?' | ':'').$ip; }
if (function_exists('curl_version')) { $curl = @curl_version(); }
else { $curl=array('version'=>'n/a', 'ssl_version'=>'n/a'); }

global $wpdb;
$mysql_version = $wpdb->db_version();

$peak='./.';
if (function_exists('memory_get_peak_usage')) { $peak=size_format(memory_get_peak_usage(true)); }

atec_little_block('Server Info');

$dt	= disk_total_space(ABSPATH);
$df	= disk_free_space(ABSPATH);
$dp	= ($dt && $df)?'('.round($df/$dt*100,1).'%)':'';

$unlimited	= atec_get_slug()==='atec_wpsi';
$tz				= date_default_timezone_get()?date_default_timezone_get():(ini_get('date.timezone')?ini_get('date.timezone'):'');
$tzOffset		= intval(get_option('gmt_offset',0));
$now			= new DateTime('', new DateTimeZone('UTC'));
$now			= $now->modify($this->offset2Str($tzOffset).' hour');
$geo				= '';

if ($ip!='' && $ip!='127.0.0.1' && $ip!='::1')
{
	$lastIP=get_option('atec_WPSI_ip','');
	$geo=get_option('atec_WPSI_geo','');
	if ($ip!=$lastIP || $geo=='')
	{
		$geo=$this->getGeo($ip);
		update_option('atec_WPSI_ip',esc_attr($ip),false);
		update_option('atec_WPSI_geo',esc_attr($geo),false);
	}
}

echo '
<div class="atec-g atec-g-50">';
	
	$this->tblHeader('computer',__('Operating system','atec-system-info'),['OS','Version',__('Architecture','atec-system-info'),__('Date/Time','atec-system-info'),'Disk&nbsp;total','Disk&nbsp;'.__('free','atec-system-info')]);
		echo '
		<td class="atec-nowrap">';
			$os=php_uname('s');
			$icon='';
			switch ($os)
			{
				case 'Darwin': $icon='apple'; break;
				case 'Windows': $icon='windows'; break;
				case 'Linux': $icon='linux'; break;
				case 'Ubuntu': $icon='ubuntu'; break;
			}
			if ($icon!=='') echo '<img class="atec-sys-icon" src="', esc_url($this->createIcon($icon)), '">';
			echo 
			esc_attr(php_uname('s')), 
		'</td>
		<td>', esc_attr(php_uname('r')), '</td>
		<td>', esc_attr(php_uname('m')), '</td>
		<td>', esc_attr(date_format($now,"Y-m-d H:i")), ' ', esc_attr($tz.' '.$this->offset2Str($tzOffset)), '</td>	
		<td class="atec-nowrap">', ($dt?esc_attr(size_format($dt)):'./.'), '</td>
		<td class="atec-nowrap">', ($df?esc_attr(size_format($df)):'./.'), '&nbsp;', esc_attr($dp), '</td>';		
	$this->tblFooter();
	
	$headArray=['Host','IP'];
	if ($geo!='') $headArray[] = __('Location','atec-system-info');
	$headArray[] = 'Server'; 	
	$headArray[] = 'CURL';
	$this->tblHeader('server','Server',$headArray);
	$serverSoftware	= $this->envExists('SERVER_SOFTWARE');
	$serverName		= $this->envExists('SERVER_NAME');
	
	echo 
		'<td>', esc_html($serverName),'</td>
		<td>', esc_html($host),'</td>';
	
	if ($geo!='') echo '<td>', esc_html($geo), '</td>';
	echo '<td>';		
			$icon=''; 
			$lowSoft=strtolower($serverSoftware);
			if (str_contains($lowSoft,'apache')) $icon='apache';
			else	if (str_contains($lowSoft,'nginx')) $icon='nginx';
					else if (str_contains($lowSoft,'litespeed')) $icon='litespeed';
			if ($icon!=='') echo '<img class="atec-sys-icon" src="', esc_url($this->createIcon($icon)), '">';
			echo esc_html($serverSoftware),'
		</td>
		<td>', 
			'<img class="atec-sys-icon" src="', esc_url($this->createIcon('curl')), '">', 'ver.&nbsp;', esc_attr(function_exists( 'curl_version')?$curl['version'].' | '.str_replace('(SecureTransport)','',$curl['ssl_version']):'n/a'),
		'</td>';
	$this->tblFooter();
	
	if ($unlimited)
	{
		$this->tblHeader('wordpress','Wordpress',['WP '.__('root','atec-system-info'),'WP&nbsp;'.__('size','atec-system-info')]);
		echo '<td>', esc_url(defined('ABSPATH')?ABSPATH:'./.'),'</td>
			<td class="atec-nowrap">', esc_attr(size_format(get_dirsize(get_home_path()))),'</td>';
		$this->tblFooter();
	
		$this->tblHeader('calender',__('Versions','atec-system-info'),['WP','PHP','mySQL']);
		echo '<td>Ver.&nbsp;', esc_html(get_bloginfo('version')),'</td>
			<td>Ver.&nbsp;', esc_attr(phpversion().(function_exists( 'php_sapi_name')?' | '.php_sapi_name():'')),'</td>
			<td>Ver.&nbsp;', esc_attr($mysql_version ?? 'n/a'),'</td>';
		$this->tblFooter();
	}

	$ram='';
	if (function_exists('exec'))
	{
		if ($os=='Darwin')
		{
			$output=null; $retval=null; $cmd='/usr/sbin/sysctl -n hw.memsize';
			@exec($cmd, $output, $retval);
			$ram=($retval==0 && getType($output)=='array' && !empty($output))?intval($output[0]):0;
		}
		elseif ($os!=='Windows')
		{
			$output=null; $retval=null; $cmd='free';
			@exec($cmd, $output, $retval);
			$ram=($retval==0 && getType($output)=='array' && !empty($output) && count($output)>=1)?$output[1]:'';
			if ($ram!=='') 
			{
				$re = '/\s+([\d]*)\s+/';		
				preg_match($re, $ram, $match);
				$ram=$match[1] ?? '';
			}
		}
	}
	$memArr=[];
	if ($ram!=='') $memArr[] = 'System RAM';
	$memArr=array_merge($memArr,['PHP mem. limit','WP mem. limit','WP max. mem. limit','mem. '.__('usage (peak)','atec-system-info')]);

	$this->tblHeader('memory','Memory',$memArr);
	if ($ram!=='') echo '<td>', esc_attr(size_format($ram)), '</td>';
	echo '<td>', esc_attr(ini_get('memory_limit')), '</td>
		<td>', esc_attr(WP_MEMORY_LIMIT), '</td>
		<td>', esc_attr(WP_MAX_MEMORY_LIMIT), '</td>
		<td>', esc_attr($peak), '</td>';
	$this->tblFooter();
	
	$this->tblHeader('php','PHP '.__('Settings','atec-system-info'),['max. exec. time','max. input vars','post max. size','upload max. filesize']);
	echo '<td>', esc_attr(gmdate('H:i:s', ini_get('max_execution_time'))),'</td>
		<td>', esc_attr(number_format(ini_get('max_input_vars'))),'</td>
		<td>', esc_attr(ini_get('post_max_size')),'</td>
		<td>', esc_attr(ini_get('upload_max_filesize')),'</td>';
	$this->tblFooter();
	
	if ($unlimited)
	{
		global $wpdb;
		// @codingStandardsIgnoreStart
		$db_soft 					= $wpdb->get_results('SHOW VARIABLES LIKE "version_comment"');
		$db_ver 					= $wpdb->get_var('SELECT VERSION() AS version from DUAL');
		$db_max_conn			= $wpdb->get_results('SHOW VARIABLES LIKE "max_connections"');
		$db_max_package 	= $wpdb->get_results('SHOW VARIABLES LIKE "max_allowed_packet"');
		$tablesstatus = $wpdb->get_results('SHOW TABLE STATUS');
		// @codingStandardsIgnoreEnd
		
		$db_disk		= 0;
		$db_index	= 0;
		foreach ($tablesstatus as $tablestatus) 
		{ $db_disk += $tablestatus->Data_length; $db_index += $tablestatus->Index_length; }
		
		$this->tblHeader('database',__('Database','atec-system-info'),['DB '.__('driver','atec-system-info'),'DB&nbsp;ver.','DB&nbsp;'.__('user','atec-system-info'),'DB&nbsp;'.__('user','atec-system-info')]);
		echo '<td>', ($db_soft?esc_html($db_soft[0]->Value):'./.'), '</td>
				<td>Ver.&nbsp;', ($db_ver?esc_attr($db_ver):'./.'), '</td>
				<td>', esc_attr(defined('DB_NAME')?DB_NAME:'./.'), '</td>
				<td>', esc_attr(defined('DB_USER')?DB_USER:'./.'), '</td>';
		$this->tblFooter();
	
		$this->tblHeader('database',__('Database settings','atec-system-info'),['DB&nbsp;max.&nbsp;'.__('conn.','atec-system-info'),'DB&nbsp;max.&nbsp;'.__('packages','atec-system-info'),'DB&nbsp;size','DB&nbsp;Index&nbsp;'.__('size','atec-system-info')]);
		echo '<td>', ($db_max_conn?esc_attr($db_max_conn[0]->Value):'./.'), '</td>
				<td class="atec-nowrap">', ($db_max_package?esc_attr(size_format($db_max_package[0]->Value)):'./.'), '</td>
				<td class="atec-nowrap">', ($db_disk?esc_attr(size_format($db_disk)):'./.'), '</td>
				<td class="atec-nowrap">', ($db_index?esc_attr(size_format($db_index)):'./.'), '</td>';
		$this->tblFooter();
	}

echo '</div>';
	
}}

new ATEC_server_info();
?>