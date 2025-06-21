<?php
// Prevent direct access to this file for security
if (! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Plugin deactivation function - cleanup when plugin is deactivated
 * This runs when the plugin is deactivated (but not uninstalled)
 */
function certify_certificate_certify_certificate_onDeactivation() {

	// Security check - only users who can activate plugins can deactivate them
	if ( ! current_user_can( 'activate_plugins' ) ) return;

	// Clear any cached rewrite rules
	flush_rewrite_rules();
}

// Hook for plugin uninstall - this removes everything when plugin is deleted
register_uninstall_hook( __FILE__, 'certify_certificate_drop_certificate_table' );

/**
 * Plugin uninstall function - complete cleanup when plugin is deleted
 * This permanently removes all plugin data from the database
 */
function certify_certificate_drop_certificate_table(){
	// Security check - only administrators can uninstall plugins
	if ( ! current_user_can( 'activate_plugins' ) ) return;
	
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit ();
	// Remove the plugin's database table and any stored options
	global $wpdb;
	$table_name = $wpdb->prefix . 'certify_certificate_management';
	$sql = "DROP TABLE IF EXISTS {$table_name}";
	$wpdb->query($sql);
	// Clean up any plugin options if needed
	// delete_option('certify_plugin_options');
}