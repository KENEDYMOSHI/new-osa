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
    <!-- Add Vessel Modal -->
    <div class="modal fade" id="addVesselModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Vessel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addVesselForm">
                        <div class="form-group">
                            <label for="vessel_name">Vessel Name</label>
                            <input type="text" name="vesselName" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="product_type">Port</label>
                            <select class="form-control" name="port" id="">
                                <option disabled selected value="">--Select Port--</option>
                                <option value="KOJ">KOJ</option>
                                <option value="Tipper">Tipper</option>
                                <option value="Kigamboni">Kigamboni</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product_type">Product Type</label>
                            <select class="form-control" name="productType" id="">
                                <option disabled selected value="">--Select Product--</option>
                                <option value="Petroleum">Petroleum</option>
                                <option value="Diesel">Diesel</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="arrivalDate">Arrival Date</label>
                            <input type="date" name="arrivalDate" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="berthingDate">Berthing Date</label>
                            <input type="date" name="berthingDate" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addVessel()">Save</button>
                </div>
            </div>
        </div>
    </div>


    <div class="card">


        <div class="card-header">
            <!-- <h3 class="card-title">Vessels</h3> -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addVesselModal">Add Vessel</button>
        </div>
        <div class="card-body">
            <table id="vesselsTable" class="table table-bordered table-sm" id="vesselsTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Vessel Name</th>
                        <th>Port</th>
                        <th>Product Type</th>
                        <th>Arrival Date</th>
                        <th>Berthing Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vessels as $vessel) : ?>
                        <tr>
                            <td><?= $vessel->vesselName ?></td>
                            <td><?= $vessel->port ?></td>
                            <td><?= $vessel->productType ?></td>
                            <td><?= dateFormatter($vessel->arrivalDate) ?></td>
                            <td><?= dateFormatter($vessel->berthingDate) ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editVesselModal" data-id="<?= $vessel->id ?>"><i class="fas fa-pen    "></i></button>
                                <button class="btn btn-danger btn-sm" onclick="deleteVessel(<?= $vessel->id ?>)"><i class="fas fa-trash-alt    "></i></button>
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
        $('#vesselsTable').DataTable({
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
    async function addVessel() {
        const form = document.getElementById('addVesselForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('addVessel', {
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
                $('#addVesselModal').modal('hide');
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

    $('#editVesselModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const id = button.data('id');
        const vessel = <?= json_encode($vessels) ?>.find(v => v.id === id);

        $('#editVesselId').val(vessel.id);
        $('#editVesselName').val(vessel.vessel_name);
        $('#editProductType').val(vessel.product_type);
        $('#editArrivalDate').val(vessel.arrival_date.replace(' ', 'T'));
        $('#editBerthingDate').val(vessel.berthing_date.replace(' ', 'T'));
    });
</script>





<?= $this->endSection(); ?>