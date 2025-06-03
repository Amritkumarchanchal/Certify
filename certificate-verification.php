<?php
/**
 * @package Certify
 * @version 1.0
 */
/**
 * Plugin Name: Certify – Certificate Management & Verification
 * Plugin URI: https://wordpress.org/plugins/certify-certificate-management-verification/
 * Description: A comprehensive certificate management and verification system for WordPress. Create, manage, and verify certificates with ease.
 * Version: 1.0.0
 * Author: WordPress.org
 * Author URI: https://wordpress.org/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 7.0
 * Requires at least: 4.0
 * Tested up to: 6.8
 * Text Domain: certify
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

// Define plugin constants
define('CERTIFY_VERSION', '1.0.0');
define('CERTIFY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CERTIFY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CERTIFY_PREFIX', 'certify_cert_');

//COURSEPREFIX

function certify_cert_plugin_styles_scripts() {
    wp_register_style('certify-datatable-css', plugin_dir_url(__FILE__).'assets/css/jquery.dataTables.css');
    wp_enqueue_style('certify-datatable-css');
    wp_register_script('certify-datatable-js', plugin_dir_url(__FILE__).'assets/js/jquery.dataTables.js', array('jquery'), '1.10.19', true);
    wp_enqueue_script('certify-datatable-js');
}
add_action('admin_enqueue_scripts', 'certify_cert_plugin_styles_scripts');

function certify_cert_include_bs_datatables() {
	wp_enqueue_script('jquery');
    wp_enqueue_style('certify-datepicker-css', plugin_dir_url(__FILE__).'assets/css/jquery-ui.css');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('certify-bootstrap-js', plugin_dir_url(__FILE__).'assets/js/bootstrap.min.js', array('jquery'), '4.4.1', true);
    wp_enqueue_style('certify-bootstrap-css', plugin_dir_url(__FILE__).'assets/css/bootstrap.min.css', array(), '4.4.1');
}
if( isset($_GET['page']) && $_GET['page'] == 'certificate-codes' ){
	add_action('admin_enqueue_scripts', 'certify_cert_include_bs_datatables');
}

function course_certificate_include_bootsrap(){ ?>
	<style type="text/css">
		.cf-search {
		    width: 700px;
		    margin: 50px auto !important;
		    background: #f7f8fd;
		    border: 3px solid #eceefb;
		    padding: 30px;
		    border-radius: 10px;
		}
		.cf-search form {
		    display: inline-flex;
    		width: 100%;
		}
		.cf-field {
			display: inline-block !important;
		    border: 1px solid #000 !important;
		    margin-bottom: 0px !important;
		    width: 90%;
		    padding-left: 16px;
		    height: 47px;
		}
		.cf-btn {
			display: inline-block;
			border: none;
		    height: 47px !important;
		    width: 200px;
		    background: #000 !important;
		    color: #fff !important;
	        min-height: 47px;
    		border-radius: 0 !important;
		}
		.success {
			color: #155724;
		    background-color: #d4edda;
		    position: relative;
		    padding: .75rem 1.25rem;
		    margin-bottom: 1rem;
		    border: 1px solid #c3e6cb;
		    border-radius: .25rem;
		}
		.danger {
		    color: #721c24;
		    background-color: #f8d7da;
	        position: relative;
		    padding: .75rem 1.25rem;
		    margin-bottom: 1rem;
		    border: 1px solid #f5c6cb;
		    border-radius: .25rem;
		}

		@media screen and ( max-width: 768px ){
			.cf-search{ width: 90%; }
		}
		@media screen and ( max-width: 480px ){
			.cf-search form { display: initial; }
			.cf-field, .cf-btn {
				display: block !important;
				width: 100%;
			}
		}
		.cf-result-card {
		    max-width: 500px;
		    margin: 30px auto 0 auto;
		    background: #fff;
		    border-radius: 12px;
		    box-shadow: 0 4px 24px rgba(67,93,125,0.10), 0 1.5px 4px rgba(67,93,125,0.08);
		    padding: 32px 28px 24px 28px;
		    font-family: "Varela Round", sans-serif;
		    font-size: 16px;
		    color: #222;
		}
		.cf-result-card .cf-row {
		    display: flex;
		    justify-content: flex-start;
		    align-items: center;
		    padding: 14px 0;
		    border-bottom: 1px solid #eceefb;
		}
		.cf-result-card .cf-row:last-child {
		    border-bottom: none;
		}
		.cf-result-card .cf-label {
		    flex: 0 0 180px;
		    font-weight: 600;
		    color: #435d7d;
		    text-align: left;
		    letter-spacing: 0.5px;
		}
		.cf-result-card .cf-value {
		    flex: 1;
		    color: #222;
		    text-align: left;
		    font-weight: 400;
		    padding-left: 48px; /* Start value from midpoint */
		    word-break: break-word;
		    position: relative;
		    display: flex;
		    align-items: center;
		}
		.cf-result-card .cf-tick {
		    display: inline-block;
		    width: 22px;
		    height: 22px;
		    margin-left: 10px;
		    vertical-align: middle;
		}
		.cf-result-card .cf-tick svg {
		    display: block;
		    width: 100%;
		    height: 100%;
		}
		.rs-heading {
			text-align: center;
		}
		.search-table {
		    border-spacing: 0 !important;
		    border-top: none !important;
		    border-right: none !important;
		    border-left: none !important;
	        min-width: 100%;
	        border-bottom: 1px solid #ddd;
		}
		.search-table thead {
			background-color: transparent;
		}
		.search-table thead tr th {
			background-color: #000 !important;
			color: #fff !important;
			text-transform: uppercase;
			text-align: center;
		    padding: 15px 0px;
		}
		.search-table tbody tr td {
			border-right: 1px solid #ddd;
			padding: 14px 10px;
			text-align: center;
		}
		.br-0 {
			border-right: none !important
		}
		body {
			overflow-x: hidden;
		}
		.btlr-10{ border-top-left-radius: 10px; }
		.btrr-10{ border-top-right-radius: 10px; }
		.bl-1{ border-left: 1px solid #ddd; }
	</style>
<?php }
add_action('wp_head', 'course_certificate_include_bootsrap');

if ( is_admin() ) {

	// Include dependencies
	require_once plugin_dir_path( __file__ ).'install.php';
	require_once plugin_dir_path( __file__ ).'uninstall.php';
	require_once plugin_dir_path( __file__ ).'inc/core-functions.php';
	require_once plugin_dir_path( __file__ ).'admin/admin-menu.php'; 
	require_once plugin_dir_path( __file__ ).'admin/settings-page.php';
}

// Plugin activation
function certify_cert_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'certify_course_certificates';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        certificate_code varchar(50) NOT NULL,
        student_name varchar(255) NOT NULL,
        course_name varchar(255) NOT NULL,
        course_hours varchar(50) NOT NULL,
        dob date NOT NULL,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY certificate_code (certificate_code)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Add version to options
    add_option('certify_db_version', CERTIFY_VERSION);
}
register_activation_hook(__FILE__, 'certify_cert_activate');

// Plugin deactivation
function certify_cert_deactivate() {
    // Cleanup if needed
}
register_deactivation_hook(__FILE__, 'certify_cert_deactivate');

// Plugin uninstall
function certify_cert_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'certify_course_certificates';
    
    // Drop the table
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    
    // Delete options
    delete_option('certify_db_version');
}
register_uninstall_hook(__FILE__, 'certify_cert_uninstall');

// Certificate search form shortcode
function certify_cert_certificate_search_form() {
    ob_start();

    // Check if form was submitted
    if (isset($_POST['certify_cert_search']) && isset($_POST['certify_cert_nonce'])) {
        // Verify nonce
        if (!wp_verify_nonce($_POST['certify_cert_nonce'], 'certify_cert_search')) {
            wp_die(__('Security check failed. Please try again.', 'certify'));
        }

        // Sanitize and validate input
        $code = isset($_POST['certificate_code']) ? sanitize_text_field($_POST['certificate_code']) : '';
        
        if (empty($code)) {
            echo '<div class="alert alert-danger">' . esc_html__('Please enter a certificate code.', 'certify') . '</div>';
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . 'certify_course_certificates';
            
            // Use prepared statement
            $certificate = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE certificate_code = %s",
                $code
            ));

            if ($certificate) {
                ?>
                <div class="cf-result-card">
                    <h3><?php echo esc_html__('Certificate Details', 'certify'); ?></h3>
                    <table class="table table-bordered">
                        <tr>
                            <th><?php echo esc_html__('Certificate No', 'certify'); ?></th>
                            <td><?php echo esc_html($certificate->certificate_code); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Candidate Name', 'certify'); ?></th>
                            <td><?php echo esc_html($certificate->student_name); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Course', 'certify'); ?></th>
                            <td><?php echo esc_html($certificate->course_name); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Hours Completed', 'certify'); ?></th>
                            <td><?php echo esc_html($certificate->course_hours); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Date of Completion', 'certify'); ?></th>
                            <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($certificate->dob))); ?></td>
                        </tr>
                    </table>
                </div>
                <?php
            } else {
                echo '<div class="alert alert-danger">' . esc_html__('Certificate not found. Please verify the certificate code.', 'certify') . '</div>';
            }
        }
    }

    // Display search form
    ?>
    <div class="cf-search">
        <form method="post" class="cf-form">
            <?php wp_nonce_field('certify_cert_search', 'certify_cert_nonce'); ?>
            <div class="form-group">
                <label for="certificate_code"><?php echo esc_html__('Enter Certificate Code', 'certify'); ?></label>
                <input type="text" class="form-control cf-field" id="certificate_code" name="certificate_code" 
                       value="<?php echo isset($_POST['certificate_code']) ? esc_attr($_POST['certificate_code']) : ''; ?>" 
                       required>
            </div>
            <button type="submit" name="certify_cert_search" class="btn btn-primary cf-btn">
                <?php echo esc_html__('Verify Certificate', 'certify'); ?>
            </button>
        </form>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('get_certificate_search_form', 'certify_cert_certificate_search_form');

// Enqueue admin scripts and styles
function certify_cert_admin_enqueue_scripts($hook) {
    // Only load on our plugin's settings page
    if ($hook !== 'toplevel_page_certify-certificate-management') {
        return;
    }

    // Enqueue jQuery UI
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

    // Enqueue Bootstrap
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css', array(), '4.6.0');
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js', array('jquery'), '4.6.0', true);

    // Enqueue Material Icons
    wp_enqueue_style('material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), null);

    // Enqueue our admin styles and scripts
    wp_enqueue_style('certify-admin', plugins_url('admin/css/admin.css', __FILE__), array(), CERTIFY_VERSION);
    wp_enqueue_script('certify-admin', plugins_url('admin/js/admin.js', __FILE__), array('jquery', 'jquery-ui-datepicker', 'bootstrap'), CERTIFY_VERSION, true);

    // Localize script
    wp_localize_script('certify-admin', 'certify_admin', array(
        'i18n' => array(
            'no_records_selected' => __('Please select at least one record to delete.', 'certify'),
            'confirm_delete' => __('Are you sure you want to delete the selected records?', 'certify'),
            'invalid_file_type' => __('Please upload a valid CSV file.', 'certify'),
            'file_too_large' => __('File size must be less than 2MB.', 'certify')
        )
    ));
}
add_action('admin_enqueue_scripts', 'certify_cert_admin_enqueue_scripts');

// Add admin menu
function certify_cert_admin_menu() {
    add_menu_page(
        __('Certificate Management', 'certify'),
        __('Certificates', 'certify'),
        'manage_options',
        'certify-certificate-management',
        'certify_cert_admin_certificate_ui',
        'dashicons-awards',
        30
    );
}
add_action('admin_menu', 'certify_cert_admin_menu');

// Enqueue frontend scripts and styles
function certify_cert_enqueue_scripts() {
    // Enqueue Bootstrap
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css', array(), '4.6.0');
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js', array('jquery'), '4.6.0', true);

    // Enqueue our frontend styles
    wp_enqueue_style('certify-search', plugins_url('assets/css/search.css', __FILE__), array('bootstrap'), CERTIFY_VERSION);
}
add_action('wp_enqueue_scripts', 'certify_cert_enqueue_scripts');