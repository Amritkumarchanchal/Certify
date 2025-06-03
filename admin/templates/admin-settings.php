<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php echo esc_html__('Manage Certificates', 'certify'); ?></h1>
    
    <?php if (!empty($error)) : ?>
        <?php echo wp_kses_post($error); ?>
    <?php endif; ?>

    <div class="notice notice-info">
        <p>
            <strong><?php echo esc_html__('Shortcode:', 'certify'); ?></strong> 
            <code>[get_certificate_search_form]</code> 
            <?php echo esc_html__('Copy and paste this shortcode on the page where you want the certificate search form to appear.', 'certify'); ?>
        </p>
    </div>

    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-6">
                    <h2><?php echo esc_html__('Manage Certificates', 'certify'); ?></h2>
                </div>
                <div class="col-sm-6">
                    <a href="#addCertificateModal" class="btn btn-success" data-toggle="modal">
                        <i class="material-icons">&#xE147;</i> 
                        <span><?php echo esc_html__('Add New Certificate', 'certify'); ?></span>
                    </a>
                    <a href="javascript:void(0);" class="btn btn-danger deleteMultiple" data-toggle="modal">
                        <i class="material-icons">&#xE15C;</i> 
                        <span><?php echo esc_html__('Delete', 'certify'); ?></span>
                    </a>
                    <a href="#bulkUploadModal" class="btn btn-primary" data-toggle="modal">
                        <i class="material-icons">&#xE2C6;</i> 
                        <span><?php echo esc_html__('Bulk Upload', 'certify'); ?></span>
                    </a>
                </div>
            </div>
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
                    <th><?php echo esc_html__('Candidate Name', 'certify'); ?></th>
                    <th><?php echo esc_html__('Course', 'certify'); ?></th>
                    <th><?php echo esc_html__('Hours Completed', 'certify'); ?></th>
                    <th><?php echo esc_html__('Certificate No', 'certify'); ?></th>
                    <th><?php echo esc_html__('Date of Completion', 'certify'); ?></th>
                    <th><?php echo esc_html__('Actions', 'certify'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($certificates)) : ?>
                    <?php foreach ($certificates as $certificate) : ?>
                        <tr>
                            <td>
                                <span class="custom-checkbox">
                                    <input type="checkbox" id="checkbox<?php echo esc_attr($certificate->id); ?>" 
                                           value="<?php echo esc_attr($certificate->id); ?>" class="checkedcert">
                                    <label for="checkbox<?php echo esc_attr($certificate->id); ?>"></label>
                                </span>
                            </td>
                            <td class="sname"><?php echo esc_html($certificate->student_name); ?></td>
                            <td class="cname"><?php echo esc_html($certificate->course_name); ?></td>
                            <td class="chour"><?php echo esc_html($certificate->course_hours); ?></td>
                            <td class="ccode"><?php echo esc_html($certificate->certificate_code); ?></td>
                            <td class="cadt" data-date="<?php echo esc_attr($certificate->dob); ?>">
                                <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($certificate->dob))); ?>
                            </td>
                            <td>
                                <a href="javascript:void(0);" class="edit editModal" data-id="<?php echo esc_attr($certificate->id); ?>">
                                    <i class="material-icons" data-toggle="tooltip" title="<?php echo esc_attr__('Edit', 'certify'); ?>">&#xE254;</i>
                                </a>
                                <a href="javascript:void(0);" class="delete deleteModal" data-id="<?php echo esc_attr($certificate->id); ?>">
                                    <i class="material-icons" data-toggle="tooltip" title="<?php echo esc_attr__('Delete', 'certify'); ?>">&#xE872;</i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <?php echo esc_html__('No certificates found.', 'certify'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1) : ?>
            <div class="clearfix">
                <div class="hint-text">
                    <?php
                    printf(
                        esc_html__('Showing %1$d to %2$d of %3$d entries', 'certify'),
                        $offset + 1,
                        min($offset + $items_per_page, $total_items),
                        $total_items
                    );
                    ?>
                </div>
                <ul class="pagination">
                    <?php
                    $page_links = paginate_links(array(
                        'base' => add_query_arg('pg', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page,
                        'type' => 'array'
                    ));

                    if ($page_links) {
                        foreach ($page_links as $link) {
                            echo '<li class="page-item">' . wp_kses_post($link) . '</li>';
                        }
                    }
                    ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Certificate Modal -->
<div id="addCertificateModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <?php wp_nonce_field('certify_cert_admin_ui', 'certify_cert_nonce'); ?>
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo esc_html__('Add Certificate', 'certify'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="std_name"><?php echo esc_html__('Candidate Name', 'certify'); ?></label>
                        <input type="text" id="std_name" class="form-control" required name="std_name">
                    </div>
                    <div class="form-group">
                        <label for="course_name"><?php echo esc_html__('Course Name', 'certify'); ?></label>
                        <input type="text" id="course_name" class="form-control" required name="course_name">
                    </div>
                    <div class="form-group">
                        <label for="course_hours"><?php echo esc_html__('Hours Completed', 'certify'); ?></label>
                        <input type="text" id="course_hours" class="form-control" required name="course_hours">
                    </div>
                    <div class="form-group">
                        <label for="certificate_code"><?php echo esc_html__('Certificate No', 'certify'); ?></label>
                        <input type="text" id="certificate_code" class="form-control" required name="certificate_code" 
                               value="<?php echo esc_attr(substr(md5(uniqid()), 0, 7)); ?>">
                    </div>
                    <div class="form-group">
                        <label for="dob"><?php echo esc_html__('Date of Completion', 'certify'); ?></label>
                        <input type="text" id="dob" class="form-control datepicker" required readonly>
                        <input type="hidden" id="adob" name="dob">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="<?php echo esc_attr__('Cancel', 'certify'); ?>">
                    <input type="submit" class="btn btn-success" value="<?php echo esc_attr__('Add', 'certify'); ?>" name="add_certificate">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Certificate Modal -->
<div id="editCertificateModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <?php wp_nonce_field('certify_cert_admin_ui', 'certify_cert_nonce'); ?>
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo esc_html__('Edit Certificate', 'certify'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_std_name"><?php echo esc_html__('Candidate Name', 'certify'); ?></label>
                        <input type="text" id="edit_std_name" class="form-control" required name="std_name">
                    </div>
                    <div class="form-group">
                        <label for="edit_course_name"><?php echo esc_html__('Course Name', 'certify'); ?></label>
                        <input type="text" id="edit_course_name" class="form-control" required name="course_name">
                    </div>
                    <div class="form-group">
                        <label for="edit_course_hours"><?php echo esc_html__('Hours Completed', 'certify'); ?></label>
                        <input type="text" id="edit_course_hours" class="form-control" required name="course_hours">
                    </div>
                    <div class="form-group">
                        <label for="edit_certificate_code"><?php echo esc_html__('Certificate No', 'certify'); ?></label>
                        <input type="text" id="edit_certificate_code" class="form-control" required name="certificate_code">
                    </div>
                    <div class="form-group">
                        <label for="edit_dob"><?php echo esc_html__('Date of Completion', 'certify'); ?></label>
                        <input type="text" id="edit_dob" class="form-control datepicker" required readonly>
                        <input type="hidden" id="edit_adob" name="dob">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="editid" value="">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="<?php echo esc_attr__('Cancel', 'certify'); ?>">
                    <input type="submit" class="btn btn-success" value="<?php echo esc_attr__('Update', 'certify'); ?>" name="add_certificate">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Certificate Modal -->
<div id="deleteCertificateModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <?php wp_nonce_field('certify_cert_admin_ui', 'certify_cert_nonce'); ?>
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo esc_html__('Delete Certificate', 'certify'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p><?php echo esc_html__('Are you sure you want to delete these records?', 'certify'); ?></p>
                    <p class="text-warning"><small><?php echo esc_html__('This action cannot be undone.', 'certify'); ?></small></p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="editid" value="">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="<?php echo esc_attr__('Cancel', 'certify'); ?>">
                    <input type="submit" class="btn btn-danger" value="<?php echo esc_attr__('Delete', 'certify'); ?>" name="add_certificate">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div id="bulkUploadModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('certify_cert_admin_ui', 'certify_cert_nonce'); ?>
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo esc_html__('Bulk Upload Certificates', 'certify'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bulk_certificate_csv"><?php echo esc_html__('Upload CSV File', 'certify'); ?></label>
                        <input type="file" id="bulk_certificate_csv" class="form-control" name="bulk_certificate_csv" accept=".csv" required>
                        <small class="form-text text-muted">
                            <?php echo esc_html__('CSV columns: certificate_code, student_name, course_name, course_hours, dob', 'certify'); ?>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="<?php echo esc_attr__('Cancel', 'certify'); ?>">
                    <input type="submit" class="btn btn-success" value="<?php echo esc_attr__('Upload', 'certify'); ?>" name="bulk_upload">
                </div>
            </form>
        </div>
    </div>
</div> 