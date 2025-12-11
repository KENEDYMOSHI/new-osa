<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<div class="container">
    <table border="0" style="width: 40%; margin-top:41px;">
        <tr>
            <td><b>Client:</b></td>
            <td><?= $report[0]->name ?></td>
        </tr>
        <tr>
            <td><b>Brand Name:</b></td>
            <td><?= $report[0]->brand ?></td>
        </tr>
        <tr>
            <td><b>Meter Size:</b></td>
            <td><?= $report[0]->meter_size ?></td>
        </tr>
        <tr>
            <td><b>Flow Rate:</b></td>
            <td><?= $report[0]->flow_rate ?> </td>
        </tr>
        <tr>
            <td><b>Class:</b></td>
            <td><?= $report[0]->class ?> </td>
        </tr>
        <tr>
            <td><b>Testing Center:</b></td>
            <td><?= $report[0]->lab ?></td>
        </tr>
        <tr>
            <td><b>Testing Method:</b></td>
            <td><?= $report[0]->testing_method ?></td>
        </tr>
        <tr>
            <td><b>Testing Date:</b></td>
            <td><?= dateFormatter($report[0]->created_at) ?></td>
        </tr>
        <tr>
            <td><b>Verified By:</b></td>
            <td><?= $report[0]->verifier ?></td>
        </tr>
        <tr>
            <td><b>Meters Passed</b></td>
            <td><?= $passedMeters ?></td>
        </tr>
        <tr>
            <td><b>Meters Failed</b></td>
            <td><?= $failedMeters ?></td>
        </tr>

    </table>
    <br>

    <table class="table" border="1">
        <thead>
            <tr>
                <th>No.</th>
                <th>Meter Serial No.</th>
                <th>Initial Reading</th>
                <th>Final Reading</th>
                <th>Indicated Volume Vi(L)</th>
                <th>Actual Volume Va(L)</th>
                <th>% Error</th>
                <th>Decision</th>
                <th>Seal No</th>
                <th>Rejection No</th>
            </tr>

        </thead>
        <?php $index = 1;  ?>

        <tbody>
            <?php foreach ($report as $meter) : ?>
                <tr class="row">
                    <td><?= $index++ ?></td>
                    <td><?= $meter->serial_number ?></td>
                    <td><?= $meter->initial_reading ?></td>
                    <td><?= $meter->final_reading ?></td>
                    <td><?= $meter->indicated_volume ?></td>
                    <td><?= $meter->actual_volume ?></td>
                    <td><?= $meter->error ?></td>
                    <td><?= $meter->decision ?></td>
                    <td><?= $meter->decision === 'PASS' ? $meter->tag : '-' ?></td>
                    <td><?= $meter->decision === 'FAIL' ? $meter->tag : '-' ?></td>

                </tr>
            <?php endforeach; ?>


        </tbody>



    </table>



    <br>
    <!-- <table class="table text-bold">
        <tr>
            <td>
                <h4><b>Verifier</b></h4>
                <?= $report[0]->first_name . ' ' . $report[0]->last_name  ?><br>

                Weights & Measures Officer <br>
                Weights & Measures Agency

            </td>

            
        </tr>
    </table> -->
    <table class="table">
        <tr class="">
            <td>
                <div>
                    <!-- Left Column Content -->
                    <h6 class="bold">Verifier </h6>
                    <br>
                    <h6 class="bold"><?=  $report[0]->verifier  ?></h6>
                    <br>
                    <h6 class="bold">Weights And Measures Officer </h6>
                    <br>
                    <h6 class="bold">P.O Box <?= wmaCenter($report[0]->region)->address ?? '' ?></h6>

                    <br>
                    <br>
                    <p class="bold">.........................................................................</p>
                    <br>

                    <h6 class="bold">Signature</h6>
                    <br>
                    <h6 class="bold">Date: <?= dateFormatter(date('Y-m-d')) ?></h6>
                </div>
            </td>
            <td>
                <div>
                    <h6 class="bold">Customer</h6>
                    <br>
                    <h6 class="bold"><?= $report[0]->name ?></h6>
                    <br>
                    <h6 class="bold"><?= !(isset($report[0]->postal_address)) ? '' : $report[0]->postal_address ?></h6>
                    <br>
                    <!-- <br>
                <h6><span class="bold">Name</span>: ....................................................</h6>
                <br>
                <br> -->
                    <p class="bold">.........................................................................</p>
                    <br>
                    <h6 class="bold">Signature</h6>
                    <br>
                    <h6 class="bold">Date: <?= dateFormatter(date('Y-m-d')) ?></h6>
                </div>
            </td>

        </tr>
    </table>
</div>
<?= $this->endSection(); ?>