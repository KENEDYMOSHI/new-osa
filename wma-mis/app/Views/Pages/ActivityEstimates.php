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
            <?= $title ?> ACTIVITY INSTRUMENT ESTIMATES (<span class="text-bold" id="remaining"><?= $remaining ?></span> INSTRUMENTS REMAINING)

            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#targetModal" style="float:right"><i class="far fa-plus-circle "></i> Add
                Estimate</button>
        </div>
        <div class="card-body" id="estimates">


            <table class="table table sm">
                <thead>
                    <tr>
                        <th>Activity</th>
                        <th>Month</th>
                        <th>Instrument Estimate</th>
                        <th>Actual</th>
                        <th>Variance</th>
                        <th>Percentage</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="activityData">
                    <?= $activityData ?>
                </tbody>
            </table>


        </div>

    </div>
    <!-- ########################################## -->
    <div id="editModal" class="modal fade " role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-title">UPDATE INSTRUMENT ESTIMATE</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateActivityEstimate" name="targeForm">
                        <input type="text" name="id" id="estimateId" value="" hidden>
                        <div class="row">



                            <div class="form-group col-md-6">
                                <label for="my-input">Month</label>
                                <select id="month" class="form-control" name="month">

                                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                                        <option value="<?= $i; ?>"><?= date("F", mktime(0, 0, 0, $i, 1)); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="form-group">
                                    <label for="my-select"> Year</label>
                                    <select id="year" class="form-control" name="year">

                                        <?php for ($i = date('Y'); $i >= 2023; $i--) : ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="my-input">Activity</label>
                                <input type="text" class="form-control" id="activity" readonly>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" placeholder="" aria-describedby="helpId" required>
                                </div>
                            </div>



                        </div>



                        <div class="modal-footer">
                            <button type="submit" id="regionalTarget" class="btn btn-primary btn-sm">
                                <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="targetModal" class="modal fade " role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-title">ADD INSTRUMENT ESTIMATE</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="targetForm" name="targeForm">
                        <div class="row">



                            <div class="form-group col-md-6">
                                <label for="my-input">Month</label>
                                <select id="" class="form-control" name="month">

                                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                                        <option value="<?= $i; ?>"><?= date("F", mktime(0, 0, 0, $i, 1)); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="form-group">
                                    <label for="my-select"> Year</label>
                                    <select id="" class="form-control" name="year">

                                        <?php for ($i = date('Y'); $i >= 2023; $i--) : ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="my-input">Activities</label>
                                <select name="activity" class="form-control select2bs4" required>

                                    <?php foreach (gfsCodes() as $code => $name) : ?>
                                        <option value="<?= $code ?>"><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Quantity</label>
                                    <input type="number" name="quantity" id="" class="form-control" min="1" placeholder="" aria-describedby="helpId" required>
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
        $(document).ready(function() {
            // Set the first tab as active by default
            $('#tab001').addClass('btn-primary');

            // Add btn-primary class when switching tabs
            $('.nav-link').on('click', function() {
                $('.nav-link').removeClass('btn-primary');
                $(this).addClass('btn-primary');
            });
        });




        const updateActivityEstimate = document.querySelector('#updateActivityEstimate');
        $('#updateActivityEstimate').validate()
        updateActivityEstimate.addEventListener('submit', e => {
            e.preventDefault();
            if ($('#updateActivityEstimate').valid()) {
                submitInProgress(e.submitter);

                const formData = new FormData(updateActivityEstimate);
                fetch('updateActivityEstimate', {
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
                            activityData,
                            remaining
                        } = data;
                        // document.querySelector('#estimates').innerHTML = estimates;
                        document.querySelector('.token').value = token;
                        submitDone(e.submitter);
                        if (status == 1) {
                            targetForm.reset()
                            $('#editModal').modal('hide')
                            document.querySelector('#remaining').textContent = remaining;
                            document.querySelector('#activityData').innerHTML = activityData;
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





        const targetForm = document.querySelector('#targetForm');
        $('#targetForm').validate()
        targetForm.addEventListener('submit', e => {
            e.preventDefault();
            if ($('#targetForm').valid()) {
                submitInProgress(e.submitter);

                const formData = new FormData(targetForm);
                fetch('createActivityEstimate', {
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
                            activityData,
                            remaining
                        } = data;
                        // document.querySelector('#estimates').innerHTML = estimates;
                        document.querySelector('.token').value = token;
                        submitDone(e.submitter);
                        if (status == 1) {
                            targetForm.reset()
                            $('#targetModal').modal('hide')
                            document.querySelector('#remaining').textContent = remaining;
                            document.querySelector('#activityData').innerHTML = activityData;
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


        function editActivityEstimate(id) {
            console.log(id);
            fetch('editActivityEstimate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json;charset=utf-8',
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSRF-TOKEN': document.querySelector('.token').value
                    },
                    body: JSON.stringify({
                        id
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    const {
                        token,
                        status,
                        msg,
                        estimate
                    } = data;
                    // document.querySelector('#estimates').innerHTML = estimates;
                    document.querySelector('.token').value = token;

                    if (status == 1) {
                        document.querySelector('#estimateId').value = estimate.id
                        document.querySelector('#activity').value = estimate.activity
                        document.querySelector('#month').value = estimate.month
                        document.querySelector('#year').value = estimate.year
                        document.querySelector('#quantity').value = estimate.quantity
                        $('#editModal').modal('show')
                    }

                    console.log(data);
                });

        }
    </script>
</div>



<?= $this->endSection(); ?>