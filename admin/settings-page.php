<?php
// Exit if file called directly
if (!defined('ABSPATH')) {
	exit;
}

// Display the plugin settings page
function certify_cert_admin_certificate_ui() {
	// Check user capabilities
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'certify'));
	}

	$error = '';
	$nonce_action = 'certify_cert_admin_ui';
	$nonce_name = 'certify_cert_nonce';

	// Handle form submissions
	if (isset($_POST['add_certificate'])) {
		// Verify nonce
		if (!isset($_POST[$nonce_name]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_name])), $nonce_action)) {
			wp_die(__('Security check failed', 'certify'));
		}

		if ($_POST['add_certificate'] === 'Delete') {
			$editid = isset($_POST['editid']) ? sanitize_text_field(wp_unslash($_POST['editid'])) : '';
			
			if (strpos($editid, ',') !== false) {
				$ids = explode(',', $editid);
				$success = true;
				foreach ($ids as $id) {
					$result = certify_cert_delete_certificate($id);
					if ($result !== 1) {
						$success = false;
						break;
					}
				}
				$error = $success 
					? '<div class="alert alert-success hide-alert">' . esc_html__('Certificates deleted successfully!', 'certify') . '<button type="button" class="close" data-dismiss="alert">×</button></div>'
					: '<div class="alert alert-danger hide-alert">' . esc_html__('Error while deleting certificates!', 'certify') . '<button type="button" class="close" data-dismiss="alert">×</button></div>';
			} else {
				$result = certify_cert_delete_certificate($editid);
				$error = $result === 1
					? '<div class="alert alert-success hide-alert">' . esc_html__('Certificate deleted successfully!', 'certify') . '<button type="button" class="close" data-dismiss="alert">×</button></div>'
					: '<div class="alert alert-danger hide-alert">' . esc_html__('Error while deleting!', 'certify') . '<button type="button" class="close" data-dismiss="alert">×</button></div>';
			}
		} else {
			// Validate required fields
			$required_fields = array('certificate_code', 'std_name', 'course_name', 'course_hours', 'dob');
			$missing_fields = array();
			
			foreach ($required_fields as $field) {
				if (empty($_POST[$field])) {
					$missing_fields[] = $field;
				}
			}
			
			if (!empty($missing_fields)) {
				$error = '<div class="alert alert-danger hide-alert">' . 
					sprintf(
						esc_html__('Required fields missing: %s', 'certify'),
						esc_html(implode(', ', $missing_fields))
					) . 
					'<button type="button" class="close" data-dismiss="alert">×</button></div>';
			} else {
				// Sanitize and validate input
				$code = sanitize_text_field(wp_unslash($_POST['certificate_code']));
				$name = sanitize_text_field(wp_unslash($_POST['std_name']));
				$course = sanitize_text_field(wp_unslash($_POST['course_name']));
				$hours = sanitize_text_field(wp_unslash($_POST['course_hours']));
				$dob = sanitize_text_field(wp_unslash($_POST['dob']));
				$editid = isset($_POST['editid']) ? sanitize_text_field(wp_unslash($_POST['editid'])) : '';

				// Validate date format
				if (!strtotime($dob)) {
					$error = '<div class="alert alert-danger hide-alert">' . 
						esc_html__('Invalid date format!', 'certify') . 
						'<button type="button" class="close" data-dismiss="alert">×</button></div>';
				} else {
					$result = certify_cert_add_certificate($code, $name, $course, $hours, $dob, $editid);
					if ($result === 1) {
						$message = $editid 
							? esc_html__('Certificate updated successfully!', 'certify')
							: esc_html__('Certificate added successfully!', 'certify');
						$error = '<div class="alert alert-success hide-alert">' . $message . '<button type="button" class="close" data-dismiss="alert">×</button></div>';
					} else {
						$error = '<div class="alert alert-danger hide-alert">' . 
							esc_html__('Submission failed!', 'certify') . 
							'<button type="button" class="close" data-dismiss="alert">×</button></div>';
					}
				}
			}
		}
	} elseif (isset($_POST['bulk_upload']) && isset($_FILES['bulk_certificate_csv']) && !empty($_FILES['bulk_certificate_csv']['tmp_name'])) {
		// Verify nonce for bulk upload
		if (!isset($_POST[$nonce_name]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_name])), $nonce_action)) {
			wp_die(__('Security check failed', 'certify'));
		}

		// Validate file type
		$file_type = wp_check_filetype($_FILES['bulk_certificate_csv']['name']);
		if ($file_type['ext'] !== 'csv') {
			$error = '<div class="alert alert-danger hide-alert">' . 
				esc_html__('Invalid file type. Please upload a CSV file.', 'certify') . 
				'<button type="button" class="close" data-dismiss="alert">×</button></div>';
		} else {
			$csvFile = $_FILES['bulk_certificate_csv']['tmp_name'];
			$handle = fopen($csvFile, 'r');
			
			if ($handle !== false) {
				$row = 0;
				$success_count = 0;
				$error_count = 0;
				
				while (($data = fgetcsv($handle, 1000, ',')) !== false) {
					if ($row === 0 && strtolower($data[0]) === 'first name') {
						$row++;
						continue;
					}
					
					if (count($data) < 5) {
						$error_count++;
						continue;
					}
					
					$name = sanitize_text_field($data[0]);
					$course = sanitize_text_field($data[1]);
					$hours = sanitize_text_field($data[2]);
					$code = sanitize_text_field($data[3]);
					$dob = sanitize_text_field($data[4]);
					
					if (empty($name) || empty($course) || empty($hours) || empty($code) || empty($dob) || !strtotime($dob)) {
						$error_count++;
						continue;
					}
					
					$result = certify_cert_add_certificate($code, $name, $course, $hours, $dob, '');
					if ($result === 1) {
						$success_count++;
					} else {
						$error_count++;
					}
					$row++;
				}
				fclose($handle);
				
				$error = '<div class="alert alert-info hide-alert">' . 
					sprintf(
						esc_html__('Bulk upload completed. Successfully added %d certificates. Failed to add %d certificates.', 'certify'),
						$success_count,
						$error_count
					) . 
					'<button type="button" class="close" data-dismiss="alert">×</button></div>';
			} else {
				$error = '<div class="alert alert-danger hide-alert">' . 
					esc_html__('Failed to read CSV file!', 'certify') . 
					'<button type="button" class="close" data-dismiss="alert">×</button></div>';
			}
		}
	}

	// Get certificates with pagination
	global $wpdb;
	$table_name = $wpdb->prefix . 'certify_course_certificates';
	
	// Get total count
	$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
	$items_per_page = 10;
	$total_pages = ceil($total_items / $items_per_page);
	
	// Get current page
	$current_page = isset($_GET['pg']) ? max(1, intval($_GET['pg'])) : 1;
	$current_page = min($current_page, $total_pages);
	$offset = ($current_page - 1) * $items_per_page;
	
	// Get certificates for current page
	$certificates = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM $table_name ORDER BY id DESC LIMIT %d OFFSET %d",
			$items_per_page,
			$offset
		)
	);

	// Enqueue required styles and scripts
	wp_enqueue_style('certify-admin-css', plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin.css');
	wp_enqueue_script('certify-admin-js', plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin.js', array('jquery'), '1.0', true);
	
	// Include the admin template
	include plugin_dir_path(__FILE__) . 'templates/admin-settings.php';
}

