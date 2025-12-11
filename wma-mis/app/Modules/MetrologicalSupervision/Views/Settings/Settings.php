<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
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
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="settings-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="products-tab" data-toggle="pill" href="#products" role="tab" aria-controls="products" aria-selected="true">
                            <i class="far fa-boxes mr-1"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="ports-tab" data-toggle="pill" href="#ports" role="tab" aria-controls="ports" aria-selected="false">
                            <i class="far fa-anchor mr-1"></i> Ports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="berths-tab" data-toggle="pill" href="#berths" role="tab" aria-controls="berths" aria-selected="false">
                            <i class="far fa-ship mr-1"></i> Berths
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="terminals-tab" data-toggle="pill" href="#terminals" role="tab" aria-controls="terminals" aria-selected="false">
                            <i class="far fa-industry mr-1"></i> Terminals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="documents-tab" data-toggle="pill" href="#documents" role="tab" aria-controls="documents" aria-selected="false">
                            <i class="far fa-file-alt mr-1"></i> Documents
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="settings-tabContent">

                    <!-- PRODUCTS TAB -->
                    <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="products-tab">
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" onclick="addProduct()">
                                <i class="far fa-plus"></i> Add Product
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <?= $productsHtml ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- PORTS TAB -->
                    <div class="tab-pane fade" id="ports" role="tabpanel" aria-labelledby="ports-tab">
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" onclick="addPort()">
                                <i class="far fa-plus"></i> Add Port
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Port Name</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="portsTableBody">
                                    <?= $portsHtml ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- BERTHS TAB -->
                    <div class="tab-pane fade" id="berths" role="tabpanel" aria-labelledby="berths-tab">
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" onclick="addBerth()">
                                <i class="far fa-plus"></i> Add Berth
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Berth Name</th>
                                        <th>Port</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="berthsTableBody">
                                    <?= $berthsHtml ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TERMINALS TAB -->
                    <div class="tab-pane fade" id="terminals" role="tabpanel" aria-labelledby="terminals-tab">
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" onclick="addTerminal()">
                                <i class="far fa-plus"></i> Add Terminal
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Terminal Name</th>
                                        <th>Phone</th>
                                        <th>Telephone</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="terminalsTableBody">
                                    <?= $terminalsHtml ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- DOCUMENTS TAB -->
                    <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" onclick="addDocument()">
                                <i class="far fa-plus"></i> Add Document
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Document Name</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="documentsTableBody">
                                    <?= $documentsHtml ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</section>

<!-- Modals -->

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="productForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="productId">
                    <input type="hidden" class="token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="form-group">
                        <label for="productName">Product Name</label>
                        <input type="text" class="form-control" id="productName" name="productName">
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

<!-- Port Modal -->
<div class="modal fade" id="portModal" tabindex="-1" role="dialog" aria-labelledby="portModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="portForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="portModalLabel">Add Port</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="portId">
                    <input type="hidden" class="token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="form-group">
                        <label for="portNameInput">Port Name</label>
                        <input type="text" class="form-control" id="portNameInput" name="portName">
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

<!-- Berth Modal -->
<div class="modal fade" id="berthModal" tabindex="-1" role="dialog" aria-labelledby="berthModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="berthForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="berthModalLabel">Add Berth</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="berthId">
                    <input type="hidden" class="token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="form-group">
                        <label for="berthName">Berth Name</label>
                        <input type="text" class="form-control" id="berthName" name="berthName">
                    </div>
                    <div class="form-group">
                        <label for="portIdSelect">Port</label>
                        <select class="form-control select2bs4" name="portId" id="portIdSelect" style="width: 100%;">
                            <?php foreach ($ports as $p): ?>
                                <option value="<?= $p->id ?>"><?= $p->portName ?></option>
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

<!-- Terminal Modal -->
<div class="modal fade" id="terminalModal" tabindex="-1" role="dialog" aria-labelledby="terminalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="terminalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="terminalModalLabel">Add Terminal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="terminalId">
                    <input type="hidden" class="token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="form-group">
                        <label for="terminalName">Terminal Name</label>
                        <input type="text" class="form-control" id="terminalName" name="terminalName">
                    </div>
                    <div class="form-group">
                        <label for="postalAddress">Postal Address</label>
                        <input type="text" class="form-control" id="postalAddress" name="postalAddress">
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber">
                    </div>
                    <div class="form-group">
                        <label for="telephone">Telephone</label>
                        <input type="text" class="form-control" id="telephone" name="telephone">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="physicalAddress">Physical Address</label>
                        <input type="text" class="form-control" id="physicalAddress" name="physicalAddress">
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

<!-- Document Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="documentForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentModalLabel">Add Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="documentId">
                    <input type="hidden" class="token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="form-group">
                        <label for="documentName">Document Name</label>
                        <input type="text" class="form-control" id="documentName" name="documentName">
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

<script src="<?= base_url('assets/js/FormEngine.js') ?>"></script>
<script>
    // --- Generic Settings Form Engine ---
    class SettingsFormEngine extends FormEngine {
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
            document.getElementById(this.tableBodyId).innerHTML = data.table;

            // Update Port Options if available
            if (data.portOptions) {
                $('#portIdSelect').html(data.portOptions);
            }
        }
    }

    // --- Products ---
    const productForm = new SettingsFormEngine('productForm', '<?= base_url('metrology/settings/products/save') ?>', 'productModal', 'productsTableBody');

    function addProduct() {
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('productModalLabel').innerText = 'Add Product';
        productForm.clearErrors();
        $('#productModal').modal('show');
    }

    function editProduct(id, name) {
        document.getElementById('productForm').reset();
        productForm.clearErrors();
        document.getElementById('productId').value = id;
        document.getElementById('productName').value = name;
        document.getElementById('productModalLabel').innerText = 'Edit Product';
        $('#productModal').modal('show');
    }

    function deleteProduct(id) {
        deleteEntity(id, '<?= base_url('metrology/settings/products/delete') ?>', 'productsTableBody');
    }

    // --- Ports ---
    const portForm = new SettingsFormEngine('portForm', '<?= base_url('metrology/settings/ports/save') ?>', 'portModal', 'portsTableBody');

    function addPort() {
        document.getElementById('portForm').reset();
        document.getElementById('portId').value = '';
        document.getElementById('portModalLabel').innerText = 'Add Port';
        portForm.clearErrors();
        $('#portModal').modal('show');
    }

    function editPort(id, name) {
        document.getElementById('portForm').reset();
        portForm.clearErrors();
        document.getElementById('portId').value = id;
        document.getElementById('portNameInput').value = name;
        document.getElementById('portModalLabel').innerText = 'Edit Port';
        $('#portModal').modal('show');
    }

    function deletePort(id) {
        deleteEntity(id, '<?= base_url('metrology/settings/ports/delete') ?>', 'portsTableBody');
    }

    // --- Berths ---
    const berthForm = new SettingsFormEngine('berthForm', '<?= base_url('metrology/settings/berths/save') ?>', 'berthModal', 'berthsTableBody');

    function addBerth() {
        document.getElementById('berthForm').reset();
        document.getElementById('berthId').value = '';
        $('#portIdSelect').val('').trigger('change');
        document.getElementById('berthModalLabel').innerText = 'Add Berth';
        berthForm.clearErrors();
        $('#berthModal').modal('show');
    }

    function editBerth(payload) {
        const data = typeof payload === 'string' ? JSON.parse(payload) : payload;
        document.getElementById('berthForm').reset();
        berthForm.clearErrors();
        document.getElementById('berthId').value = data.id;
        document.getElementById('berthName').value = data.name;
        $('#portIdSelect').val(data.portId).trigger('change');
        document.getElementById('berthModalLabel').innerText = 'Edit Berth';
        $('#berthModal').modal('show');
    }

    function deleteBerth(id) {
        deleteEntity(id, '<?= base_url('metrology/settings/berths/delete') ?>', 'berthsTableBody');
    }

    // --- Terminals ---
    const terminalForm = new SettingsFormEngine('terminalForm', '<?= base_url('metrology/settings/terminals/save') ?>', 'terminalModal', 'terminalsTableBody');

    function addTerminal() {
        document.getElementById('terminalForm').reset();
        document.getElementById('terminalId').value = '';
        document.getElementById('terminalModalLabel').innerText = 'Add Terminal';
        terminalForm.clearErrors();
        $('#terminalModal').modal('show');
    }

    function editTerminal(payload) {
        const data = typeof payload === 'string' ? JSON.parse(payload) : payload;
        document.getElementById('terminalForm').reset();
        terminalForm.clearErrors();

        document.getElementById('terminalId').value = data.id;
        document.getElementById('terminalName').value = data.name;
        document.getElementById('postalAddress').value = data.postal;
        document.getElementById('phoneNumber').value = data.phone;
        document.getElementById('telephone').value = data.telephone;
        document.getElementById('email').value = data.email;
        document.getElementById('physicalAddress').value = data.physical;

        document.getElementById('terminalModalLabel').innerText = 'Edit Terminal';
        $('#terminalModal').modal('show');
    }

    function deleteTerminal(id) {
        deleteEntity(id, '<?= base_url('metrology/settings/terminals/delete') ?>', 'terminalsTableBody');
    }

    // --- Documents ---
    const documentForm = new SettingsFormEngine('documentForm', '<?= base_url('metrology/settings/documents/save') ?>', 'documentModal', 'documentsTableBody');

    function addDocument() {
        document.getElementById('documentForm').reset();
        document.getElementById('documentId').value = '';
        document.getElementById('documentModalLabel').innerText = 'Add Document';
        documentForm.clearErrors();
        $('#documentModal').modal('show');
    }

    function editDocument(id, name) {
        document.getElementById('documentForm').reset();
        documentForm.clearErrors();

        document.getElementById('documentId').value = id;
        document.getElementById('documentName').value = name;

        document.getElementById('documentModalLabel').innerText = 'Edit Document';
        $('#documentModal').modal('show');
    }

    function deleteDocument(id) {
        deleteEntity(id, '<?= base_url('metrology/settings/documents/delete') ?>', 'documentsTableBody');
    }

    // --- Generic Delete Function ---
    function deleteEntity(id, url, tableBodyId) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this item!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then(async (willDelete) => {
            if (willDelete) {
                try {
                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('<?= csrf_token() ?>', document.querySelector('.token').value);

                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            Accept: "application/json"
                        }
                    });

                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                    const data = await response.json();
                    document.querySelector('.token').value = data.token;

                    if (data.status === 1) {
                        swal({
                            title: "Deleted!",
                            text: data.msg,
                            icon: "success"
                        });
                        document.getElementById(tableBodyId).innerHTML = data.table;

                        // Update Port Options if available
                        if (data.portOptions) {
                            $('#portIdSelect').html(data.portOptions);
                        }
                    } else {
                        swal("Error", data.msg, "error");
                    }
                } catch (error) {
                    console.error('Delete error:', error);
                    swal("Error", "An error occurred while deleting.", "error");
                }
            }
        });
    }

    // Initialize Select2 inside modal if needed, though they are usually init at document ready.
    $(document).ready(function() {
        $('#portIdSelect').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#berthModal')
        });
    });
</script>
<?= $this->endSection(); ?>