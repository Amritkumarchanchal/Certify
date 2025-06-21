<?php
//Exit if file called directly
if (! defined( 'ABSPATH' )) {
	exit;
}


// add top-level administrative menu
function course_certificate_admin_menu() {
	
	/* 
		add_menu_page(
			string   $page_title, 
			string   $menu_title, 
			string   $capability, 
			string   $menu_slug, 
			callable $function = '', 
			string   $icon_url = '', 
			int      $position = null 
		)
	*/
		add_menu_page(
		'Certify',
		'Certify',
		'manage_options',
		'certificate-codes',
		'course_certificate_admin_certificate_ui',
		plugin_dir_url(__FILE__) . '../assets/images/menu-icon.png',
		null
	);
	
}
add_action( 'admin_menu', 'course_certificate_admin_menu' );