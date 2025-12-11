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




    <!-- Certificate of Quantity Modal -->
    <div class="modal fade" id="certificateModal" tabindex="-1" role="dialog" aria-labelledby="certificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="certificateModalLabel">Certificate of Quantity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="certificateForm">
                        <input type="hidden" id="certificateId" name="certificateId">

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
                                <label for="tableUsed">Table Used</label>
                                <input type="text" class="form-control " id="tableUsed" name="tableUsed" required>
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="supplier">Supplier</label>
                                <input type="text" class="form-control number-input" id="supplier" name="supplier" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="metricTonesInAir">Metric Tones In Air</label>
                                <input type="text" class="form-control number-input" id="metricTonesInAir" name="metricTonesInAir" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="metricTonesInVac">Metric Tones In Vacuum</label>
                                <input type="text" class="form-control number-input" id="metricTonesInVac" name="metricTonesInVac" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="longTons">Long Tons</label>
                                <input type="text" class="form-control number-input" id="longTons" name="longTons" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="litresAtTwentyCentigrade">Litres at 20°C</label>
                                <input type="text" class="form-control number-input" id="litresAtTwentyCentigrade" name="litresAtTwentyCentigrade" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="litresAtFifteenCentigrade">Litres at 15°C</label>
                                <input type="text" class="form-control number-input" id="litresAtFifteenCentigrade" name="litresAtFifteenCentigrade" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="usbblsAtSixtyFahrenheit">US BBLs at 60°F</label>
                                <input type="text" class="form-control number-input" id="usbblsAtSixtyFahrenheit" name="usbblsAtSixtyFahrenheit" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="usgallonsAtSixtyFahrenheit">US Gallons at 60°F</label>
                                <input type="text" class="form-control number-input" id="usgallonsAtSixtyFahrenheit" name="usgallonsAtSixtyFahrenheit" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="stdDensityAtTwentyCentigrade">Standard Density at 20°C</label>
                                <input type="text" class="form-control number-input" id="stdDensityAtTwentyCentigrade" name="stdDensityAtTwentyCentigrade" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="densityAtFifteenCentigrade">Density at 15°C</label>
                                <input type="text" class="form-control number-input" pattern="[0-9]*" id="densityAtFifteenCentigrade" name="densityAtFifteenCentigrade" required>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" onclick="addCertificate()" class="btn btn-primary">Save Certificate</button>
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
                        <select class="form-control" name="" id="" onchange="getVesselCertificate(this.value)">
                            <option value="" disabled selected>-Select Vessel-</option>
                            <?php foreach ($vessels as $vessel): ?>
                                <option value="<?= $vessel->vesselId ?>"><?= $vessel->vesselName ?></option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                </div>

                <!-- Button on the right -->
                <div class="col-md-6 d-flex justify-content-end">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#certificateModal"><i class="far fa-plus    "></i> Add Certificate</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div id="certificate">
        
            </div>
        </div>



    </div>


</div>
</div>


<script>
    async function addCertificate() {
        const form = document.getElementById('certificateForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('addCertificateOfQuantity', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            console.log(data);
            const {
                status,
                certificate,
                msg
            } = data;

            if (status == 1) {
                document.getElementById('certificate').innerHTML = certificate;
                // form.reset();
                $('#certificateModal').modal('hide');
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
            const response = await fetch('/vessels/update/' + formData.get('id'), {
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
            const response = await fetch('/vessels/delete/' + id, {
                method: 'POST'
            });
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function editCertificate(certificateId) {
        try {
            const response = await fetch('getVesselCertificate/' + certificateId, {
                method: 'POST'
            });
            if (response.ok) {
                const data = await response.json();
                const {
                    status,
                    certificate
                } = data;
                document.getElementById('certificate').innerHTML = certificate;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    async function getVesselCertificate(vesselId) {
        try {
            const response = await fetch('getVesselCertificate/' + vesselId, {
                method: 'POST'
            });
            if (response.ok) {
                const data = await response.json();
                const {
                    status,
                    certificate
                } = data;
                document.getElementById('certificate').innerHTML = certificate;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
</script>





<?= $this->endSection(); ?>