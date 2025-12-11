<!-- adding a script to render personal details once a customer is selected -->
<?php $user = auth()->user() ?>
<!-- main  footer start here -->
<footer class="main-footer d-flex  ">
    <div class="col-md-3"><strong>Copyright &copy; <?= date('Y') ?> All rights reserved.</strong></div>

    <div class="contact    col-md-6" style="color: black;">
        <span class="p-0 m-0 text-center"><b>Email: </b>ictsupport@wma.go.tz</span> |
        <span><b>Phone Number: </b>+255 767 991 300</span>
    </div>
    <div class="float-right d-none d-sm-inline-block col-md-3 ">
        <b>Version</b> 1.0.0


    </div>
</footer>
</div>
<!-- ./wrapper -->
<!-- form Wizard -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<!-- jQuery -->


<script src="<?= base_url() ?>dist/js/timePicker.js"></script>
<script src="<?= base_url() ?>dist/js/inputMaskLibrary.js"></script>
<script src="<?= base_url() ?>dist/js/inputMask.js"></script>
<script src="<?= base_url() ?>dist/js/commonTasks.js"></script>
<!-- Bootstrap -->
<script src="<?= base_url() ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url() ?>/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url() ?>/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>dist/js/adminlte.js"></script>





<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="<?= base_url() ?>/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>


<!-- SummerNote -->
<script src="<?= base_url() ?>/plugins/summernote/summernote-bs4.min.js"></script>
<!-- ChartJS -->


<!-- PAGE SCRIPTS -->

<!-- Data Tables -->




<!-- Select2 -->
<!-- <script src="<?= base_url() ?>/plugins/select2/js/select2.full.min.js"></script> -->

<!-- ================Data Table Buttons -->







<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/plugins/datatables/jquery.dataTables.min.js"></script>

<script src="<?= base_url() ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- date-range-picker -->



<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>




<!-- ADDITIONAL SCRIPTS -->



<script src="<?= base_url() ?>dist/js/demo.js"></script>





<script src="<?= base_url() ?>dist/js/personalDetails.js"></script>

<script src="<?= base_url() ?>dist/js/searchCustomer.js"></script>






<script>
    function showError(form, errors) {
        const inputs = form.querySelectorAll('input[type="text"], input[type="password"], input[type="email"],select')
        inputs.forEach(input => {
            const inputName = input.getAttribute('name');
            const error = errors[inputName];
            let span = input.nextElementSibling;
            if (error) {
                if (!span || !span.classList.contains('text-danger')) {
                    const newSpan = document.createElement('span');
                    newSpan.classList.add('text-danger');
                    input.insertAdjacentElement('afterend', newSpan);
                    span = newSpan;
                }
                span.textContent = error;
            } else if (span && span.classList.contains('text-danger')) {
                span.remove();
            }
        });
    }
    $(document).ready(function() {


        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            //  dropdownParent: $(".modal-body")
        });

        $(function() {

            $('[data-toggle="tooltip"]').tooltip()
        })

        $('input[type="file"]').change(function(e) {
            var fileName = e.target.files[0].name;
            $('.custom-file-label').html(fileName);
        });



        // $(document).on("click", ".print", function() {
        //     const section = $("#printingSection");
        //     const modalBody = $("#modal-body").detach();

        //     const content = $(".printingContent").detach();
        //     section.append(modalBody);
        //     window.print();
        //     section.empty();
        //     section.append(content);
        //     $(".modal-body-wrapper").append(modalBody);
        // });




        let tableHeader = $('.head');

        let columnArray = [];
        for (let i = 0; i < tableHeader.length - 1; i++) {

            columnArray.push(i)
        }
        // $('#billTable').DataTable({
        //     "retrieve": true,
        //     // "cache": false,
        //     // "destroy": true,
        //     "responsive": true,
        //     "autoWidth": false,
        //     "paging": true,
        //     "lengthChange": true,
        //     "searching": true,
        //     "ordering": true,
        //     "info": true,
        // });
        $("#example1").DataTable({

            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;


                // ================Create a dynamic index to target the column==============
                const target = document.querySelector("#amount");
                const columnIndex = Array.from(document.querySelectorAll(".head")).indexOf(target);

                function formatCurrency(amount) {
                    return new Intl.NumberFormat().format(amount)
                }


                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\Tsh,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                // Total over all pages
                total = api
                    .column(columnIndex)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total over this page
                pageTotal = api
                    .column(columnIndex, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(columnIndex).footer()).html(
                    'Tsh ' + formatCurrency(pageTotal) + ' Total'
                );

                $('.total').html('Tsh ' + formatCurrency(pageTotal));
            },
            dom: 'lBfrtip',
            buttons: [

                {
                    extend: 'print',
                    // autoPrint: true,
                    orientation: 'landscape',
                    exportOptions: {
                        columns: columnArray
                    }
                },

                {
                    extend: 'copyHtml5',
                    footer: true,
                    exportOptions: {
                        columns: [0, ':visible'],
                        columns: columnArray
                    }
                },
                {
                    extend: 'excelHtml5',
                    footer: true,
                    exportOptions: {
                        columns: ':visible',
                        columns: columnArray
                    }
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    orientation: 'landscape',
                    pageSize: 'LEGAL',

                    exportOptions: {
                        columns: columnArray
                    }
                },

                'csv',
                // 'colvis'
            ],
            lengthMenu: [20, 30, 50, 70, 100],
            "responsive": true,
            "autoWidth": false,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,

        });

    });


    setInterval(function() {
        $('#message').fadeOut(7000)
    });



    // baseUrl is now defined globally in mainHeader.php
    // let baseUrl = 'http://10.1.79.253';
    // let baseUrl = 'https://sites.local/mis';




    function commaSeparator(input) {
        const formatted = new Intl.NumberFormat().format(input.value)
        return input.value = formatted
        //  return new Intl.NumberFormat().format(value)
    }


    // checking session if is active
    function checkSession() {
        // Check if baseUrl is defined before making the request
        if (typeof baseUrl === 'undefined' || !baseUrl) {
            console.warn('baseUrl not defined, skipping session check');
            return;
        }

        const sessionUrl = baseUrl + '/checkSession';
        fetch(sessionUrl)
            .then(response => response.json())
            .then(data => {
                const {
                    status,
                    redirectTo
                } = data
                if (status === 'inactive') {
                    // Log out the user if the session is inactive
                    window.location.href = redirectTo; // Replace with the URL to your logout controller method
                } else {
                    console.log(data)
                }
            })
            .catch(error => {
                console.log('Error checking session status for URL:', sessionUrl);
                console.error(error);
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        setInterval(checkSession, 30000); // Check session every 20 seconds
    });



    // Initialize the timer variables
    let inactivityTime = 100; // 3 minutes
    let inactivityTimer;
    let countdownInterval;



    // Function to reset the inactivity timer
    function resetInactivityTimer() {
        // Clear the previous timers, if any
        clearTimeout(inactivityTimer);
        clearInterval(countdownInterval);

        // Start a new timer
        inactivityTimer = setTimeout(function() {
            // The user is inactive, show a SweetAlert countdown and then send a fetch request to the logout route
            var countdownTime = 60; // seconds
            countdownInterval = setInterval(function() {
                swal({
                    title: `You will be logged out in ${countdownTime} seconds`,
                    timer: 1000,
                    icon: 'warning',
                    buttons: {
                        cancel: "Cancel",

                    },
                    onBeforeOpen: function() {
                        swal.showLoading();
                    }
                });
                countdownTime--;

                if (countdownTime < 0) {
                    clearInterval(countdownInterval);
                    swal.close();
                    fetch(baseUrl + '/destroySession')
                        .then(function(response) {
                            return response.json();
                        })
                        .then(function(data) {
                            // If the response status is 1, redirect to the login page
                            if (data.status === 1) {
                                window.location.href = baseUrl + '/';
                            }
                        })
                        .catch(function(error) {
                            console.error(error);
                        });
                }
            }, 1000);
        }, inactivityTime * 60 * 1000); // Convert to milliseconds
    }

    // Add event listeners to reset the timer when the user interacts with the page
    document.addEventListener('mousemove', resetInactivityTimer);
    document.addEventListener('keydown', resetInactivityTimer);

    // Add an event listener to clear the countdown interval when the user moves their mouse over the page
    document.addEventListener('mousemove', function() {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            swal.close();
        }
    });



    document.querySelectorAll('.number-input').forEach(input => {
        // Function to format number while preserving decimal input
        function formatNumber(value) {
            // If empty, return empty
            if (!value) return '';

            // Remove all commas first
            value = value.replace(/,/g, '');

            // Check if it's a valid number or a partial decimal input
            if (isNaN(parseFloat(value)) && value !== '.' && !value.match(/^\d*\.?\d*$/)) {
                return '';
            }

            // If it's just a decimal point or ends with decimal point, return as is
            if (value === '.' || value.endsWith('.')) {
                return value;
            }

            // If it contains a decimal point
            if (value.includes('.')) {
                const parts = value.split('.');
                // Format the integer part with commas
                const formattedInteger = new Intl.NumberFormat('en-US').format(parseFloat(parts[0] || 0));
                // Return the formatted integer part plus the decimal part (up to 4 places)
                return formattedInteger + '.' + parts[1].substring(0, 4);
            } else {
                // Just a whole number, format it
                return new Intl.NumberFormat('en-US').format(parseFloat(value));
            }
        }

        input.addEventListener('input', function(e) {
            // Store cursor position and current value
            const cursorPos = this.selectionStart;
            const currentValue = this.value;

            // Get value without commas for processing
            const cleanValue = currentValue.replace(/,/g, '');

            // Format the value
            const formattedValue = formatNumber(cleanValue);

            // Only update if needed
            if (formattedValue !== currentValue) {
                // Calculate how many commas were added/removed before cursor
                const beforeCursor = currentValue.substring(0, cursorPos);
                const beforeCursorNoCommas = beforeCursor.replace(/,/g, '');

                // Format just the part before cursor to count commas
                const formattedBefore = formatNumber(beforeCursorNoCommas);
                const commasDiff = (formattedBefore.match(/,/g) || []).length -
                    (beforeCursor.match(/,/g) || []).length;

                this.value = formattedValue;

                // Adjust cursor position based on commas added/removed
                this.setSelectionRange(cursorPos + commasDiff, cursorPos + commasDiff);
            }
        });

        // On blur, ensure proper final formatting
        input.addEventListener('blur', function() {
            if (this.value) {
                // Remove commas first
                const cleanValue = this.value.replace(/,/g, '');

                // If it's a valid number, format it properly
                if (!isNaN(parseFloat(cleanValue))) {
                    const parts = cleanValue.split('.');
                    const formattedInteger = new Intl.NumberFormat('en-US').format(parseFloat(parts[0] || 0));

                    if (parts.length > 1) {
                        // Has decimal part - limit to 4 places
                        this.value = formattedInteger + '.' + parts[1].substring(0, 4);
                    } else {
                        // No decimal part
                        this.value = formattedInteger;
                    }
                }
            }
        });
    });





    //Initialize Select2 Elements
    //$('.select2').select2()


    $(function() {
        // Summernote
        $('.textarea').summernote()
    })
</script>

<?= $this->renderSection('scripts') ?>
</body>



</html>