<?= $this->extend('Layouts/coreLayout'); ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Voyages Management</h1>
            </div>
        </div>
    </div>
</div>

<style>
    .voyage-selected {
        background-color: #e2e6ea !important;
        /* Nice light gray */
        font-weight: 500;
    }
</style>
<div class="content">
    <div class="container-fluid">
        <!-- Vessel Selection & Details Card -->
        <div class="card ">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="far fa-ship mr-2"></i> Select Vessel</h3>
                    <div style="width: 300px;">
                        <input type="hidden" class="token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                        <select class="form-control select2bs4" id="vesselSelect" style="width: 100%;">
                            <option value="">Choose a Vessel...</option>
                            <?php foreach ($vessels as $vessel): ?>
                                <option value="<?= $vessel->id ?>"><?= esc($vessel->vesselName) ?> (IMO: <?= esc($vessel->imoNumber) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body" id="vesselDetailsSection" style="display: none;">
                <!-- Content Injected via JS -->
                <div id="vesselDetailsContent"></div>
            </div>
        </div>

        <div class="row" id="manageSection" style="display: none;">
            <!-- Voyages List -->
            <div class="col-md-7">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="far fa-map mr-1"></i> Voyages List</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" onclick="addVoyage()">
                                <i class="far fa-plus"></i> New Voyage
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th>Voyage Number</th>
                                    <th>Arrival Port</th>
                                    <th>Arrival Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="voyagesTableBody">
                                <tr>
                                    <td colspan="4" class="text-center">Select a vessel to view voyages.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Voyage Products List -->
            <div class="col-md-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="far fa-list-alt mr-1"></i> Voyage Products</h3>
                        <span id="selectedVoyageRef" class="ml-2 text-muted text-sm" style="display:none;"></span>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="btnAddProduct" onclick="addProduct()" disabled>
                                <i class="far fa-plus"></i> Add Product
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>B/L (MT)</th>
                                    <th>Density @15</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <tr>
                                    <td colspan="4" class="text-center">Select a voyage to view products.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>

<!-- Add Voyage Modal -->
<div class="modal fade" id="voyageModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="voyageModalLabel">Add New Voyage</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="voyageForm">
                <div class="modal-body">
                    <input type="hidden" id="voyageVesselId" name="vesselId">
                    <input type="hidden" id="editVoyageId" name="voyageId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vessel Experience Factor (VEF)</label>
                                <input type="number" step="0.000001" class="form-control" name="vesselExperienceFactor" placeholder="0.000000">
                            </div>
                        </div>
                        <!-- Auto-generated ID and Number will be handled on backend -->
                        <div class="col-md-6">
                            <p class="text-muted mt-4"><i class="fas fa-info-circle"></i> Reference ID and Voyage Number will be auto-generated.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Loading Port</label>
                                <input type="text" class="form-control" name="loadingPort">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Loading Date</label>
                                <input type="date" class="form-control" name="loadingDate">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Arrival Port <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4-modal" name="arrivalPort" style="width: 100%;" required>
                                    <option value="">Select Port</option>
                                    <?php foreach ($ports as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= esc($p['portName']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Arrival Berth <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4-modal" name="arrivalBerth" style="width: 100%;" required>
                                    <option value="">Select Berth</option>
                                    <?php foreach ($berths as $b): ?>
                                        <option value="<?= $b['id'] ?>"><?= esc($b['berthName']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Arrival Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="arrivalDate" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Voyage</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Voyage Product Modal -->
<div class="modal fade" id="productModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Voyage Product</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="productForm">
                <input type="hidden" name="voyageId" id="productVoyageId">
                <input type="hidden" name="voyageProductId" id="editVoyageProductId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Product <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4-modal" name="productId" id="productSelect" style="width: 100%;" required>
                                    <!-- JS Populated -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bill of Lading (MT)</label>
                                <input type="number" step="any" class="form-control" name="billOfLading" placeholder="Bill Of Lading">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card ">
                                <div class="card-header">
                                    <h5 class="card-title text-sm">Load Port Parameters</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <label>Density @ 15&deg;C</label>
                                            <input type="number" step="0.00001" class="form-control density-input" data-target="loadPortWCFTAtFifteen" name="loadPortDensityAtFifteen">
                                        </div>
                                        <div class="col-6">
                                            <label>WCFT @ 15&deg;C</label>
                                            <input type="number" step="0.00001" class="form-control" name="loadPortWCFTAtFifteen" readonly>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <label>Density @ 20&deg;C</label>
                                            <input type="number" step="0.00001" class="form-control density-input" data-target="loadPortWCFTAtTwenty" name="loadPortDensityAtTwenty">
                                        </div>
                                        <div class="col-6">
                                            <label>WCFT @ 20&deg;C</label>
                                            <input type="number" step="0.00001" class="form-control" name="loadPortWCFTAtTwenty" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card  ">
                                <div class="card-header">
                                    <h5 class="card-title text-sm">TBS Parameters</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <label>Density @ 15&deg;C</label>
                                            <input type="number" step="0.00001" class="form-control density-input" data-target="tbsWCFTAtFifteen" name="tbsDensityAtFifteen">
                                        </div>
                                        <div class="col-6">
                                            <label>WCFT @ 15&deg;C</label>
                                            <input type="number" step="0.00001" class="form-control" name="tbsWCFTAtFifteen" readonly>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <label>Density @ 20&deg;C</label>
                                            <input type="number" step="0.00001" class="form-control density-input" data-target="tbsWCFTAtTwenty" name="tbsDensityAtTwenty">
                                        </div>
                                        <div class="col-6">
                                            <label>WCFT @ 20&deg;C</label>
                                            <input type="number" step="0.00001" class="form-control" name="tbsWCFTAtTwenty" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="text-secondary mt-2">Line Configuration</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Primary Line</label>
                            <select class="form-control select2bs4-modal" name="primaryLine">
                                <option value="">Select Line...</option>
                                <?php for ($i = 2; $i <= 48; $i += 2): ?>
                                    <option value="<?= $i ?>"><?= $i ?>-inch</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Secondary Line</label>
                            <select class="form-control select2bs4-modal" name="secondaryLine">
                                <option value="">Select Line...</option>
                                <option value="none">None</option>
                                <?php for ($i = 2; $i <= 48; $i += 2): ?>
                                    <option value="<?= $i ?>"><?= $i ?>-inch</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Product Details Modal (View Only) -->
<div class="modal fade" id="productDetailsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header ">
                <h4 class="modal-title">Voyage Product Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="productDetailsBody">
                <!-- Content Populated via JS -->
                <div class="row">
                    <div class="col-md-6">
                        <strong>Product:</strong> <span id="viewProductName"></span><br>
                        <strong>Bill of Lading:</strong> <span id="viewBillOfLading"></span> MT<br>
                        <strong>Line Configuration:</strong> <span id="viewPrimaryLine"></span> & <span id="viewSecondaryLine"></span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card  ">
                            <div class="card-header">
                                <h5 class="card-title text-sm">Load Port Parameters</h5>
                            </div>
                            <div class="card-body p-2">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td>Density @ 15&deg;C:</td>
                                        <td id="viewLoadDensity15"></td>
                                    </tr>
                                    <tr>
                                        <td>WCFT @ 15&deg;C:</td>
                                        <td id="viewLoadWCFT15"></td>
                                    </tr>
                                    <tr>
                                        <td>Density @ 20&deg;C:</td>
                                        <td id="viewLoadDensity20"></td>
                                    </tr>
                                    <tr>
                                        <td>WCFT @ 20&deg;C:</td>
                                        <td id="viewLoadWCFT20"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card  ">
                            <div class="card-header">
                                <h5 class="card-title text-sm">TBS Parameters</h5>
                            </div>
                            <div class="card-body p-2">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td>Density @ 15&deg;C:</td>
                                        <td id="viewTbsDensity15"></td>
                                    </tr>
                                    <tr>
                                        <td>WCFT @ 15&deg;C:</td>
                                        <td id="viewTbsWCFT15"></td>
                                    </tr>
                                    <tr>
                                        <td>Density @ 20&deg;C:</td>
                                        <td id="viewTbsDensity20"></td>
                                    </tr>
                                    <tr>
                                        <td>WCFT @ 20&deg;C:</td>
                                        <td id="viewTbsWCFT20"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/FormEngine.js') ?>"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
    // Constants
    const BASE_URL = '<?= base_url('metrology/voyages') ?>';
    const CSRF_TOKEN = '<?= csrf_token() ?>';
    let currentVesselId = null;
    let currentVoyageId = null;

    // Initialize Select2 (jQuery dependency is unavoidable for Select2)
    $(document).ready(function() {
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        });
        $('.select2bs4-modal').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#voyageModal')
        });
        $('#productSelect').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#productModal')
        });

        // Manual listener because Select2 events are jQuery events
        $('#vesselSelect').on('change', async function() {
            const vesselId = $(this).val();
            await handleVesselSelection(vesselId);
        });

        loadProductsList();

        // Auto-calculate WCFT
        $(document).on('input', '.density-input', function() {
            const density = parseFloat($(this).val());
            const targetName = $(this).data('target');
            const targetInput = $(`input[name="${targetName}"]`);

            if (!isNaN(density)) {
                // Formula: Density - 0.0011
                const wcft = (density - 0.0011).toFixed(5);
                targetInput.val(wcft);
            } else {
                targetInput.val('');
            }
        });

        // Reset modals on close to clear edit data
        $('#voyageModal').on('hidden.bs.modal', function() {
            document.getElementById('voyageForm').reset();
            $('#editVoyageId').val(''); // Clear hidden ID
            $('.select2bs4-modal').val('').trigger('change');
            $('#voyageModalLabel').text('Add New Voyage');
        });

        $('#productModal').on('hidden.bs.modal', function() {
            document.getElementById('productForm').reset();
            $('#editVoyageProductId').val(''); // Clear hidden ID
            $('#productSelect').val('').trigger('change');
            $('#productModalLabel').text('Add Voyage Product');
            $('#productSelect').prop('disabled', false); // Enable product select
        });
    });

    // --- Async Functions using Fetch API ---

    async function handleVesselSelection(vesselId) {
        if (!vesselId) {
            document.getElementById('vesselDetailsSection').style.display = 'none';
            document.getElementById('manageSection').style.display = 'none';
            currentVesselId = null;
            return;
        }

        currentVesselId = vesselId;

        try {
            const response = await fetch(`${BASE_URL}/details/${vesselId}`);
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();

            if (data.status === 1) {
                document.getElementById('vesselDetailsContent').innerHTML = data.html;
                document.getElementById('vesselDetailsSection').style.display = 'block';
                document.getElementById('manageSection').style.display = 'flex'; // row uses flex

                await loadVoyages(vesselId);
                resetProductSection();
            } else {
                console.error(data.msg);
            }
        } catch (error) {
            console.error('Error fetching details:', error);
            swal("Error", "Failed to detailed information", "error");
        }
    }

    async function loadVoyages(vesselId) {
        try {
            const response = await fetch(`${BASE_URL}/list/${vesselId}`);
            const data = await response.json();

            if (data.status === 1) {
                document.getElementById('voyagesTableBody').innerHTML = data.html;
            }
        } catch (error) {
            console.error('Error loading voyages:', error);
        }
    }

    // Called by invalid HTML onclick attribute, handled globally
    window.selectVoyage = async function(voyageId, voyageNumber) {
        currentVoyageId = voyageId;

        // Highlight logic (native JS)
        document.querySelectorAll('.voyage-row').forEach(row => row.classList.remove('voyage-selected')); // Remove prev highlight
        const activeRow = document.querySelector(`.voyage-row[data-id="${voyageId}"]`);
        if (activeRow) {
            activeRow.classList.add('voyage-selected'); // Use custom class
            // Ensure text remains visible/dark on light bg (bootstrap default usually fine)
        }

        // UI Updates
        const badge = document.getElementById('selectedVoyageRef');
        badge.innerText = `(${voyageNumber})`;
        badge.style.display = 'inline-block';
        document.getElementById('btnAddProduct').disabled = false;

        await loadVoyageProducts(voyageId);
    }

    async function loadVoyageProducts(voyageId) {
        try {
            const response = await fetch(`${BASE_URL}/products/list/${voyageId}`);
            const data = await response.json();

            if (data.status === 1) {
                document.getElementById('productsTableBody').innerHTML = data.html;
            }
        } catch (error) {
            console.error('Error loading products:', error);
        }
    }

    async function loadProductsList() {
        try {
            const response = await fetch(`${BASE_URL}/products/list_all`);
            const data = await response.json();

            if (data.products) {
                let opts = '<option value="">Select Product...</option>';
                data.products.forEach(p => {
                    opts += `<option value="${p.id}">${p.productName}</option>`;
                });
                // Select2 needs jQuery to update options properly if DOM changes
                $('#productSelect').html(opts);
            }
        } catch (error) {
            console.log(error);
        }
    }

    // --- Modal Actions ---
    window.addVoyage = function() {
        if (!currentVesselId) return;
        document.getElementById('voyageVesselId').value = currentVesselId;
        $('#voyageModalLabel').text('New Voyage');
        // Reset handled by modal event
        $('#voyageModal').modal('show');
    }

    window.addProduct = function() {
        if (!currentVoyageId) return;
        document.getElementById('productVoyageId').value = currentVoyageId;
        // Reset handled by modal event
        $('#productModal').modal('show');
    }

    window.editVoyage = async function(voyageId) {
        try {
            const response = await fetch(`${BASE_URL}/edit/${voyageId}`);
            const data = await response.json();

            if (data.status === 1) {
                const v = data.data;
                $('#editVoyageId').val(v.voyageId);
                $('#voyageVesselId').val(v.vesselId);

                $('input[name="vesselExperienceFactor"]').val(v.vesselExperienceFactor);
                $('select[name="loadingPort"]').val(v.loadingPort).trigger('change');

                $('select[name="arrivalPort"]').val(v.arrivalPort).trigger('change');
                $('select[name="arrivalBerth"]').val(v.arrivalBerth).trigger('change');

                // Dates: format YYYY-MM-DD for date input
                if (v.loadingDate) $('input[name="loadingDate"]').val(v.loadingDate.split(' ')[0]);
                if (v.arrivalDate) $('input[name="arrivalDate"]').val(v.arrivalDate.split(' ')[0]);

                $('#voyageModalLabel').text('Editing Voyage');
                $('#voyageModal').modal('show');
            } else {
                swal("Error", data.msg, "error");
            }
        } catch (e) {
            console.error(e);
        }
    }

    window.editProduct = async function(id) {
        try {
            const response = await fetch(`${BASE_URL}/products/get/${id}`);
            const data = await response.json();
            if (data.status === 1) {
                const p = data.data;
                $('#editVoyageProductId').val(p.voyageProductId);
                $('#productVoyageId').val(p.voyageId);

                $('#productSelect').val(p.productId).trigger('change');
                // Disable product selection on edit if desired, usually safer to allow but whatever
                // $('input[name="billOfLading"]').val(p.billOfLading);

                // Populate all fields
                const fields = [
                    'billOfLading',
                    'loadPortDensityAtFifteen', 'loadPortWCFTAtFifteen', 'loadPortDensityAtTwenty', 'loadPortWCFTAtTwenty',
                    'tbsDensityAtFifteen', 'tbsWCFTAtFifteen', 'tbsDensityAtTwenty', 'tbsWCFTAtTwenty',
                    'primaryLine', 'secondaryLine'
                ];

                fields.forEach(f => {
                    $(`[name="${f}"]`).val(p[f]);
                });

                $('#productModalLabel').text('Edit Product');
                $('#productModal').modal('show');
            }
        } catch (e) {
            console.error(e);
        }
    }

    window.viewProduct = async function(id) {
        try {
            const response = await fetch(`${BASE_URL}/products/get/${id}`);
            const data = await response.json();
            if (data.status === 1) {
                const p = data.data;
                $('#viewProductName').text(p.productName);
                $('#viewBillOfLading').text(p.billOfLading);
                $('#viewPrimaryLine').text(p.primaryLine + ' Inches');
                $('#viewSecondaryLine').text(p.secondaryLine + ' Inches');

                $('#viewLoadDensity15').text(p.loadPortDensityAtFifteen);
                $('#viewLoadWCFT15').text(p.loadPortWCFTAtFifteen);
                $('#viewLoadDensity20').text(p.loadPortDensityAtTwenty);
                $('#viewLoadWCFT20').text(p.loadPortWCFTAtTwenty);

                $('#viewTbsDensity15').text(p.tbsDensityAtFifteen);
                $('#viewTbsWCFT15').text(p.tbsWCFTAtFifteen);
                $('#viewTbsDensity20').text(p.tbsDensityAtTwenty);
                $('#viewTbsWCFT20').text(p.tbsWCFTAtTwenty);

                $('#productDetailsModal').modal('show');
            }
        } catch (e) {
            console.error(e);
        }
    }

    function resetProductSection() {
        currentVoyageId = null;
        document.getElementById('selectedVoyageRef').style.display = 'none';
        document.getElementById('btnAddProduct').disabled = true;
        document.getElementById('productsTableBody').innerHTML = '<tr><td colspan="4" class="text-center">Select a voyage to view products.</td></tr>';
    }

    // --- Deletions (Fetch) ---
    window.deleteVoyage = function(id) {
        swal({
            title: "Are you sure?",
            text: "This will delete the voyage and all associated data.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then(async (willDelete) => {
            if (willDelete) {
                try {
                    const formData = new FormData();
                    const tokenVal = document.querySelector('.token').value;
                    formData.append(CSRF_TOKEN, tokenVal);

                    const response = await fetch(`${BASE_URL}/delete/${id}`, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    // Update Token
                    if (data.token) document.querySelectorAll('.token').forEach(el => el.value = data.token);

                    if (data.status === 1) {
                        swal("Deleted!", data.msg, "success");
                        await loadVoyages(currentVesselId);
                        resetProductSection();
                    } else {
                        swal("Error", data.msg, "error");
                    }
                } catch (error) {
                    swal("Error", "Network error occurred", "error");
                }
            }
        });
    }

    window.deleteProduct = function(id) {
        swal({
            title: "Are you sure?",
            text: "This will delete this product entry.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then(async (willDelete) => {
            if (willDelete) {
                try {
                    const formData = new FormData();
                    const tokenVal = document.querySelector('.token').value;
                    formData.append(CSRF_TOKEN, tokenVal);

                    const response = await fetch(`${BASE_URL}/products/delete/${id}`, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    // Update Token
                    if (data.token) document.querySelectorAll('.token').forEach(el => el.value = data.token);

                    if (data.status === 1) {
                        swal("Deleted!", data.msg, "success");
                        await loadVoyageProducts(currentVoyageId);
                    } else {
                        swal("Error", data.msg, "error");
                    }
                } catch (error) {
                    swal("Error", "Network error occurred", "error");
                }
            }
        });
    }

    // --- Form Handling ---
    // Note: FormEngine.js is a custom class. If it uses $.ajax internally, 
    // we might need to update it or replace its usage here if STRICT strict vanilla is required.
    // However, usually FormEngine is a wrapper. I will proceed using it as it handles validation display nicely.
    // If user insists on NO ajax, I'd have to rewrite form submission here too. 
    // "Avoid using $ajax, only use jquery for minor stuff... FormEngine.js" 
    // Assuming FormEngine.js uses $.ajax, I should probably replace it with manual fetch here to adhere to strict rules,
    // OR just use it if user accepts it.
    // Given "Always use fetch api... avoid using $ajax", I will implement custom fetch submission for these forms 
    // and NOT use FormEngine to be safe, OR I assume FormEngine is acceptable for forms.
    // I previously looked at TankFormEngine and it used $.ajax. 
    // I will write a small helper "submitForm" using fetch to replace FormEngine for these specific forms.

    async function submitFetchForm(formId, url, successCallback) {
        const form = document.getElementById(formId);
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const tokenVal = document.querySelector('.token').value;
            formData.append(CSRF_TOKEN, tokenVal);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                // Update Token
                if (data.token) document.querySelectorAll('.token').forEach(el => el.value = data.token);

                if (data.status === 1) {
                    successCallback(data);
                } else if (data.status === 0 && data.errors) {
                    // Start simplified error display (or use logic similar to FormEngine)
                    // For now, simple alert or mapping
                    let msg = "";
                    for (const [key, value] of Object.entries(data.errors)) {
                        msg += `${value}\n`;
                        // Basic Bootstrap invalid-feedback toggle could go here
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            // Add removal listener
                            input.addEventListener('input', () => input.classList.remove('is-invalid'), {
                                once: true
                            });
                        }
                    }
                    if (msg) swal("Validation Error", msg, "error");
                } else {
                    swal("Error", data.msg || "Unknown error", "error");
                }
            } catch (error) {
                console.error(error);
                swal("Error", "Submission failed", "error");
            }
        });
    }

    // Bind Forms
    submitFetchForm('voyageForm', '<?= base_url('metrology/voyages/save') ?>', (data) => {
        $('#voyageModal').modal('hide');
        swal("Success", data.msg, "success");
        // Update table directly from response html
        document.getElementById('voyagesTableBody').innerHTML = data.html;
    });

    submitFetchForm('productForm', '<?= base_url('metrology/voyages/products/save') ?>', (data) => {
        $('#productModal').modal('hide');
        swal("Success", "Product saved", "success");
        document.getElementById('productsTableBody').innerHTML = data.html;
    });

    $(function() {
        $('.select2bs4-modal').select2({
            theme: 'bootstrap4'
        });
    });
</script>
<?= $this->endSection() ?>