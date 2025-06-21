<?php
// Prevent direct access to this file for security
if (! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Create the main admin menu page for certificate management
 * This adds "Certify" to the WordPress admin sidebar
 */
function certify_certificate_admin_menu() {
	
	/* 
		WordPress add_menu_page function parameters:
		string   $page_title, 
		string   $menu_title, 
		string   $capability,		string   $menu_slug, 
		callable $function = '', 
		string   $icon_url = '', 
		int      $position = null 
	*/
	// Add the main Certify menu page to WordPress admin
	add_menu_page(
		'Certify',
		'Certify',
		'manage_options',
		'certify-certificate-management',
		'certify_certificate_admin_certificate_ui',
		plugin_dir_url(__FILE__) . '../assets/images/menu-icon.png',
		null
	);
	
}
// Hook our menu function to WordPress admin menu system
add_action( 'admin_menu', 'certify_certificate_admin_menu' );