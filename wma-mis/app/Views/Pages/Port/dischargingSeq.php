<?=$this->extend('Layouts/coreLayout');?>
<?=$this->section('content');?>
<script>
const downloadPdf = (shipId) => {
    const downloadBtn = document.querySelector('#downloadDischargingSequence')
    downloadBtn.setAttribute('href', '<?=base_url()?>/downloadDischargingSequence/' + shipId)
}
</script>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?=$page['heading']?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?=base_url()?>/Dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?=$page['heading']?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">
        <?=$this->include('Widgets/shipOptions.php')?>
        <?=$this->include('Components/shipDetails.php')?>
        <?=$this->include('Components/PortUnit/searchShip.php')?>

        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#discharge-modal"
                    aria-pressed="false" autocomplete="off"><i class="far fa-plus-circle" aria-hidden="true"></i>Add
                    Tank</button>
                <button type="button" onclick="checkTanks()" class="btn btn-success btn-sm" id="refreshTimeLogs"><i
                        class="far fa-sync" aria-hidden="true"></i> Check Tanks</button>
                <button type="button" onclick="getDischargingSequence()" class="btn btn-warning btn-sm"
                    id="refreshTimeLogs"><i class="far fa-eye" aria-hidden="true"></i> View</button>

                <h4 id="selectedShip"></h4>
            </div>
            <div class="card-body">






                <div id="DischargingSequence">

                </div>
                <div id="tanks">

                </div>

            </div>
            <div class="card-footer">
                <a id="downloadDischargingSequence" target="_blank" class="btn btn-success btn-sm"><i
                        class="far fa-download" aria-hidden="true"></i>Download</a>
            </div>
        </div>


        <div id="discharge-modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">ADD TANK</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input id="shipId" class="form-control" type="number" hidden>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="my-input">Tank Number</label>
                                <input id="tankNumber" class="form-control" type="text" placeholder="Enter Tank Number"
                                    data-clear>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="my-input">Line Displacement (M<sup>3</sup>)</label>
                                <input id="lineDisplacement" class="form-control" type="text"
                                    placeholder="Enter Line Displacement" data-clear>
                            </div>
                        </div>
                        <div class="row">
                            <div class="timepicker_div form-group col-md-6">
                                <label for="my-input">Time (From)</label>
                                <input type="text" class="form-control timepickerFrom" id="timeFrom"
                                    placeholder="Click To Pick Time" data-clear>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="my-input">Date (From)</label>
                                <input id="dateFrom" class="form-control" type="date" placeholder="Enter Tank Number"
                                    data-clear>
                            </div>

                        </div>


                        <div class="modal-footer">
                            <button type="button" id="saveDischargingSequence"
                                class="btn btn-primary btn-sm">Save</button>
                            <!-- <button type="button" onclick="myFunction()">test</button> -->
                        </div>
                    </div>
                </div>
            </div>




        </div><!-- /.container-fluid -->
        <div id="tankModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h6 class="modal-title" id="my-modal-title">Tank Number</h6> -->
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input id="tankId" class="form-control" type="text" hidden>
                        <div class="row">
                            <div class="timepicker_div form-group col-md-6">
                                <label for="my-input">Time (To)</label>
                                <input id="timeTo" type="text" class="form-control timepickerTo"
                                    placeholder="Click To Pick Time" data-clear>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="my-input">Date (To)</label>
                                    <input id="dateTo" class="form-control" type="date" name="" data-clear>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-sm" onclick="updateTank()">Update</button>
                    </div>
                </div>
            </div>
        </div>
        <!--
        <div id="tankModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="top: 200px;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">

                    </div>
                </div>
            </div>
        </div> -->
        <script>
        $(document).ready(function() {
            $('.timepickerFrom').mdtimepicker();


        });
        </script>

        <script>
        function formatNumber(number) {
            return new Intl.NumberFormat().format(number)
        }


        //=================*********====================







        const saveDischargingSequence = document.querySelector('#saveDischargingSequence');
        saveDischargingSequence.addEventListener('click', (e) => {


            const shipId = document.querySelector('#shipId')
            const tankNumber = document.querySelector('#tankNumber')
            const lineDisplacement = document.querySelector('#lineDisplacement')
            const timeFrom = document.querySelector('#timeFrom')
            const dateFrom = document.querySelector('#dateFrom')


            function validateInput(formInput) {

                if (formInput.value == '') {

                    formInput.style.border = '1px solid #ff6348'
                    return false
                } else {
                    formInput.style.border = '1px solid #2ed573'
                    return true
                }

            }

            if (validateInput(tankNumber) && validateInput(lineDisplacement) && validateInput(timeFrom) &&
                validateInput(dateFrom)) {
                e.preventDefault()
                if (shipId.value == '') {
                    swal({
                        title: 'Please Select Ship First',
                        icon: "warning",
                        timer: 2500
                    });
                } else {


                    $.ajax({
                        type: "POST",
                        url: "addTankDischargingSequence",
                        dataType: "json",
                        data: {
                            shipId: shipId.value,
                            tankNumber: tankNumber.value,
                            lineDisplacement: lineDisplacement.value,
                            timeFrom: timeFrom.value,
                            dateFrom: dateFrom.value,



                        },
                        success: function(response) {


                            console.log(response)
                            if (response == 'Added') {

                                // getDischargingSequence()
                                clearInputs()
                                checkTanks()
                                $('#discharge-modal').modal('hide');

                                swal({
                                    title: 'Tank Added',
                                    icon: "success",
                                    button: "Ok",
                                });

                            } else {
                                swal({
                                    title: 'Something Went Wrong!',
                                    icon: "error",
                                    button: "Ok",
                                });
                            }
                        }
                    }, );
                }
            }



        })

        function checkTanks() {
            const shipId = document.querySelector('#shipId')
            if (shipId.value == '') {
                swal({
                    title: 'Please Select Ship First',
                    icon: "warning",
                    timer: 2500
                });
            } else {

                $.ajax({
                    type: "POST",
                    url: "checkTanks",
                    data: {
                        shipId: shipId.value
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response == 'nothing') {
                            $('#tanks').html('')
                            swal({
                                title: 'No Tanks Available',
                                icon: "warning",
                                timer: 2500
                            });
                        } else {


                            $('#tanks').html('')
                            for (let tank of response) {
                                if (tank.time_to == '' && tank.date_to == '') {


                                    $('#tanks').append(`

                                <div class="card bg-gradient-warning mb-3 p-2 col-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="my-input">Tank Number</label>
                                    <input id="my-input" class="form-control" type="text" value="${tank.tank_number}"
                                        style="border:0; border-radius:0; border-bottom: 2px solid #0069D9;" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="my-input">Arrival Quantity (MT)</label>
                                    <input id="my-input" class="form-control" type="text" value="${tank.arrivalQuantity1}"
                                        style="border:0; border-radius:0; border-bottom: 2px solid #0069D9;" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="my-input">Time From</label>
                                    <input id="" class="form-control" type="text" value="${tank.time_from}"
                                        style="border:0; border-radius:0; border-bottom: 2px solid #0069D9;" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="my-input">Date From</label>
                                    <input id="" class="form-control" type="text" value="${tank.date_from}"
                                        style="border:0; border-radius:0; border-bottom: 2px solid #0069D9;" readonly>
                                </div>
                            </div>
                        </div>






                        <div class="card-footer">
                            <button type="button" class="btn btn-success btn-sm" onclick="updateTankModal('${tank.id}')"
                                style="float: right;">Update Time & Date </button>
                        </div>
                    </div>
                                `)
                                } else {
                                    $('#DischargingSequence').html('')
                                    $('#tanks').append(`

                                    <div class="card bg-gradient-success mb-3 p-2 col-6">
                                    <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="my-input">Tank Number</label>
                                        <input id="my-input" class="form-control" type="text" value="${tank.tank_number}"
                                            style="border:0; border-radius:0; border-bottom: 2px solid #218838;" readonly>
                                    </div>
                                    </div>
                                    <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="my-input">Arrival Quantity (MT)</label>
                                        <input id="my-input" class="form-control" type="text" value="${tank.arrivalQuantity1}"
                                            style="border:0; border-radius:0; border-bottom: 2px solid #218838;" readonly>
                                    </div>
                                    </div>
                                    </div>
                                 <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                             <label for="my-input">Time From</label>
                                                <input id="" class="form-control" type="text" value="${tank.time_from}"
                                            style="border:0; border-radius:0; border-bottom: 2px solid #218838;" readonly>
                                         </div>
                                         </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                <label for="my-input">Date From</label>
                                                <input id="" class="form-control" type="text" value="${tank.date_from}"
                                            style="border:0; border-radius:0; border-bottom: 2px solid #218838;" readonly>
                                         </div>
                                        </div>
                                    </div>
                                 <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                             <label for="my-input">Time To</label>
                                                <input id="" class="form-control" type="text" value="${tank.time_to}"
                                            style="border:0; border-radius:0; border-bottom: 2px solid #218838;" readonly>
                                         </div>
                                         </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                <label for="my-input">Date To</label>
                                                <input id="" class="form-control" type="text" value="${tank.date_to}"
                                            style="border:0; border-radius:0; border-bottom: 2px solid #218838;" readonly>
                                         </div>
                                        </div>
                                    </div>



                                    </div>
`)
                                }
                                $('.timepickerTo').mdtimepicker();

                            }
                        }
                    }
                });
            }
        }

        function updateTankModal(id) {

            const tankId = document.querySelector('#tankId').value = id

            $('#tankModal').modal({
                show: true,
                backdrop: 'static',
                focus: true
            })

            // updateTank(id)
        }

        function updateTank() {

            const shipId = document.querySelector('#shipId').value
            const tankId = document.querySelector('#tankId').value
            const timeTo = document.querySelector('#timeTo').value
            const dateTo = document.querySelector('#dateTo').value

            $.ajax({
                type: "POST",
                url: "updateTankTimeDate",
                data: {
                    id: tankId,
                    timeTo: timeTo,
                    dateTo: dateTo,

                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response == 'updated') {
                        checkTanks()
                        timeTo.value = ''
                        dateTo.value = ''
                        $('#tankModal').modal('hide')
                        swal({
                            title: 'Tank Time And Date Updated',
                            icon: "success",
                            //    timer: 2500
                        });
                    } else {
                        swal({
                            title: 'Something Went Wrong',
                            icon: "warning",
                            timer: 4500
                        });
                    }
                }
            });
        }


        function getDischargingSequence() {
            const shipId = document.querySelector('#shipId').value
            if (shipId == '') {
                swal({
                    title: 'Please Select Ship First',
                    icon: "warning",
                    timer: 2500
                });
            } else {
                $.ajax({
                    type: "POST",
                    url: "getDischargingSequence",
                    data: {
                        shipId: shipId
                    },
                    dataType: "text",
                    success: function(response) {
                        console.log(response);
                        $('#tanks').html('')

                        if (response == 'nothing') {
                            $('#DischargingSequence').html('')
                            swal({
                                title: 'No Data Available',
                                icon: "warning",
                                timer: 3500
                            });
                        } else {
                            $('#DischargingSequence').html(response)
                            downloadPdf(shipId)

                        }
                    }
                });
            }

        }


        // function formatDate(dateInput) {
        //     const date = new Date(dateInput);
        //     const formattedDate = date.toLocaleDateString('en-GB', {
        //         day: 'numeric',
        //         month: 'short',
        //         year: 'numeric'
        //     }).replace(/ /g, '-');

        //     return formattedDate
        // }
        //=================Processing Certificate of Quantity====================
        </script>
</section>
<?=$this->endSection();?>