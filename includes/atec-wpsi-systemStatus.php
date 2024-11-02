<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_wpsi_systemStatus { 

private function yes(): void { echo '<span class="',esc_attr(atec_dash_class('yes-alt')),'"></span>'; }
private function no(): void { echo '<span class="',esc_attr(atec_dash_class('dismiss')),'"></span>'; }

private function createStatusTableHeader(): void
{
	echo '<table class="atec-table atec-mb-10">
	<thead><tr><th>Feature</th><th>Description</th><th>Recommended</th><th>Grade</th></tr></thead>
	<tbody>';
}

private function createStatusTableFooter(): void { echo '</tbody></table>'; }
  
private function bold($str, $bold=''): void
{
	if ($bold=='') echo esc_attr($str);
	else
	{
		$arr	= explode('###',str_replace($bold,'###',$str));
		echo esc_attr($arr[0]), '<font color="darkblue"><b>', esc_attr($bold), '</b></font>', (isset($arr[1])?esc_attr($arr[1]):'');
	}
}

 private function createTR($feature, $true, $trueStr, $falseStr, $yesNo, $recommended, $bold): void
 {
	 echo '
	 <tr>
		 <td>', esc_attr($feature), '</td>
		 <td>'; if ($true) $this->bold($trueStr.'.', $bold); else $this->bold($falseStr.'.',$bold); echo '</td>',
		'<td>', esc_attr($recommended), '</td>',
		 '<td>'; if ($yesNo) $this->yes(); else $this->no(); echo '</td>',
	 '</tr>';				
 }
	
function __construct() {		

atec_little_block('Versions');
$this->createStatusTableHeader();

function version($str): array
{
	$ex	= explode('.', $str); 
	$sub=isset($ex[2])?(int) $ex[2]:0;
	return ['major'=>(int) $ex[0], 'minor'=>(int) $ex[1], 'sub'=>$sub, 'str'=>$ex[0].'.'.$ex[1].'.'.$sub];
}

$response	 = wp_remote_get('https://api.wordpress.org/core/version-check/1.7/');
if (!is_wp_error($response) && isset($response['body']))
{
	$latest			= version(json_decode($response['body'])->offers[0]->version);
	$current		= version(get_bloginfo('version'));
	$up2date		= $current['major']==$latest['major'] && $current['minor']==$latest['minor'];
	$this->createTR('WordPress',$up2date,'WP version '.esc_attr($current['str']).' is up to date','WP version is '.esc_attr($current['str']).' is outdated',$up2date,'Ver. '.$latest['str'],$current['str']);
}

if (defined('PHP_VERSION_ID')) 
{
	$current	= version(PHP_VERSION);
	$up2date	= $current['major']==8 && $current['minor']>2;
	$this->createTR('PHP',$up2date,'PHP version '.esc_attr($current['str']).' is up to date','PHP version '.esc_attr($current['str']).' is outdated',$up2date,'Ver. 8.3',$current['str']);
}
	
$opcache=false; $jit=false;
if (function_exists('opcache_get_configuration'))
{
	$op_conf=opcache_get_configuration();
	$opcache=$op_conf['directives']['opcache.enable'];
	$opcacheMem= $op_conf['directives']['opcache.memory_consumption'] ?? 0;
	if (function_exists('opcache_get_status')) 
	{
		$op_status=opcache_get_status();
		$jit=isset($op_status['jit']) && $op_status['jit']['enabled'] && $op_status['jit']['on'];
		$jit_size= $op_status['jit']['buffer_size'] ?? 0;
	}
}
	
global $wpdb;
// @codingStandardsIgnoreStart
$dbVersion=$wpdb->get_var('SELECT VERSION()');
$dbName=str_contains(strtolower($dbVersion), 'mariadb')?'MariaDB':'MySQL';
$dbVersion=str_ireplace('-mariadb', '', $dbVersion);
// @codingStandardsIgnoreEnd


// @codingStandardsIgnoreEnd

$current = version($dbVersion);
if ($dbName=='MariaDB') { $recommended='11.7.0'; $up2date = $current['major']===11 && $current['minor']>=6; }
else { $recommended='9.0'; $up2date	= $current['major']===9 && $current['minor']>=0; }
$this->createTR($dbName, $up2date, $dbName.' version '.$current['str'].' is up to date', $dbName.' version '.$current['str'].' is outdated',$up2date,'Ver. '.$recommended,$current['str']);

$this->createTR('OPcache',$opcache,'OPcache is enabled with '.esc_attr(size_format($opcacheMem)),'OPcache is NOT enabled',$opcache,'Enabled',size_format($opcacheMem));
$this->createTR('JIT',$jit,'JIT is enabled with '.esc_attr(size_format($jit_size)),'JIT is NOT enabled',$jit,'Enabled',$jit?size_format($jit_size):'');

$this->createStatusTableFooter();

atec_little_block('WP settings');
$this->createStatusTableHeader();
	$value=WP_DEBUG && WP_DEBUG_DISPLAY;
	$this->createTR('WP_DEBUG_DISPLAY',$value,'WP_DEBUG & WP_DEBUG_DISPLAY are enabled, this is not recommended in a production environment','Disabled',!$value,'Disabled','');
	$value=defined('WP_MEMORY_LIMIT');
	$this->createTR('WP_MEMORY_LIMIT',$value,'WP_MEMORY_LIMIT is set to '.esc_attr(WP_MEMORY_LIMIT),'WP_MEMORY_LIMIT defaults to 40M if it is not set',$value,'> 64 MB',WP_MEMORY_LIMIT);
	$value=defined('WP_REPAIR')?WP_REPAIR:false;
	$this->createTR('WP_REPAIR',$value,'WP_REPAIR is enabled, this is not recommended in a production environment','Disabled',!$value,'Disabled','');
$this->createStatusTableFooter();

atec_little_block('Server status');
$this->createStatusTableHeader();
	
$dt		= @disk_total_space(ABSPATH);
$df		= @disk_free_space(ABSPATH);
$gb		= $df/1024/1024/1024;
$dp		= ($dt && $df)?round($df/$dt*100):0;
$yesNo = $gb>10 || $dp>10;
$this->createTR('Disk space',$yesNo,'The server has '.esc_attr(size_format($df)).' ('.esc_attr($dp).' %) of free storage','Free storeage is '.esc_attr(size_format($df)).' ('.esc_attr($dp).' %)',$yesNo,'> 10 GB || > 10 %',size_format($df));

$peak	= './.';
$mp		= '';

$memLimit	= wp_convert_hr_to_bytes(!defined('WP_MEMORY_LIMIT') || WP_MEMORY_LIMIT==''?'40M':WP_MEMORY_LIMIT);
if (function_exists('memory_get_peak_usage')) { $peak=memory_get_peak_usage(true); $mp='('.round($peak/$memLimit*100,1).' %)'; }

$yesNo=$mp<25;
$this->createTR('WP memory',$yesNo,'The peak usage is '.esc_attr(size_format($peak)).' '.esc_attr($mp),'The peak usage is above '.esc_attr($mp).' % of the WP memory limit',$yesNo,'< 25 %',size_format($peak));

$memLimit	= wp_convert_hr_to_bytes(ini_get('memory_limit'));
$inMB			= $memLimit/1024/1024;
$yesNo			= $inMB>=128;
$this->createTR('PHP memory limit',$yesNo,'The limit is set to '.esc_attr(size_format($memLimit)),'The limit of '.esc_attr(size_format($memLimit)).' is too low',$yesNo,'>= 128 MB',size_format($memLimit));

$this->createStatusTableFooter();

}}

new ATEC_wpsi_systemStatus();
?>