<?php
/**
 * @package Certify
 * @version 1.0
 */
/**
 * Plugin Name: Certify
 * Plugin URI: https://amritkumarchanchal.com/Certify/
 * Description: Admin can enter course certificate codes , and details in the panel and user can verify their certificate using the cource code in the front end.
 * Version: 1.0
 * Author: Amrit Kumar Chanchal
 * Author URI: https://amritkumarchanchal.com/
 */

if (! defined( 'ABSPATH' )) {
	exit;
}

//COURSEPREFIX

function course_certificate_plugin_styles_scripts() {
    wp_register_style('dataTable-css', plugin_dir_url(__FILE__).'assets/css/jquery.dataTables.css');
    wp_enqueue_style('dataTable-css');
    wp_register_script( 'dataTable-js', plugin_dir_url(__FILE__).'assets/js/jquery.dataTables.js');
    wp_enqueue_script('dataTable-js');
}
add_action('admin_enqueue_scripts', 'course_certificate_plugin_styles_scripts');

function course_certificate_include_bs_datatables() {
	wp_enqueue_script('jquery');
    wp_enqueue_style( 'datepicker-css', plugin_dir_url(__FILE__).'assets/css/jquery-ui.css' );
    //wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script( 'jquery-ui-datepicker' );//, plugin_dir_url(__FILE__).'assets/js/datepicker.js' );
	wp_enqueue_script( 'admin-bs', plugin_dir_url(__FILE__).'assets/js/bootstrap.min.js' );
    wp_enqueue_style( 'admin-css', plugin_dir_url(__FILE__).'assets/css/bootstrap.min.css' );
}
if( isset($_GET['page']) && $_GET['page'] == 'certificate-codes' ){
	add_action('admin_enqueue_scripts', 'course_certificate_include_bs_datatables');
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

register_activation_hook( __FILE__, 'course_certificate_certify_certificate_onActivation' );
register_deactivation_hook( __FILE__, 'course_certificate_certify_certificate_onDeactivation' );

// Search certificate
function course_certificate_certificate_search_form(){ 
	$output = '';
	$output .= '<style type="text/css">
		.cf-btn:hover {
			background: #000 !important;
		    color: #fff !important;
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
		<div class="cf-search">
		<form method="POST">
			<input type="text" required class="cf-field" placeholder="Enter Certificate Code" name="certificate_code">
			<input type="submit" class="cf-btn" value="Search" name="code_data">
		</form>
	</div>
	<div class="container">';
	if( isset($_POST['code_data']) ){
		$code = sanitize_text_field($_POST['certificate_code']);
		global $wpdb;
		$rows = $wpdb->get_results( "SELECT * FROM certify_course_certificates where certificate_code = '$code'"); 
		if( !empty($rows) ){
        foreach ( $rows as $data ){
            $tick = '<span class="cf-tick" title="Verified"><svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="10" fill="#27ae60"/><path d="M6 10.5L9 13.5L14 7.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
            $output .= '<h1 class="rs-heading">Search Result</h1></div>';
            $output .= '<div class="cf-result-card">
                <div class="cf-row"><div class="cf-label">Candidate Name</div><div class="cf-value">'.$data->student_name.' '.$tick.'</div></div>
                <div class="cf-row"><div class="cf-label">Course</div><div class="cf-value">'.$data->course_name.'</div></div>
                <div class="cf-row"><div class="cf-label">Hours Completed</div><div class="cf-value">'.$data->course_hours.'</div></div>
                <div class="cf-row"><div class="cf-label">Certification No</div><div class="cf-value">'.$data->certificate_code.'</div></div>
                <div class="cf-row"><div class="cf-label">Date of Completion</div><div class="cf-value">'.date("d/M/Y", strtotime($data->dob)).'</div></div>
            </div>';
        }
    }else{
        echo '<div class="danger">No result found against this code <strong>'.$code.'</strong></div>';
    }
}
	$output .= '</div>';
	return $output;
}
add_shortcode( 'get_certificate_search_form' , 'course_certificate_certificate_search_form' );