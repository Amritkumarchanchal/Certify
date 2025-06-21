<?php
/**
 * @package Certify_Certificate_Management
 * @version 1.0
 */
/**
 * Plugin Name: Certify – Certificate Management & Verification
 * Plugin URI: https://certify.amritkumarchanchal.me/
 * Description: Admin can enter course certificate codes, and details in the panel and user can verify their certificate using the course code in the front end.
 * Version: 1.0
 * Author: Amrit Kumar Chanchal
 * Author URI: https://www.linkedin.com/in/amritkumarchanchal/ 
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (! defined( 'ABSPATH' )) {
	exit;
}

// Plugin constants - these help identify and configure the plugin
if (!defined('CERTIFY_PLUGIN_VERSION')) {
    define('CERTIFY_PLUGIN_VERSION', '1.0');
}
if (!defined('CERTIFY_PLUGIN_PATH')) {
    define('CERTIFY_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

//CERTIFYPREFIX - All our functions start with certify_certificate_ to avoid conflicts

/**
 * Load admin assets (CSS, JS) for the certificate management interface
 * This function handles all the styling and interactive features in the admin panel
 */
function certify_certificate_admin_assets() {    wp_enqueue_script('jquery');
    // Load WordPress core jQuery UI for date picker functionality
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('wp-jquery-ui-dialog');    // Load Bootstrap 5 for modern UI components and styling
    wp_enqueue_script('certify-admin-bs', plugin_dir_url(__FILE__) . 'assets/js/bootstrap.bundle.min.js', array('jquery'), CERTIFY_PLUGIN_VERSION, true);
    wp_enqueue_style('certify-admin-css', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css', array(), CERTIFY_PLUGIN_VERSION);    // Load custom CSS for both frontend and admin styles (includes datepicker)
    wp_enqueue_style('certify-frontend', plugin_dir_url(__FILE__) . 'assets/css/certify-frontend.css', array(), CERTIFY_PLUGIN_VERSION);    // DataTables for sortable, searchable certificate tables
    wp_enqueue_style('dataTable-css', plugin_dir_url(__FILE__) . 'assets/css/dataTables.bootstrap5.min.css', array(), CERTIFY_PLUGIN_VERSION);
    wp_enqueue_script('dataTable-core-js', plugin_dir_url(__FILE__) . 'assets/js/jquery.dataTables.min.js', array('jquery'), CERTIFY_PLUGIN_VERSION, true);
    wp_enqueue_script('dataTable-js', plugin_dir_url(__FILE__) . 'assets/js/dataTables.bootstrap5.min.js', array('jquery', 'dataTable-core-js'), CERTIFY_PLUGIN_VERSION, true);    // Material Icons for consistent iconography
    wp_enqueue_style('material-icons', plugin_dir_url(__FILE__) . 'assets/css/material-icons.css', array(), CERTIFY_PLUGIN_VERSION);
    // Load custom admin JavaScript
    wp_enqueue_script('certify-admin-js', plugin_dir_url(__FILE__) . 'assets/js/certify-admin.js', array('jquery', 'jquery-ui-datepicker', 'dataTable-js'), CERTIFY_PLUGIN_VERSION, true);
    
    // Custom admin styling - makes the interface look professional and user-friendly
    $admin_css = "
        body { margin-top: 32px !important; }
        .table-wrapper { background: #fff; padding: 20px 25px; margin: 30px 0; border-radius: 3px; box-shadow: 0 1px 1px rgba(0,0,0,.05); }
        .table-title { padding: 32px 30px 16px 30px; background: #435d7d; color: #fff; margin: -20px -25px 10px; border-radius: 3px 3px 0 0; }        .table-title h2 { margin: 5px 0 0; font-size: 24px; }
        .table-title .col-sm-6:last-child { text-align: right; }        .table-title .btn { color: #fff; font-size: 15px; border: none; min-width: 50px; border-radius: 6px; outline: none !important; margin-left: 10px; padding: 10px 22px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .table-title .btn-success { background: #27ae60 !important; }
        .table-title .btn-danger { background: #e74c3c !important; }
        .table-title .btn-primary { background: #007bff !important; }
        .table-title .btn i { font-size: 18px; margin-right: 8px; vertical-align: middle; display: inline-flex; align-items: center; }
        .table-title .btn span { display: inline-flex; align-items: center; }        .table.table tr th, table.table tr td { border-color: #e9e9e9; padding: 12px 15px; vertical-align: middle; text-align: center; }
        .table.table tr th:first-child { width: 60px; }
        .table.table tr th:last-child { width: 100px; }
        .table.table tr td:last-child { text-align: center; }
        .table.table tr td:last-child .actions { display: inline-flex; align-items: center; justify-content: center; gap: 5px; }
        .table.table-striped tbody tr:nth-of-type(odd) { background-color: #fcfcfc; }
        .table.table-striped.table-hover tbody tr:hover { background: #f5f5f5; }        .table.table td a { font-weight: bold; color: #566787; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; outline: none !important; padding: 5px; margin: 0 2px; border-radius: 3px; min-width: 32px; min-height: 32px; }
        .table.table td a:hover { color: #2196F3; }
        .table.table td a.edit { color: #FFC107; }
        .table.table td a.delete { color: #F44336; }
        .table.table td a i { font-size: 18px; margin: 0; display: flex; align-items: center; justify-content: center; }
        .custom-checkbox { position: relative; display: flex; justify-content: center; align-items: center; height: 20px; }
        .custom-checkbox input[type='checkbox'] { opacity: 0; position: absolute; z-index: 9; width: 18px; height: 18px; margin: 0; }
        .custom-checkbox label { margin: 0; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .custom-checkbox label:before { content: ''; display: inline-block; background: white; border: 1px solid #bbb; border-radius: 2px; box-sizing: border-box; z-index: 2; width: 18px; height: 18px; margin: 0; }
        .custom-checkbox input[type='checkbox']:checked + label:after { content: ''; position: absolute; left: 50%; top: 50%; width: 6px; height: 11px; border: solid #fff; border-width: 0 3px 3px 0; transform: translate(-50%, -50%) rotateZ(45deg); z-index: 3; }
        .custom-checkbox input[type='checkbox']:checked + label:before { border-color: #03A9F4; background: #03A9F4; }
        .alert { position: relative; }
        .alert .btn-close { position: absolute; top: 50%; right: 15px; transform: translateY(-50%); background: none; border: none; font-size: 1.5rem; line-height: 1; color: inherit; opacity: 0.5; cursor: pointer; }
        .alert .btn-close:hover { opacity: 1; }
        .modal .modal-dialog { max-width: 400px; }        .modal .modal-header, .modal .modal-body, .modal .modal-footer { padding: 20px 30px; }
        .modal .modal-content { border-radius: 3px; }
        .modal .modal-footer { background: #ecf0f1; border-radius: 0 0 3px 3px; }
        .modal .form-control { border-radius: 2px; box-shadow: none; border-color: #dddddd; }        .modal .btn { border-radius: 2px; min-width: 100px; }        @media (max-width: 768px) {
            .table-title .col-sm-6:last-child { text-align: center; margin-top: 15px; }
            .table-title .btn { margin: 5px; }
        }    ";    wp_add_inline_style('certify-admin-css', $admin_css);
}
// Hook the admin assets function to load only in admin area
add_action('admin_enqueue_scripts', 'certify_certificate_admin_assets');


// Load frontend styles for the certificate search form
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('certify-frontend', plugin_dir_url(__FILE__).'assets/css/certify-frontend.css', array(), CERTIFY_PLUGIN_VERSION);
});

/**
 * Generate the certificate search form and handle search results
 * This creates the shortcode [certify] that users can place on any page
 */
function certify_certificate_search_form(){ 
	$output = '';	$output .= '<div class="cf-search">
		<form method="POST">
			' . wp_nonce_field('search_certificate', 'search_nonce', true, false) . '
			<input type="text" required class="cf-field" placeholder="Enter Certificate Code" name="certificate_code">
			<input type="submit" class="cf-btn" value="Search" name="code_data">
		</form>
	</div>
	<div class="container">';	if( isset($_POST['code_data']) && isset($_POST['search_nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['search_nonce'] ) ), 'search_certificate' ) ){
		// Validate that certificate_code exists in POST data
		if ( ! isset($_POST['certificate_code']) ) {
			$output .= '<div class="danger">Certificate code is required.</div>';
		} else {
			$code = sanitize_text_field( wp_unslash( $_POST['certificate_code'] ) );
		
		// Validate certificate code format (basic validation)
		if (empty($code) || strlen($code) > 50) {
			$output .= '<div class="danger">Invalid certificate code format.</div>';		} else {
			// Try to get certificate from cache first
			$cache_key = 'certify_certificate_' . md5($code);
			$rows = wp_cache_get($cache_key, 'certify_plugin');
					if (false === $rows) {
				global $wpdb;				// Use prepared statement to prevent SQL injection attacks
				$table_name = $wpdb->prefix . 'certify_certificate_management';
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Table name cannot be parameterized, custom table requires direct query
				$rows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$table_name} WHERE certificate_code = %s", $code) );
				
				// Cache the result for 10 minutes (600 seconds)
				wp_cache_set($cache_key, $rows, 'certify_plugin', 600);
			}
					if( !empty($rows) ){
				// Certificate found - display the results with verification checkmark
				foreach ( $rows as $data ){
					$tick = '<span class="cf-tick" title="Verified"><svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="10" fill="#27ae60"/><path d="M6 10.5L9 13.5L14 7.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
					$output .= '<h1 class="rs-heading">Search Result</h1></div>';
					$output .= '<div class="cf-result-card">
						<div class="cf-row"><div class="cf-label">Candidate Name</div><div class="cf-value">'.esc_html($data->student_name).' '.$tick.'</div></div>
						<div class="cf-row"><div class="cf-label">Course</div><div class="cf-value">'.esc_html($data->course_name).'</div></div>
						<div class="cf-row"><div class="cf-label">Hours Completed</div><div class="cf-value">'.esc_html($data->course_hours).'</div></div>
						<div class="cf-row"><div class="cf-label">Certification No</div><div class="cf-value">'.esc_html($data->certificate_code).'</div></div>
						<div class="cf-row"><div class="cf-label">Date of Completion</div><div class="cf-value date-field">'.esc_html(gmdate("d/M/Y", strtotime($data->dob))).'</div></div>					</div>';
				}			} else {
				$output .= '<div class="danger">No result found against this code <strong>'.esc_html($code).'</strong></div>';
			}
		}
		}
	}
	$output .= '</div>';
	return $output;
}
// Register the shortcode so users can use [certify] on their pages
add_shortcode( 'certify', 'certify_certificate_search_form' );

// Load the admin menu and settings pages
require_once plugin_dir_path(__FILE__) . 'admin/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';

// Load core database functions for certificate management
require_once plugin_dir_path(__FILE__) . 'inc/core-functions.php';

// Set up database table when plugin is activated
register_activation_hook(__FILE__, 'certify_certificate_certify_certificate_onActivation');
require_once plugin_dir_path(__FILE__) . 'install.php';

// Ensure table exists on every admin load (for troubleshooting)
if (is_admin()) {
    add_action('admin_init', function() {
        require_once plugin_dir_path(__FILE__) . 'install.php';
        certify_certificate_ensure_table_exists();
    });
}