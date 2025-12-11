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
<style>
    .green {
        /* background: #22ae32; */
        background: 'red';
        color: white;
    }
</style>

<div class="container-fluid">
    <input type="text" class="form-control" name="document" id="document" value="<?= url_is('payments') ? 'receipt' : 'bill'; ?>" hidden>

    <?php if (url_is('billManagement')) : ?>

        <?= view('Components/bill') ?>
    <?php elseif ('payments') : ?>
        <?= view('Components/receipt') ?>
    <?php endif; ?>



    <!-- Modal -->
    <div class="modal modal-static fade" id="cancelBill" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bill Cancellation</h5>

                </div>
                <div class="modal-body">
                    <form id="cancelBillForm">
                        <div class="form-group">
                            <label for="">Control Number</label>
                            <input type="text" name="controlNumber" id="controlNumber" class="form-control" readonly required>
                            <input type="text" name="requestId" id="requestId" class="form-control" readonly required hidden>

                        </div>
                        <div class="form-group">
                            <label for="">Reason For Cancelling</label>
                            <textarea class="form-control" name="reason" id="reason" rows="3" required></textarea>
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
    <!-- Modal -->
    <div class="modal modal-static fade" id="viewBillId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h5 id="theBillId" class="border-1  p-2" id="billContent" style="background: #e1e1e1;"></h5>
                    </div>
                    <div class="d-flex justify-content-center">
                        <!-- <button class="btn btn-primary btn-sm" onclick="copyToClipboard('billContent')">
                            <i class="far fa-clipboard"></i> Copy
                        </button> -->
                    </div>
                </div>
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
            <?php if (url_is('billManagement')) : ?>
                SEARCH BILL
            <?php else : ?>
                SEARCH RECEIPTS
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form id="searchBillForm">
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
                                <?php if (url_is('billManagement')) : ?>
                                    <option value="Pending">Pending</option>
                                    <option value="Partial">Partial</option>
                                <?php else : ?>
                                    <option value="Paid">Paid</option>
                                <?php endif; ?>

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
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fal fa-search"></i> Search Bill</button>
                    </div>

                </div>
                </from>
        </div>

    </div>

    <div class="card" id="billBlock" style="display:none">
        <div class="card-body">
            <table class="table table-sm" id="billResults">


            </table>
            <!-- <div id="billResults"></div> -->
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
    //Date range picker
    const searchBillForm = document.querySelector('#searchBillForm')

    searchBillForm.addEventListener('submit', (e) => {
        e.preventDefault()



        console.log('searching....')
        const formData = new FormData(searchBillForm)
        const billResults = document.querySelector('#billResults')



        fetch('searchBill', {
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
                    status,
                    token,
                    bill,
                    msg
                } = data

                // console.log(data)
                document.querySelector('.token').value = token
                if (status == 1) {
                    billBlock.style.display = 'block'
                    // console.log(bill)
                    billResults.innerHTML = bill

                    let table = $('#billResults').DataTable();

                    // Destroy the DataTable
                    table.destroy();

                    // Remove the table
                    $('#billResults').empty();

                    // Add a new table with updated `thead`
                    $('#billResults').html(bill);

                    $('[data-toggle="tooltip"]').tooltip()

                    // Re-initialize the DataTable with updated `thead`
                    table = $('#billResults').DataTable({
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
                    billBlock.style.display = 'none'
                    swal({
                        title: 'No Data Found !',
                        icon: "warning",
                        timer: 2500
                    });
                }






            });
    })



    function cancelBill(requestId, controlNumber) {


        $('#cancelBill').modal({
            open: true,
            backdrop: 'static'
        })

        document.querySelector('#requestId').value = requestId
        document.querySelector('#controlNumber').value = controlNumber


    }

    function printBill(billData) {
        const {
            status,
            bill,
            qrCodeObject,
            token,


        } = billData
        // console.log('printing ......')




        const qrCode = new QRCodeStyling({

            width: 200,
            height: 200,
            type: "svg",
            data: JSON.stringify(qrCodeObject),
            image: "<?= base_url('assets/images/emblem.png') ?>",
            dotsOptions: {
                color: "#333333",
                type: "square"
            },
            backgroundOptions: {
                color: "#ffffff",
            },
            imageOptions: {
                crossOrigin: "anonymous",
                margin: 0,
                imageSize: 0.2
            }
        });



        document.querySelector('#billDetails').innerHTML = bill
        document.querySelector('#canvas').innerHTML = ''
        qrCode.append(document.getElementById("canvas"));
        $('#printModal').modal({
            open: true,
            backdrop: 'static'
        })



    }




    function resubmitBill(requestId) {

        swal({
            title: "Do you want to resubmit this bill ?",
            icon: "warning",
            buttons: true,
            buttons: ["No", "Yes"],
            dangerMode: true,
        })
        .then((willRun) => {
            
            if (willRun) {
                document.querySelector(`#b-${requestId}`).setAttribute('disabled', 'disabled')
                swal({
                    title: 'Please Wait',
                    text: 'Bill is being resubmitted',
                    icon: '<?= base_url('assets/images/spin1.gif') ?>',
                    closeOnClickOutside: false,
                    buttons: false,
                    
                });
                    fetch('billResubmissionRequest', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json;charset=utf-8',
                                "X-Requested-With": "XMLHttpRequest",
                                'X-CSRF-TOKEN': document.querySelector('.token').value
                            },

                            body: JSON.stringify({
                                requestId
                            }),

                        }).then(response => response.json())
                        .then(data => {
                            const {
                                bill,
                                token,
                                status,
                                msg,
                                TrxStsCode

                            } = data
                            document.querySelector('.token').value = token
                            if (status == 1) {
                                document.querySelector(`#b-${requestId}`).remove()
                                swal.close();
                                printBill(data)


                            } else {
                                swal.close();
                                swal({
                                    title: msg,
                                    text: TrxStsCode,
                                    icon: "warning",
                                    // timer: 18500
                                });
                            }
                            console.log(data)
                        })

                } else {

                    swal("Bill is not resubmitted");
                }
            });



    }

    function renewBill(requestId, controlNumber,typeOfBill) {
        console.log(requestId)
        swal({
                title: "Do you want to renew this bill ?",
                text: 'Control Number: ' + controlNumber,
                icon: "warning",
                buttons: true,
                buttons: ["No", "Yes"],
                dangerMode: true,
            })
            .then((willRun) => {

                if (willRun) {
                    swal({
                        title: 'Please Wait',
                        text: 'Bill is being renewed',
                        icon: '<?= base_url('assets/images/spin1.gif') ?>',
                        closeOnClickOutside: false,
                        buttons: false,

                    });
                  let  requestUrl = typeOfBill == 'TR' ? 'trBillRenewRequest' : 'billRenewRequest'
                    fetch(requestUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json;charset=utf-8',
                                "X-Requested-With": "XMLHttpRequest",
                                'X-CSRF-TOKEN': document.querySelector('.token').value
                            },

                            body: JSON.stringify({
                                requestId
                            }),

                        }).then(response => response.json())
                        .then(data => {
                            const {
                                bill,
                                token,
                                status,
                                msg,
                                TrxStsCode

                            } = data
                            document.querySelector('.token').value = token
                            if (status == 1) {
                                swal.close();
                                printBill(data)
                             


                            } else {
                                swal.close();
                                swal({
                                    title: msg,
                                    text: TrxStsCode,
                                    icon: "warning",
                                    // timer: 18500
                                });
                            }
                            console.log(data)
                        })

                } else {

                    swal("Bill is not renewed");
                }
            });



    }




    function viewBillId(requestId) {


        $('#viewBillId').modal({
            open: true,
            // backdrop: 'static'
        })

        document.querySelector('#theBillId').textContent = requestId

    }
    //





    const cancelBillForm = document.querySelector('#cancelBillForm')
    cancelBillForm.addEventListener('submit', (e) => {
        e.preventDefault()

        const reason = document.querySelector('#reason').value


        const cancelBillForm = document.querySelector('#cancelBillForm')
        const formData = new FormData(cancelBillForm)

        // formData.append('billId', billId)
        fetch('billCancellationRequest', {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },

                body: formData,

            }).then(response => response.json())
            .then(data => {
                const {
                    requestId,
                    token,
                    status,
                    msg,
                    code
                } = data
                document.querySelector('.token').value = token
                if (status == 1) {
                    cancelBillForm.reset()
                    $('#cancelBill').modal('hide')
                    // document.querySelector('.' + billId).remove()
                    swal({
                        title: msg,
                        icon: "success",
                        // timer: 5500
                    });
                } else {
                    swal({
                        title: msg,
                        text: code,
                        icon: "warning",
                        // timer: 18500
                    });
                }
                console.log(data)
            })


    })

    function viewBill(requestId,$control) {



        // console.log(requestId)


        const params = {
            document: document.querySelector('#document').value,
            requestId: requestId,
        }

        fetch('selectBill', {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },
                body: JSON.stringify(params)
            })
            .then(response => response.json())
            .then(data => {
                const {
                    token,
                    qrCodeObject,
                    status,
                    heading
                } = data
                document.querySelector('.token').value = token
                const obj = JSON.stringify(qrCodeObject)
                console.log(data)

                if (status == 1) {
                    document.querySelector('#heading').textContent = heading
                    printBill(data)
                }


            });



    }
</script>
<?= $this->endSection(); ?>