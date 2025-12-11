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

        <?= view('Components/bill') ?>
        <?= view('Components/ClientsBlock') ?>


        <div class="">
            <div class="row">




                <div class="col-md-12">
                    <div class="card ">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6 flex">
                                    <button type="button" class="btn btn-primary btn-sm" id="addVtcButton"><i class="far fa-plus"></i> Add Vehicle</button>
                                    <button type="button" class="btn btn-success btn-sm" onclick="syncVehicles()"><i class="far fa-list"></i>
                                        List Vehicles</button>

                                </div>
                                <!-- <div class="col-md-6">
                                    <div class="input-group mt-1">
                                        <input class="form-control" type="text" placeholder="Search by plate number" id="licensePlate" oninput="this.value = this.value.toUpperCase().replaceAll(/\s/g,'')">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary btn-sm" id="plateSearch"><i class="far fa-search"></i>
                                                Search</button>
                                        </div>
                                    </div>
                                </div> -->

                            </div>
                            <!-- /.card-tools -->
                        </div>
                        <!-- /.card-header -->



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
                                                            <label for="">Compartment Number</label>
                                                            <select class="form-control" name="compNumber" id="compNumber">

                                                            </select>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="">Tank Top</label>
                                                        <input type="number" name="tankTop"  id="tankTop" class="form-control" placeholder="Tank Top">

                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="">Stamp Number</label>
                                                        <input type="text" name="stampNumber" value="<?= $stampNumber ?>" id="stampNumber" class="form-control" placeholder="Stamp Number" required readonly>

                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table table-sm" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Litre</th>
                                                        <th>mm</th>
                                                        <th>
                                                            <button type="button" class="btn btn-primary btn-sm" onclick="addRow('dataRows')">
                                                                <i class="far fa-plus"></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="dataRows">
                                                    <tr>
                                                        <td>
                                                            <input type="number" name="litres[]"  id="55" class="form-control litre" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="millimeters[]"  id="10" class="form-control mm millimeters" required>
                                                        </td>
                                                        <td>

                                                            <button type="button" class="btn btn-dark btn-sm" onclick="removeRow(this)">
                                                                <i class="far fa-minus"></i>
                                                            </button>
                                                        </td>
                                                    </tr>



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
                                                        <input type="number" name="tankTop"  id="tankTop2" class="form-control" placeholder="Tank Top" required>

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
                        <div class="card-body">
                            <div class="form-group" id="noCompartments">


                            </div>
                            <div id="vehicleDetails"></div>

                            <ul class="list-group" id="customerVehicles">


                            </ul>

                            <?= $this->include('Components/Vtc/vtcTechnicalDetails'); ?>
                        </div>
                        <!-- /.card-body -->

                        <!-- /.card-footer -->
                    </div>
                    <!-- /.card -->
                </div>

            </div>
            </form>
        </div>



        <!-- /.card -->


    </div>
    <!-- /.card -->

    </div>
    </div>


    <script>
        function calculateTotal(billAmt) {
        // Remove non-digit characters and commas
        let total = billAmt.value.replace(/\D/g, '').replace(/,/g, '');

        // Format the total with commas as thousands separators
        let formattedTotal = new Intl.NumberFormat().format(total);

        // Set the formatted value back to the input field
        billAmt.value = formattedTotal;

       
    }

        //adding a row
        function addRow(id) {
            const randomId = Math.floor(Math.random() * (20000 - 1000 + 1)) + 1000
            let tankTop = 0
            if (id == 'extraRows') {

                tankTop = document.querySelector('#tankTop2').value
            } else {

                tankTop = document.querySelector('#tankTop').value
            }
            console.log(tankTop)

            $(`#${id}`).append(`
             <tr>
                 <td>
                 <input type="text" name="id[]"  value="x"  id="id" class="form-control" required hidden>
                     <input type="number" name="litres[]"  id="litre_${randomId}" class="form-control litre" required>
                 </td>
                 <td>
                     <input type="number" name="millimeters[]"  id="mm_${randomId}" class="form-control mm millimeters"  required>
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




        //remove row from the dom
        function removeRow(row) {
            row.parentElement.parentElement.remove();
            calculateTotalLitres()

        }

        //remove row from the dom
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


        $('#calibrationUpdateForm').validate()

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



        function processChartDetails(chart) {
            const chartResults = document.querySelector('#chartResults')
            if (chart != '') {
                chartResults.style.display = 'block'

                if (chart.complete == true) $('#addChart').modal('hide')

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

        function calculateTotalAmount(vehicles) {
            const task = document.querySelector('#taskName').value
            const billBlock = document.querySelector('#billBlock')
            const vehicleAmount = document.querySelectorAll('.vehicleAmount')
            let total = 0



            for (amount of vehicleAmount) {
                total += Number(amount.value.replace(/\D/g, '').replace(/,/g, ''))
            }
            document.querySelector('#totalAmount').value = new Intl.NumberFormat().format(total)


            if (task == 'Verification' || task == 'Reverification' && total > 0) {
                billBlock.style.display = 'block'
            } else if (total == 0 && task == 'Inspection') {
                if (vehicles == 1) {
                    billBlock.style.display = 'block'


                } else {
                    billBlock.style.display = 'none'

                }

            } else {

                billBlock.style.display = 'none'
            }

        }

        function removeItem(button) {
            const parent = button.parentNode.parentNode
            parent.remove()
            calculateTotalAmount()
        }

        function getItemAmount(input) {
            let total = 0
            input.value = input.value.replace(/\D/g, '').replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',')
            let currentInputValue = input.value.replace(/,/g, '')
            const amounts = document.querySelectorAll('.vehicleAmount')
            // console.log(amounts)
            for (amount of amounts) {

                total += Number(amount.value.replace(/\D/g, '').replace(/,/g, ''))
            }

            document.querySelector('#totalAmount').value = new Intl.NumberFormat().format(total)

        }



        window.addEventListener('afterprint', (event) => {
            location.reload
        });
    </script>

</section>

<?= $this->endSection(); ?>