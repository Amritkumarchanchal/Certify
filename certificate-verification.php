<?php
/**
 * @package Certify
 * @version 1.0
 */
/**
 * Plugin Name: Certify – Certificate Management & Verification
 * Plugin URI: https://certify.amritkumarchanchal.me/
 * Description: Admin can enter course certificate codes, and details in the panel and user can verify their certificate using the course code in the front end.
 * Version: 1.0
 * Author: Amrit Kumar Chanchal
 * Author URI: https://www.linkedin.com/in/amritkumarchanchal/
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: certify
 * Domain Path: /languages
 * Network: false
 * Update URI: false
 */

if (! defined( 'ABSPATH' )) {
	exit;
}

//COURSEPREFIX

function course_certificate_admin_assets() {
    wp_enqueue_script('jquery');
    // Enqueue jQuery UI CSS and JS
    wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
    wp_enqueue_script('jquery-ui-js', 'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', array('jquery'), null, true);
    // Enqueue Bootstrap 5
    wp_enqueue_script('admin-bs', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    wp_enqueue_style('admin-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    // DataTables for Bootstrap 5
    wp_enqueue_style('dataTable-css', 'https://cdn.datatables.net/1.13.10/css/dataTables.bootstrap5.min.css');
    wp_enqueue_script('dataTable-js', 'https://cdn.datatables.net/1.13.10/js/dataTables.bootstrap5.min.js', array('jquery'), null, true);
    // Material Icons
    wp_enqueue_style('material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), null);
    
    // Add admin CSS inline
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
        .modal .form-control { border-radius: 2px; box-shadow: none; border-color: #dddddd; }        .modal .btn { border-radius: 2px; min-width: 100px; }
        @media (max-width: 768px) {
            .table-title .col-sm-6:last-child { text-align: center; margin-top: 15px; }
            .table-title .btn { margin: 5px; }
        }
    ";
    wp_add_inline_style('admin-css', $admin_css);
    
    // Add admin JS inline
    $admin_js = "
        jQuery(document).ready(function($) {
            // Initialize DataTable
            if ($('#certificates-table').length) {
                $('#certificates-table').DataTable();
            }
              // Initialize datepickers
            if ($('#doc').length) {
                $('#doc').datepicker({
                    dateFormat: 'dd/M/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '1940:' + new Date().getFullYear(),
                    altField: '#adoc',
                    altFormat: 'mm/dd/yy'
                });
            }
            
            if ($('#editdoc').length) {
                $('#editdoc').datepicker({
                    dateFormat: 'dd/M/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '1940:' + new Date().getFullYear(),
                    altField: '#eeditdoc',
                    altFormat: 'mm/dd/yy'
                });
            }
            
            // Edit modal handler
            $(document).on('click', '.editModal', function() {
                var id = $(this).data('id');
                var sname = $('.sname', $(this).closest('tr')).html();
                var cname = $('.cname', $(this).closest('tr')).html();
                var ccode = $('.ccode', $(this).closest('tr')).html();
                var chour = $('.chour', $(this).closest('tr')).html();
                var cadt = $('.cadt', $(this).closest('tr')).html();
                var ocadt = $('.cadt', $(this).closest('tr')).attr('date');
                
                $('#editEmployeeModal input[name=editid]').val(id);
                $('#editEmployeeModal input[name=std_name]').val(sname);
                $('#editEmployeeModal input[name=course_name]').val(cname);                $('#editEmployeeModal input[name=course_hours]').val(chour);
                $('#editEmployeeModal input[name=certificate_code]').val(ccode);
                $('#editEmployeeModal input[name=doc]').val(ocadt);
                $('#editEmployeeModal #editdoc').val(cadt);
                
                var editModal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
                editModal.show();
            });
            
            // Delete modal handler
            $(document).on('click', '.deleteModal', function() {
                var id = $(this).data('id');
                $('#deleteEmployeeModal input[name=editid]').val(id);
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteEmployeeModal'));
                deleteModal.show();
            });
            
            // Delete multiple handler
            $(document).on('click', '.deleteMultiple', function() {
                var allIds = [];
                $('.checkedcert:checkbox:checked').each(function() {
                    allIds.push($(this).val());
                });
                $('#deleteEmployeeModal input[name=editid]').val(allIds.join(','));
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteEmployeeModal'));
                deleteModal.show();
            });
            
            // Select all checkbox handler
            var checkbox = $('table tbody input[type=\"checkbox\"]');
            $('#selectAll').click(function() {
                if (this.checked) {
                    checkbox.each(function() {
                        this.checked = true;
                    });
                } else {
                    checkbox.each(function() {
                        this.checked = false;
                    });
                }
            });
            
            checkbox.click(function() {
                if (!this.checked) {
                    $('#selectAll').prop('checked', false);
                }
            });
            
            // Auto-hide alerts
            setTimeout(function() {
                $('.hide-alert').remove();
            }, 5000);
        });
    ";
    wp_add_inline_script('admin-bs', $admin_js);
}
add_action('admin_enqueue_scripts', 'course_certificate_admin_assets');


// Enqueue frontend CSS using WordPress best practices
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('certify-frontend', plugin_dir_url(__FILE__).'assets/css/certify-frontend.css');
});

// Search certificate
function course_certificate_search_form(){ 
	$output = '';	$output .= '<div class="cf-search">
		<form method="POST">
			' . wp_nonce_field('search_certificate', 'search_nonce', true, false) . '
			<input type="text" required class="cf-field" placeholder="Enter Certificate Code" name="certificate_code">
			<input type="submit" class="cf-btn" value="Search" name="code_data">
		</form>
	</div>
	<div class="container">';	if( isset($_POST['code_data']) && isset($_POST['search_nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['search_nonce'] ) ), 'search_certificate' ) ){
		$code = sanitize_text_field($_POST['certificate_code']);
		
		// Validate certificate code format (basic validation)
		if (empty($code) || strlen($code) > 50) {
			$output .= '<div class="danger">Invalid certificate code format.</div>';
		} else {
			global $wpdb;
			// Use prepared statement to prevent SQL injection
			$rows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM certify_course_certificates WHERE certificate_code = %s", $code) ); 
			
			if( !empty($rows) ){				foreach ( $rows as $data ){
					$tick = '<span class="cf-tick" title="Verified"><svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="10" fill="#27ae60"/><path d="M6 10.5L9 13.5L14 7.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
					$output .= '<h1 class="rs-heading">Search Result</h1></div>';
					$output .= '<div class="cf-result-card">
						<div class="cf-row"><div class="cf-label">Candidate Name</div><div class="cf-value">'.esc_html($data->student_name).' '.$tick.'</div></div>
						<div class="cf-row"><div class="cf-label">Course</div><div class="cf-value">'.esc_html($data->course_name).'</div></div>
						<div class="cf-row"><div class="cf-label">Hours Completed</div><div class="cf-value">'.esc_html($data->course_hours).'</div></div>
						<div class="cf-row"><div class="cf-label">Certification No</div><div class="cf-value">'.esc_html($data->certificate_code).'</div></div>
						<div class="cf-row"><div class="cf-label">Date of Completion</div><div class="cf-value">'.esc_html(date("d/M/Y", strtotime($data->dob))).'</div></div>
					</div>';
				}} else {
				$output .= '<div class="danger">No result found against this code <strong>'.esc_html($code).'</strong></div>';
			}
		}
	}
	$output .= '</div>';
	return $output;
}
add_shortcode( 'certify', 'course_certificate_search_form' );

// Load admin menu and settings page
require_once plugin_dir_path(__FILE__) . 'admin/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';

// Load core functions
require_once plugin_dir_path(__FILE__) . 'inc/core-functions.php';

// Add activation hook to create database table
register_activation_hook(__FILE__, 'course_certificate_certify_certificate_onActivation');
require_once plugin_dir_path(__FILE__) . 'install.php';