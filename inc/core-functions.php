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
        // Use WordPress timezone-safe date conversion
        $timestamp = strtotime($doc);
        $doc = gmdate('Y-m-d', $timestamp);
    }
      // If editid is provided, update existing certificate
    if( is_numeric($editid) && $editid != '' ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table requires direct query
        $result = $wpdb->update($table_name, array(
            'certificate_code' => $code,
            'student_name' => $name,
            'course_name'  => $course,
            'course_hours' => $hours,
            'dob' => $doc,
            ),
            array( 'id' => $editid )        );
        // Return 1 for success, 0 for failure
        $success = ($result !== false) ? 1 : 0;
          // Clear cache when certificate is updated
        if ($success) {
            wp_cache_delete('certify_certificates_all', 'certify_plugin');
            // Also clear individual certificate cache
            wp_cache_delete('certify_certificate_' . md5($code), 'certify_plugin');
        }
        
        return $success;    } else {
        // Add new certificate to database
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table requires direct query
        $result = $wpdb->insert($table_name, array(
            'certificate_code' => $code,
            'student_name' => $name,
            'course_name'  => $course,
            'course_hours' => $hours,            'dob' => $doc,
            )
        );
        
        // Return 1 for success, 0 for failure
        $success = ($result !== false) ? 1 : 0;
          // Clear cache when new certificate is added
        if ($success) {
            wp_cache_delete('certify_certificates_all', 'certify_plugin');
            // Also clear individual certificate cache
            wp_cache_delete('certify_certificate_' . md5($code), 'certify_plugin');
        }
        
        return $success;
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
    $table_name = $wpdb->prefix . 'certify_certificate_management';    $result = 0;    // Only delete if we have a valid numeric ID
    if( is_numeric($editid) && $editid != '' ) {
        // Get certificate code before deletion for cache clearing
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table requires direct query, caching not needed for deletion        $certificate = $wpdb->get_row($wpdb->prepare("SELECT certificate_code FROM {$table_name} WHERE id = %d", $editid));
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table requires direct query
        $delete_result = $wpdb->delete($table_name, array( 'id' => $editid ));
        // Return 1 for success, 0 for failure
        $result = ($delete_result !== false) ? 1 : 0;
        
        // Clear cache when certificate is deleted
        if ($result && $certificate) {
            wp_cache_delete('certify_certificates_all', 'certify_plugin');
            wp_cache_delete('certify_certificate_' . md5($certificate->certificate_code), 'certify_plugin');
        }
    }
    return $result;
}

/**
 * Get all certificates from the database with caching
 * @return array Array of certificate objects
 */
function certify_certificate_get_all_certificates() {
    // Get certificates with caching for better performance
    $cache_key = 'certify_certificates_all';
    $certificates = wp_cache_get($cache_key, 'certify_plugin');
    
    if (false === $certificates) {        global $wpdb;
        $table_name = $wpdb->prefix . 'certify_certificate_management';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Custom table requires direct query, table name cannot be parameterized
        $certificates = $wpdb->get_results("SELECT * FROM {$table_name}");
        
        // Cache for 5 minutes (300 seconds)
        wp_cache_set($cache_key, $certificates, 'certify_plugin', 300);
    }
    
    return $certificates ? $certificates : array();
}