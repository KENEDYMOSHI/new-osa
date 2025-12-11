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
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Left Side: Vessels -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Registered Vessels
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" onclick="addVessel()">
                                <i class="far fa-plus"></i> Add Vessel
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="vesselsTable" class="table table-bordered table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vessel Name</th>
                                        <th>IMO Number</th>
                                        <th>Country</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="vesselsTableBody">
                                    <?= $vesselsHtml ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Tanks -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Vessel Tanks <span id="selectedVesselName" class="text-muted font-weight-normal"></span>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" onclick="addTank()" id="addTankBtn" disabled>
                                <i class="far fa-plus"></i> Add Tanks
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tanksTable" class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Tank Name</th>
                                        <th style="width: 100px" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tanksTableBody">
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Select a vessel to view tanks</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add/Edit Vessel Modal -->
<div class="modal fade" id="vesselModal" tabindex="" role="dialog" aria-labelledby="vesselModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vesselModalLabel">Add Vessel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="vesselForm">
                <input type="hidden" class="token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                <input type="hidden" id="id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="vesselName">Vessel Name</label>
                        <input type="text" class="form-control" id="vesselName" name="vesselName" >
                    </div>
                    <div class="form-group">
                        <label for="imoNumber">IMO Number</label>
                        <input type="text" class="form-control" id="imoNumber" name="imoNumber" >
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select class="form-control select2bs4" id="country" name="country" style="width: 100%;" >
                            <option value="">Select Country</option>
                            <?php foreach ($countries as $code => $name): ?>
                                <option value="<?= $name ?>"><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Tanks Modal -->
<div class="modal fade" id="tankModal" tabindex="-1" role="dialog" aria-labelledby="tankModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tankModalLabel">Add Tanks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="tankForm">
                <!-- Token will be appended by JS or use existing if persistent -->
                <input type="hidden" class="token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                <input type="hidden" id="tankVesselId" name="vesselId">
                <div class="modal-body">
                    <div id="tankInputs">
                        <div class="d-flex mb-2 tank-row align-items-start">
                            <div class="flex-grow-1 mr-2">
                                <input type="text" class="form-control" name="tankNames[]" placeholder="Tank Name">
                            </div>
                            <button class="btn btn-danger remove-tank" type="button" style="height: 38px;"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success btn-xs mt-2" onclick="addTankInput()">
                        <i class="fas fa-plus"></i> Add Another Tank
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Tanks</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Tank Modal -->
<div class="modal fade" id="editTankModal" tabindex="-1" role="dialog" aria-labelledby="editTankModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTankModalLabel">Edit Tank</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTankForm">
                <input type="hidden" class="token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                <input type="hidden" id="editTankId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editTankName">Tank Name</label>
                        <input type="text" class="form-control" id="editTankName" name="tankName" >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Update Tank</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card-header {
        border-top: none !important;
    }

    /* Remove validation icon from input */
    .form-control.is-invalid {
        background-image: none !important;
        padding-right: 0.75rem !important;
    }
</style>
<script src="<?= base_url('assets/js/FormEngine.js') ?>"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    let currentVesselId = null;

    $(document).ready(function() {
        $('#vesselsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "lengthChange": false
        });
        $('#country').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#vesselModal')
        });
    });

    // --- Vessel Form Engine ---
    class VesselFormEngine extends FormEngine {
        constructor(formId, url, modalId, tableBodyId) {
            super(formId, url);
            this.modalId = modalId;
            this.tableBodyId = tableBodyId;
        }

        onSuccess(data) {
            $(`#${this.modalId}`).modal('hide');
            swal({
                title: "Success",
                text: data.msg,
                icon: "success"
            });

            // Re-init generic token update
            if (data.token) {
                $('.token').val(data.token);
            }

            if ($.fn.DataTable.isDataTable('#vesselsTable')) {
                $('#vesselsTable').DataTable().destroy();
            }
            document.getElementById(this.tableBodyId).innerHTML = data.html;
            $('#vesselsTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "lengthChange": false
            });
        }
    }

    const vesselForm = new VesselFormEngine('vesselForm', '<?= base_url('metrology/vessels/save') ?>', 'vesselModal', 'vesselsTableBody');

    // --- Tank Form Engine ---
    class TankFormEngine extends FormEngine {
        constructor(formId, url, modalId, tableBodyId) {
            super(formId, url);
            this.modalId = modalId;
            this.tableBodyId = tableBodyId;
        }

        showErrors(errors) {
            // Don't call clearErrors here to prevent flickering

            // 1. Handle Multiple Tank Inputs (Add Modal)
            const tankInputs = document.querySelectorAll('#tankInputs input[name="tankNames[]"]');

            if (tankInputs.length > 0) {
                tankInputs.forEach((input, index) => {
                    const key = 'tankNames.' + index;
                    const inputContainer = input.closest('.flex-grow-1'); // Find the parent div with flex-grow-1

                    if (!inputContainer) return;

                    if (errors[key]) {
                        const message = errors[key];
                        input.classList.add('is-invalid');

                        // Check if feedback already exists in the container
                        let feedback = inputContainer.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.textContent = message;
                        } else {
                            feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback d-block';
                            feedback.textContent = message;
                            inputContainer.appendChild(feedback);
                        }

                        // Clear on type
                        const clearError = () => {
                            input.classList.remove('is-invalid');
                            const fb = inputContainer.querySelector('.invalid-feedback');
                            if (fb) fb.remove();
                            input.removeEventListener('input', clearError);
                        };
                        input.removeEventListener('input', clearError); // Remove previous listener to avoid duplicates
                        input.addEventListener('input', clearError);

                    } else {
                        input.classList.remove('is-invalid');
                        const fb = inputContainer.querySelector('.invalid-feedback');
                        if (fb) fb.remove();
                    }
                });
            }

            // 2. Handle Single Tank Input (Edit Modal)
            if (errors.tankName) {
                const editInput = document.getElementById('editTankName');
                if (editInput) {
                    editInput.classList.add('is-invalid');
                    // Logic similar to above for edit...
                    let feedback = editInput.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = errors.tankName;
                    } else {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback d-block';
                        feedback.textContent = errors.tankName;
                        if (editInput.nextSibling) {
                            editInput.parentNode.insertBefore(feedback, editInput.nextSibling);
                        } else {
                            editInput.parentNode.appendChild(feedback);
                        }
                    }
                    const clearError = () => {
                        editInput.classList.remove('is-invalid');
                        const fb = editInput.nextElementSibling;
                        if (fb && fb.classList.contains('invalid-feedback')) fb.remove();
                        editInput.removeEventListener('input', clearError);
                    };
                    editInput.removeEventListener('input', clearError);
                    editInput.addEventListener('input', clearError);
                }
            }

            if (errors.vesselId) {
                swal("Error", errors.vesselId, "error");
            }
        }

        clearErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        }

        onSuccess(data) {
            $(`#${this.modalId}`).modal('hide');
            swal({
                title: "Success",
                text: data.msg,
                icon: "success"
            });

            if (data.token) {
                $('.token').val(data.token);
            }
            $('#tanksTableBody').html(data.html);
        }
    }

    const tankForm = new TankFormEngine('tankForm', '<?= base_url('metrology/vessels/tanks/save') ?>', 'tankModal', 'tanksTableBody');
    const editTankFormEngine = new TankFormEngine('editTankForm', '<?= base_url('metrology/vessels/tanks/update') ?>', 'editTankModal', 'tanksTableBody');


    // --- Tank Functions ---

    function selectVessel(id, name) {
        currentVesselId = id;
        $('#selectedVesselName').text(' - ' + name);
        $('#addTankBtn').prop('disabled', false);
        $('#tankVesselId').val(id);

        // Highlight selected row
        $('#vesselsTableBody tr').removeClass('bg-primary');
        // This relies on the click event target, but since we redraw table, we might need a better way.
        // For now, simpler to just fetch data.

        fetchTanks(id);
    }

    function fetchTanks(vesselId) {
        $.post('<?= base_url('metrology/vessels/tanks/get') ?>', {
            vesselId: vesselId,
            <?= csrf_token() ?>: $('.token').val()
        }, function(response) {
            if (response.token) $('.token').val(response.token);

            if (response.status === 1) {
                $('#tanksTableBody').html(response.html);
            }
        });
    }

    // Reset modal on open
    $('#tankModal').on('show.bs.modal', function(e) {
        document.getElementById('tankForm').reset();
        document.getElementById('tankInputs').innerHTML = `
            <div class="d-flex mb-2 tank-row align-items-start">
                <div class="flex-grow-1 mr-2">
                    <input type="text" class="form-control" name="tankNames[]" placeholder="Tank Name">
                </div>
                <button class="btn btn-danger remove-tank" type="button" style="height: 38px;"><i class="fas fa-times"></i></button>
            </div>
        `;
        tankForm.clearErrors();
    });

    function addTank() {
        if (!currentVesselId) return;
        // The show.bs.modal event handler already resets the form and inputs
        $('#tankVesselId').val(currentVesselId);
        $('#tankModal').modal('show');
    }

    function addTankInput() {
        const wrapper = document.createElement('div');
        wrapper.className = 'd-flex mb-2 tank-row align-items-start';
        wrapper.innerHTML = `
            <div class="flex-grow-1 mr-2">
                <input type="text" class="form-control" name="tankNames[]" placeholder="Tank Name">
            </div>
            <button class="btn btn-danger remove-tank" type="button" style="height: 38px;"><i class="fas fa-times"></i></button>
        `;
        document.getElementById('tankInputs').appendChild(wrapper);
    }

    $(document).on('click', '.remove-tank', function() {
        const rows = document.querySelectorAll('#tankInputs .tank-row');
        const tankRow = $(this).closest('.tank-row');

        if (rows.length > 1) {
            // Remove existing errors before removing row
            const feedback = tankRow.find('.invalid-feedback');
            if (feedback.length) feedback.remove();
            tankRow.remove();
        } else {
            // Clear input and error
            const input = tankRow.find('input');
            if (input.length) {
                input.val('');
                input.removeClass('is-invalid');
            }
            const feedback = tankRow.find('.invalid-feedback');
            if (feedback.length) feedback.remove();
        }
    });

    // Add Tank Form Submission (now handled by TankFormEngine)
    // $('#tankForm').on('submit', function(e) { e.preventDefault(); tankForm.submit(); });

    // Edit Tank Functions
    function editTank(id, name) {
        $('#editTankId').val(id);
        $('#editTankName').val(name);
        editTankFormEngine.clearErrors(); // Clear errors when opening edit modal
        $('#editTankModal').modal('show');
    }

    // Edit Tank Form Submission (now handled by TankFormEngine)
    // $('#editTankForm').on('submit', function(e) { e.preventDefault(); editTankFormEngine.submit(); });


    function deleteTank(id) {
        swal({
            title: "Are you sure?",
            text: "This tank will be permanently deleted!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.post('<?= base_url('metrology/vessels/tanks/delete') ?>', {
                    id: id,
                    <?= csrf_token() ?>: $('.token').val()
                }, function(response) {
                    if (response.token) $('.token').val(response.token);

                    if (response.status === 1) {
                        $('#tanksTableBody').html(response.html);
                        swal("Deleted!", response.msg, "success");
                    } else {
                        swal("Error", response.msg, "error");
                    }
                });
            }
        });
    }

    // Existing Vessel Functions
    function addVessel() {
        document.getElementById('vesselForm').reset();
        $('#country').val('').trigger('change');
        document.getElementById('id').value = '';
        $('#vesselModalLabel').text('Add Vessel');
        vesselForm.clearErrors();
        $('#vesselModal').modal('show');
    }

    function editVessel(payload) {
        const data = typeof payload === 'string' ? JSON.parse(payload) : payload;
        document.getElementById('vesselForm').reset();
        vesselForm.clearErrors();

        document.getElementById('id').value = data.id;
        document.getElementById('vesselName').value = data.vesselName;
        document.getElementById('imoNumber').value = data.imoNumber;
        $('#country').val(data.country).trigger('change');

        $('#vesselModalLabel').text('Edit Vessel');
        $('#vesselModal').modal('show');
    }

    function deleteVessel(id) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this vessel!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.post('<?= base_url('metrology/vessels/delete') ?>', {
                    id: id,
                    <?= csrf_token() ?>: $('.token').val()
                }, function(response) {
                    if (response.token) {
                        $('.token').val(response.token);
                    }
                    if (response.status === 1) {
                        swal("Deleted!", response.msg, "success");
                        if ($.fn.DataTable.isDataTable('#vesselsTable')) {
                            $('#vesselsTable').DataTable().destroy();
                        }
                        $('#vesselsTableBody').html(response.html);
                        $('#vesselsTable').DataTable({
                            "responsive": true,
                            "autoWidth": false,
                            "lengthChange": false
                        });
                        // Reset Right Side
                        currentVesselId = null;
                        $('#selectedVesselName').text('');
                        $('#addTankBtn').prop('disabled', true);
                        $('#tanksTableBody').html('<tr><td colspan="3" class="text-center text-muted">Select a vessel to view tanks</td></tr>');

                    } else {
                        swal("Error", response.msg, "error");
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection(); ?>