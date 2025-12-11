<?=$this->extend('Layouts/coreLayout');?>
<?=$this->section('content');?>
<script>
const downloadDocument = (id) => {
    const logDownload = document.querySelector('#downloadPressureLog')
    logDownload.setAttribute('href', '<?=base_url()?>/downloadPressureLog/' + id)
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
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                    data-target="#PressureLog-modal" aria-pressed="false" autocomplete="off"><i
                        class="far fa-plus-circle" aria-hidden="true"></i>Add
                    Pressure log</button>
                <button type="button" onclick="getAllPressureLogs()" class="btn btn-success btn-sm"
                    id="refreshPressureLogs"><i class="far fa-sync" aria-hidden="true"></i> Check
                    Logs</button>

                <h4 id="selectedShip"></h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>

                            <th scope="col">#</th>
                            <th scope="col">DATE</th>
                            <th scope="col">TIME</th>
                            <th scope="col">PRESSURE</th>
                            <th scope="col">RATE</th>

                        </tr>
                    </thead>
                    <tbody id="currentLogs">


                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a id="downloadPressureLog" target="_blank" class="btn btn-success btn-sm"><i class="far fa-download"
                        aria-hidden="true"></i>Download</a>
            </div>
        </div>


        <div id="PressureLog-modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">ADD PRESSURE LOG</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">

                            <input id="shipId" class="form-control" type="text" hidden>
                        </div>
                        <div class="form-group">
                            <label for="my-input">Date</label>
                            <input id="date" class="form-control" type="date" name="" data-clear>
                        </div>
                        <div class="timepicker_div form-group">
                            <label for="my-input">Time</label>
                            <input type="text" class="form-control timepicker" id="time"
                                placeholder="Click To Pick Time" data-clear>
                        </div>
                        <div class="form-group">
                            <label for="my-textarea">Pressure</label>
                            <input id="pressure" class="form-control" type="number" name="" data-clear>
                        </div>
                        <div class="form-group">
                            <label for="my-textarea">Rate</label>
                            <input id="rate" class="form-control" type="number" name="" data-clear>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" id="savePressureLog" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </div>
            </div>
        </div>


    </div><!-- /.container-fluid -->
    <script>
    $(document).ready(function() {
        $('.timepicker').mdtimepicker();

    });
    </script>
    <script>
    function getTheShipIdNumber(id) {
        alert('its' + id)
    }
    const savePressureLog = document.querySelector('#savePressureLog');

    savePressureLog.addEventListener('click', (e) => {
        e.preventDefault()



        const shipId = document.querySelector('#shipId')
        const date = document.querySelector('#date')
        const time = document.querySelector('#time')
        const pressure = document.querySelector('#pressure')
        const rate = document.querySelector('#rate')





        function validateInput(formInput) {

            if (formInput.value == '') {

                formInput.style.border = '1px solid #ff6348'
                return false
            } else {
                formInput.style.border = '1px solid #2ed573'
                return true
            }

        }

        if (validateInput(date) && validateInput(time) && validateInput(pressure) && validateInput(rate)) {
            if (shipId.value != '') {

                // console.log(shipId);
                $.ajax({
                    type: "POST",
                    url: "addPressureLog",
                    data: {
                        shipId: shipId.value,
                        date: date.value,
                        time: time.value,
                        pressure: pressure.value,
                        rate: rate.value,

                    },
                    dataType: "json",
                    success: function(response) {


                        console.table(response)
                        if (response == 'Added') {
                            clearInputs()
                            downloadDocument(shipId.value)
                            getAllPressureLogs()
                            $('#PressureLog-modal').modal('hide');

                            swal({
                                title: 'Pressure Log Saved',
                                // text: "You clicked the button!",
                                icon: "success",
                                button: "Ok",
                            });

                        } else {
                            swal({
                                title: 'Something Went Wrong!',
                                // text: "You clicked the button!",
                                icon: "error",
                                button: "Ok",
                            });
                        }
                    }
                }, );
            } else {
                swal({
                    title: 'Please Select  The Ship First!',
                    // text: "You clicked the button!",
                    icon: "warning",
                    button: "Ok",
                    timer: 3500
                });
            }
        }



    })



    function formatDate(dateInput) {
        const date = new Date(dateInput);
        const formattedDate = date.toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        }).replace(/ /g, '-');

        return formattedDate
    }
    //=================select all the tim logs====================
    function getAllPressureLogs() {

        const shipId = document.querySelector('#shipId')

        if (shipId.value != '') {
            $.ajax({
                type: "POST",
                url: "getAllPressureLogs",
                data: {
                    shipId: shipId.value
                },
                dataType: "json",
                success: function(response) {
                    $('#currentLogs').html('')
                    if (response == 'nothing') {
                        $('#currentLogs').html('<h3>No Pressure Logs Found</h3>')
                    } else {

                        downloadDocument(shipId.value)

                        console.log(response)
                        let index = 1;
                        let pressureTotal = 0
                        let rateTotal = 0
                        for (let log of response) {
                            pressureTotal += +log.pressure
                            rateTotal += +log.rate
                            $('#currentLogs').append(`
                            <tr>
                                <td>${index++}</td>
                                <td>${formatDate(log.date)}</td>
                                <td>${log.time}</td>
                                <td>${log.pressure}</td>
                                <td>${log.rate}</td>

                            </tr>
                `)



                        }

                        $('#currentLogs').append(`
                                <tr>
                                    <th colspan = "3" >AVERAGE</th>
                                    <th>${(pressureTotal/response.length).toFixed(2)}</th>
                                    <th>${(rateTotal/response.length).toFixed(2)}</th>

                                </tr>
                                `)
                    }




                }
            });
        } else {
            swal({
                title: 'Please Select The Ship First!',
                icon: "warning",
                timer: 3500
            });
        }

    }
    </script>
</section>
<?=$this->endSection();?>