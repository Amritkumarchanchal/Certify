<?php
/**
 * Core database functions for certificate management
 * These functions handle adding, updating, and deleting certificates
 */

/**
 * Add or update a certificate in the database
 * @param string $code Certificate code
 * @param string $name Student name
 * @param string $course Course name
 * @param string $hours Hours completed
 * @param string $doc Date of completion
 * @param string $editid ID for editing (empty for new certificates)
 * @return int 1 for success, 0 for failure
 */
function certify_certificate_add_course_certificate($code, $name, $course, $hours, $doc, $editid){
	global $wpdb;
    
    // Define table name with WordPress prefix
    $table_name = $wpdb->prefix . 'certify_certificate_management';
    
    // Convert date format if needed (from mm/dd/yyyy to proper format)
    if (!empty($doc)) {
        $doc = date('Y-m-d', strtotime($doc));
    }
    
    // If editid is provided, update existing certificate
    if( is_numeric($editid) && $editid != '' ) {
        $result = $wpdb->update($table_name, array(
            'certificate_code' => $code,
            'student_name' => $name,
            'course_name'  => $course,
            'course_hours' => $hours,
            'dob' => $doc,
            ),
            array( 'id' => $editid )
        );
        // Return 1 for success, 0 for failure
        return ($result !== false) ? 1 : 0;
    } else {
        // Add new certificate to database
        $result = $wpdb->insert($table_name, array(
            'certificate_code' => $code,
            'student_name' => $name,
            'course_name'  => $course,
            'course_hours' => $hours,
            'dob' => $doc,
            )
        );
        
        // Debug: Log any database errors
        if ($wpdb->last_error) {
            error_log('Certify Plugin DB Error: ' . $wpdb->last_error);
        }
        
        // Return 1 for success, 0 for failure
        return ($result !== false) ? 1 : 0;
    }
}

/**
 * Delete a certificate from the database
 * @param string $editid Certificate ID to delete
 * @return bool True if successful, false if failed
 */
function certify_certificate_delete_course_certificate($editid) {
    global $wpdb;
    
    // Define table name with WordPress prefix
    $table_name = $wpdb->prefix . 'certify_certificate_management';
    
    $result = 0;
    // Only delete if we have a valid numeric ID
    if( is_numeric($editid) && $editid != '' ) {
        $delete_result = $wpdb->delete($table_name, array( 'id' => $editid ));
        // Return 1 for success, 0 for failure
        $result = ($delete_result !== false) ? 1 : 0;
    }
    return $result;
}