/**
 * Certify Certificate Management - Admin JavaScript
 * Handles datepickers, modals, DataTables, and other interactive features
 */

jQuery(document).ready(function($) {
    'use strict';
      // Initialize DataTable with proper error handling
    function initializeDataTable() {
        // Wait for table to be fully loaded
        setTimeout(function() {
            var $table = $('#certificates-table');
            
            // If main table not found, try alternative selectors
            if ($table.length === 0) {
                $table = $('.table-wrapper table:first, table.table:first');
                if ($table.length > 0) {
                    $table.attr('id', 'certificates-table');
                }
            }
            
            if ($table.length > 0) {
                try {
                    // Destroy existing DataTable if it exists
                    if ($.fn.DataTable.isDataTable($table)) {
                        $table.DataTable().destroy();
                    }
                    
                    // Initialize DataTable with custom options
                    $table.DataTable({
                        "responsive": true,
                        "pageLength": 10,
                        "lengthChange": true,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "processing": true,
                        "language": {
                            "search": "Search certificates:",
                            "lengthMenu": "Show _MENU_ certificates per page",
                            "info": "Showing _START_ to _END_ of _TOTAL_ certificates",
                            "infoEmpty": "No certificates found",
                            "infoFiltered": "(filtered from _MAX_ total certificates)",
                            "emptyTable": "No certificate data available",
                            "zeroRecords": "No matching certificates found",
                            "processing": "Loading certificates..."
                        },
                        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                               '<"row"<"col-sm-12"tr>>' +
                               '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                        "columnDefs": [
                            {
                                "targets": 0, // First column (checkbox)
                                "orderable": false,
                                "searchable": false,
                                "width": "40px"
                            },
                            {
                                "targets": -1, // Last column (actions)
                                "orderable": false,
                                "searchable": false,
                                "width": "100px"
                            }
                        ],
                        "order": [[1, 'asc']], // Sort by candidate name by default
                        "initComplete": function() {
                            console.log('DataTable initialized successfully');
                            // Re-bind checkbox events after DataTable initialization
                            bindCheckboxEvents();
                        }
                    });
                } catch (error) {
                    console.error('DataTable initialization failed:', error);
                    // Fallback: at least make table sortable manually
                    addFallbackTableFeatures($table);
                }
            } else {
                console.warn('No table found for DataTable initialization');
            }
        }, 100);
    }
    
    // Fallback function for basic table functionality if DataTable fails
    function addFallbackTableFeatures($table) {
        console.log('Adding fallback table features');
        $table.addClass('sortable-fallback');
        // Add basic search functionality
        if ($('.dataTables_filter').length === 0) {
            var searchBox = '<div class="table-search mb-3"><input type="text" class="form-control" placeholder="Search certificates..." id="table-search-fallback"></div>';
            $table.before(searchBox);
            
            $('#table-search-fallback').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $table.find('tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        }
    }
    
    // Bind checkbox events
    function bindCheckboxEvents() {
        // Unbind previous events to avoid duplicates
        $(document).off('change.certify', '#selectAll');
        $(document).off('change.certify', 'table tbody input[type="checkbox"]');
        
        // Select all checkbox handler
        $(document).on('change.certify', '#selectAll', function() {
            var checkbox = $('table tbody input[type="checkbox"]');
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
        
        // Individual checkbox handler
        $(document).on('change.certify', 'table tbody input[type="checkbox"]', function() {
            if (!this.checked) {
                $('#selectAll').prop('checked', false);
            } else {
                // Check if all checkboxes are selected to update "Select All"
                var totalCheckboxes = $('table tbody input[type="checkbox"]').length;
                var checkedCheckboxes = $('table tbody input[type="checkbox"]:checked').length;
                $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
            }
        });
    }
      // Initialize datepickers with improved positioning and styling
    function initializeDatepicker(selector, altField) {
        if ($(selector).length) {
            $(selector).datepicker({
                dateFormat: 'dd/M/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '1940:' + new Date().getFullYear(),
                altField: altField,
                altFormat: 'mm/dd/yy',
                showAnim: 'fadeIn',
                showButtonPanel: false,
                showOtherMonths: true,
                selectOtherMonths: true,
                beforeShow: function(input, inst) {
                    var $input = $(input);
                    var isInModal = $input.closest('.modal').length > 0;
                    
                    // Set high z-index for modal context
                    if (isInModal) {
                        inst.dpDiv.addClass('ui-datepicker-modal');
                    }
                    
                    // Use setTimeout to ensure DOM is ready
                    setTimeout(function() {
                        var $datepicker = inst.dpDiv;
                        
                        // Apply base styles
                        $datepicker.css({
                            'z-index': isInModal ? 100000 : 9999,
                            'position': isInModal ? 'fixed' : 'absolute'
                        });
                        
                        // Position appropriately if in modal
                        if (isInModal) {
                            var inputOffset = $input.offset();
                            var inputHeight = $input.outerHeight();
                            var datepickerHeight = $datepicker.outerHeight();
                            var windowHeight = $(window).height();
                            var scrollTop = $(window).scrollTop();
                            
                            // Calculate available space
                            var spaceAbove = inputOffset.top - scrollTop;
                            var spaceBelow = windowHeight - (inputOffset.top - scrollTop + inputHeight);
                            
                            var top, left;
                            
                            // Position above if there's more space or if below would be cut off
                            if (spaceAbove > datepickerHeight + 10 || spaceAbove > spaceBelow) {
                                top = inputOffset.top - datepickerHeight - 5;
                            } else {
                                top = inputOffset.top + inputHeight + 5;
                            }
                            
                            left = inputOffset.left;
                            
                            // Ensure datepicker stays within window bounds
                            var datepickerWidth = $datepicker.outerWidth();
                            var windowWidth = $(window).width();
                            if (left + datepickerWidth > windowWidth) {
                                left = windowWidth - datepickerWidth - 10;
                            }
                            if (left < 0) {
                                left = 10;
                            }
                            
                            $datepicker.css({
                                'top': top + 'px',
                                'left': left + 'px'
                            });
                        }
                    }, 1);
                },
                onClose: function(selectedDate) {
                    // Remove modal class when closing
                    $(this).datepicker('widget').removeClass('ui-datepicker-modal');
                }
            });
        }
    }
      // Initialize all components
    function initializeComponents() {
        // Initialize DataTable
        initializeDataTable();
        
        // Initialize all datepickers
        initializeDatepicker('#doc', '#adoc');
        initializeDatepicker('#editdoc', '#eeditdoc');
        
        // Bind checkbox events initially
        bindCheckboxEvents();
        
        console.log('All components initialized');
    }
    
    // Edit modal handler
    $(document).on('click', '.editModal', function() {
        var id = $(this).data('id');
        var $row = $(this).closest('tr');
        var sname = $('.sname', $row).html();
        var cname = $('.cname', $row).html();
        var ccode = $('.ccode', $row).html();
        var chour = $('.chour', $row).html();
        var cadt = $('.cadt', $row).html();
        var ocadt = $('.cadt', $row).attr('date');
        
        // Populate edit modal
        $('#editEmployeeModal input[name=editid]').val(id);
        $('#editEmployeeModal input[name=std_name]').val(sname);
        $('#editEmployeeModal input[name=course_name]').val(cname);
        $('#editEmployeeModal input[name=course_hours]').val(chour);
        $('#editEmployeeModal input[name=certificate_code]').val(ccode);
        $('#editEmployeeModal input[name=doc]').val(ocadt);
        $('#editEmployeeModal #editdoc').val(cadt);
        
        // Show modal
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
        
        if (allIds.length === 0) {
            alert('Please select at least one certificate to delete.');
            return;
        }
        
        $('#deleteEmployeeModal input[name=editid]').val(allIds.join(','));
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteEmployeeModal'));
        deleteModal.show();
    });
    
    // Auto-hide alerts
    setTimeout(function() {
        $('.hide-alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
    
    // Initialize everything when DOM is ready
    initializeComponents();
    
    // Re-initialize DataTable after AJAX requests or dynamic content changes
    $(document).on('DOMNodeInserted', function(e) {
        if ($(e.target).hasClass('table-wrapper') || $(e.target).find('table').length) {
            setTimeout(initializeDataTable, 100);
        }
    });
});
