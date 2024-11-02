<?php
if (!defined( 'ABSPATH' )) { exit; }

if (!defined('ATEC_INIT_INC')) require_once('atec-init.php');
add_action('admin_menu', function() { atec_wp_menu(__DIR__,'atec_wpsi','System Info'); } );

add_action('init', function() 
{ 
	if (!class_exists('ATEC_wp_memory')) require_once('atec-wp-memory.php');	
	add_action('admin_bar_menu', 'atec_wp_memory_admin_bar', PHP_INT_MAX);
	
    if (in_array($slug=atec_get_slug(), ['atec_group','atec_wpsi']))
	{
		if (!defined('ATEC_TOOLS_INC')) require_once('atec-tools.php');	
		add_action( 'admin_enqueue_scripts', function() 
		{ 
			atec_reg_style('atec',__DIR__,'atec-style.min.css','1.0.002'); 
			atec_reg_style('atec-wpsi',__DIR__,'atec-wpsi.min.css','1.0.0'); 

		});
		
		if ($slug!=='atec_group')
		{
			atec_reg_script('atec_debug',__DIR__,'atec-debug.min.js','1.0.0');
			function atec_wpsi(): void { require_once(__DIR__.'/atec-system-info-dashboard.php'); }
		}
	}	
});
?>
