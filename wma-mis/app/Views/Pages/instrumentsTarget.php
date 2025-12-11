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

    <!-- <pre>
    <?php print_r([]) ?>
</pre> -->

    <div class="card">

        <div class="card-header">
            INSTRUMENT TARGETS <span id="targetTotal"></span>

            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#targetModal" style="float:right"><i class="far fa-plus-circle "></i> Add
                Target</button>
        </div>
        <div class="card-body" id="estimates">
            <?=$targets ?>


        </div>

    </div>
    <!-- ########################################## -->
    <div id="targetModal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-title">INSTRUMENTS COLLECTION TARGET</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="targetForm" name="targeForm">
                        <div class="row">

                            <div class="form-group col-md-4">
                                <label for="my-input">Collection Center</label>
                                <select name="region" class="form-control select2bs4" required>

                                    <?php foreach (collectionCenters() as $center) : ?>
                                        <option value="<?= $center->centerNumber ?>"><?= $center->centerName ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- 
                            <div class="form-group col-md-6">
                            <div class="form-group">
                                <label for="my-select">Financial Year</label>
                                <select id="year" class="form-control" name="year">
                                   
                                    <?php for ($i = 2030; $i >= 2023; $i--) : ?>
                                        <option value="<?= $i . '/' . $i + 1 ?>"><?= $i . '/' . $i + 1 ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            </div> -->



                            <div class="col-md-4">

                                <div class="form-group">
                                    <label for="">Activity</label>
                                    <select class="form-control select2bs4" name="activity" id="activity" required>
                                        <option value="">--Select Activity--</option>
                                        <?php foreach (gfsCodes() as $key => $value) : ?>
                                            <option value="<?= $key ?>"><?= $value ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Instrument Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" placeholder="" aria-describedby="helpId" required>
                                </div>
                            </div>



                        </div>



                        <div class="modal-footer">
                            <button type="submit" id="regionalTarget" class="btn btn-primary btn-sm">
                                <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ########################################## -->



    <!-- <button type="button" onclick="renderTargetList()" class="btn btn-primary btn-sm">Get</button> -->


    <script>
        const targetForm = document.querySelector('#targetForm');
        $('#targetForm').validate()
        targetForm.addEventListener('submit', e => {
            e.preventDefault();
            if ($('#targetForm').valid()) {
                submitInProgress(e.submitter);

                const formData = new FormData(targetForm);
                fetch('addInstrumentTarget', {
                        method: 'POST',
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            'X-CSRF-TOKEN': document.querySelector('.token').value
                        },
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        const {
                            token,
                            status,
                            msg,
                            estimates
                        } = data;
                        document.querySelector('#estimates').innerHTML = estimates;
                        document.querySelector('.token').value = token;
                        submitDone(e.submitter);
                        if(status == 1){
                            targetForm.reset()
                            $('#targetModal').modal('hide')
                        }
                        swal({
                            title: msg,
                            icon: status == 1 ? "success" : "warning",
                        });
                        console.log(data);
                    });
            } else {
                return false;
            }
        });
    </script>
</div>



<?= $this->endSection(); ?>