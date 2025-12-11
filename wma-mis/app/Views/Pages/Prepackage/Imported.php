<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>
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
<!-- /.content-header -->
<!-- Main content -->
<section class="content body">
    <div class="container-fluid">
        <pre>
            <!-- <?php print_r($imported) ?> -->
        </pre>
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <?php if ($pageSession->getFlashdata('Success')) : ?>
                        <div id="message" class="alert alert-success text-center" role="alert">
                            <?= $pageSession->getFlashdata('Success'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12 align-items-center">
                                    <h2 id="reportTitle" style="text-align: center;font-size:1.6rem" class="card-title mb-1 text-center"><?= $page['heading'] ?> <?= str_replace('Wakala Wa Vipimo', '', wmaCenter($user->collection_center)->centerName) ?></h2>
                                </div>
                                <div class="col-md-12">
                                    <hr>
                                </div>
                            </div>

                            <div class="row">

                                <?php if (!auth()->user()->inGroup('manager', 'officer')): ?>
                                    <div class="form-group col-md-3">
                                        <label for="">Collection Center</label>
                                        <select class="form-control select2bs4" id="region" name="region" required>
                                            <option disabled selected value="">Select Center</option>
                                            <option value="">All Regions</option>
                                            <?php foreach ($centers as $center) : ?>
                                                <option value="<?= $center->centerNumber ?>"><?= $center->centerName ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                <div class=" col-md-3 ">
                                    <div class="form-group">
                                        <label for="quarter"><input class="check checkBox" style="transform:scale(1.3); margin-right:5px" type="checkbox" name="" id="enableQuarter">Quarter/Annual</label>
                                        <select id="quarter" name="quarter" class="form-control select2bs4" disabled>
                                            <option value="Q1" selected>Quarter One</option>
                                            <option value="Q2">Quarter Two</option>
                                            <option value="Q3">Quarter Three</option>
                                            <option value="Q4">Quarter Four</option>
                                            <option value="Annually">Annually</option>
                                        </select>
                                    </div>
                                </div>
                                <div class=" col-md-3'">
                                    <div class="form-group">
                                        <label for="month"><input class="check checkBox" style="transform:scale(1.3); margin-right:5px" type="checkbox" name="" id="enableMonth">Month</label>
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
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        <label for="my-select">Year</label>
                                        <select id="year" class="form-control select2bs4" name="year" disabled>
                                            <!-- <option value="<?= date('Y') ?>"><?= date('Y') ?></option> -->
                                            <?php for ($i = date('Y'); $i >= 2023; $i--) : ?>
                                                <option value="<?= $i . '/' . $i + 1 ?>"><?= $i . '/' . $i + 1 ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        <label for="enableDateFilter"> <input class="check checkBox" style="transform:scale(1.3); margin-right:5px" type="checkbox" name="" id="enableDateFilter">Custom Date</label>
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
                                    <button class="btn btn-primary  col-sm-12" type="submit" id="generateBtn">
                                        <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                                        Filter
                                    </button>
                                </div>




                            </div>
                        </div>


                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- <h4>Total Amount: <span class="total"></span></h4> 0766710507 -->

                            <?php if ($imported) : ?>

                                <table id="imported" class="table-bordered table-sm" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Region</th>
                                            <th>Importer</th>
                                            <th>Product</th>
                                            <th>Tansad Number</th>
                                            <th>F.O.B</th>
                                            <th>Fees</th>
                                            <th>Control Number</th>
                                            <th>Payment Status</th>
                                            <th>Phone Number</th>
                                            <th>Decision</th>
                                        </tr>
                                    </thead>
                                    <tbody id="imported-table">
                                        <?php foreach ($imported as $item) : ?>
                                            <tr>
                                                <td><?= dateFormatter($item->createdAt) ?></td>
                                                <td><?= str_replace('Wakala Wa Vipimo', '', wmaCenter($item->center)->centerName) ?></td>
                                                <td><?= $item->customer ?></td>
                                                <td><?= wordwrap($item->product, 50, "<br>") ?></td>
                                                <td><?= $item->tansardNumber ?></td>
                                                <td><?= $item->fob ?></td>
                                                <td><?= number_format($item->amount) ?></td>
                                                <td><?= $item->controlNumber ?></td>
                                                <td><?= $item->PaymentStatus ?></td>
                                                <td><?= $item->phoneNumber ?></td>
                                                <td><?= $item->Status ?></td>

                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>



                            <?php else : ?>
                                <h5>There Are No Records Currently Available</h5>
                            <?php endif; ?>
                            <!-- <table id="example1" class="my-table " > -->

                        </div>
                        <div class="card-footer">
                            <a href="" id="downloadBtn" target="_blank" class="btn btn-primary">Download <i class="far fa-download    "></i></a>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>

    </div>
    <!-- /.card -->

    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the elements
            const enableQuarterCheckbox = document.getElementById('enableQuarter');
            const quarterSelect = document.getElementById('quarter');

            const enableMonthCheckbox = document.getElementById('enableMonth');
            const monthSelect = document.getElementById('month');

            const enableDateFilterCheckbox = document.getElementById('enableDateFilter');
            const dateFromInput = document.getElementById('dateFrom');
            const dateToInput = document.getElementById('dateTo');

            const yearSelect = document.getElementById('year');
            const generateBtn = document.getElementById('generateBtn');
            const spinner = document.getElementById('spinner');


            // Function to disable/enable other options
            function toggleOptions(selectedCheckbox) {
                if (selectedCheckbox === enableQuarterCheckbox) {
                    quarterSelect.disabled = !enableQuarterCheckbox.checked;
                    enableMonthCheckbox.checked = false;
                    enableDateFilterCheckbox.checked = false;
                    monthSelect.disabled = true;
                    dateFromInput.disabled = true;
                    dateToInput.disabled = true;
                    yearSelect.disabled = !enableQuarterCheckbox.checked; // Enable Year
                } else if (selectedCheckbox === enableMonthCheckbox) {
                    monthSelect.disabled = !enableMonthCheckbox.checked;
                    enableQuarterCheckbox.checked = false;
                    enableDateFilterCheckbox.checked = false;
                    quarterSelect.disabled = true;
                    dateFromInput.disabled = true;
                    dateToInput.disabled = true;
                    yearSelect.disabled = !enableMonthCheckbox.checked; // Enable Year
                } else if (selectedCheckbox === enableDateFilterCheckbox) {
                    const isEnabled = enableDateFilterCheckbox.checked;
                    dateFromInput.disabled = !isEnabled;
                    dateToInput.disabled = !isEnabled;
                    enableQuarterCheckbox.checked = false;
                    enableMonthCheckbox.checked = false;
                    quarterSelect.disabled = true;
                    monthSelect.disabled = true;
                    yearSelect.disabled = isEnabled; // Disable Year
                }
            }

            // Function to collect and validate data
            function collectValues() {
                const data = {};

                <?php if (!auth()->user()->inGroup('manager', 'officer')): ?>
                    const region = document.getElementById('region');
                    data.region = region.value
                <?php else: ?>
                    data.region = ''
                <?php endif; ?>

                if (!quarterSelect.disabled && enableQuarterCheckbox.checked) {
                    data.quarter = quarterSelect.value;
                }

                if (!monthSelect.disabled && enableMonthCheckbox.checked) {
                    data.month = monthSelect.value;
                }

                if (!dateFromInput.disabled && dateFromInput.value && !dateToInput.disabled && dateToInput.value) {
                    data.dateFrom = dateFromInput.value;
                    data.dateTo = dateToInput.value;
                }

                if (!yearSelect.disabled && yearSelect.value) {
                    data.year = yearSelect.value;
                }

                if (Object.keys(data).length === 0) {
                    alert('Please select a filter and provide the necessary inputs.');
                    throw new Error('Validation failed: No data provided.');
                }
                let uri = `downloadImportedReport/${quarterSelect.value}/${monthSelect.value}/${dateFromInput.value}/${dateToInput.value}/${yearSelect.value}`;

                data.uri = uri;

                return data;
            }

            // Function to send the fetch request
            async function sendRequest(data) {
                try {
                    spinner.style.display = 'inline-block'; // Show spinner

                    const response = await fetch('filterImportedReport', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data),
                    });

                    if (!response.ok) {
                        throw new Error(`Error: ${response.statusText}`);
                    }

                    const result = await response.json();
                    const {
                        status,
                        imported,
                        url,
                        title
                    } = result;

                    console.log('Response:', result);
                    document.getElementById('downloadBtn').setAttribute('href', url);
                    document.getElementById('reportTitle').textContent = title;

                    // Check if DataTable instance exists
                    let table = $.fn.DataTable.isDataTable('#imported') ?
                        $('#imported').DataTable() :
                        null;

                    // Destroy existing DataTable instance if it exists
                    if (table) {
                        table.destroy();
                    }

                    // Clear existing table body content
                    $('#imported tbody').empty();

                    // Inject the new rows into the table body
                    $('#imported tbody').html(imported);

                    // Reinitialize the DataTable with updated rows
                    $('#imported').DataTable({
                        dom: '<"top"lBfrtip>',
                        buttons: ['excel'],
                        lengthMenu: [30, 50, 70, 100, 150, 200],
                        responsive: true,
                        autoWidth: false,
                        paging: true,
                        lengthChange: true,
                        searching: true,
                        ordering: true,
                        info: true,
                    });
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while sending the request.');
                } finally {
                    spinner.style.display = 'none'; // Hide spinner
                }
            }

            // Event listener for Generate button
            generateBtn.addEventListener('click', async (e) => {
                e.preventDefault(); // Prevent form submission
                try {
                    const data = collectValues(); // Collect and validate values
                    await sendRequest(data); // Send the request
                } catch (error) {
                    console.error('Error in form handling:', error.message);
                }
            });

            // Event listeners for the checkboxes
            enableQuarterCheckbox.addEventListener('change', () => toggleOptions(enableQuarterCheckbox));
            enableMonthCheckbox.addEventListener('change', () => toggleOptions(enableMonthCheckbox));
            enableDateFilterCheckbox.addEventListener('change', () => toggleOptions(enableDateFilterCheckbox));
        });








        $(document).ready(function() {
            var table = $('#imported').DataTable({
                dom: '<"top"lBfrtip>',
                buttons: [
                    'excel',
                ],
                lengthMenu: [30, 50, 70, 100, 150, 200],
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
    </script>

</section>



<?= $this->endSection(); ?>