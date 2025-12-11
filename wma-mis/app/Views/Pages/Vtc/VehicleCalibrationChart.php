<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark"><?= $page['heading'] ?></h4>
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
<!-- /.content-header -->
<!-- Main content -->
<section class="content body">
    <div class="container-fluid">
        <!-- <div class="card">
            <div class="card-header">
           
            

            </div>
        </div> -->
        <form id="searchVehicleForm">
            <div class="container ">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="input-group search-container">
                            <input type="text" name="trailerPlateNumber" id="trailerPlateNumber" class="form-control" placeholder="Trailer/Tank Plate Number">
                            <div class="input-group-append">
                                <button class="btn btn-primary search-button" type="submit">
                                    <i class="far fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>

        <div id="vehicleDetails" class="mt-3">



        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addChart" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Calibration Chart</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="calibrationForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="">Select Compartment</label>
                                        <select class="form-control" name="compNumber" id="compNumber">

                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Tank Top</label>
                                    <input type="number" name="tankTop" min="0" id="tankTop" class="form-control" placeholder="Tank Top">

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Stamp Number</label>
                                    <input type="text" name="stampNumber" value="<?= $stampNumber ?>" id="stampNumber" class="form-control" placeholder="Stamp Number" required readonly>

                                </div>
                            </div>
                        </div>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Litre</th>
                                    <th>mm</th>
                                    <th style="text-align:right;padding-right:1.4rem">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="addRow('dataRows')">
                                            <i class="far fa-plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="dataRows">



                            </tbody>

                        </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                        Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- add chart end -->

    <!-- update chart -->
    <div class="modal fade" id="updateChart" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Calibration Chart</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="calibrationUpdateForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="">Select Compartment</label>
                                        <input class="form-control" name="compNumber" id="compartmentNo" readonly>

                                        </input>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Tank Top</label>
                                    <input type="number" name="tankTop" id="tankTop2" class="form-control" placeholder="Tank Top">

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Stamp Number</label>
                                    <input type="text" name="stampNumber" value="<?= $stampNumber ?>" id="stampNumber" class="form-control" placeholder="Stamp Number" required readonly>

                                </div>
                            </div>
                        </div>
                        <div id="compartment">

                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                        Update
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- update chart -->

    <div class="card">
        <div class="card-header">CALIBRATION CHART

        </div>
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
            <div id="chartDownload" style="float:right;">
            </div>
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

</section>

<script>
    function addRow(id) {
        const randomId = Math.floor(Math.random() * (20000 - 1000 + 1)) + 1000
        let tt = id == 'extraRows' ? 'tankTop2' : 'tankTop'
        const tankTop = document.querySelector(`#${tt}`).value

        $(`#${id}`).append(`
             <tr>
                 <td>
                 <input type="text" name="id[]" min="0" value="x"  id="id" class="form-control" required hidden>
                     <input type="number" name="litres[]" min="0" max="10000000"  id="litre_${randomId}" class="form-control litre" required>
                 </td>
                 <td>
                     <input type="number" name="millimeters[]" min="0"  id="mm_${randomId}" class="form-control mm millimeters"  required>
                 </td>
                 <td>
                
                     <button type="button" class="btn btn-dark btn-sm" onclick="removeRow(this)">
                         <i class="far fa-minus"></i>
                     </button>
                 </td>
            </tr>
            `)

    }

    //calc total litters
    function calculateTotalLitres() {

        let total = 0

        const litres = document.querySelectorAll('.litre')

        for (liter of litres) {
            total += Number(liter.value)
        }

    }

    function deleteRow(button, id) {

        const tr = button.parentElement.parentElement

        swal({
                title: "Do you want to delete this data?",
                // text: 'Control Number: ' + controlNumber,
                icon: "warning",
                buttons: true,
                buttons: ["No", "Yes"],
                dangerMode: true,
            })
            .then((willRun) => {

                if (willRun) {

                    fetch('deleteCompartmentData', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json;charset=utf-8',
                                "X-Requested-With": "XMLHttpRequest",
                                'X-CSRF-TOKEN': document.querySelector('.token').value
                            },

                            body: JSON.stringify({
                                id
                            }),

                        }).then(response => response.json())
                        .then(data => {
                            const {

                                token,
                                status,
                                msg,


                            } = data
                            document.querySelector('.token').value = token
                            if (status == 1) {
                                tr.remove()

                                swal({
                                    title: msg,
                                    icon: "success",
                                    // timer: 18500
                                });

                            } else {

                                swal({
                                    title: msg,
                                    icon: "warning",
                                    // timer: 18500
                                });
                            }
                            console.log(data)
                        })

                } else {

                    swal("Data is not deleted");
                }
            });



    }





    //remove row from the dom
    function removeRow(row) {
        row.parentElement.parentElement.remove();
        calculateTotalLitres()

    }

    function editCompartment(vehicleId, compartmentNumber) {
        fetch('editChart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                "X-Requested-With": "XMLHttpRequest",
                'X-CSRF-TOKEN': document.querySelector('.token').value
            },

            body: JSON.stringify({
                vehicleId,
                compartmentNumber
            }),

        }).then(response => response.json()).then(data => {
            const {
                token,
                compartment,
                compartmentNo,
                tankTop,
                msg
            } = data
            document.querySelector('.token').value = token
            document.querySelector('#compartmentNo').value = compartmentNo
            document.querySelector('#tankTop2').value = tankTop
            document.querySelector('#compartment').innerHTML = compartment
            $('#updateChart').modal('show')
            console.log(data)
        })
    }



    $('#calibrationForm').validate()

    const calibrationForm = document.querySelector('#calibrationForm')
    calibrationForm.addEventListener('submit', (e) => {
        e.preventDefault()
        if ($('#calibrationForm').valid()) {
            submitInProgress(e.submitter)
            const formData = new FormData(calibrationForm)
            formData.append('vehicleId', document.querySelector('#vehicleId').value)
            formData.append('totalCompartments', document.querySelector('#totalCompartments').value)
            fetch('createChart', {
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
                        token,
                        chart,
                        compartmentsMenu,
                        status,
                        allFilled,
                        msg
                    } = data
                    document.querySelector('.token').value = token

                    console.log(allFilled)
                    submitDone(e.submitter)
                    if (status == 1) {
                        document.querySelector('#tankTop').value = ''
                        if (allFilled == 1) {
                            document.querySelector('#chartRow').remove()
                        }
                        // document.querySelector('#dataRows').remove()
                        document.querySelector('#dataRows').innerHTML = ''

                        document.querySelector('#compNumber').innerHTML = compartmentsMenu
                        processChartDetails(chart)
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




                    // console.log(data)
                })
        } else {
            return false
        }
    })


    //updating chart data
    const calibrationUpdateForm = document.querySelector('#calibrationUpdateForm')
    calibrationUpdateForm.addEventListener('submit', (e) => {
        e.preventDefault()
        if ($('#calibrationUpdateForm').valid()) {
            submitInProgress(e.submitter)
            const formData = new FormData(calibrationUpdateForm)

            fetch('updateChart', {
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
                        token,
                        chart,
                        status,
                        // allFilled,
                        msg
                    } = data
                    document.querySelector('.token').value = token

                    console.log(data)
                    submitDone(e.submitter)

                    if (status == 1) {
                        calibrationUpdateForm.reset()
                        $('#updateChart').modal('hide')
                        document.querySelector('#tankTop').value = ''

                        // document.querySelector('#dataRows').remove()
                        document.querySelector('#dataRows').innerHTML = ''


                        processChartDetails(chart)
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




                    // console.log(data)
                })
        } else {
            return false
        }
    })









    $('#searchVehicleForm').validate()
    const searchVehicleForm = document.querySelector('#searchVehicleForm')
    searchVehicleForm.addEventListener('submit', e => {
        e.preventDefault()
        const formData = new FormData(searchVehicleForm)

        fetch('searchVehicleTank', {
            method: 'POST',

            headers: {
                "X-Requested-With": "XMLHttpRequest",
                'X-CSRF-TOKEN': document.querySelector('.token').value
            },

            body: formData
        }).then(res => res.json()).then(data => {
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
        })

    })

    function getVehicleDetails(vehicleId) {


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
                    console.log(data)
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




                    console.log('V data')
                })
        }


    }

    function processChartDetails(chart) {
        const chartResults = document.querySelector('#chartResults')
        if (chart != '') {
            chartResults.style.display = 'block'



            document.querySelector('#chartDownload').innerHTML = chart.button

            document.querySelector('#upperChart').innerHTML = chart.upperChart
            document.querySelector('#lowerChart').innerHTML = chart.lowerChart
            document.querySelector('#fillOrder').textContent = chart.fillOrder
            document.querySelector('#emptyOrder').textContent = chart.emptyOrder

            document.querySelector('#plateNumber').textContent = chart.plateNumber
            document.querySelector('#customer').textContent = chart.customer
            document.querySelector('#chartNumber').textContent = chart.chartNumber
            document.querySelector('#nextVerification').textContent = chart.nextVerification
        } else {
            chartResults.style.display = 'none'
        }

    }
</script>

<?= $this->endSection(); ?>