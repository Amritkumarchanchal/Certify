<?php
//Exit if file called directly
if (! defined( 'ABSPATH' )) {
	exit;
}

// display the plugin settings page
function course_certificate_admin_certificate_ui() {

	if ( ! current_user_can( 'manage_options' ) ) return;
	$error = "";	if( isset($_POST['add_certificate']) ) {
		if( ! isset( $_POST['course_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['course_nonce'] ) ), 'admin_certificate_ui' ) ) {
			echo wp_kses_post('<div class="alert alert-danger">Try Again Verification Failed!!</div>');
		} else if( isset($_POST['add_certificate']) && $_POST['add_certificate'] == "Delete" ) {
			$editid = sanitize_text_field($_POST['editid']);
			if (strpos($editid, ',') !== false) {
				$editid = explode(",", $editid);
				foreach( $editid as $edt ) {
					$result = course_certificate_delete_course_certificate( $edt );
				}
			} else {
				$result = course_certificate_delete_course_certificate($editid);
			}
			if( $result == 1 ) {
                $error = '<div class="alert alert-success hide-alert">Certificate deleted successfully!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            } else {
                $error = '<div class="alert alert-danger hide-alert">Error while deleting!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }		} else if( empty($_POST['certificate_code']) || empty($_POST['std_name']) || empty($_POST['course_name']) || empty($_POST['course_hours']) || empty($_POST['doc']) ) {
			$error = '<div class="alert alert-danger hide-alert">All fields are required!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
		} else {
			$code = sanitize_text_field($_POST['certificate_code']);
			$name = sanitize_text_field($_POST['std_name']);
			$course = sanitize_text_field($_POST['course_name']);
			$hours = sanitize_text_field($_POST['course_hours']);
			$doc = sanitize_text_field($_POST['doc']);
			$editid = isset($_POST['editid']) ? sanitize_text_field($_POST['editid']) : '';
			$result = course_certificate_add_course_certificate($code, $name, $course, $hours, $doc, $editid);
			if( $result == 1 ) {
				if( $editid != "" ) {
	                $error = '<div class="alert alert-success hide-alert">Certificate updated successfully!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
				} else {
	                $error = '<div class="alert alert-success hide-alert">Certificate added successfully!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
				}
            } else {
                $error = '<div class="alert alert-danger hide-alert">Submission failed!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
		}	} else if( isset($_POST['bulk_upload']) && isset($_FILES['bulk_certificate_csv']) && !empty($_FILES['bulk_certificate_csv']['tmp_name']) ) {
        if( ! isset( $_POST['course_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['course_nonce'] ) ), 'admin_certificate_ui' ) ) {
            echo wp_kses_post('<div class="alert alert-danger">Bulk Upload: Verification Failed!</div>');} else {
            $csvFile = $_FILES['bulk_certificate_csv']['tmp_name'];
            $handle = fopen($csvFile, 'r');
            if ($handle !== false) {
                $row = 0;
                $uploaded_count = 0;
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    // Skip header row if present
                    if ($row == 0 && (strtolower($data[0]) == 'certificate_code' || strtolower($data[0]) == 'student_name')) { 
                        $row++; 
                        continue; 
                    }                    // Ensure we have at least 5 columns: certificate_code, student_name, course_name, course_hours, date_of_completion
                    if (count($data) < 5) continue;
                      $code = sanitize_text_field($data[0]);
                    $name = sanitize_text_field($data[1]);
                    $course = sanitize_text_field($data[2]);
                    $hours = sanitize_text_field($data[3]);
                    $doc = sanitize_text_field($data[4]);
                      if (!empty($code) && !empty($name) && !empty($course) && !empty($hours)) {
                        course_certificate_add_course_certificate($code, $name, $course, $hours, $doc, '');
                        $uploaded_count++;
                    }
                    $row++;
                }
                fclose($handle);
                $error = '<div class="alert alert-success hide-alert">Bulk upload completed! ' . esc_html($uploaded_count) . ' certificates uploaded successfully.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            } else {
                $error = '<div class="alert alert-danger hide-alert">Bulk upload failed to read file!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }
    }	global $wpdb;
    $certificates = $wpdb->get_results( "SELECT * FROM certify_course_certificates");
    // Safely get 'pg' from $_GET with nonce verification for admin pagination
    $cpage = 1;
    if (isset($_GET['pg']) && is_numeric($_GET['pg']) && isset($_GET['pg_nonce']) && wp_verify_nonce($_GET['pg_nonce'], 'pagination_nonce')) {
        $cpage = intval($_GET['pg']);
    } elseif (isset($_GET['pg']) && is_numeric($_GET['pg']) && !isset($_GET['pg_nonce'])) {
        // Allow first page load without nonce for initial access
        $cpage = intval($_GET['pg']);
    }
    $cpage_offset = $cpage > 0 ? (($cpage-1)*10) : 0;
    $certificatesNew = array_slice($certificates, $cpage_offset, 10, true);
    ?>

	<div class="container">
	  <div class="table-wrapper">
	    <div class="table-title">
	      <div class="row">
	        <div class="col-sm-6">
	          <h2 style="color: white;">Manage <b>Certificates</b></h2>
	        </div>
	        <div class="col-sm-6">
	          <a href="#addEmployeeModal" class="btn btn-success" data-bs-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add New Certificate</span></a>
	          <a href="javascript:void(0);" class="btn btn-danger deleteMultiple" data-bs-toggle="modal"><i class="material-icons">&#xE15C;</i> <span>Delete</span></a>
	          <a href="#bulkUploadModal" class="btn btn-primary" data-bs-toggle="modal"><i class="material-icons">&#xE2C6;</i> <span>Bulk Upload</span></a>
	        </div>
	      </div>
	    </div>
	    <?php echo wp_kses_post($error);?>
		<div class="alert alert-info" role="alert">
		  	<strong>[certify]</strong> Copy and paste the shortcode on the page you want the search bar and result of the certificates to be.
		</div>
	    <table class="table table-striped table-hover">
	      <thead>
	        <tr>
	          <th>
	            <span class="custom-checkbox">
					<input type="checkbox" id="selectAll">
					<label for="selectAll"></label>
				</span>
	          </th>
	          <th>Candidate Name</th>
	          <th>Course</th>
	          <th>Hours Completed</th>
	          <th>Certificate No</th>
	          <th>Date of Completion</th>
	          <th>Actions</th>
	        </tr>
	      </thead>
	      <tbody>
	        <?php foreach ($certificatesNew as $value) { ?>
	    	 	<tr>
	    	 		<td>
			            <span class="custom-checkbox">							<input type="checkbox" id="checkbox<?php echo esc_attr($value->id);?>" value="<?php echo esc_attr($value->id);?>" class="checkedcert">
							<label for="checkbox<?php echo esc_attr($value->id);?>"></label>
						</span>
			        </td>	                <td class="sname"><?php echo esc_html($value->student_name); ?></td>
	                <td class="cname"><?php echo esc_html($value->course_name); ?></td>
	                <td class="chour"><?php echo esc_html($value->course_hours); ?></td>
	                <td class="ccode"><?php echo esc_html($value->certificate_code); ?></td>
	                <td class="cadt" date="<?php echo esc_attr($value->dob); ?>"><?php echo esc_html(date("d/M/Y", strtotime($value->dob))); ?></td>			        <td>
			        	<div class="actions">
			           		<a href="javascript:void();" class="edit editModal" data-id="<?php echo esc_attr($value->id);?>"><i class="material-icons" data-bs-toggle="tooltip" title="Edit">&#xE254;</i></a>
			           		<a href="javascript:void(0);" class="delete deleteModal" data-id="<?php echo esc_attr($value->id);?>"><i class="material-icons" data-bs-toggle="tooltip" title="Delete">&#xE872;</i></a>
			        	</div>
			        </td>
	            </tr>
	        <?php } ?>
	      </tbody>
	    </table>
	    <div class="clearfix">
	    	<?php if( count($certificates) > 0 ) { ?>
		      <div class="hint-text">Showing <b><?php echo esc_html(count($certificatesNew));?></b> out of <b><?php echo esc_html(count($certificates));?></b> entries</div>
		      <ul class="pagination">
		        <!--<li class="page-item disabled"><a href="#">Previous</a></li>-->
		        <?php		        $pages = ceil(count($certificates)/10);
		        $currentpage = isset($_GET['pg']) && is_numeric($_GET['pg']) ? intval($_GET['pg']) : 1;
		        for($i=1;$i<=$pages;$i++) { 
		            $page_url = wp_nonce_url(admin_url('admin.php?page=certificate-codes&pg='.$i), 'pagination_nonce', 'pg_nonce');
		            ?>
                <li class="page-item <?php echo esc_attr(($currentpage==$i) ? 'active' : '');?>"><a href="<?php echo esc_url($page_url);?>" class="page-link"><?php echo esc_html($i);?></a></li>
            <?php } ?>
		        <!--<li class="page-item"><a href="#" class="page-link">Next</a></li>-->
		      </ul>
	    	<?php } ?>
	    </div>
	  </div>
	</div>
	<!-- Edit Modal HTML -->
	<div id="addEmployeeModal" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
		<form class="mt-40" method="POST">
			<?php wp_nonce_field( 'admin_certificate_ui', 'course_nonce' );?>
	        <div class="modal-header">
	          <h4 class="modal-title">Add Certificate</h4>
	          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	        </div>
	        <div class="modal-body">
	          <div class="form-group">
	            <label>Candidate Name</label>
	            <input type="text" class="form-control" required name="std_name">
	          </div>
	          <div class="form-group">
				<label>Course Name</label>
				<input type="text" required class="form-control" name="course_name">
	          </div>
	          <div class="form-group">
				<label>Hours Completed</label>
				<input type="text" required class="form-control" name="course_hours">
	          </div>	          <div class="form-group">
				<label>Certification No</label>
				<input type="text" required class="form-control" value="<?php echo esc_attr(substr(md5(rand()), 0, 7)); ?>" name="certificate_code">
	          </div>			  <div class="form-group">
				<label>Date of Completion</label>
				<input type="text" id="doc" required class="form-control" readonly="readonly">
				<input type="hidden" id="adoc" name="doc">
			  </div>
	        </div>
	        <div class="modal-footer">
	          <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Cancel">
	          <input type="submit" class="btn btn-success" value="Add" name="add_certificate">
	        </div>
	      </form>
	    </div>
	  </div>
	</div>
	<!-- Edit Modal HTML -->
	<div id="editEmployeeModal" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
		<form class="mt-40" method="POST">
			<?php wp_nonce_field( 'admin_certificate_ui', 'course_nonce' );?>
	        <div class="modal-header">
	          <h4 class="modal-title">Edit Certificate</h4>
	          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	        </div>
	        <div class="modal-body">
	          <div class="form-group">
	            <label>Candidate Name</label>
	            <input type="text" class="form-control" required name="std_name">
	          </div>
	          <div class="form-group">
				<label>Course Name</label>
				<input type="text" required class="form-control" name="course_name">
	          </div>
	          <div class="form-group">
				<label>Hours Completed</label>
				<input type="text" required class="form-control" name="course_hours">
	          </div>	          <div class="form-group">
				<label>Certification No</label>
				<input type="text" required class="form-control" value="<?php echo esc_attr(substr(md5(rand()), 0, 7)); ?>" name="certificate_code">
	          </div>			  <div class="form-group">
				<label>Date of Completion</label>
				<input type="text" id="editdoc" required class="form-control" readonly="readonly">
				<input type="hidden" id="eeditdoc" name="doc">
			  </div>
	        </div>
	        <div class="modal-footer">
				<input type="hidden" name="editid" value="">
	        	<input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Cancel">
	        	<input type="submit" class="btn btn-success" value="Update" name="add_certificate">
	        </div>
	      </form>
	    </div>
	  </div>
	</div>
	<!-- Delete Modal HTML -->
	<div id="deleteEmployeeModal" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <form method="POST">
			<?php wp_nonce_field( 'admin_certificate_ui', 'course_nonce' );?>
	        <div class="modal-header">
	          <h4 class="modal-title">Delete Employee</h4>
	          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	        </div>
	        <div class="modal-body">
	          <p>Are you sure you want to delete these Records?</p>
	          <p class="text-warning"><small>This action cannot be undone.</small></p>
	        </div>
	        <div class="modal-footer">
	          <input type="hidden" name="editid" value="">
	          <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Cancel">
	          <input type="submit" class="btn btn-danger" value="Delete" name="add_certificate">
	        </div>
	      </form>
	    </div>
	  </div>
	</div>
	<!-- Bulk Upload Modal HTML -->
	<div id="bulkUploadModal" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <form class="mt-40" method="POST" enctype="multipart/form-data">
	        <?php wp_nonce_field( 'admin_certificate_ui', 'course_nonce' );?>
	        <div class="modal-header">
	          <h4 class="modal-title">Bulk Upload Certificates</h4>
	          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	        </div>        <div class="modal-body">
	          <div class="form-group">
	            <label>Upload CSV File</label>
	            <input type="file" class="form-control" name="bulk_certificate_csv" accept=".csv" required>            <small class="form-text text-muted">
            	<strong>CSV Format:</strong> certificate_code, student_name, course_name, course_hours, date_of_completion<br>
            	<strong>Example:</strong> ABC123, John Doe, Web Development, 40, 12/25/2023<br>
            	<em>Note: Date format should be MM/DD/YYYY (e.g., 12/25/2023).</em>
            </small>
	          </div>
	        </div>
	        <div class="modal-footer">
	          <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Cancel">
	          <input type="submit" class="btn btn-success" value="Upload" name="bulk_upload">
	        </div>
	      </form>
	    </div>
	  </div>
	</div>

	<?php
}

// Remove duplicate enqueues for material-icons, certify-admin-inline, and inline JS/CSS
// All admin assets are now enqueued from the main plugin file for consistency

