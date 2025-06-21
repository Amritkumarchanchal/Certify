<?php // Plugin Functions
function course_certificate_add_course_certificate($code, $name, $course, $hours, $doc, $editid){
	global $wpdb;
    if( is_numeric($editid) && $editid != '' ) {
        $result = $wpdb->update('certify_course_certificates', array(
            'certificate_code' => $code,
            'student_name' => $name,
            'course_name'  => $course,
            'course_hours' => $hours,
            'dob' => $doc,
            ),
            array( 'id' => $editid )
        );
    } else {
        $result = $wpdb->insert('certify_course_certificates', array(
            'certificate_code' => $code,
            'student_name' => $name,
            'course_name'  => $course,
            'course_hours' => $hours,
            'dob' => $doc,
            )
        );
    }
    return $result;
}

function course_certificate_delete_course_certificate($editid) {
    global $wpdb;
    $result = false;
    if( is_numeric($editid) && $editid != '' ) {
        $result = $wpdb->delete('certify_course_certificates', array( 'id' => $editid ));
    }
    return $result;
}