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





    <!-- Button trigger modal -->
    <!-- Add Importer Modal -->
    <div class="modal fade" id="addImporterModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Importer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addImporterForm">
                        <div class="form-group">
                            <label for="importer_name">Importer Name</label>
                            <input type="text" name="importerName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="importer_name">License Number</label>
                            <input type="text" name="licenseNumber" class="form-control" required>
                        </div>

                       
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addImporter()">Save</button>
                </div>
            </div>
        </div>
    </div>


    <div class="card">


        <div class="card-header">
            <!-- <h3 class="card-title">Importers</h3> -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addImporterModal">Add Importer</button>
        </div>
        <div class="card-body">
            <table id="importersTable" class="table table-bordered table-sm" id="importersTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Importer Name</th>
                        <th>License Number</th>
                  
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($importers as $importer) : ?>
                        <tr>
                            <td><?= $importer->importerName ?></td>
                            <td><?= $importer->licenseNumber ?></td>
                   
                            <td>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editImporterModal" data-id="<?= $importer->id ?>"><i class="fas fa-pen    "></i></button>
                                <button class="btn btn-danger btn-sm" onclick="deleteImporter(<?= $importer->id ?>)"><i class="fas fa-trash-alt    "></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>



    </div>


</div>
</div>


<script>
        $(document).ready(function() {
        $('#importersTable').DataTable({
            dom: '<"top"lBfrtip>',
            buttons: ['excel'],
            lengthMenu: [30, 50, 70, 100, 150, 200],
            responsive: true,
            autoWidth: false,
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
        });
    });
    async function addImporter() {
        const form = document.getElementById('addImporterForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('addImporter', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            console.log(data);
            const {
                status,
                msg
            } = data;

            if (status == 1) {
               form.reset();
               $('#addImporterModal').modal('hide');
               setTimeout(() => {
                location.reload();
               }, 2000);
            }

            swal({
                title: msg,
                icon: status == 1 ? "success" : "warning",
            });


        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function updateImporter() {
        const form = document.getElementById('editImporterForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('/importers/update/' + formData.get('id'), {
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

    async function deleteImporter(id) {
        try {
            const response = await fetch('/importers/delete/' + id, {
                method: 'POST'
            });
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    $('#editImporterModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const id = button.data('id');
        const importer = <?= json_encode($importers) ?>.find(v => v.id === id);

        $('#editImporterId').val(importer.id);
        $('#editImporterName').val(importer.importer_name);
        $('#editProductType').val(importer.product_type);
        $('#editArrivalDate').val(importer.arrival_date.replace(' ', 'T'));
        $('#editBerthingDate').val(importer.berthing_date.replace(' ', 'T'));
    });
</script>





<?= $this->endSection(); ?>