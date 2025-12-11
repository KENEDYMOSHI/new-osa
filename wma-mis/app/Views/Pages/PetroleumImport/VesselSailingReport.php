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

<div class="container-fluid">




    <!-- Vessel SailingReportModal -->
    <div class="modal fade" id="sailingReportModal" tabindex="-1" role="dialog" aria-labelledby="sailingReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sailingReportModalLabel">Vessel Sailing Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="sailingReportForm">
                        <input type="hidden" id="sailingId" name="sailingId">

                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="">Vessel</label>
                                <select class="form-control" name="vesselId" id="vesselId">
                                    <option value="" disabled selected>-Select Vessel-</option>
                                    <?php foreach ($vessels as $vessel): ?>
                                        <option value="<?= $vessel->vesselId ?>"><?= $vessel->vesselName ?></option>
                                    <?php endforeach; ?>

                                </select>
                            </div>



                        </div>

                        <div class="row">

                            <div class="col-md-6 form-group">
                                <label for="quantityMt">Arrival Quantity(Metric Tones)</label>
                                <input type="text" class="form-control number-input" id="quantityMt" name="quantityMt" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="quantityLitre">Arrival Quantity(Litres)</label>
                                <input type="text" class="form-control number-input" id="quantityLitre" name="quantityLitre" required>
                            </div>

                        </div>

                    

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="anchorageTime">Anchorage Time</label>
                                <input type="datetime-local" class="form-control" id="anchorageTime" name="anchorageTime" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="noticeOfReadiness">Notice Of Readiness</label>
                                <input type="datetime-local" class="form-control" id="noticeOfReadiness" name="noticeOfReadiness" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="berthingTime">Berthing Time</label>
                                <input type="datetime-local" class="form-control " id="berthingTime" name="berthingTime" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="commencedDischarging">Commenced Discharging</label>
                                <input type="datetime-local" class="form-control " id="commencedDischarging" name="commencedDischarging" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="completedTimeDischarging">Completed Time Discharging</label>
                                <input type="datetime-local" class="form-control " id="completedTimeDischarging" name="completedTimeDischarging" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="vesselDepartureTime">Vessel Departure Time</label>
                                <input type="datetime-local" class="form-control "  id="vesselDepartureTime" name="vesselDepartureTime" required>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" onclick="addSailingReport()" class="btn btn-primary">Save  Report</button>
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
                        <select class="form-control" name="" id="" onchange="getVesselSailingReport(this.value)">
                            <option value="" disabled selected>-Select Vessel-</option>
                            <?php foreach ($vessels as $vessel): ?>
                                <option value="<?= $vessel->vesselId ?>"><?= $vessel->vesselName ?></option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                </div>

                <!-- Button on the right -->
                <div class="col-md-6 d-flex justify-content-end">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#sailingReportModal"><i class="far fa-plus    "></i> Add Report</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div id="sailingReport">
            


            </div>
        </div>



    </div>


</div>
</div>





<script>
    async function addSailingReport() {
        const form = document.getElementById('sailingReportForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('addSailingReport', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            console.log(data);
            const {
                status,
                sailingReport,
                msg
            } = data;

            if (status == 1) {
                document.getElementById('sailingReport').innerHTML = sailingReport;
                // form.reset();
                $('#sailingReportModal').modal('hide');
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
            const response = await fetch('/sailingReport/update/' + formData.get('id'), {
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
            const response = await fetch('/sailingReport/delete/' + id, {
                method: 'POST'
            });
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function editSailingReport(sailingReportId) {
        try {
            const response = await fetch('getVesselSailingReport/' + sailingReportId, {
                method: 'POST'
            });
            if (response.ok) {
                const data = await response.json();
                const {
                    status,
                    sailingReport
                } = data;
                document.getElementById('sailingReport').innerHTML = sailingReport;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    async function getVesselSailingReport(vesselId) {
        try {
            const response = await fetch('getSailingReport/' + vesselId, {
                method: 'POST'
            });
            if (response.ok) {
                const data = await response.json();
                const {
                    status,
                    sailingReport
                } = data;
                document.getElementById('sailingReport').innerHTML = sailingReport;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
</script>





<?= $this->endSection(); ?>