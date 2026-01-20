<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<script>
    // const fetchReportParams = (id) => {
    //     const logDownload = document.querySelector('#downloadReport')
    //     
    // }
</script>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark"><?= $page['heading'] ?></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('Dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">



        <div class="card">
            <div class="card-header">
                <form id="reportForm">
                    <div class="row">
                        <div class=" <?= !$user->inGroup('officer', 'manager') ? 'col-md-3' : 'col-md-4' ?>">
                            <div class="form-group">
                                <label for="my-select">Activity</label>
                                <select id="activities" class="select2bs4 form-control" name="activity" style="width:100%">
                                    <option value="">All Activities</option>
                                    <?php foreach (gfsCodes() as $key => $value) : ?>
                                        <option value="<?= $key ?>"><?= $value ?></option>
                                    <?php endforeach; ?>
                                    <!-- <option value="sbl">Sandy & Ballast Lorries</option>
                                    <option value="waterMeter">Meters</option>
                                    <option value="prepackage">Pre Package</option>
                                    <option value="others">Others</option> -->
                                </select>
                            </div>
                        </div>



                        <div class=" <?= !$user->inGroup('officer', 'manager') ? 'col-md-3' : 'col-md-4' ?> ">
                            <div class="form-group">
                                <label for="">Task </label>
                                <select class="form-control select2bs4" name="task" id="task">
                                    <!-- <option selected disabled value="All">--Select Task--</option> -->
                                    <option value="" selected>All</option>
                                    <option value="Verification">Verification</option>
                                    <option value="Reverification">Reverification</option>
                                    <option value="Inspection">Inspection</option>
                                </select>
                            </div>
                        </div>
                        <div class=" <?= !$user->inGroup('officer', 'manager') ? 'col-md-3' : 'col-md-4' ?> ">
                            <div class="form-group">
                                <label for="">Payment Status</label>
                                <select class="form-control select2bs4" name="paymentStatus" id="paymentStatus">
                                    <!-- <option selected disabled value="All">--Select PaymentStatus--</option> -->
                                    <option value="" selected>All</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Partial">Partial</option>
                                </select>
                            </div>
                        </div>
                        <?php if (!$user->inGroup('officer', 'manager')) : ?>
                            <div class=" <?= !$user->inGroup('officer', 'manager') ? 'col-md-3' : 'col-md-4' ?> ">
                                <label for="enableRegion"><input class="check" style="transform:scale(1.3); margin-right:5px" type="checkbox" id="enableRegion">Collection Center</label>
                                <select class="form-control select2bs4" name="collectionCenter" id="collectionCenter" disabled required style="width:100%;">
                                    <option disabled selected>Select Center</option>
                                    <?php foreach ($centers as $center) : ?>
                                        <option value="<?= $center->centerNumber ?>"><?= $center->centerName ?></option>
                                    <?php endforeach; ?>
                                </select>

                            </div>

                        <?php endif; ?>

                    </div>
                    <div class="row">
                        <div class=" <?= !$user->inGroup('officer', 'manager') ? 'col-md-3' : 'col-md-4' ?> ">
                            <div class="form-group">
                                <label for="my-select"><input class="check checkBox" style="transform:scale(1.3); margin-right:5px" type="checkbox" name="" id="enableQuarter">Quarter/Annual</label>
                                <select id="quarter" name="quarter" class="form-control select2bs4" disabled>
                                    <option value="Q1" selected>Quarter One</option>
                                    <option value="Q2">Quarter Two</option>
                                    <option value="Q3">Quarter Three</option>
                                    <option value="Q4">Quarter Four</option>
                                    <option value="Annually">Annually</option>
                                </select>
                            </div>
                        </div>
                        <div class=" <?= !$user->inGroup('officer', 'manager') ? 'col-md-3' : 'col-md-4' ?>">
                            <div class="form-group">
                                <label for="my-select"><input class="check checkBox" style="transform:scale(1.3); margin-right:5px" type="checkbox" name="" id="enableMonth">Month</label>
                                <select id="month" name="month" class="form-control select2bs4" disabled>
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
                        <div class=" <?= !$user->inGroup('officer', 'manager') ? 'col-md-3' : 'col-md-4' ?> ">
                            <div class="form-group">
                                <label for="my-select">Year</label>
                                <select id="year" class="form-control select2bs4" name="year">
                                    <!-- <option value="<?= date('Y') ?>"><?= date('Y') ?></option> -->
                                    <?php for ($i = date('Y'); $i >= 2023; $i--) : ?>
                                        <option value="<?= $i . '/' . $i + 1 ?>"><?= $i . '/' . $i + 1 ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class=" <?= !$user->inGroup('officer', 'manager') ? 'col-md-3' : 'col-md-4' ?> ">
                            <div class="form-group">
                                <label for="my-input"> <input class="check checkBox" style="transform:scale(1.3); margin-right:5px" type="checkbox" name="" id="enableDateFilter">Custom Date</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input id="dateFrom" class="form-control" type="date" name="dateFrom" disabled>

                                    </div>
                                    <div class="col-md-6">
                                        <input id="dateTo" class="form-control" type="date" name="dateTo" disabled>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">

                            <label for=""><span style="color:transparent">..</span></label>
                            <!-- <input class="btn btn-primary  col-sm-12" id="generateBtn" type="submit" value="Generate"> -->
                            <button  class="btn btn-primary  col-sm-12" type="submit" id="generateBtn">
                            <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                                Generate
                            </button>
                        </div>




                    </div>
            </div>

            </form>
        </div>
        <div class="card">
            <div class="card-header">

                <h6 class="text-center" style="font-weight: bold;" id="title"></h6>

            </div>


            <div class="card-body">

                <table class="table table-bordered table-sm reportContainer" id="reportTable">




                </table>


                <br>
                <div id="summary"></div>

            </div>
            <div class="card-footer">
                <a id="downloadReport" target="_blank" class="btn btn-success btn-sm"><i class="far fa-download" aria-none="true"></i>Download</a>
            </div>
        </div>
        <input type="text" value="<?= $userLocation ?>" id="userLocation" hidden>
    </div>
    <input type="text" type="radio" id="rd" hidden>


    <script>
        function formatNumber(number) {
            return new Intl.NumberFormat().format(number)
        }



        const enableRegion = document.querySelector('#enableRegion')
        const enableMonth = document.querySelector('#enableMonth')
        const enableQuarter = document.querySelector('#enableQuarter')
        const enableDateFilter = document.querySelector('#enableDateFilter')

        const dateFrom = document.querySelector('#dateFrom')
        const dateTo = document.querySelector('#dateTo')
        const region = document.querySelector('#collectionCenter')
        const rd = document.querySelector('#rd')



        //=================##################=====================


        const enableInput = (checkBox, input, inputTwo) => {
            checkBox.addEventListener('click', (e) => {
                checkCategories()
                if (e.target.checked == true) {
                    if (input.hasAttribute('disabled')) {
                        input.removeAttribute('disabled')
                        inputTwo.removeAttribute('disabled')
                    }
                } else {
                    input.setAttribute('disabled', 'disabled')
                    inputTwo.setAttribute('disabled', 'disabled')
                    // input.value = null
                    // inputTwo.value = null
                }
            })
        }
        enableInput(enableMonth, month, rd)
        enableInput(enableQuarter, quarter, rd)
        enableInput(enableDateFilter, dateFrom, dateTo)

        <?php if (!$user->inGroup('officer', 'manager')) : ?>
            enableInput(enableRegion, region, rd)
        <?php endif; ?>
        //=================################====================

        function checkCategories() {
            const generateBtn = document.querySelector('#generateBtn')
            const checkBoxes = document.querySelectorAll('.checkBox')
            let checkedBoxes = []
            checkBoxes.forEach(checkBox => {
                if (checkBox.checked == true) {
                    checkedBoxes.push('*')
                }
            })

            if (checkedBoxes.length > 1) {
                generateBtn.setAttribute('disabled', 'disabled')
                return swal({
                    title: 'Please Choose One  Category',
                    icon: "warning",
                    timer: 4500
                });

                // return false
            } else {
                // return true
                generateBtn.removeAttribute('disabled')
            }

        }

        const reportForm = document.querySelector('#reportForm')
        reportForm.addEventListener('submit', (e) => {
            e.preventDefault()
            const reportContainer = document.querySelector('.reportContainer')

            const formData = new FormData(reportForm)








            checkCategories()
            submitInProgress(e.submitter)
            fetch('getCollectionReport', {
                    method: 'POST',
                    headers: {
                        ///'Content-Type': 'application/json;charset=utf-8',
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSRF-TOKEN': document.querySelector('.token').value
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    submitDone(e.submitter)
                    const {
                        report,
                        token,
                        summary,
                        link,
                        params,
                        activity,
                        title
                    } = data
                    document.querySelector('.token').value = token
                    console.log(data)
                    // console.table(params)
                    // console.log(title)

                    document.querySelector('#downloadReport').setAttribute('href', link)

                   


                    reportContainer.innerHTML = ''
                    reportContainer.innerHTML = report
                    document.querySelector('#summary').innerHTML = summary
                    document.querySelector('#title').textContent = title



                    $('#reportTable').DataTable({
                        destroy: true
                    });

                    let table = $('#reportTable').DataTable();

                    // Destroy the DataTable
                    table.destroy();

                    // Remove the table
                    $('#reportTable').empty();

                    // Add a new table with updated `thead`
                    $('#reportTable').html(report);

                    // Re-initialize the DataTable with updated `thead`
                    table = $('#reportTable').DataTable({
                        dom: '<"top"lBfrtip>',
                        buttons: [
                            'excel', 
                        ],
                        lengthMenu: [40,60,80,100,150,200,300]
                    });








                });







        })
    </script>
</section>
<?= $this->endSection(); ?>