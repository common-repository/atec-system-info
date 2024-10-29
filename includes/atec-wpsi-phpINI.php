<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_wpsi_phpINI { function __construct() {		

$phpIniPath=@php_ini_loaded_file();
$phpini=$phpIniPath?@parse_ini_file($phpIniPath,true):'';

atec_little_block('PHP.ini');
if ($phpIniPath!='') echo '<h4 class="atec-mb-10">Path: ', esc_url($phpIniPath), '</h4>';

if (!$phpini) echo '<p class="atec-red">Can not read/parse php.ini file.</p>';
else
{
	foreach ($phpini as $key => $value)
	{
		if (gettype($value) == 'array' && !empty($value)) { atec_createTable($key,$value,[]); }
		else if (!empty($value)) { $arr[$key]=$value; atec_createTable('',$arr,[]); unset($arr); }
	}
}

}}

new ATEC_wpsi_phpINI();
?>