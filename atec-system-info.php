<?php
if (!defined( 'ABSPATH' )) { exit; }
  /**
  * Plugin Name:  atec System Info
  * Plugin URI: https://atecplugins.com/
  * Description: Highly detailed system information (system health status, server info (OS, memory & DB) and comprehensive server and PHP configuration details.
  * Version: 1.1.8
  * Requires at least: 5.2
  * Tested up to: 6.7
  * Requires PHP: 7.4
  * Author: Chris Ahrweiler
  * Author URI: https://atec-systems.com
  * License: GPL2
  * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
  * Text Domain:  atec-system-info
  */
  
if (is_admin()) 
{ 
	wp_cache_set('atec_wpsi_version','1.1.8');
	register_activation_hook( __FILE__, function() { require_once('includes/atec-wpsi-activation.php'); });
    require_once('includes/atec-wpsi-install.php');
}
?>