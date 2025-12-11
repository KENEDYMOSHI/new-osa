<!-- <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#plateModal"><i
        class="far fa-search"></i> Search </button> -->



<?= view('Components/Vtc/searchVtc') ?>



<div class="card">
    <div class="card-header">CALIBRATION CHART</div>
    <div class="card-body" id="chartResults" style="display: none;">
        <table class="table">


            <tbody>
                <tr class="row" id="upperChart"></tr>


            </tbody>



        </table>
        <table class="table text-bold">
            <tr class=" row">
                <td class="col-md-6">The dipsticks were marked</td>
                <td class="col-md-6">
                    <span id="chartNumber"></span></br>
                    <span id="customer"></span></br>
                    <span id="plateNumber"></span></br>
                </td>
            </tr>

            <tr class="row" id="lowerChart">


            </tr>
        </table>
        <span class="text-bold">The tank was verified &nbsp; 1. On a level plane 2 . against approved measure</><br>
            NOTE (a) the compartments should be filled in the order &nbsp; <span id="fillOrder"></span> and emptied in the order &nbsp; <span id="emptyOrder"></span> <br>
            (b) THIS TANK SHALL BE VERIFIED AGAIN IF SUSPECTED OF GIVING INCORRECT MEASUREMENTS BUT IN ANY CASE NOT LATER THAN <span id="nextVerification"></span>

    </div>
    <div class="card-footer">
        <div id="chartDownload" style="float:right;"></div>
    </div>
    <!-- <table class="table text-bold">
        <tr>
            <td>
                DATE: 07 Apr 2022 <br>
                <span>DISTRIBUTION OF COPIES:-</span> <br>
                1. ASTRA LOGISTICS <br>
                1. WMA PO BOX 313 DAR ES SALAAM
            </td>
            <td class="">
                <span>REGIONAL MANAGER</span> <br>
                ILALA<br>
            </td>
            <td>
                <div class="p-1" style="border:1px solid gray;height:130px; width:130px">
                    WMA OFFICIAL SEAL
                </div>
            </td>
        </tr>
    </table> -->
</div>





<div class="card mt-3">
    <div class="card-header row">
        <div class="form-group col-md-3">
            <label for="">Task</label>
            <select class="form-control" name="taskName" id="taskName">
                <option value="">--Select Task --</option>
                <option value="Verification">Verification</option>
                <option value="Reverification">Reverification</option>
                <option value="Inspection">Inspection</option>
            </select>
        </div>
        <div class="col-md-3 mt-4">
            <button type="button" class="btn btn-primary btn-sm " onclick="getCalibratedTanks()">
                <i class="far fa-eye" aria-hidden="true"></i> View Calibrated
            </button>
        </div>
    </div>

    <div class="card-body">
        <form id="vtvDataForm">
            <div id="getCalibratedTanks"></div>

            <!-- including receipt modal -->


    </div>


</div>

<div class="card" id="billBlock" style="display: none;">

    <div class="card-header">BILL DETAILS</div>
    <div class="card-body">
        <?= view('Components/billOptions') ?>
    </div>
    <div class="card-footer">
        <button type="submit" id="submit" class="btn btn-primary btn-sm ">
            <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
            Submit
        </button>
    </div>
    </form>
</div>
</div>



<div class="vtc">

</div>


<div class="modal vtc-modal fade" id="add-vtc">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Vehicle Tank</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="vtvForm">
                <div class="modal-body">

                    <div class="row">

                        <div class="form-group col-md-6">
                            <label class="must" for="">Activity</label>
                            <select class="form-control" name="task" id="task" required>
                                <option disabled selected>-Select Activity-</option>
                                <option value="Verification">Verification</option>
                                <option value="Reverification">Reverification</option>
                                <option value="Inspection">Inspection</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tin Number </label>
                                <input type="text" class="form-control tin " name="tinNumber" id="tinNumber" placeholder="Enter Tin Number">


                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="must" for="">Visual Inspection</label>
                            <select class="form-control" name="visualInspection" id="visualInspection" required onchange="evaluateInstrument(this.value)">
                                <option disabled selected>-Select Status-</option>
                                <option value="Pass">Pass</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Condemned">Condemned</option>
                                <option value="Adjusted">Adjusted</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="must" for="">Testing</label>
                            <select class="form-control" name="testing" id="testing" required onchange="evaluateInstrument(this.value)">
                                <option disabled selected>-Select Status-</option>
                                <option value="Pass">Pass</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Condemned">Condemned</option>
                                <option value="Adjusted">Adjusted</option>
                            </select>
                        </div>










                        <div class="form-group col-md-6">
                            <div class="form-group">
                                <label for="">Driver's Full Name</label>
                                <input type="text" name="driverName" id="driverName" class="form-control" placeholder="Driver's Full Name">

                            </div>



                        </div>
                        <div class="form-group col-md-6">
                            <label for="my-input">Driver's License</label>

                            <div class="input-group">
                                <input class="form-control license " name="driverLicense" id="driverLicense" type="text" placeholder=" Enter Driver's License" value="">

                            </div>


                        </div>



                        <div class="form-group col-md-6">
                            <label class="must">Vehicle Brand</label>
                            <input type="text" class="form-control " name="vehicleBrand" id="vehicleBrand" placeholder="Enter Brand" required>


                        </div>
                        <div class="form-group col-md-6">
                            <label class="must">Number Of Compartments</label>
                            <select class="form-control" name="compartments" id="compartments" required>
                                <!-- <option value=""></option> -->
                                <?php for ($i = 1; $i <= 10; $i++) : ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>

                            </select>


                        </div>

                        <div class="form-group col-md-6">
                            <label class="">Hose Plate Number </label>
                            <input type="text" class="form-control " name="hosePlateNumber" id="hosePlateNumber" placeholder="Hose Plate Number" oninput="this.value = this.value.toUpperCase().replaceAll(/\s/g,'')">

                        </div>
                        <div class="form-group col-md-6">
                            <label class="must">Trailer Plate Number </label>
                            <input type="text" class="form-control " name="trailerPlateNumber" id="trailerPlateNumber" placeholder="Trailer Plate Number" oninput="debounceHandleInput(event)" required>

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
                        <div class="col-md-12" id="chartAlert" style="display: none;">
                            <input type="text" class="form-control" name="oldVehicleId" id="oldVehicleId" value="" hidden>
                            <div class="alert " role="alert" style="background: #C9571A;color:white;">
                                <strong> <i class="far fa-info-circle"></i> This Vehicle Has a Previous Chart. Once Data is Saved Chart Data Will Be Used, Please Cross Check The Parameters To Confirm If Any Changes Are Needed</strong>

                            </div>
                        </div>
                        <div class="form-group col-md-6">

                            <label>Calibration Sticks</label><br>
                            <div class="icheck-primary d-inline">
                                <input class="form-check-input " name="includeSticks" id="includeSticks" type="checkbox" value="1">

                                <label class="form-check-label" for="includeSticks">Include Calibration Sticks </label>
                            </div>

                        </div>
                        <div class="form-group col-md-6">

                            <label> Calibration Chart</label><br>
                            <div class="icheck-primary d-inline">
                                <input class="form-check-input " name="skipChart" id="skipChart" onchange="skipCalibrationChart(this)" type="checkbox" value="1" >

                                <label class="form-check-label" for="skipChart">Skip Calibration Chart </label>
                            </div>

                        </div>






                        <div class="form-group col-md-12" id="capacityBlock" style="display: none;">
                            <label class="must">Capacity</label>
                            <input type="text" class="form-control " name="capacity" id="capacity" placeholder="Tank Capacity">

                        </div>

                    </div class="form-group col-md-12">
                    <div class="form-group">
                        <label for="my-textarea">Remark</label>
                        <textarea id="remark" name="remark" class="form-control " name="" rows="3"></textarea>
                    </div>
                    <div class="icheck-primary d-inline mb-2">
                        <input class="form-check-input label-check" name="hasPenalty" id="fine" type="checkbox" onchange="toggleFine(this)">

                        <label class="form-check-label" for="fine">Include Fine & Penalty</label>
                    </div>
                    <div class="col-md-12 mt-2 mb-2" id="penaltyBlock" style="display: none;">
                        <div class="form-group">
                            <label class="must" for="">Penalty Amount<span class="text-danger"></span></label>
                            <input type="text" name="penaltyAmount" id="penaltyAmount" class="form-control" oninput="calculateTotal(this)" required>

                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="save-vtc">
                            <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                            Save
                        </button>

            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- Search customer -->

<script>
    function skipCalibrationChart(chartBox) {
        const capacityBlock = document.querySelector('#capacityBlock')
        if (chartBox.checked == true) {
            capacityBlock.style.display = 'block'
        } else {
            capacityBlock.style.display = 'none'

        }
    }

    function toggleFine(box) {

        const penaltyBlock = document.querySelector('#penaltyBlock');
        if (box.checked == true) {
            penaltyBlock.style.display = 'block';
        } else {
            document.querySelector('#penaltyAmount').value = '';
            penaltyBlock.style.display = 'none';
        }
    }

    let debounceTimer;

    function debounceHandleInput(event) {
        clearTimeout(debounceTimer); // Clear the previous timer
        debounceTimer = setTimeout(() => handleInput(event), 500); // Delay by 500ms
    }

    async function handleInput(event) {
        const input = event.target;

        // Transform input: uppercase and remove spaces
        input.value = input.value.toUpperCase().replace(/\s/g, '');

        // Get the value to check
        const plateNumber = input.value;

        // Avoid sending a request if the input is empty
        if (plateNumber === '') return;

        try {
            // Send fetch request to check the plate number
            const response = await fetch('/checkPlateNumber', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    plateNumber
                })
            });

            // Handle the response
            const result = await response.json();
            const {
                status,
                capacity,
                oldVehicleId
            } = result;

            const chartAlert = document.querySelector('#chartAlert');
            const oldId = document.querySelector('#oldVehicleId');
            const skipChart = document.querySelector('#skipChart');

            if(status ==1){
               chartAlert.style.display = 'block';
               oldId.value = oldVehicleId;
               skipChart.setAttribute('disabled', 'disabled');

            }else{
                chartAlert.style.display = 'none';
                oldId.value = '';
                skipChart.removeAttribute('disabled');
            }
            console.log(result);

            // You can add additional logic here, e.g., display a message to the user
        } catch (error) {
            console.error('Error checking plate number:', error);
        }
    }


    function evaluateInstrument(value) {
        // Get the values from both select boxes
        const visualInspectionValue = document.querySelector("#visualInspection").value;
        const testingValue = document.querySelector("#testing").value;
        const deadline = document.querySelector("#deadline")



        // Check if both values are "Pass"
        if (visualInspectionValue === "Pass" && testingValue === "Pass") {


            deadline.style.display = "none";
        } else if (visualInspectionValue === "Rejected" || testingValue === "Rejected") {


            deadline.style.display = "block";
        } else {
            deadline.style.display = "none";

        }
    }

    function calculateDeadlineDate(days) {
        const date = new Date();

        date.setDate(date.getDate() + Number(days));
        const expiryDate = `${date.getDate()}-${date.toLocaleString('default', { month: 'long' })}-${date.getFullYear()}`

        document.querySelector('#repairDeadline').value = expiryDate
    }


    function calculateDate(days) {
        const date = new Date();

        date.setDate(date.getDate() + Number(days));
        const expiryDate = `${date.getDate()}-${date.toLocaleString('default', { month: 'long' })}-${date.getFullYear()}`

        document.querySelector('#expiryDate').value = expiryDate
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
                //             latitude: -6.8294289,
                //             longitude: 39.2605616
                //         });
            }
        });
    }
    //=================Publish vtc data with vehicle id customer hash and control number====================


    const vtvDataForm = document.querySelector('#vtvDataForm')

    vtvDataForm.addEventListener('submit', (e) => {
        e.preventDefault()
        submitInProgress(e.submitter)
        const formData = new FormData(vtvDataForm)
        formData.append('task', document.querySelector('#taskName').value)
        fetch('publishVtcData', {
                method: 'POST',
                headers: {
                    // 'Content-Type': 'application/json;charset=utf-8',
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },

                body: formData,

            }).then(response => response.json())
            .then(data => {
                const {
                    status,
                    token,
                    msg,
                    TrxStsCode
                } = data

                document.querySelector('.token').value = token

                submitDone(e.submitter)
                if (status == 1) {
                    printBill(data)
                    vtvDataForm.reset()
                    syncVehicles()
                    getCalibratedTanks()

                } else {
                    submitDone(e.submitter)

                    swal({
                        title: msg,
                        text: TrxStsCode,
                        icon: "warning",
                        // timer: 7500
                    });
                }

                console.log(msg)
            })
    })




    function changeTransfer(method) {
        const swiftCode = document.querySelector('#swiftCode')
        if (method == 'BankTransfer') {
            swiftCode.removeAttribute('disabled')
        } else {
            swiftCode.setAttribute('disabled', 'disabled')

        }
    }



    function printBill(billData) {
        const {
            status,
            bill,
            heading,
            qrCodeObject,
            token,

        } = billData
        console.log('HEADING.......')
        console.log(heading)
        console.log(token)

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

        console.log(bill)
        document.querySelector('#heading').textContent = heading
        document.querySelector('#billDetails').innerHTML = bill
        document.querySelector('#canvas').innerHTML = ''
        qrCode.append(document.getElementById("canvas"));
        $('#printModal').modal({
            open: true,
            backdrop: 'static'
        })



    }


    function syncVehicles() {

        const totalAmount = document.querySelector('#totalAmount')
        let total = 0
        const hashValue = document.querySelector('#customerId')
        const billBlock = document.querySelector('#billBlock')

        if (hashValue) {
            $.ajax({
                type: "POST",
                url: "getUnpaidVehicles",
                data: {
                    // csrf_hash:document.querySelector('.token').value, vehicleDetails
                    hashValue: hashValue.value
                },

                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                },

                success: function(vehicles) {
                    //  console.log(vehicles)
                    // const {status,token,}

                    document.querySelector('.token').value = vehicles.token
                    if (vehicles.status == 0) {
                        document.querySelector('#vehicleDetails').innerHTML = ''
                    }
                    $('#noCompartments').html(vehicles.compartmentDropdown)


                }
            });
        } else {
            swal({
                title: 'Please Select Customer First!',
                // text: "You clicked the button!",
                icon: "warning",
                timer: 2500
            });
        }


    }




    function getVehicleDetails(vehicleId) {
        document.querySelector('#calibrationForm').reset()
        document.querySelector('#dataRows').innerHTML = ''

        if (vehicleId != '') {
            fetch('getVehicleDetails', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json;charset=utf-8',
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSRF-TOKEN': document.querySelector('.token').value
                    },

                    body: JSON.stringify({
                        vehicleId: vehicleId,

                    }),

                }).then(response => response.json())
                .then(data => {
                    console.log(data.data)
                    const {
                        token,
                        chart,
                        noOfCompartments,
                        vehicle,

                    } = data

                    // console.log(chart)
                    processChartDetails(chart)

                    document.querySelector('.token').value = token
                    document.querySelector('#vehicleDetails').innerHTML = vehicle
                    document.querySelector('#compNumber').innerHTML = noOfCompartments




                    // console.log(data)
                })
        }


    }


    const addVtcButton = document.querySelector('#addVtcButton')
    addVtcButton.addEventListener('click', (e) => {
        console.log(26 + 666 + 52 + 9986)
        e.preventDefault()
        const Hash = document.querySelector('#customerId')

        if (Hash) {
            $('#add-vtc').modal({
                show: true,
                focus: true,
                backdrop: 'static'

            })
        } else {
            checkCustomer()
        }



    })

    $('#vtvForm').validate()
    const vtvForm = document.querySelector('#vtvForm')

    vtvForm.addEventListener('submit', (e) => {
        e.preventDefault()
        if ($('#vtvForm').valid()) {
            submitInProgress(e.submitter)
            const formData = new FormData(vtvForm)
            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser. Please enable location services manually and try again.");
                return;
            }
            formData.append('customerId', document.querySelector('#customerId').value)

            getCoordinates().then(coords => {
                formData.append("latitude", coords.latitude);
                formData.append("longitude", coords.longitude);
                fetch('newVehicleTank', {
                        method: 'POST',
                        headers: {
                            // 'Content-Type': 'application/json;charset=utf-8',
                            "X-Requested-With": "XMLHttpRequest",
                            'X-CSRF-TOKEN': document.querySelector('.token').value

                        },

                        body: formData,

                    }).then(response => response.json())
                    .then(data => {
                        const {
                            status,
                            token,
                            msg,
                            vehicles
                        } = data

                        document.querySelector('.token').value = token
                        submitDone(e.submitter)
                        if (status == 1) {
                            vtvForm.reset()
                            syncVehicles()
                            document.querySelector('#chartAlert').style.display = 'none'
                            $('#add-vtc').modal('hide')
                            swal({
                                title: msg,
                                icon: "success",
                            });
                        } else {
                            swal({
                                title: msg,
                                icon: "warning",
                                timer: 5500
                            });
                        }
                        console.log(data)
                    })
            }).catch(error => {
                submitDone(e.submitter)
                swal({
                    title: error.message,
                    text: 'Please enable location services manually and try again.',
                    icon: "warning",
                    // timer: 42500
                });
            })
        } else {
            return false
        }

    })

    function getCalibratedTanks() {
        const taskName = document.querySelector('#taskName').value
        if (taskName == '') {
            swal({
                title: 'Select Task',
                icon: "warning",
                timer: 2500
            });
        } else {
            fetch('getCalibratedTanks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json;charset=utf-8',
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSRF-TOKEN': document.querySelector('.token').value
                    },

                    body: JSON.stringify({
                        customerId: document.querySelector('#customerId').value,
                        taskName: taskName,
                    }),

                }).then(response => response.json())
                .then(data => {
                    const {
                        htmlTable,
                        status,
                        token,
                        vehicles,
                        fig
                    } = data
                    document.querySelector('.token').value = token
                    const billDiv = document.querySelector('#billBlock')
                    if (vehicles == 1) {
                        console.log('can be billed')
                        billDiv.style.display = 'block'
                    } else {
                        console.log('CAN NOT')
                        billDiv.style.display = 'none'
                    }
                    document.querySelector('#getCalibratedTanks').innerHTML = htmlTable
                    calculateTotalAmount(vehicles)



                })
        }

    }
</script>