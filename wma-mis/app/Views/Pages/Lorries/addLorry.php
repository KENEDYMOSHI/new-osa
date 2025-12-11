<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>

<style>
    /* .pulse {
        animation: pulse 1s infinite alternate;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        100% {
            transform: scale(1.2);
        }
    } */
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark"><?= $page['heading'] ?></h4>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content body">
    <div class="container-fluid">
        <?= view('Components/bill') ?>
        <?= view('Components/ClientsBlock') ?>


        <!-- modal -->
        <div class="modal Sbl-modal fade" id="add-Sbl">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Lorry</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addLoryForm">
                        <div class="modal-body">
                            <div class="form-group">
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="must" for="">Activity</label>
                                    <select class="form-control" name="task" required>
                                        <option disabled selected value="">-Select Activity-</option>
                                        <option value="Verification">Verification</option>
                                        <option value="Reverification">Reverification</option>
                                        <option value="Inspection">Inspection</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tin Number </label>
                                    <input type="text" class="form-control tin " name="tinNumber" placeholder="Enter Tin Number">


                                </div>
                                <div class="form-group col-md-6">
                                    <label class="must" for="">Visual Inspection</label>
                                    <select class="form-control" name="visualInspection" id="visualInspection" required onchange="evaluateInstrument(this.value)">
                                        <option disabled selected>-Select Status-</option>
                                        <option value="Pass">Pass</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Condemned">Condemned</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="must" for="">Testing</label>
                                    <select class="form-control" name="testing" id="testing" required onchange="evaluateInstrument(this.value)">
                                        <option disabled selected>-Select Status-</option>
                                        <option value="Pass">Pass</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Condemned">Condemned</option>
                                    </select>
                                </div>
                            </div>



                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="must" for="">Driver's Full Name</label>
                                    <input type="text" name="driverName" id="" class="form-control" placeholder="Enter Driver's full Name" required>
                                    <!-- <small id="helpId" class="text-muted">Help text</small> -->
                                </div>


                                <div class="form-group col-md-6">
                                    <label for="my-input">Driver's License</label>

                                    <div class="input-group">
                                        <input class="form-control license " name="driverLicense" type="text" placeholder=" Enter Driver's License">

                                    </div>




                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="must">Vehicle Brand</label>
                                    <input type="text" class="form-control" name="vehicleBrand" placeholder="Enter Brand" required>


                                </div>
                                <div class="form-group col-md-6">
                                    <label class="must">Vehicle Type</label>
                                    <input type="text" class="form-control" name="type" placeholder="Enter Type" required>


                                </div>
                                <div class="form-group col-md-6">
                                    <label class="must">Vehicle Model</label>
                                    <input type="text" class="form-control" name="model" placeholder="Enter Model" required>


                                </div>
                                <div class="form-group col-md-6">
                                    <label class="must">Vehicle Plate Number </label>
                                    <input type="text" class="form-control " name="plateNumber" placeholder="Enter Plate Number" oninput="this.value = this.value.toUpperCase().replaceAll(/\s/g,'')" required>


                                </div>
                            </div>

                            <div class="row" id="measurements">

                                <div class="form-group col-md-4">
                                    <label class="must">Width</label>
                                    <input type="number" class="form-control" name="width" min="0" placeholder="Enter  width" required>

                                </div>
                                <div class="form-group col-md-4">
                                    <label class="must">Length</label>
                                    <input type="number" class="form-control " name="height" min="0" placeholder="Enter  length" required>

                                </div>
                                <div class="form-group col-md-4">
                                    <label class="must">Depth</label>
                                    <input type="number" class="form-control " name="depth" min="0" placeholder="Enter  depth" required>

                                </div>
                                <div class="form-group col-md-6">
                                    <label class="must">Lorry Capacity in m<sup>3</sup></label>
                                    <input type="number" class="form-control " name="lorryCapacity" placeholder="Enter  lorry Capacity in Cubic Meter" required readonly>

                                </div>
                            </div>

                            <div class="row">




                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-sm mt-2 mb-2" style="float: right;" onclick="addTrailer()"><i class="far fa-plus"></i> Add Trailer</button>
                                </div>

                            </div>
                            <div class="col-md-12" id="deadline" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="" for="">Number Of Days</label>
                                            <input type="number" name="days" class="form-control" oninput="calculateDeadlineDate(this.value)" required>

                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="must" for="">Repair Deadline<span class="text-danger"></span></label>
                                            <input type="text" name="repairDeadline" id="repairDeadline" readonly class="form-control" required>

                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div id="trailerBlock">

                            </div>


                            <div class="form-group">
                                <label for="my-textarea">Remark</label>
                                <textarea name="remark" class="form-control " rows="3" data-clear></textarea>
                            </div>
                            <div class="icheck-primary d-inline">
                                <input class="form-check-input label-check" name="hasPenalty" id="fine" type="checkbox" onchange="toggleFine(this)">

                                <label class="form-check-label" for="fine">Include Fine & Penalty</label>
                            </div>
                            <div class="col-md-12 mt-2" id="penaltyBlock" style="display: none;">
                                <div class="form-group">
                                    <label class="must" for="">Penalty Amount<span class="text-danger"></span></label>
                                    <input type="number" name="penaltyAmount" id="penaltyAmount" class="form-control" oninput="calculateTotal(this)" required>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                                Save
                            </button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- modal -->


        <div class="modal  fade" id="updateModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Lorry</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="updateLorryForm">
                        <div class="modal-body">
                            <div id="lorryInfo">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="must">Vehicle Plate Number </label>
                                        <input type="text" class="form-control " name="plateNumber" id="plateNumber" placeholder="Enter Plate Number" oninput="this.value = this.value.toUpperCase().replaceAll(/\s/g,'')" required>


                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="must">Lorry Capacity in m<sup>3</sup></label>
                                        <input type="number" class="form-control " id="lorryCapacity" name="lorryCapacity" placeholder="Enter  lorry Capacity in Cubic Meter" required readonly>

                                    </div>




                                </div>
                            </div>





                            <div id="lorryTrailers">

                            </div>



                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                                Update
                            </button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div class="card">
            <div class="card-header row">
                <div class="mt-4"><button type="button" class="btn btn-primary btn-sm" id="addSblButton"><i class="far fa-plus"></i> Add Lorry </button></div>
                <div class="form-group col-3">
                    <label for=""></label>
                    <select class="form-control" name="taskName" id="taskName">
                        <option value="">--Select Task --</option>
                        <option value="Verification">Verification</option>
                        <option value="Reverification">Reverification</option>
                        <option value="Inspection">Inspection</option>
                    </select>
                </div>
                <div class="mt-4"> <button type="button" class="btn btn-success btn-sm" onclick="syncLorries()"><i class="far fa-list"></i> List Lories</button></div>
            </div>
            <form id="sblDataForm">
                <div class="card-body" id="lorries">

                </div>
                <div class="card-footer">

                    <div class="form-group">
                        <label for=""></label>
                        <input type="text" name="billedAmount" id="billedAmount" class="form-control" oninput="calculateTotal(this)" placeholder="" readonly>

                    </div>

                </div>
        </div>

        <div class="card">

            <div class="card" id="billBlock" style="display: none;">

                <div class="card-header">BILL DETAILS</div>
                <div class="card-body">
                    <?= $this->include('Components/billOptions') ?>
                </div>
                <div class="card-footer">
                    <button type="submit" id="submit" class="btn btn-primary btn-sm ">
                        <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                        Submit
                    </button>
                </div>
                </form>
            </div>


            <!-- /.card -->


        </div>
        <!-- /.card -->

    </div>
    </div>

</section>

<!-- <script>
    (function($) {
        $(document).ready(function() {
           

        });
    })(jQuery);
</script> -->

<script>
    function toggleFine(box) {

        const penaltyBlock = document.querySelector('#penaltyBlock');
        if (box.checked == true) {
            penaltyBlock.style.display = 'block';
        } else {
            document.querySelector('#penaltyAmount').value = '';
            penaltyBlock.style.display = 'none';
        }
    }

    function calculateCapacity() {
        // Get the values from the input fields
        let width = parseFloat(document.querySelector('input[name="width"]').value);
        let height = parseFloat(document.querySelector('input[name="height"]').value);
        let depth = parseFloat(document.querySelector('input[name="depth"]').value);

        // Check if all inputs are valid numbers
        if (!isNaN(width) && !isNaN(height) && !isNaN(depth)) {
            // Calculate the capacity
            let capacity = width * height * depth;
            // Set the calculated capacity to the lorryCapacity input field
            document.querySelector('input[name="lorryCapacity"]').value = capacity.toFixed(2);
        } else {
            // Clear the lorryCapacity input field if any input is invalid
            document.querySelector('input[name="lorryCapacity"]').value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Get all input fields for width, height, and depth
        let inputs = document.querySelectorAll('input[name="width"], input[name="height"], input[name="depth"]');
        // Add an event listener to each input field to trigger the calculation on input
        inputs.forEach(input => {
            input.addEventListener('input', calculateCapacity);
        });
    });



    function calculateTrailerCapacity(trailerBox) {
        // Get the values from the input fields within the specific trailer box
        let width = parseFloat(trailerBox.querySelector('input[name="trailerWidth[]"]').value);
        let height = parseFloat(trailerBox.querySelector('input[name="trailerHeight[]"]').value);
        let depth = parseFloat(trailerBox.querySelector('input[name="trailerDepth[]"]').value);

        // Check if all inputs are valid numbers
        if (!isNaN(width) && !isNaN(height) && !isNaN(depth)) {
            // Calculate the capacity
            let capacity = width * height * depth;
            // Set the calculated capacity to the trailerCapacity input field
            trailerBox.querySelector('input[name="trailerCapacity[]"]').value = capacity;
        } else {
            // Clear the trailerCapacity input field if any input is invalid
            trailerBox.querySelector('input[name="trailerCapacity[]"]').value = '';
        }
    }

    function addTrailer() {
        const trailerBlock = document.getElementById('trailerBlock');

        const trailerBox = document.createElement('div');
        trailerBox.classList.add('row', 'p-2', 'elevation-0', 'mb-2');
        trailerBox.style.border = '1px solid #e7e5e5';
        trailerBox.style.borderRadius = '4px';
        trailerBox.id = 'trailerBox';

        trailerBox.innerHTML = `
                <div class="col-md-12">
                    <button type="button" class="btn btn-outline-secondary btn-sm" style="float: right;" onclick="this.parentNode.parentNode.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="form-group col-md-4">
                    <label class="must">Width</label>
                    <input type="number" class="form-control" name="trailerWidth[]" min="0" placeholder="Enter width" required>
                </div>
                <div class="form-group col-md-4">
                    <label class="must">Length</label>
                    <input type="number" class="form-control" name="trailerHeight[]" min="0" placeholder="Enter length" required>
                </div>
                <div class="form-group col-md-4">
                    <label class="must">Depth</label>
                    <input type="number" class="form-control" name="trailerDepth[]" min="0" placeholder="Enter depth" required>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Trailer Plate Number</label>
                        <input type="text" name="trailerPlate[]" class="form-control" placeholder="Trailer Plate Number" oninput="this.value = this.value.toUpperCase().replaceAll(/\\s/g,'')">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Trailer Plate Capacity</label>
                        <input type="number" name="trailerCapacity[]" class="form-control" placeholder="Trailer Plate Capacity" readonly>
                    </div>
                </div>
            `;

        trailerBlock.appendChild(trailerBox);

        // Add event listeners to the input fields of the new trailer box
        trailerBox.querySelectorAll('input[name="trailerWidth[]"], input[name="trailerHeight[]"], input[name="trailerDepth[]"]').forEach(input => {
            input.addEventListener('input', () => calculateTrailerCapacity(trailerBox));
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('addTrailerButton').addEventListener('click', addTrailer);
    });





    function evaluateInstrument(value) {
        // Get the values from both select boxes
        const measurements = document.querySelector('#measurements')
        const visualInspectionValue = document.querySelector("#visualInspection").value;
        const testingValue = document.querySelector("#testing").value;
        const deadline = document.querySelector("#deadline")




        // Check if both values are "Pass"
        if (visualInspectionValue === "Pass" && testingValue === "Pass") {

            measurements.style.display = "flex";
            deadline.style.display = "none";
        } else if (visualInspectionValue === "Rejected" || testingValue === "Rejected") {

            deadline.style.display = "block";
            measurements.style.display = "none";
        } else {
            deadline.style.display = "none";
            measurements.style.display = "none";

        }
    }

    function calculateDeadlineDate(days) {
        const date = new Date();

        date.setDate(date.getDate() + Number(days));
        const expiryDate = `${date.getDate()}-${date.toLocaleString('default', { month: 'long' })}-${date.getFullYear()}`

        document.querySelector('#repairDeadline').value = expiryDate
    }











    function getItemAmount(input) {
        let total = 0
        input.value = input.value.replace(/\D/g, '').replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',')
        let currentInputValue = input.value.replace(/,/g, '')
        const amounts = document.querySelectorAll('.lorryAmount')
        console.log(amounts)
        for (amount of amounts) {

            total += Number(amount.value.replace(/\D/g, '').replace(/,/g, ''))
            // console.log(Number(amount.value.replace(/\D/g, '').replace(/,/g, '')) )
        }

        document.querySelector('#billedAmount').value = new Intl.NumberFormat().format(total)
    }

    function clearRow(id) {
        // Find the button with the tooltip inside the row
        const button = document.querySelector('#' + id + ' button[data-toggle="tooltip"]');

        if (button) {
            // Hide or destroy the tooltip associated with the button
            $(button).tooltip('hide'); // or .tooltip('dispose')
        }

        // Remove the row
        document.querySelector('#' + id).remove();

        // Recalculate the total
        let total = 0;
        const amounts = document.querySelectorAll('.lorryAmount');
        for (const amount of amounts) {
            total += Number(amount.value.replace(/\D/g, '').replace(/,/g, ''));
        }

        // Update the billedAmount
        document.querySelector('#billedAmount').value = new Intl.NumberFormat().format(total);
    }




    function getCoordinates() {
        return new Promise((resolve, reject) => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        resolve({
                            latitude,
                            longitude
                        });
                    },
                    (error) => {
                        reject(error);
                        // resolve({
                        //     latitude: -6.8294289,
                        //     longitude: 39.2605616
                        // });
                    }
                );
            } else {
                reject("Geolocation is not supported by this browser.");
                // resolve({
                //     latitude: -6.8294289,
                //     longitude: 39.2605616
                // });
            }
        });
    }



    //=================Publish Sbl data to transaction table with vehicle id customer hash and control number====================




    $('#sblDataForm').validate()
    const sblDataForm = document.querySelector('#sblDataForm')
    sblDataForm.addEventListener('submit', (e) => {
        if ($('#sblDataForm').valid()) {
            const formData = new FormData(sblDataForm)

            formData.append('taskName', document.querySelector('#taskName').value)
            e.preventDefault()
            submitInProgress(e.submitter)
            formData.append('customerId', document.querySelector('#customerId').value)
            fetch('<?= base_url() ?>publishLorryData', {
                    method: 'POST',
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSRF-TOKEN': document.querySelector('.token').value
                    },

                    body: formData,

                }).then(response => response.json())
                .then(data => {

                    // console.log(data)
                    const {
                        status,
                        token,
                        msg,
                        TrxStsCode
                    } = data
                    document.querySelector('.token').value = token
                    submitDone(e.submitter)
                    if (status == 1) {
                        swal({
                            title: msg,
                            text: 'Status Code ' + TrxStsCode,
                            icon: "success",
                        });
                        syncLorries()
                        printBill(data);

                    } else {
                        swal({
                            title: msg,
                            text: 'Error Code: ' + TrxStsCode,
                            icon: "warning",
                        });

                    }
                })
        } else {

        }
    })



    function changeTransfer(method) {
        const swiftCode = document.querySelector('#swiftCode')
        if (method == 'BankTransfer') {
            swiftCode.removeAttribute('disabled')
        } else {
            swiftCode.setAttribute('disabled', 'disabled')

        }
    }

    function calculateDate(days) {
        const date = new Date();

        date.setDate(date.getDate() + Number(days));
        const expiryDate = `${date.getDate()}-${date.toLocaleString('default', { month: 'long' })}-${date.getFullYear()}`

        document.querySelector('#expiryDate').value = expiryDate
    }


    function printBill(billData) {
        const {
            status,
            bill,
            heading,
            qrCodeObject,
            token,

        } = billData
        // console.log(qrCodeObject)
        console.log(heading)
        // console.log(token)

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

        console.log('heading')
        console.log(heading)
        document.querySelector('#heading').textContent = heading
        document.querySelector('#billDetails').innerHTML = ''
        document.querySelector('#billDetails').innerHTML = bill
        qrCode.append(document.getElementById("canvas"));
        $('#printModal').modal({
            open: true,
            backdrop: 'static'
        })



    }
    //*************************************************************************** */


    //*********************************************************** */




    //=================Taking all lorry details and store to sandy lorries table====================

    const addSblButton = document.querySelector('#addSblButton')
    addSblButton.addEventListener('click', (e) => {
        e.preventDefault()
        const Hash = document.querySelector('#customerId')

        // console.log(Hash.value);

        if (!Hash) {
            swal({
                title: 'Please Select Customer First!',
                icon: "warning",
                timer: 8500
            });
        } else {
            $('.Sbl-modal').modal({
                show: true,
                focus: true,
                backdrop: 'static'

            })
        }


    })

    $('#addLoryForm').validate()


    const addLoryForm = document.querySelector('#addLoryForm')
    addLoryForm.addEventListener('submit', e => {
        e.preventDefault()
        if ($('#addLoryForm').valid()) {

            submitInProgress(e.submitter)
            const formData = new FormData(addLoryForm)
            // Get the coordinates using the getCoordinates() function
            getCoordinates()
                .then((coords) => {
                    // Add the coordinates to the FormData object

                    console.log(coords)
                    formData.append("latitude", coords.latitude);
                    formData.append("longitude", coords.longitude);
                    formData.append("sblCustomerHash", document.querySelector('#customerId').value);

                    // Make the POST request to the server
                    fetch("<?= base_url() ?>registerLorry", {
                            method: "POST",
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": document.querySelector(".token").value,
                            },
                            body: formData,
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            const {
                                token,
                                status,
                                customer,
                                msg,
                            } = data;
                            document.querySelector(".token").value = token;

                            submitDone(e.submitter)
                            if (status == 1) {
                                syncLorries()
                                addLoryForm.reset()
                                $('.Sbl-modal').modal('hide')

                                swal({
                                    title: msg,
                                    icon: "success",
                                });

                            } else {
                                swal({
                                    title: msg,
                                    icon: "warning",
                                });
                            }
                            console.log(data);
                        });
                })
                .catch((error) => {
                    // console.log(error.message);
                    swal({
                        title: error.message,
                        text: 'Please enable location services manually and try again.',
                        icon: "warning",
                        // timer: 42500
                    });
                })
        }
    })


    //=================check if there is any customer lorry which is not in the transaction table====================
    function syncLorries() {

        const totalBilledAmount = document.querySelector('#billedAmount')
        const billBlock = document.querySelector('#billBlock')
        let total = 0
        const hashString = document.querySelector('#customerId')
        const taskName = document.querySelector('#taskName')

        if (!hashString) {

            swal({
                title: 'Please Select Customer First!',
                // text: "You clicked the button!",
                icon: "warning",
                timer: 2500
            });
        } else {

            if (!taskName.value) {

                swal({
                    title: 'Select Task First!',
                    // text: "You clicked the button!",
                    icon: "warning",
                    timer: 2500
                });
            } else {
                fetch('<?= base_url() ?>getUnpaidLorries', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json;charset=utf-8',
                            "X-Requested-With": "XMLHttpRequest",
                            'X-CSRF-TOKEN': document.querySelector('.token').value
                        },

                        body: JSON.stringify({
                            hashString: hashString.value,
                            taskName: taskName.value
                        }),

                    }).then(response => response.json())
                    .then(data => {
                        const {
                            lorries,
                            billedAmount,
                            token,
                            status,
                            msg,
                            vehicles
                        } = data

                        if (status == 0) {
                            swal({
                                title: msg,
                                icon: "warning",
                                timer: 2500
                            });
                        }
                        document.querySelector('.token').value = token
                        document.querySelector('#lorries').innerHTML = lorries
                        $('[data-toggle="tooltip"]').tooltip()
                        // Convert the string to an integer
                        const billAmt = parseInt(billedAmount.replace(/,/g, ''));


                        if (taskName.value == 'Inspection') {
                            totalBilledAmount.removeAttribute('readonly')

                        } else {
                            totalBilledAmount.setAttribute('readonly', 'readonly')

                        }
                        totalBilledAmount.value = billedAmount
                        if (billAmt > 0) {
                            billBlock.style.display = 'block'
                        } else if (billAmt === 0 && (taskName.value == 'Inspection' || taskName.value == 'Verification' || taskName.value == 'Reverification') && vehicles === 1) {

                            billBlock.style.display = 'block'

                        } else {
                            billBlock.style.display = 'none'

                        }
                        //console.log(data)
                    })
            }








        }


    }

    function editLorry(vehicleId) {


        fetch("<?= base_url() ?>editLorry", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector(".token").value,
                },
                body: JSON.stringify({
                    vehicleId: vehicleId
                }),
            })
            .then((response) => response.json()).then(data => {
                const {
                    token,
                    status,
                    lorry,
                    trailers
                } = data
                document.querySelector('.token').value = token
                if (status == 1) {
                    document.querySelector('#lorryInfo').innerHTML = lorry
                    document.querySelector('#lorryTrailers').innerHTML = trailers

                    $('#updateModal').modal('show')

                }



            })
    }


    const updateLorryForm = document.querySelector('#updateLorryForm');

    $('#updateLorryForm').validate();

    updateLorryForm.addEventListener('submit', e => {
        e.preventDefault();

        fetch("updateLorryData", {
                method: "POST",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector(".token").value,
                },
                body: new FormData(updateLorryForm),
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                const {
                    token,
                    status,
                    msg
                } = data;
                document.querySelector('.token').value = token;

                if (status == 1) {
                    $('#updateModal').modal('hide')
                    syncLorries();
                }

                swal({
                    title: msg,
                    icon: status == 1 ? 'success' : 'warning',
                });
            });
    });






    function deleteLorry(vehicleId) {



        swal({
                title: "Are You Sure You Want To Delete The Record?",
                // text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: "warning",
                buttons: true,
                buttons: ["No", "Yes"],
                dangerMode: true,
            })
            .then((willRun) => {

                if (willRun) {

                    fetch("<?= base_url() ?>deleteLorry", {
                            method: "POST",
                            headers: {
                                'Content-Type': 'application/json;charset=utf-8',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector(".token").value,
                            },
                            body: JSON.stringify({
                                vehicleId: vehicleId
                            }),
                        })
                        .then((response) => response.json()).then(data => {
                            const {
                                token,
                                status,
                                msg
                            } = data
                            document.querySelector('.token').value = token
                            if (status == 1) syncLorries()

                            swal({
                                title: msg,
                                icon: status == 1 ? 'success' : 'warning',
                            });
                        })


                } else {
                    swal("Record Not Deleted");
                }
            });



    }








    //=====================================
</script>

<?= $this->endSection(); ?>