<?=$this->extend('Layouts/coreLayout');?>
<?=$this->section('content');?>
<script>
const fetchTheShipId = (id) => {
    const logDownload = document.querySelector('#downloadTimeLog')
    logDownload.setAttribute('href', '<?=base_url()?>/downloadTimeLog/' + id)
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
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#timeLog-modal"
                    aria-pressed="false" autocomplete="off"><i class="far fa-plus-circle" aria-hidden="true"></i>Add
                    Time log</button>
                <button type="button" onclick="getAllTimeLogs()" class="btn btn-success btn-sm" id="refreshTimeLogs"><i
                        class="far fa-sync" aria-hidden="true"></i> Check
                    Logs</button>

                <h4 id="selectedShip"></h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>

                            <th scope="col">DATE</th>
                            <th scope="col">TIME</th>
                            <th scope="col">EVENT/OPERATION</th>

                        </tr>
                    </thead>
                    <tbody id="currentLogs">
                        <tr>

                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a id="downloadTimeLog" target="_blank" class="btn btn-success btn-sm"><i class="far fa-download"
                        aria-hidden="true"></i>Download</a>
            </div>
        </div>


        <div id="timeLog-modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">ADD TIME LOG</h5>
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
                            <label for="my-textarea">Event/Operation</label>
                            <textarea id="event" class="form-control" name="" rows="3" data-clear></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" id="saveTimeLg" class="btn btn-primary btn-sm">Save</button>
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
    const saveTimeLG = document.querySelector('#saveTimeLg');

    saveTimeLG.addEventListener('click', (e) => {
        e.preventDefault()

        const getFormValue = (value) => {
            return document.querySelector(value)
        }

        const shipId = getFormValue('#shipId')
        const date = getFormValue('#date')
        const time = getFormValue('#time')
        const event = getFormValue('#event')
        // console.log(shipId.value)
        // console.log(date.value)
        // console.log(time.value)




        function validateInput(formInput) {

            if (formInput.value == '') {

                formInput.style.border = '1px solid #ff6348'
                return false
            } else {
                formInput.style.border = '1px solid #2ed573'
                return true
            }

        }

        if (validateInput(date) && validateInput(time) && validateInput(event)) {
            $.ajax({
                type: "POST",
                url: "addTimeLog",
                data: {
                    shipId: shipId.value,
                    date: date.value,
                    time: time.value,
                    event: event.value,

                },
                dataType: "json",
                success: function(response) {


                    console.log(response)
                    if (response == 'Added') {
                        clearInputs()
                        grabLastLog()
                        fetchTheShipId(shipId.value)
                        $('#timeLog-modal').modal('hide');

                        swal({
                            title: 'Time Log Saved',
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
        }



    })

    function grabLastLog() {
        const shipId = document.querySelector('#shipId')
        $.ajax({
            type: "POST",
            url: "getLastLog",
            data: {
                shipId: shipId.value
            },
            dataType: "json",
            success: function(response) {



                $('#currentLogs').append(`
                <tr>
                    <td>${formatDate(response.date)}</td>
                    <td>${response.time}</td>
                    <td>${response.event}</td>

                </tr>
                `)


            }
        });
    }

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
    function getAllTimeLogs() {
        $('#currentLogs').html('')
        const shipId = document.querySelector('#shipId')
        $.ajax({
            type: "POST",
            url: "getAllTimeLogs",
            data: {
                shipId: shipId.value
            },
            dataType: "json",
            success: function(response) {

                if (response == 'nothing') {
                    $('#currentLogs').html('<h3>No Time Logs Found</h3>')
                } else {

                    fetchTheShipId(shipId.value)

                    console.log(response)
                    for (let timeLog of response) {
                        $('#currentLogs').append(`
                <tr>
                    <td>${formatDate(timeLog.date)}</td>
                    <td>${timeLog.time}</td>
                    <td>${timeLog.event}</td>

                </tr>
                `)
                    }
                }




            }
        });
    }
    </script>
</section>
<?=$this->endSection();?>