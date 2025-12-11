<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class=""><?= $page['heading'] ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Collection Centers</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">




        <!-- <pre>
        <?php print_r($instruments);
        // exit;
        ?>
        </pre> -->


        <div class="card">


            <div class="card-header ">
                <form id="instrumentForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="my-select"><input class="check checkBox" style="transform:scale(1.3); margin-right:5px" type="checkbox" name="" id="quarterCheck" onchange="enableQuarter(this)">Quarter/Annual</label>
                                <select id="quarter" name="quarter" class="form-control" disabled>
                                    <option value="Q1" selected>Quarter One</option>
                                    <option value="Q2">Quarter Two</option>
                                    <option value="Q3">Quarter Three</option>
                                    <option value="Q4">Quarter Four</option>
                                    <option selected value="Annually">Annually</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="my-select"><input class="check checkBox" style="transform:scale(1.3); margin-right:5px" type="checkbox" name="" id="monthCheck" onchange="enableMonth(this)">Month</label>
                                <select id="month" name="month" class="form-control" disabled>
                                    <option value="1" selected>January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group">
                                <label for="my-select">Year</label>
                                <select id="year" class="form-control" name="year">
                                    <!-- <option value="<?= date('Y') ?>"><?= date('Y') ?></option> -->
                                    <?php for ($i = date('Y'); $i >= 2023; $i--) : ?>
                                        <option value="<?= $i . '/' . $i + 1 ?>"><?= $i . '/' . $i + 1 ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group">
                                <label for="my-select">Status</label>
                                <select id="status" class="form-control" name="status">
                                   <option value="Rejected">Rejected</option>
                                   <option value="Condemned">Condemned</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group">
                                <button type="submit" id="submit" class="btn btn-primary btn-sm mt-4">
                                    <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                                    <i class="fas fa-filter    "></i> Filter
                                </button>
                            </div>
                        </div>
                </form>
            </div>
            <pre>
                <!-- <?php
                print_r($instruments)
                ?> -->
            </pre>
            <p class="text-center"><?= '' ?></p>
            <h5 class="text-center"><b id="title">INSTRUMENTS INSPECTION REPORT OF FINANCIAL YEAR <?= $financialYear ?></b></h5>
            <div class="card-body" id="report">
                <?= $dataTable ?>
            </div>
            <?php
            $date1 = financialYear()->startDate;
            $date2 = financialYear()->endDate;
            $month = '00';
            $year = '00';
            $title = 'INSTRUMENTS INSPECTION REPORT OF FINANCIAL YEAR ' .$financialYear ?>
            
            <div class="card-footer">
                <a href="<?= base_url("downloadInspected/$date1/$date2/$month/$year/$title") ?>" id="link" target="_blank" class="btn btn-primary btn-sm "><i class="fal fa-download    "> </i> Download</a>
            </div>
        </div>

    </div><!-- /.container-fluid -->

    <script>
        const submit = document.querySelector('#submit')
        const month = document.querySelector('#month')
        const quarter = document.querySelector('#quarter')

        function enableQuarter(checkBox) {
            const monthCheck = document.querySelector('#monthCheck')
            if (checkBox.checked == true) {

                if (monthCheck.checked == true) {
                    submit.setAttribute('disabled', 'disabled')
                    swal({
                        title: 'Please Choose one Option',
                        icon: "warning",
                        timer: 8500
                    });

                }
                quarter.removeAttribute('disabled', 'disabled')
            } else {
                submit.removeAttribute('disabled', 'disabled')
                quarter.setAttribute('disabled', 'disabled')
            }
        }

        function enableMonth(checkBox) {
            const quarterCheck = document.querySelector('#quarterCheck')
            if (checkBox.checked == true) {

                if (quarterCheck.checked == true) {
                    submit.setAttribute('disabled', 'disabled')
                    swal({
                        title: 'Please Choose one Option',
                        icon: "warning",
                        timer: 8500
                    });

                }
                month.removeAttribute('disabled', 'disabled')
            } else {
                submit.removeAttribute('disabled', 'disabled')
                month.setAttribute('disabled', 'disabled')
            }
        }


        $('#instrumentForm').validate()
        const instrumentForm = document.querySelector('#instrumentForm')
        instrumentForm.addEventListener('submit', e => {
            e.preventDefault()
            submitInProgress(e.submitter)
            if ($('#instrumentForm').valid()) {
                const formData = new FormData(instrumentForm)
                fetch('filterInspected', {
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
                            status,
                            report,
                            link,
                            title,
                            params,
                            params2
                        } = data
                        document.querySelector('#link').setAttribute('href', link)
                        submitDone(e.submitter)
                        if (status == 1) {
                            document.querySelector('#report').innerHTML = report
                            document.querySelector('#title').textContent = title
                        }
                        document.querySelector('.token').value = token
                        // console.log(params)
                        // console.log('------------------------------------------')

                        // console.log(params2)
                    })
            } else {
                return false
            }
        })
    </script>

</section>
<?= $this->endSection(); ?>