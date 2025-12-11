<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!-- <h1 class="m-0 text-dark"></h1> -->
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="container-fluid">



    <?= view('Components/receipt') ?>



    <!-- Modal -->
    <div class="modal modal-static fade" id="cancelPayment" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Cancellation</h5>

                </div>
                <div class="modal-body">
                    <form id="cancelPaymentForm">
                        <div class="form-group">
                            <label for="">Control Number</label>
                            <input type="text" name="controlNumber" id="controlNumber" class="form-control" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea class="form-control" name="description" id="" rows="3" required></textarea>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('#exampleModal').on('show.bs.modal', event => {
            var button = $(event.relatedTarget);
            var modal = $(this);
            // Use above variables to manipulate the DOM

        });
    </script>
    <div class="card">

        <div class="card-header">

            SEARCH PAYMENT RECEIPTS
        </div>
        <div class="card-body">
            <form id="searchPaymentForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Activity</label>
                            <select class="form-control select2bs4" name="activity" id="" style="width:100%">
                                <option value="">All</option>
                                <?php foreach (gfsCodes() as $key => $value) : ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Payment Status</label>
                            <select class="form-control" name="payment" id="">

                                <option value="Paid">Paid</option>
                                <option value="Partial">Partial</option>

                                <!-- <option value="All">All</option> -->

                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Search By Name</label>
                            <input type="text" name="name" id="" class="form-control" placeholder="Enter Name" aria-describedby="helpId">

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Search By Control Number</label>
                            <input type="text" name="controlNumber" id="" class="form-control control" placeholder="Enter Control Number" oninput="this.value=this.value.replace(/(?![0-9])./gmi,'')">

                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Search By Phone Number</label>
                            <input type="text" name="phone" id="" class="form-control phone" placeholder="Enter Phone number" aria-describedby="helpId">

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="clearDateCheckbox"> <input type="checkbox" id="clearDateCheckbox" class="check checkBox" style="transform:scale(1.3); margin-right:5px;">Date</label>
                            <input type="date" name="date" id="date" class="form-control phone" placeholder="Enter Phone number" disabled>

                        </div>
                    </div>

                    <!-- Date range -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="clearDateRangeCheckbox"><input type="checkbox" id="clearDateRangeCheckbox" class="check checkBox" style="transform:scale(1.3); margin-right:5px">Date Range
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control float-right" name="dateRange" id="dateRange" disabled autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fal fa-search"></i> Search Payment</button>
                    </div>

                </div>
                </from>
        </div>

    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm" id="receipts"></table>

        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Initialize DateRangePicker with no initial value
        $('#dateRange').daterangepicker({
            // locale: {
            //     format: 'DD/MM/YYYY '
            // }
        });

        // Get the DateRangePicker instance
        var dateRangePicker = $('#dateRange').data('daterangepicker');

        // Listen for the cancel event
        $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
            // Clear the field if the user cancels the selection
            $(this).val('');
        });
        $('#dateRange').val('')



        $('#clearDateCheckbox').change(function() {
            if ($(this).is(':checked')) {
                $('#date').prop('disabled', false);
            } else {
                $('#date').prop('disabled', true);
                $('#date').val('')
            }
        });

        $('#clearDateRangeCheckbox').change(function() {
            if ($(this).is(':checked')) {
                $('#dateRange').prop('disabled', false);
            } else {
                $('#dateRange').prop('disabled', true);
                $('#dateRange').val('')
            }
        });
    });
</script>

<script>
    const searchPaymentForm = document.querySelector('#searchPaymentForm')

    searchPaymentForm.addEventListener('submit', (e) => {
        e.preventDefault()



        console.log('searching....')
        const formData = new FormData(searchPaymentForm)

        const receipts = document.querySelector('#receipts')


        fetch('searchPayment', {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },

                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const {
                    payments,
                    token,
                    status,
                    msg
                } = data
                document.querySelector('.token').value = token
                if (status == 1) {
                    receipts.innerHTML = payments
                    let table = $('#receipts').DataTable();

                    // Destroy the DataTable
                    table.destroy();

                    // Remove the table
                    $('#receipts').empty();

                    // Add a new table with updated `thead`
                    $('#receipts').html(payments);
                    $('[data-toggle="tooltip"]').tooltip()
                    // Re-initialize the DataTable with updated `thead`
                    table = $('#receipts').DataTable({
                        dom: '<"top"lBfrtip>',
                        buttons: [
                            'excel',
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

                } else {

                    receipts.innerHTML = ''
                    swal({
                        title: msg,
                        icon: "warning",
                        timer: 42500
                    });
                }








            });
    })





    function viewPayment(requestId, paymentRef) {





        const params = {
            requestId: requestId,
            paymentRef: paymentRef,
        }

        console.log(params)
        fetch('selectPayment', {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },
                body: JSON.stringify(params)
            })
            .then(response => response.json())
            .then(data => {
                // console.log(data)
                document.querySelector('.token').value = data.token

                printBill(data)




            });



    }

    function printBill(receiptData) {
        const {
            status,
            receipt,
            token,


        } = receiptData
        // console.log('printing ......')








        document.querySelector('#receiptDetails').innerHTML = receipt

        $('#printModal').modal({
            open: true,
            backdrop: 'static'
        })



    }
</script>
<?= $this->endSection(); ?>