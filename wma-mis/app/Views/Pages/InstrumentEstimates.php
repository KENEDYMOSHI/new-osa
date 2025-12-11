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
            INSTRUMENTS ESTIMATES <span id="targetTotal"></span>

            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#targetModal" style="float:right"><i class="far fa-plus-circle "></i> Add
                Estimate</button>
        </div>
        <div class="card-body" id="estimates">
            <!-- <?= Printer($regions) ?> -->




            <div class="row">
                <div class="col-3 col-sm-3">
                    <div class="nav flex-column nav-tabs h-100" id="custom-tabs-three-tab" role="tablist" aria-orientation="vertical">
                        <?php foreach ($regions as $index => $region) : ?>
                            <?php
                            $regionName = str_replace('Wakala Wa Vipimo', '', $region->centerName);
                            $active = $index === 0 ? 'active' : '';
                            ?>
                            <a class="nav-link <?= $active ?>" id="tab<?= $region->centerNumber ?>" data-toggle="pill" href="#region<?= $region->centerNumber ?>">
                                <?= $regionName ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-9 col-sm-9">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        <?php foreach ($estimates as $index => $item) : ?>
                            <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" id="region<?= $item->region ?>">
                                <?php
                                $regionEstimates = array_filter($estimates, function ($entry) use ($item) {
                                    return $entry->region === $item->region;
                                });
                                ?>
                                <?php if (!empty($regionEstimates)) : ?>
                                    <table class="table table-sm" id="variance">
                                        <thead>
                                            <tr>
                                                <th>Region</th>
                                                <th>Month</th>
                                                <th>Year</th>
                                                <th>instruments</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Sort estimates by month -->
                                            <?php usort($regionEstimates, function ($a, $b) {
                                                return $a->month - $b->month;
                                            }); ?>

                                            <!-- Loop through data for the specific region -->
                                            <?php foreach ($regionEstimates as $entry) : ?>
                                                <?php
                                                $monthName = date("F", mktime(0, 0, 0, $entry->month, 1));
                                                $regionName = str_replace('Wakala Wa Vipimo', '', wmaCenter($entry->region)->centerName);
                                                $instruments = number_format($entry->instruments);
                                                ?>
                                                <tr>
                                                    <td><?= $regionName ?></td>
                                                    <td><?= $monthName ?></td>
                                                    <td><?= $entry->year ?></td>
                                                    <td><?= $instruments ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-sm" onclick="editEstimate('<?= $entry->id ?>')"> <i class="far fa-pen-alt"></i></button>
                                                        <!-- <button type="button" class="btn btn-dark btn-sm" onclick="deleteEstimate('<?= $entry->id ?>')"> <i class="far fa-trash-alt"></i></button> -->
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else : ?>
                                    <p>No data available.</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>













        </div>

    </div>
    <!-- ########################################## -->
    <div id="editModal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-title">UPDATE INSTRUMENT ESTIMATE</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="estimateUpdate" name="targeForm">
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
                                <label for="my-input">Collection Center</label>
                                <input type="text" class="form-control" id="region" readonly>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">instruments</label>
                                    <input type="number" name="instruments" id="instruments" class="form-control" min="1" placeholder="" instruments required>
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
    <div id="targetModal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
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
                                <label for="my-input">Collection Center</label>
                                <select name="region" class="form-control select2bs4" required>

                                    <?php foreach (collectionCenters() as $center) : ?>
                                        <option value="<?= $center->centerNumber ?>"><?= $center->centerName ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Number Of Instruments</label>
                                    <input type="number" name="instruments" id="" class="form-control" min="1" placeholder="" instruments required>
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




        const estimateUpdate = document.querySelector('#estimateUpdate');
      //  $('#estimateUpdate').validate()
        estimateUpdate.addEventListener('submit', e => {
            e.preventDefault();
            if (true) {
                submitInProgress(e.submitter);

                const formData = new FormData(estimateUpdate);
                fetch('updateInstrumentEstimate', {
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
                            // estimates
                        } = data;
                        // document.querySelector('#estimates').innerHTML = estimates;
                        document.querySelector('.token').value = token;
                        submitDone(e.submitter);
                        if (status == 1) {
                            targetForm.reset()
                            $('#editModal').modal('hide')

                            swal({
                                title: 'Estimate Updated',
                                icon: status == 1 ? "success" : "warning",
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 3000)
                        }
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
                fetch('createInstrumentEstimate', {
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
                        // document.querySelector('#estimates').innerHTML = estimates;
                        document.querySelector('.token').value = token;
                        submitDone(e.submitter);
                        if (status == 1) {
                            targetForm.reset()
                            $('#targetModal').modal('hide')
                        }
                        swal({
                            title: 'Estimate Created',
                            icon: status == 1 ? "success" : "warning",
                        });
                        setTimeout(() => {
                            location.reload();
                        }, 3000)
                        console.log(data);
                    });
            } else {
                return false;
            }
        });


        function editEstimate(id) {
            fetch('editInstrumentEstimate', {
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
                        document.querySelector('#region').value = estimate.regionName
                        document.querySelector('#month').value = estimate.month
                        document.querySelector('#year').value = estimate.year
                        document.querySelector('#instruments').value = estimate.instruments
                        $('#editModal').modal('show')
                    }

                    console.log(data);
                });

        }
    </script>
</div>



<?= $this->endSection(); ?>