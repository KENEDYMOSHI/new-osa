<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!-- <h1><?= $page['heading'] ?></h1> -->
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">

        <!-- Button trigger modal -->


        <!-- Modal -->
        <div class="modal fade" id="billModel" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h5 class="modal-title">Modal title</h5> -->
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div id="bill"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $('#exampleModal').on('show.bs.modal', event => {
                var button = $(event.relatedTarget);
                var modal = $(this);
                // Use above variables to manipulate the DOM

            });
        </script>

        <div class="card">
            <div class="card-header">
                <a href="<?= base_url('receivableSummary') ?>" id="" class="btn btn-primary btn-sm" href="#" role="button"><i class="fas fa-arrow-left    "></i> Back </a>
            </div>
            <div class="card-body">

                <h6 class="text-center">WEIGHTS AND MEASURES AGENCY</h6>
                <h6 class="text-center"><?= strtoupper(str_replace('Wakala Wa Vipimo', ' Regional', $centerName . ' Office')) ?></h6>
                <h6 class="text-center">DEBTORS ANALYSIS REPORT (<?= str_replace('_','/',$financialYear) ?>)</h6>
                <h6 class="text-center">Aged Analysis Of Debtors As at <?= dateFormatter(date('Y-m-d')) ?></h6>
                <table class="table table-sm table-hover" id="receivable">
                    <thead class="thead-dark">
                        <tr>
                            <th>S/N</th>
                            <th>Debtor Name</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Control Number</th>
                            <th>1-30 Days</th>
                            <th>31-61 Days</th>
                            <th>61-90 Days</th>
                            <th>91-120 Days</th>
                            <th>121-365 Days</th>
                            <th>Above 365 Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $n = 1; ?>
                        <?php foreach ($collectionData as $collection) : ?>

                            <tr onclick="viewBill('<?= $collection->billId ?>')">
                                <td><?= $n++ ?></td>
                                <td><a href="#"><?= $collection->customer ?></a></td>
                                <td><a href="#"><?= dateFormatter($collection->CreatedAt) ?></a></td>
                                <td><a href="#"><?= number_format($collection->amount) ?></a></td>
                                <td><a href="#"><?= $collection->controlNumber ?></a></td>
                                <td><a href="#"><?= number_format($collection->_1_30) ?></a></td>
                                <td><a href="#"><?= number_format($collection->_31_60) ?></a></td>
                                <td><a href="#"><?= number_format($collection->_61_90) ?></a></td>
                                <td><a href="#"><?= number_format($collection->_91_120) ?></a></td>
                                <td><a href="#"><?= number_format($collection->_121_365) ?></a></td>
                                <td><a href="#"><?= number_format($collection->_above365) ?></a></td>
                            </tr>


                        <?php endforeach; ?>

                    </tbody>
                </table>

                <br>
                <br>
                <div class="col-4">
                    <table class="table table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Days</th>
                                <th>TZS</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td> 1-30 Days</td>
                                <td><?= number_format($total_1_30) ?></td>
                            </tr>
                            <tr>
                                <td> 31-60 Days</td>
                                <td><?= number_format($total_31_60) ?></td>
                            </tr>
                            <tr>
                                <td> 61-90 Days</td>
                                <td><?= number_format($total_61_90) ?></td>
                            </tr>
                            <tr>
                                <td> 91-120 Days</td>
                                <td><?= number_format($total_91_120) ?></td>
                            </tr>
                            <tr>
                                <td> 121-365 Days</td>
                                <td><?= number_format($total_121_365) ?></td>
                            </tr>
                            <tr>
                                <td> Above Days</td>
                                <td><?= number_format($total_above365) ?></td>
                            </tr>
                            <tr>
                                <td><b>TOTAL</b></td>
                                <td><b><?= number_format($total_1_30 + $total_31_60 + $total_61_90 + $total_above365 + $total_121_365 + $total_91_120) ?></b></td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>

            <div class="card-footer">
                <a href="<?= base_url("downloadRegionalReceivables/$collectionCenter/$financialYear") ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fal fa-download"></i> Download</a>
            </div>
        </div>


    </div>
    </div>
    <!-- /.container-fluid -->

</section>

<script>
    $(document).ready(function() {
        $('#receivable').DataTable({
        dom: '<"top"lBfrtip>',
        buttons: [
            'excel',
        ],
        lengthMenu: [30, 50, 70, 100],
        "responsive": true,
        "autoWidth": false,
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "responsive": true,

    });
});

    
    let baseUrl = '<?= base_url() ?>'

    function viewBill(billId) {
        fetch(baseUrl + 'getBillDetails', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },

                body: JSON.stringify({
                    billId
                }),

            }).then(response => response.json())
            .then(data => {
                const {
                    token,
                    bill
                } = data
                if (status == 1) {

                }
                document.querySelector('.token').value = token
                document.querySelector('#bill').innerHTML = bill
                $('#billModel').modal('show')
                console.log(data)
            })
    }
   
</script>
<?= $this->endSection(); ?>-