<?php
if (!defined( 'ABSPATH' )) { exit; }

class ATEC_PHPInfo_Helper {
	
	static public function phpinfo_general($tb = false): array
    { return self::_parse_phpinfo(INFO_GENERAL, $tb); }

	static public function phpinfo_configuration($tb = false): array
    { return self::_parse_phpinfo(INFO_CONFIGURATION, $tb); }

	static private function _parse_phpinfo($type, $tb): array
    {
		$info_arr = [];
		ob_start();
		// @codingStandardsIgnoreStart
		// atec-system-info is an admin-tool, therefore phpinfo() is needed.
		phpinfo($type);
		// @codingStandardsIgnoreEnd
		$info_lines = explode("\n", strip_tags(ob_get_clean(), "<tr><td><h2>"));
		foreach ($info_lines as $line) {
			if
			(preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val) OR
			preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) 
			{
				if ($tb) $info_arr[] = ["n" => trim($val[1]), "v" => trim(str_replace(';', '; ', $val[2]))];
				else $info_arr[trim($val[1])] = trim(str_replace(';', '; ', $val[2]));
			}
		}
		return $info_arr;
	}

}

class ATEC_parsePHPInfo { 

static private ATEC_PHPInfo_Helper $php;

function __construct() {		

self::$php=new ATEC_PHPInfo_Helper();

atec_createTable('General',self::$php::phpinfo_general(),['System','Server API','Loaded Configuration File','PHP API','Thread Safety']);

atec_createTable('Configuration',self::$php::phpinfo_configuration(),['allow_url_fopen','allow_url_include','disable_functions','display_errors','error_log','expose_php','extension_dir','log_errors','max_execution_time','max_file_uploads','max_input_time','max_input_vars','memory_limit','post_max_size','realpath_cache_size','upload_max_filesize']);

$serverArr=[]; $valid=['SERVER_SOFTWARE','DOCUMENT_ROOT','SERVER_ADDR','SERVER_PORT','SERVER_NAME','HTTP_ACCEPT_ENCODING','HTTPS','HTTP2'];
foreach ($valid as $v) { if (isset($_SERVER[$v])) $serverArr[$v]=sanitize_text_field(wp_unslash($_SERVER[$v])); }
atec_createTable('Environment',$serverArr,['SERVER_SOFTWARE','DOCUMENT_ROOT','SERVER_ADDR','SERVER_PORT','SERVER_NAME','HTTP_ACCEPT_ENCODING','HTTPS','HTTP2']);

}}

new ATEC_parsePHPInfo();
?>