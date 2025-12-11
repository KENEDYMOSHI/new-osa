<?= $this->extend('layouts/coreLayout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                    <li class="breadcrumb-item active"><?= $page['title'] ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">

        <!-- Vessel Information Section -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center" style="border-radius: 8px 8px 0 0; padding: 15px 20px;">
                <h5 class="mb-0 text-dark"><i class="far fa-ship mr-2 text-success"></i> Vessel Information</h5>
                <div class="d-flex align-items-center ml-auto">
                    <!-- Time Log Actions -->
                    <div class="d-flex align-items-center mr-3 bg-light rounded px-2 py-1 border">
                        <span class="text-xs font-weight-bold text-muted mr-2 text-uppercase"><i class="far fa-clock mr-1"></i> Time Log</span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-white btn-xs shadow-sm border mr-1 text-success" title="Add Time Log" onclick="addTimeLog()"><i class="fas fa-plus"></i></button>
                            <button class="btn btn-white btn-xs shadow-sm border text-info" title="View Time Log" onclick="viewTimeLogs()"><i class="far fa-eye"></i></button>
                        </div>
                    </div>

                    <!-- Pressure Log Actions -->
                    <div class="d-flex align-items-center bg-light rounded px-2 py-1 border">
                        <span class="text-xs font-weight-bold text-muted mr-2 text-uppercase"><i class="far fa-tachometer-alt mr-1"></i> Pressure Log</span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-white btn-xs shadow-sm border mr-1 text-success" title="Add Pressure Log" onclick="addPressureLog()"><i class="fas fa-plus"></i></button>
                            <button class="btn btn-white btn-xs shadow-sm border text-info" title="View Pressure Log" onclick="viewPressureLogs()"><i class="far fa-eye"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-5">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-bold w-25">Vessel Name:</td>
                                <td><?= esc($vessel->vesselName) ?></td>
                            </tr>
                            <tr>
                                <td class="text-bold">IMO Number:</td>
                                <td><?= esc($vessel->imoNumber) ?></td>
                            </tr>
                            <tr>
                                <td class="text-bold">Country:</td>
                                <td><?= esc($vessel->country) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-1 border-left d-none d-md-block"></div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-bold w-25">Voyage Number:</td>
                                <td><?= esc($voyage->voyageNumber) ?></td>
                            </tr>

                            <tr>
                                <td class="text-bold">Arrival Port:</td>
                                <td><?= esc($voyage->arrivalPortName) ?></td>
                            </tr>

                            <tr>
                                <td class="text-bold">Arrival Berth:</td>
                                <td><?= esc($voyage->arrivalBerth) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Selection Section -->
        <div class="card shadow-sm border-0" style="border-radius: 8px;">
            <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center" style="padding: 15px 20px;">
                <h5 class="mb-0 text-dark">
                    <i class="far fa-cubes mr-2 text-success"></i> Product Selection
                    <span class="badge badge-success ml-2"><?= count($products) ?> Products</span>
                </h5>
            </div>
            <div class="card-body bg-white">
                <div class="row">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $k => $p): ?>
                            <div class="col-md-4 mb-3">
                                <div class="product-card product-card-item p-3 position-relative <?= $k === 0 ? 'selected' : '' ?>"
                                    style="cursor: pointer; border-radius: 8px; min-height: 180px;"
                                    onclick="selectProduct(this)">

                                    <!-- Hidden Input -->
                                    <input type="radio" name="selected_product" value="<?= $p->voyageProductId ?>" <?= $k === 0 ? 'checked' : '' ?> style="display:none;">

                                    <h6 class="font-weight-bold mb-3">
                                        <i class="far fa-gas-pump mr-2 icon-theme"></i> <?= esc($p->productName) ?>
                                    </h6>

                                    <div class="row text-xs mb-2">
                                        <div class="col-4">
                                            <span class="d-block text-label" style="font-size: 0.7rem;">QUANTITY</span>
                                            <span class="font-weight-bold"><?= number_format($p->billOfLading, 0) ?> MT</span>
                                        </div>
                                        <div class="col-4">
                                            <span class="d-block text-label" style="font-size: 0.7rem;">LINE TYPES</span>
                                            <span class="font-weight-bold"><?= esc($p->primaryLine) ?>"</span>
                                        </div>
                                        <div class="col-4">
                                            <span class="d-block text-label" style="font-size: 0.7rem;">TBS @15°C</span>
                                            <span class="font-weight-bold"><?= esc($p->tbsDensityAtFifteen) ?></span>
                                        </div>
                                    </div>

                                    <div class="row text-xs">
                                        <div class="col-4">
                                            <span class="d-block text-label" style="font-size: 0.7rem;">TBS @20°C</span>
                                            <span class="font-weight-bold"><?= esc($p->tbsDensityAtTwenty) ?></span>
                                        </div>
                                        <div class="col-4">
                                            <span class="d-block text-label" style="font-size: 0.7rem;">LOAD @15°C</span>
                                            <span class="font-weight-bold"><?= esc($p->loadPortDensityAtFifteen) ?></span>
                                        </div>
                                        <div class="col-4">
                                            <span class="d-block text-label" style="font-size: 0.7rem;">LOAD @20°C</span>
                                            <span class="font-weight-bold"><?= esc($p->loadPortDensityAtTwenty) ?></span>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <span class="badge badge-light border text-muted font-weight-bold px-2 py-1" style="font-size: 0.7rem;">PENDING</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center text-muted py-5">
                            <i class="far fa-box-open fa-3x mb-3 text-secondary"></i>
                            <p>No products added directly.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<script>
    function selectProduct(card) {
        // Reset all cards
        document.querySelectorAll('.product-card-item').forEach(c => {
            c.classList.remove('selected');
            const radio = c.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = false;
            }
        });

        // Activate selected card
        card.classList.add('selected');
        const radio = card.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
        }
    }
</script>

<style>
    .text-xs {
        font-size: 0.85rem;
    }

    .table-borderless td {
        padding: 4px 0;
    }

    /* Product Card Themes */
    .product-card-item {
        background-color: #f8f9fa;
        /* Light background */
        color: #343a40;
        /* Dark text */
        transition: all 0.2s ease-in-out;
        border: 2px solid #e9ecef;
    }

    .text-label {
        color: #6c757d;
        /* Muted text for labels */
    }

    .icon-theme {
        color: #28a745;
        /* Success Green */
    }

    /* Selected State */
    .product-card-item.selected {
        background-color: #1e5631 !important;
        /* Dark Green */
        color: #fff !important;
        border-color: #144525;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .product-card-item.selected .text-label {
        color: rgba(255, 255, 255, 0.7) !important;
    }

    .product-card-item.selected .icon-theme {
        color: #fff !important;
    }

    .product-card-item.selected .badge {
        background-color: #fff !important;
        color: #1e5631 !important;
        /* Green text for badge */
    }
</style>


<!-- MODALS -->

<!-- Add Time Log Modal -->
<div class="modal fade" id="modal-add-time-log">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="far fa-clock mr-2"></i>Add Time Log</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="form-add-time-log">
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="logDate" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Time (24h)</label>
                                <input type="text" name="logTime" class="form-control" value="<?= date('H:i') ?>" placeholder="HH:mm" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" title="Format: HH:mm (24-hour)" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Event Description</label>
                        <textarea name="eventDescription" class="form-control" rows="3" placeholder="e.g., Vessel Arrived at Anchorage" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Time Logs Modal -->
<div class="modal fade" id="modal-view-time-logs">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="far fa-list-alt mr-2"></i>Time Logs</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Event</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="time-logs-table-body">
                        <!-- Ajax Loaded -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Pressure Log Modal -->
<div class="modal fade" id="modal-add-pressure-log">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="far fa-tachometer-alt mr-2"></i>Add Pressure Log</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="form-add-pressure-log">
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="logDate" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Time (24h)</label>
                                <input type="text" name="logTime" class="form-control" value="<?= date('H:i') ?>" placeholder="HH:mm" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" title="Format: HH:mm (24-hour)" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Pressure (Bar)</label>
                                <input type="number" step="0.01" name="pressure" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Rate (MT/Hr)</label>
                                <input type="number" step="0.01" name="rate" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Log</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Pressure Logs Modal -->
<div class="modal fade" id="modal-view-pressure-logs">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="far fa-list-alt mr-2"></i>Pressure Logs</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>Pressure</th>
                            <th>Rate</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="pressure-logs-table-body">
                        <!-- Ajax Loaded -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>

</script>

<script src="<?= base_url('assets/js/FormEngine.js') ?>"></script>

<script>
    // Configuration & Data
    var baseUrl = '<?= base_url('metrology/voyages/products'); ?>'; // Fixed Route Path
    var voyageId = <?= json_encode($voyage->voyageId) ?>;
    var vesselId = <?= json_encode($vessel->id) ?>;
</script>

<script>
    // Logic & Functions

    // FormEngine Implementations
    class TimeLogFormEngine extends FormEngine {
        beforeSubmit(formData) {
            formData.append('voyageId', voyageId);
            formData.append('vesselId', vesselId);
        }
        onSuccess(data) {
            // Check if we were editing (ID present) before resetting
            const isEdit = this.form.querySelector('[name="id"]').value !== '';

            $('#modal-add-time-log').modal('hide');
            this.form.reset();
            swal({
                icon: 'success',
                title: 'Saved!',
                text: data.msg,
                timer: 1500,
                showConfirmButton: false
            });

            // If we were editing, re-open the viewing modal
            if (isEdit) {
                viewTimeLogs();
            }
        }
    }

    class PressureLogFormEngine extends FormEngine {
        beforeSubmit(formData) {
            formData.append('voyageId', voyageId);
            formData.append('vesselId', vesselId);
        }
        onSuccess(data) {
            const isEdit = this.form.querySelector('[name="id"]').value !== '';

            $('#modal-add-pressure-log').modal('hide');
            this.form.reset();
            swal({
                icon: 'success',
                title: 'Saved!',
                text: data.msg,
                timer: 1500,
                showConfirmButton: false
            });

            // If we were editing, re-open the viewing modal
            if (isEdit) {
                viewPressureLogs();
            }
        }
    }

    // Initialize Forms
    const timeLogForm = new TimeLogFormEngine('form-add-time-log', baseUrl + '/time-logs/save');
    const pressureLogForm = new PressureLogFormEngine('form-add-pressure-log', baseUrl + '/pressure-logs/save');


    // Global functions (must be outside DOMContentLoaded for onclick to work)
    function selectProduct(card) {
        document.querySelectorAll('.product-card-item').forEach(c => {
            c.classList.remove('selected');
            const radio = c.querySelector('input[type="radio"]');
            if (radio) radio.checked = false;
        });
        card.classList.add('selected');
        const radio = card.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    function addTimeLog() {
        document.getElementById('form-add-time-log').reset();
        document.querySelector('#form-add-time-log [name="id"]').value = ''; // Clear ID
        $('#modal-add-time-log .modal-title').html('<i class="far fa-clock mr-2"></i>Add Time Log');
        $('#modal-add-time-log').modal('show');
    }

    async function editTimeLog(id) {
        try {
            const response = await fetch(baseUrl + '/time-logs/get/' + id);
            const resp = await response.json();

            if (resp.status === 1) {
                const log = resp.data;
                const form = document.getElementById('form-add-time-log');

                form.querySelector('[name="id"]').value = log.id;
                form.querySelector('[name="logDate"]').value = log.logDate;
                // Format time to HH:mm for 24h input
                const timeParts = log.logTime.split(':');
                const time24 = timeParts[0] + ':' + timeParts[1];
                form.querySelector('[name="logTime"]').value = time24;
                form.querySelector('[name="eventDescription"]').value = log.eventDescription;

                // Close view modal and open edit modal
                $('#modal-view-time-logs').modal('hide');
                $('#modal-add-time-log .modal-title').html('<i class="far fa-edit mr-2"></i>Edit Time Log');
                $('#modal-add-time-log').modal('show');
            } else {
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: resp.msg
                });
            }
        } catch (error) {
            console.error('Error fetching log:', error);
            swal({
                icon: 'error',
                title: 'Error',
                text: 'Failed to fetch log details'
            });
        }
    }

    async function viewTimeLogs() {
        try {
            const response = await fetch(baseUrl + '/time-logs/list/' + voyageId, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const resp = await response.json();

            if (resp.status === 1) {
                document.getElementById('time-logs-table-body').innerHTML = resp.html;
                $('#modal-view-time-logs').modal('show');
            } else {
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: resp.msg
                });
            }
        } catch (error) {
            console.error('Error viewing time logs:', error);
            swal({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load time logs'
            });
        }
    }

    function deleteTimeLog(id) {
        swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this entry!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then(async (willDelete) => {
                if (willDelete) {
                    try {
                        const response = await fetch(baseUrl + '/time-logs/delete/' + id, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const resp = await response.json();

                        if (resp.status === 1) {
                            swal("Poof! Your entry has been deleted!", {
                                icon: "success",
                                timer: 1500,
                                buttons: false
                            });
                            viewTimeLogs();
                        } else {
                            swal("Error", resp.msg, "error");
                        }
                    } catch (error) {
                        console.error('Error deleting time log:', error);
                        swal("Error", "Failed to delete time log", "error");
                    }
                }
            });
    }

    function addPressureLog() {
        // Removed product selection check as per request to remove productId
        document.getElementById('form-add-pressure-log').reset();
        document.querySelector('#form-add-pressure-log [name="id"]').value = ''; // Clear ID
        $('#modal-add-pressure-log .modal-title').html('<i class="far fa-tachometer-alt mr-2"></i>Add Pressure Log');
        $('#modal-add-pressure-log').modal('show');
    }

    async function editPressureLog(id) {
        try {
            const response = await fetch(baseUrl + '/pressure-logs/get/' + id);
            const resp = await response.json();

            if (resp.status === 1) {
                const log = resp.data;
                const form = document.getElementById('form-add-pressure-log');

                form.querySelector('[name="id"]').value = log.id;
                form.querySelector('[name="logDate"]').value = log.logDate;
                // Format time to HH:mm for 24h input
                const timeParts = log.logTime.split(':');
                const time24 = timeParts[0] + ':' + timeParts[1];
                form.querySelector('[name="logTime"]').value = time24;
                form.querySelector('[name="pressure"]').value = log.pressure;
                form.querySelector('[name="rate"]').value = log.rate;

                // Close view modal and open edit modal
                $('#modal-view-pressure-logs').modal('hide');
                $('#modal-add-pressure-log .modal-title').html('<i class="far fa-edit mr-2"></i>Edit Pressure Log');
                $('#modal-add-pressure-log').modal('show');
            } else {
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: resp.msg
                });
            }
        } catch (error) {
            console.error('Error fetching log:', error);
            swal({
                icon: 'error',
                title: 'Error',
                text: 'Failed to fetch log details'
            });
        }
    }

    async function viewPressureLogs() {
        try {
            const response = await fetch(baseUrl + '/pressure-logs/list/' + voyageId, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const resp = await response.json();

            if (resp.status === 1) {
                document.getElementById('pressure-logs-table-body').innerHTML = resp.html;
                $('#modal-view-pressure-logs').modal('show');
            } else {
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: resp.msg
                });
            }
        } catch (error) {
            console.error('Error viewing pressure logs:', error);
            swal({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load pressure logs'
            });
        }
    }

    function deletePressureLog(id) {
        swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this entry!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then(async (willDelete) => {
                if (willDelete) {
                    try {
                        const response = await fetch(baseUrl + '/pressure-logs/delete/' + id, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const resp = await response.json();

                        if (resp.status === 1) {
                            swal("Poof! Your entry has been deleted!", {
                                icon: "success",
                                timer: 1500,
                                buttons: false
                            });
                            viewPressureLogs();
                        } else {
                            swal("Error", resp.msg, "error");
                        }
                    } catch (error) {
                        console.error('Error deleting pressure log:', error);
                        swal("Error", "Failed to delete pressure log", "error");
                    }
                }
            });
    }
</script>
<?= $this->endSection() ?>