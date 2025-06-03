jQuery(document).ready(function($) {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Select all checkboxes
    $('#selectAll').click(function() {
        $('.checkedcert').prop('checked', this.checked);
    });

    // Handle individual checkbox changes
    $('.checkedcert').change(function() {
        if (!$(this).prop('checked')) {
            $('#selectAll').prop('checked', false);
        }
    });

    // Initialize datepicker
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: '-100:+0',
        beforeShow: function(input, inst) {
            // Ensure datepicker is above modal
            $('#ui-datepicker-div').css({
                'z-index': 999999
            });
        }
    });

    // Update hidden date field when datepicker changes
    $('.datepicker').on('change', function() {
        var dateId = $(this).attr('id');
        var hiddenId = dateId === 'dob' ? 'adob' : 'edit_adob';
        $('#' + hiddenId).val($(this).val());
    });

    // Handle edit button click
    $('.editModal').click(function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        // Populate edit form
        $('#edit_std_name').val(row.find('.sname').text());
        $('#edit_course_name').val(row.find('.cname').text());
        $('#edit_course_hours').val(row.find('.chour').text());
        $('#edit_certificate_code').val(row.find('.ccode').text());
        
        var date = row.find('.cadt').data('date');
        $('#edit_dob').val(date);
        $('#edit_adob').val(date);
        
        $('input[name="editid"]').val(id);
        
        // Show modal
        $('#editCertificateModal').modal('show');
    });

    // Handle delete button click
    $('.deleteModal').click(function() {
        var id = $(this).data('id');
        $('input[name="editid"]').val(id);
        $('#deleteCertificateModal').modal('show');
    });

    // Handle bulk delete
    $('.deleteMultiple').click(function() {
        var selectedIds = [];
        $('.checkedcert:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length === 0) {
            alert(certify_admin.i18n.no_records_selected);
            return;
        }
        
        if (confirm(certify_admin.i18n.confirm_delete)) {
            $('input[name="editid"]').val(selectedIds.join(','));
            $('#deleteCertificateModal').modal('show');
        }
    });

    // Handle form submissions
    $('form').on('submit', function() {
        var $form = $(this);
        var $submitBtn = $form.find('input[type="submit"]');
        
        // Disable submit button to prevent double submission
        $submitBtn.prop('disabled', true);
        
        // Re-enable after 2 seconds if form submission fails
        setTimeout(function() {
            $submitBtn.prop('disabled', false);
        }, 2000);
    });

    // Handle bulk upload
    $('#bulk_certificate_csv').on('change', function() {
        var file = this.files[0];
        if (file) {
            // Check file type
            if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
                alert(certify_admin.i18n.invalid_file_type);
                $(this).val('');
                return;
            }
            
            // Check file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert(certify_admin.i18n.file_too_large);
                $(this).val('');
                return;
            }
        }
    });

    // Handle modal close
    $('.modal').on('hidden.bs.modal', function() {
        // Reset form
        $(this).find('form')[0].reset();
        
        // Clear any error messages
        $(this).find('.alert').remove();
        
        // Reset submit button
        $(this).find('input[type="submit"]').prop('disabled', false);
    });

    // Add error message display function
    window.showError = function(message) {
        var alert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>');
        
        $('.modal-body').prepend(alert);
    };

    // Add success message display function
    window.showSuccess = function(message) {
        var alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>');
        
        $('.modal-body').prepend(alert);
    };
}); 