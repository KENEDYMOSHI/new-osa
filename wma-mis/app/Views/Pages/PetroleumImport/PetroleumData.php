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
    <div class="card">

        <div class="card-header">
            <!-- <h3 class="card-title">PetroleumData</h3> -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addPetroleumDataModal">Add PetroleumData</button>
        </div>
        <div class="card-body">

            <table class="table table-bordered table-striped" id="petroleumData">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Vessel Name</th>
                        <th>Product Type</th>
                        <th>Arrival Date</th>
                        <th>Berthing Date</th>
                        <th>Import Status</th>
                        <th>Importer Name</th>
                        <th>License Number</th>
                        <th>Storage Depot</th>
                        <th>Port of Discharge</th>
                        <th>Bill of Lading No</th>

                        <th>Notifying Party</th>
                        <th>Bill of Lading Qty (MT)</th>
                        <th>Bill of Lading Qty (Litre)</th>
                        <th>Load Port Qty (MT)</th>
                        <th>Load Port Qty (Litre)</th>
                        <th>Arrival Qty (MT)</th>
                        <th>Arrival Qty (Litre)</th>
                        <th>Discharge Port Qty (MT)</th>
                        <th>Discharge Port Qty (Litre)</th>
                        <th>Outturn Qty (MT)</th>
                        <th>Outturn Qty (Litre)</th>
                        <th>Diff Outturn-Bill (MT)</th>
                        <th>Diff Outturn-Bill (Litre)</th>
                        <th>Diff Outturn-Arrival (MT)</th>
                        <th>Diff Outturn-Arrival (Litre)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($petroleumData)): ?>
                        <?php foreach ($petroleumData as $index => $data): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $data->vesselName ?></td>
                                <td><?= $data->productType ?></td>
                                <td><?= dateFormatter($data->arrivalDate) ?></td>
                                <td><?= dateFormatter($data->berthingDate) ?></td>
                                <td><?= $data->importStatus ?></td>
                                <td><?= $data->importerName ?></td>
                                <td><?= $data->licenseNumber ?></td>
                                <td><?= $data->storageDepotName ?></td>
                                <td><?= $data->portOfDischarge ?></td>
                                <td><?= $data->billOfLadingNo ?></td>
                                <td><?= $data->notifyingParty ?></td>
                                <td><?= number_format($data->billOfLadingQuantityMt, 2) ?></td>
                                <td><?= number_format($data->billOfLadingQuantityLitre, 2) ?></td>
                                <td><?= number_format($data->loadPortQuantityMt, 2) ?></td>
                                <td><?= number_format($data->loadPortQuantityLitre, 2) ?></td>
                                <td><?= number_format($data->arrivalQuantityMt, 2) ?></td>
                                <td><?= number_format($data->arrivalQuantityLitre, 2) ?></td>
                                <td><?= number_format($data->dischargePortQuantityMt, 2) ?></td>
                                <td><?= number_format($data->dischargePortQuantityLitre, 2) ?></td>
                                <td><?= number_format($data->outturnQuantityMt, 2) ?></td>
                                <td><?= number_format($data->outturnQuantityLitre, 2) ?></td>
                                <td><?= number_format($data->differenceOutturnBillMt, 2) ?></td>
                                <td><?= number_format($data->differenceOutturnBillLitre, 2) ?></td>
                                <td><?= number_format($data->differenceOutturnArrivalMt, 2) ?></td>
                                <td><?= number_format($data->differenceOutturnArrivalLitre, 2) ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editDataModal" data-id="<?= $data->id ?>"><i class="fas fa-pen    "></i></button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteData(<?= $data->id ?>)"><i class="fas fa-trash-alt    "></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="22" class="text-center">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>



    </div>


    <div class="modal fade" id="addPetroleumDataModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add PetroleumData</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addPetroleumDataForm">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="vesselId">Vessel</label>
                                <select id="vesselId" name="vesselId" class="form-control" required>
                                    <option value="" disabled selected>Select Vessel</option>
                                    <?php foreach ($vessels as $vessel): ?>
                                        <option value="<?= $vessel->vesselId ?>"><?= $vessel->vesselName ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label for="importerId">Importer</label>
                                <select id="importerId" name="importerId" class="form-control" required>
                                    <option value="" disabled selected>Select Importer</option>
                                    <?php foreach ($importers as $importer): ?>
                                        <option value="<?= $importer->importerId ?>"><?= $importer->importerName ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label for="importStatus">Import Status</label>
                                <select id="importStatus" name="importStatus" class="form-control" required>
                                    <option value="" disabled selected>Select Import Status</option>
                                    <option value="Local">Local</option>
                                    <option value="Transit">Transit</option>
                                </select>
                            </div>


                            <div class="col-md-3 mb-2">
                                <label for="storageDepotName">Storage Depot Name</label>
                                <input type="text" id="storageDepotName" name="storageDepotName" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="portOfDischarge">Port of Discharge</label>
                                <input type="text" id="portOfDischarge" name="portOfDischarge" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="billOfLadingNo">Bill of Lading No</label>
                                <input type="text" id="billOfLadingNo" name="billOfLadingNo" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="notifyingParty">Notifying Party</label>
                                <input type="text" id="notifyingParty" name="notifyingParty" class="form-control">
                            </div>



                            <div class="col-md-3 mb-2">
                                <label for="billOfLadingQuantityMt">Bill of Lading Quantity (MT)</label>
                                <input type="number" step="0.01" id="billOfLadingQuantityMt" name="billOfLadingQuantityMt" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="billOfLadingQuantityLitre">Bill of Lading Quantity (Litre)</label>
                                <input type="number" step="0.01" id="billOfLadingQuantityLitre" name="billOfLadingQuantityLitre" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="loadPortQuantityMt">Load Port Quantity (MT)</label>
                                <input type="number" step="0.01" id="loadPortQuantityMt" name="loadPortQuantityMt" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="loadPortQuantityLitre">Load Port Quantity (Litre)</label>
                                <input type="number" step="0.01" id="loadPortQuantityLitre" name="loadPortQuantityLitre" class="form-control">
                            </div>



                            <div class="col-md-3 mb-2">
                                <label for="arrivalQuantityMt">Arrival Quantity (MT)</label>
                                <input type="number" step="0.01" id="arrivalQuantityMt" name="arrivalQuantityMt" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="arrivalQuantityLitre">Arrival Quantity (Litre)</label>
                                <input type="number" step="0.01" id="arrivalQuantityLitre" name="arrivalQuantityLitre" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="dischargePortQuantityMt">Discharge Port Quantity (MT)</label>
                                <input type="number" step="0.01" id="dischargePortQuantityMt" name="dischargePortQuantityMt" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="dischargePortQuantityLitre">Discharge Port Quantity (Litre)</label>
                                <input type="number" step="0.01" id="dischargePortQuantityLitre" name="dischargePortQuantityLitre" class="form-control">
                            </div>



                            <div class="col-md-3 mb-2">
                                <label for="outturnQuantityMt">Outturn Quantity (MT)</label>
                                <input type="number" step="0.01" id="outturnQuantityMt" name="outturnQuantityMt" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="outturnQuantityLitre">Outturn Quantity (Litre)</label>
                                <input type="number" step="0.01" id="outturnQuantityLitre" name="outturnQuantityLitre" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="differenceOutturnBillMt">Difference Outturn vs Bill (MT)</label>
                                <input type="number" step="0.01" id="differenceOutturnBillMt" name="differenceOutturnBillMt" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="differenceOutturnBillLitre">Difference Outturn vs Bill (Litre)</label>
                                <input type="number" step="0.01" id="differenceOutturnBillLitre" name="differenceOutturnBillLitre" class="form-control">
                            </div>



                            <div class="col-md-3 mb-2">
                                <label for="differenceOutturnArrivalMt">Difference Outturn vs Arrival (MT)</label>
                                <input type="number" step="0.01" id="differenceOutturnArrivalMt" name="differenceOutturnArrivalMt" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="differenceOutturnArrivalLitre">Difference Outturn vs Arrival (Litre)</label>
                                <input type="number" step="0.01" id="differenceOutturnArrivalLitre" name="differenceOutturnArrivalLitre" class="form-control">
                            </div>
                        </div> <!-- End of sixth row -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="addPetroleumData()">Save</button>
                        </div>


                </div>
                </form>
            </div>

        </div>
    </div>
</div>



</div>





<script>
    $(document).ready(function() {
        $('#petroleumData').DataTable({
            dom: '<"top"lBfrtip>',
            buttons: [{
                extend: 'excel',
                exportOptions: {
                    columns: ':not(:last-child)' // Exclude the last column (assuming it's "Action")
                }
            }],
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


    async function addPetroleumData() {
        const form = document.getElementById('addPetroleumDataForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('addPetroleumData', {
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
                $('#addPetroleumDataModal').modal('hide');
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
</script>





<?= $this->endSection(); ?>