<?php
// Prevent direct access to this file for security
if (! defined( 'ABSPATH' )) { 
    exit; 
}

/**
 * Plugin activation function - creates the database table
 * This runs when the plugin is first activated
 */
function certify_certificate_certify_certificate_onActivation(){
	global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    // Define table name with WordPress prefix
    $table_name = $wpdb->prefix . 'certify_certificate_management';
    
    // Create the certificates table with all necessary fields
    $create_table_query = "
    CREATE TABLE IF NOT EXISTS `{$table_name}` (
        id INTEGER NOT NULL AUTO_INCREMENT,
        certificate_code TEXT NOT NULL,
        student_name TEXT NOT NULL,
        course_name TEXT NOT NULL,
        course_hours TEXT NOT NULL,
        dob TEXT NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $create_table_query );
}

/**
 * Check if the database table exists and create it if it doesn't
 * This is a helper function for troubleshooting
 */
function certify_certificate_ensure_table_exists() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'certify_certificate_management';
      // Check if table exists
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Required for table existence check during installation
    $table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );    if ($table_exists != $table_name) {
        // Table doesn't exist, create it
        certify_certificate_certify_certificate_onActivation();
    }
}