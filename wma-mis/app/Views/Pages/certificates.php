<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>
<div class="content-header">

    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark"><?= $page['heading'] ?></h4>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/AdminDashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<!-- Modal for editing user -->

<!-- ======================================================== -->

<div class="container-fluid">


    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade" id="certificatePreview" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title">CERTIFICATE PREVIEW</h5> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="cert">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <div id="download">

                    </div>

                </div>
            </div>
        </div>
    </div>

    <input type="text" class="form-control" value="conformity" id="tab" hidden>
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-tabs">
                <div class="form-group">



                </div>
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#conformity" role="tab" aria-controls="conformity" aria-selected="true" onclick="currentTab('conformity')"><i class="far fa-file-certificate fa-lg"></i> Certificate Of Conformity</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#correctness" role="tab" aria-controls="correctness" aria-selected="false" onclick="currentTab('correctness')"><i class="far fa-file-check fa-lg"></i> Certificate Of Correctness</a>
                        </li>

                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade show active" id="conformity" role="tabpanel" aria-labelledby="conformity">

                            <div class="card">
                                <div class="card-body">
                                    <form id="conformityForm">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="">Activity</label>
                                                    <select class="form-control select2bs4" name="activity" id="" style="width:100%">

                                                        <option value="<?= setting('Gfs.prePackages') ?>">Pre Package</option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Search By Name</label>
                                                    <input type="text" name="conformity-name" id="conformity-name" class="form-control" placeholder="Enter Name" aria-describedby="helpId">

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="">Search By Control Number</label>
                                                    <input type="text" name="controlNumber" id="conformity-controlNumber" class="form-control control" placeholder="Enter Control Number" oninput="this.value=this.value.replace(/(?![0-9])./gmi,'')">

                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="my-select">Month</label>
                                                    <select id="conformity-month" name="month" class="form-control select2bs4">
                                                        <option value="" selected disabled>Month</option>
                                                        <option value="1">January</option>
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
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="my-select">Year</label>
                                                    <select id="conformity-year" class="form-control select2bs4" name="year">
                                                        <option value="" selected disabled>Year</option>
                                                        <?php for ($i = date('Y'); $i >= 2023; $i--) : ?>
                                                            <option value="<?= $i ?>"><?= $i ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Date range -->

                                            <div class="col-md-1 mt-4">
                                                <button type="button" class="btn btn-primary" onclick="fetchCertificates()">
                                                    <!-- <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div> -->
                                                    Search
                                                </button>
                                            </div>

                                        </div>
                                        </from>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-1 mb-1">
                                                <div>
                                                    <label for="" class="form-label">Show</label>
                                                    <select class="form-control " name="" id="conformity-perPage" onchange="updateAndFetch()" style="width: 3.2rem;">
                                                        <option value="5">5</option>
                                                        <option value="10">10</option>
                                                        <option selected value="20">20</option>
                                                        <option value="30">30</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table table-bordered table-hover table-sm" id="conformityTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Customer</th>
                                                    <th>Certificate Number</th>
                                                    <th>Control Number</th>
                                                    <th>Products</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="conformityData">

                                            </tbody>
                                        </table>
                                        <div id="conformity-nav"></div>
                                </div>
                            </div>
                        </div>



                        <!-- certificates of correctness -->
                        <div class="tab-pane fade" id="correctness" role="tabpanel" aria-labelledby="correctness">
                            <div class="card">
                                <div class="card-body">
                                    <form id="correctnessForm">
                                        <div class="row">


                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Search By Name</label>
                                                    <input type="text" name="name" id="correctness-name" class="form-control" placeholder="Enter Name" aria-describedby="helpId">

                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Search By Control Number</label>
                                                    <input type="text" name="correctness-controlNumber" id="correctness-controlNumber" class="form-control control" placeholder="Enter Control Number" oninput="this.value=this.value.replace(/(?![0-9])./gmi,'')">

                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="my-select">Month</label>
                                                    <select name="month" id="correctness-month" class="form-control select2bs4">
                                                        <option value="" selected disabled>Month</option>
                                                        <option value="1">January</option>
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
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="my-select">Year</label>
                                                    <select id="correctness-year" class="form-control select2bs4" name="year">
                                                        <option value="" selected disabled>Year</option>
                                                        <?php for ($i = date('Y'); $i >= 2023; $i--) : ?>
                                                            <option value="<?= $i ?>"><?= $i ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Date range -->

                                            <div class="col-md-2 mt-4">
                                                <button type="button" class="btn btn-primary " onclick="fetchCertificates()">
                                                    <!-- <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div> -->
                                                    Search
                                                </button>
                                            </div>

                                        </div>
                                        </from>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-1 mb-1">
                                                <div>
                                                    <label for="" class="form-label">Show</label>
                                                    <select class="form-control " name="" id="correctness-perPage" onchange="updateAndFetch()" style="width: 3.2rem;">
                                                        <option value="5">5</option>
                                                        <option value="10">10</option>
                                                        <option selected value="20">20</option>
                                                        <option value="30">30</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table table-bordered table-hover table-sm" id="correctnessTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <!-- <th>#</th> -->
                                                    <th>Date</th>
                                                    <th>Customer</th>
                                                    <th>Certificate Number</th>
                                                    <th>Control Number</th>
                                                    <th>Items</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="correctnessData">

                                            </tbody>
                                        </table>

                                        <div id="correctness-nav"></div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>

    </div>
</div>



<script>
    let tab = document.getElementById('tab').value;

    function currentTab(tabName) {
        document.getElementById('tab').value = tabName
    }
    $(function() {
        $(`#${tab}Table`).DataTable({
            dom: '<"top"lBfrtip>',
            buttons: [
                'excel',
            ],

            "responsive": true,
            "autoWidth": false,
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,

        });
    });




    let currentPage = 1;

    // Function to construct URL query parameters
    function createUrlWithParams(params) {
        const queryParams = new URLSearchParams(params);
        return `&${queryParams.toString()}`;
    }

    // Fetch certificates based on the current filters and pagination
    function fetchCertificates(page = 1) {
        // Update the current page when fetching new data
        currentPage = page;

        let tab = document.getElementById('tab').value;
        let route = tab == 'correctness' ? 'getCertificates' : 'getCertificates';
        const perPage = document.querySelector(`#${tab}-perPage`).value;
        const month = document.querySelector(`#${tab}-month`).value;
        const year = document.querySelector(`#${tab}-year`).value;
        const controlNumber = document.querySelector(`#${tab}-controlNumber`).value;


        const params = {
            month,
            year,
            controlNumber
        };
        console.log(params)

        let urlPart = tab.charAt(0).toUpperCase() + tab.slice(1)

        const url = `search${urlPart}Certificate?page=${page}&perPage=${perPage}${createUrlWithParams(params)}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Handle pagination controls
                const {
                    links,
                    certificates
                } = data;



                if (links) {
                    document.querySelector(`#${tab}-nav`).innerHTML = links;
                }

                // Populate certificates




                let table = $(`#${tab}Table`).DataTable();

                // // Destroy the DataTable
                table.destroy();

                // // // // Remove the table
                $(`#${tab}Data`).empty();

                // // // Add a new table with updated `thead`
                $(`#${tab}Data`).html(certificates);

                $('[data-toggle="tooltip"]').tooltip()

                // // Re-initialize the DataTable with updated `thead`
                table = $(`#${tab}Table`).DataTable({
                    dom: '<"top"lBfrtip>',
                    buttons: [
                        'excel',
                    ],
                    lengthMenu: [20, 30, 50, 70, 100],
                    "responsive": true,
                    "autoWidth": false,
                    "paging": false,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "responsive": true,

                });

            })
            .catch(error => {
                console.error('Error fetching certificates:', error);
            });
    }

    // Function to fetch data while maintaining the current page
    function updateAndFetch() {
        fetchCertificates(currentPage); // Maintain the current page when filters are applied
    }

    // Function to get data from pagination links
    function getCertificatesData(page) {
        fetchCertificates(page); // Fetch data for a specific page
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Fetch certificates on initial page load
        fetchCertificates(1);


    });


























    //Date range picker
    const conformityForm = document.querySelector(`#${tab}Form`)

    conformityForm.addEventListener('submit', (e) => {
        e.preventDefault()

        let tab = document.getElementById('tab').value;

        console.log('searching....' + tab)
        const formData = new FormData(conformityForm)
        const conformityData = document.querySelector(`#${tab}Data`)


        let urlPart = tab.charAt(0).toUpperCase() + tab.slice(1)
        submitInProgress(e.submitter)
        fetch(`search${urlPart}Certificate`, {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },

                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let tab = document.getElementById('tab').value
                submitDone(e.submitter)
                const {
                    status,
                    token,
                    htmlData,

                } = data


                document.querySelector('.token').value = token
                if (status == 1) {

                    // console.log(conformityData)
                    conformityData.innerHTML = htmlData

                    let table = $(`#${tab}Table`).DataTable();

                    // // Destroy the DataTable
                    table.destroy();

                    // // // // Remove the table
                    $(`#${tab}Data`).empty();

                    // // // Add a new table with updated `thead`
                    $(`#${tab}Data`).html(htmlData);

                    $('[data-toggle="tooltip"]').tooltip()

                    // // Re-initialize the DataTable with updated `thead`
                    table = $(`#${tab}Table`).DataTable({
                        dom: '<"top"lBfrtip>',
                        buttons: [
                            'excel',
                        ],
                        lengthMenu: [20, 30, 50, 70, 100],
                        "responsive": true,
                        "autoWidth": false,
                        "paging": false,
                        "lengthChange": false,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "responsive": true,

                    });




                } else {
                    swal({
                        title: 'No Data Found !',
                        icon: "warning",
                        timer: 2500
                    });
                }






            });
    })


































    function viewCertificate(certificateId) {


        let tab = document.getElementById('tab').value

        let urlPart = tab.charAt(0).toUpperCase() + tab.slice(1)


        const params = {
            certificateId: certificateId,
        }

        fetch(`view${urlPart}Certificate`, {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },
                body: JSON.stringify(params)
            })
            .then(response => response.json())
            .then(data => {
                const {
                    token,
                    certificate,
                    button,
                    status
                } = data
                console.log(data)
                document.querySelector('.token').value = token


                if (status == 1) {
                    document.querySelector('#cert').innerHTML = certificate
                    document.querySelector('#download').innerHTML = button
                    $(`#certificatePreview`).modal({
                        open: true,
                        // backdrop: 'static'
                    })


                }


            });



    }
</script>

<?= $this->endSection(); ?>