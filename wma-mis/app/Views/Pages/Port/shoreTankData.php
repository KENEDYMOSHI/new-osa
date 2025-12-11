<?=$this->extend('Layouts/coreLayout');?>
<?=$this->section('content');?>

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
        <input hidden class="txt_csrfname form-control" name="<?=csrf_token()?>" value="<?=csrf_hash()?>" />
        <input hidden id="shipId" class="form-control" type="number" name="">
        <!-- SHORE TANKS CARD -->

        <div class="card">
            <div class="card-header">


                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" aria-pressed="false"
                    onclick="openTankModal()"><i class="far fa-plus-circle"></i> Add
                    Tank</button>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" aria-pressed="false"
                    onclick="checkTanks()"><i class="far fa-sync"></i> Check
                    Tank</button>


            </div>
            <div class="card-body">


                <div class="form-group col-4">
                    <label for="my-select">Select Tank</label>
                    <select id="tanksList" class="form-control" onchange="getTankDetails(this.value)">

                    </select>
                </div>

                <div id="tankInfo">
                    <input type="text" id="tankId" class="form-control col-3" hidden>
                    <table class="table table-bordered col-12">
                        <tbody id="currentTank">

                        </tbody>
                    </table>

                </div>


            </div>
            <!-- <div class="card-footer">
                <a id="downloadNoteOfFactAfter" target="_blank" class="btn btn-success btn-sm"><i
                        class="far fa-download"></i>Download</a>
            </div> -->
        </div>

        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary btn-sm" onclick="openMeasurementModal()"><i
                        class="far fa-plus-circle">
                    </i> Add Measurements</button>
                <button type="button" class="btn btn-success btn-sm" onclick="checkMeasurements()"><i
                        class="far fa-sync">
                    </i> Check Measurements</button>
            </div>
            <div class="card-body">

                <div id="measurementsTable"></div>

            </div>

        </div>

        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary btn-sm" onclick="openSealModal()"><i
                        class="far fa-plus-circle">
                    </i> Add Seal</button>
                <button type="button" class="btn btn-success btn-sm" onclick="checkSeals()"><i class="far fa-sync">
                    </i> Check Seals</button>
            </div>
            <div class="card-body">
                <div id="sealPositionTable"></div>
            </div>

        </div>
        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary btn-sm" onclick="openStatusModal()"><i
                        class="far fa-plus-circle">
                    </i> Add Status</button>
                <button type="button" class="btn btn-success btn-sm" onclick="checkStatus()"><i class="far fa-sync">
                    </i> Check Status</button>
            </div>
            <div class="card-body">
                <div id="statusTable"></div>
            </div>
            <div class="card-footer">
                <a class="btn btn-success btn-sm" target="_blank" id="downloadShoreTankMeasurementData"><i
                        class="far fa-download">
                    </i> Download</a>
            </div>

        </div>
        <!-- ========================== -->

        <div id="measurementModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">TANK MEASUREMENTS</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="my-select">Particular</label>
                                <select id="particularId" class="form-control" name="">
                                    <?php foreach ($measurementParticulars as $measurement): ?>
                                    <option value="<?=$measurement->particular_id?>"><?=$measurement->title?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="my-input">First Measurement</label>
                                <input id="measurement1" class="form-control" type="number" data-clear>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="my-input">Second Measurement</label>
                                <input id="measurement2" class="form-control" type="number" data-clear
                                    oninput="calcAverageOnTwo(this.value)">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="my-input">Third Measurement</label>
                                <input id="measurement3" class="form-control" type="number" data-clear
                                    oninput="calcAverageOnThree(this.value)">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="my-input">Average Measurement</label>
                                <input id="average" class="form-control" type="number" data-clear>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm"
                            onclick="addMeasurementData()">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- add tank model -->
        <div id="tankModal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="my-input">Terminal</label>
                                    <input id="shoreTerminal" class="form-control" type="text" data-clear>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="my-input">Tank Number</label>
                                    <input id="tankNumber" class="form-control" type="text" data-clear>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="my-input">Product</label>
                                    <input id="product" class="form-control" type="text" data-clear>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="my-input">Time</label>
                                    <input id="time" class="form-control" type="time" data-clear>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="my-input">Date</label>
                                    <input id="date" class="form-control" type="date" data-clear>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-inline">
                                    <input id="beforeLoading" class="form-check-input" type="radio" name="1"
                                        style="transform: scale(1.3);" data-before>
                                    <label for="beforeLoading" class="form-check-label">Before Discharge /
                                        Loading:</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-inline">
                                    <input id="afterLoading" class="form-check-input" type="radio" name="1"
                                        style="transform: scale(1.3);" data-after>
                                    <label for="afterLoading" class="form-check-label">After Discharge /
                                        Loading:</label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="saveTankData()" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </div>
            </div>






        </div>
        <!-- seal position modal -->
        <div id="sealModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">WMA SEAL</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="my-select">Wma Seal Position</label>
                            <select id="sealPosition" class="form-control" name="">
                                <?php foreach ($sealPositions as $seal): ?>
                                <option value="<?=$seal->id?>"><?=$seal->seal_name?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="my-input">Seal Number</label>
                            <input id="sealNumber" class="form-control" type="text" data-clear>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="addSealPosition()">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="statusModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">LINE STATUS</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="my-select">Line Status</label>
                                <select id="status" class="form-control" name="">
                                    <option value="Full">Full</option>
                                    <option value="Partial">Partial</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="my-input">Product</label>
                                <input id="productSt" class="form-control" type="text" data-clear>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="my-textarea">How Verified</label>
                                <textarea id="verified" class="form-control" name="" rows="3"></textarea>
                            </div>
                        </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="addStatus()">Save</button>
                    </div>
                </div>
            </div>
        </div>


        <script>
        function formatNumber(number) {
            return new Intl.NumberFormat().format(number)
        }

        function updateToken(token) {
            document.querySelector('.txt_csrfname').value = token
        }

        function validateInput(formInput) {

            if (formInput.value == '') {

                formInput.style.border = '1px solid #ff6348'
                return false
            } else {
                formInput.style.border = '1px solid #2ed573'
                return true
            }

        }

        function isChecked(input) {
            if (input.checked == true) {
                return 1
            } else {
                return 0
            }
        }

        function makeChecked(val) {
            if (val == 1) {
                return 'checked'
            } else {
                return ''

            }
        }

        function calcAverageOnTwo(m2) {
            const m1 = document.querySelector('#measurement1').value
            const av = document.querySelector('#average')

            av.value = (+m1 + +m2) / 2

        }

        function calcAverageOnThree(m3) {
            const m1 = document.querySelector('#measurement1').value
            const m2 = document.querySelector('#measurement2').value
            const av = document.querySelector('#average')

            if (m3 == 0) {
                av.value = ((+m1 + +m2 + +m3) / 2).toFixed(2)
            } else {

                av.value = ((+m1 + +m2 + +m3) / 3).toFixed(2)
            }


        }




        //=================*********====================


        const shipId = document.querySelector('#shipId')
        const shoreTerminal = document.querySelector('#shoreTerminal')
        const tankNumber = document.querySelector('#tankNumber')
        const product = document.querySelector('#product')
        const time = document.querySelector('#time')
        const date = document.querySelector('#date')
        const beforeLoading = document.querySelector('#beforeLoading')
        const afterLoading = document.querySelector('#afterLoading')


        function openTankModal() {

            if (shipId.value == '') {
                checkShip()

            } else {
                $('#tankModal').modal({
                    show: true
                })
            }
        }

        function openMeasurementModal() {
            const tankId = document.querySelector('#tankId')
            if (tankId.value == '') {
                checkId('Please Select Tank First')
            } else {
                $('#measurementModal').modal({
                    show: true
                })
            }
        }

        function openSealModal() {
            const tankId = document.querySelector('#tankId')
            if (tankId.value == '') {
                checkId('Please Select Tank First')
            } else {
                $('#sealModal').modal({
                    show: true
                })
            }
        }

        function openStatusModal() {
            const tankId = document.querySelector('#tankId')
            if (tankId.value == '') {
                checkId('Please Select Tank First')
            } else {
                $('#statusModal').modal({
                    show: true
                })
            }
        }

        function checkTanks() {
            const csrfName = document.querySelector('.txt_csrfname').getAttribute('name'); // CSRF Token name
            const csrfHash = document.querySelector('.txt_csrfname').value; // CSRF hash
            if (shipId.value != '') {

                $.ajax({
                    type: "POST",
                    url: "checkShoreTanks",
                    data: {
                        [csrfName]: csrfHash,
                        shipId: shipId.value
                    },
                    dataType: "json",
                    success: function(response) {
                        updateToken(response.token)

                        if (response == 'nothing') {
                            console.log('No Data Found');
                            updateToken(response.token)
                        } else {

                            console.log(response);


                            let tankBox = ''
                            for (tank of response.shoreTanks) {
                                tankBox +=
                                    `<option value ="${tank.tank_id}">Tank Number ${tank.tank_number}</option>`
                            }
                            $('#tanksList').html(tankBox)



                        }

                    }
                });

            } else {
                checkShip('Pease Select Ship First')
            }

        }

        function saveTankData() {

            const csrfName = document.querySelector('.txt_csrfname').getAttribute('name'); // CSRF Token name
            const csrfHash = document.querySelector('.txt_csrfname').value; // CSRF hash

            if (validateInput(shoreTerminal) && validateInput(tankNumber) && validateInput(product) &&
                validateInput(
                    time) &&
                validateInput(date)) {

                $.ajax({
                    type: "POST",
                    url: "addShoreTank",
                    data: {
                        [csrfName]: csrfHash,
                        shipId: shipId.value,
                        terminal: shoreTerminal.value,
                        product: product.value,
                        tankNumber: tankNumber.value,
                        product: product.value,
                        time: time.value,
                        date: date.value,
                        beforeLoading: isChecked(beforeLoading),
                        afterLoading: isChecked(afterLoading),
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.message == 'Added') {
                            const lastTank = response.lastTank
                            updateToken(response.token)
                            $('#tankModal').modal('hide')
                            succeed('Shore Tank Added')
                            clearInputs()
                            $('#currentTank').html(
                                `
                                <tr>
                                <td>Ship : <b>${lastTank.ship_name}</b></td>
                                <td>Product : <b> ${lastTank.product}</b></td>
                            </tr>
                            <tr>
                                <td>Terminal : <b>${lastTank.terminal}</b></td>
                                <td>Tank No : <b> ${lastTank.tank_number}</b></td>
                            </tr>
                            <tr>
                                <td>Date : <b>${formatDate(lastTank.date)}</b></td>
                                <td>Time : <b> ${lastTank.time}</b></td>
                            </tr>
                               `
                            )
                        }
                        console.log(response);
                        checkMeasurements(lastTank.tank.tank_id)

                    }
                });

            }
        }




        function getTankDetails(tankId) {
            const shipId = document.querySelector('#shipId').value
            const csrfName = document.querySelector('.txt_csrfname').getAttribute('name'); // CSRF Token name
            const csrfHash = document.querySelector('.txt_csrfname').value; // CSRF hash
            document.querySelector('#tankId').value = tankId
            if (shipId == '') {
                checkShip('Please Select Ship First')
            } else {
                $.ajax({
                    type: "POST",
                    url: "getTankDetails",
                    data: {
                        [csrfName]: csrfHash,
                        tankId: tankId
                    },
                    dataType: "json",
                    success: function(response) {
                        let tank = response.tank
                        console.log(response);
                        updateToken(response.token)

                        if (response.message == 'nothing') {
                            $('#currentTank').html('<h3>No Dat Found</h3>')
                        } else {
                            $('#currentTank').html(
                                `
                                <tr>
                                <td>Ship : <b>Black Perl</b></td>
                                <td>Product : <b> ${tank.product}</b></td>
                            </tr>
                            <tr>
                                <td>Terminal : <b>${tank.terminal}</b></td>
                                <td>Tank No : <b> ${tank.tank_number}</b></td>
                            </tr>
                            <tr>
                                <td>Date : <b>${formatDate(tank.date)}</b></td>
                                <td>Time : <b> ${tank.time}</b></td>
                            </tr>
                               `
                            )

                            checkMeasurements(tank.tank_id)
                            document.querySelector('#productSt').value = tank.product
                        }




                    }
                });
            }

        }

        function addMeasurementData() {
            const csrfName = document.querySelector('.txt_csrfname').getAttribute('name'); // CSRF Token name
            const csrfHash = document.querySelector('.txt_csrfname').value; // CSRF hash
            $.ajax({
                type: "POST",
                url: "addMeasurementData",
                data: {
                    [csrfName]: csrfHash,
                    tankId: document.querySelector('#tankId').value,
                    particularId: document.querySelector('#particularId').value,
                    measurement1: document.querySelector('#measurement1').value,
                    measurement2: document.querySelector('#measurement2').value,
                    measurement3: document.querySelector('#measurement3').value,
                    average: document.querySelector('#average').value,
                },
                dataType: "json",

                success: function(response) {
                    console.log(response);
                    if (response.message === 'Exists') {
                        messageAlert(response.exist)
                        updateToken(response.token)
                        renderMeasurements(response.measurements)
                    } else if (response.message === 'Added') {
                        succeed('Measurement Added')
                        updateToken(response.token)
                        $('#measurementModal').modal('hide')
                        renderMeasurements(response.measurements)
                        clearInputs()
                    }


                }
            });
        }

        function renderMeasurements(measurements) {
            let dataTable = `
            <table class="table table-bordered">
                    <tbody>
                    <thead class="thead-light">
                     <tr>
                            <th>PARTICULARS</th>
                            <th colspan="4" class="text-center">MEASUREMENTS</th>
                        </tr>
                    </thead>

                        <tr>
                            <th></th>
                            <th>1<sup>st</sup> Measurement</th>
                            <th>2<sup>nd</sup> Measurement</th>
                            <th>1<sup>rd</sup> Measurement</th>
                            <th>Average Measurement</th>

                        </tr>
            `
            for (let measurement of measurements) {
                dataTable += `
                <tr>
                    <td>${measurement.title}</td>
                    <td>${measurement.measurement1}</td>
                    <td>${measurement.measurement2}</td>
                    <td>${measurement.measurement3}</td>
                    <td>${measurement.average}</td>
                </tr>
                   `
            }

            dataTable += `

                  </tbody>
                </table>
            `

            $('#measurementsTable').html(dataTable)
        }


        //=================check and grab tank measurements====================
        function checkMeasurements() {
            const csrfName = document.querySelector('.txt_csrfname').getAttribute('name'); // CSRF Token name
            const csrfHash = document.querySelector('.txt_csrfname').value; // CSRF hash
            if (tankId.value == '') {
                checkId('Please Select Tank First')
            } else {
                $.ajax({
                    type: "POST",
                    url: "getTankMeasurements",
                    data: {
                        [csrfName]: csrfHash,
                        tankId: tankId.value
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        updateToken(response.token)
                        if (response.message == 'nothing') {
                            $('#measurementsTable').html('<h5 class="text-center">No Data Found</h5>')
                        } else {

                            renderMeasurements(response.measurements)
                        }




                    }
                });

            }


        }




        function addSealPosition() {
            const csrfName = document.querySelector('.txt_csrfname').getAttribute('name'); // CSRF Token name
            const csrfHash = document.querySelector('.txt_csrfname').value; // CSRF hash
            $.ajax({
                type: "POST",
                url: "addSealPosition",
                data: {
                    [csrfName]: csrfHash,
                    tankId: document.querySelector('#tankId').value,
                    sealPosition: document.querySelector('#sealPosition').value,
                    sealNumber: document.querySelector('#sealNumber').value,

                },
                dataType: "json",

                success: function(response) {
                    console.log(response);
                    if (response.message === 'Added') {
                        succeed('Seal Added')
                        updateToken(response.token)
                        $('#sealModal').modal('hide')
                        renderSealData(response.seals)
                        clearInputs()
                    }


                }
            });
        }

        function checkSeals() {
            const csrfName = document.querySelector('.txt_csrfname').getAttribute('name'); // CSRF Token name
            const csrfHash = document.querySelector('.txt_csrfname').value; // CSRF hash
            if (tankId.value == '') {
                checkId('Please Select Tank First')
            } else {
                $.ajax({
                    type: "POST",
                    url: "getSealPositions",
                    data: {
                        [csrfName]: csrfHash,
                        tankId: tankId.value
                    },
                    dataType: "json",
                    success: function(response) {
                        // console.log(response);
                        updateToken(response.token)

                        if (response.message == 'nothing') {
                            $('#sealPositionTable').html('<h5 class="text-center">No Data Found</h5>')
                        } else {

                            renderSealData(response.seals)
                        }




                    }
                });

            }


        }


        function renderSealData(sealData) {

            function getSealNumbers(sealPosition) {
                let sealNumberList = ''
                sealData.filter((data) => data.seal_name === sealPosition).map((data) => data.seal_number).forEach(
                    (
                        seal) => {
                        sealNumberList += seal + ' , '
                    })
                return sealNumberList
            }

            const outlet = getSealNumbers('Outlet')
            const inlet = getSealNumbers('Inlet')
            const terminalManifold = getSealNumbers('Terminal Manifold at KOJ')
            const tiperManifold = getSealNumbers('TIPER Manifold at KOJ')
            const otherBranching = getSealNumbers('Other Branching lines (terminal or else where)')

            $('#sealPositionTable').html(`
             <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>WMA SEAL POSITIONS</th>
                            <th>WMA SEAL NUMBERS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Outlet</td>
                            <td>${outlet}</td>
                        </tr>
                        <tr>
                            <td>Inlet</td>
                            <td>${inlet}</td>
                        </tr>
                        <tr>
                            <td>Terminal Manifold at KOJ</td>
                            <td>${terminalManifold}</td>
                        </tr>
                        <tr>
                            <td>TIPER Manifold at KOJ</td>
                            <td>${tiperManifold}</td>
                        </tr>
                        <tr>
                            <td>Other Branching lines (terminal or else where)</td>
                            <td>${otherBranching}</td>
                        </tr>
                    </tbody>

                </table>
            `)

        }


        function addStatus() {
            const csrfName = document.querySelector('.txt_csrfname').getAttribute('name'); // CSRF Token name
            const csrfHash = document.querySelector('.txt_csrfname').value; // CSRF hash
            $.ajax({
                type: "POST",
                url: "addStatus",
                data: {
                    [csrfName]: csrfHash,
                    tankId: document.querySelector('#tankId').value,
                    status: document.querySelector('#status').value,
                    product: document.querySelector('#productSt').value,
                    verified: document.querySelector('#verified').value,

                },
                dataType: "json",

                success: function(response) {
                    console.log(response);
                    updateToken(response.token)
                    if (response.message === 'Added') {
                        succeed('Line Status Added')
                        updateToken(response.token)
                        $('#statusModal').modal('hide')
                        renderStatusData(response.status)
                        clearInputs()
                        downloadPdf()
                    }


                }
            });
        }


        function checkStatus() {
            const csrfName = document.querySelector('.txt_csrfname').getAttribute('name'); // CSRF Token name
            const csrfHash = document.querySelector('.txt_csrfname').value; // CSRF hash
            if (tankId.value == '') {
                checkId('Please Select Tank First')
            } else {
                $.ajax({
                    type: "POST",
                    url: "getStatus",
                    data: {
                        [csrfName]: csrfHash,
                        tankId: tankId.value
                    },
                    dataType: "json",
                    success: function(response) {
                        // console.log(response);
                        updateToken(response.token)
                        if (response.message == 'nothing') {
                            $('#statusTable').html('<h5 class="text-center">No Data Found</h5>')
                        } else {

                            renderStatusData(response.status)
                            downloadPdf()
                        }




                    }
                });

            }


        }

        function renderStatusData(lineStatus) {
            let dataTable = `
            <table class="table table-bordered">
                    <tbody>
                    <thead class="thead-light">
                     <tr>
                            <th>LINE STATUS</th>
                            <th>PRODUCT</th>
                            <th>HOW VERIFIED</th>

                        </tr>
                    </thead>

                <tr>
                    <td>${lineStatus.status}</td>
                    <td>${lineStatus.product}</td>
                    <td>${lineStatus.verified}</td>

                </tr>
                  </tbody>
                </table>
            `

            $('#statusTable').html(dataTable)
        }

        function downloadPdf() {
            const shipIdNumber = document.querySelector('#shipId').value
            const tankIdNumber = document.querySelector('#tankId').value
            const downloadBtn = document.querySelector('#downloadShoreTankMeasurementData')
            downloadBtn.setAttribute('href', '<?=base_url()?>/downloadShoreTankMeasurementData/' + shipIdNumber +
                '/' +
                tankIdNumber)
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
        </script>
</section>
<?=$this->endSection();?>