<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>


<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
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
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }
        th {
            /* background-color: #f2f2f2; */
        }
      
        .header-right {
            text-align: right;
            background-color: #f2f2f2;
        }
    </style>

<div class="container-fluid">




    <!-- Vessel OutturnReportModal -->
    <div class="modal fade" id="outturnReportModal" tabindex="-1" role="dialog" aria-labelledby="outturnReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="outturnReportModalLabel">Vessel Outturn Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="outturnReportForm">
                        <input type="hidden" id="outturnId" name="outturnId">

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="">Vessel</label>
                                <select class="form-control" name="vesselId" id="vesselId">
                                    <option value="" disabled selected>-Select Vessel-</option>
                                    <?php foreach ($vessels as $vessel): ?>
                                        <option value="<?= $vessel->vesselId ?>"><?= $vessel->vesselName ?></option>
                                    <?php endforeach; ?>

                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <?php $terminals = ['AFROIL','MOGAS','CAMEL OIL','LAKE OIL','PUMA','MOIL'] ?>
                                <label for="">Terminal</label>
                                <select class="form-control" name="terminal" id="terminal">
                                    <option value="" disabled selected>-Select Terminal-</option>
                                    <?php foreach ($terminals as $terminal): ?>
                                        <option value="<?= $terminal ?>"><?= $terminal ?></option>
                                    <?php endforeach; ?>

                                </select>
                            </div>



                        </div>

                        <div class="card mb-3">
                            <div class="card-header">SHIP FIGURES</div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-6 form-group">
                                        <label for="shipMt">Metric Tones</label>
                                        <input type="text" class="form-control number-input" id="shipMt" name="shipMt" oninput="calcDischargeQuantityMtDifference()" required>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="shipVol"> Volume @ 20&deg;C </label>
                                        <input type="text" class="form-control number-input" id="shipVol" name="shipVol" oninput="calcDischargeQuantityVolDifference()" required>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">SHORE FIGURES</div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-6 form-group">
                                        <label for="shoreMt">Metric Tones</label>
                                        <input type="text" class="form-control number-input" id="shoreMt" name="shoreMt" oninput="calcDischargeQuantityMtDifference()" required>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="shoreVol"> Volume @ 20&deg;C </label>
                                        <input type="text" class="form-control number-input" id="shoreVol" name="shoreVol" oninput="calcDischargeQuantityVolDifference()" required>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header">BILL OF LADING</div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-6 form-group">
                                        <label for="billOfLadingMt">Metric Tones</label>
                                        <input type="text" class="form-control number-input" id="billOfLadingMt" name="billOfLadingMt" oninput="calcBillOfLadingMtDifference();" required>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="billOfLadingVol"> Volume @ 20&deg;C </label>
                                        <input type="text" class="form-control number-input" id="billOfLadingVol" oninput="calcBillOfLadingVolDifference()" name="billOfLadingVol" required>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <h4 class="text-center">DIFFERENCE</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header">DISCHARGE QUANTITY VS FINAL</div>
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-md-6 form-group">
                                                <label for="dischargeQuantityMtDifference">Metric Tones</label>
                                                <input type="text" class="form-control number-input" id="dischargeQuantityMtDifference" name="dischargeQuantityMtDifference" required readonly>
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label for="dischargeQuantityMtDifferencePercent"> % DIFF</label>
                                                <input type="text" class="form-control number-input" id="dischargeQuantityMtDifferencePercent" name="dischargeQuantityMtDifferencePercent" required readonly>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="dischargeQuantityVolDifference">Volume @ 20&deg;C </label>
                                                <input type="text" class="form-control number-input" id="dischargeQuantityVolDifference" name="dischargeQuantityVolDifference" required readonly>
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label for="dischargeQuantityVolDifferencePercent">% DIFF </label>
                                                <input type="text" class="form-control number-input" id="dischargeQuantityVolDifferencePercent" name="dischargeQuantityVolDifferencePercent" required readonly>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header">BILL OF LADING VS FINAL</div>
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-md-6 form-group">
                                                <label for="billOfLadingMtDifference">Metric Tones</label>
                                                <input type="text" class="form-control number-input" id="billOfLadingMtDifference" name="billOfLadingMtDifference" required readonly>
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label for="billOfLadingMtDifferencePercent"> % DIFF</label>
                                                <input type="text" class="form-control number-input" id="billOfLadingMtDifferencePercent" name="billOfLadingMtDifferencePercent" required readonly>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="billOfLadingVolDifference">Volume</label>
                                                <input type="text" class="form-control number-input" id="billOfLadingVolDifference" name="billOfLadingVolDifference" required readonly>
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label for="billOfLadingVolDifferencePercent"> % DIFF</label>
                                                <input type="text" class="form-control number-input" id="billOfLadingVolDifferencePercent" name="billOfLadingVolDifferencePercent" required readonly>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>



                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" onclick="addOutturnReport()" class="btn btn-primary">Save Report</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


    <div class="card">


        <div class="card-header">
            <div class="row align-items-center">
                <!-- Form on the left -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Select Vessel</label>
                        <select class="form-control" name="" id="" onchange="getVesselOutturnReport(this.value)">
                            <option value="" disabled selected>-Select Vessel-</option>
                            <?php foreach ($vessels as $vessel): ?>
                                <option value="<?= $vessel->vesselId ?>"><?= $vessel->vesselName ?></option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                </div>

                <!-- Button on the right -->
                <div class="col-md-6 d-flex justify-content-end">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#outturnReportModal"><i class="far fa-plus    "></i> Add Report</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div id="outturnReport">



            </div>
        </div>



    </div>


</div>
</div>





<script>
    function calcDischargeQuantityMtDifference() {
        let shoreMt = document.querySelector('#shoreMt').value.replace(/,/g, '') ?? 0
        let shipMt = document.querySelector('#shipMt').value.replace(/,/g, '') ?? 0
        let diff = shoreMt - shipMt
        document.querySelector('#dischargeQuantityMtDifference').value = diff.toFixed(3)
        document.querySelector('#dischargeQuantityMtDifferencePercent').value = ((diff / shoreMt) * 100).toFixed(3)

    }

    function calcDischargeQuantityVolDifference() {
        let shoreVol = document.querySelector('#shoreVol').value.replace(/,/g, '') ?? 0
        let shipVol = document.querySelector('#shipVol').value.replace(/,/g, '') ?? 0
        let diff = shoreVol - shipVol
        document.querySelector('#dischargeQuantityVolDifference').value = diff.toFixed(3)
        document.querySelector('#dischargeQuantityVolDifferencePercent').value = ((diff / shoreVol) * 100).toFixed(3)

        console.log('shoreVol ' + shoreVol);
        console.log('shipVol' + shipVol);
    }


    function calcBillOfLadingMtDifference() {
        let billOfLadingMt = document.querySelector('#billOfLadingMt').value.replace(/,/g, '') ?? 0
        let shoreMt = document.querySelector('#shoreMt').value.replace(/,/g, '') ?? 0
        let diff = shoreMt - billOfLadingMt
        document.querySelector('#billOfLadingMtDifference').value = diff.toFixed(3)
        document.querySelector('#billOfLadingMtDifferencePercent').value = ((diff / billOfLadingMt) * 100).toFixed(3)

        console.log('shoreMt ' + shoreMt);
        console.log('billOfLadingMt ' + billOfLadingMt);
    }




    function calcBillOfLadingVolDifference() {
        billOfLadingVol = document.querySelector('#billOfLadingVol').value.replace(/,/g, '') ?? 0
        shipVol = document.querySelector('#shipVol').value.replace(/,/g, '') ?? 0
        let diff = shipVol -billOfLadingVol 
        document.querySelector('#billOfLadingVolDifference').value = diff.toFixed(3)
        document.querySelector('#billOfLadingVolDifferencePercent').value = ((diff / billOfLadingVol) * 100).toFixed(3)

        console.log('shoreVol ' + shoreVol);

    }


    console.log('shoreMt' + shoreMt);

    async function addOutturnReport() {
        const form = document.getElementById('outturnReportForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('addOutturnReport', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            console.log(data);
            const {
                status,
                outturnReport,
                msg
            } = data;

            if (status == 1) {
                document.getElementById('outturnReport').innerHTML = outturnReport;
                form.reset();
                $('#outturnReportModal').modal('hide');
                // setTimeout(() => {
                //     location.reload();
                // }, 2000);
            }

            swal({
                title: msg,
                icon: status == 1 ? "success" : "warning",
            });


        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function updateVessel() {
        const form = document.getElementById('editVesselForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('/outturnReport/update/' + formData.get('id'), {
                method: 'POST',
                body: formData
            });
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function deleteVessel(id) {
        try {
            const response = await fetch('/outturnReport/delete/' + id, {
                method: 'POST'
            });
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function editOutturnReport(outturnReportId) {
        try {
            const response = await fetch('getVesselOutturnReport/' + outturnReportId, {
                method: 'POST'
            });
            if (response.ok) {
                const data = await response.json();
                const {
                    status,
                    outturnReport
                } = data;
                document.getElementById('outturnReport').innerHTML = outturnReport;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    async function getVesselOutturnReport(vesselId) {
        try {
            const response = await fetch('getOutturnReport/' + vesselId, {
                method: 'POST'
            });
            if (response.ok) {
                const data = await response.json();
                const {
                    status,
                    outturnReport
                } = data;
                document.getElementById('outturnReport').innerHTML = outturnReport;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
</script>





<?= $this->endSection(); ?>