<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!-- <h1 class="m-0 text-dark"></h1> -->
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
            CASH BOOK TO BANK MANUAL MATCHING <strong>(<?= $accountName ?>)</strong>
        </div>

        <div class="card-body">
            <form id="reconForm">
                <input type="text" name="accountNumber" value="<?= $accountNumber ?>" class="form-control" hidden>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Bank Csv File</label>
                            <input type="file" name="csvFile" id="csvFile" class="form-control" placeholder="">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" id="requestBtn" class="btn btn-primary mt-4 btn-sm"><i class="fal fa-upload"></i> Upload</button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <a href="<?=base_url('paymentReconciliation')?>" class="btn btn-primary btn-sm"><i class="fal fa-arrow-left"></i> Back</a>
                    <button type="button" class="btn btn-primary btn-sm"><i class="fal fa-check"></i> Match</button>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">GENERAL LEDGE OUTSTANDING LIST</div>
                        <div class="card-body">
                            <table class="table table-sm reconTable" id="cashBookTable">

                            </table>


                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">BANK OUTSTANDING LIST</div>
                        <div class="card-body">

                            <table class="table table-sm reconTable" id="bankTable">

                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>




    </div>
</div>
</div>

<script>
    const url = '<?= base_url('cashbookToBankMatch') ?>'
    const reconForm = document.querySelector('#reconForm')
    reconForm.addEventListener('submit', e => {
        e.preventDefault()
        const formData = new FormData(reconForm)

        fetch(url, {
                method: 'POST',
                headers: {
                    // 'Content-Type': 'application/json;charset=utf-8',
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },

                body: formData,

            }).then(response => response.json())
            .then(data => {
                const {
                    token,
                    bankTable,
                    cashBookTable
                } = data
                document.querySelector('.token').value = token
                document.querySelector('#bankTable').innerHTML = bankTable
                document.querySelector('#cashBookTable').innerHTML = cashBookTable
                console.log(data.payments)
                let table1 = $('#cashBookTable').DataTable();
                let table2 = $('#bankTable').DataTable();

                // Destroy the DataTable
                table1.destroy();
                table2.destroy();

                // Remove the table
                $('#cashBookTable').empty();
                $('#bankTable').empty();

                // Add a new table with updated `thead`
                $('#cashBookTable').html(cashBookTable);
                $('#bankTable').html(bankTable);

                // Re-initialize the DataTable with updated `thead`
                table1 = $('#cashBookTable').DataTable();
                table2 = $('#bankTable').DataTable();
            })
    })

    // $(document).ready(function() {
    //     $('.reconTable').DataTable();
    // });
</script>
<?= $this->endSection(); ?>