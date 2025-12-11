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
            RECONCILIATION STATUS
        </div>
        <div class="card-body">
            <?php $months = ['--Month--', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] ?>
            <form action="" id="reconForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Month</label>
                            <select class="form-control" name="month" id="">
                                <?php for ($i = 0; $i < 13; $i++) : ?>
                                    <option <?= $i == 0 ? 'disabled' : '' ?> value="<?= $i == 0 ? '' : $i ?>"><?= $months[$i] ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Year</label>
                            <select class="form-control" name="year" id="">
                                <?php for ($i = date('Y'); $i > 2015; $i--) : ?>
                                    <option value="<?= $i  ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">

                        <br>
                        <button type="submit" id="requestBtn" class="btn btn-primary btn-sm mt-2"><i class="fal fa-search"></i> Search</button>
                    </div>
                </div>
            </form>



            <table class="table table-sm" id="reconTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <th>Date</th>
                        <th>Auto Matched</th>
                        <th>Manual Matched</th>
                        <th>GL Outstanding</th>
                        <th>Bank Outstanding</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody id="summary">
                    <?=$reconTable ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    const reconForm = document.querySelector('#reconForm')
    reconForm.addEventListener('submit', e => {
        e.preventDefault()
        const formData = new FormData(reconForm)
        fetch('processRecon', {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },

                body: formData,

            }).then(response => response.json())
            .then(data => {
                const{table,token} = data
                document.querySelector('.token').value = token
                document.querySelector('#summary').innerHTML = table
                console.log(data)
            })
    })

   

</script>



<?= $this->endSection(); ?>